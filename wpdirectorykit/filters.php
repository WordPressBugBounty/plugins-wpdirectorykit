<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* fix for redirect */
add_action('init', 'wdk_do_output_buffer');
function wdk_do_output_buffer() {
    ob_start();
} 

/* add elementor body class */
add_filter( 'body_class', function( $classes ) {
    global $wp_query;
    global $wdk_listing_id;
    global $wdk_listing_page_id;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        $wdk_listing_page_id = get_option('wdk_listing_page');
        if(!empty($wdk_listing_page_id) && get_post_status($wdk_listing_page_id) =='publish')
        {
            return array_merge( $classes, array( 'elementor-page-'.$wdk_listing_page_id ) );
        }
    }
    
    return array_merge( $classes, array( 'class-name' ) );
} );

add_filter('the_content', 'wdk_content');  

function wdk_content( $content )
{  
    global $wp_query;
    global $wdk_listing_id;
    global $wdk_listing_page_id;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        $wdk_listing_page_id = get_option('wdk_listing_page');
        if(!empty($wdk_listing_page_id) && get_post_status($wdk_listing_page_id) =='publish')
        {
            global $Winter_MVC_WDK;
            $Winter_MVC_WDK->load_helper('listing');

            if(
                (
                    wdk_field_value('is_activated', $wdk_listing_id,false) && wdk_field_value('is_approved', $wdk_listing_id,false)) || 
                    (get_current_user_id() != 0 && get_current_user_id() == wdk_field_value('user_id_editor', $wdk_listing_id,false)) || 
                    (get_current_user_id() != 0 && wmvc_user_in_role('administrator'))
                ) {

                $Winter_MVC_WDK->model('listing_m');
                $Winter_MVC_WDK->listing_m->update_counter($wdk_listing_id);
                
                if(wdk_get_option('wdk_membership_is_enable_subscriptions') &&  wdk_get_option('wdk_membership_subscriptions_view_listing_enabled') && function_exists('run_wdk_membership')){
                    /* if required subscription with View details listing */

                    /* if not login, return alert for ask login */
                    if(is_user_logged_in()) {

                        global $Winter_MVC_wdk_membership;
                        $Winter_MVC_wdk_membership->model('subscription_m');
                        $Winter_MVC_wdk_membership->model('subscription_user_m');
                        $user_subscriptions_listings_view = $Winter_MVC_wdk_membership->subscription_user_m->get_pagination(NULL, FALSE, array('is_activated'=>1,'is_view_private_listings'=>1));

                        /* if not allow subscription, return alert for subscription on dash page */
                        if(empty($user_subscriptions_listings_view)){
                            $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Required subscription for "View Listings Details", please', 'wpdirectorykit').' <a href="'.esc_url(wdk_dash_url('dash_page=membership')).'">'.esc_html__('purchase subscription here', 'wpdirectorykit').'</a></p></div>';
                        }  else {
                            if(class_exists('Elementor\Plugin'))
                            {
                                $content = '';
                                $elementor_instance = Elementor\Plugin::instance();
                                $content = $elementor_instance->frontend->get_builder_content_for_display($wdk_listing_page_id);
                            }
                        }
                    } else {
                        $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Required subscription for "View Listings Details", please', 'wpdirectorykit').' <a href="'.wdk_login_url(wdk_server_current_url(), esc_html__('Login for open Listing', 'wpdirectorykit')).'">'.esc_html__('register or login here', 'wpdirectorykit').'</a></p></div>';
                    }

                } 
                /* if login required */
                elseif(get_option('wdk_membership_login_required_listing_preview') && !is_user_logged_in()){
                    if(get_option('wdk_membership_login_page')){
                        wp_redirect(wdk_url_suffix(get_permalink(get_option('wdk_membership_login_page')),'redirect_to='.urlencode(wdk_current_url()).'&custom_message='.urlencode(esc_html__('Please login for open Listing', 'wpdirectorykit'))));
                      //  exit();
                    }  else {
                        wp_redirect(wp_login_url(wdk_current_url()));
                    }
                }
                else {
                    if(class_exists('Elementor\Plugin'))
                    {
                        $content = '';

                        if(!wdk_field_value('is_approved', $wdk_listing_id, false) || !wdk_field_value('is_activated', $wdk_listing_id, false)) {
                            $content = '<div style="margin: 35px auto;max-width:768px;margin-bottom: 35px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Listing is not activated/approved and not visible for public', 'wpdirectorykit').'</p></div>';
                        }

                        $elementor_instance = Elementor\Plugin::instance();
                        $content .= $elementor_instance->frontend->get_builder_content_for_display($wdk_listing_page_id);
                        
                        $custom_css = '';

                        $wdk_listing_id;
                        $Winter_MVC_WDK->model('field_m');

                        $Winter_MVC_WDK->db->where(array('field_type !='=> 'SECTION'));
                        $fields = $Winter_MVC_WDK->field_m->get();

                        foreach($fields as $field) {
                            if(wdk_field_value ($field->idfield, $wdk_listing_id)) continue;

                            if(!empty($custom_css)) $custom_css .= ',';

                            $custom_css .= '.wdk_'.$field->idfield.'_hide_empty';
                        }

                        if(!wdk_field_value ('lat', $wdk_listing_id, false) || !wdk_field_value ('lng', $wdk_listing_id)) {
                            if(!empty($custom_css)) $custom_css .= ',';
                            $custom_css .= '.wdk_map_hide_empty';
                        }

                        if(!wdk_field_value ('is_featured', $wdk_listing_id, false)) {
                            if(!empty($custom_css)) $custom_css .= ',';
                            $custom_css .= '.wdk_is_featured_hide_empty';
                        }

                        $custom_css .= '{display: none !important}';

                        wp_enqueue_style('wdk-custom-inline', WPDIRECTORYKIT_URL.'public/css/custom-inline.css');
                        wp_add_inline_style( 'wdk-custom-inline', $custom_css);

                    }
                }

            } else {
                $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Listing missing or not activated', 'wpdirectorykit').', <a href="'.get_home_url().'">'.esc_html__('return to Homepage', 'wpdirectorykit').'</a></p></div>';
            }
        } else {
            $content = '<div style="margin: 35px auto;max-width:768px"><p class="wdk_alert wdk_alert-danger">'.esc_html__('Missing listing Preview Page', 'wpdirectorykit').', <a href="'.esc_url(get_admin_url()).'admin.php?page=wdk_settings&function=import_demo">'.esc_html__('Import demo Page', 'wpdirectorykit').'</a></p></div>';
        }

    }
    return $content;
}

