<?php
/**
 * Template for the Minutos donation form.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/templates
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get the campaign
if (!isset($campaign) || !$campaign) {
    return;
}
?>

<div class="wp-crowdfundtime-form-container">
    <h3><?php echo esc_html__('Spende Minutos', 'wp-crowdfundtime'); ?></h3>
    
    <div class="wp-crowdfundtime-notice-container">
        <?php
        // Display success message if set
        if (isset($_GET['wp_crowdfundtime_success'])) {
            echo '<div class="wp-crowdfundtime-notice success">' . esc_html__('Thank you for your donation!', 'wp-crowdfundtime') . '</div>';
        }
        
        // Display error message if set
        if (isset($_GET['wp_crowdfundtime_error'])) {
            echo '<div class="wp-crowdfundtime-notice error">' . esc_html(urldecode($_GET['wp_crowdfundtime_error'])) . '</div>';
        }
        ?>
    </div>
    
    <form class="wp-crowdfundtime-form" method="post" action="">
        <?php wp_nonce_field('wp_crowdfundtime_donation_form', 'wp_crowdfundtime_nonce'); ?>
        <input type="hidden" name="campaign_id" value="<?php echo esc_attr($campaign->campaign_id); ?>">
        <input type="hidden" name="donation_type" value="minutos">
        
        <div class="form-field">
            <label for="name"><?php echo esc_html__('Name', 'wp-crowdfundtime'); ?> *</label>
            <input type="text" name="name" id="name" required>
        </div>
        
        <div class="form-field">
            <label for="email"><?php echo esc_html__('Email', 'wp-crowdfundtime'); ?> *</label>
            <input type="email" name="email" id="email" required>
        </div>
        
        <div class="form-field">
            <label for="minutos"><?php echo esc_html__('Minutos', 'wp-crowdfundtime'); ?> *</label>
            <input type="number" name="minutos" id="minutos" min="15" value="15" required>
            <!--p class="description"><?php echo esc_html__('Please send your Minutos by mail after submitting this form.', 'wp-crowdfundtime'); ?></p-->
            <p class="description">Bitte sende Deine <a target="_blank" href="https://minuto.org/de">Minutos</a> per Post nach der Übermittelung dieses Formulares.</p>
        </div>
        
        <div class="form-field">
            <button type="submit" name="wp_crowdfundtime_submit" class="submit-button"><?php echo esc_html__('Minutos spenden', 'wp-crowdfundtime'); ?></button>
        </div>
    </form>
    
    <!--div class="wp-crowdfundtime-social-sharing">
        <h4><?php echo esc_html__('Share This Campaign', 'wp-crowdfundtime'); ?></h4>
        <div class="social-buttons">
            <a href="#" class="social-button facebook-button" data-url="<?php echo esc_url(get_permalink()); ?>" data-title="<?php echo esc_attr($campaign->title); ?>"><?php echo esc_html__('Share on Facebook', 'wp-crowdfundtime'); ?></a>
            <a href="#" class="social-button x-button" data-url="<?php echo esc_url(get_permalink()); ?>" data-title="<?php echo esc_attr($campaign->title); ?>"><?php echo esc_html__('Share on X', 'wp-crowdfundtime'); ?></a>
        </div>
    </div-->
</div>
