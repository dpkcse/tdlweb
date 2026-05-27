<?php
/**
 * Header Footer Builder.
 *
 * Registers custom post types for Header and Footer builders
 * and adds them to the WP Travel Engine admin menu.
 *
 * @package WPTRAVELENGINEEB
 * @since 1.4.8
 */

namespace WPTRAVELENGINEEB;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Header Footer Builder class.
 *
 * @since 1.4.8
 */
class Header_Footer_Builder {

	/**
	 * Instance of the class.
	 *
	 * @var Header_Footer_Builder|null
	 */
	private static $instance = null;

	/**
	 * Header post type name.
	 *
	 * @var string
	 */
	const HEADER_POST_TYPE = 'wpte_header';

	/**
	 * Footer post type name.
	 *
	 * @var string
	 */
	const FOOTER_POST_TYPE = 'wpte_footer';

	/**
	 * Mobile Menu post type name.
	 *
	 * @var string
	 */
	const MOBILE_MENU_POST_TYPE = 'wpte_mobile_menu';

	/**
	 * Mobile Header post type name.
	 *
	 * @var string
	 */
	const MOBILE_HEADER_POST_TYPE = 'wpte_mobile_header';

	/**
	 * Off Canvas post type name.
	 *
	 * @var string
	 */
	const OFFCANVAS_POST_TYPE = 'wpte_offcanvas';

	/**
	 * Menu slug for WPTE Builder.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'wpte-builder';

	/**
	 * Returns the instance of the class.
	 *
	 * @return Header_Footer_Builder
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Only initialize if Travel Monster theme is active.
		if ( ! self::is_travel_monster_theme_active() ) {
			return;
		}

		// If init has already fired (e.g. activated via AJAX during demo import), call directly.
		if ( did_action( 'init' ) ) {
			$this->register_post_types();
		} else {
			add_action( 'init', array( $this, 'register_post_types' ), 15 );
		}
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ), 99 );
		add_filter( 'single_template', array( $this, 'load_canvas_template' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_header_footer_category' ), 1 );

		// Include customizer settings.
		add_action( 'after_setup_theme', array( $this, 'include_customizer' ), 20 );

		// Frontend hooks for header/footer rendering.
		add_action( 'wp', array( $this, 'setup_header_footer_hooks' ) );

		// Enqueue admin styles (for menu icon).
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );

		// Enqueue header builder styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_header_builder_styles' ) );
	}

	/**
	 * Include customizer settings.
	 *
	 * @return void
	 */
	public function include_customizer() {
		require_once WPTRAVELENGINEEB_PATH . 'includes/class-customizer.php';
	}

