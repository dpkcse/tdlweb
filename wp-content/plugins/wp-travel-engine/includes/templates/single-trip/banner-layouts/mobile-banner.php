<?php
/**
 * Mobile Banner Layout.
 *
 * This template is also used for desktop banner layout 1.
 *
 * @since 6.3.3
 */
/**
 * @var array $list_images List of image sizes.
 * @var string $banner_layout
 * @var bool $full_width_banner
 */
$show_image_gallery = wptravelengine_get_template_arg( 'show_image_gallery', true );
if ( ! $show_image_gallery ) {
	$banner_layout = 'banner-layout-1';
}
$fullwidth_class = $full_width_banner && 'banner-layout-1' === $banner_layout ? ' banner-layout-full' : '';
$image_size      = 'banner-layout-1' === $banner_layout ? 'full' : 'large'; // If it is desktop banner layout 1 then we need to show full size image.

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
		<div class="wpte-trip-feat-img">
			<?php
			the_post_thumbnail( $image_size );
			?>
		</div>
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