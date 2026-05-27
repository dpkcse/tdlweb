<?php
/**
 *
 * @since 6.4.0
 */

namespace WPTravelEngine\Builders\FormFields;

use WPTravelEngine\Abstracts\BookingEditFormFields;
use WPTravelEngine\Core\Models\Post\Booking;

class OrderTripEditFormFields extends BookingEditFormFields {

	protected ?Booking $booking = null;

	public function __construct( array $defaults = array(), string $mode = 'edit' ) {
		parent::__construct( $defaults, $mode );
		$this->init( $this->map_fields( static::structure( $mode ) ) );
	}

	/**
	 * Map Field
	 *
	 * @param mixed $field
	 *
	 * @updated 6.7.0
	 */
	protected function map_field( $field ) {
		$name = null;

		$field = parent::map_field( $field );

		if ( preg_match( '#\[([^\]]+)\]\[\]$#', $field['name'], $matches ) ) {
			$name = $matches[1];
		} elseif ( preg_match( '#\[([^\]]+)\]$#', $field['name'], $matches ) ) {
			$name = $matches[1];
		}

		if ( $name ) {
			$field['name'] = sprintf( 'order_trip[%s]', $name );
			$field['id']   = sprintf( 'order_trip_%s', $name );
		}
		if ( ! empty( $field['placeholder'] ) ) {
			$field['attributes']['placeholder'] = $field['placeholder'];
		}
		$field['default'] = $this->defaults[ $name ] ?? $field['default'] ?? '';

		if ( 'booked_date' === $name && ! empty( $field['default'] ) ) {
			$field['default'] = $this->format_date_for_view( $field['default'], true );
		}

		if ( 'readonly' === static::$mode ) {
			$class         = $field['class'] ?? '';
			$is_timepicker = false !== strpos( $class, 'wpte-time-picker' );
			$is_datepicker = ! $is_timepicker && ( isset( $field['attributes']['data-options'] ) || false !== strpos( $class, 'wpte-date-picker' ) );

			if ( $is_timepicker ) {
				// Time is already included in the date field display in readonly mode.
				return null;
			} elseif ( $is_datepicker ) {
				$field['class'] = trim( str_replace( 'wpte-date-picker', '', $class ) );
				unset( $field['attributes']['data-options'] );
				if ( ! empty( $field['default'] ) ) {
					$parsed        = date_parse( $field['default'] );
					$date_has_time = $parsed['hour'] !== false;
					if ( ! $date_has_time ) {
						// Date-only default: combine with the corresponding time field for display.
						$time_key         = str_replace( '_date', '_time', $name );
						$time_val         = $this->defaults[ $time_key ] ?? '';
						$combined         = ! empty( $time_val ) ? $field['default'] . 'T' . $time_val : $field['default'];
						$parsed           = date_parse( $combined );
						$date_has_time    = $parsed['hour'] !== false;
						$field['default'] = $this->format_trip_date_for_view( $combined, $date_has_time );
					} else {
						$field['default'] = $this->format_trip_date_for_view( $field['default'], true );
					}
				}
			}
		}

		// Ensure empty default for new bookings
		if ( $name === 'id' && static::$mode === 'edit' && empty( $this->defaults[ $name ] ) ) {
			$field['default'] = '';
		}
		if ( static::$mode === 'readonly' ) {
			$field['attributes']['readonly'] = 'readonly';
			$field['attributes']['disabled'] = 'disabled';
		}

		if ( $field['disabled'] ?? false ) {
			$field['attributes']['disabled'] = 'disabled';
		}

		if ( ( $field['type'] ?? '' ) === 'package_select' ) {
			$field['trip_id']    = (int) ( $this->defaults['id'] ?? 0 );
			$field['package_id'] = (int) ( $this->defaults['package_id'] ?? 0 );
		}

		if ( isset( $this->defaults['is_new_booking'] ) ) {
			$field['is_new_booking'] = (bool) $this->defaults['is_new_booking'];
		}

		if ( $field['name'] == 'order_trip[id]' ) {
			$field['validations'] = array(
				'required' => true,
			);
		}

		if ( $field['name'] == 'order_trip[number_of_travelers]' ) {
			$field['validations'] = array(
				'required' => true,
				'min'      => 1,
			);
		}

		// Set dynamic minDate for date picker fields
		if ( isset( $field['attributes']['data-options'] ) && is_array( $field['attributes']['data-options'] ) ) {
			$data_options = &$field['attributes']['data-options'];

			// For end_date field, set minDate to start_date if available
			if ( $name === 'end_date' && ! empty( $this->defaults['start_date'] ) ) {
				$data_options['minDate'] = $this->defaults['start_date'];
			}
		}

		return $field;
	}