	/**
	 * Enqueue admin styles for menu icon.
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {
		$css = '
			.dashicons-wpte-builder-icon::before {
				content: "";
				display: block;
				width: 20px;
				height: 20px;
				margin: 0 auto;
				mask-image: url("data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' fill=\'none\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath fill-rule=\'evenodd\' clip-rule=\'evenodd\' d=\'M2 1.75C2 0.784 2.96492 0 4.15385 0H15.8462C17.0351 0 18 0.784 18 1.75V3.25C18 3.71413 17.7731 4.15925 17.3692 4.48744C16.9652 4.81563 16.4174 5 15.8462 5H4.15385C3.58261 5 3.03477 4.81563 2.63085 4.48744C2.22692 4.15925 2 3.71413 2 3.25V1.75ZM4.15385 1.5C4.07224 1.5 3.99398 1.52634 3.93627 1.57322C3.87857 1.62011 3.84615 1.6837 3.84615 1.75V3.25C3.84615 3.388 3.984 3.5 4.15385 3.5H15.8462C15.9278 3.5 16.006 3.47366 16.0637 3.42678C16.1214 3.37989 16.1538 3.3163 16.1538 3.25V1.75C16.1538 1.6837 16.1214 1.62011 16.0637 1.57322C16.006 1.52634 15.9278 1.5 15.8462 1.5H4.15385Z\' fill=\'%239CA2A7\'/%3E%3Cpath fill-rule=\'evenodd\' clip-rule=\'evenodd\' d=\'M2 15.75C2 14.784 2.96492 14 4.15385 14H15.8462C17.0351 14 18 14.784 18 15.75V17.25C18 17.7141 17.7731 18.1592 17.3692 18.4874C16.9652 18.8156 16.4174 19 15.8462 19H4.15385C3.58261 19 3.03477 18.8156 2.63085 18.4874C2.22692 18.1592 2 17.7141 2 17.25V15.75ZM4.15385 15.5C4.07224 15.5 3.99398 15.5263 3.93627 15.5732C3.87857 15.6201 3.84615 15.6837 3.84615 15.75V17.25C3.84615 17.388 3.984 17.5 4.15385 17.5H15.8462C15.9278 17.5 16.006 17.4737 16.0637 17.4268C16.1214 17.3799 16.1538 17.3163 16.1538 17.25V15.75C16.1538 15.6837 16.1214 15.6201 16.0637 15.5732C16.006 15.5263 15.9278 15.5 15.8462 15.5H4.15385Z\' fill=\'%239CA2A7\'/%3E%3Cg opacity=\'0.72\'%3E%3Cpath d=\'M3 8.25C3 7.56 3.686 7 4.53125 7H4.96875C5.1428 7 5.30972 7.05644 5.43279 7.15691C5.55586 7.25737 5.625 7.39363 5.625 7.53571C5.625 7.67779 5.55586 7.81406 5.43279 7.91452C5.30972 8.01499 5.1428 8.07143 4.96875 8.07143H4.53125C4.47323 8.07143 4.41759 8.09024 4.37657 8.12373C4.33555 8.15722 4.3125 8.20264 4.3125 8.25V8.60714C4.3125 8.74922 4.24336 8.88548 4.12029 8.98595C3.99722 9.08642 3.8303 9.14286 3.65625 9.14286C3.4822 9.14286 3.31528 9.08642 3.19221 8.98595C3.06914 8.88548 3 8.74922 3 8.60714V8.25Z\' fill=\'%239CA2A7\'/%3E%3Cpath d=\'M3 10.75C3 11.44 3.686 12 4.53125 12H4.96875C5.1428 12 5.30972 11.9435 5.43279 11.8431C5.55586 11.7426 5.625 11.6063 5.625 11.4643C5.625 11.3222 5.55586 11.1859 5.43279 11.0855C5.30972 10.985 5.1428 10.9285 4.96875 10.9285H4.53125C4.47323 10.9285 4.41759 10.9097 4.37657 10.8762C4.33555 10.8428 4.3125 10.7973 4.3125 10.75V10.3928C4.3125 10.2508 4.24336 10.1145 4.12029 10.014C3.99722 9.91356 3.8303 9.85712 3.65625 9.85712C3.4822 9.85712 3.31528 9.91356 3.19221 10.014C3.06914 10.1145 3 10.2508 3 10.3928V10.75Z\' fill=\'%239CA2A7\'/%3E%3Cpath d=\'M15.4688 7C16.314 7 17 7.56 17 8.25V8.60714C17 8.74922 16.9309 8.88548 16.8078 8.98595C16.6847 9.08642 16.5178 9.14286 16.3438 9.14286C16.1697 9.14286 16.0028 9.08642 15.8797 8.98595C15.7566 8.88548 15.6875 8.74922 15.6875 8.60714V8.25C15.6875 8.20264 15.6645 8.15722 15.6234 8.12373C15.5824 8.09024 15.5268 8.07143 15.4688 8.07143H15.0312C14.8572 8.07143 14.6903 8.01499 14.5672 7.91452C14.4441 7.81406 14.375 7.67779 14.375 7.53571C14.375 7.39363 14.4441 7.25737 14.5672 7.15691C14.6903 7.05644 14.8572 7 15.0312 7H15.4688Z\' fill=\'%239CA2A7\'/%3E%3Cpath d=\'M15.4688 12C15.8749 12 16.2643 11.8683 16.5515 11.6339C16.8387 11.3994 17 11.0815 17 10.75V10.3928C17 10.2508 16.9309 10.1145 16.8078 10.014C16.6847 9.91356 16.5178 9.85712 16.3438 9.85712C16.1697 9.85712 16.0028 9.91356 15.8797 10.014C15.7566 10.1145 15.6875 10.2508 15.6875 10.3928V10.75C15.6875 10.7973 15.6645 10.8428 15.6234 10.8762C15.5824 10.9097 15.5268 10.9285 15.4688 10.9285H15.0312C14.8572 10.9285 14.6903 10.985 14.5672 11.0855C14.4441 11.1859 14.375 11.3222 14.375 11.4643C14.375 11.6063 14.4441 11.7426 14.5672 11.8431C14.6903 11.9435 14.8572 12 15.0312 12H15.4688Z\' fill=\'%239CA2A7\'/%3E%3Cpath d=\'M12.1875 7.53571C12.1875 7.67779 12.1184 7.81406 11.9953 7.91452C11.8722 8.01499 11.7053 8.07143 11.5312 8.07143H8.03125C7.8572 8.07143 7.69028 8.01499 7.56721 7.91452C7.44414 7.81406 7.375 7.67779 7.375 7.53571C7.375 7.39363 7.44414 7.25737 7.56721 7.15691C7.69028 7.05644 7.8572 7 8.03125 7H11.5312C11.7053 7 11.8722 7.05644 11.9953 7.15691C12.1184 7.25737 12.1875 7.39363 12.1875 7.53571Z\' fill=\'%239CA2A7\'/%3E%3Cpath d=\'M11.5312 12C11.7053 12 11.8722 11.9436 11.9953 11.8431C12.1184 11.7426 12.1875 11.6064 12.1875 11.4643C12.1875 11.3222 12.1184 11.186 11.9953 11.0855C11.8722 10.985 11.7053 10.9286 11.5312 10.9286H8.03125C7.8572 10.9286 7.69028 10.985 7.56721 11.0855C7.44414 11.186 7.375 11.3222 7.375 11.4643C7.375 11.6064 7.44414 11.7426 7.56721 11.8431C7.69028 11.9436 7.8572 12 8.03125 12H11.5312Z\' fill=\'%239CA2A7\'/%3E%3C/g%3E%3C/svg%3E%0A");
				mask-size: 20px 20px;
				mask-position: center;
				mask-repeat: no-repeat;
				background-color: currentColor;
				transition: all 0.3s ease-in-out;
			}
		';
		\wp_add_inline_style( 'dashicons', $css );
	}

	/**
	 * Enqueue header builder styles for desktop/mobile header visibility.
	 *
	 * @return void
	 */
	public function enqueue_header_builder_styles() {
		$header_type      = \get_theme_mod( 'wpte_header_type', 'prebuilt' );
		$header_id        = \get_theme_mod( 'wpte_header_builder_id', '' );
		$mobile_header_id = \get_theme_mod( 'wpte_mobile_header_builder_id', '' );

		// Only add styles if header builder is active.
		if ( 'builder' !== $header_type || empty( $header_id ) ) {
			return;
		}

		$header_post = \get_post( $header_id );
		if ( ! $header_post || 'publish' !== $header_post->post_status ) {
			return;
		}

		$enable_mobile_header = \get_theme_mod( 'wpte_enable_mobile_header', false );

		// Add CSS for mobile builder header when the toggle is enabled (regardless of content).
		if ( $enable_mobile_header && ! empty( $mobile_header_id ) ) {
			$css = '
				.wte-mobile-header { display: none; }
				@media only screen and (max-width: 1024px) {
					.site-header.wte-header-builder.has-mobile-builder { display: block; }
					.site-header.wte-header-builder.has-mobile-builder .wte-desktop-header { display: none; }
					.wte-mobile-header { display: block; }
					.mobile-header { display: none !important; }
				}
			';
			\wp_add_inline_style( 'travel-monster-style', $css );
		}
	}

