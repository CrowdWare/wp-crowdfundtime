<?php
/**
 * Template for the progress display.
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

// Get the campaign and stats
if (!isset($campaign) || !$campaign || !isset($stats)) {
    return;
}

// Get the type (hours or money)
$type = isset($type) && $type === 'money' ? 'money' : 'hours';

// Get the display type (bar or text)
$display = isset($display) && $display === 'text' ? 'text' : 'bar';

// Calculate percentage
$percentage = $type === 'hours' ? $stats['hours_percentage'] : $stats['amount_percentage'];
$class = $type === 'hours' ? 'hours-progress' : 'money-progress';

// Format the text
if ($type === 'hours') {
    $text = sprintf(
        __('%d von %d Stunden (%.1f%%)', 'wp-crowdfundtime'),
        $stats['total_hours'],
        $stats['goal_hours'],
        $percentage
    );
} else {
    $text = sprintf(
        __('%.2f,- € von %.2f,- € (%.1f%%)', 'wp-crowdfundtime'),
        $stats['total_amount'],
        $stats['goal_amount'],
        $percentage
    );
}
?>

<?php if ($display === 'text') : ?>
    <div class="wp-crowdfundtime-progress-text">
        <?php echo esc_html($text); ?>
    </div>
<?php else : ?>
    <div class="wp-crowdfundtime-progress-container" data-campaign-id="<?php echo esc_attr($campaign->campaign_id); ?>" data-type="<?php echo esc_attr($type); ?>">
        <div class="wp-crowdfundtime-progress-bar">
            <div class="wp-crowdfundtime-progress-fill <?php echo esc_attr($class); ?>" style="width: <?php echo esc_attr($percentage); ?>%"></div>
        </div>
        <div class="wp-crowdfundtime-progress-text">
            <?php echo esc_html($text); ?>
        </div>
    </div>
<?php endif; ?>
