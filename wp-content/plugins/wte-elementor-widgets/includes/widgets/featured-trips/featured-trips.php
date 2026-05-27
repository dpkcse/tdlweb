<?php
namespace WPTRAVELENGINEEB;

use WPTRAVELENGINEEB\Widget;

/**
 * Widgets.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Featured Trips Widget.
 *
 * @since 1.3.5
 */
class Widget_Featured_Trips extends Widget {

	/**
	 *
	 * @var $widget_name
	 */
	public $widget_name = 'wptravelengine-featured-trips';

	/**
	 * Widget keywords.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	protected $keywords = array( 'trip', 'wp travel engine', 'wte' );

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wpte-feat-trips', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-feat-trips.css' );

		return array( 'wpte-feat-trips' );
	}

	/**
	 * Javascripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'trip-wishlist' );
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

		$controls = include WPTRAVELENGINEEB_PATH . 'includes/widgets/featured-trips/controls.php';

		$this->_wte_add_controls( $controls );
	}

	/**
	 * Renders Widget.
	 *
	 * @since 1.3.0
	 */
	protected function render() {
		$attributes = $this->get_settings_for_display();

		if ( ! empty( $attributes['listby'] ) ) {
			$query_args      = array(
				'ignore_sticky_posts' => 1,
				'post_type'           => WP_TRAVEL_ENGINE_POST_TYPE,
				'post__in'            => $attributes['listby'],
				'suppress_filters'    => false,
			);
			$query_args['p'] = $attributes['listby'];
		}

		$results = array();

		// Set render attributes for the post grid wrapper
		$this->add_render_attribute(
			'main-wrapper-classes',
			'class',
			array(
				'wpte-featured-trips',
				'wpte-elementor-widget',
			)
		);

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
				'wpte-card--grid',
				'wpte-card--overlap',
				'wpte-card--overlap-white',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) && $attributes['cardlayout'] == '1' ? 'wpte-card--overlap-stack' : '',
				isset( $attributes['cardlayout'] ) && ! empty( $attributes['cardlayout'] ) ? esc_attr( "layout-{$attributes['cardlayout']}" ) : 'layout-1',
			)
		);

		// Output the post grid container with the view included
		?>
		<div <?php $this->print_render_attribute_string( 'main-wrapper-classes' ); ?>>
			<?php
				$duration_mapping    = array(
					'days'   => array( __( 'Day', 'wptravelengine-elementor-widgets' ), __( 'Days', 'wptravelengine-elementor-widgets' ) ),
					'nights' => array( __( 'Night', 'wptravelengine-elementor-widgets' ), __( 'Nights', 'wptravelengine-elementor-widgets' ) ),
					'hours'  => array( __( 'Hour', 'wptravelengine-elementor-widgets' ), __( 'Hours', 'wptravelengine-elementor-widgets' ) ),
				);
				$results['duration'] = $duration_mapping;
				$args                = array( $attributes, $results );
				if ( isset( $attributes['listby'] ) && ! empty( $attributes['listby'] ) ) {
					$query = new \WP_Query( $query_args );
					if ( $query->have_posts() ) :

						while ( $query->have_posts() ) :
							$query->the_post();
							// Fetch post details
							$layout_data = wte_array_get( $attributes, 'cardlayout', '1' );
							$layout_path = __DIR__ . '/' . sanitize_file_name( 'layout-' . $layout_data . '.php' );
							if ( file_exists( $layout_path ) ) {
								include $layout_path;
							} else {
								include __DIR__ . '/layout-1.php';
							}
						endwhile;
						wp_reset_postdata();
					endif;
				} else {
					?>
						<p><?php esc_html_e( 'No trips found.', 'wptravelengine-elementor-widgets' ); ?></p>
					<?php
				}
				?>
		</div>
		<?php
	}
}
