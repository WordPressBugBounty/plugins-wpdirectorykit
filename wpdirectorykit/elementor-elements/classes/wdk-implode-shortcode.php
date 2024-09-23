<?php

namespace Wdk\Elementor\Widgets;

use Wdk\Elementor\Widgets\WdkElementorBase;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Typography;
use Elementor\Editor;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Core\Schemes;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * @since 1.1.0
 */
class WdkImplodeShortcode extends WdkElementorBase {

    public $field_id = NULL;
    public $fields_list = array();

    public function __construct($data = array(), $args = null) {

        \Elementor\Controls_Manager::add_tab(
            'tab_conf',
            esc_html__('Settings', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_layout',
            esc_html__('Layout', 'wpdirectorykit')
        );

        \Elementor\Controls_Manager::add_tab(
            'tab_content',
            esc_html__('Main', 'wpdirectorykit')
        );

        add_action( 'elementor/editor/after_enqueue_styles', function()
        {
            wp_add_inline_style( 'elementor-editor', '.elementor-control-content select option[value*="section"]{color:#fff;background:#000}');
        } ); 
        
		if ($this->is_edit_mode_load()) {
            $this->enqueue_styles_scripts();
        }

        parent::__construct($data, $args);

    }

    /**
     * Retrieve the widget name.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'wdk-implode-shortcode';
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Wdk Implode Shortcode', 'wpdirectorykit');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.1.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-post-title';
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function register_controls() {
        $this->generate_controls_conf();
        
        $this->insert_pro_message('1');
        parent::register_controls();
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.1.0
     *
     * @access protected
     */
    protected function render() {
        parent::render();
        
        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();
        
        $shortcode = $this->data['settings']['shortcode'];
        $profile_id = wdk_get_profile_page_id();
        if(stripos($shortcode, '{profile_id}') !== false && $profile_id){
            $shortcode= str_replace('{profile_id}', $profile_id, $shortcode);
        }
        
        global $wdk_listing_id;
        if(stripos($shortcode, '{listing_id}') !== false && isset($wdk_listing_id)){
            $shortcode= str_replace('{listing_id}', $wdk_listing_id, $shortcode);
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            echo $shortcode; 
        } else {
            return do_shortcode($shortcode); 
        }

    }


    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control(
            'shortcode',
            [
                'label' => __( 'Shortcode', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'description' => '<span style="word-break: break-all;">'.__( 'Special vars:', 'wpdirectorykit' ).
                                    '<br> {profile_id} - replaced on profile id'.
                                    '<br> {listing_id} - replaced on listing id'.
                                '</span>',
            ]
        );
        
        $this->end_controls_section();
    }
}
