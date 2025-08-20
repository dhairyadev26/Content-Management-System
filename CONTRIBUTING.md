# Contributing to Advanced Bulk Content Management

Thank you for your interest in contributing to the Advanced Bulk Content Management plugin! This document provides guidelines and instructions for contributing to the project.

## Table of Contents

1. [Code of Conduct](#code-of-conduct)
2. [Getting Started](#getting-started)
3. [Development Workflow](#development-workflow)
4. [Coding Standards](#coding-standards)
5. [Testing](#testing)
6. [Documentation](#documentation)
7. [Submitting Changes](#submitting-changes)
8. [Bug Reports](#bug-reports)
9. [Feature Requests](#feature-requests)

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inspiring community for all. By participating in this project, you agree to abide by our code of conduct.

### Expected Behavior

- **Be respectful**: Treat everyone with respect and kindness
- **Be collaborative**: Work together constructively
- **Be inclusive**: Welcome newcomers and diverse perspectives
- **Be patient**: Help others learn and grow
- **Be professional**: Maintain professional standards in all interactions

### Unacceptable Behavior

- Harassment, discrimination, or offensive language
- Personal attacks or trolling
- Spam or self-promotion unrelated to the project
- Sharing private information without permission
- Any behavior that would be inappropriate in a professional setting

## Getting Started

### Prerequisites

Before contributing, ensure you have:

- **WordPress Development Environment**: Local WordPress installation
- **PHP 7.4+**: Required for plugin compatibility
- **Git**: For version control
- **Code Editor**: VS Code, PHPStorm, or similar
- **Browser**: For testing UI changes

### Development Environment Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/dhairyadev26/Content-Management-System.git
   cd Content-Management-System
   ```

2. **Set Up WordPress**
   ```bash
   # Using Local by Flywheel, XAMPP, or similar
   # Copy plugin to wp-content/plugins/
   cp -r . /path/to/wordpress/wp-content/plugins/advanced-bulk-content-management/
   ```

3. **Install Dependencies**
   ```bash
   # If using Composer for development tools
   composer install --dev
   
   # If using Node.js for build tools
   npm install
   ```

4. **Activate Plugin**
   - Log into WordPress admin
   - Navigate to Plugins
   - Activate "Advanced Bulk Content Management"

### Project Structure

Understanding the codebase:

```
advanced-bulk-content-management/
├── assets/                     # Frontend assets
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
├── includes/                   # Core PHP classes
├── languages/                  # Translation files
├── tests/                     # Unit and integration tests
├── docs/                      # Documentation
├── advanced-bulk-content-management.php  # Main plugin file
├── uninstall.php             # Cleanup on uninstall
└── README.md                 # Main documentation
```

## Development Workflow

### 1. Choose an Issue

- Browse [open issues](https://github.com/dhairyadev26/Content-Management-System/issues)
- Look for issues labeled `good first issue` for beginners
- Comment on the issue to express interest
- Wait for assignment before starting work

### 2. Create a Branch

```bash
# Create and switch to a new branch
git checkout -b feature/issue-number-short-description

# Examples:
git checkout -b feature/123-add-custom-filters
git checkout -b bugfix/456-fix-memory-leak
git checkout -b docs/789-update-user-guide
```

### 3. Make Changes

- Follow coding standards (see below)
- Write tests for new functionality
- Update documentation as needed
- Test thoroughly in different environments

### 4. Commit Changes

```bash
# Stage changes
git add .

# Commit with descriptive message
git commit -m "Add custom filter functionality

- Implement custom filter hooks
- Add UI for filter configuration
- Include unit tests
- Update documentation

Fixes #123"
```

### 5. Push and Create Pull Request

```bash
# Push to your fork
git push origin feature/123-add-custom-filters

# Create pull request on GitHub
# Include detailed description and reference issue
```

## Coding Standards

### PHP Standards

Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/) with these additions:

#### File Headers
```php
<?php
/**
 * Brief description of the file
 *
 * Longer description if needed.
 *
 * @package Advanced_Bulk_Content_Management
 * @subpackage Includes
 * @since 0.1.0
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die('Direct access not allowed.');
}
```

#### Class Structure
```php
/**
 * Class description
 *
 * @since 0.1.0
 */
class ABCM_Example_Class {
    
    /**
     * Property description
     *
     * @since 0.1.0
     * @var string
     */
    private $property;
    
    /**
     * Constructor
     *
     * @since 0.1.0
     * @param string $param Parameter description.
     */
    public function __construct($param) {
        $this->property = sanitize_text_field($param);
    }
    
    /**
     * Method description
     *
     * @since 0.1.0
     * @param array $args Method arguments.
     * @return bool True on success, false on failure.
     */
    public function example_method($args = array()) {
        // Method implementation
        return true;
    }
}
```

#### Function Documentation
```php
/**
 * Function description
 *
 * Longer description if needed.
 *
 * @since 0.1.0
 * @param string $param1 First parameter description.
 * @param array  $param2 {
 *     Optional. Array of arguments.
 *
 *     @type string $key1 Description of key1.
 *     @type int    $key2 Description of key2.
 * }
 * @return string|WP_Error String on success, WP_Error on failure.
 */
function abcm_example_function($param1, $param2 = array()) {
    // Function implementation
}
```

### JavaScript Standards

Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/):

```javascript
/**
 * Module description
 *
 * @since 0.1.0
 */
(function($) {
    'use strict';
    
    /**
     * Example object
     *
     * @since 0.1.0
     */
    var ABCMExample = {
        
        /**
         * Initialize functionality
         *
         * @since 0.1.0
         */
        init: function() {
            this.bindEvents();
        },
        
        /**
         * Bind event handlers
         *
         * @since 0.1.0
         */
        bindEvents: function() {
            $(document).on('click', '.example-button', this.handleClick);
        },
        
        /**
         * Handle button click
         *
         * @since 0.1.0
         * @param {Event} event Click event object.
         */
        handleClick: function(event) {
            event.preventDefault();
            // Handle click
        }
    };
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        ABCMExample.init();
    });
    
})(jQuery);
```

### CSS Standards

Follow [WordPress CSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/):

```css
/**
 * Component styles
 *
 * @since 0.1.0
 */

/* Component container */
.abcm-component {
    display: flex;
    flex-direction: column;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Component title */
.abcm-component__title {
    margin: 0 0 15px;
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

/* Component content */
.abcm-component__content {
    flex: 1;
    line-height: 1.6;
}

/* Responsive styles */
@media (max-width: 768px) {
    .abcm-component {
        padding: 15px;
    }
}
```

### Naming Conventions

#### PHP
- **Classes**: `ABCM_Class_Name`
- **Functions**: `abcm_function_name()`
- **Variables**: `$variable_name`
- **Constants**: `ABCM_CONSTANT_NAME`
- **Hooks**: `abcm_hook_name`

#### JavaScript
- **Objects**: `ABCMObjectName`
- **Functions**: `functionName`
- **Variables**: `variableName`
- **Constants**: `CONSTANT_NAME`

#### CSS
- **Classes**: `.abcm-class-name`
- **IDs**: `#abcm-element-id`
- **BEM**: `.abcm-component__element--modifier`

## Testing

### Test Types

1. **Unit Tests**: Test individual functions and classes
2. **Integration Tests**: Test component interactions
3. **Functional Tests**: Test user workflows
4. **Performance Tests**: Test with large datasets

### Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/test-bulk-actions.php

# Run tests with coverage
vendor/bin/phpunit --coverage-html coverage/
```

### Writing Tests

#### Unit Test Example
```php
<?php
/**
 * Test bulk actions functionality
 */
class Test_ABCM_Bulk_Actions extends WP_UnitTestCase {
    
    private $bulk_actions;
    
    public function setUp() {
        parent::setUp();
        $this->bulk_actions = new ABCM_Bulk_Actions();
    }
    
    public function test_bulk_edit_posts() {
        // Create test posts
        $post_ids = $this->factory->post->create_many(3);
        
        // Execute bulk edit
        $result = $this->bulk_actions->bulk_edit($post_ids, array(
            'post_status' => 'draft'
        ));
        
        // Assert results
        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['processed']);
        
        // Verify posts were updated
        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);
            $this->assertEquals('draft', $post->post_status);
        }
    }
}
```

### Manual Testing Checklist

Before submitting:

- [ ] Test on different WordPress versions
- [ ] Test with different themes
- [ ] Test with common plugins activated
- [ ] Test on different PHP versions
- [ ] Test with large datasets
- [ ] Test mobile responsiveness
- [ ] Test accessibility features
- [ ] Test in different browsers

## Documentation

### Types of Documentation

1. **Code Comments**: Inline documentation
2. **User Guides**: End-user documentation
3. **Developer Docs**: Technical documentation
4. **API Reference**: Function and class reference
5. **Tutorials**: Step-by-step guides

### Documentation Standards

#### Inline Comments
```php
// Explain complex logic
if ($condition) {
    // Brief explanation of why this condition matters
    $result = complex_calculation();
}

/**
 * Detailed explanation for complex functions
 */
function complex_function() {
    // Implementation
}
```

#### User Documentation
- Write for non-technical users
- Include screenshots and examples
- Provide step-by-step instructions
- Address common use cases

#### Developer Documentation
- Include code examples
- Document all parameters and return values
- Explain integration points
- Provide troubleshooting guidance

### Updating Documentation

When making changes:

1. Update relevant documentation files
2. Add changelog entries
3. Update version numbers if needed
4. Review for accuracy and completeness

## Submitting Changes

### Pull Request Guidelines

#### Before Submitting
- [ ] All tests pass
- [ ] Code follows style guidelines
- [ ] Documentation is updated
- [ ] Changes are tested manually
- [ ] Commit messages are clear

#### Pull Request Template
```markdown
## Description
Brief description of changes made.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Unit tests added/updated
- [ ] Manual testing completed
- [ ] Tested on multiple environments

## Checklist
- [ ] Code follows project standards
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] Tests pass locally

## Related Issues
Fixes #123
Related to #456
```

#### Review Process
1. **Automated Checks**: Tests and code quality checks
2. **Code Review**: Manual review by maintainers
3. **Testing**: Additional testing if needed
4. **Approval**: Final approval and merge

### Commit Message Format

Use clear, descriptive commit messages:

```
type(scope): brief description

Longer description explaining the change in detail.
Include motivation for the change and contrast with
previous behavior.

Fixes #123
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

## Bug Reports

### Before Reporting

1. **Search existing issues** to avoid duplicates
2. **Test with default theme** and no other plugins
3. **Gather system information**
4. **Create minimal reproduction steps**

### Bug Report Template

```markdown
## Bug Description
Clear description of the bug.

## Steps to Reproduce
1. Go to '...'
2. Click on '...'
3. See error

## Expected Behavior
What you expected to happen.

## Actual Behavior
What actually happened.

## Environment
- WordPress Version: 
- Plugin Version: 
- PHP Version: 
- Browser: 
- Theme: 
- Other Plugins: 

## Additional Context
Screenshots, error logs, or other relevant information.
```

### Critical Bugs

For security vulnerabilities or critical bugs:

1. **Do not create public issues**
2. **Email maintainers directly**
3. **Provide detailed information**
4. **Allow time for fix before disclosure**

## Feature Requests

### Before Requesting

1. **Check existing issues** for similar requests
2. **Review project roadmap**
3. **Consider if it fits project scope**
4. **Think about implementation complexity**

### Feature Request Template

```markdown
## Feature Description
Clear description of the proposed feature.

## Problem/Use Case
What problem would this solve?
Who would benefit from this feature?

## Proposed Solution
How should this feature work?
Include mockups or examples if helpful.

## Alternatives Considered
What other solutions have you considered?

## Additional Context
Any other relevant information.
```

### Feature Development

If you want to implement a feature:

1. **Discuss the feature first** in an issue
2. **Get approval** from maintainers
3. **Create detailed implementation plan**
4. **Follow the development workflow**

## Community

### Communication Channels

- **GitHub Issues**: Bug reports and feature requests
- **Discussions**: General questions and ideas
- **WordPress.org Forum**: User support
- **Discord/Slack**: Real-time chat (if available)

### Getting Help

- **Documentation**: Check existing docs first
- **Search Issues**: Look for similar problems
- **Ask Questions**: Create discussion or issue
- **Community**: Engage with other contributors

### Recognition

Contributors are recognized through:

- **Changelog credits**
- **GitHub contributors list**
- **Special thanks in releases**
- **Community highlights**

## Release Process

### Version Numbering

Following [Semantic Versioning](https://semver.org/):

- **Major (1.0.0)**: Breaking changes
- **Minor (0.1.0)**: New features
- **Patch (0.1.1)**: Bug fixes

### Release Checklist

- [ ] All tests pass
- [ ] Documentation updated
- [ ] Changelog updated
- [ ] Version numbers bumped
- [ ] Compatibility tested
- [ ] Security review completed

---

## Thank You!

Thank you for contributing to Advanced Bulk Content Management! Your contributions help make WordPress content management more efficient for users worldwide.

For questions about contributing, please:
- Create a discussion on GitHub
- Review existing documentation
- Contact the maintainers

We appreciate your time and effort in making this project better! 🎉
