<?php

namespace WPTRAVELENGINEEB;

/**
 * Trips Destination/Activities
 */
list($settings, $term_object, $results) = $args;
$image_size                             = wte_array_get( $settings, 'image_size', 'medium_large' );
$img_custom_size                        = wte_array_get( $settings, 'image_custom_size', false );
$image_size                             = 'custom' === $image_size && $img_custom_size ? Widget::wte_get_custom_image_size( $img_custom_size ) : $image_size;
$thumbnail                              = wp_get_attachment_image_src( $term_object->thumbnail, $image_size );
$image_title                            = get_the_title( $term_object->thumbnail );
$alt_text                               = get_post_meta( $term_object->thumbnail, '_wp_attachment_image_alt', true );
$alt_attribute                          = $alt_text ? $alt_text : $image_title;
$headingTag                             = wte_array_get( $settings, 'headingTag', 'h3' );
$show_trip_counts                       = wte_array_get( $settings, 'showTripCounts', false );
$count_label                            = wte_array_get( $settings, 'countLabel', 'Trip|Trips' );

?>
<div class="wpte-card" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	<div class="wpte-card__wrap wpte-card--overlap wpte-card--overlap-stack">
		<div class="wpte-card__media">
			<figure class="wpte-card__image">
				<?php $add_class = $thumbnail ? 'wpte-card__has-img' : 'wpte-card__fallback-img'; ?>
				<a href="<?php echo esc_url( $term_object->link ); ?>" class="<?php echo esc_attr( $add_class ); ?>">
					<?php if ( $thumbnail ) { ?> 
						<img src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="<?php echo esc_attr( $alt_attribute ); ?>" />
						<?php
					}
					?>
									</a>
			</figure>
		</div>
		<div class="wpte-card__content">
			<<?php \Elementor\Utils::print_validated_html_tag( $headingTag ); ?> class="wpte-card__tax-title">
				<a href="<?php echo esc_url( $term_object->link ); ?>">
					<?php echo esc_html( $term_object->name ); ?>
				</a>
			</<?php \Elementor\Utils::print_validated_html_tag( $headingTag ); ?>>
			<?php
			if ( $show_trip_counts ) :
				if ( strpos( $count_label, '|' ) !== false ) {
					$countlabels = explode( '|', $count_label );
					$count_label = (int) $term_object->count === 1 ? $countlabels[0] : $countlabels[1];
				}
				?>
				<div class="wpte-card__tax-count"><?php echo esc_html( sprintf( '%1$d %2$s', $term_object->count, $count_label ) ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</div>