function wdk_do_header() {
    // get all elementor pro builder conditions
    $pro_theme_builder_conditions = get_option('elementor_pro_theme_builder_conditions');
    $listing_page_id = get_option('wdk_listing_page');

    if(!empty($pro_theme_builder_conditions))
    {
        foreach($pro_theme_builder_conditions['header'] as $header_post_id => $header_on_pages)
        {
            foreach($header_on_pages as $pages)
            {
                $accepted_page_id = substr($pages, strrpos($pages, '/')+1);

                if($accepted_page_id == $listing_page_id)
                {
                    // render header content

                    $elementor_instance = \Elementor\Plugin::instance();
                    $content = $elementor_instance->frontend->get_builder_content_for_display($header_post_id);
            
                    echo wp_kses_post( $content );
                    return;
                }
            }
        }
    }
}

function wdk_do_footer() {
    // get all elementor pro builder conditions
    $pro_theme_builder_conditions = get_option('elementor_pro_theme_builder_conditions');
    $listing_page_id = get_option('wdk_listing_page');

    if(!empty($pro_theme_builder_conditions))
    {
        foreach($pro_theme_builder_conditions['footer'] as $footer_post_id => $footer_on_pages)
        {
            foreach($footer_on_pages as $pages)
            {
                $accepted_page_id = substr($pages, strrpos($pages, '/')+1);

                if($accepted_page_id == $listing_page_id)
                {
                    // render footer content

                    $elementor_instance = \Elementor\Plugin::instance();
                    $content = $elementor_instance->frontend->get_builder_content_for_display($footer_post_id);
            
                    echo wp_kses_post( $content );
                    return;
                }
            }
        }
    }
}

function wdk_do_body_class($classes)
{
    if(class_exists('Elementor\Plugin'))
    {
        $elementor_instance = \Elementor\Plugin::instance();
        $classes = $elementor_instance->frontend->body_class();
    }

    return $classes;
}

