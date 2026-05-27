<?php
/**
 * Additional Itinerary Info Fields Template.
 *
 * @since 1.5.1
 */

if ( defined( 'WTEAI_VERSION' ) && version_compare( WP_TRAVEL_ENGINE_VERSION, '6.6.9', '>=' ) && version_compare( WTEAI_VERSION, '2.2.4', '>=' ) ) :
	$itinerary_info = wptravelengine_advanced_itinerary_get_info( $trip_id );
	if ( wptravelengine_advanced_itinerary_info_enable() && ! empty( $itinerary_info[ $key + 1 ] ) ) :
		$infos = $itinerary_info[ $key + 1 ]; ?>
		<div class="wpte-itinerary-infos">
			<?php foreach ( $infos as $info ) : ?>
				<div class="wpte-itinerary-info-item">
					<div class="wpte-itinerary-info-item-icon">
						<?php
						if ( isset( $info['icon'] ) && $info['icon'] !== '' ) {
							$icon_data = isset( $info['icon']['id'] ) ? wp_get_attachment_image( $info['icon']['id'], 'thumbnail', true ) : wptravelengine_svg_by_fa_icon( $info['icon'], false );
							echo $icon_data;
						}
						?>
					</div>
					<div class="wpte-itinerary-info-item-content">
						<span class="wpte-itinerary-info-item-content-title"><?php echo esc_html( $info['title'] ); ?></span>
						<span class="wpte-itinerary-info-item-content-value">
							<?php echo esc_html( $info['value'] ); ?>
						</span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	endif;
endif;

