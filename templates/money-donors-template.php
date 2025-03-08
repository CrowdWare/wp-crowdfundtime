<?php
/**
 * Template for the money donors list.
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

// Get the campaign and stripe orders
if (!isset($campaign) || !$campaign || !isset($stripe_orders)) {
    return;
}
?>

<div class="wp-crowdfundtime-money-donors-container" data-campaign-id="<?php echo esc_attr($campaign->campaign_id); ?>">
    <h3><?php echo esc_html__('Money Donors', 'wp-crowdfundtime'); ?></h3>
    
    <?php if (empty($stripe_orders)) : ?>
        <p><?php echo esc_html__('No money donations yet. Be the first to donate!', 'wp-crowdfundtime'); ?></p>
    <?php else : ?>
        <table class="wp-crowdfundtime-donors-table wp-crowdfundtime-money-donors-table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Name', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Amount', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Product', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Date', 'wp-crowdfundtime'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stripe_orders as $order) : 
                    $order_data = $order['order_data'];
                    $is_test = isset($order_data['is_live']) && $order_data['is_live'] == 0;
                ?>
                    <tr<?php echo $is_test ? ' class="test-mode-order"' : ''; ?>>
                        <td>
                            <?php echo esc_html($order_data['customer_name']); ?>
                            <?php if ($is_test) : ?>
                                <span class="test-mode-badge" title="<?php echo esc_attr__('Test Mode', 'wp-crowdfundtime'); ?>">[<?php echo esc_html__('Test', 'wp-crowdfundtime'); ?>]</span>
                            <?php endif; ?>
                        </td>
                        <td><?php 
                            if (class_exists('AcceptStripePayments')) {
                                echo esc_html(AcceptStripePayments::formatted_price($order_data['paid_amount'], $order_data['currency_code']));
                            } else {
                                echo esc_html(number_format($order_data['paid_amount'], 2) . ' ' . $order_data['currency_code']);
                            }
                        ?></td>
                        <td><?php echo esc_html($order_data['item_name']); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), $order['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
