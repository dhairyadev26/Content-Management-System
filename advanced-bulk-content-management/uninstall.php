<?php
/**
 * Uninstall script for Advanced Bulk Content Management
 *
 * @package AdvancedBulkContentManagement
 */

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('abcm_settings');

// Add more clean up code as needed
