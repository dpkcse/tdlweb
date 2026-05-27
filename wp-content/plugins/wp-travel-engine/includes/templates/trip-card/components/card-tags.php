<?php
/**
 * @var int $trip_id
 */

$tag_terms = get_the_terms( $trip_id, 'trip_tag' );

if ( empty( $tag_terms ) || is_wp_error( $tag_terms ) ) {
	return;
}
?>

<span class="category-trip-wtetags">
	<?php
	foreach ( $tag_terms as $tg ) :
		$tags_description = term_description( $tg->term_id );
		$tag_link         = get_term_link( $tg );
		$tag_name         = $tg->name;
		$tag_span_class   = $tags_description ? 'tippy-exist' : '';

		printf(
			'<span class="%s"%s><a rel="tag" target="_self" href="%s">%s</a></span>',
			esc_attr( $tag_span_class ),
			$tags_description ? ' data-content="' . esc_attr( $tags_description ) . '"' : '',
			esc_url( $tag_link ),
			esc_html( $tag_name )
		);
	endforeach;
	?>
</span>
