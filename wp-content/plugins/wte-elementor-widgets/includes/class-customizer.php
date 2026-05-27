<?php
/**
 * Customizer Settings for Header and Footer Builder.
 *
 * Extends Travel Monster theme customizer with Header/Footer Builder options.
 *
 * @package WPTRAVELENGINEEB
 * @since 1.4.8
 */

namespace WPTRAVELENGINEEB;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Customizer class.
 *
 * @since 1.4.8
 */
class Customizer {

	/**
	 * Instance of the class.
	 *
	 * @var Customizer|null
	 */
	private static $instance = null;

	/**
	 * Returns the instance of the class.
	 *
	 * @return Customizer
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
		add_action( 'customize_register', array( $this, 'register_customizer_settings' ), 20 );
	}

	/**
	 * Register customizer settings for Header and Footer Builder.
	 *
	 * @param \WP_Customize_Manager $wp_customize The WordPress Customizer object.
	 * @return void
	 */
	public function register_customizer_settings( $wp_customize ) {
		// Register Header Type setting.
		$this->register_header_settings( $wp_customize );

		// Register Footer Type setting.
		$this->register_footer_settings( $wp_customize );
	}

	/**
	 * Register Header Builder settings.
	 *
	 * @param \WP_Customize_Manager $wp_customize The WordPress Customizer object.
	 * @return void
	 */
	private function register_header_settings( $wp_customize ) {

		/** General Settings */
		$wp_customize->add_section(
			'main_header_builder',
			array(
				'title'    => __( 'Header Builder', 'wptravelengine-elementor-widgets' ),
				'priority' => 10,
				'panel'    => 'layout_header',
			)
		);

		// Header Type Setting.
		$wp_customize->add_setting(
			'wpte_header_type',
			array(
				'default'           => 'prebuilt',
				'sanitize_callback' => array( $this, 'sanitize_select' ),
			)
		);

		$wp_customize->add_control(
			new \Travel_Monster_Radio_Buttonset_Control(
				$wp_customize,
				'wpte_header_type',
				array(
					'label'    => __( 'Type of Header', 'wptravelengine-elementor-widgets' ),
					'section'  => 'main_header_builder',
					'choices'  => array(
						'prebuilt' => __( 'Prebuilt', 'wptravelengine-elementor-widgets' ),
						'builder'  => __( 'Builder', 'wptravelengine-elementor-widgets' ),
					),
					'priority' => 1,
				)
			)
		);

		// Header Builder ID Setting.
		$wp_customize->add_setting(
			'wpte_header_builder_id',
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new \Travel_Monster_Select_Control(
				$wp_customize,
				'wpte_header_builder_id',
				array(
					'label'           => __( 'Choose Header (Desktop)', 'wptravelengine-elementor-widgets' ),
					'section'         => 'main_header_builder',
					'choices'         => $this->get_builder_posts( Header_Footer_Builder::HEADER_POST_TYPE ),
					'priority'        => 2,
					'active_callback' => array( $this, 'is_header_builder_active' ),
				)
			)
		);

		// Enable Builder Mobile Header Toggle Setting.
		$wp_customize->add_setting(
			'wpte_enable_mobile_header',
			array(
				'default'           => false,
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new \Travel_Monster_Toggle_Control(
				$wp_customize,
				'wpte_enable_mobile_header',
				array(
					'label'           => __( 'Enable Builder Mobile Header', 'wptravelengine-elementor-widgets' ),
					'section'         => 'main_header_builder',
					'priority'        => 3,
					'active_callback' => array( $this, 'is_header_builder_active' ),
				)
			)
		);

		// Mobile Header Builder ID Setting.
		$wp_customize->add_setting(
			'wpte_mobile_header_builder_id',
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new \Travel_Monster_Select_Control(
				$wp_customize,
				'wpte_mobile_header_builder_id',
				array(
					'label'           => __( 'Choose Header (Mobile)', 'wptravelengine-elementor-widgets' ),
					'description'     => __( 'Select a header template to use on mobile devices. If not set, the default mobile header will be used.', 'wptravelengine-elementor-widgets' ),
					'section'         => 'main_header_builder',
					'choices'         => $this->get_builder_posts( Header_Footer_Builder::MOBILE_MENU_POST_TYPE ),
					'priority'        => 4,
					'active_callback' => array( $this, 'is_mobile_header_enabled' ),
				)
			)
		);

		// Hide Mobile Header Settings section when builder mobile header is enabled.
		$this->modify_mobile_header_section_visibility( $wp_customize );
	}

	/**
	 * Register Footer Builder settings.
	 *
	 * @param \WP_Customize_Manager $wp_customize The WordPress Customizer object.
	 * @return void
	 */
	private function register_footer_settings( $wp_customize ) {
		// Check if footer panel exists.
		$footer_panel = $wp_customize->get_panel( 'footer_panel' );
		if ( ! $footer_panel ) {
			return;
		}

		// Add Footer Builder Section.
		$wp_customize->add_section(
			'footer_builder_section',
			array(
				'title'    => __( 'Footer Builder', 'wptravelengine-elementor-widgets' ),
				'priority' => 5,
				'panel'    => 'footer_panel',
			)
		);

		// Footer Type Setting.
		$wp_customize->add_setting(
			'wpte_footer_type',
			array(
				'default'           => 'prebuilt',
				'sanitize_callback' => array( $this, 'sanitize_select' ),
			)
		);

		$wp_customize->add_control(
			new \Travel_Monster_Radio_Buttonset_Control(
				$wp_customize,
				'wpte_footer_type',
				array(
					'label'    => __( 'Type of Footer', 'wptravelengine-elementor-widgets' ),
					'section'  => 'footer_builder_section',
					'choices'  => array(
						'prebuilt' => __( 'Prebuilt', 'wptravelengine-elementor-widgets' ),
						'builder'  => __( 'Builder', 'wptravelengine-elementor-widgets' ),
					),
					'priority' => 1,
				)
			)
		);

		// Footer Builder ID Setting.
		$wp_customize->add_setting(
			'wpte_footer_builder_id',
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new \Travel_Monster_Select_Control(
				$wp_customize,
				'wpte_footer_builder_id',
				array(
					'label'           => __( 'Choose Footer', 'wptravelengine-elementor-widgets' ),
					'section'         => 'footer_builder_section',
					'choices'         => $this->get_builder_posts( Header_Footer_Builder::FOOTER_POST_TYPE ),
					'priority'        => 2,
					'active_callback' => array( $this, 'is_footer_builder_active' ),
				)
			)
		);

		// Modify Upper Footer and Bottom Footer sections to hide when builder is active.
		$this->modify_footer_sections_visibility( $wp_customize );
	}

	/**
	 * Hide the Mobile Header Settings section when builder mobile header is enabled.
	 *
	 * @param \WP_Customize_Manager $wp_customize The WordPress Customizer object.
	 * @return void
	 */
	private function modify_mobile_header_section_visibility( $wp_customize ) {
		$section = $wp_customize->get_section( 'mobile_header_settings' );
		if ( $section ) {
			$section->active_callback = array( $this, 'is_prebuilt_mobile_header_active' );
		}
	}

	/**
	 * Active callback for Mobile Header Settings section.
	 * Returns false when builder header is active and builder mobile header is enabled.
	 *
	 * @return bool
	 */
	public function is_prebuilt_mobile_header_active() {
		if ( 'builder' === \get_theme_mod( 'wpte_header_type', 'prebuilt' ) && \get_theme_mod( 'wpte_enable_mobile_header', false ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Modify Upper Footer and Bottom Footer sections visibility based on footer type.
	 *
	 * @param \WP_Customize_Manager $wp_customize The WordPress Customizer object.
	 * @return void
	 */
	private function modify_footer_sections_visibility( $wp_customize ) {
		// Get Upper Footer section.
		$upper_footer_section = $wp_customize->get_section( 'upper_footer_settings' );
		if ( $upper_footer_section ) {
			$upper_footer_section->active_callback = array( $this, 'is_footer_prebuilt_active' );
		}

		// Get Bottom Footer section.
		$bottom_footer_section = $wp_customize->get_section( 'bottom_footer_settings' );
		if ( $bottom_footer_section ) {
			$bottom_footer_section->active_callback = array( $this, 'is_footer_prebuilt_active' );
		}
	}

	/**
	 * Get builder posts for dropdown.
	 *
	 * @param string $post_type The post type to query.
	 * @return array Array of posts for dropdown.
	 */
	private function get_builder_posts( $post_type ) {
		$posts = get_posts(
			array(
				'posts_per_page'   => -1,
				'post_type'        => $post_type,
				'post_status'      => 'publish',
				'suppress_filters' => true,
				'orderby'          => 'title',
				'order'            => 'ASC',
			)
		);

		$options = array(
			'' => __( '-- Select --', 'wptravelengine-elementor-widgets' ),
		);

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
	}

	/**
	 * Active callback for Header Builder ID control.
	 *
	 * @param \WP_Customize_Control $control The control object.
	 * @return bool
	 */
	public function is_header_builder_active( $control ) {
		$header_type = $control->manager->get_setting( 'wpte_header_type' )->value();
		return 'builder' === $header_type;
	}

	/**
	 * Active callback: returns true when the mobile header toggle is enabled.
	 *
	 * @param \WP_Customize_Control $control The control object.
	 * @return bool
	 */
	public function is_mobile_header_enabled( $control ) {
		return (bool) $control->manager->get_setting( 'wpte_enable_mobile_header' )->value();
	}

	/**
	 * Active callback for Footer Builder ID control.
	 *
	 * @param \WP_Customize_Control $control The control object.
	 * @return bool
	 */
	public function is_footer_builder_active( $control ) {
		$footer_type = $control->manager->get_setting( 'wpte_footer_type' )->value();
		return 'builder' === $footer_type;
	}

	/**
	 * Active callback for showing prebuilt footer sections.
	 * Returns true when footer type is 'prebuilt', hiding Upper/Bottom Footer sections when builder is active.
	 *
	 * @return bool
	 */
	public function is_footer_prebuilt_active() {
		$footer_type = get_theme_mod( 'wpte_footer_type', 'prebuilt' );
		return 'prebuilt' === $footer_type;
	}

	/**
	 * Sanitize select/radio input.
	 *
	 * @param string $input The input value.
	 * @return string Sanitized value.
	 */
	public function sanitize_select( $input ) {
		$valid = array( 'prebuilt', 'builder' );
		if ( in_array( $input, $valid, true ) ) {
			return $input;
		}
		return 'prebuilt';
	}
}

// Initialize the Customizer.
Customizer::instance();
