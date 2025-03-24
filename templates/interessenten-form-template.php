<?php
/**
 * Template for the interessenten form.
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

    <form class="wp-crowdfundtime-form" method="post" action="">
        <?php wp_nonce_field('wp_crowdfundtime_interessent_form', 'wp_crowdfundtime_interessent_nonce'); ?>
        <input type="hidden" name="campaign_id" value="<?php echo esc_attr($campaign->campaign_id); ?>">

        <div class="form-field">
            <label for="name"><?php echo esc_html__('Name', 'wp-crowdfundtime'); ?> *</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="form-field">
            <label for="email"><?php echo esc_html__('Email', 'wp-crowdfundtime'); ?> *</label>
            <input type="email" name="email" id="email" required>
        </div>

        <p><?php echo esc_html__('Ich möchte wie folgt helfen:', 'wp-crowdfundtime'); ?></p>

        <div class="form-field checkbox-field">
            <input type="checkbox" name="entwicklerhilfe" id="entwicklerhilfe" value="1">
            <label for="entwicklerhilfe"><?php echo esc_html__('Ich würde als Entwickler helfen', 'wp-crowdfundtime'); ?></label>
        </div>

        <div class="form-field checkbox-field">
            <input type="checkbox" name="mundpropaganda" id="mundpropaganda" value="1">
            <label for="mundpropaganda"><?php echo esc_html__('Ich würde helfen mit Mund-zu-Mund-Propaganda', 'wp-crowdfundtime'); ?></label>
        </div>

        <div class="form-field checkbox-field">
            <input type="checkbox" name="geldspende" id="geldspende" value="1">
            <label for="geldspende"><?php echo esc_html__('Ich würde einen Betrag an Geld spenden', 'wp-crowdfundtime'); ?></label>
        </div>

        <div class="form-field checkbox-field">
            <input type="checkbox" name="projektfortschritt" id="projektfortschritt" value="1">
            <label for="projektfortschritt"><?php echo esc_html__('Ich möchte über den Projektfortschritt informiert werden', 'wp-crowdfundtime'); ?></label>
        </div>

        <div class="form-field">
            <button type="submit" name="wp_crowdfundtime_interessent_submit" class="submit-button"><?php echo esc_html__('Interesse bekunden', 'wp-crowdfundtime'); ?></button>
        </div>
    </form>
</div>
