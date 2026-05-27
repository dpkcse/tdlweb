<?php
/**
 * Trips Taxonomy Slider 3.
 *
 * @since 1.3.7
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB;

use WPTRAVELENGINEEB\Widget;
use Elementor\Icons_Manager;
defined( 'ABSPATH' ) || exit;

/**
 * Class Trips Taxonomy Slider 3 Widget.
 *
 * @since 1.3.7
 */
class Widget_Trips_Tax_Slider_Three extends Widget {

	/**
	 * Widget name.
	 *
	 * @since 1.3.6
	 *
	 * @var string
	 */
	public $widget_name = 'wptravelengine-trips-tax-slider-three';

	/**
	 * Widget keywords.
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $keywords = array( 'trip', 'destination', 'activities', 'wp travel engine', 'wte', 'slider' );

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wpte-trips-tax-slider', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-trips-tax-slider.css' );

		return array( 'wpte-trips-tax-slider' );
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
				<div class="wpte-swiper-btn-prev trips-tax-slider-<?php echo esc_attr( $_id ); ?>-prev">
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
				<div class="wpte-swiper-btn-next trips-tax-slider-<?php echo esc_attr( $_id ); ?>-next">
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
		if ( isset( $attributes['listby'] ) ) {
			if ( 'byids' !== $attributes['listby'] ) {

				$items = get_terms(
					array(
						'taxonomy'  => $attributes['selectTax'],
						'childless' => true,
						'number'    => isset( $attributes['itemsCount'] ) ? $attributes['itemsCount'] : 4,
						'fields'    => 'ids',
					)
				);

				if ( is_array( $items ) ) {
					$attributes['listItems'] = $items;
				} else {
					$attributes['listItems'] = array();
				}
			} else {
				$tax_selected = $attributes['selectTax'] === 'destination' ? $attributes['listItemsDestination'] : ( $attributes['selectTax'] === 'trip_types' ? $attributes['listItemsTripTypes'] : $attributes['listItemsActivities'] );

				if ( isset( $tax_selected ) && is_array( $tax_selected ) ) {
					$attributes['listItems'] = $tax_selected;
				} else {
					$attributes['listItems'] = array();
				}
			}
		}

		$results = array();
		if ( ! empty( $attributes['listItems'] ) ) {
			$results = wte_get_terms_by_id(
				$attributes['selectTax'],
				array(
					'taxonomy'   => $attributes['selectTax'],
					'number'     => 300,
					'hide_empty' => true,
				)
			);

			if ( ! is_array( $results ) ) {
				return;
			}
		}
		// swiper settings
		$_id             = $this->get_id();
		$cardLayout      = wte_array_get( $attributes, 'cardlayout', '1' );
		$slider_settings = array(
			'speed'          => wte_array_get( $attributes, 'speed', 300 ),
			'effect'         => wte_array_get( $attributes, 'effect', 'slide' ),
			'loop'           => wte_array_get( $attributes, 'loop', 'yes' ) === 'yes',
			'wrapperClass'   => 'wte-swiper-wrapper',
			'slidesPerView'  => wte_array_get( $attributes, 'slidesPerViewDesktop_mobile', 1 ),
			'spaceBetween'   => '2' === $cardLayout ? wte_array_get( $attributes, 'spaceBetween', 5 ) : 0,
			'centeredSlides' => true,
			'navigation'     => array(
				'nextEl' => '.trips-tax-slider-' . esc_attr( $_id ) . '-next',
				'prevEl' => '.trips-tax-slider-' . esc_attr( $_id ) . '-prev',
			),
			'pagination'     => array(
				'el'        => '.slider-' . esc_attr( $_id ) . '-pagination',
				'clickable' => true,
			),
			'breakpoints'    => wte_array_get(
				$attributes,
				'breakpoints',
				array(
					768  => array(
						'slidesPerView' => (int) wte_array_get( $attributes, 'slidesPerViewDesktop_tablet', 2.5 ),
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

		if ( $cardLayout === '1' ) {
			$slider_settings['effect']          = 'coverflow';
			$slider_settings['coverflowEffect'] = array(
				'rotate'       => 0,
				'stretch'      => 0,
				'depth'        => 100,
				'modifier'     => 4,
				'slideShadows' => false,
			);
		}

		if ( $cardLayout === '3' ) {
			$slider_settings['effect']          = 'coverflow';
			$slider_settings['coverflowEffect'] = array(
				'rotate'       => 20,
				'stretch'      => 0,
				'depth'        => 400,
				'modifier'     => 1,
				'slideShadows' => false,
			);
		}

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
				'wpte-elementor-widget',
				'wpte-trips-tax-slider',
				'wpte-trips-tax-slider--three',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) ? esc_attr( "wpte-trips-tax-slider--three--layout-{$attributes['cardlayout']}" ) : esc_attr( 'wpte-trips-tax-slider--three--layout-1' ),
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

		if ( $results && is_array( $results ) ) :
			?>
			<div <?php $this->print_render_attribute_string( 'main-wrapper-classes' ); ?>>
				<div <?php $this->print_render_attribute_string( 'swiper-wrapper' ); ?>>
					<div class="wte-swiper-wrapper swiper-wrapper">
						<?php
						foreach ( $attributes['listItems'] as $term_id ) :
							if ( ! isset( $results[ $term_id ] ) ) {
								continue;
							}
							$args = array( $attributes, $results[ $term_id ], $results );

							include __DIR__ . '/view.php';

							endforeach;
						?>
					</div><!-- .wte-swiper-wrapper -->
				</div><!-- .wpte-swiper -->
				<?php $this->get_swiper_pagination( $attributes, $_id ); ?>
			</div>
			<?php
		else :
			echo esc_html__( 'No taxonomy available. Please add a new term.', 'wptravelengine-elementor-widgets' );
		endif;
	}
}