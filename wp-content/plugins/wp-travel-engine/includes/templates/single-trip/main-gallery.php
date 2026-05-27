<?php
global $post;

// Retrieve settings and gallery images.
$wptravelengine_trip_images     = get_post_meta( $post->ID, 'wpte_gallery_id', true );
$show_image_gallery             = isset( $wptravelengine_trip_images['enable'] ) && '1' === $wptravelengine_trip_images['enable'];
$wptravelengine_settings        = wptravelengine_settings()->get();
$show_featured_image_in_gallery = ! isset( $wptravelengine_settings['show_featured_image_in_gallery'] ) || 'yes' === $wptravelengine_settings['show_featured_image_in_gallery'];
$gallery_autoplay               = $wptravelengine_settings['gallery_autoplay'] ?? 'no';
$hide_featured_image            = isset( $wptravelengine_settings['feat_img'] ) && '1' === $wptravelengine_settings['feat_img'];
$is_main_slider               ??= false;
$wptravelengine_trip_settings   = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
$enable_video_gallery           = $wptravelengine_trip_settings['enable_video_gallery'] ?? false;
$banner_layout                  = $wptravelengine_settings['trip_banner_layout'] ?? 'banner-default';
$default_banner_class           = ! $related_query && 'banner-default' === $banner_layout ? ' banner-layout-default' : ''; // Add default class for banner only.
$is_archive                     = is_archive() || ( isset( $_POST['action'] ) && 'wte_show_ajax_result' === $_POST['action'] );

// Determine appropriate image size based on context.
if ( $related_query || $is_archive ) {
	// Archive/related trips use smaller images (500x500) if show_original_size_image is disabled.
	$default_image_size = wptravelengine_toggled( $wptravelengine_settings['show_original_size_image'] ?? false ) ? 'full' : 'trip-thumb-size';
} elseif ( 'banner-default' === $banner_layout ) {
	$default_image_size = 'full';
} else {
	$default_image_size = 'large';
}

if ( wp_is_mobile() && ! $related_query ) {
	$default_image_size = 'large';
}

