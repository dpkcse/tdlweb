<?php
namespace WPTRAVELENGINEEB;

use WPTRAVELENGINEEB\Widget;

/**
 * Widgets.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Trip Taxonomy Two Widget.
 *
 * @since 1.3.7
 */
class Widget_Trip_Tax_Two extends Widget {

	/**
	 *
	 * @var $widget_name
	 */
	public $widget_name = 'wptravelengine-trip-tax-two';

	/**
	 * Widget keywords.
	 *
	 * @since 1.3.7
	 *
	 * @var array
	 */
	protected $keywords = array( 'trip', 'wp travel engine', 'wte', 'destination', 'activities' );

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wpte-trips-tax', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-trips-tax.css' );

		return array( 'wpte-trips-tax' );
	}

	/**
	 * Javascripts dependencies.
	 */
	public function get_script_depends() {
		return array();
	}

	/**
	 * Widget categories.
	 */
	public function get_categories() {
		return array( 'wptravelengine' );
	}

	/**
	 * Widget Settings.
	 */
	protected function register_controls() {
		$settings = Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
		$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();
		$this->_wte_add_controls( $settings );

		$controls = include WPTRAVELENGINEEB_PATH . 'includes/widgets/trip-tax-two/controls.php';

		$this->_wte_add_controls( $controls );
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
					'number'     => 100,
					'hide_empty' => true,
				)
			);

			if ( ! is_array( $results ) ) {
				return;
			}
		}

		// Add classes to render on the HTML
		$this->add_render_attribute(
			'main-wrapper-classes',
			'class',
			array(
				'wpte-trips-tax',
				'wpte-trips-tax_two',
				'wpte-elementor-widget',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) ? esc_attr( "layout-{$attributes['cardlayout']}" ) : 'layout-1',
			)
		);

		$this->add_render_attribute(
			'inner-wrapper',
			'class',
			array(
				'wpte-grid',
			)
		);

		if ( $results && is_array( $results ) ) : ?>
			<div <?php $this->print_render_attribute_string( 'main-wrapper-classes' ); ?>>
				<div <?php $this->print_render_attribute_string( 'inner-wrapper' ); ?>>
					<?php
					foreach ( $attributes['listItems'] as $term_id ) :
						if ( ! isset( $results[ $term_id ] ) ) {
							continue;
						}
						$args = array( $attributes, $results[ $term_id ], $results );

						include __DIR__ . '/view.php';

						endforeach;
					?>
				</div><!-- .wte-adv-trips -->
			</div>
			<?php
		else :
			echo esc_html__( 'No taxonomy available. Please add a new term.', 'wptravelengine-elementor-widgets' );
		endif;
	}
}