	/**
	 * Setup header and footer hooks based on customizer settings.
	 *
	 * @return void
	 */
	public function setup_header_footer_hooks() {
		$header_type = \get_theme_mod( 'wpte_header_type', 'prebuilt' );
		$header_id   = \get_theme_mod( 'wpte_header_builder_id', '' );

		$footer_type = \get_theme_mod( 'wpte_footer_type', 'prebuilt' );
		$footer_id   = \get_theme_mod( 'wpte_footer_builder_id', '' );

		// Handle custom header.
		if ( 'builder' === $header_type && ! empty( $header_id ) && class_exists( '\Elementor\Plugin' ) ) {
			$header_post = \get_post( $header_id );
			if ( $header_post && 'publish' === $header_post->post_status ) {
				// Remove default Travel Monster header actions.
				$this->remove_theme_header_actions();
				// Add custom header.
				\add_action( 'travel_monster_header', array( $this, 'render_custom_header' ), 10 );
			}
		}

		// Handle custom footer.
		if ( 'builder' === $footer_type && ! empty( $footer_id ) && class_exists( '\Elementor\Plugin' ) ) {
			$footer_post = \get_post( $footer_id );
			if ( $footer_post && 'publish' === $footer_post->post_status ) {
				// Remove default Travel Monster footer actions.
				$this->remove_theme_footer_actions();
				// Add custom footer.
				\add_action( 'travel_monster_footer', array( $this, 'render_custom_footer' ), 10 );
			}
		}
	}

