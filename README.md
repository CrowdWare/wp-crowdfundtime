# WP CrowdFundTime

WP CrowdFundTime is a WordPress plugin for time-based crowdfunding campaigns where users can donate their time instead of money. Users can promote campaigns or products through social media posts.

## Description

WP CrowdFundTime allows you to create crowdfunding campaigns where supporters can donate their time rather than money. This is perfect for community projects, non-profits, or any initiative that values time contributions.

### Key Features

- **Time-based Donations**: Collect time commitments from supporters
- **Social Media Integration**: Supporters can pledge to promote on Facebook and X (Twitter)
- **Campaign Management**: Create and manage multiple campaigns
- **Progress Tracking**: Display progress towards time and monetary goals
- **Donor Lists**: Show all time donors in a scrollable table
- **Shortcodes**: Easy integration with any WordPress page or post
- **Export Functionality**: Export donor data as PDF
- **Responsive Design**: Works on all devices

## Installation

1. Upload the `wp-crowdfundtime` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'CrowdFundTime' in your admin menu to create your first campaign

## Usage

### Creating a Campaign

1. Go to CrowdFundTime > Add Campaign in your WordPress admin
2. Fill in the campaign details:
   - Title
   - Description
   - Hours Goal
   - Money Goal (optional, for integration with Stripe)
   - Start/End Dates (optional)
   - Associated Page (optional)
3. Click "Create Campaign"

### Adding Campaign Elements to Pages

Use these shortcodes to display campaign elements on your pages:

- `[crowdfundtime_form id=X]` - Displays the donation form for campaign X
- `[crowdfundtime_donors id=X]` - Displays the donors list for campaign X
- `[crowdfundtime_progress id=X type=hours display=bar]` - Displays the hours progress bar
- `[crowdfundtime_progress id=X type=money display=bar]` - Displays the money progress bar

### Donation Form

The donation form includes:
- Name field (required)
- Email field (required)
- Checkboxes for "Facebook Posts" and "X Post"
- Text field for "Sonstiges" (Other support)
- Numeric field for "Stunden" (Hours) (minimum 1, required)
- "Zeit spenden" (Donate time) button

### Managing Donations

1. Go to CrowdFundTime > Campaigns in your WordPress admin
2. View campaign statistics and donor information
3. Export donor lists as PDF

## Customization

You can customize the plugin's appearance by overriding the CSS styles in your theme.

## Integration with Stripe

If you're using the Stripe payment plugin for monetary donations, WP CrowdFundTime can display the total amount donated alongside time donations.

## Creating a ZIP File

To create a distributable ZIP file of the plugin:

1. Navigate to the plugin directory
2. Run the included script: `./create-zip.sh`
3. The script will create a ZIP file named `wp-crowdfundtime-1.0.0.zip` (or with your current version number)

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by Your Name
