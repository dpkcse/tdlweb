<?php
/**
 * Base Class For Booking Edit Forms.
 *
 * @since 6.4.0
 */

namespace WPTravelEngine\Abstracts;

use WPTravelEngine\Builders\FormFields\FormField;
use WPTravelEngine\Core\Models\Post\Booking;
use WPTravelEngine\Helpers\Countries;

abstract class BookingEditFormFields extends FormField {

	protected array $defaults = array();

	protected static string $mode;

	public function __construct( array $defaults = array(), string $mode = 'edit' ) {
		$this->defaults = $defaults;
		parent::__construct();
		static::$mode = $mode;
	}

	/**
	 * @inheritDoc
	 */
	public function render(): void {
		echo $this->process();
	}

	protected function map_field( $field ) {
		$field['wrapper_class'] = 'wpte-field';

		return $field;
	}

	protected function map_fields( $fields ): array {
		return array_map( array( $this, 'map_field' ), $fields );
	}

	/**
	 * Get defaults array.
	 *
	 * @return array Defaults array.
	 * @since 6.7.0
	 */
	public function get_defaults(): array {
		return $this->defaults;
	}

	/**
	 * Format date using WP date/time format.
	 *
	 * @param string $date      Stored date string.
	 * @param bool   $with_time Include time format.
	 * @return string
	 * @since 6.8.0
	 */
	protected function format_date_for_view( string $date, bool $with_time = false ): string {
		try {
			$dt = new \DateTimeImmutable( $date, wp_timezone() );
		} catch ( \Exception $e ) {
			return $date;
		}
		$format = get_option( 'date_format' );
		if ( $with_time ) {
			$format .= ' ' . get_option( 'time_format' );
		}
		return wp_date( $format, $dt->getTimestamp() );
	}

	/**
	 * Format a stored HH:MM time value using WP time format for display.
	 *
	 * @param string $time Stored time string (HH:MM).
	 * @return string
	 * @since 6.8.0
	 */
	protected function format_time_for_view( string $time ): string {
		$timestamp = strtotime( $time );
		if ( false === $timestamp ) {
			return $time;
		}
		return wp_date( get_option( 'time_format' ), $timestamp );
	}

	/**
	 * Convert datepicker field: flatpickr in edit mode, formatted date in view mode.
	 *
	 * @param array $field Field array.
	 * @return array
	 * @since 6.8.0
	 */
	protected function apply_datepicker_field( array $field ): array {
		$field['type'] = 'text';
		if ( static::$mode !== 'edit' ) {
			if ( ! empty( $field['default'] ) ) {
				$field['default'] = $this->format_date_for_view( $field['default'] );
			}
		} else {
			$field['class'] = 'wpte-date-picker';
		}
		return $field;
	}

	/**
	 * Resolve country name to its country code for field default.
	 *
	 * @param array $field Field array.
	 * @return array
	 * @since 6.8.0
	 */
	protected function resolve_country_field_default( array $field ): array {
		foreach ( Countries::list() as $key => $value ) {
			if ( $field['default'] === $value ) {
				$field['default'] = $key;
				break;
			}
		}
		return $field;
	}

	abstract public static function structure( string $mode = 'edit' );
}