	/**
	 * Remove default Travel Monster header actions.
	 *
	 * @return void
	 */
	private function remove_theme_header_actions() {
		// Remove Travel Monster header hooks.
		\remove_all_actions( 'travel_monster_header' );
	}

	/**
	 * Remove default Travel Monster footer actions.
	 *
	 * @return void
	 */
	private function remove_theme_footer_actions() {
		// Remove Travel Monster footer hooks.
		\remove_all_actions( 'travel_monster_footer' );
	}

	/**
	 * Render custom header from Elementor builder.
	 * Shows Elementor header on desktop and either a custom mobile builder header
	 * or the theme's prebuilt mobile header on responsive views.
	 *
	 * @return void
	 */
	public function render_custom_header() {
		$header_id        = \get_theme_mod( 'wpte_header_builder_id', '' );
		$mobile_header_id = \get_theme_mod( 'wpte_mobile_header_builder_id', '' );

		if ( empty( $header_id ) ) {
			return;
		}

		$header_content = self::get_header_content( $header_id );

		if ( ! empty( $header_content ) ) {
			$enable_mobile_header  = (bool) \get_theme_mod( 'wpte_enable_mobile_header', false );
			$has_mobile_builder    = $enable_mobile_header && ! empty( $mobile_header_id );
			$mobile_header_content = $has_mobile_builder ? self::get_mobile_menu_content( $mobile_header_id ) : '';

			echo '<header class="site-header wte-header-builder' . ( $has_mobile_builder ? ' has-mobile-builder' : '' ) . '" itemtype="https://schema.org/WPHeader" itemscope>';
			// Desktop header (Elementor content) - hidden on mobile via CSS.
			echo '<div class="wte-desktop-header">';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $header_content;
			echo '</div>';
			// Mobile header (Elementor content) - shown only on mobile when builder mobile is enabled.
			if ( $has_mobile_builder ) {
				echo '<div class="wte-mobile-header">';
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $mobile_header_content;
				echo '</div>';
			}
			echo '</header>';
			?>
			<div class="header-search-wrap search-modal cover-modal" data-modal-target-string=".search-modal">
				<div class="header-search-inner">
					<button aria-label="<?php esc_attr_e( 'search form close', 'wptravelengine-elementor-widgets' ); ?>" class="btn-form-close close" data-toggle-target=".search-modal" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field" aria-expanded="false">
					<svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M24 1L1 24M1 1L24 24" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
					</svg>
					</button>
					<?php get_search_form(); ?>
				</div>
			</div>
			<?php
			// Render theme's mobile header only when builder mobile header is NOT enabled.
			if ( ! $has_mobile_builder && \function_exists( 'travel_monster_mobile_header' ) ) {
				\travel_monster_mobile_header();
			}
		}
	}

	/**
	 * Render custom footer from Elementor builder.
	 *
	 * @return void
	 */
	public function render_custom_footer() {
		$footer_id = \get_theme_mod( 'wpte_footer_builder_id', '' );

		if ( empty( $footer_id ) ) {
			return;
		}

		$footer_content = self::get_footer_content( $footer_id );

		if ( ! empty( $footer_content ) ) {
			echo '<footer class="site-footer wte-footer-builder" itemtype="https://schema.org/WPFooter" itemscope>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $footer_content;
			echo '</footer>';
		}
	}

