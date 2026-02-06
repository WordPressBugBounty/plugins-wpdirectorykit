<?php

namespace Wdk\Elementor\Extensions;
        

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
class WdkListingCardButton extends \Wdk\Elementor\Widgets\WdkButton {
    public $field_types = array();

    public function __construct($data = array(), $args = null) {
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
        return 'wdk-listing-card-button';
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
        return esc_html__('Wdk Listing Card Button', 'wpdirectorykit');
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

        $this->data['id_element'] = $this->get_id();
        $this->data['settings'] = $this->get_settings();

        $enable = false;
        if(wmvc_show_data('event_is_login_all', $this->data['settings'])) {
            if(is_user_logged_in()){
                $enable = true;
            }
        }

        if(wmvc_show_data('event_is_not_login', $this->data['settings'])) {
            if(!is_user_logged_in()){
                $enable = true;
            }
        }

        if(wmvc_show_data('event_is_login_custom_user_type', $this->data['settings'])) {
            if(is_user_logged_in()){
                foreach ($this->data['settings']['event_is_login_custom_user_type_list'] as $user_type) {
                    if(wmvc_user_in_role($user_type['role'])) {
                        $enable = true;
                    }
                }
            }
        }

        global $wdk_listing_id;
        if(wdk_is_listing_page_enabled() && isset($wdk_listing_id)) {
            $this->data['settings']['link_url'] = get_permalink($wdk_listing_id);
        }

        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
        } else {
            if(!$enable) {
                return false;
            }
        }

        echo $this->view('wdk-button', $this->data);
        
    }

    protected function generate_controls_conf() {

        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );
        $this->add_control(
            'conf_pagination_enable',
            [
                'label' => __( 'Pagination', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'wpdirectorykit' ),
                'label_off' => __( 'Hide', 'wpdirectorykit' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'per_page',
            [
                'label' => __( 'Limit Results (Per page)', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'default' => 10,
            ]
        );

        $this->add_control(
			'hide_profiles_with_no_listings',
			[
				'label' => __( 'Hide Profiles Without Listings', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'wpdirectorykit' ),
				'label_off' => __( 'Show', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
                'separator' => 'before',
			]
		);

        $this->end_controls_section();
    }
}

/* remove */
add_action(
    'elementor/element/before_section_end',
    function($section, $section_id, $args) {
        if( $section->get_name() == 'wdk-membership-class-contact-form'/* && $section_id == 'section_style'*/ ) 
        {
            $section->remove_control('mail_data_to_email');
        }
    }, 10, 3
);
