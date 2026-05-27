<?php


namespace WPTRAVELENGINEEB;

/**
 * Trips Taxonomy One Widget Controls.
 *
 * @since 1.3.7
 * @package wptravelengine-elementor-widgets
 */

$selectors = array(
	// general section
	'card_background_color'    => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-bg: {{VALUE}};' ),
	'card_padding'             => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'card_border'              => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__wrap',
	'card_border_radius'       => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'card_boxshadow'           => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__wrap',

	// content section
	'content_alignment'        => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-alignment: {{VALUE}};' ),
	'content_background_color' => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-background: {{VALUE}};' ),
	'content_padding'          => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'content_margin'           => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'content_border'           => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__content',
	'content_border_radius'    => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'content_boxshadow'        => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__content',


	// image section
	'image_scale'              => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-fit: {{VALUE}};' ),
	'image_width'              => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-w: {{SIZE}}{{UNIT}};' ),
	'image_height'             => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-h: {{SIZE}}{{UNIT}};' ),
	'animation_type'           => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card img' => 'transition-timing-function: {{VALUE}};' ),
	'img_animation_duration'   => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card img' => 'transition-duration: {{VALUE}}s;' ),
	'image_border_radius'      => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// title
	'title_typography'         => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__tax-title',
	'title_color'              => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-fc: {{VALUE}};' ),
	'title_color_hover'        => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-fc-h: {{VALUE}};' ),
	'title_margin'             => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// trip count
	'trip_count_typography'    => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__tax-count',
	'trip_count_color'         => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--tax-count-color: {{VALUE}};' ),
	'trip_count_margin'        => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--tax-count-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
);

