<?php
namespace WPTRAVELENGINEEB;

/**
 * Main Plugin File.
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var null|Plugin
	 */
	protected static $instance = null;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version = WPTRAVELENGINEEB_VERSION;

		/**
		 * Deinfe constants.
		 */
		$this->define_constants();

		/**
		 * Includes.
		 */
		$this->includes();

		/**
		 * Init hooks.
		 */
		$this->init_hooks();
	}

	/**
	 * Returns instance of the Class.
	 *
	 * @since 1.0.0
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Define constants
	 *
	 * @since 1.0.0
	 */
	public function define_constants() {
	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		include_once WPTRAVELENGINEEB_PATH . 'includes/class-widgets.php';

		/**
		 * Add custom templates import functionality if elementor is activated
		 */
		if ( class_exists( 'Elementor\\Plugin' ) ) {
			require_once WPTRAVELENGINEEB_PATH . 'includes/import-templates/elementor-import-templates.php';
		}

		/**
		 * Header and Footer Builder functionality.
		 *
		 * @since 1.4.8
		 */
		require_once WPTRAVELENGINEEB_PATH . 'includes/class-header-footer-builder.php';
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	public function init_hooks() {
		add_action( 'wpte_save_and_continue_additional_meta_data', array( $this, 'update_has_sale_meta' ) );
		add_action( 'save_post_' . WP_TRAVEL_ENGINE_POST_TYPE, array( $this, 'update_has_sale_meta' ) );

		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ), 9999 );
		add_action( 'elementor/frontend/before_enqueue_styles', array( $this, 'register_styles' ), 20 );
		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'register_styles' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_offcanvas_editor_script' ) );

		// Ensure styles and scripts are loaded on frontend for Global Templates.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		// Add elementor-page class to body for Global Templates (archive pages, etc.).
		add_filter( 'body_class', array( $this, 'add_elementor_page_body_class' ) );

		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );
		// Set default elementor library post.
		// If init has already fired (e.g. activated via AJAX during demo import), call directly.
		if ( did_action( 'init' ) ) {
			$this->create_elementor_library_post_once();
		} else {
			add_action( 'init', array( $this, 'create_elementor_library_post_once' ) );
		}

		/**
		 * Elementor Ajax.
		 *
		 * @since 1.4.3
		 */
		add_action( 'wp_ajax_elementor_ajax', array( $this, 'elementor_ajax' ), 2 );
	}

	/**
	 * Normalizes the html tag of the elementor editor.
	 *
	 * @since 1.4.3
	 */
	public function elementor_ajax() {

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$my_actions = json_decode( wp_unslash( $_REQUEST['actions'] ?? '' ), true );

		if ( ! is_array( $my_actions ) ) {
			return;
		}

		$update_actions = false;
		foreach ( $my_actions as &$args ) {
			$action = $args['action'] ?? '';
			switch ( $action ) :
				case 'save_builder':
					$elements = &$args['data']['elements'];

					if ( ! $elements ) {
						return;
					}

					foreach ( $elements as &$element ) {
						$inner_elements = &$element['elements'];
						if ( (bool) $inner_elements ) {
							foreach ( $inner_elements as &$inner_element ) {
								if ( isset( $inner_element['settings']['html_tag'] ) ) {
									$inner_element['settings']['html_tag'] = wptravelengineeb_normalize_html_tag( $inner_element['settings']['html_tag'] );
									$update_actions                        = true;
								}
							}
						}
					}

					break;
				case 'render_widget':
					$settings = &$args['data']['data']['settings'];

					if ( is_array( $settings ) && isset( $settings['html_tag'] ) ) {
						$settings['html_tag'] = wptravelengineeb_normalize_html_tag( $settings['html_tag'] );
						$update_actions       = true;
					}

					break;
				default:
					return;
			endswitch;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( $update_actions ) {
			$_POST['actions'] = $_REQUEST['actions'] = wp_slash( wp_json_encode( $my_actions ) );
		}
	}

	/**
	 * Registers custom controls.
	 *
	 * @param object $controls_manager The controls manager with which to register the custom control.
	 */
	public function register_controls( $controls_manager ) {
		require_once WPTRAVELENGINEEB_PATH . 'includes/controls/class-trip-selector-control.php';
		$controls_manager->register( new \WPTRAVELENGINEEB\Controls\Trip_Selector_Control() );
	}

	/**
	 * Register Scripts.
	 */
	public function register_scripts() {
		// Enqueue owl-carousel css/js from WTE.
		$url_prefix = apply_filters( 'wte_vendors_directory', plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'assets/lib/' );
		wp_enqueue_script( 'owl-carousel', $url_prefix . 'owl-carousel-2.3.4/owl.carousel.js', array( 'jquery' ), '2.3.4', true );
		wp_enqueue_style( 'owl-carousel', $url_prefix . 'owl-carousel-2.3.4/owl.carousel.css', array(), '2.3.4' );

		// Enqueue dropzone,rateyo css/js for review widget from WTE.
		wp_enqueue_script( 'wte-dropzone', $url_prefix . 'dropzone/dropzone.min.js', array( 'jquery' ), '5.9.2', true );
		wp_enqueue_style( 'wte-dropzone', $url_prefix . 'dropzone/dropzone.min.css', array(), '5.9.2' );

		// Ensure trip-wishlist script is registered with localization data for Elementor widgets.
		$this->register_wishlist_script();
		if ( defined( 'WTE_TRIP_REVIEW_VERSION' ) ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'datepicker-style', WTE_TRIP_REVIEW_FILE_URL . '/assets/css/lib/datepicker-style.css', WTE_TRIP_REVIEW_VERSION, 'all' );
			wp_enqueue_style( 'jquery-rateyo', WTE_TRIP_REVIEW_FILE_URL . '/assets/css/lib/jquery.rateyo.min.css', array(), WTE_TRIP_REVIEW_VERSION, 'all' );
			wp_enqueue_script( 'jquery-rateyo', WTE_TRIP_REVIEW_FILE_URL . '/assets/js/lib/jquery.rateyo.min.js', array( 'jquery' ), WTE_TRIP_REVIEW_VERSION, true );
			wp_enqueue_script( 'jquery-appear', WTE_TRIP_REVIEW_FILE_URL . '/assets/js/lib/jquery.appear.js', array( 'jquery' ), WTE_TRIP_REVIEW_VERSION, true );
		}

		// Registration of editor js for slider widget.
		wp_register_script(
			'wte-editor',
			plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/editor.js',
			array( 'jquery', 'elementor-frontend' ),
			filemtime( plugin_dir_path( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/editor.js' ),
			true
		);
		wp_register_script(
			'wptravelengineeeb-trips',
			plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/frontend.js',
			array( 'jquery', 'elementor-frontend' ),
			filemtime( plugin_dir_path( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/frontend.js' ),
			true
		);

		// Off Canvas widget script — enqueued on all Elementor pages (tiny file, always needed when header is active).
		wp_enqueue_script(
			'wte-offcanvas',
			plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/wte-offcanvas.js',
			array(),
			WPTRAVELENGINEEB_VERSION,
			true
		);

		$wpte_animation_path = plugin_dir_path( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/wpte-animation.js';
		if ( file_exists( $wpte_animation_path ) ) {
			wp_register_script(
				'wpte-animation',
				plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/wpte-animation.js',
				array(),
				filemtime( $wpte_animation_path ),
				true
			);
			wp_enqueue_script( 'wpte-animation' );
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$post = get_post();
			if ( $post && is_object( $post ) ) {
				$elementor_page = \Elementor\Plugin::$instance->documents->get( $post->ID );
				if ( $elementor_page && $elementor_page->is_built_with_elementor() ) {
					$settings = $elementor_page->get_properties();
					if ( isset( $settings['support_wp_page_templates'] ) ) {
						$trip_id = $post->ID;
						$wtetrip = \wte_get_trip( $trip_id );
						$package = $wtetrip->default_package;
						if ( isset( $package ) && $package ) {
							wp_enqueue_script( 'wte-redux' );
							wp_enqueue_script( 'wp-travel-engine' );
							wte_get_template( 'script-templates/booking-process/wte-booking.php' );
						}
					}
				}
			}
		}
	}

	/**
	 * Enqueue Off Canvas editor script in the Elementor editor parent frame.
	 *
	 * @since 1.5.2
	 */
	public function enqueue_offcanvas_editor_script() {
		wp_enqueue_script(
			'wte-offcanvas-editor',
			plugin_dir_url( WPTRAVELENGINEEB_FILE__ ) . 'dist/js/wte-offcanvas-editor.js',
			array( 'jquery' ),
			WPTRAVELENGINEEB_VERSION,
			true
		);
	}

	/**
	 * Register Styles.
	 */
	public function register_styles() {
		$plugin_url = plugin_dir_url( WPTRAVELENGINEEB_FILE__ );

		// Register and enqueue main widget styles.
		wp_register_style( 'wte-elementor-widget-styles', $plugin_url . 'dist/css/wte-elementor-widgets.css', array(), WPTRAVELENGINEEB_VERSION );
		wp_enqueue_style( 'wte-elementor-widget-styles' );
		wp_register_style( 'wte-elementor-swiper-styles', $plugin_url . 'dist/css/swiper.min.css', array(), WPTRAVELENGINEEB_VERSION );
		wp_enqueue_style( 'wte-elementor-swiper-styles' );

		// Pre-register all widget-specific styles so they're available when Elementor renders widgets.
		wp_register_style( 'wpte-adv-trips', $plugin_url . 'dist/css/wpte-adv-trips.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-trips-list', $plugin_url . 'dist/css/wpte-trips-list.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-trip-slider', $plugin_url . 'dist/css/wpte-trips-slider.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-trips-tax-slider', $plugin_url . 'dist/css/wpte-trips-tax-slider.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-trips-accordion', $plugin_url . 'dist/css/wpte-trips-accordion.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-trip-tabs', $plugin_url . 'dist/css/wpte-trips-tab.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-trips-module', $plugin_url . 'dist/css/wpte-trips-module.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-feat-trips', $plugin_url . 'dist/css/wpte-feat-trips.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-trips-tax', $plugin_url . 'dist/css/wpte-trips-tax.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'trip-tax-module', $plugin_url . 'dist/css/wpte-tax-module.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
		wp_register_style( 'wpte-tour-search', $plugin_url . 'dist/css/wpte-tour-search.css', array( 'wte-elementor-widget-styles' ), WPTRAVELENGINEEB_VERSION );
	}

	/**
	 * Add elementor-page class to body when Elementor is active.
	 * This is required for widget CSS selectors that use .elementor-page prefix.
	 *
	 * @param array $classes Body classes.
	 * @return array Modified body classes.
	 * @since 1.4.7
	 * @since 1.5.0 Updated to check if the page is built with Elementor.
	 */
	public function add_elementor_page_body_class( $classes ) {
		if ( ! in_array( 'elementor-page', $classes, true ) && class_exists( '\Elementor\Plugin' ) ) {
			$post = get_post();
			if ( $post && is_object( $post ) ) {
				$elementor_page = \Elementor\Plugin::$instance->documents->get( $post->ID );
				if ( $elementor_page && $elementor_page->is_built_with_elementor() ) {
					$classes[] = 'elementor-page';
				}
			}
		}
		return $classes;
	}

	/**
	 * Register and localize the trip-wishlist script for Elementor widgets.
	 *
	 * The core WP Travel Engine plugin registers the trip-wishlist script but only
	 * enqueues it on specific pages. This method ensures the script is properly
	 * registered with its localization data so Elementor widgets can use it as a dependency.
	 *
	 * @since 1.4.7
	 */
	protected function register_wishlist_script() {
		$wte_dist_url = plugin_dir_url( WP_TRAVEL_ENGINE_FILE_PATH ) . 'dist/';
		$version      = defined( 'WP_TRAVEL_ENGINE_VERSION' ) ? WP_TRAVEL_ENGINE_VERSION : '1.0.0';

		// Register the script if not already registered by core WP Travel Engine.
		if ( ! wp_script_is( 'trip-wishlist', 'registered' ) ) {
			wp_register_script(
				'trip-wishlist',
				$wte_dist_url . 'public/trip-wishlist.js',
				array( 'jquery' ),
				$version,
				true
			);

			wp_register_style(
				'trip-wishlist',
				$wte_dist_url . 'public/trip-wishlist.css',
				array(),
				$version
			);
		}

		// Add localization data for the wishlist functionality.
		wp_localize_script(
			'trip-wishlist',
			'wptravelengineWishlist',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'action'  => 'wte_user_wishlist',
				'nonce'   => wp_create_nonce( 'wp_xhr' ),
			)
		);

		// The trip-wishlist.js script requires wte_advanced_search to be defined.
		// Provide the data from TripSearch module or a minimal fallback.
		if ( class_exists( '\WPTravelEngine\Modules\TripSearch' ) && method_exists( '\WPTravelEngine\Modules\TripSearch', 'get_localized_data' ) ) {
			$advanced_search_data = \WPTravelEngine\Modules\TripSearch::get_localized_data();
		} else {
			// Provide minimal fallback data structure.
			$advanced_search_data = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			);
		}

		wp_localize_script(
			'trip-wishlist',
			'wte_advanced_search',
			$advanced_search_data
		);
	}

	/**
	 *
	 * Checks if trip has sale meta.
	 *
	 * @param int $post_id The ID of the post to update.
	 * @since 1.0.0
	 */
	public function update_has_sale_meta( $post_id ) {
		/**
		 * This should be handled from core.
		 */
		$should_update = apply_filters( 'wte_search_param_has_sale_meta', true );

		if ( ! $should_update ) {
			return;
		}

		\wp_cache_delete( $post_id, 'trips' );
		$trip = \wte_get_trip( $post_id );

		if ( $trip instanceof \WPTravelEngine\Posttype\Trip ) {
			if ( $trip->has_sale ) {
				update_post_meta( $post_id, '_s_has_sale', 'yes' );
			} else {
				update_post_meta( $post_id, '_s_has_sale', '' );
			}
		}
	}

	/**
	 * Creates a custom template post forsingle trip page elementor widgets in the custom post type 'elementor_library'.
	 *
	 * @since 1.3.0
	 * */
	public function create_elementor_library_post_once() {
		$post_created      = get_option( '_wte_elementor_widget_default_template', false );
		$elementor_content = '[{"id":"165a8bc9","elType":"container","settings":{"flex_direction":"column","content_width":"full","padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":true}},"elements":[{"id":"9768019","elType":"container","settings":{"background_background":"gradient","background_color":"#000000","background_color_b":"#00000000","padding":{"unit":"px","top":"32","right":"0","bottom":"32","left":"0","isLinked":false},"position":"absolute","z_index":11},"elements":[{"id":"fa4bf90","elType":"widget","settings":{"item_gap":{"unit":"px","size":24,"sizes":[]},"color":"#FFFFFF","hover_color":"#1A84EE","active_color":"#1A84EE","separator_color":"#B9B9B9"},"elements":[],"widgetType":"wte-breadcrumbs"}],"isInner":false},{"id":"d6f3df3","elType":"widget","settings":{"carousel_height":{"unit":"px","size":700,"sizes":[]},"arrow_bg_color":"#FFFFFF00","arrow_color":"#FFFFFF","arrow_border_border":"solid","arrow_border_width":{"unit":"px","top":"2","right":"2","bottom":"2","left":"2","isLinked":true},"arrow_border_color":"#FFFFFF","arrow_bg_hover_color":"#1A84EE","arrow_hover_color":"#FFFFFF","arrow_size":{"unit":"px","size":25,"sizes":[]},"button_border_radius":{"unit":"px","top":"4","right":"4","bottom":"4","left":"4","isLinked":true},"button_bg_hover_color":"#1A84EE"},"elements":[],"widgetType":"wte-carousel"}],"isInner":false},{"id":"31db0a6a","elType":"container","settings":{"flex_direction":"row","flex_gap":{"unit":"px","size":32},"padding":{"unit":"px","top":"60","right":"10","bottom":"60","left":"10","isLinked":false}},"elements":[{"id":"777fa8d","elType":"container","settings":{"flex_direction":"column","content_width":"full","width":{"unit":"%","size":"66.6666"},"flex_gap":{"unit":"px","size":0,"sizes":[]},"background_background":"classic","background_color":"#FFFFFF","border_border":"solid","border_width":{"unit":"px","top":"1","right":"1","bottom":"1","left":"1","isLinked":true},"border_color":"#E0E0E0","border_radius":{"unit":"px","top":"4","right":"4","bottom":"4","left":"4","isLinked":true},"padding":{"unit":"px","top":"24","right":"24","bottom":"24","left":"24","isLinked":true}},"elements":[{"id":"7106b4d3","elType":"container","settings":{"flex_gap":{"unit":"px","size":8,"sizes":[]},"padding":{"unit":"px","top":"0","right":"70","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"2b175cd","elType":"widget","settings":{"title_typography_typography":"custom","title_typography_font_family":"Amiri","title_typography_font_size":{"unit":"px","size":40,"sizes":[]},"title_color":"#000000"},"elements":[],"widgetType":"wte-title"},{"id":"5b5fb1d","elType":"widget","settings":{"duration_bg_color":"#1A84EE","_position":"absolute","_offset_orientation_h":"end","_offset_y":{"unit":"px","size":0,"sizes":[]}},"elements":[],"widgetType":"wte-duration"}],"isInner":false},{"id":"5a19550","elType":"widget","settings":{"star_gap":{"unit":"px","size":10,"sizes":[]},"color":"#929292","icon_size":{"unit":"px","size":22,"sizes":[]},"margin":{"unit":"px","top":"0","right":"8","bottom":"0","left":"0","isLinked":false}},"elements":[],"widgetType":"wte-trip-ratings"},{"id":"33eefebf","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"c3ba2da","elType":"widget","settings":{"factsgap":{"unit":"px","size":20,"sizes":[]},"facts_title_typography_typography":"custom","facts_title_typography_font_size":{"unit":"px","size":16,"sizes":[]},"facts_title_typography_line_height":{"unit":"em","size":1,"sizes":[]},"facts_title_color":"#1A84EE","facts_content_typography_typography":"custom","facts_content_typography_font_size":{"unit":"px","size":20,"sizes":[]},"facts_content_typography_font_weight":"500","icon_color":"#1A84EE","icon_size":{"unit":"px","size":35,"sizes":[]},"_margin":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":true},"noofcolumn":2},"elements":[],"widgetType":"wte-trip-facts"},{"id":"1029c111","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"18c9aca9","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"d77338b","elType":"widget","settings":{"general_typography_typography":"custom","general_typography_line_height":{"unit":"em","size":1.75,"sizes":[]}},"elements":[],"widgetType":"wte-overview"}],"isInner":false},{"id":"5b23f050","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"66eaaa5","elType":"widget","settings":{"general_typography_typography":"custom","general_typography_line_height":{"unit":"px","size":29,"sizes":[]},"_padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":true}},"elements":[],"widgetType":"wte-highlights"}],"isInner":false},{"id":"75d103d0","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"5a106502","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"1bea28b","elType":"widget","settings":{"title":"Itinerary","expand_all_text":"Expand All","card_typography_typography":"custom","card_typography_line_height":{"unit":"em","size":1.75,"sizes":[]},"card_padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":true},"title_typography_typography":"custom","title_typography_font_family":"Amiri","title_typography_font_size":{"unit":"px","size":32,"sizes":[]},"title_typography_font_weight":"500","title_padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false},"title_margin":{"unit":"px","top":"0","right":"0","bottom":"24","left":"0","isLinked":false},"days_title_typography_typography":"custom","days_title_typography_font_family":"Amiri","days_title_typography_font_weight":"600","expand_all_toggle":"#1A84EE","expand_icon":"#1A84EE","expand_icon_size":{"unit":"px","size":"","sizes":[]},"day_icon":"#020101","day_bg_icon":"#1A84EE"},"elements":[],"widgetType":"wte-itinerary"},{"id":"35ee5b4b","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"90fd291","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"4d9ad4b8","elType":"widget","settings":{"title":"What is included in this trek package?","title_color":"#000000","typography_typography":"custom","typography_font_family":"Amiri","typography_font_size":{"unit":"px","size":32,"sizes":[]},"typography_font_weight":"500","_margin":{"unit":"px","top":"0","right":"0","bottom":"24","left":"0","isLinked":false}},"elements":[],"widgetType":"heading"},{"id":"b671bea","elType":"widget","settings":{"costincludes_margin":{"unit":"px","top":"0","right":"0","bottom":"16","left":"0","isLinked":false},"icon_color":"#00C004","icon":{"value":"far fa-check-circle","library":"fa-regular"}},"elements":[],"widgetType":"wte-costincludes"}],"isInner":false}],"isInner":false},{"id":"521bb5f9","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"20d94521","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"410ede18","elType":"widget","settings":{"title":"What is excluded in this trek package?","title_color":"#000000","typography_typography":"custom","typography_font_family":"Amiri","typography_font_size":{"unit":"px","size":32,"sizes":[]},"typography_font_weight":"500","_margin":{"unit":"px","top":"0","right":"0","bottom":"24","left":"0","isLinked":false}},"elements":[],"widgetType":"heading"}],"isInner":false},{"id":"4da8e34","elType":"widget","settings":{"costexcludes_margin":{"unit":"px","top":"0","right":"0","bottom":"8","left":"0","isLinked":false},"icon_color":"#B1B1B1","icon":{"value":"far fa-times-circle","library":"fa-regular"}},"elements":[],"widgetType":"wte-costexcludes"},{"id":"6b343708","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"9159798","elType":"widget","settings":{"title":"Join Our Fixed Trip Starting Date","dateLabel":"TRIP DATES","availabilityLabel":"AVAILABILITY","priceLabel":"PRICE","spaceLabel":"SPACE LEFT","loadMore":"Load More","showLess":"Show Less","typography_typography":"custom","typography_font_family":"Amiri","typography_font_weight":"500","title_margin":{"unit":"px","top":"0","right":"0","bottom":"24","left":"0","isLinked":false},"chooseDate_padding":{"unit":"px","top":"8","right":"16","bottom":"8","left":"16","isLinked":false},"chooseDate_border_radius":{"unit":"px","top":"4","right":"4","bottom":"4","left":"4","isLinked":true}},"elements":[],"widgetType":"wte-fixed-starting-date"},{"id":"6aa3b605","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"15f6d65d","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"1319ad","elType":"widget","settings":{"title":"Official Trek Map","title_color":"#000000","typography_typography":"custom","typography_font_family":"Amiri","typography_font_size":{"unit":"px","size":32,"sizes":[]},"typography_font_weight":"500","_margin":{"unit":"px","top":"0","right":"0","bottom":"24","left":"0","isLinked":false}},"elements":[],"widgetType":"heading"}],"isInner":false},{"id":"ea0a0ed","elType":"widget","settings":[],"elements":[],"widgetType":"wte-map"},{"id":"6a1b72dd","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"2d014a74","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"32c911f","elType":"widget","settings":{"expand_all_label":"Expand All","generals_gap":{"unit":"px","size":0,"sizes":[]},"title_typography_typography":"custom","title_typography_font_family":"Amiri","title_typography_font_size":{"unit":"px","size":20,"sizes":[]},"title_margin":{"unit":"px","top":"0","right":"0","bottom":"24","left":"0","isLinked":false},"faq_title_typography_typography":"custom","faq_title_typography_font_family":"Amiri","faq_title_typography_font_size":{"unit":"px","size":22,"sizes":[]},"faq_title_typography_font_weight":"500","toogle_background_color":"#1A84EE","icon_color":"#1A84EE"},"elements":[],"widgetType":"wte-faqs"}],"isInner":false},{"id":"6b699180","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"0422377","elType":"widget","settings":{"reviewTitle":"Write a Review.","buttonText":"Submit"},"elements":[],"widgetType":"wte-trip-review-form"},{"id":"647531d4","elType":"widget","settings":{"text":"Divider","color":"#00000026","gap":{"unit":"px","size":32,"sizes":[]}},"elements":[],"widgetType":"divider"},{"id":"2654405c","elType":"container","settings":{"flex_gap":{"unit":"px","size":0,"sizes":[]}},"elements":[{"id":"6e2b624b","elType":"widget","settings":{"title":"Our Customer Reviews","title_color":"#000000","typography_typography":"custom","typography_font_family":"Amiri","typography_font_size":{"unit":"px","size":32,"sizes":[]},"typography_font_weight":"500","_margin":{"unit":"px","top":"0","right":"0","bottom":"24","left":"0","isLinked":false}},"elements":[],"widgetType":"heading"},{"id":"886da22","elType":"widget","settings":[],"elements":[],"widgetType":"wte-trip-reviews"}],"isInner":false},{"id":"bfa716f","elType":"widget","settings":{"enquiryTitle":"You can send your enquiry via the form below.","buttonText":"Send Email","enquiry_typography_typography":"custom","enquiry_typography_font_family":"Amiri"},"elements":[],"widgetType":"wte-enquiry"}],"isInner":false},{"id":"7274caa2","elType":"container","settings":{"flex_direction":"column","content_width":"full","width":{"unit":"%","size":"33.3333"},"padding":{"unit":"px","top":"0","right":"0","bottom":"0","left":"0","isLinked":false}},"elements":[{"id":"f38bf0e","elType":"widget","settings":{"booking_border_border":"solid","booking_border_width":{"unit":"px","top":"1","right":"1","bottom":"1","left":"1","isLinked":true},"booking_border_color":"#EBEBEB","price_typography_typography":"custom","price_typography_font_family":"Amiri","price_typography_font_weight":"600","strike_typography_typography":"custom","strike_typography_font_family":"Amiri"},"elements":[],"widgetType":"wte-booking"}],"isInner":false}],"isInner":false}]';
		// Check if the post has already been created.
		if ( ! $post_created ) {
			$post_data = array(
				'post_title'  => 'WPTE Single Trip Template',
				'post_status' => 'publish',
				'post_type'   => 'elementor_library',
			);
			$post_id   = wp_insert_post( $post_data );
			if ( $post_id ) {
				update_post_meta( $post_id, '_elementor_data', $elementor_content );
				update_post_meta( $post_id, '_elementor_page_layout', 'full_width' );
				update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
				update_post_meta( $post_id, '_elementor_template_type', 'page' );
			}
			update_option( '_wte_elementor_widget_default_template', true );
		}
	}
}

new Plugin();
