<?php

/**
 * Trips Accordion Widget.
 *
 * @since 1.3.5
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB;

use WPTRAVELENGINEEB\Widget;

defined( 'ABSPATH' ) || exit;

/**
 * Class Trips Accordion Widget.
 *
 * @since 1.3.5
 */

class Widget_Trips_Accordion extends Widget {


	/**
	 * Widget name.
	 *
	 * @since 1.3.5
	 *
	 * @var string
	 */
	public $widget_name = 'wptravelengine-trips-accordion';

	/**
	 * Widget keywords.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	protected $keywords = array( 'trip', 'wp travel engine', 'wte', 'accordion' );


	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {

		wp_register_style( 'wpte-trips-accordion', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-trips-accordion.css', array(), filemtime( plugin_dir_path( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-trips-accordion.css' ) );

		return array( 'wpte-trips-accordion' );
	}

	/**
	 * Javascripts dependencies.
	 */
	public function get_script_depends() {
		wp_register_script( 'wpte-trips-accordion', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'includes/widgets/trips-accordion/trips-accordion.js', array( 'jquery' ), WPTRAVELENGINEEB_VERSION, true );

		return array( 'wpte-trips-accordion', 'trip-wishlist' );
	}

	/**
	 * Widget categories.
	 */
	public function get_categories() {
		return array( 'wptravelengine' );
	}

	/**
	 * Widget Settings.
	 *
	 * @since 1.3.0
	 */
	protected function register_controls() {
		$settings = Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
		$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();
		$this->_wte_add_controls( $settings );
		$controls = include WPTRAVELENGINEEB_PATH . 'includes/widgets/trips-accordion/controls.php';
		$this->_wte_add_controls( $controls );
	}

	/**
	 * Renders Widget.
	 *
	 * @since 1.3.0
	 */
	protected function render() {
		$attributes = $this->get_settings_for_display();

		// Dynamically get all WP Travel Engine taxonomies including custom ones
		$attributes['default_taxonomies'] = array_keys( wptravelengineeb_get_trip_taxonomies() );

		if ( isset( $attributes['listby'] ) ) {
			$query_args                     = array(
				'post_type'      => \WP_TRAVEL_ENGINE_POST_TYPE,
				'posts_per_page' => $attributes['tripsCount'],
				'fields'         => 'ids',
				'post_status'    => 'publish',
			);
			$query_args['suppress_filters'] = false;
			if ( 'byid' === $attributes['listby'] ) {
				$attributes['filters']['tripsToDisplay'] = $attributes['tripsToDisplay'];
			} elseif ( 'byterms' === $attributes['listby'] ) {
				if ( isset( $attributes['default_taxonomies'] ) && 'byterms' === $attributes['listby'] ) {
					$query_args['tax_query'] = array(
						'relation' => isset( $attributes['tax_relation'] ) && '' != $attributes['tax_relation'] ? 'OR' : 'AND',
					);
					foreach ( $attributes['default_taxonomies'] as $taxonomy ) {
						if ( isset( $attributes[ '' . $taxonomy . '_termstoDisplay' ] ) && is_array( $attributes[ '' . $taxonomy . '_termstoDisplay' ] ) && count( $attributes[ '' . $taxonomy . '_termstoDisplay' ] ) > 0 ) {
							$query_args['tax_query'][] = array(
								'taxonomy' => $taxonomy,
								'terms'    => $attributes[ '' . $taxonomy . '_termstoDisplay' ],
							);
						}
					}
				}
				$trips = get_posts( $query_args );
				if ( is_array( $trips ) ) {
					$attributes['filters']['tripsToDisplay'] = $trips;
				} else {
					$attributes['filters']['tripsToDisplay'] = array();
				}
			} else {

				if ( 'featured' === $attributes['listby'] ) {
					$query_args['meta_key']   = 'wp_travel_engine_featured_trip';
					$query_args['meta_value'] = 'yes';
				} elseif ( 'onsale' === $attributes['listby'] ) {
					$query_args['meta_key']   = '_s_has_sale';
					$query_args['meta_value'] = 'yes';
				}

				$trips = get_posts( $query_args );

				if ( is_array( $trips ) ) {
					$attributes['filters']['tripsToDisplay'] = $trips;
				} else {
					$attributes['filters']['tripsToDisplay'] = array();
				}
			}
		}

		$results = array();
		if ( ! empty( $attributes['filters']['tripsToDisplay'] ) ) {
			$results = get_posts(
				array(
					'post_type'      => \WP_TRAVEL_ENGINE_POST_TYPE,
					'post__in'       => $attributes['filters']['tripsToDisplay'],
					'posts_per_page' => 100,
				)
			);
			if ( ! is_array( $results ) ) {
				return;
			}
		}

		$results = array_combine( array_column( $results, 'ID' ), $results );

		$ribbonType        = wte_array_get( $attributes, 'ribbonType', '3' );
		$ribbonAlignment   = wte_array_get( $attributes, 'ribbonAlignment', 'left' );
		$discountType      = wte_array_get( $attributes, 'discountType', '1' );
		$discountAlignment = wte_array_get( $attributes, 'discountAlignment', 'left' );
		$priceType         = wte_array_get( $attributes, 'priceType', '1' );

		$this->add_render_attribute(
			'featured-ribbon',
			'class',
			array(
				'wpte-badge',
				'wpte-badge_featured',
				'wpte-badge--layout-' . $ribbonType,
				'wpte-badge--' . $ribbonAlignment,
			)
		);

		$this->add_render_attribute(
			'discount-badge',
			'class',
			array(
				'wpte-badge',
				'wpte-badge_discount',
				'wpte-badge--layout-' . $discountType,
				'wpte-badge--' . $discountAlignment,
			)
		);

		$this->add_render_attribute(
			'price-data',
			'class',
			array(
				'wpte-card__price',
				'wpte-card__price--layout-' . $priceType,
			)
		);

		$this->add_render_attribute(
			'wpte-card',
			'class',
			array(
				'wpte-card',
				'wpte-card--ac',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) && ( $attributes['cardlayout'] === '1' || $attributes['cardlayout'] === '2' ) ? 'wpte-card--overlap-white' : '',
			)
		);

