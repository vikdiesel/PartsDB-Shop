<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mail Messaging Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Mail_msgs_model extends CI_Model
{
	/// JB sitedata cached
	var $jb_sitedata;
	
	var $sending_acc;
	var $sending_name;
	var $sending_domain;
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->jb_sitedata = _jb_sitedata();
		$this->load->library('email');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Preconfig
	 *
	 * Contains Email initialization routine. Called from within class functions.
	 *
	 * @todo Move domain name to configs
	 *
	 * @param	string $cart_id
	 * @return	int
	 */	
	public function preconfig($to, $void=NULL, $type='text', $from_addr = null, $from_name = null)
	{
		$this->email->initialize(array
		(
			'wordwrap'		=> FALSE,
			'mailtype'		=> $type,
		));

		if (!empty($from_addr) && !empty($from_name))
		{
			$this->email->from($from_addr, $from_name);
		}
		else
		{
			$sending_domain = $this->input->server('HTTP_HOST');
			$sending_acc = 'no-reply';
			$sending_name = $this->jb_sitedata->title;

			$this->email->from($sending_acc . '@' . $sending_domain, $sending_name);
		}
		
		$this->email->to($to);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Registration
	 *
	 * Message containing client's registration information, sent together with info of the first order.
	 *
	 * @param	string $email
	 * @param	string $pass
	 * @return	void
	 */	
	public function registration($email, $pass)
	{
		$this->preconfig($email);
		
		$this->email->subject('Ваша регистрация');
		$message = "Ваши e-mail и пароль для будущих заказов в магазине {$this->jb_sitedata->title}. Вводите при заказе, или здесь: %s\n\nE-mail: $email\nПароль: $pass";
		$site_url = site_url('user/login');

		$this->email->message(sprintf($message, $site_url));
		$this->email->send();
	}

	public function order_status_changed($email, $id, $vericode)
	{
		$this->preconfig($email);

		$this->email->subject("Изменение статуса заказа №$id");
		$message = "Уважаемый покупатель! Статус вашего заказа №$id в магазине автозапчастей " . _jb_sitedata('title') ." изменен.\n \nСсылка для просмотра статуса: " . site_url('order/' . $vericode) . "\n \nСпасибо!";

		$this->email->message($message);
		$this->email->send();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Registration_by_admin
	 *
	 * Message containing client's registration information, sent when administrator adds a user.
	 *
	 * @param	string $email
	 * @param	string $name
	 * @param	string $pass
	 * @return	void
	 */	
	public function registration_by_admin($email, $name, $pass)
	{
		$this->preconfig($email);
		
		$this->email->subject("$name, Ваша регистрация");
		$message = "Здравствуйте, $name!\n\nВаши e-mail и пароль для будущих заказов в магазине {$this->jb_sitedata->title}.\nВводите в момент заказа, или здесь: %s\n\nE-mail: $email\nПароль: $pass\n\n--\nВы были зарегистрированы администатором магазина (%s).";
		$site_url = site_url('user/login');
		$base_url = base_url();

		$this->email->message(sprintf($message, $site_url, $base_url));
		$this->email->send();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Order Finished
	 *
	 * Message sent to the client when an ordering process is complete.
	 * Contains the link to order status page
	 *
	 * @param	string $email
	 * @param	string $vericode
	 * @param	int $id
	 * @param	array $items
	 * @return	void
	 */	
	public function order_finished($email, $vericode, $id, $items)
	{
		$this->preconfig($email);
		$this->email->subject("Ваш заказ получен [№$id]");
		
		$order = '';
		
		foreach ($items as $item)
		{
			$order .= ' - ' . $item->art_number . ' ' . $item->sup_brand . ' ' . $item->description . ' [' . $item->qty . " шт.]\n";
		}
		$base_url = base_url();
		$status_link = site_url('order/' . $vericode);

		$this->email->message(sprintf("Ваш заказ в магазине %s оформлен.\n\n%s\n\nСтатус заказа и реквизиты для оплаты доступны по постоянной ссылке: %s", $base_url, $order, $status_link));
		$this->email->send();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * New Order
	 *
	 * New order notification which is sent to the website's owner
	 *
	 * @param	string $email
	 * @param	array $items
	 * @return	int
	 */	
	public function new_order($email, $items)
	{
		$this->preconfig($email);
		$this->email->subject('Новый заказ');
		
		$order = '';
		
		foreach ($items as $item)
		{
			$order .= ' - ' . $item->art_number . ' ' . $item->sup_brand . ' ' . $item->description . ' [' . $item->qty . " шт.]\n";
		}
		$admin_url = '(' . site_url('admin/orders') . ')';
		$base_url = base_url();

		$this->email->message(sprintf("В вашем магазине %s оформлен новый заказ.\n\n%s \nПодробная информация, управление статусом в административном разделе %s", $base_url, $order, $admin_url));
		$this->email->send();
	}
	
	public function order_to_vendor($email, $items, $client, $csv)
	{
		$this->preconfig($email);
		$this->email->subject("Заказ");
		
		$order = '';
		
		foreach ($items as $item)
		{
			$order .= ' - ' . $item->art_number . ' ' . $item->sup_brand . ' ' . $item->description . ' [' . $item->qty . " шт.]\n";
		}
		
		$this->email->message(sprintf("Здравствуйте! Примите пожалуйста в работу заказ.\nКлиент: %s\n\n%s \nЗаказ продублирован в прикрепленном файле CSV. Спасибо.", $client, $order));
		$this->email->attach($csv);
		$this->email->send();
	}
	
	// --------------------------------------------------------------------
	
	public function search_inquiry($email, $search, $name, $contact, $car)
	{
		$this->preconfig($email);
		$this->email->subject("Запрос на запчасть с номером $search");
		
		$this->email->message(sprintf("Здравствуйте! Был получен запрос на деталь. Информация ниже.\n\nИмя: %s\nКонтакт: %s\nЗапрос: %s\nАвтомобиль: %s", $name, $contact, $search, $car));
		$this->email->send();
	}
}

/* End of file mail_msgs_model.php */
/* Location: ./application/models/mail_msgs_model.php */