<?php
/**
 * Upcoming tours details controller.
 *
 * @since 6.4.1
 */
namespace WPTravelEngine\Core\Controllers\Ajax;

use WPTravelEngine\Abstracts\AjaxController;
use WPTravelEngine\Pages\Admin\UpcomingTours;

class UpcomingToursDetails extends AjaxController {

	const NONCE_KEY    = 'nonce';
	const NONCE_ACTION = 'wte_upcoming_tours_details';
	const ACTION       = 'wte_upcoming_tours_details';
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
		$get  = $this->request->get_query_params();
		$id   = isset( $get['id'] ) ? sanitize_text_field( wp_unslash( $get['id'] ) ) : '';
		$html = UpcomingTours::get_details_html( $id );
		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
	}
}
