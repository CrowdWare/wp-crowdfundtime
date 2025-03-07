<?php
/**
 * Database operations for the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 */

/**
 * Database operations for the plugin.
 *
 * This class handles all database operations for the plugin.
 *
 * @since      1.0.0
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 * @author     Your Name <email@example.com>
 */
class WP_CrowdFundTime_Database {

    /**
     * The table name for campaigns.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $campaigns_table    The table name for campaigns.
     */
    private $campaigns_table;

    /**
     * The table name for donations.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $donations_table    The table name for donations.
     */
    private $donations_table;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->campaigns_table = $wpdb->prefix . 'crowdfundtime_campaigns';
        $this->donations_table = $wpdb->prefix . 'crowdfundtime_donations';
    }

    /**
     * Create a new campaign.
     *
     * @since    1.0.0
     * @param    array    $campaign_data    The campaign data.
     * @return   int|false                  The campaign ID on success, false on failure.
     */
    public function create_campaign($campaign_data) {
        global $wpdb;
        
        $result = $wpdb->insert(
            $this->campaigns_table,
            $campaign_data
        );
        
        if ($result) {
            return $wpdb->insert_id;
        }
        
        return false;
    }

    /**
     * Update an existing campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id      The campaign ID.
     * @param    array    $campaign_data    The campaign data.
     * @return   bool                       True on success, false on failure.
     */
    public function update_campaign($campaign_id, $campaign_data) {
        global $wpdb;
        
        $result = $wpdb->update(
            $this->campaigns_table,
            $campaign_data,
            array('campaign_id' => $campaign_id)
        );
        
        return $result !== false;
    }

    /**
     * Get a campaign by ID.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   object|null              The campaign object, or null if not found.
     */
    public function get_campaign($campaign_id) {
        global $wpdb;
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->campaigns_table} WHERE campaign_id = %d",
                $campaign_id
            )
        );
    }

    /**
     * Get a campaign by page ID.
     *
     * @since    1.0.0
     * @param    int      $page_id    The page ID.
     * @return   object|null          The campaign object, or null if not found.
     */
    public function get_campaign_by_page_id($page_id) {
        global $wpdb;
        
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$this->campaigns_table} WHERE page_id = %d",
                $page_id
            )
        );
    }

    /**
     * Get all campaigns.
     *
     * @since    1.0.0
     * @return   array    The campaigns.
     */
    public function get_campaigns() {
        global $wpdb;
        
        return $wpdb->get_results(
            "SELECT * FROM {$this->campaigns_table} ORDER BY created_at DESC"
        );
    }

    /**
     * Delete a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   bool                     True on success, false on failure.
     */
    public function delete_campaign($campaign_id) {
        global $wpdb;
        
        // First delete all donations for this campaign
        $wpdb->delete(
            $this->donations_table,
            array('campaign_id' => $campaign_id)
        );
        
        // Then delete the campaign
        $result = $wpdb->delete(
            $this->campaigns_table,
            array('campaign_id' => $campaign_id)
        );
        
        return $result !== false;
    }

    /**
     * Create a new donation.
     *
     * @since    1.0.0
     * @param    array    $donation_data    The donation data.
     * @return   int|false                  The donation ID on success, false on failure.
     */
    public function create_donation($donation_data) {
        global $wpdb;
        
        $result = $wpdb->insert(
            $this->donations_table,
            $donation_data
        );
        
        if ($result) {
            return $wpdb->insert_id;
        }
        
        return false;
    }

    /**
     * Get donations for a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   array                    The donations.
     */
    public function get_donations_by_campaign($campaign_id) {
        global $wpdb;
        
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->donations_table} WHERE campaign_id = %d ORDER BY created_at DESC",
                $campaign_id
            )
        );
    }

    /**
     * Get total hours donated for a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   int                      The total hours donated.
     */
    public function get_total_hours($campaign_id) {
        global $wpdb;
        
        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(hours) FROM {$this->donations_table} WHERE campaign_id = %d",
                $campaign_id
            )
        );
    }

    /**
     * Get total monetary donations for a campaign from Stripe.
     * This is a placeholder function that would need to be integrated with Stripe.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   float                    The total monetary donations.
     */
    public function get_total_monetary_donations($campaign_id) {
        // This is a placeholder function
        // In a real implementation, this would query the Stripe payment data
        // For now, we'll return a dummy value
        return 0.00;
        
        // Example implementation with Stripe integration:
        /*
        global $wpdb;
        
        // Assuming there's a table that stores Stripe payment information
        $stripe_payments_table = $wpdb->prefix . 'stripe_payments';
        
        return (float) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(amount) FROM {$stripe_payments_table} WHERE campaign_id = %d AND status = 'completed'",
                $campaign_id
            )
        );
        */
    }

    /**
     * Get donation statistics for a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   array                    The donation statistics.
     */
    public function get_donation_stats($campaign_id) {
        global $wpdb;
        
        $campaign = $this->get_campaign($campaign_id);
        if (!$campaign) {
            return array(
                'total_hours' => 0,
                'goal_hours' => 0,
                'hours_percentage' => 0,
                'total_amount' => 0,
                'goal_amount' => 0,
                'amount_percentage' => 0,
                'total_donors' => 0,
                'facebook_posts' => 0,
                'x_posts' => 0,
            );
        }
        
        $total_hours = $this->get_total_hours($campaign_id);
        $total_amount = $this->get_total_monetary_donations($campaign_id);
        
        $total_donors = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT donation_id) FROM {$this->donations_table} WHERE campaign_id = %d",
                $campaign_id
            )
        );
        
        $facebook_posts = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->donations_table} WHERE campaign_id = %d AND facebook_post = 1",
                $campaign_id
            )
        );
        
        $x_posts = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->donations_table} WHERE campaign_id = %d AND x_post = 1",
                $campaign_id
            )
        );
        
        $hours_percentage = $campaign->goal_hours > 0 ? ($total_hours / $campaign->goal_hours) * 100 : 0;
        $amount_percentage = $campaign->goal_amount > 0 ? ($total_amount / $campaign->goal_amount) * 100 : 0;
        
        return array(
            'total_hours' => $total_hours,
            'goal_hours' => $campaign->goal_hours,
            'hours_percentage' => min(100, $hours_percentage),
            'total_amount' => $total_amount,
            'goal_amount' => $campaign->goal_amount,
            'amount_percentage' => min(100, $amount_percentage),
            'total_donors' => $total_donors,
            'facebook_posts' => $facebook_posts,
            'x_posts' => $x_posts,
        );
    }
}
