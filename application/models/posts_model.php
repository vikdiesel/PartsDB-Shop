<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Posts Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Posts_model extends CI_Model {
	
	/// Number of posts retrieved in the last call
	var $num_posts;
	
	/// The last typeid requested via ::type - working type. Used to access via callback.
	var $thistypeid;
	
	/// Registered post types
	var $types;
	
	/**
	 * Constructor
	 */
	function __construct()
    {
        parent::__construct();
		
		$this->types = array
		(
			'page'	=> (object) array
			(
				'labels'	=> array ('Страницы', 'Управление вашими страницами', 'Новая страница', 'Редактирование страницы'),
				'params'	=> array
				(
					'termtypeid'	=> false,
					'thumbnail'		=> false, 
					'meta'			=> array
					(
						'Показать в меню?',
						'',
						
						'validation'	=> 'required|greater_than[-1]|less_than[2]',
						'processor'		=> false,
						
						'options'		=> array
						(
							'Не показывать',
							'Показать',
						),
						
						'disable_for'	=> array('payment', 'homepage'),
					),
					
					// Show piece of advice on `post edit` screen for a post with specified `permalink`.
					// The advice should be placed in views/admin/post_advice_{pemalink}.php
					'editor_advice'	=> array('payment', 'homepage'),
					
					'admin_posts_list_advice'	=> false,
				),
			),
			
			'item'	=> (object) array
			(
				'labels'	=> array ('Товары', 'Управление вашими товарами', 'Новый товар', 'Редактирование товара'),
				'params'	=> array 
				(
					'termtypeid'				=> 'itemcats', 
					'thumbnail'					=> false, 
					
					'meta'						=> array
					(
						'Артикульный номер',
						'<strong>Обязательно.</strong> По артикульному номеру будет производиться сопоставление с данными в прайсе.<!--<br>Можно указать <i>несколько через запятую или точку с запятой</i>.-->',
						
						'validation'	=> 'required|callback__clb_parse_article_nrs|xss_clean|strip_tags', 
						'processor'		=> 'parse_art_nrs'
					),
					
					'meta2'						=> array
					(
						'Бренд',
						'<strong>Желательно.</strong> По бренду будет производиться сопоставление с данными в прайсе.<br>Бренд стоит указать, т.к. у некоторых производителей артикульные номера совпадают.',
						
						'validation'	=> 'max_length[50]|xss_clean|strip_tags', 
						
						// Meta2 is processed together with meta.
						'processor'		=> false,
					),
					'editor_advice'				=> false,
					'admin_posts_list_advice'	=> true,
				),
			),
			
			'slide'	=> (object) array
			(
				'labels'	=> array ('Слайды на главной', 'Управление вашими слайдами', 'Новый слайд', 'Редактирование слайда'),
				'params'	=> array
				(
					'termtypeid'				=> false,
					'thumbnail'					=> array('Изображение', '<strong>Обязательно.</strong> Изображение <i>JPG</i> или <i>PNG</i>; <i>900&times;350 px</i>; <i>50-250 кб</i>.', 'is_required'=>TRUE, 'width'=>'900', 'height'=>'350'), 
					'no_editor'					=> true,
					'editor_advice'				=> false,
					'admin_posts_list_advice'	=> false,
					
					'meta'						=> array
					(
						'Ссылка',
						'Ссылка куда будет осуществлен переход по клику на слайд.<br>Пример: <i>http://example.com/page/</i> или <i>/page/</i>',
						
						'validation'	=> 'xss_clean|strip_tags', 
						'processor'		=> false,
						'field-css-class'	=> 'input-xlarge',
						'placeholder'		=> '(необязательно)',
					),
				),
			),
			
			'extcat'	=> (object) array
			(
				'labels'	=> array ('Сторонние каталоги', 'Подключение сторонних каталогов', 'Добавить каталог', 'Редактирование'),
				'params'	=> array
				(
					'termtypeid'				=> false,
					// 'thumbnail'					=> array('Изображение', '<strong>Обязательно.</strong> Изображение <i>JPG</i> или <i>PNG</i>; <i>900&times;350 px</i>; <i>50-250 кб</i>.', 'is_required'=>TRUE, 'width'=>'900', 'height'=>'350'), 
					'no_editor'					=> true,
					'editor_advice'				=> false,
					'admin_posts_list_advice'	=> false,
					
					'title_field_name'			=> 'Название бренда',
					'title_field_desc'			=> '<strong>Обязательно.</strong> Указывайте латинскими буквами и без орфографических ошибок.<br>Правильное написание брендов можно посмотреть <a href="/admin/options_mfgs" target="_blank" title="Откроется в новом окне">здесь</a>.',
					
					'meta'						=> array
					(
						'Ссылка',
						'<strong>Обязательно.</strong> Ссылку на каталог предоставляет поставщик.<br><i>Пример: http://catalog.autodoc.ru/bmw/</i>',
						
						'validation'	=> 'required|callback__clb_valid_url|xss_clean|strip_tags', 
						'processor'		=> false,
						'field-css-class'	=> 'input-xlarge',
					),
					
					'admin_posts_list_advice'	=> true,
				),
			),
		);
    }
	
	// --------------------------------------------------------------------
	
	/**
	 * Type
	 *
	 * 
	 *
	 * @param	string|bool $select
	 * @param	bool $menu
	 * @return	dbresult
	 */	
	public function type ($typeid)
	{
		if (isset($this->types[$typeid]))
		{
			$typedata = (object) array
			(
				'id'			=> $typeid,
				
				'title'			=> $this->types[$typeid]->labels[0],
				'description'	=> $this->types[$typeid]->labels[1],
				'new_item'		=> $this->types[$typeid]->labels[2],
				'edit_item'		=> $this->types[$typeid]->labels[3],
			);
			
			foreach ($this->types[$typeid]->params as $k=>$v)
			{
				$typedata->$k = $v;
			}
			
			$this->thistypeid = $typeid;
			
			return $typedata;
		}
		
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Pages Result
	 *
	 * Retrieves page list (either all or marked for publishing in menu)
	 * The list of fields retrieved can be controlled via $select
	 *
	 * @param	string|bool $select
	 * @param	bool $menu
	 * @return	dbresult
	 */

	
	public function get($type, $termstypeid=false, $id=false, $by='posts.id', $return = 'objects')
	{		
		if ($id !== FALSE)
		{
			$this->db->where ($by, $id);
		}
		
		$this->db->where('post_type', $type);

		if ($termstypeid)
		{
			$this->db->select('
				posts.id,
				posts.title,
				posts.permalink,
				posts.thumbnail,
				posts.text,
				posts.meta,
				posts.meta2,
				posts.last_update,
				posts.allow_delete,
				posts.menu_order,
				post_terms.id as post_term_id,
				post_terms.title as post_term_title,
				post_terms.permalink as post_term_permalink,
				post_terms.image as post_term_image
			');
			
			$this->db->join('post_terms', 'posts.term_id=post_terms.id', 'left');
		}
		
		$this->db->order_by('menu_order','ASC');
		
		if ($return == 'count')
		{
			$this->num_posts = $this->db->count_all_results('posts');
			
			return $this->num_posts;
		}
		else
		{
			$q = $this->db->get('posts');
			
			$this->num_posts = $q->num_rows();

			if ($this->num_posts == 0 and $return != 'objects_set')
			{
				return FALSE;
			}
			elseif ($this->num_posts == 1 and $id and $return != 'objects_set')
			{
				$r = $q->row();
				return $this->_parse($type, $r);
			}
			else
			{
				$array = array();
				
				foreach ($q->result() as $r)
				{
					$array[] = $this->_parse($type, $r);
				}
				
				return $array;
			}
		}
	}
	
	private function _parse($type, $r)
	{
		$typedata = $this->type($type);
		
		$r->title_prepped = htmlspecialchars($r->title);
		
		if ($typedata and $typedata->meta)
		{
			$r->meta_prepped = htmlspecialchars($r->meta);
			
			if (strlen($r->meta_prepped) > 50)
			{
				$r->meta_prepped = substr($r->meta_prepped, 0, 50) . '&hellip;';
			}
			
			$r->meta_processed = $this->_meta($typedata->meta['processor'], $r->meta);
		}
		
		if ($typedata and $typedata->meta2)
		{
			$r->meta2_prepped = htmlspecialchars($r->meta2);
			$r->meta2_processed = $this->_meta($typedata->meta2['processor'], $r->meta2);
			
			if (strlen($r->meta2_prepped) > 50)
			{
				$r->meta2_prepped = substr($r->meta2_prepped, 0, 50) . '&hellip;';
			}
		}
		
		return $r;
	}
	
	private function _meta($processor, $meta)
	{
		if ($processor == 'parse_art_nrs')
		{
			$nrs = array_filter(explode(';', $meta), 'mktt_is_empty');
			return $nrs;
		}
		else
		{
			return $meta;
		}
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Add Page
	 *
	 * Insert page to DB with unique permalink. We expect data supplied to be validated.
	 *
	 * @param	array $ins
	 * @return	void
	 */	
	function add($type, $data)
	{		
		$processed = array
		(
			'permalink'		=> $this->appflow->unique_permalink(permalink($data['title']), 'posts'),
			'last_update'	=> time(),
			'allow_delete'	=> 1,
			'post_type'		=> $type,
		);
		
		// The order of merge vars is important, because the later value overwrite earlier.
		$this->db->insert('posts', array_merge($data, $processed));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Update
	 *
	 * Updates page specified by permalink (since permalink is unique).
	 *
	 * @param	string $current_permalink
	 * @param	array $vars
	 * @return	void
	 */	
	public function update($type, $id, $data, $by='id')
	{
		$data['last_update'] = time();
		
		$this->db->where('post_type', $type);
		$this->db->where($by, $id);

		$this->db->limit(1);
		
		$this->db->update('posts', $data);
		
		return $this->db->affected_rows();
	}
	
	public function delete($type, $id, $by='id')
	{
		$this->db->where('post_type', $type);
		$this->db->where($by, $id);

		$this->db->where('allow_delete', 1);
		
		$this->db->limit(1);
		
		$this->db->delete('posts');
		
		return $this->db->affected_rows();
	}
}

/* End of file posts_model.php */
/* Location: ./application/models/posts_model.php */