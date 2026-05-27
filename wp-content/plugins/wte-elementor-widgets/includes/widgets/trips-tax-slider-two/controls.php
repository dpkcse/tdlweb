<?php

namespace WPTRAVELENGINEEB;

/**
 * Trips Taxonomy Slider 2 Controls.
 *
 * @since 1.3.7
 * @package wptravelengine-elementor-widgets
 */

$selectors = array(
	// general section
	'card_background_color'            => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-bg: {{VALUE}};' ),
	'card_padding'                     => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'card_border'                      => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__wrap',
	'card_border_radius'               => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'card_boxshadow'                   => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__wrap',

	// content section
	'content_alignment'                => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-alignment: {{VALUE}};' ),
	'content_background_color'         => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-background: {{VALUE}};' ),
	'content_padding'                  => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'content_border'                   => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__content',
	'content_border_radius'            => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'content_boxshadow'                => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__content',

	// image section
	'image_scale'                      => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-fit: {{VALUE}};' ),
	'image_width'                      => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-w: {{SIZE}}{{UNIT}};' ),
	'image_height'                     => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-h: {{SIZE}}{{UNIT}};' ),
	'animation_type'                   => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card img' => 'transition-timing-function: {{VALUE}};' ),
	'img_animation_duration'           => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card img' => 'transition-duration: {{VALUE}}s;' ),
	'image_border_radius'              => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// CTA
	'cta_content_alignment'            => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-content-alignment: {{VALUE}};' ),
	'cta_content_padding'              => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// CTA title
	'cta_title_typography'             => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta-title',
	'cta_title_color'                  => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-t-fc: {{VALUE}};' ),
	'cta_title_margin'                 => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-t-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// CTA desc
	'cta_desc_typography'              => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta-desc',
	'cta_desc_color'                   => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-desc-fc: {{VALUE}};' ),
	'cta_desc_margin'                  => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-desc-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// CTA button
	'cta_button_typography'            => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta-btn',
	'cta_button_padding'               => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-btn-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'cta_button_bg_color'              => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-btn-bg: {{VALUE}};' ),
	'cta_button_color'                 => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-btn-fc: {{VALUE}};' ),
	'cta_button_border'                => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta-btn',
	'cta_button_border_radius'         => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-btn-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'cta_button_boxshadow'             => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta-btn',
	'cta_button_bg_hover_color'        => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-btn-bg-h: {{VALUE}};' ),
	'cta_button_hover_color'           => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta' => '--cta-btn-fc-h: {{VALUE}};' ),
	'cta_button_hover_border'          => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta-btn:hover',
	'cta_button_hover_boxshadow'       => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tax-slider__cta-btn:hover',

	// title
	'title_typography'                 => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__tax-title',
	'title_color'                      => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-fc: {{VALUE}};' ),
	'title_color_hover'                => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-fc-h: {{VALUE}};' ),
	'title_margin'                     => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// trip count
	'trip_count_typography'            => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__tax-count',
	'trip_count_color'                 => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--tax-count-color: {{VALUE}};' ),
	'trip_count_margin'                => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--tax-count-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// Slider Arrow
	'slider_arrow_padding'             => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'slider_arrow_bg_color'            => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-bg-n: {{VALUE}};' ),
	'slider_arrow_color'               => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-color-n: {{VALUE}};' ),
	'slider_arrow_bg_color_hover'      => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-bg-h: {{VALUE}};' ),
	'slider_arrow_color_hover'         => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-color-h: {{VALUE}};' ),
	'slider_arrow_border'              => '{{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-prev, {{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-next',
	'slider_arrow_border_radius'       => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'slider_arrow_box_shadow'          => '{{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-prev, {{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-next',
	'slider_arrow_border_hover'        => '{{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-prev:hover, {{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-next:hover',
	'slider_arrow_border_radius_hover' => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-radius-h: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'slider_arrow_size'                => array( '{{WRAPPER}} .wpte-elementor-widget ' => '--slider-arrow-size: {{SIZE}}{{UNIT}};' ),
	'slider_arrow_offset'              => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-offset: {{SIZE}}{{UNIT}};' ),

	// slider pagination.
	'slider_dots_active_color'         => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-pagination-active-color: {{VALUE}};' ),
	'slider_dots_color'                => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-pagination-color: {{VALUE}};' ),
	'slider_dots_spacing'              => array( '{{WRAPPER}} .wpte-elementor-widget' => '--slider-pagination-spacing: {{SIZE}}{{UNIT}};' ),
);


