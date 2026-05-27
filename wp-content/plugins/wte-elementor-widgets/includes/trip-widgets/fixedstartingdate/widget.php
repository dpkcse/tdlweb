<?php
/** Fixed Starting Date Widget.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB\Trip;

use WPTRAVELENGINEEB\Widget;
use WPTRAVELENGINEEB;

/**
 * Class FixedStartingDate.
 *
 * @since 1.3.0
 */
class FixedstartingdateWidget extends Widget {

	/**
	 * Widget name.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	protected $widget_name = 'wte-fixed-starting-date';

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
	protected $keywords = array( 'fixed-starting-date', 'wp travel engine', 'wte' );

	/**
	 * Set Widget Title.
	 *
	 * @since 1.3.0
	 */
	public function get_title() {
		return __( 'Trip - Fixed Starting Date', 'wptravelengine-elementor-widgets' );
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
		return 'eicon-date';
	}

	/**
	 * Style Dependencies.
	 *
	 * @since 1.3.8
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'wte-fsd-public', 'wte-fixed-departure-dates', 'style-trip-booking-modal', 'wte-fpickr' );
	}

	/**
	 * Widget Settings.
	 *
	 * @since 1.3.0
	 */
	protected function register_controls() {
		if ( defined( 'WTE_FIXED_DEPARTURE_VERSION' ) ) {
			wp_enqueue_style( 'wte-fonts-style' );
			$settings = WPTRAVELENGINEEB\Widgets_Controller::instance()->get_core_widget_setting( $this->widget_name, 'controls' );
			$controls = isset( $settings['controls'] ) && is_array( $settings['controls'] ) ? $settings['controls'] : array();
			$this->_wte_add_controls( $settings );
			if ( version_compare( WTE_FIXED_DEPARTURE_VERSION, '2.4.0', '<' ) || ! apply_filters( 'is_wptravelengine_active', false ) ) {
				$controls = include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/fixedstartingdate/controls.php';
			} else {
				$controls = include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/fixedstartingdate/controls-newfsd.php';
			}
			$this->_wte_add_controls( $controls );
		}
	}

	/**
	 * Renders Widget.
	 *
	 * @since 1.3.0
	 */
	protected function render() {
		if ( defined( 'WTE_FIXED_DEPARTURE_VERSION' ) ) {
			$attributes = $this->get_settings_for_display();
			if ( version_compare( WTE_FIXED_DEPARTURE_VERSION, '2.4.0', '<' ) || ! apply_filters( 'is_wptravelengine_active', false ) ) {
				include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/fixedstartingdate/fixedstartingdate.php';
			} else {
				global $post;
				$is_elementor_editor_page = $this->is_elementor_editor_page();
				if ( ! $is_elementor_editor_page ) {
					$args = \WTE_Fixed_Starting_Dates::$general->get_all_fsd_details(
						array(
							'trip_id'      => $post->ID,
							'is_shortcode' => true,
						)
					);

					$temp_array = array(
						'show_trip_title'         => 'yes' === $attributes['show_trip_title'],
						'show_start_date'         => 'yes' === $attributes['show_start_date'],
						'show_end_date'           => 'yes' === $attributes['show_end_date'],
						'show_price_column'       => 'yes' === $attributes['show_price_column'],
						'show_space_left_column'  => 'yes' === $attributes['show_space_left_column'],
						'time_slots_label'        => $attributes['time_slots_label'],
						'group_discount_label'    => $attributes['group_discount_label'],
						'book_now_btn_txt'        => $attributes['book_now_btn_txt'],
						'sold_out_btn_txt'        => $attributes['sold_out_btn_txt'],
						'show_more_btn_txt'       => $attributes['show_more_btn_txt'],
						'show_less_btn_txt'       => $attributes['show_less_btn_txt'],
						'date_format'             => $attributes['date_format'],
						'days_format'             => $attributes['days_format'],
						'fsd_not_available_label' => $attributes['fsd_not_available_text'],
					);
					echo \WTE_Fixed_Starting_Dates::$general::get_new_table_html( array_replace( $args, $temp_array ) );
				} else {
					include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/fixedstartingdate/demo.php';
				}
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
								__( 'Trip - Fixed Starting Dates Widget requires WP Travel Engine - Trip Fixed Starting Dates Addon to work. %1$sGet Trip Fixed Starting Dates extension now%2$s.', 'wptravelengine-elementor-widgets' ),
								'<a target="_blank" href="https://wptravelengine.com/plugins/trip-fixed-starting-dates/?utm_source=setting&amp;utm_medium=customer_site&amp;utm_campaign=setting_addon">',
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
