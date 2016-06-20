<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
function rus_month($month_number)
{
	$months = array
	(
		'1'=>'января',
		'2'=>'февраля',
		'3'=>'марта',
		'4'=>'апреля',
		'5'=>'мая',
		'6'=>'июня',
		'7'=>'июля',
		'8'=>'августа',
		'9'=>'сентября',
		'10'=>'октября',
		'11'=>'ноября',
		'12'=>'декабря'
	);

	if (isset($months[$month_number]))
	{
		return $months[$month_number];
	}

	return 'undefined';
}

/* End of file EXT_date_helper.php */
/* Location: ./application/helpers/EXT_date_helper.php */