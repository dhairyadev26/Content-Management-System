<?php
// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

// Updated:  - Add PHP for bulk actions

// Updated:  - Improve PHP for admin interface

// Updated:  - Refactor PHP for performance

/**
 * Plugin Name: Advanced Bulk Content Management
 * Plugin URI: https://example.com/plugins/advanced-bulk-content-management
 * Description: Advanced management tools for WordPress content with bulk actions and filtering
 * Version: 0.1.0
 * Author: dhairyadev26
 * Author URI: https://example.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: advanced-bulk-content-management
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('ABCM_VERSION', '0.1.0');
define('ABCM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ABCM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once ABCM_PLUGIN_DIR . 'includes/class-admin-page.php';

// Load plugin textdomain
add_action('plugins_loaded', 'abcm_load_textdomain');

/**
 * Load plugin textdomain
 */
function abcm_load_textdomain() {
    load_plugin_textdomain(
        'advanced-bulk-content-management',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
}

// Register admin menu
add_action('admin_menu', 'abcm_register_admin_menu');

/**
 * Register admin menu
 */
function abcm_register_admin_menu() {
    // Add top level menu
    add_menu_page(
        'Advanced Bulk Content Management',
        'Bulk Content',
        'manage_options',
        'advanced-bulk-content-management',
        'abcm_admin_page_callback',
        'dashicons-database'
    );
}

/**
 * Admin page callback
 */
function abcm_admin_page_callback() {
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'advanced-bulk-content-management'));
    }
    
    // Create admin page instance
    $admin_page = new ABCM_Admin_Page();
    
    // Render admin page
    $admin_page->render();
}


// Updated: 2025-07-01T10:45:00 - Enqueue admin page JavaScript



























// Updated: 2025-06-30T16:45:00 - Add version check for WordPress compatibility


// Updated: 2025-07-30T11:15:00 - Final code review and cleanup


// Updated: 2025-07-30T15:30:00 - Version bump to 0.2.0


// Updated: 2025-07-31T14:00:00 - Prepare for initial release
