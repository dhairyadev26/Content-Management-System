# Advanced Bulk Content Management

A powerful WordPress plugin that provides advanced content management capabilities with bulk operations, intelligent filtering, and streamlined workflow automation.

## 🚀 Features

### Core Functionality
- **Bulk Operations**: Edit, delete, publish, and manage multiple posts/pages simultaneously
- **Advanced Filtering**: Search and filter content by date, author, category, tags, and custom fields
- **Custom Post Type Support**: Works with all post types including custom post types
- **Asynchronous Processing**: Handle large operations without timeouts
- **Progress Tracking**: Real-time progress indicators for bulk operations

### User Experience
- **Intuitive Interface**: Clean, modern admin interface with responsive design
- **Dark Mode Support**: Built-in dark mode toggle for better user experience
- **Accessibility Features**: WCAG compliant with keyboard navigation and screen reader support
- **Mobile Responsive**: Fully functional on tablets and mobile devices

### Advanced Features
- **Import/Export**: Support for CSV and JSON data formats
- **Scheduling**: Schedule bulk operations for future execution
- **User Permissions**: Role-based access control for different user levels
- **Logging System**: Comprehensive logging for debugging and audit trails
- **Performance Optimization**: Cached queries and optimized database operations

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or MariaDB 10.0 or higher
- Minimum 256MB PHP memory limit (512MB recommended for large operations)

## 🔧 Installation

### Automatic Installation
1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins** → **Add New**
3. Search for "Advanced Bulk Content Management"
4. Click **Install Now** and then **Activate**

### Manual Installation
1. Download the plugin ZIP file
2. Log in to your WordPress admin dashboard
3. Navigate to **Plugins** → **Add New** → **Upload Plugin**
4. Choose the ZIP file and click **Install Now**
5. Activate the plugin

### From Source
1. Clone or download this repository
2. Upload the `advanced-bulk-content-management` folder to `/wp-content/plugins/`
3. Activate the plugin through the WordPress admin

## 🎯 Quick Start

1. **Access the Plugin**: After activation, find "Bulk Content Manager" in your WordPress admin menu
2. **Select Content**: Choose the post type and apply filters to select content
3. **Choose Action**: Select from available bulk actions (edit, delete, publish, etc.)
4. **Execute**: Click "Apply" to perform the bulk operation
5. **Monitor Progress**: Watch the real-time progress indicator

## 📖 Usage Guide

### Basic Bulk Operations
```
1. Navigate to "Bulk Content Manager" in admin menu
2. Select post type (Posts, Pages, or Custom Post Types)
3. Apply filters (optional):
   - Date range
   - Author
   - Categories/Tags
   - Custom fields
4. Select posts using checkboxes or "Select All"
5. Choose bulk action from dropdown
6. Click "Apply" to execute
```

### Advanced Filtering
- **Date Filters**: Filter by creation date, modification date, or custom date fields
- **Author Filters**: Filter by author or multiple authors
- **Taxonomy Filters**: Filter by categories, tags, or custom taxonomies
- **Custom Field Filters**: Filter by custom field values and meta data
- **Status Filters**: Filter by post status (published, draft, private, etc.)

### Import/Export Features
- **CSV Import**: Import posts with custom fields and taxonomies
- **JSON Export**: Export filtered results for backup or migration
- **Bulk Media**: Import and assign featured images during bulk operations

## ⚙️ Configuration

### Plugin Settings
Access settings via **Settings** → **Bulk Content Manager**:

- **Performance Settings**: Configure batch sizes and timeout limits
- **User Permissions**: Set role-based access controls
- **Default Filters**: Configure default filter values
- **Logging**: Enable/disable operation logging
- **Interface**: Customize UI elements and default views

### Hooks and Filters

#### Actions
```php
// Before bulk operation starts
do_action('abcm_before_bulk_operation', $operation, $post_ids);

// After bulk operation completes
do_action('abcm_after_bulk_operation', $operation, $results);

// Before individual post processing
do_action('abcm_before_post_process', $post_id, $operation);
```

