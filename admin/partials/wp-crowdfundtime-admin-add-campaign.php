<?php
/**
 * Admin add/edit campaign page template.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    WP_CrowdFundTime
 * @subpackage WP_CrowdFundTime/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Check if we're editing an existing campaign
$is_edit = isset($campaign) && $campaign;
$campaign_id = $is_edit ? $campaign->campaign_id : 0;
$title = $is_edit ? $campaign->title : '';
$description = $is_edit ? $campaign->description : '';
$goal_hours = $is_edit ? $campaign->goal_hours : 0;
$goal_amount = $is_edit ? $campaign->goal_amount : 0.00;
$start_date = $is_edit && $campaign->start_date ? date('Y-m-d', strtotime($campaign->start_date)) : '';
$end_date = $is_edit && $campaign->end_date ? date('Y-m-d', strtotime($campaign->end_date)) : '';
$page_id = $is_edit ? $campaign->page_id : 0;

// Get all pages for the dropdown
$pages = get_pages();
?>

<div class="wrap wp-crowdfundtime-admin-wrap">
    <div class="wp-crowdfundtime-admin-header">
        <h1><?php echo $is_edit ? esc_html__('Edit Campaign', 'wp-crowdfundtime') : esc_html__('Add New Campaign', 'wp-crowdfundtime'); ?></h1>
    </div>
    
    <div class="wp-crowdfundtime-admin-notices"></div>
    
    <form id="wp-crowdfundtime-campaign-form" class="wp-crowdfundtime-campaign-form">
        <?php wp_nonce_field('wp_crowdfundtime_campaign_form', 'wp_crowdfundtime_campaign_nonce'); ?>
        <input type="hidden" name="campaign_id" value="<?php echo esc_attr($campaign_id); ?>">
        
        <div class="form-field">
            <label for="title"><?php echo esc_html__('Title', 'wp-crowdfundtime'); ?> *</label>
            <input type="text" name="title" id="title" value="<?php echo esc_attr($title); ?>" required>
        </div>
        
        <div class="form-field">
            <label for="description"><?php echo esc_html__('Description', 'wp-crowdfundtime'); ?></label>
            <textarea name="description" id="description" rows="5"><?php echo esc_textarea($description); ?></textarea>
        </div>
        
        <div class="form-field">
            <label for="goal_hours"><?php echo esc_html__('Hours Goal', 'wp-crowdfundtime'); ?></label>
            <input type="number" name="goal_hours" id="goal_hours" min="0" value="<?php echo esc_attr($goal_hours); ?>">
        </div>
        
        <div class="form-field">
            <label for="goal_amount"><?php echo esc_html__('Money Goal (€)', 'wp-crowdfundtime'); ?></label>
            <input type="number" name="goal_amount" id="goal_amount" min="0" step="0.01" value="<?php echo esc_attr($goal_amount); ?>">
        </div>
        
        <div class="form-field">
            <label for="start_date"><?php echo esc_html__('Start Date', 'wp-crowdfundtime'); ?></label>
            <input type="date" name="start_date" id="start_date" class="wp-crowdfundtime-datepicker" value="<?php echo esc_attr($start_date); ?>">
        </div>
        
        <div class="form-field">
            <label for="end_date"><?php echo esc_html__('End Date', 'wp-crowdfundtime'); ?></label>
            <input type="date" name="end_date" id="end_date" class="wp-crowdfundtime-datepicker" value="<?php echo esc_attr($end_date); ?>">
        </div>
        
        <div class="form-field">
            <label for="page_id"><?php echo esc_html__('Associated Page', 'wp-crowdfundtime'); ?></label>
            <select name="page_id" id="page_id" class="wp-crowdfundtime-page-select">
                <option value=""><?php echo esc_html__('-- Select Page --', 'wp-crowdfundtime'); ?></option>
                <?php foreach ($pages as $page) : ?>
                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($page_id, $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php echo esc_html__('Select the page where this campaign will be displayed.', 'wp-crowdfundtime'); ?></p>
        </div>
        
        <?php
        // Check if Stripe integration is enabled
        $stripe_integration = get_option('wp_crowdfundtime_stripe_integration', 0);
        
        if ($stripe_integration) {
            // Get Stripe products
            $stripe_products = array();
            
            // Check if ASPMain class exists
            if (class_exists('ASPMain')) {
                $args = array(
                    'post_type' => ASPMain::$products_slug,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC',
                );
                $products_query = new WP_Query($args);
                if ($products_query->have_posts()) {
                    while ($products_query->have_posts()) {
                        $products_query->the_post();
                        $stripe_products[get_the_ID()] = get_the_title();
                    }
                    wp_reset_postdata();
                }
            }
            
            // Get selected Stripe product IDs
            $selected_product_ids = array();
            if ($is_edit) {
                $selected_product_ids = get_post_meta($campaign_id, 'stripe_product_ids', true);
                if (!empty($selected_product_ids) && !is_array($selected_product_ids)) {
                    $selected_product_ids = explode(',', $selected_product_ids);
                }
                if (empty($selected_product_ids)) {
                    $selected_product_ids = array();
                }
            }
            ?>
            
            <div class="form-field">
                <label for="stripe_product_ids"><?php echo esc_html__('Associated Stripe Products', 'wp-crowdfundtime'); ?></label>
                <?php if (!empty($stripe_products)) : ?>
                    <select name="stripe_product_ids[]" id="stripe_product_ids" class="wp-crowdfundtime-product-select" multiple="multiple" style="min-height: 100px; width: 100%;">
                        <?php foreach ($stripe_products as $product_id => $product_title) : ?>
                            <option value="<?php echo esc_attr($product_id); ?>" <?php echo in_array($product_id, $selected_product_ids) ? 'selected="selected"' : ''; ?>><?php echo esc_html($product_title); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description"><?php echo esc_html__('Select the Stripe products that are associated with this campaign. Hold Ctrl/Cmd to select multiple products.', 'wp-crowdfundtime'); ?></p>
                <?php else : ?>
                    <p><?php echo esc_html__('No Stripe products found. Please create some products in the Stripe Payments plugin first.', 'wp-crowdfundtime'); ?></p>
                <?php endif; ?>
            </div>
        <?php } ?>
        
        <div class="form-field submit-button">
            <button type="submit" class="button button-primary">
                <?php echo $is_edit ? esc_html__('Update Campaign', 'wp-crowdfundtime') : esc_html__('Create Campaign', 'wp-crowdfundtime'); ?>
            </button>
        </div>
    </form>
    
    <?php if ($is_edit) : ?>
        <div class="wp-crowdfundtime-shortcodes-info">
            <h3><?php echo esc_html__('Shortcodes for this Campaign', 'wp-crowdfundtime'); ?></h3>
            <p><?php echo esc_html__('Use the following shortcodes to display this campaign on your pages:', 'wp-crowdfundtime'); ?></p>
            <ul>
                <li><code>[crowdfundtime_form id=<?php echo esc_html($campaign_id); ?>]</code> - <?php echo esc_html__('Displays the donation form.', 'wp-crowdfundtime'); ?></li>
                <li><code>[crowdfundtime_donors id=<?php echo esc_html($campaign_id); ?> type=time]</code> - <?php echo esc_html__('Displays the time donors list.', 'wp-crowdfundtime'); ?></li>
                <li><code>[crowdfundtime_donors id=<?php echo esc_html($campaign_id); ?> type=money]</code> - <?php echo esc_html__('Displays the money donors list.', 'wp-crowdfundtime'); ?></li>
                <li><code>[crowdfundtime_donors id=<?php echo esc_html($campaign_id); ?> type=both]</code> - <?php echo esc_html__('Displays both time and money donors lists.', 'wp-crowdfundtime'); ?></li>
                <li><code>[crowdfundtime_progress id=<?php echo esc_html($campaign_id); ?> type=hours display=bar]</code> - <?php echo esc_html__('Displays the hours progress bar.', 'wp-crowdfundtime'); ?></li>
                <li><code>[crowdfundtime_progress id=<?php echo esc_html($campaign_id); ?> type=money display=bar]</code> - <?php echo esc_html__('Displays the money progress bar.', 'wp-crowdfundtime'); ?></li>
            </ul>
        </div>
    <?php endif; ?>
</div>
