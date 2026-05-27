<?php
/**
 * Traveller Form Fields.
 *
 * @since 6.3.0
 */

namespace WPTravelEngine\Builders\FormFields;

use WTE_Default_Form_Fields;

/**
 * Form field class to render billing form fields.
 *
 * @since 6.3.0
 */
class TravellerFormFields extends FormField {

	/**
	 * Summary of traveller_count
	 *
	 * @var int
	 */
	public int $traveller_count = 0;

	public function __construct() {
		parent::__construct( false );
	}

	public function render() {
		if ( $this->use_legacy_template ) {
			parent::render();

			return;
		}
		echo '<div class="wpte-checkout__form-row">';
		parent::render();
		echo '</div>';
	}

	/**
	 * @param array $form_data
	 *
	 * @return array
	 * @since 6.3.3
	 */
	public function with_values( array $form_data, $booking = null ): array {
		++$this->traveller_count;

		if ( $this->traveller_count == 1 && $booking ) {
			$this->fields = 'old' === $booking->get_meta( 'traveller_page_type' )
				? WTE_Default_Form_Fields::traveller_information()
				: DefaultFormFields::lead_traveller();
		} else {
			$this->fields = WTE_Default_Form_Fields::traveller_information();
		}
		return array_map(
			function ( $field ) use ( $form_data ) {
				$name = preg_match( '#\[([^\[]+)]$#', $field['name'], $matches ) ? $matches[1] : $field['name'];
				if ( $name ) {
						$field['class']         = 'wpte-checkout__input';
						$field['wrapper_class'] = 'wpte-checkout__form-col';
						$field['name']          = sprintf( 'travellers[%s]', $name );

						$field['id'] = sprintf( 'travellers_%s', $name );
				}
				$field['field_label'] = isset( $field['placeholder'] ) && $field['placeholder'] !== '' ? $field['placeholder'] : $field['field_label'];
				$value                = $form_data[ $name ] ?? $field['default'] ?? '';
				$field['value']       = self::resolve_display_value( $value, $field['type'] );
				return $field;
			},
			$this->fields
		);
	}
}
