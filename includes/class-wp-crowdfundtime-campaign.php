<?php
/**
 * Campaign management functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 */

/**
 * Campaign management functionality.
 *
 * This class handles all campaign-related operations.
 *
 * @since      1.0.0
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 * @author     Your Name <email@example.com>
 */
class WP_CrowdFundTime_Campaign {

    /**
     * The database handler.
     *
     * @since    1.0.0
     * @access   private
     * @var      WP_CrowdFundTime_Database    $db    The database handler.
     */
    private $db;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    WP_CrowdFundTime_Database    $db    The database handler.
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new campaign.
     *
     * @since    1.0.0
     * @param    array    $campaign_data    The campaign data.
     * @return   int|false                  The campaign ID on success, false on failure.
     */
    public function create_campaign($campaign_data) {
        // Validate required fields
        if (empty($campaign_data['title'])) {
            return false;
        }
        
        // Set default values if not provided
        if (!isset($campaign_data['goal_hours'])) {
            $campaign_data['goal_hours'] = 0;
        }
        
        if (!isset($campaign_data['goal_amount'])) {
            $campaign_data['goal_amount'] = 0.00;
        }
        
        return $this->db->create_campaign($campaign_data);
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
        // Validate campaign ID
        if (empty($campaign_id)) {
            return false;
        }
        
        return $this->db->update_campaign($campaign_id, $campaign_data);
    }

    /**
     * Get a campaign by ID.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   object|null              The campaign object, or null if not found.
     */
    public function get_campaign($campaign_id) {
        return $this->db->get_campaign($campaign_id);
    }

    /**
     * Get a campaign by page ID.
     *
     * @since    1.0.0
     * @param    int      $page_id    The page ID.
     * @return   object|null          The campaign object, or null if not found.
     */
    public function get_campaign_by_page_id($page_id) {
        return $this->db->get_campaign_by_page_id($page_id);
    }

    /**
     * Get all campaigns.
     *
     * @since    1.0.0
     * @return   array    The campaigns.
     */
    public function get_campaigns() {
        return $this->db->get_campaigns();
    }

    /**
     * Delete a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   bool                     True on success, false on failure.
     */
    public function delete_campaign($campaign_id) {
        return $this->db->delete_campaign($campaign_id);
    }

    /**
     * Get donation statistics for a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   array                    The donation statistics.
     */
    public function get_donation_stats($campaign_id) {
        return $this->db->get_donation_stats($campaign_id);
    }

    /**
     * Format the progress display for a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @param    string   $type           The type of progress to display ('hours' or 'money').
     * @return   string                   The formatted progress display.
     */
    public function format_progress_display($campaign_id, $type = 'hours') {
        $stats = $this->get_donation_stats($campaign_id);
        
        if ($type === 'hours') {
            return sprintf(
                '%d von %d Stunden',
                $stats['total_hours'],
                $stats['goal_hours']
            );
        } else {
            return sprintf(
                '%.2f,- € von %.2f,- €',
                $stats['total_amount'],
                $stats['goal_amount']
            );
        }
    }

    /**
     * Generate a progress bar HTML for a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @param    string   $type           The type of progress to display ('hours' or 'money').
     * @return   string                   The progress bar HTML.
     */
    public function generate_progress_bar($campaign_id, $type = 'hours') {
        $stats = $this->get_donation_stats($campaign_id);
        
        $percentage = $type === 'hours' ? $stats['hours_percentage'] : $stats['amount_percentage'];
        $class = $type === 'hours' ? 'hours-progress' : 'money-progress';
        
        $html = '<div class="wp-crowdfundtime-progress-container">';
        $html .= '<div class="wp-crowdfundtime-progress-bar ' . esc_attr($class) . '">';
        $html .= '<div class="wp-crowdfundtime-progress-fill" style="width: ' . esc_attr($percentage) . '%"></div>';
        $html .= '</div>';
        
        if ($type === 'hours') {
            $html .= '<div class="wp-crowdfundtime-progress-text">';
            $html .= esc_html(sprintf(
                '%d von %d Stunden (%.1f%%)',
                $stats['total_hours'],
                $stats['goal_hours'],
                $percentage
            ));
            $html .= '</div>';
        } else {
            $html .= '<div class="wp-crowdfundtime-progress-text">';
            $html .= esc_html(sprintf(
                '%.2f,- € von %.2f,- € (%.1f%%)',
                $stats['total_amount'],
                $stats['goal_amount'],
                $percentage
            ));
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
