<?php
/**
 * Plugin Name: WP CrowdFundTime
 * Plugin URI: https://example.com/wp-crowdfundtime
 * Description: A WordPress plugin for time-based crowdfunding campaigns where users can donate their time instead of money.
 * Version: 1.3.12
 * Author: CrowdWare
 * Author URI: https://example.com
 * Text Domain: wp-crowdfundtime
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WP_CROWDFUNDTIME_VERSION', '1.3.12');
define('WP_CROWDFUNDTIME_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_CROWDFUNDTIME_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_CROWDFUNDTIME_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Check if Stripe Payments plugin is active and include necessary files
if (!class_exists('ASPMain') && file_exists(WP_CROWDFUNDTIME_PLUGIN_DIR . 'stripe-payments/accept-stripe-payments.php')) {
    // Define ASPMain class and products_slug if not already defined
    if (!class_exists('ASPMain')) {
        class ASPMain {
            public static $products_slug = 'asp-products';
        }
    }
}

/**
 * The code that runs during plugin activation.
 */
function activate_wp_crowdfundtime() {
    require_once WP_CROWDFUNDTIME_PLUGIN_DIR . 'includes/class-wp-crowdfundtime-activator.php';
    WP_CrowdFundTime_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wp_crowdfundtime() {
    require_once WP_CROWDFUNDTIME_PLUGIN_DIR . 'includes/class-wp-crowdfundtime-deactivator.php';
    WP_CrowdFundTime_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_crowdfundtime');
register_deactivation_hook(__FILE__, 'deactivate_wp_crowdfundtime');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once WP_CROWDFUNDTIME_PLUGIN_DIR . 'includes/class-wp-crowdfundtime.php';

/**
 * Begins execution of the plugin.
 */
function run_wp_crowdfundtime() {
    $plugin = new WP_CrowdFundTime();
    $plugin->run();
}
run_wp_crowdfundtime();