function wdk_page_template ($template) {
    global $wp_query;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $listing_page_id = get_option('wdk_listing_page');
        $template_elem = get_post_meta($listing_page_id, '_wp_page_template', true);

        if($template_elem == 'elementor_theme')
        {
            // for elementor pro theme builder basic support
            $template_elem = 'elementor_canvas';

            add_filter( 'body_class', 'wdk_do_body_class' );
            add_action( 'elementor/page_templates/canvas/before_content', 'wdk_do_header' , 0 );
            add_action( 'elementor/page_templates/canvas/after_content', 'wdk_do_footer' , 0 );
        }

        if($template_elem == 'elementor_canvas' || $template_elem == 'elementor_header_footer' ){
            /**
			 * @var \Elementor\Modules\PageTemplates\Module $page_templates_module
			 */
            if(class_exists('Elementor\Plugin'))
            {
                $page_templates_module =  Elementor\Plugin::instance()->modules_manager->get_modules( 'page-templates' );
                $template = $page_templates_module->get_template_path( $template_elem );
            }
        }
    }
    return $template;
}
add_filter ('single_template', 'wdk_page_template');

add_action('admin_bar_menu', 'wdk_add_toolbar_items', 100);
function wdk_add_toolbar_items($admin_bar){
    global $wp_query;
    if(isset($wp_query->post))
    if ($wp_query->post->post_type == 'wdk-listing') {
        $admin_bar->add_menu(array(
            'id'    => 'edit',
            'title' => __('Edit Listing', 'wpdirectorykit'),
            'href'  => admin_url('admin.php?page=wdk_listing&id='.$wp_query->post->ID),
        ));
    }
}

if(get_option('wdk_slug_listing_preview_page')){
    function wdk_custom_listing_preview_page_slug( $args, $post_type ) {
                
        if(get_option('wdk_slug_listing_preview_page_changed')){
            update_option('wdk_slug_listing_preview_page_changed', 0);
            flush_rewrite_rules();
        }

        /*item post type slug*/   
        if ( 'wdk-listing' === $post_type ) {
           $args['rewrite']['slug'] = get_option('wdk_slug_listing_preview_page');
        }
     
        return $args;
    }
    add_filter( 'register_post_type_args', 'wdk_custom_listing_preview_page_slug', 10, 2 );
}

// Hook example, custom page title for 
add_filter( 'document_title_parts', function( $title_parts_array ) {
    global $wp_query;
    if ( isset( $wp_query->post ) && $wp_query->post->post_type === 'wdk-listing' ) {
        $title_parts_array['title'] = wdk_listing_seo_title();
    }
    return $title_parts_array;
} );

/* seo wdk feature, wp overwrite wp_head */
add_action( 'wp_head', 'wdk_seo_metatags');
function wdk_seo_metatags(){
    global $wp_query;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        if($wdk_listing_id) {
            global $Winter_MVC_WDK;
            $Winter_MVC_WDK->model('field_m');
            $Winter_MVC_WDK->load_helper('listing');

            $seo_title = wp_get_document_title();

            echo '<meta name="title" content="'.esc_attr($seo_title).'">'.PHP_EOL;
            echo '<meta property="og:type" content="'.esc_attr(get_post_type()).'" />'.PHP_EOL;
            echo '<meta property="og:title"  content="'.esc_attr($seo_title).'" />'.PHP_EOL;

            if(get_option('wdk_seo_description')) {
                echo '<meta name="description" content="'.esc_attr(wp_trim_words(wp_strip_all_tags(wpautop(wdk_field_value(get_option('wdk_seo_description'), $wdk_listing_id))), 20)).'">'.PHP_EOL;
                echo '<meta property="og:description" content="'.esc_attr(wp_trim_words(wp_strip_all_tags(wpautop(wdk_field_value(get_option('wdk_seo_description'), $wdk_listing_id))), 20)).'">'.PHP_EOL;
            }
            
            if(get_option('wdk_seo_keywords')) {
                echo '<meta name="keywords" content="'.esc_attr(wp_trim_words(wp_strip_all_tags(wpautop(wdk_field_value(get_option('wdk_seo_keywords'), $wdk_listing_id))), 20)).'">'.PHP_EOL;
            }

            if(!in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
                echo '<meta property="og:image" content="'.esc_url(wdk_image_src(array('listing_images'=>wdk_field_value('listing_images', $wdk_listing_id)), 'full')).'" />'.PHP_EOL;
            }
        }
    }
}

