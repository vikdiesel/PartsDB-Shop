<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Form Helper
 *
 * Loaded together with Codeigniter's native form helper
 *
 * @package			2find
 * @subpackage	Helpers
 * @author			Viktor Kuzhelnyi @marketto.ru
 * @version 		0.1
 */
 
// --------------------------------------------------------------------
	
/**
 * Set Radio
 *
 * -
 *
 * @param	string
 * @param	string
 * @return	string
 */	
function setradio($thisval, $dbval, $field = FALSE, &$counter = FALSE)
{
	if ($field === FALSE)
	{
		if ($thisval == $dbval)
			return 'checked="checked"';
		
		else
			return '';
	}
	else
	{
		if (((is_null($dbval) or $dbval == '') and $counter === 0) or ($thisval == $dbval)) 
		{
			$default = TRUE;
		}
		else
		{
			$default = FALSE;
		}
		
		return set_radio($field, $thisval, $default);
	}
}

// --------------------------------------------------------------------
	
/**
 * Set Select
 *
 * -
 *
 * @param	string
 * @param	string
 * @return	string
 */	
function setselect($thisval, $dbval, $field = FALSE)
{	
	if ($field === FALSE)
	{
		if ($thisval == $dbval)
			return 'selected="selected"';
		
		else
			return '';
	}
	else
	{
		if ($thisval == $dbval) 
		{
			$default = TRUE;
		}
		
		return set_select($field, $thisval, $default);
	}
}

// --------------------------------------------------------------------
	
/**
 * Set Value
 *
 * -
 *
 * @param	string
 * @param	stirng
 * @param	&array
 * @return	string
 */	
function setval($fieldname, $defval, &$form)
{
	if (isset($form[$fieldname]))
	{
		$defval = $form[$fieldname];
	}
	
	return set_value($fieldname, $defval);
}

// --------------------------------------------------------------------
	
/**
 * Set Checkbox
 *
 * -
 *
 * @param	string
 * @param	string
 * @param	&array
 * @return	string
 */	
function setchkbox($fieldname, $fieldval, &$form)
{
	$checked = FALSE;
	
	if (isset($form[$fieldname]) and $fieldval == $form[$fieldname])
	{
		$checked = TRUE;
	}
	
	return set_checkbox($fieldname, $fieldval, $checked);
}

// --------------------------------------------------------------------
	
/**
 * Form Input
 *
 * -
 *
 * @param	string
 * @param	bool|array
 * @param	bool
 * @param	bool|string
 * @param	int
 * @param	string
 * @return	string
 */	
function form__input($name, $form=FALSE, $no_edit_mode=FALSE, $field_id=FALSE, $maxlength=255, $class="")
{
	
	if (!is_array($form) and $form !== FALSE)
	{
		$form = array($name=>$form);
	}
	elseif ($form === FALSE)
	{
		$form[$name] = '';
	}
	
	if ($no_edit_mode)
	{
		echo '<span class="data_value ' . $class . ' uneditable-input">' . $form[$name] . '</span>' . form_hidden($name, $form[$name]);
	}
	else
	{
		if ($field_id == FALSE)
		{
			$field_id = $name;
		}
		
		echo form_input($name, set_value($name, $form[$name]), "id=\"$field_id\" maxlength=\"$maxlength\" class=\"form-control $class\"");
	}
}

// --------------------------------------------------------------------
	
/**
 * Form Password
 *
 * -
 *
 * @param	string
 * @param	int
 * @return	string
 */	
function form__pass2($name, $maxlength=20)
{
	global $form;
	echo form_password($name, set_value($name, $form[$name]), "id=\"$name\" maxlength=\"$maxlength\"");
}

// --------------------------------------------------------------------
	
/**
 * Form Checkbox
 *
 * -
 *
 * @param	string
 * @param	string
 * @param	array
 * @return	string
 */	
function form__checkbox($name, $value, $form)
{
	if (isset($form[$name]) && $form[$name] == 1)
	{
		$form[$name] = TRUE;
	}
	
	$data = array
	(
		'name'        => $name,
		'id'          => $name,
		'value'       => $value,
		'checked'     => set_checkbox($name, $value, $form[$name]),
		'class'				=> 'radio',
    );

	echo form_checkbox($data);
}

// --------------------------------------------------------------------
	
/**
 * Form Label
 *
 * -
 *
 * @param	string
 * @param	string
 * @param	bool|string
 * @return	string
 */	
function form__label($id, $label, $comment=FALSE)
{
	echo "<label for=\"$id\" class=\"control-label\">$label";
	if ($comment)
	{
		echo " <small>$comment</small>";
	}
	echo "</label>";
}

/* End of file EXT_form_helper.php */
/* Location: ./application/helpers/EXT_form_helper.php */