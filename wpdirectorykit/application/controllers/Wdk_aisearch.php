<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly;

class Wdk_aisearch extends Winter_MVC_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        $this->load->model('field_m');

        $this->data['fields'] = $this->field_m->get();
        $this->data['selected_fields'] = [];

        $this->data['selected_fields'] = get_option('wdk_ai_search_fields', []);

        if (!is_array($this->data['selected_fields'])) {
            $this->data['selected_fields'] = [];
        }

        $this->load->view('wdk_aisearch/aisearch_edit', $this->data);
    }
    
}
