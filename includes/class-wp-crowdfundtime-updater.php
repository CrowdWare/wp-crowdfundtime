<?php
/**
 * Database updater for the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 */

/**
 * Database updater for the plugin.
 *
 * This class handles all database updates for the plugin.
 *
 * @since      1.0.0
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 * @author     Your Name <email@example.com>
 */
class WP_CrowdFundTime_Updater {

    /**
     * The current database version.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $db_version    The current database version.
     */
    private $db_version = '1.0.1';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('plugins_loaded', array($this, 'check_db_version'));
    }

    /**
     * Check the current database version and run updates if necessary.
     *
     * @since    1.0.0
     */
    public function check_db_version() {
        $installed_db_version = get_option('wp_crowdfundtime_db_version');

        if ($installed_db_version !== $this->db_version) {
            // update_option('wp_crowdfundtime_db_version', $this->db_version);
        }
    }
}

// Initialize the updater
new WP_CrowdFundTime_Updater();
