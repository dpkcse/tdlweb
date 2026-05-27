<?php
/**
 * Billing Form Fields.
 *
 * @since 6.3.0
 */

namespace WPTravelEngine\Builders\FormFields;

use WPTravelEngine\Core\Models\Post\Customer;


/**
 * Form field class to render billing form fields.
 *
 * @since 6.3.0
 */
class BillingFormFields extends FormField {

	public function __construct( array $args = array() ) {
		parent::__construct( false );

		$this->init( $this->map_fields( \WTE_Default_Form_Fields::billing_form_fields(), $args['booking_ref'] ?? null ) );
	}

	/**
	 * @inheritDoc
	 */
	public function render(): void {
		?>
		<div class="wpte-checkout__form-section">
			<div class="wpte-checkout__form-row">
				<?php parent::render(); ?>
			</div>
		</div>
		<?php
	}

	protected function map_fields( $fields, $booking_ref ) {
		$billing_form_data = $this->get_billing_form_data( $booking_ref );

		return array_map(
			function ( $field ) use ( $billing_form_data ) {
				$name = null;

				// Extract the name using regex patterns.
				if ( preg_match( '#\[([^\[]+)]$#', $field['name'], $matches ) ) {
						$name = $matches[1];
				} elseif ( preg_match( '/^[^\s]+$/', $field['name'], $matches ) ) {
					$name = $matches[0];
				}

				// If a name was found, set field attributes.
				if ( $name ) {
					$field['class']         = 'wpte-checkout__input';
					$field['wrapper_class'] = 'wpte-checkout__form-col';
					if ( $field['type'] === 'file' ) {
						$field['name'] = sprintf( '%s', $name );
						$field['id']   = sprintf( '%s', $name );
					} else {
						$field['name'] = sprintf( 'billing[%s]', $name );
						$field['id']   = sprintf( 'billing_%s', $name );
					}
					$field['field_label'] = isset( $field['placeholder'] ) && $field['placeholder'] !== '' ? $field['placeholder'] : $field['field_label'];
					$field['default']     = $billing_form_data[ $name ] ?? $field['default'] ?? '';

				}

				return $field;
			},
			$fields
		);
	}

	/**
	 * Resolves billing form data from booking, customer, or session.
	 *
	 * Priority: booking meta > session data (merged with customer defaults) > empty array.
	 *
	 * @param int|null $booking_ref Booking post ID.
	 *
	 * @return array
	 * @since 6.8.0
	 */
	protected function get_billing_form_data( $booking_ref ): array {
		if ( $booking_ref ) {
			$booking_data = get_post_meta( $booking_ref, 'wptravelengine_billing_details', true );
			if ( is_array( $booking_data ) && ! empty( $booking_data ) ) {
				return $booking_data;
			}
		}

		$session_data = WTE()->session->get( 'billing_form_data' );
		$session_data = is_array( $session_data ) ? $session_data : array();

		$user = get_user_by( 'id', get_current_user_id() );
		if ( ! $user ) {
			return $session_data;
		}

		$customer_id = Customer::is_exists( $user->user_email );
		if ( ! $customer_id ) {
			return $session_data;
		}

		$customer          = new Customer( $customer_id );
		$customer_defaults = array_merge(
			array(
				'fname' => $customer->get_customer_fname(),
				'lname' => $customer->get_customer_lname(),
				'email' => $customer->get_customer_email(),
			),
			$customer->get_customer_addresses()
		);

		return array_merge( $customer_defaults, array_filter( $session_data ) );
	}

	/**
	 * @param array $form_data
	 * @return array
	 */
	public function with_values( array $form_data ) {
		$this->fields = DefaultFormFields::billing_form_fields();

		return array_map(
			function ( $field ) use ( $form_data ) {
				$name = null;

				// Extract the name using regex patterns.
				if ( preg_match( '#\[([^\[]+)]$#', $field['name'], $matches ) ) {
						$name = $matches[1];
				} elseif ( preg_match( '/^[^\s]+$/', $field['name'], $matches ) ) {
					$name = $matches[0];
				}

				// If a name was found, set field attributes.
				if ( $name ) {
					$field['class']         = 'wpte-checkout__input';
					$field['wrapper_class'] = 'wpte-checkout__form-col';
					if ( $field['type'] === 'file' ) {
						$field['name'] = sprintf( '%s', $name );
						$field['id']   = sprintf( '%s', $name );
					} else {
						$field['name'] = sprintf( 'billing[%s]', $name );
						$field['id']   = sprintf( 'billing_%s', $name );
					}
					$field['field_label'] = isset( $field['placeholder'] ) && $field['placeholder'] !== '' ? $field['placeholder'] : $field['field_label'];
					$value                = $form_data[ $name ] ?? $field['default'] ?? '';
					$field['default']     = $value;
					$field['value']       = self::resolve_display_value( $value, $field['type'] );
				}

				return $field;
			},
			$this->fields
		);
	}
}
