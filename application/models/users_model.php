<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Users_model extends CI_Model
{
	/**
	 * Constructor
	 */
	public function __construct()
    {
        parent::__construct();		
    }
	
	// --------------------------------------------------------------------
	
	/**
	 * Get
	 *
	 * Gets users either filtered by column/value or all. User specified in $selected can be marked as selected.
	 *
	 * @param	int $id
	 * @param	string $by
	 * @param	bool $selected
	 * @return	array|obj|bool
	 */	
	public function get($id=FALSE, $by='id', $selected=FALSE)
	{
		if ($id)
		{
			$this->db->where($by, $id);
			$this->db->limit(1);
		}

		$q = $this->db->get('users');
		
		if ($id and $q->num_rows() > 0)
		{
			return $q->row();
		}
		elseif (!$id)
		{
			$array = array();
			
			foreach ($q->result() as $r)
			{
				if ($selected and $r->id == $selected)
				{
					$r->selected = TRUE;
				}
				else
				{
					$r->selected = FALSE;
				}
				
				$array[] = $this->_format($r);
			}
			
			return $array;
		}
		
		return FALSE;
	}	
	
	// --------------------------------------------------------------------
	
	/**
	 * Add
	 *
	 * Creates new user.
	 *
	 * @param	string $email
	 * @param	array $vars
	 * @return	void
	 */	
	public function add($email, $vars, $discount = 0)
	{
		$data = array
		(
			'password'		=> (!empty($vars['password']))?$vars['password']:random_string('numeric', 8),
			'vericode'		=> random_string('unique', 32),
			'userdata'		=> serialize($vars),
			'discount'		=> $discount,
		);
		
		if ($email)
			$data['email'] = $email;
		
		$this->db->insert('users', $data);
		
		return (object) array
		(
			'id'				=> $this->db->insert_id(),
			'email'			=> $email,
			'password'	=> $data['password'],
		);
	}
	
	/// Used in xmlimport
	/// @todo use this in add()
	public function _db_prep_save($data, $op)
	{
		if ($op == 'insert')
		{
			if (empty($data['password']))
				$data['password'] = random_string('numeric', 8);
				
			if (empty($data['vericode']))
				$data['vericode'] = random_string('unique', 32);
				
			if (empty($data['discount']))
				$data['discount'] = 0;
				
			$insert = array
			(
				'email'					=> $data['email'],
				'password'			=> $data['password'],
				'vericode'			=> $data['vericode'],
				'discount'			=> $data['discount'],
			);
			
			unset($data['email']);
			unset($data['password']);
			unset($data['vericode']);
			unset($data['discount']);
			
			$insert['userdata'] = serialize($data);
			
			return $insert;
		}
		
		
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Userdata one-level array
	 *
	 * Helper function which merges userdata (which is stored as an array) with an entire parent array.
	 *
	 * @todo Possible improvement can be made by useing array_merge
	 *
	 * @param	array $cart_items
	 * @return	array
	 */	
	function userdata_one_level_array($userdata)
	{
		$d['email'] = $userdata->email;
		$d['discount'] = $userdata->discount;
		
		foreach ($userdata->userdata as $k=>$v)
		{
			$d[$k] = $v;
		}
		
		return $d;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Is Email Unique
	 *
	 * Checks if the email is not already in users table
	 *
	 * @param	string $email
	 * @return	bool
	 */	
	function is_email_unique($email)
	{
		$this->db->where('email', $email);

		if ($this->db->count_all_results('users') == 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * User Update
	 *
	 * Updates user specified by email.
	 *
	 * @param	string $email
	 * @param	array $vars
	 * @return	int
	 */	
	function user_update($email, $vars, $discount=FALSE, $id=FALSE)
	{
		if ($id)
		{
			$this->db->where('id', $id);
			$this->db->set('email', $email);

			if ($discount !== FALSE)
			{
				$this->db->set('discount',$discount);
			}
		}
		else
		{		
			$this->db->where('email', $email);
		}
		
		$this->db->set('userdata', serialize($vars));
		$this->db->limit(1);
		$this->db->update('users');
		
		if (!empty($this->access->accdata))
		{
			$this->access->accdata->userdata = $vars;
			return $this->access->accdata->id;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update Password
	 *
	 * Updates user password for user_id
	 *
	 * @access	public
	 * @param	int
	 * @param	str
	 * @return	void
	 */
	public function update_password($id, $password)
	{
		$this->db->where('id', $id);
		$this->db->set('password', $password);
		$this->db->update('users');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * User Update Discount
	 *
	 * This function takes a user id and a discount amount
	 * and updates the discount of that user
	 *
	 * @access	public
	 * @param	int
	 * @param	int
	 * @return	void
	 */
	public function user_update_discount($id, $discount)
	{
		$this->db->where('id', $id);
		$this->db->set('discount', $discount);
		$this->db->update('users');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Format
	 *
	 * Basic walker. Unserializes userdata field.
	 *
	 * @param	obj $r
	 * @return	obj
	 */	
	
	
	private function _format($r)
	{
		$r->userdata = (object) unserialize($r->userdata);
		return $r;
	}

}

?>