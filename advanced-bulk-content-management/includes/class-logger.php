<?php
/**
 * Logger Class
 *
 * @package Advanced_Bulk_Content_Management
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Logger Class
 *
 * Handles logging of bulk actions for audit and tracking purposes.
 *
 * @since 1.0.0
 */
class ABCM_Logger {

    /**
     * Table name
     *
     * @var string
     */
    private $table_name;

    /**
     * Initialize the class
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'abcm_logs';
        
        // Create log table if it doesn't exist
        $this->maybe_create_table();
    }

    /**
     * Create log table if it doesn't exist
     */
    private function maybe_create_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            action varchar(50) NOT NULL,
            object_type varchar(50) NOT NULL,
            object_id bigint(20) NOT NULL,
            object_title varchar(255) NOT NULL,
            date_created datetime NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Log an action
     *
     * @param string $action      The action performed.
     * @param string $object_type The type of object (post, page, etc.).
     * @param int    $object_id   The ID of the object.
     * @param string $object_title The title of the object.
     * @return bool|int The ID of the inserted row, or false on failure.
     */
    public function log( $action, $object_type, $object_id, $object_title ) {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $result = $wpdb->insert(
            $this->table_name,
            array(
                'user_id'      => $user_id,
                'action'       => $action,
                'object_type'  => $object_type,
                'object_id'    => $object_id,
                'object_title' => $object_title,
                'date_created' => current_time( 'mysql' ),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
            )
        );
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Get logs
     *
     * @param array $args Query arguments.
     * @return array Array of logs.
     */
    public function get_logs( $args = array() ) {
        global $wpdb;
        
        $defaults = array(
            'number'     => 20,
            'offset'     => 0,
            'orderby'    => 'id',
            'order'      => 'DESC',
            'user_id'    => '',
            'action'     => '',
            'object_type' => '',
            'object_id'  => '',
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where = 'WHERE 1=1';
        
        if ( ! empty( $args['user_id'] ) ) {
            $where .= $wpdb->prepare( ' AND user_id = %d', $args['user_id'] );
        }
        
        if ( ! empty( $args['action'] ) ) {
            $where .= $wpdb->prepare( ' AND action = %s', $args['action'] );
        }
        
        if ( ! empty( $args['object_type'] ) ) {
            $where .= $wpdb->prepare( ' AND object_type = %s', $args['object_type'] );
        }
        
        if ( ! empty( $args['object_id'] ) ) {
            $where .= $wpdb->prepare( ' AND object_id = %d', $args['object_id'] );
        }
        
        $sql = "SELECT * FROM {$this->table_name} {$where} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d";
        
        $sql = $wpdb->prepare( $sql, $args['offset'], $args['number'] );
        
        return $wpdb->get_results( $sql );
    }
    
    /**
     * Count logs
     *
     * @param array $args Query arguments.
     * @return int Number of logs.
     */
    public function count_logs( $args = array() ) {
        global $wpdb;
        
        $defaults = array(
            'user_id'    => '',
            'action'     => '',
            'object_type' => '',
            'object_id'  => '',
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where = 'WHERE 1=1';
        
        if ( ! empty( $args['user_id'] ) ) {
            $where .= $wpdb->prepare( ' AND user_id = %d', $args['user_id'] );
        }
        
        if ( ! empty( $args['action'] ) ) {
            $where .= $wpdb->prepare( ' AND action = %s', $args['action'] );
        }
        
        if ( ! empty( $args['object_type'] ) ) {
            $where .= $wpdb->prepare( ' AND object_type = %s', $args['object_type'] );
        }
        
        if ( ! empty( $args['object_id'] ) ) {
            $where .= $wpdb->prepare( ' AND object_id = %d', $args['object_id'] );
        }
        
        $sql = "SELECT COUNT(*) FROM {$this->table_name} {$where}";
        
        return (int) $wpdb->get_var( $sql );
    }
    
    /**
     * Export logs to CSV
     *
     * @param array $args Query arguments.
     * @return string|bool CSV content or false on failure.
     */
    public function export_csv( $args = array() ) {
        $logs = $this->get_logs( $args );
        
        if ( empty( $logs ) ) {
            return false;
        }
        
        $output = fopen( 'php://temp', 'r+' );
        
        // Add CSV headers
        fputcsv( $output, array(
            __( 'ID', 'advanced-bulk-content-management' ),
            __( 'User', 'advanced-bulk-content-management' ),
            __( 'Action', 'advanced-bulk-content-management' ),
            __( 'Object Type', 'advanced-bulk-content-management' ),
            __( 'Object ID', 'advanced-bulk-content-management' ),
            __( 'Object Title', 'advanced-bulk-content-management' ),
            __( 'Date', 'advanced-bulk-content-management' ),
        ) );
        
        // Add logs to CSV
        foreach ( $logs as $log ) {
            $user = get_user_by( 'id', $log->user_id );
            $username = $user ? $user->user_login : __( 'Unknown', 'advanced-bulk-content-management' );
            
            fputcsv( $output, array(
                $log->id,
                $username,
                $log->action,
                $log->object_type,
                $log->object_id,
                $log->object_title,
                $log->date_created,
            ) );
        }
        
        rewind( $output );
        $csv = stream_get_contents( $output );
        fclose( $output );
        
        return $csv;
    }
}


// Updated: 2025-06-20T15:30:00 - Add logging functionality for debugging
