<?php
/**
 * Single Trip Faqs Template
 *
 * @var string $wrapper_attributes
 * @var Render $render
 * @var Attributes $attributes_parser
 * @package Wp_Travel_Engine
 * @since 5.9
 */

use WPTravelEngine\Blocks\SampleData;

global $wtetrip;

if ( $render->is_editor() ) {
	$faqs = SampleData::faqs();
} else {

	$post_meta = get_post_meta( $wtetrip->post->ID, 'wp_travel_engine_setting', true );
	$faqs      = array();

	// Check for new category-based format first.
	$faqs_data = ( $post_meta['faqs_data'] ?? false );
	if ( $faqs_data && isset( $faqs_data['categories'] ) && is_array( $faqs_data['categories'] ) ) {
		$global_faq_map = wptravelengine_get_global_faq_map();
		$global_faq_ids = array_keys( $global_faq_map );

		foreach ( $faqs_data['categories'] as $category ) {
			if ( isset( $category['faqs'] ) && is_array( $category['faqs'] ) ) {
				foreach ( $category['faqs'] as $faq ) {
					$source_id     = (string) ( $faq['sourceId'] ?? '' );
					$added_in_bulk = isset( $faq['addedInBulk'] ) ? (bool) $faq['addedInBulk'] : false;

					if ( $added_in_bulk && '' !== $source_id && ! in_array( $source_id, $global_faq_ids, true ) ) {
						continue;
					}

					$question = $faq['question'] ?? '';
					$answer   = $faq['answer'] ?? '';

					if ( $added_in_bulk && '' !== $source_id && isset( $global_faq_map[ $source_id ] ) ) {
						$question = $global_faq_map[ $source_id ]['question'];
						$answer   = $global_faq_map[ $source_id ]['answer'];
					}

					$faqs[] = array(
						'question' => $question,
						'answer'   => $answer,
					);
				}
			}
		}
	} else {
		// Fallback to legacy format.
		$trip_faqs = ( $post_meta['faq'] ?? false );

		if ( $trip_faqs && is_array( $trip_faqs['faq_title'] ) ) {
			foreach ( $trip_faqs['faq_title'] as $key => $faq_title ) {
				if ( isset( $trip_faqs['faq_content'][ $key ] ) ) {
					$faqs[] = array(
						'question' => $faq_title,
						'answer'   => $trip_faqs['faq_content'][ $key ],
					);
				}
			}
		}
	}

	// Return if no FAQs found.
	if ( empty( $faqs ) ) {
		return;
	}
}

?>
	<div <?php $attributes_parser->wrapper_attributes(); ?>>
		<div class="post-data faq">
			<div class="wp-travel-engine-faq-tab-content<?php echo $attributes_parser->get( 'showDivider' ) ? ' has-divider' : ''; ?>">
				<?php
				foreach ( $faqs as $index => $faq ) {
					$active = ( 0 === $index );
					?>
					<div id="faq-tabs" class="faq-row">
						<a class="accordion-tabs-toggle <?php echo esc_attr( $active ? 'active' : '' ); ?>"
							href="javascript:void(0);">
							<span
								class="dashicons dashicons-arrow-down custom-toggle-tabs rotator <?php echo esc_attr( $active ? 'open' : '' ); ?>"></span>
							<div class="faq-title">
								<?php echo esc_html( $faq['question'] ); ?>
							</div>
						</a>
						<div class="faq-content" style="<?php echo( ! $active ? 'display: none;' : '' ); ?>">
							<?php echo wp_kses_post( $faq['answer'] ); ?>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
<?php
