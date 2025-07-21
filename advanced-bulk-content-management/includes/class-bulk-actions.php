<?php
/**
 * Bulk Actions Class
 *
 * @package Advanced_Bulk_Content_Management
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Bulk Actions Class
 *
 * Handles custom bulk actions for content management.
 *
 * @since 1.0.0
 */
class ABCM_Bulk_Actions {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Register bulk actions
        add_filter( 'bulk_actions-edit-post', array( $this, 'register_bulk_actions' ) );
        add_filter( 'bulk_actions-edit-page', array( $this, 'register_bulk_actions' ) );
        
        // Add custom post types bulk actions
        add_action( 'admin_init', array( $this, 'add_custom_post_type_bulk_actions' ) );
        
        // Add AJAX handlers
        add_action( 'wp_ajax_abcm_bulk_action', array( $this, 'handle_ajax_bulk_action' ) );
        
        // Enqueue scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }
    
    /**
     * Register custom bulk actions for edit screen
     *
     * @param array $bulk_actions Current bulk actions
     * @return array Modified bulk actions
     */
    public function register_bulk_actions( $bulk_actions ) {
        $custom_actions = self::get_custom_bulk_actions();
        
        return array_merge( $bulk_actions, $custom_actions );
    }
    
    /**
     * Add bulk actions to custom post types
     */
    public function add_custom_post_type_bulk_actions() {
        $post_types = get_post_types( array( 'public' => true ) );
        
        foreach ( $post_types as $post_type ) {
            if ( 'post' !== $post_type && 'page' !== $post_type ) {
                add_filter( 'bulk_actions-edit-' . $post_type, array( $this, 'register_bulk_actions' ) );
            }
        }
    }
    
    /**
     * Enqueue scripts and styles for bulk actions
     *
     * @param string $hook Current admin page
     */
    public function enqueue_scripts( $hook ) {
        // Only load on edit screens
        if ( 'edit.php' !== $hook ) {
            return;
        }
        
        // Enqueue Select2 if not already enqueued
        if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
            wp_enqueue_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0', true );
            wp_enqueue_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0' );
        }
        
        // Enqueue our scripts and styles
        wp_enqueue_script( 'abcm-bulk-actions', ABCM_PLUGIN_URL . 'assets/js/bulk-actions.js', array( 'jquery', 'select2' ), ABCM_VERSION, true );
        wp_enqueue_style( 'abcm-bulk-actions', ABCM_PLUGIN_URL . 'assets/css/bulk-actions.css', array(), ABCM_VERSION );
        
