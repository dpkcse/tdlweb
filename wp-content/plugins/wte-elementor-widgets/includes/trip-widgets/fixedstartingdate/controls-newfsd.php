<?php
/**
 * Trip Fixed Starting Date (New Design) Widget Controls.
 *
 * @since 1.3.8
 * @package wptravelengine-elementor-widgets
 */

namespace WPTRAVELENGINEEB;

$selectors = array(
	// Choose a date.
	'date_padding'                 => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
	),
	'date_border'                  => '{{WRAPPER}} .elementor-widget-container .wte-fsd__button',
	'date_border_radius'           => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'date_color_active'            => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button.is-active' => 'color: {{VALUE}};',
	),
	'date_bg_color_active'         => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button.is-active' => 'background-color: {{VALUE}};',
	),
	'date_color_normal'            => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button' => 'color: {{VALUE}};',
	),
	'date_bg_color_normal'         => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button' => 'background-color: {{VALUE}};',
	),
	'date_color_hover'             => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button:hover' => 'color: {{VALUE}};',
	),
	'date_bg_color_hover'          => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__button:hover' => 'background-color: {{VALUE}};',
	),

	// Label Styles
	'time_slots_bg_color'          => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__time-slots' => 'background-color: {{VALUE}};',
	),
	'time_slots_color'             => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__time-slots' => 'color: {{VALUE}};',
	),
	'time_slots_border'            => '{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__time-slots',
	'time_slots_border_radius'     => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__time-slots' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'time_slots_boxshadow'         => '{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__time-slots',
	'group_discount_bg_color'      => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__group-discount' => 'background-color: {{VALUE}};',
	),
	'group_discount_color'         => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__group-discount' => 'color: {{VALUE}};',
	),
	'group_discount_border'        => '{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__group-discount',
	'group_discount_border_radius' => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__group-discount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'group_discount_boxshadow'     => '{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__group-discount',
	'availability_bg_color'        => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__availability-label' => 'background-color: {{VALUE}};',
	),
	'availability_color'           => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__availability-label' => 'color: {{VALUE}};',
	),
	'availability_border'          => '{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__availability-label',
	'availability_border_radius'   => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__availability-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'availability_boxshadow'       => '{{WRAPPER}} .elementor-widget-container .wte-fsd__tag.wte-fsd__availability-label',

	// Seat Slots Styles
	'available_seats_color'        => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-remaining-seats' => 'color: {{VALUE}};',
	),
	'unavailable_seats_color'      => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-remaining-seats.wte-fsd__sold-out' => 'color: {{VALUE}};',
	),

	// Book Now Button.
	'booknow_typography'           => '{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn',
	'booknow_padding'              => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'booknow_margin'               => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),

	'booknow_bgcolor'              => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn' => 'background-color: {{VALUE}};',
	),
	'booknow_color'                => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn' => 'color: {{VALUE}};',
	),
	'booknow_border'               => '{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn',
	'booknow_border_radius'        => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'booknow_boxshadow'            => '{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn',

	// Book Now Button Hover.
	'booknow_bg_hover_color'       => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn:hover' => 'background-color: {{VALUE}};',
	),
	'booknow_hover_color'          => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn:hover' => 'color: {{VALUE}};',
	),
	'booknow_hover_border'         => '{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn:hover',
	'booknow_hover_border_radius'  => array(
		'{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'booknow_hover_boxshadow'      => '{{WRAPPER}} .elementor-widget-container button.book-btn.wte-fsd__booknow-btn:hover',

	// LoadMore and ShowLess Button.
	'button_typography'            => '{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more, {{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less',
	'button_padding'               => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'button_margin'                => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),

	'button_bg_normal_color'       => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more' => 'background-color: {{VALUE}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less' => 'background-color: {{VALUE}};',
	),
	'button_normal_color'          => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more' => 'color: {{VALUE}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less' => 'color: {{VALUE}};',
	),
	'button_normal_border'         => '{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more, {{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less',
	'button_border_normal_radius'  => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'button_normal_boxshadow'      => '{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more, {{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less',

	// LoadMore and ShowLess Button Hover.
	'button_bg_hover_color'        => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more:hover' => 'background-color: {{VALUE}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less:hover' => 'background-color: {{VALUE}};',
	),
	'button_hover_color'           => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more:hover' => 'color: {{VALUE}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less:hover' => 'color: {{VALUE}};',
	),
	'button_hover_border_color'    => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more:hover' => 'border-color: {{VALUE}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less:hover' => 'border-color: {{VALUE}};',
	),
	'button_hover_border'          => '{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more:hover, {{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less:hover',
	'button_hover_border_radius'   => array(
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		'{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'button_hover_boxshadow'       => '{{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-more:hover, {{WRAPPER}} .elementor-widget-container .wte-fsd__availability-show-less:hover',
);

$controls = array(
	'trip_display'          => array(
		'type'        => 'control_section',
		'label'       => __( 'Trip Display', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'show_trip_title' => array(
				'label'     => __( 'Show Trip Title', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'default'   => 'yes',
			),
		),
	),
	'date_format_display'   => array(
		'type'        => 'control_section',
		'label'       => __( 'Date Format', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'date_format' => array(
				'type'    => 'SELECT',
				'label'   => __( 'Date Format', 'wptravelengine-elementor-widgets' ),
				'options' => array(
					'Y-m-d'  => __( '' . date_i18n( 'Y-m-d' ) . '', 'wptravelengine-elementor-widgets' ),
					'm/d/Y'  => __( '' . date_i18n( 'm/d/Y' ) . '', 'wptravelengine-elementor-widgets' ),
					'd/m/Y'  => __( '' . date_i18n( 'd/m/Y' ) . '', 'wptravelengine-elementor-widgets' ),
					'F j, Y' => __( '' . date_i18n( 'F j, Y' ) . '', 'wptravelengine-elementor-widgets' ),
					'M j, Y' => __( '' . date_i18n( 'M j, Y' ) . '', 'wptravelengine-elementor-widgets' ),
					'jS F Y' => __( '' . date_i18n( 'jS F Y' ) . '', 'wptravelengine-elementor-widgets' ),
				),
				'default' => 'M j, Y',
			),
			'days_format' => array(
				'type'    => 'SELECT',
				'label'   => __( 'Days Format', 'wptravelengine-elementor-widgets' ),
				'options' => array(
					'l' => __( '' . date_i18n( 'l' ) . '', 'wptravelengine-elementor-widgets' ),
					'D' => __( '' . date_i18n( 'D' ) . '', 'wptravelengine-elementor-widgets' ),
				),
				'default' => 'l',
			),
		),
	),
	'table_display'         => array(
		'type'        => 'control_section',
		'label'       => __( 'Table Columns', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'show_start_date'        => array(
				'label'     => __( 'Show Starting Date', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'default'   => 'yes',
			),
			'show_end_date'          => array(
				'label'     => __( 'Show Ending Date', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'default'   => 'yes',
			),
			'show_price_column'      => array(
				'label'     => __( 'Show Price', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'default'   => 'yes',
			),
			'show_space_left_column' => array(
				'label'     => __( 'Show Space Left', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'wptravelengine-elementor-widgets' ),
				'label_off' => __( 'Hide', 'wptravelengine-elementor-widgets' ),
				'default'   => 'yes',
			),

		),
	),
	'tags_label'            => array(
		'type'        => 'control_section',
		'label'       => __( 'Tags Labels', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'time_slots_label'     => array(
				'default' => __( 'Time Slots Available', 'wptravelengine-elementor-widgets' ),
				'type'    => 'TEXT',
				'label'   => __( 'Time Slots label', 'wptravelengine-elementor-widgets' ),
			),
			'group_discount_label' => array(
				'default' => __( 'Group Discount Available', 'wptravelengine-elementor-widgets' ),
				'type'    => 'TEXT',
				'label'   => __( 'Group Discount label', 'wptravelengine-elementor-widgets' ),
			),
		),
	),
	'button'                => array(
		'type'        => 'control_section',
		'label'       => __( 'Button Labels', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'book_now_btn_txt'  => array(
				'default' => __( 'Book Now', 'wptravelengine-elementor-widgets' ),
				'type'    => 'TEXT',
				'label'   => __( 'Book Now label', 'wptravelengine-elementor-widgets' ),
			),
			'sold_out_btn_txt'  => array(
				'default' => __( 'Sold Out', 'wptravelengine-elementor-widgets' ),
				'type'    => 'TEXT',
				'label'   => __( 'Sold Out label', 'wptravelengine-elementor-widgets' ),
			),
			'show_more_btn_txt' => array(
				'default' => __( 'Show More Dates', 'wptravelengine-elementor-widgets' ),
				'type'    => 'TEXT',
				'label'   => __( 'Load More label', 'wptravelengine-elementor-widgets' ),
			),
			'show_less_btn_txt' => array(
				'default' => __( 'Show Less Dates', 'wptravelengine-elementor-widgets' ),
				'type'    => 'TEXT',
				'label'   => __( 'Show Less label', 'wptravelengine-elementor-widgets' ),
			),
		),
	),
	'no_fsd_available_text' => array(
		'type'        => 'control_section',
		'label'       => __( 'No Dates Available Text', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'fsd_not_available_text' => array(
				'default' => __( 'No Fixed Departure Dates available.', 'wptravelengine-elementor-widgets' ),
				'type'    => 'TEXT',
				'label'   => __( 'No FSD dates label', 'wptravelengine-elementor-widgets' ),
			),
		),
	),
	'choose_date'           => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Date Selector', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'date_padding'       => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['date_padding'],
				'default'    => array(
					'top'    => '12',
					'right'  => '20',
					'bottom' => '12',
					'left'   => '20',
				),
			),
			'date_border'        => array(
				'type'     => \Elementor\Group_Control_Border::get_type(),
				'selector' => $selectors['date_border'],
			),
			'date_border_radius' => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => $selectors['date_border_radius'],
				'default'    => array(
					'top'    => '8',
					'right'  => '8',
					'bottom' => '8',
					'left'   => '8',
				),
			),
			'date_button_tabs'   => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'date_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'date_bg_color_normal' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['date_bg_color_normal'],
							),
							'date_color_normal'    => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['date_color_normal'],
							),
						),
					),
					'date_active' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Active', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'date_bg_color_active' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['date_bg_color_active'],
							),
							'date_color_active'    => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['date_color_active'],
							),
						),
					),
					'date_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'date_bg_color_hover' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['date_bg_color_hover'],
							),
							'date_color_hover'    => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['date_color_hover'],
							),
						),
					),
				),
			),
		),
	),
	'tags_style'            => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Tags Style', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'tags_tabs' => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'time_slots_tag'     => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Time Slots', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'time_slots_bg_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['time_slots_bg_color'],
								'default'   => '#2A85FF1A',
							),
							'time_slots_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['time_slots_color'],
								'default'   => '#018BFF',
							),
							'time_slots_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['time_slots_border'],
							),
							'time_slots_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['time_slots_border_radius'],
								'default'    => array(
									'top'    => '16',
									'right'  => '16',
									'bottom' => '16',
									'left'   => '16',
								),
							),
							'time_slots_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['time_slots_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
					'group_discount_tag' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Group Discount', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'group_discount_bg_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['group_discount_bg_color'],
								'default'   => '#12B76A14',
							),
							'group_discount_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['group_discount_color'],
								'default'   => '#12B76A',
							),
							'group_discount_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['group_discount_border'],
							),
							'group_discount_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['group_discount_border_radius'],
								'default'    => array(
									'top'    => '16',
									'right'  => '16',
									'bottom' => '16',
									'left'   => '16',
								),
							),
							'group_discount_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['group_discount_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
					'availability_tag'   => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Availability', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'availability_bg_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['availability_bg_color'],
								'default'   => '#D86C3514',
							),
							'availability_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['availability_color'],
								'default'   => '#D86C35',
							),
							'availability_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['availability_border'],

							),
							'availability_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['availability_border_radius'],
								'default'    => array(
									'top'    => '16',
									'right'  => '16',
									'bottom' => '16',
									'left'   => '16',
								),
							),
							'availability_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['availability_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
				),
			),
		),
	),
	'seat_slots_style'      => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Seat Slots', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'seat_slots_label_tabs' => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'available_seats_label'   => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Available Seats', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'available_seats_color' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['available_seats_color'],
								'default'   => '#000000',
							),
						),
					),
					'unavailable_seats_label' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Unavailable Seats', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'unavailable_seats_color' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['unavailable_seats_color'],
							),
						),
					),
				),
			),
		),
	),
	'booknow_button_style'  => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Book Now Button', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'booknow_typography'  => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['booknow_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'booknow_padding'     => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['booknow_padding'],
			),
			'booknow_margin'      => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['booknow_margin'],
			),
			'booknow_button_tabs' => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'booknow_button_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'booknow_bgcolor'       => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['booknow_bgcolor'],
							),
							'booknow_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['booknow_color'],
							),
							'booknow_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['booknow_border'],
							),
							'booknow_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['booknow_border_radius'],
								'default'    => array(
									'top'    => '100',
									'right'  => '100',
									'bottom' => '100',
									'left'   => '100',
								),
							),
							'booknow_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['booknow_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
					'booknow_button_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'booknow_bg_hover_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['booknow_bg_hover_color'],
							),
							'booknow_hover_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['booknow_hover_color'],
							),
							'booknow_hover_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['booknow_hover_border'],
							),
							'booknow_hover_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['booknow_hover_border_radius'],
								'default'    => array(
									'top'    => '100',
									'right'  => '100',
									'bottom' => '100',
									'left'   => '100',
								),
							),
							'booknow_hover_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['booknow_hover_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
				),
			),
		),
	),
	'loader_button_style'   => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Loader Buttons', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'button_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['button_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'button_padding'    => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['button_padding'],
				'default'    => array(
					'top'    => '18',
					'right'  => '32',
					'bottom' => '18',
					'left'   => '32',
				),
			),
			'button_margin'     => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['button_margin'],
				'default'    => array(
					'top'    => '0',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
				),
			),
			'button_tabs'       => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'button_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'button_bg_normal_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['button_bg_normal_color'],
							),
							'button_normal_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['button_normal_color'],
							),
							'button_normal_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['button_normal_border'],
								'default'  => 'solid',
							),
							'button_border_normal_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['button_border_normal_radius'],
								'default'    => array(
									'top'    => '100',
									'right'  => '100',
									'bottom' => '100',
									'left'   => '100',
								),
							),
							'button_normal_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['button_normal_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
					'button_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'button_bg_hover_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['button_bg_hover_color'],
							),
							'button_hover_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['button_hover_color'],
							),
							'button_hover_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['button_hover_border'],
							),
							'button_hover_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['button_hover_border_radius'],
								'default'    => array(
									'top'    => '100',
									'right'  => '100',
									'bottom' => '100',
									'left'   => '100',
								),
							),
							'button_hover_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['button_hover_boxshadow'],
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
