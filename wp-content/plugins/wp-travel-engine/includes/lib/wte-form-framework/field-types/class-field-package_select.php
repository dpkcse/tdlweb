<?php
/**
 * Package Select field class
 *
 * @package WP Travel Engine
 * @since 6.8.0
 */
use WPTravelEngine\Core\Models\Post\Trip;

class WP_Travel_Engine_Form_Field_Package_Select extends WP_Travel_Engine_Form_Field_Select {

	/**
	 * Field type name
	 *
	 * @var string
	 */
	protected $field_type = 'package_select';

	/**
	 * Initialize class
	 *
	 * @param mixed $field
	 * @return $this
	 */
	public function init( $field ) {

		$trip_id     = is_array( $field ) ? (int) ( $field['trip_id'] ?? 0 ) : (int) ( $field->trip_id ?? 0 );
		$package_id  = is_array( $field ) ? (int) ( $field['package_id'] ?? 0 ) : (int) ( $field->package_id ?? 0 );
		$stored_name = is_array( $field ) ? ( $field['default'] ?? '' ) : ( $field->default ?? '' );

		$available_pack = Trip::get_packages( $trip_id, true );

		$id_to_title    = array();
		$title_to_id    = array();
		$title_to_title = array();
		/** @var WP_Post $post */
		foreach ( $available_pack as $post ) {
			$id    = $post->ID;
			$title = $post->post_title;

			if ( wptravelengine_toggled( get_post_meta( $id, 'is_manual_package', true ) ) ) {
				$title .= ' [ ' . __( 'Manually Created', 'wp-travel-engine' ) . ' ]';
			}

			$id_to_title[ $id ]       = $title;
			$title_to_id[ $title ]    = $id;
			$title_to_title[ $title ] = $title;
		}

		// If the stored package no longer exists, inject a read-only deleted indicator.
		if ( $package_id && ! isset( $id_to_title[ $package_id ] ) ) {
			$deleted_title                    = ( $stored_name ? $stored_name . ' ' : '' ) . __( '(Deleted)', 'wp-travel-engine' );
			$id_to_title[ $package_id ]       = $deleted_title;
			$title_to_id[ $deleted_title ]    = $package_id;
			$title_to_title[ $deleted_title ] = $deleted_title;
		}

		$packages_options = array(
			''      => __( 'Choose a Package', 'wp-travel-engine' ),
			'other' => __( 'Other', 'wp-travel-engine' ),
		) + $title_to_title;

		$package_id_attr = array(
			''      => 0,
			'other' => 0,
		) + $title_to_id;
		$resolved        = $id_to_title[ $package_id ] ?? ( $stored_name ? 'other' : '' );

		if ( is_array( $field ) ) {
			$field['default']                         = $resolved;
			$field['options']                         = $packages_options;
			$field['assoc_option_atts']['package-id'] = $package_id_attr;
		} elseif ( is_object( $field ) ) {
			$field->default                           = $resolved;
			$field->options                           = $packages_options;
			$field->assoc_option_atts->{'package-id'} = $package_id_attr;
		}

		$this->field = $field;

		return $this;
	}

	/**
	 * Collect a trip_id => packages[] map for all published trips.
	 *
	 * @return array
	 */
	private function collect_packages_by_trip(): array {
		$trips = wp_travel_engine_get_trips_array( false, true );

		$result = array();
		foreach ( $trips as $tid => $_ ) {
			$pkgs      = Trip::get_packages( (int) $tid, true );
			$trip_pkgs = array();
			foreach ( $pkgs as $pkg ) {
				$title = $pkg->post_title;
				if ( wptravelengine_toggled( get_post_meta( $pkg->ID, 'is_manual_package', true ) ) ) {
					$title .= ' [ ' . __( 'Manually Created', 'wp-travel-engine' ) . ' ]';
				}
				$trip_pkgs[] = array(
					'id'    => $pkg->ID,
					'title' => $title,
				);
			}
			$result[ $tid ] = $trip_pkgs;
		}

		return $result;
	}

