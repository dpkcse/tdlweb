<?php
/**
 * Preview Template Controller.
 *
 * @package WPTravelEngine/Core/Controllers
 * @since 6.7.9
 */

namespace WPTravelEngine\Core\Controllers\Ajax;

use WPTravelEngine\Abstracts\AjaxController;
use WPTravelEngine\Email\Email;
use WPTravelEngine\Booking\Email\Dummy\DummyTags;

/**
 * Hanldes Preview Template.
 */
class PreviewEmail extends AjaxController {

	const NONCE_KEY    = 'nonce';
	const NONCE_ACTION = 'wptravelengine_email_template_preview';
	const ACTION       = 'email-template-preview';

	/**
	 * Email Template Preview process_request
	 *
	 * @since 6.7.9
	 */
	public function process_request() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wp-travel-engine' ) ) );
		}
		if ( empty( $_POST['content'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No content provided.', 'wp-travel-engine' ) ) );
		}

		$content = wp_unslash( $_POST['content'] );
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$content = wp_kses_post( $content );
		}

		$dummy_email_tags = new DummyTags();
		$email            = new Email();
		$content          = $this->get_test_alert() . $content;

		$email->set( 'content', $content )
				->set_tags( $dummy_email_tags->get_email_tags( $content ) );
		wp_send_json_success( wpautop( $email->apply_tags( $email->get( 'body' ) ), false ) );
	}

	/**
	 * Summary of get_test_alert
	 *
	 * @return string
	 */
	public function get_test_alert(): string {
		ob_start();
		?>
		<span class="wpte-test-email-tag"><?php echo esc_html__( 'Test Mode', 'wp-travel-engine' ); ?></span>
		<?php
		return ob_get_clean();
	}
}
