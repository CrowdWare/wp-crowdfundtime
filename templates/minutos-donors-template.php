<?php
/**
 * Template for the Minutos donors list.
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
if (!isset($campaign) || !$campaign || !isset($minutos_donations)) {
    return;
}
?>

<div class="wp-crowdfundtime-donors-container" data-campaign-id="<?php echo esc_attr($campaign->campaign_id); ?>">
    <h3><?php echo esc_html__('Minutos-Spenden', 'wp-crowdfundtime'); ?></h3>
    
    <?php if (empty($minutos_donations)) : ?>
        <p><?php echo esc_html__('No Minutos donations yet. Be the first to donate!', 'wp-crowdfundtime'); ?></p>
    <?php else : ?>
        <table class="wp-crowdfundtime-donors-table minutos-donors-table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Name', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Minutos', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Value (€)', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Status', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Social Media', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Other Support', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Date', 'wp-crowdfundtime'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($minutos_donations as $donation) : ?>
                    <tr class="<?php echo $donation->minutos_received ? 'minutos-received' : 'minutos-pending'; ?>">
                        <td><?php echo esc_html($donation->name); ?></td>
                        <td><?php echo esc_html($donation->minutos); ?></td>
                        <td><?php echo esc_html(number_format($donation->minutos / 2, 2)); ?> €</td>
                        <td>
                            <?php if ($donation->minutos_received) : ?>
                                <span class="minutos-status received" title="<?php echo esc_attr__('Minutos Received', 'wp-crowdfundtime'); ?>"><?php echo esc_html__('Received', 'wp-crowdfundtime'); ?></span>
                            <?php else : ?>
                                <span class="minutos-status pending" title="<?php echo esc_attr__('Minutos Pending', 'wp-crowdfundtime'); ?>"><?php echo esc_html__('Pending', 'wp-crowdfundtime'); ?></span>
                            <?php endif; ?>
                        </td>
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
