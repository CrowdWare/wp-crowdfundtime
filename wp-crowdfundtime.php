<?php
/**
 * Plugin Name: WP CrowdFundTime
 * Plugin URI: https://example.com/wp-crowdfundtime
 * Description: A WordPress plugin for time-based crowdfunding campaigns where users can donate their time instead of money.
 * Version: 1.4.7
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
define('WP_CROWDFUNDTIME_VERSION', '1.4.7');
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
add_shortcode('crowdfundtime_vote_form', 'crowdfundtime_vote_form');
add_shortcode('crowdfundtime_vote_list', 'crowdfundtime_vote_list');
run_wp_crowdfundtime();

/**
 * Updated function for voting list.
 */
function crowdfundtime_vote_list($atts) {
    $atts = shortcode_atts(array('id' => 0), $atts);
    $campaign_id = intval($atts['id']);

    if (!$campaign_id) {
        return "<p style='color:red;'>Campaign ID is required.</p>";
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "crowdfundtime_votes";
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT email, interest, contribution_role, notes FROM $table_name WHERE campaign_id = %d",
        $campaign_id
    ));

    if (empty($results)) {
        return "<p>No votes found for this campaign.</p>";
    }

    ob_start(); ?>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Interest</th>
                <th>Role</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo esc_html($row->email); ?></td>
                    <td><?php echo $row->interest ? 'Yes' : 'No'; ?></td>
                    <td><?php echo esc_html($row->contribution_role); ?></td>
                    <td><?php echo esc_html($row->notes); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}

/**
 * Updated function for voting form.
 */
function crowdfundtime_vote_form($atts) {
    $atts = shortcode_atts(array('id' => 0), $atts);
    $campaign_id = intval($atts['id']);

    if (!$campaign_id) {
        return "<p style='color:red;'>Campaign ID is required.</p>";
    }

    if (isset($_POST['crowdfundtime_vote_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . "crowdfundtime_votes";

        $email = sanitize_email($_POST['email']);
        $interest = isset($_POST['interest']) ? 1 : 0;
        $role = sanitize_text_field($_POST['role']);
        $notes = sanitize_textarea_field($_POST['notes']);

        if (!is_email($email)) {
            echo "<p style='color:red;'>Please enter a valid email.</p>";
        } else {
            $wpdb->insert($table_name, [
                'campaign_id' => $campaign_id,
                'email' => $email,
                'interest' => $interest,
                'contribution_role' => $role,
                'notes' => $notes,
            ]);

            wp_mail(get_option('admin_email'), 'New Vote Received', "A new vote has been submitted.\n\nEmail: $email\nInterest: $interest\nRole: $role\nNotes: $notes");

            echo "<p style='color:green;'>Thank you for your submission!</p>";
        }
    }

    ob_start(); ?>
    <div class="wp-crowdfundtime-form-container">
    <h3><?php echo esc_html__('Werde ein Interessent', 'wp-crowdfundtime'); ?></h3>
    <div class="wp-crowdfundtime-notice-container">
        <?php
        // Display success message if set
        if (isset($_GET['wp_crowdfundtime_interessent_success'])) {
            echo '<div class="wp-crowdfundtime-notice success">' . esc_html__('Vielen Dank für dein Interesse!', 'wp-crowdfundtime') . '</div>';
        }

        // Display error message if set
        if (isset($_GET['wp_crowdfundtime_interessent_error'])) {
            echo '<div class="wp-crowdfundtime-notice error">' . esc_html(urldecode($_GET['wp_crowdfundtime_interessent_error'])) . '</div>';
        }
        ?>
    </div>
    <form method="post">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Would you use this product?</label>
        <input type="checkbox" name="interest"><br><br>

        <label>How can you contribute?</label><br>
        <select name="role">
            <option value="Entwickler">Entwickler</option>
            <option value="Werbung">Werbung</option>
            <option value="Tester">Tester</option>
        </select><br><br>

        <label>Notes:</label><br>
        <textarea name="notes"></textarea><br><br>

        <input type="submit" name="crowdfundtime_vote_submit" value="Submit">
    </form>
</div>
    <?php
    return ob_get_clean();
}
