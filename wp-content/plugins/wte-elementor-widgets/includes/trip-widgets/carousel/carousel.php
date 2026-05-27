<?php
/**
 * Carousel Widget Render.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

wp_enqueue_style( 'jquery-fancy-box' );
wp_enqueue_script( 'jquery-fancy-box' );
wp_enqueue_script( 'owl-carousel' );
wp_enqueue_style( 'owl-carousel' );

global $post;
global $wp_query;
$trip_id = $post->ID;
do_action( 'wp_travel_engine_feat_img_trip_galleries' );
$global_settings     = get_option( 'wp_travel_engine_settings', true );
$hide_featured_image = isset( $global_settings['feat_img'] ) && '1' === $global_settings['feat_img'];

$settings          = wptravelengine_settings();
$banner_layout     = $settings->get( 'trip_banner_layout', 'banner-default' );
$full_width_banner = $settings->get( 'display_banner_fullwidth', 'no' ) === 'yes';
$fullwidth_class   = $full_width_banner && 'banner-layout-1' === $banner_layout ? ' banner-layout-full' : '';
$is_main_slider    = isset( $is_main_slider ) && $is_main_slider;

$wpte_trip_images               = get_post_meta( $post->ID, 'wpte_gallery_id', true );
$global_settings                = get_option( 'wp_travel_engine_settings', array() );
$show_featured_image_in_gallery = ! isset( $global_settings['show_featured_image_in_gallery'] ) || 'yes' === $global_settings['show_featured_image_in_gallery'];

if ( ! is_array( $wpte_trip_images ) ) {
	$wpte_trip_images = array();
}

$hide_featured_image = isset( $global_settings['feat_img'] ) && '1' === $global_settings['feat_img'];

// Retrieve attributes value form elementor.
$attributes        = (object) $attributes;
$show_gallerypopup = isset( $attributes->{'showGalleryPopup'} ) ? $attributes->{'showGalleryPopup'} : true;
$show_videopopup   = isset( $attributes->{'showVideoPopup'} ) ? $attributes->{'showVideoPopup'} : true;
$popup_position    = isset( $attributes->{'popup_alignment'} ) ? $attributes->{'popup_alignment'} : 'bottom-left';

if ( ! ( 'banner-default' === $banner_layout || 'banner-layout-6' === $banner_layout ) ) {
	extract( array( $banner_layout, $wpte_trip_images, $show_gallerypopup, $show_videopopup, $fullwidth_class, $trip_id ) );
	include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/carousel/dynamic-banner.php';
	return;
}

if ( ! empty( $wpte_trip_images ) ) :?>
<div class="wpte-gallery-wrapper wte-elementor-widget">
	<?php
	if ( isset( $wpte_trip_images['enable'] ) && '1' === $wpte_trip_images['enable'] ) {
		if ( isset( $wpte_trip_images ) && '' !== $wpte_trip_images ) {
			unset( $wpte_trip_images['enable'] );
			?>
			<?php ob_start(); ?>
			<div class="wpte-trip-feat-img-gallery owl-carousel elementor-trip-main-carousel" >
				<?php
				if ( $show_featured_image_in_gallery && has_post_thumbnail( $post->ID ) ) {
					array_unshift( $wpte_trip_images, get_post_thumbnail_id( $post->ID ) );
				}
				foreach ( $wpte_trip_images as $image ) {
					$gallery_image_size = apply_filters( 'wp_travel_engine_trip_single_gallery_image_size', 'full' );
					if ( wp_is_mobile() ) {
						$gallery_image_size = 'large';
					}
					$_link     = wp_get_attachment_image_src( $image, $gallery_image_size );
					$image_alt = get_post_meta( $image, '_wp_attachment_image_alt', true );
					if ( empty( $image_alt ) ) {
						$image_alt = get_the_title( $image );
					}
					if ( isset( $_link[0] ) ) :
						?>
						<div class="item" data-thumb="<?php echo esc_url( $_link[0] ); ?>">
							<img alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" itemprop="image" src="<?php echo esc_url( $_link[0] ); ?>">
						</div>
						<?php
					endif;
				}
				?>
			</div>
			<?php
			$html = ob_get_clean();
			echo wp_kses_post( apply_filters( 'wpte_trip_gallery_images', $html, $wpte_trip_images ) );
		}
	} else {
		$featured_image_url = WP_TRAVEL_ENGINE_IMG_URL . '/public/css/images/single-trip-featured-img.jpg';
		$image_alt          = get_the_title();
		if ( has_post_thumbnail( $post->ID ) ) {
			$trip_feat_img_size         = apply_filters( 'wp_travel_engine_single_trip_feat_img_size', 'trip-single-size' );
			list( $featured_image_url ) = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $trip_feat_img_size );
			$image_alt                  = get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
			if ( empty( $image_alt ) ) {
				$image_alt = get_the_title( get_post_thumbnail_id( $post->ID ) );
			}
		}
		?>
		<div class="wpte-trip-feat-img">
			<img alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" itemprop="image" src="<?php echo esc_url( $featured_image_url ); ?>">
		</div>
		<?php
	}
	?>

	<?php
	if ( $show_gallerypopup === 'yes' || $show_videopopup === 'yes' ) :
		extract( array( $popup_position ) );
		include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/carousel/gallery-popup.php';
	endif;
	?>
</div>
	<?php
endif;
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
