<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Admin {
	private $auth;
	private $calendar;
	private $mapper;
	private $logger;
	private $notices = array();

	public function __construct( TDL_CCS_Google_Auth $auth, TDL_CCS_Calendar $calendar, TDL_CCS_Booking_Mapper $mapper, TDL_CCS_Logger $logger ) {
		$this->auth = $auth;
		$this->calendar = $calendar;
		$this->mapper = $mapper;
		$this->logger = $logger;
	}

	public function init() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function admin_menu() {
		add_options_page( __( 'TDL Chauffeur Calendar', 'tdl-chauffeur-calendar-sync' ), __( 'TDL Chauffeur Calendar', 'tdl-chauffeur-calendar-sync' ), 'manage_options', 'tdl-chauffeur-calendar-sync', array( $this, 'render_page' ) );
	}

	public function enqueue( $hook ) {
		if ( 'settings_page_tdl-chauffeur-calendar-sync' === $hook ) { wp_enqueue_style( 'tdl-ccs-admin', TDL_CCS_URL . 'assets/admin.css', array(), TDL_CCS_VERSION ); }
	}

	public function admin_notices() {
		if ( ! current_user_can( 'manage_options' ) ) { return; }

		$credentials = $this->auth->validate_credentials();
		if ( is_wp_error( $credentials ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $credentials->get_error_message() ) . '</p></div>';
		}

		if ( $this->auth->is_connected() && $this->auth->is_token_expired() ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'TDL Chauffeur Calendar Sync Google access token is expired. The plugin will try to refresh it automatically; reconnect Google if refresh fails.', 'tdl-chauffeur-calendar-sync' ) . '</p></div>';
		}

		$refresh_error = get_transient( 'tdl_ccs_google_refresh_error' );
		if ( $refresh_error ) {
			echo '<div class="notice notice-error"><p>' . esc_html( $refresh_error ) . '</p></div>';
		}

		if ( ! $this->mapper->is_chauffeur_active() ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'TDL Chauffeur Calendar Sync is active, but Chauffeur Booking System does not appear to be active. Sync will wait until bookings are available.', 'tdl-chauffeur-calendar-sync' ) . '</p></div>';
		}

		foreach ( $this->notices as $notice ) {
			echo '<div class="notice notice-' . esc_attr( $notice['type'] ) . ' is-dismissible"><p>' . esc_html( $notice['message'] ) . '</p></div>';
		}
	}

	public function handle_actions() {
		if ( ! current_user_can( 'manage_options' ) || empty( $_GET['page'] ) || 'tdl-chauffeur-calendar-sync' !== $_GET['page'] ) { return; }

		$oauth = $this->auth->handle_oauth_callback();
		if ( true === $oauth ) { $this->notices[] = array( 'type' => 'success', 'message' => __( 'Google account connected.', 'tdl-chauffeur-calendar-sync' ) ); }
		if ( is_wp_error( $oauth ) ) { $this->notices[] = array( 'type' => 'error', 'message' => $oauth->get_error_message() ); }

		if ( empty( $_POST['tdl_ccs_action'] ) ) { return; }
		check_admin_referer( 'tdl_ccs_admin_action', 'tdl_ccs_nonce' );
		$action = sanitize_key( wp_unslash( $_POST['tdl_ccs_action'] ) );

		switch ( $action ) {
			case 'save_google': $this->save_google_settings(); break;
			case 'save_calendar': $this->save_calendar_settings(); break;
			case 'save_email': $this->save_email_settings(); break;
			case 'disconnect': $this->auth->disconnect(); $this->notices[] = array( 'type' => 'success', 'message' => __( 'Google disconnected.', 'tdl-chauffeur-calendar-sync' ) ); break;
			case 'test_connection': $this->notice_from_result( $this->calendar->test_connection(), __( 'Google Calendar connection succeeded.', 'tdl-chauffeur-calendar-sync' ) ); break;
			case 'test_event': $this->notice_from_result( $this->calendar->create_test_event(), __( 'Test calendar event created.', 'tdl-chauffeur-calendar-sync' ) ); break;
			case 'test_email': $this->notice_from_result( $this->calendar->send_test_email(), __( 'Test email sent.', 'tdl-chauffeur-calendar-sync' ) ); break;
			case 'manual_sync': $this->manual_sync(); break;
			case 'clear_logs': $this->logger->clear(); $this->notices[] = array( 'type' => 'success', 'message' => __( 'Logs cleared.', 'tdl-chauffeur-calendar-sync' ) ); break;
		}
	}


	private function save_google_settings() {
		$settings = TDL_CCS_Plugin::get_settings();
		$client_id = trim( wp_unslash( $_POST['google_client_id'] ?? '' ) );
		$secret = trim( wp_unslash( $_POST['google_client_secret'] ?? '' ) );
		$json_credentials = $this->extract_google_oauth_json_credentials( $client_id );
		if ( $json_credentials ) {
			$client_id = $json_credentials['client_id'];
			$secret = $json_credentials['client_secret'];
		}
		$settings['google_client_id'] = sanitize_text_field( $client_id );
		$settings['google_redirect_uri'] = esc_url_raw( trim( wp_unslash( $_POST['google_redirect_uri'] ?? '' ) ) );
		if ( '' !== $secret ) {
			$settings['google_client_secret'] = sanitize_text_field( $secret );
		}
		TDL_CCS_Plugin::update_settings( $settings );
		$this->notices[] = array( 'type' => 'success', 'message' => __( 'Google OAuth settings saved.', 'tdl-chauffeur-calendar-sync' ) );
	}


	private function extract_google_oauth_json_credentials( $value ) {
		$decoded = json_decode( (string) $value, true );
		if ( ! is_array( $decoded ) ) { return false; }
		$source = array();
		if ( ! empty( $decoded['web'] ) && is_array( $decoded['web'] ) ) {
			$source = $decoded['web'];
		} elseif ( ! empty( $decoded['installed'] ) && is_array( $decoded['installed'] ) ) {
			$source = $decoded['installed'];
		}
		if ( empty( $source['client_id'] ) || empty( $source['client_secret'] ) ) { return false; }
		return array(
			'client_id' => (string) $source['client_id'],
			'client_secret' => (string) $source['client_secret'],
		);
	}

	private function save_calendar_settings() {
		$settings = TDL_CCS_Plugin::get_settings();
		$settings['enabled'] = empty( $_POST['enabled'] ) ? '0' : '1';
		$settings['calendar_id'] = sanitize_text_field( wp_unslash( $_POST['calendar_id'] ?? 'primary' ) );
		$settings['default_duration'] = max( 1, absint( $_POST['default_duration'] ?? 60 ) );
		$settings['title_template'] = sanitize_text_field( wp_unslash( $_POST['title_template'] ?? '' ) );
		$settings['description_template'] = sanitize_textarea_field( wp_unslash( $_POST['description_template'] ?? '' ) );
		$settings['timezone'] = TDL_CCS_Plugin::sanitize_timezone( wp_unslash( $_POST['timezone'] ?? '' ) );
		TDL_CCS_Plugin::update_settings( $settings );
		$this->notices[] = array( 'type' => 'success', 'message' => __( 'Calendar settings saved.', 'tdl-chauffeur-calendar-sync' ) );
	}

	private function save_email_settings() {
		$settings = TDL_CCS_Plugin::get_settings();
		$settings['email_enabled'] = empty( $_POST['email_enabled'] ) ? '0' : '1';
		$settings['email_to'] = sanitize_text_field( wp_unslash( $_POST['email_to'] ?? '' ) );
		$settings['email_cc'] = sanitize_text_field( wp_unslash( $_POST['email_cc'] ?? '' ) );
		$settings['email_bcc'] = sanitize_text_field( wp_unslash( $_POST['email_bcc'] ?? '' ) );
		$settings['email_subject_template'] = sanitize_text_field( wp_unslash( $_POST['email_subject_template'] ?? '' ) );
		$settings['email_body_template'] = wp_kses_post( wp_unslash( $_POST['email_body_template'] ?? '' ) );
		TDL_CCS_Plugin::update_settings( $settings );
		$this->notices[] = array( 'type' => 'success', 'message' => __( 'Email settings saved.', 'tdl-chauffeur-calendar-sync' ) );
	}

	private function manual_sync() {
		$booking_id = absint( $_POST['booking_id'] ?? 0 );
		$this->logger->log( 'manual_sync', 'info', 'Manual sync requested from tools tab.', $booking_id );
		$this->notice_from_result( $this->calendar->sync_booking( $booking_id, true ), __( 'Manual sync completed.', 'tdl-chauffeur-calendar-sync' ) );
	}

	private function notice_from_result( $result, $success ) {
		if ( is_wp_error( $result ) ) { $this->notices[] = array( 'type' => 'error', 'message' => $result->get_error_message() ); }
		else { $this->notices[] = array( 'type' => 'success', 'message' => $success ); }
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) { return; }
		$tab = sanitize_key( $_GET['tab'] ?? 'google' );
		$tabs = array( 'google' => __( 'Google Connection', 'tdl-chauffeur-calendar-sync' ), 'calendar' => __( 'Calendar Settings', 'tdl-chauffeur-calendar-sync' ), 'email' => __( 'Email Notification', 'tdl-chauffeur-calendar-sync' ), 'logs' => __( 'Logs', 'tdl-chauffeur-calendar-sync' ), 'tools' => __( 'Test Tools', 'tdl-chauffeur-calendar-sync' ) );
		echo '<div class="wrap tdl-ccs"><h1>' . esc_html__( 'TDL Chauffeur Calendar Sync', 'tdl-chauffeur-calendar-sync' ) . '</h1><nav class="nav-tab-wrapper">';
		foreach ( $tabs as $key => $label ) {
			echo '<a class="nav-tab ' . esc_attr( $tab === $key ? 'nav-tab-active' : '' ) . '" href="' . esc_url( admin_url( 'options-general.php?page=tdl-chauffeur-calendar-sync&tab=' . $key ) ) . '">' . esc_html( $label ) . '</a>';
		}
		echo '</nav>';
		if ( 'google' === $tab ) { $this->render_google_tab(); }
		elseif ( 'calendar' === $tab ) { $this->render_calendar_tab(); }
		elseif ( 'email' === $tab ) { $this->render_email_tab(); }
		elseif ( 'logs' === $tab ) { $this->render_logs_tab(); }
		else { $this->render_tools_tab(); }
		echo '</div>';
	}

	private function form_open( $action ) { echo '<form method="post">'; wp_nonce_field( 'tdl_ccs_admin_action', 'tdl_ccs_nonce' ); echo '<input type="hidden" name="tdl_ccs_action" value="' . esc_attr( $action ) . '">'; }
	private function submit_button( $label, $type = 'primary' ) { submit_button( $label, $type ); echo '</form>'; }

	private function render_google_tab() {
		$settings = TDL_CCS_Plugin::get_settings();
		$auth_url = $this->auth->get_auth_url();
		$status = $this->auth->is_connected() ? __( 'Connected', 'tdl-chauffeur-calendar-sync' ) : __( 'Not connected', 'tdl-chauffeur-calendar-sync' );
		$email = $this->auth->get_account_email();
		$login_label = ( $this->auth->is_connected() && $this->auth->is_token_expired() ) ? __( 'Reconnect Google', 'tdl-chauffeur-calendar-sync' ) : __( 'Login with Google', 'tdl-chauffeur-calendar-sync' );
		$redirect_uri = $this->auth->get_redirect_uri();
		$default_redirect_uri = $this->auth->get_default_redirect_uri();

		$this->form_open( 'save_google' );
		echo '<table class="form-table">';
		echo '<tr><th>' . esc_html__( 'Google Client ID', 'tdl-chauffeur-calendar-sync' ) . '</th><td><input class="regular-text" name="google_client_id" value="' . esc_attr( $settings['google_client_id'] ) . '"><p class="description">' . esc_html__( 'Paste the OAuth 2.0 Web application Client ID from Google Cloud Console. It should end with .apps.googleusercontent.com. Do not paste an API key, Project ID, email address, or Client Secret here.', 'tdl-chauffeur-calendar-sync' ) . '</p></td></tr>';
		echo '<tr><th>' . esc_html__( 'Google Client Secret', 'tdl-chauffeur-calendar-sync' ) . '</th><td><input class="regular-text" type="password" name="google_client_secret" value="" placeholder="' . esc_attr__( 'Leave blank to keep existing secret', 'tdl-chauffeur-calendar-sync' ) . '"><p class="description">' . esc_html__( 'Paste the matching OAuth 2.0 Client Secret from the same Web application client. The saved secret is never printed back into the page.', 'tdl-chauffeur-calendar-sync' ) . '</p></td></tr>';
		echo '<tr><th>' . esc_html__( 'Redirect URI', 'tdl-chauffeur-calendar-sync' ) . '</th><td><input class="large-text code" type="url" name="google_redirect_uri" value="' . esc_attr( $redirect_uri ) . '"><p class="description">' . esc_html__( 'Copy this exact URI into Google Cloud Console as an Authorized redirect URI. The plugin always includes tdl_ccs_google_callback=1 in the OAuth request; if Google error details show this parameter missing, save this full URI again. Generated default:', 'tdl-chauffeur-calendar-sync' ) . ' <code>' . esc_html( $default_redirect_uri ) . '</code></p></td></tr>';
		echo '</table>';
		echo '<div class="tdl-ccs-guide"><h2>' . esc_html__( 'How to get Google Client ID and Client Secret', 'tdl-chauffeur-calendar-sync' ) . '</h2>';
		echo '<ol><li>' . esc_html__( 'Go to Google Cloud Console: https://console.cloud.google.com/', 'tdl-chauffeur-calendar-sync' ) . '</li><li>' . esc_html__( 'Create or select a project.', 'tdl-chauffeur-calendar-sync' ) . '</li><li>' . esc_html__( 'Enable the Google Calendar API for that project.', 'tdl-chauffeur-calendar-sync' ) . '</li><li>' . esc_html__( 'Open APIs & Services > OAuth consent screen and configure/publish the consent screen.', 'tdl-chauffeur-calendar-sync' ) . '</li><li>' . esc_html__( 'Open APIs & Services > Credentials > Create Credentials > OAuth client ID.', 'tdl-chauffeur-calendar-sync' ) . '</li><li>' . esc_html__( 'Choose Web application, add the exact Redirect URI shown above as an Authorized redirect URI, then copy the Client ID and Client Secret from that same OAuth client into these fields. Error 401 invalid_client means the Client ID is wrong, deleted, belongs to another project, or is not an OAuth 2.0 Web application Client ID. Error 400 redirect_uri_mismatch means the redirect URI in this plugin and Google Cloud Console do not match character-for-character; make sure the URI includes tdl_ccs_google_callback=1.', 'tdl-chauffeur-calendar-sync' ) . '</li></ol></div>';
		$this->submit_button( __( 'Save Google Settings', 'tdl-chauffeur-calendar-sync' ) );

		echo '<table class="form-table">';
		echo '<tr><th>' . esc_html__( 'Status', 'tdl-chauffeur-calendar-sync' ) . '</th><td>' . esc_html( $status ) . '</td></tr>';
		echo '<tr><th>' . esc_html__( 'Connected Google Account', 'tdl-chauffeur-calendar-sync' ) . '</th><td>' . ( $email ? esc_html( $email ) : '&mdash;' ) . '</td></tr>';
		echo '</table>';

		if ( is_wp_error( $auth_url ) ) {
			echo '<p><button type="button" class="button button-primary" disabled>' . esc_html( $login_label ) . '</button></p>';
			echo '<p class="description">' . esc_html( $auth_url->get_error_message() ) . '</p>';
		} else {
			echo '<p><a class="button button-primary" href="' . esc_url( $auth_url ) . '">' . esc_html( $login_label ) . '</a></p>';
		}

		$this->form_open( 'disconnect' ); $this->submit_button( __( 'Disconnect Google', 'tdl-chauffeur-calendar-sync' ), 'secondary' );
		$this->form_open( 'test_connection' ); $this->submit_button( __( 'Test Calendar Connection', 'tdl-chauffeur-calendar-sync' ), 'secondary' );
	}

	private function render_calendar_tab() {
		$s = TDL_CCS_Plugin::get_settings(); $this->form_open( 'save_calendar' );
		echo '<table class="form-table"><tr><th>Enable calendar sync</th><td><label><input type="checkbox" name="enabled" value="1" ' . checked( $s['enabled'], '1', false ) . '> Enable</label></td></tr>';
		echo '<tr><th>Calendar ID</th><td><input class="regular-text" name="calendar_id" value="' . esc_attr( $s['calendar_id'] ) . '"><p class="description">' . esc_html__( 'Default is primary. Only change this if you want to sync to a specific Google Calendar.', 'tdl-chauffeur-calendar-sync' ) . '</p></td></tr>';
		echo '<tr><th>Default event duration</th><td><input type="number" min="1" name="default_duration" value="' . esc_attr( $s['default_duration'] ) . '"> minutes</td></tr>';
		echo '<tr><th>Event title template</th><td><input class="large-text" name="title_template" value="' . esc_attr( $s['title_template'] ) . '"></td></tr>';
		echo '<tr><th>Event description template</th><td><textarea class="large-text" rows="10" name="description_template">' . esc_textarea( $s['description_template'] ) . '</textarea></td></tr>';
		echo '<tr><th>Event timezone</th><td><select name="timezone">';
		foreach ( timezone_identifiers_list() as $timezone ) {
			echo '<option value="' . esc_attr( $timezone ) . '" ' . selected( $s['timezone'], $timezone, false ) . '>' . esc_html( $timezone ) . '</option>';
		}
		echo '</select><p class="description">' . esc_html__( 'Use Europe/Lisbon for Portugal bookings. This keeps the Chauffeur pickup time as the visible Google Calendar time.', 'tdl-chauffeur-calendar-sync' ) . '</p></td></tr></table>';
		$this->submit_button( __( 'Save Calendar Settings', 'tdl-chauffeur-calendar-sync' ) );
	}

	private function render_email_tab() {
		$s = TDL_CCS_Plugin::get_settings(); $this->form_open( 'save_email' );
		echo '<table class="form-table"><tr><th>Enable email notification</th><td><label><input type="checkbox" name="email_enabled" value="1" ' . checked( $s['email_enabled'], '1', false ) . '> Enable</label></td></tr>';
		foreach ( array( 'email_to' => 'Recipient emails', 'email_cc' => 'CC', 'email_bcc' => 'BCC', 'email_subject_template' => 'Subject template' ) as $key => $label ) { echo '<tr><th>' . esc_html( $label ) . '</th><td><input class="large-text" name="' . esc_attr( $key ) . '" value="' . esc_attr( $s[ $key ] ) . '"></td></tr>'; }
		echo '<tr><th>HTML body template</th><td><textarea class="large-text" rows="8" name="email_body_template">' . esc_textarea( $s['email_body_template'] ) . '</textarea></td></tr></table>';
		$this->submit_button( __( 'Save Email Settings', 'tdl-chauffeur-calendar-sync' ) );
		$this->form_open( 'test_email' ); $this->submit_button( __( 'Send Test Email', 'tdl-chauffeur-calendar-sync' ), 'secondary' );
	}

	private function render_logs_tab() {
		$this->form_open( 'clear_logs' ); submit_button( __( 'Clear Logs', 'tdl-chauffeur-calendar-sync' ), 'secondary' ); echo '</form><table class="widefat striped"><thead><tr><th>Timestamp</th><th>Booking ID</th><th>Action</th><th>Status</th><th>Message</th><th>Response summary</th></tr></thead><tbody>';
		foreach ( $this->logger->get_logs() as $log ) { echo '<tr><td>' . esc_html( $log['timestamp'] ) . '</td><td>' . esc_html( $log['booking_id'] ) . '</td><td>' . esc_html( $log['action'] ) . '</td><td>' . esc_html( $log['status'] ) . '</td><td>' . esc_html( $log['message'] ) . '</td><td>' . esc_html( $log['response_summary'] ) . '</td></tr>'; }
		echo '</tbody></table>';
	}

	private function render_tools_tab() {
		foreach ( array( 'test_connection' => 'Test Google Connection', 'test_event' => 'Create Test Calendar Event' ) as $action => $label ) { $this->form_open( $action ); submit_button( $label, 'secondary' ); echo '</form>'; }
		$this->form_open( 'manual_sync' ); echo '<h2>' . esc_html__( 'Manual sync by Booking ID', 'tdl-chauffeur-calendar-sync' ) . '</h2><p><input type="number" min="1" name="booking_id" placeholder="Booking ID"> '; submit_button( __( 'Sync / Resync Booking', 'tdl-chauffeur-calendar-sync' ), 'primary', 'submit', false ); echo '</p></form>';
	}
}