	/**
	 * Check if Travel Monster theme or child theme is active.
	 *
	 * @return bool
	 */
	public static function is_travel_monster_theme_active() {
		$theme        = wp_get_theme();
		$theme_name   = $theme->get( 'Name' );
		$parent_theme = $theme->parent();

		// Check if current theme is Travel Monster.
		if ( 'Travel Monster' === $theme_name ) {
			return true;
		}

		// Check if parent theme is Travel Monster (for child themes).
		if ( $parent_theme && 'Travel Monster' === $parent_theme->get( 'Name' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Register custom post types for Header and Footer.
	 *
	 * @return void
	 */
	public function register_post_types() {
		// Register Header post type.
		register_post_type(
			self::HEADER_POST_TYPE,
			array(
				'labels'                => array(
					'name'                  => __( 'Headers', 'wptravelengine-elementor-widgets' ),
					'singular_name'         => __( 'Header', 'wptravelengine-elementor-widgets' ),
					'all_items'             => __( 'All Headers', 'wptravelengine-elementor-widgets' ),
					'archives'              => __( 'Header Archives', 'wptravelengine-elementor-widgets' ),
					'attributes'            => __( 'Header Attributes', 'wptravelengine-elementor-widgets' ),
					'insert_into_item'      => __( 'Insert into Header', 'wptravelengine-elementor-widgets' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Header', 'wptravelengine-elementor-widgets' ),
					'featured_image'        => _x( 'Featured Image', 'wpte_header', 'wptravelengine-elementor-widgets' ),
					'set_featured_image'    => _x( 'Set featured image', 'wpte_header', 'wptravelengine-elementor-widgets' ),
					'remove_featured_image' => _x( 'Remove featured image', 'wpte_header', 'wptravelengine-elementor-widgets' ),
					'use_featured_image'    => _x( 'Use as featured image', 'wpte_header', 'wptravelengine-elementor-widgets' ),
					'filter_items_list'     => __( 'Filter Headers list', 'wptravelengine-elementor-widgets' ),
					'items_list_navigation' => __( 'Headers list navigation', 'wptravelengine-elementor-widgets' ),
					'items_list'            => __( 'Headers list', 'wptravelengine-elementor-widgets' ),
					'new_item'              => __( 'New Header', 'wptravelengine-elementor-widgets' ),
					'add_new'               => __( 'Add New', 'wptravelengine-elementor-widgets' ),
					'add_new_item'          => __( 'Add New Header', 'wptravelengine-elementor-widgets' ),
					'edit_item'             => __( 'Edit Header', 'wptravelengine-elementor-widgets' ),
					'view_item'             => __( 'View Header', 'wptravelengine-elementor-widgets' ),
					'view_items'            => __( 'View Headers', 'wptravelengine-elementor-widgets' ),
					'search_items'          => __( 'Search Headers', 'wptravelengine-elementor-widgets' ),
					'not_found'             => __( 'No Headers found', 'wptravelengine-elementor-widgets' ),
					'not_found_in_trash'    => __( 'No Headers found in trash', 'wptravelengine-elementor-widgets' ),
					'parent_item_colon'     => __( 'Parent Header:', 'wptravelengine-elementor-widgets' ),
					'menu_name'             => __( 'WTE Headers', 'wptravelengine-elementor-widgets' ),
				),
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_menu'          => false,
				'show_in_nav_menus'     => false,
				'supports'              => array( 'title', 'elementor' ),
				'has_archive'           => false,
				'rewrite'               => false,
				'query_var'             => false,
				'capability_type'       => 'post',
				'menu_icon'             => 'dashicons-welcome-widgets-menus',
				'show_in_rest'          => true,
				'rest_base'             => 'wpte_header',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			)
		);

		// Register Footer post type.
		register_post_type(
			self::FOOTER_POST_TYPE,
			array(
				'labels'                => array(
					'name'                  => __( 'Footers', 'wptravelengine-elementor-widgets' ),
					'singular_name'         => __( 'Footer', 'wptravelengine-elementor-widgets' ),
					'all_items'             => __( 'All Footers', 'wptravelengine-elementor-widgets' ),
					'archives'              => __( 'Footer Archives', 'wptravelengine-elementor-widgets' ),
					'attributes'            => __( 'Footer Attributes', 'wptravelengine-elementor-widgets' ),
					'insert_into_item'      => __( 'Insert into Footer', 'wptravelengine-elementor-widgets' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Footer', 'wptravelengine-elementor-widgets' ),
					'featured_image'        => _x( 'Featured Image', 'wpte_footer', 'wptravelengine-elementor-widgets' ),
					'set_featured_image'    => _x( 'Set featured image', 'wpte_footer', 'wptravelengine-elementor-widgets' ),
					'remove_featured_image' => _x( 'Remove featured image', 'wpte_footer', 'wptravelengine-elementor-widgets' ),
					'use_featured_image'    => _x( 'Use as featured image', 'wpte_footer', 'wptravelengine-elementor-widgets' ),
					'filter_items_list'     => __( 'Filter Footers list', 'wptravelengine-elementor-widgets' ),
					'items_list_navigation' => __( 'Footers list navigation', 'wptravelengine-elementor-widgets' ),
					'items_list'            => __( 'Footers list', 'wptravelengine-elementor-widgets' ),
					'new_item'              => __( 'New Footer', 'wptravelengine-elementor-widgets' ),
					'add_new'               => __( 'Add New', 'wptravelengine-elementor-widgets' ),
					'add_new_item'          => __( 'Add New Footer', 'wptravelengine-elementor-widgets' ),
					'edit_item'             => __( 'Edit Footer', 'wptravelengine-elementor-widgets' ),
					'view_item'             => __( 'View Footer', 'wptravelengine-elementor-widgets' ),
					'view_items'            => __( 'View Footers', 'wptravelengine-elementor-widgets' ),
					'search_items'          => __( 'Search Footers', 'wptravelengine-elementor-widgets' ),
					'not_found'             => __( 'No Footers found', 'wptravelengine-elementor-widgets' ),
					'not_found_in_trash'    => __( 'No Footers found in trash', 'wptravelengine-elementor-widgets' ),
					'parent_item_colon'     => __( 'Parent Footer:', 'wptravelengine-elementor-widgets' ),
					'menu_name'             => __( 'WTE Footers', 'wptravelengine-elementor-widgets' ),
				),
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_menu'          => false,
				'show_in_nav_menus'     => false,
				'supports'              => array( 'title', 'elementor' ),
				'has_archive'           => false,
				'rewrite'               => false,
				'query_var'             => false,
				'capability_type'       => 'post',
				'menu_icon'             => 'dashicons-welcome-widgets-menus',
				'show_in_rest'          => true,
				'rest_base'             => 'wpte_footer',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			)
		);

		// Register Mobile Menu post type.
		register_post_type(
			self::MOBILE_MENU_POST_TYPE,
			array(
				'labels'                => array(
					'name'               => __( 'Mobile Menus', 'wptravelengine-elementor-widgets' ),
					'singular_name'      => __( 'Mobile Menu', 'wptravelengine-elementor-widgets' ),
					'all_items'          => __( 'All Mobile Menus', 'wptravelengine-elementor-widgets' ),
					'new_item'           => __( 'New Mobile Menu', 'wptravelengine-elementor-widgets' ),
					'add_new'            => __( 'Add New', 'wptravelengine-elementor-widgets' ),
					'add_new_item'       => __( 'Add New Mobile Menu', 'wptravelengine-elementor-widgets' ),
					'edit_item'          => __( 'Edit Mobile Menu', 'wptravelengine-elementor-widgets' ),
					'view_item'          => __( 'View Mobile Menu', 'wptravelengine-elementor-widgets' ),
					'search_items'       => __( 'Search Mobile Menus', 'wptravelengine-elementor-widgets' ),
					'not_found'          => __( 'No Mobile Menus found', 'wptravelengine-elementor-widgets' ),
					'not_found_in_trash' => __( 'No Mobile Menus found in trash', 'wptravelengine-elementor-widgets' ),
					'menu_name'          => __( 'WTE Mobile Menus', 'wptravelengine-elementor-widgets' ),
				),
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_menu'          => false,
				'show_in_nav_menus'     => false,
				'supports'              => array( 'title', 'elementor' ),
				'has_archive'           => false,
				'rewrite'               => false,
				'query_var'             => false,
				'capability_type'       => 'post',
				'menu_icon'             => 'dashicons-welcome-widgets-menus',
				'show_in_rest'          => true,
				'rest_base'             => 'wpte_mobile_menu',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			)
		);

		// Register Mobile Header post type.
		register_post_type(
			self::MOBILE_HEADER_POST_TYPE,
			array(
				'labels'                => array(
					'name'               => __( 'Mobile Headers', 'wptravelengine-elementor-widgets' ),
					'singular_name'      => __( 'Mobile Header', 'wptravelengine-elementor-widgets' ),
					'all_items'          => __( 'All Mobile Headers', 'wptravelengine-elementor-widgets' ),
					'new_item'           => __( 'New Mobile Header', 'wptravelengine-elementor-widgets' ),
					'add_new'            => __( 'Add New', 'wptravelengine-elementor-widgets' ),
					'add_new_item'       => __( 'Add New Mobile Header', 'wptravelengine-elementor-widgets' ),
					'edit_item'          => __( 'Edit Mobile Header', 'wptravelengine-elementor-widgets' ),
					'view_item'          => __( 'View Mobile Header', 'wptravelengine-elementor-widgets' ),
					'search_items'       => __( 'Search Mobile Headers', 'wptravelengine-elementor-widgets' ),
					'not_found'          => __( 'No Mobile Headers found', 'wptravelengine-elementor-widgets' ),
					'not_found_in_trash' => __( 'No Mobile Headers found in trash', 'wptravelengine-elementor-widgets' ),
					'menu_name'          => __( 'WTE Mobile Headers', 'wptravelengine-elementor-widgets' ),
				),
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_menu'          => false,
				'show_in_nav_menus'     => false,
				'supports'              => array( 'title', 'elementor' ),
				'has_archive'           => false,
				'rewrite'               => false,
				'query_var'             => false,
				'capability_type'       => 'post',
				'menu_icon'             => 'dashicons-welcome-widgets-menus',
				'show_in_rest'          => true,
				'rest_base'             => 'wpte_mobile_header',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			)
		);

		// Register Off Canvas post type.
		register_post_type(
			self::OFFCANVAS_POST_TYPE,
			array(
				'labels'                => array(
					'name'               => __( 'Off Canvas', 'wptravelengine-elementor-widgets' ),
					'singular_name'      => __( 'Off Canvas', 'wptravelengine-elementor-widgets' ),
					'all_items'          => __( 'All Off Canvas', 'wptravelengine-elementor-widgets' ),
					'new_item'           => __( 'New Off Canvas', 'wptravelengine-elementor-widgets' ),
					'add_new'            => __( 'Add New', 'wptravelengine-elementor-widgets' ),
					'add_new_item'       => __( 'Add New Off Canvas', 'wptravelengine-elementor-widgets' ),
					'edit_item'          => __( 'Edit Off Canvas', 'wptravelengine-elementor-widgets' ),
					'view_item'          => __( 'View Off Canvas', 'wptravelengine-elementor-widgets' ),
					'search_items'       => __( 'Search Off Canvas', 'wptravelengine-elementor-widgets' ),
					'not_found'          => __( 'No Off Canvas found', 'wptravelengine-elementor-widgets' ),
					'not_found_in_trash' => __( 'No Off Canvas found in trash', 'wptravelengine-elementor-widgets' ),
					'menu_name'          => __( 'WTE Off Canvas', 'wptravelengine-elementor-widgets' ),
				),
				'public'                => true,
				'hierarchical'          => false,
				'show_ui'               => true,
				'show_in_menu'          => false,
				'show_in_nav_menus'     => false,
				'supports'              => array( 'title', 'elementor' ),
				'has_archive'           => false,
				'rewrite'               => false,
				'query_var'             => false,
				'capability_type'       => 'post',
				'menu_icon'             => 'dashicons-welcome-widgets-menus',
				'show_in_rest'          => true,
				'rest_base'             => 'wpte_offcanvas',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			)
		);
	}

	/**
	 * Add menu pages for WPTE Builder.
	 *
	 * @return void
	 */
	public function add_menu_pages() {
		// Add WPTE Builder top-level menu.
		add_menu_page(
			esc_html__( 'WPTE Builder', 'wptravelengine-elementor-widgets' ),
			esc_html__( 'WPTE Builder', 'wptravelengine-elementor-widgets' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_builder_page' ),
			'dashicons-wpte-builder-icon',
			30
		);

		// Add Header Builder submenu under WPTE Builder.
		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Header Builder', 'wptravelengine-elementor-widgets' ),
			esc_html__( 'Header Builder', 'wptravelengine-elementor-widgets' ),
			'manage_options',
			'edit.php?post_type=' . self::HEADER_POST_TYPE
		);

		// Add Footer Builder submenu under WPTE Builder.
		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Footer Builder', 'wptravelengine-elementor-widgets' ),
			esc_html__( 'Footer Builder', 'wptravelengine-elementor-widgets' ),
			'manage_options',
			'edit.php?post_type=' . self::FOOTER_POST_TYPE
		);

		// Add Mobile Menu submenu under WPTE Builder.
		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Mobile Menu', 'wptravelengine-elementor-widgets' ),
			esc_html__( 'Mobile Menu', 'wptravelengine-elementor-widgets' ),
			'manage_options',
			'edit.php?post_type=' . self::MOBILE_MENU_POST_TYPE
		);

		// Add Off Canvas Builder submenu under WPTE Builder.
		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Off Canvas Builder', 'wptravelengine-elementor-widgets' ),
			esc_html__( 'Off Canvas Builder', 'wptravelengine-elementor-widgets' ),
			'manage_options',
			'edit.php?post_type=' . self::OFFCANVAS_POST_TYPE
		);

		// Remove the default submenu item that duplicates the parent.
		remove_submenu_page( self::MENU_SLUG, self::MENU_SLUG );
	}

	/**
	 * Render the WPTE Builder page.
	 *
	 * This is a placeholder that redirects to Header Builder by default.
	 *
	 * @return void
	 */
	public function render_builder_page() {
		// Redirect to Header Builder page.
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . self::HEADER_POST_TYPE ) );
		exit;
	}

