<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Calendar {
	private $auth;
	private $mapper;
	private $logger;

	public function __construct( TDL_CCS_Google_Auth $auth, TDL_CCS_Booking_Mapper $mapper, TDL_CCS_Logger $logger ) {
		$this->auth = $auth;
		$this->mapper = $mapper;
		$this->logger = $logger;
	}

	public function sync_booking( $booking_id, $force = false ) {
		$settings = TDL_CCS_Plugin::get_settings();
		if ( '1' !== (string) $settings['enabled'] && ! $force ) { return new WP_Error( 'tdl_ccs_disabled', __( 'Calendar sync is disabled.', 'tdl-chauffeur-calendar-sync' ) ); }
		if ( get_post_meta( $booking_id, TDL_CCS_EVENT_ID_META, true ) && ! $force ) { return true; }
		$data = $this->mapper->map( $booking_id );
		if ( is_wp_error( $data ) ) { return $data; }
		$event = $this->build_event( $data, $settings );
		if ( is_wp_error( $event ) ) {
			$this->logger->log( 'calendar_sync_failed', 'error', $event->get_error_message(), $booking_id );
			return $event;
		}
		$response = $this->create_event( $event, $settings['calendar_id'] );
		if ( is_wp_error( $response ) ) {
			$this->logger->log( 'calendar_sync_failed', 'error', $response->get_error_message(), $booking_id );
			return $response;
		}
		update_post_meta( $booking_id, TDL_CCS_EVENT_ID_META, sanitize_text_field( $response['id'] ) );
		update_post_meta( $booking_id, TDL_CCS_SYNCED_AT_META, current_time( 'mysql' ) );
		update_post_meta( $booking_id, TDL_CCS_SYNC_HASH_META, $this->mapper->sync_hash( $data ) );
		$this->logger->log( 'calendar_sync_success', 'success', 'Google Calendar event created.', $booking_id, array( 'event_id' => $response['id'], 'htmlLink' => $response['htmlLink'] ?? '' ) );
		$this->maybe_send_email( $data, $response );
		return $response;
	}

	public function test_connection() {
		$token = $this->auth->get_access_token();
		if ( is_wp_error( $token ) ) { return $token; }
		$response = wp_remote_get( 'https://www.googleapis.com/calendar/v3/users/me/calendarList', array( 'headers' => array( 'Authorization' => 'Bearer ' . $token ), 'timeout' => 15 ) );
		if ( is_wp_error( $response ) ) { return $response; }
		$code = wp_remote_retrieve_response_code( $response );
		return ( $code >= 200 && $code < 300 ) ? true : new WP_Error( 'tdl_ccs_google_test_failed', __( 'Google Calendar connection test failed.', 'tdl-chauffeur-calendar-sync' ) );
	}

	public function create_test_event() {
		$settings = TDL_CCS_Plugin::get_settings();
		$timezone = wp_timezone_string();
		$start = new DateTimeImmutable( 'now', wp_timezone() );
		$end = $start->modify( '+15 minutes' );
		$event = array(
			'summary' => 'TDL Chauffeur Calendar Sync Test Event',
			'description' => 'This is a test event created from WordPress.',
			'start' => array( 'dateTime' => $start->format( DATE_RFC3339 ), 'timeZone' => $timezone ),
			'end' => array( 'dateTime' => $end->format( DATE_RFC3339 ), 'timeZone' => $timezone ),
		);
		return $this->create_event( $event, $settings['calendar_id'] );
	}

	private function create_event( array $event, $calendar_id ) {
		$token = $this->auth->get_access_token();
		if ( is_wp_error( $token ) ) { return $token; }
		$calendar_id = $calendar_id ? $calendar_id : 'primary';
		$url = 'https://www.googleapis.com/calendar/v3/calendars/' . rawurlencode( $calendar_id ) . '/events';
		$response = wp_remote_post( $url, array( 'timeout' => 20, 'headers' => array( 'Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json' ), 'body' => wp_json_encode( $event ) ) );
		if ( is_wp_error( $response ) ) { return $response; }
		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $code < 200 || $code >= 300 || ! is_array( $body ) || empty( $body['id'] ) ) {
			$message = is_array( $body ) && ! empty( $body['error']['message'] ) ? $body['error']['message'] : __( 'Google Calendar API request failed.', 'tdl-chauffeur-calendar-sync' );
			return new WP_Error( 'tdl_ccs_event_failed', $message );
		}
		return $body;
	}

	private function build_event( array $data, array $settings ) {
		$timezone = wp_timezone_string();
		$start = $this->parse_datetime( $data['pickup_datetime'], $data['pickup_date'], $data['pickup_time'] );
		if ( ! $start ) { return new WP_Error( 'tdl_ccs_no_start', __( 'Booking pickup date/time is incomplete.', 'tdl-chauffeur-calendar-sync' ) ); }
		$return = $this->parse_datetime( $data['return_datetime'] ?? '', $data['return_date'], $data['return_time'] );
		$duration = max( 1, absint( $settings['default_duration'] ) );
		$end = ( $return && $return > $start ) ? $return : $start->modify( '+' . $duration . ' minutes' );
		return array(
			'summary' => $this->replace_tokens( $settings['title_template'], $data ),
			'description' => $this->replace_tokens( $settings['description_template'], $data ),
			'location' => $data['pickup_location'],
			'start' => array( 'dateTime' => $start->format( DATE_RFC3339 ), 'timeZone' => $timezone ),
			'end' => array( 'dateTime' => $end->format( DATE_RFC3339 ), 'timeZone' => $timezone ),
		);
	}

	private function parse_datetime( $datetime, $date, $time ) {
		$value = trim( (string) $datetime );
		if ( '' === $value || '0000-00-00 00:00:00' === $value || '00-00-0000 00:00' === $value ) {
			$value = trim( (string) $date . ' ' . (string) $time );
		}
		if ( '' === trim( $value ) || false !== strpos( $value, '00-00-0000' ) || false !== strpos( $value, '0000-00-00' ) ) { return null; }
		$formats = array( 'Y-m-d H:i:s', 'Y-m-d H:i', 'd-m-Y H:i', 'm/d/Y H:i', 'Y/m/d H:i' );
		foreach ( $formats as $format ) {
			$dt = DateTimeImmutable::createFromFormat( $format, $value, wp_timezone() );
			if ( $dt instanceof DateTimeImmutable ) { return $dt; }
		}
		try { return new DateTimeImmutable( $value, wp_timezone() ); } catch ( Exception $e ) { return null; }
	}

	public function replace_tokens( $template, array $data, array $extra = array() ) {
		$data = array_merge( $data, $extra );
		foreach ( $data as $key => $value ) {
			if ( is_scalar( $value ) ) { $template = str_replace( '{' . $key . '}', (string) $value, $template ); }
		}
		return str_replace( '#{booking_id}', '#' . ( $data['booking_id'] ?? '' ), $template );
	}

	private function maybe_send_email( array $data, array $event ) {
		$settings = TDL_CCS_Plugin::get_settings();
		if ( '1' !== (string) $settings['email_enabled'] || empty( $settings['email_to'] ) ) { return; }
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		foreach ( array( 'email_cc' => 'Cc', 'email_bcc' => 'Bcc' ) as $setting => $label ) {
			$emails = $this->sanitize_email_list( $settings[ $setting ] );
			if ( $emails ) { $headers[] = $label . ': ' . implode( ',', $emails ); }
		}
		$to = $this->sanitize_email_list( $settings['email_to'] );
		$subject = sanitize_text_field( $this->replace_tokens( $settings['email_subject_template'], $data ) );
		$body = wp_kses_post( $this->replace_tokens( $settings['email_body_template'], $data, array( 'google_event_link' => esc_url_raw( $event['htmlLink'] ?? '' ) ) ) );
		$sent = wp_mail( $to, $subject, $body, $headers );
		$this->logger->log( $sent ? 'email_success' : 'email_failed', $sent ? 'success' : 'error', $sent ? 'Sync notification email sent.' : 'Sync notification email failed.', $data['booking_id'] );
	}

	public function send_test_email() {
		$settings = TDL_CCS_Plugin::get_settings();
		$to = $this->sanitize_email_list( $settings['email_to'] );
		if ( ! $to ) { return new WP_Error( 'tdl_ccs_no_email', __( 'No recipient email is configured.', 'tdl-chauffeur-calendar-sync' ) ); }
		return wp_mail( $to, 'TDL Chauffeur Calendar Sync Test Email', '<p>This is a test email from TDL Chauffeur Calendar Sync.</p>', array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

	private function sanitize_email_list( $emails ) {
		$list = preg_split( '/[,;\s]+/', (string) $emails );
		$list = array_filter( array_map( 'sanitize_email', $list ) );
		return array_values( array_filter( $list, 'is_email' ) );
	}
}
