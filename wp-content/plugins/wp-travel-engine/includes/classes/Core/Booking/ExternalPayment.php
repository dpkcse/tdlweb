<?php
/**
 * Handle External Payment Request.
 *
 * @since 6.4.0
 */

namespace WPTravelEngine\Core\Booking;

use WPTravelEngine\Core\Controllers\Ajax\AddToCart;
use WPTravelEngine\Core\Models\Post\Payment;
use WPTravelEngine\Helpers\Functions;
use WPTravelEngine\Utilities\RequestParser;

/**
 *
 * @since 6.4.0.
 */
class ExternalPayment {

	/**
	 * Handles external payment request identified by `_payment_key`.
	 *
	 * @param RequestParser $request Incoming HTTP request containing `_payment_key`.
	 * @since 6.8.0 Redirects to 404 if the key is missing, expired, the booking ID is absent, or {@see AddToCart} returns a WP_Error.
	 */
	public function __construct( RequestParser $request ) {
		$result = false;
		$key    = $request->get_param( '_payment_key' );

		if ( $data = get_transient( '_payment_key_' . $key ) ) {
			$data = json_decode( $data, true );

			$booking_id = $data['booking_id'] ?? false;

			if ( $booking_id ) {
				$request = Functions::create_request( 'POST' );

				$request->set_body(
					wp_json_encode(
						array(
							'_nonce'       => wp_create_nonce( 'wte_add_trip_to_cart' ),
							'cart_version' => '2.0',
							'booking_id'   => $booking_id,
						)
					)
				);

				$result = AddToCart::create( $request )->add_to_cart();

				if ( is_wp_error( $result ) ) {
					$result = false;
				}
			}
		}

		if ( ! $result ) {
			$result = array(
				'redirect' => home_url( '/404' ),
			);
		}

		if ( ! headers_sent() ) {
			if ( isset( $result['redirect'] ) ) {
				wp_safe_redirect( $result['redirect'] );
				exit;
			}
		}
	}

	public static function is_request(): bool {
		if ( isset( $_REQUEST['_payment_key'] ) ) {
			return true;
		}

		return false;
	}
}