	/**
	 * Render template for package select with conditional custom input
	 *
	 * @param boolean $display
	 * @return string|void
	 */
	public function render( $display = true ) {
		$field     = $this->field;
		$select_id = esc_js( is_array( $field ) ? ( $field['id'] ?? '' ) : ( is_object( $field ) ? ( $field->id ?? '' ) : '' ) );
		$_is_new   = (bool) ( is_array( $field ) ? ( $field['is_new_booking'] ?? false ) : ( $field->is_new_booking ?? false ) );

		$output = parent::render( false );

		$output .= '<script>
		(function() {
			function initPackageSelect() {
				var packageSelect = document.getElementById( "' . $select_id . '" );
				if ( ! packageSelect ) return;

				var customPackageInput = document.getElementById( "order_trip_custom_package" )
					|| document.querySelector( "input[name=\'order_trip[custom_package]\']" );
				var customPackageWrapper = customPackageInput ? customPackageInput.closest( ".wpte-field" ) : null;
				var packageIdInput = document.getElementById( "order_trip_package_id" );

				var emptyOption = packageSelect.querySelector( "option[value=\'\']" );
				if ( emptyOption ) emptyOption.disabled = true;

				function applyPackageToggle() {
					var isOther = packageSelect.value === "other";
					if ( customPackageWrapper ) customPackageWrapper.style.display = isOther ? "" : "none";
					if ( customPackageInput ) customPackageInput.disabled = ! isOther;
					if ( packageIdInput ) {
						var selected = packageSelect.options[ packageSelect.selectedIndex ];
						packageIdInput.value = ! isOther ? ( ( selected && selected.dataset.packageId ) || 0 ) : 0;
					}
				}

				packageSelect.addEventListener( "change", applyPackageToggle );
				applyPackageToggle();';

		if ( $_is_new ) {
			$packages_json        = wp_json_encode( $this->collect_packages_by_trip() );
			$label_choose_package = wp_json_encode( __( 'Choose a Package', 'wp-travel-engine' ) );
			$label_other          = wp_json_encode( __( 'Other', 'wp-travel-engine' ) );

			$output .= '
				var packagesByTrip = ' . $packages_json . ';
				var tripSelectEl   = document.getElementById( "order_trip_id" );

				function buildPackageOptions( tripId ) {
					var pkgs    = packagesByTrip[ tripId ] || [];
					var prevVal = packageSelect.value;

					while ( packageSelect.options.length > 0 ) {
						packageSelect.remove( 0 );
					}

					var emptyOpt      = new Option( ' . $label_choose_package . ', "" );
					emptyOpt.disabled = true;
					packageSelect.add( emptyOpt );

					var otherOpt             = new Option( ' . $label_other . ', "other" );
					otherOpt.dataset.packageId = 0;
					packageSelect.add( otherOpt );

					pkgs.forEach( function( pkg ) {
						var opt             = new Option( pkg.title, pkg.title );
						opt.dataset.packageId = pkg.id;
						packageSelect.add( opt );
					} );

					packageSelect.value = prevVal;
					if ( packageSelect.value !== prevVal ) {
						packageSelect.value = "";
					}

					applyPackageToggle();
				}

				if ( tripSelectEl ) {
					tripSelectEl.addEventListener( "change", function() {
						buildPackageOptions( this.value );
					} );
					if ( tripSelectEl.value && tripSelectEl.value !== "" && tripSelectEl.value !== "other" ) {
						buildPackageOptions( tripSelectEl.value );
					}
				}';
		}

		$output .= '
			}

			if ( document.readyState === "loading" ) {
				document.addEventListener( "DOMContentLoaded", initPackageSelect );
			} else {
				initPackageSelect();
			}
		})();
		</script>';

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}
}
