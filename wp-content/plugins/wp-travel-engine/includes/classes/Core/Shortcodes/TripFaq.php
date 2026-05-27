<?php
/**
 * ShortCode TripFaq.
 *
 * @package    WPTravelEngine
 * @subpackage WPTravelEngine\Core\Shortcodes
 */

namespace WPTravelEngine\Core\Shortcodes;

use WPTravelEngine\Abstracts\Shortcode;

/**
 * Class TripFaq.
 *
 * Renders the FAQ section for a trip.
 * Usage: [wte_trip_faqs id="123"]
 *
 * @since 6.7.11
 */
class TripFaq extends Shortcode {

	/**
	 * Shortcode tag.
	 *
	 * @var string
	 */
	const TAG = 'wte_trip_faqs';

	/**
	 * Default attributes.
	 *
	 * @return array
	 */
	protected function default_attributes(): array {
		global $post;
		return array(
			'id' => isset( $post->ID ) ? $post->ID : 0,
		);
	}

	/**
	 * Renders the FAQ section for the given trip ID.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function output( $atts ): string {
		$trip_id = absint( $atts['id'] );

		if ( ! $trip_id ) {
			return '';
		}

		$wp_travel_engine_setting = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$faqs_data                = $wp_travel_engine_setting['faqs_data'] ?? array();

		$settings_available = function_exists( 'wptravelengine_settings' );

		$global_faq_map = wptravelengine_get_global_faq_map();
		$global_faq_ids = array_keys( $global_faq_map );

		// Filter out orphaned bulk-imported FAQs.
		if ( ! empty( $faqs_data['categories'] ) && $settings_available ) {
			$faqs_data['categories'] = wptravelengine_filter_orphaned_faqs( $faqs_data['categories'], $global_faq_ids );
		}

		$has_faqs = false;
		if ( ! empty( $faqs_data['categories'] ) ) {
			foreach ( $faqs_data['categories'] as $category ) {
				if ( ! empty( $category['faqs'] ) ) {
					$has_faqs = true;
					break;
				}
			}
		}

		if ( ! $has_faqs ) {
			return '';
		}

		$section_title = ! empty( $faqs_data['sectionTitle'] )
			? $faqs_data['sectionTitle']
			: ( $wp_travel_engine_setting['faq_section_title']
				?? $wp_travel_engine_setting['faqs_title']
				?? __( 'Frequently Asked Questions', 'wp-travel-engine' ) );

		if ( ! is_singular( 'trip' ) ) {
			wp_enqueue_style( 'wte-trip-faqs' );
			wp_enqueue_script( 'wte-trip-faqs' );
		}

		ob_start();
		wte_get_template(
			'single-trip/trip-tabs/_faqs.php',
			array(
				'faqs_data'      => $faqs_data,
				'section_title'  => $section_title,
				'global_faq_map' => $global_faq_map,
			)
		);
		return ob_get_clean();
	}
}
