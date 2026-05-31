<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Booking_Listener {
	private $calendar;
	private $mapper;
	private $logger;
	private $queued = array();

	public function __construct( TDL_CCS_Calendar $calendar, TDL_CCS_Booking_Mapper $mapper, TDL_CCS_Logger $logger ) {
		$this->calendar = $calendar;
		$this->mapper = $mapper;
		$this->logger = $logger;
	}

	public function init() {
		add_action( 'save_post', array( $this, 'save_post' ), 20, 3 );
		add_action( 'wp_after_insert_post', array( $this, 'after_insert_post' ), 20, 4 );
		add_action( 'transition_post_status', array( $this, 'transition_post_status' ), 20, 3 );
		add_action( 'chbs_after_booking_sent', array( $this, 'chauffeur_after_booking_sent' ), 20, 1 );
		add_action( 'shutdown', array( $this, 'process_shutdown_queue' ) );
		add_action( 'tdl_ccs_sync_booking_event', array( $this, 'cron_sync_booking' ), 10, 1 );
		add_filter( 'post_row_actions', array( $this, 'booking_row_action' ), 10, 2 );
		add_action( 'admin_post_tdl_ccs_manual_row_sync', array( $this, 'handle_row_sync' ) );
	}

	public function save_post( $post_id, $post, $update ) {
		if ( ! $update ) {
			$this->maybe_queue( $post_id, $post, 'save_post_' . $this->mapper->get_booking_cpt() );
		}
	}

	public function after_insert_post( $post_id, $post, $update, $post_before ) {
		if ( $update ) { return; }
		$this->maybe_queue( $post_id, $post, 'wp_after_insert_post' );
	}

	public function transition_post_status( $new_status, $old_status, $post ) {
		if ( 'publish' === $new_status && 'publish' !== $old_status ) {
			$this->maybe_queue( $post->ID, $post, 'transition_post_status' );
		}
	}

	public function chauffeur_after_booking_sent( $booking_id ) {
		$post = get_post( $booking_id );
		$this->maybe_queue( $booking_id, $post, 'chbs_after_booking_sent' );
	}

	public function process_shutdown_queue() {
		foreach ( array_unique( array_map( 'absint', $this->queued ) ) as $booking_id ) {
			if ( ! get_post_meta( $booking_id, TDL_CCS_EVENT_ID_META, true ) ) {
				wp_schedule_single_event( time() + 30, 'tdl_ccs_sync_booking_event', array( $booking_id ) );
			}
		}
	}

	public function cron_sync_booking( $booking_id ) { $this->calendar->sync_booking( absint( $booking_id ) ); }

	private function maybe_queue( $post_id, $post, $source ) {
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! $post || 'trash' === $post->post_status ) { return; }
		if ( $post->post_type !== $this->mapper->get_booking_cpt() ) { return; }
		if ( get_post_meta( $post_id, TDL_CCS_EVENT_ID_META, true ) ) { return; }
		$this->queued[] = absint( $post_id );
		$this->logger->log( 'booking_detected', 'success', 'Booking detected by ' . sanitize_key( $source ) . '.', $post_id );
	}

	public function booking_row_action( $actions, $post ) {
		if ( ! current_user_can( 'manage_options' ) || $post->post_type !== $this->mapper->get_booking_cpt() ) { return $actions; }
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=tdl_ccs_manual_row_sync&booking_id=' . absint( $post->ID ) ), 'tdl_ccs_row_sync_' . $post->ID );
		$actions['tdl_ccs_sync'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Sync to Google Calendar', 'tdl-chauffeur-calendar-sync' ) . '</a>';
		return $actions;
	}

	public function handle_row_sync() {
		$booking_id = absint( $_GET['booking_id'] ?? 0 );
		if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'tdl_ccs_row_sync_' . $booking_id ) ) {
			wp_die( esc_html__( 'Permission denied.', 'tdl-chauffeur-calendar-sync' ) );
		}
		$this->logger->log( 'manual_sync', 'info', 'Manual row sync requested.', $booking_id );
		$result = $this->calendar->sync_booking( $booking_id, true );
		$redirect = wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=' . $this->mapper->get_booking_cpt() );
		wp_safe_redirect( add_query_arg( 'tdl_ccs_sync', is_wp_error( $result ) ? 'failed' : 'success', $redirect ) );
		exit;
	}
}
