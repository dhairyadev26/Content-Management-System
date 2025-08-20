# Frequently Asked Questions (FAQ)

## General Questions

### Q: What is Advanced Bulk Content Management?
**A:** Advanced Bulk Content Management is a WordPress plugin that allows you to perform bulk operations on multiple posts, pages, and custom post types simultaneously. It includes advanced filtering, import/export capabilities, scheduling, and user permission management.

### Q: Is this plugin free?
**A:** Yes, this plugin is free and open-source, licensed under GPL v2 or later. You can use it on unlimited websites without any licensing fees.

### Q: Which WordPress versions are supported?
**A:** The plugin requires WordPress 5.0 or higher. We recommend using the latest version of WordPress for the best experience and security.

### Q: Does it work with custom post types?
**A:** Yes! The plugin supports all public post types, including custom post types created by themes or other plugins. You can perform bulk operations on products, events, portfolios, or any custom content type.

## Installation & Setup

### Q: How do I install the plugin?
**A:** You can install it through the WordPress admin:
1. Go to **Plugins** → **Add New**
2. Search for "Advanced Bulk Content Management"
3. Click **Install Now** and then **Activate**

For detailed installation instructions, see our [Installation Guide](installation.md).

### Q: What are the system requirements?
**A:** Minimum requirements:
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+ (or MariaDB 10.0+)
- 256MB PHP memory limit

Recommended:
- WordPress 6.0+
- PHP 8.0+
- 512MB PHP memory limit

### Q: Why don't I see the plugin menu after activation?
**A:** Make sure you have Administrator privileges. The plugin requires `manage_options` capability to access the main interface. If you're still having issues:
1. Deactivate and reactivate the plugin
2. Check for plugin conflicts
3. Review your PHP error logs

### Q: The plugin isn't working after installation. What should I do?
**A:** First, try these troubleshooting steps:
1. Deactivate all other plugins and test
2. Switch to a default WordPress theme
3. Check PHP error logs for conflicts
4. Increase PHP memory limit to 512MB
5. Ensure file permissions are correct (644 for files, 755 for directories)

## Features & Functionality

### Q: What bulk actions can I perform?
**A:** The plugin supports numerous bulk operations:
- **Content Management**: Edit, delete, publish, unpublish posts
- **Taxonomy Management**: Add/remove categories, tags, custom taxonomies
- **Meta Data**: Update custom fields and meta values
- **Author Management**: Change post authors in bulk
- **Media Management**: Set/remove featured images
- **Advanced**: Duplicate posts, schedule operations, import/export data

### Q: Can I filter content before performing bulk actions?
**A:** Yes! The plugin includes advanced filtering options:
- **Date Filters**: Publication date, modification date, custom date ranges
- **Author Filters**: Filter by specific authors or user roles
- **Taxonomy Filters**: Categories, tags, custom taxonomies
- **Meta Filters**: Custom field values and meta data
- **Status Filters**: Published, draft, private, scheduled posts
- **Text Search**: Search in titles, content, excerpts

### Q: How many posts can I process at once?
**A:** The limit depends on your server configuration. By default, the plugin processes 50 posts per batch to avoid timeouts. You can adjust this in settings, but consider:
- Your PHP memory limit
- Server execution time limits
- Database performance
- For very large operations (1000+ posts), use the background processing feature

### Q: Does the plugin support scheduling?
**A:** Yes! You can schedule bulk operations for:
- **One-time execution**: Set specific date and time
- **Recurring operations**: Daily, weekly, monthly schedules
- **Conditional triggers**: Based on content age or events
- **Background processing**: Large operations run in the background

### Q: Can I import/export data?
**A:** Yes, the plugin supports multiple formats:
- **CSV Import/Export**: Spreadsheet-compatible format
- **JSON Export**: Structured data format
- **WordPress XML**: Native WordPress export format
- **Custom Mapping**: Map fields during import

## Performance & Limitations

### Q: The plugin is slow with large datasets. How can I improve performance?
**A:** Try these optimization techniques:

**Server-side optimizations:**
```php
// Add to wp-config.php
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
ini_set('max_input_vars', 3000);
```

**Plugin settings:**
- Reduce batch size to 25-50 posts
- Enable background processing for large operations
- Use specific filters to reduce dataset size
- Schedule heavy operations during off-peak hours

### Q: I'm getting memory limit errors. What should I do?
**A:** Increase your PHP memory limit:

**Method 1: wp-config.php**
```php
ini_set('memory_limit', '512M');
define('WP_MEMORY_LIMIT', '512M');
```

**Method 2: .htaccess (Apache)**
```apache
php_value memory_limit 512M
```

**Method 3: Contact your hosting provider**
If you can't modify these settings, contact your hosting provider to increase the memory limit.

### Q: Operations are timing out. How can I fix this?
**A:** Timeout issues can be resolved by:

1. **Enable background processing** in plugin settings
2. **Reduce batch size** to 25 or fewer posts
3. **Increase execution time**:
   ```php
   ini_set('max_execution_time', 300); // 5 minutes
   ```
