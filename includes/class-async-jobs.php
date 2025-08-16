<?php
/**
 * Async Jobs Class
 *
 * @package Advanced_Bulk_Content_Management
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Async Jobs Class
 *
 * Handles asynchronous processing of bulk actions for large jobs.
 *
 * @since 1.0.0
 */
class ABCM_Async_Jobs {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Add action for cron hook
        add_action( 'abcm_process_async_job', array( $this, 'process_job' ) );
        
        // Register activation hook to schedule event
        register_activation_hook( ABCM_PLUGIN_BASENAME, array( $this, 'schedule_event' ) );
        
        // Register deactivation hook to unschedule event
        register_deactivation_hook( ABCM_PLUGIN_BASENAME, array( $this, 'unschedule_event' ) );
    }

    /**
     * Schedule event
     */
    public function schedule_event() {
        if ( ! wp_next_scheduled( 'abcm_process_async_job' ) ) {
            wp_schedule_event( time(), 'hourly', 'abcm_process_async_job' );
        }
    }

    /**
     * Unschedule event
     */
    public function unschedule_event() {
        $timestamp = wp_next_scheduled( 'abcm_process_async_job' );
        
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'abcm_process_async_job' );
        }
    }

    /**
     * Schedule a new job
     *
     * @param string $action    The action to perform.
     * @param array  $post_ids  The post IDs to process.
     * @param array  $args      Additional arguments.
     * @return int|bool The job ID, or false on failure.
     */
    public function schedule_job( $action, $post_ids, $args = array() ) {
        $job = array(
            'id'        => uniqid( 'abcm_job_' ),
            'action'    => $action,
            'post_ids'  => $post_ids,
            'args'      => $args,
            'status'    => 'pending',
            'created'   => time(),
            'processed' => 0,
            'total'     => count( $post_ids ),
        );
        
        $jobs = get_option( 'abcm_async_jobs', array() );
        $jobs[ $job['id'] ] = $job;
        
        update_option( 'abcm_async_jobs', $jobs );
        
        return $job['id'];
    }

    /**
     * Process job
     */
    public function process_job() {
        $jobs = get_option( 'abcm_async_jobs', array() );
        
        if ( empty( $jobs ) ) {
            return;
        }
        
        foreach ( $jobs as $job_id => $job ) {
            if ( 'pending' !== $job['status'] && 'processing' !== $job['status'] ) {
                continue;
            }
            
            // Update job status to processing
            $jobs[ $job_id ]['status'] = 'processing';
            update_option( 'abcm_async_jobs', $jobs );
            
            // Process batch of posts (10 at a time)
            $batch = array_slice( $job['post_ids'], $job['processed'], 10 );
            
            if ( empty( $batch ) ) {
                // Job completed
                $jobs[ $job_id ]['status'] = 'completed';
                update_option( 'abcm_async_jobs', $jobs );
                continue;
            }
            
            $logger = new ABCM_Logger();
            $processed = 0;
            
            foreach ( $batch as $post_id ) {
                $post = get_post( $post_id );
                $post_type = get_post_type( $post_id );
                
                if ( ! $post ) {
                    continue;
                }
                
                switch ( $job['action'] ) {
                    case 'trash':
                        if ( wp_trash_post( $post_id ) ) {
                            $processed++;
                            $logger->log( 'trash', $post_type, $post_id, $post->post_title );
                        }
                        break;
                        
                    case 'publish':
                        $result = wp_update_post( array(
                            'ID'          => $post_id,
                            'post_status' => 'publish',
                        ) );
                        
                        if ( $result ) {
                            $processed++;
                            $logger->log( 'publish', $post_type, $post_id, $post->post_title );
                        }
                        break;
                        
                    case 'draft':
                        $result = wp_update_post( array(
                            'ID'          => $post_id,
                            'post_status' => 'draft',
                        ) );
                        
                        if ( $result ) {
                            $processed++;
                            $logger->log( 'draft', $post_type, $post_id, $post->post_title );
                        }
                        break;
                }
            }
            
            // Update job processed count
            $jobs[ $job_id ]['processed'] += $processed;
            
            // If all posts processed, update job status to completed
            if ( $jobs[ $job_id ]['processed'] >= $job['total'] ) {
                $jobs[ $job_id ]['status'] = 'completed';
            }
            
            update_option( 'abcm_async_jobs', $jobs );
            
            // Only process one job per cron run
            break;
        }
    }

    /**
     * Get job status
     *
     * @param string $job_id The job ID.
     * @return array|bool The job data, or false if not found.
     */
    public function get_job_status( $job_id ) {
        $jobs = get_option( 'abcm_async_jobs', array() );
        
        return isset( $jobs[ $job_id ] ) ? $jobs[ $job_id ] : false;
    }

    /**
     * Get all jobs
     *
     * @return array Array of jobs.
     */
    public function get_jobs() {
        return get_option( 'abcm_async_jobs', array() );
    }
}


// Updated: 2025-06-13T16:30:00 - Add asynchronous job processing framework