/* disable some sw_seo feature if Yost seo plugin detected, for only for listing preview page and profile page */
add_action( 'template_redirect', 'wdk_disable_seo' );
function wdk_disable_seo() {
    global $wp_query;
    if(isset($wp_query->post))
    if($wp_query->post->post_type == 'wdk-listing')
    {
        if(in_array('wordpress-seo/wp-seo.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
            add_filter( 'wpseo_title', '__return_false');
            add_filter( 'wpseo_metadesc', '__return_false');
            add_filter( 'wpseo_opengraph_url', '__return_false');
            add_filter( 'wpseo_opengraph_title', '__return_false');
            add_filter( 'wpseo_opengraph_image', '__return_false');
            add_filter( 'wpseo_opengraph_type', '__return_false');
            add_filter( 'wpseo_opengraph_desc', '__return_false');
        }
    }
}

function wdk_yoast_presentation($presentation) {
    global $wp_query;
    if(isset($wp_query->post))
    if ($wp_query->post->post_type == 'wdk-listing')
    {
        $wdk_listing_id = $wp_query->post->ID;
        if($wdk_listing_id) {
            global $Winter_MVC_WDK;
            $Winter_MVC_WDK->model('field_m');
            $Winter_MVC_WDK->load_helper('listing');

            $presentation->open_graph_images = [
                [
                    'url' => esc_url(wdk_image_src(array('listing_images'=>wdk_field_value('listing_images', $wdk_listing_id)), 'full')),
                ]
            ];

        }
    }
    return $presentation;
}
add_filter('wpseo_frontend_presentation', 'wdk_yoast_presentation', 30);

add_filter( 'wp_kses_allowed_html', 'wpdirectorykit_wpkses_post_iframe', 10, 2 );
function wpdirectorykit_wpkses_post_iframe( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
	}
	return $tags;
}

add_action('wdk-membership/dash/homepage/widgets', function() {
    if(!current_user_can('edit_own_listings') && !wmvc_user_in_role('administrator')) return false;
    
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->model('listing_m');
    $total_items = $Winter_MVC_WDK->listing_m->total(array(), TRUE, get_current_user_id());
    if(function_exists('wdk_dash_url')){
    ?>
    <div class="wdk-col-12 wdk-col-md-3 wdk-membership-dash-widget_listings"> 
        <a href="<?php echo esc_url(wdk_dash_url('dash_page=listings'));?>" class="wdk-membership-dash-widget">
            <span class="wdk-content">
                <span class="icon"><span class="dashicons dashicons-location"></span></span>
            </span>
            <span class="wdk-side">
            <span class="title"><?php echo esc_html__('Listings','wpdirectorykit');?></span>
                <span class="wdk-count"><?php echo esc_html($total_items);?></span>
            </span>
        </a>
    </div>
<?php
    }
},1);

add_action('wdk-membership/dash/homepage/widgets', function() {
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->model('messages_m');
    $total_items = $Winter_MVC_WDK->messages_m->total_merge(array(), TRUE, get_current_user_id());
    if(function_exists('wdk_dash_url')){
    ?>
    <div class="wdk-col-12 wdk-col-md-3 wdk-membership-dash-widget_messsages"> 
        <a href="<?php echo esc_url(wdk_dash_url('dash_page=messages'));?>" class="wdk-membership-dash-widget">
            <span class="wdk-content">
                <span class="icon"><span class="dashicons dashicons-email"></span></span>
            </span>
            <span class="wdk-side">
            <span class="title"><?php echo esc_html__('Messages','wpdirectorykit');?></span>
                <span class="wdk-count"><?php echo esc_html($total_items);?></span>
            </span>
        </a>
    </div>
<?php
    }
});

add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );


