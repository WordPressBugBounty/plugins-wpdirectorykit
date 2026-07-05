<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              wpdirectorykit.com
 * @since             1.0.0
 * @package           Wpdirectorykit
 *
 * @wordpress-plugin
 * Plugin Name:       WP Directory Kit
 * Plugin URI:        https://wpdirectorykit.com/plugins/wpdirectorykit.html
 * Description:       Build your Directory portal, demos for Real Estate Agencies and Car Dealership included
 * Version:           1.5.4
 * Requires PHP:      7.0
 * Author:            wpdirectorykit.com
 * Author URI:        https://wpdirectorykit.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpdirectorykit
 * Domain Path:       /languages
 * 
 * Elementor tested up to: 3.33.1
 * Elementor Pro tested up to: 3.23.2
 * 
 * Copyright (C) SWIT Sandi Winter IT.
 * 
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WPDIRECTORYKIT_VERSION', '1.5.4');
define('WPDIRECTORYKIT_NAME', 'wdk');
define('WPDIRECTORYKIT_PATH', plugin_dir_path(__FILE__));
define('WPDIRECTORYKIT_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpdirectorykit-activator.php
 */
function activate_wpdirectorykit()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wpdirectorykit-activator.php';
    Wpdirectorykit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpdirectorykit-deactivator.php
 */
function deactivate_wpdirectorykit()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-wpdirectorykit-deactivator.php';
    Wpdirectorykit_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wpdirectorykit');
register_deactivation_hook(__FILE__, 'deactivate_wpdirectorykit');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wpdirectorykit.php';


if (file_exists(WPDIRECTORYKIT_PATH . 'premium_functions.php') && !defined('WDK_FS_DISABLE')) {
    require_once WPDIRECTORYKIT_PATH . 'premium_functions.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpdirectorykit()
{

    $plugin = new Wpdirectorykit();
    $plugin->run();
}
run_wpdirectorykit();


/*
* Add admin notify
* @param (string) $key unique key of notify, prefix included related plugin
* @param (string) $text test of message
* @param (function) $callback_filter custom function should be return true if not need show
* @param (string) $class notify alert class, by default 'notice notice-error'
* @return boolen true 
*/
function wdk_notify_admin($key = '', $text = 'Custom Text of message', $callback_filter = '', $class = 'notice notice-error')
{
    $key = 'wdk_notify_' . $key;
    $key_dismiss = $key . '_dismiss';

    add_action('admin_notices', function () use ($key_dismiss, $text, $class, $callback_filter) {

        $user_id = get_current_user_id();

        // Already dismissed
        if (get_user_meta($user_id, $key_dismiss, true)) {
            return;
        }

        // Optional filter
        if (!empty($callback_filter) && $callback_filter()) {
            return;
        }

        // Nonce URL
        $url = wp_nonce_url(
            add_query_arg($key_dismiss, '1'),
            $key_dismiss . '_nonce'
        );

?>
        <div class="<?php echo esc_attr($class); ?> is-dismissible">
            <p><?php echo $text; // safe if you control it 
                ?></p>
            <a href="<?php echo esc_url($url); ?>"><button type="button" class="notice-dismiss"></button></a>
        </div>
<?php
    });

    // Handle dismiss
    add_action('admin_init', function () use ($key_dismiss) {

        if (!isset($_GET[$key_dismiss])) {
            return;
        }

        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], $key_dismiss . '_nonce')) {
            return;
        }

        $user_id = get_current_user_id();

        update_user_meta($user_id, $key_dismiss, 1);

        wp_redirect(remove_query_arg([$key_dismiss, '_wpnonce']));
        exit;
    });

    return true;
}
