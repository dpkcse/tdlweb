<?php
/**
 * Custom Trips Tab Widget.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB\Trip;

use WPTRAVELENGINEEB\Widget;
use WPTRAVELENGINEEB;

/**
 * Class Custom Trips Tab Widget.
 *
 * @since 1.3.9
 */
class CustomTripsTabWidget extends Widget {

	/**
	 * Widget name.
	 *
	 * @since 1.3.9
	 *
	 * @var string
	 */
	public $widget_name = 'wte-custom-trips-tab';

	/**
	 * Widget categories.
	 *
	 * @since 1.3.9
	 *
	 * @var array
	 */
	protected $categories = array( 'single-wptravelengine' );


	/**
	 * Widget keywords.
	 *
	 * @since 1.3.9
	 *
	 * @var array
	 */
	protected $keywords = array( 'trip', 'wp travel engine', 'wte', 'custom-trips-tab', 'custom-tab' );

	/**
	 * Set Widget Title.
	 *
	 * @since 1.3.9
	 */
	public function get_title() {
		return __( 'Trip - Custom Trips Tab', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Widget Category
	 *
	 * @since 1.3.5
	 */
	public function get_categories() {
		return array( 'single-wptravelengine' );
	}

	/**
	 * Set Widget Icon.
	 *
	 * @since 1.3.9
	 */
	public function get_icon() {
		return 'custom-trips-tab';
	}

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wpte-custom-trip-tabs', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-custom-trip-tabs.css' );

		return array( 'wpte-custom-trip-tabs' );
	}

	/**
	 * Get Custom Trip Tabs.
	 *
	 * @since 1.3.9
	 *
	 * @return array
	 */
	public function get_custom_trip_tabs() {
		$settings = get_option( 'wp_travel_engine_settings', array() );
		$post_id  = get_the_ID();

		// In global template context, we may not have a valid trip ID
		// Check if we're editing a trip or if we're in a template context
		$post_type = get_post_type( $post_id );
		$is_trip   = 'trip' === $post_type;

		$filtered_default_array = array();
		$def_tabs               = array( 'itinerary', 'cost', 'dates', 'faqs', 'map', 'review' );

		foreach ( $settings['trip_tabs']['id'] ?? array() as $key => $i ) {
			$i     = (int) $i;
			$field = $settings['trip_tabs']['field'][ $i ] ?? '';
			if ( 1 === $i || in_array( $field, $def_tabs ) ) {
				continue;
			}

			// Get global tab label from settings
			$global_tab_label = $settings['trip_tabs']['name'][ $i ] ?? sprintf( __( 'Custom Tab %d', 'wptravelengine-elementor-widgets' ), $i );

			// Only get trip-specific data if we're on an actual trip page
			if ( $is_trip ) {
				$trip                                  = new \WPTravelEngine\Core\Models\Post\Trip( $post_id );
				$filtered_default_array[ 'tab_' . $i ] = array(
					'title'   => (string) $trip->get_setting( 'tab_' . $i . '_title', $global_tab_label ),
					'content' => (string) $trip->get_setting( 'tab_content.' . $i . '_wpeditor', '' ),
				);
			} else {
				// In template context, use global tab settings
				$filtered_default_array[ 'tab_' . $i ] = array(
					'title'   => $global_tab_label,
					'content' => '', // Will be populated when template is applied to actual trip
				);
			}
		}

		return $filtered_default_array;
	}
	/**
	 * Get Custom Trip Tabs Selector.
	 *
	 * @since 1.3.9
	 *
	 * @return array
	 */
	public function get_custom_trip_tabs_selector() {
		$custom_trip_tabs = $this->get_custom_trip_tabs();
		$options          = array();
		foreach ( $custom_trip_tabs as $key => $tab ) {
			$options[ $key ] = $tab['title'];
		}
		return $options;
	}
	/**
	 * Widget Settings.
	 *
	 * @since 1.3.9
	 */
	protected function register_controls() {
		$settings = WPTRAVELENGINEEB\Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
		$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();
		$this->_wte_add_controls( $settings );
		$controls = include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/customtripstab/controls.php';

		$this->_wte_add_controls( $controls );
	}

	/**
	 * Renders Widget.
	 *
	 * @since 1.3.9
	 */
	protected function render() {
		$settings         = $this->get_settings_for_display();
		$custom_trip_tabs = isset( $settings['custom_trip_tabs'] ) ? $settings['custom_trip_tabs'] : '';

		if ( empty( $custom_trip_tabs ) ) {
			return;
		}

		// Assuming you have $custom_trip_tabs array with titles
		$all_trip_tabs = $this->get_custom_trip_tabs();
		$selected_tab  = isset( $all_trip_tabs[ $custom_trip_tabs ] ) ? $all_trip_tabs[ $custom_trip_tabs ] : '';
		// get the attributes from the widget.
		$show_title = isset( $settings['show_title'] ) ? $settings['show_title'] : 'yes';
		$html_tag   = wptravelengineeb_normalize_html_tag( $settings['html_tag'] ?? 'h3' );
		$tab_title  = isset( $selected_tab['title'] ) ? $selected_tab['title'] : '';

		?>
		<div id="wte-<?php echo $custom_trip_tabs; ?>" class="wte-custom-tab-wrapper">
			<div class="wp-travel-engine-custom-tab-header">
				<?php
					printf( '<%1$s class="wpte-custom-tab-title">%2$s</%1$s>', esc_html( $html_tag ), esc_html( ( $show_title && $tab_title ) ? esc_html( $tab_title ) : '' ) );
				?>
				<?php if ( ! empty( $selected_tab['content'] ) ) : ?>
					<div class="wpte-custom-tab-content">
						<?php echo wp_kses( $selected_tab['content'], wptravelengineeb_kses_allowed_html() ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}