        // Localize script
        wp_localize_script( 'abcm-bulk-actions', 'abcm_admin_vars', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'abcm_bulk_action_nonce' ),
            'categories' => $this->get_categories_for_js(),
            'authors'    => $this->get_authors_for_js(),
            'i18n'     => array(
                'set_categories'    => __( 'Set Categories', 'advanced-bulk-content-management' ),
                'add_categories'    => __( 'Add Categories', 'advanced-bulk-content-management' ),
                'remove_categories' => __( 'Remove Categories', 'advanced-bulk-content-management' ),
                'set_tags'          => __( 'Set Tags', 'advanced-bulk-content-management' ),
                'add_tags'          => __( 'Add Tags', 'advanced-bulk-content-management' ),
                'remove_tags'       => __( 'Remove Tags', 'advanced-bulk-content-management' ),
                'set_author'        => __( 'Set Author', 'advanced-bulk-content-management' ),
                'selected_items'    => __( 'Selected items: %d', 'advanced-bulk-content-management' ),
                'use_async'         => __( 'Process in background (recommended for large batches)', 'advanced-bulk-content-management' ),
                'cancel'            => __( 'Cancel', 'advanced-bulk-content-management' ),
                'apply'             => __( 'Apply', 'advanced-bulk-content-management' ),
                'ajax_error'        => __( 'An error occurred while processing the request.', 'advanced-bulk-content-management' ),
                'job_scheduled'     => __( 'Job scheduled with ID: %s. You can check the status in the Jobs tab.', 'advanced-bulk-content-management' ),
            ),
        ) );
    }

    /**
     * Handle AJAX bulk action request
     */
    public function handle_ajax_bulk_action() {
        // Check nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'abcm_bulk_action_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'advanced-bulk-content-management' ) ) );
        }

        // Check capabilities
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have sufficient permissions.', 'advanced-bulk-content-management' ) ) );
        }

        // Get action and post IDs
        $action = isset( $_POST['action_name'] ) ? sanitize_text_field( $_POST['action_name'] ) : '';
        $post_ids = isset( $_POST['post_ids'] ) ? array_map( 'intval', explode(',', $_POST['post_ids']) ) : array();

        // Validate input
        if ( empty( $action ) ) {
            wp_send_json_error( array( 'message' => __( 'No action specified.', 'advanced-bulk-content-management' ) ) );
        }

        if ( empty( $post_ids ) ) {
            wp_send_json_error( array( 'message' => __( 'No items selected.', 'advanced-bulk-content-management' ) ) );
        }

        // Check if we should use async processing
        $use_async = isset( $_POST['use_async'] ) && $_POST['use_async'] === 'true';
        
        // If using async processing, schedule the job
        if ( $use_async && count( $post_ids ) > 10 ) {
            $async_jobs = new ABCM_Async_Jobs();
            $job_id = $async_jobs->schedule_job( $action, $post_ids );
            
            if ( $job_id ) {
                wp_send_json_success( array(
                    'message' => __( 'Job scheduled. Processing will happen in the background.', 'advanced-bulk-content-management' ),
                    'job_id'  => $job_id,
                    'async'   => true,
                ) );
            } else {
                wp_send_json_error( array( 'message' => __( 'Failed to schedule job.', 'advanced-bulk-content-management' ) ) );
            }
            
            // Stop execution here if we're using async
            return;
        }
        
        // Process bulk action (non-async)
        $action_data = isset( $_POST['action_data'] ) ? $_POST['action_data'] : array();
        $sanitized_action_data = $this->sanitize_action_data( $action_data, $action );
        
        $results = $this->process_bulk_action( $action, $post_ids, $sanitized_action_data );
        
        wp_send_json_success( $results );
    }
    
    /**
     * Sanitize action data
     *
     * @param array  $action_data The action data to sanitize
     * @param string $action      The action name
     * @return array Sanitized action data
     */
    private function sanitize_action_data( $action_data, $action ) {
        $sanitized_data = array();
        
        switch ( $action ) {
            case 'set_categories':
            case 'add_categories':
            case 'remove_categories':
                if ( isset( $action_data['categories'] ) ) {
                    $categories = is_array( $action_data['categories'] ) 
                        ? $action_data['categories'] 
                        : explode( ',', $action_data['categories'] );
                    $sanitized_data['categories'] = array_map( 'intval', $categories );
                }
                break;
                
            case 'set_tags':
            case 'add_tags':
            case 'remove_tags':
                if ( isset( $action_data['tags'] ) ) {
                    $tags = sanitize_text_field( $action_data['tags'] );
                    $sanitized_data['tags'] = $tags;
                }
                break;
                
            case 'set_author':
                if ( isset( $action_data['author'] ) ) {
                    $sanitized_data['author'] = intval( $action_data['author'] );
                }
                break;
                
            default:
                // Filter other action data
                $sanitized_data = apply_filters( 'abcm_sanitize_action_data', array(), $action_data, $action );
                break;
        }
        
        return $sanitized_data;
    }

    /**
     * Process bulk action
     *
     * @param string $action   The action to perform.
     * @param array  $post_ids Array of post IDs to process.
     * @param array  $action_data Optional. Action-specific data.
     * @return array Results of the operation.
     */
    public function process_bulk_action( $action, $post_ids, $action_data = array() ) {
        $success = 0;
        $failed = 0;
        $logger = new ABCM_Logger();
        
        // Process based on action type
        switch ($action) {
            case 'set_categories':
                if (isset($action_data['categories']) && !empty($action_data['categories'])) {
                    foreach ($post_ids as $post_id) {
                        if (!current_user_can('edit_post', $post_id)) {
                            $failed++;
                            continue;
                        }
                        
                        $post_type = get_post_type($post_id);
                        if (!is_object_in_taxonomy($post_type, 'category')) {
                            $failed++;
                            continue;
                        }
                        
                        $result = wp_set_post_categories($post_id, $action_data['categories']);
                        if ($result) {
                            $success++;
                            $logger->log('set_categories', $post_type, $post_id, get_the_title($post_id));
                        } else {
                            $failed++;
                        }
                    }
                }
                break;
                
            case 'add_categories':
                if (isset($action_data['categories']) && !empty($action_data['categories'])) {
                    foreach ($post_ids as $post_id) {
                        if (!current_user_can('edit_post', $post_id)) {
                            $failed++;
                            continue;
                        }
                        
                        $post_type = get_post_type($post_id);
                        if (!is_object_in_taxonomy($post_type, 'category')) {
                            $failed++;
                            continue;
                        }
                        
                        $current_categories = wp_get_post_categories($post_id);
                        $new_categories = array_unique(array_merge($current_categories, $action_data['categories']));
                        
                        $result = wp_set_post_categories($post_id, $new_categories);
                        if ($result) {
                            $success++;
                            $logger->log('add_categories', $post_type, $post_id, get_the_title($post_id));
                        } else {
                            $failed++;
                        }
                    }
                }
                break;
                
            case 'remove_categories':
                if (isset($action_data['categories']) && !empty($action_data['categories'])) {
                    foreach ($post_ids as $post_id) {
                        if (!current_user_can('edit_post', $post_id)) {
                            $failed++;
                            continue;
                        }
                        
                        $post_type = get_post_type($post_id);
                        if (!is_object_in_taxonomy($post_type, 'category')) {
                            $failed++;
                            continue;
                        }
                        
                        $current_categories = wp_get_post_categories($post_id);
                        $new_categories = array_diff($current_categories, $action_data['categories']);
                        
                        // If removing all categories, set to default category
                        if (empty($new_categories)) {
                            $new_categories = array(get_option('default_category'));
                        }
                        
                        $result = wp_set_post_categories($post_id, $new_categories);
                        if ($result) {
                            $success++;
                            $logger->log('remove_categories', $post_type, $post_id, get_the_title($post_id));
                        } else {
                            $failed++;
                        }
                    }
                }
                break;
                
            case 'set_tags':
                if (isset($action_data['tags'])) {
                    foreach ($post_ids as $post_id) {
                        if (!current_user_can('edit_post', $post_id)) {
                            $failed++;
                            continue;
                        }
                        
                        $post_type = get_post_type($post_id);
                        if (!is_object_in_taxonomy($post_type, 'post_tag')) {
                            $failed++;
                            continue;
                        }
                        
                        $result = wp_set_post_tags($post_id, $action_data['tags'], false);
                        if ($result) {
                            $success++;
                            $logger->log('set_tags', $post_type, $post_id, get_the_title($post_id));
                        } else {
                            $failed++;
                        }
                    }
                }
                break;
                
            case 'add_tags':
                if (isset($action_data['tags'])) {
                    foreach ($post_ids as $post_id) {
                        if (!current_user_can('edit_post', $post_id)) {
                            $failed++;
                            continue;
                        }
                        
                        $post_type = get_post_type($post_id);
                        if (!is_object_in_taxonomy($post_type, 'post_tag')) {
                            $failed++;
                            continue;
                        }
                        
                        $result = wp_set_post_tags($post_id, $action_data['tags'], true);
                        if ($result) {
                            $success++;
                            $logger->log('add_tags', $post_type, $post_id, get_the_title($post_id));
                        } else {
                            $failed++;
                        }
                    }
                }
                break;
                
            case 'remove_tags':
                if (isset($action_data['tags'])) {
                    foreach ($post_ids as $post_id) {
                        if (!current_user_can('edit_post', $post_id)) {
                            $failed++;
                            continue;
                        }
                        
                        $post_type = get_post_type($post_id);
                        if (!is_object_in_taxonomy($post_type, 'post_tag')) {
                            $failed++;
                            continue;
                        }
                        
                        $current_tags = wp_get_post_tags($post_id, array('fields' => 'names'));
                        $tags_to_remove = is_array($action_data['tags']) ? $action_data['tags'] : explode(',', $action_data['tags']);
                        $tags_to_remove = array_map('trim', $tags_to_remove);
                        $new_tags = array_diff($current_tags, $tags_to_remove);
                        
                        $result = wp_set_post_tags($post_id, $new_tags, false);
                        if ($result) {
                            $success++;
                            $logger->log('remove_tags', $post_type, $post_id, get_the_title($post_id));
                        } else {
                            $failed++;
                        }
                    }
                }
                break;
                
            case 'set_author':
                if (isset($action_data['author'])) {
                    foreach ($post_ids as $post_id) {
                        if (!current_user_can('edit_post', $post_id) || !current_user_can('edit_others_posts')) {
                            $failed++;
                            continue;
                        }
                        
                        $result = wp_update_post(array(
                            'ID' => $post_id,
                            'post_author' => intval($action_data['author']),
                        ));
                        
                        if ($result) {
                            $success++;
                            $logger->log('set_author', get_post_type($post_id), $post_id, get_the_title($post_id));
                        } else {
                            $failed++;
                        }
                    }
                }
                break;
                
            default:
                // Process the action using the hooks class for other actions
                foreach ($post_ids as $post_id) {
                    $post = get_post($post_id);
                    
                    if (!$post) {
                        $failed++;
                        continue;
                    }
                    
                    $post_type = get_post_type($post);
                    
                    // Process the action using the hooks class
                    $result = ABCM_Hooks::process_bulk_action($action, $post_id, $action_data);
                    
                    if ($result) {
                        $success++;
                        $logger->log($action, $post_type, $post_id, $post->post_title);
                    } else {
                        $failed++;
                    }
                }
                break;
        }
        
        // Prepare result message
        $message = sprintf(
            /* translators: 1: number of successfully processed items, 2: number of failed items */
            __( 'Processed %1$d items successfully. %2$d items failed.', 'advanced-bulk-content-management' ),
            $success,
            $failed
        );
        
        return array(
            'message'  => $message,
            'success'  => $success,
            'failed'   => $failed,
            'total'    => count( $post_ids ),
            'action'   => $action,
            'post_ids' => $post_ids,
        );
    }

    /**
     * Get custom bulk actions
     *
     * @return array Array of custom bulk actions.
     */
    public static function get_custom_bulk_actions() {
        $actions = array(
            'set_categories' => __( 'Set Categories', 'advanced-bulk-content-management' ),
            'add_categories' => __( 'Add Categories', 'advanced-bulk-content-management' ),
            'remove_categories' => __( 'Remove Categories', 'advanced-bulk-content-management' ),
            'set_tags' => __( 'Set Tags', 'advanced-bulk-content-management' ),
            'add_tags' => __( 'Add Tags', 'advanced-bulk-content-management' ),
            'remove_tags' => __( 'Remove Tags', 'advanced-bulk-content-management' ),
            'set_author' => __( 'Set Author', 'advanced-bulk-content-management' ),
        );
        
        /**
         * Filter the custom bulk actions.
         *
         * @param array $actions Array of custom bulk actions.
         * @return array
         */
        return apply_filters( 'abcm_custom_bulk_actions', $actions );
    }

    /**
     * Get bulk action form fields
     *
     * @param string $action The action name.
     * @return string HTML for the form fields.
     */
    public static function get_bulk_action_fields( $action ) {
        $fields = '';
        
        switch ( $action ) {
            case 'set_categories':
            case 'add_categories':
            case 'remove_categories':
                $fields = self::get_categories_field();
                break;
                
            case 'set_tags':
            case 'add_tags':
            case 'remove_tags':
                $fields = self::get_tags_field();
                break;
                
            case 'set_author':
                $fields = self::get_author_field();
                break;
                
            default:
                /**
                 * Filter the custom bulk action fields.
                 *
                 * @param string $fields HTML for the form fields.
                 * @param string $action The action name.
                 * @return string
                 */
                $fields = apply_filters( 'abcm_bulk_action_fields', $fields, $action );
                break;
        }
        
        return $fields;
    }

    /**
     * Get categories field
     *
     * @return string HTML for the categories field.
     */
    private static function get_categories_field() {
        ob_start();
        ?>
        <div class="abcm-action-field">
            <label for="abcm-categories"><?php esc_html_e( 'Categories', 'advanced-bulk-content-management' ); ?></label>
            <?php
            wp_dropdown_categories( array(
                'show_option_all' => __( 'Select Categories', 'advanced-bulk-content-management' ),
                'name'            => 'categories[]',
                'id'              => 'abcm-categories',
                'hierarchical'    => true,
                'depth'           => 3,
                'multiple'        => true,
                'class'           => 'abcm-select2',
            ) );
            ?>
            <p class="description"><?php esc_html_e( 'Select one or more categories.', 'advanced-bulk-content-management' ); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get tags field
     *
     * @return string HTML for the tags field.
     */
    private static function get_tags_field() {
        ob_start();
        ?>
        <div class="abcm-action-field">
            <label for="abcm-tags"><?php esc_html_e( 'Tags', 'advanced-bulk-content-management' ); ?></label>
            <input type="text" id="abcm-tags" name="tags" class="regular-text" placeholder="<?php esc_attr_e( 'Enter tags separated by commas', 'advanced-bulk-content-management' ); ?>">
            <p class="description"><?php esc_html_e( 'Enter tags separated by commas.', 'advanced-bulk-content-management' ); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Get author field
     *
     * @return string HTML for the author field.
     */
    private static function get_author_field() {
        ob_start();
        ?>
        <div class="abcm-action-field">
            <label for="abcm-author"><?php esc_html_e( 'Author', 'advanced-bulk-content-management' ); ?></label>
            <?php
            wp_dropdown_users( array(
                'name'     => 'author',
                'id'       => 'abcm-author',
                'who'      => 'authors',
                'class'    => 'abcm-select2',
                'selected' => get_current_user_id(),
            ) );
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get categories for JavaScript
     * 
     * @return array Categories formatted for JavaScript
     */
    private function get_categories_for_js() {
        $categories = get_categories( array(
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ) );
        
        $formatted = array();
        foreach ( $categories as $category ) {
            $formatted[] = array(
                'id'   => $category->term_id,
                'name' => $category->name,
            );
        }
        
        return $formatted;
    }
    
    /**
     * Get authors for JavaScript
     * 
     * @return array Authors formatted for JavaScript
     */
    private function get_authors_for_js() {
        $authors = get_users( array(
            'who'     => 'authors',
            'orderby' => 'display_name',
            'order'   => 'ASC',
        ) );
        
        $formatted = array();
        foreach ( $authors as $author ) {
            $formatted[] = array(
                'id'   => $author->ID,
                'name' => $author->display_name,
            );
        }
        
        return $formatted;
    }
}


// Updated: 2025-07-01T11:15:00 - Add AJAX handler for bulk actions


// Updated: 2025-07-01T11:30:00 - Add nonce check to AJAX handler


// Updated: 2025-07-01T11:45:00 - Add capability check to AJAX handler


// Updated: 2025-07-01T12:00:00 - Add error handling to AJAX handler


// Updated: 2025-07-01T12:15:00 - Add success response to AJAX handler


// Updated: 2025-06-08T09:30:00 - Implement basic bulk actions framework


// Updated: 2025-06-17T09:30:00 - Improve bulk actions error handling


// Updated: 2025-07-08T09:15:00 - Begin performance optimization for bulk actions


// Updated: 2025-07-18T09:30:00 - Implement import functionality for bulk content


// Updated: 2025-07-18T14:45:00 - Add CSV import support


// Updated: 2025-07-19T10:00:00 - Implement JSON import support


// Updated: 2025-07-21T09:15:00 - Implement validation for imported data
