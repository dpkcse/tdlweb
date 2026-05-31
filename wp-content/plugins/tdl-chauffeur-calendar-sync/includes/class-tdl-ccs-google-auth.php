<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Google_Auth {
	private $logger;
	private $scopes = array( 'https://www.googleapis.com/auth/calendar.events', 'openid', 'email', 'profile' );

	public function __construct( TDL_CCS_Logger $logger ) { $this->logger = $logger; }

	public function get_redirect_uri() {
		return admin_url( 'admin.php?page=tdl-chauffeur-calendar-sync' );
	}

	public function get_auth_url() {
		$settings = TDL_CCS_Plugin::get_settings();
		$state = wp_create_nonce( 'tdl_ccs_google_oauth' );
		return add_query_arg(
			array(
				'client_id' => $settings['google_client_id'],
				'redirect_uri' => $this->get_redirect_uri(),
				'response_type' => 'code',
				'scope' => implode( ' ', $this->scopes ),
				'access_type' => 'offline',
				'prompt' => 'consent',
				'state' => $state,
			),
			'https://accounts.google.com/o/oauth2/v2/auth'
		);
	}

	public function handle_oauth_callback() {
		if ( empty( $_GET['code'] ) || empty( $_GET['state'] ) ) { return null; }
		if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['state'] ) ), 'tdl_ccs_google_oauth' ) ) {
			$this->logger->log( 'google_auth_failed', 'error', 'Invalid Google OAuth state.' );
			return new WP_Error( 'tdl_ccs_bad_state', __( 'Invalid Google OAuth state.', 'tdl-chauffeur-calendar-sync' ) );
		}
		$settings = TDL_CCS_Plugin::get_settings();
		$response = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'timeout' => 15,
				'body' => array(
					'code' => sanitize_text_field( wp_unslash( $_GET['code'] ) ),
					'client_id' => $settings['google_client_id'],
					'client_secret' => $settings['google_client_secret'],
					'redirect_uri' => $this->get_redirect_uri(),
					'grant_type' => 'authorization_code',
				),
			)
		);
		$result = $this->store_token_response( $response );
		if ( is_wp_error( $result ) ) {
			$this->logger->log( 'google_auth_failed', 'error', $result->get_error_message() );
			return $result;
		}
		$this->logger->log( 'google_auth_success', 'success', 'Google account connected.' );
		return true;
	}

	public function disconnect() { delete_option( TDL_CCS_OPTION_TOKENS ); }

	public function get_tokens() {
		$tokens = get_option( TDL_CCS_OPTION_TOKENS, array() );
		return is_array( $tokens ) ? $tokens : array();
	}

	public function is_connected() {
		$tokens = $this->get_tokens();
		return ! empty( $tokens['access_token'] ) || ! empty( $tokens['refresh_token'] );
	}

	public function get_account_email() {
		$tokens = $this->get_tokens();
		return empty( $tokens['account_email'] ) ? '' : sanitize_email( $tokens['account_email'] );
	}

	public function get_access_token() {
		$tokens = $this->get_tokens();
		if ( empty( $tokens['access_token'] ) ) { return new WP_Error( 'tdl_ccs_no_token', __( 'Google is not connected.', 'tdl-chauffeur-calendar-sync' ) ); }
		if ( ! empty( $tokens['expires_at'] ) && (int) $tokens['expires_at'] <= time() + 60 ) {
			return $this->refresh_access_token();
		}
		return $tokens['access_token'];
	}

	private function refresh_access_token() {
		$tokens = $this->get_tokens();
		$settings = TDL_CCS_Plugin::get_settings();
		if ( empty( $tokens['refresh_token'] ) ) { return new WP_Error( 'tdl_ccs_no_refresh', __( 'Google refresh token is missing. Reconnect Google.', 'tdl-chauffeur-calendar-sync' ) ); }
		$response = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'timeout' => 15,
				'body' => array(
					'client_id' => $settings['google_client_id'],
					'client_secret' => $settings['google_client_secret'],
					'refresh_token' => $tokens['refresh_token'],
					'grant_type' => 'refresh_token',
				),
			)
		);
		$result = $this->store_token_response( $response, true );
		return is_wp_error( $result ) ? $result : $this->get_tokens()['access_token'];
	}

	private function store_token_response( $response, $preserve_refresh = false ) {
		if ( is_wp_error( $response ) ) { return $response; }
		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $code < 200 || $code >= 300 || ! is_array( $body ) || empty( $body['access_token'] ) ) {
			return new WP_Error( 'tdl_ccs_token_failed', __( 'Google token exchange failed.', 'tdl-chauffeur-calendar-sync' ) );
		}
		$old = $this->get_tokens();
		$tokens = array(
			'access_token' => sanitize_text_field( $body['access_token'] ),
			'refresh_token' => ! empty( $body['refresh_token'] ) ? sanitize_text_field( $body['refresh_token'] ) : ( $preserve_refresh && ! empty( $old['refresh_token'] ) ? $old['refresh_token'] : '' ),
			'expires_at' => time() + absint( $body['expires_in'] ?? 3600 ),
			'account_email' => $old['account_email'] ?? '',
		);
		update_option( TDL_CCS_OPTION_TOKENS, $tokens, false );
		$this->hydrate_account_email();
		return true;
	}

	public function hydrate_account_email() {
		$token = $this->get_access_token();
		if ( is_wp_error( $token ) ) { return; }
		$response = wp_remote_get( 'https://www.googleapis.com/oauth2/v2/userinfo', array( 'headers' => array( 'Authorization' => 'Bearer ' . $token ), 'timeout' => 15 ) );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $body ) && ! empty( $body['email'] ) ) {
			$tokens = $this->get_tokens();
			$tokens['account_email'] = sanitize_email( $body['email'] );
			update_option( TDL_CCS_OPTION_TOKENS, $tokens, false );
		}
	}
}
