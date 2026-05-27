<?php
/**
 * Migrate booking AJAX controller.
 *
 * @since 6.8.0
 */

namespace WPTravelEngine\Core\Controllers\Ajax;

use WPTravelEngine\Abstracts\AjaxController;
use WPTravelEngine\Core\Booking\MigrateBooking as CoreMigrateBooking;

class MigrateBooking extends AjaxController {

	const NONCE_KEY    = 'nonce';
	const NONCE_ACTION = 'wptravelengine_migrate_booking';
	const ACTION       = 'wptravelengine_migrate_booking';
	const ALLOW_NOPRIV = false;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Process request.
	 */
	protected function process_request() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( new \WP_Error( 'MIGRATE_BOOKING_ERROR', __( 'Insufficient permissions.', 'wp-travel-engine' ) ) );
			return;
		}

		$post       = $this->request->get_body_params();
		$booking_id = absint( $post['booking_id'] ?? 0 );

		if ( ! $booking_id || get_post( $booking_id ) === null ) {
			wp_send_json_error( new \WP_Error( 'MIGRATE_BOOKING_ERROR', __( 'Invalid booking ID.', 'wp-travel-engine' ) ) );
			return;
		}

		$migrate        = new CoreMigrateBooking( $booking_id );
		$new_booking_id = $migrate->get_new_booking_id();

		if ( ! $new_booking_id ) {
			wp_send_json_error( new \WP_Error( 'MIGRATE_BOOKING_ERROR', __( 'Migration failed or booking is already up to date.', 'wp-travel-engine' ) ) );
			return;
		}

		wp_send_json_success(
			array(
				'message'      => __( 'Booking migrated successfully.', 'wp-travel-engine' ),
				'booking_id'   => $new_booking_id,
				'redirect_url' => admin_url( "post.php?post={$new_booking_id}&action=edit" ),
			)
		);
	}
}
