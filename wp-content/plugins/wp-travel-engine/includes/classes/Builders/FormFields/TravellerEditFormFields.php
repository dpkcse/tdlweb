<?php
/**
 *
 * @since 6.4.0
 */

namespace WPTravelEngine\Builders\FormFields;

use WPTravelEngine\Abstracts\BookingEditFormFields;
use WTE_Default_Form_Fields;
class TravellerEditFormFields extends BookingEditFormFields {

	/**
	 * Traveller count
	 *
	 * @var int
	 */
	protected $count;

	public function __construct( array $defaults = array(), string $mode = 'edit', $booking = null ) {
		parent::__construct( $defaults, $mode );
		$this->count = intval( $defaults['index'] ?? $defaults['total_count'] ?? 0 );
		$this->init( $this->map_fields( static::structure( $mode, $defaults['index'] ?? 'new_traveller', $booking ) ) );
	}

	protected function map_field( $field ) {

		$name = null;

		$field = parent::map_field( $field );

		// Extract the name using regex patterns.
		if ( preg_match( '#\[([^\[]+)]$#', $field['name'], $matches ) ) {
			$name = $matches[1];
		} elseif ( preg_match( '/^[^\s]+$/', $field['name'], $matches ) ) {
			$name = $matches[0];
		}

		// If a name was found, set field attributes.
		if ( $name ) {
			$field['name']        = sprintf( 'travellers[%s][%s]', $name, $this->count );
			$field['id']          = sprintf( 'travellers[%s][%s]', $name, $this->count );
			$field['field_label'] = isset( $field['placeholder'] ) && $field['placeholder'] !== '' ? $field['placeholder'] : ( $field['field_label'] ?? '' );

			$field['default']                 = $this->defaults[ $name ] ?? $field['default'] ?? '';
			$field['validations']['required'] = false;
			if ( $field['type'] == 'country' ) {
				$field = $this->resolve_country_field_default( $field );
			}
		}

		// Convert datepicker to text input to fix styling issue and to enable time selection.
		if ( $field['type'] === 'datepicker' ) {
			$field = $this->apply_datepicker_field( $field );
		}

		if ( static::$mode !== 'edit' && ! ( $field['skip_disabled'] ?? false ) ) {
			$field['option_attributes'] = array(
				'disabled' => 'disabled',
			);
			$field['attributes']        = array(
				'disabled' => 'disabled',
			);
		}

		return $field;
	}

	/**
	 * Structure the fields.
	 * Passed second parameter @since 6.4.3 to handle lead traveller form fields.
	 *
	 * @param string $mode Mode.
	 * @param mixed  $count Count.
	 *
	 * @return array
	 */
	public static function structure( string $mode = 'edit', string $count = 'new_traveller', $booking = null ): array {
		if ( $booking && $booking->get_meta( 'traveller_page_type' ) == 'old' ) {
			return WTE_Default_Form_Fields::traveller_information();
		} elseif ( $mode == 'edit' && $count == 'new_traveller' ) {
			return DefaultFormFields::traveller( $mode );
		} else {
			return $count == 0
				? DefaultFormFields::lead_traveller( $mode )
				: DefaultFormFields::traveller( $mode );
		}
	}

	public static function create( ...$args ): TravellerEditFormFields {
		return new static( ...$args );
	}
}
