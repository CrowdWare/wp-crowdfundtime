<?php
/**
 * Fired during plugin activation.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 * @author     Your Name <email@example.com>
 */
class WP_CrowdFundTime_Activator {

    /**
     * Create the necessary database tables for the plugin.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table names
        $campaigns_table = $wpdb->prefix . 'crowdfundtime_campaigns';
        $donations_table = $wpdb->prefix . 'crowdfundtime_donations';
        $votes_table = $wpdb->prefix . 'crowdfundtime_votes';

        // SQL for creating campaigns table
        $campaigns_sql = "CREATE TABLE $campaigns_table (
            campaign_id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description longtext,
            goal_hours int(11) NOT NULL DEFAULT 0,
            goal_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            goal_minutos int(11) NOT NULL DEFAULT 0,
            start_date datetime DEFAULT NULL,
            end_date datetime DEFAULT NULL,
            page_id bigint(20) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (campaign_id),
            KEY page_id (page_id)
        ) $charset_collate;";

        // SQL for creating donations table
        $donations_sql = "CREATE TABLE $donations_table (
            donation_id bigint(20) NOT NULL AUTO_INCREMENT,
            campaign_id bigint(20) NOT NULL,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            facebook_post tinyint(1) NOT NULL DEFAULT 0,
            x_post tinyint(1) NOT NULL DEFAULT 0,
            other_support text,
            hours int(11) NOT NULL DEFAULT 1,
            
            minutos int(11) NOT NULL DEFAULT 0,
            minutos_received tinyint(1) NOT NULL DEFAULT 0,
            minutos_received_date datetime DEFAULT NULL,
            donation_type varchar(20) NOT NULL DEFAULT 'time',
            
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (donation_id),
            KEY campaign_id (campaign_id)
        ) $charset_collate;";
        
        $votes_sql = "CREATE TABLE $votes_table (
            votes_id BIGINT(20) NOT NULL AUTO_INCREMENT,
            campaign_id BIGINT(20) NOT NULL,
            name varchar(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            interest BOOLEAN NOT NULL,
            contribution_role VARCHAR(50),
            notes TEXT,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (votes_id),
            KEY campaign_id (campaign_id)
        ) $charset_collate;";

        // Include WordPress database upgrade functions
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Create the tables
        dbDelta($campaigns_sql);
        dbDelta($donations_sql);
        dbDelta($votes_sql);

        // Add version to options
        add_option('wp_crowdfundtime_db_version', WP_CROWDFUNDTIME_VERSION);
    }
}
