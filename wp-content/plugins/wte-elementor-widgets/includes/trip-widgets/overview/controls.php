<?php
/**
 * Overview Widget Controls.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB;

$selectors = array(
	// general section.
	'general_typography'       => '{{WRAPPER}} .elementor-widget-container .overview-content',
	'general_padding'          => array(
		'{{WRAPPER}} .elementor-widget-container .overview-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
	),
	'general_margin'           => array(
		'{{WRAPPER}} .elementor-widget-container .overview-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
	),
	'general_color'            => array(
		'{{WRAPPER}} .elementor-widget-container .overview-content' => 'color: {{VALUE}};',
	),
	'general_boxshadow'        => '{{WRAPPER}} .elementor-widget-container .overview-content',
	'general_border'           => '{{WRAPPER}} .elementor-widget-container .overview-content',
	'general_background_color' => array(
		'{{WRAPPER}} .elementor-widget-container .overview-content' => 'background-color: {{VALUE}};',
	),
	'general_border_radius'    => array(
		'{{WRAPPER}} .elementor-widget-container .overview-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'text_alignment'           => array(
		'{{WRAPPER}} .elementor-widget-container .overview-content' => 'text-align: {{VALUE}};',
	),
	'text_shadow'              => array(
		'{{WRAPPER}} .elementor-widget-container .overview-content' => 'text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
	),
);

$controls = array(
	'categories'      => 'wptravelengine',
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
	'general_section' => array(
		'type'        => 'control_section',
		'label'       => __( 'General', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'general_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['general_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'general_color'      => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Text color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['general_color'],
			),
			'text_alignment'     => array(
				'label'     => esc_html__( 'Alignment', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'toggle'    => true,
				'selectors' => $selectors['text_alignment'],
			),
		),
	),
);

return $controls;
