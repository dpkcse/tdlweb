<?php
/**
 * File Downloader Widget.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB\Trip;

use WPTRAVELENGINEEB\Widget;
use WPTRAVELENGINEEB;
/**
 * Class FileDownloader
 *
 * @since 1.3.9
 */

class FileDownLoaderWidget extends Widget {
	/**
	 * Widget name.
	 *
	 * @since 1.3.9
	 *
	 * @var string
	 */
	protected $widget_name = 'wte-trip-file-downloader';


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
	protected $keywords = array( 'wte', 'trip', 'file downloader', 'wp travel engine' );

	/**
	 * Get Title.
	 *
	 * @since 1.3.9
	 */
	public function get_title() {
		return __( 'Trip - File Downloader', 'wptravelengine-elementor-widgets' );
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
		return 'eicon-form-horizontal';
	}

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wte-elementor-widget-styles', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wte-elementor-widgets.css' );

		return array( 'wte-elementor-widget-styles' );
	}

	/**
	 * Widget Settings.
	 *
	 * @since 1.3.9
	 */
	protected function register_controls() {
		wp_enqueue_style( 'wte-fonts-style' );
		$settings = WPTRAVELENGINEEB\Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
		$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();
		$this->_wte_add_controls( $settings );
		$controls = include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/filedownloader/controls.php';
		$this->_wte_add_controls( $controls );
	}

	/**
	 * Renders Widget.
	 *
	 * @since 1.3.9
	 */
	protected function render() {
		if ( defined( 'WTEFD_VERSION' ) ) {
			$attributes = $this->get_settings_for_display();
			if ( file_exists( WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/filedownloader/filedownloader.php' ) ) {
				include_once WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/filedownloader/filedownloader.php';
			} else {
				echo esc_html__( '<p>Oops! No preview/output available for this widget.</p>', 'wptravelengine-elementor-widgets' );
			}
		} else {
			$is_elementor_editor_page = $this->is_elementor_editor_page();
			if ( ! $is_elementor_editor_page ) {
				return '';
			}
			?>
				<div class="wpte-info-block">
					<p>
						<?php
						echo wp_kses(
							sprintf(
								// translators: %1$s: opening anchor tag, %2$s: closing anchor tag.
								__( 'Trip - Review Form Widget requires WP Travel Engine - File Downloads to work. %1$sGet WP Travel Engine - File Downloads extension now%2$s.', 'wptravelengine-elementor-widgets' ),
								'<a target="_blank" href="https://wptravelengine.com/plugins/file-downloads/?utm_source=setting&amp;utm_medium=customer_site&amp;utm_campaign=setting_addon">',
								'</a>'
							),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						);
						?>
					</p>
				</div>
			<?php
		}
	}
}