#### Filters
```php
// Modify available bulk actions
apply_filters('abcm_bulk_actions', $actions, $post_type);

// Customize filter options
apply_filters('abcm_filter_options', $filters, $post_type);

// Modify batch size
apply_filters('abcm_batch_size', $size, $operation);
```

## 🎨 Customization

### Custom Bulk Actions
Add your own bulk actions:

```php
function my_custom_bulk_action($post_ids, $action_data) {
    // Your custom logic here
    foreach ($post_ids as $post_id) {
        // Process each post
    }
}

add_action('abcm_custom_action_my_action', 'my_custom_bulk_action', 10, 2);
```

### Custom Filters
Add custom filter options:

```php
function my_custom_filter($query_args, $filter_value) {
    if (!empty($filter_value)) {
        $query_args['meta_query'][] = array(
            'key' => 'my_custom_field',
            'value' => $filter_value,
            'compare' => '='
        );
    }
    return $query_args;
}

add_filter('abcm_apply_custom_filter', 'my_custom_filter', 10, 2);
```

## 🔧 Developer Guide

### File Structure
```
advanced-bulk-content-management/
├── assets/
│   ├── css/
│   │   ├── admin-style.css
│   │   ├── bulk-actions.css
│   │   └── search-filter.css
│   └── js/
│       ├── admin-script.js
│       ├── bulk-actions.js
│       ├── search-filter.js
│       └── accessibility.js
├── includes/
│   ├── class-admin-page.php
│   ├── class-bulk-actions.php
│   ├── class-search-filter.php
│   ├── class-async-jobs.php
│   ├── class-hooks.php
│   └── class-logger.php
├── languages/
│   └── advanced-bulk-content-management.pot
├── tests/
│   └── [test files]
├── docs/
│   └── accessibility.md
├── advanced-bulk-content-management.php
├── uninstall.php
└── README.md
```

### Database Tables
The plugin creates the following custom tables:
- `wp_abcm_logs`: Operation logs and audit trail
- `wp_abcm_jobs`: Async job queue and status
- `wp_abcm_settings`: Plugin configuration settings

## 🔍 Troubleshooting

### Common Issues

**Memory Limit Errors**
- Increase PHP memory limit in wp-config.php: `ini_set('memory_limit', '512M');`
- Reduce batch size in plugin settings

**Timeout Issues**
- Enable asynchronous processing in settings
- Reduce batch size for large operations
- Check server execution time limits

**Permission Errors**
- Verify user roles and capabilities
- Check plugin permission settings
- Ensure proper WordPress user roles

### Debug Mode
Enable debug logging:
```php
// Add to wp-config.php
define('ABCM_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 🧪 Testing

Run the test suite:
```bash
# Install PHPUnit (if not already installed)
composer install

# Run tests
vendor/bin/phpunit tests/
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Setup
```bash
git clone https://github.com/dhairyadev26/Content-Management-System.git
cd Content-Management-System
# Set up local WordPress development environment
# Activate plugin for testing
```

## 📝 Changelog

### Version 0.2.0 (Latest)
- Added accessibility features (ARIA, keyboard navigation)
- Implemented dark mode support
- Added mobile responsive design
- Enhanced performance optimization
- Added import/export functionality
- Implemented user permission management
- Added scheduling capabilities

### Version 0.1.0
- Initial release
- Basic bulk operations
- Advanced filtering system
- Admin interface

## 🔒 Security

- All inputs are sanitized and validated
- Nonce verification for all operations
- User capability checks
- SQL injection prevention
- XSS protection

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](http://www.gnu.org/licenses/gpl-2.0.txt) file for details.

## 👨‍💻 Author

**dhairyadev26**
- GitHub: [@dhairyadev26](https://github.com/dhairyadev26)
- WordPress.org: [dhairyadev26](https://profiles.wordpress.org/dhairyadev26/)

## 🙏 Support

- **Documentation**: Full documentation available in the `/docs` folder
- **Issues**: Report bugs and request features on [GitHub Issues](https://github.com/dhairyadev26/Content-Management-System/issues)
- **Community**: Join discussions in the WordPress.org plugin forum

---

⭐ **If this plugin helps you, please consider starring the repository!**


