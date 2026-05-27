<?php
/**
 * Trips List field class
 *
 * @package WP Travel Engine
 * @since 6.7.0
 */
class WP_Travel_Engine_Form_Field_Trips_Select extends WP_Travel_Engine_Form_Field_Select {

	/**
	 * Field type name
	 *
	 * @var string
	 */
	protected $field_type = 'trips_select';

	/**
	 * Initialize class
	 *
	 * @param mixed $field
	 * @return $this
	 */
	public function init( $field ) {

		$is_array       = is_array( $field );
		$is_new_booking = (bool) ( $is_array ? ( $field['is_new_booking'] ?? false ) : ( $field->is_new_booking ?? false ) );
		$trip_id        = (int) ( $is_array ? ( $field['default'] ?? 0 ) : ( $field->default ?? 0 ) );

		if ( $is_new_booking ) {
			static $all_trips = null;
			if ( $all_trips === null ) {
				$all_trips = wp_travel_engine_get_trips_array( false, true );
			}
			$trips_options = array(
				''      => __( 'Choose a Trip', 'wp-travel-engine' ),
				'other' => __( 'Other', 'wp-travel-engine' ),
			) + $all_trips;

			if ( ! $trip_id ) {
				$trip_id = (int) ( array_key_first( $all_trips ) ?? 0 );
			}
		} else {
			if ( get_post( $trip_id ) ) {
				$title_suffix = wptravelengine_toggled( get_post_meta( $trip_id, 'is_created_from_booking', true ) ) ? ' [ ' . __( 'Manually Created', 'wp-travel-engine' ) . ' ] ' : '';
				$trip_title   = get_the_title( $trip_id ) . $title_suffix;
			} else {
				// Trip post was permanently deleted — show ID so the record is traceable.
				$trip_title = sprintf( __( '#%d (Deleted)', 'wp-travel-engine' ), $trip_id );
			}
			$trips_options = array(
				''       => __( 'Choose a Trip', 'wp-travel-engine' ),
				'other'  => __( 'Other', 'wp-travel-engine' ),
				$trip_id => $trip_title,
			);
		}

		$this->field = $field;

		if ( $is_array ) {
			$this->field['options'] = $trips_options;
			if ( $is_new_booking && $trip_id ) {
				$this->field['default'] = $trip_id;
			}
		} elseif ( is_object( $this->field ) ) {
			$this->field->options = $trips_options;
			if ( $is_new_booking && $trip_id ) {
				$this->field->default = $trip_id;
			}
		}

		return $this;
	}

	/**
	 * Render template for trips list with conditional custom input
	 *
	 * @param boolean $display
	 * @return string|void
	 */
	function render( $display = true ) {

		// Capture parent select field output
		ob_start();
		parent::render( true );
		$output = ob_get_clean();

		// Add JavaScript to toggle existing custom_trip input field
		$output .= sprintf(
			'<script>
			(function($) {
				$(document).ready(function() {
					var tripSelect = $("#%s");
					var customTripInput = $("#order_trip_custom_trip, input[name=\'order_trip[custom_trip]\']");
					var customTripWrapper = customTripInput.closest(".wpte-field");
					var tripCodeInput = $("#order_trip_trip_code, input[name=\'order_trip[trip_code]\']");
					var tripCodeWrapper = tripCodeInput.closest(".wpte-field");

					var emptyTripOption = tripSelect.find("option[value=\'\']").get(0);
					if ( emptyTripOption ) emptyTripOption.disabled = true;

					// If custom trip already has a value (edit mode), force select to Other
					if ($.trim(customTripInput.val()).length > 0) {
						tripSelect.val("other");
					}

					// Toggle custom trip field based on selection
					tripSelect.on("change", function() {
						if ($(this).val() === "other") {
							customTripWrapper.show();
							customTripInput.prop("disabled", false);
							tripCodeWrapper.show();
							tripCodeInput.prop("disabled", true);
						} else {
							customTripWrapper.hide();
							customTripInput.prop("disabled", true);
							customTripInput.val("");
							tripCodeWrapper.show();
							tripCodeInput.prop("disabled", true);
						}
					});

					// Trigger on page load if "other" is already selected
					if (tripSelect.val() === "other") {
						customTripWrapper.show();
						tripCodeWrapper.show();
						tripCodeInput.prop("disabled", true);
					} else {
						customTripWrapper.hide();
						customTripInput.prop("disabled", true);
						tripCodeWrapper.show();
						tripCodeInput.prop("disabled", true);
					}
				});
			})(jQuery);
			</script>',
			esc_js( is_array( $this->field ) ? ( $this->field['id'] ?? '' ) : ( is_object( $this->field ) ? ( $this->field->id ?? '' ) : '' ) )
		);

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
