<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Plugin {
	private static $instance;
	private $logger;
	private $auth;
	private $mapper;
	private $calendar;
	private $listener;
	private $admin;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function activate() {
		$settings = get_option( TDL_CCS_OPTION_SETTINGS, array() );
		update_option( TDL_CCS_OPTION_SETTINGS, self::sanitize_settings( $settings ), false );
	}

	public static function default_settings() {
		return array(
			'enabled' => '1',
			'calendar_id' => 'primary',
			'default_duration' => 60,
			'title_template' => 'Chauffeur Booking #{booking_id} - {client_name}',
			'description_template' => self::default_description_template(),
			'email_enabled' => '0',
			'email_to' => get_option( 'admin_email' ),
			'email_cc' => '',
			'email_bcc' => '',
			'email_subject_template' => 'New Chauffeur Booking Synced #{booking_id}',
			'email_body_template' => self::default_email_template(),
			'google_client_id' => '',
			'google_client_secret' => '',
		);
	}

	public static function default_description_template() {
		return "Booking ID: {booking_id}\nClient: {client_name}\nPhone: {client_phone}\nEmail: {client_email}\nPickup: {pickup_datetime}\nPickup location: {pickup_location}\nDrop-off location: {dropoff_location}\nService type: {service_type}\nTransfer type: {transfer_type}\nVehicle: {vehicle_name}\nDriver: {driver_name}\nTotal amount: {total_amount} {currency}\nBooking status: {booking_status}\nAdmin edit link: {admin_edit_url}";
	}

	public static function default_email_template() {
		return '<p>A Chauffeur booking was synced to Google Calendar.</p><p><strong>Booking:</strong> #{booking_id}<br><strong>Client:</strong> {client_name}<br><strong>Pickup:</strong> {pickup_datetime}<br><strong>Route:</strong> {pickup_location} to {dropoff_location}<br><strong>Google event:</strong> <a href="{google_event_link}">{google_event_link}</a></p>';
	}

	public function init() {
		$this->logger = new TDL_CCS_Logger();
		$this->auth = new TDL_CCS_Google_Auth( $this->logger );
		$this->mapper = new TDL_CCS_Booking_Mapper( $this->logger );
		$this->calendar = new TDL_CCS_Calendar( $this->auth, $this->mapper, $this->logger );
		$this->listener = new TDL_CCS_Booking_Listener( $this->calendar, $this->mapper, $this->logger );
		$this->admin = new TDL_CCS_Admin( $this->auth, $this->calendar, $this->mapper, $this->logger );

		$this->listener->init();
		$this->admin->init();
	}

	public static function get_settings() {
		return self::sanitize_settings( get_option( TDL_CCS_OPTION_SETTINGS, array() ) );
	}

	public static function update_settings( array $settings ) {
		update_option( TDL_CCS_OPTION_SETTINGS, self::sanitize_settings( $settings ), false );
	}

	private static function sanitize_settings( $settings ) {
		$settings = is_array( $settings ) ? $settings : array();
		return wp_parse_args( $settings, self::default_settings() );
	}
}