4. **Use scheduling** for large operations
5. **Break large operations** into smaller chunks

### Q: Can I undo bulk operations?
**A:** It depends on the operation:
- **Bulk Delete**: Posts go to trash and can be restored for 30 days
- **Bulk Edit**: No automatic undo, but you can export data before operations as backup
- **Status Changes**: Can be reversed with another bulk operation
- **Meta Updates**: No automatic undo (backup recommended)

**Best Practice**: Always backup your database before major bulk operations.

## Import/Export

### Q: What file formats are supported for import?
**A:** Supported formats:
- **CSV**: Comma-separated values (most common)
- **JSON**: JavaScript Object Notation
- **XML**: WordPress export format
- **TXT**: Tab-delimited text files

### Q: How do I prepare a CSV file for import?
**A:** Your CSV should include these columns:
```csv
title,content,author,status,categories,tags,custom_field_1
"Post Title","Post content here","admin","publish","Category1,Category2","tag1,tag2","Custom Value"
```

**Tips:**
- Use quotes around text with commas
- Separate multiple categories/tags with commas
- Use author username or email
- Include custom field columns as needed

### Q: Can I import featured images?
**A:** Yes! Include a column with image URLs:
```csv
title,content,featured_image
"Post Title","Content here","https://example.com/image.jpg"
```

The plugin will download and attach the images automatically.

### Q: Export is taking too long. What can I do?
**A:** For large exports:
1. Apply filters to reduce the dataset
2. Select only necessary fields for export
3. Use background processing if available
4. Consider exporting in smaller chunks

## User Management & Permissions

### Q: Can different user roles access the plugin?
**A:** Yes, but with different capabilities:
- **Administrator**: Full access to all features
- **Editor**: Can perform bulk actions on posts they can edit
- **Author**: Limited to their own posts
- **Custom Roles**: Configure specific permissions

### Q: How do I restrict access to certain features?
**A:** You can customize permissions using WordPress capabilities:
```php
// Only allow administrators to delete posts in bulk
if (!current_user_can('manage_options')) {
    // Remove delete action for non-admins
}
```

### Q: Can multiple users work on bulk operations simultaneously?
**A:** Yes, but consider:
- Each user sees operations they initiated
- Simultaneous operations on the same content may conflict
- Use user-specific filters to avoid conflicts
- Monitor system resources during concurrent operations

## Troubleshooting

### Q: The plugin interface is not loading correctly.
**A:** Check for these common issues:
1. **JavaScript conflicts**: Deactivate other plugins temporarily
2. **Theme conflicts**: Switch to a default theme
3. **Browser cache**: Clear cache and try incognito mode
4. **Console errors**: Check browser developer console for errors

### Q: Bulk actions are not working as expected.
**A:** Troubleshooting steps:
1. **Check user permissions**: Ensure you can edit the selected posts
2. **Review filter criteria**: Make sure filters are selecting intended posts
3. **Test with small dataset**: Try with 2-3 posts first
4. **Check logs**: Enable logging and review operation logs
5. **Verify post types**: Ensure the post type supports the operation

### Q: Import is failing. What could be wrong?
**A:** Common import issues:
1. **File format**: Ensure CSV is properly formatted
2. **Character encoding**: Use UTF-8 encoding
3. **Column mapping**: Verify field mapping is correct
4. **Data validation**: Check for invalid data in required fields
5. **File size**: Large files may need to be split

### Q: I'm seeing database errors.
**A:** Database error solutions:
1. **Check database connection**: Verify WordPress can connect to database
2. **User permissions**: Ensure database user has required privileges
3. **Table creation**: Plugin may need to recreate custom tables
4. **Database optimization**: Run database optimization/repair

### Q: The plugin is conflicting with other plugins.
**A:** To identify conflicts:
1. **Deactivate all plugins** except this one
2. **Test functionality** to confirm it works
3. **Reactivate plugins one by one** to identify the conflict
4. **Check plugin logs** for error messages
5. **Contact support** with conflict details

## Security & Data Safety

### Q: Is my data safe when using bulk operations?
**A:** The plugin includes several safety measures:
- Input sanitization and validation
- User permission checks
- Nonce verification for all operations
- Operation logging for audit trails
- Backup recommendations before major operations

### Q: Should I backup before using the plugin?
**A:** **Absolutely!** Always backup your database before:
- Large bulk delete operations
- Major content modifications
- Import operations
- First-time plugin use

### Q: How can I monitor what changes were made?
**A:** The plugin includes comprehensive logging:
- Operation details and timestamps
- User who performed the operation
- Number of posts processed
- Success/failure rates
- Error messages for failed operations

Access logs via **Settings** → **Bulk Content Manager** → **Logs**.

## Integration & Compatibility

### Q: Does the plugin work with page builders?
**A:** Yes, it works with popular page builders:
- **Elementor**: Can bulk edit Elementor pages
- **Gutenberg**: Full block editor support
- **Beaver Builder**: Compatible with BB pages
- **Divi**: Works with Divi builder content

