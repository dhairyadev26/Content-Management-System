# Developer Documentation

This documentation is intended for developers who want to extend, customize, or integrate with the Advanced Bulk Content Management plugin.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Hooks and Filters](#hooks-and-filters)
3. [Class Reference](#class-reference)
4. [Database Schema](#database-schema)
5. [API Endpoints](#api-endpoints)
6. [Custom Extensions](#custom-extensions)
7. [Testing](#testing)

## Architecture Overview

### Plugin Structure

```
advanced-bulk-content-management/
├── advanced-bulk-content-management.php    # Main plugin file
├── uninstall.php                          # Cleanup on uninstall
├── assets/                                # Frontend assets
│   ├── css/                              # Stylesheets
│   │   ├── admin-style.css               # Main admin styles
│   │   ├── bulk-actions.css              # Bulk action styles
│   │   └── search-filter.css             # Filter interface styles
│   └── js/                               # JavaScript files
│       ├── admin-script.js               # Main admin script
│       ├── bulk-actions.js               # Bulk action handlers
│       ├── search-filter.js              # Filter functionality
│       └── accessibility.js             # Accessibility features
├── includes/                             # Core classes
│   ├── class-admin-page.php              # Admin interface
│   ├── class-bulk-actions.php            # Bulk operations
│   ├── class-search-filter.php           # Filtering system
│   ├── class-async-jobs.php              # Background processing
│   ├── class-hooks.php                   # WordPress hooks
│   └── class-logger.php                  # Logging system
├── languages/                            # Internationalization
│   └── advanced-bulk-content-management.pot
├── tests/                                # Unit tests
├── docs/                                 # Documentation
└── README.md                             # Main documentation
```

### Core Classes

#### ABCM_Admin_Page
Handles the main admin interface and user interactions.

```php
class ABCM_Admin_Page {
    public function __construct();
    public function render_admin_page();
    public function handle_bulk_action();
    public function enqueue_assets();
}
```

#### ABCM_Bulk_Actions
Manages all bulk operations on content.

```php
class ABCM_Bulk_Actions {
    public function execute_action($action, $post_ids, $data);
    public function bulk_edit($post_ids, $edit_data);
    public function bulk_delete($post_ids, $options);
    public function bulk_publish($post_ids);
}
```

#### ABCM_Search_Filter
Handles content filtering and search functionality.

```php
class ABCM_Search_Filter {
    public function apply_filters($query_args, $filters);
    public function get_filtered_posts($args);
    public function build_meta_query($meta_filters);
    public function build_tax_query($tax_filters);
}
```

### Data Flow

```
User Request → Admin Page → Bulk Actions → Database
                    ↓
             Search Filter ← Query Builder
                    ↓
            Async Jobs (if needed) → Logger
```

## Hooks and Filters

### Action Hooks

#### Core Operations
```php
// Before bulk operation starts
do_action('abcm_before_bulk_operation', $operation, $post_ids, $data);

// After bulk operation completes
do_action('abcm_after_bulk_operation', $operation, $results, $data);

// Before individual post processing
do_action('abcm_before_post_process', $post_id, $operation, $data);

// After individual post processing
do_action('abcm_after_post_process', $post_id, $operation, $result);

// When operation fails
do_action('abcm_operation_failed', $operation, $error, $post_ids);
```

#### Admin Interface
```php
// Before admin page renders
do_action('abcm_before_admin_page');

// After admin page renders
do_action('abcm_after_admin_page');

// Before filter form renders
do_action('abcm_before_filter_form');

// After filter form renders
do_action('abcm_after_filter_form');
```

#### Import/Export
```php
// Before import starts
do_action('abcm_before_import', $import_data, $options);

// After import completes
do_action('abcm_after_import', $results, $options);

// Before export starts
do_action('abcm_before_export', $export_args);

// After export completes
do_action('abcm_after_export', $export_file, $args);
```

### Filter Hooks

#### Bulk Actions
```php
// Modify available bulk actions
apply_filters('abcm_bulk_actions', $actions, $post_type);

// Customize bulk action data
apply_filters('abcm_bulk_action_data', $data, $action, $post_ids);

// Modify batch size
apply_filters('abcm_batch_size', $size, $operation, $post_type);

// Filter operation results
apply_filters('abcm_operation_results', $results, $operation, $post_ids);
```

#### Filtering System
```php
// Modify available filters
apply_filters('abcm_filter_options', $filters, $post_type);

// Customize filter queries
apply_filters('abcm_filter_query_args', $query_args, $filters);

// Modify meta query
apply_filters('abcm_meta_query', $meta_query, $meta_filters);

// Modify taxonomy query
apply_filters('abcm_tax_query', $tax_query, $tax_filters);
```

#### User Interface
```php
// Customize admin page content
apply_filters('abcm_admin_page_content', $content, $page);

// Modify form fields
apply_filters('abcm_form_fields', $fields, $context);

// Customize table columns
apply_filters('abcm_table_columns', $columns, $post_type);

// Filter admin notices
apply_filters('abcm_admin_notices', $notices, $context);
```

### Example Implementations

#### Adding Custom Bulk Action
```php
// Add custom action to actions list
function add_custom_bulk_action($actions, $post_type) {
    if ($post_type === 'post') {
        $actions['custom_action'] = __('Custom Action', 'textdomain');
    }
    return $actions;
}
add_filter('abcm_bulk_actions', 'add_custom_bulk_action', 10, 2);

// Handle custom action execution
function handle_custom_bulk_action($post_ids, $action_data) {
    foreach ($post_ids as $post_id) {
        // Your custom logic here
        update_post_meta($post_id, 'custom_field', 'custom_value');
    }
}
add_action('abcm_execute_custom_action', 'handle_custom_bulk_action', 10, 2);
```

#### Adding Custom Filter
```php
// Add custom filter option
function add_custom_filter($filters, $post_type) {
    $filters['custom_filter'] = array(
        'label' => __('Custom Filter', 'textdomain'),
        'type' => 'select',
        'options' => array(
            'option1' => 'Option 1',
            'option2' => 'Option 2'
        )
    );
    return $filters;
}
add_filter('abcm_filter_options', 'add_custom_filter', 10, 2);

// Apply custom filter to query
function apply_custom_filter($query_args, $filters) {
    if (!empty($filters['custom_filter'])) {
        $query_args['meta_query'][] = array(
            'key' => 'custom_field',
            'value' => $filters['custom_filter'],
            'compare' => '='
        );
    }
    return $query_args;
}
add_filter('abcm_filter_query_args', 'apply_custom_filter', 10, 2);
```

## Class Reference

### ABCM_Admin_Page

#### Properties
```php
private $page_slug = 'abcm-bulk-manager';
private $capability = 'manage_options';
private $bulk_actions;
private $search_filter;
```

#### Methods
```php
public function __construct()
// Initialize admin page

public function render_admin_page()
// Render main admin interface

public function handle_ajax_request()
// Process AJAX requests

public function enqueue_assets($page)
// Load CSS and JavaScript files

private function render_filter_form()
// Display filter interface

private function render_content_table()
// Display content list with checkboxes

private function process_bulk_action()
// Handle bulk action execution
```

### ABCM_Bulk_Actions

#### Properties
```php
private $batch_size = 50;
private $timeout = 300;
private $logger;
```

#### Methods
```php
public function execute_action($action, $post_ids, $data = array())
// Execute bulk action on selected posts

public function bulk_edit($post_ids, $edit_data)
// Edit multiple posts simultaneously

public function bulk_delete($post_ids, $options = array())
// Delete multiple posts

public function bulk_status_change($post_ids, $new_status)
// Change post status in bulk

public function bulk_author_change($post_ids, $new_author_id)
// Change post author in bulk

public function bulk_taxonomy_update($post_ids, $taxonomy_data)
// Update taxonomies in bulk

private function process_batch($post_ids, $callback, $data)
// Process posts in batches to avoid timeouts

private function validate_operation($action, $post_ids, $data)
// Validate operation before execution
```

### ABCM_Search_Filter

#### Properties
```php
private $default_filters = array();
private $post_types = array();
private $meta_keys = array();
```

#### Methods
```php
public function get_filtered_posts($args = array())
// Get posts based on applied filters

public function apply_filters($query_args, $filters)
// Apply filters to WordPress query

public function get_filter_options($post_type)
// Get available filter options for post type

public function build_meta_query($meta_filters)
// Build meta query from filter data

public function build_tax_query($tax_filters)
// Build taxonomy query from filter data

public function build_date_query($date_filters)
// Build date query from filter data

private function sanitize_filters($filters)
// Sanitize filter input data

private function validate_filters($filters, $post_type)
// Validate filter values
```

### ABCM_Async_Jobs

#### Properties
```php
private $job_table = 'abcm_jobs';
private $max_execution_time = 300;
private $max_memory_usage = '256M';
```

#### Methods
```php
public function schedule_job($job_type, $data, $schedule = 'now')
// Schedule background job

public function process_jobs()
// Process pending jobs

public function get_job_status($job_id)
// Get status of specific job

public function cancel_job($job_id)
// Cancel pending job

public function retry_failed_job($job_id)
// Retry failed job

private function execute_job($job_data)
// Execute individual job

private function update_job_status($job_id, $status, $result = null)
// Update job status in database

private function cleanup_completed_jobs()
// Remove old completed jobs
```

## Database Schema

### Custom Tables

#### abcm_logs
```sql
CREATE TABLE wp_abcm_logs (
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
);
```

#### abcm_jobs
```sql
CREATE TABLE wp_abcm_jobs (
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
);
```

#### abcm_settings
```sql
CREATE TABLE wp_abcm_settings (
    setting_name varchar(100) NOT NULL,
    setting_value longtext,
    setting_type varchar(20) DEFAULT 'string',
    autoload varchar(3) DEFAULT 'yes',
    created_at datetime NOT NULL,
    updated_at datetime DEFAULT NULL,
    PRIMARY KEY (setting_name),
    KEY autoload (autoload)
);
```

### WordPress Options
```php
// Plugin settings stored in wp_options
'abcm_version' => '0.2.0'
'abcm_settings' => array(
    'batch_size' => 50,
    'timeout' => 300,
    'enable_logging' => true,
    'log_retention' => 30, // days
    'default_post_type' => 'post',
    'user_permissions' => array(),
    'performance_mode' => 'standard'
)
'abcm_user_preferences' => array(
    // User-specific settings
)
```

## API Endpoints

### REST API Routes

#### Get Filtered Posts
```
GET /wp-json/abcm/v1/posts
Parameters:
- post_type: string (default: 'post')
- filters: array of filter conditions
- page: int (default: 1)
- per_page: int (default: 50)

Response:
{
    "posts": [...],
    "total": 150,
    "pages": 3,
    "current_page": 1
}
```

#### Execute Bulk Action
```
POST /wp-json/abcm/v1/bulk-action
Body:
{
    "action": "bulk_edit",
    "post_ids": [1, 2, 3],
    "data": {
        "post_status": "publish"
    }
}

Response:
{
    "success": true,
    "results": {
        "processed": 3,
        "successful": 3,
        "failed": 0,
        "errors": []
    }
}
```

#### Import Data
```
POST /wp-json/abcm/v1/import
Body: FormData with file upload
Parameters:
- format: csv|json|xml
- mapping: field mapping configuration
- options: import options

Response:
{
    "job_id": 123,
    "status": "processing",
    "estimated_time": 300
}
```

#### Get Job Status
```
GET /wp-json/abcm/v1/jobs/{job_id}

Response:
{
    "job_id": 123,
    "status": "completed",
    "progress": 100,
    "results": {...},
    "created_at": "2025-08-20T10:00:00Z",
    "completed_at": "2025-08-20T10:05:00Z"
}
```

### AJAX Endpoints

#### Load Content
```javascript
// JavaScript usage
wp.ajax.post('abcm_load_content', {
    post_type: 'post',
    filters: filters,
    page: 1
}).done(function(response) {
    // Handle response
});
```

#### Execute Action
```javascript
wp.ajax.post('abcm_execute_action', {
    action: 'bulk_edit',
    post_ids: [1, 2, 3],
    data: editData,
    nonce: abcm_ajax.nonce
}).done(function(response) {
    // Handle response
});
```

## Custom Extensions

### Creating Custom Bulk Actions

#### Step 1: Register Action
```php
function register_custom_action() {
    add_filter('abcm_bulk_actions', function($actions, $post_type) {
        $actions['my_custom_action'] = __('My Custom Action', 'textdomain');
        return $actions;
    }, 10, 2);
}
add_action('init', 'register_custom_action');
```

#### Step 2: Handle Action Execution
```php
function handle_custom_action($post_ids, $data) {
    $results = array(
        'processed' => 0,
        'successful' => 0,
        'failed' => 0,
        'errors' => array()
    );
    
    foreach ($post_ids as $post_id) {
        $results['processed']++;
        
        try {
            // Your custom logic here
            $success = perform_custom_operation($post_id, $data);
            
            if ($success) {
                $results['successful']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to process post {$post_id}";
            }
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = "Error processing post {$post_id}: " . $e->getMessage();
        }
    }
    
    return $results;
}
add_action('abcm_execute_my_custom_action', 'handle_custom_action', 10, 2);
```

#### Step 3: Add Configuration Form (Optional)
```php
function custom_action_form_fields($fields, $action) {
    if ($action === 'my_custom_action') {
        $fields['custom_field'] = array(
            'type' => 'text',
            'label' => __('Custom Field Value', 'textdomain'),
            'description' => __('Enter value for custom field', 'textdomain'),
            'required' => true
        );
    }
    return $fields;
}
add_filter('abcm_action_form_fields', 'custom_action_form_fields', 10, 2);
```

### Creating Custom Filters

#### Step 1: Register Filter
```php
function register_custom_filter() {
    add_filter('abcm_filter_options', function($filters, $post_type) {
        $filters['my_custom_filter'] = array(
            'label' => __('My Custom Filter', 'textdomain'),
            'type' => 'select',
            'options' => get_custom_filter_options(),
            'description' => __('Filter by custom criteria', 'textdomain')
        );
        return $filters;
    }, 10, 2);
}
add_action('init', 'register_custom_filter');
```

#### Step 2: Apply Filter to Query
```php
function apply_custom_filter($query_args, $filters) {
    if (!empty($filters['my_custom_filter'])) {
        // Modify query arguments based on filter value
        $query_args['meta_query'][] = array(
            'key' => 'custom_meta_key',
            'value' => $filters['my_custom_filter'],
            'compare' => '='
        );
    }
    return $query_args;
}
add_filter('abcm_filter_query_args', 'apply_custom_filter', 10, 2);
```

### Integration with Third-Party Plugins

#### WooCommerce Integration
```php
function integrate_woocommerce() {
    // Add product-specific bulk actions
    add_filter('abcm_bulk_actions', function($actions, $post_type) {
        if ($post_type === 'product') {
            $actions['update_stock'] = __('Update Stock', 'textdomain');
            $actions['change_price'] = __('Change Price', 'textdomain');
        }
        return $actions;
    }, 10, 2);
    
    // Handle product stock update
    add_action('abcm_execute_update_stock', function($post_ids, $data) {
        foreach ($post_ids as $post_id) {
            $product = wc_get_product($post_id);
            if ($product) {
                $product->set_stock_quantity($data['stock_quantity']);
                $product->save();
            }
        }
    }, 10, 2);
}
add_action('init', 'integrate_woocommerce');
```

#### ACF Integration
```php
function integrate_acf() {
    // Add ACF field filters
    add_filter('abcm_filter_options', function($filters, $post_type) {
        $field_groups = acf_get_field_groups();
        foreach ($field_groups as $group) {
            $fields = acf_get_fields($group['key']);
            foreach ($fields as $field) {
                $filters["acf_{$field['name']}"] = array(
                    'label' => $field['label'],
                    'type' => 'text',
                    'description' => "Filter by ACF field: {$field['label']}"
                );
            }
        }
        return $filters;
    }, 10, 2);
    
    // Apply ACF field filters
    add_filter('abcm_filter_query_args', function($query_args, $filters) {
        foreach ($filters as $key => $value) {
            if (strpos($key, 'acf_') === 0 && !empty($value)) {
                $field_name = substr($key, 4);
                $query_args['meta_query'][] = array(
                    'key' => $field_name,
                    'value' => $value,
                    'compare' => 'LIKE'
                );
            }
        }
        return $query_args;
    }, 10, 2);
}
add_action('init', 'integrate_acf');
```

## Testing

### Unit Testing Setup

#### PHPUnit Configuration
```xml
<!-- phpunit.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true">
    <testsuites>
        <testsuite name="ABCM Test Suite">
            <directory>./tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

#### Test Bootstrap
```php
// tests/bootstrap.php
<?php
// Load WordPress test environment
$_tests_dir = getenv('WP_TESTS_DIR');
if (!$_tests_dir) {
    $_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
    require dirname(__FILE__) . '/../advanced-bulk-content-management.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require $_tests_dir . '/includes/bootstrap.php';
```

#### Example Test Class
```php
// tests/test-bulk-actions.php
<?php
class Test_ABCM_Bulk_Actions extends WP_UnitTestCase {
    
    private $bulk_actions;
    
    public function setUp() {
        parent::setUp();
        $this->bulk_actions = new ABCM_Bulk_Actions();
    }
    
    public function test_bulk_edit() {
        // Create test posts
        $post_ids = $this->factory->post->create_many(3);
        
        // Test bulk edit
        $edit_data = array('post_status' => 'draft');
        $result = $this->bulk_actions->bulk_edit($post_ids, $edit_data);
        
        // Assert results
        $this->assertEquals(3, $result['successful']);
        $this->assertEquals(0, $result['failed']);
        
        // Verify posts were updated
        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            $this->assertEquals('draft', $post->post_status);
        }
    }
    
    public function test_bulk_delete() {
        // Create test posts
        $post_ids = $this->factory->post->create_many(2);
        
        // Test bulk delete
        $result = $this->bulk_actions->bulk_delete($post_ids);
        
        // Assert results
        $this->assertEquals(2, $result['successful']);
        
        // Verify posts were deleted
        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            $this->assertEquals('trash', $post->post_status);
        }
    }
}
```

### Integration Testing

#### AJAX Testing
```javascript
// tests/js/test-ajax.js
QUnit.test('AJAX bulk action', function(assert) {
    var done = assert.async();
    
    wp.ajax.post('abcm_execute_action', {
        action: 'bulk_edit',
        post_ids: [1, 2, 3],
        data: {post_status: 'publish'},
        nonce: abcm_test.nonce
    }).done(function(response) {
        assert.ok(response.success, 'AJAX request successful');
        assert.equal(response.data.processed, 3, 'All posts processed');
        done();
    }).fail(function() {
        assert.ok(false, 'AJAX request failed');
        done();
    });
});
```

#### Performance Testing
```php
// tests/test-performance.php
class Test_ABCM_Performance extends WP_UnitTestCase {
    
    public function test_large_bulk_operation() {
        // Create 1000 test posts
        $post_ids = $this->factory->post->create_many(1000);
        
        // Measure execution time
        $start_time = microtime(true);
        $memory_start = memory_get_usage();
        
        $bulk_actions = new ABCM_Bulk_Actions();
        $result = $bulk_actions->bulk_edit($post_ids, array('post_status' => 'draft'));
        
        $execution_time = microtime(true) - $start_time;
        $memory_used = memory_get_usage() - $memory_start;
        
        // Assert performance criteria
        $this->assertLessThan(30, $execution_time, 'Operation completed within 30 seconds');
        $this->assertLessThan(50 * 1024 * 1024, $memory_used, 'Memory usage under 50MB');
        $this->assertEquals(1000, $result['successful'], 'All posts processed successfully');
    }
}
```

### Manual Testing Checklist

#### Functionality Testing
- [ ] Basic bulk operations (edit, delete, publish)
- [ ] Advanced filtering (date, author, taxonomy, meta)
- [ ] Import/export functionality
- [ ] Scheduled operations
- [ ] User permission system
- [ ] Error handling and recovery

#### User Interface Testing
- [ ] Responsive design on different screen sizes
- [ ] Accessibility features (keyboard navigation, screen readers)
- [ ] Dark mode toggle
- [ ] Form validation and user feedback
- [ ] Loading states and progress indicators

#### Performance Testing
- [ ] Large dataset handling (1000+ posts)
- [ ] Memory usage monitoring
- [ ] Execution time optimization
- [ ] Database query efficiency
- [ ] Background job processing

#### Security Testing
- [ ] Input sanitization and validation
- [ ] User capability checks
- [ ] Nonce verification
- [ ] SQL injection prevention
- [ ] XSS protection

## Contributing

### Development Workflow

1. **Fork Repository**: Create your own fork
2. **Create Branch**: `git checkout -b feature/new-feature`
3. **Write Tests**: Add tests for new functionality
4. **Implement Feature**: Write clean, documented code
5. **Run Tests**: Ensure all tests pass
6. **Submit PR**: Create pull request with description

### Code Standards

- Follow WordPress Coding Standards
- Use meaningful variable and function names
- Add inline documentation for complex logic
- Include PHPDoc blocks for all functions
- Maintain backward compatibility

### Documentation Requirements

- Update relevant documentation files
- Add code examples for new features
- Include migration guides for breaking changes
- Update changelog with version information
