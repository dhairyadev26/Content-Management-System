<?php
/**
 * Search and Filter Class
 *
 * @package Advanced_Bulk_Content_Management
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Search and Filter Class
 *
 * Handles advanced search and filtering of content.
 *
 * @since 1.0.0
 */
class ABCM_Search_Filter {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Add AJAX handlers
        add_action( 'wp_ajax_abcm_ajax_filter', array( $this, 'handle_ajax_filter' ) );
    }

    /**
     * Handle AJAX filter request
     */
    public function handle_ajax_filter() {
        // Check nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'abcm_ajax_nonce' ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'advanced-bulk-content-management' ) ) );
        }

        // Check capabilities
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have sufficient permissions.', 'advanced-bulk-content-management' ) ) );
        }

        // Get filter values
        $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';
        $post_status = isset( $_POST['post_status'] ) ? sanitize_text_field( $_POST['post_status'] ) : 'any';
        $search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( $_POST['search_term'] ) : '';
        $category = isset( $_POST['category'] ) ? intval( $_POST['category'] ) : 0;
        $tag = isset( $_POST['tag'] ) ? sanitize_text_field( $_POST['tag'] ) : '';
        $author = isset( $_POST['author'] ) ? intval( $_POST['author'] ) : 0;
        $date_from = isset( $_POST['date_from'] ) ? sanitize_text_field( $_POST['date_from'] ) : '';
        $date_to = isset( $_POST['date_to'] ) ? sanitize_text_field( $_POST['date_to'] ) : '';
        $per_page = isset( $_POST['per_page'] ) ? intval( $_POST['per_page'] ) : 20;
        $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

        // Build query args
        $args = array(
            'post_type'      => $post_type,
            'post_status'    => $post_status,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        // Add search term
        if ( ! empty( $search_term ) ) {
            $args['s'] = $search_term;
        }

        // Add category
        if ( ! empty( $category ) ) {
            $args['cat'] = $category;
        }

        // Add tag
        if ( ! empty( $tag ) ) {
            $args['tag'] = $tag;
        }

        // Add author
        if ( ! empty( $author ) ) {
            $args['author'] = $author;
        }

        // Add date range
        $date_query = array();

        if ( ! empty( $date_from ) ) {
            $date_query['after'] = $date_from;
        }

        if ( ! empty( $date_to ) ) {
            $date_query['before'] = $date_to;
        }

        if ( ! empty( $date_query ) ) {
            $date_query['inclusive'] = true;
            $args['date_query'] = array( $date_query );
        }

        // Apply filters to allow extensions to modify the query
        $args = apply_filters( 'abcm_filter_query_args', $args, $_POST );

        // Get posts
        $query = new WP_Query( $args );

        // Prepare response
        $response = array(
            'success' => true,
            'posts'   => array(),
            'total'   => $query->found_posts,
            'pages'   => ceil( $query->found_posts / $per_page ),
        );

        // Format posts for response
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $response['posts'][] = array(
                    'ID'        => $post_id,
                    'title'     => get_the_title(),
                    'permalink' => get_permalink( $post_id ),
                    'edit_link' => get_edit_post_link( $post_id, 'raw' ),
                    'author'    => get_the_author(),
                    'date'      => get_the_date(),
                    'status'    => get_post_status(),
                    'categories' => get_the_category_list( ', ', '', $post_id ),
                    'tags'      => get_the_tag_list( '', ', ', '', $post_id ),
                );
            }
            wp_reset_postdata();
        }

        // Send response
        wp_send_json_success( $response );
    }

    /**
     * Get advanced filter form HTML
     *
     * @return string Form HTML.
     */
    public function get_filter_form() {
        ob_start();
        ?>
        <div class="abcm-filter-section">
            <h3><?php esc_html_e( 'Advanced Filter', 'advanced-bulk-content-management' ); ?></h3>
            
            <div class="abcm-filter-row">
                <div class="abcm-filter-item">
                    <label for="abcm-search-term"><?php esc_html_e( 'Search', 'advanced-bulk-content-management' ); ?></label>
                    <input type="text" id="abcm-search-term" name="search_term" placeholder="<?php esc_attr_e( 'Search term...', 'advanced-bulk-content-management' ); ?>">
                </div>
                
                <div class="abcm-filter-item">
                    <label for="abcm-post-type"><?php esc_html_e( 'Post Type', 'advanced-bulk-content-management' ); ?></label>
                    <select id="abcm-post-type" name="post_type">
                        <?php
                        $post_types = ABCM_Hooks::get_post_types();
                        foreach ( $post_types as $type => $label ) {
                            printf(
                                '<option value="%s">%s</option>',
                                esc_attr( $type ),
                                esc_html( $label )
                            );
                        }
                        ?>
                    </select>
                </div>
                
                <div class="abcm-filter-item">
                    <label for="abcm-post-status"><?php esc_html_e( 'Status', 'advanced-bulk-content-management' ); ?></label>
                    <select id="abcm-post-status" name="post_status">
                        <?php
                        $post_statuses = ABCM_Hooks::get_post_statuses();
                        foreach ( $post_statuses as $status => $label ) {
                            printf(
                                '<option value="%s">%s</option>',
                                esc_attr( $status ),
                                esc_html( $label )
                            );
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="abcm-filter-row">
                <div class="abcm-filter-item">
                    <label for="abcm-category"><?php esc_html_e( 'Category', 'advanced-bulk-content-management' ); ?></label>
                    <?php
                    wp_dropdown_categories( array(
                        'show_option_all' => __( 'All Categories', 'advanced-bulk-content-management' ),
                        'name'            => 'category',
                        'id'              => 'abcm-category',
                        'hierarchical'    => true,
                        'depth'           => 3,
                    ) );
                    ?>
                </div>
                
                <div class="abcm-filter-item">
                    <label for="abcm-tag"><?php esc_html_e( 'Tag', 'advanced-bulk-content-management' ); ?></label>
                    <input type="text" id="abcm-tag" name="tag" placeholder="<?php esc_attr_e( 'Tag slug...', 'advanced-bulk-content-management' ); ?>">
                </div>
                
                <div class="abcm-filter-item">
                    <label for="abcm-author"><?php esc_html_e( 'Author', 'advanced-bulk-content-management' ); ?></label>
                    <?php
                    wp_dropdown_users( array(
                        'show_option_all' => __( 'All Authors', 'advanced-bulk-content-management' ),
                        'name'            => 'author',
                        'id'              => 'abcm-author',
                        'who'             => 'authors',
                    ) );
                    ?>
                </div>
            </div>
            
            <div class="abcm-filter-row">
                <div class="abcm-filter-item">
                    <label for="abcm-date-from"><?php esc_html_e( 'Date From', 'advanced-bulk-content-management' ); ?></label>
                    <input type="date" id="abcm-date-from" name="date_from">
                </div>
                
                <div class="abcm-filter-item">
                    <label for="abcm-date-to"><?php esc_html_e( 'Date To', 'advanced-bulk-content-management' ); ?></label>
                    <input type="date" id="abcm-date-to" name="date_to">
                </div>
                
                <div class="abcm-filter-item">
                    <label for="abcm-per-page"><?php esc_html_e( 'Per Page', 'advanced-bulk-content-management' ); ?></label>
                    <select id="abcm-per-page" name="per_page">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            
            <div class="abcm-filter-actions">
                <button type="button" id="abcm-apply-filter" class="button button-primary"><?php esc_html_e( 'Apply Filters', 'advanced-bulk-content-management' ); ?></button>
                <button type="button" id="abcm-reset-filter" class="button"><?php esc_html_e( 'Reset', 'advanced-bulk-content-management' ); ?></button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Build WP_Query args from request
     *
     * @param array $request Request data.
     * @return array Query args.
     */
    public static function build_query_args( $request ) {
        // Get filter values
        $post_type = isset( $request['post_type'] ) ? sanitize_text_field( $request['post_type'] ) : 'post';
        $post_status = isset( $request['post_status'] ) ? sanitize_text_field( $request['post_status'] ) : 'any';
        $search_term = isset( $request['search_term'] ) ? sanitize_text_field( $request['search_term'] ) : '';
        $category = isset( $request['category'] ) ? intval( $request['category'] ) : 0;
        $tag = isset( $request['tag'] ) ? sanitize_text_field( $request['tag'] ) : '';
        $author = isset( $request['author'] ) ? intval( $request['author'] ) : 0;
        $date_from = isset( $request['date_from'] ) ? sanitize_text_field( $request['date_from'] ) : '';
        $date_to = isset( $request['date_to'] ) ? sanitize_text_field( $request['date_to'] ) : '';
        $per_page = isset( $request['per_page'] ) ? intval( $request['per_page'] ) : 20;
        $page = isset( $request['paged'] ) ? intval( $request['paged'] ) : 1;

        // Build query args
        $args = array(
            'post_type'      => $post_type,
            'post_status'    => $post_status,
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'date',
            'order'          => 'DESC',
        );

        // Add search term
        if ( ! empty( $search_term ) ) {
            $args['s'] = $search_term;
        }

        // Add category
        if ( ! empty( $category ) ) {
            $args['cat'] = $category;
        }

        // Add tag
        if ( ! empty( $tag ) ) {
            $args['tag'] = $tag;
        }

        // Add author
        if ( ! empty( $author ) ) {
            $args['author'] = $author;
        }

        // Add date range
        $date_query = array();

        if ( ! empty( $date_from ) ) {
            $date_query['after'] = $date_from;
        }

        if ( ! empty( $date_to ) ) {
            $date_query['before'] = $date_to;
        }

        if ( ! empty( $date_query ) ) {
            $date_query['inclusive'] = true;
            $args['date_query'] = array( $date_query );
        }

        // Apply filters to allow extensions to modify the query
        return apply_filters( 'abcm_filter_query_args', $args, $request );
    }
}


// Updated: 2025-07-10T09:00:00 - Add filter for post types in admin table


// Updated: 2025-07-10T09:15:00 - Add filter for post status in admin table


// Updated: 2025-07-10T09:30:00 - Add filter for categories in admin table


// Updated: 2025-07-10T09:45:00 - Add filter for tags in admin table


// Updated: 2025-07-10T10:00:00 - Add filter for authors in admin table


// Updated: 2025-07-10T10:45:00 - Add AJAX handler for filters


// Updated: 2025-07-10T11:00:00 - Add nonce check to filter AJAX handler


// Updated: 2025-07-10T11:15:00 - Add capability check to filter AJAX handler


// Updated: 2025-07-10T11:30:00 - Add error handling to filter AJAX handler


// Updated: 2025-07-10T11:45:00 - Add success response to filter AJAX handler


// Updated: 2025-07-10T12:15:00 - Add reset button for filters


// Updated: 2025-07-10T13:00:00 - Add error message for invalid filters


// Updated: 2025-07-10T13:15:00 - Add success message for valid filters


// Updated: 2025-07-10T13:45:00 - Refactor filter PHP code


// Updated: 2025-07-15T09:00:00 - Add comments to filter PHP code


// Updated: 2025-07-15T10:00:00 - Fix bug in filter AJAX handler


// Updated: 2025-06-10T10:30:00 - Create search and filter class structure
