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
class WdkTabs extends WdkElementorBase {

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
        return 'wdk-tabs';
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
        return esc_html__('Wdk Tabs', 'wpdirectorykit');
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
        return 'eicon-tabs';
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
        $this->generate_controls_layout();
        $this->generate_controls_styles();
        $this->generate_controls_content();
        
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


        $this->data['is_edit_mode'] = false;          
        if(Plugin::$instance->editor->is_edit_mode()) {
            $this->data['is_edit_mode'] = true;
        }

        echo $this->view('wdk-tabs', $this->data); 
    }


    private function generate_controls_conf() {
        $this->start_controls_section(
			'section_config',
			[
				'label' => __( 'Configuration', 'wpdirectorykit' ),
			]
		);

        
        $this->add_control(
            'important_note',
            [
                'label' => '',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => wdk_sprintf(__( '<a href="%1$s" target="_blank">Have a question how to configure? Check our Guide</a>', 'wpdirectorykit' ), '//wpdirectorykit.com/wp/wpdirectorykit-elementor-element-wdk-tabs'),
                'content_classes' => 'wdk_elementor_hint',
            ]
        );
        
        $this->add_control(
            'important_note_2',
            [
                'label' => 'Guide:',
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<span style="word-break: break-all;">'.
                                '<br> 1. '.__( 'Create Tabs and set Tab Target', 'wpdirectorykit' ).''.
                                '<br> 2. '.__( 'Tab Target, set in CSS Classes of tabs and also class "wdk_tab_mobile"', 'wpdirectorykit' ).''.
                                '<br> 3. '.__( 'For choose first active tab, add for tab class "active"', 'wpdirectorykit' ).''.
                        '</span>',
                'content_classes' => 'wdk_elementor_hint',
            ]
        );
        

        $repeater = new Repeater();
        $repeater->start_controls_tabs( 'custom_fields_repeat' );
            
        $repeater->add_control(
            'tab_name',
            [
                'label' => __( 'Tab Name', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );
            
        $repeater->add_control(
            'tab_target',
            [
                'description' => __( 'put name for css of css class, then set this class for tab', 'wpdirectorykit' ),
                'label' => __( 'Tab Target', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );
            
        $repeater->add_control(
            'tab_css_class',
            [
                'label' => __( 'Special Class for Tab', 'wpdirectorykit' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );
            
        $repeater->add_control(
            'tab_icon',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
            ]
        );

        $repeater->end_controls_tabs();

                        
        $this->add_control(
            'tabs',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'label' => __( 'Field id', 'wpdirectorykit' ),
                'default' => [
                    [
                        'tab_name' => esc_html__('Tab 1', 'elementinvader-addons-for-elementor'),
                        'tab_target' => 'tab_1',
                        'tab_css_class' => '',
                        'tab_icon' => 'fas fa-list',
                    ],
                    [
                        'tab_name' => esc_html__('Tab 1', 'elementinvader-addons-for-elementor'),
                        'tab_target' => 'tab_1',
                        'tab_css_class' => '',
                        'tab_icon' => 'fas fa-list',
                    ],
                ],
                'title_field' => "{{{ tab_name }}}",
            ]
        );

        $this->end_controls_section();
    }

    private function generate_controls_layout() {
       
    }

    private function generate_controls_styles() {

        $items = [
            [
                'key'=>'wrapper',
                'label'=> esc_html__('Styles Wrapper', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-tabs',
                'options'=> ['margin','border','border_radius','padding','shadow','transition','background_group'],
            ],
            [
                'key'=>'btns',
                'label'=> esc_html__('Styles Buttons', 'wpdirectorykit'),
                'selector'=>'{{WRAPPER}} .wdk-tabs .tab',
                'selector_hover'=>'{{WRAPPER}} .wdk-tabs .tab%1$s',
                'options'=>['typo','color','border','border_radius','padding','shadow','transition','background_group'],
            ],
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => 'tab_form_styles',
                ]
            );

            if(!empty($item['selector_hide'])) {
                $this->add_responsive_control(
                    $item['key'].'_hide',
                    [
                        'label' => esc_html__( 'Hide Element', 'wdk-svg-map' ),
                        'type' => Controls_Manager::SWITCHER,
                        'none' => esc_html__( 'Hide', 'wdk-svg-map' ),
                        'block' => esc_html__( 'Show', 'wdk-svg-map' ),
                        'return_value' =>  'none',
                        'default' => ($item['key'] == 'field_button_reset' ) ? 'none':'',
                        'selectors' => [
                            $item['selector_hide'] => 'display: {{VALUE}};',
                        ],
                    ]
                );
            }

            $selectors = array();
            if(!empty($item['selector']))
                $selectors['normal'] = $item['selector'];

            if(!empty($item['selector_hover']))
                $selectors['hover'] = $item['selector_hover'];

            if(!empty($item['selector_focus']))
                $selectors['focus'] = $item['selector_hover'];
                
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options']);

            $this->end_controls_section();
            /* END special for some elements */
        }

    }

    private function generate_controls_content() {
        
    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style( 'wdk-tabs');
        wp_enqueue_script( 'wdk-tabs');
    }
}
