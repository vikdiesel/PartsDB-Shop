<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Access Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
 
class Access_model extends CI_Model
{
	/// Here we use fixed admin login for simplicity
	var $adminlogin = 'admin';
	
	/// Here we store data of the authorised person
	var $accdata;
	
	/// Authorized siteid (populated by admin_no_siteid)
	var $siteid;
	
	// --------------------------------------------------------------------
	
	/**
	 * Is Auth
	 *
	 * If user is authorised. Detects users registered as customers.
	 *
	 * @return	bool
	 */	
	public function _is_auth()
	{
		// Get email from session
		$email = $this->session->userdata('email');
		
		// Get password from session
		$pass = $this->session->userdata('password');
		
		// Pass data to check
		if ($this->auth($email, $pass))
		{
			// Success
			return TRUE;
		}
		
		// Fail
		return FALSE;
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Is Admin
	 *
	 * If administrator is authorised.
	 *
	 * @return	bool
	 */	
	public function _is_admin()
	{
		// Get login from session
		$login = $this->session->userdata('adm_login');
		
		// Get password from session
		$pass = $this->session->userdata('adm_pass');
		
		// Pass data to check
		if ($this->admin($login, $pass))
		{
			// Success
			return TRUE;
		}
		
		// Fail
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Auth
	 *
	 * Basic authorisation for users.
	 *
	 * @param	string|bool $email
	 * @param	string|bool $pass
	 * @return	bool
	 */	
	public function auth($email, $pass)
	{
		// Email/Pass may be inserted via $this->input->post('pass'), so they can possibly be equal to false
		if (!$email or !$pass)
		{
			// Fail
			return FALSE;
		}
		
		// Prep DB conditions
		$this->db->where(array
		(
			'email'			=> $email,
			'password'		=> $pass,
		));
		
		// Get data from `users` table
		$q = $this->db->get('users');
		
		// If entry exists
		if ($q->num_rows() > 0)
		{
			// Fetch as a row
			$r = $q->row();
			
			// Userdata is stored as serialized object
			$r->userdata = unserialize($r->userdata);
			
			// Save to accdata for future use
			$this->accdata = $r;
			
			// Success
			return TRUE;
		}
		
		// Fail
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin
	 *
	 * Admin-level authorisation
	 *
	 * @param	string $email
	 * @param	string $pass
	 * @return	bool
	 */	
	public function admin($login, $pass)
	{
		// Grab sitedata
		$jb_sitedata = _jb_sitedata();

		// Password is md5 hash.
		if ($login == $this->adminlogin and md5($pass) == $jb_sitedata->adminpass)
		{
			// Success
			return TRUE;
		}
		
		// Fail
		return FALSE;
	}
}

/* End of file access_model.php */
/* Location: ./application/models/access_model.php */
?>