<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       listing-themes.com
 * @since      1.0.0
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpdirectorykit
 * @subpackage Wpdirectorykit/admin
 * @author     listing-themes.com <dev@listing-themes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Wdk_Elementor {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The name of Elementor Category.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $elementor_category_name = 'wdk-elementor';
    private $elementor_category_name_listing_preview = 'wdk-elementor-listing-preview';
    private $elementor_category_name_listing_preview_sliders = 'wdk-elementor-listing-preview-sliders';
    

    /**
     * The name Elementors Custom Controls.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $custom_controls = [
		WPDIRECTORYKIT_PATH.'elementor-elements/classes/includes/controls/wdk-post-filter.php'=>'Wdk\Includes\Controls\Wdk_Post_Filter',
		WPDIRECTORYKIT_PATH.'elementor-elements/classes/includes/controls/wdk-field-selector.php'=>'Wdk\Includes\Controls\Wdk_Field_Selector',
	];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name='wpdirectorykit', $version='1.0' ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;


        add_action( 'elementor/controls/register', array( $this, 'add_controls' ) );

		add_action( 'wp_ajax_wdk_handle_live_editor', array( $this, 'wdk_handle_live_editor' ) );
		add_action( 'wp_ajax_wdk_update_filter', array( $this, 'get_posts_list' ) );


        add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'load_live_editor_modal' ) );
        add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'after_enqueue_scripts' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpdirectorykit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpdirectorykit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_style( 'font-awesome' );
	}
	
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpdirectorykit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpdirectorykit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'wdk-elementor-main', plugin_dir_url( __FILE__ ) . 'assets/js/wdk-main.js', array(), $this->version, true );

    }

	/**
	 * Load class/script files
	 *
	 * @since    1.0.0
	 */
	public function loader() {
        
    }

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes() {
		require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-elementor-base.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-field-label.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-field-value.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-field-images.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-field-files.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-field-files-list.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-field-icon.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-agents-listings.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-similar-listings.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-related-listings.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-related-listings-table.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-locations-grid.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-categories-grid-cover.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-locations-grid-cover.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-categories-grid.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-categories-list.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-categories-tree.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-locations-tree.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-categories-tree-top.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-locations-list.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listings-results.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listings-carousel.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-slider.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-categories-carousel.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-locations-carousel.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-map.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-search.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-fields-section.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listings-list.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-map.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-agent.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-agent-field.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-agent-avatar.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-button.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-button-login.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-button-addlisting.php';

        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-carousel.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-sliders.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-sliders-more-grid-images.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-sliders-grid-images.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-listing-sliders-carousel.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-last-search.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-implode-shortcode.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-tabs.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-language-switcher.php';
        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-button-share.php';

        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_search_popup')) {
            require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-search-popup.php';
        }

		do_action('wpdirectorykit/elementor-elements/includes');
    }
   
	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget() {

        $listing_page_id = get_option('wdk_listing_page');

        if(!empty($listing_page_id))
        {
			$post_data = NULL;
			if(isset($_GET['post']))
            	$post_data = get_post(intval($_GET['post']));
            if(
                (isset($_GET['post']) && $_GET['post'] == $listing_page_id) ||
                (isset($post_data->post_type) && $post_data->post_type == 'elementor_library')
            )
            {
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldLabel');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldValue');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldFiles');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldFilesList');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldImages');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldIcon');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingAgentsListings');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingSimilarListings');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingRelatedListingsTable');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingRelatedListings');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingSlider');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingFieldsSection');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingMap');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListinAgent');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingAgentField');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingAgentAvatar');

				$this->add_widget('Wdk\Elementor\Widgets\WdkCoolListingCarousel');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSlidersCarousel');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSlidersGridMoreImages');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSlidersGridImages');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSliders');
				do_action('wpdirectorykit/elementor-elements/register_widget/listing', $this);
            } 
            else if(isset($_GET['post']))
            {

            }
            else
            {
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldLabel');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldValue');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldFiles');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldFilesList');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldImages');
                $this->add_widget('Wdk\Elementor\Widgets\WdkFieldIcon');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingAgentsListings');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingSimilarListings');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingRelatedListingsTable');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingRelatedListings');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingSlider');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingFieldsSection');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingMap');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListinAgent');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingAgentField');
                $this->add_widget('Wdk\Elementor\Widgets\WdkListingAgentAvatar');

				$this->add_widget('Wdk\Elementor\Widgets\WdkCoolListingCarousel');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSlidersCarousel');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSlidersGridMoreImages');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSlidersGridImages');
				$this->add_widget('Wdk\Elementor\Widgets\WdkListingSliders');
				do_action('wpdirectorykit/elementor-elements/register_widget/listing', $this);
            }

            //POST data[elementor_post_lock][post_ID]	
        }

		$this->add_widget('Wdk\Elementor\Widgets\WdkLocationsList');
		$this->add_widget('Wdk\Elementor\Widgets\WdkCategoriesList');
		$this->add_widget('Wdk\Elementor\Widgets\WdkCategoriesTree');
		$this->add_widget('Wdk\Elementor\Widgets\WdkCategoriesTreeTop');
		$this->add_widget('Wdk\Elementor\Widgets\WdkLocationsTree');
		$this->add_widget('Wdk\Elementor\Widgets\WdkListingsResults');
		$this->add_widget('Wdk\Elementor\Widgets\WdkListingsCarousel');
		$this->add_widget('Wdk\Elementor\Widgets\WdkCategoriesCarousel');
		$this->add_widget('Wdk\Elementor\Widgets\WdkLocationsCarousel');
		$this->add_widget('Wdk\Elementor\Widgets\WdkCategoriesGrid');
		$this->add_widget('Wdk\Elementor\Widgets\WdkLocationsGrid');
		$this->add_widget('Wdk\Elementor\Widgets\WdkCategoriesGridCover');
		$this->add_widget('Wdk\Elementor\Widgets\WdkLocationsGridCover');
		$this->add_widget('Wdk\Elementor\Widgets\WdkSearch');
		$this->add_widget('Wdk\Elementor\Widgets\WdkMap');
		$this->add_widget('Wdk\Elementor\Widgets\WdkListingsList');
		$this->add_widget('Wdk\Elementor\Widgets\WdkButton');
		$this->add_widget('Wdk\Elementor\Widgets\WdkButtonLogin');
		$this->add_widget('Wdk\Elementor\Widgets\WdkButtonAddlisting');
		$this->add_widget('Wdk\Elementor\Widgets\WdkLastSearch');
		$this->add_widget('Wdk\Elementor\Widgets\WdkImplodeShortcode');
		$this->add_widget('Wdk\Elementor\Widgets\WdkTabs');
		$this->add_widget('Wdk\Elementor\Widgets\WdkLanguageSwitcher');
		$this->add_widget('Wdk\Elementor\Widgets\WdkButtonShare');

        if(wdk_get_option('wdk_experimental_features') && wdk_get_option('wdk_experimental_search_popup')) {
            $this->add_widget('Wdk\Elementor\Widgets\WdkSearchPopup');
        }

		do_action('wpdirectorykit/elementor-elements/register_widget', $this);
    }
        
    public function add_widget($class = ''){
        if(class_exists($class))
        {
            $object = new $class();
            \Elementor\Plugin::instance()->widgets_manager->register( $object );
        };
    }

        
	/**
	 * Registers Custom Controls.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function add_controls() {


		$control_manager = \Elementor\Plugin::instance();

        $this->custom_controls = apply_filters('wpdirectorykit/elementor/custom_controls', $this->custom_controls);

		foreach ($this->custom_controls as $include_file => $class) {
			if(empty($class) || !file_exists($include_file)) continue;
           
            require_once $include_file;
            $control_manager->controls_manager->register( new $class() );
		}

	}

    /**
     * Register Widget
     *
     * @since 1.0.0
     *
     * @access private
     */
    private function register_modules() {
        
    }  
         

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered() {
		$this->includes();
		$this->register_widget();
		$this->register_modules();
	}

	/**
	 * Add elementor category
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */  
    function load_category( $elements_manager ) {
        $elements_manager->add_category(
            $this->elementor_category_name,
            [
                'title' => esc_html__( 'Wdk', 'wpdirectorykit' ),
                'icon' => 'fa fa-plug',
            ]
        );

        $elements_manager->add_category(
            $this->elementor_category_name_listing_preview,
            [
                'title' => esc_html__( 'Wdk Listing Preview', 'wpdirectorykit' ),
                'icon' => 'fa fa-plug',
            ]
        );

        $elements_manager->add_category(
            $this->elementor_category_name_listing_preview_sliders,
            [
                'title' => esc_html__( 'Wdk Listing Sliders', 'wpdirectorykit' ),
                'icon' => 'fa fa-plug',
            ]
        );
    }

