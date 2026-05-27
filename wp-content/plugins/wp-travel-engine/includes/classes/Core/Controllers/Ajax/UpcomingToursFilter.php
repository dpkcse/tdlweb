<?php
/**
 * Upcoming tours filter controller.
 *
 * @since 6.4.1
 */
namespace WPTravelEngine\Core\Controllers\Ajax;

use WPTravelEngine\Abstracts\AjaxController;
use WPTravelEngine\Pages\Admin\UpcomingTours;

class UpcomingToursFilter extends AjaxController {

	const NONCE_KEY    = 'nonce';
	const NONCE_ACTION = 'wte_filter_upcoming_tours';
	const ACTION       = 'wte_filter_upcoming_tours';
	const ALLOW_NOPRIV = false;


	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Process request
	 */
	protected function process_request() {
		$post = $this->request->get_body_params();

		$valid_statuses = array_keys( UpcomingTours::get_filtered_statuses() );
		$status         = isset( $post['status'] ) ? sanitize_text_field( $post['status'] ) : 'all';
		if ( ! in_array( $status, $valid_statuses, true ) ) {
			$status = 'all';
		}

		$html = UpcomingTours::get_upcoming_tours_html(
			array(
				'date'        => isset( $post['date'] ) ? sanitize_text_field( $post['date'] ) : 'all',
				'count'       => isset( $post['count'] ) ? absint( $post['count'] ) : 10,
				'status'      => $status,
				'keywords'    => isset( $post['keywords'] ) ? sanitize_text_field( $post['keywords'] ) : '',
				'destination' => isset( $post['destination'] ) ? sanitize_text_field( $post['destination'] ) : '',
				'activity'    => isset( $post['activity'] ) ? sanitize_text_field( $post['activity'] ) : '',
			)
		);
		wp_send_json_success( array( 'html' => $html ) );
	}
}