	/**
	 * Format trip date/datetime for backend view mode using WP timezone.
	 *
	 * Stored trip datetime values are treated as UTC and converted for display.
	 *
	 * @param string $date      Stored date or datetime.
	 * @param bool   $with_time Whether to include time in output.
	 * @return string
	 * @since 6.8.0
	 */
	private function format_trip_date_for_view( string $date, bool $with_time = false ): string {
		try {
			// Treat plain stored values as site-local times.
			// Only apply UTC conversion when timezone is explicitly present in the value.
			$has_explicit_tz = (bool) preg_match( '/(Z|[+\-]\d{2}:?\d{2})$/', $date );
			$source_tz       = $has_explicit_tz ? new \DateTimeZone( 'UTC' ) : wp_timezone();
			$dt              = new \DateTimeImmutable( $date, $source_tz );
		} catch ( \Exception $e ) {
			return $date;
		}

		$format = get_option( 'date_format' );
		if ( $with_time ) {
			$format .= ' ' . get_option( 'time_format' );
		}

		return wp_date( $format, $dt->getTimestamp(), wp_timezone() );
	}

	public static function create(): OrderTripEditFormFields {
		return new static();
	}

	/**
	 * @updated 6.7.0
	 */
	public static function structure( string $mode = 'edit' ): array {
		$booking      = Booking::for( get_the_ID(), get_post() );
		$is_curr_cart = $booking->is_curr_cart();
		if ( $is_curr_cart ) {
			return self::order_trip_form_fields_in_v4( $mode );
		} else {
			return self::order_trip_form_fields_before_v4( $mode );
		}
	}

