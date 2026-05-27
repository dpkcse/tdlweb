<?php
/**
 * Trips Slider Widget.
 *
 * @since 1.3.6
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB;

use WPTRAVELENGINEEB\Widget;
use Elementor\Icons_Manager;
defined( 'ABSPATH' ) || exit;

/**
 * Class Trips Slider Widget.
 *
 * @since 1.3.6
 */
class Widget_Trips_Slider extends Widget {

	/**
	 * Widget name.
	 *
	 * @since 1.3.6
	 *
	 * @var string
	 */
	public $widget_name = 'wptravelengine-trips-slider';

	/**
	 * Widget keywords.
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $keywords = array( 'trip', 'wp travel engine', 'wte', 'trips-slider', 'slider' );

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wpte-trip-slider', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-trips-slider.css' );

		return array( 'wpte-trip-slider' );
	}

	/**
	 * Javascripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'trip-wishlist', 'wptravelengineeeb-trips' );
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
		$controls = include __DIR__ . '/controls.php';
		$this->_wte_add_controls( $controls );
	}

	protected function get_swiper_pagination( $attributes, $_id ) {
		$prev_arrow_class = ! empty( $attributes['slider_prev_arrow_icon']['value'] ) ? 'custom-prev-arrow' : '';
		$next_arrow_class = ! empty( $attributes['slider_next_arrow_icon']['value'] ) ? ' custom-next-arrow' : '';
		$hidden_class_xl  = 'yes' !== wte_array_get( $attributes, 'arrow', 'yes' ) ? 'hide-xl' : '';
		$hidden_class_lg  = 'yes' !== wte_array_get( $attributes, 'arrow_laptop', 'yes' ) ? 'hide-lg' : '';
		$hidden_class_md  = 'yes' !== wte_array_get( $attributes, 'arrow_tablet', 'yes' ) ? 'hide-md' : '';
		$hidden_class_sm  = 'yes' !== wte_array_get( $attributes, 'arrow_mobile', '' ) ? 'hide-sm' : '';
		$hidden_pg_xl     = 'yes' !== wte_array_get( $attributes, 'pagination', 'yes' ) ? 'hide-xl' : '';
		$hidden_pg_lg     = 'yes' !== wte_array_get( $attributes, 'pagination_laptop', 'yes' ) ? 'hide-lg' : '';
		$hidden_pg_md     = 'yes' !== wte_array_get( $attributes, 'pagination_tablet', 'yes' ) ? 'hide-md' : '';
		$hidden_pg_sm     = 'yes' !== wte_array_get( $attributes, 'pagination_mobile', 'yes' ) ? 'hide-sm' : '';

		$this->add_render_attribute(
			'swiper-navigation',
			'class',
			array(
				'wpte-swiper-navigation',
				esc_attr( $hidden_class_lg ),
				esc_attr( $hidden_class_md ),
				esc_attr( $hidden_class_sm ),
				esc_attr( $prev_arrow_class ),
				esc_attr( $next_arrow_class ),
				esc_attr( $hidden_class_xl ),
			)
		);

		$this->add_render_attribute(
			'swiper-pagination',
			'class',
			array(
				'wpte-swiper-page',
				'slider-' . esc_attr( $_id ) . '-pagination',
				esc_attr( $hidden_pg_xl ),
				esc_attr( $hidden_pg_lg ),
				esc_attr( $hidden_pg_md ),
				esc_attr( $hidden_pg_sm ),
			)
		);

		?>
			<div <?php $this->print_render_attribute_string( 'swiper-navigation' ); ?>>
				<!-- If we need navigation buttons -->
				<div class="wpte-swiper-btn-prev trips-slider-<?php echo esc_attr( $_id ); ?>-prev">
				<?php
				if ( ! empty( $attributes['slider_prev_arrow_icon'] ) && is_array( $attributes['slider_prev_arrow_icon'] ) && ! empty( $attributes['slider_prev_arrow_icon']['value'] ) && ! is_array( $attributes['slider_prev_arrow_icon']['value'] ) ) :
					?>
						<i class="<?php echo esc_attr( $attributes['slider_prev_arrow_icon']['value'] ); ?>"></i>
					<?php
					elseif ( is_array( $attributes['slider_prev_arrow_icon']['value'] ) && ! empty( $attributes['slider_prev_arrow_icon']['value']['url'] ) ) :
						Icons_Manager::render_icon( $attributes['slider_prev_arrow_icon'] );
					else :
						?>
						<?php
					endif;
					?>
				</div>
				<div class="wpte-swiper-btn-next trips-slider-<?php echo esc_attr( $_id ); ?>-next">
				<?php
				if ( ! empty( $attributes['slider_next_arrow_icon'] ) && is_array( $attributes['slider_next_arrow_icon'] ) && ! empty( $attributes['slider_next_arrow_icon']['value'] ) && ! is_array( $attributes['slider_next_arrow_icon']['value'] ) ) :
					?>
						<i class="<?php echo esc_attr( $attributes['slider_next_arrow_icon']['value'] ); ?>"></i>
					<?php
					elseif ( is_array( $attributes['slider_next_arrow_icon']['value'] ) && ! empty( $attributes['slider_next_arrow_icon']['value']['url'] ) ) :
						Icons_Manager::render_icon( $attributes['slider_next_arrow_icon'] );
					else :
						?>
						<?php
					endif;
					?>
			</div>
		</div><!-- .wpte-swiper-navigation -->
			<!-- If we need pagination -->
			<div <?php $this->print_render_attribute_string( 'swiper-pagination' ); ?>></div>
		<?php
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

		$_id               = $this->get_id();
		$ribbonType        = wte_array_get( $attributes, 'ribbonType', '3' );
		$ribbonAlignment   = wte_array_get( $attributes, 'ribbonAlignment', 'left' );
		$discountType      = wte_array_get( $attributes, 'discountType', '1' );
		$discountAlignment = wte_array_get( $attributes, 'discountAlignment', 'left' );
		$priceType         = wte_array_get( $attributes, 'priceType', '1' );
		// swiper settings
		$slider_settings = array(
			'speed'         => wte_array_get( $attributes, 'speed', 300 ),
			'effect'        => wte_array_get( $attributes, 'effect', 'slide' ),
			'loop'          => wte_array_get( $attributes, 'loop', 'yes' ) === 'yes',
			'wrapperClass'  => 'wte-swiper-wrapper',
			'slidesPerView' => wte_array_get( $attributes, 'slidesPerViewDesktop_mobile', 1 ),
			'spaceBetween'  => wte_array_get( $attributes, 'spaceBetween', 30 ),
			'navigation'    => array(
				'nextEl' => '.trips-slider-' . esc_attr( $_id ) . '-next',
				'prevEl' => '.trips-slider-' . esc_attr( $_id ) . '-prev',
			),
			'pagination'    => array(
				'el'        => '.slider-' . esc_attr( $_id ) . '-pagination',
				'clickable' => true,
			),
			'breakpoints'   => wte_array_get(
				$attributes,
				'breakpoints',
				array(
					768  => array(
						'slidesPerView' => (int) wte_array_get( $attributes, 'slidesPerViewDesktop_tablet', 2 ),
					),
					1025 => array(
						'slidesPerView' => (int) wte_array_get( $attributes, 'slidesPerViewDesktop_laptop', 3 ),
					),
					1367 => array(
						'slidesPerView' => (int) wte_array_get( $attributes, 'slidesPerViewDesktop', 3 ),
					),
				)
			),
		);

		if ( wte_array_get( $attributes, 'autoplay', 'yes' ) === 'yes' ) {
			$slider_settings['autoplay'] = array(
				'delay'                => (int) wte_array_get( $attributes, 'autoplaydelay', 3000 ),
				'disableOnInteraction' => false,
			);
		}

		// Add classes to render on the HTML
		$this->add_render_attribute(
			'main-wrapper-classes',
			'class',
			array(
				'wpte-trips-slider',
				'wpte-trips-slider_one',
				'wpte-elementor-widget',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) ? esc_attr( "layout-{$attributes['cardlayout']}" ) : 'layout-1',
			)
		);

		$this->add_render_attribute(
			'inner-wrapper-classes',
			'class',
			array(
				'category-slider',
			)
		);

		$this->add_render_attribute(
			'swiper-wrapper',
			'class',
			array(
				'wpte-swiper',
				'swiper',
			)
		);

		$this->add_render_attribute(
			'swiper-wrapper',
			'data-swiper-options',
			array(
				esc_attr( wp_json_encode( $slider_settings ) ),
			)
		);

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

		if ( $results && is_array( $results ) ) :
			?>
			<div <?php $this->print_render_attribute_string( 'main-wrapper-classes' ); ?>>
				<div <?php $this->print_render_attribute_string( 'inner-wrapper-classes' ); ?>>
					<div <?php $this->print_render_attribute_string( 'swiper-wrapper' ); ?>>
						<div class="wte-swiper-wrapper swiper-wrapper">
							<?php
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

								endforeach;
							?>
						</div><!-- .wte-swiper-wrapper -->
					</div><!-- .wpte-swiper -->
					<?php $this->get_swiper_pagination( $attributes, $_id ); ?>
				</div> <!-- .category-slider -->
			</div>
			<?php
		else :
			echo esc_html__( 'No trips available. Please add a new trip.', 'wptravelengine-elementor-widgets' );
		endif;
	}
}