<?php

class Pdb_api_model extends CI_Model {
    var $protocol = 'http';
    var $host;

    public function __construct()
    {
        parent::__construct();

        $this->host = $this->config->item('jb_apihost');

        if (empty($this->host)) {
            show_jb_error('Требуется API-аккаунт', 'Активируйте аккаунт на <a href="https://partsdb.info/page/api-docs" target="_blank">PartsDB.info</a> и укажите ссылку на него в файле <code>./application/config/jb.php</code>');
        }
    }

    public function brands () {
        return $this->query("brands");
    }

    public function brand_image_url ($brand) {
        return $this->protocol . "://" . $this->host . "/e/images/" . $brand->name_clear . ".png";
    }

    public function find ($brand_id, $model_id = NULL, $type_id = NULL, $category_id = NULL) {
        $q = array (
            $brand_id,
            $model_id,
            $type_id,
            $category_id,
        );

        $qf = array_filter($q, function ($val) {
            if (!empty($val)) {
                return TRUE;
            }

            return FALSE;
        });

        return $this->query('find', implode("/", $qf));
    }

    public function search ($number) {
        return $this->query("search", $number);
    }

    public function part_compatibility ($id) {
        return $this->query("part_compatibility", $id);
    }

    public function part ($brand, $number) {
        return $this->query("part", "$brand/$number");
    }

    public function query ($fn, $path = '') {
        if (strpos($path, "/") !== 0)
            $path = "/" . $path;

        $url = $this->protocol . "://" . $this->host . "/api/v2/" . $fn . $path;
        $r = file_get_contents($url);

        if ($r !== false) {
            return json_decode($r);
        }

        return NULL;
    }
}