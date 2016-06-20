<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Xmlimport Model
 *
 * @package			2find
 * @subpackage	Models
 * @author			Viktor Kuzhelnyi @marketto.ru
 */
class Xmlimport_model extends CI_Model {
	
	/// Here we count the number of rows that were actually inserted by import_db_batch()
	/**
	 * @var int
     */
	var $db_batch_inserted_rows_count	= 0;
	
	/// import_db_batch() accumulates fields for insert here
	/**
	 * @var array
     */
	var $db_batch_cache					= array();
	
	/// import_db_batch() accumulates this number of rows before perfoming the batch insert
	/**
	 * @var int
     */
	var $db_batch_size					= 100;
	
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Import Utility XMLReader
	 *
	 * XMLReader based import utlity. Takes file name and vendor data. Processes the file and stores the data. 
	 *
	 * @param	string $xmlfile
	 * @param	obj $vendor
	 * @return	void
	 */	
	public function prices($xmlfile, $vendor)
	{
		// This will be import_group_id
		$vendor->new_import_group_id = random_string('unique');
		
		// Init XMLReader
		$z = new XMLReader;
		
		// Open the file uploaded
		$z->open($xmlfile);
		
		$nodesMatch = $this->local->vendor_price_structure($vendor);
		
		if (empty($nodesMatch))
		{
			// The default order of cells in a file
			$nodesMatch = array
			(
				'1'	=> 'art_number',
				'2'	=> 'sup_brand',
				'3'	=> 'description',
				'4'	=> 'qty',
				'5'	=> 'price',
			);
		}
		
		// Line Count is 0. With the first opening <Row> it's gonna be 1.
		$line_count = 0;
		
		// We log nodes to distinguish opening/closing nodes
		$open_nodes_log = array();
		
		while ($z->read())
		{
			$_is_closing_tag = FALSE;
			
			if (isset($open_nodes_log[$z->depth]))
			{
				if ($z->name == $open_nodes_log[$z->depth])
				{
					$_is_closing_tag = TRUE;
				}
			}
			
			$open_nodes_log[$z->depth] = $z->name;
			
			// The <Cell>
			if ($z->name == 'Cell' and !$_is_closing_tag)
			{		
				$cell_count++;
				
				// Some cells may be skipped in file
				$ssIndex = $z->getAttribute('ss:Index');
				
				if ($ssIndex != NULL)
				{
					$cell_count = $ssIndex;
				}
				
				// The node should exist
				if (isset($nodesMatch[$cell_count]))
				{
					if (!$z->isEmptyElement and ($cell_contents = trim(preg_replace('#\s#',' ', $z->readString()))) != '')
					{
						$insert_line[$nodesMatch[$cell_count]] = $cell_contents;
					}
				}
			}
			
			// Opening <Row>
			elseif ($z->name == 'Row' and !$_is_closing_tag)
			{
				// Prep new array for storing values
				$insert_line = array(); 
				
				// Reset cell counter
				$cell_count = 0;
				
				// Add to line counter
				$line_count++;
			}
			
			// Closing </Row>
			elseif ($z->name == 'Row' and $_is_closing_tag)
			{
				// Is there something to insert? And the line is not the first one?
				if (count($insert_line) > 0 and $line_count > 1)
				{
					// ART_NUMBER
					
					// Article number is required
					if (!isset($insert_line['art_number']))
					{
						echo "[Строка #$line_count] Строка пропущена: Проблема с артикульным номером.\n";
						continue;
					}
					
					// Let's generate clear article number
					$insert_line['art_number_clear'] = preg_replace('/\W/u', '', $insert_line['art_number']);
					
					// Is something's got left?
					if ($insert_line['art_number_clear'] == '')
					{
						echo "[Строка #$line_count] Строка пропущена: Проблема с артикульным номером.\n";
						continue;
					}
					
					// Convert special chars
					$insert_line['art_number'] = htmlspecialchars($insert_line['art_number']);
					
					// Cut the length
					if (mb_strlen($insert_line['art_number']) > 100)
					{
						$insert_line['art_number'] = mb_substr($insert_line['art_number'], 0, 100);
					}

					
					// PRICE
					
					// The price is required
					if (!isset($insert_line['price']))
					{
						echo "[Строка #$line_count] Строка пропущена: Не указана цена.\n";
						continue;
					}
					
					// Let's make a price of a string
					$price_raw = (float) str_replace(',','.',$insert_line['price']);
					
					// Is it larger than 0?
					if ($price_raw <= 0)
					{
						echo "[Строка #$line_count] Строка пропущена: Похоже, что цена равняется 0.\n";
						continue;
					}
					
					// Price multiplied by price_correction in db-ready format
					$insert_line['price'] = number_format($this->currency->price_prep($price_raw * $vendor->price_correction), 2, '.', '');
					
					// SUP_BRAND
					// Do we have a brand?
					if (!isset($insert_line['sup_brand']))
					{
						echo "[Строка #$line_count] Не указан бренд. Автозамена на [noname].\n";
						$insert_line['sup_brand'] = '[noname]';
					}
					
					// We have a brand
					else
					{
						// Convert special chars
						$insert_line['sup_brand'] = htmlspecialchars($insert_line['sup_brand']);
						
						// Cut the length
						if (mb_strlen($insert_line['sup_brand']) > 256)
						{
							echo "[Строка #$line_count] Бренд сокращен до длины 256 символов.\n";
							$insert_line['sup_brand'] = mb_substr($insert_line['sup_brand'], 0, 256);
						}
					}
					
					// DESCRIPTION
					
					// Do we have a description?
					if (!isset($insert_line['description']))
					{
						echo "[Строка #$line_count] Не указано описание. Автозамена на ---.\n";
						$insert_line['description'] = '---';
					}
					
					// We have a description
					else
					{
						// Convert special chars
						$insert_line['description'] = htmlspecialchars($insert_line['description']);
						
						// Add soft hyphens to prevent long words to break layout
						$insert_line['description'] = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $insert_line['description']);
						
						// Cut the length
						if (mb_strlen($insert_line['description']) > 256)
						{
							echo "[Строка #$line_count] Описание сокращено до длины 256 символов.\n";
							$insert_line['description'] = mb_substr($insert_line['description'], 0, 256);
						}
					}
					
					// QTY
					
					// Make Qty of a string
					$insert_line['qty'] = (int) preg_replace('/\D/', '', $insert_line['qty']);
					
					if (empty($insert_line['qty']) or $insert_line['qty'] < 0)
					{
						echo "[Строка #$line_count] Строка пропущена. Не указано количество (либо количество равно 0).\n";
						continue;
					}
					elseif ($insert_line['qty'] > 1000)
					{
						echo "[Строка #$line_count] Количество лимитировано 1000 штук.\n";
						$insert_line['qty'] = 1000;
					}
					
					// Vendor Data
					$insert_line['vendor_id'] = $vendor->id;
					$insert_line['import_group_id'] = $vendor->new_import_group_id;
					
					// Cache for future insert
					$this->import_db_batch('prices', $insert_line);
				}
				elseif ($line_count == 1)
				{
					echo "[Строка #$line_count] Пропущена, т.к. обычно содержит заголовки.\n";
				}
				else
				{
					echo "[Строка #$line_count] Пустая.\n";
				}
			}
		}
		
