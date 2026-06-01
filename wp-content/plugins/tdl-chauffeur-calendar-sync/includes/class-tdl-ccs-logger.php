<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Logger {
	const MAX_LOGS = 200;

	public function log( $action, $status, $message, $booking_id = 0, $response_summary = '' ) {
		$logs = get_option( TDL_CCS_OPTION_LOGS, array() );
		if ( ! is_array( $logs ) ) {
			$logs = array();
		}
		$logs[] = array(
			'timestamp' => current_time( 'mysql' ),
			'booking_id' => absint( $booking_id ),
			'action' => sanitize_key( $action ),
			'status' => sanitize_key( $status ),
			'message' => sanitize_text_field( $message ),
			'response_summary' => $this->sanitize_summary( $response_summary ),
		);
		$logs = array_slice( $logs, - self::MAX_LOGS );
		update_option( TDL_CCS_OPTION_LOGS, $logs, false );
	}

	public function get_logs() {
		$logs = get_option( TDL_CCS_OPTION_LOGS, array() );
		return is_array( $logs ) ? array_reverse( $logs ) : array();
	}

	public function clear() {
		delete_option( TDL_CCS_OPTION_LOGS );
	}

	private function sanitize_summary( $summary ) {
		if ( is_array( $summary ) || is_object( $summary ) ) {
			$summary = wp_json_encode( $summary );
		}
		$summary = (string) $summary;
		$summary = preg_replace( '/(access_token|refresh_token|client_secret)([^,}\s]*)/i', '$1_redacted', $summary );
		return sanitize_textarea_field( wp_trim_words( $summary, 60, '...' ) );
	}
}
