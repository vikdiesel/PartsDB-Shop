<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Setup
 *
 * Used to process setup for new websites.
 *
 * @package			2find
 * @subpackage	Controllers
 * @category		Main Site
 * @author			Viktor Kuzhelnyi @marketto.ru
 *
 * @todo Comment methods flow
 */
class Setup extends CI_Controller {
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
    	parent::__construct();

		// Helpers
		$this->load->helper(array('input','url','form','website','text','string','language'));
		
		// Configs
		$this->load->config('jb');
		
		// Language
		$this->lang->load('pdbshop');

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Promo
	 * 
	 * The main entrance
	 * 
	 * @return	void
	 */	
	public function index()
	{
		$this->load->database();
		if (!$this->db->initialize()) {
			$cnt = $this->load->view('setup/setup_advice','',TRUE);
		} elseif ($this->tables_exist()) {
            $cnt = $this->load->view('setup/setup_success', '', TRUE);
		} else {
			$this->load->library('form_validation');

			$this->form_validation->set_rules('title', 'Название сайта', 'required|max_length[100]|xss_clean|strip_tags');
			$this->form_validation->set_rules('subtitle', 'Описание сайта', 'required|max_length[100]|xss_clean|strip_tags');
			$this->form_validation->set_rules('adminemail', 'E-mail администратора', 'required|valid_email|max_length[100]');
			$this->form_validation->set_rules('adminpass', 'Пароль администратора', 'required|max_length[32]');

			if ($this->form_validation->run() == FALSE) {
				$cnt = $this->load->view('setup/setup_form', array(
					'errors' => validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after')),
				), TRUE);
			} else {
				$query = file_get_contents('devdb_partsdbshop.sql');

				$query = preg_split("/-- [^\n]+\n/", $query);

				foreach ($query as $item) {
					$item = trim(preg_replace('/-- ?.*\n/', '', $item));

					if (!empty($item)) {
						$this->db->query($item);
					}
				}

				$this->load->model('setup_helper_model', 'setup_helper', TRUE);

				$this->setup_helper->setup(p(array('title','subtitle','adminemail','adminpass'), TRUE), TRUE);
				$this->setup_helper->setup_sample_pages();

				$cnt = $this->load->view('setup/setup_success', '', TRUE);
			}
		}

		$this->load->view('setup/template', array('cnt' => $cnt));

	}

    private function tables_exist() {
        $tables = array(
            'brands_lc',
            'crosses',
            'crosses_search',
            'delivery_methods',
            'options',
            'orders',
            'order_items',
            'posts',
            'post_terms',
            'prices',
            'sessions',
            'sites',
            'stats_search',
            'users',
            'vendors',
            'vendor_apipull_cache',
        );

        foreach ($tables as $table) {
            if (!$this->db->table_exists($table)) {
                return false;
            }
        }

        return true;
    }
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */
?>