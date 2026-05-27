<?php

namespace WPTRAVELENGINEEB;

/**
 * Custom Trips Tabs Widget Controls.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */


// Determine if we should show custom trip tabs options.
// During AJAX requests, get_post_type() may not work correctly, so we check editor_post_id.
$current_post_type = get_post_type();
if ( ! $current_post_type && isset( $_POST['editor_post_id'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$editor_post_id    = absint( wp_unslash( $_POST['editor_post_id'] ) );
	$current_post_type = get_post_type( $editor_post_id );
}
$show_custom_tabs = in_array( $current_post_type, array( 'trip', 'elementor_library' ), true );

$controls = array(
	// Content.
	'general_section' => array(
		'type'        => 'control_section',
		'label'       => __( 'General', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'custom_trip_tabs' => array(
				'label'   => __( 'Custom Trip Tabs', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SELECT',
				'options' => $show_custom_tabs ? $this->get_custom_trip_tabs_selector() : array(),
				'default' => '',
			),
		),
	),
	'title_settings'  => array(
		'type'        => 'control_section',
		'label'       => __( 'Title', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'show_title' => array(
				'label'     => __( 'Show Title', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'default'   => 'yes',
			),
			'html_tag'   => array(
				'type'    => 'SELECT',
				'label'   => __( 'HTML Tag', 'wptravelengine-elementor-widgets' ),
				'default' => 'h3',
				'options' => array(
					'h1'   => __( 'H1', 'wptravelengine-elementor-widgets' ),
					'h2'   => __( 'H2', 'wptravelengine-elementor-widgets' ),
					'h3'   => __( 'H3', 'wptravelengine-elementor-widgets' ),
					'h4'   => __( 'H4', 'wptravelengine-elementor-widgets' ),
					'h5'   => __( 'H5', 'wptravelengine-elementor-widgets' ),
					'h6'   => __( 'H6', 'wptravelengine-elementor-widgets' ),
					'div'  => __( 'div', 'wptravelengine-elementor-widgets' ),
					'span' => __( 'span', 'wptravelengine-elementor-widgets' ),
					'p'    => __( 'p', 'wptravelengine-elementor-widgets' ),
				),
			),
		),
	),
);

return $controls;
