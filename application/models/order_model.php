<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Order Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Order_model extends CI_Model
{
	/// Timestamp cache. Used to ensure that timestamp in all lines of the order saved is equal.
	var $time = 0;
	
	// Properties with info for payment details
	var $show_payment_details		= FALSE;
	var $amount_unpaid					= 0;
	
	var $order_total						= 0;
	var $order_items_total			= 0;
	var $order_services_total		= 0;
	
	/// Properties for order list
	var $is_sample_orders				= FALSE;
	var $is_non_sample_exist		= FALSE;
	
	/// Order lines per page
	var $orders_per_page		= 20;
	
	/// Order statuses
	var $statuses = array
	(
		'1'	=> array
		(
			'name'					=> 'Ждет оплаты',
			'comment'				=> 'Ваш заказ ожидает оплаты. Информация по оплате указана в информации о заказе.',
			'class'					=> 'error',
			
			'is_final'				=> FALSE,
			'show_payment_details'	=> TRUE,
		),
		
		'2'	=> array
		(
			'name'					=> 'Получена оплата',
			'comment'				=> 'Оплата вашего заказа получена. В данный момент мы отправили запрос на подтверждение наличия поставщику.',
			'class'					=> 'info',

			'is_final'				=> FALSE,
		),
		
		// The number 8 is hardcoded in admin::orders_email_to_vendor
		'8'	=> array
		(
			'name'					=> 'В заказ поставщику',
			'comment'				=> 'Ваш заказ стоит в очереди на отправку поставщику.',
			'class'					=> 'info',

			'is_final'				=> FALSE,
		),
		
		// The number 9 is hardcoded in admin::orders_email_to_vendor
		'9'	=> array
		(
			'name'					=> 'Отправлен в заказ',
			'comment'				=> 'Ваш заказ отправлен поставщику. Мы ожидаем подтверждения.',
			'class'					=> 'info',

			'is_final'				=> FALSE,
		),

		'3'	=> array
		(
			'name'					=> 'Подтвержден поставщиком',
			'comment'				=> 'Поставщик подтвердил наличие на своем складе. В данный момент идет комплектование и подготовка вашего заказа к отправке.',
			'class'					=> 'info',
			
			'is_final'				=> FALSE,
		),
		
		'4'	=> array
		(
			'name'					=> 'Можно забирать на складе',
			'comment'				=> 'Ваш заказ находится на нашем складе. Вы можете забрать его в рабочие часы.',
			'class'					=> 'warning',

			'is_final'				=> FALSE,
		),

		'5'	=> array
		(
			'name'					=> 'Выдано',
			'comment'				=> 'Заказ получен вами и помечен как завершенный. Спасибо!',
			'class'					=> 'success',

			'is_final'				=> TRUE,
		),

		'6'	=> array
		(
			'name'					=> 'Отменен поставщиком',
			'comment'				=> 'Требуемые запчасти отсутствуют на складах поставщика (к сожалению такое бывает и мы не всегда владеем полной информацией по наличию). Заказ отменен. Вы можете либо забрать предоплаченный аванс, либо оформить другой заказ.',
			'class'					=> 'cancelled',
			
			'is_final'				=> TRUE,
		),
		
		'7'	=> array
		(
			'name'					=> 'Отправлен покупателю',
			'comment'				=> 'Ваш заказ отправлен к вам выбранным способом доставки.',
			'class'					=> 'success',

			'is_final'				=> TRUE,
		),
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
	 * Make
	 *
	 * Creates a new order for user specified by user_id (which can be 0 for no user).
	 *
	 * @param	int $user_id
	 * @param	array $cart_items
	 * @return	object
	 */	
	public function make($user_id, $cart_items, $add_to_order = NULL, $order_status = NULL)
	{		
		// Grab session_id
		$session_id = $this->session->userdata('session_id');
		
		// Get current timestamp
		$time			= time();
		
		if (empty($add_to_order))
		{
			// Generate vericode
			$vericode		= random_string('unique', 32);
			
			// Generate a human id for this order
			$human_id		= $this->human_id();
			
			// Insert order into DB
			$this->db->insert('orders', array
			(
				'user_id'					=> $user_id,
				'vericode'				=> $vericode,
				'date'						=> $time,
				'order_human_id'	=> $human_id,
				'order_status'		=> $order_status,
				'order_comment'		=> (!empty($cart_items['order_comment']))?$cart_items['order_comment']:null,
			));
			
			// Get an ID for this order
			$order_id = $this->db->insert_id();
		}
		else
		{
			$order_id = $add_to_order;
			$vericode = NULL;
			$human_id = NULL;
		}
		
		// For service orders we don't need cart for an order
		if (!empty($cart_items))
		{
			// Condition to update order_items
			$this->db->where(array
			(
				'cartid'		=> $session_id,
			));
			
			$this->db->update('order_items', array
			(
				'cartid'							=> null,
				'orderid'							=> $order_id,
				'discount'						=> $cart_items['discount'],
				'delivery_method'			=> $cart_items['delivery']->id,
				'status'							=> 1,
				'status_change_date'	=> $time,
			));
		}
		
		return (object) array
		(
			'id'					=> $order_id,
			'human_id'		=> $human_id,
			'vericode'		=> $vericode
		);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Human ID
	 *
	 * Generates a Human ID for an order. 
	 * In multisite installation, Order IDs generated by db can have numbers skipped.
	 *
	 * @return	int
	 */	
	public function human_id()
	{
		// Select the maximum
		$q = $this->db
		
			-> select_max('order_human_id')
			-> get('orders');
			
		// Generate the result
		$hid = $q->row()->order_human_id;
		
		// Add one
		$hid++;
		
		// and return
		return $hid;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Is Order
	 *
	 * Checks if order specified by orderid exists.
	 *
	 * @param	int $orderid
	 * @return	bool
	 */	
	public function is_order($orderid)
	{
		if ($this->db->where('id', $orderid)->count_all_results('orders') > 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function items_set_status ($order, $status)
	{
		foreach ($order as $o)
		{
			$this->items_update_dbcall($o->order_line_id, array('status'=>$status), TRUE, $o->order_id);
		}
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Items Update
	 *
	 * Update items in order.
	 *
	 * @warning Be shure to check $orderid before calling this method (especially which site does it belong to).
	 *
	 * @param	int $orderid
	 * @param	array $data
	 * @param	array $fields
	 * @param	bool $user_update_marker
	 * @param	bool $touch_user_updated_lines
	 * @return	void
	 */	
	public function items_update($orderid, $data, $fields = array('qty', 'price', 'status', 'discount', 'delivery_method'), $user_update_marker=TRUE, $touch_user_updated_lines=TRUE)
	{
		foreach ($data as $iid=>$dataline)
		{			
			if ($iid == 'new' and !empty($dataline['description']))
			{
				// We don't have all the data needed
				// So we grab this from existent order data
				$order = $this->order->get_all($orderid);
				
				$this->items_insert_dbcall($orderid, (object) $dataline, $order /* if the order is empty - there's a workaround */);
			}
			else
			{
				if ($dataline['qty'] == 0)
				{
					$this->item_delete_dbcall($iid, $orderid);
				}
				else
				{
					$upd = array();
					
					foreach ($fields as $field)
					{
						if (!empty($dataline[$field]))
							$upd[$field] = $dataline[$field];
					}
					
					// Mark order as changed by user
					if ($user_update_marker)
					{
						$upd['is_updated_by_user'] = 1;
					}
					
					$this->items_update_dbcall($iid, $upd, $touch_user_updated_lines, $orderid);
				}
			}
		}
	}
	// --------------------------------------------------------------------
	
	/**
	 * Items Update DBCALL
	 *
	 * Performs database call to update specific line of specific order.
	 *
	 * @param	int $lineid
	 * @param	int $orderid
	 * @param	array $upd
	 * @param	bool $touch_user_updated_lines
	 * @return	void
	 */	
	public function items_update_dbcall($lineid, $upd, $touch_user_updated_lines, $orderid = NULL)
	{
		if ($lineid)
			$this->db->where('id', $lineid);
			
		if (!$touch_user_updated_lines)
			$this->db->where('is_updated_by_user',0);
			
		if (!$this->time)
			$this->time = time();
			
		$upd['status_change_date'] = $this->time;
		
		$this->db->update('order_items', $upd);
		
		// Actually it should always be present
		// We check just for compatibility
		if (!empty($orderid))
			$this->sync_set_sync_status(0, $orderid);
	}
	
	public function items_insert_dbcall($orderid, $item, $order)
	{
		if (empty($order))
		{
			$common_data = (object) array
			(
				'discount'				=> 0,
				'delivery_method'	=> 0,
				'delivery_days'		=> 0,
			);
		}
		else
		{
			$common_data = (object) array
			(
				'discount'				=> $order[0]->discount,
				'delivery_method'	=> $order[0]->delivery_method,
				'delivery_days'		=> $order[0]->delivery_days,
			);
		}
		
		$this->db->insert('order_items', array
		(
			'cartid'											=> null,
			'orderid'											=> $orderid,
			'line_hash'										=> null,
			'art_number'									=> (!empty($item->art_number))?$item->art_number:NULL,
			'sup_brand'										=> (!empty($item->sup_brand))?$item->sup_brand:NULL,
			'description'									=> (!empty($item->description))?$item->description:NULL,
			'price'												=> (!empty($item->price))?$item->price:0,
			'qty'													=> (!empty($item->qty))?$item->qty:0,
			'qty_limit'										=> 100,
			'qty_lot_size'								=> 1,
			'discount'										=> (!empty($order[0]->discount))?$order[0]->discount:0,
			'delivery_method'							=> (!empty($order[0]->delivery_method))?$order[0]->discount:0,
			'vendor_name'									=> (!empty($item->vendor_name))?$item->art_number:NULL,
			'delivery_days'								=> (!empty($order[0]->delivery_days))?$order[0]->discount:0,
			'status'											=> 1,
			'status_change_date'					=> time(),
			'type'												=> (!empty($item->type))?$item->type:"item",
		));
		
		$this->sync_set_sync_status(0, $orderid);
	}
	
	private function item_delete_dbcall($lineid, $orderid = NULL)
	{
		$this->db->where('id', $lineid);

		$this->db->limit(1);
		
		$this->db->delete('order_items');
		
		// Actually it should always be present
		// We check just for compatibility
		if (!empty($orderid))
			$this->sync_set_sync_status(0, $orderid);
	}
	
	public function sync_set_inprogress($ordernums)
	{
		// Firstly, set all to false
		$this->db->update('orders', array('is_sync_inprogress' => 0));
		
		// Secondly, set necessary to true
		$this->db->where_in('id', $ordernums);
		$this->db->update('orders', array('is_sync_inprogress' => 1));
	}
	
	public function sync_set_synched()
	{
		$this->sync_set_sync_status(1);
	}
	
	public function sync_set_sync_status($status, $orderid = null)
	{
		if (!empty($orderid))
			$this->db->where('id', $orderid);
		else
			$this->db->where('is_sync_inprogress', 1);
			
		$this->db->update('orders', array('is_synched' => $status, 'is_sync_inprogress' => 0));
	}
	
	public function fetch_status_data($status_id, $d = array(), $return = 'array')
	{
		///@todo avoid repetion
		$d['order_statuses']					= $this->statuses;
		$d['order_statuses_service']	= $this->statuses_service;
		
		// This is a global status for the whole order
		// Used only with service orders
		if (!empty($status_id))
		{
			if (isset($this->statuses[$order->order_status]))
			{
				$d['order_status_data'] = (object) $this->statuses[$status_id];
				$d['order_status_data']->id = $status_id;
			}
			elseif (isset($this->statuses_service[$status_id]))
			{
				$d['order_status_data'] = (object) $this->statuses_service[$status_id];
				$d['order_status_data']->id = $status_id;
			}
		}
		
		if (!empty($status_id) and $return == 'this')
		{
			return $d['order_status_data'];
		}
		elseif ($return == 'object')
		{
			return (object) $d;
		}
		
		return $d;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get All
	 *
	 * Adds delivery method.
	 *
	 * @param	int|bool $orderid
	 * @param	string $column
	 * @param	int $page
	 * @param	array $return
	 * @return	array|int|bool
	 */	
	public function get_all ($orderid=FALSE, $column='orders.id', $page=0, $return='array', $order_by = 'orders.date desc')
	{
		$this->db->select
		('
			orders.user_id,
			orders.order_human_id,
			orders.id as order_id,
			orders.date as order_date, 
			orders.vericode,
			orders.is_archived,
			orders.order_status,
			orders.order_status_changed,
			orders.order_comment,

			order_items.art_number, 
			order_items.sup_brand, 
			order_items.description, 
			order_items.price, 
			order_items.discount,
			order_items.status,
			order_items.status_change_date,
			order_items.qty,
			order_items.id as order_line_id,
			order_items.delivery_days,
			order_items.delivery_method,
			order_items.vendor_id,
			order_items.type,
			
			delivery_methods.title as dmthd_title,
			delivery_methods.price as dmthd_price,
			
			users.id as user_id,
			users.email,
			users.is_sample,
			users.userdata,
			users.discount as user_default_discount,
			
			vendors.orderemail,
			vendors.ordername,
			order_items.vendor_name,
			
			(SELECT SUM(order_items.qty) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_qty_total,
			(SELECT SUM(order_items.price * order_items.qty) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_price_total,
			(SELECT MIN(order_items.status) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_status_min,
			(SELECT MAX(order_items.status) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_status_max,
		')
			->from('orders')
			->join('order_items', 'order_items.orderid = orders.id')
			->join('users', 'user_id = users.id', 'left')
			->join('delivery_methods', 'delivery_method = delivery_methods.id', 'left')
			->join('vendors', 'vendor_id = vendors.id', 'left')
			->order_by($order_by);
			
		if (is_array($orderid))
		{
			$this->db->where($orderid);
		}
		elseif ($orderid !== FALSE)
		{
			$this->db->where($column, $orderid);
		}
		
		if ($return == 'count')
		{
			return $this->db->count_all_results();
		}
		else
		{
			if (!empty($this->orders_per_page))
				$this->db->limit($this->orders_per_page, $page * $this->orders_per_page);
				
			$q = $this->db->get();
			
			if (strpos($return, '_allow_false') !== FALSE and $q->num_rows() == 0)
			{
				return FALSE;
			}
			
			$array = array();
			$array_services = array();
			
			// Reset properties
			$this->amount_unpaid = 0;
			$this->show_payment_details = FALSE;
			
			foreach ($q->result() as $r)
			{
				// Some vendors can have no e-mail
				$r->is_orderemail_available = (!empty($r->orderemail))?TRUE:FALSE;
				
				$r->is_statuses_equal = ($r->order_items_status_min == $r->order_items_status_max)?TRUE:FALSE;
				
				$r->userdata = (object) unserialize($r->userdata);
				
				// Sample trigger. Gives signal that some of the orders are sample.
				if ($r->is_sample and !$this->is_sample_orders)
					$this->is_sample_orders = TRUE;
					
				elseif (!$r->is_sample and !$this->is_non_sample_exist)
					$this->is_non_sample_exist = TRUE;
					
				// Subtotal
				if ($r->discount > 0)
				{
					$r->item_subtotal = $r->price * $r->qty * (100-$r->discount) / 100;
				}
				else
				{
					$r->item_subtotal = $r->price * $r->qty;
				}
				
				// ... and Totals
				if ($r->type == 'item')
				{
					$this->order_items_total += $r->item_subtotal;
				}
				else
				{
					$this->order_services_total += $r->item_subtotal;
				}
				
				$this->order_total += $r->item_subtotal;
				
				
				if (isset($this->statuses[$r->status]['show_payment_details']))
				{
					// Property that asks controller to show payment details
					$this->show_payment_details = TRUE;
					$this->amount_unpaid += $r->item_subtotal;
				}
				
				if (strpos($return, 'items_services_split') !== FALSE)
				{
					if ($r->type == 'item')
					{
						$array[] = $r;
					}
					else
					{
						$array_services[] = $r;
					}
				}
				else
				{
					$array[] = $r;
				}
			}

			if (strpos($return, 'items_services_split') !== FALSE)
			{
				if (!empty($array))
				{
					$meta = $array[0];
				}
				elseif (!empty($array_services))
				{
					$meta = $array_services[0];
				}
				else
				{
					$meta = (object) array();
				}

				$meta->num_items = count($array);
				$meta->num_services = count($array_services);

				return (object) array
				(
						'meta'			=> $meta,
						'items'			=> $array,
						'services'	=> $array_services,
				);
			}
			
			return $array;
		}
	}
	
	public function get_all_compact($orderid=FALSE, $column='orders.id', $page=0, $return='array', $order_by = 'orders.date desc')
	{
		$this->db->select
		('
			orders.user_id,
			orders.order_human_id,
			orders.id as order_id,
			orders.date as order_date, 
			orders.vericode,
			orders.is_archived,
			orders.order_status,
			orders.order_status_changed,

			users.email,
			users.is_sample,
			users.userdata,
			users.discount as user_default_discount,

			(SELECT SUM(order_items.qty) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_qty_total,
			(SELECT SUM(order_items.price * order_items.qty) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_price_total,
			(SELECT MIN(order_items.status) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_status_min,
			(SELECT MAX(order_items.status) FROM order_items WHERE order_items.orderid = orders.id) AS order_items_status_max,
		')
			->from('orders')
			->join('users', 'user_id = users.id', 'left')
			->order_by($order_by);
			
		if (is_array($orderid))
		{
			$this->db->where($orderid);
		}
		elseif ($orderid !== FALSE)
		{
			$this->db->where($column, $orderid);
		}
		
		if ($return == 'count')
		{
			return $this->db->count_all_results();
		}
		else
		{
			if (!empty($this->orders_per_page))
				$this->db->limit($this->orders_per_page, $page * $this->orders_per_page);
				
			$q = $this->db->get();
			
			if ($return == 'array_allow_false' and $q->num_rows() == 0)
			{
				return FALSE;
			}
			
			$array = array();
			
			foreach ($q->result() as $r)
			{			
				$r->is_statuses_equal = ($r->order_items_status_min == $r->order_items_status_max)?TRUE:FALSE;
				
				$r->userdata = (object) unserialize($r->userdata);
				
				// Sample trigger. Gives signal that some of the orders are sample.
				if ($r->is_sample and !$this->is_sample_orders)
					$this->is_sample_orders = TRUE;
					
				elseif (!$r->is_sample and !$this->is_non_sample_exist)
					$this->is_non_sample_exist = TRUE;
					
				$r->order_total = $r->order_items_qty_total * $r->order_items_price_total;
				
				if (!empty($r->order_status))
				{
					if (isset($this->statuses[$r->order_status]))
					{
						$r->order_status_data = (object) $this->statuses[$r->order_status];
					}
					elseif (isset($this->statuses_service[$r->order_status]))
					{
						$r->order_status_data = (object) $this->statuses_service[$r->order_status];
					}
				}
				
				$array[] = $r;
			}
			
			return $array;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Assign to user
	 *
	 * Assigns an order specified by orderid to user (which can be 0 for no user).
	 * Updates the discount's in the associated order to the user's basic param (skipping lines previously edited by administrator).
	 *
	 * @param	int $orderid
	 * @param	int $user
	 * @return	void
	 */	
	function assign_to_user($orderid, $user)
	{
		if ($user == 0 or ($user = $this->users->get($user)) !== FALSE)
		{
			// User can be anonymous
			if ($user == 0)
			{
				$user = (object) array
				(
					'id'					=> 0,
					'discount'		=> 0,
				);
			}
			
			// Update user_id for order
			$this->db
			
				->where('id', $orderid)

				->update('orders', array
				(
					'user_id'									=> $user->id,

					// Here we manually set synched status to false (to optimise number of DB calls)
					'is_synched'							=> 0,
					'is_sync_inprogress'			=> 0,
				));
				
			// Update discount to user's basic discount
			$this->items_update_dbcall(FALSE, array('discount'=>$user->discount), FALSE, $orderid);
		}
	}
	
	// A global status-setter
	public function status_set($id, $status)
	{
		if (!empty($this->statuses[$status]) or !empty($this->statuses_service[$status]))
		{
			$this->db
				-> where('id', $id)
				-> set('order_status', $status)
				-> set('order_status_changed', time())
				-> update('orders');
			
			return $this->db->affected_rows();
		}
		else
		{
			return false;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Archived Status Set
	 *
	 * Marks order either as archive or non-archive.
	 *
	 * @param	int $id
	 * @param	int $set
	 * @return	void
	 */	
	public function archived_status_set($id, $set)
	{
		$this->db
			
			->where(array
			(
				'id'						=> $id,
			))
			
			->update('orders', array
			(
				'is_archived'							=> $set,
				
				// Here we manually set synched status to false (to optimise number of DB calls)
				'is_synched'							=> 0,
				'is_sync_inprogress'			=> 0,
			));
	}
	
	public function csv($fp, $order)
	{
		if ($fp == false)
		{
			header('Content-type: text/csv');
			header("Content-Disposition: attachment; filename=order.csv");
			
			$fp = fopen('php://output', 'w');
		}
		
		fputcsv($fp, array('#', iconv("UTF-8", "WINDOWS-1251", 'Дата'), iconv("UTF-8", "WINDOWS-1251", 'Артикул'), iconv("UTF-8", "WINDOWS-1251", 'Бренд'), iconv("UTF-8", "WINDOWS-1251", 'Наименование'), iconv("UTF-8", "WINDOWS-1251", 'Количество')), ";");
			
		foreach ($order as $o)
		{
			fputcsv($fp, array($o->order_human_id, date('d.m.Y H:i', $o->order_date), $o->art_number, $o->sup_brand, iconv("UTF-8", "WINDOWS-1251", $o->description), $o->qty), ";");
		}
	}
}

/* End of file order_model.php */
/* Location: ./application/models/order_model.php */