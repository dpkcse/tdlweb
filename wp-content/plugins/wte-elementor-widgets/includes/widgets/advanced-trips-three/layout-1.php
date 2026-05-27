<?php
namespace WPTRAVELENGINEEB;

/**
 * Trip Card Layout - 1
 */
list( $settings, $trip, $results ) = $args;

$trip_id           = $trip->ID;
$is_featured       = wte_is_trip_featured( $trip_id );
$meta              = \wte_trip_get_trip_rest_metadata( $trip_id );
$image_size        = wte_array_get( $settings, 'image_size', 'trip-thumb-size' );
$image_custom_size = wte_array_get( $settings, 'image_custom_size', false );
$showPrice         = wte_array_get( $settings, 'showPrice', true );
$priceType         = wte_array_get( $settings, 'priceType', '3' );
$price_label       = wte_array_get( $settings, 'priceLabel', __( 'from', 'wptravelengine-elementor-widgets' ) );
$image_size        = 'custom' === $image_size && $image_custom_size ? Widget::wte_get_custom_image_size( $image_custom_size ) : $image_size;
$position          = wte_array_get( $settings, 'loc_position', 'top' );
$show_meta_data    = wte_array_get( $settings, 'showTripMeta', array( 'showDuration', 'showGroupSize', 'showDifficulty', 'showActivities' ) );
$rating_layout     = wte_array_get( $settings, 'rating_layout', '1' );
$showRating        = wte_array_get( $settings, 'showReviews', false );
$rating_position   = wte_array_get( $settings, 'rating_position', 'top' );
$showButton        = wte_array_get( $settings, 'showViewMoreButton', true );
$meta_data         = array( 'group-size', 'age-group', 'difficulty', 'activity', 'trip-types', 'altitude' );

$_meta_data = array();
foreach ( $meta_data as $item ) {
	$_meta_data[ $item ] = wptravelengineeb_get_trip_metadata( $trip_id, $item );
}

