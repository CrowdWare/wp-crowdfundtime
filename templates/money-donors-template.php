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

// Get the campaign and donations
if (!isset($campaign) || !$campaign || !isset($stripe_orders)) {
    return;
}
?>

<div class="wp-crowdfundtime-donors-container" data-campaign-id="<?php echo esc_attr($campaign->campaign_id); ?>">
    <h3><?php echo esc_html__('Geld-Spenden', 'wp-crowdfundtime'); ?></h3>
    
    <?php if (empty($stripe_orders)) : ?>
        <p><?php echo esc_html__('No money donations yet. Be the first to donate!', 'wp-crowdfundtime'); ?></p>
    <?php else : ?>
        <table class="wp-crowdfundtime-donors-table money-donors-table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Name', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Amount', 'wp-crowdfundtime'); ?></th>
                    <th><?php echo esc_html__('Date', 'wp-crowdfundtime'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stripe_orders as $order) : 
                    $order_data = $order['order_data'];
                    $name = isset($order_data['customer_name']) ? $order_data['customer_name'] : __('Anonymous', 'wp-crowdfundtime');
                    $amount = isset($order_data['paid_amount']) ? floatval($order_data['paid_amount']) : 0;
                    $currency = isset($order_data['currency']) ? strtoupper($order_data['currency']) : 'EUR';
                    $date = isset($order['created_at']) ? $order['created_at'] : time();
                ?>
                    <tr>
                        <td><?php echo esc_html($name); ?></td>
                        <td><?php echo esc_html(number_format($amount, 2) . ' ' . $currency); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), $date)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
