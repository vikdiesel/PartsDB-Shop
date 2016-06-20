<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cart Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Cart_model extends CI_Model
{
	/// Number of distinct items currently in the cart
	var $num_items = 0;
	
	/// Number of items currently in the cart
	var $total_items = 0;
	
	/// Validator Salt
	var $md5_salt = 'ls.3`cm2`azK)*W.]i]X{X8I2*A8AaQ[2,*gCp,/Txzp+FnDU-O2#8M4mtFsce%Y';
	
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
	 * List Items
	 *
	 * Lists items currently in the cart. Calculates the total
	 *
	 * @todo cart_id in the return set is probably not necessary
	 *
	 * @param	string $cart_id
	 * @return	obj
	 */	
	public function list_items()
	{
		// Grab session_id
		$session_id = $this->session->userdata('session_id');
		
		// Prep empty array for cart items
		$array = array();
		
		// Prep total amount
		$total = 0;
		
		// Query DB
		$q = $this->db
			
			->where('cartid', $session_id)
			->get('order_items');
			
		// Iterate through results (iteration will not start if we've got an empty set)
		foreach ($q->result() as $r)
		{
			// Limit quantity to quantity available in stock
			if ($r->qty > $r->qty_limit)
			{
				$r->qty = $r->qty_limit;
			}
			
			// Calculate the sub total
			$r->subtotal = $r->price * $r->qty;
		
			// Add subtotal to total
			$total += $r->subtotal;
			
			// Format the subtotal
			$r->subtotal_formatted = price_format($r->subtotal);
			
			// Format the price
			$r->price_formatted = price_format($r->price);
			
			// Put item into an array
			$array[] = $r;			
		}
		
		return array
		(
			// Array of objects stored in the cart.
			'items'			=> $array,
			
			// Total amount
			'total'			=> $total,
			
			// Cart ID
			/// @todo Do we need this?
			'cart_id'		=> $session_id,
		);
	}
	
	public function add_or_update($item, $qty)
	{
		// Grab session_id
		$session_id = $this->session->userdata('session_id');
		
		if (is_object($item))
		{
			// Generate Hash
			$hash = md5($item->art_number_clear . $item->sup_brand . $item->price . $item->vendor_name);
			
			// Check, if item's already in the cart
			$this->db->where(array
			(
				'cartid'				=> $session_id,
				'line_hash'				=> $hash,
			));
			
			if ($this->db->count_all_results('order_items') == 0)
			{
				// Insert new entry
				$this->db->insert('order_items', array
				(
					'cartid'										=> $session_id,
					'line_hash'										=> $hash,
					'art_number'									=> $item->art_number_clear,
					'sup_brand'										=> $item->sup_brand,
					'description'									=> $item->description,
					'price'											=> $item->price,
					'qty'											=> ($qty === TRUE)?1:$qty,
					'qty_limit'										=> $item->qty,
					'qty_lot_size'									=> (isset($item->qty_lot_size))?$item->qty_lot_size:1,
					'discount'										=> 0, // Just for now
					'vendor_name'									=> $item->vendor_name,
					'vendor_id'										=> $item->vendor_id,
					'delivery_days'									=> $item->delivery_days,
					'status'										=> 0, // We assume that 0 means non-order status
					'status_change_date'							=> time(),
				));
				
				
				
				// We return to stop execution
				return TRUE;
			}
		}
		
		// The item is an object and is already in the cart
		if (is_object($item))
		{
			$this->db->where(array
			(
				'cartid'					=> $session_id,
				'line_hash'				=> $hash,
			));
		}
		// We've got just line id
		else
		{
			$this->db->where(array
			(
				'id'							=> $item,
			));
		}
		
		// Qty equals to zero or is false
		if (!$qty)
		{
			$this->db->delete('order_items');
		}
		// Qty is either true or more than 0
		else
		{
			if ($qty === TRUE)
			{
				$this->db->set('qty', 'qty+1', FALSE);
			}
			else
			{
				if (!preg_match('#\d#', $qty))
				{
					$qty = 1;
				}
				
				$this->db->set('qty', $qty);
			}
			
			// Update the time
			$this->db->set('status_change_date', time());
			
			// Run the query
			$this->db->update('order_items');
		}
	}
}

/* End of file cart_model.php */
/* Location: ./application/models/cart_model.php */