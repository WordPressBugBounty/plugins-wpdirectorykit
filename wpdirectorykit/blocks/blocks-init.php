<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Blocks
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'blocks/inc/block-product-field/block-product-field.php';

function my_custom_block_category($categories, $post) {
    return array_merge(
        array(
            array(
                'slug' => 'wdk-blocks',
                'title' => __('WDK Blocks', 'text-domain'),
                'icon'  => 'dashicons-heart',
            ),
        ),
        $categories
    );
}
add_filter('block_categories_all', 'my_custom_block_category', 10, 2);

function wdk_block_view($view_file = '', $element = '', $print = false)
{
    if(empty($view_file)) return false;
    $file = false;
    if(is_child_theme() && file_exists(get_stylesheet_directory().'/wpdirectorykit/blocks/inc/'.$view_file))
    {
        $file = get_stylesheet_directory().'/wpdirectorykit/blocks/inc/'.$view_file;
    }
    elseif(file_exists(get_template_directory().'/wpdirectorykit/blocks/inc/'.$view_file))
    {
        $file = get_template_directory().'/wpdirectorykit/blocks/inc/'.$view_file;
    }
    elseif(file_exists(WPDIRECTORYKIT_PATH.'blocks/inc/'.$view_file))
    {
        $file = WPDIRECTORYKIT_PATH.'blocks/inc/'.$view_file;
    }

    if($file)
    {
        extract($element);
        if($print) {
            include $file;
        } else {
            ob_start();
            include $file;
            return ob_get_clean();
        }
    }
    else
    {
        if($print) {
            echo 'View file not found in: '.esc_html($file);
        } else {
            return 'View file not found in: '.$file;
        } 
    }
}

?>