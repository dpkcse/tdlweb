<?php
/**
 *
 * @since 6.3.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var string $banner_layout
 * @var array $list_images List of image sizes.
 * @var bool $show_image_gallery
 * @var bool $show_video_gallery
 * @var bool $full_width_banner Is full width banner enabled?
 */

$fullwidth_class = $full_width_banner && 'banner-layout-1' === $banner_layout ? ' banner-layout-full' : '';

$fancybox_images = array();
foreach ( array_values( $list_images ) as $image ) {
	$url = wp_get_attachment_image_url( $image, 'full' );
	if ( $url ) {
		$fancybox_images[] = array( 'src' => $url );
	}
}
?>
<div class="wpte-gallery-wrapper <?php echo esc_attr( $banner_layout ); ?>" data-images="<?php echo esc_attr( wp_json_encode( $fancybox_images ) ); ?>">
	<div class="wpte-multi-banner-layout<?php echo esc_attr( $fullwidth_class ); ?>">
		<?php
		/**
		 * Use this filter to generate markup for images.
		 *
		 * @param $list_images List of attachment IDs.
		 */
		$list_images = apply_filters( 'wptravelengine_trip_dynamic_banner_list_images', $list_images, $banner_layout, $show_image_gallery, $show_video_gallery );
		foreach ( $list_images as $image ) {
			if ( is_numeric( $image ) ) {
				continue;
			}
			echo wp_kses_post( $image );
		}
		?>
	</div>
	<?php
	if ( $show_image_gallery || $show_video_gallery ) {
		wptravelengine_get_template(
			'single-trip/banner-layouts/list-gallery.php',
			compact( 'show_image_gallery', 'show_video_gallery' )
		);
	}
	?>
</div>

