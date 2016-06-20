<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Application Flow Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Appflow_model extends CI_Model {
	
	/// Breadcrumbs cache
	var $breadcrumbs;
	
	/// We use this in <title></title> as a base
	var $pagetitle_base;
	
	/// Different parts of the template are generated throughout the controller classes. We keep them here.
	var $hdata;
	
	/// @experimental
	var $jdata;
	
	/// Path to the template
	var $template_path = 'front';
	
	/// Initial query bits
	/// To be reused in links and breadcrumbs
	var $q;
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();	
	
		$this->pagetitle_base = lang('jb_appflow_pagetitle_base');
		
		// Set hdata to empty object
		$this->hdata = (object) array();
		
		// Bodyclass (empty by default)
		$this->hdata->bodyclass = '';
		
		// Javascript data
		$this->hdata->js_data	= ' data-jsbm_urlbase="' . site_url('autoparts') . '"';
		
		// Author tag in template
		$this->hdata->meta_author = '<meta name="author" content="PartsDB.info // @Marketto">';
		
		// Timestamp tag in template
		$this->hdata->meta_timestamp = '<meta name="jb-timestamp" content="' . time() . '">';
		
		// Bootstrap CSS & JS
		$this->hdata->bootstrap_css			= '//netdna.bootstrapcdn.com/bootstrap/2.3.2/css/bootstrap.min.css';
		$this->hdata->bootstrap_js			= '//netdna.bootstrapcdn.com/bootstrap/2.3.2/js/bootstrap.min.js';
		
		// General CSS & JS
		$this->hdata->general_css			= '/e/front.css/general.css?213';
		$this->hdata->general_js			= '/e/front.js/general.js?217-1';

		// Admin CSS & JS
		$this->hdata->admin_css				= '/e/admin.css/general.css?210';
		$this->hdata->admin_js				= '/e/admin.js/general.js?210';
		
		// jQuery
		$this->hdata->jquery_js				= '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js';
		$this->hdata->jquery_ui_js			= '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js';
		
		// Content
		$this->hdata->content				= '';
		
		// Generate title
		$this->hdata->pagetitle				= $this->pagetitle();
		
		// Mobile is false by default
		$this->hdata->is_mobile = false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Qprep
	 *
	 * Sanitizer for parameters. Contains patterns for various datatypes.
	 *
	 * @param	string $data
	 * @param	string $context
	 * @return	string|bool
	 */	
	public function qprep($data, $context)
	{
		// Define patterns
		// pattern_id => array(preg pattern, additional function or array of additional functions, default value)
		$pregs = array
		(
			// prices_row_id - only digits
			'prices_row_id'		=> array('#\D#', FALSE),
			
			// art_id - only digits
			'art_id'					=> array('#\D#', FALSE),
			
			// art_nr - numbers and letters
			'art_nr'					=> array('#\W#', 'strtolower'),
			
			// sup_brand - numbers and letters
			'sup_brand'				=> array('#\W#u', 'mb_strtolower'),
			
			// qty - numbers
			'qty'							=> array('#\D#', FALSE, 100),
			
			// delivery days - numbers
			'ddays'						=> array('#\D#', FALSE, 5),
		);
		
		// If pattern exists
		if (isset($pregs[$context]))
		{
			if ($context == 'sup_brand')
			{
				$data = string_remove_umlauts($data);
			}
			
			// Apply pattern
			$data = preg_replace($pregs[$context][0], '', $data);
			
			// Additional function?
			if ($pregs[$context][1] and !is_array($pregs[$context][1]))
			{
				$function = $pregs[$context][1];
				$data = $function($data);
			}
			// List of additional functions?
			elseif ($pregs[$context][1])
			{
				foreach ($pregs[$context][1] as $function)
				{
					$data = $function($data);
				}
			}
			
			// String empty? Default value exists?
			if (strlen($data) == 0 and isset($pregs[$context][2]))
			{
				$data = $pregs[$context][2];
			}
			
			// Return
			return $data;
		}
		
		// Return false in case pattern doesn't exist
		return false;
	}
	
	// --------------------------------------------------------------------

	/**
	 * @param $thistitle
	 * @param array $replace_data
	 * @return string
	 */
	public function pagetitle($thistitle = false, $replace_data = array())
	{
		$title = '';
		
		if ($thistitle)
		{
			$title .= $this->custom_title($thistitle, $replace_data) . ' - ';
		}
			
		$title .= _jb_sitedata('title');
		
		if (!$thistitle)
			$title .= ' - ' . _jb_sitedata('subtitle');
		
		return $title;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Custom Title
	 *
	 * 
	 *
	 * @param	string $data
	 * @param	string $context
	 * @return	string|bool
	 */	
	public function custom_title($thistitle, $replace_data = array())
	{
		$replace_from = array();
		$replace_to = array();
		
		if (!empty($replace_data))
		{
			foreach ($replace_data as $k=>$v)
			{
				$replace_from[] = '%' . $k;
				$replace_to[] = $v;
			}
			
			$thistitle = str_replace($replace_from, $replace_to, $thistitle);
		}
		
		return $thistitle;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Is Permalink Used
	 *
	 * Checks if permalink is already present.
	 * Used when new permalink is being generated.
	 *
	 * @param	string $permalink
	 * @return	bool
	 */	
	public function is_permalink_used($permalink, $table)
	{
		if ($this->db->where('permalink', $permalink)->count_all_results($table) == 0)
		{
			return FALSE;
		}
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Unique Permalink
	 *
	 * Generates an unique permalink. Adds number after the link if the desired permalink is already in use.
	 *
	 * @param	string $desired_permalink
	 * @return	string
	 */	
	public function unique_permalink($desired_permalink, $table)
	{
		$counter = 1;
		$permalink = $desired_permalink;
		
		while ($this->is_permalink_used($permalink, $table))
		{
			$permalink = "$desired_permalink-$counter";
			$counter++;
		}
		
		return $permalink;
	}

	public function breadcrumbs($breadcrumb_array) {
		$breadcrumbs_prepped = array();

		foreach ($breadcrumb_array as $brc) {
			$brc->link = str_replace('api/v2/', '', $brc->link);

			if (strpos($brc->link, '/') !== 0)
				$brc->link = '/' . $brc->link;

			$breadcrumbs_prepped[] = $brc;
		}

		return $breadcrumbs_prepped;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Content
	 *
	 * 
	 *
	 * @param	string $data
	 * @param	string $context
	 * @return	string|bool
	 */	
	public function cnt ($view_file, $data = array())
	{
		$this->hdata->content .= $this->load->view($view_file, $data, TRUE);
	}
	
	public function tmplt ()
	{
		if ($this->template_path == 'frontend.common')
		{
			if (($template = $this->options->get('template')) !== FALSE)
			{
				$this->parser->parse_string($template, (array) $this->hdata);
			}
			else
			{
				$this->parser->parse($this->template_path . '/template', (array) $this->hdata);
			}
		}
		else
		{
			$this->parser->parse($this->template_path . '/template', (array) $this->hdata);
		}
	}
	
	public function stats_search_store($q)
	{
		$this->db->insert('stats_search', array
		(
			'q'					=> $q,
			'timestamp'	=> time(),
		));
	}
	
	public function stats_search_get($period='1 month')
	{
		$timediff = strtotime($period, 0);
		
		if (!$timediff)
			$timediff = 2678400;
			
		$timeafter = time() - $timediff;
		
		$q = $this->db
			->where("timestamp >=", $timeafter)
			->order_by('q', 'asc')
			->order_by('timestamp', 'desc')
			->get('stats_search');
			
		$array = array();
		
		foreach ($q->result() as $r)
		{
			$r->date_r = date(lang('jb_date_format') . ' ' . lang('jb_time_format'), $r->timestamp);
			$array[] = $r;
		}
		
		return $array;
	}
}

/* End of file appflow_model.php */
/* Location: ./application/models/appflow_model.php */