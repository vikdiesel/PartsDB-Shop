<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Delivery Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Delivery_model extends CI_Model
{
	var $current;
	var $methods;
	var $price;

    /**
	 * Constructor
	 */
	function __construct()
    {
        parent::__construct();
    }
	
	// --------------------------------------------------------------------
	
	/**
	 * Methods
	 *
	 * Returns the DB result of methods available. 
	 * Data is ordered.
	 *
	 * @return	dbresult
	 */	
	public function methods()
	{
		// Order
		$this->db->order_by('order','asc');
		
		// Run
		$q = $this->db->get('delivery_methods');
		
		// Return
		return $q;
	}
	// --------------------------------------------------------------------
	
	/**
	 * Is Delivery
	 *
	 * Checks if method with the supplied ID exists.
	 *
	 * @param	int $id
	 * @return	bool
	 */	
	public function is_delivery($id)
	{
		// Method ID
		$this->db->where('id', $id);
		
		// If we have at least one result with this ID
		if ($this->db->count_all_results('delivery_methods') > 0)
		{
			// Method exists
			return TRUE;
		}
		
		// Method doesn't exist
		return FALSE;
	}
	// --------------------------------------------------------------------
	
	/**
	 * Add
	 *
	 * Adds delivery method.
	 * Input should be sanitized in controller.
	 *
	 * @param	array $ins
	 * @return	void
	 */	
	public function add($ins)
	{		
		// Prep data for insert
		$insert = array
		(
			// Method title
			'title'		=> $ins['title'],
			
			// Method price.
			'price'		=> $ins['price'],
			
			// Method order
			'order'		=> $ins['order'],
		);
		
		// Insert
		$this->db->insert('delivery_methods', $insert);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update
	 *
	 * Updates the delivery method specified by an ID.
	 *
	 * @warning not used anywhere
	 *
	 * @param	int $id
	 * @param	array $data
	 * @return	void
	 */	
	public function update($id, $data)
	{
		// Prep data for update
		
		$update = array
		(
			// Method title
			'title'		=> $data['title'],
			
			// Method price.
			'price'		=> $data['price'],
			
			// Method order
			'order'		=> $data['order'],
		);
		
		// ID of the method
		$this->db->where('id', $id);
		
		// Perform update
		$this->db->update('delivery_methods', $update);
	}

	// --------------------------------------------------------------------

	/**
	 * List
	 *
	 * Generates delivery methods list. Marks one of them as a selected:
	 * Either the cheapest one or the one that is selected by the user.
	 * Stores generated data in $methods, $price, $current class props.
	 *
	 * @todo Migrate to model
	 *
	 * @return    object
	 */
	public function list_methods()
	{
		$d_id = $this->session->userdata('delivery');

		if ($this->is_delivery($d_id) == FALSE) {
			$d_id = FALSE;
			$this->session->unset_userdata('delivery');
		}

		$q = $this->methods();

		$this->methods = array();
		$minimal_price = FALSE;

		foreach ($q->result() as $r) {
			if (!$d_id) {
				if ($minimal_price === FALSE) {
					$minimal_price = $r->price;
					$r->is_checked = TRUE;
					$this->price = $r->price;
					$this->current = $r;
				} elseif ($r->price < $minimal_price) {
					//uncheck previously selected
					$this->methods[$this->current->id]->is_checked = FALSE;

					$minimal_price = $r->price;
					$r->is_checked = TRUE;
					$this->price = $r->price;
					$this->current = $r;
				}
			} elseif ($d_id == $r->id) {
				$r->is_checked = TRUE;
				$this->price = $r->price;
				$this->current = $r;
			} else {
				$r->is_default = FALSE;
				$r->is_checked = FALSE;
			}

			$this->methods[$r->id] = $r;
		}

		return (object)array
		(
            'methods' => $this->methods,
            'current_price' => $this->price,
            'current' => $this->current,
		);
	}

}

/* End of file delivery_model.php */
/* Location: ./application/models/delivery_model.php */