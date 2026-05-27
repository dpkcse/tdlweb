<?php
/**
 *
 * @since 6.3.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var string $banner_layout
 * @var array $wpte_trip_images List of image sizes.
 * @var bool $show_gallerypopup
 * @var bool $show_videopopup
 * @var bool $fullwidth_class Is full width banner enabled?
 */

if ( isset( $wpte_trip_images['enable'] ) ) {
	unset( $wpte_trip_images['enable'] );
}

if ( $thumbnail_id = get_post_thumbnail_id( $trip_id ) ) {
	if ( ! in_array( $thumbnail_id, $wpte_trip_images, false ) ) {
		array_unshift( $wpte_trip_images, $thumbnail_id );
	}
}

?>
<div class="wpte-gallery-wrapper__multi-banners">
	<div class="wpte-gallery-wrapper <?php echo esc_attr( $banner_layout ); ?>">
		<div class="wpte-multi-banner-layout<?php echo esc_attr( $fullwidth_class ); ?>">
			<?php
			if ( 'banner-layout-1' === $banner_layout ) {
				$images_to_display = array( $wpte_trip_images[0] );
			} else {
				/**
				 * Use this filter to generate markup for images.
				 *
				 * @param $wpte_trip_images List of attachment IDs.
				 */
				$images_to_display = apply_filters(
					'wptravelengine_trip_dynamic_banner_list_images',
					$wpte_trip_images,
					$banner_layout,
					$show_gallerypopup,
					$show_videopopup
				);
			}

			foreach ( $images_to_display as $image ) {
				if ( is_numeric( $image ) ) {
					$attachment_url = wp_get_attachment_image_url( $image, 'full' );
					?>
					<div class="wpte-multi-banner-image">
						<img
							src="<?php echo esc_url( $attachment_url ); ?>"
							alt="<?php echo esc_attr( get_post_meta( $image, '_wp_attachment_image_alt', true ) ); ?>"
						/>
					</div>
					<?php
				} else {
					echo wp_kses_post( $image );
				}
			}
			?>
		</div>
		<?php
		if ( $show_gallerypopup === 'yes' || $show_videopopup === 'yes' ) :
			extract( array( $show_gallerypopup, $show_videopopup, $popup_position ) );
			include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/carousel/gallery-popup.php';
		endif;
		?>
	</div>
</div>