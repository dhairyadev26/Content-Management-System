<?php
/**
 * Hooks Class
 *
 * @package Advanced_Bulk_Content_Management
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Hooks Class
 *
 * Provides hooks and filters for extending the plugin functionality.
 *
 * @since 1.0.0
 */
class ABCM_Hooks {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Nothing to initialize here
    }

    /**
     * Get available bulk actions
     *
     * @return array Array of bulk actions.
     */
    public static function get_bulk_actions() {
        $actions = array(
            'trash'   => __( 'Move to Trash', 'advanced-bulk-content-management' ),
            'publish' => __( 'Set to Published', 'advanced-bulk-content-management' ),
            'draft'   => __( 'Set to Draft', 'advanced-bulk-content-management' ),
        );
        
        /**
         * Filter the available bulk actions.
         *
         * @param array $actions Array of bulk actions.
         * @return array
         */
        return apply_filters( 'abcm_bulk_actions', $actions );
    }

    /**
     * Get available post types
     *
     * @return array Array of post types.
     */
    public static function get_post_types() {
        $post_types = array(
            'post' => __( 'Posts', 'advanced-bulk-content-management' ),
            'page' => __( 'Pages', 'advanced-bulk-content-management' ),
        );
        
        // Get custom post types
        $custom_post_types = get_post_types( array(
            'public'   => true,
            '_builtin' => false,
        ), 'objects' );
        
        foreach ( $custom_post_types as $post_type ) {
            $post_types[ $post_type->name ] = $post_type->label;
        }
        
        /**
         * Filter the available post types.
         *
         * @param array $post_types Array of post types.
         * @return array
         */
        return apply_filters( 'abcm_post_types', $post_types );
    }

    /**
     * Get available post statuses
     *
     * @return array Array of post statuses.
     */
    public static function get_post_statuses() {
        $statuses = array(
            'any'     => __( 'Any', 'advanced-bulk-content-management' ),
            'publish' => __( 'Published', 'advanced-bulk-content-management' ),
            'draft'   => __( 'Draft', 'advanced-bulk-content-management' ),
            'pending' => __( 'Pending', 'advanced-bulk-content-management' ),
            'trash'   => __( 'Trash', 'advanced-bulk-content-management' ),
        );
        
        /**
         * Filter the available post statuses.
         *
         * @param array $statuses Array of post statuses.
         * @return array
         */
        return apply_filters( 'abcm_post_statuses', $statuses );
    }

    /**
     * Process bulk action
     *
     * @param string $action   The action to perform.
     * @param int    $post_id  The post ID to process.
     * @param array  $args     Additional arguments.
     * @return bool|int True/post ID on success, false on failure.
     */
    public static function process_bulk_action( $action, $post_id, $args = array() ) {
        $result = false;
        
        switch ( $action ) {
            case 'trash':
                $result = wp_trash_post( $post_id );
                break;
                
            case 'publish':
                $result = wp_update_post( array(
                    'ID'          => $post_id,
                    'post_status' => 'publish',
                ) );
                break;
                
            case 'draft':
                $result = wp_update_post( array(
                    'ID'          => $post_id,
                    'post_status' => 'draft',
                ) );
                break;
                
            case 'set_categories':
                if ( isset( $args['categories'] ) && ! empty( $args['categories'] ) ) {
                    $result = wp_set_post_categories( $post_id, $args['categories'], false );
                }
                break;
                
            case 'add_categories':
                if ( isset( $args['categories'] ) && ! empty( $args['categories'] ) ) {
                    $current_categories = wp_get_post_categories( $post_id );
                    $new_categories = array_unique( array_merge( $current_categories, $args['categories'] ) );
                    $result = wp_set_post_categories( $post_id, $new_categories, false );
                }
                break;
                
            case 'remove_categories':
                if ( isset( $args['categories'] ) && ! empty( $args['categories'] ) ) {
                    $current_categories = wp_get_post_categories( $post_id );
                    $new_categories = array_diff( $current_categories, $args['categories'] );
                    
                    // Ensure at least the default category if all are removed
                    if ( empty( $new_categories ) ) {
                        $new_categories = array( get_option( 'default_category' ) );
                    }
                    
                    $result = wp_set_post_categories( $post_id, $new_categories, false );
                }
                break;
                
            case 'set_tags':
                if ( isset( $args['tags'] ) ) {
                    $result = wp_set_post_tags( $post_id, $args['tags'], false );
                }
                break;
                
            case 'add_tags':
                if ( isset( $args['tags'] ) ) {
                    $result = wp_set_post_tags( $post_id, $args['tags'], true );
                }
                break;
                
            case 'remove_tags':
                if ( isset( $args['tags'] ) ) {
                    $current_tags = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
                    $tags_to_remove = is_array( $args['tags'] ) ? $args['tags'] : explode( ',', $args['tags'] );
                    $tags_to_remove = array_map( 'trim', $tags_to_remove );
                    $new_tags = array_diff( $current_tags, $tags_to_remove );
                    $result = wp_set_post_tags( $post_id, $new_tags, false );
                }
                break;
                
            case 'set_author':
                if ( isset( $args['author'] ) ) {
                    $result = wp_update_post( array(
                        'ID'          => $post_id,
                        'post_author' => intval( $args['author'] ),
                    ) );
                }
                break;
                
            default:
                /**
                 * Process custom bulk action.
                 *
                 * @param bool|int $result  The result of the action.
                 * @param string   $action  The action to perform.
                 * @param int      $post_id The post ID to process.
                 * @param array    $args    Additional arguments.
                 * @return bool|int
                 */
                $result = apply_filters( 'abcm_process_bulk_action', $result, $action, $post_id, $args );
                break;
        }
        
        return $result;
    }
}


// Updated: 2025-06-03T11:20:00 - Add plugin activation and deactivation hooks