$controls = array(
	'trips_tax_slider_settings' => array(
		'type'        => 'control_section',
		'label'       => __( 'Layout', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'cardlayout' => array(
				'label'   => __( 'Layouts', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SELECT',
				'options' => array(
					'1' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'2' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
				),
				'default' => '1',
			),
		),
	),
	'sorting_filtering'         => array(
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
				'default'   => 4,
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
	'additional_settings'       => array(
		'type'        => 'control_section',
		'label'       => __( 'Additional', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'ctaTitleLabel'   => array(
				'label'     => __( 'CTA Title', 'wptravelengine-elementor-widgets' ),
				'type'      => 'TEXT',
				'condition' => array( 'cardlayout' => '1' ),
				'default'   => __( 'Travel Around The World.', 'wptravelengine-elementor-widgets' ),
			),
			'ctaContentLabel' => array(
				'label'     => __( 'CTA Content', 'wptravelengine-elementor-widgets' ),
				'type'      => 'TEXTAREA',
				'condition' => array( 'cardlayout' => '1' ),
				'default'   => __( 'The United States, with its diverse landscapes, iconic landmarks, vibrant cities, and rich cultural heritage, offers an unparalleled tourist destination.', 'wptravelengine-elementor-widgets' ),
			),
			'ctaBtnLabel'     => array(
				'label'     => __( 'CTA Button', 'wptravelengine-elementor-widgets' ),
				'type'      => 'TEXT',
				'condition' => array( 'cardlayout' => '1' ),
				'default'   => __( 'Explore Now', 'wptravelengine-elementor-widgets' ),
			),
			'ctaBtnLink'      => array(
				'label'       => __( 'CTA Button Link', 'wptravelengine-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'default'     => array(
					'url'         => '',
					'is_external' => 'true',
				),
				'placeholder' => 'https://',
				'label_block' => true,
				'condition'   => array( 'cardlayout' => '1' ),
			),
			'cta_btn_arrow'   => array(
				'label'     => __( 'CTA Icon', 'wptravelengine-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'eicon-arrow-right',
					'library' => 'fa-solid',
				),
				'condition' => array( 'cardlayout' => '1' ),
			),
			'ctaHeadingTag'   => array(
				'type'      => 'SELECT',
				'label'     => esc_html__( 'CTA Title Tag', 'wptravelengine-elementor-widgets' ),
				'options'   => array(
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
				'condition' => array( 'cardlayout' => '1' ),
				'default'   => 'h2',
			),
			'headingTag'      => array(
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
			'showTripCounts'  => array(
				'label'   => __( 'Show Trip Counts', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => '',
			),
			'countLabel'      => array(
				'label'       => __( 'Trips Count Label', 'wptravelengine-elementor-widgets' ),
				'type'        => 'TEXT',
				'condition'   => array( 'showTripCounts' => 'yes' ),
				'default'     => __( 'Trip|Trips', 'wptravelengine-elementor-widgets' ),
				'description' => __( 'First: singular (1 Trip) | Second: plural (2 Trips)', 'wptravelengine-elementor-widgets' ),
			),

		),
	),
	'slider_settings'           => array(
		'type'        => 'control_section',
		'label'       => __( 'Slider', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'slidesPerViewDesktop'     => array(
				'type'           => \Elementor\Controls_Manager::NUMBER,
				'label'          => __( 'Slides Number', 'wptravelengine-elementor-widgets' ),
				'default'        => 2,
				'laptop_default' => 2,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'is_responsive'  => true,
				'condition'      => array( 'cardlayout' => '1' ),
			),
			'slidesPerViewDesktopLay2' => array(
				'type'           => \Elementor\Controls_Manager::NUMBER,
				'label'          => __( 'Slides Number', 'wptravelengine-elementor-widgets' ),
				'default'        => 5,
				'laptop_default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 1,
				'is_responsive'  => true,
				'condition'      => array( 'cardlayout' => '2' ),
			),
			'spaceBetween'             => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Space Between Slides', 'wptravelengine-elementor-widgets' ),
				'default' => 20,
			),
			'autoplay'                 => array(
				'type'    => 'SWITCHER',
				'label'   => __( 'Autoplay', 'wptravelengine-elementor-widgets' ),
				'default' => 'yes',
			),
			'autoplaydelay'            => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Autoplay Speed', 'wptravelengine-elementor-widgets' ),
				'default' => 3000,
			),
			'loop'                     => array(
				'type'    => 'SWITCHER',
				'label'   => __( 'Loop', 'wptravelengine-elementor-widgets' ),
				'default' => 'yes',
			),
			'speed'                    => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Transition Speed (ms)', 'wptravelengine-elementor-widgets' ),
				'default' => 300,
			),
			'arrow'                    => array(
				'type'           => \Elementor\Controls_Manager::SWITCHER,
				'label'          => __( 'Slider Arrow', 'wptravelengine-elementor-widgets' ),
				'default'        => 'yes',
				'laptop_default' => 'yes',
				'tablet_default' => 'yes',
				'is_responsive'  => true,
				'return_value'   => 'yes',
			),
			'pagination'               => array(
				'type'           => \Elementor\Controls_Manager::SWITCHER,
				'label'          => __( 'Slider Pagination', 'wptravelengine-elementor-widgets' ),
				'default'        => 'yes',
				'laptop_default' => 'yes',
				'tablet_default' => 'yes',
				'mobile_default' => 'yes',
				'is_responsive'  => true,
				'return_value'   => 'yes',
			),
		),
	),

	'general_section'           => array(
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

	'content_section'           => array(
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

	'cta_section'               => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'CTA', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'cta_content_alignment' => array(
				'type'      => 'CHOOSE',
				'label'     => __( 'Alignment', 'wptravelengine-elementor-widgets' ),
				'options'   => array(
					'start'  => array(
						'title' => esc_html__( 'Left', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-text-align-center',
					),
					'end'    => array(
						'title' => esc_html__( 'Right', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => $selectors['cta_content_alignment'],
			),
			'cta_padding'           => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['cta_content_padding'],
			),
			'title_heading'         => array(
				'type'      => 'HEADING',
				'label'     => __( 'Title', 'wptravelengine-elementor-widgets' ),
				'separator' => 'before',
			),
			'cta_title_typography'  => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['cta_title_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'cta_title_color'       => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['cta_title_color'],
			),
			'cta_title_margin'      => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['cta_title_margin'],
			),
			'desc_heading'          => array(
				'type'      => 'HEADING',
				'label'     => __( 'Description', 'wptravelengine-elementor-widgets' ),
				'separator' => 'before',
			),
			'cta_desc_typography'   => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['cta_desc_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'cta_desc_color'        => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['cta_desc_color'],
			),
			'cta_desc_margin'       => array(
				'label'      => esc_html__( 'Margin', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['cta_desc_margin'],
			),
			'desc_heading'          => array(
				'type'      => 'HEADING',
				'label'     => __( 'Description', 'wptravelengine-elementor-widgets' ),
				'separator' => 'before',
			),
			'button_heading'        => array(
				'type'      => 'HEADING',
				'label'     => __( 'Button', 'wptravelengine-elementor-widgets' ),
				'separator' => 'before',
			),
			'cta_button_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['cta_button_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'cta_button_padding'    => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['cta_button_padding'],
			),
			'cta_button_tabs'       => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'cta_button_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'cta_button_bg_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['cta_button_bg_color'],
							),
							'cta_button_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['cta_button_color'],
							),
							'cta_button_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['cta_button_border'],
							),
							'cta_button_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['cta_button_border_radius'],
							),
							'cta_button_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['cta_button_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
					'cta_button_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'cta_button_bg_hover_color'  => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['cta_button_bg_hover_color'],
							),
							'cta_button_hover_color'     => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['cta_button_hover_color'],
							),
							'cta_button_hover_border'    => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'selector' => $selectors['cta_button_hover_border'],
							),
							'cta_button_hover_boxshadow' => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['cta_button_hover_boxshadow'],
								'label'    => esc_html__( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
				),
			),
		),
		'condition'   => array( 'cardlayout' => '1' ),
	),

	'title_section'             => array(
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

	'count_section'             => array(
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

	'image_section'             => array(
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
								'default' => 'destination-thumb-size',
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
	'slider_style_section'      => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Slider', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'heading_style_arrow'      => array(
				'type'  => 'HEADING',
				'label' => __( 'Arrow', 'wptravelengine-elementor-widgets' ),
			),
			'slider_prev_arrow_icon'   => array(
				'type'          => \Elementor\Controls_Manager::ICONS,
				'label'         => esc_html__( 'Prev Arrow', 'wptravelengine-elementor-widgets' ),
				'skin'          => 'inline',
				'label_block'   => false,
				'skin_settings' => array(
					'inline' => array(
						'none' => array(
							'label' => 'Default',
							'icon'  => 'eicon-arrow-left',
						),
						'icon' => array(
							'icon' => 'eicon-star',
						),
					),
				),
				'recommended'   => array(
					'fa-regular' => array(
						'arrow-alt-circle-left',
						'caret-square-left',
					),
					'fa-solid'   => array(
						'angle-double-left',
						'angle-left',
						'arrow-alt-circle-left',
						'arrow-circle-left',
						'arrow-left',
						'caret-left',
						'caret-square-left',
						'chevron-circle-left',
						'chevron-left',
						'long-arrow-alt-left',
					),
				),
			),
			'slider_next_arrow_icon'   => array(
				'type'          => \Elementor\Controls_Manager::ICONS,
				'label'         => esc_html__( 'Next Arrow', 'wptravelengine-elementor-widgets' ),
				'skin'          => 'inline',
				'label_block'   => false,
				'skin_settings' => array(
					'inline' => array(
						'none' => array(
							'label' => 'Default',
							'icon'  => 'eicon-arrow-right',
						),
						'icon' => array(
							'icon' => 'eicon-star',
						),
					),
				),
				'recommended'   => array(
					'fa-regular' => array(
						'arrow-alt-circle-right',
						'caret-square-right',
					),
					'fa-solid'   => array(
						'angle-double-right',
						'angle-right',
						'arrow-alt-circle-right',
						'arrow-circle-right',
						'arrow-right',
						'caret-right',
						'caret-square-right',
						'chevron-circle-right',
						'chevron-right',
						'long-arrow-alt-right',
					),
				),
			),
			'slider_arrow_padding'     => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['slider_arrow_padding'],
			),
			'slider_arrow_size'        => array(
				'type'       => 'SLIDER',
				'label'      => esc_html__( 'Size', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => $selectors['slider_arrow_size'],
			),
			'slider_options_tabs'      => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'slider_navigation_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'slider_arrow_bg_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['slider_arrow_bg_color'],
							),
							'slider_arrow_color'         => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['slider_arrow_color'],
							),
							'slider_arrow_border'        => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'label'    => __( 'Border', 'wptravelengine-elementor-widgets' ),
								'selector' => $selectors['slider_arrow_border'],
							),
							'slider_arrow_border_radius' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['slider_arrow_border_radius'],
							),
							'slider_arrow_box_shadow'    => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'selector' => $selectors['slider_arrow_box_shadow'],
								'label'    => __( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
							),
						),
					),
					'slider_navigation_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'slider_arrow_bg_color_hover' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['slider_arrow_bg_color_hover'],
							),
							'slider_arrow_color_hover'    => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['slider_arrow_color_hover'],
							),
							'slider_arrow_border_hover'   => array(
								'type'     => \Elementor\Group_Control_Border::get_type(),
								'label'    => esc_html__( 'Border Color', 'wptravelengine-elementor-widgets' ),
								'selector' => $selectors['slider_arrow_border_hover'],
							),
							'slider_arrow_border_radius_hover' => array(
								'type'       => \Elementor\Controls_Manager::DIMENSIONS,
								'label'      => __( 'Border Radius', 'wptravelengine-elementor-widgets' ),
								'size_units' => array( 'px', '%' ),
								'selectors'  => $selectors['slider_arrow_border_radius_hover'],
							),
						),
					),
				),
			),
			'slider_pagination_label'  => array(
				'type'      => 'HEADING',
				'label'     => __( 'Pagination', 'wptravelengine-elementor-widgets' ),
				'separator' => 'before',
			),
			'slider_dots_color'        => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['slider_dots_color'],
			),
			'slider_dots_active_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Active Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['slider_dots_active_color'],
			),
			'slider_dots_spacing'      => array(
				'label'      => esc_html__( 'Spacing', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors'  => $selectors['slider_dots_spacing'],
			),
		),
	),
);
// Add controls to add Trips by taxonomy
// Dynamically get all WP Travel Engine taxonomies including custom ones
$taxonomies = wptravelengineeb_get_trip_taxonomies();

$terms_display_settings = array();
foreach ( $taxonomies as $filter_name => $filter_args ) {
	$terms_display_settings[ "{$filter_name}_termstoDisplay" ] = array(
		'type'          => 'TAXONOMY_TERMS_SELECT2',
		'label'         => $filter_args . WPTRAVELENGINEEB_NEWCONTROL,
		'taxonomy_name' => $filter_name,
		'condition'     => array(
			'listby' => $filter_name,
		),
		'multiple'      => true,
	);
}

foreach ( $terms_display_settings as $term => $value ) {
	$controls['sorting_filtering']['subcontrols'][ $term ] = $value;
}

return $controls;
