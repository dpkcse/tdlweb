<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TDL_CCS_Booking_Mapper {
	private $logger;

	public function __construct( TDL_CCS_Logger $logger ) { $this->logger = $logger; }

	public function get_booking_cpt() {
		if ( class_exists( 'CHBSBooking' ) && method_exists( 'CHBSBooking', 'getCPTName' ) ) {
			$cpt = call_user_func( array( 'CHBSBooking', 'getCPTName' ) );
			if ( is_string( $cpt ) && '' !== $cpt ) { return $cpt; }
		}
		return 'chbs_booking';
	}

	public function is_chauffeur_active() {
		return class_exists( 'CHBSBooking' ) || post_type_exists( $this->get_booking_cpt() );
	}

	public function map( $booking_id ) {
		$post = get_post( $booking_id );
		if ( ! $post || $post->post_type !== $this->get_booking_cpt() ) {
			return new WP_Error( 'tdl_ccs_not_booking', __( 'Booking was not found or is not a Chauffeur booking.', 'tdl-chauffeur-calendar-sync' ) );
		}

		$booking = $this->get_chbs_booking( $booking_id );
		$meta = $booking && ! empty( $booking['meta'] ) && is_array( $booking['meta'] ) ? $booking['meta'] : $this->get_prefixed_meta( $booking_id );
		$billing = $this->get_billing( $booking_id );
		$coordinates = $this->get_value( $meta, 'coordinate', array() );
		$pickup_location = $this->coordinate_address( is_array( $coordinates ) && isset( $coordinates[0] ) ? $coordinates[0] : array() );
		$dropoff_location = '';
		if ( is_array( $coordinates ) && count( $coordinates ) > 1 ) {
			$dropoff_location = $this->coordinate_address( $coordinates[ count( $coordinates ) - 1 ] );
		}

		$first = $this->get_value( $meta, 'client_contact_detail_first_name' );
		$last = $this->get_value( $meta, 'client_contact_detail_last_name' );
		$data = array(
			'booking_id' => absint( $booking_id ),
			'booking_title' => get_the_title( $booking_id ),
			'booking_status' => $this->get_value( $booking, 'booking_status_name', $this->get_value( $meta, 'booking_status_id' ) ),
			'created_at' => $post->post_date,
			'pickup_date' => $this->get_value( $meta, 'pickup_date' ),
			'pickup_time' => $this->get_value( $meta, 'pickup_time' ),
			'pickup_datetime' => $this->get_value( $meta, 'pickup_datetime' ),
			'return_date' => $this->get_value( $meta, 'return_date' ),
			'return_time' => $this->get_value( $meta, 'return_time' ),
			'return_datetime' => $this->get_value( $meta, 'return_datetime' ),
			'client_name' => trim( $first . ' ' . $last ),
			'client_first_name' => $first,
			'client_last_name' => $last,
			'client_email' => $this->get_value( $meta, 'client_contact_detail_email_address' ),
			'client_phone' => $this->get_value( $meta, 'client_contact_detail_phone_number' ),
			'pickup_location' => $pickup_location,
			'dropoff_location' => $dropoff_location,
			'service_type' => $this->get_value( $booking, 'service_type_name', $this->get_value( $meta, 'service_type_id' ) ),
			'transfer_type' => $this->get_value( $booking, 'transfer_type_name', $this->get_value( $meta, 'transfer_type_id' ) ),
			'vehicle_name' => $this->get_value( $meta, 'vehicle_name' ),
			'driver_name' => $this->get_value( $booking, 'driver_full_name', $this->driver_name_from_id( $this->get_value( $meta, 'driver_id' ) ) ),
			'payment_method' => $this->get_value( $booking, 'payment_name', $this->get_value( $meta, 'payment_name' ) ),
			'total_amount' => $this->get_total_amount( $billing ),
			'currency' => $this->get_value( $meta, 'currency_id' ),
			'admin_edit_url' => get_edit_post_link( $booking_id, '' ),
		);
		if ( '' === $data['client_name'] ) { $data['client_name'] = $data['client_email']; }
		$this->logger->log( 'booking_mapped', 'success', 'Booking mapped for Google Calendar.', $booking_id );
		return $data;
	}

	public function sync_hash( array $data ) { return wp_hash( wp_json_encode( $data ) ); }

	private function get_chbs_booking( $booking_id ) {
		if ( class_exists( 'CHBSBooking' ) && method_exists( 'CHBSBooking', 'getBooking' ) ) {
			$booking = ( new CHBSBooking() )->getBooking( $booking_id );
			return is_array( $booking ) ? $booking : array();
		}
		return array();
	}

	private function get_billing( $booking_id ) {
		if ( class_exists( 'CHBSBooking' ) && method_exists( 'CHBSBooking', 'createBilling' ) ) {
			$billing = ( new CHBSBooking() )->createBilling( $booking_id );
			return is_array( $billing ) ? $billing : array();
		}
		return array();
	}

	private function get_prefixed_meta( $booking_id ) {
		if ( class_exists( 'CHBSPostMeta' ) && method_exists( 'CHBSPostMeta', 'getPostMeta' ) ) {
			$meta = CHBSPostMeta::getPostMeta( $booking_id );
			return is_array( $meta ) ? $meta : array();
		}
		$raw = get_post_meta( $booking_id );
		$meta = array();
		foreach ( $raw as $key => $value ) {
			$clean_key = preg_replace( '/^chbs_/', '', $key );
			$single = maybe_unserialize( $value[0] ?? '' );
			$meta[ $clean_key ] = $single;
		}
		return $meta;
	}

	private function coordinate_address( $coordinate ) {
		if ( is_object( $coordinate ) ) { $coordinate = (array) $coordinate; }
		if ( ! is_array( $coordinate ) ) { return ''; }
		if ( class_exists( 'CHBSHelper' ) && method_exists( 'CHBSHelper', 'getAddress' ) ) {
			return sanitize_text_field( CHBSHelper::getAddress( $coordinate ) );
		}
		$parts = array();
		foreach ( array( 'address', 'formatted_address', 'street', 'street_number', 'city', 'state', 'zip_code', 'postal_code', 'country' ) as $key ) {
			if ( ! empty( $coordinate[ $key ] ) ) { $parts[] = $coordinate[ $key ]; }
		}
		return sanitize_text_field( implode( ', ', array_unique( $parts ) ) );
	}

	private function driver_name_from_id( $driver_id ) {
		$driver_id = absint( $driver_id );
		if ( ! $driver_id ) { return ''; }
		$title = get_the_title( $driver_id );
		return $title ? $title : '';
	}

	private function get_total_amount( $billing ) {
		if ( isset( $billing['summary']['value_gross'] ) ) { return (string) $billing['summary']['value_gross']; }
		return '';
	}

	private function get_value( $source, $key, $default = '' ) {
		if ( is_array( $source ) && array_key_exists( $key, $source ) ) { return is_scalar( $source[ $key ] ) ? (string) $source[ $key ] : $source[ $key ]; }
		return $default;
	}
}
