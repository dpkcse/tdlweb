<div class="wpte-gallery-container <?php echo esc_attr( $popup_position ); ?>">
	<?php
	global $post;
	$random                   = wp_rand();
	$wp_travel_engine_setting = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
	$wpte_trip_images         = get_post_meta( $post->ID, 'wpte_gallery_id', true );
	if ( isset( $wpte_trip_images['enable'] ) && '1' === $wpte_trip_images['enable'] ) {
		if ( count( $wpte_trip_images ) > 1 ) {
			unset( $wpte_trip_images['enable'] );
			?>
			<?php if ( $show_gallerypopup ) : ?>
			<span class="wp-travel-engine-image-gal-popup">
				<a
					data-galtarget="#wte-image-gallary-popup-<?php echo esc_attr( $post->ID . $random ); ?>"
					data-variable="<?php echo esc_attr( 'wteimageGallery' . $random ); ?>"
					href="#wte-image-gallary-popup-<?php echo esc_attr( $post->ID . $random ); ?>"
					class="wte-trip-image-gal-popup-trigger"><?php echo esc_html_e( 'Gallery', 'wptravelengine-elementor-widgets' ); ?>
				</a>
			</span>
				<?php
			endif;
			$_image_size    = wp_is_mobile() ? 'large' : 'full';
			$gallery_images = array_map(
				function ( $image ) use ( $_image_size ) {
					return array( 'src' => wp_get_attachment_image_url( $image, $_image_size ) );
				},
				$wpte_trip_images
			);
			?>
			<script type="text/javascript">
				var <?php echo 'wtevideoGallery' . $random; ?> = <?php echo wp_json_encode( array_values( $gallery_images ) ); ?>;
				jQuery(function($){
					$('.wte-trip-image-gal-popup-trigger').on( 'click', function(){
						jQuery.fn.fancybox && $.fancybox.open(<?php echo 'wtevideoGallery' . $random; ?>, {
							buttons: [
								"zoom",
								"slideShow",
								"fullScreen",
								"close"
							]
						});
					});
				});
			</script>
			<?php
		}
	}
	if ( ! empty( $wp_travel_engine_setting['enable_video_gallery'] ) && $show_videopopup ) {
		global $post;
		$_post_id = is_object( $post ) && isset( $post->ID ) ? $post->ID : false;
		$atts     = '';
		$atts     = shortcode_atts(
			array(
				'title'   => false,
				'trip_id' => $_post_id,
				'type'    => 'popup',
				'label'   => esc_html__( 'Video', 'wptravelengine-elementor-widgets' ),
			),
			$atts,
			'wte_video_gallery'
		);
		// Bail if no trip ID found.
		if ( ! $atts['trip_id'] ) {
			esc_html_e( 'No Trip ID supplied. Gallery Unavailable.', 'wptravelengine-elementor-widgets' );
			$output = ob_get_clean();
			return $output;
		}
		$video_gallery = get_post_meta( $atts['trip_id'], 'wpte_vid_gallery', true );
		if ( ! empty( $video_gallery ) ) {
			if ( 'popup' === $atts['type'] ) {
				wp_enqueue_script( 'jquery-fancy-box' );
				wp_enqueue_style( 'jquery-fancy-box' );
				if ( $atts['title'] ) :
					?>
						<h3><?php echo esc_html( $atts['title'] ); ?></h3>
					<?php
				endif;
				if ( 'Video' === $atts['label'] ) {
					$atts['label'] = __( 'Video', 'wptravelengine-elementor-widgets' );
				}
				if ( ! empty( $video_gallery ) ) :
					$random = wp_rand();
					?>
					<span class="wp-travel-engine-vid-gal-popup">
						<a
							data-galtarget="#wte-video-gallary-popup-<?php echo esc_attr( $atts['trip_id'] . $random ); ?>"
							data-variable="<?php echo esc_attr( 'wtevideoGallery' . $random ); ?>"
							href="#wte-video-gallary-popup-<?php echo esc_attr( $atts['trip_id'] . $random ); ?>"
							class="wte-trip-vidgal-popup-trigger"><?php echo esc_html( $atts['label'] ); ?></a>
					</span>
					<?php
					$slides = array();
					foreach ( $video_gallery as $key => $gallery_item ) :
						$video_id  = $gallery_item['id'];
						$video_url = 'youtube' === $gallery_item['type'] ? '//www.youtube.com/watch?v=' . $video_id : '//vimeo.com/' . $video_id;
						$slides[]  = array( 'src' => $video_url );
					endforeach;
					?>
					<script type="text/javascript">
						var <?php echo 'wtevideoGallery' . $random; ?> = <?php echo wp_json_encode( $slides ); ?>;
						jQuery(function($){
							$('.wte-trip-vidgal-popup-trigger').on( 'click', function(){
								jQuery.fn.fancybox && $.fancybox.open(<?php echo 'wtevideoGallery' . $random; ?>, {
									buttons: [
										"zoom",
										"slideShow",
										"fullScreen",
										"close"
									]
								});
							});
						});
					</script>
					<?php
				endif;
			} elseif ( 'slider' === $atts['type'] ) {
				wte_get_template(
					'single-trip/gallery-video-slider.php',
					array(
						'label'   => $atts['label'],
						'title'   => $atts['title'],
						'trip_id' => $atts['trip_id'],
						'gallery' => $video_gallery,
					)
				);
			}
		}
	}
	?>
</div>