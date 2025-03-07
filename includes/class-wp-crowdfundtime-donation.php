<?php
/**
 * Donation processing functionality.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 */

/**
 * Donation processing functionality.
 *
 * This class handles all donation-related operations.
 *
 * @since      1.0.0
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/includes
 * @author     Your Name <email@example.com>
 */
class WP_CrowdFundTime_Donation {

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
     * Process a donation form submission.
     *
     * @since    1.0.0
     * @param    array    $form_data    The form data.
     * @return   int|WP_Error           The donation ID on success, WP_Error on failure.
     */
    public function process_donation($form_data) {
        // Validate required fields
        $required_fields = array('campaign_id', 'name', 'email', 'hours');
        foreach ($required_fields as $field) {
            if (empty($form_data[$field])) {
                return new WP_Error(
                    'missing_required_field',
                    sprintf(__('Missing required field: %s', 'wp-crowdfundtime'), $field)
                );
            }
        }
        
        // Validate hours (minimum 1)
        if ((int) $form_data['hours'] < 1) {
            return new WP_Error(
                'invalid_hours',
                __('Hours must be at least 1', 'wp-crowdfundtime')
            );
        }
        
        // Validate email
        if (!is_email($form_data['email'])) {
            return new WP_Error(
                'invalid_email',
                __('Invalid email address', 'wp-crowdfundtime')
            );
        }
        
        // Prepare donation data
        $donation_data = array(
            'campaign_id' => (int) $form_data['campaign_id'],
            'name' => sanitize_text_field($form_data['name']),
            'email' => sanitize_email($form_data['email']),
            'facebook_post' => isset($form_data['facebook_post']) ? 1 : 0,
            'x_post' => isset($form_data['x_post']) ? 1 : 0,
            'other_support' => isset($form_data['other_support']) ? sanitize_textarea_field($form_data['other_support']) : '',
            'hours' => (int) $form_data['hours'],
        );
        
        // Create the donation
        $donation_id = $this->db->create_donation($donation_data);
        
        if (!$donation_id) {
            return new WP_Error(
                'donation_creation_failed',
                __('Failed to create donation', 'wp-crowdfundtime')
            );
        }
        
        return $donation_id;
    }

    /**
     * Get donations for a campaign.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   array                    The donations.
     */
    public function get_donations_by_campaign($campaign_id) {
        return $this->db->get_donations_by_campaign($campaign_id);
    }

    /**
     * Generate the HTML for the donation form.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   string                   The donation form HTML.
     */
    public function generate_donation_form($campaign_id) {
        // Get the campaign
        $campaign = $this->db->get_campaign($campaign_id);
        if (!$campaign) {
            return '<p>' . __('Campaign not found.', 'wp-crowdfundtime') . '</p>';
        }
        
        // Start output buffering
        ob_start();
        
        // Include the form template
        include WP_CROWDFUNDTIME_PLUGIN_DIR . 'templates/form-template.php';
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * Generate the HTML for the donors list.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   string                   The donors list HTML.
     */
    public function generate_donors_list($campaign_id) {
        // Get the campaign
        $campaign = $this->db->get_campaign($campaign_id);
        if (!$campaign) {
            return '<p>' . __('Campaign not found.', 'wp-crowdfundtime') . '</p>';
        }
        
        // Get the donations
        $donations = $this->db->get_donations_by_campaign($campaign_id);
        
        // Start output buffering
        ob_start();
        
        // Include the donors template
        include WP_CROWDFUNDTIME_PLUGIN_DIR . 'templates/donors-template.php';
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * Generate a PDF export of the donors list.
     *
     * @since    1.0.0
     * @param    int      $campaign_id    The campaign ID.
     * @return   string                   The path to the generated PDF file.
     */
    public function generate_pdf_export($campaign_id) {
        // This is a placeholder function
        // In a real implementation, this would generate a PDF using a library like TCPDF or FPDF
        // For now, we'll just return a message
        return __('PDF export functionality would be implemented here.', 'wp-crowdfundtime');
        
        // Example implementation with TCPDF:
        /*
        // Get the campaign
        $campaign = $this->db->get_campaign($campaign_id);
        if (!$campaign) {
            return false;
        }
        
        // Get the donations
        $donations = $this->db->get_donations_by_campaign($campaign_id);
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('WP CrowdFundTime');
        $pdf->SetTitle('Donors List - ' . $campaign->title);
        $pdf->SetSubject('Donors List');
        
        // Set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Donors List', $campaign->title);
        
        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        
        // Table header
        $html = '<table border="1" cellpadding="5">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Hours</th>
                <th>Facebook Post</th>
                <th>X Post</th>
                <th>Other Support</th>
                <th>Date</th>
            </tr>';
        
        // Table rows
        foreach ($donations as $donation) {
            $html .= '<tr>';
            $html .= '<td>' . $donation->name . '</td>';
            $html .= '<td>' . $donation->email . '</td>';
            $html .= '<td>' . $donation->hours . '</td>';
            $html .= '<td>' . ($donation->facebook_post ? 'Yes' : 'No') . '</td>';
            $html .= '<td>' . ($donation->x_post ? 'Yes' : 'No') . '</td>';
            $html .= '<td>' . $donation->other_support . '</td>';
            $html .= '<td>' . date('Y-m-d', strtotime($donation->created_at)) . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Close and output PDF document
        $file_path = WP_CONTENT_DIR . '/uploads/wp-crowdfundtime/donors-' . $campaign_id . '.pdf';
        $pdf->Output($file_path, 'F');
        
        return $file_path;
        */
    }
}
