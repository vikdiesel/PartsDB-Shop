<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Website Helper
 *
 * -
 *
 * @package		2find
 * @subpackage	Helpers
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
 
// --------------------------------------------------------------------
	
/**
 * Menu Selected
 *
 * Returns selected class if link1 matches link2.
 * Link2, if omitted, is retrieved from uri_string().
 * Link1 is wrapped in REGEX container, so it's possible to use things like [a-z]+ or .+ there.
 *
 * @param	string $link1
 * @param	string|bool $link2
 * @return	string
 */		
function menu_selected($link1, $link2=FALSE, $exclude=false)
{
	if (is_array($link1))
	{
		foreach ($link1 as $link1_str)
		{
			$return = menu_selected($link1_str, $link2, $exclude);
			
			if (!is_null($return))
			{	
				return $return;
			}
		}
	}
	
	if ($link2 === FALSE)
	{
		$link2 = uri_string();
	}
	
	if (preg_match("#^$link1$#", $link2))
	{
		if ($exclude and preg_match($exclude, $link2))
			return '';
		
		return 'selected active active-non-js';
	}
}

// --------------------------------------------------------------------
	
/**
 * Is Authorized
 *
 * Checks if user is authorised. Used in html view files.
 *
 * @return	bool
 */		
function is_authorized()
{
	$ci =& get_instance();
	
	if ($ci->access->_is_auth())
	{
		return $ci->access->accdata->email;
	}
	
	return FALSE;
}

// --------------------------------------------------------------------
	
/**
 * Price Format
 *
 * Returns price  together with currency mark in a readable formatted style
 *
 * @param	int $price
 * @return	string
 */	
function price_format($price, $discount=0)
{
	// Get CodeIgniter's instance reference link
	$ci =& get_instance();
	
	// Load currency model
	$ci->load->model('currency_model', 'currency');
	
	// Get site's default currency
	$currency = $ci->currency->get();
		
	if ($currency->is_no_decimals)
		$price_f = number_format($price, 0, '.', ',');
		
	else
		$price_f = number_format($price, 2, '.', ',');
	
	$price_f = str_replace('.', '<span class="decimals">.', $price_f) . '</span>';
	$price_f = '<strong>' . $price_f . '<span class="currency">&nbsp;' . $currency->symbol . '</span></strong>';
	
	if ($discount > 0)
	{
		return $price_f . " (-$discount%)";
	}
	else
	{
		return $price_f;
	}
}

// --------------------------------------------------------------------
	
/**
 * Backlink Mask
 *
 * Used to preserve backlinks in this kind of calls user/auth/(backlink)
 *
 * @param	string $loginlink
 * @return	string
 */
function bcklnk_mask($loginlink)
{
	$backlink = uri_string();
	
	if (strpos($backlink, $loginlink) !== FALSE)
	{
		return '';
	}		
	elseif (strlen($backlink) > 0)
	{		
		return str_replace('/', '--', $backlink);
	}
	else
	{
		return '';
	}
}

// --------------------------------------------------------------------
	
/**
 * Backlink Unmask
 *
 * Masking is used to preserve backlinks in this kind of calls user/auth/(backlink)
 * This function unmasks masked backlink
 *
 * @param	string $backlink_masked
 * @return	string
 */
function bcklnk_unmask($backlink_masked)
{
	return str_replace('--', '/', $backlink_masked);
}

// --------------------------------------------------------------------
	
/**
 * Permalink
 *
 * Takes the title and generates a transliterated permalink without any 'dodgy' characters.
 *
 * @param	string $title
 * @return	string
 */
function permalink($title)
{
	$ru_caps = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 
					'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь', 'Ы', 'Ъ', 'Э', 'Ю', 'Я', ' ');
	
	$ru = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 
					'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ы', 'ъ', 'э', 'ю', 'я', ' ');

	$en = array('a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u',
					'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya', '-');
					
	$title = trim($title);
	$title = string_remove_umlauts($title);
	$title = str_replace($ru_caps, $en, $title);
	$title = str_replace($ru, $en, $title);
	$title = strtolower($title);
	$title = preg_replace("#[^a-z0-9_-]#", '', $title);
	
	// More than 2 dashes together
	$title = preg_replace("/-+/", "-", $title);
	
	return $title;
}



// --------------------------------------------------------------------
	
/**
 * JB Year Classes
 *
 * Generates the list of years starting form $start and ending at $end.
 *
 * @param	string
 * @param	int
 * @param	int
 * @return	bool
 */	
