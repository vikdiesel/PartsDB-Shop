<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stock Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Stock_model extends CI_Model {
	
	var $arts_keyvals = array();
	var $stock = array();
	var $numbers = array();
	var $brands = array();
	var $stock_num_rows = null;
    var $all_brands_are_similar = true;

	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Digest
	 *
	 * Gathers up all stock data and returns a set of structured arrays together with statistical data
	 *
	 * @param	array $arts
	 * @param	bool $use_crosses
	 * @param	obj|bool $primary_article
	 * @return	obj
	 */	
	public function digest($arts, $use_crosses = FALSE, $primary_article = FALSE)
	{
//		if (empty($arts)) {
//			$arts = array($primary_article);
//		}

		// Check authorisation ('cause we offer price discounts for some users)
		if (!empty($this->access) and $this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		// Extend $arts with crosses
//		if ($use_crosses)
//		{
//			$arts = $this->extend_with_crosses($arts);
//		}

		// Grab Prices for each Article ID with one query
		$this->_get_prices_result($arts, $discount, $primary_article, true);

		// Pull vendors (required for `is_require_apis` to be set properly)
		$this->local->_vendors_array('all');
		
		// Used by JS module to query particular vendors. Empty by default.
		$vendor_api_ids	= array();
        $vendor_api_ids_imploded = '';
		
		// Gives signal to pull apis. False by default.
		$is_require_apis = FALSE;

		// Do we have apis to be pulled?
		if (($num_vendor_apis = count($this->local->vendor_apis)) > 0)
		{
			$is_require_apis = TRUE;

			foreach ($this->local->vendor_apis as $vendor_id=>$api_params)
			{
				$vendor_api_ids[] = $vendor_id;
			}

            $vendor_api_ids_imploded = implode(' ', $vendor_api_ids);
		}

		return (object) array
		(
			'in_stock'							=> $this->stock,
			'num_in_stock'						=> $this->stock_num_rows,
			
			'not_in_stock'						=> $this->arts_keyvals,
			'num_not_in_stock'					=> count($this->arts_keyvals),
			
			'stock_artnrs'						=> $this->numbers,
			'stock_brands'						=> $this->brands,
			'all_brands_are_similar'			=> $this->all_brands_are_similar,
			'discount'							=> $discount,
			
			'is_require_apis'					=> $is_require_apis,
			'api_list'							=> $this->local->vendor_apis,
			'api_ids_list'						=> $vendor_api_ids,
			'api_list_imploded'					=> $vendor_api_ids_imploded,
		);
	}


	public function _get_prices_result($arts, $discount = 0, $primary_article = null, $is_join_vendors = true)
	{
		// prep import group ids
		$i_ids_array = $this->local->import_group_ids_array();

        $this->numbers = array();
        $this->brands = array();
		$this->stock = array();

        // Prep the brand warning trigger
        $this->all_brands_are_similar = TRUE;

		//
		$this->arts_keyvals = array();

		if (empty($arts) || !is_array($arts))
			return null;

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
			if (!in_array($nr, $this->numbers, TRUE))
			{
				// Add to array
                $this->numbers[] = $nr;
			}

			if (!empty($art->brand_clear) && !empty($art->number_clear))
				$this->arts_keyvals[$art->brand_clear . $art->number_clear] = $art;
			else
				$this->arts_keyvals[] = $art;
		}

		// Prep a DB where_in routine
		$this->db->where_in('art_number_clear', $this->numbers);

		// Other DB conditions
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

        $this->stock_num_rows = $q->num_rows();

		if ($this->stock_num_rows > 0) {
			foreach ($q->result() as $r) {
				$r->sup_brand_clear = $this->appflow->qprep($r->sup_brand, "sup_brand");
				$r->art_number_clear = $this->appflow->qprep($r->art_number, "art_nr");

				$r->idkey = $r->sup_brand_clear . $r->art_number_clear;

                if (!empty($this->arts_keyvals[$r->idkey])) {
                    $stock_art = clone $this->arts_keyvals[$r->idkey];
                }
                else {
                    $stock_art = new stdClass();

                    if (count($arts) == 1 && !empty($primary_article)) {
                        $stock_art->number = $primary_article->number;
                        $stock_art->brand = $primary_article->brand;
                        $stock_art->number_clear = $primary_article->number_clear;
                        $stock_art->brand_clear = $primary_article->brand_clear;
                    }
                }

                $stock_art->number_prc = $r->art_number;
                $stock_art->number_prc_clear = $r->art_number_clear;
                $stock_art->brand_prc = $r->sup_brand;
                $stock_art->brand_prc_clear = $r->sup_brand_clear;
                $stock_art->prices_row_id = $r->id;
                $stock_art->name_prc = $r->description;
                $stock_art->qty = $r->qty;
                $stock_art->price = $r->price;
                $stock_art->vendor_id = $r->vendor_id;
                $stock_art->vendor_name = $this->local->vendor($r->vendor_id)->vendor_name;
                $stock_art->vendor_delivery_days = $this->local->vendor($r->vendor_id)->delivery_days;
                $stock_art->vendor_last_update_readable = date('d.m.Y', $this->local->vendor($r->vendor_id)->last_update);
                $stock_art->discount = $discount;

                if ($discount > 0)
                {
                    $stock_art->discount_price = $r->price * (1 - $discount/100);
                }
                else
                {
                    $stock_art->discount_price = $r->price;
                }

                // Check Brands for matching
                $stock_art->brands_match = $this->is_similar_brands($stock_art->brand_prc_clear, $stock_art->brand_clear);

                if ($stock_art->brands_match === FALSE or $stock_art->brands_match === 'neutral')
                {
                    $this->all_brands_are_similar = FALSE;
                }

                // Mark as primary
                if (!empty($primary_article) && $stock_art->brands_match === TRUE && $primary_article->number_clear == $stock_art->number_clear)
                    $stock_art->primary = TRUE;
                else
                    $stock_art->primary = FALSE;

                // no duplicates from prices
                $this->stock[$r->id] = $stock_art;

                // simply brands list
                if (!in_array($stock_art->brand_clear, $this->brands))
                    $this->brands[] = $stock_art->brand_clear;
			}

			return true;
		}



		// Return
		return false;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Extend With Crosses
	 *
	 * Gathers-up available crosses for the list of articles
	 *
	 * @param	array $arts
	 * @return	array
	 */	
	public function extend_with_crosses($arts)
	{
		$crosses = $this->local->crosses_joined_array($arts);
		
		if (count($crosses) > 0)
		{
			foreach ($crosses as $cross_nr)
			{
				$new_art = new stdClass();
				
				$new_art->number = $cross_nr;
				$new_art->number_clear = $cross_nr;
				$new_art->brand = NULL;
				$new_art->brand_clear = NULL;
				$new_art->name = NULL;
				$new_art->status = 'Cross';
				
				// Maybe, someday we'll need to identify cross-articles somehow ...
				$new_art->cross_article = TRUE;
				
				// Add it to the array
				$arts[] = $new_art;
			}
		}
		
		return $arts;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Is Similar Brands
	 *
	 * Performs specific logic to determine whether brands given look similar, or not.
	 * In case, we have `KNECHT` and `Knecht-Mahle` for instance.
	 *
	 * @todo Improvements required.
	 * @todo Letters with umlaut and without umlaut do not give match
	 *
	 * @param	string $brand_1
	 * @param	string $brand_2
	 * @return	bool
	 */	
	function is_similar_brands($brand_1, $brand_2)
	{
        if (empty($brand_1) or empty($brand_2))
			return 'neutral';
			
		$len_1 = mb_strlen($brand_1);
		$len_2 = mb_strlen($brand_2);
		
		if ($len_1 > $len_2)
		{
			$needle		= $brand_2;
			$haystack	= $brand_1;
		}
		else
		{
			$needle		= $brand_1;
			$haystack	= $brand_2;
		}
		
		if (preg_match("/^$needle/Ui", $haystack))
		{	
			return TRUE;
		}
		
		return FALSE;
	}
}

/* End of file stock_model.php */
/* Location: ./application/models/stock_model.php */