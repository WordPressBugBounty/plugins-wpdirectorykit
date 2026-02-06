<?php

if (! defined('WPINC')) {
    die;
}

//'anonymous_mode' => true,


if (! function_exists('wdk_fs')) {
    // Create a helper function for easy SDK access.
    function wdk_fs()
    {
        global $wdk_fs;

        if (! isset($wdk_fs)) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/vendor/freemius/start.php';

            $wdk_fs = fs_dynamic_init(array(
                'id'                  => '10131',
                'slug'                => 'wpdirectorykit',
                'premium_slug'        => 'wdk-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_024925d617beb399759247d65ac26',
                'is_premium'          => false,
                'has_addons'          => true,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'wdk',
                    'contact'        => false,
                    'support'        => false,
                    'addons'         => false,
                ),
                'anonymous_mode' => true,
            ));
        }

        return $wdk_fs;
    }

    // Init Freemius.
    wdk_fs();
    // Signal that SDK was initiated.
    do_action('wdk_fs_loaded');
}
