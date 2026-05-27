<?php
/**
 * Partial template: FAQ list (new category-based structure).
 *
 * Included by faqs.php after it has prepared the data. The following
 * variables must be set in the including scope before this file is loaded:
 *
 * @var array  $faqs_data      Structured FAQ data with 'categories' key.
 * @var string $section_title  Section heading (may be empty).
 * @var array  $global_faq_map Map of global FAQ IDs to ['question', 'answer'].
 *                             Built via wptravelengine_get_global_faq_map().
 * @since 6.7.11
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php do_action( 'wte_before_faq_content' ); ?>

<div class="wpte-faq-section">
	<?php if ( ! empty( $section_title ) ) : ?>
		<h2 class="wpte-faq-section-title"><?php echo esc_html( $section_title ); ?></h2>
	<?php endif; ?>

	<?php
	// Display new structure FAQs with categories
	if ( ! empty( $faqs_data['categories'] ) ) :
		foreach ( $faqs_data['categories'] as $cat_index => $category ) :
			if ( empty( $category['faqs'] ) ) {
				continue;
			}
			$category_id = $category['id'] ?? 'faq-cat-' . $cat_index;
			?>
			<div class="wpte-faq-category">
				<div class="wpte-faq-category-header">
					<h3 class="wpte-faq-category-title"><?php echo esc_html( $category['name'] ?? '' ); ?></h3>
					<div class="wpte-faq-expand-all expand-all-button" data-category="<?php echo esc_attr( $category_id ); ?>">
						<label for="expand-all-<?php echo esc_attr( $category_id ); ?>" class="faq-button-label">
							<?php esc_html_e( 'Expand all', 'wp-travel-engine' ); ?>
						</label>
						<input id="expand-all-<?php echo esc_attr( $category_id ); ?>" type="checkbox" class="checkbox">
					</div>
				</div>

				<div class="wpte-faq-list" data-category-id="<?php echo esc_attr( $category_id ); ?>">
					<?php
					$faq_index = 0;
					foreach ( $category['faqs'] as $faq_item ) :
						$faq_question  = (string) ( $faq_item['question'] ?? '' );
						$faq_answer    = (string) ( $faq_item['answer'] ?? '' );
						$source_id     = (string) ( $faq_item['sourceId'] ?? $faq_item['globalFaqId'] ?? '' );
						$added_in_bulk = isset( $faq_item['addedInBulk'] ) ? (bool) $faq_item['addedInBulk'] : false;

						if ( $added_in_bulk && '' !== $source_id && isset( $global_faq_map[ $source_id ] ) ) {
							$faq_question = $global_faq_map[ $source_id ]['question'];
							$faq_answer   = $global_faq_map[ $source_id ]['answer'];
						}

						if ( '' === $faq_question ) {
							continue;
						}
						++$faq_index;
						$item_id = $faq_item['id'] ?? 'faq-' . $cat_index . '-' . $faq_index;
						?>
						<div class="wpte-faq-item" data-faq-id="<?php echo esc_attr( $item_id ); ?>">
							<button class="wpte-faq-question" aria-expanded="false" aria-controls="wpte-faq-answer-<?php echo esc_attr( $item_id ); ?>">
								<span class="wpte-faq-question-text"><?php echo esc_html( $faq_question ); ?></span>
								<span class="wpte-faq-icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</button>
							<div class="wpte-faq-answer" id="wpte-faq-answer-<?php echo esc_attr( $item_id ); ?>" hidden>
								<div class="wpte-faq-answer-content">
									<?php echo wp_kses( $faq_answer, 'wptravelengine_post' ); ?>
								</div>
							</div>
						</div>
						<?php
					endforeach;
					?>
				</div>
			</div>
			<?php
		endforeach;
	endif;
	?>
</div>

<?php
do_action( 'wte_after_faq_content' );
