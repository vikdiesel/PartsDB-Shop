<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Administrative area Controller
 *
 * @package		2find
 * @subpackage	Controllers
 * @category	Backend
 * @author		Viktor Kuzhelnyi @marketto.ru
 *
 * @todo Comment methods flow
 * @todo Migrate success messages layout to configs
 * @todo clear user carts on prices update
 */
class Admin extends CI_Controller
{
	/// Price-files are uploaded to this folder. They are deleted after being processed.
	var $drop_in_path = 'e/upload_drop_in';
	
	/// Header & Footer path.
	var $hf_path = 'admin';
	
	/// Bodyclass store. Body class can be altered within the constructor, so we keep it commonly available.
	var $bodyclass;
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Set internal character encoding to UTF-8
		mb_internal_encoding("UTF-8");
		
		// Helpers
		$this->load->helper(array('input','string','url','form','website','text','date','language'));
		
		// Libraries
		$this->load->library('session');
		
		// Configs
		$this->load->config('jb');
		$this->config->set_item('jb_admin_mode', TRUE);
		
		// Critical models
		$this->load->model('local_helper_model', 'local', TRUE);
		$this->load->model('options_model','options', TRUE); // NOT yet used everywhere.

        $this->options->unserialize_on_get = TRUE;
		
		// Language
		// Load after critical model
		$this->lang->load('pdbshop');
		
		// Other models
		$this->load->model('cart_model', 'cart', TRUE);
		$this->load->model('delivery_model', 'delivery', TRUE);
		$this->load->model('users_model', 'users', TRUE);
		$this->load->model('order_model', 'order', TRUE);
		$this->load->model('access_model', 'access', TRUE);
		$this->load->model('posts_model', 'posts', TRUE);
		$this->load->model('terms_model', 'terms', TRUE);
		$this->load->model('appflow_model', 'appflow', TRUE);
		$this->load->model('currency_model', 'currency', TRUE);
		
