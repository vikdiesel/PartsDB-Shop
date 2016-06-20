<?php

class Setup_helper_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
    }

	function setup ($regdata, $is_setup_sample_data = TRUE)
	{
		if (is_array($regdata))
		{
			$regdata = (object) $regdata;
		}
		elseif (!is_object($regdata))
		{
			return FALSE;
		}
		
		$_ids = new stdClass();
		$time = time();
		
		// ----------
		// Sites
		// ----------

        $regdata->adminpass = md5($regdata->adminpass);
		$this->db->insert('sites', $regdata);

		if ($is_setup_sample_data)
		{
			// ----------
			// Delivery methods
			// ----------
			
			$this->db->insert('delivery_methods', array
			( 
				'title'		=> 'Самовывоз',
				'price'		=> 0,
			));
			
			$_ids->pickup = $this->db->insert_id();
			
			$this->db->insert('delivery_methods', array
			( 
				'title'		=> 'Доставка курьером',
				'price'		=> 300,
			));
			
			$_ids->courier = $this->db->insert_id();
			
			// ----------
			// Vendors
			// ----------
			
			$this->db->insert('vendors', array
			(
				'vendor_name'	=> 'Наш склад',
				'allow_delete'	=> 0,
				'is_primary'	=> 1,
				'last_update'	=> $time,
			));
			
			$_ids->vendor1 = $this->db->insert_id();
			
			$this->db->insert('vendors', array
			(
				'vendor_name'	=> 'Склад партнера №1',
				'allow_delete'	=> 1,
				'last_update'	=> $time,
			));
			
			$_ids->vendor2 = $this->db->insert_id();
			
			$this->db->insert('vendors', array
			(
				'vendor_name'	=> 'Склад партнера №2',
				'allow_delete'	=> 1,
				'last_update'	=> $time,
			));
			
			$_ids->vendor3 = $this->db->insert_id();
			
			// ----------
			// Users
			// ----------
			
			$this->db->insert('users', array
			(
				'email'			=> 'sample-user-1@example.com',
				'password'		=> '28683347',
				'vericode'		=> 'ebaf28429b213a9c71598a0eb1b86f7c',
				'discount'		=> 0,
				'userdata'		=> 'a:3:{s:4:"name";s:21:"Егоров Олег";s:5:"phone";s:16:"(000) 777-777-77";s:7:"address";s:56:"123000, г. Москва, ул. Неглинная, 1-22";}',
				'is_sample'		=> 1,
			));
			
			$_ids->user1 = $this->db->insert_id();
			
			$this->db->insert('users', array
			(
				'email'			=> 'sample-user-2@example.com',
				'password'		=> '28683347',
				'vericode'		=> 'ebaf28429b213a9c71598a0eb1b86f7c',
				'discount'		=> 5,
				'userdata'		=> 'a:3:{s:4:"name";s:35:"Харрисон Александр";s:5:"phone";s:16:"(000) 777-777-76";s:7:"address";s:55:"123000, г. Москва, ул. Тверская, 22-44";}',
				'is_sample'		=> 1,
			));
			
			$_ids->user2 = $this->db->insert_id();
			
			$this->db->insert('users', array
			(
				'email'			=> 'sample-user-3@example.com',
				'password'		=> '28683347',
				'vericode'		=> 'ebaf28429b213a9c71598a0eb1b86f7c',
				'discount'		=> 0,
				'userdata'		=> 'a:3:{s:4:"name";s:27:"Неглинный Иван";s:5:"phone";s:16:"(343) 000-777-33";s:7:"address";s:66:"333000, г. Екатеринбург, ул. Высоцкого, 18";}',
				'is_sample'		=> 1,
			));
			
			$_ids->user3 = $this->db->insert_id();
			
			$this->db->insert('users', array
			(
				'email'			=> 'sample-user-4@example.com',
				'password'		=> '28683347',
				'vericode'		=> 'ebaf28429b213a9c71598a0eb1b86f7c',
				'discount'		=> 5,
				'userdata'		=> 'a:3:{s:4:"name";s:31:"Березина Евгения";s:5:"phone";s:16:"(800) 400-000-44";s:7:"address";s:92:"222000, г. Санкт-Петербург, ул. Морская Набережная, 188-45";}',
				'is_sample'		=> 1,
			));
			
			$_ids->user4 = $this->db->insert_id();
			
			// ----------
			// Orders
			// ----------
			
			$times = array
			(
				$time - 3600 * 24 * 1, 
				$time - 3600 * 24 * 2, 
				$time - 3600 * 23 * 3, 
				$time - 3600 * 22 * 4, 
				$time - 3600 * 21 * 5, 
				$time - 3600 * 20 * 6
			);
			
			$this->db->insert('orders', array
			(
				'user_id'			=> $_ids->user1,
				'vericode'			=> '1a60c8b3e102c2a639beb8d1b45650bc',
				'date'				=> $times[5],
				'order_human_id'	=> 1,
			));
			$_ids->order1 = $this->db->insert_id();
			
			$this->db->insert('orders', array
			(
				'user_id'			=> $_ids->user2,
				'vericode'			=> 'e6fca4e56459ab67b13a4a84f2580043',
				'date'				=> $times[4],
				'order_human_id'	=> 2,
			));
			$_ids->order2 = $this->db->insert_id();
			
			$this->db->insert('orders', array
			(
				'user_id'			=> $_ids->user3,
				'vericode'			=> '3c4d8db731f085448f1c2e702617f33d',
				'date'				=> $times[3],
				'order_human_id'	=> 3,
			));
			$_ids->order3 = $this->db->insert_id();
			
			$this->db->insert('orders', array
			(
				'user_id'			=> $_ids->user2,
				'vericode'			=> '7b3ed078a0c9ff930ec054e9dbaf881d',
				'date'				=> $times[2],
				'order_human_id'	=> 4,
			));
			$_ids->order4 = $this->db->insert_id();
			
			$this->db->insert('orders', array
			(
				'user_id'			=> $_ids->user4,
				'vericode'			=> 'd35dd3bf8769c99c3d368e63d2b78281',
				'date'				=> $times[1],
				'order_human_id'	=> 5,
			));
			$_ids->order5 = $this->db->insert_id();
			
			$this->db->insert('orders', array
			(
				'user_id'			=> $_ids->user2,
				'vericode'			=> 'fe8afdfe736f982259e6236b89dd0f80',
				'date'				=> $times[0],
				'order_human_id'	=> 6,
			));
			$_ids->order6 = $this->db->insert_id();
				
			
			$this->db->query
			("
				INSERT INTO `order_items` (`orderid`, `art_number`, `sup_brand`, `description`, `price`, `qty`, `discount`, `vendor_id`, `vendor_name`, `delivery_days`, `delivery_method`, `status`, `status_change_date`) VALUES
				($_ids->order1, '334371',	'KYB',			'Стойка амортизационная - Excel-G перед. лев. KYB',					3279.74, 1, 0, 0,	'Наш склад', 0,			$_ids->pickup, 5, $times[5]),
				($_ids->order1, 'dox-0306',	'Denso',		'Лямбда-зонд Forester, Impreza 2.0',								2692.80, 1, 0, 0,	'Наш склад', 0,			$_ids->pickup, 5, $times[5]),
				($_ids->order2, '2329900',	'UFI',			'Фильтр ufi',														131.63, 5, 5, 0,	'А-Ю Москва', 5,		$_ids->courier, 3, $times[4]),
				($_ids->order2, 'LC-1502',	'Lynx',			'Масляный фильтр lynx',												82.74, 1, 5, 0,		'А-Ю Москва', 5,		$_ids->courier, 3, $times[4]),
				($_ids->order2, 'OC405/3',	'Knecht',		'Фильтр масляный OPEL ASTRA G/H/VECTRA C/ZAFIRA 1.4-2.0',			168.16, 1, 5, 0,	'МСК Москва', 5,		$_ids->courier, 3, $times[4]),
				($_ids->order3, 'OC617',	'KNECHT',		'Маслянный фильтр ACCORD IX',										123.17, 1, 0, 0,	'РИК СПб.вираж', 5,		$_ids->pickup, 4, $times[3]),
				($_ids->order3, 'J1314013',	'NIPPARTS',		'Фильтр масляный HONDA ACCORD 2.0/2.4 07-/CIVIC 1.4/1.5/1.6 98-',	120.38, 1, 0, 0,	'СМК Москва', 5,		$_ids->pickup, 4, $times[3]),
				($_ids->order3, 'fo410s',	'JapanParts',	'Фильтр масляный',													78.40, 1, 0, 0,		'Наш склад', 0,			$_ids->pickup, 5, $times[3]),
				($_ids->order4, '2071',		'FEBI',			'Подшипник ступ.MB W202/W124/W210 93-03 пер.',						612.25, 1, 0, 0,	'СМК Москва', 5,		$_ids->pickup, 1, $times[2]),
				($_ids->order5, 'W9142',	'MANN',			'Фильтр масляный MANN ВАЗ-2108/09',									136.16, 1, 5, 0,	'АМр Ект..rossko', 3,	$_ids->pickup, 5, $times[1]),
				($_ids->order6, '7PK1835',	'GATES',		'Поликлиновой ремень',												943.27, 1, 0, 0,	'БЕРГ Москва', 5,		$_ids->courier, 3, $times[0]),
				($_ids->order6, '2071',		'Febi',			'Подшипник ступицы в комплекте',									354.56, 1, 0, 0,	'Наш склад', 0,			$_ids->courier, 1, $times[0])
			");
		}
	}
	
	/*--------------------------
	| SAMPLE PAGES FOR NEW WEBSHOP
	|--------------------------*/
	
	function setup_sample_pages()
	{
		$time = time();
		
		$insert_batch = array
		(
			array
			( 
				'title'				=> 'Оферта',
				'permalink'			=> 'agreement',
				'post_type'			=> 'page',
				'last_update'		=> $time,
				'allow_delete'		=> 0,
				'meta'				=> 1,
				'menu_order'		=> 10,
				'thumbnail'			=> null,
				'text'				=> $this->sample_pagedata('agreement'),
			),
			array
			( 
				'title'				=> 'Контакты',
				'permalink'			=> 'kontakty',
				'post_type'			=> 'page',
				'last_update'		=> $time,
				'allow_delete'		=> 0,
				'meta'				=> 1,
				'menu_order'		=> 20,
				'thumbnail'			=> null,
				'text'				=> null,
			),
			array
			( 
				'title'				=> '[Системная] Текст на главной странице',
				'permalink'			=> 'homepage',
				'post_type'			=> 'page',
				'last_update'		=> $time,
				'allow_delete'		=> 0,
				'meta'				=> 0,
				'menu_order'		=> 0,
				'thumbnail'			=> null,
				'text'				=> null,
			),
			array
			( 
				'title'				=> '[Системная] Способы оплаты',
				'permalink'			=> 'payment',
				'post_type'			=> 'page',
				'last_update'		=> $time,
				'allow_delete'		=> 0,
				'meta'				=> 0,
				'menu_order'		=> 0,
				'thumbnail'			=> null,
				'text'				=> $this->sample_pagedata('payment'),
			),
			array
			( 
				'title'				=> 'Слайд 1',
				'permalink'			=> 'sample-slide-1',
				'post_type'			=> 'slide',
				'last_update'		=> $time,
				'allow_delete'		=> 1,
				'meta'				=> null,
				'menu_order'		=> 10,
				'thumbnail'			=> 'sample-slide-1.jpg',
				'text'				=> null,
			),
			array
			( 
				'title'				=> 'Слайд 2',
				'permalink'			=> 'sample-slide-2',
				'post_type'			=> 'slide',
				'last_update'		=> $time,
				'allow_delete'		=> 1,
				'meta'				=> null,
				'menu_order'		=> 20,
				'thumbnail'			=> 'sample-slide-2.jpg',
				'text'				=> null,
			),
		);	
		
		$this->db->insert_batch('posts', $insert_batch);
	}
	
	
	/*--------------------------
	| SAMPLE PAGE DATA
	|--------------------------*/
	
	function sample_pagedata($datakey)
	{
		$sampledata['agreement'] = '<p>Данный документ является договором-офертой между посетителем и продавцом. Осуществляя заказ через вебсайт компании, вы выражаете свое согласие с данным документом.</p>
			<p>Данный портал разработам с учетом наших знаний и опыта по подбору и продаже запасных частей и расходных материалов для автомобилей иностранного производства (в основном запчастей сторонних производителей).</p>
			<p>Данный портал позволяет:</p>
			<ul>
			<li>Размещать заказы запчастей в компании через интернет.</li>
			<li>Видеть остатки по нашему складу с актуальным наличием</li>
			<li>Видеть остатки складов партнеров с наличием и датой последнего обновления</li>
			<li>Искать запчасти по различным параметрам, с подбором аналогов.</li>
			<li>Подбирать запчасти по модели автомобиля.</li>
			<li>Искать аналоги для любой запчасти.</li>
			<li>Видеть как базовые цены на товар, так и цены с учетом Вашей скидки.</li>
			<li>Просматривать историю размещенных заказов.</li>
			</ul>
			<p>Можно осуществлять поиск по артикулу или оригинальному номеру. Остальные варианты поиска позволяют подбирать запчасти под конкретную модель автомобиля.</p>
			<p>Подбор запчастей под автомобиль можно разбить на 4 шага:</p>
			<ol start="1">
			<li>Выбор марки авто.</li>
			<li>Выбор модели.</li>
			<li>Выбор комплектации.</li>
			<li>Выбор сборочной группы (раздела каталога) и переход к списку запчастей.</li>
			</ol>
			<p>Список марок автомобилей отсортирован по алфавиту. Щелчок по названию марки осуществляет переход к списку моделей данной марки. При выборе модели осуществляется переход к списку комплектаций данной модели.</p>
			<p>Для каждой модели кроме названия приведен период производства.</p>
			<p>Для комплектации указаны период производства, тип кузова, рабочий объем двигателя, мощность в киловаттах и лошадиных силах, и код двигателя.</p>
			<p>После выбора сборочной группы выбираете конкретную запчасть, если она есть у нас на складе вы видите наличие и цену, есди она есть на складах наших поставщиков&nbsp; то вы видите количество и цену , а также примерный срок поставки и дату обновления прайса и наличия по данному постащику.</p>
			<p>Данный портал создан и работает на базе кроссов производителей, баз данных поставщиков и данных получененных из иных источников. <strong>Подбор запчастей не привязан к оригинальным каталогам производителей автомобилей и не учитывает возможных несовпадений и замен которые могли иметь место быть в периоды производства той, или иной модели.</strong> Нужно знать и обращать внимание на технические данные по запчасти, если они присутствуют в описании. Поэтому, если вы не уверены в том что правильно идентифицировали запчасть, то <a href="/page/kontakty">обращайтесь к менеджерам по телефонам</a> или <a href="/page/kontakty">задайте ворпрос по электронной почте</a>.</p>
			<p><strong>Подбирая самостоятельно запчасти на нашем портале вы получаете адекватную цену</strong>, она будет ниже чем если вы будете покупать ее в нашем магазине по розничным ценам, но учитывайте вы сами несете ответственность за правильность подбора.</p>
			<p>База данных по запасным частям и кроссам может содержать неполную или неточную информацию и предоставляется Вам в том виде как она есть и вы согласны использовать ее под свою ответственность. Компания и ее уполномоченные представители не принимают никаких претензий, как в рамках действующего законодательства, так и на перечисленные ниже позиции: содержание, качество, точность, пригодность использования в определенных целях, применимость испоьзования результатов, полученных с помощью базы данных, а также непрерывность и отсутствие ошибок, содержащихся в базе данных или на сервере.</p>
			<p>Отказ от обязательств: Компания и ее уполномоченные представители заявляют об отказе от ответственности перед Вами: в отношении каких-либо претензий, требований или действий, связанных с прямыми или косвенными убытками, которые могут возникнуть в результате использования или владения информацией полученной с использованием этого ресурса.</p>
			<p><strong>Заказывая запчасть со складов наших партнеров </strong>(склады партнеров отмечены на сайте сроком поставки выше 0 дней) имейте в виду, что <strong>в большинстве случаев возврат невозможен</strong> или возможен с дисконтом, возможность отказа или возврата уточняйте.</p>
		';
	
		$sampledata['payment'] = '<h5>== Пример страницы с платежными реквизитами ==</h5>
			<h3>Оплата банковским переводом</h3>
			<p>Получатель: ИП Иванов Иван Иванович<br> ИНН 773300000000<br> ОГРН 304773300000000<br> р/с 40802810000000000000 (в Банке &laquo;Наименование Банка&raquo;)<br> к/с 30101810300000000000<br> БИК 040000000<br> Юр. Адрес: г.Москва, Гоголевский бульвар, д.888<br> Тел: (499) 000-00-00, (499) 000-00-00<br> Назначение платежа: <strong>Оплата заказа %order_number%</strong><br> <strong>Сумма: %order_total%</strong></p>
			<h3>Оплата наличными в офисе</h3>
			<p>Заказ можно оплатить наличными в офисе. Адрес и схему проезда можно найти на <a href="/page/kontakty">соответствующей странице</a>.<br> <strong><strong>Сумма: %order_total%</strong></strong></p>
			<h3>Оплата банковскими картами</h3>
			<p><a class="btn btn-primary btn-large disabled">Перейти к оплате</a></p>
		';

		$sampledata['homepage'] = '<h1>&ge; %in_stock% запчастей в наличии</h1><p>Свыше <strong>%in_stock%</strong> запчастей в наличии со сроком поставки от <strong>%min_delivery_days% дней</strong> (на %last_update%)</p>';
		
		if (isset($sampledata[$datakey]))
		{
			return $sampledata[$datakey];
		}
		
		return null;
	}
}

?>