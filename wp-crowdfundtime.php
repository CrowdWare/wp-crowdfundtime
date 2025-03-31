<?php
/**
 * Plugin Name: WP CrowdFundTime
 * Plugin URI: https://example.com/wp-crowdfundtime
 * Description: A WordPress plugin for time-based crowdfunding campaigns where users can donate their time instead of money.
 * Version: 1.4.19
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
define('WP_CROWDFUNDTIME_VERSION', '1.4.19');
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
// Hook the vote form submission handler to init
add_action('init', 'handle_crowdfundtime_vote_submission');
run_wp_crowdfundtime();


/**
 * Handles the submission of the vote form.
 * Hooked to 'init'.
 */
function handle_crowdfundtime_vote_submission() {
    // Check if our specific form was submitted
    if (!isset($_POST['crowdfundtime_vote_submit'])) {
        return;
    }

    // Verify nonce
    if (!isset($_POST['wp_crowdfundtime_vote_nonce']) || !wp_verify_nonce($_POST['wp_crowdfundtime_vote_nonce'], 'wp_crowdfundtime_vote_form')) {
        wp_die('Security check failed.');
    }

    // --- Determine Redirect URL ---
    $redirect_url = home_url(); // Default fallback

    if (isset($_POST['_wp_http_referer'])) {
        $form_path = wp_unslash($_POST['_wp_http_referer']);
        // Ensure the path starts with a slash if it's not empty
        if (!empty($form_path) && strpos($form_path, '/') !== 0) {
            $form_path = '/' . $form_path;
        }
        // Reconstruct the full URL using the site's base URL and the path from the form
        $potential_redirect_url = home_url($form_path);
        // Basic validation: check if it looks like a valid URL after reconstruction
        // Use esc_url_raw for validation/sanitization before using it
        $validated_url = esc_url_raw($potential_redirect_url);
        if (!empty($validated_url)) {
             $redirect_url = $validated_url;
        }
    } else {
        // Fallback to wp_get_referer() if the hidden field is missing
        $referer = wp_get_referer();
        if ($referer) {
            // Sanitize the referer as well
             $validated_referer = esc_url_raw($referer);
             if (!empty($validated_referer)) {
                $redirect_url = $validated_referer;
             }
        }
    }
    // --- End Determine Redirect URL ---


    // Get campaign ID from hidden field
    $campaign_id = isset($_POST['campaign_id']) ? intval($_POST['campaign_id']) : 0;

    if (!$campaign_id) {
        // Redirect back with error if campaign ID is missing
        wp_redirect(add_query_arg('wp_crowdfundtime_vote_error', urlencode('Invalid Campaign ID.'), $redirect_url));
        exit;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "crowdfundtime_votes";

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $interest = isset($_POST['interest']) ? 1 : 0;
    $role = sanitize_text_field($_POST['role']);
    $notes = sanitize_textarea_field($_POST['notes']);

    if (!is_email($email)) {
        // Redirect back with error for invalid email
        wp_redirect(add_query_arg('wp_crowdfundtime_vote_error', urlencode('Please enter a valid email.'), $redirect_url));
        exit;
    } else {
        $inserted = $wpdb->insert($table_name, [
            'campaign_id' => $campaign_id,
            'name' => $name,
            'email' => $email,
            'interest' => $interest,
            'contribution_role' => $role,
            'notes' => $notes
            // Removed 'submission_date' => current_time('mysql', 1) as the column doesn't exist
        ]);

        if ($inserted === false) {
            // Redirect back with database error
            wp_redirect(add_query_arg('wp_crowdfundtime_vote_error', urlencode('Database error. Could not save submission.'), $redirect_url));
            exit;
        } else {
            // Send email notification
            wp_mail(get_option('admin_email'), 'New Vote Received', "A new vote has been submitted for campaign ID $campaign_id.\n\nName: $name\nEmail: $email\nInterest: $interest\nRole: $role\nNotes: $notes");

            // Redirect back with success message (wp_redirect handles sanitization)
            wp_redirect(add_query_arg('wp_crowdfundtime_vote_success', '1', $redirect_url));
            exit;
        }
    }
}


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
        "SELECT name, email, interest, contribution_role, notes FROM $table_name WHERE campaign_id = %d",
        $campaign_id
    ));

    if (empty($results)) {
        return "<p>No votes found for this campaign.</p>";
    }

    ob_start(); ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Interest</th>
                <th>Role</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo esc_html($row->name); ?></td>
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
 * Displays the voting form.
 */
function crowdfundtime_vote_form($atts) {
    $atts = shortcode_atts(array('id' => 0), $atts);
    $campaign_id = intval($atts['id']);

    if (!$campaign_id) {
        return "<p style='color:red;'>Campaign ID is required.</p>";
    }

    // The processing logic is now handled by handle_crowdfundtime_vote_submission() hooked to 'init'.
    // This function now only displays the form and handles GET parameter messages.

    ob_start(); ?>
    <div class="wp-crowdfundtime-form-container">
    <h3><?php echo esc_html__('Become an interested party', 'wp-crowdfundtime'); ?></h3>
    <div class="wp-crowdfundtime-notice-container">
        <?php
        // Display success message if set
        if (isset($_GET['wp_crowdfundtime_vote_success'])) {
            echo '<div class="wp-crowdfundtime-notice success">' . esc_html__('Thank you for your interest!', 'wp-crowdfundtime') . '</div>';
        }

        // Display error message if set
        if (isset($_GET['wp_crowdfundtime_vote_error'])) {
            echo '<div class="wp-crowdfundtime-notice error">' . esc_html(urldecode($_GET['wp_crowdfundtime_vote_error'])) . '</div>';
        }
        ?>
    </div>
    <form class="wp-crowdfundtime-form" method="post" action=""> <?php // Post to the current URL ?>
    <?php wp_nonce_field('wp_crowdfundtime_vote_form', 'wp_crowdfundtime_vote_nonce'); ?>
    <input type="hidden" name="campaign_id" value="<?php echo esc_attr($campaign_id); ?>">
    <input type="hidden" name="donation_type" value="vote">
    <?php // Add hidden field for the current URL ?>
    <input type="hidden" name="_wp_http_referer" value="<?php echo esc_url(wp_unslash($_SERVER['REQUEST_URI'])); ?>">
    <div class="form-field">
        <label>Name:</label>
        <input type="text" name="name" required><br>
    </div>
    <div class="form-field">
        <label>Email:</label>
        <input type="email" name="email" required><br>
    </div>
    <div class="form-field">
        <label>Would you use this product?</label>
        <input type="checkbox" name="interest"><br>
    </div>
    <div class="form-field">
        <label>How can you contribute?</label>
        <select name="role">
            <option value="Entwickler">Developer</option>
            <option value="Tester">Tester</option>
            <option value="Werbung">Advertiser</option>  
        </select><br>
    </div>
    <div class="form-field">
        <label>Notes:</label>
        <textarea name="notes"></textarea><br>
    </div>
    <div class="form-field">
        <input type="submit" class="submit-button" name="crowdfundtime_vote_submit" value="Submit">
    </div>
    </form>
</div>
    <?php
    return ob_get_clean();
}
