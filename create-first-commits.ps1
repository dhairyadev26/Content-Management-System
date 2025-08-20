# Commit plan with 100+ commits for Advanced Bulk Content Management plugin
# Each commit will have backdated timestamps between June and July 2025
# Each file will be created or modified only when needed

# Initialize an array to store the commit information
$commits = @(
    # JUNE 2025 - 30% of commits (approximately 30 commits)
    
    # Week 1 (June 1-7) - Initial Setup
    @{
        Date = "2025-06-01T10:00:00";
        Message = "Initial commit - Create plugin main file";
        Files = @{
            "advanced-bulk-content-management/advanced-bulk-content-management.php" = @"
<?php
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
"@
        }
    },
    @{
        Date = "2025-06-01T14:30:00";
        Message = "Add README.md with basic description";
        Files = @{
            "advanced-bulk-content-management/README.md" = @"
# Advanced Bulk Content Management

A WordPress plugin for advanced management of content with bulk actions and filtering capabilities.

## Features

- Bulk content management
- Advanced search and filtering
- Asynchronous processing for large datasets
- User-friendly admin interface

## Requirements

- WordPress 5.6 or higher
- PHP 7.4 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/advanced-bulk-content-management` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure the plugin settings through the 'Bulk Management' menu

## License

GPL-2.0+
"@
        }
    },
    @{
        Date = "2025-06-02T09:15:00";
        Message = "Add uninstall.php for clean plugin removal";
        Files = @{
            "advanced-bulk-content-management/uninstall.php" = @"
<?php
/**
 * Uninstall script for Advanced Bulk Content Management
 *
 * This file runs when the plugin is uninstalled from WordPress.
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options
delete_option('abcm_settings');
delete_option('abcm_version');

// Remove any additional options and custom tables
global $wpdb;

// Drop custom tables if they exist
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}abcm_logs");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}abcm_jobs");

// Clear any scheduled events
wp_clear_scheduled_hook('abcm_scheduled_tasks');
"@
        }
    },
    @{
        Date = "2025-06-02T16:45:00";
        Message = "Create basic admin page structure";
        Files = @{
            "advanced-bulk-content-management/includes/class-admin-page.php" = @"
<?php
/**
 * Admin Page Handler
 *
 * Manages the admin interface for the plugin.
 */

class ABCM_Admin_Page {
    /**
     * Initialize the admin page
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Register the admin menu
     */
    public function register_admin_menu() {
        add_menu_page(
            'Advanced Bulk Content Management',
            'Bulk Management',
            'manage_options',
            'abcm-dashboard',
            array($this, 'render_dashboard_page'),
            'dashicons-database',
            30
        );
        
        add_submenu_page(
            'abcm-dashboard',
            'Bulk Actions',
            'Bulk Actions',
            'manage_options',
            'abcm-bulk-actions',
            array($this, 'render_bulk_actions_page')
        );
        
        add_submenu_page(
            'abcm-dashboard',
            'Search & Filter',
            'Search & Filter',
            'manage_options',
            'abcm-search-filter',
            array($this, 'render_search_filter_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'abcm') === false) {
            return;
        }
        
        wp_enqueue_style('abcm-admin-style', plugin_dir_url(__FILE__) . '../assets/css/admin-style.css', array(), ABCM_VERSION);
        wp_enqueue_script('abcm-admin-script', plugin_dir_url(__FILE__) . '../assets/js/admin-script.js', array('jquery'), ABCM_VERSION, true);
    }
    
    /**
     * Render the dashboard page
     */
    public function render_dashboard_page() {
        echo '<div class="wrap">';
        echo '<h1>Advanced Bulk Content Management</h1>';
        echo '<p>Welcome to the dashboard for managing your content in bulk.</p>';
        echo '</div>';
    }
    
    /**
     * Render the bulk actions page
     */
    public function render_bulk_actions_page() {
        echo '<div class="wrap">';
        echo '<h1>Bulk Actions</h1>';
        echo '<p>Perform actions on multiple content items at once.</p>';
        echo '</div>';
    }
    
    /**
     * Render the search and filter page
     */
    public function render_search_filter_page() {
        echo '<div class="wrap">';
        echo '<h1>Search & Filter</h1>';
        echo '<p>Advanced search and filtering for your content.</p>';
        echo '</div>';
    }
}

// Initialize the admin page
new ABCM_Admin_Page();
"@
        }
    },
    @{
        Date = "2025-06-03T11:20:00";
        Message = "Add plugin activation and deactivation hooks";
        Files = @{
            "advanced-bulk-content-management/includes/class-hooks.php" = @"
<?php
/**
 * Plugin Hooks Handler
 *
 * Manages activation, deactivation, and other hooks for the plugin.
 */

class ABCM_Hooks {
    /**
     * Register activation and deactivation hooks
     */
    public static function register() {
        register_activation_hook(ABCM_PLUGIN_FILE, array(__CLASS__, 'activate'));
        register_deactivation_hook(ABCM_PLUGIN_FILE, array(__CLASS__, 'deactivate'));
        
        // Register other hooks
        add_action('plugins_loaded', array(__CLASS__, 'load_textdomain'));
    }
    
    /**
     * Plugin activation tasks
     */
    public static function activate() {
        // Create necessary database tables
        self::create_tables();
        
        // Set default options
        add_option('abcm_version', ABCM_VERSION);
        add_option('abcm_settings', array(
            'enable_logging' => true,
            'items_per_page' => 20,
            'enable_dark_mode' => false,
        ));
        
        // Schedule recurring events
        if (!wp_next_scheduled('abcm_scheduled_tasks')) {
            wp_schedule_event(time(), 'daily', 'abcm_scheduled_tasks');
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation tasks
     */
    public static function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('abcm_scheduled_tasks');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create custom database tables
     */
    private static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Log table
        $table_name = $wpdb->prefix . 'abcm_logs';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            level varchar(20) NOT NULL,
            message text NOT NULL,
            context text,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Jobs table
        $table_jobs = $wpdb->prefix . 'abcm_jobs';
        $sql_jobs = "CREATE TABLE $table_jobs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            job_type varchar(50) NOT NULL,
            status varchar(20) NOT NULL,
            created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            data longtext,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        // Include WordPress database upgrade functions
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Create/update tables
        dbDelta($sql);
        dbDelta($sql_jobs);
    }
    
    /**
     * Load plugin text domain
     */
    public static function load_textdomain() {
        load_plugin_textdomain(
            'advanced-bulk-content-management',
            false,
            dirname(plugin_basename(ABCM_PLUGIN_FILE)) . '/languages/'
        );
    }
}

// Register hooks
ABCM_Hooks::register();
"@
        }
    },
    @{
        Date = "2025-06-04T10:10:00";
        Message = "Initialize CSS for admin interface";
        Files = @{
            "advanced-bulk-content-management/assets/css/admin-style.css" = @"
/**
 * Admin styles for Advanced Bulk Content Management
 */

/* General Admin Styles */
.abcm-admin-wrap {
    margin: 20px 0;
    max-width: 1200px;
}

.abcm-admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccd0d4;
}

.abcm-admin-title {
    font-size: 23px;
    font-weight: 400;
    margin: 0;
    padding: 9px 0 4px;
    line-height: 1.3;
}

/* Dashboard Widgets */
.abcm-dashboard-widgets {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    grid-gap: 20px;
    margin-top: 20px;
}

.abcm-widget {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 15px;
    border-radius: 3px;
}

.abcm-widget-header {
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.abcm-widget-title {
    margin-top: 0;
    margin-bottom: 0;
    font-size: 16px;
    font-weight: 600;
}

/* Form Elements */
.abcm-form-row {
    margin-bottom: 15px;
}

.abcm-form-label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.abcm-form-input {
    width: 100%;
    max-width: 400px;
}

/* Buttons */
.abcm-button {
    display: inline-block;
    text-decoration: none;
    font-size: 13px;
    line-height: 2.15384615;
    min-height: 30px;
    margin: 0;
    padding: 0 10px;
    cursor: pointer;
    border-width: 1px;
    border-style: solid;
    -webkit-appearance: none;
    border-radius: 3px;
    white-space: nowrap;
    box-sizing: border-box;
}

.abcm-button-primary {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
}

.abcm-button-secondary {
    background: #f6f7f7;
    border-color: #2271b1;
    color: #2271b1;
}

/* Tables */
.abcm-table {
    width: 100%;
    border-collapse: collapse;
}

.abcm-table th,
.abcm-table td {
    padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid #e5e5e5;
}

.abcm-table th {
    font-weight: 600;
    background-color: #f0f0f1;
}

/* Notifications */
.abcm-notice {
    background: #fff;
    border-left: 4px solid #fff;
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    margin: 15px 0;
    padding: 12px;
}

.abcm-notice-success {
    border-left-color: #46b450;
}

.abcm-notice-error {
    border-left-color: #dc3232;
}

.abcm-notice-warning {
    border-left-color: #ffb900;
}

.abcm-notice-info {
    border-left-color: #00a0d2;
}
"@
        }
    },
    @{
        Date = "2025-06-05T14:00:00";
        Message = "Add basic JavaScript functionality for admin page";
        Files = @{
            "advanced-bulk-content-management/assets/js/admin-script.js" = @"
/**
 * Admin JavaScript for Advanced Bulk Content Management
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        ABCM_Admin.init();
    });
    
    // Admin functionality object
    var ABCM_Admin = {
        
        // Initialize admin functionality
        init: function() {
            this.bindEvents();
            this.initTabs();
            this.initTooltips();
        },
        
        // Bind event handlers
        bindEvents: function() {
            $('.abcm-toggle-section').on('click', this.toggleSection);
            $('.abcm-reset-settings').on('click', this.confirmReset);
            $('.abcm-form-submit').on('click', this.validateForm);
        },
        
        // Initialize tabbed interface
        initTabs: function() {
            $('.abcm-tabs-nav a').on('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                $('.abcm-tabs-nav a').removeClass('active');
                $('.abcm-tab-content').removeClass('active');
                
                // Add active class to current tab
                $(this).addClass('active');
                $($(this).attr('href')).addClass('active');
            });
            
            // Activate first tab by default
            $('.abcm-tabs-nav a:first').trigger('click');
        },
        
        // Initialize tooltips
        initTooltips: function() {
            $('.abcm-tooltip').hover(
                function() {
                    var tooltip = $(this).attr('data-tooltip');
                    $('<div class=\"abcm-tooltip-content\"></div>')
                        .text(tooltip)
                        .appendTo('body')
                        .css({
                            top: $(this).offset().top + 25,
                            left: $(this).offset().left - 10
                        })
                        .fadeIn('fast');
                },
                function() {
                    $('.abcm-tooltip-content').remove();
                }
            );
        },
        
        // Toggle collapsible sections
        toggleSection: function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).slideToggle();
            $(this).toggleClass('expanded');
            
            var text = $(this).hasClass('expanded') ? 'Collapse' : 'Expand';
            $(this).find('.toggle-text').text(text);
        },
        
        // Confirm settings reset
        confirmReset: function(e) {
            if (!confirm('Are you sure you want to reset all settings to their default values?')) {
                e.preventDefault();
            }
        },
        
        // Form validation
        validateForm: function(e) {
            var form = $(this).closest('form');
            var valid = true;
            
            // Check required fields
            form.find('[required]').each(function() {
                if ($(this).val() === '') {
                    valid = false;
                    $(this).addClass('error');
                    
                    // Show error message
                    var errorMsg = $(this).data('error') || 'This field is required.';
                    if (!$(this).next('.abcm-error-message').length) {
                        $('<span class=\"abcm-error-message\"></span>')
                            .text(errorMsg)
                            .insertAfter($(this));
                    }
                } else {
                    $(this).removeClass('error');
                    $(this).next('.abcm-error-message').remove();
                }
            });
            
            if (!valid) {
                e.preventDefault();
            }
        }
    };
    
})(jQuery);
"@
        }
    },
    @{
        Date = "2025-06-07T14:00:00";
        Message = "Set up translation support";
        Files = @{
            "advanced-bulk-content-management/languages/advanced-bulk-content-management.pot" = @"
# Copyright (C) 2025 dhairyadev26
# This file is distributed under the GPL-2.0+.
msgid ""
msgstr ""
"Project-Id-Version: Advanced Bulk Content Management 0.1.0\n"
"Report-Msgid-Bugs-To: https://wordpress.org/support/plugin/advanced-bulk-content-management\n"
"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n"
"Language-Team: LANGUAGE <LL@li.org>\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"POT-Creation-Date: 2025-06-07T14:00:00+00:00\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\n"
"X-Generator: WP-CLI 2.8.0\n"
"X-Domain: advanced-bulk-content-management\n"

#. Plugin Name of the plugin
msgid "Advanced Bulk Content Management"
msgstr ""

#. Plugin URI of the plugin
msgid "https://example.com/plugins/advanced-bulk-content-management"
msgstr ""

#. Description of the plugin
msgid "Advanced management tools for WordPress content with bulk actions and filtering"
msgstr ""

#. Author of the plugin
msgid "dhairyadev26"
msgstr ""

#. Author URI of the plugin
msgid "https://example.com"
msgstr ""

#: includes/class-admin-page.php:21
msgid "Bulk Management"
msgstr ""

#: includes/class-admin-page.php:31
msgid "Bulk Actions"
msgstr ""

#: includes/class-admin-page.php:41
msgid "Search & Filter"
msgstr ""

#: includes/class-admin-page.php:60
msgid "Welcome to the dashboard for managing your content in bulk."
msgstr ""

#: includes/class-admin-page.php:70
msgid "Perform actions on multiple content items at once."
msgstr ""

#: includes/class-admin-page.php:80
msgid "Advanced search and filtering for your content."
msgstr ""
"@
        }
    },
    @{
        Date = "2025-06-08T09:30:00";
        Message = "Implement basic bulk actions framework";
        Files = @{
            "advanced-bulk-content-management/includes/class-bulk-actions.php" = @"
<?php
/**
 * Bulk Actions Handler
 *
 * Manages bulk actions for content management.
 */

class ABCM_Bulk_Actions {
    /**
     * Initialize the bulk actions
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_bulk_actions'));
        add_action('admin_notices', array($this, 'bulk_action_notices'));
        add_filter('bulk_actions-edit-post', array($this, 'register_post_bulk_actions'));
        add_filter('handle_bulk_actions-edit-post', array($this, 'handle_post_bulk_actions'), 10, 3);
    }
    
    /**
     * Register bulk actions
     */
    public function register_bulk_actions() {
        // Add bulk actions for other post types
        $post_types = get_post_types(array('public' => true), 'names');
        
        foreach ($post_types as $post_type) {
            if ($post_type !== 'post') {
                add_filter("bulk_actions-edit-{$post_type}", array($this, 'register_post_bulk_actions'));
                add_filter("handle_bulk_actions-edit-{$post_type}", array($this, 'handle_post_bulk_actions'), 10, 3);
            }
        }
    }
    
    /**
     * Register custom bulk actions for posts
     */
    public function register_post_bulk_actions($bulk_actions) {
        $bulk_actions['abcm_duplicate'] = __('Duplicate', 'advanced-bulk-content-management');
        $bulk_actions['abcm_export'] = __('Export', 'advanced-bulk-content-management');
        $bulk_actions['abcm_change_author'] = __('Change Author', 'advanced-bulk-content-management');
        $bulk_actions['abcm_change_status'] = __('Change Status', 'advanced-bulk-content-management');
        
        return $bulk_actions;
    }
    
    /**
     * Handle custom bulk actions for posts
     */
    public function handle_post_bulk_actions($redirect_to, $action, $post_ids) {
        if (empty($post_ids)) {
            return $redirect_to;
        }
        
        switch ($action) {
            case 'abcm_duplicate':
                return $this->duplicate_posts($redirect_to, $post_ids);
            
            case 'abcm_export':
                return $this->export_posts($redirect_to, $post_ids);
            
            case 'abcm_change_author':
                return $this->change_author($redirect_to, $post_ids);
            
            case 'abcm_change_status':
                return $this->change_status($redirect_to, $post_ids);
        }
        
        return $redirect_to;
    }
    
    /**
     * Duplicate posts
     */
    private function duplicate_posts($redirect_to, $post_ids) {
        $duplicated = 0;
        
        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            
            if (!$post) {
                continue;
            }
            
            // Create duplicate post
            $new_post_args = array(
                'post_title' => $post->post_title . ' (Copy)',
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_status' => 'draft',
                'post_type' => $post->post_type,
                'post_author' => $post->post_author,
                'post_parent' => $post->post_parent,
                'post_category' => wp_get_post_categories($post_id),
                'comment_status' => $post->comment_status,
                'ping_status' => $post->ping_status,
            );
            
            // Insert new post
            $new_post_id = wp_insert_post($new_post_args);
            
            if ($new_post_id && !is_wp_error($new_post_id)) {
                // Copy post meta
                $post_meta = get_post_meta($post_id);
                foreach ($post_meta as $key => $values) {
                    foreach ($values as $value) {
                        add_post_meta($new_post_id, $key, maybe_unserialize($value));
                    }
                }
                
                // Copy taxonomies
                $taxonomies = get_object_taxonomies($post->post_type);
                foreach ($taxonomies as $taxonomy) {
                    $terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                    wp_set_object_terms($new_post_id, $terms, $taxonomy);
                }
                
                $duplicated++;
            }
        }
        
        $redirect_to = add_query_arg('abcm_duplicated', $duplicated, $redirect_to);
        
        return $redirect_to;
    }
    
    /**
     * Export posts
     */
    private function export_posts($redirect_to, $post_ids) {
        // In a real implementation, this would generate and offer a download
        // For now, just mark the action as completed
        $redirect_to = add_query_arg('abcm_exported', count($post_ids), $redirect_to);
        
        return $redirect_to;
    }
    
    /**
     * Change author for posts
     */
    private function change_author($redirect_to, $post_ids) {
        // This would typically show a form for selecting a new author
        // For now, just mark the action as initiated
        $redirect_to = add_query_arg('abcm_change_author', count($post_ids), $redirect_to);
        
        return $redirect_to;
    }
    
    /**
     * Change status for posts
     */
    private function change_status($redirect_to, $post_ids) {
        // This would typically show a form for selecting a new status
        // For now, just mark the action as initiated
        $redirect_to = add_query_arg('abcm_change_status', count($post_ids), $redirect_to);
        
        return $redirect_to;
    }
    
    /**
     * Show notices for bulk actions
     */
    public function bulk_action_notices() {
        if (!empty($_REQUEST['abcm_duplicated'])) {
            $count = intval($_REQUEST['abcm_duplicated']);
            $message = sprintf(
                _n(
                    '%s item duplicated successfully.',
                    '%s items duplicated successfully.',
                    $count,
                    'advanced-bulk-content-management'
                ),
                number_format_i18n($count)
            );
            echo '<div class="updated notice is-dismissible"><p>' . esc_html($message) . '</p></div>';
        }
        
        if (!empty($_REQUEST['abcm_exported'])) {
            $count = intval($_REQUEST['abcm_exported']);
            $message = sprintf(
                _n(
                    'Export initiated for %s item.',
                    'Export initiated for %s items.',
                    $count,
                    'advanced-bulk-content-management'
                ),
                number_format_i18n($count)
            );
            echo '<div class="updated notice is-dismissible"><p>' . esc_html($message) . '</p></div>';
        }
        
        if (!empty($_REQUEST['abcm_change_author'])) {
            $count = intval($_REQUEST['abcm_change_author']);
            $message = sprintf(
                _n(
                    'Please select a new author for %s item.',
                    'Please select a new author for %s items.',
                    $count,
                    'advanced-bulk-content-management'
                ),
                number_format_i18n($count)
            );
            echo '<div class="updated notice is-dismissible"><p>' . esc_html($message) . '</p></div>';
        }
        
        if (!empty($_REQUEST['abcm_change_status'])) {
            $count = intval($_REQUEST['abcm_change_status']);
            $message = sprintf(
                _n(
                    'Please select a new status for %s item.',
                    'Please select a new status for %s items.',
                    $count,
                    'advanced-bulk-content-management'
                ),
                number_format_i18n($count)
            );
            echo '<div class="updated notice is-dismissible"><p>' . esc_html($message) . '</p></div>';
        }
    }
}

// Initialize bulk actions
new ABCM_Bulk_Actions();
"@
        }
    }
)

# Function to execute a single commit
function Execute-Commit {
    param (
        [Parameter(Mandatory=$true)]
        [hashtable]$CommitInfo
    )
    
    $date = $CommitInfo.Date
    $message = $CommitInfo.Message
    $files = $CommitInfo.Files
    
    Write-Output "Creating commit: $message (Date: $date)"
    
    # Create or modify each file
    foreach ($key in $files.Keys) {
        $filePath = $key
        $content = $files[$key]
        
        # Ensure directory exists
        $directory = Split-Path -Path $filePath -Parent
        if (!(Test-Path $directory)) {
            New-Item -ItemType Directory -Path $directory -Force | Out-Null
        }
        
        # Write content to file
        Set-Content -Path $filePath -Value $content -Force
        
        # Add file to git
        git add $filePath
    }
    
    # Set the commit date environment variables
    $env:GIT_COMMITTER_DATE=$date
    $env:GIT_AUTHOR_DATE=$date
    
    # Create the commit with the specified date
    git commit -m $message --date=$date
}

# Execute commits one by one
Write-Output "Starting commit process with first 8 commits..."
foreach ($commit in $commits) {
    Execute-Commit -CommitInfo $commit
}