function jb_year_classes ($class_prefix, $start, $end)
{
	if (empty($end)) {
		$end = date('Y');
	}

	$classes = '';
	
	for ($x=$start; $x<=$end; $x++)
	{
		$classes .= $class_prefix . $x . ' ';
	}
	
	return $classes;
}

// --------------------------------------------------------------------
	
/**
 * JB Sitedata
 *
 * Shortcut to get JB Sitedata
 *
 * @param	bool|string
 * @return	mixed
 */	
function _jb_sitedata($field=FALSE)
{
	$ci =& get_instance();
	$ci->load->model('local_helper_model', 'local', TRUE);
	
	if ($field)
	{
		if (!empty($ci->local->sitedata) && !empty($ci->local->sitedata->$field))
			return $ci->local->sitedata->$field;
		else
			return false;
	}
	
	return $ci->local->sitedata;
}

// This things are used in view files
// to fetch data from hdata
function _tmplt_hdata($key)
{
	$ci =& get_instance();
	
	if (!empty($ci->appflow->hdata->{$key}))
		return $ci->appflow->hdata->{$key};
	else
		return NULL;
}

// --------------------------------------------------------------------
	
/**
 * Date TZ
 *
 * Formatted date with regards to currently set timezone offset
 *
 * @param	stirng $format
 * @param	int $date
 * @return	bool
 */	
function date_tz($format, $date) // Keeps in mind current timezone offset
{
	$ci =& get_instance();
	$ci->load->helper('date');
	$timezone = _cfg('timezone');
	
	return date($format, gmt_to_local($date, $timezone, (boolean) date('I')));
}

// --------------------------------------------------------------------
	
/**
 * JB Option
 *
 * Returns an option specified by option_name
 *
 * @param	string $option_name
 * @return	bool|mixed
 */	
function _jb_option($option_name)
{
	$ci =& get_instance();
	$ci->load->model('options_model', 'options', TRUE);
	return $ci->options->get($option_name, TRUE);
}

// --------------------------------------------------------------------
	
/**
 * CFG
 *
 * Shortcut to Codeigniter's native config class. Config items can be overriden in Options table.
 *
 * @param	string $key
 * @return	bool|mixed
 */	
function _cfg($key, $_jb_option_ignore = FALSE)
{
	$ci =& get_instance();
	
	if (!$_jb_option_ignore && ($jbopt = _jb_option($key)) !== FALSE)
	{
		return $jbopt;
	}
	
	return $ci->config->item($key);
}

// --------------------------------------------------------------------
	
/**
 * Show JB Error
 *
 * Generates styled error message and terminates the program.
 * Contains an array of human-readable error messages
 *
 * @param	string $errid ID of an error or readable title
 * @param	string|bool $m Readable message (not needed, if first param is valid error code)
 * @return	void
 */	
function show_jb_error ($errid, $m=FALSE)
{
	$template = 'error_general';
	
	$errs = array
	(

	);
	
	if (isset($errs[$errid]))
	{
		$h = $errs[$errid][0];
		$m = $errs[$errid][1];
		
		if (isset($errs[$errid][2]))
		{
			$template = $errs[$errid][2];
		}
	}
	else
	{
		$h = $errid;
	}
	
	$_error =& load_class('Exceptions', 'core');
	echo $_error->show_error($h, $m, $template, 500);
	exit;
}

// used by posts->meta
function mktt_is_empty ($el) 
{
	$el = trim($el);
	
	if (empty($el))
	{
		return false; 
	}
	else
	{
		return true;
	}
}

// --------------------------------------------------------------------

/**
 * Str umlauts converter.
 *
 * Takes s.c. umlaut chars and converts them to the most-likely ASCII.
 *
 * @param	string $str
 * @return	string
 */

// function str_strip_umlauts( $str )
// {
// return strtr(utf8_decode($str), utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'), 'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
// }

function string_remove_umlauts($string)
{
	if (preg_match("/[ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ]+/ui", $string))
	{
		return strtr(utf8_decode($string), utf8_decode("ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ"), "AAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
	}
	
	return $string;
}

/// @debug function
function jb_dump($var, $m = 'full')
{
	if ($m != 'e')
		ob_start();
	
	if ($m == 'full')
		var_dump($var);
	
	if ($m != 's')
	{
		$dump = ob_get_contents();
		ob_end_clean();
		file_put_contents('jb_dump.txt', $dump, FILE_APPEND);
	}
}

/* End of file website_helper.php */
/* Location: ./application/helpers/website_helper.php */