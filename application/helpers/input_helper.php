<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Input Helper
 *
 * Loaded together with Codeigniter's native input helper
 *
 * @package		2find
 * @subpackage	Helpers
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
 
// --------------------------------------------------------------------
	
/**
 * P
 *
 * A convenient wrapper for Codeigniter's native input->post() method.
 * Returns either one var or an array of vars.
 * If prevalidated is set to false and an array of vars is expected, function will return False if one of the entries does not exist.
 *
 * @param	sitring|array The variable name
 * @param	bool If true, the process will not be stopped if one of the vars is empty. Works when $var is array
 * @param	bool 
 * @return	mixed
 */	
function p($var, $prevalidated = FALSE, $defaults = FALSE)
{
	$ci =& get_instance();
	
	if (is_array($var))
	{
		$newArray = array();
		
		foreach ($var as $varName)
		{
			$varValue = $ci->input->post($varName);
			
			if ($varValue !== FALSE and $varValue != '')
			{
				$newArray[$varName] = $varValue;
			}
			elseif (is_array($defaults) and array_key_exists($varName, $defaults))
			{
				$newArray[$varName] = $defaults[$varName];
			}
			elseif (!$prevalidated)
			{
				return FALSE;
			}
		}
		
		return $newArray;
	}
	else
	{
		$p = $ci->input->post($var);
		if ($p != FALSE and $p != '')
		{
			return $p;
		}
		else
		{
			return FALSE;
		}
	}
}

/* End of file input_helper.php */
/* Location: ./application/helpers/input_helper.php */