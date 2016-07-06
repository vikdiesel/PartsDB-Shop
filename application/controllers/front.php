<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Autoparts Primary Controller
 *
 * @package            2find
 * @subpackage    Controllers
 * @category        Frontend
 * @author            Viktor Kuzhelnyi @marketto.ru
 *
 * @todo Add workflow comments
 * @todo Prices_search_results_utility
 */
class Front extends CI_Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Helpers
        $this->load->helper(array('url', 'form', 'website', 'text', 'language'));

        $this->load->database();

        if (!$this->db->initialize()) {
            redirect('setup');
        }

        // Essential for templates
        $this->load->library('parser');// Essential for templates

        // Configs
        $this->load->config('jb');

        // Critical models
        $this->load->model('local_helper_model', 'local', TRUE);

        if (empty($this->local->local_tables_status)) {
            redirect('setup');
        }

        $this->load->model('options_model', 'options', TRUE);

        // Libraries
        $this->load->library('session');
        $this->load->library('mobile_detect');

        $this->options->unserialize_on_get = TRUE;

        // Language
        // Load after CI_Model::jb_sites and before CI_Model::parts_db
        $this->lang->load('pdbshop');

        // PartsDB API
        $this->load->model('pdb_api_model', 'partsdb_api');

        // Other models
        $this->load->model('stock_model', 'stock', TRUE);
        $this->load->model('appflow_model', 'appflow', TRUE);
        $this->load->model('terms_model', 'terms', TRUE);
        $this->load->model('posts_model', 'posts', TRUE);
        $this->load->model('currency_model', 'currency', TRUE);

        // Cart models
        /// @todo Can be optimised by loading them only when needed.
        $this->load->model('cart_model', 'cart', TRUE);
        $this->load->model('delivery_model', 'delivery', TRUE);
        $this->load->model('users_model', 'users', TRUE);
        $this->load->model('order_model', 'order', TRUE);
        $this->load->model('access_model', 'access', TRUE);
        $this->load->model('mail_msgs_model', 'mail_messaging');

        // Cart Helpers
        $this->load->helper('input');
        $this->load->helper('string');

        // Catalogues
        $this->appflow->hdata->catalogues = array
        (
            array
            (
                'title' => 'Общие каталоги',
                'title_attr' => 'Общие каталоги',
                'url' => base_url(),
                'selected_class' => menu_selected(''),
            ),
        );

        if ($this->posts->get('extcat', FALSE, FALSE, FALSE, 'count')) {
            $this->appflow->hdata->is_extcats_present = TRUE;

            // Add the menu item
            $this->appflow->hdata->catalogues[] = array
            (
                'title' => 'Оригинальные каталоги',
                'title_attr' => 'Оригинальные каталоги',
                'url' => site_url('auto/genuine'),
                'selected_class' => menu_selected('auto/genuine'),
            );

            // We are using this in $this->genuine() in order to close access when nothing's there
            define('is_extcats_present', TRUE);
        } else {
            $this->appflow->hdata->is_extcats_present = FALSE;
        }

        /// Background head snippet
        /// @todo there is a condition inside this view file. Move it here.
        $this->appflow->hdata->bg_head_snippet_include = $this->load->view('front/bg_head_snippet', '', TRUE);


        // Top Banner
        $this->appflow->hdata->topbanner = $this->load->view('front/topbanner', '', TRUE);

        // Menu
        // Home Page
        $this->appflow->hdata->homepage_url = base_url();
        $this->appflow->hdata->homepage_selected_class = menu_selected('');

        // Cart Page
        $this->appflow->hdata->cart_url = site_url('cart');
        $this->appflow->hdata->cart_selected_class = menu_selected('cart.*');

        // Custom Pages List
        $menu_pages_list = $this->posts->get('page', false, '1', 'posts.meta', 'objects_set');

        foreach ($menu_pages_list as $key => $menu_page) {
            $menu_page->selected_class = menu_selected('page/' . $menu_page->permalink);
            $menu_page->url = site_url('page/' . $menu_page->permalink);
            $menu_page->title_attr = htmlspecialchars($menu_page->title);

            $menu_pages_list[$key] = $menu_page;
        }

        $this->appflow->hdata->topmenu = $menu_pages_list;

        // Admin Authorisation block
        if (!$this->options->get('hideadmlink')) {
            $this->appflow->hdata->admin_auth_block = $this->load->view('front/admin_auth_block', '', TRUE);
        } else {
            $this->appflow->hdata->admin_auth_block = '';
        }

        // Register block
        $this->appflow->hdata->reg_link_title = 'Регистрация';
        $this->appflow->hdata->reg_link_url = site_url('user/register');
        $this->appflow->hdata->reg_link_selected_class = menu_selected('user/register');

        // Auth Block
        if ($this->access->_is_auth()) {
            $this->appflow->hdata->auth_link_title = $this->access->accdata->email;
            $this->appflow->hdata->auth_link_url = site_url('user/orders');
            $this->appflow->hdata->auth_link_selected_class = menu_selected(array('user/orders.*', 'order/.+'));

            $this->appflow->hdata->auth_block_title = $this->access->accdata->email;
            $this->appflow->hdata->auth_block_links = array
            (
                array
                (
                    'title' => 'Ваши заказы',
                    'title_attr' => 'Ваши заказы',
                    'icon' => '<i class="icon-list-alt"></i>',
                    'url' => site_url('user/orders'),
                    'selected_class' => menu_selected(array('user/orders', 'order/.+')),
                ),
                array
                (
                    'title' => 'Сменить пароль',
                    'title_attr' => 'Сменить пароль',
                    'icon' => '<i class="icon-lock"></i>',
                    'url' => site_url('user/change_pass'),
                    'selected_class' => menu_selected('user/change_pass'),
                ),
                array
                (
                    'title' => 'Выход',
                    'title_attr' => 'Выход',
                    'icon' => '<i class="icon-off"></i>',
                    'url' => site_url('user/logout/' . bcklnk_mask('user/logout')),
                    'selected_class' => '',
                ),
            );
        } else {
            $this->appflow->hdata->auth_link_title = 'Вход';
            $this->appflow->hdata->auth_link_url = site_url('user/login/' . bcklnk_mask('user/login'));
            $this->appflow->hdata->auth_link_selected_class = menu_selected('user/login.*');

            $this->appflow->hdata->auth_block_title = 'Авторизация';

            $this->appflow->hdata->auth_block_links = array
            (
                array
                (
                    'title' => 'Вход клиента',
                    'title_attr' => 'Вход клиента',
                    'icon' => '<i class="icon-user"></i>',
                    'url' => site_url('user/login/' . bcklnk_mask('user/login')),
                    'selected_class' => menu_selected('user/login'),
                ),
            );
        }


        // Sidebar snippet
        if (($this->appflow->hdata->sidebar_include_snippet = $this->options->get('js_sidebar')) === FALSE) {
            $this->appflow->hdata->sidebar_include_snippet = '';
        }

        // Footnote
        if (($this->appflow->hdata->footnote = $this->options->get('footnote')) === FALSE) {
            $this->appflow->hdata->footnote = '&copy; ' . date('Y') . ' <strong>' . $this->jb_sites->sitedata->title . '</strong><br><small>' . $this->jb_sites->sitedata->title . '</small>';
        }

        // Footer snippet include (configured via site options)
        $this->appflow->hdata->foot_snippet_include = _cfg('js_footer');


        // Detect mobile
        if ($this->mobile_detect->isMobile()) {
            $this->appflow->hdata->is_mobile = true;
        }



        // Header snippet include (configured via site options)
        $this->appflow->hdata->head_snippet_include = _cfg('js_header');

        // Search box defaults
        $this->appflow->hdata->search_mode = 'number';
        $this->appflow->hdata->search_string = '';
        $this->appflow->hdata->search_form_action_default = site_url('search/number');
        $this->appflow->hdata->search_form_action_number = site_url('search/number');
        $this->appflow->hdata->search_form_action_brand = site_url('search/brand');

        $cart_list = $this->cart->list_items();
        $this->appflow->hdata->cart_items = $cart_list['items'];

        // Admin auth
        $this->appflow->hdata->is_admin = $this->access->_is_admin();

        // Developer
        $this->appflow->hdata->jb_domain_devbrand = _cfg('jb_domain_devbrand');

        // Generate list of top-level itemcats to use on the left
        $this->appflow->hdata->custom_items = $this->terms->get('itemcats', '0', 'parent_id', 'objects_set');
        $this->appflow->hdata->num_custom_items = $this->terms->num_terms;

        foreach ($this->appflow->hdata->custom_items as $key => $item) {
            $item->selected_class = menu_selected('terms/itemcats/' . $item->id);
            $item->url = site_url('terms/itemcats/' . $item->id);
            $item->title_attr = htmlspecialchars($item->title);

            $this->appflow->hdata->custom_items[$key] = $item;
        }

    }

    // --------------------------------------------------------------------

    /**
     * Index
     *
     * This is a default method.
     *
     * @return    void
     */
    public function index()
    {
        $this->_brands();
    }

    // --------------------------------------------------------------------

    /**
     * Nice Permalink
     *
     * We catch all calls to autoparts/(:any)
     * and point them here. This function generates breadcrumb data and loads appropriate sub-method.
     * ./config/routes.php contains a rewrite rule for this.
     *
     * @access    public
     * @param    int $mfg
     * @param    bool|int $model
     * @param    bool|int $type
     * @param    bool|int $catid
     * @param    bool|string $stock
     * @return    void
     */
    public function nice_permalink($mfg, $model = FALSE, $type = FALSE, $catid = FALSE)
    {
        $this->appflow->q = (object) array
        (
            'mfg' => $mfg,
            'model' => $model,
            'type' => $type,
            'catid' => $catid,
        );

        if ($catid !== FALSE) {
            $this->_tree($mfg, $model, $type, $catid);
        } elseif ($type !== FALSE) {
            $this->_tree($mfg, $model, $type);
        } elseif ($model !== FALSE) {
            $this->_types($mfg, $model);
        } else {
            $this->_models($mfg);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Brands
     *
     * Vehicle Brands list
     *
     * @return    void
     */
    private function _brands()
    {
        // Get Brands
        $brands = $this->partsdb_api->brands();

        // Local Brands Filter
        $brands_order = $this->options->get('brands_order');

        // Prep brands with images & other data
        $brands_with_images = array();
        $brands_prepped = array();

        foreach ($brands->data as $brand) {

            if (empty($brands_order) || (!empty($brands_order) && in_array($brand->id, $brands_order))) {
                $brand->first_letter = substr($brand->name_clear, 0, 1);

                if ($brand->has_image) {
                    $brand->image_url = $this->partsdb_api->brand_image_url($brand);
                    $brands_with_images[$brand->id] = $brand;
                }

                $brands_prepped[$brand->id] = $brand;
            }

        }

        // Get Slides
        $slides = $this->posts->get('slide');

        // Are there any?
        if ($this->posts->num_posts > 0) {
            $this->appflow->cnt('front/slides', array
            (
                'slides' => $slides,
                'num_slides' => $this->posts->num_posts,
            ));
        }

        $this->appflow->cnt('front/brands', array
        (
            'brands' => $brands_prepped,
            'brands_with_images' => $brands_with_images,
            'brands_count' => count($brands->data),
            'brands_per_col' => ceil(count($brands->data) / 4 /* 4 is the number of columns */),
            'is_extcats_present' => $this->appflow->hdata->is_extcats_present,
        ));

        // Homepage text
        if (($homepage = $this->posts->get('page', FALSE, 'homepage', 'posts.permalink')) !== FALSE) {
            $stats = $this->local->prices_vndr_stats();

            $homepage->text = str_replace(array
            (
                '%in_stock%',
                '%min_delivery_days%',
                '%last_update%',
            ), array
            (
                $stats->qtys,
                $stats->delivery_days,
                date('d.m.Y', $stats->last_update),
            ), $homepage->text);

            $this->appflow->cnt('front/home_homepagetext', array('homepage' => $homepage));
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Genuine
     *
     * Generate page with custom and native genuine cats
     *
     * @return    void
     */
    public function genuine()
    {
        if (!defined('is_extcats_present')) {
            show_404();
        }

        $this->appflow->cnt('front/extcats_header');

        if (($extcats = $this->posts->get('extcat', FALSE)) !== FALSE) {
            $this->appflow->cnt('front/extcats_custom', array
            (
                'brands_txt' => $extcats,
            ));
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Models
     *
     * Shows the list of models for the requested Brand_ID
     *
     * @param $brand_id
     */
    private function _models($brand_id)
    {
        $models = $this->partsdb_api->find($brand_id);

        // Find minimal/maximum year
        $all_years		= array();

        foreach ($models->data as $model) {
            if (!empty($model->start_year) && !in_array($model->start_year, $all_years)) {
                $all_years[] = (int) $model->start_year;
            }
            if (!empty($model->end_year) && !in_array($model->end_year, $all_years)) {
                $all_years[] = (int) $model->end_year;
            }
        }

        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("{$models->dataname} - " . lang('jb_models_pgttl'));

        $this->appflow->cnt('front/models', array
        (
            'breadcrumbs' => $this->load->view('front/breadcrumbs', array('breadcrumb_array'=>$this->appflow->breadcrumbs($models->breadcrumbs)), TRUE),
            'models' => $models->data,
            'brand_name' => $models->dataname,
            'this_id' => $brand_id,
            'num_total' => count($models->data),

            'yearfilter' => $this->load->view('front/models_yearfilter', array
            (
                'year_min' => min($all_years),
                'year_max' => max($all_years),
            ), TRUE),

        ));

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Types
     *
     * Shows the list of types for the selected Model ID
     *
     * @param  $brand_id
     * @param $model_id
     */
    private function _types($brand_id, $model_id)
    {
        $types = $this->partsdb_api->find($brand_id, $model_id);

        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("{$types->dataname} - " . lang('jb_types_pgttl'));

        $this->appflow->cnt('front/types', array
        (
            'breadcrumbs' => $this->load->view('front/breadcrumbs', array('breadcrumb_array'=>$this->appflow->breadcrumbs($types->breadcrumbs)), TRUE),
            'types' => $types->data,
            'title' => $types->dataname,
            'brand_id' => $brand_id,
            'model_id' => $model_id,
        ));

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Tree
     *
     * Show the list of available autoparts groups for the reqested Type ID and Subcategory ID (optional)
     *
     * @param    int $mfa_id
     * @param    int $mod_id
     * @param    int $typ_id
     * @param    string|int $str_id
     * @return    void
     */
    private function _tree($brand_id, $model_id, $type_id, $category_id = NULL)
    {
        $parts_tree = $this->partsdb_api->find($brand_id, $model_id, $type_id, $category_id);

        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle($parts_tree->dataname);

        if ($parts_tree->datatype == 'tree') {
            $this->appflow->cnt('front/tree', array
            (
                'breadcrumbs' => $this->load->view('front/breadcrumbs', array('breadcrumb_array'=>$this->appflow->breadcrumbs($parts_tree->breadcrumbs)), TRUE),
                'title' => $parts_tree->dataname,
                'brand_id' => $brand_id,
                'model_id' => $model_id,
                'type_id' => $type_id,
                'category_id' => $category_id,
                'tree' => $parts_tree->data,

                'jbSM_ttl' => $parts_tree->dataname,
                'jbSM_lnk' => "find/{$this->appflow->q->mfg}/{$this->appflow->q->model}/{$this->appflow->q->type}",
            ));
        } elseif ($parts_tree->datatype == 'stock') {
            $stock = $this->stock->digest($parts_tree->data);

            // Set bodyclass-trigger, if we need to pull apis for data
            $this->appflow->hdata->bodyclass .= ($stock->is_require_apis) ? " require_apis api_list_combo_page api_list_thisnr" : "";

            // List vendor ids to be pulled
            $this->appflow->hdata->js_data .= ' data-vendors="' . $stock->api_list_imploded . '"';

            $this->appflow->cnt('front/stock_catlevel', array
            (
                'in_stock' => $this->load->view('front/stock_table', array
                (
                    'arts_in_stock' => $stock->in_stock,
                    'num_arts_in_stock' => $stock->num_in_stock,
                    'all_brands_are_similar' => $stock->all_brands_are_similar,
                    'discount' => $stock->discount,
                    'is_require_apis' => $stock->is_require_apis,
                ), TRUE),

                'not_in_stock' => $this->load->view('front/not_in_stock_table', array
                (
                    'arts_not_in_stock' => $stock->not_in_stock,
                    'num_not_in_stock' => $stock->num_not_in_stock
                ), TRUE),

                'title' => $parts_tree->dataname,

                'num_arts_in_stock' => $stock->num_in_stock, /// @todo Do we need this?

                'breadcrumbs' => $this->load->view('front/breadcrumbs', array('breadcrumb_array'=>$this->appflow->breadcrumbs($parts_tree->breadcrumbs)), TRUE),

            ));
        }

        $this->appflow->tmplt();
    }

    function part($brand, $number)
    {
        $part = $this->partsdb_api->part($brand, $number);

        // Get stock data
        if (!empty($part) && !empty($part->data)) {
            $stock = $this->stock->digest($part->data->parts, TRUE /* with crosses */, $part->data->part->data);
        } else {
            $primary_article = (object) array (
                'number' => $number,
                'number_clear' => $number,
                'brand' => $brand,
                'brand_clear' => $brand,
            );

            $stock = $this->stock->digest(array($primary_article), TRUE /* with crosses */, $primary_article);
        }

        // Set bodyclass-trigger, if we need to pull apis for data
        $this->appflow->hdata->bodyclass .= ($stock->is_require_apis) ? " require_apis api_list_article_page" : "";

        // List vendor ids to be pulled
        $this->appflow->hdata->js_data .= ' data-vendors="' . $stock->api_list_imploded . '" data-art_number_clear="' . $part->data->part->data->number_clear . '" data-sup_brand_clear="' . $part->data->part->data->brand_clear . '"';

        if (!empty($part) && !empty($part->data->part->id)) {
            $this->appflow->cnt('front/article_data_tabbed', array
            (
                'title' => $part->dataname,
                'art_id' => $part->data->part->id,

                'artinfo' => $this->load->view('front/article_data_infopane', array
                (
                    'title' => $part->dataname,
                    'characteristics' => $part->data->part->properties,
                    'pdfs' => $part->data->part->pdf,
                    'jpegs' => $part->data->part->jpg,
                    'art_data' => $part->data->part,
                ), TRUE),

                'in_stock' => $this->load->view('front/stock_table', array
                (
                    'arts_in_stock' => $stock->in_stock,
                    'num_arts_in_stock' => $stock->num_in_stock,
                    'all_brands_are_similar' => $stock->all_brands_are_similar,
                    'discount' => $stock->discount,
                    'primary_art_number' => $part->data->part->data->number_clear,
                    'is_require_apis' => $stock->is_require_apis,

                ), TRUE),

                'not_in_stock' => $this->load->view('front/not_in_stock_table', array
                (
                    'arts_not_in_stock' => $stock->not_in_stock,
                    'num_not_in_stock' => $stock->num_not_in_stock
                ), TRUE),

            ));
        } else {
            $this->appflow->cnt('front/article_data_p_tabbed', array
            (
                'title' => (!empty($part))?$part->dataname:"$brand/$number",

                'in_stock' => $this->load->view('front/stock_table', array
                (
                    'arts_in_stock' => $stock->in_stock,
                    'num_arts_in_stock' => $stock->num_in_stock,
                    'all_brands_are_similar' => $stock->all_brands_are_similar,
                    'discount' => $stock->discount,
                    'primary_art_number' => $part->data->part->data->number_clear,
                    'is_require_apis' => $stock->is_require_apis,
                ), TRUE),

                'not_in_stock' => $this->load->view('front/not_in_stock_table', array
                (
                    'arts_not_in_stock' => $stock->not_in_stock,
                    'num_not_in_stock' => $stock->num_not_in_stock
                ), TRUE),

            ));
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Api Pull
     *
     * Pulls the list of the configured apis for the number provided
     *
     * @param    string Aritcle number
     * @return    void
     */

    public function api_pull_ajax($artnr, $brand, $vendor_id, $mode = 'thisnr')
    {
        // Load Puller
        $this->load->model('apipuller_model', 'apipuller', TRUE);

        $pull_result = $this->apipuller->pull($artnr, $brand, $vendor_id, $mode);

        if (is_array($pull_result)) {
            $pull_result_json = json_encode($pull_result);
        } elseif (!empty($pull_result)) {
            $pull_result_json = $pull_result;
        } else {
            $pull_result_json = json_encode(array());
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output($pull_result_json);
    }

    public function search_inquiry()
    {
        // Form validation
        $this->load->library('form_validation');

        // Form validation rules
        $this->form_validation->set_rules('name', 'Имя', 'required|max_length[255]|xss_clean|strip_tags');
        $this->form_validation->set_rules('contact', 'Контакт', 'required|max_length[255]|xss_clean|strip_tags');
        $this->form_validation->set_rules('search', 'Поисковый номер', 'required|max_length[255]|xss_clean|strip_tags');
        $this->form_validation->set_rules('car', 'Автомобиль', 'max_length[255]|xss_clean|strip_tags');

        $d['with_header'] = TRUE;

        if ($this->form_validation->run() == FALSE) {
            $d['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));
            $this->appflow->cnt('front/search_inquiry', $d);
        } else {
            $this->mail_messaging->search_inquiry(_jb_sitedata()->adminemail, p('search'), p('name'), p('contact'), p('car'));
            $this->appflow->cnt('front/search_inquiry_success');
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Search
     *
     * This is a search wrapper.
     * It takes mode as an argument and makes a call to an appropriate method.
     *
     * @param    bool|string Search mode
     * @param    bool|string Only art_number, used to catch redirects from non-existent prices articles
     * @return    void
     */
    public function search($mode = FALSE, $search = FALSE)
    {
        if ($search or ($search = $this->input->post('search', TRUE)) !== FALSE) {
            if (!$mode) {
                $this->appflow->hdata->search_mode = $this->input->post('search_type', TRUE);
            } else {
                $this->appflow->hdata->search_mode = $mode;
            }

            $search_stripped = strip_tags($search);
            $this->appflow->stats_search_store($search_stripped);

            if ($this->appflow->hdata->search_mode == 'brand') {
                $this->_search_brand($search, $search_stripped);
            }
            else {
                $this->_search_number($search, $search_stripped);
            }
        } else {
            show_error('Поле поиска является пустым');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Search Number
     *
     * This performs a search on article number field in prices/stock table.
     *
     * @param    string $search
     * @param    string $search_stripped
     * @return    void
     */
    private function _search_number($search, $search_stripped)
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle($search_stripped);

        // Set form params
        $this->appflow->hdata->search_mode = 'number';
        $this->appflow->hdata->search_string = $search_stripped;
        $this->appflow->hdata->search_form_action_default = $this->appflow->hdata->search_form_action_number;

        $results = array();

        $clear_search_string = preg_replace('#\W#', '', (string)$search);

        if ($search and strlen($clear_search_string) > 0) {
            $results = $this->partsdb_api->search($clear_search_string);

            // grab crosses for this article
            $crosses = $this->local->crosses_joined_array($clear_search_string);
            $crosses[] = $clear_search_string;

            $prices_articles = array();

            if (($prices_rslt = $this->local->prices_v2($crosses)) !== FALSE and $prices_rslt->prices_result->num_rows() > 0) {
                $prices_articles = $this->_prices_search_results_utility($prices_rslt->prices_result, $results->data, $clear_search_string);
            }

            $results = array_merge($results->data, $prices_articles);

            if (count($results) == 0) {
                ///@info copied form models/stock
                // Pull vendors (required for `is_require_apis` to be set properly)
                $this->local->_vendors_array('all');

                // Used by JS module to query particular vendors. Empty by default.
                $vendor_apis_imploded = '';
                $vendor_api_ids = array();

                // Gives signal to pull apis. False by default.
                $is_require_apis = FALSE;

                // Do we have apis to be pulled?
                if (($num_vendor_apis = count($this->local->vendor_apis)) > 0) {
                    $is_require_apis = TRUE;

                    $x = 1;

                    foreach ($this->local->vendor_apis as $vendor_id => $api_params) {
                        $vendor_api_ids[] = $vendor_id;

                        $vendor_apis_imploded .= $vendor_id;

                        if ($x < $num_vendor_apis) {
                            $vendor_apis_imploded .= ' ';
                        }

                        $x++;
                    }
                }

                if ($is_require_apis) {
                    // Set bodyclass-trigger, if we need to pull apis for data
                    // api_list_article_page
                    // api_list_thisnr
                    $this->appflow->hdata->bodyclass .= " require_apis api_list_article_page";

                    // List vendor ids to be pulled
                    $this->appflow->hdata->js_data .= ' data-vendors="' . $vendor_apis_imploded . '" data-art_number_clear="' . $clear_search_string . '" data-sup_brand_clear="0"';
                }
            }

        }

        $this->appflow->cnt('front/search_head', array
        (
            'clear_search_string' => $search_stripped,
            'search_string' => $search_stripped,
            'mode' => 'number',
        ));

        $this->appflow->cnt('front/search_results', array
        (
            'results' => $results,
            'is_require_apis' => (!empty($is_require_apis)) ? true : false,
            'clear_search_string' => $clear_search_string,
            'search_string' => $search_stripped,
            'mode' => 'number',
            'more_btn_text' => lang('jb_search_tbl_btn'),
        ));

        if (!empty($is_require_apis)) {
            // Check authorisation ('cause we offer price discounts for some users)
            if ($this->access->_is_auth()) {
                $discount = $this->access->accdata->discount;
            } else {
                $discount = 0;
            }

            // We use stock's default view, so we put some vars to the default state
            $this->appflow->cnt('front/stock_table', array
            (
                'num_arts_in_stock' => 0,
                'is_require_apis' => true,
                'primary_art_number' => $clear_search_string,
                'arts_in_stock' => array(),
                'discount' => $discount,
            ));
        }

        if (empty($results)) {
            $this->appflow->cnt('front/search_inquiry', array
            (
                'search_string' => $search_stripped,
            ));
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Search Brand
     *
     * This performs a textual search on brand field in prices/stock table.
     *
     * @param    string $search
     * @param    string $search_stripped
     * @return    void
     */
    private function _search_brand($search, $search_stripped)
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle($search_stripped);

        // Set form params
        $this->appflow->hdata->search_mode = 'brand';
        $this->appflow->hdata->search_string = $search_stripped;
        $this->appflow->hdata->search_form_action_default = $this->appflow->hdata->search_form_action_brand;

        $results = array();

        if ($search) {
            $results = $this->_prices_search_results_utility_brandsearch($this->local->prices_by_brand($search)->prices_result);
        }

        $this->appflow->cnt('front/search_head', array
        (
            'clear_search_string' => $search_stripped,
            'search_string' => $search_stripped,
            'mode' => 'brand',
        ));

        $this->appflow->cnt('front/search_results_brandsearch', array
        (
            'results' => $results,
            'clear_search_string' => $search_stripped,
            'search_string' => $search_stripped,
            'mode' => 'brand',
        ));

        $this->appflow->tmplt();
    }


    // --------------------------------------------------------------------

    /**
     * Prices search result utility [DEBUG]
     *
     * This function takes a dbresult and returns an array of articles.
     *
     * @param    dbresult $prices_rslt
     * @param    array $results
     * @return    array
     */
    private function _prices_search_results_utility_brandsearch($prices_rslt)
    {
        $prices_articles = array();

        foreach ($prices_rslt->result() as $r) {

            $prices_articles[] = (object)array
            (
                'brand' => $r->sup_brand,
                'brand_clear' => $this->appflow->qprep($r->sup_brand, "sup_brand"),
                'number' => $r->art_number,
                'number_clear' => $this->appflow->qprep($r->art_number, "art_nr"),
                'name' => $r->description,
                'price_row_id' => $r->id,
                'is_prices_article' => TRUE,
                'is_number_matches' => TRUE,
            );
        }

        return $prices_articles;
    }

    // --------------------------------------------------------------------

    /**
     * Prices search result utility
     *
     * This function takes a dbresult and returns an array of articles.
     *
     * @param    dbresult $prices_rslt
     * @param    array $results
     * @param    bool|string $clear_search_string
     * @return    array
     */
    private function _prices_search_results_utility($prices_rslt, $results = array(), $clear_search_string = FALSE)
    {
        $prices_articles = array();

        foreach ($prices_rslt->result() as $r) {
            $key = $this->appflow->qprep($r->art_number_clear, 'art_nr') . $this->appflow->qprep($r->sup_brand, 'sup_brand');

            if (!isset($results[$key])) {
                $prices_aricle = (object)array
                (
                    'brand' => $r->sup_brand,
                    'number' => $r->art_number,
                    'number_clear' => $this->appflow->qprep($r->art_number, "art_nr"),
                    'brand_clear' => $this->appflow->qprep($r->sup_brand, "sup_brand"),
                    'name' => $r->description,
                    'price_row_id' => $r->id,
                    'is_prices_article' => TRUE,
                    'is_number_matches' => FALSE,
                );

                if ($clear_search_string and strtolower($clear_search_string) == strtolower($r->number_clear)) {
                    $prices_aricle->is_number_matches = TRUE;
                }

                $prices_articles[$key] = $prices_aricle;
            }
        }

        return $prices_articles;
    }

    // --------------------------------------------------------------------

    /**
     * Auto Compatibility Ajax
     *
     * Publishes the list of compatible autos for this article ID.
     *
     * @param    int $id
     * @return    void
     */
    public function auto_compatibility_ajax($id)
    {
        $part_c = $this->partsdb_api->part_compatibility($id);

        if (!empty($part_c)) {
            $this->load->view('front/compatibility', array
            (
                'autos' => $part_c->data,
            ));
        }

    }

    // --------------------------------------------------------------------

    /**
     * Terms
     *
     * Lists terms of specified type and the contents of selected term_id (child terms and posts).
     * The default level is 0.
     *
     * @param    string Term type
     * @param    string An id of the selected term
     * @return    void
     */
    public function terms($type, $term_id = FALSE)
    {
        if (($typedata = $this->terms->type($type)) !== FALSE) {
            $d['thistitle'] = $typedata->title;
            $d['typedata'] = $typedata;

            if ($term_id !== FALSE) {
                $d['breadcrumbs'] = $this->terms->breadcrumb($type, $term_id);
            } else {
                $d['breadcrumbs'] = array();
            }

            if ($term_id !== FALSE and ($this_term = $this->terms->get($type, $term_id)) !== FALSE) {
                $d['thistitle'] = $this_term->title;
                $d['terms'] = $this->terms->get($type, $term_id, 'parent_id', 'objects_set');
            } else {
                // In case it was not false and we found nothing in db
                $term_id = FALSE;

                // Retrieve top-level list of terms
                $d['terms'] = $this->terms->get($type, '0', 'parent_id', 'objects_set');
            }

            $d['num_terms'] = $this->terms->num_terms;

            // Generate appropriate title for use in <title></title>
            $this->appflow->hdata->pagetitle = $this->appflow->pagetitle($d['thistitle']);

            // Add bodyclass
            $this->appflow->hdata->bodyclass .= ' text-page';

            if ($term_id !== FALSE) {

                if ($typedata->posttypeid and ($post_typedata = $this->posts->type($typedata->posttypeid))) {
                    $d['post_typedata'] = $post_typedata;
                    $d['posts'] = $this->posts->get($typedata->posttypeid, false, $term_id, 'posts.term_id', 'objects_set');
                    $d['num_posts'] = $this->posts->num_posts;
                }
            }

            $this->appflow->cnt("posts/$type", $d);
            $this->appflow->tmplt();
        } else {
            show_jb_error('Invalid type');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Page
     *
     * Shows the page specified by permalink. This is a wrapper.
     *
     * @param    string $permalink
     * @return    void
     */
    public function page($permalink)
    {
        $this->post('page', $permalink);
    }

    // --------------------------------------------------------------------

    /**
     * Post
     *
     * Shows the post specified by $type and $permalink
     *
     * @param    string $type
     * @param    string $permalink
     * @return    void
     */
    public function post($type, $permalink)
    {
        if (($typedata = $this->posts->type($type)) !== FALSE) {
            if (($post = $this->posts->get($type, $typedata->termtypeid, $permalink, 'posts.permalink')) !== FALSE) {
                // Generate appropriate title for use in <title></title>
                $this->appflow->hdata->pagetitle = $this->appflow->pagetitle($post->title);

                // Add bodyclass
                $this->appflow->hdata->bodyclass .= ' text-page';

                if ($typedata->termtypeid and ($term_typedata = $this->terms->type($typedata->termtypeid)) !== FALSE) {
                    $post->term_typedata = $term_typedata;

                    if ($post->post_term_id != 0) {
                        $post->breadcrumbs = $this->terms->breadcrumb($typedata->termtypeid, $post->post_term_id);
                    } else {
                        $post->breadcrumbs = array();
                    }
                }

                $this->appflow->cnt("posts/$type", $post);

                if ($typedata->meta and $typedata->meta2 and $typedata->meta['processor'] == 'parse_art_nrs') {
                    $articles = array();

                    $number = $post->meta;

                    // In case, we've got 183718l1;13913;13913
                    if (strpos($number, ";") !== FALSE)
                        $number = strstr($number, ";", TRUE);

                    $brand = $post->meta2;

                    foreach ($post->meta_processed as $number_el) {
                        $articles[] = (object)array
                        (
                            'number' => $number_el,
                            'brand' => $brand,
                            'name' => $post->title,
                            'status' => '-',
                            'number_clear' => $this->appflow->qprep($number_el, "art_nr"),
                            'brand_clear' => $this->appflow->qprep($brand, "sup_brand"),
                        );
                    }

                    // Get stock data
                    $stock = $this->stock->digest($articles, TRUE, (object) array(
                        'number' => $number,
                        'number_clear' => $this->appflow->qprep($number, "art_nr"),
                        'brand' => $brand,
                        'brand_clear' => $this->appflow->qprep($brand, "sup_brand"),
                    ));

                    // Set bodyclass-trigger, if we need to pull apis for data
                    // Do we need that here?
                    $this->appflow->hdata->bodyclass .= ($stock->is_require_apis) ? " require_apis" : "";

                    // List vendor ids to be pulled
                    $this->appflow->hdata->js_data .= ' data-vendors="' . $stock->api_list_imploded . '"';

                    $this->appflow->cnt('front/article_data_p_tabbed', array
                    (
                        'title' => "",

                        'in_stock' => $this->load->view('front/stock_table', array
                        (
                            'arts_in_stock' => $stock->in_stock,
                            'num_arts_in_stock' => $stock->num_in_stock,
                            'all_brands_are_similar' => $stock->all_brands_are_similar,
                            'discount' => $stock->discount,
                            'primary_art_number' => $this->appflow->qprep($number, "art_nr"),
                            'is_require_apis' => $stock->is_require_apis,

                        ), TRUE),

                        'not_in_stock' => $this->load->view('front/not_in_stock_table', array
                        (
                            'arts_not_in_stock' => $stock->not_in_stock,
                            'num_not_in_stock' => $stock->num_not_in_stock
                        ), TRUE)

                    ));
                }

                $this->appflow->tmplt();
            } else {
                show_404("$type/$permalink");
            }
        } else {
            show_404("$type/$permalink");
        }
    }

    // --------------------------------------------------------------------

    /**
     * Cart Show
     *
     * Shows the contents of the cart
     *
     * @return    void
     */
    public function cart_show()
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("Корзина");

        // List cart items
        $cart_list = $this->cart->list_items();

        if (count($cart_list['items']) > 0) {
            // Delivery methods
            $delivery = $this->delivery->list_methods();
            $cart_list['d_mthds'] = $delivery->methods;

            // discount
            $cart_list['discount'] = 0;

            if ($this->access->_is_auth()) {
                $cart_list['discount'] = $this->access->accdata->discount;
                $cart_list['dm'] = (1 - $this->access->accdata->discount / 100);
                $cart_list['total'] = ($cart_list['total'] * $cart_list['dm']) + $delivery->current_price;
            } else {
                $cart_list['total'] += $delivery->current_price;
            }

            $this->appflow->cnt('shop/cart_list', $cart_list);

            if ($this->access->_is_auth()) {
                $this->appflow->cnt('shop/order_form', array
                (
                    'order_full_title' => 'Проверьте ваши данные',
                    'form' => $this->users->userdata_one_level_array($this->access->accdata),
                    'no_edit_mode' => TRUE,
                    'email_no_edit_mode' => TRUE,
                ));
            } else {
                $this->appflow->cnt('shop/order_forms_doubled', array
                (
                    'new' => $this->load->view('shop/order_form', array
                    (
                        'order_full_title' => 'Заказ для новых клиентов',

                    ), TRUE),

                    'auth' => $this->load->view('shop/auth_form', array
                    (
                        'formaction' => 'user/login/cart#order_form_anchor',

                    ), TRUE),
                ));
            }
        } else {
            $this->appflow->cnt('shop/cart_is_empty');
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Cart Add
     *
     * Adds item specified by prices_line_id to the cart.
     * An optional ajax trigger controls the when-finished-behaviour: whether to redirect back to referrer, or to output JSON.
     *
     * @param    int $prices_line_id
     * @param    bool|string $ajax
     * @return    void
     */
    public function cart_add($prices_line_id = FALSE, $ajax = FALSE)
    {
        $this->load->model('apipuller_model','apipuller',TRUE);

        if (!empty($prices_line_id) && ($item = $this->local->prices_row($prices_line_id, 'object_extended')) !== FALSE) {
            $this->cart->add_or_update($item, TRUE);

            $result['code'] = 1;
            $result['id'] = $prices_line_id;
            $result['items'] = 1; // We don't use it
        } elseif (($item = $this->input->post('item')) !== FALSE and is_array($item) !== FALSE and $this->apipuller->validate((object)$item)) {
            $this->cart->add_or_update((object)$item, TRUE);

            $result['code'] = 1;
            $result['items'] = 1; // We don't use it

            if (isset($item['hash']))
                $result['hash'] = $item['hash'];
        } else {
            $result['code'] = 0;
            $result['items'] = 0; // We don't use it
        }

        if ($ajax) {
            $array = array();

            $cart_list = $this->cart->list_items();

            foreach ($cart_list['items'] as $i) {
                $array[] = (object)array
                (
                    'art_number' => $i->art_number,
                    'sup_brand' => $i->sup_brand,
                    'description' => $i->description,
                    'qty' => $i->qty,
                    'subtotal_formatted' => $i->subtotal_formatted,
                );
            }

            $result['_meta_cart_list'] = $array;

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result));
        } elseif ($this->input->server('HTTP_REFERER') and strpos($this->input->server('HTTP_REFERER'), str_replace('www.', '', $this->input->server('SERVER_NAME'))) !== FALSE) {
            redirect($this->input->server('HTTP_REFERER'));
        } else {
            redirect('');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Update Cart
     *
     * Takes Post input with cart item quantities and performs an update.
     *
     * @return    void
     */
    public function cart_update()
    {
        $qtys = p('qty');
        $delivery = p('delivery');

        if ($qtys and is_array($qtys)) {
            foreach ($qtys as $item_id => $qty) {
                $this->cart->add_or_update($item_id, $qty);
            }
        }

        if ($delivery and $this->delivery->is_delivery($delivery)) {
            $this->session->set_userdata('delivery', $delivery);
        }

        redirect('cart');
    }

    // --------------------------------------------------------------------

    /**
     * Order Full Form
     *
     * Normal behaviour - when an order is made by customer.
     * Shows the order form and processes cart data together with user input upon form submission.
     * Redirects to order information page on success.
     *
     * @return    void
     */
    public function order_make()
    {
        $this->load->library('form_validation');

        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("Оформление заказа");

        // List cart items
        $cart_list = $this->cart->list_items();

        if (count($cart_list['items']) > 0) {
            $this->form_validation->set_rules('order_comment', '', 'xss_clean|strip_tags');

            $this->form_validation->set_rules('name', 'ФИО', 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('address', 'Адрес доставки', 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('phone', 'Телефон', 'required|xss_clean|strip_tags');
            //$this->form_validation->set_rules('skype', 'Skype', '');
            //$this->form_validation->set_rules('icq', 'ICQ', '');
            //$this->form_validation->set_rules('other_contact', 'Другой способ связи', '');

            // Is user authorized?
            if ($this->access->_is_auth()) {
                $order_form_data = array
                (
                    'order_full_title' => 'Редактирование данных',
                    'is_single_page_form' => TRUE,
                    'no_edit_mode' => FALSE,
                    'email_no_edit_mode' => TRUE,

                    'form' => $this->users->userdata_one_level_array($this->access->accdata),
                );
            } // User isn't authorized
            else {
                $order_form_data = array
                (
                    'order_full_title' => 'Заказ для новых клиентов',
                    'is_single_page_form' => TRUE,
                );

                $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback__clb_email_unique');
            }

            if ($this->form_validation->run() == FALSE) {
                $order_form_data['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));

                $this->appflow->cnt('shop/order_form', $order_form_data);

                $this->appflow->tmplt();
            } else {
                $order = $cart_list;

                // Delivery methods
                $delivery = $this->delivery->list_methods();
                $order['delivery'] = $delivery->current;

                // Existent user
                if ($this->access->_is_auth()) {
                    // Prep empty user var
                    $user = (object)array();

                    // Update user with data submitted
                    $user_id = $this->users->user_update($this->access->accdata->email, p(array('name', 'phone', 'address'), TRUE));

                    // Email
                    $user->email = $this->access->accdata->email;

                    // Discount
                    $order['discount'] = $this->access->accdata->discount;
                } // New user
                else {
                    $user = $this->users->add(p('email'), p(array('name', 'phone', 'address'), TRUE));
                    $user_id = $user->id;
                    $order['discount'] = 0;

                    $this->mail_messaging->registration($user->email, $user->password);
                }

                $order['order_comment'] = p('order_comment');

                // Make an order
                $neworder = $this->order->make($user_id, $order);

                // Send messages
                $this->mail_messaging->order_finished($user->email, $neworder->vericode, $neworder->human_id, $order['items']); // Order finished E-mail for the customer
                $this->mail_messaging->new_order(_jb_sitedata()->adminemail, $order['items']); // Order E-mail for the siteadmin

                redirect('order/' . $neworder->vericode);
            }
        } else {
            redirect('cart');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Order
     *
     * Shows order contents together with order status for an order specified by vericode.
     * Vericode is believed to be unique for each order.
     *
     * @param    string $vericode
     * @return    void
     */
    public function order_get($vericode)
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("Информация о заказе");

        // Posts model is used to get page with payment details
        $this->load->model('posts', '', TRUE);

        // Get data for the order matching vericode provided
        $order_data = $this->order->get_all($vericode, 'orders.vericode');

        // If there's something
        if (count($order_data) > 0) {
            // Load currency model
            $this->load->model('currency');

            // Get site's default currency
            $currency = $this->currency->get();

            // Prep currency for publishing
            $d['currency_symbol'] = $currency->symbol;

            // Prep data for publishing
            $d['order'] = $order_data;
            $d['order_statuses'] = $this->order->statuses;

            // Not all the statuses require us to show payment details
            if ($this->order->show_payment_details) {
                // $this->order->amount_unpaid = $this->currency->price_prep($this->order->amount_unpaid);

                // walk-thru payopts
                $r_from = array
                (
                    '%order_number%',
                    '%order_number_raw%',
                    '%order_total%',
                    '%order_total_raw%',
                    '%order_total_nodelivery%',
                    '%order_total_nodelivery_raw%',
                    '%order_delivery%',
                    '%order_delivery_raw%',
                    '%email%',
                    '%customer_id%',
                    '%customer_name%',
                    '%customer_address%',
                    '%success_url%',
                    '%fail_url%'
                );

                $r_to = array
                (
                    '№' . $order_data[0]->order_human_id,
                    $order_data[0]->order_human_id,
                    '<strong>' . price_format($this->order->amount_unpaid + $order_data[0]->dmthd_price) . '</strong> (с учетом доставки ' . $order_data[0]->dmthd_price . ' ' . $currency->symbol . ')',
                    $this->order->amount_unpaid + $order_data[0]->dmthd_price,
                    price_format($this->order->amount_unpaid),
                    $this->order->amount_unpaid,
                    price_format($order_data[0]->dmthd_price),
                    $order_data[0]->dmthd_price,
                    $order_data[0]->email,
                    $order_data[0]->user_id,
                    $order_data[0]->userdata->name,
                    $order_data[0]->userdata->address,
                    site_url('payment/success'),
                    site_url('payment/fail/' . $order_data[0]->vericode)
                );

                $d['payopts'] = str_replace($r_from, $r_to, $this->posts->get('page', false, 'payment', 'posts.permalink')->text);
            }

            $this->appflow->cnt('shop/order_status', $d);
            $this->appflow->cnt('shop/order_items_list', $d);
            $this->appflow->cnt('shop/order_statuses', $d);
        } else {
            $this->appflow->cnt('shop/order_not_found');
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * User Orders
     *
     * Lists all orders for an authorised user.
     *
     * @todo    check if $slctd var is used
     *
     * @return    void
     */
    public function orders_list()
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("История заказов");

        if ($this->access->_is_auth()) {
            $d['email'] = $this->access->accdata->email;
            $d['order'] = $this->order->get_all($this->access->accdata->id, 'orders.user_id');
            $d['order_statuses'] = $this->order->statuses;

            $this->appflow->cnt('shop/orders_list', $d);
            $this->appflow->tmplt();
        } else {
            redirect('user/login/' . bcklnk_mask('user/orders'));
        }
    }

    // --------------------------------------------------------------------

    /**
     * Payment
     *
     * The Success/Failure landing page after payment processing.
     * Payment processing is done via 3rd party service, which redirects users here.
     *
     * This is a user-shop-level function (used when somebody buys something from the shop).
     *
     * @param    string $result
     * @param    int|string $vericode
     * @return    void
     */
    public function payment_result($result = 'fail', $vericode = FALSE)
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("Статус платежа");

        $slctd = 'show_cart';

        if ($result == 'success') {
            $this->appflow->cnt('shop/payment_success');
        } else {
            $this->appflow->cnt('shop/payment_fail', array('vericode' => $vericode));
        }

        $this->appflow->tmplt();
    }

    // --------------------------------------------------------------------

    /**
     * Register User
     *
     * Function to register user without making an order
     *
     * @return    void
     */

    public function user_register()
    {
        if ($this->access->_is_auth()) {
            redirect('user/orders');
        } else {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback__clb_email_unique');
            $this->form_validation->set_rules('password', 'Пароль', 'required|min_length[6]|max_length[32]');
            $this->form_validation->set_rules('password2', 'Пароль еще раз', 'required|matches[password]');
            $this->form_validation->set_rules('name', 'Имя и фамилия', 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('address', 'Адрес доставки', 'required|xss_clean|strip_tags');
            $this->form_validation->set_rules('phone', 'Телефон', 'required|xss_clean|strip_tags');

            if ($this->form_validation->run() == FALSE) {
                $order_form_data['form_type'] = 'register';
                $order_form_data['form_action'] = 'user/register';
                $order_form_data['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));

                $this->appflow->cnt('shop/user_register_head');
                $this->appflow->cnt('shop/order_form_form', $order_form_data);
            } else {
                $user = $this->users->add(p('email'), p(array('name', 'phone', 'address', 'password'), TRUE));
                $this->mail_messaging->registration($user->email, $user->password);

                $this->appflow->cnt('shop/user_register_success', array
                (
                    'email' => $user->email,
                    'password' => $user->password,
                ));
            }

            $this->appflow->tmplt();
        }
    }

    public function user_change_password()
    {
        if ($this->access->_is_auth()) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('password', 'Текущий пароль', 'required|callback__clb_user_pass_valid');
            $this->form_validation->set_rules('new_password', 'Новый пароль', 'required|max_length[32]|min_length[6]');
            $this->form_validation->set_rules('new_password2', 'Новый пароль (еще раз)', 'required|matches[new_password]');

            if ($this->form_validation->run() == FALSE) {
                $d['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));

                $this->appflow->cnt('shop/user_change_pass', $d);
            } else {
                $new_password = p('new_password');

                $this->users->update_password($this->access->accdata->id, $new_password);
                $this->session->set_userdata('password', $new_password);

                $d['success'] = '<div class="alert alert-success"><i class="icon-ok-sign"></i> <strong>Успешно</strong>! Пароль изменен.</div>';

                $this->appflow->cnt('shop/user_change_pass', $d);
            }

            $this->appflow->tmplt();
        } else {
            redirect('user/login/' . bcklnk_mask('user/change_pass'));
        }
    }

    // --------------------------------------------------------------------

    /**
     * Login
     *
     * Login page with optional backlink
     *
     * @param    string $bcklnk
     * @return    void
     */
    public function user_login($bcklnk = FALSE)
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("Вход");

        // Check, if authorisation already succedeed
        if ($this->access->_is_auth()) {
            // Conditional redirect
            $this->_bcklnk_redirect($bcklnk, 'user/login');
        }

        // Form validation
        $this->load->library('form_validation');

        // Validation Rules
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback__clb_auth');
        $this->form_validation->set_rules('password', 'Пароль', 'required');

        // If nothing was submitted, or validation failed
        if ($this->form_validation->run() === FALSE) {
            // Form
            $this->appflow->cnt('shop/auth_form', array
            (
                // This will throw an empty string in case nothing was submitted
                'errors' => validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after')),

                // Justlogin trigger
                'justlogin' => TRUE,

                // Form Action
                'formaction' => 'user/login/' . $bcklnk,

            ));

            // Footer
            $this->appflow->tmplt();
        } // If validation successful
        else {
            // Conditional redirect
            $this->_bcklnk_redirect($bcklnk, 'user/login');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Logoff
     *
     * Destroys session variables and redirects to the place of request
     *
     * @param    string $bcklnk
     * @return    void
     */
    public function user_logout($bcklnk = '')
    {
        // Destroy email and password vars in session
        // We don't destroy the whole session, because something else may be stored there
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('password');

        // Conditional redirect
        $this->_bcklnk_redirect($bcklnk, 'user/logout');
    }

    // --------------------------------------------------------------------

    /**
     * Password Remind
     *
     * Password remind form with optional email param
     *
     * @param    string $email
     * @return    void
     */
    public function user_pass_remind($email = FALSE)
    {
        // Generate appropriate title for use in <title></title>
        $this->appflow->hdata->pagetitle = $this->appflow->pagetitle("Напоминание пароля");

        // These models are necessary only here
        // So, we don't load them in the __construct
        $this->load->model('order', '', TRUE);
        $this->load->model('users', '', TRUE);

        // Form validation (neccessary only in certain places)
        $this->load->library('form_validation');

        // If e-mail has been supplied via URL
        if ($email) {
            // We unmask it
            $form['form']['email'] = str_replace(array('::', ':'), array('.', '@'), $email);
        }

        // Validation Rules
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback__clb_email_exists');

        // If nothing was submitted, or validation failed
        if ($this->form_validation->run() == FALSE) {
            // This will throw an empty string in case nothing was submitted
            $form['errors'] = validation_errors($this->config->item('form_vld_err_before'), $this->config->item('form_vld_err_after'));

            // Form
            $this->appflow->cnt('shop/remind_pass', $form);
        } // If validation successful
        else {
            // Get e-mail from POST
            $email = p('email');

            // Get user by email
            $user = $this->users->get($email, 'email');

            // Send an after-registration message
            $this->mail_messaging->registration($email, $user->password);

            // Remind ok
            $this->appflow->cnt('shop/remind_ok', array('email' => $email));
        }

        // Footer
        $this->appflow->tmplt();
    }



    // --------------------------------------------------------------------

    /**
     * Backlink redirect
     *
     * This function makes a conditional redirect which protects from Loop Redirects
     *
     * @param    string $bcklnk
     * @param    string $thislink
     * @return    void
     */
    private function _bcklnk_redirect($bcklnk, $thislink)
    {
        // We mask backlinks to be safely used in urls.
        // This unmasks
        $bcklnk = bcklnk_unmask($bcklnk);

        // It can happen, that something wrong was supplied as backlink
        if (!$bcklnk) {
            // So, we redirect to default place
            redirect('user/orders');
        } // Backlink is ok. And we are not already there.
        elseif ($bcklnk and $bcklnk != $thislink) {
            redirect($bcklnk);
        } // All other cases - home
        else {
            redirect('');
        }
    }

    // --------------------------------------------------------------------

    /**
     * CALLBACK Email unique
     *
     * Checks if email doesn't already exists in our system.
     *
     * @todo This is a duplicate function, which already exists in Controllers:Admin. Migrate this to a separate model.
     *
     * @param    string $email
     * @return    void
     */
    public function _clb_email_unique($email)
    {
        $this->form_validation->set_message('_clb_email_unique', '<strong>E-mail уже зарегистрирован.</strong> Вам необходимо <a href="/user/login">авторизоваться</a>, а если вы забыли пароль, то <a href="' . site_url('user/remind-pass/') . '/' . str_replace(array('@', '.'), array(':', '::'), $email) . '">можно его вспомнить</a>.');

        if ($this->users->is_email_unique($email)) {
            return TRUE;
        }

        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * CALLBACK Email Exists
     *
     * Check if email already exists
     *
     * @param    string $email
     * @return    bool
     */
    public function _clb_email_exists($email)
    {
        // Validation error message setter
        $this->form_validation->set_message('_clb_email_exists', '<strong>Ошибка.</strong> Такой e-mail, увы, не зарегистрирован.');

        // Check if email is in DB
        if ($this->users->is_email_unique($email)) {
            return FALSE;
        }

        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * CALLBACK Authrisation
     *
     * Processes authorisation routine matching DB entry against username and password
     *
     * @param    string $email
     * @return    bool
     */
    public function _clb_auth($email)
    {
        // Validation error message setter
        $this->form_validation->set_message('_clb_auth', 'Неверный e-mail/пароль. Попробуйте еще раз или воспользуйтесь <a href="' . site_url('user/remind-pass') . '">напоминанием</a>.');

        // Retrieve password from POST
        $pass = $this->input->post('password');

        // Match DB entry against username and password
        if ($this->access->auth($email, $pass)) {
            // Store session vars
            $this->session->set_userdata('email', $email);
            $this->session->set_userdata('password', $pass);

            return TRUE;
        }

        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * CALLBACK Match Password
     *
     * Processes authorisation routine matching DB entry against username and password
     *
     * @param    string $email
     * @return    bool
     */
    public function _clb_user_pass_valid($password)
    {
        // Validation error message setter
        $this->form_validation->set_message('_clb_user_pass_valid', 'Вы указали неверный пароль.');

        // Retrieve email from session
        $email = $this->access->accdata->email;

        // Match DB entry against username and password
        if ($this->access->auth($email, $password)) {
            return TRUE;
        }

        return FALSE;
    }
}

/* End of file auto.php */
/* Location: ./application/controllers/auto.php */