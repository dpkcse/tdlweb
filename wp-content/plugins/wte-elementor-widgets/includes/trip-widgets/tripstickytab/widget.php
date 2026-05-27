<?php
/**
 * Trips Tab Widget.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB\Trip;

use WPTRAVELENGINEEB\Widget;
use WPTRAVELENGINEEB;

use function ElementorDeps\DI\env;

defined( 'ABSPATH' ) || exit;

/**
 * Class Trips Tab Widget.
 *
 * @since 1.3.9
 */
class TripStickyTabWidget extends Widget {

	/**
	 * Widget name.
	 *
	 * @since 1.3.9
	 *
	 * @var string
	 */
	public $widget_name = 'wte-trips-sticky-tab';

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
	protected $keywords = array( 'trip', 'trip sticky tab', 'wp travel engine', 'wte', 'trips-sticky-tab', 'sticky-tab' );

	/**
	 * Set Widget Title.
	 *
	 * @since 1.3.9
	 */
	public function get_title() {
		return __( 'Trip - Trips Sticky Tab', 'wptravelengine-elementor-widgets' );
	}

	/**
	 * Widget categories.
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
		return 'trips-sticky-tab';
	}

	/**
	 * Style dependencies.
	 */
	public function get_style_depends() {
		wp_register_style( 'wpte-trip-sticky-tabs', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/css/wpte-trips-sticky-tab.css' );

		return array( 'wpte-trip-sticky-tabs' );
	}

	/**
	 * Javascripts dependencies.
	 */
	public function get_script_depends() {
		wp_register_script( 'wpte-trips-sticky-tab', plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'includes/trip-widgets/tripstickytab/trips-sticky-tab.js', array( 'jquery' ), WPTRAVELENGINEEB_VERSION, true );
		return array( 'wpte-trips-sticky-tab', 'trip-wishlist' );
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
		$controls = include WPTRAVELENGINEEB_PATH . 'includes/trip-widgets/tripstickytab/controls.php';
		$this->_wte_add_controls( $controls );
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
		if ( ! is_singular( 'trip' ) ) {
			return array();
		}
		$trip = new \WPTravelEngine\Core\Models\Post\Trip( get_the_ID() );

		$filtered_default_array = array();
		$def_tabs               = array( 'itinerary', 'cost', 'dates', 'faqs', 'map', 'review' );
		foreach ( $settings['trip_tabs']['id'] ?? array() as $key => $i ) {
			$i     = (int) $i;
			$field = $settings['trip_tabs']['field'][ $i ] ?? '';
			if ( 1 === $i || in_array( $field, $def_tabs ) ) {
				continue;
			}
			$filtered_default_array[ 'tab_' . $i ] = array(
				'title'   => (string) $trip->get_setting( 'tab_' . $i . '_title', '' ),
				'content' => (string) $trip->get_setting( 'tab_content.' . $i . '_wpeditor', '' ),
			);
		}
		return $filtered_default_array;
	}


	/**
	 * Get Custom Trip Tabs Field Name.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function get_custom_trip_tabs_field_icon( $key ) {
		$settings = get_option( 'wp_travel_engine_settings', array() );

		// Check if required array key exists
		if ( ! isset( $settings['trip_tabs']['name'] ) ) {
			return array();
		}

		// Extract tab number from key (e.g., 'tab_10' -> '10')
		$tab_number = (int) str_replace( 'tab_', '', $key );

		// Return the field name if it exists, otherwise empty string
		return $settings['trip_tabs']['name'][ $tab_number ] ?? '';
	}
	/**
	 * Helper function to get the sticky tab widgets mapping
	 *
	 * @return array
	 */
	private function get_sticky_tab_widgets_mapping() {
		// Default mapping coming from the tabs
		$default_mapping = array(
			'overview'  => 'wte-overview',
			'itinerary' => 'wte-itinerary',
			'dates'     => 'wte-dates',
			'map'       => 'wte-map',
			'faqs'      => 'wte-faqs',
			'cost'      => 'wte-cost',
			'reviews'   => 'wte-trip-reviews',
			'booking'   => 'wte-booking',
			'enquiry'   => 'wte-enquiry',
		);

		// Get all custom trip tabs
		$custom_trip_tabs = $this->get_custom_trip_tabs();

		// Find all instances of custom-trips-tab widget in the page
		$document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
		$data     = $document ? $document->get_elements_data() : array();

		$custom_tab_widgets = array();
		$this->find_custom_tab_widgets( $data, $custom_tab_widgets );

		// Map each custom tab widget to its selected tab
		$custom_trip_mapping = array();
		foreach ( $custom_tab_widgets as $widget_settings ) {
			if ( isset( $widget_settings['custom_trip_tabs'] ) && ! empty( $widget_settings['custom_trip_tabs'] ) ) {
				$tab_key = $widget_settings['custom_trip_tabs'];
				if ( isset( $custom_trip_tabs[ $tab_key ] ) ) {
					// Use the widget ID to ensure unique mapping for each instance
					$custom_trip_mapping[ $tab_key ] = 'wte-custom-trips-tab';
				}
			}
		}

		return array_merge( $default_mapping, $custom_trip_mapping );
	}

	/**
	 * Helper function to find all custom-trips-tab widgets in the page
	 *
	 * @param array $elements The elements to search through
	 * @param array &$results Array to store found widgets
	 */
	private function find_custom_tab_widgets( $elements, &$results ) {
		foreach ( $elements as $element ) {
			if ( isset( $element['widgetType'] ) && $element['widgetType'] === 'wte-custom-trips-tab' ) {
				$results[] = $element['settings'];
			}
			if ( isset( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$this->find_custom_tab_widgets( $element['elements'], $results );
			}
		}
	}

	/**
	 * Helper function to find widget positions in the page
	 *
	 * @param array  $elements    Array of elements to search through
	 * @param string $widget_name Widget name to search for
	 * @param int    $index      Current index for tracking position
	 * @return int|false Returns index if found, false otherwise
	 */
	private function find_widget_position( $elements, $widget_name, $index = 0 ) {
		foreach ( $elements as $element ) {
			if ( isset( $element['widgetType'] ) ) {
				++$index;
				if ( $element['widgetType'] === $widget_name ) {
					return $index;
				}
			}
			if ( isset( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$result = $this->find_widget_position( $element['elements'], $widget_name, $index );
				if ( $result !== false ) {
					return $result;
				}
				// Add the count of all widgets in this branch
				$index = $this->count_total_widgets( $element['elements'], $index );
			}
		}
		return false;
	}

	/**
	 * Helper function to count total widgets in an element branch
	 *
	 * @param array $elements Array of elements
	 * @param int   $count    Current count
	 * @return int Total count of widgets
	 */
	private function count_total_widgets( $elements, $count ) {
		foreach ( $elements as $element ) {
			if ( isset( $element['widgetType'] ) ) {
				++$count;
			}
			if ( isset( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$count = $this->count_total_widgets( $element['elements'], $count );
			}
		}
		return $count;
	}

	/**
	 * Renders Widget.
	 *
	 * @since 1.3.9
	 */
	protected function render() {

		if ( ! is_singular( 'trip' ) ) {
			echo esc_html__( 'This widget works only on single trip.', 'wptravelengine-elementor-widgets' );
			return;
		}

		$settings = $this->get_settings_for_display();
		$this->get_custom_trip_tabs_field_icon( 'tab_10' );
		$document = \Elementor\Plugin::$instance->documents->get( get_the_ID() );
		$data     = $document ? $document->get_elements_data() : array();

		// Get widget mapping from the existing function
		$tab_widgets = $this->get_sticky_tab_widgets_mapping();

		// Get positions for all widgets
		$widget_positions = array();

		// Get the position of the enquiry form it should be always sticky and last
		foreach ( $tab_widgets as $key => $widget ) {
			$position = $this->find_widget_position( $data, $widget, 0 );
			if ( $position !== false ) {
				$widget_positions[ $key ] = $position;
			}
			if ( $position && $widget === 'wte-enquiry' ) {
				$widget_positions['enquiry'] = 99;
			}
			if ( $position && $widget === 'wte-reviews' ) {
				$widget_positions['reviews'] = 98;
			}
		}

		// Sort tabs by their position
		asort( $widget_positions );

		$trip            = new \WPTravelEngine\Core\Models\Post\Trip( get_the_ID() );
		$overview_title  = $trip->get_setting( 'overview_section_title' );
		$itinerary_title = $trip->get_setting( 'itinerary_section_title' );
		$cost_title      = $trip->get_setting( 'cost_tab_sec_title' );
		$dates_title     = $trip->get_setting( 'dates_section_title' );
		$map_title       = $trip->get_setting( 'map_section_title' );
		$faqs_title      = $trip->get_setting( 'faq_section_title' );
		$reviews_title   = $trip->get_setting( 'reviews_section_title' );
		$booking_title   = $trip->get_setting( 'booking_section_title' );
		$enquiry_title   = $trip->get_setting( 'enquiry_section_title' );
		$tab_orientation = isset( $settings['tab_orientation'] ) ? $settings['tab_orientation'] : 'horizontal';

		// Get custom tabs
		$custom_trip_tabs = $this->get_custom_trip_tabs();

		?>
		<div id="tabs-container" class="wpte-sticky-tabs-wrapper wpte-tabs-container wpte-tabs-sticky wpte-tabs-scrollable clearfix">
			<nav class="wpte-sticky-tabs nav-tab-wrapper">
				<div class="tab-inner-wrapper <?php echo esc_attr( $tab_orientation ); ?>">
					<?php
					foreach ( $widget_positions as $key => $position ) :
						$icon_class = '';
						$label      = '';

						// Check if this is a custom tab
						if ( strpos( $key, 'tab_' ) === 0 && isset( $custom_trip_tabs[ $key ] ) ) {
							$icon_class = 'fas fa-list';
							$label      = $custom_trip_tabs[ $key ]['title'];
						} else {
							switch ( $key ) {
								case 'overview':
									$icon_class = 'fas fa-list';
									$label      = $overview_title ? $overview_title : __( 'Overview', 'wptravelengine-elementor-widgets' );
									break;
								case 'itinerary':
									$icon_class = 'fas fa-route';
									$label      = $itinerary_title ? $itinerary_title : __( 'Itinerary', 'wptravelengine-elementor-widgets' );
									break;
								case 'cost':
									$icon_class = 'fas fa-dollar-sign';
									$label      = $cost_title ? $cost_title : __( 'Cost', 'wptravelengine-elementor-widgets' );
									break;
								case 'dates':
									$icon_class = 'fas fa-calendar';
									$label      = $dates_title ? $dates_title : __( 'Dates', 'wptravelengine-elementor-widgets' );
									break;
								case 'map':
									$icon_class = 'fas fa-map-marker-alt';
									$label      = $map_title ? $map_title : __( 'Map', 'wptravelengine-elementor-widgets' );
									break;
								case 'faqs':
									$icon_class = 'fas fa-question-circle';
									$label      = $faqs_title ? $faqs_title : __( 'FAQs', 'wptravelengine-elementor-widgets' );
									break;
								case 'reviews':
									$icon_class = 'fas fa-star';
									$label      = $reviews_title ? $reviews_title : __( 'Reviews', 'wptravelengine-elementor-widgets' );
									break;
								case 'booking':
									$icon_class = 'fas fa-book';
									$label      = $booking_title ? $booking_title : __( 'Booking', 'wptravelengine-elementor-widgets' );
									break;
								case 'enquiry':
									$icon_class = 'fas fa-envelope';
									$label      = $enquiry_title ? $enquiry_title : __( 'Enquiry', 'wptravelengine-elementor-widgets' );
									break;

								default:
									$icon_class = 'fas fa-list';
									$label      = '';
									break;
							}
						}

						// Skip if no icon class or label is set
						if ( empty( $icon_class ) || empty( $label ) ) {
							continue;
						}

						// should be handled differently for enquiry form
						if ( $key === 'enquiry' ) {
							$href = '#wte_enquiry_form_scroll_wrapper';
						} else {
							$href = '#wte-' . esc_attr( $key );
						}
						?>
						<div class="tab-anchor-wrapper">
							<h2 class="wte-tab-title">
								<a href="<?php echo esc_attr( $href ); ?>" class="nav-tab nb-tab-trigger <?php echo ( $key === array_key_first( $widget_positions ) ) ? ' active' : ''; ?>">
									<i class="<?php echo esc_attr( $icon_class ); ?>"></i> <?php echo esc_html( $label ); ?>
								</a>
							</h2>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="wpte-sticky-tab-mobile <?php echo esc_attr( $tab_orientation ); ?>">
					<?php
					foreach ( $widget_positions as $key => $position ) :
						$icon_class = '';
						$label      = '';

						// Check if this is a custom tab
						if ( strpos( $key, 'tab_' ) === 0 && isset( $custom_trip_tabs[ $key ] ) ) {
							$icon_class = 'fas fa-list';
							$label      = $custom_trip_tabs[ $key ]['title'];
						} else {
							switch ( $key ) {
								case 'overview':
									$icon_class = 'fas fa-list';
									$label      = $overview_title ? $overview_title : __( 'Overview', 'wptravelengine-elementor-widgets' );
									break;
								case 'itinerary':
									$icon_class = 'fas fa-route';
									$label      = $itinerary_title ? $itinerary_title : __( 'Itinerary', 'wptravelengine-elementor-widgets' );
									break;
								case 'cost':
									$icon_class = 'fas fa-dollar-sign';
									$label      = $cost_title ? $cost_title : __( 'Cost', 'wptravelengine-elementor-widgets' );
									break;
								case 'dates':
									$icon_class = 'fas fa-calendar';
									$label      = $dates_title ? $dates_title : __( 'Dates', 'wptravelengine-elementor-widgets' );
									break;
								case 'map':
									$icon_class = 'fas fa-map-marker-alt';
									$label      = $map_title ? $map_title : __( 'Map', 'wptravelengine-elementor-widgets' );
									break;
								case 'faqs':
									$icon_class = 'fas fa-question-circle';
									$label      = $faqs_title ? $faqs_title : __( 'FAQs', 'wptravelengine-elementor-widgets' );
									break;
								case 'reviews':
									$icon_class = 'fas fa-star';
									$label      = $reviews_title ? $reviews_title : __( 'Reviews', 'wptravelengine-elementor-widgets' );
									break;
								case 'booking':
									$icon_class = 'fas fa-book';
									$label      = $booking_title ? $booking_title : __( 'Booking', 'wptravelengine-elementor-widgets' );
									break;
								case 'enquiry':
									$icon_class = 'fas fa-envelope';
									$label      = $enquiry_title ? $enquiry_title : __( 'Enquiry', 'wptravelengine-elementor-widgets' );
									break;

								default:
									$icon_class = 'fas fa-list';
									$label      = '';
									break;
							}
						}

						// Skip if no icon class or label is set
						if ( empty( $icon_class ) || empty( $label ) ) {
							continue;
						}

						// should be handled differently for enquiry form
						if ( $key === 'enquiry' ) {
							$href = '#wte_enquiry_form_scroll_wrapper';
						} else {
							$href = '#wte-' . esc_attr( $key );
						}
						?>
						<div class="tab-anchor-wrapper">
							<h2 class="wte-tab-title">
								<a href="<?php echo esc_attr( $href ); ?>" class="nav-tab nb-tab-trigger <?php echo ( $key === array_key_first( $widget_positions ) ) ? ' active' : ''; ?>">
									<i class="<?php echo esc_attr( $icon_class ); ?>"></i> <?php echo esc_html( $label ); ?>
								</a>
							</h2>
						</div>
					<?php endforeach; ?>
				</div>
			</nav>
		</div>
		<?php
	}
}