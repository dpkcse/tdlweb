<?php
namespace WPTRAVELENGINEEB;

/**
 * Trip Card Layout - 6
 */
list( $settings, $trip, $results, $pax_label ) = $args;

$is_featured       = wte_is_trip_featured( $trip->ID );
$meta              = \wte_trip_get_trip_rest_metadata( $trip->ID );
$image_size        = wte_array_get( $settings, 'image_size', false );
$image_custom_size = wte_array_get( $settings, 'image_custom_size', false );
$image_size        = 'custom' === $image_size && $image_custom_size ? Widget::wte_get_custom_image_size( $image_custom_size ) : $image_size;
$meta_dir          = wte_array_get( $settings, 'meta_direction', false );
?>
<div class="category-trips-single wpte-layout-6">
	<div class="category-trips-single-inner-wrap">
		<?php if ( wte_array_get( $settings, 'layoutFilters.showFeaturedImage', true ) ) : ?>
			<div class="category-trip-fig <?php echo wte_array_get( $settings, 'layoutFilters.showPrice', true ) ? '' : ''; ?>">
				<a href="<?php echo esc_url( get_the_permalink( $trip ) ); ?>">
					<?php
					$size = apply_filters( 'wp_travel_engine_archive_trip_feat_img_size', 'destination-thumb-trip-size' );
					if ( has_post_thumbnail( $trip ) ) :
						echo get_the_post_thumbnail( $trip, $image_size );
					endif;
					?>
				</a>
				<?php if ( wte_array_get( $settings, 'layoutFilters.showFeaturedRibbon', false ) && $is_featured ) : ?>
					<div class="category-feat-ribbon">
						<span class="category-feat-ribbon-txt"><?php esc_html_e( 'Featured', 'wptravelengine-elementor-widgets' ); ?></span>
					</div>
					<?php
				endif;
				$display_price = $meta->has_sale ? $meta->sale_price : $meta->price;
				if ( wte_array_get( $settings, 'layoutFilters.showPrice', true ) && ! empty( $display_price ) && $display_price > 0 ) :
					?>
					<div class="wpte-trip-price-wrap">
					<div class="normal-price">
						<?php if ( wte_array_get( $settings, 'layoutFilters.showStrikedPrice', true ) && $meta->has_sale ) : ?>
							<span class="wpte-trip-price-label"><?php echo esc_html( wte_array_get( $settings, 'strikedPriceLabel', __( 'from', 'wptravelengine-elementor-widgets' ) ) ); ?></span>
							<del><?php echo wte_esc_price( wte_get_formated_price_html( $meta->price ) ); ?></del>
						<?php endif; ?>
					</div>
					<ins><?php echo wte_esc_price( wte_get_formated_price_html( $display_price ) ); ?></ins>
					</div>
					<?php
				endif;
				if ( wte_array_get( $settings, 'layoutFilters.showDiscount', false ) && $meta->discount_percent ) :
					?>
				<div class="discount-text-wrap">
					<span class="discount-percent"><?php echo isset( $meta->discount_label ) ? $meta->discount_label : sprintf( esc_html__( '%1$s%% Off', 'wptravelengine-elementor-widgets' ), (float) $meta->discount_percent ); ?></span>
				</div>
					<?php
				endif;

				if ( wte_array_get( $settings, 'layoutFilters.showWishlist', false ) ) {
					wptravelengineeb_get_wishlist( $trip_id );
				}

				if ( wte_array_get( $settings, 'layoutFilters.showReviews', false ) ) :
						\wptravelengineeb_get_rating( $trip_id, $rating_layout );
				endif;
				?>
			</div>
		<?php endif; ?>
		<div class="wpte-trip-details-wrap">
			<div class="wpte-trip-header-wrap">
				<?php
				if ( wte_array_get( $settings, 'layoutFilters.showLocation', false ) ) :
					$terms = wte_get_the_tax_term_list( $trip->ID, 'destination', '', ', ', '' );
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
						?>
						<span class="wpte-trip-meta wpte-trip-destination">
							<span class="wpte-icon-map-marker"></span>
							<span><?php echo wp_kses_post( $terms ); ?></span>
						</span>
						<?php
					endif;
				endif;
				?>
				<?php if ( wte_array_get( $settings, 'layoutFilters.showTitle', true ) ) : ?>
					<div class="wpte-trip-title-wrap">
						<h3 class="wpte-trip-title">
							<a itemprop="url" href="<?php echo esc_url( get_the_permalink( $trip ) ); ?>"><?php echo esc_html( $trip->post_title ); ?></a>
						</h3>
					</div>
				<?php endif; ?>
			</div>
			<div class="wpte-trip_meta-container">
				<div class="category-trip-meta-infos <?php echo $meta_dir === 'vertical' ? esc_attr( 'wpte-dir-vertical' ) : esc_attr( 'wpte-dir-horizontal' ); ?>">
					<?php
					if ( wte_array_get( $settings, 'layoutFilters.showDuration', false ) ) :
						$trip_duration_unit   = $meta->duration['duration_unit'];
						$trip_duration_nights = $meta->duration['nights'];
						$set_duration_types   = $settings['durationType'];
						$duration_label       = array();
						?>
												<span class="category-trip-meta-info">
							<?php if ( $meta->duration['days'] != 0 ) : ?>
								<span class="category-trip-meta-info-icon">
									<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M21 10.3018H3M16 2.30176V6.30176M8 2.30176V6.30176M7.8 22.3018H16.2C17.8802 22.3018 18.7202 22.3018 19.362 21.9748C19.9265 21.6872 20.3854 21.2282 20.673 20.6637C21 20.022 21 19.1819 21 17.5018V9.10176C21 7.4216 21 6.58152 20.673 5.93979C20.3854 5.3753 19.9265 4.91636 19.362 4.62874C18.7202 4.30176 17.8802 4.30176 16.2 4.30176H7.8C6.11984 4.30176 5.27976 4.30176 4.63803 4.62874C4.07354 4.91636 3.6146 5.3753 3.32698 5.93979C3 6.58152 3 7.4216 3 9.10176V17.5018C3 19.1819 3 20.022 3.32698 20.6637C3.6146 21.2282 4.07354 21.6872 4.63803 21.9748C5.27976 22.3018 6.11984 22.3018 7.8 22.3018Z" stroke="currentColor" stroke-width="1.39" stroke-linecap="round" stroke-linejoin="round"></path>
									</svg>
								</span>
								<div>
									<span class="category-trip-meta-info-label"><?php echo esc_html( wte_array_get( $settings, 'durationLabel', __( 'Duration', 'wptravelengine-elementor-widgets' ) ) ); ?>
									</span>
									<span class="category-trip-meta-info-value">
										<?php
										if ( ( 'days' !== $trip_duration_unit ) || ( 'days' === $trip_duration_unit && $meta->duration['days'] && in_array( $set_duration_types, array( 'both', 'days' ) ) ) ) {
											$duration_label[] = sprintf(
												_nx( '%1$d %2$s', '%1$d %3$s', (int) $meta->duration['days'], 'trip duration', 'wptravelengine-elementor-widgets' ),
												(int) $meta->duration['days'],
												$results['duration'][ $trip_duration_unit ][0],
												$results['duration'][ $trip_duration_unit ][1]
											);
										}
										if ( 'days' === $trip_duration_unit && $trip_duration_nights && in_array( $set_duration_types, array( 'both', 'nights' ) ) ) {
											$duration_label[] = sprintf( _nx( '%1$d Night', '%1$d Nights', (int) $trip_duration_nights, 'trip duration night', 'wptravelengine-elementor-widgets' ), (int) $trip_duration_nights );
										}
										?>
											<?php echo esc_html( implode( ' - ', $duration_label ) ); ?>
									</span>
								</div>
								<?php endif; ?>
							</span>
						<?php
					endif;

					if ( wte_array_get( $settings, 'layoutFilters.showTripType', false ) ) :
						$terms = wte_get_the_tax_term_list( $trip->ID, 'trip_types', '', ', ', '' );
						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
							?>
							<span class="wpte-trip-meta category-trip-types">
								<span class="wpte-icon-trip-types"></span>
								<span><?php echo wp_kses_post( $terms ); ?></span>
							</span>
							<?php
						endif;
					endif;

					if ( wte_array_get( $settings, 'layoutFilters.showGroupSize', false ) && (int) $meta->min_pax ) :
						?>
						<span class="category-trip-meta-info">
							<span class="category-trip-meta-info-icon">
								<span class="wpte-icon-users"></span>
							</span>
							<div>
								<span class="category-trip-meta-info-label"><?php echo esc_html( wte_array_get( $settings, 'groupSizeLabel', __( 'Group Size', 'wptravelengine-elementor-widgets' ) ) ); ?>
								</span>
								<span class="category-trip-meta-info-value">
									<?php
									/* translators: 1: Pax count, 2: Person label */
									printf( esc_html__( '%1$s %2$s', 'wptravelengine-elementor-widgets' ), $meta->max_pax ? (int) $meta->min_pax . '-' . (int) $meta->max_pax : (int) $meta->min_pax, $pax_label );
									?>
								</span>
							</div>
						</span>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( wte_array_get( $settings, 'layoutFilters.showViewMoreButton', true ) ) : ?>
			<div class="wpte-trip-btn-wrap">
				<a href="<?php echo esc_url( get_the_permalink( $trip->ID ) ); ?>" class="wpte-trip-explore-btn"><?php echo esc_html( wte_array_get( $settings, 'viewMoreButtonText', __( 'View Details', 'wptravelengine-elementor-widgets' ) ) ); ?></a>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
