<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Currency Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Currency_model extends CI_Model {
	
	/// Currencies list
	/// Currency_code => array (Currency_title, Currency_symbol, Is_symbol_before_number, is_put_divider_before, is_no_decimals?, ceil_param, )
	var $currencies = array
	(
		'RUR'	=> array ('Российский рубль', 'руб.', FALSE, TRUE),
		
		'EUR'	=> array ('Евро', 'EUR', FALSE, TRUE),
		'USD'	=> array ('Доллар США', 'USD', FALSE),
		'GBP'	=> array ('Фунт Стерлингов', 'GBP', FALSE),
		'CNY'	=> array ('Китайский Юань', 'CNY', FALSE),
		
		'AZN'	=> array ('Азербайджанский манат', 'м.', FALSE, TRUE),
		'AMD'	=> array ('Армянский драм', 'д.', FALSE),
		'BYR'	=> array ('Белорусский рубль', 'р.', FALSE, NULL, TRUE, 1000),
		'KZT'	=> array ('Казахский тенге', 'т.', FALSE),
		'KGS'	=> array ('Киргизский сом', 'с.', FALSE),
		'MDL'	=> array ('Молдавский лей', 'л.', FALSE),
		'TMT'	=> array ('Туркменский манат', 'м.', FALSE),
		'TJS'	=> array ('Таджикский сомони', 'с.', FALSE),
		'UZS'	=> array ('Узбекский сум', 'с.', FALSE),
		'UAH'	=> array ('Украинская гривна', 'грн.', FALSE),
	);
	
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
	 * Get
	 *
	 * Gets data for currency id supplied (uses site's primary currency id, or 0, if nothing's provided)
	 *
	 * @param	string
	 * @return	object
	 */	
	public function get ($id = FALSE)
	{
		// If $id is not provided
		if (!$id)
		{
			// Set $id based on site's primary currency
			$id = $this->options->get('currency');
		}
		
		// $id still can be false, because primary currency can be unset.
		if (!$id or !isset($this->currencies[$id]))
		{
			// Use default currency
			$id = $this->config->item('currency');
		}
		
		// Return
		return (object) array
		(
			'code'							=> $id,
			'title'							=> $this->currencies[$id][0],
			'symbol'						=> $this->currencies[$id][1],
			'is_symbol_before'	=> $this->currencies[$id][2],
			'is_no_decimals'	=> (!empty($this->currencies[$id][4]))?$this->currencies[$id][4]:FALSE,
			'ceil'		=> (!empty($this->currencies[$id][5]))?$this->currencies[$id][5]:FALSE,
		);
	}
	
	public function price_prep ($price)
	{
		$currency = $this->get();
		
		if ($currency->ceil)
			$price = ceil($price / $currency->ceil) * $currency->ceil;
			
		return $price;
	}
}


/* End of file currency_model.php */
/* Location: ./application/models/currency_model.php */