?>
<div class="wpte-gallery-wrapper<?php echo esc_attr( $default_banner_class ); ?>">
	<?php
	if ( isset( $wptravelengine_trip_images['enable'] ) && '1' === $wptravelengine_trip_images['enable'] ) {
		if ( ! empty( $wptravelengine_trip_images ) ) {
			unset( $wptravelengine_trip_images['enable'] );
			if ( $show_featured_image_in_gallery && has_post_thumbnail( $post->ID ) ) {
				$thumbnail_id = get_post_thumbnail_id( $post->ID );
				// Remove featured image from gallery if it exists at any position
				$wptravelengine_trip_images = array_diff( $wptravelengine_trip_images, array( $thumbnail_id ) );
				// Add featured image to the beginning
				array_unshift( $wptravelengine_trip_images, $thumbnail_id );
			}
			if ( ! empty( $wptravelengine_trip_images ) ) :
				$html          = '<div class="splide' . ( $is_main_slider ? ' single-trip-main-carousel' : '' )
									. ( $is_main_slider && 'yes' === $gallery_autoplay ? ' is-autoplay' : '' ) . '">';
				$html         .= '<div class="splide__track">';
				$gallery_class = 'splide__list';
				$html         .= '<div class="' . esc_attr( $gallery_class ) . '">';
				$image_index   = 0;
				foreach ( $wptravelengine_trip_images as $image ) {
					if ( is_wp_error( $image ) ) {
						continue;
					}
					$gallery_image_size = apply_filters( 'wp_travel_engine_trip_single_gallery_image_size', $default_image_size );
					$image_url          = wp_get_attachment_image_src( $image, $gallery_image_size );
					$image_alt          = get_post_meta( $image, '_wp_attachment_image_alt', true ) ?? get_the_title( $image );
					if ( $image_url ) {
						$loading       = ' loading="' . esc_attr( ( 0 === $image_index ) ? 'eager' : 'lazy' ) . '"';
						$fetchpriority = ( 0 === $image_index ) ? ' fetchpriority="high"' : '';

						$img_width  = ! empty( $image_url[1] ) ? ' width="' . esc_attr( $image_url[1] ) . '"' : '';
						$img_height = ! empty( $image_url[2] ) ? ' height="' . esc_attr( $image_url[2] ) . '"' : '';

						// Add clickable wrapper and data attributes for fancybox (only on single trip pages)
						$clickable_class = ( is_singular( 'trip' ) && ! $related_query ) ? ' wte-gallery-image-clickable' : '';
						$data_attrs      = '';
						if ( is_singular( 'trip' ) && ! $related_query ) {
							$data_attrs = ' data-gallery-index="' . esc_attr( $image_index ) . '"';
							// Also add data attribute with full image URL for fancybox
							$full_image_url = wp_get_attachment_image_url( $image, 'full' );
							if ( $full_image_url ) {
								$data_attrs .= ' data-full-image="' . esc_url( $full_image_url ) . '"';
							}
						}

						$html .= '<div class="splide__slide' . esc_attr( $clickable_class ) . '" data-thumb="' . esc_url( $image_url[0] ) . '"' . $data_attrs . ' role="group" aria-roledescription="' . esc_attr__( 'slide', 'wp-travel-engine' ) . '">';
						$html .= '<img alt="' . esc_attr( $image_alt ) . '" itemprop="image" src="' . esc_url( $image_url[0] ) . '"' . $loading . $fetchpriority . $img_width . $img_height . '>';
						$html .= '</div>';
					}
					++$image_index;
				}
				$html .= '</div> </div> </div>';
				echo wp_kses_post( apply_filters( 'wpte_trip_gallery_images', $html, $wptravelengine_trip_images ) );
				endif;
		}
	} elseif ( ! $hide_featured_image ) {
		if ( has_post_thumbnail( $post->ID ) ) :
			$featured_img_attrs = array(
				'loading'       => 'eager',
				'fetchpriority' => 'high',
			);
			$featured_image_url = wp_get_attachment_image(
				get_post_thumbnail_id( $post->ID ),
				'full',
				false,
				$featured_img_attrs
			);

			printf(
				'<div class="wpte-trip-feat-img">%s</div>',
				wp_kses( $featured_image_url, 'img' )
			);

			else :
				$featured_image_url = WP_TRAVEL_ENGINE_IMG_URL . '/public/css/images/single-trip-featured-img.jpg';
				$image_alt          = get_the_title( $post->ID );
				$placeholder_attrs  = ' loading="eager" fetchpriority="high"';
				?>
					<div class="wpte-trip-feat-img">
						<img alt="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>" itemprop="image"
						width="910" height="490"<?php echo $placeholder_attrs; ?>
								src="<?php echo esc_url( WP_TRAVEL_ENGINE_IMG_URL . '/public/css/images/single-trip-featured-img.jpg' ); ?>">
					</div>
				<?php
				endif;
	}
	if ( is_singular( 'trip' ) && ! $related_query ) :
		wp_enqueue_style( 'jquery-fancy-box' );
		wp_enqueue_script( 'jquery-fancy-box' );
		// Check if gallery button will be visible (has valid images)
		$has_gallery_button = false;
		if ( $show_image_gallery && ! empty( $wptravelengine_trip_images ) ) {
			$temp_images = $wptravelengine_trip_images;
			if ( isset( $temp_images['enable'] ) ) {
				unset( $temp_images['enable'] );
			}
			// Check if there are valid gallery images (same logic as below)
			$gallery_images_check = array_map(
				function ( $image ) {
					return is_wp_error( $image ) ? '' : array( 'src' => wp_get_attachment_image_url( $image, 'full' ) );
				},
				$temp_images
			);
			$gallery_images_check = array_filter( $gallery_images_check, fn ( $value ) => ! empty( $value['src'] ) );
			$has_gallery_button   = ! empty( $gallery_images_check );
		}

		// Check if video button will be visible (has video gallery data)
		$has_video_button = false;
		if ( $enable_video_gallery ) {
			$video_gallery    = get_post_meta( $post->ID, 'wpte_vid_gallery', true );
			$has_video_button = ! empty( $video_gallery );
		}

		// Output lazy loading script if gallery/video buttons OR banner layout images are present
		// Banner layouts use data-fancybox="gallery" attributes, so we need the script even without gallery button
		if ( $has_gallery_button || $has_video_button || $show_image_gallery || $enable_video_gallery ) :
			$random = wp_rand();
			?>
				<div class="wpte-gallery-container">
				<?php
				if ( $show_image_gallery && count( $wptravelengine_trip_images ) >= 1 ) :
					if ( isset( $wptravelengine_trip_images['enable'] ) ) {
						unset( $wptravelengine_trip_images['enable'] );
					}
					$gallery_images = array_map(
						function ( $image ) {
							return is_wp_error( $image ) ? '' : array( 'src' => wp_get_attachment_image_url( $image, 'full' ) );
						},
						$wptravelengine_trip_images
					);
					$gallery_images = array_filter( $gallery_images, fn ( $value ) => ! empty( $value['src'] ) );

					if ( ! empty( $gallery_images ) ) :
						?>
						<span class="wp-travel-engine-image-gal-popup">
						<a data-galtarget="#wte-image-gallary-popup-<?php echo esc_attr( $post->ID . $random ); ?>"
							data-variable="<?php echo esc_attr( 'wteimageGallery' . $random ); ?>"
							href="#wte-image-gallary-popup-<?php echo esc_attr( $post->ID . $random ); ?>"
							data-items="<?php echo esc_attr( wp_json_encode( array_values( $gallery_images ) ) ); ?>"
							class="wte-trip-image-gal-popup-trigger"><?php esc_html_e( 'Gallery', 'wp-travel-engine' ); ?>
						</a>
					</span>
					<?php endif; ?>
						<script type="text/javascript">
							document.addEventListener('DOMContentLoaded', function() {
								const galleryTriggers = document.querySelectorAll('.wte-trip-image-gal-popup-trigger')
								galleryTriggers.forEach(trigger => {
									trigger.addEventListener('click', () => {
										jQuery.fancybox.open(JSON.parse(trigger.getAttribute('data-items') || '[]'), {
											buttons: ['zoom', 'slideShow', 'fullScreen', 'close'],
										})
									})
								})
							})
						</script>
					<?php
					endif;
				if ( $enable_video_gallery ) {
					echo do_shortcode( '[wte_video_gallery label="Video"]' );
				}
				?>
				</div>
			<?php
			endif;
		endif;
	?>
</div>
