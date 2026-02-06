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

class WdkLanguageSwitcher extends WdkElementorBase {

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

		if (method_exists($this, 'is_edit_mode_load') && $this->is_edit_mode_load()) {
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
        return 'wdk-language-switcher';
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
        return esc_html__('Wdk Language Switcher', 'wpdirectorykit');
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
        return 'eicon-export-kit';
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

        $this->data['langs'] = wdk_get_languages();
        $this->data['is_edit_mode']= false;          
        if(Plugin::$instance->editor->is_edit_mode()){
            $this->data['is_edit_mode']= true;
        } else {
            if(empty( $this->data['langs'])) return false;
        }

        $layout = 'drop';

        if($this->data['settings']['enable_list_layout'] == 'yes')
            $layout = 'list';

        echo $this->view('wdk-language-switcher-'.$layout, $this->data);
    }

    private function generate_controls_conf() {
        $this->start_controls_section(
            'tab_conf_main_section',
            [
                'label' => esc_html__('Main', 'wpdirectorykit'),
                'tab' => '1',
            ]
        );

        $this->add_control (
			'enable_list_layout',
			[
				'label' => __( 'Enable List Layout', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'wpdirectorykit' ),
				'label_off' => __( 'Show', 'wpdirectorykit' ),
				'return_value' => 'yes',
				'default' => '',
			]
		);

        $this->add_responsive_control (
			'list_layout_column',
			[
				'label' => __( 'Show in one column', 'wpdirectorykit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Hide', 'wpdirectorykit' ),
				'label_off' => __( 'Show', 'wpdirectorykit' ),
				'return_value' => 'flex-direction:column',
				'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'enable_list_layout',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-language-switcher-list' => '{{VALUE}};',
                ],
			]
		);

        
        $this->add_responsive_control(
            'row_gap',
            [
                'label' => esc_html__('Rows Gap', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-language-switcher-list' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'enable_list_layout',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'сol_gap',
            [
                'label' => esc_html__('Column Gap', 'wpdirectorykit'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wdk-language-switcher-list' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'enable_list_layout',
                            'operator' => '==',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );

        $this->end_controls_section();

    }


    private function generate_controls_layout() {
    }

    private function generate_controls_styles() {
        $this->start_controls_section(
            'icon_section',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'tab' => '1',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'enable_list_layout',
                            'operator' => '!=',
                            'value' => 'yes',
                        ]
                    ],
                ],

            ]
        );+

        $this->add_control(
            'select_icon',
            [
                'label' => esc_html__('Icon', 'wpdirectorykit'),
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fa fa-angle-down',
                    'library' => 'solid',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'enable_list_layout',
                            'operator' => '!=',
                            'value' => 'yes',
                        ]
                    ],
                ],
            ]
        );

        $selectors = array(
            'normal' => '{{WRAPPER}} .wdk-language-switcher-drop .wdk-language-switcher-btn-toggle i',
            'hover'=>'{{WRAPPER}} .wdk-language-switcher-drop .wdk-language-switcher-btn-toggle%1$s i'
        );

        $this->generate_renders_tabs($selectors, 'icon_dynamic', ['margin','color','font-size','hover_animation']);
        $this->end_controls_section();


        $items = [
            [
                'key'=>'button',
                'label'=> esc_html__('Button Switcher', 'wpdirectorykit'),
                'selector'=>'.wdk-language-switcher-drop .wdk-language-switcher-btn-toggle',
                'options'=>'block',
            ],
            [
                'key'=>'dropdown_box',
                'label'=> esc_html__('DropDown Box', 'wpdirectorykit'),
                'selector'=>'.wdk-language-switcher-drop .wdk-language-switcher-menu',
                'options'=>'full',
            ],
            [
                'key'=>'items',
                'label'=> esc_html__('Items', 'wpdirectorykit'),
                'selector'=>'.wdk-language-switcher-drop .wdk-language-switcher-menu .item',
                'options'=>'full',
            ],
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => '1',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'enable_list_layout',
                                'operator' => '!=',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            if( $item ['key'] == 'items'){
                $selectors = array(
                    'normal' => '{{WRAPPER}} '.$item['selector'],
                );
                $this->generate_renders_tabs($selectors, $item['key'].'_dynamic_align', ['align']);
            }

            $selectors = array(
                'normal' => '{{WRAPPER}} '.$item['selector'],
                'hover'=>'{{WRAPPER}} '.$item['selector'].'%1$s'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options'],  ['align']);

            $this->end_controls_section();
            /* END special for some elements */
        }

        $items = [
            [
                'key'=>'items_list',
                'label'=> esc_html__('Items', 'wpdirectorykit'),
                'selector'=>'.wdk-language-switcher-list .wdk-language-switcher-menu .item',
                'options'=>'full',
            ],
        ];

        foreach ($items as $item) {
            $this->start_controls_section(
                $item['key'].'_section',
                [
                    'label' => $item['label'],
                    'tab' => '1',
                    'conditions' => [
                        'terms' => [
                            [
                                'name' => 'enable_list_layout',
                                'operator' => '==',
                                'value' => 'yes',
                            ]
                        ],
                    ],
                ]
            );

            $selectors = array(
                'normal' => '{{WRAPPER}} '.$item['selector'],
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic_align', ['align']);

            $selectors = array(
                'normal' => '{{WRAPPER}} '.$item['selector'],
                'hover'=>'{{WRAPPER}} '.$item['selector'].'%1$s'
            );
            $this->generate_renders_tabs($selectors, $item['key'].'_dynamic', $item['options'],  ['align']);

            $this->end_controls_section();
            /* END special for some elements */
        }
    }

    private function generate_controls_content() {

    }
            
    public function enqueue_styles_scripts() {
        wp_enqueue_style('wdk-language-switcher');
    }
}