		$this->add_render_attribute(
			'wpte-card-wrap',
			'class',
			array(
				'wpte-card__wrap',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) && ( $attributes['cardlayout'] === '1' || $attributes['cardlayout'] === '2' ) ? 'wpte-card--grid wpte-card--overlap wpte-card--overlap-stack' : 'wpte-flex',
			)
		);

		// Add classes to render on the HTML
		$this->add_render_attribute(
			'main-wrapper-classes',
			'class',
			array(
				'wpte-trips-accordion',
				'wpte-elementor-widget',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) ? esc_attr( "layout-{$attributes['cardlayout']}" ) : 'layout-1',
			)
		);

		if ( $results && is_array( $results ) ) : ?>
			<div <?php $this->print_render_attribute_string( 'main-wrapper-classes' ); ?>>
				<?php
				$count = 0;
				foreach ( $attributes['filters']['tripsToDisplay'] as $trip_id ) :
					if ( ! isset( $results[ $trip_id ] ) ) {
						continue;
					}
					$trip                = $results[ $trip_id ];
					$duration_mapping    = array(
						'days'   => array( __( 'Day', 'wptravelengine-elementor-widgets' ), __( 'Days', 'wptravelengine-elementor-widgets' ) ),
						'nights' => array( __( 'Night', 'wptravelengine-elementor-widgets' ), __( 'Nights', 'wptravelengine-elementor-widgets' ) ),
						'hours'  => array( __( 'Hour', 'wptravelengine-elementor-widgets' ), __( 'Hours', 'wptravelengine-elementor-widgets' ) ),
					);
					$results['duration'] = $duration_mapping;
					$args                = array( $attributes, $trip, $results );

					include __DIR__ . '/view.php';

					++$count;

				endforeach;
				?>
			</div>
			<?php
		else :
			echo esc_html__( 'No trips available. Please add a new trip.', 'wptravelengine-elementor-widgets' );
		endif;
	}
}
