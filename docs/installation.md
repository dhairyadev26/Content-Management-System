# Installation Guide

This guide provides detailed instructions for installing the Advanced Bulk Content Management plugin.

## System Requirements

### Minimum Requirements
- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher (or MariaDB 10.0+)
- **Memory**: 256MB PHP memory limit
- **Disk Space**: 10MB free space

### Recommended Requirements
- **WordPress**: 6.0 or higher
- **PHP**: 8.0 or higher
- **MySQL**: 8.0 or higher (or MariaDB 10.5+)
- **Memory**: 512MB PHP memory limit
- **Disk Space**: 50MB free space (for logs and cache)

## Installation Methods

### Method 1: WordPress Admin Dashboard (Recommended)

1. **Access Plugin Installation**
   - Log in to your WordPress admin dashboard
   - Navigate to **Plugins** → **Add New**

2. **Search for Plugin**
   - In the search box, type "Advanced Bulk Content Management"
   - Look for the plugin by dhairyadev26

3. **Install and Activate**
   - Click **Install Now**
   - Wait for installation to complete
   - Click **Activate** to enable the plugin

### Method 2: Manual Upload via Admin

1. **Download Plugin**
   - Download the plugin ZIP file from the official source
   - Save it to your computer

2. **Upload via Admin**
   - Go to **Plugins** → **Add New** → **Upload Plugin**
   - Click **Choose File** and select the ZIP file
   - Click **Install Now**

3. **Activate Plugin**
   - After installation, click **Activate Plugin**

### Method 3: FTP/File Manager Upload

1. **Extract Plugin Files**
   - Download and extract the plugin ZIP file
   - You should have a folder named `advanced-bulk-content-management`

2. **Upload via FTP**
   ```bash
   # Using FTP client or file manager
   # Upload the entire folder to:
   /wp-content/plugins/advanced-bulk-content-management/
   ```

3. **Set Permissions**
   ```bash
   # Recommended file permissions
   chmod 755 advanced-bulk-content-management/
   chmod 644 advanced-bulk-content-management/*.php
   chmod 644 advanced-bulk-content-management/assets/css/*.css
   chmod 644 advanced-bulk-content-management/assets/js/*.js
   ```

4. **Activate via Admin**
   - Go to **Plugins** in WordPress admin
   - Find "Advanced Bulk Content Management"
   - Click **Activate**

### Method 4: WP-CLI Installation

```bash
# Install plugin via WP-CLI
wp plugin install advanced-bulk-content-management --activate

# Or install from ZIP file
wp plugin install /path/to/plugin.zip --activate
```

## Post-Installation Setup

### 1. Verify Installation

After activation, verify the plugin is working:

- Check for "Bulk Content Manager" in your admin menu
- Go to **Plugins** and ensure the plugin shows as "Active"
- Look for any error messages or warnings

### 2. Initial Configuration

1. **Access Plugin Settings**
   - Navigate to **Settings** → **Bulk Content Manager**
   - Review default settings

2. **Configure Basic Settings**
   ```
   - Batch Size: 50 (default, adjust based on server capacity)
   - Timeout: 300 seconds (5 minutes)
   - Enable Logging: Yes (recommended for troubleshooting)
   - User Permissions: Configure based on your needs
   ```

3. **Test Basic Functionality**
   - Go to **Bulk Content Manager**
   - Try filtering some posts
   - Perform a simple bulk action to test

### 3. Performance Optimization

#### PHP Configuration
Add to your `wp-config.php` or server configuration:

```php
// Increase memory limit
ini_set('memory_limit', '512M');

// Increase execution time
ini_set('max_execution_time', 300);

// Increase input variables
ini_set('max_input_vars', 3000);
```

#### WordPress Configuration
```php
// Add to wp-config.php
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '512M');
```

#### Server Configuration (if you have access)

**Apache (.htaccess)**
```apache
php_value memory_limit 512M
php_value max_execution_time 300
php_value max_input_vars 3000
```

**Nginx (server block)**
```nginx
client_max_body_size 100M;
```

