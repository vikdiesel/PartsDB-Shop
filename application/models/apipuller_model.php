<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Api Puller Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Apipuller_model extends CI_Model
{
	/// For debug purposes
	var $is_return_raw = FALSE;
	
	/// Validator Salt
	var $key = 'I2*A8AaQ[2,*gCp,/Txls.3`cm2`azK)*W.]i]X{X8zp+FnDU-O2#8M4mtFsce%Y';
	
	/// Cache store time in seconds
	var $cache_store_time = 3600;
	
	/// List of available APIs
	/// API-ID => array (HUMAN-READABLE-NAME, NUMBER OF KEYS REQUIRED)
	var $apis = array
	(
		'emex'					=> array('EMEX', 2),
		'berg'					=> array('Berg', 1),
		'adeopro'				=> array('Adeo.pro', 2),
		'mikado'				=> array('Mikado', 2),
		// 'weloveparts'		=> array('Weloveparts', 2),
		'autopiter'			=> array('Autopiter', 2),
		'moskvorechie'	=> array('Moskvorechie', 2),
	);
	
	// Used to access SOAP client in case, we need multiple queries
	var $client;
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	// --------------------------------------------------------------------
	
	public function validate($item)
	{
		// jb_dump($item->hash . " == " . $this->hash($item));
		if (isset($item->hash) and $item->hash == $this->hash($item))
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function hash($item)
	{
		return md5
		(
			$item->art_number . 
			$item->sup_brand . 
			$item->description . 
			$item->price . 
			$item->qty_limit . 
			// $item->qty_lot_size . 
			// $item->sub_vendor_name .
			$item->delivery_days .
			$this->key
		);
	}
	
	public function hash_short($item)
	{
		return md5
		(
			$item->art_number . 
			$item->sup_brand . 
			$this->key
		);
	}
	
	public function pull($artnr, $brand, $vendor_id, $mode='thisnr')
	{
		// Prep artnr
		$artnr = $this->appflow->qprep($artnr, 'art_nr');
		
		// Prep brand
		if ($brand == '0')
			$brand = false;
		else
			$brand = $this->appflow->qprep($brand, 'sup_brand');
			
		// Make request object
		$request = (object) array
		(
			'art_number_clear'	=> $artnr,
			'sup_brand_clear'		=> $brand,
			'vendor_id'					=> $vendor_id,
			'mode'							=> $mode,
		);
		
		// The actual cache is disabled (see $this->cache_get)
		if (($data = $this->cache_get($request)) !== FALSE)
		{
			echo $data;
		}
		else
		{
			// Pull vendors
			$this->local->_vendors_array('all');
			
			// Check, if vendor is on api list
			if (isset($this->local->vendor_apis[$vendor_id]))
			{
				// Get vendor data
				$vendor_data = $this->local->vendor($vendor_id);
				
				// If vendor's API is EMEX
				if ($this->local->vendor_apis[$vendor_id]->api_id == 'emex')
				{
					$client = new SoapClient("http://ws.emex.ru/EmExService.asmx?WSDL");

					$items = $client->FindDetailAdv4(array
					(
						'login'								=> $this->local->vendor_apis[$vendor_id]->api_key1,
						'password'						=> $this->local->vendor_apis[$vendor_id]->api_key2,
						'detailNum'						=> $artnr,
						// 'substLevel'					=> 'OriginalOnly',
						'substLevel'					=> ($mode == 'article_page')?'All':'OriginalOnly',
						'substFilter'					=> 'FilterOriginalAndAnalogs',
						'deliveryRegionType'	=> 'PRI',
					));
					
					// jb_dump($items);

					$array = $this->dtst_emex($items->FindDetailAdv4Result->Details->SoapDetailItem, array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all');
					
					// Encode to JSON format
					// $data = json_encode($array);
					
					// Store to cache
					// $this->cache_store($request, $data);
					
					return $array;
				}
				
				// If vendor's API is Mikado
				elseif ($this->local->vendor_apis[$vendor_id]->api_id == 'mikado')
				{
					$this->client = new SoapClient("http://mikado-parts.ru/ws/service.asmx?WSDL");
					
					$items = $this->client->Code_Search(array
					(
						'Search_Code'					=> $artnr,
						'ClientID'						=> $this->local->vendor_apis[$vendor_id]->api_key1,
						'Password'						=> $this->local->vendor_apis[$vendor_id]->api_key2,
					));
					
					
					$array = $this->dtst_mikado($items->Code_SearchResult->List->Code_List_Row, array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all');
					
					// Encode to JSON format
					// $data = json_encode($array);
					
					// Store to cache
					// $this->cache_store($request, $data);
					
					return $array;
				}
				
				// If vendor's API is ADEOPRO
				elseif ($this->local->vendor_apis[$vendor_id]->api_id == 'adeopro')
				{
					$xml = $this->load->view('apipuller/adeopro-prices-request', array
					(
						'api_login'					=> $this->local->vendor_apis[$vendor_id]->api_key1,
						'api_password'			=> $this->local->vendor_apis[$vendor_id]->api_key2,
						'art_number'				=> $artnr,
						'brand'							=> $brand,
						
						// Should be equal to `price` (as described in adeo.pro xml standard)
						'api_action'				=> 'price',
						
						// Match method: 1 - exact match; 2 - match all that begin with number specified (as described in adeo.pro xml standard)
						'api_match_method'	=> '1',
					), TRUE);
					
					$url = 'http://adeo.pro/pricedetals2.php';
					$ch = curl_init();
					 
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, array('xml'=>$xml));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					 
					$response = curl_exec($ch);
					
					curl_close($ch);
					
					$array = $this->dtst_adeopro($response, array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all');
					
					// Encode to JSON format
					// $data = json_encode($array);
					
					// Store to cache
					// $this->cache_store($request, $data);
					
					return $array;
				}
				
				// If vendor's API is BERG
				elseif ($this->local->vendor_apis[$vendor_id]->api_id == 'berg')
				{
					// jb_dump("$artnr + $brand");
					
					$response = $this->berg_pull_data($artnr, $brand, $this->local->vendor_apis[$vendor_id]->api_key1, $mode);
					
					$response_decoded = json_decode($response);
					$array = array();
					
					// WARN_ARTICLE_IS_AMBIGUOUS
					if (isset($response_decoded->warnings) and isset($response_decoded->resources))
					{
						foreach ($response_decoded->warnings as $warning)
						{
							if ($warning->code == 'WARN_ARTICLE_IS_AMBIGUOUS')
							{
								$is_ambiguous = true;
								break;
							}
						}
						if (!empty($is_ambiguous))
						{
							foreach ($response_decoded->resources as $resource)
							{
								$response = $this->berg_pull_data($artnr, $resource->brand->name, $this->local->vendor_apis[$vendor_id]->api_key1, $mode);
								$array = array_merge($array, $this->dtst_berg(json_decode($response), array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all'));
							}
						}
					}
					else
					{
						$array = $this->dtst_berg(json_decode($response), array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all');
					}
					
					// Encode to JSON format
					// $data = json_encode($array);
					
					// Store to cache
					// $this->cache_store($request, $data);
					
					return $array;
				}
				
				// We love parts
				elseif ($this->local->vendor_apis[$vendor_id]->api_id == 'weloveparts')
				{
					$array = array();
					
					// Weloveparts doesn't work without brand
					if (empty($brand))
						return array();
					
					$login = $this->local->vendor_apis[$vendor_id]->api_key1;
					$pass_md5 = md5($this->local->vendor_apis[$vendor_id]->api_key2);
					
					$url = "http://weloveparts.ru.public.api.abcp.ru/search/articles/?userlogin=$login&userpsw=$pass_md5&number=$artnr&brand=$brand";
					
					$ch = curl_init();
 
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array
					(
						'Content-type: application/x-www-form-urlencoded',
						'Accept: application/json',
					));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					 
					$response = curl_exec($ch);
					$response = json_decode($response);

					curl_close($ch);
					
					if (empty($response->errorCode))
					{
						$array = $this->dtst_weloveparts($response, array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all');
					}
					
					return $array;
				}
				
				// Autopiter
				elseif ($this->local->vendor_apis[$vendor_id]->api_id == 'autopiter')
				{
					$array = array();
					
					$userid	= $this->local->vendor_apis[$vendor_id]->api_key1;
					$pass		= $this->local->vendor_apis[$vendor_id]->api_key2;
					
					ini_set('default_socket_timeout', 15);
					
					// trace=>1 allows to get response headers
					$client = new SoapClient("http://service.autopiter.ru/price.asmx?WSDL", array('trace' => 1, 'connection_timeout' => 15, "soap_version" => SOAP_1_2));
					
					// echo 'xxx';
					
					// $isauth = $client->IsAuthorization();
					
					// if (!$isauth->IsAuthorizationResult)
					// {
						$auth = $client->Authorization(array
						(
							'UserID'					=> $userid,
							'Password'				=> $pass,
							'Save'						=> 0,
						));
					// }
					// else
					// {
						// $auth = (object) array
						// (
							// 'AuthorizationResult' => true,
						// );
					// }
					
					var_dump($auth->AuthorizationResult);
					
					if ($auth->AuthorizationResult === TRUE)
					{
						$resph = $client->__getLastResponseHeaders();
						
						if (preg_match_all("/(AuthCoocies)=([^;]+);/i", $resph, $m))
						{
							$cookie_name = array_pop($m[1]);
							$cookie_val = array_pop($m[2]);
							
							$client->__setCookie($cookie_name, $cookie_val);
							
							try {
								$client->FindCatalog(array
							(
								'ShortNumberDetail'	=> $artnr
							));
							} catch(Exception $e){echo $e->getMessage();}
							
							if (!empty($pre_response->FindCatalogResult->SearchedTheCatalog))
							{
								$pre_response = $pre_response->FindCatalogResult->SearchedTheCatalog;
								
								if (!is_array($pre_response))
								{
									$pre_response = array($pre_response);
								}
						
								// We can list articles without prices in this case
								if (empty($brand))
								{
									/// @todo 
									// $array = $this->dtst_autopiter_nobrand($pre_response, array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data);
									
									return array();
								}
								else
								{
									$thisid = null;
									
									foreach ($pre_response as $itm)
									{
										if ($this->stock->is_similar_brands($brand, $itm->Name) === TRUE)
										{
											$thisid = $itm->id;
											break;
										}
									}
									
									if (!empty($thisid))
									{
										$response = $client->GetPriceId(array
										(
											'ID'								=> $thisid,
											'FormatCurrency'		=> 'РУБ',
											'SearchCross'				=> ($mode == 'article_page')?1:0,
										));
										
										if (!empty($response->GetPriceIdResult->BasePriceForClient))
										{
											$response = $response->GetPriceIdResult->BasePriceForClient;
											
											if (!is_array($response))
											{
												$response = array($response);
											}
											
											$array = $this->dtst_autopiter($response, array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all');
										}
									}
								}
							}
						}
					}
					
					return $array;
				}
				
				// Moskvorechie
				elseif ($this->local->vendor_apis[$vendor_id]->api_id == 'moskvorechie')
				{
					$array = array();
					
					$login			= $this->local->vendor_apis[$vendor_id]->api_key1;
					$key				= $this->local->vendor_apis[$vendor_id]->api_key2;
					
					$url = "http://portal.moskvorechie.ru/portal.api?l=$login&p=$key&cs=utf8&act=price_by_nr_firm&nr=$artnr&alt";
					
					if (!empty($brand))
						$url .= "&f=$brand";
					
					$ch = curl_init();
 
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array
					(
						'Content-type: application/x-www-form-urlencoded',
						'Accept: application/json',
					));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					 
					$response = curl_exec($ch);
					
					if ($this->is_return_raw)
						return $response;
					
					$response = json_decode($response);

					curl_close($ch);
					
					if (empty($response->errorCode))
					{
						$array = $this->dtst_moskvorechie($response, array('brand'=>$brand, 'artnr'=>$artnr), $vendor_data, ($mode == 'thisnr')?'lowest_prices':'all');
					}
					
					return $array;
				}
			}
		}
	}
	
	// DATASETS
	
	public function dtst_emex($dataset, $thisart, $vendor_data, $mode = 'all')
	{
		// Cast $thisart to object;		
		$thisart = (object) $thisart;
		
		// Check authorisation ('cause we offer price discounts for some users)
		if ($this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		// Article Types
		$art_types = array
		(
			'Original'									=> 'thisart',
			'NewNumber'									=> 'newnumber',
			'ReplacementOriginal'				=> 'replacement',
			'ReplacementNonOriginal'		=> 'analogue',
			'unknown'										=> 'analogue', // In case we get an unknown type
		);
		
		$array = array();
		$lowest_prices = array();
		
		foreach ($dataset as $item)
		{
			if ($item->Quantity != 'null' and $item->Quantity != null)
			{
				$new_item = (object) array
				(
					'art_number'				=> $item->DetailNum,
					'art_number_clear'	=> $this->appflow->qprep($item->DetailNum, 'art_nr'),
					'sup_brand'					=> $item->MakeName,
					'sup_brand_clear'		=> $this->appflow->qprep($item->MakeName, 'sup_brand'),
					'art_type'					=> (isset($art_types[$item->PriceGroup]))?$art_types[$item->PriceGroup]:'analogue',
					'description'				=> $item->DetailNameRus,
					'price'							=> $item->ResultPrice,
					'qty'								=> $item->Quantity, // For transition (it is more logical to 
					'qty_limit'					=> $item->Quantity,
					'qty_lot_size'			=> $item->LotQuantity,
					'vendor_name'				=> $vendor_data->vendor_name . '/' . $item->PriceLogo,
					'vendor_id'					=> $vendor_data->id,
					'delivery_days'			=> $item->ADDays + $vendor_data->delivery_days,
					'dd_percent'				=> $item->DDPercent,
					'discount'					=> $discount,
					'hash'							=> null,
					'hash_short'				=> null,
					'price_f'						=> null,
				);
				
				$new_item->price				= $new_item->price*$vendor_data->price_correction;
				$new_item->price_orig		= $new_item->price;
				$new_item->price_dscnt	= ($discount > 0)?($new_item->price * (1-$discount/100)):$new_item->price;
				$new_item->price_f			= price_format($new_item->price_dscnt, $discount);
				
				// Add soft hyphens to prevent long words that break layout
				$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
				
				$new_item->brands_match = $this->stock->is_similar_brands($new_item->sup_brand_clear, $this->appflow->qprep($thisart->brand, 'sup_brand'));
				$new_item->brand_requested = $thisart->brand;
				
				$new_item->is_primary = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
				
				// Hashes should be the last things added
				$new_item->hash					= $this->hash($new_item);
				$new_item->hash_short		= $this->hash_short($new_item);
				
				$array[] = $new_item;
				
				if (!isset($lowest_prices[$new_item->hash_short]) or $lowest_prices[$new_item->hash_short]->price > $new_item->price)
				{
					$lowest_prices[$new_item->hash_short] = $new_item;
				}
			}
		}
		
		if ($mode == 'all')
		{
			return $array;
		}
		elseif ($mode == 'lowest_prices')
		{
			return $lowest_prices;
		}
	}
	
	public function dtst_mikado($dataset, $thisart, $vendor_data, $mode = 'all')
	{
		// Cast $thisart to object;		
		$thisart = (object) $thisart;
		
		// Check authorisation ('cause we offer price discounts for some users)
		if ($this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		// Article Types
		$art_types = array
		(
			'OEM'												=> 'analogue',
			'Aftermarket'								=> 'original',
			'Analog'										=> 'analogue',
			'AnalogOEM'									=> 'analogue',
			'unknown'										=> 'analogue', // In case we get an unknown type
		);
		
		$array = array();
		$lowest_prices = array();
		
		// Mikado may return an object with one article instead of an array
		if (empty($dataset))
			$dataset = array();
		elseif (!is_array($dataset))
			$dataset = array($dataset);
		
		foreach ($dataset as $item)
		{
			// Workaround for 'АНАЛОГИ ПРОЧИЕ (БРЭНД НЕИЗВЕСТЕН). ВНИМАНИЕ!!! ТОЛЬКО ДЛЯ ИНФОРМАЦИИ! ВОЗМОЖНЫ ОШИБКИ!!!'
			if (mb_stripos($item->Brand, "аналоги") !== FALSE)
				continue;
			
			if ($item->CodeType == 'Aftermarket' or $item->CodeType == 'Analog' or $item->CodeType == 'NotDefined' or isset($item->jb_mikado_processed))
			{
				if (empty($item->OnStock) and !empty($item->Srock) and $item->Srock == '?')
					continue;
				
				if (!empty($item->Srock))
					$this_item_delivery_days = $this->appflow->qprep($item->Srock, 'ddays');
				else
					$this_item_delivery_days = 0;
				
				if (!empty($item->OnStock))
					$this_item_qty = $this->appflow->qprep($item->OnStock, 'qty');
				else
					$this_item_qty = 101;
			
				$new_item = (object) array
				(
					'art_number'				=> $item->ProducerCode,
					'art_number_clear'	=> $this->appflow->qprep($item->ProducerCode, 'art_nr'),
					'sup_brand'					=> $item->Brand,
					'sup_brand_clear'		=> $this->appflow->qprep($item->Brand, 'sup_brand'),
					'art_type'					=> (isset($art_types[$item->CodeType]))?$art_types[$item->CodeType]:'analogue',
					'description'				=> $item->Name,
					'price'							=> $item->PriceRUR,
					'qty'								=> $this_item_qty, // For transition (it is more logical to 
					'qty_limit'					=> $this_item_qty,
					'qty_lot_size'			=> 1,
					'vendor_name'				=> $vendor_data->vendor_name . '/' . $item->Supplier,
					'vendor_id'					=> $vendor_data->id,
					'delivery_days'			=> $this_item_delivery_days + $vendor_data->delivery_days,
					'discount'					=> $discount,
					'hash'							=> null,
					'hash_short'				=> null,
					'price_f'						=> null,
				);
				
				if (!empty($item->Rating))
					$new_item->dd_percent = $item->Rating;
				
				// Add soft hyphens to prevent long words that break layout
				$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
				
				$new_item->price				= $new_item->price*$vendor_data->price_correction;
				$new_item->price_orig		= $new_item->price;
				$new_item->price_dscnt	= ($discount > 0)?($new_item->price * (1-$discount/100)):$new_item->price;
				$new_item->price_f			= price_format($new_item->price_dscnt, $discount);
				
				$new_item->brands_match = $this->stock->is_similar_brands($new_item->sup_brand_clear, $this->appflow->qprep($thisart->brand, 'sup_brand'));
				$new_item->brand_requested = $thisart->brand;
				
				$new_item->is_this_art = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
				$new_item->is_primary = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
				
				// Hashes should be the last things added
				$new_item->hash					= $this->hash($new_item);
				$new_item->hash_short		= $this->hash_short($new_item);
				
				$array[] = $new_item;
				
				if (!isset($lowest_prices[$new_item->hash_short]) or $lowest_prices[$new_item->hash_short]->price > $new_item->price)
				{
					$lowest_prices[$new_item->hash_short] = $new_item;
				}
			}
			// Getting more articles with alternate method
			elseif (!empty($item->ZakazCode))
			{					
				$items_plus = $this->client->Code_Info(array
				(
					'ZakazCode'						=> $item->ZakazCode,
					'ClientID'						=> $vendor_data->api_key1,
					'Password'						=> $vendor_data->api_key2,
				));
				
				// jb_dump($items_plus);
				
				$items_plus_dataset = array();
				
				if (!is_array($items_plus->Code_InfoResult->Prices->Code_PriceInfo))
					$items_plus->Code_InfoResult->Prices->Code_PriceInfo = array($items_plus->Code_InfoResult->Prices->Code_PriceInfo);
				
				foreach ($items_plus->Code_InfoResult->Prices->Code_PriceInfo as $item_plus_el)
				{
					// if ($item_plus_el->OnStock == 0)
						// continue;
					
					$item_plus_dataset = (object) array
					(
						'Supplier'				=> $items_plus->Code_InfoResult->Supplier . '/' . $items_plus->Code_InfoResult->Country,
						'ProducerCode' 		=> $item->ProducerCode,
						'Brand' 					=> $item->Brand,
						'CodeType'				=> $item->CodeType,
						'Name' 						=> $item->Name,
						
						'Srock' 					=> $item_plus_el->Srock,
						'PriceRUR' 				=> $item_plus_el->PriceRUR,
						'OnStock' 				=> $item_plus_el->OnStock,
						
						'jb_mikado_processed'	=> TRUE,
					);
					
					// jb_dump($item_plus_dataset);
					
					$items_plus_dataset[] = $item_plus_dataset;
				}
				if ($mode == 'all')
				{
					$array = array_merge($array, $this->dtst_mikado($items_plus_dataset, array('brand'=>$thisart->brand, 'artnr'=>$thisart->artnr), $vendor_data, $mode));
				}
				elseif ($mode == 'lowest_prices')
				{
					$lowest_prices = array_merge($lowest_prices, $this->dtst_mikado($items_plus_dataset, array('brand'=>$thisart->brand, 'artnr'=>$thisart->artnr), $vendor_data, $mode));
				}
			}
		}
		
		if ($mode == 'all')
		{
			return $array;
		}
		elseif ($mode == 'lowest_prices')
		{
			return $lowest_prices;
		}
	}
	
	public function dtst_adeopro($xml, $thisart, $vendor_data, $mode = 'all')
	{
		// Cast $thisart to object
		$thisart = (object) $thisart;
		
		// Check authorisation ('cause we offer price discounts for some users)
		if ($this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		// Init XMLReader
		$z = new XMLReader;
		
		// String to process
		$z->xml($xml, 'UTF-8');
		
		// The list of cells in a file
		$nodesMatch = array
		(
			'caption',
			'code',
			'delivery',
			'deliverydays',
			'price',
			'producer',
			'rest',
			'stock',
		);
		
		// We log nodes to distinguish opening/closing nodes
		$open_nodes_log = array();
		
		// Items list
		$array = array();
		
		// Traverse
		while ($z->read())
		{
			$_is_closing_tag = FALSE;
			
			if (isset($open_nodes_log[$z->depth]))
			{
				if ($z->name == $open_nodes_log[$z->depth])
				{
					$_is_closing_tag = TRUE;
				}
			}
			
			$open_nodes_log[$z->depth] = $z->name;
			
			if (in_array($z->name, $nodesMatch) and !$_is_closing_tag and !$z->isEmptyElement)
			{
				$cell_contents = $z->readString();
				$item[$z->name] = $cell_contents;
			}
			elseif ($z->name == 'detail' and !$_is_closing_tag)
			{
				$item = array();
			}
			elseif ($z->name == 'detail' and $_is_closing_tag)
			{
				$item = (object) $item;
				
				$item->rest = $this->appflow->qprep($item->rest, 'qty');
				
				$new_item = (object) array
				(
					'art_number'				=> $item->code,
					'art_number_clear'	=> $this->appflow->qprep($item->code, 'art_nr'),
					'sup_brand'					=> $item->producer,
					'sup_brand_clear'		=> $this->appflow->qprep($item->producer, 'sup_brand'),
					// 'art_type'					=> (isset($art_types[$item->PriceGroup]))?$art_types[$item->PriceGroup]:'analogue',
					'description'				=> $item->caption,
					'price'							=> $item->price,
					'qty'								=> $item->rest, // For transition (it is more logical to use qty_limit)
					'qty_limit'					=> $item->rest,
					// 'qty_lot_size'			=> $item->LotQuantity,
					'vendor_name'				=> $vendor_data->vendor_name . '/' . $item->stock,
					'vendor_id'					=> $vendor_data->id,
					'delivery_days'			=> ceil(($item->deliverydays + $item->delivery)/2) + $vendor_data->delivery_days,
					// 'dd_percent'				=> $item->DDPercent,
					'discount'					=> $discount,
					'hash'							=> null,
					'hash_short'				=> null,
					'price_f'						=> null,
				);
				
				$new_item->price				= $new_item->price*$vendor_data->price_correction;
				$new_item->price_orig		= $new_item->price;
				$new_item->price_dscnt	= ($discount > 0)?($new_item->price * (1-$discount/100)):$new_item->price;
				$new_item->price_f			= price_format($new_item->price_dscnt, $discount);
				
				// Add soft hyphens to prevent long words that break layout
				$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
				
				$new_item->brands_match = $this->stock->is_similar_brands($new_item->sup_brand_clear, $this->appflow->qprep($thisart->brand, 'sup_brand'));
				$new_item->brand_requested = $thisart->brand;
				
				$new_item->is_primary = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
				
				// Hashes should be the last things added
				$new_item->hash					= $this->hash($new_item);
				$new_item->hash_short		= $this->hash_short($new_item);
				
				$array[] = $new_item;
			}
		}
			
		// Close XML
		$z->close();
		
		return $array;
	}
	
	public function dtst_berg($dataset, $thisart, $vendor_data, $mode = 'all')
	{
		// Cast $thisart to object;		
		$thisart = (object) $thisart;
		
		// Check authorisation ('cause we offer price discounts for some users)
		if ($this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		$array = array();
		$lowest_prices = array();
		
		foreach ($dataset->resources as $res)
		{
			// Common data for each item
			$description				= $res->name;
			$art_number					= $res->article;
			$art_number_clear 	= $this->appflow->qprep($art_number, 'art_nr');
			$sup_brand					= $res->brand->name;
			$sup_brand_clear		= $this->appflow->qprep($sup_brand, 'sup_brand');
			
			foreach ($res->offers as $item)
			{
				// Sometimes berg returns non-existent articles
				if ($item->quantity == 0)
					continue;
				
				$new_item = (object) array
				(
					'art_number'				=> $art_number,
					'art_number_clear'	=> $art_number_clear,
					'sup_brand'					=> $sup_brand,
					'sup_brand_clear'		=> $sup_brand_clear,
					'art_type'					=> 'thisart',
					'description'				=> $description,
					'price'							=> $item->price,
					'qty'								=> $item->quantity, // For transition (it is more logical to 
					'qty_limit'					=> $item->quantity,
					// 'qty_lot_size'			=> $item->LotQuantity,
					'vendor_name'				=> $vendor_data->vendor_name . '/' . $item->warehouse->name,
					'vendor_id'					=> $vendor_data->id,
					'delivery_days'			=> $item->average_period + $vendor_data->delivery_days,
					'dd_percent'				=> $item->reliability,
					'discount'					=> $discount,
					'hash'							=> null,
					'hash_short'				=> null,
					'price_f'						=> null,
				);
				
				$new_item->price				= $new_item->price*$vendor_data->price_correction;
				$new_item->price_orig		= $new_item->price;
				$new_item->price_dscnt	= ($discount > 0)?($new_item->price * (1-$discount/100)):$new_item->price;
				$new_item->price_f			= price_format($new_item->price_dscnt, $discount);
				
				// Add soft hyphens to prevent long words that break layout
				$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
				
				// jb_dump($thisart->brand);
				
				$new_item->brands_match = $this->stock->is_similar_brands($new_item->sup_brand_clear, $this->appflow->qprep($thisart->brand, 'sup_brand'));
				$new_item->brand_requested = $thisart->brand;
				
				$new_item->is_primary = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
				
				// Hashes should be the last things added
				$new_item->hash					= $this->hash($new_item);
				$new_item->hash_short		= $this->hash_short($new_item);
				
				if ($mode == 'lowest_prices')
				{
					$lowest_prices[$new_item->hash_short] = $new_item;
					break;
				}
				
				$array[] = $new_item;
			}
		}
		
		if ($mode == 'all')
		{
			return $array;
		}
		elseif ($mode == 'lowest_prices')
		{
			return $lowest_prices;
		}
		
	}
	
	public function berg_pull_data($artnr, $brand, $key, $mode = 'thisnr')
	{
		$url = 'https://api.berg.ru/ordering/get_stock.json?items[0][resource_article]=' . $artnr . '&key=' . $key;
					
		if ($brand)
			$url .= '&items[0][brand_name]=' . $brand;
		
		if ($mode == 'article_page')
		{
			$url .= '&analogs=1';
		}
		
		$ch = curl_init();
		 
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		$response = curl_exec($ch);
		
		curl_close($ch);
		
		return $response;
	}
	
	public function dtst_weloveparts ($dataset, $thisart, $vendor_data, $mode = 'all')
	{
		// Cast $thisart to object;		
		$thisart = (object) $thisart;
		
		// Check authorisation ('cause we offer price discounts for some users)
		if ($this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		$array = array();
		$lowest_prices = array();
		
		foreach ($dataset as $item)
		{
			$new_item = (object) array
			(
				'art_number'						=> $item->number,
				'art_number_clear'			=> $this->appflow->qprep($item->number, 'art_nr'),
				'sup_brand'							=> $item->brand,
				'sup_brand_clear'				=> $this->appflow->qprep($item->brand, 'sup_brand'),
				'prices_brand_clear'		=> $this->appflow->qprep($item->brand, 'sup_brand'),
				'SUP_BRAND_ORIGINAL'		=> $this->appflow->qprep($item->brand, 'sup_brand'),
				// 'art_type'					=> 'thisart',
				'description'				=> $item->description,
				'price'							=> $item->price,
				'qty'								=> $item->availability, // For transition (it is more logical to 
				'qty_limit'					=> $item->availability,
				// 'qty_lot_size'			=> $item->LotQuantity,
				'vendor_name'				=> $vendor_data->vendor_name,
				'vendor_id'					=> $vendor_data->id,
				'delivery_days'			=> $item->deliveryPeriod + $vendor_data->delivery_days,
				'dd_percent'				=> $item->deliveryProbability,
				'discount'					=> $discount,
				'hash'							=> null,
				'hash_short'				=> null,
				'price_f'						=> null,
			);
			
			$new_item->price				= $new_item->price*$vendor_data->price_correction;
			$new_item->price_orig		= $new_item->price;
			$new_item->price_dscnt	= ($discount > 0)?($new_item->price * (1-$discount/100)):$new_item->price;
			$new_item->price_f			= price_format($new_item->price_dscnt, $discount);
			
			// Add soft hyphens to prevent long words that break layout
			$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
			
			$new_item->brands_match = $this->stock->is_similar_brands($new_item->sup_brand_clear, $this->appflow->qprep($thisart->brand, 'sup_brand'));
			$new_item->brand_requested = $thisart->brand;
			
			$new_item->is_primary = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
			
			// Hashes should be the last things added
			$new_item->hash					= $this->hash($new_item);
			$new_item->hash_short		= $this->hash_short($new_item);
			
			if ($mode == 'lowest_prices')
			{
				$lowest_prices[$new_item->hash_short] = $new_item;
				break;
			}
			
			$array[] = $new_item;
		}
		
		if ($mode == 'all')
		{
			return $array;
		}
		elseif ($mode == 'lowest_prices')
		{
			return $lowest_prices;
		}
	}
	
	public function dtst_moskvorechie ($dataset, $thisart, $vendor_data, $mode = 'all')
	{
		// Cast $thisart to object;		
		$thisart = (object) $thisart;
		
		// Check authorisation ('cause we offer price discounts for some users)
		if ($this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		$array = array();
		$lowest_prices = array();
		
		foreach ($dataset ->result as $item)
		{
			$new_item = (object) array
			(
				'art_number'						=> $item->nr,
				'art_number_clear'			=> $this->appflow->qprep($item->nr, 'art_nr'),
				'sup_brand'							=> $item->brand,
				'sup_brand_clear'				=> $this->appflow->qprep($item->brand, 'sup_brand'),
				'prices_brand_clear'		=> $this->appflow->qprep($item->brand, 'sup_brand'),
				'SUP_BRAND_ORIGINAL'		=> $this->appflow->qprep($item->brand, 'sup_brand'),
				// 'art_type'					=> 'thisart',
				'description'				=> $item->name,
				'price'							=> $item->price,
				'qty'								=> $item->stock, // For transition (it is more logical to 
				'qty_limit'					=> $item->stock,
				'qty_lot_size'			=> $item->minq,
				'vendor_name'				=> $vendor_data->vendor_name,
				'vendor_id'					=> $vendor_data->id,
				'delivery_days'			=> $item->delivery + $vendor_data->delivery_days,
				'dd_percent'				=> $item->deliveryProbability,
				'discount'					=> $discount,
				'hash'							=> null,
				'hash_short'				=> null,
				'price_f'						=> null,
			);
			
			$new_item->price				= $new_item->price*$vendor_data->price_correction;
			$new_item->price_orig		= $new_item->price;
			$new_item->price_dscnt	= ($discount > 0)?($new_item->price * (1-$discount/100)):$new_item->price;
			$new_item->price_f			= price_format($new_item->price_dscnt, $discount);
			
			// Add soft hyphens to prevent long words that break layout
			$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
			
			$new_item->brands_match = $this->stock->is_similar_brands($new_item->sup_brand_clear, $this->appflow->qprep($thisart->brand, 'sup_brand'));
			$new_item->brand_requested = $thisart->brand;
			
			$new_item->is_primary = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
			
			// Hashes should be the last things added
			$new_item->hash					= $this->hash($new_item);
			$new_item->hash_short		= $this->hash_short($new_item);
			
			if ($mode == 'lowest_prices')
			{
				$lowest_prices[$new_item->hash_short] = $new_item;
				break;
			}
			
			$array[] = $new_item;
		}
		
		if ($mode == 'all')
		{
			return $array;
		}
		elseif ($mode == 'lowest_prices')
		{
			return $lowest_prices;
		}
	}
	
	public function dtst_autopiter ($dataset, $thisart, $vendor_data, $mode = 'all')
	{
		// Cast $thisart to object;		
		$thisart = (object) $thisart;
		
		// Check authorisation ('cause we offer price discounts for some users)
		if ($this->access->_is_auth())
		{
			$discount = $this->access->accdata->discount;
		}
		else
		{
			$discount = 0;
		}
		
		$array = array();
		$lowest_prices = array();
		
		foreach ($dataset as $item)
		{
			$new_item = (object) array
			(
				'art_number'				=> $item->Number,
				'art_number_clear'	=> $this->appflow->qprep($item->Number, 'art_nr'),
				'sup_brand'					=> $item->NameOfCatalog,
				'sup_brand_clear'		=> $this->appflow->qprep($item->NameOfCatalog, 'sup_brand'),
				// 'art_type'					=> 'thisart',
				'description'				=> $item->NameRus,
				'price'							=> $item->SalePrice,
				'qty'								=> empty($item->NumberOfAvailable)?99:$item->NumberOfAvailable, // For transition
				'qty_limit'					=> empty($item->NumberOfAvailable)?99:$item->NumberOfAvailable,
				// 'qty_lot_size'			=> $item->LotQuantity,
				'vendor_name'				=> $vendor_data->vendor_name,
				'vendor_id'					=> $vendor_data->id,
				'delivery_days'			=> $item->NumberOfDaysSupply + $vendor_data->delivery_days,
				// 'dd_percent'				=> $item->deliveryProbability,
				'discount'					=> $discount,
				'hash'							=> null,
				'hash_short'				=> null,
				'price_f'						=> null,
			);
			
			$new_item->price				= $new_item->price*$vendor_data->price_correction;
			$new_item->price_orig		= $new_item->price;
			$new_item->price_dscnt	= ($discount > 0)?($new_item->price * (1-$discount/100)):$new_item->price;
			$new_item->price_f			= price_format($new_item->price_dscnt, $discount);
			
			// Add soft hyphens to prevent long words that break layout
			$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
			
			$new_item->brands_match = $this->stock->is_similar_brands($new_item->sup_brand_clear, $this->appflow->qprep($thisart->brand, 'sup_brand'));
			$new_item->brand_requested = $thisart->brand;
			
			$new_item->is_primary = ($new_item->brands_match === TRUE and $thisart->artnr == $new_item->art_number_clear)?TRUE:FALSE;
			
			// Hashes should be the last things added
			$new_item->hash					= $this->hash($new_item);
			$new_item->hash_short		= $this->hash_short($new_item);
			
			$array[] = $new_item;
		}
		
		return $array;
	}
	
	public function dtst_autopiter_nobrand ($dataset, $thisart = NULL, $vendor_data = NULL)
	{
		$array = array();
		
		foreach ($dataset as $item)
		{
			$new_item = (object) array
			(
				'art_number'				=> $item->ShortNumber,
				'art_number_clear'	=> $this->appflow->qprep($item->ShortNumber, 'art_nr'),
				'sup_brand'					=> $item->Name,
				'sup_brand_clear'		=> $this->appflow->qprep($item->Name, 'sup_brand'),
				'art_type'					=> null,
				'description'				=> $item->NameDetail,
				'price'							=> null,
				'qty'								=> null,
				'qty_limit'					=> null,
				'vendor_name'				=> $vendor_data->vendor_name,
				'vendor_id'					=> $vendor_data->id,
				'delivery_days'			=> null,
				'dd_percent'				=> null,
				'discount'					=> null,
				'hash'							=> null,
				'hash_short'				=> null,
				'price_f'						=> null,
			);
			
			// Add soft hyphens to prevent long words that break layout
			$new_item->description = preg_replace("/([^\s-]{5})([^\s-]{5})/u", "$1&shy;$2", $new_item->description);
			
			$new_item->brands_match = null;
			$new_item->brand_requested = null;
			
			$new_item->is_primary = false;
			
			// Hashes should be the last things added
			$new_item->hash					= $this->hash($new_item);
			$new_item->hash_short		= $this->hash_short($new_item);
			
			$array[] = $new_item;
		}
		
		return $array;
	}
	
	public function cache_get ($request)
	{
		/// @disabled
		return false;
		
		$request_hash = $this->cache_store_hash($request);
		
		$this->db->select('data')->where(array
		(
			'hash'			=> $request_hash,
			'time <'		=> time(),
		));
		
		$q = $this->db->get('vendor_apipull_cache');
		
		if ($q->num_rows() > 0)
		{
			$r = $q->row();
			return $r->data;
		}
		
		return false;
	}

	public function cache_store ($request, $response)
	{
//		$request_hash = $this->cache_store_hash($request);
		
		// $this->db->insert('vendor_apipull_cache', array
		// (
			// 'hash'			=> $request_hash,
			// 'data'			=> $response,
			// 'time'			=> time() +  $this->cache_store_time,
			
			// 'siteid'		=> _jb_siteid(),
		// ));
	}
	
	public function cache_store_hash ($request)
	{
		return md5($request->art_number_clear . $request->sup_brand_clear . $request->vendor_id . $request->type);
	}
}