### Q: Is it compatible with SEO plugins?
**A:** Yes, it integrates with:
- **Yoast SEO**: Bulk edit meta descriptions, keywords
- **RankMath**: SEO field management
- **All in One SEO**: Meta data operations
- **SEOPress**: Compatible with SEO fields

### Q: Can I use it with WooCommerce?
**A:** Yes! Special features for WooCommerce:
- Bulk edit product information
- Update stock quantities
- Change product categories
- Modify prices and attributes
- Import/export product data

### Q: Does it work with custom fields?
**A:** Yes, including:
- **ACF (Advanced Custom Fields)**: Full support
- **Meta Box**: Custom field integration
- **Toolset**: Compatible with Toolset fields
- **Native WordPress**: Standard custom fields

## Performance Optimization

### Q: How can I optimize the plugin for better performance?
**A:** Performance optimization tips:

**Server Configuration:**
```php
// wp-config.php optimizations
define('WP_MEMORY_LIMIT', '512M');
ini_set('max_execution_time', 300);
ini_set('max_input_vars', 3000);
```

**Plugin Settings:**
- Use appropriate batch sizes (25-100 posts)
- Enable object caching if available
- Use specific filters to reduce dataset size
- Schedule heavy operations during off-peak hours

**Database Optimization:**
- Regularly optimize database tables
- Remove unnecessary post revisions
- Clean up expired transients
- Use database indexing for custom fields

### Q: The admin interface is slow. How can I speed it up?
**A:** Admin interface optimization:
1. **Reduce items per page** in settings
2. **Use specific filters** to limit displayed content
3. **Clear WordPress cache** regularly
4. **Optimize database** tables
5. **Check for plugin conflicts**

## Advanced Usage

### Q: Can I create custom bulk actions?
**A:** Yes! Developers can add custom actions using hooks:
```php
// Add custom action
add_filter('abcm_bulk_actions', function($actions) {
    $actions['my_custom_action'] = 'My Custom Action';
    return $actions;
});

// Handle custom action
add_action('abcm_execute_my_custom_action', function($post_ids, $data) {
    foreach ($post_ids as $post_id) {
        // Your custom logic here
    }
});
```

### Q: How can I add custom filters?
**A:** Custom filters can be added programmatically:
```php
add_filter('abcm_filter_options', function($filters) {
    $filters['my_filter'] = array(
        'label' => 'My Custom Filter',
        'type' => 'select',
        'options' => array(
            'option1' => 'Option 1',
            'option2' => 'Option 2'
        )
    );
    return $filters;
});
```

### Q: Can I automate bulk operations?
**A:** Yes, through several methods:
1. **Scheduled operations**: Set up recurring tasks
2. **WP-CLI integration**: Command-line automation
3. **Custom scripts**: PHP scripts for automation
4. **Webhook triggers**: External system integration

## Support & Resources

### Q: Where can I get help if I'm stuck?
**A:** Support resources:
1. **Documentation**: Comprehensive guides in the `/docs` folder
2. **GitHub Issues**: Report bugs and request features
3. **WordPress.org Forum**: Community support
4. **Email Support**: Direct assistance for complex issues

### Q: How do I report a bug?
**A:** When reporting bugs, please include:
1. WordPress version
2. PHP version
3. Plugin version
4. Error messages (exact text)
5. Steps to reproduce the issue
6. Other active plugins
7. Theme information

Submit reports at: [GitHub Issues](https://github.com/dhairyadev26/Content-Management-System/issues)

### Q: Can I contribute to the plugin development?
**A:** Absolutely! Contributions are welcome:
1. **Code contributions**: Submit pull requests
2. **Bug reports**: Help identify and fix issues
3. **Documentation**: Improve guides and examples
4. **Translations**: Help translate the plugin
5. **Testing**: Test new features and report feedback

### Q: Is commercial support available?
**A:** While the plugin is free, commercial support options may be available for:
- Custom feature development
- Priority support
- Installation and setup assistance
- Training and consultation

Contact the developer for commercial support inquiries.

## Migration & Updates

### Q: How do I migrate from another bulk management plugin?
**A:** Migration steps:
1. **Export data** from your current plugin
2. **Install** Advanced Bulk Content Management
3. **Import data** using the import feature
4. **Test functionality** with small operations
5. **Deactivate** the old plugin once satisfied

### Q: Will my settings be preserved during plugin updates?
**A:** Yes, plugin updates preserve:
- All configuration settings
- User preferences
- Custom filters and actions
- Operation logs (based on retention settings)

### Q: How do I backup plugin settings?
**A:** Settings backup methods:
1. **Database backup**: Full WordPress database backup
2. **Export settings**: Use the export feature (if available)
3. **Manual backup**: Document custom configurations

---

## Still Have Questions?

If you can't find the answer to your question here:

1. **Check the documentation**: Review the [User Guide](usage.md) and [Developer Documentation](developer.md)
2. **Search existing issues**: Look through [GitHub Issues](https://github.com/dhairyadev26/Content-Management-System/issues)
3. **Ask the community**: Post in the WordPress.org support forum
4. **Contact support**: Submit a detailed issue report

We're here to help you get the most out of the Advanced Bulk Content Management plugin!
