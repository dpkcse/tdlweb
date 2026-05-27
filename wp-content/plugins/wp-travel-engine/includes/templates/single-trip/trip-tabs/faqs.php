<?php
/**
 * Single Trip Faqs Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/single-trip/trip-tabs/faqs.php.
 *
 * @package Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes/templates
 * @since 6.7.11 Introduced category-based FAQ rendering via _faqs.php partial.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! isset( $post->ID ) ) {
	return;
}

$wp_travel_engine_setting = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
$faqs_data                = $wp_travel_engine_setting['faqs_data'] ?? array();

// Get current global FAQs for syncing bulk-imported items.
$settings_available = function_exists( 'wptravelengine_settings' );
$global_settings    = $settings_available ? wptravelengine_settings()->get() : array();

$global_faq_map = wptravelengine_get_global_faq_map();
$global_faq_ids = array_keys( $global_faq_map );

// Filter out FAQs that were imported from globals but whose global entry was removed.
if ( ! empty( $faqs_data['categories'] ) && $settings_available ) {
	$faqs_data['categories'] = wptravelengine_filter_orphaned_faqs( $faqs_data['categories'], $global_faq_ids );
}

// Fallback to old structure for backward compatibility.
$old_faq = $wp_travel_engine_setting['faq'] ?? array();

// Get section title
$section_title = '';
if ( ! empty( $faqs_data['sectionTitle'] ) ) {
	$section_title = $faqs_data['sectionTitle'];
} else {
	$section_title = $wp_travel_engine_setting['faq_section_title'] ?? __( 'Frequently Asked Questions', 'wp-travel-engine' );
}

// Check if we have FAQs from new or old structure
$has_faqs = false;
if ( ! empty( $faqs_data['categories'] ) ) {
	foreach ( $faqs_data['categories'] as $category ) {
		if ( ! empty( $category['faqs'] ) ) {
			$has_faqs = true;
			break;
		}
	}
} elseif ( isset( $old_faq['faq_title'] ) && ! empty( $old_faq['faq_title'] ) ) {
	$has_faqs = true;
}

if ( ! $has_faqs ) {
	return;
}

/**
 * Backward compatibility.
 *
 * If the trip still uses the legacy FAQ structure, render the legacy template
 * directly to keep markup consistent for older themes.
 */

if ( ! empty( $faqs_data['categories'] ) ) :
	wte_get_template(
		'single-trip/trip-tabs/_faqs.php',
		array(
			'faqs_data'      => $faqs_data,
			'section_title'  => $section_title,
			'global_faq_map' => $global_faq_map,
		)
	);
	return;
endif;
?>

<?php do_action( 'wte_before_faq_content' ); ?>

<div class="post-data faq">
	<div class="wp-travel-engine-faq-tab-header">
		<?php
			/**
			 * Hook - Display tab content title, left for themes.
			 */
			do_action( 'wte_faqs_tab_title' );
		?>
		<div class="wpte-faq-button-toggle expand-all-button">
			<label for="faq-toggle-btn" class="wpte-faq-button-label"><?php echo esc_html__( 'Expand all', 'wp-travel-engine' ); ?></label>
			<input id="faq-toggle-btn" type="checkbox" class="checkbox">
		</div>
	</div>
	<div class="wp-travel-engine-faq-tab-content">
	<?php
	if ( isset( $old_faq['faq_title'] ) && ! empty( $old_faq['faq_title'] ) ) {
		$maxlen   = max( array_keys( $old_faq['faq_title'] ) );
		$arr_keys = array_keys( $old_faq['faq_title'] );
		foreach ( $arr_keys as $key => $value ) {
			if ( array_key_exists( $value, $old_faq['faq_title'] ) ) {
				?>
				<div id="faq-tabs<?php echo esc_attr( $value ); ?>"
					data-id="<?php echo esc_attr( $value ); ?>" class="faq-row">
					<span class="accordion-tabs-toggle">
						<span class="dashicons dashicons-arrow-down custom-toggle-tabs rotator"></span>
						<div class="faq-title">
							<?php echo ( isset( $old_faq['faq_title'][ $value ] ) ? esc_attr( $old_faq['faq_title'][ $value ] ) : '' ); ?>
						</div>
					</span>
					<div class="faq-content">
						<p>
							<?php
							$faq_content = isset( $old_faq['faq_content'][ $value ] ) ? $old_faq['faq_content'][ $value ] : '';
							echo apply_filters( 'the_content', wp_kses_post( $faq_content ) );
							?>
						</p>
					</div>
				</div>
				<?php
			}
		}
	}
	?>
	</div>
</div>

<?php
do_action( 'wte_after_faq_content' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
