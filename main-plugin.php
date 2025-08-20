<?php
/**
 * Advanced Bulk Content Management
 *
 * A powerful WordPress plugin that provides advanced content management capabilities 
 * with bulk operations, intelligent filtering, and streamlined workflow automation.
 *
 * @package     Advanced_Bulk_Content_Management
 * @author      dhairyadev26
 * @copyright   2025 dhairyadev26
 * @license     GPL-2.0+
 * @version     0.2.0
 *
 * @wordpress-plugin
 * Plugin Name:         Advanced Bulk Content Management
 * Plugin URI:          https://github.com/dhairyadev26/Content-Management-System
 * Description:         Advanced management tools for WordPress content with bulk actions, intelligent filtering, import/export capabilities, scheduling, and user permission management. Features accessibility support, dark mode, mobile responsiveness, and performance optimization.
 * Version:             0.2.0
 * Requires at least:   5.0
 * Requires PHP:        7.4
 * Author:              dhairyadev26
 * Author URI:          https://github.com/dhairyadev26
 * License:             GPL v2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         advanced-bulk-content-management
 * Domain Path:         /languages
 * Network:             false
 * Update URI:          https://github.com/dhairyadev26/Content-Management-System
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die('Direct access not allowed.');
}

/**
 * Plugin version.
 * Used for cache-busting of style and script file references.
 */
define('ABCM_VERSION', '0.2.0');

/**
 * Plugin file path.
 * Used for activation, deactivation, and uninstall hooks.
 */
define('ABCM_PLUGIN_FILE', __FILE__);

/**
 * Plugin directory path.
 * Used for including files and assets.
 */