/* detected in smart search location and replace to location id */
add_action( 'template_redirect', 'wdk_search_parameters' );
function wdk_search_parameters() {
    global $wp_query;
    if(!empty($_GET['field_search'])) {
        global $Winter_MVC_WDK;
        $Winter_MVC_WDK->model('location_m');
        $location = $Winter_MVC_WDK->location_m->get_by(array('location_title'=>sanitize_text_field(wp_unslash($_GET['field_search']))), TRUE);
        if($location) {
            $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
          
            $query_link = substr($url, strpos($url, '?')+1);
            $clear_link = substr($url, 0, strpos($url, '?'));

            $string_par = array();
            parse_str( $query_link, $string_par);
            unset($string_par['field_search']);
            $string_par['search_location'] = $location->idlocation;
            
            $new_url =  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http").'://'.$clear_link.'?'.http_build_query($string_par);
            wp_redirect($new_url);
            exit;        
        }

    }
   
}

add_filter( 'plugin_action_links_wpdirectorykit/wpdirectorykit.php', 'wdk_buy_link' );
function wdk_buy_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( get_admin_url().'admin.php?page=wdk_addons' );
	// Create the link.
	$settings_link = "<a style=\"color:rgb(0, 163, 42);font-weight:bold;\" href='$url'>" . __( 'Check Premium Features', 'wpdirectorykit') . '</a>';
	// Adds the link to the end of the array.
    $links[] = $settings_link;
	return $links;
}


add_filter( 'wpdirectorykit/listing/field/value', 'wdk_filter_field_format_date', 2, 10);
if(!function_exists('wdk_filter_field_format_date')) {
    function wdk_filter_field_format_date ($field_value = '', $field_id = NULL, $number_format = TRUE) {
        if(in_array($field_id, array('date', 'date-modified'))) {
           $field_value = wdk_get_date($field_value, TRUE);
        }
        
        return $field_value;
    }
}

	
// fix sitemap issue
function wdk_modify_sitemap_index($content) {
    // Modify the $content as needed
    // For example, replace & with &amp;
    $content = str_replace('&', '&amp;', $content);
    return $content;
}

// Hook the filter to the wpseo_sitemap_index filter hook
add_filter('wpseo_sitemap_index', 'wdk_modify_sitemap_index');

if(false) {
    // Hook example, custom page title for 
    add_filter( 'document_title_parts', function( $title_parts_array ) {
        global $wp_query;
        if(isset($wp_query->post))
        if($wp_query->post->post_type == 'wdk-listing')
        {
            $wdk_listing_id = $wp_query->post->ID;
            if($wdk_listing_id) {
                global $Winter_MVC_WDK;
                $Winter_MVC_WDK->model('field_m');
                $Winter_MVC_WDK->load_helper('listing');
                
                $title_parts_array['title'] = wp_strip_all_tags(wpautop(wdk_field_value('post_title', $wdk_listing_id).' - '.wdk_field_value('address', $wdk_listing_id)));
            }
        }
        return $title_parts_array;
    } );
}

add_filter('tiny_mce_before_init', function($init){
    if(get_option('wdk_disable_toolbar_on_content_editor')) {
        $init['wpautop'] = false;
        $init['toolbar'] = false;
    }

    return $init;
});

/*
*  configuration number_format_i18n
*/
add_filter('init', function($init){ 

    if(!get_option('wdk_disable_custom_muber_format'))
        if(get_option('wdk_number_format_decimal_point') || get_option('wdk_number_format_thousands_sep')) {
            global $wp_locale;
            if(get_option('wdk_number_format_decimal_point'))
                $wp_locale->number_format['decimal_point'] = get_option('wdk_number_format_decimal_point');
            
            if(get_option('wdk_number_format_thousands_sep'))
                $wp_locale->number_format['thousands_sep'] = get_option('wdk_number_format_thousands_sep');
        }
});

add_filter( 'wdk/listings/results', function($listings) {
    global $Winter_MVC_WDK;
    $Winter_MVC_WDK->model('field_m');
    $Winter_MVC_WDK->load_helper('listing');

    $listings_ids = array();
    foreach($listings as $listing) {
        $listings_ids[] = $listing->post_id;
    }

    if(!empty($listings_ids)) {
        $Winter_MVC_WDK->listing_m->update_listings_views($listings_ids);
    }

    return $listings;
} );

add_filter('body_class', function($classes){

    if(isset($_GET['popup'])){
        $classes[] = 'popup-mode';
    }

    return $classes;

});