	/**
	 * Load Elementor canvas template for header/footer post types.
	 *
	 * @param string $single_template The single template path.
	 * @return string Modified template path.
	 */
	public function load_canvas_template( $single_template ) {
		global $post;

		if ( ! $post ) {
			return $single_template;
		}

		if ( defined( 'ELEMENTOR_PATH' ) && in_array( $post->post_type, array( self::HEADER_POST_TYPE, self::FOOTER_POST_TYPE, self::MOBILE_HEADER_POST_TYPE, self::MOBILE_MENU_POST_TYPE, self::OFFCANVAS_POST_TYPE ), true ) ) {
			$elementor_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

			if ( file_exists( $elementor_canvas ) ) {
				return $elementor_canvas;
			} else {
				// Fallback for older Elementor versions.
				return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
			}
		}

		return $single_template;
	}

	/**
	 * Add Header/Footer Builder category to Elementor.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 * @return void
	 */
	public function add_header_footer_category( $elements_manager ) {
		$elements_manager->add_category(
			'wte-header-footer',
			array(
				'title' => __( 'WP Travel Engine - Header/Footer', 'wptravelengine-elementor-widgets' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Get header content by ID.
	 *
	 * @param int $header_id The header post ID.
	 * @return string The rendered header content.
	 */
	public static function get_header_content( $header_id ) {
		if ( ! $header_id || ! class_exists( '\Elementor\Plugin' ) ) {
			return '';
		}

		$header_post = get_post( $header_id );
		if ( ! $header_post || 'publish' !== $header_post->post_status ) {
			return '';
		}

		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_id );
	}

	/**
	 * Get footer content by ID.
	 *
	 * @param int $footer_id The footer post ID.
	 * @return string The rendered footer content.
	 */
	public static function get_footer_content( $footer_id ) {
		if ( ! $footer_id || ! class_exists( '\Elementor\Plugin' ) ) {
			return '';
		}

		$footer_post = get_post( $footer_id );
		if ( ! $footer_post || 'publish' !== $footer_post->post_status ) {
			return '';
		}

		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $footer_id );
	}

	/**
	 * Get all published headers.
	 *
	 * @return array Array of header posts.
	 */
	public static function get_headers() {
		return get_posts(
			array(
				'post_type'      => self::HEADER_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
	}

	/**
	 * Get all published footers.
	 *
	 * @return array Array of footer posts.
	 */
	public static function get_footers() {
		return get_posts(
			array(
				'post_type'      => self::FOOTER_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
	}

	/**
	 * Get all published mobile menus.
	 *
	 * @return array Array of mobile menu posts.
	 */
	public static function get_mobile_menus() {
		return get_posts(
			array(
				'post_type'      => self::MOBILE_MENU_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
	}

	/**
	 * Get mobile menu content by ID.
	 *
	 * @param int $menu_id The mobile menu post ID.
	 * @return string The rendered mobile menu content.
	 */
	public static function get_mobile_menu_content( $menu_id ) {
		if ( ! $menu_id || ! class_exists( '\Elementor\Plugin' ) ) {
			return '';
		}

		$menu_post = get_post( $menu_id );
		if ( ! $menu_post || 'publish' !== $menu_post->post_status ) {
			return '';
		}

		return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $menu_id );
	}
}

// Initialize the Header Footer Builder.
Header_Footer_Builder::instance();