define('ABCM_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * Plugin directory URL.
 * Used for loading assets.
 */
define('ABCM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Plugin basename.
 * Used for plugin identification in WordPress.
 */
define('ABCM_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Database table names.
 */
define('ABCM_LOGS_TABLE', 'abcm_logs');
define('ABCM_JOBS_TABLE', 'abcm_jobs');
define('ABCM_SETTINGS_TABLE', 'abcm_settings');

/**
 * Plugin capabilities.
 */
define('ABCM_CAPABILITY_MANAGE', 'abcm_manage_content');
define('ABCM_CAPABILITY_BULK_EDIT', 'abcm_bulk_edit');
define('ABCM_CAPABILITY_BULK_DELETE', 'abcm_bulk_delete');
define('ABCM_CAPABILITY_IMPORT_EXPORT', 'abcm_import_export');
define('ABCM_CAPABILITY_SCHEDULE', 'abcm_schedule_jobs');
define('ABCM_CAPABILITY_SETTINGS', 'abcm_manage_settings');
define('ABCM_CAPABILITY_LOGS', 'abcm_view_logs');

/**
 * Debug mode.
 * Set to true to enable debug logging.
 */
if (!defined('ABCM_DEBUG')) {
    define('ABCM_DEBUG', false);
}

/**
 * Minimum requirements check.
 */
function abcm_requirements_check() {
    $errors = array();
    
    // Check WordPress version
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        $errors[] = __('WordPress 5.0 or higher is required.', 'advanced-bulk-content-management');
    }
    
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        $errors[] = __('PHP 7.4 or higher is required.', 'advanced-bulk-content-management');
    }
    
    // Check required PHP extensions
    $required_extensions = array('json', 'mbstring');
    foreach ($required_extensions as $extension) {
        if (!extension_loaded($extension)) {
            $errors[] = sprintf(__('PHP extension %s is required.', 'advanced-bulk-content-management'), $extension);
        }
    }
    
    return $errors;
}

/**
 * Display admin notice for requirement errors.
 */
function abcm_requirements_notice() {
    $errors = abcm_requirements_check();
    if (!empty($errors)) {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>' . __('Advanced Bulk Content Management plugin cannot be activated:', 'advanced-bulk-content-management') . '</strong><br>';
        echo implode('<br>', $errors);
        echo '</p></div>';
        
        // Deactivate the plugin
        deactivate_plugins(ABCM_PLUGIN_BASENAME);
    }
}

// Check requirements before loading
$requirement_errors = abcm_requirements_check();
if (!empty($requirement_errors)) {
    add_action('admin_notices', 'abcm_requirements_notice');
    return;
}

/**
 * Include required files
 */
require_once ABCM_PLUGIN_DIR . 'includes/class-admin-page.php';
require_once ABCM_PLUGIN_DIR . 'includes/class-bulk-actions.php';
require_once ABCM_PLUGIN_DIR . 'includes/class-search-filter.php';
require_once ABCM_PLUGIN_DIR . 'includes/class-async-jobs.php';
require_once ABCM_PLUGIN_DIR . 'includes/class-hooks.php';
require_once ABCM_PLUGIN_DIR . 'includes/class-logger.php';

/**
 * Main plugin class
 */
class Advanced_Bulk_Content_Management {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Admin page instance
     */
    public $admin_page;
    
    /**
     * Bulk actions instance
     */
    public $bulk_actions;
    
    /**
     * Search filter instance
     */
    public $search_filter;
    
    /**
     * Async jobs instance
     */
    public $async_jobs;
    
    /**
     * Logger instance
     */
    public $logger;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->init_components();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(ABCM_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(ABCM_PLUGIN_FILE, array($this, 'deactivate'));
        
        // Plugin loaded hook
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // AJAX hooks
        add_action('wp_ajax_abcm_load_content', array($this, 'ajax_load_content'));
        add_action('wp_ajax_abcm_execute_action', array($this, 'ajax_execute_action'));
        add_action('wp_ajax_abcm_get_job_status', array($this, 'ajax_get_job_status'));
        
        // Cron hooks
        add_action('abcm_process_jobs', array($this, 'process_scheduled_jobs'));
        
        // Plugin row meta
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
    }
    
    /**
     * Initialize plugin components
     */
    private function init_components() {
        $this->logger = new ABCM_Logger();
        $this->bulk_actions = new ABCM_Bulk_Actions($this->logger);
        $this->search_filter = new ABCM_Search_Filter();
        $this->async_jobs = new ABCM_Async_Jobs($this->logger);
        $this->admin_page = new ABCM_Admin_Page($this->bulk_actions, $this->search_filter, $this->async_jobs);
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        $this->create_tables();
        
        // Add capabilities to administrator role
        $this->add_capabilities();
        
        // Schedule cron job
        if (!wp_next_scheduled('abcm_process_jobs')) {
            wp_schedule_event(time(), 'hourly', 'abcm_process_jobs');
        }
        
        // Set default options
        $this->set_default_options();
        
        // Log activation
        if (class_exists('ABCM_Logger')) {
            $logger = new ABCM_Logger();
            $logger->log('Plugin activated', 'info', array('version' => ABCM_VERSION));
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear scheduled hooks
        wp_clear_scheduled_hook('abcm_process_jobs');
        
        // Log deactivation
        if (class_exists('ABCM_Logger')) {
            $logger = new ABCM_Logger();
            $logger->log('Plugin deactivated', 'info');
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'advanced-bulk-content-management',
            false,
            dirname(ABCM_PLUGIN_BASENAME) . '/languages/'
        );
    }
    
    /**
     * Register admin menu
     */
    public function register_admin_menu() {
        // Main menu page
        add_menu_page(
            __('Bulk Content Manager', 'advanced-bulk-content-management'),
            __('Bulk Content Manager', 'advanced-bulk-content-management'),
            ABCM_CAPABILITY_MANAGE,
            'abcm-bulk-manager',
            array($this->admin_page, 'render_admin_page'),
            'dashicons-admin-tools',
            30
        );
        
        // Settings submenu
        add_submenu_page(
            'abcm-bulk-manager',
            __('Settings', 'advanced-bulk-content-management'),
            __('Settings', 'advanced-bulk-content-management'),
            ABCM_CAPABILITY_SETTINGS,
            'abcm-settings',
            array($this->admin_page, 'render_settings_page')
        );
        
        // Logs submenu
        add_submenu_page(
            'abcm-bulk-manager',
            __('Operation Logs', 'advanced-bulk-content-management'),
            __('Logs', 'advanced-bulk-content-management'),
            ABCM_CAPABILITY_LOGS,
            'abcm-logs',
            array($this->admin_page, 'render_logs_page')
        );
        
        // Jobs submenu
        add_submenu_page(
            'abcm-bulk-manager',
            __('Scheduled Jobs', 'advanced-bulk-content-management'),
            __('Jobs', 'advanced-bulk-content-management'),
            ABCM_CAPABILITY_SCHEDULE,
            'abcm-jobs',
            array($this->admin_page, 'render_jobs_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'abcm-') === false) {
            return;
        }
        
        // Enqueue styles
        wp_enqueue_style(
            'abcm-admin-style',
            ABCM_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            ABCM_VERSION
        );
        
        wp_enqueue_style(
            'abcm-bulk-actions',
            ABCM_PLUGIN_URL . 'assets/css/bulk-actions.css',
            array(),
            ABCM_VERSION
        );
        
        wp_enqueue_style(
            'abcm-search-filter',
            ABCM_PLUGIN_URL . 'assets/css/search-filter.css',
            array(),
            ABCM_VERSION
        );
        
        // Enqueue scripts
        wp_enqueue_script(
            'abcm-admin-script',
            ABCM_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery', 'wp-util'),
            ABCM_VERSION,
            true
        );
        
        wp_enqueue_script(
            'abcm-bulk-actions',
            ABCM_PLUGIN_URL . 'assets/js/bulk-actions.js',
            array('jquery'),
            ABCM_VERSION,
            true
        );
        
        wp_enqueue_script(
            'abcm-search-filter',
            ABCM_PLUGIN_URL . 'assets/js/search-filter.js',
            array('jquery'),
            ABCM_VERSION,
            true
        );
        
        wp_enqueue_script(
            'abcm-accessibility',
            ABCM_PLUGIN_URL . 'assets/js/accessibility.js',
            array('jquery'),
            ABCM_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('abcm-admin-script', 'abcm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('abcm_ajax_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete the selected items? This action cannot be undone.', 'advanced-bulk-content-management'),
                'processing' => __('Processing...', 'advanced-bulk-content-management'),
                'completed' => __('Operation completed successfully.', 'advanced-bulk-content-management'),
                'error' => __('An error occurred. Please try again.', 'advanced-bulk-content-management'),
                'no_items_selected' => __('Please select at least one item.', 'advanced-bulk-content-management'),
                'no_action_selected' => __('Please select an action to perform.', 'advanced-bulk-content-management')
            )
        ));
    }
    
    /**
     * AJAX: Load content
     */
    public function ajax_load_content() {
        check_ajax_referer('abcm_ajax_nonce', 'nonce');
        
        if (!current_user_can(ABCM_CAPABILITY_MANAGE)) {
            wp_die(__('Insufficient permissions.', 'advanced-bulk-content-management'));
        }
        
        $post_type = sanitize_text_field($_POST['post_type'] ?? 'post');
        $filters = $_POST['filters'] ?? array();
        $page = intval($_POST['page'] ?? 1);
        
        $results = $this->search_filter->get_filtered_posts(array(
            'post_type' => $post_type,
            'filters' => $filters,
            'page' => $page,
            'per_page' => 50
        ));
        
        wp_send_json_success($results);
    }
    
    /**
     * AJAX: Execute action
     */
    public function ajax_execute_action() {
        check_ajax_referer('abcm_ajax_nonce', 'nonce');
        
        $action = sanitize_text_field($_POST['action_type'] ?? '');
        $post_ids = array_map('intval', $_POST['post_ids'] ?? array());
        $data = $_POST['data'] ?? array();
        
        // Verify user can perform action
        if (!$this->user_can_perform_action($action, $post_ids)) {
            wp_send_json_error(__('Insufficient permissions for this action.', 'advanced-bulk-content-management'));
        }
        
        $results = $this->bulk_actions->execute_action($action, $post_ids, $data);
        
        if ($results['success']) {
            wp_send_json_success($results);
        } else {
            wp_send_json_error($results);
        }
    }
    
    /**
     * AJAX: Get job status
     */
    public function ajax_get_job_status() {
        check_ajax_referer('abcm_ajax_nonce', 'nonce');
        
        if (!current_user_can(ABCM_CAPABILITY_SCHEDULE)) {
            wp_die(__('Insufficient permissions.', 'advanced-bulk-content-management'));
        }
        
        $job_id = intval($_POST['job_id'] ?? 0);
        $status = $this->async_jobs->get_job_status($job_id);
        
        wp_send_json_success($status);
    }
    
    /**
     * Process scheduled jobs
     */
    public function process_scheduled_jobs() {
        $this->async_jobs->process_jobs();
    }
    
    /**
     * Add plugin row meta links
     */
    public function plugin_row_meta($links, $file) {
        if (ABCM_PLUGIN_BASENAME === $file) {
            $row_meta = array(
                'docs' => '<a href="https://github.com/dhairyadev26/Content-Management-System/blob/main/README.md" target="_blank">' . __('Documentation', 'advanced-bulk-content-management') . '</a>',
                'support' => '<a href="https://github.com/dhairyadev26/Content-Management-System/issues" target="_blank">' . __('Support', 'advanced-bulk-content-management') . '</a>',
            );
            return array_merge($links, $row_meta);
        }
        return $links;
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Logs table
        $logs_table = $wpdb->prefix . ABCM_LOGS_TABLE;
        $logs_sql = "CREATE TABLE $logs_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            operation varchar(100) NOT NULL,
            operation_type varchar(50) NOT NULL,
            user_id bigint(20) NOT NULL,
            post_ids longtext,
            operation_data longtext,
            results longtext,
            status varchar(20) DEFAULT 'completed',
            message text,
            execution_time decimal(10,4),
            memory_usage varchar(20),
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY operation (operation),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Jobs table
        $jobs_table = $wpdb->prefix . ABCM_JOBS_TABLE;
        $jobs_sql = "CREATE TABLE $jobs_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            job_type varchar(100) NOT NULL,
            priority int(11) DEFAULT 10,
            status varchar(20) DEFAULT 'pending',
            schedule_time datetime NOT NULL,
            start_time datetime DEFAULT NULL,
            end_time datetime DEFAULT NULL,
            data longtext,
            result longtext,
            error_message text,
            retry_count int(11) DEFAULT 0,
            max_retries int(11) DEFAULT 3,
            created_by bigint(20) NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime DEFAULT NULL,
            PRIMARY KEY (id),
            KEY job_type (job_type),
            KEY status (status),
            KEY schedule_time (schedule_time),
            KEY created_by (created_by)
        ) $charset_collate;";
        
        // Settings table
        $settings_table = $wpdb->prefix . ABCM_SETTINGS_TABLE;
        $settings_sql = "CREATE TABLE $settings_table (
            setting_name varchar(100) NOT NULL,
            setting_value longtext,
            setting_type varchar(20) DEFAULT 'string',
            autoload varchar(3) DEFAULT 'yes',
            created_at datetime NOT NULL,
            updated_at datetime DEFAULT NULL,
            PRIMARY KEY (setting_name),
            KEY autoload (autoload)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($logs_sql);
        dbDelta($jobs_sql);
        dbDelta($settings_sql);
    }
    
    /**
     * Add capabilities to administrator role
     */
    private function add_capabilities() {
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $capabilities = array(
                ABCM_CAPABILITY_MANAGE,
                ABCM_CAPABILITY_BULK_EDIT,
                ABCM_CAPABILITY_BULK_DELETE,
                ABCM_CAPABILITY_IMPORT_EXPORT,
                ABCM_CAPABILITY_SCHEDULE,
                ABCM_CAPABILITY_SETTINGS,
                ABCM_CAPABILITY_LOGS
            );
            
            foreach ($capabilities as $cap) {
                $admin_role->add_cap($cap);
            }
        }
    }
    
    /**
     * Set default plugin options
     */
    private function set_default_options() {
        $default_settings = array(
            'batch_size' => 50,
            'timeout' => 300,
            'enable_logging' => true,
            'log_retention_days' => 30,
            'default_post_type' => 'post',
            'performance_mode' => 'standard',
            'enable_dark_mode' => false,
            'enable_accessibility' => true
        );
        
        add_option('abcm_settings', $default_settings);
        add_option('abcm_version', ABCM_VERSION);
    }
    
    /**
     * Check if user can perform action
     */
    private function user_can_perform_action($action, $post_ids) {
        // Check general capability
        if (!current_user_can(ABCM_CAPABILITY_MANAGE)) {
            return false;
        }
        
        // Check action-specific capabilities
        switch ($action) {
            case 'bulk_delete':
                if (!current_user_can(ABCM_CAPABILITY_BULK_DELETE)) {
                    return false;
                }
                break;
            case 'bulk_edit':
                if (!current_user_can(ABCM_CAPABILITY_BULK_EDIT)) {
                    return false;
                }
                break;
        }
        
        // Check if user can edit individual posts
        foreach ($post_ids as $post_id) {
            if (!current_user_can('edit_post', $post_id)) {
                return false;
            }
        }
        
        return true;
    }
}

/**
 * Initialize the plugin
 */
function abcm_init() {
    return Advanced_Bulk_Content_Management::get_instance();
}

// Initialize the plugin
add_action('init', 'abcm_init');

/**
 * Plugin activation hook
 */
function abcm_activate() {
    $plugin = Advanced_Bulk_Content_Management::get_instance();
    $plugin->activate();
}
register_activation_hook(__FILE__, 'abcm_activate');

/**
 * Plugin deactivation hook
 */
function abcm_deactivate() {
    $plugin = Advanced_Bulk_Content_Management::get_instance();
    $plugin->deactivate();
}
register_deactivation_hook(__FILE__, 'abcm_deactivate');
