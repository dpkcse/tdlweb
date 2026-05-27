<?php

namespace WPTRAVELENGINEEB;

/**
 * Advanced Trips Widget Controls.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

$selectors = array(
	// tab
	'tab_typography'     => '{{WRAPPER}} .nav-tab-wrapper .tab-anchor-wrapper a.nav-tab',
	'tab_active_color'   => array( '{{WRAPPER}} .nav-tab-wrapper .tab-anchor-wrapper .nav-tab ' => '--tab-color: {{VALUE}};' ),
	'tab_inactive_color' => array( '{{WRAPPER}} .nav-tab-wrapper .tab-anchor-wrapper .nav-tab ' => '--tab-active-color: {{VALUE}};' ),
	'tab_gap'            => array( '{{WRAPPER}} .nav-tab-wrapper .tab-inner-wrapper' => '--wpte-sticky-tab-gap: {{ROW}}{{UNIT}} {{COLUMN}}{{UNIT}};' ),
);


$controls = array(
	'tabs_section' => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Tabs', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'tab_gap'        => array(
				'label'      => __( 'Gap', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::GAPS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'row'    => '30',
					'column' => '30',
					'unit'   => 'px',
				),
				'selectors'  => $selectors['tab_gap'],
				'validators' => array(
					'Number' => array(
						'min' => 0,
					),
				),
			),
			'tab_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['tab_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'tab_color'      => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'tab_active'   => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Active', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'tab_active_color' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['tab_active_color'],
							),
						),
					),
					'tab_inactive' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Inactive', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'tab_inactive_color' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => __( 'Tab Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['tab_inactive_color'],
							),
						),
					),
				),
			),
		),
	),
);

return $controls;
