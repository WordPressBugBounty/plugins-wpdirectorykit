<?php
/*
Plugin Name: My Custom Block
Description: A custom block for my theme.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function iaprojektiranje_block_product_field_enqueue_assets() {
    wp_enqueue_script(
        'my-custom-block',
         WPDIRECTORYKIT_URL.'/blocks/inc/block-product-field/block.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n', 'wp-api-fetch'),
        22
    );
    wp_enqueue_style(
        'wdk-listings-list',
        WPDIRECTORYKIT_URL. 'elementor-elements/assets/css/widgets/wdk-listings-list.css',
        array(),
        22
    );
    wp_enqueue_script(
        'wdk-latest-listing-block-frontend',
        WPDIRECTORYKIT_URL.'/blocks/inc/block-product-field/frontend.js',
        array(),
        22
    );
}
add_action('enqueue_block_editor_assets', 'iaprojektiranje_block_product_field_enqueue_assets');
add_action('enqueue_block_assets', 'iaprojektiranje_block_product_field_enqueue_assets');

function iaprojektiranje_block_product_field_register_meta() {
    register_rest_route('wdk-blocks/v1', '/last-listings/', array(
        'methods' => 'GET',
        'callback' => 'iaprojektiranje_block_product_field_get_meta',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'iaprojektiranje_block_product_field_register_meta');

function iaprojektiranje_block_product_field_get_meta(WP_REST_Request $request) {

    $post_count = $request->get_param('postCount');

    $atts = array_merge(array(
        'id'=>NULL,
        'custom_class'=>'',
        'order'=>'',
        'conf_query'=>'',
        'limit'=> $post_count,
        'only_is_featured'=>'',
        'conf_custom_results'=>'',
    ), []);
    $data = array();

    /* settings from atts */
    $data['settings'] = $atts;
    $data['id_element'] = '';

    $data['listings_count'] = 0;
    $data['results'] = array();
    $data['pagination_output'] = '';

    /* load css/js */
    wp_enqueue_style('wdk-listings-list');

    $WMVC = &wdk_get_instance();
    $WMVC->model('listingfield_m');
    $WMVC->model('listing_m');
    $WMVC->load_helper('listing');
    $columns = array('ID', 'location_id', 'category_id', 'post_title', 'post_date', 'search', 'order_by','is_featured', 'address');
    $external_columns = array('location_id', 'category_id', 'post_title');
    if(!empty($data['settings']['conf_custom_results'])) {
        /* where in */
        if(!empty($listings_ids)){
            $WMVC->db->where( $WMVC->db->prefix.'wdk_listings.post_id IN(' . $data['settings']['conf_custom_results']  . ')', null, false);
            $data['results'] = $WMVC->listing_m->get();
        }
    } else {
        $controller = 'listing';
        $offset = NULL;
        $original_GET = wmvc_xss_clean($_GET);
        $_GET =  array();

        $_GET['order_by'] = $data['settings']['order'];

        if(!empty($data['settings']['conf_query'])) {
            $qr_string = trim($data['settings']['conf_query'],'?');
            parse_str($qr_string, $_GET);
            $_GET = array_map('trim', $_GET);
        }
        if($data['settings']['only_is_featured'] == 'yes') {
            $_GET['is_featured'] = 'on';
        }

        wdk_prepare_search_query_GET($columns, $controller.'_m', $external_columns);
        $data['results'] = $WMVC->listing_m->get_pagination($data['settings']['limit'], $offset, array('is_activated' => 1,'is_approved'=>1));

    }  
    
    if(isset($original_GET))
        $_GET = $original_GET;

    $output = wdk_block_view('block-product-field/views/view-block.php', $data);
    wp_send_json_success($output);
}
function my_vanilla_js_block_register_block() {
    register_block_type('my-plugin/wdk-latest-listing-block', array(
        'editor_script' => 'wdk-latest-listing-block',
        'style'         => 'wdk-latest-listing-block-style',
        'editor_style'  => 'wdk-latest-listing-block-editor',
    ));
}
add_action('init', 'my_vanilla_js_block_register_block');

?>