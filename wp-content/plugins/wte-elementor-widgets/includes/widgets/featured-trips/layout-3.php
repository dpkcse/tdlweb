<?php

/**
 * Layout 1 markup
 */

namespace WPTRAVELENGINEEB;

list($settings, $results) = $args;

global $post;
$trip_id           = $post->ID;
$meta              = \wte_trip_get_trip_rest_metadata( $trip_id );
$post_title        = get_the_title();
$image_size        = wte_array_get( $attributes, 'image_size', 'full' );
$image_custom_size = wte_array_get( $attributes, 'image_custom_size', false );
$image_size        = 'custom' === $image_size && $image_custom_size ? Widget::wte_get_custom_image_size( $image_custom_size ) : $image_size;
$showPrice         = wte_array_get( $attributes, 'showPrice', true );
$priceType         = wte_array_get( $attributes, 'priceType', '1' );
$price_label       = wte_array_get( $attributes, 'priceLabel', __( 'from', 'wptravelengine-elementor-widgets' ) );
$position          = wte_array_get( $attributes, 'loc_position', 'top' );
$show_meta_data    = wte_array_get( $attributes, 'showTripMeta', array( 'showDuration', 'showDifficulty', 'showActivities', 'showAltitude' ) );
$meta_data         = array( 'group-size', 'age-group', 'difficulty', 'activity', 'trip-types', 'altitude' );
$rating_layout     = wte_array_get( $settings, 'rating_layout', '1' );
$showRating        = wte_array_get( $settings, 'showReviews', false );