	/**
	 * Order trip form fields before v4.
	 *
	 * @param string $mode Mode.
	 * @return array
	 */
	private static function order_trip_form_fields_before_v4( string $mode = 'edit' ): array {
		$fields = apply_filters(
			'wptravelengine_order_trip_edit_fields_structure',
			array(
				'booked_trip'         => array(
					'type'          => 'trips_list',
					'wrapper_class' => 'row-repeater name-holder',
					'field_label'   => __( 'Booked Trip', 'wp-travel-engine' ),
					'name'          => 'order_trip[id]',
					'id'            => 'order_trip_booked_trip',
				),
				'booked_date'         => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater name-holder',
					'field_label'   => __( 'Booked Date', 'wp-travel-engine' ),
					'name'          => 'order_trip[booked_date]',
					'id'            => 'order_trip_booked_date',
					'class'         => 'input',
					'disabled'      => true,
				),
				'start_date'          => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Start Date', 'wp-travel-engine' ),
					'name'          => 'order_trip[start_date]',
					'id'            => 'order_trip_start_date',
					'class'         => 'wpte-date-picker',
					'attributes'    => array(
						'data-options' => array(
							'enableTime' => true,
							'dateFormat' => 'Y-m-d H:i',
						),
					),
					'context'       => array(
						'readonly' => array(
							'type'          => 'text',
							'wrapper_class' => 'row-repeater',
							'field_label'   => __( 'Start Date', 'wp-travel-engine' ),
							'name'          => 'order_trip[start_date]',
							'id'            => 'order_trip_start_date',
						),
					),
				),
				'end_date'            => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'End Date', 'wp-travel-engine' ),
					'name'          => 'order_trip[end_date]',
					'id'            => 'order_trip_end_date',
					'class'         => 'wpte-date-picker',
					'attributes'    => array(
						'data-options' => array(
							'enableTime' => true,
							'dateFormat' => 'Y-m-d H:i',
						),
					),
					'context'       => array(
						'readonly' => array(
							'type'          => 'text',
							'wrapper_class' => 'row-repeater',
							'field_label'   => __( 'End Date', 'wp-travel-engine' ),
							'name'          => 'order_trip[end_date]',
							'id'            => 'order_trip_end_date',
						),
					),
				),
				'trip_code'           => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Trip Code', 'wp-travel-engine' ),
					'name'          => 'order_trip[trip_code]',
					'id'            => 'order_trip_trip_code',
					'attributes'    => array( 'readonly' => 'readonly' ),
				),
				'number_of_travelers' => array(
					'type'          => 'number',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Number of Travelers', 'wp-travel-engine' ),
					'name'          => 'order_trip[number_of_travelers]',
					'id'            => 'order_trip_number_of_travelers',
					'attributes'    => array( 'readonly' => 'readonly' ),
				),
				'package_name'        => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Package Name', 'wp-travel-engine' ),
					'name'          => 'order_trip[package_name]',
					'id'            => 'order_trip_package_name',
					'class'         => 'input',
				),
			)
		);

		return DefaultFormFields::by_mode( $fields, $mode );
	}

	/**
	 * Order trip form fields in v4.
	 *
	 * @param string $mode Mode.
	 * @return array
	 * @updated 6.7.0
	 */
	private static function order_trip_form_fields_in_v4( string $mode = 'edit' ): array {
		$time_format     = get_option( 'time_format', 'g:i a' );
		$is_24hr         = false !== strpos( $time_format, 'H' ) || false !== strpos( $time_format, 'G' );
		$time_fp_options = array(
			'enableTime'      => true,
			'noCalendar'      => true,
			'time_24hr'       => $is_24hr,
			'dateFormat'      => 'H:i',
			'altInput'        => true,
			'altFormat'       => $is_24hr ? 'H:i' : 'h:i K',
			'altInputClass'   => 'wpte-time-picker',
			'minuteIncrement' => 1,
			'static'          => true,
		);

		$type = $mode === 'edit' ? 'text' : 'hidden';

		$fields = apply_filters(
			'wptravelengine_order_trip_edit_fields_structure',
			array(
				'trip_code'           => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Trip Code', 'wp-travel-engine' ),
					'name'          => 'order_trip[trip_code]',
					'id'            => 'order_trip_trip_code',
					'attributes'    => array( 'readonly' => 'readonly' ),
				),
				'booked_date'         => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater name-holder',
					'field_label'   => __( 'Booked Date', 'wp-travel-engine' ),
					'name'          => 'order_trip[booked_date]',
					'id'            => 'order_trip_booked_date',
					'class'         => 'input',
					'disabled'      => true,
				),
				'number_of_travelers' => array(
					'type'          => 'number',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Number of Travelers', 'wp-travel-engine' ),
					'name'          => 'order_trip[number_of_travelers]',
					'id'            => 'order_trip_number_of_travelers',
					'attributes'    => array(
						'readonly' => 'readonly',
						'disabled' => 'disabled',
					),
				),
				'booked_trip'         => array(
					'type'          => 'trips_select',
					'wrapper_class' => 'row-repeater name-holder',
					'field_label'   => __( 'Booked Trip', 'wp-travel-engine' ),
					'name'          => 'order_trip[id]',
					'id'            => 'order_trip_booked_trip',
					'default'       => '',
				),
				'custom_trip'         => array(
					'type'          => $type,
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Custom Trip', 'wp-travel-engine' ),
					'name'          => 'order_trip[custom_trip]',
					'id'            => 'order_trip_custom_trip',
					'class'         => 'input',
				),
				'package_name'        => array(
					'type'          => 'package_select',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Package Name', 'wp-travel-engine' ),
					'name'          => 'order_trip[package_name]',
					'id'            => 'order_trip_package_name',
					'class'         => 'input',
				),
				'package_id'          => array(
					'type'          => 'hidden',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Package ID', 'wp-travel-engine' ),
					'name'          => 'order_trip[package_id]',
					'id'            => 'order_trip_package_id',
					'class'         => 'input',
				),
				'custom_package'      => array(
					'type'          => $type,
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Custom Package Name', 'wp-travel-engine' ),
					'name'          => 'order_trip[custom_package]',
					'id'            => 'order_trip_custom_package',
					'default'       => '',
					'class'         => 'input',
				),
				'start_date'          => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Start Date', 'wp-travel-engine' ),
					'name'          => 'order_trip[start_date]',
					'id'            => 'order_trip_start_date',
					'class'         => 'wpte-date-picker',
					'context'       => array(
						'readonly' => array(
							'type'          => 'text',
							'wrapper_class' => 'row-repeater',
							'field_label'   => __( 'Start Date', 'wp-travel-engine' ),
							'name'          => 'order_trip[start_date]',
							'id'            => 'order_trip_start_date',
						),
					),
				),
				'start_time'          => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'Start Time', 'wp-travel-engine' ),
					'name'          => 'order_trip[start_time]',
					'id'            => 'order_trip_start_time',
					'class'         => 'wpte-time-picker',
					'placeholder'   => '--   --',
					'attributes'    => array(
						'data-options' => $time_fp_options,
					),
				),
				'end_date'            => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'End Date', 'wp-travel-engine' ),
					'name'          => 'order_trip[end_date]',
					'id'            => 'order_trip_end_date',
					'class'         => 'wpte-date-picker',
					'context'       => array(
						'readonly' => array(
							'type'          => 'text',
							'wrapper_class' => 'row-repeater',
							'field_label'   => __( 'End Date', 'wp-travel-engine' ),
							'name'          => 'order_trip[end_date]',
							'id'            => 'order_trip_end_date',
						),
					),
				),
				'end_time'            => array(
					'type'          => 'text',
					'wrapper_class' => 'row-repeater',
					'field_label'   => __( 'End Time', 'wp-travel-engine' ),
					'name'          => 'order_trip[end_time]',
					'id'            => 'order_trip_end_time',
					'class'         => 'wpte-time-picker',
					'placeholder'   => '--   --',
					'attributes'    => array(
						'data-options' => $time_fp_options,
					),
				),
			)
		);

		return DefaultFormFields::by_mode( $fields, $mode );
	}
}
