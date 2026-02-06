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
class WdkListingCardThumbnail extends \Wdk\Elementor\Widgets\WdkFieldImages {
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
        return 'wdk-listing-card-thumbnail';
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
        return esc_html__('Wdk Listing Card Thumbnail', 'wpdirectorykit');
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

}


add_action('elementor/element/wdk-listing-card-thumbnail/tab_conf_main_section/before_section_end', function($element, $args) {
    $element->add_control(
        'cover_in_listing_link',
        [
            'label' => __( 'Cover in listing link', 'wpdirectorykit' ),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __( 'True', 'wpdirectorykit' ),
            'label_off' => __( 'False', 'wpdirectorykit' ),
            'return_value' => 'yes',
            'default' => '',
        ]
    );
}, 10, 2);

/* remove */
add_action(
    'elementor/element/before_section_end',
    function($section, $section_id, $args) {
        if( $section->get_name() == 'wdk-listing-card-thumbnail'/* && $section_id == 'section_style'*/ ) 
        {
            $section->remove_control('enable_js_gallery');
        }
    }, 10, 3
);