/**
	 * Handle Live Editor Modal.
	 *
	 * @access public
	 * @since 4.8.10
	 */
	public function wdk_handle_live_editor() {

		check_ajax_referer( 'wdk-live-editor', 'security' );

		if ( ! isset( $_POST['post_id'] ) ) {
			wp_send_json_error();
		}

		$post_id  = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
		$meta_input = array(
			'_elementor_edit_mode'     => 'builder',
			'_elementor_template_type' => 'page',
			'_wp_page_template'        => 'elementor_canvas',
		);

		$post = null;
		
		if(!empty($post_id))
			$post = get_post( $post_id );

		if ( empty( $post ) ) { // create a new one.

			$post_title = 'WDK Template | #' . substr( md5( rand(0000,9999) ), 0, 4 );

			$params = array(
				'post_content' => '',
				'post_type'    => 'elementor_library',
				'post_title'   => $post_title,
				'post_status'  => 'publish',
				'meta_input'   => $meta_input,
			);

			$post_id = wp_insert_post( $params );

		} else {
			$post_id    = $post->ID;
			$post_title = $post->post_title;
		}

		$edit_url = get_admin_url() . '/post.php?post=' . $post_id . '&action=elementor';

		$result = array(
			'url'   => $edit_url,
			'id'    => $post_id,
			'title' => $post_title,
		);

		wp_send_json_success( $result );
	}


	/**
	 * Get posts list
	 *
	 * Get posts list array
	 *
	 * @since 4.2.8
	 * @access public
	 */
	public static function get_posts_list() {

		check_ajax_referer( 'wdk-blog-widget-nonce', 'nonce' );

		$post_type = isset( $_POST['post_type'] ) ? wp_unslash( $_POST['post_type'] ) : '';

		$post_type = array_map( 'sanitize_text_field', $post_type );

		if ( empty( $post_type ) ) {
			wp_send_json_error( __( 'Empty Post Type.', 'premium-addons-for-elementor' ) );
		}

		$list = get_posts(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => -1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		$options = array();

		if ( ! empty( $list ) && ! is_wp_error( $list ) ) {
			foreach ( $list as $post ) {
				$key =  in_array( 'elementor_library', $post_type, true ) ? $post->post_title : $post->ID;
				$options[ $key ] = $post->post_title;
			}
		}

		wp_send_json_success( wp_json_encode( $options ) );
	}

	/**
	 * Load Live Editor Modal.
	 * Puts live editor popup html into the editor.
	 *
	 * @access public
	 * @since 4.8.10
	 */
	public function load_live_editor_modal() {
		ob_start();
		include_once WPDIRECTORYKIT_PATH . 'elementor-elements/classes/includes/live-editor-modal.php';
		$output = ob_get_contents();
		ob_end_clean();
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * After Enqueue Scripts
	 *
	 * Loads editor scripts for our controls.
	 *
	 * @access public
	 * @return void
	 */
	public function after_enqueue_scripts() {

        wp_enqueue_script( 'wdk-elementor-live-editor', WPDIRECTORYKIT_URL . 'elementor-elements/classes/includes/js/live-editor.js', array(), $this->version, true );

		wp_localize_script( 'wdk-elementor-live-editor', 'liveEditor', array(
			'ajaxurl' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'   => wp_create_nonce( 'wdk-live-editor' ),
		));

		wp_enqueue_script(
			'wdk-controls-handlers',
			 WPDIRECTORYKIT_URL . 'elementor-elements/classes/includes/js/controls-handlers.js',
			array( 'elementor-editor', 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			'wdk-controls-handlers',
			'PremiumSettings',
			array(
				'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'        => wp_create_nonce( 'wdk-blog-widget-nonce' ),
			)
		);
	}

	/**
	 * Loads plugin icons font
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_editor_styles() {

		$theme = 'dark';

		wp_enqueue_style(
			'wdk-elementor-live-editor',
			WPDIRECTORYKIT_URL . 'elementor-elements/classes/includes/css/wdk-elementor-live-editor.css',
			array(),
			$this->version
		);

		// Enqueue required style for Elementor dark UI Theme.
		if ( 'dark' == $theme ) {
			wp_add_inline_style(
				'wdk-elementor-live-editor',
				'.elementor-panel .elementor-control-section_pa_docs .elementor-panel-heading-title.elementor-panel-heading-title,
				.elementor-control-raw-html.editor-pa-doc a {
					color: #e0e1e3 !important;
				}
				[class^="pa-"]::after,
				[class*=" pa-"]::after {
					color: #aaa;
                    opacity: 1 !important;
				}
                .wdk-promotion-dialog .wdk-promotion-btn {
                    background-color: #202124 !important
                }'
			);
		}
	}

    /**
     * Start Elementor Addon
     */
    public function run() { 

        require_once plugin_dir_path( __FILE__ ) . 'classes/wdk-elementor-base.php';

		add_action( 'wp_enqueue_scripts',[ $this, 'enqueue_styles' ]);
		add_action( 'wp_enqueue_scripts',[ $this, 'enqueue_scripts' ]);

        add_action( 'elementor/elements/categories_registered', [ $this, 'load_category' ] );
        add_action( 'elementor/widgets/register', [ $this, 'on_widgets_registered' ] );

		do_action('wpdirectorykit/elementor-elements/run');

        /* load module */
        $this->loader();
		//wmvc_dump($meta);
    }
	 

}

/* Init lib after elementor loaded */
add_action( 'elementor/init', function() {
	$wdk_elementor = new Wdk_Elementor( );
	$wdk_elementor->run();
	do_action('wpdirectorykit/elementor-elements/init');
}, 1);

/* example extension for wdk elements 
add_action('wpdirectorykit/elementor-elements/includes', function(){
	require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-contact-form.php';
});

add_action('wpdirectorykit/elementor-elements/register_widget/listing', function(){
	$object = new Wdk\Elementor\Widgets\WdkContactFormExt();
	\Elementor\Plugin::instance()->widgets_manager->register( $object );
});
*/

/* load extension */

add_action('wpdirectorykit/elementor-elements/register_widget/listing', function(){
	add_action('eli/includes', function(){
		require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-contact-form.php';
	});

	add_action('eli/register_widget', function(){
		$object = new Wdk\Elementor\Extensions\WdkContactFormExt();
		\Elementor\Plugin::instance()->widgets_manager->register( $object );
	});

});


if((get_option('wdk_experimental_features') && get_option('wdk_experimental_listing_card_elementor_layout'))) {
    add_action('wpdirectorykit/elementor-elements/register_widget', function(){
        $card_elements= [
            'wdk-listing-card-button' => 'WdkListingCardButton',
            'wdk-listing-card-field-value' => 'WdkListingCardFieldValue',
            'wdk-listing-card-field-icon' => 'WdkListingCardFieldIcon',
            'wdk-listing-card-field-label' => 'WdkListingCardFieldLabel',
            'wdk-listing-card-thumbnail' => 'WdkListingCardThumbnail',
        ];
        foreach ($card_elements as $file => $object_name) {
            if(!file_exists(WPDIRECTORYKIT_PATH . '/elementor-extensions/'.$file.'.php')) continue;
            
            require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/'.$file.'.php';
            $class_name = 'Wdk\\Elementor\\Extensions\\' . $object_name;
            $object = new $class_name();
            \Elementor\Plugin::instance()->widgets_manager->register( $object );
        }
    });
}



require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-ajax-handler.php';
$object = new Wdk\Elementor\Extensions\AjaxHandler();

require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-hider.php';
require_once WPDIRECTORYKIT_PATH . '/elementor-extensions/class-hider-addons.php';