		// Process cached inserts. False in 2nd argument forces an insert.
		$this->import_db_batch('prices', FALSE);
		
		// Set current Import as finished
		$this->local->clear_vendor_prices($vendor->id, $vendor->new_import_group_id);
		$this->local->set_import_group($vendor->id, $vendor->new_import_group_id);
		
		// Cache stats for this import
		$this->local->prices_vndr_stats_cache($vendor->id);
		
		// Optimize critical tables
		// $this->local->optimize_tables();
		
		// Remove xml file
		unlink($xmlfile);
		
		// Close XML
		$z->close();
		
		// After processing we have a plenty of data
		return (object) array
		(
			// Number of <Row>s in the file. The actual line_count is reduced by 1, because the 1st row is always skipped.
			'line_count'	=> $line_count - 1,
			
			// Number of rows inserted
			'rows_inserted'	=> $this->db_batch_inserted_rows_count,
			
			// Number of rows skipped. Reduced by 1, because the 1st row is always skipped.
			'rows_skipped'	=> $line_count - $this->db_batch_inserted_rows_count - 1,
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Import Utility Crosses XMLReader
	 *
	 * XMLReader based import utlity for crosses. Takes file name and vendor data. Processes the file and stores the data. 
	 *
	 * @param	string $xmlfile
	 * @param	obj $vendor
	 * @return	void
	 */	
	public function crosses($xmlfile, $vendor)
	{
		// Prep some vendor data
		$vendor->new_import_group_id = random_string('unique');
		
		// Init XMLReader
		$z = new XMLReader;
		
		// Open the file uploaded
		$z->open($xmlfile);
		
		// Line Count is 0. With the first opening <Row> it's gonna be 1.
		$line_count = 0;
		
		// We log nodes to distinguish opening/closing nodes
		$open_nodes_log = array();
		
		while ($z->read())
		{
			$_is_closing_tag = FALSE;
			
			if (isset($open_nodes_log[$z->depth]))
			{
				if ($z->name == $open_nodes_log[$z->depth])
				{
					$_is_closing_tag = TRUE;
				}
			}
			
			$open_nodes_log[$z->depth] = $z->name;
			
			// The <Cell>
			if ($z->name == 'Cell' and !$_is_closing_tag)
			{				
				if (!$z->isEmptyElement)
				{
					// Get <Cell> contents
					$the_string = $z->readString();
					
					// We have two or more art_numbers in a cell separated by coma/semicolon
					if (preg_match("/[,;]/", $the_string))
					{
						// We remove all whitespaces, then replace comas/semicolons by single space, then we remove everything except "words" and whitespaces
						// The result is well-formatted string AB234 GH24234 RTE88
						$art_numbers = preg_replace('#[^\w ]#', '', preg_replace("/[,;]/", ' ', preg_replace("#\s#", '', $the_string)));
					}
					
					// We have two or more art_numbers in a cell separated by space
					// This is disabled, because many files supplied have whitespaces in numbers. 
					// We can think that there are 2 or more numbers in a cell, when really we have only one. Consider: DG 879 455 R
					/* elseif (preg_match("/\s/", $the_string))
					{
						$art_numbers = preg_replace('#[^\w ]#', '', $the_string);
					} */
					
					// No separators? Let's treat it as a single art_number
					else
					{
						// Remove everything except "words"
						$art_numbers = preg_replace('#\W#', '', $the_string);
					}
					
					// Do we really have something left? Additionaly, we check if there is something besides whitespaces, in case user supplies ;;;;;; as an art_number
					if ($art_numbers != '' and preg_match("#\S#", $art_numbers))
					{
						// If we have data from previous cell in a row?
						if (isset($insert_line['art_numbers']))
						{
							// Append!
							$insert_line['art_numbers'] .= ' ' . $art_numbers;
						}
						else
						{
							// Let's create a new line
							$insert_line['art_numbers'] = $art_numbers;
						}
					}
				}
			}
			
			// Opening <Row>
			elseif ($z->name == 'Row' and !$_is_closing_tag)
			{				
				// Prep new array for storing values
				$insert_line = array(); 
				
				// Add to line counter
				$line_count++;
			}
			
			// Closing </Row>
			elseif ($z->name == 'Row' and $_is_closing_tag)
			{
				// Is there something to insert? Row isn't the first? ...And we have at least two art numbers (whitespace in a line means that we have two at least)
				if (isset($insert_line['art_numbers']) and $line_count > 1 and strpos($insert_line['art_numbers'], ' ') !== FALSE)
				{
					// Vendor Data
					$insert_line['vendor_id'] = $vendor->id;
					$insert_line['import_group_id'] = $vendor->new_import_group_id;
					
					// Cache for future insert
					$this->import_db_batch('crosses', $insert_line);
				}
				elseif ($line_count == 1)
				{
					echo "[Строка #$line_count] Пропущена, т.к. обычно содержит заголовки.\n";
				}
				else
				{
					echo "[Строка #$line_count] Пустая, либо содержит всего лишь один номер.\n";
				}				
			}
		}
		
		// Process cached
		$this->import_db_batch('crosses', FALSE);
		
		// Set current Import as finished
		$this->local->clear_cross_list($vendor->id, $vendor->new_import_group_id);
		$this->local->clear_cross_search($vendor->id, $vendor->new_import_group_id);
		$this->local->set_import_group($vendor->id, $vendor->new_import_group_id);
		
		// Cache stats for this import
		$this->local->crosses_vndr_stats_cache($vendor->id);
		
		// Optimize
		// $this->local->optimize_tables();
		
		// Remove xml file
		unlink($xmlfile);
		
		$z->close();
		
		// After processing we have a plenty of data
		return (object) array
		(
			// Number of <Row>s in the file. The actual line_count is reduced by 1, because the 1st row is always skipped.
			'line_count'	=> $line_count - 1,
			
			// Number of rows inserted
			'rows_inserted'	=> $this->db_batch_inserted_rows_count,
			
			// Number of rows skipped. Reduced by 1, because the 1st row is always skipped.
			'rows_skipped'	=> $line_count - $this->db_batch_inserted_rows_count - 1,
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Import Utility XMLReader
	 *
	 * XMLReader based import utlity. Takes file name and vendor data. Processes the file and stores the data. 
	 *
	 * @param	string $xmlfile
	 * @param	obj $vendor
	 * @return	void
	 */	
	public function users($xmlfile)
	{
		// Init XMLReader
		$z = new XMLReader;
		
		// Open the file uploaded
		$z->open($xmlfile);
		
		// The default order of cells in a file
		$nodesMatch = array
		(
			'1'	=> 'email',
			'2'	=> 'name',
			'3'	=> 'phone',
			'4'	=> 'address',
			'5'	=> 'corp_inn',
			'6'	=> 'corp_ogrn',
			'7'	=> 'corp_rs',
			'8'	=> 'corp_bik',
			'9'	=> 'password',
			'10'	=> 'discount',
		);
		
		// Line Count is 0. With the first opening <Row> it's gonna be 1.
		$line_count = 0;
		
		// We log nodes to distinguish opening/closing nodes
		$open_nodes_log = array();
		
		while ($z->read())
		{
			$_is_closing_tag = FALSE;
			
			if (isset($open_nodes_log[$z->depth]))
			{
				if ($z->name == $open_nodes_log[$z->depth])
				{
					$_is_closing_tag = TRUE;
				}
			}
			
			$open_nodes_log[$z->depth] = $z->name;
			
			// The <Cell>
			if ($z->name == 'Cell' and !$_is_closing_tag)
			{		
				$cell_count++;
				
				// Some cells may be skipped in file
				$ssIndex = $z->getAttribute('ss:Index');
				
				if ($ssIndex != NULL)
				{
					$cell_count = $ssIndex;
				}
				
				// The node should exist
				if (isset($nodesMatch[$cell_count]))
				{
					if (!$z->isEmptyElement and ($cell_contents = trim(preg_replace('#\s#',' ', $z->readString()))) != '')
					{
						$insert_line[$nodesMatch[$cell_count]] = $cell_contents;
					}
				}
			}
			
			// Opening <Row>
			elseif ($z->name == 'Row' and !$_is_closing_tag)
			{
				// Prep new array for storing values
				$insert_line = array(); 
				
				// Reset cell counter
				$cell_count = 0;
				
				// Add to line counter
				$line_count++;
			}
			
			// Closing </Row>
			elseif ($z->name == 'Row' and $_is_closing_tag)
			{
				// Is there something to insert? And the line is not the first one?
				if (count($insert_line) > 0 and $line_count > 1)
				{
					// E-MAIL
					$insert_line['email'] = trim($insert_line['email']);
					if (!empty($insert_line['email']) and !filter_var($insert_line['email'], FILTER_VALIDATE_EMAIL))
					{
						$insert_line['email'] = '';
						echo "[Строка #$line_count] E-mail некорректен.\n";
					}
					
					// NAME
					if (empty($insert_line['name']))
					{
						echo "[Строка #$line_count] Строка пропущена: не указано наименование клиента.\n";
					}
					
					// PASSWORD
					if (empty($insert_line['password']))
					{
						$insert_line['password'] = random_string('numeric', 8);
						echo "[Строка #$line_count] Сгенерирован пароль.\n";
					}
					
					// DISCOUNT
					if (empty($insert_line['discount']) or !preg_match("/[0-9]+/", $insert_line['discount']))
					{
						$insert_line['discount'] = 0;
					}
					else
					{
						$insert_line['discount'] = preg_replace("/[^0-9]+/", "", $insert_line['discount']);
					}
					
					$insert_line = $this->users->_db_prep_save($insert_line, 'insert');
					
					// Cache for future insert
					$this->import_db_batch('users', $insert_line);
				}
				elseif ($line_count == 1)
				{
					echo "[Строка #$line_count] Пропущена, т.к. обычно содержит заголовки.\n";
				}
				else
				{
					echo "[Строка #$line_count] Пустая.\n";
				}
			}
		}
		
		// Process cached inserts. False in 2nd argument forces an insert.
		$this->import_db_batch('users', FALSE);

		// Remove xml file
		unlink($xmlfile);
		
		// Close XML
		$z->close();
		
		// After processing we have a plenty of data
		return (object) array
		(
			// Number of <Row>s in the file. The actual line_count is reduced by 1, because the 1st row is always skipped.
			'line_count'	=> $line_count - 1,
			
			// Number of rows inserted
			'rows_inserted'	=> $this->db_batch_inserted_rows_count,
			
			// Number of rows skipped. Reduced by 1, because the 1st row is always skipped.
			'rows_skipped'	=> $line_count - $this->db_batch_inserted_rows_count - 1,
		);
	}
	
	// Experimental
	/**
	 * @param $xmlfile
	 * @param $mdata
	 * @return object
     */
	public function posts($xmlfile, $mdata)
	{
		// Enable Profiler
		// $this->output->enable_profiler(TRUE);
		
		// Benchmark
		// $this->benchmark->mark('XML_start');
		
		// Init XMLReader
		$z = new XMLReader;
		
		// Open the file uploaded
		$z->open($xmlfile);
		
		$nodesMatch = $this->local->vendor_price_structure($vendor, $mdata);
		
		if (empty($nodesMatch))
		{
			// The default order of cells in a file
			$nodesMatch = array
			(
				'1'	=> 'title',
				'2'	=> 'meta',
				'3'	=> 'meta2',
			);
		}
		
		// Line Count is 0. With the first opening <Row> it's gonna be 1.
		$line_count = 0;
		
		// We log nodes to distinguish opening/closing nodes
		$open_nodes_log = array();
		
		while ($z->read())
		{
			$_is_closing_tag = FALSE;
			
			if (isset($open_nodes_log[$z->depth]))
			{
				if ($z->name == $open_nodes_log[$z->depth])
				{
					$_is_closing_tag = TRUE;
				}
			}
			
			$open_nodes_log[$z->depth] = $z->name;
			
			// The <Cell>
			if ($z->name == 'Cell' and !$_is_closing_tag)
			{		
				$cell_count++;
				
				// Some cells may be skipped in file
				$ssIndex = $z->getAttribute('ss:Index');
				
				if ($ssIndex != NULL)
				{
					$cell_count = $ssIndex;
				}
				
				// The node should exist
				if (isset($nodesMatch[$cell_count]))
				{
					if (!$z->isEmptyElement and ($cell_contents = trim(preg_replace('#\s#',' ', $z->readString()))) != '')
					{
						$insert_line[$nodesMatch[$cell_count]] = $cell_contents;
					}
				}
			}
			
			// Opening <Row>
			elseif ($z->name == 'Row' and !$_is_closing_tag)
			{
				// Prep new array for storing values
				$insert_line = array(); 
				
				// Reset cell counter
				$cell_count = 0;
				
				// Add to line counter
				$line_count++;
			}
			
			// Closing </Row>
			elseif ($z->name == 'Row' and $_is_closing_tag)
			{
				// Is there something to insert? And the line is not the first one?
				if (count($insert_line) > 0 and $line_count > 1)
				{
					// Title
					if (!isset($insert_line['title']))
					{
						echo "[Строка #$line_count] Строка пропущена: Нет названия.\n";
						continue;
					}
					else
					{
						// Convert special chars
						$insert_line['title'] = htmlspecialchars($insert_line['title']);
						
						// Cut the length
						if (mb_strlen($insert_line['title']) > 256)
						{
							echo "[Строка #$line_count] Название сокращено до длины 256 символов.\n";
							$insert_line['title'] = mb_substr($insert_line['title'], 0, 256);
						}
					}
					
					// Article number
					if (!isset($insert_line['meta']))
					{
						echo "[Строка #$line_count] Строка пропущена: Нет артикульного номера.\n";
						continue;
					}
					else
					{
						// Let's generate clear article number
						$insert_line['meta'] = $this->appflow->qprep($insert_line['meta'], 'art_nr');
					}
					
					// Brand
					if (empty($insert_line['meta2']))
					{
						$insert_line['meta2'] = NULL;
					}
					else
					{
						$insert_line['meta2'] = htmlspecialchars($insert_line['meta2']);
					}
					
					$insert_line['permalink'] = permalink($insert_line['title']);
					$insert_line['last_update'] = time();
					$insert_line['is_in_menu'] = 0;
					$insert_line['term_id'] = $mdata->term_id;
					$insert_line['post_type'] = $mdata->post_type;

					// Cache for future insert
					$this->import_db_batch('posts', $insert_line);
				}
				elseif ($line_count == 1)
				{
					echo "[Строка #$line_count] Пропущена, т.к. обычно содержит заголовки.\n";
				}
				else
				{
					echo "[Строка #$line_count] Пустая.\n";
				}
			}
		}
		
		// Process cached inserts. False in 2nd argument forces an insert.
		$this->import_db_batch('posts', FALSE);
		
		// Remove xml file
		//unlink($xmlfile); // [RESEARCH] Switched off for research purposes
		
		// Close XML
		$z->close();
		
		// Benchmark end
		// $this->benchmark->mark('XML_stop');
		
		// After processing we have a plenty of data
		return (object) array
		(
			// Number of <Row>s in the file. The actual line_count is reduced by 1, because the 1st row is always skipped.
			'line_count'	=> $line_count - 1,
			
			// Number of rows inserted
			'rows_inserted'	=> $this->db_batch_inserted_rows_count,
			
			// Number of rows skipped. Reduced by 1, because the 1st row is always skipped.
			'rows_skipped'	=> $line_count - $this->db_batch_inserted_rows_count - 1,
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Import DB Batch
	 *
	 * This function takes table name and an array of inserts.
	 * It accumulates lines to reduce number of db queries and performs a
	 * batch insert when qty of lines reaches certain amount. Setting the values param to
	 * false forces all accumulated lines to be inserted.
	 * Used in XMLReader-based import.
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @return	void
	 */	
	public function import_db_batch($table, $values=FALSE /* setting values to FALSE will force dbquery cache to run */)
	{		
		// Afected rows are 0 by default.
		$affected_rows = 0;
		
		// Some vals?
		if ($values)
		{
			// Accumulate!
			$this->db_batch_cache[] = $values;
		}
		
		if (($db_batch_cache_count = count($this->db_batch_cache)) >= $this->db_batch_size or (!$values and $db_batch_cache_count > 0))
		{
			// Insert what's accumulated
			$this->db->insert_batch($table, $this->db_batch_cache);
			
			// We inserted something. Let's update $affected_rows.
			$affected_rows = $this->db->affected_rows();
			
			// ...and add it to our total
			$this->db_batch_inserted_rows_count += $affected_rows;
			
			// Crosses?
			if ($table == 'crosses')
			{
				// Build crosses search tree
				$this->cross_search_build($this->db->insert_id(), $affected_rows, $this->db_batch_cache);
			}
			
			// Clean up what's accumulated
			$this->db_batch_cache = array();
		}
		
		// We always return $affected_rows. Zero will mean that nothing was inserted either because of errors, or just because we are only accumulating data.
		return $affected_rows;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Cross Search Build
	 *
	 * Builds an index tree for cross search. This is a part of crosses search optimization.
	 *
	 * @access	public
	 * @param	int
	 * @param	int
	 * @param	array
	 * @return	mixed
	 */
	public function cross_search_build ($insert_id, $affected_rows, $inserted_data)
	{
		// If nothing was inserted
		if ($insert_id < 1 or $affected_rows < 1)
		{
			return FALSE;
		}
		
		// Empty array to be filled with insert entrys
		$search_inserts = array();
		
		foreach ($inserted_data as $array_id => $inserted_line)
		{
			// Split inserted numbers by any whitespace
			$art_nrs = preg_split('#\s#', trim($inserted_line['art_numbers']));
			
			foreach ($art_nrs as $nr)
			{
				$nr = trim($nr);
				
				if (strlen($nr) > 0)
				{
					// Prep insert line for batch insert
					$search_inserts[] = array
					(
						'art_number_clear'		=> $nr,
						'line_id'							=> $array_id + $insert_id,
						'vendor_id'						=> $inserted_line['vendor_id'],
						'import_group_id'			=> $inserted_line['import_group_id'],
					);
				}
			}
		}
		
		// Insert as a batch
		$this->db->insert_batch('crosses_search', $search_inserts);
	}
	
}

/* End of file xmlimport_model.php */
/* Location: ./application/models/xmlimport_model.php */