<?php
/**
 * Send Test Email Controller.
 *
 * @package WPTravelEngine/Core/Controllers
 * @since 6.7.9
 */

namespace WPTravelEngine\Core\Controllers\Ajax;

use WPTravelEngine\Abstracts\AjaxController;
use WPTravelEngine\Email\Email;

/**
 * Send Test Email Template.
 */
class SendTestEmail extends AjaxController {

	const NONCE_KEY    = 'nonce';
	const NONCE_ACTION = 'wptravelengine_test_email_nonce';
	const ACTION       = 'wptravelengine_send_test_email';

	/**
	 * Email Template Preview process_request
	 *
	 * @since 6.7.9
	 */
	public function process_request() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'wp-travel-engine' ) ) );
		}
		if ( isset( $_POST['content'] ) && ! empty( $_POST['content'] ) ) {
			$subject = '(Test Email) ' . sanitize_text_field( wp_unslash( $_POST['subject'] ?? '' ) );
			$content = wp_unslash( $_POST['content'] );
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				$content = wp_kses_post( $content );
			}
		} else {
			$subject = get_bloginfo( 'name' ) . ' Test Email: Confirming Server Configuration';
			$content = "This is a test email to confirm that your email server configuration is set up correctly. You should have received this email shortly after initiating the test from our system.\n\nIf you are reading this message, it means that your server is properly configured for email sending. Congratulations!";
		}
		$to_raw = sanitize_email( $_POST['email'] ?? '' );
		$to     = is_email( $to_raw ) ? $to_raw : wp_get_current_user()->user_email;

		$dummy_email_tags = new \WPTravelEngine\Booking\Email\Dummy\DummyTags();
		$email            = new Email();
		$email->set( 'to', $to )
				->set( 'my_subject', $subject )
				->set( 'content', $content )
				->set_tags( $dummy_email_tags->get_email_tags( $content, $subject ) );

		if ( $email->send() ) {
			wp_send_json_success( array( 'message' => __( 'Test Email Sent Successfully. Please check your inbox.', 'wp-travel-engine' ) ) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Failed to send the test email. Something went wrong.', 'wp-travel-engine' ) ) );
		}
	}
}
