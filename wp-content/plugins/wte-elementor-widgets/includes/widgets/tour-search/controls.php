<?php
namespace WPTRAVELENGINEEB;

/**
 * Trip Widget Controls.
 *
 * @since 1.4.1
 */
$selectors = array(

	// Search Field
	'search_boxshadow'                  => '{{WRAPPER}} .wpte-tour-search .search-field',
	'search_border'                     => '{{WRAPPER}} .wpte-tour-search .search-field',
	'search_padding'                    => array(
		'{{WRAPPER}} .wpte-tour-search' => '--search-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};--right: {{RIGHT}}{{UNIT}};',
	),
	'search_margin'                     => array(
		'{{WRAPPER}} .wpte-tour-search' => '--search-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};--right: {{RIGHT}}{{UNIT}};',
	),
	'search_background_color'           => array(
		'{{WRAPPER}} .wpte-tour-search' => '--search-bg: {{VALUE}};',
	),
	'search_primary_color'              => array(
		'{{WRAPPER}} .wpte-tour-search' => '--search-color: {{VALUE}};',
	),
	'search_border_radius'              => array(
		'{{WRAPPER}} .wpte-tour-search' => '--search-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),

	// Category
	'categoryTypography'                => '{{WRAPPER}} .wpte-widget.wpte-tour-search .cat-title',

	'categorySpaceBetween'              => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-spacing: {{SIZE}}{{UNIT}};',
	),
	'categoryItemPadding'               => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'categoryMargin'                    => array(
		'{{WRAPPER}} .wpte-tour-search' => '--category-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),

	// normal
	'category_color'                    => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-color: {{VALUE}};',
	),
	'category_background'               => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-bg: {{VALUE}};',
	),
	'category_border'                   => '{{WRAPPER}} .wpte-tour-search .cat-title',
	'category_radius'                   => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),

	// hover
	'category_color_hover'              => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-color-hover: {{VALUE}};',
	),
	'category_background_hover'         => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-bg-hover: {{VALUE}};',
	),
	'category_border_hover'             => '{{WRAPPER}} .wpte-tour-search .cat-title:hover',
	'category_radius_hover'             => array(
		'{{WRAPPER}} .wpte-tour-search' => '--item-radius-hover: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),

	// button.
	'search_button_typography'          => '{{WRAPPER}} .wpte-tour-search .wpte-tour-search__btn',
	'search_button_padding'             => array(
		'{{WRAPPER}} .wpte-tour-search' => '--button-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'search_button_bg_color'            => array(
		'{{WRAPPER}} .wpte-tour-search' => '--button-bg: {{VALUE}};',
	),
	'search_button_color'               => array(
		'{{WRAPPER}} .wpte-tour-search' => '--button-color: {{VALUE}};',
	),
	'search_button_border'              => '{{WRAPPER}} .wpte-tour-search .wpte-tour-search__btn',
	'search_button_border_radius'       => array(
		'{{WRAPPER}} .wpte-tour-search' => '--button-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'search_button_boxshadow'           => '{{WRAPPER}} .wpte-tour-search .wpte-tour-search__btn',

	// hover
	'search_button_bg_hover_color'      => array(
		'{{WRAPPER}} .wpte-tour-search' => '--button-bg-hover: {{VALUE}};',
	),
	'search_button_hover_color'         => array(
		'{{WRAPPER}} .wpte-tour-search' => '--button-color-hover: {{VALUE}};',
	),
	'search_button_hover_border'        => '{{WRAPPER}} .wpte-tour-search .wpte-tour-search__btn:hover',
	'search_button_hover_border_radius' => array(
		'{{WRAPPER}} .wpte-tour-search' => '--button-radius-hover: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'search_button_hover_boxshadow'     => '{{WRAPPER}} .wpte-tour-search .wpte-tour-search__btn:hover',
);

$controls = array(
	'sorting_filtering'      => array(
		'type'        => 'control_section',
		'label'       => __( 'Query', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'selectTax'            => array(
				'type'    => 'SELECT',
				'label'   => __( 'Select Taxonomy', 'wptravelengine-elementor-widgets' ),
				'default' => 'destination',
				'options' => array(
					'destination' => __( 'Destination', 'wptravelengine-elementor-widgets' ),
					'activities'  => __( 'Activities', 'wptravelengine-elementor-widgets' ),
					'trip_types'  => __( 'Trip Types', 'wptravelengine-elementor-widgets' ),
				),
			),
			'listby'               => array(
				'type'    => 'SELECT',
				'label'   => __( 'Show Terms By', 'wptravelengine-elementor-widgets' ),
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'wptravelengine-elementor-widgets' ),
					'byids'   => __( 'Choose from the list', 'wptravelengine-elementor-widgets' ),
				),
			),
			'listItemsDestination' => array(
				'type'          => 'TAXONOMY_TERMS_SELECT2',
				'label'         => __( 'Choose terms', 'wptravelengine-elementor-widgets' ),
				'default'       => array(),
				'multiple'      => true,
				'taxonomy_name' => 'destination',
				'condition'     => array(
					'listby'    => 'byids',
					'selectTax' => 'destination',
				),
			),
			'listItemsActivities'  => array(
				'type'          => 'TAXONOMY_TERMS_SELECT2',
				'label'         => __( 'Choose terms', 'wptravelengine-elementor-widgets' ),
				'default'       => array(),
				'multiple'      => true,
				'taxonomy_name' => 'activities',
				'condition'     => array(
					'listby'    => 'byids',
					'selectTax' => 'activities',
				),
			),
			'listItemsTripTypes'   => array(
				'type'          => 'TAXONOMY_TERMS_SELECT2',
				'label'         => __( 'Choose terms', 'wptravelengine-elementor-widgets' ),
				'default'       => array(),
				'multiple'      => true,
				'taxonomy_name' => 'trip_types',
				'condition'     => array(
					'listby'    => 'byids',
					'selectTax' => 'trip_types',
				),
			),
		),
	),
	'additional_settings'    => array(
		'type'        => 'control_section',
		'label'       => __( 'Additional', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'showCategory'     => array(
				'type'         => 'SWITCHER',
				'label'        => esc_html__( 'Show Category', 'wptravelengine-elementor-widgets' ),
				'label_on'     => esc_html__( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off'    => esc_html__( 'Hide', 'wptravelengine-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			),
			'searchBtnLabel'   => array(
				'type'    => 'TEXT',
				'label'   => esc_html__( 'Button Text', 'wptravelengine-elementor-widgets' ),
				'default' => esc_html__( 'Or, Browse All Tour', 'wptravelengine-elementor-widgets' ),
			),
			'searchBtnLink'    => array(
				'type'        => 'URL',
				'label'       => esc_html__( 'Button Link', 'wptravelengine-elementor-widgets' ),
				'default'     => array(
					'url'         => '',
					'is_external' => 'true',
				),
				'placeholder' => 'https://',
				'label_block' => true,
			),
			'search_btn_arrow' => array(
				'type'    => \Elementor\Controls_Manager::ICONS,
				'label'   => esc_html__( 'Button Icon', 'wptravelengine-elementor-widgets' ),
				'default' => array(
					'value'   => 'eicon-arrow-right',
					'library' => 'fa-solid',
				),
			),
		),
	),
	'content_style_section'  => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Search Field', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'search_background_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['search_background_color'],
			),
			'search_primary_color'    => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Search Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['search_primary_color'],
			),
			'search_padding'          => array(
				'label'      => esc_html__( 'Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => $selectors['search_padding'],
			),
			'search_margin'           => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => $selectors['search_margin'],
			),
			'search_border'           => array(
				'type'     => \Elementor\Group_Control_Border::get_type(),
				'selector' => $selectors['search_border'],
			),
			'search_border_radius'    => array(
				'type'       => 'DIMENSIONS',
				'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => $selectors['search_border_radius'],
			),
			'search_boxshadow'        => array(
				'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
				'selector' => $selectors['search_boxshadow'],
				'label'    => __( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
			),
		),
	),
	'category_style_section' => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Category', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'categorySpaceBetween' => array(
				'label'      => esc_html__( 'Space Between Items', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .wpte-tour-search' => '--item-spacing: {{SIZE}}{{UNIT}};',
				),
			),
			'categoryItemPadding'  => array(
				'label'      => esc_html__( 'Item Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wpte-tour-search' => '--item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			'categoryMargin'       => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wpte-tour-search' => '--category-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),

			'categoryTypography'   => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['categoryTypography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'category_tabs'        => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'category_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'categoryColor'        => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['category_color'],
							),
							'categoryBackground'   => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['category_background'],
							),
							'categoryBorder'       => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['category_border'],
							),
							'categoryBorderRadius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['category_radius'],
							),
						),
					),
					'category_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'categoryHoverColor'        => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['category_color_hover'],
							),
							'categoryHoverBackground'   => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['category_background_hover'],
							),
							'categoryHoverBorder'       => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['category_border_hover'],
							),
							'categoryHoverBorderRadius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['category_radius_hover'],
							),
						),
					),
				),
			),
		),
	),
	'search_button_section'  => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Button', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'search_button_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['search_button_typography'],
				'label'    => esc_html__( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'search_button_padding'    => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['search_button_padding'],
			),
			'search_button_tabs'       => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'search_button_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'search_button_bg_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['search_button_bg_color'],
							),
							'search_button_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['search_button_color'],
							),
							'search_button_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['search_button_border'],
							),
							'search_button_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['search_button_border_radius'],
							),
							'search_button_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['search_button_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
					'search_button_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'search_button_bg_hover_color' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['search_button_bg_hover_color'],
							),
							'search_button_hover_color'    => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['search_button_hover_color'],
							),
							'search_button_hover_border'   => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['search_button_hover_border'],
							),
							'search_button_hover_border_radius' => array(
								'type'       => 'DIMENSIONS',
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['search_button_hover_border_radius'],
							),
							'search_button_hover_boxshadow' => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['search_button_hover_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
				),
			),
		),
	),
);

return $controls;