$_meta_data = array();
foreach ( $meta_data as $item ) {
	$_meta_data[ $item ] = wptravelengineeb_get_trip_metadata( $trip_id, $item );
}
?>
<div <?php $this->print_render_attribute_string( 'wpte-card' ); ?> itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	<div class="wpte-card__media-wrap wpte-card--overlap wpte-card--overlap-stack">
		<div class="wpte-card__media">
			<?php if ( wte_array_get( $settings, 'showFeaturedRibbon', false ) ) : ?>
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
				<?php $class = has_post_thumbnail( $trip_id ) ? '' : 'wpte-card__fallback-img'; ?>
				<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" class="<?php echo esc_attr( $class ); ?>">
					<?php
					if ( has_post_thumbnail( $trip_id ) ) :
						echo get_the_post_thumbnail( $trip_id, $image_size );
					endif;
					?>
				</a>
				<?php
				if ( wte_array_get( $settings, 'showReviews', false ) ) :
					echo \wte_get_the_trip_reviews( $trip_id );
				endif;
				?>
			</figure>

			<?php if ( wte_array_get( $settings, 'showWishlist', false ) ) : ?>
				<?php wptravelengineeb_get_wishlist( $trip_id ); ?>
			<?php endif; ?>

			<?php
			$display_price = $meta->has_sale ? $meta->sale_price : $meta->price;
			if ( $showPrice && $priceType !== '3' && ! empty( $display_price ) && $display_price > 0 ) :
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
						<ins class="actual-price"><?php echo wte_esc_price( wte_get_formated_price_html( $meta->has_sale ? $meta->sale_price : $meta->price ) ); ?></ins>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		</div>
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
						$duration_label = apply_filters( 'wptravelengine_trip_duration_arr', $duration_label, $trip_id, $set_duration_types );
						?>
						<span class="wpte-card__meta-title"><?php \esc_html_e( 'Duration', 'wptravelengine-elementor-widgets' ); ?></span>
						<span class="wpte-card__meta-value"><?php echo esc_html( implode( ' - ', $duration_label ) ); ?></span>
					</div>
				</div>
				<?php
			endif;

			if ( in_array( 'showDifficulty', $show_meta_data ) && ! empty( $_meta_data['difficulty'] ) ) :
				?>
				<div class="category trip-diff">
					<?php wptravelengineeb_get_icon_by_slug( 'difficulties' ); ?>
					<div class="wpte-card__meta-content">
						<span class="wpte-card__meta-title"><?php esc_html_e( 'Difficulty', 'wptravelengine-elementor-widgets' ); ?></span>
						<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['difficulty'] ); ?></span>
					</div>
				</div>
				<?php
			endif;

			if ( in_array( 'showActivities', $show_meta_data ) && ! empty( $_meta_data['activity'] ) ) :
				?>
				<div class="category trip-act">
					<?php wptravelengineeb_get_icon_by_slug( 'activities' ); ?>
					<div class="wpte-card__meta-content">
						<span class="wpte-card__meta-title"><?php esc_html_e( 'Activity', 'wptravelengine-elementor-widgets' ); ?></span>
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
				<?php
			endif;

			if ( in_array( 'showGroupSize', $show_meta_data ) && ! empty( $_meta_data['group-size'] ) ) :
				?>
				<div class="category trip-group">
					<?php wptravelengineeb_get_icon_by_slug( 'group-size' ); ?>
					<div class="wpte-card__meta-content">
						<span class="wpte-card__meta-title"><?php esc_html_e( 'Group Size', 'wptravelengine-elementor-widgets' ); ?></span>
						<span class="wpte-card__meta-value">
						<?php
						echo esc_html( $_meta_data['group-size'] );
															esc_html_e( ' People', 'wptravelengine-elementor-widgets' )
						?>
															</span>
					</div>
				</div>
				<?php
			endif;

			if ( in_array( 'showAgeGroup', $show_meta_data ) && ! empty( $_meta_data['age-group'] ) ) :
				?>
				<div class="category trip-age">
					<?php wptravelengineeb_get_icon_by_slug( 'maximum-age' ); ?>
					<div class="wpte-card__meta-content">
						<span class="wpte-card__meta-title"><?php esc_html_e( 'Age Group', 'wptravelengine-elementor-widgets' ); ?></span>
						<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['age-group'] ); ?></span>
					</div>
				</div>
				<?php
			endif;

			if ( in_array( 'showTripType', $show_meta_data ) && ! empty( $_meta_data['trip-types'] ) ) :
				?>
				<div class="category trip-act">
					<?php wptravelengineeb_get_icon_by_slug( 'trip_types' ); ?>
					<div class="wpte-card__meta-content">
						<span class="wpte-card__meta-title"><?php esc_html_e( 'Trip Type', 'wptravelengine-elementor-widgets' ); ?></span>
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
				<?php
			endif;

			if ( in_array( 'showAltitude', $show_meta_data ) && ! empty( $_meta_data['altitude'] ) ) :
				?>
				<div class="category trip-altitude">
					<?php wptravelengineeb_get_icon_by_slug( 'altitude' ); ?>
					<div class="wpte-card__meta-content">
						<span class="wpte-card__meta-title"><?php esc_html_e( 'Altitude', 'wptravelengine-elementor-widgets' ); ?></span>
						<span class="wpte-card__meta-value"><?php echo esc_html( $_meta_data['altitude'] ); ?></span>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="wpte-card__content-wrap">
		<div class="wpte-card__content">
			<?php
			if ( 'top' === $position && wte_array_get( $settings, 'showLocation', false ) ) :
				$terms = wte_get_the_tax_term_list( $trip_id, 'destination', '', ', ', '' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
					?>
					<div class="wpte-card__location">
						<?php wptravelengineeb_get_icon_by_slug( 'location' ); ?>
						<span><?php echo wp_kses_post( $terms ); ?></span>
					</div>
					<?php
				endif;
			endif;
			if ( wte_array_get( $settings, 'showSubTitle', true ) ) :
				?>
				<span class="wpte-card__sub-title">
					<span><?php echo esc_html( wte_array_get( $settings, 'showSubTitleText', __( 'TRIP OF THE MONTH', 'wptravelengine-elementor-widgets' ) ) ); ?></span>
				</span>
				<?php
			endif;
			if ( wte_array_get( $settings, 'showTitle', true ) ) :
				?>
				<h2 class="wpte-card__title" itemprop="name">
					<a itemprop="url" href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>"><?php echo esc_html( $post_title ); ?></a>
				</h2>
				<?php
				endif;
			if ( 'bottom' === $position && wte_array_get( $settings, 'showLocation', false ) ) :
				$terms = wte_get_the_tax_term_list( $trip_id, 'destination', '', ', ', '' );
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

			<div class="wpte-flex">
				<?php
				if ( $showRating ) :
					\wptravelengineeb_get_rating( $trip_id, $rating_layout );
						endif;
				?>
				<?php if ( $showPrice && $priceType === '3' && ! empty( $display_price ) && $display_price > 0 ) : ?>
					<div class="wpte-card__price wpte-card__price--layout-3">
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
					</div>
				<?php endif; ?>
			</div>
			<?php if ( wte_array_get( $settings, 'showViewMoreButton', true ) ) : ?>
				<div class="wpte-card__button-wrap">
					<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" class="wpte-card__button"><?php echo esc_html( wte_array_get( $settings, 'viewMoreButtonText', __( 'View Details', 'wptravelengine-elementor-widgets' ) ) ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>