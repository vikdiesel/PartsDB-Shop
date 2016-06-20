<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Terms Model
 *
 * @package		2find
 * @subpackage	Models
 * @author		Viktor Kuzhelnyi @marketto.ru
 */
class Terms_model extends CI_Model {
	
	/// Number of terms retrieved in the last call
	var $num_terms;
	
	/// The last typeid requested via ::type working type. Used to access via callback.
	var $thistypeid;
	
	/// Registered term types
	var $types = array
	(
		'itemcats'	=> array
		(
			'labels'	=> array ('Категории товаров', 'Управление вашими категориями товаров', 'Новая категория', '', 'Категория', '<strong>Желательно.</strong> Принадлежность к категории. Товар без категории найти можно будет только по номеру.<br><i class="icon-info-sign"></i> Категории можно добавить/удалить в разделе <a href="/admin/terms/itemcats" target="_blank" title="Откроется в новом окне/вкладке">Категории товаров</a>.'),
			'params'	=> array ('posttypeid'	=> 'item'),
		),
	);
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	public function type ($typeid)
	{
		if (isset($this->types[$typeid]))
		{
			$typedata = (object) array
			(
				'id'			=> $typeid,
				
				'title'			=> $this->types[$typeid]['labels'][0],
				'description'	=> $this->types[$typeid]['labels'][1],
				'new_item'		=> $this->types[$typeid]['labels'][2],
				
				'post_formlabel'		=> $this->types[$typeid]['labels'][4],
				'post_formcomment'		=> $this->types[$typeid]['labels'][5],
			);
			
			foreach ($this->types[$typeid]['params'] as $k=>$v)
			{
				$typedata->$k = $v;
			}
			
			$this->thistypeid = $typeid;
			
			return $typedata;
		}
		
		return FALSE;
	}
	
	public function get($type, $id=false, $by='id', $return = 'objects')
	{
		if ($id !== FALSE)
		{
			$this->db->where ($by, $id);
		}
		
		$this->db->where('term_type', $type);
		$this->db->order_by('order','ASC');
		
		if ($return == 'count')
		{
			$this->num_terms = $this->db->count_all_results('post_terms');
			
			return $this->num_terms;
		}
		else
		{
			$q = $this->db->get('post_terms');
			
			$this->num_terms = $q->num_rows();
			
			if ($this->num_terms == 0 and $return != 'objects_set')
			{
				return FALSE;
			}
			elseif ($this->num_terms == 1 and $id and $return != 'objects_set')
			{
				$r = $q->row();
				
				return $r;
			}
			else
			{
				$array = array();
				
				foreach ($q->result() as $r)
				{
					$array[] = $r;
				}
				
				return $array;
			}
		}
	}
	
	public function add($type, $data)
	{
		$data['permalink']		= $this->appflow->unique_permalink(permalink($data['title']), 'post_terms');
		$data['term_type']		= $type;

		$this->db->insert('post_terms', $data);
	}
	
	public function update($type, $id, $data, $by='id')
	{
		$this->db->where('term_type', $type);
		$this->db->where($by, $id);

		$this->db->update('post_terms', $data);
		
		return $this->db->affected_rows();
	}
	
	public function delete($type, $id, $by='id')
	{
		$this->db->where('term_type', $type);
		$this->db->where($by, $id);

		$this->db->limit(1);
		
		$this->db->delete('post_terms');
		
		$num_deleted = $this->db->affected_rows();
		
		// Has this term been parent to something?
		$this->db->where('term_type', $type);
		$this->db->where('parent_id', $id);
		$this->db->update('post_terms', array ('parent_id' => 0));
		
		// Has this term had some posts attached?
		$this->db->where('term_id', $id);
		$this->db->update('posts', array ('term_id' => 0));
		
		return $num_deleted;
	}
	
	public function is_term($type, $id, $by='id')
	{
		if ($this->get($type, $id, $by, 'count') > 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function breadcrumb($type, $thisterm_id)
	{
		$crumb		= array();
		
		$x			= 0;
		
		while (($term = $this->get($type, $thisterm_id)) !== FALSE)
		{
			if ($x == 0)
			{
				$term->is_current = TRUE;
			}
			else
			{
				$term->is_current = FALSE;
			}
			
			$crumb[]			= $term;
			
			if ($term->parent_id == 0)
			{
				break;
			}
			else
			{
				$thisterm_id	= $term->parent_id;
			}
			
			$x++;
		}
		
		return array_reverse($crumb);
	}
}

/* End of file terms_model.php */
/* Location: ./application/models/terms_model.php */