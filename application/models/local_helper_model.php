<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Local DB Model
 *
 * Vendor management
 * Prices/Crosses Import/Retrieve
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Local_helper_model extends CI_Model {

	/// Vendors are retrieved in one call and are cached here
	var $vendors_cache						= FALSE;
	
	/// When we iterate through the list of vendors, we check if any of them has an API trigger. And, if so, we put an API ID (together with auth data) here.
	/// Non-empty array tells that we should pull data through the appropriate API
	var $vendor_apis							= array();
	
	/// Vendor statistics
	var $prices_vndr_stats_cache	= array();
	
	
	var $options_cache						= array();

	var $sitedata;

    var $local_tables_status;

	/**
	 * Constructor
	 */	
	public function __construct()
	{
    	parent::__construct();

        if (empty($this->sitedata)) {
            $q = $this->db
                -> select
                ('
				sites.adminpass,
				sites.adminemail,
				sites.title,
				sites.subtitle,
				')
                -> from('sites')
                ->limit(1)
                -> get();

            if (!empty($q) && $q->num_rows() > 0)
            {
                // fetch
                $r = $q->row();

                // cache
                $this->sitedata = $r;

                $this->local_tables_status = TRUE;
            }
            else {
                $this->local_tables_status = FALSE;
            }
        }
  	}

    function update_sitedata($newdata)
    {
        $this->db->update('sites',$newdata);
    }
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Common
	 *
	 * Allows to delete entrys while checking for special allowances.
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @param	string
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	public function delete($table, $id, $colname='id', $limit=1, $check_rights=FALSE)
	{
		// Limit number (just in case, to make deletions more safe)
		if ($limit !== FALSE)
		{
			$this->db->limit($limit);
		}
		
		// Special allowance tag (used in some tables)
		if ($check_rights)
		{
			$this->db->where('allow_delete', '1');
		}
		
		// Primary condition (colname and id)
		$this->db->where($colname, $id);
		
		// Perform Delete
		$this->db->delete($table);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clear Old Vendor Data
	 *
	 * This function takes vendor_id and new import_group_id
	 * and clears all other import_groups for Prices/Crosses/Crosses_search
	 *
	 * @access	private
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	void
	 */	
	private function clear_vendor_data($vendor_id, $new_import_group_id, $table)
	{
		// Set conditions
		$this->db->where('vendor_id', $vendor_id);
		$this->db->where('import_group_id !=', $new_import_group_id);
		
		// Perform deletion
		$this->db->delete($table);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clear Old Vendor Prices
	 *
	 * This function takes vendor_id and new import_group_id
	 * and clears all other import_groups for prices
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @return	void
	 */	
	public function clear_vendor_prices($vendor_id, $new_import_group_id)
	{
		$this->clear_vendor_data($vendor_id, $new_import_group_id, 'prices');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clear Old Crosses List
	 *
	 * This function takes vendor_id and new import_group_id
	 * and clears all other import_groups for crosses
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @return	void
	 */	
	public function clear_cross_list($vendor_id, $new_import_group_id)
	{
		$this->clear_vendor_data($vendor_id, $new_import_group_id, 'crosses');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Clear Old Crosses Search Tree
	 *
	 * This function takes vendor_id and new import_group_id
	 * and clears all other import_groups for crosses_search_tree
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @return	void
	 */	
	public function clear_cross_search($vendor_id, $new_import_group_id)
	{
		$this->clear_vendor_data($vendor_id, $new_import_group_id, 'crosses_search');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set import group
	 *
	 * This function takes vendor_id and new import_group_id
	 * and sets new_import_group_id as a current in vendors table
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @return	void
	 */	
	public function set_import_group ($vendor_id, $new_import_group_id)
	{
		// Change working import group
		$this->db->set('import_group_id', $new_import_group_id);
		
		// Set the last_update timestamp
		$this->db->set('last_update', time());
		
		// Conditions
		$this->db->where('id', $vendor_id);
		
		// Perform an update
		$this->db->update('vendors');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Optimize preformance-essential tables
	 *
	 * This function performs an optimize query on
	 * performance-essential tables. Used after import is complete.
	 *
	 * @access	public
	 * @return	void
	 */		
	public function optimize_tables()
	{
		$this->db->query("OPTIMIZE TABLE `prices`, `crosses`, `crosses_search`");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor's Unimported Stats
	 *
	 * This function takes table, vendor_id and working import_group_id
	 * and returns statistics for any unused entrys in that table
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @param	string
	 * @return	array
	 */	
	public function vendor_unimported($table, $vendor_id, $working_import_group_id)
	{
		// GET distinct import_group_ids
		$q = $this->db
			->select('import_group_id')
			->distinct()
			->where('vendor_id', $vendor_id)
			->where('import_group_id !=', $working_import_group_id)
			->get($table);
		
		// Prep an empty array to be returned
		$array = array();
		
		// Iterate through result set
		foreach ($q->result() as $r)
		{
			// Count number of entrys for this particular import_group_id
			$this->db
				->where('vendor_id', $vendor_id)
				->where('import_group_id', $r->import_group_id);
			
			// Perform a count call
			$count = $this->db->count_all_results($table);
			
			// If more than zero (it should definitely be more, but just in case)
			if ($count > 0)
			{
				// Add to array
				$array[] = (object) array
				(
					'count'				=> $count,
					'import_group_id'	=> $r->import_group_id,
				);
			}
		}
		
		return $array;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor's Data Count
	 *
	 * This function takes vendor_id, import_group_id, table and brand (optional)
	 * and returns the number of entrys in table
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	int
	 */	
	public function vendor_data_count($vendor_id, $import_group_id, $table, $brand=FALSE)
	{
		// Optional filter by brand
		if ($brand)
		{
			$this->db->where('sup_brand', $brand);
		}
		
		// Conditions
		$this->db
			->where('vendor_id', $vendor_id)
			->where('import_group_id', $import_group_id);
		
		// Perform count call and return
		return $this->db->count_all_results($table);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor Data Lookup
	 *
	 * This function takes vendor_id, import_group_id, table, page number, number per page and brand (optional)
	 * and returns the dbresult of enrtys retrieved
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	dbresult
	 */	
	public function vendor_lookup_rslt($vendor_id, $import_group_id, $page, $per_page, $table, $brand=FALSE)
	{
		// Optional filter by brand
		if ($brand)
		{
			$this->db->where('sup_brand', $brand);
		}
		
		// Conditions and limitations by page/per_page
		$this->db
			->where('vendor_id', $vendor_id)
			->where('import_group_id', $import_group_id)
			->limit($per_page, $page*$per_page);
			
		// Perform and return
		return $this->db->get($table);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor Prices Table Distinct Brands List
	 *
	 * This function takes vendor_id and import_group_id
	 * and returns the list of distinct brands contained within prices table
	 * for this vendor_id and this import_group_id.
	 * The dataset is ordered by brand.
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @return	dbresult
	 */		
	public function vendor_prices_brands_rslt($vendor_id, $import_group_id)
	{
		$this->db
			->select('sup_brand')
			->distinct()
			->where('vendor_id', $vendor_id)
			->where('import_group_id', $import_group_id)
			->order_by('sup_brand', 'asc');
		
		// Perform and return
		return $this->db->get('prices');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendors List
	 *
	 * This function takes vendor_type and datatype
	 * and returns either a dbresult or a number of entries
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */	
	public function vendors_list_result($vendor_type, $return='result')
	{
		// This is prices supplier (vendor type: default)
		if ($vendor_type == 'default')
		{
			// List essential columns
			// The second param is set to FALSE, because we have column names quoted
			$this->db->select
			(
				'`id`,
				`vendor_name`,
				`delivery_days`,
				`price_correction`,
				`structure_id`,
				`last_update`,
				`allow_delete`,
				`import_group_id`,
				`rows_cache`,
				`qtys_cache`,
				`api_id`,
				`api_key1`,
				`api_key2`,
				`is_primary`',
				
				FALSE
				
			);
			
			// Push primary to the top
			$this->db->order_by('is_primary', 'desc');
		}
		
		// This is crosses supplier (vendor type: crosses)
		elseif ($vendor_type == 'crosses')
		{
			// List essential columns
			// The second param is set to FALSE, because we have column names quoted
			$this->db->select
			(
				'`id`,
				`vendor_name`,
				`last_update`,
				`allow_delete`,
				`import_group_id`,
				`rows_cache`',
				
				FALSE
			);
		}
		
		// DB conditions
		$this->db->where('vendor_type', $vendor_type);

		// Returning result set?
		if ($return == 'result')
		{
			// Apply order, perform query and return
			return $this->db->order_by('id','asc')->get('vendors');
		}
		
		// Returning number of entries?
		elseif ($return == 'count')
		{
			// Perform and return
			return $this->db->count_all_results('vendors');
		}
		
		// For unknown return types we return FALSE
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get Vendor
	 *
	 * This function takes vendor_id and an optional columns list
	 * and returns object containing vendor's data or false if nothing is found
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @return	mixed
	 */
	public function get_vendor($id, $select=FALSE)
	{
		// Particular fields? Why select unnecessary stuff?
		if ($select)
		{
			// Limit the list of fields
			$this->db->select($select);
		}
		
		// Set id, limit to one and perform
		$q = $this->db
			->where('id',$id)
			->limit(1)
			->get('vendors');
		
		// If we got something
		if ($q->num_rows() > 0)
		{
			if (!empty($r->structure))
			{
				$r->structure = unserialize($structure);
			}
			
			$r = $q->row();
			return $r;
		}
		
		// Return false otherwise
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor ADD
	 *
	 * This function takes insert fieldset and vendor_type (optional),
	 * fills-in skipped values with defaults and performs a DB insert
	 * 
	 * @todo Migrate 'magic numbers' to properties or settings
	 *
	 * @access	public
	 * @param	array
	 * @param	string
	 * @return	void
	 */
	public function add_vendor($ins, $vendor_type='default')
	{
		// No vendor_name?
		if (!isset($ins['vendor_name']) or $ins['vendor_name'] == '')
		{
			$ins['vendor_name']				= 'Новый поставщик';
		}
		
		// No delivery_days?
		if (!isset($ins['delivery_days']) or $ins['delivery_days'] == '')
		{
			$ins['delivery_days']			= '0';
		}
		
		// No price_correction?
		if (!isset($ins['price_correction']) or $ins['price_correction'] == '')
		{
			$ins['price_correction']	= '1.00';
		}
		
		// No Structure id?
		if (!isset($ins['structure_id']) or $ins['structure_id'] == '')
		{
			$ins['structure_id']			= '1';
		}
		
		if (empty($ins['struct_art_number']))
			$ins['struct_art_number'] = 1;
			
		if (empty($ins['struct_sup_brand']))
			$ins['struct_sup_brand'] = 2;
			
		if (empty($ins['struct_description']))
			$ins['struct_description'] = 3;
			
		if (empty($ins['struct_qty']))
			$ins['struct_qty'] = 4;
			
		if (empty($ins['struct_price']))
			$ins['struct_price'] = 5;
			
		if (empty($ins['orderemail']))
			$ins['orderemail'] = NULL;
			
		if (empty($data['ordername']))
			$data['ordername'] = NULL;
			
		
		
		
		// Compile insert
		$insert = array
		(
			'vendor_name'					=> $ins['vendor_name'],
			'delivery_days'				=> preg_replace('#\D#', '', $ins['delivery_days']),
			'price_correction'		=> $ins['price_correction'],
			'vendor_type'					=> $vendor_type,
			'structure_id'				=> $ins['structure_id'],
			'struct_art_number'		=> $ins['struct_art_number'],
			'struct_sup_brand'		=> $ins['struct_sup_brand'],
			'struct_description'	=> $ins['struct_description'],
			'struct_qty'					=> $ins['struct_qty'],
			'struct_price'				=> $ins['struct_price'],
			'last_update'					=> 0,
			'api_key1'						=> $ins['api_key1'],
			'api_key2'						=> $ins['api_key2'],
			'orderemail'					=> $ins['orderemail'],
			'ordername'						=> $ins['ordername'],
		);
		
		// Set api_id if neccessary
		if ($ins['api_id'] != '0')
		{
			$insert['api_id'] = $ins['api_id'];
		}
		
		$this->db->insert('vendors', $insert);
	}
	
	public function vendor_price_structure($vend)
	{
		$vend = (array) $vend;
		$structure_fields = array('art_number', 'sup_brand', 'description', 'qty', 'price');
		$nodesMatch  = array();
		
		foreach ($structure_fields as $sfield)
		{
			if (empty($vend['struct_' . $sfield]))
			{
				return NULL;
			}
			
			// There should be no duplicates in nodesMatch
			if (isset($nodesMatch[$vend['struct_' . $sfield]]))
			{
				// echo $vend['struct_' . $sfield];
				return NULL;
			}
			
			// echo "K: " . $vend['struct_' . $sfield] . ' = ' . $sfield . "<br>";
			$nodesMatch[$vend['struct_' . $sfield]] = $sfield;
		}
		
		if (count($nodesMatch) == 5)
			return $nodesMatch;
		
		return NULL;
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor UPDATE
	 *
	 * Updates vendor data (limiting the list of cols allowed to be updated)
	 *
	 * @access	public
	 * @param	int
	 * @param	array
	 * @return	void
	 */
	public function update_vendor($id, $data)
	{
		// Unset, if neccessary
		if ($data['api_id'] == '0')
			$data['api_id'] = NULL;
		
		if (empty($data['orderemail']))
			$data['orderemail'] = NULL;
			
		if (empty($data['ordername']))
			$data['ordername'] = NULL;
		
		// Limit to this vendor_id
		$this->db->where('id', $id);
		
		// Perform an update
		$this->db->update('vendors', $data);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Crosses joined array
	 *
	 * Searches for crosses for given article or articles list
	 *
	 * @access	public
	 * @param	mixed
	 * @return	array
	 */
	public function crosses_joined_array($arts)
	{
		// prep import group ids
		$i_ids_array = $this->import_group_ids_array();
		
		// List of articles
		if (is_array($arts))
		{
			// Create empty array
			$art_nrs = array();
			
			foreach ($arts as $art)
			{
				$art_nrs[] = $art->number_clear;
			}
			
			// DB where_in routine for numbers list
			$this->db->where_in('art_number_clear', $art_nrs);
		}
		
		// Single article number
		else
		{
			// DB where for clear art number
			$this->db->where('art_number_clear', $arts);
		}
		
		// Distinct
		$this->db->distinct();
		
		// Select only art_numbers column
		$this->db->select('`art_numbers`', FALSE); 
		
		// Conditions
		$this->db->where_in('crosses_search.import_group_id', $i_ids_array);
		$this->db->where('crosses_search.line_id=crosses.id', NULL, FALSE);
		$this->db->from('`crosses`, `crosses_search`');
		
		// Perform query
		$q = $this->db->get();
		
		// Prep an empty array
		$crosses = array();
		
		// If we got something
		if ($q->num_rows() > 0)
		{			
			foreach ($q->result() as $r)
			{
				// Crosses in DB are separated by space
				$crosses_line = explode(' ', trim($r->art_numbers));
				
				// Put together
				$crosses = array_merge($crosses, $crosses_line);
			}
		}
		
		// Return
		return $crosses;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prices lookup (logic number 2)
	 *
	 * We take articles array and skim through prices
	 * table to get what's in stock
	 *
	 * @access	public
	 * @param	mixed
	 * @param	bool
	 * @return	mixed
	 */
	public function prices_v2($arts, $rebuild_arts_array=FALSE /* works when $arts is array */, $is_join_vendors = false /* works when $arts is just string */)
	{		
		// prep import group ids
		$i_ids_array = $this->import_group_ids_array();
		
		// new arts array. it's just a rebuild with artid as key
		$new_arts_array = array();
		
		// Array with numbers
		$art_nrs_array = array();
		
		// prep art numbers for query
		// multiple numbers
		if (is_array($arts))
		{
			// Iterate through input
			foreach ($arts as $art)
			{
				// There can be complete article objects
				if (is_object($art))
				{
					$nr = (string) $art->number_clear;
				}
				
				// Or just article numbers (AZ1232 is also meant to be a number)
				else
				{
					// We cast string for the case when article number starts with zero
					$nr = (string) $art;
				}
				
				// If the number is not already in array
				if (!in_array($nr, $art_nrs_array, TRUE))
				{
					// Add to array
					$art_nrs_array[] = $nr;
					
					// If rebuilt array is requested
//					if ($rebuild_arts_array)
//					{
//						$new_arts_array[strtoupper($nr)] = $art;
//					}
				}
			}

			// If we've got something
			if (count($art_nrs_array) > 0)
			{
				// Prep a DB where_in routine
				$this->db->where_in('art_number_clear', $art_nrs_array);
			}
			// if we didn't get anything, request without where will pull the whole table, what isn't good.
			else
			{
				return FALSE;
			}
		}
		// One single number
		else
		{
			// Put into an array
			$art_nrs_array[] = $arts;
			
			// DB where routine
			$this->db->where('art_number_clear', $arts);
		}
		
		// Db conditions
		if ($is_join_vendors)
		{
			$this->db->select
			('
				prices.id,
				prices.art_number,
				prices.art_number_clear,
				prices.sup_brand,
				prices.vendor_id,
				prices.description,
				prices.qty,
				prices.price,
				vendors.vendor_name,
				vendors.delivery_days
			');
			
			$this->db->join('vendors', 'prices.vendor_id = vendors.id');
		}
		else
		{
			$this->db->select('`id`, `art_number_clear`, `art_number`, `sup_brand`, `vendor_id`, `description`, `qty`, `price`', FALSE);
		}
		$this->db->where_in('prices.import_group_id', $i_ids_array);

		// Limit to 500 just in case (as prices table is very heavy)
		$this->db->limit(500);
		
		// Perform query
		$q = $this->db->get('prices');
		
		// Return an object
		return (object) array
		(
			'prices_result'		=> $q,
//			'arts_rebuilded'	=> $new_arts_array,
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prices Row
	 *
	 * Gets single row from prices table, specified by ID
	 *
	 * @access	public
	 * @param	int $rowid
	 * @param	string $datatype
	 * @return	mixed
	 */
	public function prices_row($rowid, $datatype='object')
	{		
		// Where
		$this->db
			->where('prices.id', $rowid);
			
		// Limit to 1. Just in case.
		$this->db->limit(1);
		
		// Specify the table
		$this->db->from('prices');
		
		if ($datatype == 'object')
		{
			// Limit the list of fields to be selected
			$this->db->select
			('
				id, 
				art_number,
				art_number_clear,
				sup_brand, 
				vendor_id, 
				description, 
				qty, 
				price
			');
		}
		elseif ($datatype == 'object_extended')
		{
			$this->db->select
			('
				prices.id,
				prices.art_number,
				prices.art_number_clear,
				prices.sup_brand,
				prices.vendor_id,
				prices.description,
				prices.qty,
				prices.price,
				vendors.vendor_name,
				vendors.delivery_days
			');
			
			$this->db->join('vendors', 'prices.vendor_id = vendors.id');
			
		}
		elseif ($datatype == 'count')
		{
			// return number of rows
			return $this->db->count_all_results();
		}
			
		// If datatype is not count, - Perform query
		$q = $this->db->get();
		
		// If we got nothing
		if ($q->num_rows() == 0)
			return FALSE;
		
		// return object containing row (if we didn't return false in previous statement)
		return $q->row();
	}
	
	public function prices_row_find ($brand, $number)
	{
		$this->db->select('id');
		
		// Where
		$this->db
			->where('art_number_clear', $number)
			->where('sup_brand', $brand);
			
		// Limit to 1. Just in case.
		$this->db->limit(1);
		
		// Run
		$q = $this->db->get('prices');
		
		if ($q->num_rows() > 0)
		{
			return $q->row()->id;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prices By Brand
	 *
	 * Get prices table entrys filtered by brand column.
	 *
	 * @todo Move 'magic numbers' to top
	 *
	 * @access	public
	 * @param	string
	 * @return	object
	 */
	public function prices_by_brand($brand)
	{
		// For prototyping purposes we limit the number per page
		$num_per_page = 3000;
		
		// Prep import group ids
		$i_ids_array = $this->import_group_ids_array();
		
		// DB Conditions
		$this->db->select('`id`, `art_number_clear`, `art_number`, `sup_brand`, `description`', FALSE);
		
		$this->db->where_in('import_group_id', $i_ids_array);
		$this->db->where('sup_brand', $brand);

		$this->db->group_by(array('art_number_clear','sup_brand'));
		
		$this->db->order_by('art_number_clear');
		
		// For prototyping purposes we limit the number per page
		$this->db->limit($num_per_page);
		
		// Perform a query
		$q = $this->db->get('prices');
		
		// Return an object containing dbresult
		return (object) array
		(
			'prices_result'	=> $q
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prices by Description
	 *
	 * Gets prices enrtys which contain the description
	 * 
	 * @warning Experimental. Not currently in use.
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	object
	 */
	public function prices_by_description($description, $pagenum=0)
	{
		$num_per_page = 1000;
		
		// prep import group ids
		$i_ids_array = $this->import_group_ids_array();
		
		// This stuff will be used with count_all and with get results portion
		$this->db->start_cache();
		$this->db->where_in('import_group_id', $i_ids_array);
		$this->db->where('MATCH (description) AGAINST ("'.$description.'")', NULL, FALSE);
		$this->db->group_by(array('art_number_clear','sup_brand'));
		$this->db->stop_cache();
		
		// Count
		$num_found = $this->db->count_all_results('prices');
		
		// Get result portion
		$this->db->select('`art_number_clear`,`art_number`,`id`,`sup_brand`, `description`', FALSE);
		$this->db->order_by('art_number_clear');
		$this->db->limit($num_per_page, $num_per_page * $pagenum);

		$q = $this->db->get('prices');
		
		// Flush cache
		$this->db->flush_cache();
		
		return (object) array
		(
			'prices_result'	=> $q,
			'num_per_page'	=> $num_per_page,
			'num_found'			=> $num_found
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prices Vendor Statistics
	 *
	 * Gets various statistical information based on vendors table data
	 *
	 * @access	public
	 * @param	string
	 * @return	object
	 */
	public function prices_vndr_stats($stattypes = array('last_update','delivery_days','qtys'))
	{
		// Empty array to be returned
		$array = array();
		
		// If single string is supplied
		if (!is_array($stattypes))
		{
			$stattypes = array($stattypes);
		}
		
		// Iterate through the list of desired stat types
		foreach ($stattypes as $stype)
		{
			// If requested data is in the cache
			if (isset($this->prices_vndr_stats_cache[$stype]))
			{
				$array[$stype] = $this->prices_vndr_stats_cache[$stype];
			}
			
			// Get requested data from DB
			else
			{
				if ($stype == 'last_update')
				{		
					$q = $this->db
						->select_max('last_update')
						->get('vendors');
					
					$array[$stype] = $q->row()->last_update;
				}
				elseif ($stype == 'delivery_days')
				{		
					$q = $this->db
						->select_min('delivery_days')
						->get('vendors');
						
					$array[$stype] = $q->row()->delivery_days;
				}
				elseif ($stype == 'qtys')
				{		
					$q = $this->db
						->select_sum('qtys_cache')
						->get('vendors');
						
					$array[$stype] = $q->row()->qtys_cache;
				}
				elseif ($stype == 'rows')
				{		
					$q = $this->db
						->select_sum('rows_cache')
						->get('vendors');
						
					$array[$stype] = $q->row()->rows_cache;
				}
				
				// Cache data retrieved
				$this->prices_vndr_stats_cache[$stype] = $array[$stype];
			}
		}
		
		// Return data
		return (object) $array;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prices vendor Stats Cache
	 *
	 * Stores Prices Statistical info to vendors table (for a particular vendor)
	 * Usually after prices update
	 *
	 * @access	public
	 * @param	int
	 * @return	void
	 */
	public function prices_vndr_stats_cache($vendor_id)
	{
		$where = array
		(
			'vendor_id'		=> $vendor_id,
		);
		
		// DB conditions to get Quantities total
		$q = $this->db
			->select_sum('qty')
			->where($where)
			->get('prices');
		
		// Quantities total
		$qtys = $q->row()->qty;
		
		// Rows total
		$rows = $this->db
			->where($where)
			->count_all_results('prices');
		
		// Save this stuff
		$this->db->where(array
		(
			'id'			=> $vendor_id,

		))->update('vendors', array
		(
			'rows_cache'	=> (!empty($rows))?$rows:0,
			'qtys_cache'	=> (!empty($qtys))?$qtys:0,
			
		));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Crosses vendor Stats Cache
	 *
	 * Stores Crosses Statistical info to vendors table (for a particular vendor)
	 * Usually after crosses update
	 *
	 * @access	public
	 * @param	int
	 * @return	void
	 */
	public function crosses_vndr_stats_cache($vendor_id)
	{
		$where = array
		(
			'vendor_id'		=> $vendor_id,
		);
		
		// Get number of rows
		$rows = $this->db->where($where)->count_all_results('crosses');
		
		// Save this stuff
		$this->db->where(array
		(
			'id'			=> $vendor_id,

		))->update('vendors', array
		(
			'rows_cache'	=> $rows,
			
		));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Import_group_ids as string
	 *
	 * Gets all used import_group_ids and produces a DB where string
	 *
	 * @access	public
	 * @return	string
	 */
	public function import_group_ids_string()
	{
		// Get vendors as array
		$vendors = $this->_vendors_array('all');
		
		// Prep empty string for return
		$string			= '';
		$line_count		= 0;

		foreach ($vendors as $vendor)
		{
			// Unite multiples with OR
			if ($line_count > 0)
			{
				$string .= ' OR ';
			}
			
			// Add to string
			$string .= "`import_group_id`='".$vendor->import_group_id."'";
			
			// Line count
			$line_count++;
		}
		
		// Return
		return $string;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Import_group_ids as string (logic number 2)
	 *
	 * Gets all used import_group_ids and produces a DB where_in string
	 *
	 * @access	public
	 * @return	string
	 */
	public function import_group_ids_string_v2()
	{
		// Get vendors as array
		$vendors = $this->_vendors_array('all');
		
		// Prep empty string for return
		$string			= '';
		$line_count		= 0;
		
		foreach ($vendors as $vendor)
		{
			// Unite multiples with comma
			if ($line_count > 0)
			{
				$string .= ', ';
			}
			
			// Add to string
			$string .= "'".$vendor->import_group_id."'";
			
			// Line count
			$line_count++;
		}
		
		// Return
		return $string;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Import_group_ids as array
	 *
	 * Gets all used import_group_ids and produces an array
	 *
	 * @access	public
	 * @return	array
	 */
	public function import_group_ids_array()
	{
		$vendors = $this->_vendors_array('all');
		
		$ids = array();
		
		foreach ($vendors as $vendor)
		{
			$ids[] = $vendor->import_group_id;
		}
		
		if (empty($ids))
			return NULL;
		else
			return $ids;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor GET
	 *
	 * Get a vendor by ID. This function gets all vendors from DB
	 * and returns data for a requested one.
	 *
	 * @todo Check neccesity of all-select and of returning the first one.
	 * @todo Why do we return $this->vendors_cache[1]?;
	 *
	 * @access	public
	 * @param	int
	 * @return	object
	 */
	public function vendor($vendor_id)
	{
		$this->_vendors_array('all');
		
		if (isset($this->vendors_cache[$vendor_id]))
		{
			return $this->vendors_cache[$vendor_id];
		}
		else
		{
			return $this->vendors_cache[1];
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendors Array
	 *
	 * Utility function for ::vendor()
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	public function _vendors_array($mode='all')
	{
		// If not cached, - create cache entry
		if ($this->vendors_cache === FALSE)
		{
			$q = $this->db->get('vendors');
			
			// Prep an empty array
			$vendors = array();
			
			foreach ($q->result() as $r)
			{
				// Is there any API associated? We don't check $r->api_key2 for presence, as it may be optional
				if (isset($r->api_id, $r->api_key1))
				{
					// Put it to the list, for future reference.
					$this->vendor_apis[$r->id] = (object) array
					(
						'api_id'			=> $r->api_id,
						'api_key1'		=> $r->api_key1,
						'api_key2'		=> $r->api_key2,
					);
				}
				
				// Put into array with id as a key
				$vendors[$r->id] = $r;
			}
			
			// Store
			$this->vendors_cache = $vendors;
		}
		
		// Retrun
		return $this->vendors_cache;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * DB Result Walker
	 *
	 * This is a default walker function for database result
	 *
	 * @access	public
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	public function _arr($q)
	{
		
		$array = array();
		
		foreach ($q->result() as $r)
		{
			$array[] = $r;
		}
		
		return $array;
	}

	public function get_payment_details_page ()
	{
		$q = $this->db
			->select('id')
			->where('permalink', 'payment')
			->where('post_type','page')
			->where('allow_delete',0)
			->get('posts');
			
		// we need only one result
		if ($q->num_rows() == 1)
		{
			return $q->row();
		}
		
		return false;
	}
}

/* End of file local_helper_model.php */
/* Location: ./application/models/local_helper_model.php */