?>
<div class="wpte-card wpte-card--t-b" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	<div class="wpte-card__wrap">
		<div class="wpte-card__title-meta">
			<?php
			if ( 'top' === $position && wte_array_get( $settings, 'showLocation', false ) ) :
				$terms = wte_get_the_tax_term_list( $trip->ID, 'destination', '', ', ', '' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
					?>
					<div class="wpte-card__location">
						<?php wptravelengineeb_get_icon_by_slug( 'location' ); ?>
						<span><?php echo wp_kses_post( $terms ); ?></span>
					</div>
					<?php
				endif;
			endif;
			if ( wte_array_get( $settings, 'showTitle', true ) ) :
				?>
				<h2 class="wpte-card__title" itemprop="name">
					<a itemprop="url" href="<?php echo esc_url( get_the_permalink( $trip ) ); ?>"><?php echo esc_html( $trip->post_title ); ?></a>
				</h2>
				<?php
			endif;

			if ( $showRating && 'bottom' === $rating_position ) :
					\wptravelengineeb_get_rating( $trip_id, $rating_layout );
			endif;

			if ( 'bottom' === $position && wte_array_get( $settings, 'showLocation', false ) ) :
				$terms = wte_get_the_tax_term_list( $trip->ID, 'destination', '', ', ', '' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
					?>
					<span class="wpte-card__location">
						<?php wptravelengineeb_get_icon_by_slug( 'location' ); ?>
						<span><?php echo wp_kses_post( $terms ); ?></span>
					</span>
					<?php
				endif;
			endif;
			?>
		</div>
		<div class="wpte-card__media">
			<?php if ( wte_array_get( $settings, 'showFeaturedRibbon', false ) && $is_featured ) : ?>
				<div <?php $this->print_render_attribute_string( 'featured-ribbon' ); ?>>
					<span class="wpte-badge__text"><?php esc_html_e( 'Featured', 'wptravelengine-elementor-widgets' ); ?></span>
				</div>
			<?php endif; ?>
			<?php if ( wte_array_get( $settings, 'showDiscount', false ) && $meta->discount_percent ) : ?>
				<div <?php $this->print_render_attribute_string( 'discount-badge' ); ?>>
					<span class="wpte-badge__text">
						<span><?php echo isset( $meta->discount_label ) ? $meta->discount_label : sprintf( esc_html__( '%1$s%% Off', 'wptravelengine-elementor-widgets' ), (float) $meta->discount_percent ); ?></span>
					</span>
				</div>
			<?php endif; ?>
			<figure class="wpte-card__image">
				<?php $add_class = has_post_thumbnail( $trip ) ? '' : 'wpte-card__fallback-img'; ?>
				<a href="<?php echo esc_url( get_the_permalink( $trip ) ); ?>" class="<?php echo esc_attr( $add_class ); ?>">
					<?php
					if ( has_post_thumbnail( $trip ) ) :
						echo get_the_post_thumbnail( $trip, $image_size );
					endif;
					?>
				</a>
			</figure>
			<?php
			if ( $showRating && 'top' === $rating_position ) :
					\wptravelengineeb_get_rating( $trip_id, $rating_layout );
				endif;
			?>
			<?php if ( wte_array_get( $settings, 'showWishlist', false ) ) : ?>
				<?php wptravelengineeb_get_wishlist( $trip_id ); ?>
				<?php
			endif;
			$display_price = $meta->has_sale ? $meta->sale_price : $meta->price;
			if ( $showPrice && ! empty( $display_price ) && $display_price > 0 && $priceType !== '3' ) :
				?>
				<span <?php $this->print_render_attribute_string( 'price-data' ); ?>>
					<?php if ( wte_array_get( $settings, 'showStrikedPrice', true ) && $meta->has_sale ) : ?>
						<div class="striked-price">
							<?php
							if ( $price_label ) {
								echo '<label>' . esc_html( $price_label ) . '</label>';}
							?>
							<del><?php echo wte_esc_price( wte_get_formated_price_html( $meta->price ) ); ?></del>
						</div>
						<?php
					endif;
					if ( $showPrice ) :
						?>
						<ins class="actual-price"><?php echo wte_esc_price( wte_get_formated_price_html( $display_price ) ); ?></ins>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		</div>
		<div class="wpte-card__content">
			<div class="wpte-card__meta">
				<?php
				if ( in_array( 'showDuration', $show_meta_data ) && $meta->duration['days'] != 0 ) :
					$trip_duration_unit   = $meta->duration['duration_unit'];
					$trip_duration_nights = $meta->duration['nights'];
					$set_duration_types   = $settings['durationType'];
					$duration_label       = array();
					?>

					<div class="category trip-dur">
						<?php wptravelengineeb_get_icon_by_slug( 'duration' ); ?>
						<div class="wpte-card__meta-content">
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
							$duration_label = apply_filters( 'wptravelengine_trip_duration_arr', $duration_label, $trip->ID, $set_duration_types );
							?>
								<span class="wpte-card__meta-value"><?php echo esc_html( implode( ' - ', $duration_label ) ); ?></span>
						</div>
					</div>
					<span class="wpte-card__meta-seperator"></span>		
					<?php
				endif;

				if ( in_array( 'showDifficulty', $show_meta_data ) && ! empty( $_meta_data['difficulty'] ) ) :
					?>
					<div class="category trip-diff">
						<?php wptravelengineeb_get_icon_by_slug( 'difficulties' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['difficulty'] ); ?></span>
						</div>
					</div>
					<span class="wpte-card__meta-seperator"></span>		
					<?php
				endif;

				if ( in_array( 'showActivities', $show_meta_data ) && ! empty( $_meta_data['activity'] ) ) :
					?>
					<div class="category trip-act">
						<?php wptravelengineeb_get_icon_by_slug( 'activities' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-value">
								<?php
									echo esc_html( $_meta_data['activity'] );
								if ( gettype( $_meta_data['activity'] ) === 'integer' ) {
									esc_html_e( ' Activities', 'wptravelengine-elementor-widgets' );
								}
								?>
							</span>
						</div>
					</div>
					<span class="wpte-card__meta-seperator"></span>		
					<?php
				endif;

				if ( in_array( 'showGroupSize', $show_meta_data ) && ! empty( $_meta_data['group-size'] ) ) :
					?>
					<div class="category trip-group">
						<?php wptravelengineeb_get_icon_by_slug( 'group-size' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-value">
							<?php
							echo esc_html( $_meta_data['group-size'] );
							esc_html_e( ' People', 'wptravelengine-elementor-widgets' );
							?>
							</span>
						</div>
					</div>
					<span class="wpte-card__meta-seperator"></span>		
					<?php
				endif;

				if ( in_array( 'showAgeGroup', $show_meta_data ) && ! empty( $_meta_data['age-group'] ) ) :
					?>
					<div class="category trip-age">
						<?php wptravelengineeb_get_icon_by_slug( 'maximum-age' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['age-group'] ); ?></span>
						</div>
					</div>
					<span class="wpte-card__meta-seperator"></span>		
					<?php
				endif;

				if ( in_array( 'showTripType', $show_meta_data ) && ! empty( $_meta_data['trip-types'] ) ) :
					?>
					<div class="category trip-act">
						<?php wptravelengineeb_get_icon_by_slug( 'trip_types' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-value">
								<?php
									echo esc_html( $_meta_data['trip-types'] );
								if ( gettype( $_meta_data['trip-types'] ) === 'integer' ) {
									esc_html_e( ' Trip Types', 'wptravelengine-elementor-widgets' );
								}
								?>
							</span>
						</div>
					</div>
					<span class="wpte-card__meta-seperator"></span>		
					<?php
				endif;

				if ( in_array( 'showAltitude', $show_meta_data ) && ! empty( $_meta_data['altitude'] ) ) :
					?>
					<div class="category trip-altitude">
						<?php wptravelengineeb_get_icon_by_slug( 'altitude' ); ?>
						<div class="wpte-card__meta-content">
							<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['altitude'] ); ?></span>
						</div>
					</div>	
				<?php endif; ?>
			</div>
			<?php
			if ( $showPrice || $showButton ) {
				echo '<div class="wpte-card__price-wrapper">';}
				$display_price = $meta->has_sale ? $meta->sale_price : $meta->price;
			if ( $showPrice && ! empty( $display_price ) && $display_price > 0 && $priceType === '3' ) :
				?>
					<span class="wpte-card__price wpte-card__price--layout-3">
						<?php if ( wte_array_get( $settings, 'showStrikedPrice', true ) && $meta->has_sale ) : ?>
							<div class="striked-price">
								<?php
								if ( $price_label ) {
									echo '<label>' . esc_html( $price_label ) . '</label>';}
								?>
								<del><?php echo wte_esc_price( wte_get_formated_price_html( $meta->price ) ); ?></del>
							</div>
							<?php
						endif;
						if ( $showPrice ) :
							?>
							<ins class="actual-price"><?php echo wte_esc_price( wte_get_formated_price_html( $display_price ) ); ?></ins>
						<?php endif; ?>
					</span>
					<?php
				endif;
			if ( $showButton ) :
				?>
					<div class="wpte-card__button-wrap">
						<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" class="wpte-card__button"><?php echo esc_html( wte_array_get( $settings, 'viewMoreButtonText', __( 'View Details', 'wptravelengine-elementor-widgets' ) ) ); ?></a>
					</div>
					<?php
				endif;
			if ( $showPrice || $showButton ) {
				echo '</div>';
			} // End of wrapper .wpte-card__price-wrapper
			?>
		</div>

	</div>

</div>