$controls = array(
	'trips_module_layout_settings' => array(
		'type'        => 'control_section',
		'label'       => __( 'Layout', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'trips_col_no'   => array(
				'type'           => \Elementor\Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'label'          => __( 'Columns', 'wptravelengine-elementor-widgets' ),
				'default'        => '5',
				'tablet_default' => '3',
				'mobile_default' => '1',
				'is_responsive'  => true,
				'selectors'      => array(
					'{{WRAPPER}} .wpte-elementor-widget ' => '--column-no: {{value}};',
				),
			),
			'trips_card_gap' => array(
				'label'      => __( 'Gap', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::GAPS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'row'    => '20',
					'column' => '20',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wpte-elementor-widget ' => '--gap:{{ROW}}{{UNIT}} {{COLUMN}}{{UNIT}}',
				),
				'validators' => array(
					'Number' => array(
						'min' => 0,
					),
				),
			),
			'cardlayout'     => array(
				'label'   => __( 'Layouts', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SELECT',
				'options' => array(
					'1' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'2' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
					'3' => __( 'Layout 3', 'wptravelengine-elementor-widgets' ),
				),
				'default' => '1',
			),
		),
	),
	'sorting_filtering'            => array(
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
			'itemsCount'           => array(
				'type'      => 'NUMBER',
				'label'     => __( 'Number of items', 'wptravelengine-elementor-widgets' ),
				'min'       => 1,
				'default'   => 5,
				'condition' => array(
					'listby!' => 'byids',
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

	'additional_settings'          => array(
		'type'        => 'control_section',
		'label'       => __( 'Additional', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'headingTag'     => array(
				'type'    => 'SELECT',
				'label'   => esc_html__( 'Title Tag', 'wptravelengine-elementor-widgets' ),
				'options' => array(
					'h1'   => esc_html__( 'H1', 'wptravelengine-elementor-widgets' ),
					'h2'   => esc_html__( 'H2', 'wptravelengine-elementor-widgets' ),
					'h3'   => esc_html__( 'H3', 'wptravelengine-elementor-widgets' ),
					'h4'   => esc_html__( 'H4', 'wptravelengine-elementor-widgets' ),
					'h5'   => esc_html__( 'H5', 'wptravelengine-elementor-widgets' ),
					'h6'   => esc_html__( 'H6', 'wptravelengine-elementor-widgets' ),
					'div'  => esc_html__( 'Div', 'wptravelengine-elementor-widgets' ),
					'span' => esc_html__( 'span', 'wptravelengine-elementor-widgets' ),
					'p'    => esc_html__( 'p', 'wptravelengine-elementor-widgets' ),
				),
				'default' => 'h3',
			),
			'showTripCounts' => array(
				'label'   => __( 'Show Trip Counts', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => '',
			),
			'countLabel'     => array(
				'label'       => __( 'Trips Count Label', 'wptravelengine-elementor-widgets' ),
				'type'        => 'TEXT',
				'condition'   => array( 'showTripCounts' => 'yes' ),
				'default'     => __( 'Trip|Trips', 'wptravelengine-elementor-widgets' ),
				'description' => __( 'First: singular (1 Trip) | Second: plural (2 Trips)', 'wptravelengine-elementor-widgets' ),
			),
		),
	),
	'general_section'              => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'General', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'card_background_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['card_background_color'],
			),
			'card_padding'          => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['card_padding'],
			),
			'card_border'           => array(
				'type'     => \Elementor\Group_Control_Border::get_type(),
				'selector' => $selectors['card_border'],
			),
			'card_border_radius'    => array(
				'type'       => 'DIMENSIONS',
				'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => $selectors['card_border_radius'],
			),
			'card_boxshadow'        => array(
				'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
				'selector' => $selectors['card_boxshadow'],
				'label'    => __( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
			),
		),
	),
	'content_section'              => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Content', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'content_alignment'     => array(
				'type'      => 'CHOOSE',
				'label'     => __( 'Alignment', 'wptravelengine-elementor-widgets' ),
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
				'selectors' => $selectors['content_alignment'],
			),
			'content_bg_color'      => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['content_background_color'],
			),
			'content_margin'        => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['content_margin'],
			),
			'content_padding'       => array(
				'label'      => esc_html__( 'Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['content_padding'],
			),
			'content_border'        => array(
				'type'     => \Elementor\Group_Control_Border::get_type(),
				'selector' => $selectors['content_border'],
			),
			'content_border_radius' => array(
				'type'       => 'DIMENSIONS',
				'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => $selectors['content_border_radius'],
			),
			'content_boxshadow'     => array(
				'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
				'label'    => __( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
				'selector' => $selectors['content_boxshadow'],
			),
		),
	),

	'title_section'                => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Title', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'title_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['title_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'title_tabs'       => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'title_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'title_color' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['title_color'],
							),
						),
					),
					'title_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'title_color_hover' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['title_color_hover'],
							),
						),
					),
				),
			),
			'title_margin'     => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['title_margin'],
			),
		),
	),

	'count_section'                => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Count', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'trip_count_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['trip_count_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'trip_count_color'      => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['trip_count_color'],
			),
			'trip_count_margin'     => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['trip_count_margin'],
			),
		),
		'condition'   => array( 'showTripCounts' => 'yes' ),
	),

	'image_section'                => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Image', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'image_tabs' => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'image_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'image_size'          => array(
								'type'    => 'SELECT',
								'label'   => esc_html__( 'Image Size', 'wptravelengine-elementor-widgets' ),
								'options' => Widget::get_image_size_options(),
								'default' => 'trip-thumb-size',
							),
							'image_custom_size'   => array(
								'type'      => 'IMAGE_DIMENSIONS',
								'label'     => esc_html__( 'Custom Image Size', 'wptravelengine-elementor-widgets' ),
								'condition' => array(
									'image_size' => 'custom',
								),
							),
							'image_scale'         => array(
								'type'      => 'SELECT',
								'label'     => esc_html__( 'Object Fit', 'wptravelengine-elementor-widgets' ),
								'options'   => array(
									'initial' => esc_html__( 'Original', 'wptravelengine-elementor-widgets' ),
									'contain' => esc_html__( 'Contain', 'wptravelengine-elementor-widgets' ),
									'cover'   => esc_html__( 'Cover', 'wptravelengine-elementor-widgets' ),
									'fill'    => esc_html__( 'Fill', 'wptravelengine-elementor-widgets' ),
								),
								'default'   => 'cover',
								'selectors' => $selectors['image_scale'],
							),
							'image_width'         => array(
								'type'       => 'SLIDER',
								'label'      => esc_html__( 'Width', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'range'      => array(
									'%'  => array(
										'min' => 0,
										'max' => 100,
									),
									'px' => array(
										'min' => 0,
										'max' => 1000,
									),
								),
								'selectors'  => $selectors['image_width'],
							),
							'image_height'        => array(
								'type'       => 'SLIDER',
								'label'      => esc_html__( 'Height', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'range'      => array(
									'%'  => array(
										'min' => 0,
										'max' => 100,
									),
									'px' => array(
										'min' => 0,
										'max' => 1000,
									),
								),
								'selectors'  => $selectors['image_height'],
							),
							'image_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['image_border_radius'],
							),
						),
					),
					'image_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'animation_type'         => array(
								'type'      => 'SELECT',
								'label'     => esc_html__( 'Animation Type', 'wptravelengine-elementor-widgets' ),
								'options'   => array(
									'linear'      => esc_html__( 'Linear', 'wptravelengine-elementor-widgets' ),
									'ease'        => esc_html__( 'Ease', 'wptravelengine-elementor-widgets' ),
									'ease-in'     => esc_html__( 'Ease-in', 'wptravelengine-elementor-widgets' ),
									'ease-out'    => esc_html__( 'Ease-out', 'wptravelengine-elementor-widgets' ),
									'ease-in-out' => esc_html__( 'Ease-in-out', 'wptravelengine-elementor-widgets' ),
									'step-start'  => esc_html__( 'Step-start', 'wptravelengine-elementor-widgets' ),
									'step-end'    => esc_html__( 'Step-end', 'wptravelengine-elementor-widgets' ),
									'initial'     => esc_html__( 'Initial', 'wptravelengine-elementor-widgets' ),
									'inherit'     => esc_html__( 'Inherit', 'wptravelengine-elementor-widgets' ),
								),
								'default'   => 'linear',
								'selectors' => $selectors['animation_type'],
							),
							'img_animation_duration' => array(
								'type'      => \Elementor\Controls_Manager::NUMBER,
								'label'     => esc_html__( 'Animation Duration (sec)', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['img_animation_duration'],
								'min'       => 0,
								'max'       => 100,
								'step'      => 0.1,
								'default'   => 0.3,
							),
						),
					),
				),
			),
		),
	),
);

return $controls;