## Database Setup

The plugin automatically creates necessary database tables on activation:

### Custom Tables Created
- `wp_abcm_logs` - Operation logs and audit trail
- `wp_abcm_jobs` - Asynchronous job queue
- `wp_abcm_settings` - Plugin configuration

### Manual Database Setup (if needed)
If tables aren't created automatically, you can create them manually:

```sql
-- Logs table
CREATE TABLE wp_abcm_logs (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    operation varchar(100) NOT NULL,
    user_id bigint(20) NOT NULL,
    details text,
    created_at datetime NOT NULL,
    PRIMARY KEY (id)
);

-- Jobs table
CREATE TABLE wp_abcm_jobs (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    job_type varchar(100) NOT NULL,
    status varchar(20) DEFAULT 'pending',
    data longtext,
    created_at datetime NOT NULL,
    completed_at datetime DEFAULT NULL,
    PRIMARY KEY (id)
);

-- Settings table
CREATE TABLE wp_abcm_settings (
    setting_name varchar(100) NOT NULL,
    setting_value longtext,
    PRIMARY KEY (setting_name)
);
```

## Troubleshooting Installation

### Common Issues

#### 1. Plugin Not Appearing in Menu
**Problem**: Plugin activated but no menu item appears
**Solutions**:
- Check user permissions (must be Administrator)
- Deactivate and reactivate the plugin
- Check for PHP errors in error log

#### 2. White Screen/Fatal Error
**Problem**: Site shows white screen after activation
**Solutions**:
```php
// Check PHP error log
// Common causes:
// - Insufficient memory
// - PHP version incompatibility
// - Plugin conflicts

// Quick fix: Deactivate via database
UPDATE wp_options 
SET option_value = '' 
WHERE option_name = 'active_plugins';
```

#### 3. Database Table Creation Fails
**Problem**: Custom tables not created
**Solutions**:
- Check database user permissions
- Verify WordPress database connection
- Manually create tables (see SQL above)

#### 4. Permission Errors
**Problem**: File permission errors
**Solutions**:
```bash
# Fix file permissions
find /wp-content/plugins/advanced-bulk-content-management/ -type f -exec chmod 644 {} \;
find /wp-content/plugins/advanced-bulk-content-management/ -type d -exec chmod 755 {} \;
```

### Debug Mode

Enable debug mode for troubleshooting:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('ABCM_DEBUG', true);
```

Check debug logs:
- `/wp-content/debug.log`
- `/wp-content/plugins/advanced-bulk-content-management/logs/`

## Uninstallation

### Complete Removal

1. **Deactivate Plugin**
   - Go to **Plugins** → **Installed Plugins**
   - Click **Deactivate** under the plugin name

2. **Delete Plugin**
   - Click **Delete** to remove plugin files
   - Confirm deletion

3. **Clean Database (Optional)**
   ```sql
   -- Remove custom tables
   DROP TABLE wp_abcm_logs;
   DROP TABLE wp_abcm_jobs;
   DROP TABLE wp_abcm_settings;
   
   -- Remove options
   DELETE FROM wp_options WHERE option_name LIKE 'abcm_%';
   ```

### Backup Before Uninstall

Always backup before uninstalling:

```bash
# Backup database
mysqldump -u username -p database_name > backup.sql

# Backup plugin folder
tar -czf abcm-backup.tar.gz /wp-content/plugins/advanced-bulk-content-management/
```

## Next Steps

After successful installation:

1. **Read User Guide**: See [usage.md](usage.md) for detailed usage instructions
2. **Configure Settings**: Customize the plugin for your needs
3. **Test Functionality**: Try bulk operations with small datasets first
4. **Set Up Monitoring**: Enable logging and monitor performance

## Support

If you encounter issues during installation:

- Check our [FAQ](faq.md)
- Review [troubleshooting guide](troubleshooting.md)
- Submit an issue on [GitHub](https://github.com/dhairyadev26/Content-Management-System/issues)
- Contact support via WordPress.org forum
