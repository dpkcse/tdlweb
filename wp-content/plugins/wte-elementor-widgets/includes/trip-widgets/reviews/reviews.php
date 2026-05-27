<?php
/**
 * Rating Widget Template.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */
global $post;
$attributes   = $this->get_settings_for_display();
$post_meta    = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
$review_title = isset( $post_meta['review']['review_title'] ) && '' !== $post_meta['review']['review_title'] ? $post_meta['review']['review_title'] : '';
?>
<div id="wte-reviews" class="post-data">
	<div class="content">
		<?php
		if ( ! empty( $review_title ) ) {
			echo '<h3>' . esc_attr( $review_title ) . '</h3>';
		}
		if ( ! defined( 'WTE_TRIP_REVIEW_VERSION' ) ) {
			?>
		<div class="wpte-info-block">
			<p>
				<?php
					echo wp_kses(
						sprintf(
							// translators: %1$s: opening anchor tag, %2$s: closing anchor tag.
							__( 'Trip - Reviews Widget requires WP Travel Engine - Trip Reviews to work. %1$sGet Trip Reviews extension now%2$s.', 'wptravelengine-elementor-widgets' ),
							'<a target="_blank" href="https://wptravelengine.com/plugins/trip-reviews/?utm_source=setting&amp;utm_medium=customer_site&amp;utm_campaign=setting_addon">',
							'</a>'
						),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
							),
						)
					);
				?>
			</p>
		</div>
			<?php
		} else {
			$obj = new Wte_Trip_Review_Init();
			// Check if the trip has reviews and if not then show the message.
			$args     = array(
				'post_id' => $post->ID,
				'status'  => 'approve',
				'orderby' => 'comment_date',
				'parent'  => 0,
			);
			$comments = get_comments( $args );

			if ( empty( $comments ) ) {
				echo esc_html__( 'Oops! No reviews found for this trip.', 'wptravelengine-elementor-widgets' );
				return;
			}

			do_action( 'wte_review_wrap_open' );
			do_action( 'wte_trip_review_header' );
			do_action( 'wte_review_sub_wrap_open' );
			do_action( 'wte_trip_total_reviews' );
			do_action( 'wte_trip_average_rating_from_wte' );
			do_action( 'wte_trip_overall_review' );
			do_action( 'wte_review_sub_wrap_close' );
			do_action( 'wte_write_review_btn' );
			do_action( 'wte_trip_review_schema_json' );
			do_action( 'wte_list_reviews' );
			do_action( 'wte_review_wrap_close' );
		}
		?>
	</div>
</div>
<?php
