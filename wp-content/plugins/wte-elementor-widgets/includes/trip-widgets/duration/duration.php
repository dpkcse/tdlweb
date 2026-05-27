<?php
/**
 * Duration Widget.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

global $post;
$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

$duration      = isset( $post_meta['trip_duration'] ) && '' !== $post_meta['trip_duration']
	? $post_meta['trip_duration'] : '';
$duration_unit = isset( $post_meta['trip_duration_unit'] ) && '' !== $post_meta['trip_duration_unit']
	? $post_meta['trip_duration_unit'] : 'days';

$nights = isset( $post_meta['trip_duration_nights'] ) && '' !== $post_meta['trip_duration_nights']
	? $post_meta['trip_duration_nights'] : '';

// Get WP Travel Engine global settings to respect Trip Duration Format preference
$wp_travel_engine_settings = get_option( 'wp_travel_engine_settings', array() );
$trip_duration_format      = isset( $wp_travel_engine_settings['trip_duration_format'] ) ? $wp_travel_engine_settings['trip_duration_format'] : 'days_and_nights';

// Retrieve attributes from elementor.
$attributes    = (object) $attributes;
$display_style = isset( $attributes->{'displayStyle'} ) ? $attributes->{'displayStyle'} : 'vertically';
?>

<?php if ( ! empty( $duration ) ) : ?>
	<div class="wte-trips-duration">
		<span class="wte-title-duration <?php echo esc_attr( $display_style ); ?>">
			<span class="duration">
				<?php echo esc_html( number_format_i18n( $duration ) ); ?>
			</span>
			<span class="days">
				<?php
				if ( 'days' === $duration_unit ) {
					echo wp_kses_post( sprintf( _nx( 'Day', 'Days', $duration, 'days', 'wptravelengine-elementor-widgets' ) ), 'wptravelengine-elementor-widgets' );
				}
				if ( 'hours' === $duration_unit ) {
					echo wp_kses_post( sprintf( _nx( 'Hour', 'Hours', $duration, 'hours', 'wptravelengine-elementor-widgets' ) ), 'wptravelengine-elementor-widgets' );
				}
				?>
			</span>
		</span>
	
		<?php if ( ! empty( $nights ) && 'days_and_nights' === $trip_duration_format && 'hours' !== $duration_unit ) { ?>
			<span class="wte-title-duration wte-duration-night <?php echo esc_attr( $display_style ); ?>">
				<span class="duration">
					<?php echo esc_html( number_format_i18n( $nights ) ); ?>
				</span>
				<span class="days">
					<?php printf( esc_html( _nx( 'Night', 'Nights', $nights, 'nights', 'wptravelengine-elementor-widgets' ) ) ); ?>
				</span>
			</span>
		<?php } ?>
		
	</div>
<?php endif; ?>
<?php do_action( 'wp_travel_engine_header_hook' ); ?>
