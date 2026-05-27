<?php
/**
 * Abstract Email Tags.
 *
 * Base class for all email tag resolvers.
 * Inherits {sitename}, {site_admin_email}, {ip_address} from TemplateTags.
 * Subclasses implement build_callbacks() with their own data source.
 *
 * @since 6.7.9
 */

namespace WPTravelEngine\Abstracts;

use WPTravelEngine\Email\TemplateTags;

/**
 * Abstract EmailTags class.
 *
 * @since 6.7.9
 */
abstract class EmailTags extends TemplateTags {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Each subclass defines its own tag => callable registry.
	 *
	 * @return array<string, callable>
	 */
	abstract protected function build_callbacks(): array;

	// Customer / billing.
	abstract public function get_last_name(): string;
	abstract public function get_fullname(): string;
	abstract public function get_user_email(): string;
	abstract public function get_billing_address(): string;
	abstract public function get_city(): string;
	abstract public function get_country(): string;

	// Trip.
	abstract public function get_trip_url(): string;
	abstract public function get_booked_trip_name(): string;
	abstract public function get_trip_code(): string;
	abstract public function get_trip_start_date(): string;
	abstract public function get_trip_end_date(): string;
	abstract public function get_traveler_count(): int;

	// Booking.
	abstract public function get_booking_id(): string;
	abstract public function get_booking_url(): string;
	abstract public function get_booking_trips_count(): int;
	abstract public function get_date(): string;
	abstract public function get_trip_booked_date(): string;

	// Payment / price.
	abstract public function get_payment_id(): int;
	abstract public function get_payment_method(): string;
	abstract public function get_payment_link(): string;
	abstract public function get_price(): string;
	abstract public function get_total_cost(): string;
	abstract public function get_subtotal(): string;
	abstract public function get_total(): string;
	abstract public function get_paid_amount(): string;
	abstract public function get_due(): string;
	abstract public function get_trip_extra_fee(): string;
	abstract public function get_total_gateway_fee(): string;

	// Discount.
	abstract public function get_discount_name(): string;
	abstract public function get_discount_amount(): string;
	abstract public function get_discount_sign(): string;
	abstract public function get_discount_value(): string;

	// HTML blocks.
	abstract public function get_booking_details(): string;
	abstract public function get_trip_booking_summary(): string;
	abstract public function get_trip_payment_details(): string;
	abstract public function get_trip_booking_details(): string;
	abstract public function get_additional_note(): string;
	abstract public function get_billing_details(): string;
	abstract public function get_traveler_details(): string;
	abstract public function get_emergency_details(): string;
	abstract public function get_bank_details(): string;
	abstract public function get_check_payment_instruction(): string;

	/**
	 * Resolve and return the full tag array.
	 *
	 * @param string $content Template body content.
	 * @param string $subject Template subject line.
	 * @return array<string, string>
	 */
	final public function get_email_tags( string $content = '', string $subject = '' ): array {
		$callbacks = $this->build_callbacks();

		$scan = $subject . $content;
		if ( $scan !== '' ) {
			preg_match_all( '/\{(\w+)\}/i', $scan, $matches );
			if ( ! empty( $matches[0] ) ) {
				$callbacks = array_intersect_key( $callbacks, array_flip( $matches[0] ) );
			}
		}

		// $this->tags already holds the 3 site tags; merge resolved callbacks on top.
		return array_merge( $this->tags, array_map( 'call_user_func', $callbacks ) );
	}
}
