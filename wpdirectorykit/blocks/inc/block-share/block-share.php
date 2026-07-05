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

add_action('enqueue_block_editor_assets', function () {
    if (is_user_logged_in()) {
        wp_enqueue_script(
            'wdk-block-share-backend',
            WPDIRECTORYKIT_URL . '/blocks/inc/block-share/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n', 'wp-api-fetch'),
            23
        );
    }
});

add_action('wdk_block_block_share_enqueue_assets', function () {
    wp_enqueue_script(
        'wdk-block-share-frontend',
        WPDIRECTORYKIT_URL . '/blocks/inc/block-share/frontend.js',
        array(),
        22
    );
});


add_action('init', function ()
    {
        register_block_type('my-plugin/wdk-block-share', array(
            'editor_script' => 'wdk-block-share',
            'style'         => 'wdk-block-share-style',
            'editor_style'  => 'wdk-block-share-editor',
        ));
    }
);