add_action('init', function() {
    $required_version = '1.0.5';
    $plugin_file = 'wdk-membership/wdk-membership.php';

    if ( !file_exists(WP_PLUGIN_DIR .'/'. $plugin_file) ) {
        return;
    }

    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugin_data = get_plugin_data(WP_PLUGIN_DIR .'/'. $plugin_file);
    
    if ( is_plugin_active($plugin_file) && isset($plugin_data['Version']) && version_compare($plugin_data['Version'], $required_version, '<') ) {

        if ( isset($_GET['page']) && !empty($plugin_data['TextDomain']) && strpos($_GET['page'], $plugin_data['TextDomain']) === 0 ) {
            $message = '<span class="dashicons dashicons-warning"></span> &nbsp;&nbsp;' . esc_html__('Important!','wpdirectorykit') . '<br>';
            $message .= wdk_sprintf(
                __('%1$sYou need to update WDK Membership Addon to ensure compatibility with the latest plugin version. You can download the latest addons from your Gumroad account: %4$shttps://gumroad.com/library%3$s%2$s or update via freemius, depends where you purchased our premium plugin.
                %1$sIf you prefer to continue using older plugin versions, please note that this increases security risks and is not recommended. However, you can roll back to a previous version using the following plugin: %5$shttps://wordpress.org/plugins/wp-rollback/%3$s%2$s
                %1$sIf you experience any issues or need assistance, feel free to contact us through any available channel. We continue to provide fully human-based support: %6$shttps://wpdirectorykit.com/contact/%3$s%2$s','wpdirectorykit'),
                '<p>',
                '</p>',
                '</a>',
                '<a target="_blank" href="https://gumroad.com/library">',
                '<a target="_blank" href="https://wordpress.org/plugins/wp-rollback/">',
                '<a target="_blank" href="https://wpdirectorykit.com/contact/">'
            );
            wdk_notify_admin('wdk_notify_wpdirectorykit_required_version_' . $plugin_data['TextDomain'], $message, '', 'notice notice-error wdk-notice-upgrade');
        }
    }
});



add_action('admin_footer', function() {

    if (file_exists(WPDIRECTORYKIT_PATH . 'premium_functions.php')) {
        return;
    }
   
    if (!current_user_can('manage_options')) {
       return;
    }

    $user_id = get_current_user_id();
    $last_closed = (int) get_user_meta($user_id, '_wdk_admin_popup_closed', true);

    // 14 days = 1209600 seconds
    if ($last_closed && (time() - $last_closed) < 1209600) {
        return;
    }
?>
<div id="wdk-admin-popup">
    <div class="wdk-admin-popup-inner">
        <button id="wdk-admin-popup-close" class="wdk-admin-popup-close">&times;</button>

        <div class="wdk-popup-header">
            <h2>🚀 <?php echo esc_html__('Welcome to our free WP Directory Kit plugin!', 'wpdirectorykit'); ?></h2>
        </div>

        <div class="wdk-popup-content">

            <p>💬 <?php echo esc_html__('If you encounter any issues, please contact us via Telegram, WhatsApp, or email.', 'wpdirectorykit'); ?></p>

            <p class="wdk-contact">
                <strong>📩 <?php echo esc_html__('Telegram', 'wpdirectorykit'); ?>:</strong>
                <a href="https://t.me/wpdirectorykit" target="_blank">https://t.me/wpdirectorykit</a><br>

                <strong>📱 <?php echo esc_html__('WhatsApp', 'wpdirectorykit'); ?>:</strong>
                <a href="https://wa.me/+385959055516" target="_blank">https://wa.me/+385959055516</a>
            </p>

            <p class="wdk-links">
                ✨ <?php echo esc_html__('Check out our premium features here:', 'wpdirectorykit'); ?><br>
                <a href="https://wpdirectorykit.com/plugins/" target="_blank">https://wpdirectorykit.com/plugins/</a>
            </p>

            <p class="wdk-links">
                🎨 <?php echo esc_html__('and other cool designs here:', 'wpdirectorykit'); ?><br>
                <a href="https://wpdirectorykit.com/themes/" target="_blank">https://wpdirectorykit.com/themes/</a>
            </p>

            <p class="wdk-features">
                ⚙️ <?php echo esc_html__('We offer everything you need for a professional directory website, including login and registration, a front-end dashboard, agent search, individual agent pages, booking, multi-currency support, import tools, favorites, reviews, listing comparisons, SVG maps, live chat, and much more!', 'wpdirectorykit'); ?>
            </p>

            <p class="wdk-footer-note">
                ❤️ <?php echo esc_html__('Please consider supporting our work by purchasing one of our premium features — we’ll do our very best to assist you!', 'wpdirectorykit'); ?>
            </p>

             <!-- ACTION BUTTONS -->
             <div class="wdk-actions">
                <button class="wdk-btn wdk-dismiss wdk-admin-popup-close"><?php echo esc_html__('Not interested', 'wpdirectorykit'); ?></button>

                <a href="https://wpdirectorykit.com/plugins/" target="_blank" class="wdk-btn wdk-primary wdk-premium">
                    <?php echo esc_html__('Check premium plugins', 'wpdirectorykit'); ?>
                </a>
            </div>

        </div>
    </div>

    <?php
        $popup_css = '
        #wdk-admin-popup {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(6px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            opacity: 0;
            visibility: hidden;
            transition: all .3s ease;
        }
        #wdk-admin-popup.show {
            opacity: 1;
            visibility: visible;
        }
        .wdk-admin-popup-inner {
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            padding: 35px;
            width: 700px;
            max-width: calc(100% - 30px);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            transform: translateY(20px);
            transition: all .3s ease;
            position: relative;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            line-height: 1.6;
        }
        #wdk-admin-popup.show .wdk-admin-popup-inner {
            transform: translateY(0);
        }
        #wdk-admin-popup-close {
            position: absolute;
            top: 12px;
            right: 14px;
            border: none;
            background: transparent;
            font-size: 22px;
            cursor: pointer;
            opacity: .6;
        }
        #wdk-admin-popup-close:hover {
            opacity: 1;
        }
        .wdk-popup-header h2 {
            margin: 0 0 15px;
            font-size: 22px;
        }
        .wdk-popup-content p {
            margin: 12px 0;
            color: #444;
        }
        .wdk-contact {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 10px;
        }
        .wdk-links {
            background: #eef2ff;
            padding: 12px;
            border-radius: 10px;
        }
        .wdk-features {
            font-size: 13px;
            color: #555;
        }
        .wdk-footer-note {
            font-size: 13px;
            color: #777;
        }
        .wdk-popup-content a {
            color: #4f46e5;
            text-decoration: none;
            word-break: break-all;
        }
        .wdk-popup-content a:hover {
            text-decoration: underline;
        }
        .wdk-actions {
            margin-top: 25px;
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }
        .wdk-btn {
            padding: 10px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
        }
        .wdk-btn:hover {
            filter: brightness(0.95);
            text-decoration: none;
        }
        .wdk-dismiss {
            background: #eee;
        }
        .wdk-popup-content .wdk-primary {
            background: #4f46e5;
            color: #fff;
            text-decoration: none;
        }
        ';
        
        // Enqueue a WordPress built-in style to attach inline CSS to, or register a custom handle.
        wp_register_style('wdk-admin-popup', false);
        wp_enqueue_style('wdk-admin-popup');
        wp_add_inline_style('wdk-admin-popup', $popup_css);
    ?>
<?php
$popup_js = "
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('wdk-admin-popup');

    if (popup) {
        setTimeout(() => popup.classList.add('show'), 2000);

        const closeBtns = document.querySelectorAll('.wdk-admin-popup-close');

        function closePopup() {
            popup.classList.remove('show');

            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=wdk_close_popup'
            });
        }

        if (closeBtns && closeBtns.length > 0) {
            closeBtns.forEach(btn => {
                btn.addEventListener('click', closePopup);
            });
        }

        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                closePopup();
            }
        });
   
    }
});
";
wp_register_script('wdk-admin-popup', false);
wp_enqueue_script('wdk-admin-popup');
wp_add_inline_script('wdk-admin-popup', $popup_js);
?>
</div>
<?php
});

add_action('wp_ajax_wdk_close_popup', function() {
    if (!current_user_can('manage_options')) {
        wp_die();
    }

    update_user_meta(get_current_user_id(), '_wdk_admin_popup_closed', time());

    wp_die();
});