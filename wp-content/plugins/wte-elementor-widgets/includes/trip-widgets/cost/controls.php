<?php
/**
 * Trip Cost Widget Controls.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB;

$selectors = array(
	// Cost Excludes.
	'costexcludes_title_typography' => '{{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes h3',
	'costexcludes_typography'       => '{{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes ul li',
	'exclude_color'                 => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes ul li' => 'color: {{VALUE}};',
	),
	'costexcludes_margin'           => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
	),

	// Icon.
	'exclude_icon_size'             => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes ul li' => '--icon-size: {{SIZE}}{{UNIT}};',
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes ul li, {{WRAPPER}} ul#exclude-result li' => 'padding-left: calc({{SIZE}}{{UNIT}} + 14px);',
	),
	'exclude_icon_color'            => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content #exclude-result li:before' => 'background: {{VALUE}};',
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes ul.custom-icon li i, {{WRAPPER}} .elementor-widget-container .post-data .content .cost-excludes ul.custom-icon li svg' => 'color: {{VALUE}};',
	),

	// Cost Includes.
	'costincludes_title_typography' => '{{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes h3',
	'costincludes_typography'       => '{{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes ul li',
	'include_color'                 => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes ul li' => 'color: {{VALUE}};',
	),
	'costincludes_margin'           => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
	),

	// Icon.
	'include_icon_size'             => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes ul li' => '--icon-size: {{SIZE}}{{UNIT}};',
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes ul li, {{WRAPPER}} ul#include-result li' => 'padding-left: calc({{SIZE}}{{UNIT}} + 14px);',
	),
	'include_icon_color'            => array(
		'{{WRAPPER}} .elementor-widget-container .post-data .content #include-result li:before' => 'background: {{VALUE}};',
		'{{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes ul.custom-icon li i, {{WRAPPER}} .elementor-widget-container .post-data .content .cost-includes ul.custom-icon li svg' => 'color: {{VALUE}};',
	),
);

$controls = array(
	'title'                   => __( 'Trip Cost', 'wptravelengine-elementor-widgets' ),
	'icon'                    => 'wtei-b-trips',
	'categories'              => 'wptravelengine',
	'controls'                => array(),
	'cost_section'            => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Cost', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'show_title'             => array(
				'label'     => __( 'Show Title', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'default'   => 'yes',
			),
			'html_tag'               => array(
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
			'showCostInclude'        => array(
				'label'   => __( 'Show Cost Include', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'IncludeTitleTypogrpahy' => array(
				'type'      => \Elementor\Group_Control_Typography::get_type(),
				'selector'  => $selectors['costincludes_title_typography'],
				'label'     => __( 'Title Typography', 'wptravelengine-elementor-widgets' ),
				'condition' => array( 'showCostInclude' => 'yes' ),
			),
			'showCostExclude'        => array(
				'label'   => __( 'Show Cost Exclude', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'ExcludeTitleTypogrpahy' => array(
				'type'      => \Elementor\Group_Control_Typography::get_type(),
				'selector'  => $selectors['costexcludes_title_typography'],
				'label'     => __( 'Title Typography', 'wptravelengine-elementor-widgets' ),
				'condition' => array( 'showCostExclude' => 'yes' ),
			),
		),
	),
	'include_general_section' => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Include List Items', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showCostInclude' => 'yes' ),
		'subcontrols' => array(
			'include_cost_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['costincludes_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'include_color'           => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['include_color'],
			),
			'costincludes_margin'     => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['costincludes_margin'],
			),
		),
	),
	'include_icon_section'    => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Include Icon', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showCostInclude' => 'yes' ),
		'subcontrols' => array(
			'include_icon_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['include_icon_color'],
			),
			'include_icon_size'  => array(
				'type'       => 'SLIDER',
				'label'      => esc_html__( 'Size', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 6,
						'max' => 40,
					),
					'%'  => array(
						'min' => 6,
						'max' => 40,
					),
				),
				'selectors'  => $selectors['include_icon_size'],
			),
			'include_icon'       => array(
				'type'          => \Elementor\Controls_Manager::ICONS,
				'label'         => esc_html__( 'Custom Upload', 'wptravelengine-elementor-widgets' ),
				'skin'          => 'inline',
				'label_block'   => false,
				'skin_settings' => array(
					'inline' => array(
						'none' => array(
							'label' => 'Default',
							'icon'  => 'eicon-check',
						),
						'icon' => array(
							'icon' => 'eicon-star',
						),
					),
				),
			),
		),
	),
	'exclude_general_section' => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Exclude List Items', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showCostExclude' => 'yes' ),
		'subcontrols' => array(
			'exclude_cost_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['costexcludes_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'exclude_color'           => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['exclude_color'],
			),
			'costexcludes_margin'     => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['costexcludes_margin'],
			),
		),
	),
	'exclude_icon_section'    => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Exclude Icon', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showCostExclude' => 'yes' ),
		'subcontrols' => array(
			'exclude_icon_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['exclude_icon_color'],
			),
			'exclude_icon_size'  => array(
				'type'       => 'SLIDER',
				'label'      => esc_html__( 'Size', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 6,
						'max' => 40,
					),
					'%'  => array(
						'min' => 6,
						'max' => 40,
					),
				),
				'selectors'  => $selectors['exclude_icon_size'],
			),
			'exclude_icon'       => array(
				'type'          => \Elementor\Controls_Manager::ICONS,
				'label'         => esc_html__( 'Custom Upload', 'wptravelengine-elementor-widgets' ),
				'skin'          => 'inline',
				'label_block'   => false,
				'skin_settings' => array(
					'inline' => array(
						'none' => array(
							'label' => 'Default',
							'icon'  => ' eicon-editor-close',
						),
						'icon' => array(
							'icon' => 'eicon-star',
						),
					),
				),
			),
		),
	),
);

return $controls;
