<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Options Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 *
 */
class Options_model extends CI_Model {

	/// Options cache
	var $options_cache = array();
	
	/// All values if they are stored as serialized items are unserlialized upon retrieval. False by default for compatibility.
	var $unserialize_on_get = FALSE;

	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_from_db ($id=FALSE, $col=FALSE, $use_like=FALSE, $option_name=FALSE, $is_count=FALSE)
	{
		if ($id and $col)
		{
			if ($use_like)
			{
				$this->db->like($col, $id, $use_like);
				
				if ($option_name)
				{
					$this->db->where('option_name', $option_name);
				}
			}
			else
			{
				$this->db->where($col, $id);
			}
		}
		
		if ($is_count)
		{
			return $this->db->count_all_results('options');
		}
		else
		{
			return $this->db->select('id, option_name, option_value')->get('options');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get
	 *
	 * Gets an option specified by name.
	 *
	 * @param	string|bool $name
	 * @param	bool $single Not in use
	 * @return	mixed
	 */	
	public function get ($name=FALSE, $single=TRUE)
	{
		// Cache, if not cached
		
		if (empty($this->options_cache))
		{
			$array = array();
			
			$q = $this->get_from_db();
			
			foreach ($q->result() as $r)
			{
				if ($this->unserialize_on_get)
				{
					$unserialized = @unserialize($r->option_value);
					
					if ($r->option_value === 'b:0;' || $unserialized !== false)
					{
						$array[$r->option_name] = $unserialized;
					} 
					else 
					{
						$array[$r->option_name] = $r->option_value;
					}
				}
				else
				{
					$array[$r->option_name] = $r->option_value;
				}
			}
			
			$this->options_cache = $array;
		}
		
		// Get and return
		if ($name and isset($this->options_cache[$name]))
		{
			return $this->options_cache[$name];
		}
		elseif ($name)
		{
			return FALSE;
		}
		
		return $this->options_cache;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set
	 *
	 * Set an option specified by name
	 *
	 * @param	string $name
	 * @param	mixed $value
	 * @param	bool $single
	 * @param	string|bool $siteid
	 * @return	void
	 */	
	public function set ($name, $value, $single=TRUE)
	{
		if (is_array($value))
		{
			$value = serialize($value);
		}
		
		if ($single)
		{
			$opts_count = $this->db->where(array('option_name'=>$name))->count_all_results('options');
			
			if ($opts_count == 0)
			{
				$this->db->insert('options', array
				(
					'option_name'=>$name,
					'option_value'=>$value,
				));
			}
			else
			{
				$this->db->where(array('option_name'=>$name))->update('options', array('option_value'=>$value));
			}
		}
		else
		{
			$this->db->insert('options', array
			(
				'option_name'=>$name,
				'option_value'=>$value,
			));
		}
		
		// Update cache
		$this->options_cache[$name] = $value;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete
	 *
	 * Deletes an option specified by name
	 *
	 * @param	string $name
	 * @param	string|bool $siteid
	 * @return	void
	 */	
	public function delete ($name)
	{
		$this->db->where(array('option_name'=>$name))->delete('options');
		
		// Update cache
		unset ($this->options_cache[$name]);
	}
}

/* End of file options_model.php */
/* Location: ./application/models/options_model.php */