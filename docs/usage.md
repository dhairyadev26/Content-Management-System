# User Guide

This comprehensive guide covers all features and functionality of the Advanced Bulk Content Management plugin.

## Table of Contents

1. [Getting Started](#getting-started)
2. [Basic Operations](#basic-operations)
3. [Advanced Filtering](#advanced-filtering)
4. [Bulk Actions](#bulk-actions)
5. [Import/Export](#importexport)
6. [Scheduling](#scheduling)
7. [User Management](#user-management)
8. [Settings](#settings)

## Getting Started

### Accessing the Plugin

After installation and activation:

1. **Main Interface**: Navigate to **Bulk Content Manager** in your WordPress admin menu
2. **Settings**: Access via **Settings** → **Bulk Content Manager**
3. **Quick Actions**: Some bulk actions are available directly from the Posts/Pages list

### Interface Overview

The main interface consists of:

- **Content Type Selector**: Choose Posts, Pages, or Custom Post Types
- **Filter Panel**: Apply various filters to narrow down content
- **Content List**: View and select items for bulk operations
- **Action Panel**: Choose and execute bulk actions
- **Progress Indicator**: Monitor operation progress

## Basic Operations

### Selecting Content

#### 1. Choose Content Type
```
1. Click on "Content Type" dropdown
2. Select from:
   - Posts
   - Pages
   - Custom Post Types (if available)
   - Media (for file operations)
```

#### 2. Apply Basic Filters
```
- Date Range: Select start and end dates
- Author: Choose specific authors
- Status: Published, Draft, Private, etc.
- Categories/Tags: Select relevant taxonomies
```

#### 3. Select Items
```
- Individual Selection: Check boxes next to items
- Select All: Use "Select All" checkbox
- Select by Filter: "Select All Filtered" option
- Range Selection: Shift+click for range selection
```

### Performing Bulk Actions

#### Basic Workflow
```
1. Filter and select content
2. Choose action from "Bulk Actions" dropdown
3. Configure action parameters (if required)
4. Click "Apply" to execute
5. Monitor progress and review results
```

#### Available Actions
- **Edit**: Modify multiple posts simultaneously
- **Delete**: Remove multiple items
- **Publish/Unpublish**: Change post status
- **Change Author**: Reassign posts to different authors
- **Add/Remove Tags**: Bulk taxonomy management
- **Set Featured Image**: Assign images in bulk
- **Duplicate**: Create copies of posts

## Advanced Filtering

### Date Filters

#### Publication Date
```
- Exact Date: Posts published on specific date
- Date Range: Posts within date range
- Relative Dates: Last 7 days, last month, etc.
- Custom Periods: Define your own date ranges
```

#### Modification Date
```
- Recently Modified: Posts changed within timeframe
- Stale Content: Posts not modified for X days
- Batch Dates: Filter by modification patterns
```

### Content Filters

#### Text Search
```
- Title Search: Find posts by title keywords
- Content Search: Search within post content
- Excerpt Search: Filter by excerpt content
- Meta Search: Search custom field values
```

#### Advanced Text Options
```
- Exact Match: "exact phrase"
- Wildcard: use * for partial matches
- Exclude Terms: -term to exclude
- Multiple Terms: term1 OR term2
```

### Taxonomy Filters

#### Categories and Tags
```
- Include: Posts with specific terms
- Exclude: Posts without specific terms
- Multiple Terms: AND/OR logic
- Hierarchical: Parent/child relationships
```

#### Custom Taxonomies
```
- Product Categories
- Portfolio Types
- Event Categories
- Any registered taxonomy
```

### Custom Field Filters

#### Meta Key/Value Pairs
```
- Key Exists: Posts with specific meta key
- Value Match: Posts with specific meta value
- Numeric Comparison: >, <, =, between
- Date Comparison: Before/after dates
```

#### Advanced Meta Queries
```
- Multiple Conditions: AND/OR logic
- Nested Queries: Complex filter combinations
- Data Type Handling: String, numeric, date
```

### User and Permission Filters

#### Author Filters
```
- Specific Authors: Choose individual users
- Author Roles: Filter by user roles
- Multiple Authors: Include/exclude lists
- Guest Content: Posts without authors
```

#### Capability Filters
```
- User Can Edit: Posts user has permission to modify
- User Role Content: Posts by specific roles
- Ownership: Posts owned by current user
```

## Bulk Actions

### Content Management Actions

#### Bulk Edit
```
Purpose: Modify multiple posts simultaneously
Options:
- Title: Append, prepend, or replace
- Content: Add content to beginning/end
- Excerpt: Update post excerpts
- Status: Change publication status
- Date: Modify publication dates
- Author: Reassign to different users

Usage:
1. Select posts to edit
2. Choose "Bulk Edit" action
3. Configure editing options
4. Preview changes (optional)
5. Apply changes
```

#### Bulk Delete
```
Purpose: Remove multiple posts
Options:
- Move to Trash: Reversible deletion
- Permanent Delete: Complete removal
- Delete Revisions: Clean up post revisions
- Delete Meta: Remove associated metadata

Safety Features:
- Confirmation dialogs
- Backup options
- Undo capability (for trash)
- Progress tracking
```

#### Status Management
```
Purpose: Change post status in bulk
Options:
- Publish: Make posts live
- Draft: Convert to draft status
- Private: Make posts private
- Schedule: Set future publication
- Pending: Set for review

Batch Processing:
- Respect user permissions
- Maintain post hierarchy
- Update related data
```

### SEO and Meta Actions

#### Meta Information
```
Purpose: Update SEO and meta data
Options:
- Meta Descriptions: Bulk update descriptions
- Keywords: Add/remove keyword tags
- Custom Fields: Update meta values
- Schema Data: Structured data updates

Integration:
- Yoast SEO compatibility
- RankMath support
- Custom SEO plugins
- Schema.org data
```

#### Featured Images
```
Purpose: Manage featured images in bulk
Options:
- Set Featured Image: Apply same image to all
- Remove Featured Images: Bulk removal
- Import from URLs: Set images from external URLs
- Generate from Content: Auto-set from post images

Image Processing:
- Automatic resizing
- Format optimization
- Alt text assignment
- Image validation
```

### Advanced Actions

#### Content Duplication
```
Purpose: Create copies of existing content
Options:
- Exact Copies: Duplicate everything
- Template Copies: Copy structure only
- Partial Copies: Select fields to copy
- Cross-Site: Duplicate to other sites (multisite)

Customization:
- Title modifications
- Author reassignment
- Status changes
- Category adjustments
```

#### Import/Export Operations
```
Purpose: Data migration and backup
Formats:
- CSV: Spreadsheet-compatible format
- JSON: Structured data format
- XML: WordPress export format
- Custom: Define your own format

Features:
- Field mapping
- Data validation
- Progress tracking
- Error handling
```

## Import/Export

### CSV Import

#### Supported Data
```
- Post Title
- Post Content
- Post Excerpt
- Publication Date
- Author (by username or email)
- Categories (by name or ID)
- Tags
- Custom Fields
- Featured Image URL
- Post Status
```

#### Import Process
```
1. Prepare CSV File:
   - Use proper column headers
   - Ensure data formatting
   - Validate required fields

2. Upload and Map:
   - Upload CSV file
   - Map columns to WordPress fields
   - Set import options

3. Preview and Import:
   - Review mapping
   - Preview first few rows
   - Execute import with progress tracking
```

#### CSV Format Example
```csv
title,content,author,status,categories,tags,custom_field_1
"Sample Post","Post content here","admin","publish","Category 1,Category 2","tag1,tag2","Custom Value"
"Another Post","More content","editor","draft","Category 1","tag3,tag4","Another Value"
```

### Export Options

#### Export Formats
```
CSV Export:
- Filtered results only
- All posts of selected type
- Custom field selection
- Configurable delimiters

JSON Export:
- Complete post data
- Hierarchical structure
- Meta data included
- API-compatible format

WordPress XML:
- Native WordPress format
- Full site export capability
- Import to other WordPress sites
- Preserves all relationships
```

#### Export Configuration
```
1. Select Data:
   - Apply filters first
   - Choose export format
   - Select fields to include

2. Configure Options:
   - File naming
   - Data formatting
   - Compression options

3. Generate and Download:
   - Process in background
   - Download when ready
   - Email notification option
```

## Scheduling

### Scheduled Operations

#### Setup Scheduled Actions
```
1. Configure Action:
   - Select bulk action
   - Apply filters
   - Set action parameters

2. Set Schedule:
   - One-time: Future date/time
   - Recurring: Daily, weekly, monthly
   - Custom: Cron-style scheduling

3. Review and Save:
   - Verify settings
   - Set notifications
   - Save scheduled job
```

#### Schedule Types
```
One-Time Schedules:
- Specific date and time
- Timezone awareness
- Delay options

Recurring Schedules:
- Daily at specific time
- Weekly on specific days
- Monthly on specific dates
- Custom cron expressions

Conditional Schedules:
- Based on content age
- Triggered by events
- Dynamic conditions
```

### Managing Scheduled Jobs

#### Job Queue Interface
```
View Jobs:
- Pending jobs list
- Running jobs status
- Completed jobs history
- Failed jobs with errors

Job Management:
- Edit pending jobs
- Cancel scheduled jobs
- Retry failed jobs
- Clone job configurations
```

#### Monitoring and Notifications
```
Email Notifications:
- Job completion alerts
- Error notifications
- Progress reports
- Summary emails

Dashboard Widgets:
- Upcoming jobs
- Recent activity
- System status
- Performance metrics
```

## User Management

### Permission System

#### Role-Based Access
```
Administrator:
- Full access to all features
- Manage plugin settings
- View all logs and reports
- Manage other users' content

Editor:
- Bulk actions on own content
- Limited bulk operations
- Basic filtering options
- Cannot modify settings

Author:
- Own posts only
- Basic bulk actions
- Limited filtering
- No administrative functions

Custom Roles:
- Define specific permissions
- Granular control
- Feature-based access
- Integration with role plugins
```

#### Capability Management
```
Custom Capabilities:
- abcm_bulk_edit: Perform bulk edits
- abcm_bulk_delete: Delete multiple posts
- abcm_import_export: Import/export data
- abcm_schedule_jobs: Create scheduled operations
- abcm_manage_settings: Modify plugin settings
- abcm_view_logs: Access operation logs
```

### Multi-User Workflows

#### Collaborative Features
```
Content Review:
- Bulk assign for review
- Review queues
- Approval workflows
- Comment management

Team Management:
- Assign content to team members
- Track work progress
- Collaborative editing
- Role-based notifications
```

## Settings

### General Settings

#### Performance Configuration
```
Batch Size:
- Default: 50 items per batch
- Range: 10-500 items
- Auto-adjustment based on server
- Memory usage optimization

Timeout Settings:
- Script execution timeout
- AJAX request timeout
- Background job timeout
- User interface timeout

Memory Management:
- Memory limit detection
- Automatic cleanup
- Progress optimization
- Error recovery
```

#### User Interface
```
Default Views:
- Default content type
- Default filters
- Items per page
- Column visibility

Accessibility:
- Keyboard navigation
- Screen reader support
- High contrast mode
- Font size options

Mobile Settings:
- Responsive breakpoints
- Touch optimization
- Mobile-specific features
- Simplified interface
```

### Advanced Settings

#### Database Optimization
```
Query Optimization:
- Index usage
- Query caching
- Result pagination
- Memory usage

Data Retention:
- Log retention period
- Cleanup schedules
- Archive options
- Storage limits

Performance Monitoring:
- Query performance tracking
- Memory usage monitoring
- Execution time logging
- Performance alerts
```

#### Integration Settings
```
Third-Party Plugins:
- SEO plugin integration
- Page builder compatibility
- E-commerce plugins
- Custom post type plugins

API Configuration:
- REST API endpoints
- Authentication settings
- Rate limiting
- External integrations

Webhook Settings:
- Operation completion hooks
- Error notification hooks
- Progress update hooks
- Custom event triggers
```

## Tips and Best Practices

### Performance Optimization

#### Large Dataset Handling
```
1. Use Appropriate Batch Sizes:
   - Start with smaller batches (25-50)
   - Increase gradually based on performance
   - Monitor memory usage

2. Filter Effectively:
   - Apply filters before bulk operations
   - Use database-indexed fields
   - Avoid complex text searches on large datasets

3. Schedule Heavy Operations:
   - Run large operations during off-peak hours
   - Use background processing
   - Monitor server resources
```

#### Memory Management
```
1. Monitor Memory Usage:
   - Check current memory limit
   - Monitor plugin memory consumption
   - Increase limits if needed

2. Optimize Queries:
   - Use efficient filters
   - Limit result sets
   - Avoid nested loops

3. Clean Up Regularly:
   - Clear logs periodically
   - Remove unnecessary data
   - Optimize database tables
```

### Security Best Practices

#### Data Protection
```
1. Backup Before Operations:
   - Create database backups
   - Test restore procedures
   - Document backup locations

2. Validate Input Data:
   - Check import data format
   - Sanitize user inputs
   - Validate file uploads

3. Monitor Operations:
   - Enable logging
   - Review operation results
   - Monitor user activities
```

#### Access Control
```
1. User Permissions:
   - Assign minimum required permissions
   - Regular permission audits
   - Monitor user activities

2. Content Security:
   - Verify user can edit content
   - Respect post ownership
   - Maintain content integrity
```

### Troubleshooting Common Issues

#### Performance Issues
```
Problem: Slow bulk operations
Solutions:
- Reduce batch size
- Increase memory limit
- Use background processing
- Optimize server configuration

Problem: Timeouts during operations
Solutions:
- Increase execution time limit
- Enable asynchronous processing
- Break large operations into smaller chunks
- Check server resources
```

#### Data Issues
```
Problem: Import errors
Solutions:
- Validate CSV format
- Check data encoding
- Verify field mappings
- Review error logs

Problem: Inconsistent results
Solutions:
- Clear caches
- Refresh data
- Check filter settings
- Verify permissions
```

## Getting Help

### Documentation Resources
- [Installation Guide](installation.md)
- [Developer Documentation](developer.md)
- [API Reference](api.md)
- [FAQ](faq.md)

### Support Channels
- GitHub Issues: Technical problems and feature requests
- WordPress.org Forum: Community support
- Documentation: Comprehensive guides and examples
- Email Support: Direct assistance for licensed users

### Community Resources
- User Community: Share tips and experiences
- Plugin Directory: Ratings and reviews
- Blog Posts: Tutorials and use cases
- Video Tutorials: Step-by-step guides
