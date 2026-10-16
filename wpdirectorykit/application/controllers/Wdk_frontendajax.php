<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_frontendajax extends Winter_MVC_Controller {

	public function __construct(){
		if(defined( 'WP_DEBUG' ) && WP_DEBUG) {
			ini_set('display_errors',1);
			ini_set('display_startup_errors',1);
			error_reporting(-1);
		}
		
		parent::__construct();

        $this->data['is_ajax'] = true;
        
	}
    
	public function index(&$output=NULL, $atts=array())
	{

	}

	public function map_infowindow() {
		$this->load->load_helper('listing');
		$this->load->model('listing_m');
		$this->load->model('listingfield_m');
		
		$data = array();
        $data['message'] = '';
		$listing_post_id = NULL;
		if(isset($_POST['listing_post_id']))
        	$listing_post_id = sanitize_text_field($_POST['listing_post_id']);

		$listing = $this->load->listing_m->get($listing_post_id, TRUE);
			
		if (!$listing || $listing->post_status != 'publish' || !empty($listing->post_password)) {
			return false;
		}

		/* protect check */
		if (
			!wdk_field_value('is_activated', $listing, false) || !wdk_field_value('is_approved', $listing,false)
		) {
			$data['popup_content'] = __( 'Listing is not public', 'wpdirectorykit' );
			$this->output($data);
		}

		$popup_layout = 'result_item_card';
		if(!empty($_POST['custom_layout_id'])) {
			$popup_layout = (int) sanitize_text_field($_POST['custom_layout_id']);
		}

		if(!empty($listing)) {
			$data['popup_content'] = wdk_listing_card($listing, array('infobox' => true), false, '<div class="infobox map-box">%1$s<div>', $popup_layout);
		} else {
			$data['popup_content'] = __( 'Listing is missing', 'wpdirectorykit' );
		}

        $data['success'] = true;

        $this->output($data);
	}

	public function map_infowindow_dash() {
		$this->load->load_helper('listing');
		$this->load->model('listing_m');
		$this->load->model('listingfield_m');
		
		$data = array();
        $data['message'] = '';
		$listing_post_id = NULL;
		if(isset($_POST['listing_post_id']))
        	$listing_post_id = sanitize_text_field($_POST['listing_post_id']);

		$listing = $this->load->listing_m->get($listing_post_id, TRUE);

			
		if (!$listing || $listing->post_status != 'publish' || !empty($listing->post_password)) {
			return false;
		}

		/* protect check */
		if (
			!wdk_field_value('is_activated', $listing, false) || !wdk_field_value('is_approved', $listing,false)
		) {
			$data['popup_content'] = __( 'Listing is not public', 'wpdirectorykit' );
			$this->output($data);
		}

		if(!empty($listing)) {
			$data['popup_content'] = wdk_listing_card($listing, [], false, '<div class="infobox map-box">%1$s<div>', 'result_item_card_dash_edit');
		} else {
			$data['popup_content'] = __( 'Listing is missing', 'wpdirectorykit' );
		}

        $data['success'] = true;

        $this->output($data);
	}
	  
    public function treefieldid($output="", $atts=array(), $instance=NULL)
    {

		check_ajax_referer('wdk_secure_treefieldid', 'wdk_secure');

		$this->load->load_helper('listing');
		$this->load->model('listing_m');
		$this->load->model('listingfield_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $results = array();
        
        $parameters = array();
        
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

        $table_name = $table = $parameters['table'];
        
        if(empty($parameters['empty_value']))
            $parameters['empty_value'] = ' - ';
        
        if(empty($parameters['limit']))
            $parameters['limit'] = 10;
            
        if(empty($parameters['attribute_id']))
            $parameters['attribute_id'] = 'id';
            
        if(empty($parameters['attribute_value']))
            $parameters['attribute_value'] = 'address';
            
        if(empty($parameters['offset']))
            $parameters['offset'] = 0;

        if(empty($parameters['sql_where']))
            $parameters['sql_where'] = '';

        if(empty($parameters['hide_fields']))
            $parameters['hide_fields'] = '';
        
        $start_id = '';
        if(isset($parameters['start_id']))
            $start_id = $parameters['start_id'];

        if($parameters['offset'] == 0) // currently don't have load_more functionality'
            
        if(!empty($parameters['empty_value']))
        {
            $results[0]['key'] = '';
            $results[0]['value'] = sanitize_text_field($parameters['empty_value']);
        }
	
		// it's model

		if($table == 'calendar_listing_m')
			$table = 'listing_m';


		$allowed_models = ['category_m', 'location_m', 'field_m', 'listing_m', 'user_m', 'icons_list', 'calendar_listing_m'];
		if (!in_array(strtolower($table), $allowed_models, true)) {
			wp_die('Forbidden');
		}

		$post_table = $this->db->prefix.'posts';

		$table_name = substr($table,0, -2);

		$attr_id = preg_replace("/[^0-9a-zA-Z_\-]/", "",sanitize_text_field($parameters['attribute_id']));
		$attr_val = preg_replace("/[^0-9a-zA-Z_\-]/", "",sanitize_text_field($parameters['attribute_value']));


		$attr_search = wdk_esc_sql(sanitize_text_field($parameters['search_term'] ?? ''));
		$skip_id = intval($parameters['skip_id'] ?? 0);
		$language_id = intval($parameters['language_id'] ?? 0);

		if(empty($language_id))
			$language_id = NULL;

		$id_part="";
		if(is_numeric($attr_search))
			$id_part = "$attr_id=$attr_search OR ";
	
		if($table == 'icons_list') {
			$icons = $this->get_fa_icons();

			$tree_results = array();

			if(empty($attr_search)) {
				$tree_results = $icons;
			} else {
				foreach ($icons as $c){
					if (stripos($c, $attr_search) !== FALSE){
						//if $c starts with $input, add to matches list
						$tree_results[] = $c;
					} else if (strcmp($attr_search, $c) < 0){
						//$input comes after $c in alpha order
						//since $colors is sorted, we know that we won't find any more matches
						continue;
					}
				}
			}
			$tree_results = array_slice($tree_results,intval($parameters['offset']), intval($parameters['limit']));
			// limit

		} else {
			$this->load->model($table);
			
			$where = array();
			if(!empty($attr_search)) {
				$attr_val_sql = $attr_val;
				if($attr_val == 'location_title') {
					$this->load->model($table);
					$attr_val_sql = $this->$table->_table_name.'.location_title';
				} elseif($attr_val == 'category_title'){
					$this->load->model($table);
					$attr_val_sql = $this->$table->_table_name.'.category_title';
				}
				
				/* if category/locations search also in translated field */
				if($attr_val == 'location_title'  || $attr_val == 'category_title') {
					$where["($id_part $attr_val_sql LIKE '%$attr_search%' OR ".$this->$table->_table_name.".titles_for_search LIKE '%$attr_search%')"] = NULL;
				} else {
					$where["($id_part $attr_val_sql LIKE '%$attr_search%')"] = NULL;
				}
			}

			if(!empty($parameters['sql_where'] )) {
				$sql_where = '';
				if($parameters['sql_where'] == 'only_nochilds') {
					$sql_where = "((".$this->listing_m->_table_name.".listing_parent_post_id IS NULL OR ".$this->listing_m->_table_name.".listing_parent_post_id = 0) AND (".$this->listing_m->_table_name.".`listing_related_ids` IS NULL OR ".$this->listing_m->_table_name.".`listing_related_ids` = '') )";
				} else {
					
				}
				if(!empty($sql_where))
					$where[$sql_where] = NULL;
			}

			if(!empty($parameters['hide_fields'])) {
				$parameters['hide_fields'] = preg_replace('/[^0-9,]/', '', $parameters['hide_fields']);
				if($table == 'location_m') {
					$sql_where = esc_sql($this->$table->_table_name.'.idlocation').' NOT IN ('.esc_sql(sanitize_text_field($parameters['hide_fields'])).')';
					foreach (explode(',', $parameters['hide_fields']) as $key => $id) {
						$sql_where .= ' AND CONCAT(",", '.$this->$table->_table_name.'.parent_path, ",") NOT LIKE CONCAT("%,", '.intval($id).', ",%")';
					}

					$where[$sql_where] = NULL;
				}
				
				if($table == 'category_m') {
					$sql_where = esc_sql($this->$table->_table_name.'.idcategory').' NOT IN ('.esc_sql(sanitize_text_field($parameters['hide_fields'])).')';
					foreach (explode(',', $parameters['hide_fields']) as $key => $id) {
						$sql_where .= ' AND CONCAT(",", '.$this->$table->_table_name.'.parent_path, ",") NOT LIKE CONCAT("%,", '.intval($id).', ",%")';
					}
					$where[$sql_where] = NULL;
				}
			}
			
			if(isset($parameters['user_check']) && ($parameters['user_check'] == 'true' || $parameters['user_check'] == '1')) {
				if($table == 'listing_m') {
					if($parameters['table'] == 'calendar_listing_m') {
						global $Winter_MVC_wdk_bookings;
						$Winter_MVC_wdk_bookings->model('calendar_m');
						$this->db->join($Winter_MVC_wdk_bookings->calendar_m->_table_name.' ON '.$post_table.'.ID = '.$Winter_MVC_wdk_bookings->calendar_m->_table_name.'.post_id');
					} 

					$tree_results = $this->$table->get_pagination(intval($parameters['limit']),intval($parameters['offset']), $where, TRUE, get_current_user_id());
				} else {
					
					if(!empty($parameters['filter_ids'])){
						$this->db->where(array( esc_sql($this->$table->_table_name.'.'.$this->$table->_primary_key).' IN ('.esc_sql(preg_replace('/[^0-9,]/', '', $parameters['filter_ids'])).')' => NULL));
					}

					$tree_results = $this->$table->get_pagination(intval($parameters['limit']),intval($parameters['offset']), $where );
				}
			} else {
				if($parameters['table'] == 'calendar_listing_m') {
					global $Winter_MVC_wdk_bookings;
					$Winter_MVC_wdk_bookings->model('calendar_m');
					$this->db->join($Winter_MVC_wdk_bookings->calendar_m->_table_name.' ON '.$post_table.'.ID = '.$Winter_MVC_wdk_bookings->calendar_m->_table_name.'.post_id');
				}
				
				if(!empty($parameters['filter_ids'])){
					$this->db->where(array( esc_sql($this->$table->_table_name.'.'.$this->$table->_primary_key).' IN ('.esc_sql(preg_replace('/[^0-9,]/', '', $parameters['filter_ids'])).')' => NULL));
				}
				
				if($table == 'user_m') {
					$like = "(meta_value LIKE '%wdk_%' )";
					$tree_results = $this->$table->get_pagination(intval($parameters['limit']),intval($parameters['offset']), $where, NULL, $like);

				} else {

					if(method_exists($this->$table, 'get_pagination_simple')) {
						if($table == 'location_m') {
							$this->db->select('location_title, idlocation, level');
						} else if($table == 'category_m') {
							$this->db->select('category_title, idcategory, level');
						}
						$tree_results = $this->$table->get_pagination_simple(intval($parameters['limit']),intval($parameters['offset']), $where);
					} else {
						$tree_results = $this->$table->get_pagination(intval($parameters['limit']),intval($parameters['offset']), $where);
					}

				}
			}

		}

		$ind_order=1;
		foreach ($tree_results as $key=>$row)
		{
				$level_gen='';
				if(empty($attr_search) && isset($row->level))
					$level_gen = str_pad('', $row->level*12, '&nbsp;').'';
				
				$results[$ind_order]['key'] = wmvc_show_data($attr_id, $row);
				if($table == 'listing_m') {
					$results[$ind_order]['value'] = $level_gen
												.'#'.wmvc_show_data($attr_id, $row).', '.wmvc_show_data($attr_val, $row);
				} elseif($table == 'user_m') {
					$results[$ind_order]['value'] = $level_gen
												.'#'.wmvc_show_data($attr_id, $row).', '.wmvc_show_data('display_name', $row);

					//if(current_user_can('wdk_listings_manage'))
					//	$results[$ind_order]['value'] .= ' ('.wmvc_show_data('user_email', $row).')';
				} elseif($table == 'icons_list') {
					$results[$ind_order]['key'] = $row;
					if(defined('ELEMENTOR_ASSETS_URL')){
						$results[$ind_order]['value'] = '<i class="'. $row.'"></i>&nbsp;&nbsp;'.$row;
					} else {
						$results[$ind_order]['value'] = $row;
					}
				} else {
					$results[$ind_order]['value'] = $level_gen
					// Dynamic field values are registered in the translation catalog separately.
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
													.esc_html__(wmvc_show_data($attr_val, $row), 'wpdirectorykit');
				}
			$ind_order++;
		}

	
		// get current value by ID
		$row=NULL;
		if($table == 'icons_list') {
			if(!empty($parameters['curr_id'])) {
				$row = '<i class="'. $parameters['curr_id'].'"></i>&nbsp;&nbsp;'.$parameters['curr_id'];
			}
		} else {
			if(!empty($parameters['curr_id']))
				$row = $this->$table->get(intval($parameters['curr_id']), TRUE);
		}

		if($table == 'icons_list') {
			$data['curr_val'] = $row;
		}elseif(is_object($row))
		{
            $level_gen='';
			if(isset($row->level))
			    $level_gen = str_pad('', $row->level*12, '&nbsp;').'';

			if($table == 'user_m') {
				$data['curr_val'] = $level_gen
											.wmvc_show_data('display_name', $row);
				
			} else {
				$data['curr_val'] = $level_gen
				// Dynamic field values are registered in the translation catalog separately.
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
							.esc_html__(wmvc_show_data(wmvc_show_data('attribute_value', $parameters), $row), 'wpdirectorykit');
			}


			//if(!empty($start_id) && $start_id == $parameters['curr_id'] && isset($parameters['sub_empty_value']) && !empty($parameters['sub_empty_value'])) $this->data['curr_val'] = wmvc_show_data('sub_empty_value', $parameters);
			//elseif(!empty($start_id) && $start_id == $parameters['curr_id']) $this->data['curr_val'] = $parameters['empty_value'];
		}
		else
		{
			$data['curr_val'] = $parameters['empty_value'];
		}
	
		$this->data['success'] = true;
        
        $data['results'] = $results;
        $data['sql'] = $this->db->last_query();
		$this->output($data);
    }
	  
    public function treefieldid_checkboxes($output="", $atts=array(), $instance=NULL)
    {
		check_ajax_referer('wdk_secure_treefieldid_checkboxes', 'wdk_secure');

		$this->load->load_helper('listing');
		$this->load->model('listing_m');
		$this->load->model('listingfield_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $results = array();
        
        $parameters = array();
        
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

        $table_name = $table = $parameters['table'];
        
        if(empty($parameters['empty_value']))
            $parameters['empty_value'] = ' - ';
        
        if(empty($parameters['limit']))
            $parameters['limit'] = 10;
            
        if(empty($parameters['attribute_id']))
            $parameters['attribute_id'] = 'id';
            
        if(empty($parameters['attribute_value']))
            $parameters['attribute_value'] = 'address';
            
        if(empty($parameters['offset']))
            $parameters['offset'] = 0;
        
		if(empty($parameters['sql_where']))
            $parameters['sql_where'] = '';
        
		if(empty($parameters['hide_fields']))
            $parameters['hide_fields'] = '';

        $start_id = '';
        if(isset($parameters['start_id']))
            $start_id = $parameters['start_id'];

        if($parameters['offset'] == 0) // currently don't have load_more functionality'
            
        if(!empty($parameters['empty_value']))
        {
            $results[0]['key'] = '';
            $results[0]['value'] = sanitize_text_field($parameters['empty_value']);
        }
	
		// it's model

		if($table == 'calendar_listing_m')
			$table = 'listing_m';

		
		$allowed_models = ['category_m', 'location_m', 'field_m', 'listing_m', 'user_m', 'icons_list', 'calendar_listing_m'];
		if (!in_array(strtolower($table), $allowed_models, true)) {
			wp_die('Forbidden');
		}
		
		$table_name = substr($table,0, -2);
		$attr_id = preg_replace("/[^0-9a-zA-Z_\-]/", "",sanitize_text_field($parameters['attribute_id']));
		$attr_val = preg_replace("/[^0-9a-zA-Z_\-]/", "",sanitize_text_field($parameters['attribute_value']));
		
		$attr_search = wdk_esc_sql(sanitize_text_field($parameters['search_term']));
		$skip_id = intval($parameters['skip_id']);
		$language_id = intval($parameters['language_id']);

		if(empty($language_id))
			$language_id = NULL;

		$id_part="";
		if(is_numeric($attr_search))
			$id_part = "$attr_id=$attr_search OR ";
	
		$this->load->model($table);
		
		$where = array();
		if(!empty($attr_search))
			$attr_val_sql = $attr_val;
			if($attr_val == 'location_title') {
				$this->load->model($table);
				$attr_val_sql = $this->$table->_table_name.'.location_title';
			} elseif($attr_val == 'category_title'){
				$this->load->model($table);
				$attr_val_sql = $this->$table->_table_name.'.category_title';
			}
			
			/* if category/locations search also in translated field */
			if($attr_val == 'location_title'  || $attr_val == 'category_title') {
				$where["($id_part $attr_val_sql LIKE '%$attr_search%' OR ".$this->$table->_table_name.".titles_for_search LIKE '%$attr_search%')"] = NULL;
			} else {
				$where["($id_part $attr_val_sql LIKE '%$attr_search%')"] = NULL;
			}
		
			if(isset($parameters['selected']) && !empty($parameters['selected'])) {
                   
				if(is_string($parameters['selected']) && strpos($parameters['selected'], ',') !== FALSE){
					$selected = explode(',', $parameters['selected']);
				} elseif(is_string($parameters['selected'])){
					$selected = array($parameters['selected']);
				}

				$ids = array();
				foreach($selected as $selected_item) {
					if(!empty($selected_item) && is_intval($selected_item)) {
						$ids [] = $selected_item;
					}
				}
				
				/* where in */
				if(!empty($ids)){
					$this->db->order_by('FIELD('.$this->$table->_table_name.'.'.$this->$table->_primary_key.', '. implode(',', array_reverse($ids)) . ') DESC');
					if(intval($parameters['limit']) < count($ids)) {
						$parameters['limit'] = count($ids);
					}
				}
            } 

			if(!empty($parameters['filter_ids'])){
				$this->db->where(array( esc_sql($this->$table->_table_name.'.'.$this->$table->_primary_key).' IN ('.esc_sql(preg_replace('/[^0-9,]/', '', $parameters['filter_ids'])).')' => NULL));
			}

			if(!empty($parameters['hide_fields'])) {
				$parameters['hide_fields'] = preg_replace('/[^0-9,]/', '', $parameters['hide_fields']);
				if($table == 'location_m') {
					$sql_where = esc_sql($this->$table->_table_name.'.idlocation').' NOT IN ('.esc_sql(sanitize_text_field($parameters['hide_fields'])).')';
					foreach (explode(',', $parameters['hide_fields']) as $key => $id) {
						$sql_where .= ' AND CONCAT(",", '.$this->$table->_table_name.'.parent_path, ",") NOT LIKE CONCAT("%,", '.intval($id).', ",%")';
					}

					$where[$sql_where] = NULL;
				}
				
				if($table == 'category_m') {
					$sql_where = esc_sql($this->$table->_table_name.'.idcategory').' NOT IN ('.esc_sql(sanitize_text_field($parameters['hide_fields'])).')';
					foreach (explode(',', $parameters['hide_fields']) as $key => $id) {
						$sql_where .= ' AND CONCAT(",", '.$this->$table->_table_name.'.parent_path, ",") NOT LIKE CONCAT("%,", '.intval($id).', ",%")';
					}
					$where[$sql_where] = NULL;
				}
			}
			
			$tree_results = $this->$table->get_pagination(intval($parameters['limit']),intval($parameters['offset']), $where);
	
		$ind_order=1;
		foreach ($tree_results as $key=>$row)
		{
				$level_gen='';
				if(empty($attr_search) && isset($row->level))
					$level_gen = str_pad('', $row->level*12, '&nbsp;').'';
				
				$results[$ind_order]['key'] = wmvc_show_data($attr_id, $row);
				$results[$ind_order]['value'] = $level_gen
				// Dynamic field values are registered in the translation catalog separately.
				// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
												.esc_html__(wmvc_show_data($attr_val, $row), 'wpdirectorykit');
			$ind_order++;
		}
	
		// get current value by ID
		$row=NULL;
		if(!empty($parameters['curr_id']))
			$row = $this->$table->get(intval($parameters['curr_id']), TRUE);

		if(is_object($row))
		{
            $level_gen='';
			if(isset($row->level))
			    $level_gen = str_pad('', $row->level*12, '&nbsp;').'';
			$data['curr_val'] = $level_gen
			// Dynamic field values are registered in the translation catalog separately.
								// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
							.esc_html__(wmvc_show_data(wmvc_show_data('attribute_value', $parameters), $row), 'wpdirectorykit');

		}
		else
		{
			$data['curr_val'] = $parameters['empty_value'];
		}
	
		$this->data['success'] = true;
        
        $data['results'] = $results;
        //$data['sql'] = $this->db->last_query();
		$this->output($data);
    }
	    
    private function output($data, $print = TRUE) {
        if($print) {
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache');
            header('Content-Type: application/json; charset=utf8');
            //header('Content-Length: '.$length); // special characters causing troubles
            echo (wp_json_encode($data));
            exit();
        } else {
            return $data;
        }
    }
	
  
    public function select_2_ajax($output="", $atts=array(), $instance=NULL)
    {

		check_ajax_referer('wdk_secure_ajax', 'wdk_secure');

		$this->load->load_helper('listing');
		$this->load->model('listing_m');
		$this->load->model('listingfield_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $results = array();
		$data['pagination'] = true;
        $parameters = array();
        
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		if(empty($parameters['table'])) return false;

		$model_name = $parameters['table'];

		// allow only 'listing_m', 'category_m', or 'location_m' for security
		$allowed_models = array('listing_m', 'category_m', 'location_m');
		if (!in_array($model_name, $allowed_models)) {
			return false;
		}

		$public_columns = array(
			'category_m' => array('idcategory', 'category_title', 'titles_for_search'),
			'location_m' => array('idlocation', 'location_title', 'titles_for_search'),
			'listing_m' => array('post_id', 'post_title', 'post_title'),
		);
		$key_column = $public_columns[$model_name][0];
		$print_column = $public_columns[$model_name][1];
		$this->load->model($model_name);
		$search_table = $model_name === 'listing_m' ? $this->db->prefix . 'posts' : $this->$model_name->_table_name;
		$search_column = $search_table . '.' . $print_column;
		if ($model_name !== 'listing_m') {
			$search_column .= ',' . $search_table . '.' . $key_column . ',' . $search_table . '.' . $public_columns[$model_name][2];
		}

		$limit = 20;
		if(!empty($parameters['limit'])) {
			$limit = intval($parameters['limit']);
		}
		$limit = max(1, min(100, $limit));

		$offset = NULL;
		if(!empty($parameters['page_result']) && $parameters['page_result'] > 1) {
			$offset = (intval($parameters['page_result']) - 1) * $limit;
		}

		$this->load->model($model_name);

		$where = array();
		if(!empty($_POST['q']['term']) && !empty($search_column)) {
			$sql_search = '';
			foreach (explode(',', $search_column) as $column) {
				if(empty($column)) continue;

				if(!empty($sql_search))
					$sql_search .= " OR ";

				$sql_search .= " ".esc_sql($column)." LIKE '%".esc_sql($_POST['q']['term'])."%'";
			}

			$where ["($sql_search)"] = NULL;
		}

		if ($model_name === 'listing_m') {
			$where['post_status'] = 'publish';
			$where[$this->db->prefix . "posts.post_password = ''"] = NULL;
			$where['is_activated'] = 1;
			$where['is_approved'] = 1;
		}
		$db_results_total = $this->$model_name->total($where);
		$db_results = $this->$model_name->get_pagination($limit,$offset, $where);
       
		if (!$db_results) {
            $data['errors'] = '';
        } else {
            foreach($db_results as $row) {

				$level_gen='';
				if(empty($attr_search) && isset($row->level))
					$level_gen = str_pad('', $row->level*12, '&nbsp;').'';

                $results[] = [
                        'id'=> wmvc_show_data($key_column, $row),
						// Dynamic field values are registered in the translation catalog separately.
// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
                        'text'=> $level_gen.esc_html__(trim(wmvc_show_data($print_column, $row)), 'wpdirectorykit'),
                ];
            }

        }

		$data['success'] = true;
        
        $data['results'] = $results;


		if($db_results_total >= $limit + $offset) {
			$data['pagination'] =[
				"more"=> true
			];
		}

        //$data['sql'] = $this->db->last_query();
		$this->output($data);
    }
	  
    public function select_2_ajax_user($output="", $atts=array(), $instance=NULL)
    {
		$this->load->load_helper('listing');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $results = array();
		$data['pagination'] = true;
        $parameters = array();
        
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		$model_name = 'user_m';

		$key_column = '';
		$print_column = '';
		
		$limit = 20;
		if(!empty($parameters['limit'])) {
			$limit = intval($parameters['limit']);
		}

		$offset = NULL;
		if(!empty($parameters['page_result']) && $parameters['page_result'] > 1) {
			$offset = (intval($parameters['page_result']) - 1) * $limit;
		}

		$this->load->model($model_name);
		$search_column = 'display_name,user_login,ID';
		if(current_user_can('administrator')) {
			$search_column = 'display_name,user_email,user_login,ID';
		}
		
		$where = array();
		if(!empty($_POST['q']['term']) && !empty($search_column)) {
			$sql_search = '';
			foreach (explode(',', $search_column) as $column) {
				if(empty($column)) continue;

				if(!empty($sql_search))
					$sql_search .= " OR ";

				$sql_search .= " ".esc_sql($column)." LIKE '%".esc_sql($_POST['q']['term'])."%'";
			}

			$where ["($sql_search)"] = NULL;
		}

		$like = "(meta_value LIKE '%wdk_%' )";
		$db_results_total = $this->$model_name->total($where, NULL, $like);

		$db_results = $this->$model_name->get_pagination($limit,$offset, $where, NULL, $like);
       
		if (!$db_results) {
            $data['errors'] = '';
        } else {
            foreach($db_results as $row) {

				$level_gen='';
				//if(empty($attr_search) && isset($row->level))
				//	$level_gen = str_pad('', $row->level*12, '&nbsp;').'';

                $results[] = [
                        'id'=> wmvc_show_data('ID', $row),
                        'text'=> $level_gen.trim(wmvc_show_data('display_name', $row)),
                ];
            }

        }

		$data['success'] = true;
        
        $data['results'] = $results;
		if($db_results_total >= $limit + $offset) {
			$data['pagination'] =[
				"more"=> true
			];
		}

        //$data['sql'] = $this->db->last_query();
		$this->output($data);
    }
		  
    public function wdk_tree_dropdowns($output="", $atts=array(), $instance=NULL)
    {
		$this->load->load_helper('listing');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
        $results = array();
        $parameters = array();
        
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		if(empty($parameters['table'])) return false;

		switch ($parameters['table']) {
			case 'category_id':
			case 'search_category':
				$model_name = 'category_m';
				break;
			case 'location_id':
			case 'search_location':
				$model_name = 'location_m';
				break;
			default:
				# code...
				break;
		}

		$key_column = '';
		$print_column = '';
		$current_id = '0';

		if(!empty($parameters['id'])) {
			$current_id = intval($parameters['id']);
		}

		if(empty($parameters['key_column'])) {
			switch ($model_name) {
				case 'category_m':
					$key_column = 'idcategory';
					break;
				case 'location_m':
					$key_column = 'idlocation';
					break;
				
				default:
					# code...
					break;
			}
		} else {
			$key_column = $parameters['key_column'];
		}

		if(empty($parameters['print_column'])) {
			switch ($model_name) {
				case 'category_m':
					$print_column = 'category_title';
					break;
				case 'location_m':
					$print_column = 'location_title';
					break;
				
				default:
					# code...
					break;
			}
		} else {
			$print_column = $parameters['print_column'];
		}

		$this->load->model($model_name);

		$db_results = $this->$model_name->get_by(array('parent_id = '.$current_id => NULL)); 
		$level = 5;

		if(isset($db_results[0]->level)) {
			$level = $db_results[0]->level;
		}

		$data['output'] =  $db_results;
       
		if (!$db_results) {
            $data['errors'] = '';
        } else {

			if($model_name == 'category_m') {
				$placeholder_texts = [
					0 => esc_html__('Select Categories','wpdirectorykit'),
					1 => esc_html__('Select Sub Categories','wpdirectorykit'),
					2 => esc_html__('Select Sub Categories','wpdirectorykit'),
					3 => esc_html__('Select Sub Categories','wpdirectorykit'),
					4 => esc_html__('Select Sub Categories','wpdirectorykit'),
					5 => esc_html__('Select Sub Categories','wpdirectorykit'),
				];
			} else {
				$placeholder_texts = [
					0 => esc_html__('Select Country','wpdirectorykit'),
					1 => esc_html__('Select City','wpdirectorykit'),
					2 => esc_html__('Select Neighborhood','wpdirectorykit'),
					3 => esc_html__('Select Sub Area','wpdirectorykit'),
					4 => esc_html__('Select Sub Area','wpdirectorykit'),
					5 => esc_html__('Select Sub Area','wpdirectorykit'),
				];
			}
			
            if(isset($placeholder_texts[$level])) {
                $placeholder = $placeholder_texts[$level];
            } else {
                $placeholder = esc_html__('Select Sub Categories','wpdirectorykit');
            }

			$results[] = [
				'id'=> '',
				'text'=> $placeholder
			];

            foreach($db_results as $row) {
                $results[] = [
                        'id'=> wmvc_show_data($key_column, $row),
						// Dynamic field values are registered in the translation catalog separately.
// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
                        'text'=> esc_html__(trim(wmvc_show_data($print_column, $row)),'wpdirectorykit'),
                ];
            }

        }

		$data['success'] = true;
        $data['results'] = $results;

        //$data['sql'] = $this->db->last_query();
		$this->output($data);
    }

		  
    public function search_suggestion($output="", $atts=array(), $instance=NULL)
    {
		$this->load->load_helper('listing');
		$this->load->model('category_m');
		$this->load->model('location_m');
		$this->load->model('listing_m');

        $data = array();
		$categories_limit = 5;
		$categories_search_column = array('idcategory','category_title');
		$locations_limit = 5;
		$locations_search_column = array('idlocation','location_title');

		$results = array();
		/*
		[
			'field_key' => 'string',
			'value' => 'string',
			'print' => [
				'html' => 'string',
				'parsed_html' => [
					'left_column' => 'string',
					'middle_column' => 'string',
					'right_column' => 'string',
				],
				'parsed_content' => [
					'icon_class' => 'string',
					'title' => 'string',
					'sub_title' => 'string',
					'right_text' => 'string',
				]
			]
		]

		*/

        $data['message'] = __('No message returned!', 'wpdirectorykit');

        $parameters = array();
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		$search_text = '';
		if(!empty($parameters['search']))
			$search_text = trim($parameters['search']);

		/* Categories */
		$where = array();
		if($search_text) {
			$sql_search = '';
			foreach ($categories_search_column as $column) {
				if(!empty($sql_search))
					$sql_search .= " OR ";

				$sql_search .= " ".$this->category_m->_table_name.'.'.esc_sql($column)." LIKE '%".esc_sql($search_text)."%'";
			}
			$where ["($sql_search)"] = NULL;
			$db_results = $this->category_m->get_pagination($categories_limit, NULL, $where);
		} else {
			$db_results = $this->category_m->get_pagination($categories_limit, NULL, array('parent_id = 0' => NULL));
		}

		if($db_results) foreach($db_results as $row) {
			$results[] = [
				'field_key' => 'search_category',
				'value' => $row->category_title,
				'print' => [
					'parsed_content' => [
						'icon_class' => (!empty(wmvc_show_data('font_icon_code', $row, false))) ? esc_attr(wmvc_show_data('font_icon_code', $row)): 'fa fa-tag',
						// Dynamic field values are registered in the translation catalog separately.
						// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
						'title' => esc_html__($row->category_title, 'wpdirectorykit'),
						'sub_title' => '',
						'right_text' => __('Category', 'wpdirectorykit')
					]
				]
			];
		}

		/* END Categories */

		/* Locations */
		$where = array();
		if($search_text) {
			$sql_search = '';
			foreach ($locations_search_column as $column) {
				if(!empty($sql_search))
					$sql_search .= " OR ";

				$sql_search .= " ".$this->db->prefix."wdk_locations.".esc_sql($column)." LIKE '%".esc_sql($search_text)."%'";
			}
			$where ["($sql_search)"] = NULL;
			
			$select = $this->db->prefix.'wdk_locations.idlocation, '.$this->db->prefix.'wdk_locations.location_title';
			$select .= ',location_1.location_title AS location_title_1';
	
			$this->db->join($this->db->prefix.'wdk_locations AS location_1 ON '.$this->db->prefix.'wdk_locations.parent_id = location_1.idlocation', NULL, 'LEFT');
			for($i = 2 ; $i <= 2; $i++) {
				$select .= ',location_'.$i.'.location_title AS location_title_'.$i.'';
				$this->db->join($this->db->prefix.'wdk_locations AS location_'.$i.' ON location_'.($i-1).'.parent_id = location_'.$i.'.idlocation', NULL, 'LEFT');
			}
			$this->db->select($select);
			$db_results = $this->location_m->get_pagination($locations_limit, NULL, $where);
		} else {
			$db_results = $this->location_m->get_pagination($locations_limit, NULL, array('parent_id = 0' => NULL));
		}

		if($db_results) foreach($db_results as $row) {
			$subtitle = '';

			for($i = 1 ; $i <= 2; $i++) {
				if(wmvc_show_data('location_title_'.$i, $row, false)){
					$subtitle .= wmvc_show_data('location_title_'.$i, $row, false).', ';
				}
			}

			$subtitle = substr($subtitle,0,-2);

			$results[] = [
				'field_key' => 'search_location',
				'value' => $row->location_title,
				'print' => [
					'parsed_content' => [
						'icon_class' => (!empty(wmvc_show_data('font_icon_code', $row, false))) ? esc_attr(wmvc_show_data('font_icon_code', $row)) : 'fa fa-map-marker',
						// Dynamic field values are registered in the translation catalog separately.
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
						'title' => esc_html__($row->location_title, 'wpdirectorykit'),
						// Dynamic field values are registered in the translation catalog separately.
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
						'sub_title' => esc_html__($subtitle, 'wpdirectorykit'),
						'right_text' => __('Location', 'wpdirectorykit')
					]
				]
			];
		}
		
		/* END Locations */

		/* address suggestion if empty other */
		$limit = apply_filters('wdk/search_suggestion/address_limit', 5);
		if(count($results)<10) {
			$this->db->select('address');
			$db_results = $this->db->where(array("address LIKE '%".esc_sql($search_text)."%'" => NULL));
			$this->db->from($this->listing_m->_table_name);
			$this->restrict_public_suggestions($this->db, $this->listing_m->_table_name);
			$this->db->group_by('address');
			$this->db->limit($limit);
		
			$query = $this->db->get();
			if ($this->db->num_rows() > 0) {
				$db_results = $this->db->results();
			} else {
				$db_results = array();
			}

			if($db_results) foreach($db_results as $row) {
				$results[] = [
					'field_key' => 'address',
					'value' => $row->address,
					'print' => [
						'parsed_content' => [
							'icon_class' => 'fa fa-map-marker',
							'title' => $row->address,
							'sub_title' => '',
							'right_text' => __('Address', 'wpdirectorykit')
						]
					]
				];
			}
		}
		
		/* listings by title suggestion */
		// Example of how to use add_filter for 'wdk/search_suggestion/listings_limit'
		// In your theme's functions.php or a custom plugin, you can add:
		// add_filter('wdk/search_suggestion/listings_limit', function($limit) { return 10; });

		$limit = apply_filters('wdk/search_suggestion/listings_limit', 5);
		$this->db->where(array("post_title LIKE '%".esc_sql($search_text)."%'" => NULL));
		$db_results = $this->listing_m->get_pagination($limit, NULL, array('post_status' => 'publish','post_password=""' => NULL,'is_activated' => 1,'is_approved'=>1));

		if($db_results) foreach($db_results as $row) {
			if (
				!isset($row->post_status, $row->post_password) ||
				$row->post_status !== 'publish' ||
				$row->post_password !== ''
			) {
				continue;
			}

			$results[] = [
				'field_key' => 'link',
				'value' => get_permalink($row),
				'print' => [
					'parsed_content' => [
						'icon_class' => 'fa fa-circle',
						'title' => $row->post_title,
						'sub_title' => '',
						'right_text' => __('Listing', 'wpdirectorykit')
					]
				]
			];
		}

		if(count($results)<10) {

			$name_part = str_replace(' ','+',$search_text);
        
			//$url = 'http://photon.komoot.de/api/?q='.$name_part;
			$url = 'https://api.teleport.org/api/cities/?limit=1&search='.$name_part;

			$request    = wp_remote_get( $url );
			$response = '';

			// request failed
			if ( is_wp_error( $request ) ) {
				$response = $request;
			}
			$code = (int) wp_remote_retrieve_response_code( $request );
	
			// make sure the fetch was successful
			if (empty($response) && $code == 200 ) {
				$response = wp_remote_retrieve_body( $request );
	
				// Decode the json
				$resp = json_decode( $response,true ); 
				if(!empty($resp) && isset($resp['_embedded']) && isset($resp["_embedded"]["city:search-results"]) && !empty($resp["_embedded"]["city:search-results"])) {
					foreach($resp["_embedded"]["city:search-results"] as $prediction)
					{
						if(isset($prediction["matching_full_name"])){
							$results[] = [
								'field_key' => 'search_location',
								'value' => $prediction["matching_full_name"],
								'print' => [
									'parsed_content' => [
										'icon_class' => 'fa fa-map-marker',
										'title' => $prediction["matching_full_name"],
										'sub_title' => '',
										'right_text' => ''
									]
								]
							];

						}
						break;
					}
				}
			} 
		}

		$data['success'] = true;
        $data['results'] = $results;

        //$data['sql'] = $this->db->last_query();
		$this->output($data);
    }
	
	public function booking_price_calculate() {
		$data = array();
		$data['success'] = false;
		
		$this->load->load_helper('listing');
		global $Winter_MVC_wdk_bookings;

		$Winter_MVC_wdk_bookings->model('reservation_m');
		$Winter_MVC_wdk_bookings->model('calendar_m');

		$results = array();
        $parameters = array();
		foreach ($_POST as $key => $value) {
			$parameters[sanitize_text_field($key)] = sanitize_text_field($value);
		}

		$post_id = NULL;
		$date_from = NULL;
		$date_to = NULL;

		if(isset($parameters['post_id'])) {
			$post_id = $parameters['post_id'];
		}
		
		$calendar = $Winter_MVC_wdk_bookings->calendar_m->get_by(array('post_id'=>$post_id), TRUE);
		$time_format = "00:00:00";
		if($calendar && $calendar->is_hour_enabled == 1)
	    	$time_format = "H:i:s";
		

		if(isset($parameters['date_from'])) {
			$date_from = wdk_normalize_date_db($parameters['date_from'], 'Y-m-d H:i:s', 'Y-m-d '.$time_format);
		}

		if(isset($parameters['date_to'])) {
			$date_to = wdk_normalize_date_db($parameters['date_to'], 'Y-m-d H:i:s', 'Y-m-d '.$time_format);
		}


		if($post_id && $date_from && $date_to) {
			$price = $Winter_MVC_wdk_bookings->reservation_m->calculate_price($post_id, $date_from, $date_to);

		
			$calendar_fees = array();
			if($calendar && !empty($calendar->json_data_fees))
				$calendar_fees = json_decode($calendar->json_data_fees );

			$results['symbol'] = '';
			if(function_exists('wdk_booking_currency_symbol'))
				$results['symbol'] = wdk_booking_currency_symbol();
			
			if($price) {
				$results['price'] = $price['price'];
				$results['total'] = $price['price'];
				$results['fees_data'] = array();
				$results['fees'] = array();
				foreach ($calendar_fees as $key => $fee) {
					if(!wmvc_show_data('is_activated', $fee, false,TRUE,TRUE)) continue;
					if(is_intval(wmvc_show_data('value', $fee,'',TRUE,TRUE))) {
						$field = wdk_generate_slug(strtolower(esc_html(wmvc_show_data('title', $fee,'-',TRUE,TRUE)))); 
						if(!wmvc_show_data('is_required', $fee, false,TRUE,TRUE) && isset($parameters['fee_'.$field]) && $parameters['fee_'.$field] == 0) {
						} else {
							$price = 0;
							if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_night') {
								$nights = (int)abs(strtotime($date_from) - strtotime($date_to))/(60*60*24);
								$price += intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * $nights;
							} else if(wmvc_show_data('calculation_base', $fee,'',TRUE,TRUE) == 'per_person') {
								$price += intval(wmvc_show_data('value', $fee,'',TRUE,TRUE)) * intval(wmvc_show_data('guests', $_POST, 0,TRUE,TRUE));
							} else {
								$price += intval(wmvc_show_data('value', $fee,'',TRUE,TRUE));
							}
							$results['fees'][wmvc_show_data('title', $fee,'',TRUE,TRUE)] = $price;
							$results['total'] += $price;
						}
					}

					$results['fees_data'][$key] = $fee;
					$results['fees_data'][$key]->field_id = wdk_generate_slug(strtolower(esc_html(wmvc_show_data('title', $fee,'-',TRUE,TRUE))));
					$results['fees_data'][$key]->calculation_base_text = (isset($Winter_MVC_wdk_bookings->calendar_m->calculation_base[wmvc_show_data('calculation_base', $fee,'-',TRUE,TRUE)])) ? $Winter_MVC_wdk_bookings->calendar_m->calculation_base[wmvc_show_data('calculation_base', $fee,'-',TRUE,TRUE)] : 'per stay';
				}
				$data['success'] = true;
			} else {
				$data['popup_text_error'] = __('Those dates are not available', 'wpdirectorykit');
			}
		}
		
        $data['results'] = $results;

        //$data['sql'] = $this->db->last_query();
		$this->output($data);
	}

	public function loading_listings() {
		$this->load->load_helper('listing'); 
		$this->load->load_helper('get_element_settings'); 
		$this->load->model('listing_m');
		$this->load->model('listingfield_m');
		$this->load->model('category_m');
		$public_where = array('post_status' => 'publish', "post_password = ''" => NULL, 'is_activated' => 1, 'is_approved' => 1);
		
		$data = array();
        $data['message'] = '';
        $data['popup_text_success'] = '';
        $data['popup_text_error'] = '';
        $data['output'] = [
			'map_results'=>null,
			'listings_result'=>null,
			'listings_result_message'=>null,
			'pagination_html'=>null,
			'listings_count'=>null,
			'listings_count_html'=>null,
		];
		$data['success'] = false;

        $parameters = array();
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}
		$public_widgets = array();
		foreach (array('el_results' => 'wdk-listings-results', 'el_map' => 'wdk-map') as $prefix => $widget_type) {
			if (!isset($parameters[$prefix . '_id'])) continue;
			$public_widgets[$prefix] = $this->public_widget($parameters, $prefix, $widget_type);
			if (!$public_widgets[$prefix]) {
				$data['message'] = __('The requested widget is not public.', 'wpdirectorykit');
				$this->output($data);
				return;
			}
		}

		$options = [
			'offset' => 0,
			'limit' => 6,
			'current_page' => 1,
			'limit_pagination' => 5,
			'layout_type' => 'grid',
		];

        $custom_parameters = array();
		$parameters['url'] = strstr($parameters['url'], "#", true);
		$parameters['url'] = strstr($parameters['url'], "?");
        if(!empty($parameters['url'])) {
            $qr_string = trim($parameters['url'],'?');
            $string_par = array();
            parse_str($qr_string, $string_par);
            $custom_parameters += $string_par;
        }
        
		$controller = 'listing';
		$columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
        $external_columns = array('location_id', 'category_id', 'post_title');

		if(isset($parameters['el_results_id'])) {
			wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters);
			$data['output']['listings_count'] = $this->listing_m->total($public_where);
			$data['output']['listings_count_html'] = $data['output']['listings_count'].' '.esc_html__('Listings', 'wpdirectorykit');

			if(isset($custom_parameters['wmvc_paged']))
				$options['current_page'] = intval(wmvc_xss_clean($custom_parameters['wmvc_paged']));

			$get_settings_el_results = $public_widgets['el_results']['loader'];
			$settings_el_results = $public_widgets['el_results']['settings'];

			if(wmvc_show_data('per_page', $settings_el_results, false)) {
				$options['limit'] = wmvc_show_data('per_page', $settings_el_results, false);
			}
			$options['limit'] = max(1, min(100, (int) $options['limit']));

			if(wmvc_show_data('layout_type', $settings_el_results, false)) {
				$options['layout_type'] = wmvc_show_data('layout_type', $settings_el_results, false);
			}

			$offset = $options['limit']*( $options['current_page']-1);

			global $_GET;
			$temp_get = $_GET;
			$_GET = $custom_parameters;
			do_action('wpdirectorykit/ajax/loading_listings');
			$REQUEST_URI = $_SERVER["REQUEST_URI"];

			$_SERVER["REQUEST_URI"] = $_SERVER['HTTP_REFERER'];
			
			$data['output']['pagination_html'] = wdk_wp_frontend_paginate($data['output']['listings_count'], $options['limit'], 'wmvc_paged', array(), TRUE,
																		FALSE, FALSE, $options['limit_pagination']);

			$_SERVER["REQUEST_URI"] = $REQUEST_URI;															
			$_GET = $temp_get;

			wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters);
			$listings_result = $this->listing_m->get_pagination($options['limit'], $offset, $public_where);

			if(wmvc_show_data('layout_type', $settings_el_results, false)) {
				$settings_el_results['content_button_icon'] = $get_settings_el_results->generate_icon(wmvc_show_data('layout_type', $settings_el_results, false));
			} else {
				$settings_el_results['content_button_icon'] = '';
			}

			if($listings_result) {
				foreach ($listings_result as $key => $listing) {
					if (!$this->is_public_listing($listing)) continue;
					$listing->card_view = '';

					if (
						wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_listing_card_elementor_layout') &&
						wmvc_show_data('is_custom_layout_enable', $settings_el_results) == 'yes'
					) {
						if ($options['layout_type'] == 'list' && wmvc_show_data('custom_layout_id_list', $settings_el_results, false)) {
							$content = '';
							$post_data = get_post(wmvc_show_data('custom_layout_id_list', $settings_el_results, false));

							global $wdk_listing_id;
							$wdk_listing_id = wmvc_show_data('post_id', $listing);
							if ($post_data && in_array($post_data->post_type, array('page', 'elementor_library'), true)
								&& $post_data->post_status === 'publish' && !post_password_required($post_data)) {
								if ($post_data->post_type == 'page' || $post_data->post_type == 'elementor_library') {
									$elementor_instance = \Elementor\Plugin::instance();
									$content = $elementor_instance->frontend->get_builder_content_for_display(wmvc_show_data('custom_layout_id_list', $settings_el_results, false));
									if (empty($content))
										$content = $post_data->post_content;
								} else {
									$content = $post_data->post_content;
								}
							}
							$listing->card_view = ($content);
						} elseif ($options['layout_type'] == 'grid'  &&  wmvc_show_data('custom_layout_id_grid', $settings_el_results, false)) {
							$content = '';
							$post_data = get_post( wmvc_show_data('custom_layout_id_grid', $settings_el_results, false));

							global $wdk_listing_id;
							$wdk_listing_id = wmvc_show_data('post_id', $listing);
							if ($post_data && in_array($post_data->post_type, array('page', 'elementor_library'), true)
								&& $post_data->post_status === 'publish' && !post_password_required($post_data)) {
								if ($post_data->post_type == 'page' || $post_data->post_type == 'elementor_library') {
									$elementor_instance = \Elementor\Plugin::instance();
									$content = $elementor_instance->frontend->get_builder_content_for_display(wmvc_show_data('custom_layout_id_grid', $settings_el_results, false));
									if (empty($content))
										$content = $post_data->post_content;
								} else {
									$content = $post_data->post_content;
								}
							}
							$listing->card_view = ($content);
						} else {
							$listing->card_view = wdk_listing_card($listing, $settings_el_results);
						}
					} else {
						$listing->card_view = wdk_listing_card($listing, $settings_el_results);
					}

					$data['output']['listings_result'][] = array('post_id' => (int) $listing->post_id, 'card_view' => $listing->card_view);
				}
			} else {
				$data['output']['listings_result_message'] = '<p class="wdk_alert wdk_alert-danger">'.esc_html__('Results not found', 'wpdirectorykit').'</p>';
			}

		}

		/* map results */
		if(isset($parameters['el_map_id'])) {
			wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns, $custom_parameters);
			$map_results = $this->listing_m->get_pagination(NULL, NULL, $public_where);

			$get_settings_el_map = $public_widgets['el_map']['loader'];
			$settings_el_map = $public_widgets['el_map']['settings'];

			foreach ($map_results as $key => $listing) {
				if (!$this->is_public_listing($listing)) continue;
				$listing->inner_marker = '';

				$pin_icon = "";
				$font_class = "";
				$font_icon = (wmvc_show_data('conf_custom_map_pin_icon', $settings_el_map)) ? esc_url($get_settings_el_map->generate_icon($settings_el_map['conf_custom_map_pin_icon'])) : '<i class="fa fa-home"></i>';
				$pin_icon = (isset( $settings_el_map['conf_custom_map_pin'])) ? esc_url($settings_el_map['conf_custom_map_pin']['url']) : '';
				
				if(wmvc_show_data('conf_hide_real_location', $settings_el_map) == 'yes') {
					$listing->lat = wdk_move_gps($listing->lat);
					$listing->lng = wdk_move_gps($listing->lng);
				}

				if(!empty(wmvc_show_data('category_id', $listing))){
					$category = $this->category_m->get_data(wmvc_show_data('category_id', $listing));
					if(wmvc_show_data('marker_image_id', $category, false, TRUE, TRUE)){
						$pin_icon = wdk_image_src($category, 'full', NULL,'marker_image_id');
					} else if(!empty(wmvc_show_data('font_icon_code', $category))) {
						$font_class = wmvc_show_data('font_icon_code', $category);
					} 
				} else {
					$font_class = "";
				}

				if($pin_icon){
					$listing->inner_marker = '<div class="wdk_marker-container wdk_marker-container-image"><img src="'.esc_url($pin_icon).'" alt=""></div>';
				}elseif($font_icon && empty($font_class)){
					$listing->inner_marker = '<div class="wdk_marker-container category_id_'.esc_js(wmvc_show_data('category_id', $listing)).'"><div class="front wdk_face">'.$font_icon.'</div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
				}else{
					$listing->inner_marker = '<div class="wdk_marker-container category_id_'.esc_attr(wmvc_show_data('category_id', $listing)).'"><div class="front wdk_face"><i class="'.esc_attr($font_class).'"></i></div><div class="wdk_marker-card"><div class="wdk_marker-arrow"></div></div></div>';
				}

				$data['output']['map_results'][] = array('post_id' => (int) $listing->post_id,
					'lat' => $listing->lat, 'lng' => $listing->lng, 'inner_marker' => $listing->inner_marker);
			}
		}

        $data['success'] = true;
        $this->output($data);

	}

	    
	public function suggestion_locations (&$output=NULL, $atts=array())
	{

		$this->load->load_helper('listing');
		$this->load->model('category_m');
		$this->load->model('location_m');
		$this->load->model('listing_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
		
		$data['results'] = array();
        $parameters = array();
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		$search_text = '';
		if(!empty($parameters['search']))
			$search_text = trim($parameters['search']);

		if(!empty($search_text)) {
			$this->db->select("location_title as value");
			$this->db->from($this->location_m->_table_name);
			$this->db->where(array("location_title LIKE '%".esc_sql($search_text)."%'" => NULL));

			$query = $this->db->get();
			if($this->db->num_rows() > 0) foreach($this->db->results() as $row) {
				$data['results'][] = $row->value;
			}
		}

		$data['success'] = true;
		$this->output($data);
	}
    
	public function suggestion_categories (&$output=NULL, $atts=array())
	{

		$this->load->load_helper('listing');
		$this->load->model('category_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
		
		$data['results'] = array();
        $parameters = array();
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		$search_text = '';
		if(!empty($parameters['search']))
			$search_text = trim($parameters['search']);

		if(!empty($search_text)) {
			$this->db->select("category_title as value");
			$this->db->from($this->category_m->_table_name);
			$this->db->where(array("category_title LIKE '%".esc_sql($search_text)."%'" => NULL));

			$query = $this->db->get();
			if($this->db->num_rows() > 0) foreach($this->db->results() as $row) {
				$data['results'][] = $row->value;
			}
		}

		$data['success'] = true;
		$this->output($data);
	}
    
	public function suggestion_db_field (&$output=NULL, $atts=array())
	{
        $this->load->load_helper('listing');
		$this->load->model('category_m');
		$this->load->model('location_m');
		$this->load->model('listing_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
		
		$data['results'] = array();
        $parameters = array();
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		$search_text = '';
		if(!empty($parameters['search']))
			$search_text = trim($parameters['search']);

		$search_field_id = '';
		if(!empty($parameters['field_id']))
			$search_field_id = (int) trim($parameters['field_id']);

		if(!empty($search_field_id) && wdk_field_option($search_field_id, 'field_type', false)
			&& wdk_field_option($search_field_id, 'is_visible_frontend', false)) {

			/* listings by title suggestion */
			$this->db->select("DISTINCT field_".$search_field_id."_".wdk_field_option($search_field_id, 'field_type', false)." as value");
			$this->db->from($this->db->prefix.'wdk_listings_fields');
			$this->restrict_public_suggestions($this->db, $this->db->prefix.'wdk_listings_fields');
			$this->db->where(array("field_".$search_field_id."_".wdk_field_option($search_field_id, 'field_type', false)." LIKE '%".esc_sql($search_text)."%'" => NULL));

			$query = $this->db->get();
			if($this->db->num_rows() > 0) foreach($this->db->results() as $row) {
				$data['results'][] = $row->value;
			}

			$field_values = wdk_field_option($search_field_id, 'values', '');
			$field_values = explode(',', $field_values);
			if(!empty($field_values)) {
				foreach($field_values as $value) {
					if(empty($value)) continue;
					$data['results'][] = $value;
				}
			}

		}

		$data['success'] = true;
		$this->output($data);

	}

	function select_2_ajax_field_db_suggestion($output="", $atts=array(), $instance=NULL)
    {
	    global $Winter_MVC_WDK;
		$Winter_MVC_WDK->load_helper('listing');
		$Winter_MVC_WDK->model('location_m');

        $data = array();
        $data['message'] = __('No message returned!', 'wpdirectorykit');
		$data['pagination'] = true;
        $results = array();

        $parameters = array();
		foreach ($_POST as $key => $value) {
			$parameters[$key] = sanitize_text_field($value);
		}

		$limit = 10;
		if(!empty($parameters['limit'])) {
			$limit = intval($parameters['limit']);
		}

		$offset = 0;
		if(!empty($parameters['page_result']) && $parameters['page_result'] > 1) {
			$offset = (intval($parameters['page_result']) - 1) * $limit;
		}

		$search_text = '';
		if(!empty($_POST['q']['term']))
			$search_text = sanitize_text_field($_POST['q']['term']);

		$search_field_id = '';
		if(!empty($parameters['field_id']))
			$search_field_id = (int) trim($parameters['field_id']);

		if(!empty($search_field_id) && wdk_field_option($search_field_id, 'field_type', false)
			&& wdk_field_option($search_field_id, 'is_visible_frontend', false)) {

			/* listings by title suggestion */
			$Winter_MVC_WDK->db->select("DISTINCT field_".$search_field_id."_".wdk_field_option($search_field_id, 'field_type', false)." as value");
			$Winter_MVC_WDK->db->from($Winter_MVC_WDK->db->prefix.'wdk_listings_fields');
			$this->restrict_public_suggestions($Winter_MVC_WDK->db, $Winter_MVC_WDK->db->prefix.'wdk_listings_fields');
			$Winter_MVC_WDK->db->where(array("field_".$search_field_id."_".wdk_field_option($search_field_id, 'field_type', false)." LIKE '%".esc_sql($search_text)."%'" => NULL));

			$query = $Winter_MVC_WDK->db->get();
			if($Winter_MVC_WDK->db->num_rows() > 0) foreach($Winter_MVC_WDK->db->results() as $row) {
			
					$results[] = [
                        'id'=> $row->value,
                        'text'=> $row->value,
                    ];
			}

			$field_values = wdk_field_option($search_field_id, 'values', '');
			$field_values = explode(',', $field_values);
			if(!empty($field_values)) {
				foreach($field_values as $value) {
					if(empty($value)) continue;
				
					 $results[] = [
                        'id'=> $value,
                        'text'=> $value,
                    ];
				}
			}

		}

		$data['success'] = true;
        
        $data['results'] = array_slice($results, $offset, $limit);
		if(count($results) >= $limit + $offset) {
			$data['pagination'] =[
				"more"=> true
			];
		}

        $data['success'] = true;
        $this->output($data);
    }
	    
	private function public_widget($parameters, $prefix, $widget_type) {
		$page_id = isset($parameters[$prefix . '_page_id']) ? absint($parameters[$prefix . '_page_id']) : 0;
		$page = $page_id ? get_post($page_id) : NULL;
		if (!$page || $page->post_status !== 'publish' || post_password_required($page)
			|| (!is_post_type_viewable($page->post_type) && $page->post_type !== 'elementor_library')) return false;
		if (isset($parameters[$prefix . '_type']) && $parameters[$prefix . '_type'] !== $widget_type) return false;
		$loader = new WdkGetElementSettings($page_id, $parameters[$prefix . '_id'], $widget_type);
		$widget = $loader->get_settings();
		if (!is_array($widget) || !isset($widget['id'], $widget['widgetType'], $widget['settings'])
			|| $widget['id'] !== $parameters[$prefix . '_id'] || $widget['widgetType'] !== $widget_type
			|| !is_array($widget['settings'])) return false;
		return array('loader' => $loader, 'settings' => $widget['settings']);
	}

	private function is_public_listing($listing) {
		return isset($listing->post_type, $listing->post_status, $listing->post_password)
			&& $listing->post_type === 'wdk-listing' && $listing->post_status === 'publish'
			&& $listing->post_password === '' && !empty($listing->is_activated) && !empty($listing->is_approved);
	}

	private function restrict_public_suggestions($db, $source_table) {
		$prefix = $db->prefix;
		$db->where("EXISTS (SELECT 1 FROM {$prefix}wdk_listings AS public_listing
			INNER JOIN {$prefix}posts AS public_post ON public_post.ID = public_listing.post_id
			WHERE public_listing.post_id = {$source_table}.post_id
			AND public_listing.is_activated = 1 AND public_listing.is_approved = 1
			AND public_post.post_type = 'wdk-listing' AND public_post.post_status = 'publish'
			AND public_post.post_password = '')", NULL);
	}

	private function get_fa_icons() {

		/* icons font Awesome 5 */

		return array("fab fa-500px","fab fa-accessible-icon","fab fa-accusoft","fas fa-address-book","far fa-address-book","fas fa-address-card","far fa-address-card","fas fa-adjust","fab fa-adn","fab fa-adversal","fab fa-affiliatetheme","fab fa-algolia",
		"fas fa-align-center","fas fa-align-justify","fas fa-align-left","fas fa-align-right","fab fa-amazon","fas fa-ambulance","fas fa-american-sign-language-interpreting","fab fa-amilia","fas fa-anchor","fab fa-android","fab fa-angellist",
		"fas fa-angle-double-down","fas fa-angle-double-left","fas fa-angle-double-right","fas fa-angle-double-up","fas fa-angle-down","fas fa-angle-left","fas fa-angle-right","fas fa-angle-up","fab fa-angrycreative","fab fa-angular","fab fa-app-store",
		"fab fa-app-store-ios","fab fa-apper","fab fa-apple","fab fa-apple-pay","fas fa-archive","fas fa-arrow-alt-circle-down","far fa-arrow-alt-circle-down","fas fa-arrow-alt-circle-left","far fa-arrow-alt-circle-left","fas fa-arrow-alt-circle-right",
		"far fa-arrow-alt-circle-right","fas fa-arrow-alt-circle-up","far fa-arrow-alt-circle-up","fas fa-arrow-circle-down","fas fa-arrow-circle-left","fas fa-arrow-circle-right","fas fa-arrow-circle-up","fas fa-arrow-down","fas fa-arrow-left",
		"fas fa-arrow-right","fas fa-arrow-up","fas fa-arrows-alt","fas fa-arrows-alt-h","fas fa-arrows-alt-v","fas fa-assistive-listening-systems","fas fa-asterisk","fab fa-asymmetrik","fas fa-at","fab fa-audible","fas fa-audio-description",
		"fab fa-autoprefixer","fab fa-avianex","fab fa-aviato","fab fa-aws","fas fa-backward","fas fa-balance-scale","fas fa-ban","fab fa-bandcamp","fas fa-barcode","fas fa-bars","fas fa-bath","fas fa-battery-empty","fas fa-battery-full",
		"fas fa-battery-half","fas fa-battery-quarter","fas fa-battery-three-quarters","fas fa-bed","fas fa-beer","fab fa-behance","fab fa-behance-square","fas fa-bell","far fa-bell","fas fa-bell-slash","far fa-bell-slash","fas fa-bicycle","fab fa-bimobject",
		"fas fa-binoculars","fas fa-birthday-cake","fab fa-bitbucket","fab fa-bitcoin","fab fa-bity","fab fa-black-tie","fab fa-blackberry","fas fa-blind","fab fa-blogger","fab fa-blogger-b","fab fa-bluetooth","fab fa-bluetooth-b","fas fa-bold","fas fa-bolt",
		"fas fa-bomb","fas fa-book","fas fa-bookmark","far fa-bookmark","fas fa-braille","fas fa-briefcase","fab fa-btc","fas fa-bug","fas fa-building","far fa-building","fas fa-bullhorn","fas fa-bullseye","fab fa-buromobelexperte","fas fa-bus","fab fa-buysellads",
		"fas fa-calculator","fas fa-calendar","far fa-calendar","fas fa-calendar-alt","far fa-calendar-alt","fas fa-calendar-check","far fa-calendar-check","fas fa-calendar-minus","far fa-calendar-minus","fas fa-calendar-plus","far fa-calendar-plus","fas fa-calendar-times",
		"far fa-calendar-times","fas fa-camera","fas fa-camera-retro","fas fa-car","fas fa-caret-down","fas fa-caret-left","fas fa-caret-right","fas fa-caret-square-down","far fa-caret-square-down","fas fa-caret-square-left","far fa-caret-square-left","fas fa-caret-square-right",
		"far fa-caret-square-right","fas fa-caret-square-up","far fa-caret-square-up","fas fa-caret-up","fas fa-cart-arrow-down","fas fa-cart-plus","fab fa-cc-amex","fab fa-cc-apple-pay","fab fa-cc-diners-club","fab fa-cc-discover","fab fa-cc-jcb","fab fa-cc-mastercard",
		"fab fa-cc-paypal","fab fa-cc-stripe","fab fa-cc-visa","fab fa-centercode","fas fa-certificate","fas fa-chart-area","fas fa-chart-bar","far fa-chart-bar","fas fa-chart-line","fas fa-chart-pie","fas fa-check","fas fa-check-circle","far fa-check-circle",
		"fas fa-check-square","far fa-check-square","fas fa-chevron-circle-down","fas fa-chevron-circle-left","fas fa-chevron-circle-right","fas fa-chevron-circle-up","fas fa-chevron-down","fas fa-chevron-left","fas fa-chevron-right","fas fa-chevron-up",
		"fas fa-child","fab fa-chrome","fas fa-circle","far fa-circle","fas fa-circle-notch","fas fa-clipboard","far fa-clipboard","fas fa-clock","far fa-clock","fas fa-clone","far fa-clone","fas fa-closed-captioning","far fa-closed-captioning",
		"fas fa-cloud","fas fa-cloud-download-alt","fas fa-cloud-upload-alt","fab fa-cloudscale","fab fa-cloudsmith","fab fa-cloudversify","fas fa-code","fas fa-code-branch","fab fa-codepen","fab fa-codiepie","fas fa-coffee","fas fa-cog",
		"fas fa-cogs","fas fa-columns","fas fa-comment","far fa-comment","fas fa-comment-alt","far fa-comment-alt","fas fa-comments","far fa-comments","fas fa-compass","far fa-compass","fas fa-compress","fab fa-connectdevelop","fab fa-contao",
		"fas fa-copy","far fa-copy","fas fa-copyright","far fa-copyright","fab fa-cpanel","fab fa-creative-commons","fas fa-credit-card","far fa-credit-card","fas fa-crop","fas fa-crosshairs","fab fa-css3","fab fa-css3-alt","fas fa-cube","fas fa-cubes",
		"fas fa-cut","fab fa-cuttlefish","fab fa-d-and-d","fab fa-dashcube","fas fa-database","fas fa-deaf","fab fa-delicious","fab fa-deploydog","fab fa-deskpro","fas fa-desktop","fab fa-deviantart","fab fa-digg","fab fa-digital-ocean",
		"fab fa-discord","fab fa-discourse","fab fa-dochub","fab fa-docker","fas fa-dollar-sign","fas fa-dot-circle","far fa-dot-circle","fas fa-download","fab fa-draft2digital","fab fa-dribbble","fab fa-dribbble-square","fab fa-dropbox","fab fa-drupal",
		"fab fa-dyalog","fab fa-earlybirds","fab fa-edge","fas fa-edit","far fa-edit","fas fa-eject","fas fa-ellipsis-h","fas fa-ellipsis-v","fab fa-ember","fab fa-empire","fas fa-envelope","far fa-envelope","fas fa-envelope-open","far fa-envelope-open",
		"fas fa-envelope-square","fab fa-envira","fas fa-eraser","fab fa-erlang","fab fa-etsy","fas fa-euro-sign","fas fa-exchange-alt","fas fa-exclamation","fas fa-exclamation-circle","fas fa-exclamation-triangle","fas fa-expand","fas fa-expand-arrows-alt",
		"fab fa-expeditedssl","fas fa-external-link-alt","fas fa-external-link-square-alt","fas fa-eye","fas fa-eye-dropper","fas fa-eye-slash","far fa-eye-slash","fab fa-facebook","fab fa-facebook-f","fab fa-facebook-messenger","fab fa-facebook-square",
		"fas fa-fast-backward","fas fa-fast-forward","fas fa-fax","fas fa-female","fas fa-fighter-jet","fas fa-file","far fa-file","fas fa-file-alt","far fa-file-alt","fas fa-file-archive","far fa-file-archive","fas fa-file-audio","far fa-file-audio",
		"fas fa-file-code","far fa-file-code","fas fa-file-excel","far fa-file-excel","fas fa-file-image","far fa-file-image","fas fa-file-pdf","far fa-file-pdf","fas fa-file-powerpoint","far fa-file-powerpoint","fas fa-file-video","far fa-file-video",
		"fas fa-file-word","far fa-file-word","fas fa-film","fas fa-filter","fas fa-fire","fas fa-fire-extinguisher","fab fa-firefox","fab fa-first-order","fab fa-firstdraft","fas fa-flag","far fa-flag","fas fa-flag-checkered","fas fa-flask","fab fa-flickr",
		"fab fa-fly","fas fa-folder","far fa-folder","fas fa-folder-open","far fa-folder-open","fas fa-font","fab fa-font-awesome","fab fa-font-awesome-alt","fab fa-font-awesome-flag","fab fa-fonticons","fab fa-fonticons-fi","fab fa-fort-awesome",
		"fab fa-fort-awesome-alt","fab fa-forumbee","fas fa-forward","fab fa-foursquare","fab fa-free-code-camp","fab fa-freebsd","fas fa-frown","far fa-frown","fas fa-futbol","far fa-futbol","fas fa-gamepad","fas fa-gavel","fas fa-gem","far fa-gem",
		"fas fa-genderless","fab fa-get-pocket","fab fa-gg","fab fa-gg-circle","fas fa-gift","fab fa-git","fab fa-git-square","fab fa-github","fab fa-github-alt","fab fa-github-square","fab fa-gitkraken","fab fa-gitlab","fab fa-gitter",
		"fas fa-glass-martini","fab fa-glide","fab fa-glide-g","fas fa-globe","fab fa-gofore","fab fa-goodreads","fab fa-goodreads-g","fab fa-google","fab fa-google-drive","fab fa-google-play","fab fa-google-plus","fab fa-google-plus-g",
		"fab fa-google-plus-square","fab fa-google-wallet","fas fa-graduation-cap","fab fa-gratipay","fab fa-grav","fab fa-gripfire","fab fa-grunt","fab fa-gulp","fas fa-h-square","fab fa-hacker-news","fab fa-hacker-news-square","fas fa-hand-lizard",
		"far fa-hand-lizard","fas fa-hand-paper","far fa-hand-paper","fas fa-hand-peace","far fa-hand-peace","fas fa-hand-point-down","far fa-hand-point-down","fas fa-hand-point-left","far fa-hand-point-left","fas fa-hand-point-right","far fa-hand-point-right","fas fa-hand-point-up",
		"far fa-hand-point-up","fas fa-hand-pointer","far fa-hand-pointer","fas fa-hand-rock","far fa-hand-rock","fas fa-hand-scissors","far fa-hand-scissors","fas fa-hand-spock","far fa-hand-spock","fas fa-handshake","far fa-handshake","fas fa-hashtag","fas fa-hdd",
		"far fa-hdd","fas fa-heading","fas fa-headphones","fas fa-heart","far fa-heart","fas fa-heartbeat","fab fa-hire-a-helper","fas fa-history","fas fa-home","fab fa-hooli","fas fa-hospital","far fa-hospital","fab fa-hotjar","fas fa-hourglass","far fa-hourglass",
		"fas fa-hourglass-end","fas fa-hourglass-half","fas fa-hourglass-start","fab fa-houzz","fab fa-html5","fab fa-hubspot","fas fa-i-cursor","fas fa-id-badge","far fa-id-badge","fas fa-id-card","far fa-id-card","fas fa-image","far fa-image","fas fa-images",
		"far fa-images","fab fa-imdb","fas fa-inbox","fas fa-indent","fas fa-industry","fas fa-info","fas fa-info-circle","fab fa-instagram","fab fa-internet-explorer","fab fa-ioxhost","fas fa-italic","fab fa-itunes","fab fa-itunes-note","fab fa-jenkins",
		"fab fa-joget","fab fa-joomla","fab fa-js","fab fa-js-square","fab fa-jsfiddle","fas fa-key","fas fa-keyboard","far fa-keyboard","fab fa-keycdn","fab fa-kickstarter","fab fa-kickstarter-k","fas fa-language","fas fa-laptop","fab fa-laravel",
		"fab fa-lastfm","fab fa-lastfm-square","fas fa-leaf","fab fa-leanpub","fas fa-lemon","far fa-lemon","fab fa-less","fas fa-level-down-alt","fas fa-level-up-alt","fas fa-life-ring","far fa-life-ring","fas fa-lightbulb","far fa-lightbulb",
		"fab fa-line","fas fa-link","fab fa-linkedin","fab fa-linkedin-in","fab fa-linode","fab fa-linux","fas fa-lira-sign","fas fa-list","fas fa-list-alt","far fa-list-alt","fas fa-list-ol","fas fa-list-ul","fas fa-location-arrow",
		"fas fa-lock","fas fa-lock-open","fas fa-long-arrow-alt-down","fas fa-long-arrow-alt-left","fas fa-long-arrow-alt-right","fas fa-long-arrow-alt-up","fas fa-low-vision","fab fa-lyft","fab fa-magento","fas fa-magic","fas fa-magnet",
		"fas fa-male","fas fa-map","far fa-map","fas fa-map-marker","fas fa-map-marker-alt","fas fa-map-pin","fas fa-map-signs","fas fa-mars","fas fa-mars-double","fas fa-mars-stroke","fas fa-mars-stroke-h","fas fa-mars-stroke-v","fab fa-maxcdn",
		"fab fa-medapps","fab fa-medium","fab fa-medium-m","fas fa-medkit","fab fa-medrt","fab fa-meetup","fas fa-meh","far fa-meh","fas fa-mercury","fas fa-microchip","fas fa-microphone","fas fa-microphone-slash","fab fa-microsoft","fas fa-minus",
		"fas fa-minus-circle","fas fa-minus-square","far fa-minus-square","fab fa-mix","fab fa-mixcloud","fab fa-mizuni","fas fa-mobile","fas fa-mobile-alt","fab fa-modx","fab fa-monero","fas fa-money-bill-alt","far fa-money-bill-alt",
		"fas fa-moon","far fa-moon","fas fa-motorcycle","fas fa-mouse-pointer","fas fa-music","fab fa-napster","fas fa-neuter","fas fa-newspaper","far fa-newspaper","fab fa-nintendo-switch","fab fa-node","fab fa-node-js","fab fa-npm","fab fa-ns8",
		"fab fa-nutritionix","fas fa-object-group","far fa-object-group","fas fa-object-ungroup","far fa-object-ungroup","fab fa-odnoklassniki","fab fa-odnoklassniki-square","fab fa-opencart","fab fa-openid","fab fa-opera","fab fa-optin-monster","fab fa-osi",
		"fas fa-outdent","fab fa-page4","fab fa-pagelines","fas fa-paint-brush","fab fa-palfed","fas fa-paper-plane","far fa-paper-plane","fas fa-paperclip","fas fa-paragraph","fas fa-paste","fab fa-patreon","fas fa-pause","fas fa-pause-circle",
		"far fa-pause-circle","fas fa-paw","fab fa-paypal","fas fa-pen-square","fas fa-pencil-alt","fas fa-percent","fab fa-periscope","fab fa-phabricator","fab fa-phoenix-framework","fas fa-phone","fas fa-phone-square","fas fa-phone-volume",
		"fab fa-pied-piper","fab fa-pied-piper-alt","fab fa-pied-piper-pp","fab fa-pinterest","fab fa-pinterest-p","fab fa-pinterest-square","fas fa-plane","fas fa-play","fas fa-play-circle","far fa-play-circle","fab fa-playstation","fas fa-plug","fas fa-plus",
		"fas fa-plus-circle","fas fa-plus-square","far fa-plus-square","fas fa-podcast","fas fa-pound-sign","fas fa-power-off","fas fa-print","fab fa-product-hunt","fab fa-pushed","fas fa-puzzle-piece","fab fa-python","fab fa-qq","fas fa-qrcode","fas fa-question",
		"fas fa-question-circle","far fa-question-circle","fab fa-quora","fas fa-quote-left","fas fa-quote-right","fas fa-random","fab fa-ravelry","fab fa-react","fab fa-rebel","fas fa-recycle","fab fa-red-river","fab fa-reddit","fab fa-reddit-alien",
		"fab fa-reddit-square","fas fa-redo","fas fa-redo-alt","fas fa-registered","far fa-registered","fab fa-rendact","fab fa-renren","fas fa-reply","fas fa-reply-all","fab fa-replyd","fab fa-resolving","fas fa-retweet","fas fa-road","fas fa-rocket","fab fa-rocketchat",
		"fab fa-rockrms","fas fa-rss","fas fa-rss-square","fas fa-ruble-sign","fas fa-rupee-sign","fab fa-safari","fab fa-sass","fas fa-save","far fa-save","fab fa-schlix","fab fa-scribd","fas fa-search","fas fa-search-minus","fas fa-search-plus",
		"fab fa-searchengin","fab fa-sellcast","fab fa-sellsy","fas fa-server","fab fa-servicestack","fas fa-share","fas fa-share-alt","fas fa-share-alt-square","fas fa-share-square","far fa-share-square","fas fa-shekel-sign","fas fa-shield-alt",
		"fas fa-ship","fab fa-shirtsinbulk","fas fa-shopping-bag","fas fa-shopping-basket","fas fa-shopping-cart","fas fa-shower","fas fa-sign-in-alt","fas fa-sign-language","fas fa-sign-out-alt","fas fa-signal","fab fa-simplybuilt","fab fa-sistrix",
		"fas fa-sitemap","fab fa-skyatlas","fab fa-skype","fab fa-slack","fab fa-slack-hash","fas fa-sliders-h","fab fa-slideshare","fas fa-smile","far fa-smile","fab fa-snapchat","fab fa-snapchat-ghost","fab fa-snapchat-square","fas fa-snowflake",
		"far fa-snowflake","fas fa-sort","fas fa-sort-alpha-down","fas fa-sort-alpha-up","fas fa-sort-amount-down","fas fa-sort-amount-up","fas fa-sort-down","fas fa-sort-numeric-down","fas fa-sort-numeric-up","fas fa-sort-up","fab fa-soundcloud",
		"fas fa-space-shuttle","fab fa-speakap","fas fa-spinner","fab fa-spotify","fas fa-square","far fa-square","fab fa-stack-exchange","fab fa-stack-overflow","fas fa-star","far fa-star","fas fa-star-half","far fa-star-half","fab fa-staylinked",
		"fab fa-steam","fab fa-steam-square","fab fa-steam-symbol","fas fa-step-backward","fas fa-step-forward","fas fa-stethoscope","fab fa-sticker-mule","fas fa-sticky-note","far fa-sticky-note","fas fa-stop","fas fa-stop-circle","far fa-stop-circle",
		"fab fa-strava","fas fa-street-view","fas fa-strikethrough","fab fa-stripe","fab fa-stripe-s","fab fa-studiovinari","fab fa-stumbleupon","fab fa-stumbleupon-circle","fas fa-subscript","fas fa-subway","fas fa-suitcase","fas fa-sun","far fa-sun",
		"fab fa-superpowers","fas fa-superscript","fab fa-supple","fas fa-sync","fas fa-sync-alt","fas fa-table","fas fa-tablet","fas fa-tablet-alt","fas fa-tachometer-alt","fas fa-tag","fas fa-tags","fas fa-tasks","fas fa-taxi","fab fa-telegram",
		"fab fa-telegram-plane","fab fa-tencent-weibo","fas fa-terminal","fas fa-text-height","fas fa-text-width","fas fa-th","fas fa-th-large","fas fa-th-list","fab fa-themeisle","fas fa-thermometer-empty","fas fa-thermometer-full","fas fa-thermometer-half",
		"fas fa-thermometer-quarter","fas fa-thermometer-three-quarters","fas fa-thumbs-down","far fa-thumbs-down","fas fa-thumbs-up","far fa-thumbs-up","fas fa-thumbtack","fas fa-ticket-alt","fas fa-times","fas fa-times-circle","far fa-times-circle",
		"fas fa-tint","fas fa-toggle-off","fas fa-toggle-on","fas fa-trademark","fas fa-train","fas fa-transgender","fas fa-transgender-alt","fas fa-trash","fas fa-trash-alt","far fa-trash-alt","fas fa-tree","fab fa-trello","fab fa-tripadvisor",
		"fas fa-trophy","fas fa-truck","fas fa-tty","fab fa-tumblr","fab fa-tumblr-square","fas fa-tv","fab fa-twitch","fab fa-twitter","fab fa-twitter-square","fab fa-typo3","fab fa-uber","fab fa-uikit","fas fa-umbrella","fas fa-underline",
		"fas fa-undo","fas fa-undo-alt","fab fa-uniregistry","fas fa-universal-access","fas fa-university","fas fa-unlink","fas fa-unlock","fas fa-unlock-alt","fab fa-untappd","fas fa-upload","fab fa-usb","fas fa-user","far fa-user",
		"fas fa-user-circle","far fa-user-circle","fas fa-user-md","fas fa-user-plus","fas fa-user-secret","fas fa-user-times","fas fa-users","fab fa-ussunnah","fas fa-utensil-spoon","fas fa-utensils","fab fa-vaadin","fas fa-venus",
		"fas fa-venus-double","fas fa-venus-mars","fab fa-viacoin","fab fa-viadeo","fab fa-viadeo-square","fab fa-viber","fas fa-video","fab fa-vimeo","fab fa-vimeo-square","fab fa-vimeo-v","fab fa-vine","fab fa-vk","fab fa-vnv",
		"fas fa-volume-down","fas fa-volume-off","fas fa-volume-up","fab fa-vuejs","fab fa-weibo","fab fa-weixin","fab fa-whatsapp","fab fa-whatsapp-square","fas fa-wheelchair","fab fa-whmcs","fas fa-wifi","fab fa-wikipedia-w","fas fa-window-close",
		"far fa-window-close","fas fa-window-maximize","far fa-window-maximize","fas fa-window-minimize","fas fa-window-restore","far fa-window-restore","fab fa-windows","fas fa-won-sign","fab fa-wordpress","fab fa-wordpress-simple",
		"fab fa-wpbeginner","fab fa-wpexplorer","fab fa-wpforms","fas fa-wrench","fab fa-xbox","fab fa-xing","fab fa-xing-square","fab fa-y-combinator","fab fa-yahoo","fab fa-yandex","fab fa-yandex-international","fab fa-yelp","fas fa-yen-sign","fab fa-yoast","fab fa-youtube","fas fa-school");
	} 
    
}

