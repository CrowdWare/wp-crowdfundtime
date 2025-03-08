<?php
/**
 * Template for the donors list.
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

// Get the campaign and donations
if (!isset($campaign) || !$campaign || !isset($donations)) {
    return;
}
?>

<div class="wp-crowdfundtime-donors-container" data-campaign-id="<?php echo esc_attr($campaign->campaign_id); ?>">
    <h3><?php echo esc_html__('Zeit-Spenden', 'wp-crowdfundtime'); ?></h3>
    
    <?php if (empty($donations)) : ?>
        <p><?php echo esc_html__('No donations yet. Be the first to donate!', 'wp-crowdfundtime'); ?></p>
    <?php else : ?>
        <table class="wp-crowdfundtime-donors-table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Name', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Hours', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Social Media', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Other Support', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Date', 'wp-crowdfundtime'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donations as $donation) : ?>
                    <tr>
                        <td><?php echo esc_html($donation->name); ?></td>
                        <td><?php echo esc_html($donation->hours); ?></td>
                        <td class="social-icons">
                            <?php if ($donation->facebook_post) : ?>
                                <span class="social-icon facebook-icon" title="<?php echo esc_attr__('Facebook Posts', 'wp-crowdfundtime'); ?>"></span>
                            <?php endif; ?>
                            
                            <?php if ($donation->x_post) : ?>
                                <span class="social-icon x-icon" title="<?php echo esc_attr__('X Post', 'wp-crowdfundtime'); ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($donation->other_support); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($donation->created_at))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
