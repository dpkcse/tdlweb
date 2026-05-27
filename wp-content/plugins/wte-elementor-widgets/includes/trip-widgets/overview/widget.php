<?php
/**
 * Overview Widget.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB\Trip;

use WPTRAVELENGINEEB\Widget;
use WPTRAVELENGINEEB;

/**
 * Class Overview.
 *
 * @since 1.3.0
 */
class OverviewWidget extends Widget {

	/**
	 * Widget name.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	protected $widget_name = 'wte-overview';

	/**
	 * Widget categories.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	protected $categories = array( 'single-wptravelengine' );

	/**
	 * Widget keywords.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	protected $keywords = array( 'Overview', 'wp travel engine', 'wte' );

	/**
	 * Set Widget Title.
	 *
	 * @since 1.3.0
	 */
	public function get_title() {
		return __( 'Trip - Overview', 'wptravelengine-elementor-widgets' );
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
	 * @since 1.3.0
	 */
	public function get_icon() {
		return 'eicon-preview-thin';
	}

	/**
	 * Register Widget Controls.
	 *
	 * @since 1.3.0
	 */
	protected function register_controls() {
		wp_enqueue_style( 'wte-fonts-style' );
		$settings = WPTRAVELENGINEEB\Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
		$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();
		$this->_wte_add_controls( $settings );

		$controls = include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/overview/controls.php';

		$this->_wte_add_controls( $controls );
	}

	/**
	 * Renders Widget.
	 *
	 * @since 1.3.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		global $post;

		$trip_settings            = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
		$key                      = '1_wpeditor';
		$tab_title                = isset( $trip_settings['overview_section_title'] ) && ! empty( $trip_settings['overview_section_title'] ) ? $trip_settings['overview_section_title'] : false;
		$overview                 = isset( $trip_settings['tab_content'][ $key ] ) ? $trip_settings['tab_content'][ $key ] : '';
		$is_elementor_editor_page = $this->is_elementor_editor_page();

		$show_title = isset( $settings['show_title'] ) ? $settings['show_title'] : 'yes';
		$html_tag   = wptravelengineeb_normalize_html_tag( $settings['html_tag'] ?? 'h3' );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing.
		if ( empty( $overview ) && $is_elementor_editor_page ) {
			$overview = "Embark on a breathtaking journey to the iconic Everest Base Camp, a trek that offers stunning vistas, cultural experiences, and a chance to be up close with the world\'s highest peak. This adventure takes you through picturesque Himalayan landscapes, traditional Sherpa villages, and concludes at the base of Mount Everest itself.";
		}?>
		<div id="wte-overview" class="overview-content">
			<?php printf( '<%1$s class="wpte-overview-title">%2$s</%1$s>', esc_html( $html_tag ), esc_html( ( $show_title && $tab_title ) ? esc_html( $tab_title ) : '' ) ); ?>
			<?php echo wp_kses( $overview, wptravelengineeb_kses_allowed_html() ); ?>
		</div>
		<?php
	}
}
