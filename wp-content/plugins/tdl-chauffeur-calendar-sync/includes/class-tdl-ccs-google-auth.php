<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Google_Auth {
	private $logger;
	private $scopes = array( 'https://www.googleapis.com/auth/calendar.events', 'openid', 'email', 'profile' );

	public function __construct( TDL_CCS_Logger $logger ) { $this->logger = $logger; }

	public function has_credentials() {
		return ! is_wp_error( $this->validate_credentials() );
	}

	public function validate_credentials() {
		$client_id = trim( $this->get_client_id() );
		$client_secret = trim( $this->get_client_secret() );

		if ( '' === $client_id || '' === $client_secret ) {
			return new WP_Error( 'tdl_ccs_missing_credentials', $this->get_missing_credentials_message() );
		}

		if ( ! preg_match( '/^[A-Za-z0-9._-]+\.apps\.googleusercontent\.com$/', $client_id ) ) {
			return new WP_Error( 'tdl_ccs_invalid_client_id', __( 'Google Client ID looks invalid. Use the OAuth 2.0 Web application Client ID from Google Cloud Console; it usually ends with .apps.googleusercontent.com. Do not use an API key, Project ID, email address, or Client Secret in this field.', 'tdl-chauffeur-calendar-sync' ) );
		}

		return true;
	}

	public function get_missing_credentials_message() {
		return __( 'Google OAuth credentials are not configured. Enter Google Client ID and Google Client Secret in the Google Connection tab, or define TDL_CCS_GOOGLE_CLIENT_ID and TDL_CCS_GOOGLE_CLIENT_SECRET in wp-config.php.', 'tdl-chauffeur-calendar-sync' );
	}

	private function get_client_id() {
		$settings = TDL_CCS_Plugin::get_settings();
		if ( ! empty( $settings['google_client_id'] ) ) {
			return (string) $settings['google_client_id'];
		}
		return defined( 'TDL_CCS_GOOGLE_CLIENT_ID' ) ? (string) TDL_CCS_GOOGLE_CLIENT_ID : '';
	}

	private function get_client_secret() {
		$settings = TDL_CCS_Plugin::get_settings();
		if ( ! empty( $settings['google_client_secret'] ) ) {
			return (string) $settings['google_client_secret'];
		}
		return defined( 'TDL_CCS_GOOGLE_CLIENT_SECRET' ) ? (string) TDL_CCS_GOOGLE_CLIENT_SECRET : '';
	}

	public function get_default_redirect_uri() {
		return add_query_arg(
			array(
				'page' => 'tdl-chauffeur-calendar-sync',
				'tdl_ccs_google_callback' => '1',
			),
			admin_url( 'admin.php' )
		);
	}

	public function get_redirect_uri() {
		$settings = TDL_CCS_Plugin::get_settings();
		$redirect_uri = trim( (string) ( $settings['google_redirect_uri'] ?? '' ) );
		$redirect_uri = '' !== $redirect_uri ? $redirect_uri : $this->get_default_redirect_uri();
		return $this->ensure_callback_arg( $redirect_uri );
	}

	private function ensure_callback_arg( $redirect_uri ) {
		return add_query_arg( 'tdl_ccs_google_callback', '1', $redirect_uri );
	}

	public function get_auth_url() {
		$credentials = $this->validate_credentials();
		if ( is_wp_error( $credentials ) ) {
			return $credentials;
		}

		$state = wp_create_nonce( 'tdl_ccs_google_oauth' );
		return add_query_arg(
			array(
				'client_id' => $this->get_client_id(),
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
		if ( empty( $_GET['tdl_ccs_google_callback'] ) ) { return null; }

		$credentials = $this->validate_credentials();
		if ( is_wp_error( $credentials ) ) {
			$this->logger->log( 'google_auth_failed', 'error', $credentials->get_error_message() );
			return $credentials;
		}

		if ( empty( $_GET['code'] ) || empty( $_GET['state'] ) ) {
			$this->logger->log( 'google_auth_failed', 'error', 'Google OAuth callback did not include a code or state.' );
			return new WP_Error( 'tdl_ccs_missing_oauth_params', __( 'Google OAuth callback was incomplete.', 'tdl-chauffeur-calendar-sync' ) );
		}

		if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['state'] ) ), 'tdl_ccs_google_oauth' ) ) {
			$this->logger->log( 'google_auth_failed', 'error', 'Invalid Google OAuth state.' );
			return new WP_Error( 'tdl_ccs_bad_state', __( 'Invalid Google OAuth state.', 'tdl-chauffeur-calendar-sync' ) );
		}

		$response = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'timeout' => 15,
				'body' => array(
					'code' => sanitize_text_field( wp_unslash( $_GET['code'] ) ),
					'client_id' => $this->get_client_id(),
					'client_secret' => $this->get_client_secret(),
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

	public function is_token_expired() {
		$tokens = $this->get_tokens();
		return ! empty( $tokens['expires_at'] ) && (int) $tokens['expires_at'] <= time() + 60;
	}

	public function get_account_email() {
		$tokens = $this->get_tokens();
		return empty( $tokens['account_email'] ) ? '' : sanitize_email( $tokens['account_email'] );
	}

	public function get_access_token() {
		$tokens = $this->get_tokens();
		if ( empty( $tokens['access_token'] ) ) { return new WP_Error( 'tdl_ccs_no_token', __( 'Google is not connected.', 'tdl-chauffeur-calendar-sync' ) ); }
		if ( $this->is_token_expired() ) {
			return $this->refresh_access_token();
		}
		return $tokens['access_token'];
	}

	private function refresh_access_token() {
		$tokens = $this->get_tokens();
		$credentials = $this->validate_credentials();
		if ( is_wp_error( $credentials ) ) {
			$this->logger->log( 'token_refresh_failed', 'error', $credentials->get_error_message() );
			set_transient( 'tdl_ccs_google_refresh_error', $credentials->get_error_message(), HOUR_IN_SECONDS );
			return $credentials;
		}
		if ( empty( $tokens['refresh_token'] ) ) {
			$message = __( 'Google refresh token is missing. Reconnect Google.', 'tdl-chauffeur-calendar-sync' );
			$this->logger->log( 'token_refresh_failed', 'error', 'Google refresh token is missing.' );
			set_transient( 'tdl_ccs_google_refresh_error', $message, HOUR_IN_SECONDS );
			return new WP_Error( 'tdl_ccs_no_refresh', $message );
		}

		$response = wp_remote_post(
			'https://oauth2.googleapis.com/token',
			array(
				'timeout' => 15,
				'body' => array(
					'client_id' => $this->get_client_id(),
					'client_secret' => $this->get_client_secret(),
					'refresh_token' => $tokens['refresh_token'],
					'grant_type' => 'refresh_token',
				),
			)
		);

		$result = $this->store_token_response( $response, true );
		if ( is_wp_error( $result ) ) {
			$this->logger->log( 'token_refresh_failed', 'error', $result->get_error_message() );
			set_transient( 'tdl_ccs_google_refresh_error', $result->get_error_message(), HOUR_IN_SECONDS );
			return $result;
		}

		delete_transient( 'tdl_ccs_google_refresh_error' );
		$this->logger->log( 'token_refresh_success', 'success', 'Google access token refreshed.' );
		$tokens = $this->get_tokens();
		return $tokens['access_token'];
	}

	private function store_token_response( $response, $preserve_refresh = false ) {
		if ( is_wp_error( $response ) ) { return $response; }
		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( $code < 200 || $code >= 300 || ! is_array( $body ) || empty( $body['access_token'] ) ) {
			$message = is_array( $body ) && ! empty( $body['error_description'] ) ? sanitize_text_field( $body['error_description'] ) : __( 'Google token exchange failed.', 'tdl-chauffeur-calendar-sync' );
			return new WP_Error( 'tdl_ccs_token_failed', $message );
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
		$tokens = $this->get_tokens();
		if ( empty( $tokens['access_token'] ) ) { return; }
		$response = wp_remote_get( 'https://www.googleapis.com/oauth2/v2/userinfo', array( 'headers' => array( 'Authorization' => 'Bearer ' . $tokens['access_token'] ), 'timeout' => 15 ) );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( is_array( $body ) && ! empty( $body['email'] ) ) {
			$tokens['account_email'] = sanitize_email( $body['email'] );
			update_option( TDL_CCS_OPTION_TOKENS, $tokens, false );
		}
	}
}
