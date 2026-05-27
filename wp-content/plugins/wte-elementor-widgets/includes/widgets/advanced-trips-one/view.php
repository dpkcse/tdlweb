<?php
/**
 * Trip Card Layout - 1
 *
 * @category Addon
 * @package  WPTRAVELENGINEEB
 * @author   WP Travel Engine <support@wptravelengine.com>
 * @license  GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link     http://wptravelengine.com
 * @since    1.0.0
 */

namespace WPTRAVELENGINEEB;

use WPTRAVELENGINEEB\Widget;

list( $settings, $trip, $results, $index ) = $args;

$trip_id           = $trip->ID;
$is_featured       = wte_is_trip_featured( $trip_id );
$meta              = \wte_trip_get_trip_rest_metadata( $trip_id );
$image_size        = wte_array_get( $settings, 'image_size', 'trip-thumb-size' );
$image_custom_size = wte_array_get( $settings, 'image_custom_size', false );
$showPrice         = wte_array_get( $settings, 'showPrice', true );
$priceType         = wte_array_get( $settings, 'priceType', '1' );
$layout_data       = wte_array_get( $settings, 'cardlayout', '1' );
$rating_layout     = wte_array_get( $settings, 'rating_layout', '1' );
$showRating        = wte_array_get( $settings, 'showReviews', false );
$rating_position   = wte_array_get( $settings, 'rating_position', 'top' );
$price_label       = wte_array_get( $settings, 'priceLabel', __( 'from', 'wptravelengine-elementor-widgets' ) );
$image_size        = 'custom' === $image_size && $image_custom_size ? Widget::wte_get_custom_image_size( $image_custom_size ) : $image_size;
$position          = wte_array_get( $settings, 'loc_position', 'top' );
$show_meta_data    = wte_array_get( $settings, 'showTripMeta', array( 'showDuration', 'showDifficulty', 'showActivities' ) );

$meta_data = array( 'group-size', 'age-group', 'difficulty', 'activity', 'trip-types', 'altitude' );

$_meta_data = array();
foreach ( $meta_data as $item ) {
	$_meta_data[ $item ] = wptravelengineeb_get_trip_metadata( $trip_id, $item );
}

?>
<div class="wpte-card wpte-card--t-b" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	<div class="wpte-card__wrap">
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
			if ( $showPrice && $priceType !== '3' && ! empty( $display_price ) && $display_price > 0 ) :
				?>
				<span <?php $this->print_render_attribute_string( 'price-data' ); ?>>
					<?php if ( wte_array_get( $settings, 'showStrikedPrice', true ) && $meta->has_sale ) : ?>
						<div class="striked-price">
							<?php
							if ( $price_label ) {
								echo '<label>' . esc_html( $price_label ) . '</label>';
							}
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
		<?php if ( '4' === $layout_data ) : ?>
			<div class="wpte-card__content-wrapper">
				<div class="wpte-card__counter">
					<span><?php echo esc_html( $index ); ?></span>
				</div>
		<?php endif; ?>

			<div class="wpte-card__content">
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

				if ( $showRating && 'bottom' === $rating_position ) :
					\wptravelengineeb_get_rating( $trip_id, $rating_layout );
				endif;

				?>
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
								esc_html_e( ' People', 'wptravelengine-elementor-widgets' );
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

					<?php
					// Custom Filters meta (support multiple selected taxonomies)
					$custom_taxonomies = (array) wte_array_get( $settings, 'customFilterType', array() );

					if ( in_array( 'showCustom', $show_meta_data ) && ! empty( $custom_taxonomies ) ) :
						$filters_option = get_option( 'wte_custom_filters', array() );
						foreach ( $custom_taxonomies as $custom_taxonomy ) :
							if ( empty( $custom_taxonomy ) ) {
								continue;
							}

							if ( ! taxonomy_exists( $custom_taxonomy ) ) {
								continue;
							}

							$custom_terms = get_the_terms( $trip_id, $custom_taxonomy );
							if ( empty( $custom_terms ) || is_wp_error( $custom_terms ) ) {
								continue;
							}

							// Determine display value: single term name or count for multiple
							$custom_value = count( $custom_terms ) > 1 ? count( $custom_terms ) : $custom_terms[0]->name;

							// Resolve label from custom filters option; fallback to prettified taxonomy slug
							$custom_label = ucwords( str_replace( array( '-', '_' ), ' ', $custom_taxonomy ) );
							if ( is_array( $filters_option ) && isset( $filters_option[ $custom_taxonomy ]['label'] ) ) {
								$custom_label = $filters_option[ $custom_taxonomy ]['label'];
							}

							?>
							<div class="category trip-custom-filter">
								<svg width="30" height="30" viewBox="0 0 30 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<g clip-path="url(#clip0_8360_736)">
									<path d="M22.625 28.25L23.9375 29.5625C24.1875 29.8125 24.5625 30 25 30C25.375 30 25.75 29.875 26.0625 29.5625L29.5625 26.0625C29.8125 25.8125 30 25.4375 30 25C30 24.625 29.875 24.25 29.5625 23.9375L21.6875 16.125L16.0625 21.75L18.3125 24L20.5625 21.75C20.8125 21.5 21.25 21.5 21.5 21.75C21.75 22 21.75 22.4375 21.5 22.6875L19.25 24.9375L21.625 27.3125L25.0625 23.875C25.3125 23.625 25.75 23.625 26 23.875C26.25 24.125 26.25 24.5625 26 24.8125L22.625 28.25Z" fill="currentColor"/>
									<path d="M6 11.625L8.25 13.875L13.875 8.25L6.0625 0.4375C5.75 0.125 5.375 0 5 0C4.625 0 4.25 0.125 3.9375 0.4375L0.4375 3.9375C0.125 4.25 0 4.625 0 5C0 5.375 0.125 5.75 0.4375 6.0625L1.75 7.375L5.1875 3.9375C5.4375 3.6875 5.875 3.6875 6.125 3.9375C6.375 4.1875 6.375 4.625 6.125 4.875L2.6875 8.3125L5.0625 10.6875L7.3125 8.4375C7.5625 8.1875 8 8.1875 8.25 8.4375C8.5 8.6875 8.5 9.125 8.25 9.375L6 11.625Z" fill="currentColor"/>
									<path d="M25.1875 0.25C24.8125 -0.125 24.1875 -0.125 23.8125 0.25L21.875 2.1875L27.75 8.0625L29.6875 6.125C29.875 6 30 5.75 30 5.5C30 5.25 29.875 5 29.75 4.8125L25.1875 0.25Z" fill="currentColor"/>
									<path d="M2.35107 21.7675L20.954 3.16452L26.8309 9.04146L8.22801 27.6444L2.35107 21.7675Z" fill="currentColor"/>
									<path d="M1.18771 29.9375L6.93771 28.25L1.68771 23L0.00020998 28.75C-0.12479 29.0625 0.000209976 29.4375 0.25021 29.6875C0.50021 30 0.87521 30.0625 1.18771 29.9375Z" fill="currentColor"/>
									</g>
									<defs>
									<clipPath id="clip0_8360_736">
									<rect width="30" height="30" fill="white"/>
									</clipPath>
									</defs>
								</svg>
								<div class="wpte-card__meta-content">
									<span class="wpte-card__meta-title"><?php echo esc_html( $custom_label ); ?></span>
									<span class="wpte-card__meta-value">
										<?php
										echo esc_html( $custom_value );
										if ( is_int( $custom_value ) ) {
											echo ' ' . sprintf(
												_n( '%s', '%s', $custom_value, 'wptravelengine-elementor-widgets' ),
												esc_html( $custom_label )
											);
										}
										?>
									</span>
								</div>
							</div>
							<?php
						endforeach;
					endif;
					?>
				</div>
				<?php if ( $showPrice && $priceType === '3' && ! empty( $display_price ) && $display_price > 0 ) : ?>
					<div class="wpte-card__price wpte-card__price--layout-3">
						<?php if ( wte_array_get( $settings, 'showStrikedPrice', true ) && $meta->has_sale ) : ?>
							<div class="striked-price">
								<?php
								if ( $price_label ) {
									echo '<label>' . esc_html( $price_label ) . '</label>';
								}
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

		<?php
		if ( '4' === $layout_data ) {
			echo '</div>';}
		?>
	</div>
</div>