		// Authorize user
		if (uri_string() != 'admin/login' and uri_string() != 'admin/password-reset' and !$this->access->_is_admin())
		{
			redirect('admin/login');
		}
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Index
	 *
	 * It is a default method.
	 *
	 * @return	void
	 */	
	public function index()
	{
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass . ' home',
		));

		$this->load->view($this->hf_path . '/quickstart', $d);
		$this->load->view($this->hf_path . '/footer');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Users
	 *
	 * Lists site's registered users (customers) with option to change discount
	 *
	 * @param	bool|string $result
	 * @return	void
	 */	
	public function users($result=FALSE)
	{
		// This helper contains `php_shorthand_val_to_bytes` function
		$this->load->helper('number');
		
		$users = $this->users->get();
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
			'javascripts'	=> $this->load->view('admin/js_importer', FALSE, TRUE),
		));
		$this->load->view('admin/users_list', array
		(
			'users'					=> $users,
			'result'				=> $result,
			'import_form'		=> $this->load->view('admin/import', array
			(
				'id'				 					=> 'void',
				'type' 								=> 'users',
				'upload_max' 					=> php_shorthand_val_to_bytes(ini_get('upload_max_filesize')) / 1024 / 1024 - 6 /* Just a safety measure, we reduce the actual maximum by 6Mb */,
				'is_show_guide'				=> 0,
				'is_embedded'					=> TRUE,
				'text_import_howto'		=> $this->load->view("admin/import_howto_users", "", TRUE),
			), TRUE),
		));
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Users Update
	 *
	 * Updates user discount data and redirects back to users list.
	 *
	 * @return	void
	 */	
	public function users_update()
	{
		if (($user_discounts = p('user_discount')) !== FALSE)
		{			
			foreach ($user_discounts as $user_id=>$user_discount)
			{
				$user_discount = substr(preg_replace('#\D#', '', $user_discount), 0, 3);
				
				if ($user_discount != '')
				{
					$this->users->user_update_discount($user_id, $user_discount);
				}
			}
		}
		
		redirect('admin/users/updated');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * User Delete
	 *
	 * Deletes user (asks to confirm) and redirects back to the users list.
	 *
	 * @param	int $user_id
	 * @param	bool|string $confirmed
	 * @return	void
	 */	
	public function user_delete($user_id, $confirmed = FALSE)
	{
		if (!$confirmed)
		{
			$this->load->view($this->hf_path . '/header', array
			(
				'bodyclass'		=> $this->bodyclass,
			));
			$this->load->view('admin/user_delete_confirm', array('user_id'=>$user_id));
			$this->load->view($this->hf_path . '/footer');
		}
		else
		{
			// Delete user's orders
			$this->local->delete('orders', $user_id, 'user_id', FALSE);
			
			// Delete user
			$this->local->delete('users', $user_id);
			
			redirect('admin/users');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Users Export
	 *
	 * Export users database
	 *
	 * @param	bool|string $result
	 * @return	void
	 */
	 
	public function users_export()
	{
		$this->load->helper('download');
		
		$users = $this->users->get();
		
		$csv = '"E-mail";"Имя";"Телефон",;"Адрес";"ИНН/КПП";"ОГРН/ОГРНИП";"Р/с";"БИК";"Пароль";"Скидка"' . "\r\n";
		
		foreach ($users as $user)
		{
			$name			= (empty($user->userdata->name))?"---":$user->userdata->name;
			$phone			= (empty($user->userdata->phone))?"---":$user->userdata->phone;
			$address		= (empty($user->userdata->address))?"---":$user->userdata->address;
			$corp_inn		= (empty($user->userdata->corp_inn))?"---":$user->userdata->corp_inn;
			$corp_ogrn		= (empty($user->userdata->corp_ogrn))?"---":$user->userdata->corp_ogrn;
			$corp_rs		= (empty($user->userdata->corp_rs))?"---":$user->userdata->corp_rs;
			$corp_bik		= (empty($user->userdata->corp_bik))?"---":$user->userdata->corp_bik;
			
			$csv .= "\"{$user->email}\";\"$name\";\"тел.: $phone\";\"$address\";\"$corp_inn\";\"$corp_ogrn\";\"$corp_rs\";\"$corp_bik\";\"{$user->password}\";\"{$user->discount}\"\r\n";
		}
		
		force_download('2find.csv', mb_convert_encoding($csv, "Windows-1251", "UTF-8"));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options
	 *
	 * List of available options. The entry point of the options section.
	 *
	 * @return	void
	 */	
	public function options()
	{		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));

		$this->load->view('admin/options', $d);
		
		$this->load->view($this->hf_path . '/footer');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Options Common
	 *
	 * The list of general options that can be configured. Update is performed on form submit.
	 *
	 * @return	void
	 */	
	public function options_common()
	{
		// Models
		$this->load->model('currency_model', 'currency');
		
		// Form validation
		$this->load->library('form_validation');

		// Form validation rules
		$this->form_validation->set_rules('title', 'Заголовок', 'required|max_length[100]|xss_clean|strip_tags');
		$this->form_validation->set_rules('subtitle', 'Подзаголовок', 'required|max_length[100]|xss_clean|strip_tags');
		$this->form_validation->set_rules('adminemail', 'E-mail администратора', 'required|valid_email|max_length[100]');
		$this->form_validation->set_rules('timezone', 'Часовой пояс', 'required|alpha_numeric|min_length[3]|max_length[4]');
		$this->form_validation->set_rules('currency', 'Валюта', 'required|alpha|exact_length[3]');

		// This fields are ignored in offline mode
		$this->form_validation->set_rules('footnote', 'Копирайт внизу', 'xss_clean|strip_tags');
		$this->form_validation->set_rules('hideadmlink', 'Ссылка на административный раздел', 'numeric|exact_length[1]');
		
		$d['form'] = (array) _jb_sitedata();
		
		if (($footnote = $this->options->get('footnote')) !== FALSE)
		{
			$d['form']['footnote'] = $footnote;
		}
			
		if ($this->options->get('hideadmlink') !== FALSE)
		{
			$d['form']['hideadmlink'] = TRUE;
		}
		else
		{
			$d['form']['hideadmlink'] = FALSE;
		}
			
		// I think we should avoid repetition of the code below (except post-check)
		
		if (($timezone = $this->input->post('timezone')) !== FALSE)
		{
			$d['form']['timezone'] = $timezone;
		}
		elseif (($timezone = $this->options->get('timezone')) !== FALSE)
		{
			$d['form']['timezone'] = $timezone;
		}
		else
		{
			$d['form']['timezone'] = $this->config->item('timezone'); // Setting default timezone
		}
		
		// Currency list
		$d['currency_list'] = $this->currency->currencies;
		
		if (p('currency'))
		{
			$d['form']['currency'] = $this->currency->get(p('currency'))->code;
		}
		else
		{
			$d['form']['currency'] = $this->currency->get()->code;
		}
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));

		if ($this->form_validation->run() == FALSE)
		{
			$d['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
			$this->load->view('admin/options_common', $d);
		}
		else
		{
			if (p('footnote'))
			{
				$this->options->set('footnote', p('footnote'));
			}
			else
			{
				$this->options->delete('footnote');
			}
				
			if (p('hideadmlink'))
			{
				$this->options->set('hideadmlink', '1');
			}
			else
			{
				$this->options->delete('hideadmlink');
			}
			
			$this->options->set('timezone', p('timezone'));
			$this->options->set('currency', p('currency'));

			$this->local->update_sitedata(p(array('title','subtitle','adminemail'), TRUE));
			
			$d['success'] = '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Настройки обновлены.</div>';
			
			$this->load->view('admin/options_common', $d);
		}
		
		$this->load->view($this->hf_path . '/footer');
	}
	
	public function options_payment()
	{
		$p = $this->local->get_payment_details_page();
		
		if ($p)
		{
			$this->post_add('page', $p->id);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Change Pass
	 *
	 * Show a change password form and performs password change if the form is submitted.
	 * Updates current authorisation info, so user is not required to log in again.
	 *
	 * @return	void
	 */	
	public function options_change_pass()
	{
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('adminpass', 'Текущий пароль администратора', 'required|callback__clb_checkadminpass');
		$this->form_validation->set_rules('adminpass_new', 'Новый пароль администратора', 'required|max_length[32]|min_length[6]');
		$this->form_validation->set_rules('adminpass_new_confirm', 'Новый пароль администратора (еще раз)', 'required|matches[adminpass_new]');

		if ($this->form_validation->run() == FALSE)
		{
			$d['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
			$this->load->view('admin/options_change_pass', $d);
		}
		else
		{
			$this->local->update_sitedata(array('adminpass'=>md5(p('adminpass_new'))));
			$this->access->admin($this->access->adminlogin, p('adminpass_new'));
			$this->session->set_userdata('adm_pass', p('adminpass_new'));
			
			$d['success'] = '<div class="alert alert-success">Пароль администратора изменен.</div>';

			$this->load->view('admin/options_change_pass', $d);
		}
		
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Header Footer
	 *
	 * Allows to change insert HTML code in header, footer and sidebar of the website.
	 * Shows form and updates options if form was submitted.
	 * 
	 * @todo	Migrate site-status check to the __construct.
	 *
	 * @param	bool|string $action
	 * @return	void
	 */	
	public function options_header_footer($action=FALSE)
	{
		if ($action == 'update')
		{
			if (p('js_header'))
			{
				$this->options->set('js_header', p('js_header'));
			}
			else
			{
				$this->options->delete('js_header');
			}

			if (p('js_footer'))
			{
				$this->options->set('js_footer', p('js_footer'));
			}
			else
			{
				$this->options->delete('js_footer');
			}

			if (p('js_sidebar'))
			{
				$this->options->set('js_sidebar', p('js_sidebar'));
			}
			else
			{
				$this->options->delete('js_sidebar');
			}
		}
		
		$form = array
		(
			'js_header'		=> $this->options->get('js_header'),
			'js_footer'		=> $this->options->get('js_footer'),
			'js_sidebar'	=> $this->options->get('js_sidebar')
		);
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		$this->load->view('admin/options_header_footer', array
		(
			'form'			=> $form, 
			'action'		=> $action,
		));
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Template Editor
	 *
	 * Allows to change insert HTML code in header, footer and sidebar of the website.
	 * Shows form and updates options if form was submitted.
	 * 
	 * @todo	Migrate site-status check to the __construct.
	 *
	 * @param	bool|string $action
	 * @return	void
	 */	
	public function options_template_editor($action=FALSE)
	{
		if ($action == 'update')
		{
			if (p('template'))
			{
				$this->options->set('template', p('template'));
			}
			else
			{
				$this->options->delete('template');
			}
		}
		elseif ($action == "reset")
		{
			$this->options->delete('template');
		}
		
		if (($template = $this->options->get('template')) === FALSE)
		{
			$template = $this->load->view('frontend.common/template', '', TRUE);
		}
		
		$form = array
		(
			'template' => $template,
		);
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
			'javascripts'	=> $this->load->view('admin/js_codemirror', '', TRUE),

		));
		$this->load->view('admin/options_template_editor', array
		(
			'form'			=> $form, 
			'action'		=> $action,
		));
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Head Image
	 *
	 * Allows to upload personalised head image.
	 *
	 * @todo	migrate settings to configs
	 *
	 * @param	bool|string $action
	 * @return	void
	 */	
	public function options_headimage($action=FALSE)
	{		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'				=> $this->bodyclass,
		));
		
		$config['upload_path']		= 'e/files/_headers';
		$config['allowed_types']	= 'jpg|png|gif';
		$config['max_size']			= '512';
		$config['overwrite']		= TRUE;
		$config['file_name']		= 'default';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			if ($action == 'do_upload')
			{
				$result['errors'] = $this->upload->display_errors('<div class="alert alert-error"><i class="icon-remove-sign"></i> ', '</div>');
			}
		}
		else
		{
			$result = $this->upload->data();
			
			$this->options->set('head_image_filename', $result['file_name']);
			
			$result['success'] = '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Файл загружен.</strong> Шапка обновлена.</div>';
		}
		
		
		$this->load->view('admin/options_upload_image', $result);
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Head Image Delete
	 *
	 * Deletes perviously saved Head Image.
	 *
	 * @return	void
	 */	
	public function options_headimage_delete()
	{
		$this->options->delete('head_image_filename');
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		
		$this->load->view('admin/options_image_deleted');
		
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Background Image
	 *
	 * Allows to upload personalised background image.
	 *
	 * @todo	migrate settings to configs
	 *
	 * @param	bool|string $action
	 * @return	void
	 */	
	public function options_bgimage($action=FALSE)
	{		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'				=> $this->bodyclass,
		));
		
		$config['upload_path']		= 'e/files/_backgrounds';
		$config['allowed_types']	= 'jpg|png|gif';
		$config['max_size']			= '512';
		$config['overwrite']		= TRUE;
		$config['file_name']		= 'default';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			if ($action == 'do_upload')
			{
				$result['errors'] = $this->upload->display_errors('<div class="alert alert-error"><i class="icon-remove-sign"></i> ', '</div>');
			}
		}
		else
		{
			$result = $this->upload->data();
			
			$this->options->set('bgimage_filename', $result['file_name']);
			
			$result['success'] = '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Файл загружен.</strong> Фон обновлен.</div>';
		}
		
		
		$this->load->view('admin/options_bgimage', $result);
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Background Image Delete
	 *
	 * Deletes perviously saved Background Image.
	 *
	 * @return	void
	 */	
	public function options_bgimage_delete()
	{
		$this->options->delete('bgimage_filename');
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		
		$this->load->view('admin/options_image_deleted');
		
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Background image params
	 *
	 * Show a change password form and perform password change if the form is submitted.
	 * Updates current authorisation info, so user is not required to log in again.
	 *
	 * @return	void
	 */	
	public function options_bgimage_params()
	{
		$this->load->library('form_validation');
		
		// Css style
		if (($bgimage_css = $this->options->get('bgimage_css')) !== FALSE)
		{
			$d['form']['bgimage_css'] = $bgimage_css;
		}
		
		// Wrapper transparency
		if ($this->options->get('bgwrapper_transp'))
		{
			$d['form']['bgwrapper_transp'] = '1';
		}
		else
		{
			$d['form']['bgwrapper_transp'] = '0';
		}
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		
		$this->form_validation->set_rules('bgimage_css', 'CSS-стили фона', 'xss_clean|strip_tags');
		$this->form_validation->set_rules('bgwrapper_transp', 'Фон подложки', 'required|greater_than[-1]|less_than[2]');

		if ($this->form_validation->run() == FALSE)
		{
			$d['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
			$this->load->view('admin/options_bgimage_params', $d);
		}
		else
		{			
			if (($p_bgimage_css = p('bgimage_css')) !== FALSE and strlen($p_bgimage_css) > 0)
			{
				$this->options->set('bgimage_css', $p_bgimage_css);
			}
			else
			{
				$this->options->delete('bgimage_css');
			}
			
			if (p('bgwrapper_transp'))
			{
				$this->options->set('bgwrapper_transp', '1');
			}
			else
			{
				$this->options->delete('bgwrapper_transp');
			}
			
			$d['success'] = '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Параметры обновлены.</div>';
			
			$this->load->view('admin/options_bgimage_params', $d);
		}
		
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Delivery
	 *
	 * Shows the list of delivery methods.
	 *
	 * @param	bool|string $action_done
	 * @return	void
	 */	
	public function options_delivery($action_done=FALSE)
	{		
		// Load currency model
		$this->load->model('currency_model', 'currency');
		
		// Get site's default currency
		$currency = $this->currency->get();
		
		// Get the delivery methods list
		$d_mthds = $this->delivery->methods();
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'					=> $this->bodyclass,
			'include_tableeditor_js'	=> TRUE,
		));
		
		$this->load->view('admin/options_delivery', array
		(
			'd_mthds'				=> $d_mthds,
			
			'currency_symbol'		=> $currency->symbol,
			'action_done'			=> $action_done,
		));
		
		$this->load->view($this->hf_path . '/footer');
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Delivery Add
	 *
	 * Shows Add delivery method form and saves data if form was submitted.
	 *
	 * @param	void $id
	 * @return	void
	 */	
	public function options_delivery_add($id=FALSE /* this is not used */)
	{
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		

		$this->load->library('form_validation');

		$this->form_validation->set_rules('title', 'Название', 'required|max_length[255]|xss_clean|strip_tags');
		$this->form_validation->set_rules('price', 'Стоимость', 'required|is_natural|max_length[9]');
		$this->form_validation->set_rules('order', 'Порядковый номер', 'required|integer|max_length[4]');

		if ($this->form_validation->run() == FALSE)
		{
			// Load currency model
			$this->load->model('currency_model', 'currency');
			
			// Get site's default currency
			$currency = $this->currency->get();
			
			// Publish currency symbol
			$d['currency_symbol'] = $currency->symbol;
			
			// Publish errors (if some)
			$d['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
			
			$this->load->view('admin/options_delivery_add', $d);
		}
		else
		{
			$this->delivery->add(p(array('title','price','order')));
			
			if (!$id)
			{
				redirect('admin/options_delivery/added');
			}
// This behaviour's not used
//			else
//			{
//				$d['success'] = '<div class="alert alert-success">Параметры способа доставки изменены.</div>';
//				$this->load->view('admin/options_delivery_add', $d);
//			}
		}
		
		$this->load->view($this->hf_path . '/footer');
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Delivery Delete
	 *
	 * Deletes delivery method specified by ID.
	 *
	 * @param	int $id
	 * @return	void
	 */	
	public function options_delivery_delete($id)
	{
		$this->local->delete('delivery_methods', $id);
		redirect('admin/options_delivery/deleted');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Options Mfgs
	 *
	 * Manufacturers list management. The list of manufacturers available in partsdb is vast.
	 * Here user can specify which of them he would like to be displayed on the main page and how they gonna be sorted.
	 * 
	 * This function lists all manufacturers available in partsdb and shows checkboxes and ordering parameters.
	 *
	 * @param	bool|string $success
	 * @return	void
	 */	
	public function options_mfgs($success=FALSE)
	{
		$this->load->model('pdb_api_model', 'partsdb_api');
		$brands = $this->partsdb_api->brands();

		// Check listed
		$brands_order = $this->options->get('brands_order');

		if (empty($brands_order))
		{
			$is_check_all = true;

			foreach ($brands->data as $pdb_mfg)
			{
				$form['mfg[' . $pdb_mfg->id . ']']			= '1';
				$form['mfg_order[' . $pdb_mfg->id . ']']	= '0';
			}
		}
		else
		{
			$is_check_all = false;

            foreach ($brands->data as $pdb_mfg)
			{
				$form['mfg[' . $pdb_mfg->id . ']']			= (in_array($pdb_mfg->id, $brands_order))?"1":"";
			}
		}
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		
		$this->load->view('admin/mfgs_list', array
		(
			'brands'		=> $brands->data,
			'form'			=> $form,
			'success'		=> $success,
		));
		$this->load->view($this->hf_path . '/footer');
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Mfg Update
	 *
	 * Performs an update routine from the form submitted at options_mfgs.
	 * Redirects back to options_mfgs after the action is complete.
	 *
	 * @return	void
	 */	
	public function mfg_update()
	{
		$this->load->model('pdb_api_model', 'partsdb_api');

		$brands = $this->partsdb_api->brands();

		if (($mfgs = p('mfg')) !== FALSE)
		{
			$store = array();
			
			foreach ($brands->data as $brand)
			{
				if (isset($mfgs[$brand->id]))
				{
					$store[] = $brand->id;
				}
			}

			$this->options->set('brands_order', $store);
		}
		
		redirect('admin/options_mfgs/success');
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Terms
	 *
	 * Lists terms of a given type
	 *
	 * @param	string $type
	 * @param	bool|int $id
	 * @return	void
	 */		
	public function terms($type, $completed_action=FALSE)
	{
		if (($typedata = $this->terms->type($type)) !== FALSE)
		{
			if ($typedata->posttypeid and ($post_typedata = $this->posts->type($typedata->posttypeid)))
			{
				$d['post_typedata'] = $post_typedata;
			}
			
			$d['terms']				= $this->terms->get($type);
			$d['num_terms']			= $this->terms->num_terms;
			$d['typedata']			= $typedata;
			$d['completed_action']	= $completed_action;
			
			$this->load->view($this->hf_path . '/header', array
			(
				'bodyclass'		=> $this->bodyclass,
			));
			
			$this->load->view('admin/terms_list', $d);
			$this->load->view($this->hf_path . '/footer');
		}
		else
		{
			show_jb_error('Invalid type');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Term Edit
	 *
	 * A concise name for term edit. Simply redirects calls term_add with an $id.
	 *
	 * @param	string $type
	 * @param	bool|int $id
	 * @return	void
	 */	
	public function term_edit($type, $id=FALSE)
	{
		$this->term_add($type, $id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Term Add
	 *
	 * Adds new term or edits if an id is specified.
	 *
	 * @param	string $type
	 * @param	bool|int $id
	 * @return	void
	 */	
	public function term_add($type, $id=FALSE)
	{		
		if (($typedata = $this->terms->type($type)) !== FALSE)
		{
			if ($id and ($term = $this->terms->get($type, $id)) !== FALSE)
			{
				$formdata['form']		= (array) $term;
				$formdata['id']			= $id;
			}
			
			$formdata['typedata']		= $typedata;
			$formdata['parent_terms']	= $this->terms->get($type, $id, 'id !=', 'objects_set');
			
			$this->load->library('form_validation');

			$this->form_validation->set_rules('title', 'Название', 'required|max_length[255]|xss_clean|strip_tags');
			$this->form_validation->set_rules('parent_id', 'Родительская категория', 'required|integer|callback__clb_is_valid_term');
			$this->form_validation->set_rules('order', 'Порядковый номер', 'required|numeric|less_than[999999]|greater_than[-999999]');

			if ($this->form_validation->run() == FALSE)
			{
				$formdata['errors']			= validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
				
				$this->load->view($this->hf_path . '/header', array
				(
					'bodyclass'			=> $this->bodyclass,
				));
				$this->load->view('admin/term_edit', $formdata);
			}
			else
			{
				$data = p(array('title','order','parent_id'), TRUE);
				
				if ($id)
				{
					$this->terms->update($type, $id, $data);
					
					$formdata['success']	= '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Параметры обновлены.</div>';
					
					$this->load->view($this->hf_path . '/header', array
					(
						'bodyclass'			=> $this->bodyclass,
					));
					
					$this->load->view('admin/term_edit', $formdata);
				}
				else
				{
					$this->terms->add($type, $data);
					redirect("admin/terms/$type/added");
				}
				
			}
			
			$this->load->view($this->hf_path . '/footer');
		}
		else
		{
			show_jb_error('Invalid type');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Term delete
	 *
	 * Deletes term of a type and id given.
	 *
	 * @param	string $type
	 * @param	int $id
	 * @return	void
	 */	
	public function term_delete($type, $id)
	{
		if ($this->terms->type($type) and $this->terms->delete($type, $id))
		{
			redirect("admin/terms/$type/deleted");
		}
		else
		{
			redirect("admin/terms/$type");
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Post delete
	 *
	 * Deletes a post of type and id given.
	 *
	 * @param	string $type
	 * @param	int $id
	 * @return	void
	 */	
	public function post_delete($type, $id)
	{
		if ($this->posts->type($type) and $this->posts->delete($type, $id))
		{
			redirect("admin/posts/$type/deleted");
		}
		else
		{
			redirect("admin/posts/$type");
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Post Edit
	 *
	 * A concise name for post edit. Simply redirects calls post_add with an $id.
	 *
	 * @param	string $type
	 * @param	bool|int $id
	 * @return	void
	 */	
	public function post_edit($type, $id=FALSE)
	{
		$this->post_add($type, $id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Post Add
	 *
	 * Adds new post of a given type, or edits if an id is specified.
	 *
	 * @param	string $type
	 * @param	bool|int $id
	 * @return	void
	 */	
	public function post_add($type, $id=FALSE)
	{		
		if (($typedata = $this->posts->type($type)) !== FALSE)
		{
			// Get post data, if id is supplied.
			if ($id and ($post = $this->posts->get($type, false, $id)) !== FALSE)
			{
				$formdata['form']		= (array) $post;
				$formdata['id']			= $id;
				
				// This is post editing mode
				define('is_post_edit_mode', TRUE);
			}
			
			// Load library
			$this->load->library('form_validation');
			
			// Vars to get from POST upon submit. We put them here, because the list differs for different post types.
			$pvars = array('title', 'menu_order');
			
			// Title is always present, but with optional name trigger
			$this->form_validation->set_rules('title', (isset($typedata->title_field_name))?$typedata->title_field_name:"Название", 'required|max_length[255]|xss_clean|strip_tags');
			
			// Text can be optional
			if (!isset($typedata->no_editor))
			{
				$this->form_validation->set_rules('text', 'Текст', 'required|max_length[2097152]');
				$pvars[] = 'text';
			}
			
			// Menu_order is always present
			$this->form_validation->set_rules('menu_order', 'Порядковый номер', 'required|numeric|less_than99999]|greater_than[-99999]');
			
			// Are terms in use?
			if ($typedata->termtypeid and ($term_typedata = $this->terms->type($typedata->termtypeid)))
			{
				// List terms for use in form
				$formdata['terms']		= $this->terms->get($typedata->termtypeid);
				
				// Make term typedata available in form
				$formdata['term_typedata'] = $term_typedata;
				
				// Add validation rule
				$this->form_validation->set_rules('term_id', 'Категория', 'required|integer|callback__clb_is_valid_term');
				
				// Add post var
				$pvars[] = 'term_id';
			}
			
			// Does post have meta field?
			if ($typedata->meta)
			{
				if (defined('is_post_edit_mode') and isset($typedata->meta['disable_for']) and in_array($post->permalink, $typedata->meta['disable_for']))
				{
					$typedata->meta = false;
				}
				else
				{
					// Add validation rule
					$this->form_validation->set_rules('meta', $typedata->meta[0], $typedata->meta['validation']);
					
					// Add post var
					$pvars[] = 'meta';
				}
			}
			
			// And one more metafield?
			if ($typedata->meta2)
			{
				if (defined('is_post_edit_mode') and isset($typedata->meta2['disable_for']) and in_array($post->permalink, $typedata->meta2['disable_for']))
				{
					$typedata->meta2 = false;
				}
				else
				{
					// Add validation rule
					$this->form_validation->set_rules('meta2', $typedata->meta2[0], $typedata->meta2['validation']);
					
					// Add post var
					$pvars[] = 'meta2';
				}
			}
			
			// Thumbnails
			if ($typedata->thumbnail)
			{				
				$config['upload_path']		= 'e/files/';
				$config['allowed_types']	= 'gif|jpg|png';
				$config['max_size']			= '1024';
				$config['max_width']		= $typedata->thumbnail['width'];
				$config['max_height']		= $typedata->thumbnail['height'];

				$this->load->library('upload', $config);
			}
			
			// Piece of advice?
			if ($typedata->editor_advice and defined('is_post_edit_mode') and in_array($post->permalink, $typedata->editor_advice))
			{
				$formdata['editor_advice'] = $this->load->view('admin/post_advice_' . $post->permalink, '', TRUE);
			}
			
			// Make typedata accessible in view file
			$formdata['typedata']		= $typedata;

			// If form isn't submitted or has errors
			if ($this->form_validation->run() == FALSE or ($typedata->thumbnail and ($upload_status = $this->upload->do_upload('thumbnail')) == FALSE and $typedata->thumbnail['is_required'] and !defined('is_post_edit_mode')))
			{
				$formdata['errors']			= validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
				
				if ($typedata->thumbnail)
				{
					$formdata['errors']			.= $this->upload->display_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
				}
				
				$this->load->view($this->hf_path . '/header', array
				(
					'bodyclass'			=> $this->bodyclass,
					'javascripts'		=> $this->load->view('admin/js_tinymce', '', TRUE),
				));
				
				$this->load->view('admin/post_edit', $formdata);
			}
			else
			{
				// Get data from POST. Pvars is altered depending on particular post params.
				$data = p($pvars, TRUE, array('meta'=>NULL,'meta2'=>NULL));
				
				if (defined('is_post_edit_mode'))
				{
					
					if ($typedata->thumbnail and defined('is_post_edit_mode') and isset($upload_status) and $upload_status == FALSE)
					{
						// Show success message
						$formdata['success']	= $this->config->item('form_vld_success_before') . '<strong>Параметры сохранены, файл не обновлен.</strong> Не обращайте внимания, если вы не хотели менять файл.' . $this->config->item('form_vld_success_after');
						
						// Show upload errors
						$formdata['errors']		= $this->upload->display_errors($this->config->item('form_vld_info_before'), $this->config->item('form_vld_info_after'));
					}
					else
					{
						$formdata['success']	= '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Параметры обновлены.</div>';
						
						if ($typedata->thumbnail and isset($upload_status) and $upload_status == TRUE)
						{
							// Prep filename for db insert
							$upload_data = $this->upload->data();
							$data['thumbnail'] = $upload_data['file_name'];
						}
					}
					
					// Update data in db
					$this->posts->update($type, $id, $data);
					
					$this->load->view($this->hf_path . '/header', array
					(
						'bodyclass'			=> $this->bodyclass,
						'javascripts'		=> $this->load->view('admin/js_tinymce', '', TRUE),
					));
					
					$this->load->view('admin/post_edit', $formdata);
				}
				else
				{
					if ($typedata->thumbnail and isset($upload_status) and $upload_status == TRUE)
					{
						// Prep filename for db insert
						$upload_data = $this->upload->data();
						$data['thumbnail'] = $upload_data['file_name'];
					}
					
					$this->posts->add($type, $data);
					redirect("admin/posts/$type/added");
				}
			}
			
			$this->load->view($this->hf_path . '/footer');
		}
		else
		{
			show_jb_error('Invalid type');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Posts
	 *
	 * Lists posts of a given type
	 *
	 * @param	string $type
	 * @param	string|bool $completed_action
	 * @return	void
	 */	
	public function posts($type, $completed_action=FALSE)
	{
		if (($typedata = $this->posts->type($type)) !== FALSE)
		{
			if ($typedata->termtypeid and ($term_typedata = $this->terms->type($typedata->termtypeid)))
			{
				$d['term_typedata'] = $term_typedata;
				
			}
			
			if ($typedata->admin_posts_list_advice)
			{
				$d['posts_list_advice'] = $this->load->view('admin/posts_list_advice_' . $type, '', TRUE);
			}
			
			$d['posts']				= $this->posts->get($type, $typedata->termtypeid);
			$d['num_posts']			= $this->posts->num_posts;
			$d['typedata']			= $typedata;
			$d['completed_action']	= $completed_action;
			
			$this->load->view($this->hf_path . '/header', array
			(
				'bodyclass'		=> $this->bodyclass,
			));
			
			$this->load->view('admin/posts_list', $d);
			$this->load->view($this->hf_path . '/footer');
		}
		else
		{
			show_jb_error('Invalid type');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Import
	 *
	 * Import Stock/Prices/Crosses file for the vendor specified by vendor ID.
	 *
	 * @param	int $vendor_id
	 * @return	void
	 */	
	public function import($type, $id = false)
	{
		// This helper contains `php_shorthand_val_to_bytes` function
		$this->load->helper('number');
		
		$is_show_guide = 1;
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass . ' hide-message-no-prices',
			'javascripts'	=> $this->load->view('admin/js_importer', FALSE, TRUE),
		));
		
		if ($type == 'vendor')
		{
			if (($vendor = $this->local->get_vendor($id, 'id, price_correction, structure_id, vendor_type, rows_cache')) === FALSE)
			{
				show_error('No such vendor');
			}
			
			if ($vendor->rows_cache > 0)
			{
				$is_show_guide = 0;
			}
			
			if ($vendor->vendor_type == 'crosses')
			{
				$subfiles_group = 'crosses';
				$backlink = site_url('admin/vendor_crosses_list');
			}
			else
			{
				$subfiles_group = 'prices';
				$backlink = site_url('admin/vendors_list');
			}
			
			$this->load->view("admin/import_subhead_$subfiles_group");
			$text_import_howto = $this->load->view("admin/import_howto_$subfiles_group", "", TRUE);
		}
		
		$this->load->view('admin/import', array
		(
			'id'				 					=> $id,
			'type' 								=> $type,
			'upload_max' 					=> php_shorthand_val_to_bytes(ini_get('upload_max_filesize')) / 1024 / 1024 - 6 /* Just a safety measure, we reduce the actual maximum by 6Mb */,
			'is_show_guide'				=> $is_show_guide,
			'text_import_howto'		=> $text_import_howto,
		));
		
		$this->load->view($this->hf_path . '/footer');
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Prices Lookup
	 *
	 * Lists data from prices/stock for the vendor and vendor group specified.
	 * Optionally filtered by brand. Page number can be controlled as well.
	 *
	 * @todo	Migrate per_page param to configs
	 *
	 * @param	int $vendor_id
	 * @param	string $import_group_id
	 * @param	string $brand_coded
	 * @param	int $page
	 * @return	void
	 */	
	public function prices_lookup ($vendor_id, $import_group_id, $brand_coded=0, $page=1)
	{		
		if ($brand_coded)
		{
			$brand = base64_decode($brand_coded);
		}
		
		$page = $page-1;
		
		$config['uri_segment']			= 6;
		$config['base_url']				= site_url('admin/prices_lookup/' . $vendor_id . '/' . $import_group_id . '/' . $brand_coded) . '/';
		$config['total_rows']			= $this->local->vendor_data_count($vendor_id, $import_group_id, 'prices', $brand);
		$config['per_page']				= 200;

		$this->load->library('pagination');	
		$this->pagination->initialize($config); 
		
		$prices = $this->local->vendor_lookup_rslt($vendor_id, $import_group_id, $page, $config['per_page'], 'prices', $brand);
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'					=> $this->bodyclass . ' hide-message-no-prices',
		));
		
		$this->load->view('admin/vendor_prices_lookup', array
		(
			'prices'					=> $prices, 
			'vendor'					=> $this->local->get_vendor($vendor_id),
			'brands'					=> $this->local->vendor_prices_brands_rslt($vendor_id, $import_group_id),
			'thisbrand'					=> $brand,
			'total_rows'				=> $config['total_rows'],
			'import_group_id'			=> $import_group_id,
			'page'						=> $page,
			'pagination'				=> $this->pagination->create_links(),
		));

		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Crosses Lookup
	 *
	 * Lists data from crosses table for the vendor & import group specified.
	 * Page number can optionally be controlled.
	 *
	 * @todo	Migrate per_page param to configs
	 *
	 * @param	int $vendor_id
	 * @param	string $import_group_id
	 * @param	int $page
	 * @return	void
	 */	
	public function crosses_lookup ($vendor_id, $import_group_id, $page=1)
	{		
		$page = $page-1;
		
		$config['uri_segment']			= 5;
		$config['base_url']				= site_url('admin/crosses_lookup/' . $vendor_id . '/' . $import_group_id) . '/';
		$config['total_rows']			= $this->local->vendor_data_count($vendor_id, $import_group_id, 'crosses');
		$config['per_page']				= 200;

		$this->load->library('pagination');	
		$this->pagination->initialize($config); 
		
		$crosses = $this->local->vendor_lookup_rslt($vendor_id, $import_group_id, $page, $config['per_page'], 'crosses');
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'					=> $this->bodyclass,
		));
		
		$this->load->view('admin/vendor_crosses_lookup', array
		(
			'crosses'					=> $crosses, 
			'vendor'					=> $this->local->get_vendor($vendor_id),
			'total_rows'				=> $total_rows,
			'import_group_id'			=> $import_group_id,
			'page'						=> $page,
			'pagination'				=> $this->pagination->create_links(),
		));
		
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor Edit
	 *
	 * A proxy that points query to vendor_add with an ID specified.
	 *
	 * @param	int $id
	 * @return	void
	 */	
	public function vendor_edit($id)
	{
		$this->vendor_add($id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor Add
	 *
	 * Adds new vendor or edits if an id is specified.
	 *
	 * @param	bool|int $id
	 * @return	void
	 */	
	public function vendor_add($id=FALSE)
	{
		// Load apipuller
		$this->load->model('apipuller_model', 'apipuller', TRUE);
		
		// List APIs
		$formdata['apis'] = $this->apipuller->apis;
		
		// Is ID provided?
		if ($id)
		{
			// Get vendor data for this ID
			$vendor = $this->local->get_vendor($id, 'id, vendor_name, price_correction, structure_id, struct_art_number, struct_sup_brand, struct_description, struct_qty, struct_price, vendor_type, delivery_days, allow_delete, api_id, api_key1, api_key2, ordername, orderemail');
			
			$formdata['form']		= (array) $vendor;
			$formdata['id']			= $id;
			// $nn = $this->local->vendor_price_structure($vendor);
			// var_dump($nn);
		}
		
		$this->load->library('form_validation');

		$this->form_validation->set_rules('vendor_name',					'Название', 'required|max_length[255]|xss_clean|strip_tags');
		$this->form_validation->set_rules('delivery_days',				'Срок поставки (дней)', 'required|is_natural|max_length[3]');
		$this->form_validation->set_rules('price_correction',			'Коррекция цены', 'required|decimal');
		$this->form_validation->set_rules('orderemail',						'E-mail для заказа', 'valid_email|max_length[128]|xss_clean|strip_tags');
		$this->form_validation->set_rules('ordername',						'Ваше наименование для заказа', 'max_length[128]|xss_clean|strip_tags');
		$this->form_validation->set_rules('struct_art_number',		'Порядковый номер (Артикул)', 'required|is_natural_no_zero|larger_than[0]|less_than[100]');
		$this->form_validation->set_rules('struct_sup_brand',			'Порядковый номер (Бренд)', 'required|is_natural_no_zero|larger_than[0]|less_than[100]');
		$this->form_validation->set_rules('struct_description',		'Порядковый номер (Наименование)', 'required|is_natural_no_zero|larger_than[0]|less_than[100]');
		$this->form_validation->set_rules('struct_qty',						'Порядковый номер (Кол-во)', 'required|is_natural_no_zero|larger_than[0]|less_than[100]');
		$this->form_validation->set_rules('struct_price',					'Порядковый номер (Цена)', 'required|is_natural_no_zero|larger_than[0]|less_than[100]');
		$this->form_validation->set_rules('api_id',								'Сервер поставщика', 'callback__clb_valid_api_id');
		$this->form_validation->set_rules('api_key1',							'Ключ 1', '');
		$this->form_validation->set_rules('api_key2',							'Ключ 2', '');

		// $this->form_validation->set_rules('structure_id', 			'Структура файла', 'required|callback__clb_is_valid_structure');

		if ($this->form_validation->run() == FALSE)
		{
			$formdata['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
			$formdata['ordername_default']	= _jb_sitedata('title');

			$this->load->view($this->hf_path . '/header', array
			(
				'bodyclass'			=> $this->bodyclass . ' hide-message-no-prices',
			));
			
			$this->load->view('admin/vendor_add', $formdata);
		}
		else
		{
			$data = p(array(
					'vendor_name',
					'delivery_days',
					'price_correction',
					'orderemail',
					'ordername',
					'struct_art_number',
					'struct_sup_brand',
					'struct_description',
					'struct_qty',
					'struct_price',
					'api_id',
					'api_key1',
					'api_key2',
			), TRUE);
			
			if ($id)
			{
				$this->local->update_vendor($id, $data);
				$formdata['success']	= '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно.</strong> Параметры склада обновлены.</div>';
				
				$this->load->view($this->hf_path . '/header', array
				(
					'bodyclass'			=> $this->bodyclass,
				));
				
				$this->load->view('admin/vendor_add', $formdata);
			}
			else
			{
				$this->local->add_vendor($data, 'default');
				redirect('admin/vendors_list/added');
			}
			
		}
		
		$this->load->view($this->hf_path . '/footer');
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor Crosses Add
	 *
	 * Adds crosses vendor or edits one, if the ID is specified.
	 * The difference between normal vendor and crosses vendor is that we just need a name for second one.
	 * As opposite to normal were we ask to specify delivery days and price multiplier.
	 *
	 * @param	bool|int $id
	 * @return	void
	 */	
	public function vendor_crosses_add($id=FALSE)
	{
        $this->load->view($this->hf_path . '/header', array
        (
            'bodyclass'		=> $this->bodyclass,
        ));

        if ($id)
        {
            $vendor	= $this->local->get_vendor($vendor_id, 'id, vendor_name, vendor_type');
            $formdata['form'] = (array) $vendor;
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('vendor_name', 'Название', 'required|max_length[255]|xss_clean|strip_tags');

        if ($this->form_validation->run() == FALSE)
        {
            $formdata['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
            $this->load->view('admin/vendor_crosses_add', $formdata);
        }
        else
        {
            $this->local->add_vendor(p(array('vendor_name')), 'crosses');
            redirect('admin/vendor_crosses_list/added');
        }

        $this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendors List
	 *
	 * Shows the list of vendors available.
	 *
	 * @param	bool|string $message
	 * @return	void
	 */	
	public function vendors_list($message=FALSE)
	{
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass . ' hide-message-no-prices',
		));
		
		$this->load->view('admin/vendor_list', array
		(
			'vendors'			=> $this->local->vendors_list_result('default'),
			'message'			=> $message,
			'no_prices'		=> ($this->local->prices_vndr_stats('rows')->rows == 0)?TRUE:FALSE,
		));
		
		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Vendor Crosses List
	 *
	 * Lists vendors marked as crosses vendors.
	 *
	 * @param	bool|string $message
	 * @return	void
	 */	
	public function vendor_crosses_list($message=FALSE)
	{
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		
        $vendors = $this->local->vendors_list_result('crosses');

        $this->load->view('admin/vendor_crosses_list', array
        (
            'vendors'		=> $vendors,
            'num_vendors'	=> $vendors->num_rows(),
            'message'		=> $message
        ));

		$this->load->view($this->hf_path . '/footer');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Delete Vendor
	 *
	 * Performs delete of a vendor specified by ID and redirects back to the calling method.
	 *
	 * @param	int $vendor_id
	 * @param	string $calling_method
	 * @param	bool|string $is_confirmed
	 * @return	void
	 */	
	public function delete_vendor($vendor_id, $calling_method = 'import_prices' /* used for redirect */, $confirmed=FALSE)
	{
		// Get vendor data
		if (($vendor = $this->local->get_vendor($vendor_id, 'vendor_type, allow_delete')) !== FALSE)
		{
		
			if (!$confirmed)
			{
				$this->load->view($this->hf_path . '/header', array
				(
					'bodyclass'			=> $this->bodyclass,
				));
				
				$this->load->view('admin/vendor_delete_confirm', array
				(
					'vendor_id'				=> $vendor_id,
					'calling_method'	=> $calling_method,
				));
				
				$this->load->view($this->hf_path . '/footer');
			}
			else
			{
				// Vendor type is default
				if ($vendor->vendor_type == 'default')
				{
					// Delete vendor's prices
					$this->local->delete('prices', $vendor_id, 'vendor_id', FALSE);
				}
				
				// Vendor is crosses vendor
				else
				{
					// Delete vendor's crosses
					$this->local->delete('crosses', $vendor_id, 'vendor_id', FALSE);
					
					// Delete vendor's crosses search table data
					$this->local->delete('crosses_search', $vendor_id, 'vendor_id', FALSE);
				}
				
				// Optimize after delete
				// $this->local->optimize_tables();
				
				// Vendor delete is allowed
				if ($vendor->allow_delete)
				{
					// Delete vendor
					$this->local->delete('vendors', $vendor_id, 'id', 1, TRUE);
					
					// Redirect back
					redirect('admin/' . $calling_method . '/deleted');
				}
				
				// Vendor delete is prohibited
				else
				{
					// Clear vendor stats cache
					$this->local->update_vendor($vendor_id, array
					(
						'rows_cache'	=> 0,
						'qtys_cache'	=> 0
					));
					
					// Redirect back
					redirect('admin/' . $calling_method . '/cleared');
				}
				
			}
		}
		else
		{
			show_error("Vendor doesn't exist");
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Ajax Upload
	 *
	 * An iframe uploader, usually called ajax uploader. Takes file and passes it to XMLReader after upload is complete.
	 *
	 * @param	int $vendor_id
	 * @return	void
	 */	
	public function ajax_upload($type, $id = false)
	{
		$this->load->model('xmlimport_model', 'xmlimport');
		
		if ($type == 'vendor')
		{
			if (($vendor = $this->local->get_vendor($id, 'id, price_correction, structure_id, vendor_type, struct_art_number, struct_sup_brand, struct_description, struct_qty, struct_price')) === FALSE)
			{
				show_error('no such vendor');
			}
			
			// For research purposes we are not encrypting name making a readable name instead
			$config['file_name']			= $vendor->vendor_type . '_' . $vendor->id . '_' . random_string('unique') . '.xml';
		}
		else
		{
			$config['encrypt_name']		= TRUE;
		}
		
		$config['upload_path']			= $this->drop_in_path;
		$config['allowed_types']		= 'xml';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$d['result']					= $this->upload->display_errors(' ', ' ');
			$d['resultcode']				= 'upload_failed';
			
			$this->load->view('admin/ajax_upload_result', $d);
		}
		else
		{
			// Load the head
			echo $this->load->view('admin/ajax_upload_result_head','', TRUE);
			
			// Grab uploaded file data
			$upload = $this->upload->data();
			
			// Load the appropriate scenario
			if ($type == 'vendor')
			{
				// Prices vendor
				if ($vendor->vendor_type == 'default')
				{
					$import_result = $this->xmlimport->prices($upload['full_path'], $vendor);
				}
				
				// Crosses vendor
				elseif ($vendor->vendor_type == 'crosses')
				{
					$import_result = $this->xmlimport->crosses($upload['full_path'], $vendor);
				}
			}
			elseif ($type == 'users')
			{
				$import_result = $this->xmlimport->users($upload['full_path']);
			}
			
			// Nothing was inserted. We failed.
			if ($import_result->rows_inserted == 0)
			{
				// File has 0 rows
				if ($import_result->line_count == 0)
				{
					$d['result']					= '<h4>Ошибка импорта</h4><p>Файл загрузился успешно, но нам не удалось найти в нем строк с данными. <b>Проверьте, пожалуйста, соответствие файла формату.</b></p>';
				}
				
				// File has rows, but nothing was inserted
				else
				{
					$d['result']					= '<h4>Ошибка импорта</h4><p>Из <b>' . $import_result->line_count . ' строк</b> в файле, нам не удалось импортировать ни одной. <b>Проверьте пожалуйста соответствие формату.</b></p>';
				}
				
				$d['resultcode']				= 'import_failed';
			}
			
			// We've got something
			elseif ($import_result->rows_inserted < $import_result->line_count)
			{
				// Let's calculate how much (in %) was actually inserted (P = A1 / A2 * 100. A2 is haystack.)
				$insert_rate = ceil($import_result->rows_inserted / $import_result->line_count * 100);
				
				// More than 60 percent was inserted. Not bad.
				if ($insert_rate > 60)
				{
					$d['result']					= '<h4>Успешно. Нам удалось импортировать почти ' . $insert_rate . '% файла</h4> <p>Это очень неплохо. Видимо, часть строк просто не соотвествует формату.</p>';
					$d['resultcode']				= 'soft_soft_fail';
				}
				else
				{
					$d['result']					= '<h4>Успешно. Но не совсем.</h4><p>Нам удалось импортировать только ' . $insert_rate . '% файла.</strong> Видимо, большая часть строк не соответствует формату.</p>';
					$d['resultcode']				= 'soft_fail';
				}
			}
			
			// Everything was inserted
			else
			{
				$d['result']					= "<h4>Успешно.</h4><p>Процесс импорта завершен успешно. Импортированы все строки.</p>";
				$d['resultcode']				= 'ok';
			}
			
			// Output the last line 
			echo "\n== Импортировано строк: $import_result->rows_inserted из $import_result->line_count ==";
			
			// Load view
			// $this->load->view('admin/ajax_upload_result', $d);
			
			// Load the bottom
			echo $this->load->view('admin/ajax_upload_result_bottom', $d, TRUE);
		}
		
	}
	
	public function orders_by_user($user_id, $page = 1)
	{
		$this->orders('service_by_user', $page, $user_id);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Orders
	 *
	 * Paged list of archived/non-archived orders.
	 *
	 * @param	bool|int|string $seg1
	 * @param	bool|int|string $seg2
	 * @return	void
	 */
	public function orders($seg1 = FALSE /* msg / page / is_archived */, $seg2 = FALSE /* msg / page */)
	{
		$page = 0;
		$is_archived = 0;
		$msg = FALSE;

		$subpages = array('archived');

		// decide where to go
		if (ctype_digit($seg1)) {
			$page = $seg1 - 1;
		} elseif (in_array($seg1, $subpages)) {
			if ($seg1 == 'archived') {
				$is_archived = 1;
			}
			if (ctype_digit($seg2)) {
				$page = $seg2 - 1;
			} elseif ($seg2 !== FALSE) {
				$msg = $seg2;
			}
		} elseif ($seg1 !== FALSE) {
			$msg = $seg1;
		}

		// in case we've got wrong page
		if ($page < 0)
			$page = 0;

		// the logic
		if ($is_archived) {
			$d['is_archived'] = TRUE;

			$config['uri_segment'] = 4;
			$config['base_url'] = site_url('admin/orders/archived') . '/';
		} else {
			$config['base_url'] = site_url('admin/orders') . '/';
		}

		// Filter
		$orders_filter['orders.is_archived'] = $is_archived;

		$config['total_rows'] = $this->order->get_all($orders_filter, '', FALSE, 'count');
		$config['per_page'] = $this->order->orders_per_page;

		// Init Pagination
		$this->load->library('pagination');
		$this->pagination->initialize($config);

		// Load currency model
		$this->load->model('currency_model', 'currency');

		// Get site's default currency
		$currency = $this->currency->get();

		// Data for display
		$d['orders'] = $this->order->get_all($orders_filter, '', $page);

		$d['pagination'] = $this->pagination->create_links();
		$d['msg'] = $msg;
		$d['order_statuses'] = $this->order->statuses;
		$d['is_sample_orders'] = $this->order->is_sample_orders;
		$d['is_non_sample_exist'] = $this->order->is_non_sample_exist;
		$d['currency_symbol'] = $currency->symbol;

		$this->load->view($this->hf_path . '/header', array
		(
				'bodyclass' => $this->bodyclass,
		));

		$this->load->view('admin/orders_list', $d);
		$this->load->view($this->hf_path . '/footer');
	}
	
	public function orders_export_table($is_archived=FALSE)
	{
		if ($is_archived)
			$is_archived = 1;
		else
			$is_archived = 0;
			
		// $this->output->set_content_type('xml');
		
		$this->order->orders_per_page = FALSE;
		$os = $this->order->get_all($is_archived, 'orders.is_archived');
		
		$this->load->view('admin/orders_export_table', array('os'=>$os, 'sts'=>$this->order->statuses));
	}
	
	function orders_by_vendor ()
	{
		$this->order->orders_per_page = FALSE;
		
		$d['order']				= $this->order->get_all(array('order_items.status'=>8, 'orders.is_archived'=>0), NULL, 0, 'array', 'order_items.vendor_name, orders.date desc');
		
		$d['prev_vendor_name'] = '';
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'				=> $this->bodyclass,
		));
		
		$this->load->view('admin/orders_by_vendor_list', $d);
		$this->load->view('admin/orders_by_vendor_table', $d);
		$this->load->view($this->hf_path . '/footer');
	}
	
	public function orders_by_vendor_csv($vendor_id, $download=false)
	{
		// Just in case. Old orders doesn't have vendor_id
		if (empty($vendor_id))
			show_error('no vendor id supplied');
			
		$this->order->orders_per_page = FALSE;
		
		$d['order']				= $this->order->get_all(array('order_items.status'=>8, 'order_items.vendor_id'=>$vendor_id, 'orders.is_archived'=>0), NULL, 0, 'array', 'order_items.vendor_name, orders.date desc');
		$d['vendor_id']		= $vendor_id;
		
		if ($download)
		{
			$this->order->items_set_status($d['order'], 9);
			$this->order->csv(false /* makes direct output */ , $d['order']);
		}
		else
		{
			$d['prev_vendor_name'] = $d['order'][0]->vendor_name;
			
			$this->load->view($this->hf_path . '/header', array
			(
				'bodyclass'				=> $this->bodyclass,
			));
			
			$this->load->view('admin/orders_by_vendor_predownload_prompt', $d);
			$this->load->view('admin/orders_by_vendor_table', $d);
			$this->load->view('admin/orders_by_vendor_predownload_prompt_btns', $d);
			$this->load->view($this->hf_path . '/footer');
		}
	}
	
	public function orders_email_to_vendor($vendor_id, $send=false)
	{
		// Just in case. Old orders doesn't have vendor_id
		if (empty($vendor_id))
			show_error('no vendor id supplied');
		
		$this->order->orders_per_page = FALSE;
		
		$d['order']				= $this->order->get_all(array('order_items.status'=>8, 'order_items.vendor_id'=>$vendor_id, 'orders.is_archived'=>0), NULL, 0, 'array', 'order_items.vendor_name, orders.date desc');
		$d['vendor_id']		= $vendor_id;
		
		if ($send)
		{
			$this->load->model('mail_msgs_model', 'mail_messaging');
			$file = "e/" . random_string('unique') . ".csv";
			
			$fp = fopen($file, "w");
			$this->order->csv($fp, $d['order']);
			
			$clientname = (!empty($d['order'][0]->ordername))?$d['order'][0]->ordername:_jb_sitedata('title');
			
			$this->mail_messaging->order_to_vendor($d['order'][0]->orderemail, $d['order'],  $clientname, $file);
			
			$this->order->items_set_status($d['order'], 9);
			
			unlink($file);
			redirect('admin/orders_by_vendor');
		}
		else
		{
			$d['prev_vendor_name'] = $d['order'][0]->vendor_name;
			
			$this->load->view($this->hf_path . '/header', array
			(
				'bodyclass'				=> $this->bodyclass,
			));
			
			$this->load->view('admin/orders_by_vendor_presend_prompt', $d);
			$this->load->view('admin/orders_by_vendor_table', $d);
			$this->load->view('admin/orders_by_vendor_presend_prompt_btns', $d);
			$this->load->view($this->hf_path . '/footer');
		}
	}
	

	
	// --------------------------------------------------------------------
	
	/**
	 * Put_title_here
	 *
	 * 
	 *
	 * @param	
	 * @return
	 */	
	 
	public function order_new_empty()
	{
		$neworder = $this->order->make(0 /* no user */, null /* empty cart */, null, 21 /* status */);
		redirect("admin/order_data/{$neworder->id}");
	}
	

	public function order_add_item($orderid, $rowid = false)
	{
		if (($order_data = $this->order->get_all($orderid, 'orders.id', 0, 'array_allow_false')) !== FALSE)
		{
			if ($rowid and ($dbitem = $this->local->prices_row($rowid, 'object_extended')) !== FALSE)
			{
				$item = (object) array
				(
					'art_number'									=> $dbitem->art_number,
					'sup_brand'										=> $dbitem->sup_brand,
					'description'									=> $dbitem->description,
					'price'												=> $dbitem->price,
					'qty'													=> ($dbitem->qty > 0)?1:0, // Some items may have zero availability
					'vendor_name'									=> $dbitem->vendor_name,
				);
				
				$this->order->items_insert_dbcall($orderid, $item, $order_data);
				
				redirect("admin/order_data/$orderid");
			}
			else
			{
				$d['orderid'] = $orderid;
				
				$this->load->view($this->hf_path . '/header', array
				(
					'bodyclass'				=> $this->bodyclass,
				));
				
				$this->load->view('admin/orders_add_item_search_form', $d);
				
				if (p('search'))
				{
					$data = $this->local->prices_v2(p('search'), TRUE, TRUE);
					$d['prices'] = array();
					
					foreach ($data->prices_result->result() as $r)
					{
						$d['prices'][] = $r;
					}
					
					$this->load->view('admin/orders_add_item_search_results', $d);
				}
				
				$this->load->view('admin/order_add_item_bottom_actions');
			}
		}
		else
		{
			show_error('No such order');
		}
	}
	
	// A much better one thing than the order_add_item()
	public function order_add_to($id)
	{
		// List cart items
		$order = $this->cart->list_items();
		
		$dborder = $this->order->get_all($id, 'orders.id', 0, 'items_services_split_allow_false', 'orders.date,order_items.type desc');
		// $dborder = $this->order->get_all_compact($id, 'orders.id', 0, 'array_allow_false', 'orders.date desc');
		// var_dump($dborder);
		
		if (!empty($dborder->items))
		{
			$dborder = $dborder->items;
			$order['discount'] = $dborder->meta->discount;
			$order['delivery'] = (object) array
			(
				'id'	=> $dborder[0]->delivery_method,
			);
		}
		else
		{
			$dborder = $this->order->get_all_compact($id, 'orders.id', 0, 'array_allow_false', 'orders.date desc');
			
			if (empty($dborder))
			{
				show_error('Заказ не найден');
			}
			
			$order['discount'] = (!empty($dborder[0]->user_default_discount))?$dborder[0]->user_default_discount:0;
			$order['delivery'] = (object) array
			(
				'id'	=> null,
			);
		}
		
		if (count($order['items']) > 0 and !empty($dborder))
		{
			// Make an order
			$neworder = $this->order->make(null /* not needed when adding */, $order, $id);
		}
		
		// redirect('admin/order_data/' . $id);
	}
	
	// --------------------------------------------------------------------
	

	/**
	 * Order Data
	 *
	 * Shows data of an order specified by ID.
	 * Optional print-ready layout can be tirggered.
	 *
	 * @param	bool|int $id
	 * @param	bool|string $print
	 * @param	bool|string $msg
	 * @return	void
	 */
	 
	public function order_data($id=false, $print=false, $msg=false)
	{
		$this->load->model('currency_model', 'currency');

		if ($id)
		{
			// Fetch order data
			$order_db = $this->order->get_all($id, 'orders.id', 0, 'items_services_split_allow_false', 'orders.date,order_items.type desc');

			// This happens when we have order with no order_items
			if (empty($order_db))
			{
				$order_db = $this->order->get_all_compact($id, 'orders.id', 0, 'array_allow_false', 'orders.date desc');
				$order = $order_db[0];
				
				if (empty($order))
				{
					show_error('Заказ не найден');
				}
			}
			else
			{
				$order								= $order_db->meta;
				$d['order_items']			= $order_db->items;
				$d['order_services']	= $order_db->services;
			}
			
			// Get site's default currency
			$currency = $this->currency->get();
			$d['currency_symbol']		= $currency->symbol;
			
			// Add vendors
			$vendors = $this->local->vendors_list_result('default');
			$d['vendors'] = array();
			
			foreach ($vendors->result() as $v)
			{
				$d['vendors'][$v->id] = $v->vendor_name;
			}
			
			// Dellivery Methods
			$d_mthds = $this->delivery->methods();
			$d['d_mthds'] = array();
			
			foreach ($d_mthds->result() as $r)
			{
				$d['d_mthds'][$r->id] = "{$r->title} (" . price_format($r->price) . ")";
			}
			
			$order->order_date_r 		= date_tz('d.m.Y H:i', $order->order_date);
			
			$d = $this->order->fetch_status_data($order->order_status, $d);
			
			$order->order_total_f						= number_format($this->order->order_total, 2, ".", "");
			$order->order_items_total_f			= number_format($this->order->order_items_total, 2, ".", "");
			$order->amount_unpaid_f					= number_format($this->order->amount_unpaid, 2, ".", "");
			
			if ($order->dmthd_price > 0)
			{
				$order->order_items_total_dlvr	= number_format($this->order->order_items_total + $order->dmthd_price, 2, ".", "");
				$order->order_total_dlvr				= number_format($this->order->order_total + $order->dmthd_price, 2, ".", "");
			}
			
			$d['order']									= $order;
			$d['msg']										= $msg;
			
			if ($print and $print != 'false')
			{
				$this->load->helper('number');
				
				$d['sitetitle']				= _jb_sitedata()->title;
				$d['sitesubtitle']		= _jb_sitedata()->subtitle;
				
				$d['order_date_rus']	= sprintf(date('d %\s Y г.', $order->order_date), rus_month(date('n', $order->order_date)));
				
				// Num2Str uses Roubles as the one and only currency
				if ($currency->code == 'RUR')
				{
					$order_total_str = num2str($this->order->order_total);
					$order_total_dlvr_str = num2str($this->order->order_total + $order->dmthd_price);
					
					$order_total_str = mb_convert_case(mb_substr($order_total_str, 0, 1), MB_CASE_UPPER, "UTF-8") . mb_substr($order_total_str, 1);
					$order_total_dlvr_str = mb_convert_case(mb_substr($order_total_dlvr_str, 0, 1), MB_CASE_UPPER, "UTF-8") . mb_substr($order_total_dlvr_str, 1);
					
					$d['order_total_str']	= $order_total_str;
					$d['order_total_dlvr_str']	= $order_total_dlvr_str;
				}
				
				$this->load->view('admin/order_data_print', $d);
			}
			else
			{
				$this->load->view($this->hf_path . '/header', array
				(
					'bodyclass'			=> $this->bodyclass,
				));
				
				$this->load->view('admin/order_data_header', $d);
				$this->load->view('admin/order_data_meta', $d);
				$this->load->view('admin/order_data', $d);
					
				$this->load->view($this->hf_path . '/footer');
			}
		}
		else
		{
			redirect('admin/orders');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Order Update
	 *
	 * Performs an order update routine. Prompts to send an update notification to user. 
	 * Redirects afterwards.
	 *
	 * @param	int $id
	 * @param	string|bool $send_msg
	 * @return	void
	 */	
	public function order_update($id, $send_msg=FALSE)
	{
		if (($order_data = $this->order->get_all_compact($id, 'orders.id', 0, 'array_allow_false')) !== FALSE)
		{
		
			if ($send_msg)
			{				
				$this->mail_messaging($order_data[0]->email, $order_data[0]->order_human_id, $order_data[0]->vericode);
				redirect('admin/orders/updated');
			}
			else
			{
				$this->order->items_update($id, p('data'));

				$this->load->view($this->hf_path . '/header', array
				(
						'bodyclass'		=> $this->bodyclass,
				));

				$this->load->view('admin/order_send_notification_prompt', array
				(
						'id'			=> $id,
						'human_id'		=> $order_data[0]->order_human_id,
				));

				$this->load->view($this->hf_path . '/footer');
			}
		}
		else
		{
			show_error('No such order');
		}
	}
	
	// A global status setter for an order
	public function order_set_status($orderid, $status)
	{
		$this->order->status_set($orderid, $status);
		redirect("admin/order_data/$orderid");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Order Archive
	 *
	 * Marks an order specified by ID as an archived. 
	 * Rollback param allows to un-archive.
	 * Redirects to the orders (or archived orders) list afterwards.
	 *
	 * @param	int $id
	 * @param	string|bool $rollback
	 * @return	void
	 */	
	public function order_archive($id, $rollback=FALSE)
	{
		if ($rollback)
		{
			$this->order->archived_status_set($id, 0); // Moving out of archive
			redirect('admin/orders/archived/sent_out_of_archive');
		}
		else
		{
			$this->order->archived_status_set($id, 1); // Putting into archive
			redirect('admin/orders/sent_to_archive');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Order Change User
	 *
	 * Allows to change the user (customer) attached to the order specified by ID.
	 * Lists all available customers, saves and redirects to the order data page.
	 *
	 * @param	int $id
	 * @param	bool|string $set
	 * @return	void
	 */	
	public function order_change_user($id, $set=FALSE)
	{		
		if ($this->order->is_order($id))
		{
			if ($set !== FALSE)
			{
				$this->order->assign_to_user($id, $set);				
				redirect("admin/order_data/$id/false/user_updated");
			}
			else
			{
				$order_data = $this->order->get_all_compact($id);
				
				$this->load->view($this->hf_path . '/header', array
				(
					'bodyclass'			=> $this->bodyclass,
				));
				$this->load->view('admin/order_change_user', array
				(
					'orderid'					=> $id,
					'order_human_id'	=> $order_data[0]->order_human_id,
					'userid'					=> $order_data[0]->user_id,
					'users'						=> $this->users->get(FALSE, FALSE, $order_data[0]->user_id /* selected id */),
					'order_date_r'		=> date_tz('d.m.Y H:i', $order_data[0]->order_date),
				));
				
				$this->load->view($this->hf_path . '/footer');
			}
		}
		else
		{
			show_error('No such order');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * User Add
	 *
	 * Adds a new user (customer) with an option to attach to an order specified by ID.
	 *
	 * @param	bool|int $attach_to_order
	 * @return	void
	 */	
	public function user_add($attach_to_order = FALSE, $edit_id = FALSE)
	{
		$this->load->model('mail_msgs_model', 'mail_messaging');
		
		$this->load->library('form_validation');
		
		if ($edit_id)
			$this->form_validation->set_rules('email', 'E-mail', 'valid_email');
		else
			$this->form_validation->set_rules('email', 'E-mail', 'valid_email|callback__clb_email_unique');
		
		$this->form_validation->set_rules('name', 'Имя клиента', 'required|xss_clean|strip_tags');
		$this->form_validation->set_rules('phone', 'Телефон', 'xss_clean|strip_tags');
		$this->form_validation->set_rules('address', 'Адрес', 'xss_clean|strip_tags');
		$this->form_validation->set_rules('discount', 'Скидка', 'required|is_natural|greater_than[-1]|less_than[101]');
		
		$this->form_validation->set_rules('corp_inn', 'ИНН / КПП', 'xss_clean|strip_tags');
		$this->form_validation->set_rules('corp_ogrn', 'ОГРН / ОГРНИП', 'xss_clean|strip_tags');
		$this->form_validation->set_rules('corp_rs', 'Р/с', 'xss_clean|strip_tags');
		$this->form_validation->set_rules('corp_bik', 'БИК', 'xss_clean|strip_tags');

		
		if ($edit_id)
		{
			$user = $this->users->get($edit_id);
			
			if ($user)
			{
				$user->userdata = unserialize($user->userdata);
				$form = $this->users->userdata_one_level_array($user);
			}
		}
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view($this->hf_path . '/header', array
			(
				'bodyclass'		=> $this->bodyclass,
			));
			
			$this->load->view('admin/user_add', array
			(
				'orderid'						=> $id,
				'attach_to_order'		=> $attach_to_order,
				'errors'						=> validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after')),
				'form'							=> $form,
				'edit_id'						=> $edit_id,
				// 'users'				=> $this->users->get(FALSE, FALSE, $order_data[0]->user_id /* selected id */),
				// 'order_date_r'		=> date_tz('d.m.Y H:i', $order_data[0]->order_date),
			));
			
			$this->load->view($this->hf_path . '/footer');
		}
		else
		{
			$pdata = p(array('name','phone','address','corp_inn','corp_ogrn','corp_rs','corp_bik'), TRUE);
			
			if ($edit_id)
			{
				$this->users->user_update(p('email'), $pdata, $this->input->post('discount'), $edit_id);
				
				if ($attach_to_order)
					redirect("admin/order_data/$attach_to_order");
				else
					redirect('admin/users/client-updated');
			}
			else
			{
				$email = p('email');
				$new_user = $this->users->add($email, $pdata, p('discount'));
				
				if ($email)
					$this->mail_messaging->registration_by_admin($email, p('name'), $new_user->password);
					
				if ($attach_to_order)
					$this->order_change_user($attach_to_order, $new_user->id);
				else
					redirect('admin/users/added');
			}
		}
	}
	

	
	 
	
	// --------------------------------------------------------------------
	
	/**
	 * Login
	 *
	 * Shows login form. Redirects to admin's home if user is authorised.
	 *
	 * @return	void
	 */	
	public function login()
	{
		if ($this->access->_is_admin())
		{
			redirect('admin');
		}
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('login', 'Логин', 'required|callback__clb_auth');
		$this->form_validation->set_rules('password', 'Пароль', 'required');
		
		if ($this->form_validation->run() == FALSE)
		{			
			$d['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
			
			$this->load->view('admin/login_form_solo', $d);
		}
		else
		{
			redirect('admin');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Logout
	 *
	 * Destroys session variables associated with authorisation and redirects to login form.
	 *
	 * @return	void
	 */	
	public function logout()
	{
		$this->session->unset_userdata('adm_login');
		$this->session->unset_userdata('adm_pass');
		
		redirect('admin/login');
	}
	

	
	public function stats()
	{
		$d['stats'] = $this->appflow->stats_search_get();
		
		$this->load->view($this->hf_path . '/header', array
		(
			'bodyclass'		=> $this->bodyclass,
		));
		
		$this->load->view('admin/stats_table', $d);
		$this->load->view($this->hf_path . '/footer');
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Jbimages Upload
	 *
	 * The upload routine function moved from jbimages
	 *
	 * @return	void
	 */	
	public function jbimages_upload (/* $lang='english' */)
	{
		// Set language
		// $this->_lang_set($lang);
		
		// User configured settings
		$this->config->load('uploader_settings', TRUE);
		
		// Get configuartion data (we fill up 2 arrays - $config and $conf)
		
		$conf['img_path']			= $this->config->item('img_path',		'uploader_settings');
		$conf['allow_resize']		= $this->config->item('allow_resize',	'uploader_settings');
		
		$config['allowed_types']	= $this->config->item('allowed_types',	'uploader_settings');
		$config['max_size']			= $this->config->item('max_size',		'uploader_settings');
		$config['encrypt_name']		= $this->config->item('encrypt_name',	'uploader_settings');
		$config['overwrite']		= $this->config->item('overwrite',		'uploader_settings');
		$config['upload_path']		= $this->config->item('upload_path',	'uploader_settings');
		
		if (!$conf['allow_resize'])
		{
			$config['max_width']	= $this->config->item('max_width',		'uploader_settings');
			$config['max_height']	= $this->config->item('max_height',		'uploader_settings');
		}
		else
		{
			$conf['max_width']		= $this->config->item('max_width',		'uploader_settings');
			$conf['max_height']		= $this->config->item('max_height',		'uploader_settings');
			
			if ($conf['max_width'] == 0 and $conf['max_height'] == 0)
			{
				$conf['allow_resize'] = FALSE;
			}
		}
		
		// Load uploader
		$this->load->library('upload', $config);
		
		if ($this->upload->do_upload()) // Success
		{
			// General result data
			$result = $this->upload->data();
			
			// Shall we resize an image?
			if ($conf['allow_resize'] and $conf['max_width'] > 0 and $conf['max_height'] > 0 and (($result['image_width'] > $conf['max_width']) or ($result['image_height'] > $conf['max_height'])))
			{				
				// Resizing parameters
				$resizeParams = array
				(
					'source_image'	=> $result['full_path'],
					'new_image'		=> $result['full_path'],
					'width'			=> $conf['max_width'],
					'height'		=> $conf['max_height']
				);
				
				// Load resize library
				$this->load->library('image_lib', $resizeParams);
				
				// Do resize
				$this->image_lib->resize();
			}
			
			// Add our stuff
			$result['result']		= "file_uploaded";
			$result['resultcode']	= 'ok';
			$result['file_name']	= $conf['img_path'] . '/' . $result['file_name'];
			
			// Output to user
			$this->load->view('admin/jbimages/ajax_upload_result', $result);
		}
		else // Failure
		{
			// Compile data for output
			$result['result']		= $this->upload->display_errors(' ', ' ');
			$result['resultcode']	= 'failed';
			
			// Output to user
			$this->load->view('admin/jbimages/ajax_upload_result', $result);
		}
	}

    /**
     * Sample XML
     *
     * A wrapper. Used to download the sample xml file with prices and stockdata.
     *
     * @return	void
     */
    public function sample_xml()
    {
        // Load Download Helper
        $this->load->helper('download');

        // Grab the file
        $data = file_get_contents("e/sample-data/prices-sample-xml.xml");

        // Send to user
        force_download('prices_sample.xml', $data);
    }

    // --------------------------------------------------------------------

    /**
     * Sample Crosses XML
     *
     * A wrapper. Used to download the sample sml file with crosses.
     *
     * @return	void
     */
    public function sample_crosses_xml()
    {
        // Load Download Helper
        $this->load->helper('download');

        // Grab the file
        $data = file_get_contents("e/sample-data/crosses-sample-xml.xml");

        // Send to user
        force_download('crosses_sample.xml', $data);
    }
	
	// --------------------------------------------------------------------
	
	/**
	 * Jbimages Blank
	 *
	 * This is default source for jbimages upload iframe.
	 *
	 * @return	void
	 */	
	public function jbimages_blank($lang='english')
	{
		$this->_lang_set($lang);
		$this->load->view('admin/jbimages/blank');
	}

	// --------------------------------------------------------------------
	
	/**
	 * CALLBACK Auth
	 *
	 * Processes an authorisation routine.
	 *
	 * @param	string $login
	 * @return	bool
	 */	
	function _clb_auth($login)
	{
		$this->form_validation->set_message('_clb_auth', '<strong>Неверный логин/пароль.</strong> Попробуйте еще раз.');
		
		$pass = $this->input->post('password');
		
		if ($this->access->admin($login, $pass))
		{
			$this->session->set_userdata('adm_login', $login);
			$this->session->set_userdata('adm_pass', $pass);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CALLBACK Check Admin Pass
	 *
	 * Checks if password input was right. Required on password change.
	 *
	 * @param	string $adminpass
	 * @return	bool
	 */	
	public function _clb_checkadminpass ($adminpass)
	{
		$this->form_validation->set_message('_clb_checkadminpass', '<strong>Неверный пароль</strong>. Указанный текущий пароль не верен.');
		return $this->access->admin($this->access->adminlogin, $adminpass);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CALLBACK Is Valid Structure [Experimental]
	 *
	 * Checks if structure requested exists. 
	 * Structures are not yet implemented, so this callback return true if structure equals to 1.
	 *
	 * @param	int $value
	 * @return	bool
	 */	
	public function _clb_is_valid_structure($value)
	{
		$this->form_validation->set_message('_clb_is_valid_structure', 'Выбранная структура файла не существует.');
		
		if ($value == 1) return TRUE;
		return FALSE;
	}
	

	
	// --------------------------------------------------------------------
	
	/**
	 * CALLBACK Is Valid Term
	 *
	 * Checks if termid supplied exists for this termtype
	 *
	 * @param	string $domain
	 * @return	bool
	 */	
	public function _clb_is_valid_term($termid)
	{
		$this->form_validation->set_message('_clb_is_valid_term', 'Идентификатор родительской категории указан неверно.');
		
		if ($termid == 0 /* 0 when no parent defined */ or $this->terms->get($this->terms->thistypeid, $termid, 'id', 'count'))
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CALLBACK Parse Brand
	 *
	 * 
	 *
	 * @param	string $the_string
	 * @return	bool
	 */	
	public function _clb_parse_brand($the_string)
	{
		$this->form_validation->set_message('_clb_parse_brand', 'Наименование бренда может состоять только из латинских букв и цифр.');
		
		$brand = preg_replace('#\W#', '', strtolower($the_string));
		
		if ($brand != '')
		{
			return $brand;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CALLBACK Parse Article Numbers
	 *
	 * 
	 *
	 * @param	string $the_string
	 * @return	bool
	 */	
	public function _clb_parse_article_nrs($the_string)
	{
		$this->form_validation->set_message('_clb_parse_article_nrs', 'Артикульный номер может состоять только из латинских букв и цифр.');
		
		// We have two or more art_numbers in a cell separated by coma/semicolon
		if (preg_match("/[,;]/", $the_string))
		{
			// We remove all whitespaces, then replace comas/semicolons by single space, then we remove everything except "words" and whitespaces
			// The result is well-formatted string AB234 GH24234 RTE88
			$art_numbers = preg_replace('#[^\w ]#', '', preg_replace("/[,;]/", ' ', preg_replace("#\s#", '', $the_string)));
			
			// Let's put semicolons back
			$art_numbers = str_replace(" ", ";", $art_numbers);
		}
		else
		{
			$art_numbers = preg_replace('#\W#', '', strtolower($the_string));
		}
		
		// Do we really have something left? Additionaly, we check if there is something besides whitespaces, in case user supplies ;;;;;; as an art_number
		if ($art_numbers != '' and preg_match("#\S#", $art_numbers))
		{
			return $art_numbers;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * CALLBACK Parse Multiple Article Numbers
	 *
	 * @note not used
	 *
	 * @param	string $domain
	 * @return	bool
	 */	
	public function _clb_parse_mltpl_article_nrs($the_string)
	{
		$this->form_validation->set_message('_clb_parse_mltpl_article_nrs', 'Артикульные номера указаны неверно');
		
		$art_numbers = '';
					
		if (preg_match("/[,;]/", $the_string))
		{
			$art_numbers = preg_replace('#[^\w,;]#', '', preg_replace("#\s#", '', $the_string));
		}
		else
		{
			$art_numbers = preg_replace('#\W#', '', $the_string);
		}
		
		if ($art_numbers != '')
		{
			return $art_numbers;
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------

	/**
	 * CALLBACK Parse Multiple Article Numbers
	 *
	 * @note not used
	 *
	 * @param	string $domain
	 * @return	bool
	 */


	public function _clb_valid_api_id($api_id)
	{
		// Load apipuller
		$this->load->model('apipuller_model', 'apipuller', TRUE);

		// List APIs
		$apis = $this->apipuller->apis;

		if (isset($apis[$api_id]) or $api_id == '0')
		{
			return TRUE;
		}

		return FALSE;
	}
	
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */