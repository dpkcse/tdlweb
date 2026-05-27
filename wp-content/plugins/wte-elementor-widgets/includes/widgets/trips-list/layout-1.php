<?php
namespace WPTRAVELENGINEEB;

/**
 * Trip List Layout - 1
 */
list( $settings, $trip, $results, $index ) = $args;

$trip_id           = $trip->ID;
$image_size        = wte_array_get( $settings, 'image_size', 'trip-thumb-size' );
$image_custom_size = wte_array_get( $settings, 'image_custom_size', false );
$image_size        = 'custom' === $image_size && $image_custom_size ? Widget::wte_get_custom_image_size( $image_custom_size ) : $image_size;
$position          = wte_array_get( $settings, 'loc_position', 'top' );

?>
<div class="wpte-card wpte-card--l-r" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	<div class="wpte-card__wrap">
		<div class="wpte-card__media">
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
			<?php if ( wte_array_get( $settings, 'showWishlist', false ) ) : ?>
				<?php wptravelengineeb_get_wishlist( $trip_id ); ?>
			<?php endif; ?>
		</div>
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
			if ( wte_array_get( $settings, 'showTitle', true ) ) :
				?>
				<h2 class="wpte-card__title" itemprop="name">
					<a itemprop="url" href="<?php echo esc_url( get_the_permalink( $trip ) ); ?>"><?php echo esc_html( $trip->post_title ); ?></a>
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
		</div>
	</div>
</div>