<?php

namespace WPTRAVELENGINEEB;

/**
 * Advanced Trips Widget Controls.
 *
 * @since 1.3.0
 * @package wptravelengine-elementor-widgets
 */

$selectors = array(
	// general section
	'card_background_color'            => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-bg: {{VALUE}};',
	),
	'card_padding'                     => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-p: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'card_border'                      => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__wrap',
	'card_border_radius'               => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--g-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

	),
	'card_boxshadow'                   => '{{WRAPPER}} .wpte-elementor-widget .wpte-card',

	// content section
	'content_alignment'                => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-alignment: {{VALUE}};',
	),
	'content_background_color'         => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-background: {{VALUE}};',
	),
	'content_padding'                  => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'content_border'                   => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__content',
	'content_border_radius'            => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--content-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'content_boxshadow'                => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__content',

	// image section
	'image_scale'                      => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-fit: {{VALUE}};',
	),
	'image_width'                      => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-w: {{SIZE}}{{UNIT}};',
	),
	'image_height'                     => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-h: {{SIZE}}{{UNIT}};',
	),
	'animation_type'                   => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card img' => 'transition-timing-function: {{VALUE}};',
	),
	'img_animation_duration'           => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card img' => 'transition-duration: {{VALUE}}s;',
	),
	'image_border_radius'              => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--img-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'image_boxshadow'                  => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__media .wpte-card__image',


	// title
	'title_typography'                 => '{{WRAPPER}} .wpte-elementor-widget .wpte-card__title',
	'title_color'                      => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-fc: {{VALUE}};' ),
	'title_color_hover'                => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-fc-h: {{VALUE}};' ),
	'title_margin'                     => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--t-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// location
	'location_typography'              => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__location',
	'location_icon_color'              => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--l-ic: {{VALUE}};' ),
	'location_icon_size'               => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--l-is: {{SIZE}}{{UNIT}};' ),
	'location_margin'                  => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--l-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'location_color'                   => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--l-fc: {{VALUE}};' ),
	'location_hover_color'             => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--l-fc-h: {{VALUE}};' ),
	'location_hover_decoration'        => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--l-decoration: {{VALUE}};' ),


	// meta
	'meta_typography'                  => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__meta',
	'meta_color'                       => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--m-fc: {{VALUE}};',
	),
	'meta_icon_color'                  => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--m-ic: {{VALUE}};',
	),
	'meta_spacing'                     => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--m-sb: {{SIZE}}{{UNIT}};',
	),
	'meta_margin'                      => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--m-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'meta_icon_size'                   => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--m-is: {{SIZE}}{{UNIT}};',
	),

	// price
	'price_typography'                 => '{{WRAPPER}} .wpte-elementor-widget .wpte-card:not(.hero-color) .wpte-card__price .actual-price',
	'price_color'                      => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card:not(.hero-color)' => '--p-fc-n: {{VALUE}};',
	),
	'price_bg_color'                   => array(
		'{{WRAPPER}} .wpte-elementor-widget.wpte-adv-trips_two .wpte-card' => '--p-bg: {{VALUE}};',
	),
	'strike_typography'                => '{{WRAPPER}} .wpte-elementor-widget .wpte-card:not(.hero-color) .wpte-card__price .striked-price',
	'strike_color'                     => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card:not(.hero-color)' => '--p-fc-s: {{VALUE}};',
	),
	'price_margin'                     => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card:not(.hero-color)' => '--p-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),


	// feat tag
	'feat_typography'                  => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-badge_featured .wpte-badge__text',
	'feat_tag_color'                   => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--f-fc: {{VALUE}};',
	),
	'feat_tag_bg_color'                => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--f-bg: {{VALUE}};',
	),

	// discounttag
	'discount_typography'              => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-badge_discount .wpte-badge__text',
	'discount_tag_color'               => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--d-fc: {{VALUE}};',
	),
	'discount_tag_bg_color'            => array(
		'{{WRAPPER}} .wpte-elementor-widget .wpte-card' => '--d-bg: {{VALUE}};',
	),

	// tab
	'tab_typography'                   => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"]',
	'tab_active_color'                 => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"][aria-selected="true"]' => '--tab-color: {{VALUE}};' ),
	'tab_inactive_color'               => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"][aria-selected="false"]' => '--tab-color: {{VALUE}};' ),
	'tab_active_background'            => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"][aria-selected="true"]' => '--tab-bg: {{VALUE}};' ),
	'tab_inactive_background'          => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"][aria-selected="false"]' => '--tab-bg: {{VALUE}};' ),
	'tab_active_border'                => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"][aria-selected="true"]',
	'tab_inactive_border'              => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"][aria-selected="false"]',
	'tab_border_radius'                => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"]' => '--tab-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'tab_nav_background'               => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav--layout-2' => '--nav-bg: {{VALUE}};' ),
	'tab_nav_border'                   => '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav',
	'tab_nav_padding'                  => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav' => '--nav-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'tab_nav_border_radius'            => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav' => '--nav-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'tab_padding'                      => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav button[role="tab"]' => '--tab-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
	'tab_gap'                          => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-trips-tab__nav' => '--nav-gap: {{ROW}}{{UNIT}} {{COLUMN}}{{UNIT}};' ),

	// rating
	'rating_typography'                => '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__rating',
	'rating_color'                     => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__rating' => '--r-fc: {{VALUE}};' ),
	'rating_margin'                    => array( '{{WRAPPER}} .wpte-elementor-widget .wpte-card .wpte-card__rating' => '--r-m: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),

	// Slider Arrow
	'slider_arrow_padding'             => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'slider_arrow_bg_color'            => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-bg-n: {{VALUE}};',
	),
	'slider_arrow_color'               => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-color-n: {{VALUE}};',
	),
	'slider_arrow_bg_color_hover'      => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-bg-h: {{VALUE}};',
	),
	'slider_arrow_color_hover'         => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-color-h: {{VALUE}};',
	),
	'slider_arrow_border'              => '{{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-prev, {{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-next',
	'slider_arrow_border_radius'       => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'slider_arrow_box_shadow'          => '{{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-prev, {{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-next',
	'slider_arrow_border_hover'        => '{{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-prev:hover, {{WRAPPER}} .wpte-elementor-widget .wpte-swiper-btn-next:hover',
	'slider_arrow_border_radius_hover' => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-radius-h: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	),
	'slider_arrow_size'                => array(
		'{{WRAPPER}} .wpte-elementor-widget ' => '--slider-arrow-size: {{SIZE}}{{UNIT}};',
	),
	'slider_arrow_offset'              => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-arrow-offset: {{SIZE}}{{UNIT}};',
	),

	// slider pagination.
	'slider_dots_active_color'         => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-pagination-active-color: {{VALUE}};',
	),
	'slider_dots_color'                => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-pagination-color: {{VALUE}};',
	),
	'slider_dots_spacing'              => array(
		'{{WRAPPER}} .wpte-elementor-widget' => '--slider-pagination-spacing: {{SIZE}}{{UNIT}};',
	),
);


$controls = array(
	'trips_tab_layout_settings' => array(
		'type'        => 'control_section',
		'label'       => __( 'Layout', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'trips_col_no'   => array(
				'type'           => \Elementor\Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 4,
				'label'          => __( 'Columns', 'wptravelengine-elementor-widgets' ),
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'is_responsive'  => true,
				'selectors'      => array(
					'{{WRAPPER}} .wpte-elementor-widget ' => '--column-no: {{value}};',
				),
				'condition'      => array(
					'enableSlider!' => 'yes',
				),
			),
			'trips_card_gap' => array(
				'label'      => __( 'Gap', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::GAPS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'row'    => '30',
					'column' => '30',
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
				'condition'  => array(
					'enableSlider!' => 'yes',
				),
			),
			'cardlayout'     => array(
				'label'   => __( 'Layouts', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SELECT',
				'options' => array(
					'1' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'2' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
					'3' => __( 'Layout 3', 'wptravelengine-elementor-widgets' ),
					'4' => __( 'Layout 4', 'wptravelengine-elementor-widgets' ),
					'5' => __( 'Layout 5', 'wptravelengine-elementor-widgets' ),
				),
				'default' => '1',
			),
		),
	),
	'sorting_filtering'         => array(
		'type'        => 'control_section',
		'label'       => __( 'Query', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'tripsCount' => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Number of Trips', 'wptravelengine-elementor-widgets' ),
				'default' => 6,
				'min'     => '1',
			),
			'offset'     => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Offset', 'wptravelengine-elementor-widgets' ),
				'default' => 0,
				'min'     => '1',
			),
			'listby'     => array(
				'type'    => 'SELECT',
				'label'   => __( 'Taxonomy', 'wptravelengine-elementor-widgets' ),
				'default' => 'destination',
				'options' => array(
					'destination' => __( 'Destination', 'wptravelengine-elementor-widgets' ),
					'activities'  => __( 'Activities', 'wptravelengine-elementor-widgets' ),
					'trip_types'  => __( 'Trip Types', 'wptravelengine-elementor-widgets' ),
				),
			),
			'listItems'  => array(
				'type'    => 'SELECT',
				'label'   => __( 'List By', 'wptravelengine-elementor-widgets' ),
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'wptravelengine-elementor-widgets' ),
					'byid'    => __( 'Choose from the list', 'wptravelengine-elementor-widgets' ),
				),
			),

		),
	),
	'additional_settings'       => array(
		'type'        => 'control_section',
		'label'       => __( 'Additional', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'enableSlider'       => array(
				'label'   => __( 'Enable Slider', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'showFeaturedRibbon' => array(
				'label'   => __( 'Featured Ribbon', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'showDiscount'       => array(
				'label'   => __( 'Discount', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'showReviews'        => array(
				'label' => __( 'Reviews', 'wptravelengine-elementor-widgets' ),
				'type'  => 'SWITCHER',
			),
			'showLocation'       => array(
				'label'   => __( 'Location', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'showTitle'          => array(
				'label'   => __( 'Title', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'showTripMeta'       => array(
				'label'       => __( 'Trip Meta', 'wptravelengine-elementor-widgets' ),
				'type'        => 'SELECT2',
				'label_block' => true,
				'multiple'    => true,
				'options'     => array(
					'showDuration'   => esc_html__( 'Duration', 'wptravelengine-elementor-widgets' ),
					'showDifficulty' => esc_html__( 'Difficulty', 'wptravelengine-elementor-widgets' ),
					'showActivities' => esc_html__( 'Activity', 'wptravelengine-elementor-widgets' ),
					'showTripType'   => esc_html__( 'Trip Type', 'wptravelengine-elementor-widgets' ),
					'showGroupSize'  => esc_html__( 'Group Size', 'wptravelengine-elementor-widgets' ),
					'showAgeGroup'   => esc_html__( 'Age Group', 'wptravelengine-elementor-widgets' ),
					'showAltitude'   => esc_html__( 'Altitude', 'wptravelengine-elementor-widgets' ),
				),
				'default'     => array( 'showDuration' ),
			),
			'durationType'       => array(
				'type'      => 'SELECT',
				'label'     => __( 'Duration Type', 'wptravelengine-elementor-widgets' ),
				'default'   => 'days',
				'options'   => array(
					'both'   => __( 'Both Days & Nights', 'wptravelengine-elementor-widgets' ),
					'days'   => __( 'Days only', 'wptravelengine-elementor-widgets' ),
					'nights' => __( 'Nights only', 'wptravelengine-elementor-widgets' ),
				),
				'condition' => array( 'showTripMeta' => 'showDuration' ),
			),
			'showPrice'          => array(
				'label'   => __( 'Price', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'showStrikedPrice'   => array(
				'label'     => __( 'Show striked price on sale', 'wptravelengine-elementor-widgets' ),
				'type'      => 'SWITCHER',
				'default'   => 'yes',
				'condition' => array( 'showPrice' => 'yes' ),
			),
			'priceLabel'         => array(
				'default'   => __( 'from', 'wptravelengine-elementor-widgets' ),
				'type'      => 'TEXT',
				'condition' => array(
					'showStrikedPrice' => 'yes',
					'showPrice'        => 'yes',
				),
				'label'     => __( 'Price label', 'wptravelengine-elementor-widgets' ),
			),
			'showWishlist'       => array(
				'label'   => __( 'Wishlist', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
			'showViewMoreButton' => array(
				'label'   => __( 'Show view more button', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SWITCHER',
				'default' => 'yes',
			),
		),
	),

	'slider_settings'           => array(
		'type'        => 'control_section',
		'label'       => __( 'Slider', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'slidesPerViewDesktop' => array(
				'type'           => \Elementor\Controls_Manager::NUMBER,
				'label'          => __( 'Slides Number', 'wptravelengine-elementor-widgets' ),
				'default'        => 3,
				'laptop_default' => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'is_responsive'  => true,
			),
			'spaceBetween'         => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Space Between Slides', 'wptravelengine-elementor-widgets' ),
				'default' => 30,
			),
			'autoplay'             => array(
				'type'    => 'SWITCHER',
				'label'   => __( 'Autoplay', 'wptravelengine-elementor-widgets' ),
				'default' => 'yes',
			),
			'autoplaydelay'        => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Autoplay Speed', 'wptravelengine-elementor-widgets' ),
				'default' => 3000,
			),
			'loop'                 => array(
				'type'    => 'SWITCHER',
				'label'   => __( 'Loop', 'wptravelengine-elementor-widgets' ),
				'default' => 'yes',
			),
			'speed'                => array(
				'type'    => 'NUMBER',
				'label'   => __( 'Transition Speed (ms)', 'wptravelengine-elementor-widgets' ),
				'default' => 300,
			),
			'arrow'                => array(
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label'         => __( 'Slider Arrow', 'wptravelengine-elementor-widgets' ),
				'default'       => 'yes',
				'is_responsive' => true,
			),
			'pagination'           => array(
				'type'          => \Elementor\Controls_Manager::SWITCHER,
				'label'         => __( 'Slider Pagination', 'wptravelengine-elementor-widgets' ),
				'default'       => 'yes',
				'is_responsive' => true,
			),
		),
		'condition'   => array(
			'enableSlider' => 'yes',
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
	'tabs_section'              => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Tabs', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'tab_layout'            => array(
				'label'   => __( 'Layouts', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SELECT',
				'options' => array(
					'1' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'2' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
					'3' => __( 'Layout 3', 'wptravelengine-elementor-widgets' ),
					'4' => __( 'Layout 4', 'wptravelengine-elementor-widgets' ),
				),
				'default' => '1',
			),
			'tab_gap'               => array(
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
			'tab_orientation'       => array(
				'type'    => 'SELECT',
				'label'   => esc_html__( 'Orientation', 'wptravelengine-elementor-widgets' ),
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', 'wptravelengine-elementor-widgets' ),
					'vertical'   => esc_html__( 'Vertical', 'wptravelengine-elementor-widgets' ),
				),
				'default' => 'horizontal',
			),
			'tab_typography'        => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['tab_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'tab_nav_background'    => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Nav Background Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['tab_nav_background'],
				'condition' => array( 'tab_layout' => '2' ),
			),
			'tab_nav_padding'       => array(
				'label'      => esc_html__( 'Nav Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['tab_nav_padding'],
				'condition'  => array( 'tab_layout' => '2' ),
			),
			'tab_nav_border'        => array(
				'type'      => \Elementor\Group_Control_Border::get_type(),
				'selector'  => $selectors['tab_nav_border'],
				'condition' => array( 'tab_layout' => '2' ),
			),
			'tab_nav_border_radius' => array(
				'type'       => 'DIMENSIONS',
				'label'      => __( 'Nav Border Radius', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => $selectors['tab_nav_border_radius'],
				'condition'  => array( 'tab_layout' => '2' ),
			),
			'tab_padding'           => array(
				'label'      => esc_html__( 'Tab Padding', 'wptravelengine-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['tab_padding'],
				'condition'  => array( 'tab_layout!' => '1' ),
			),
			'tab_border_radius'     => array(
				'type'       => 'DIMENSIONS',
				'label'      => __( 'Tab Border Radius', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'selectors'  => $selectors['tab_border_radius'],
				'condition'  => array( 'tab_layout!' => '1' ),
			),
			'tab_color'             => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'tab_active'   => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Active', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'tab_active_border' => array(
								'type'      => \Elementor\Group_Control_Border::get_type(),
								'selector'  => $selectors['tab_active_border'],
								'condition' => array( 'tab_layout!' => '1' ),
							),
							'tab_active_color'  => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => __( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['tab_active_color'],
							),
							'tab_active_bg'     => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['tab_active_background'],
								'condition' => array( 'tab_layout!' => '1' ),
							),
						),
					),
					'tab_inactive' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Inactive', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'tab_inactive_border' => array(
								'type'      => \Elementor\Group_Control_Border::get_type(),
								'selector'  => $selectors['tab_inactive_border'],
								'condition' => array( 'tab_layout!' => '1' ),
							),
							'tab_inactive_color'  => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => __( 'Tab Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['tab_inactive_color'],
							),
							'tab_inactive_bg'     => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Tab Background Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['tab_inactive_background'],
								'condition' => array( 'tab_layout!' => '1' ),
							),
						),
					),
				),
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
				'condition' => array( 'cardlayout!' => '5' ),
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
									'initial' => esc_html__( 'initial', 'wptravelengine-elementor-widgets' ),
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
							'image_boxshadow'     => array(
								'type'     => \Elementor\Group_Control_Box_Shadow::get_type(),
								'label'    => __( 'Box Shadow', 'wptravelengine-elementor-widgets' ),
								'selector' => $selectors['image_boxshadow'],
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
	'location_section'          => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Location', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'loc_position'   => array(
				'type'      => 'SELECT',
				'label'     => esc_html__( 'Position', 'wptravelengine-elementor-widgets' ),
				'options'   => array(
					'top'    => esc_html__( 'Above Title', 'wptravelengine-elementor-widgets' ),
					'bottom' => esc_html__( 'Below Title', 'wptravelengine-elementor-widgets' ),
				),
				'default'   => 'top',
				'condition' => array( 'cardlayout!' => '5' ),
			),
			'loc_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['location_typography'],
			),
			'loc_icon_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['location_icon_color'],
			),
			'loc_icon_size'  => array(
				'type'       => 'SLIDER',
				'label'      => esc_html__( 'Icon Size', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => $selectors['location_icon_size'],
			),
			'loc_margin'     => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['location_margin'],
			),
			'loc_tabs'       => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'loc_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'loc_color' => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Text Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['location_color'],
							),
						),
					),
					'loc_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Hover', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'loc_hover_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Text Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['location_hover_color'],
							),
							'loc_hover_decoration' => array(
								'type'      => 'SELECT',
								'label'     => esc_html__( 'Link Text Decoration', 'wptravelengine-elementor-widgets' ),
								'options'   => array(
									'default'      => esc_html__( 'Default', 'wptravelengine-elementor-widgets' ),
									'underline'    => esc_html__( 'Underline', 'wptravelengine-elementor-widgets' ),
									'overline'     => esc_html__( 'Overline', 'wptravelengine-elementor-widgets' ),
									'line-through' => esc_html__( 'Line Through', 'wptravelengine-elementor-widgets' ),
									'none'         => esc_html__( 'None', 'wptravelengine-elementor-widgets' ),
								),
								'default'   => 'default',
								'selectors' => $selectors['location_hover_decoration'],
							),
						),
					),
				),
			),
		),
	),
	'meta_section'              => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Meta', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'meta_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['meta_typography'],
			),
			'meta_color'      => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Text Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['meta_color'],
			),
			'meta_icon_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Icon Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['meta_icon_color'],
			),
			'meta_icon_size'  => array(
				'type'       => 'SLIDER',
				'label'      => esc_html__( 'Icon Size', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => $selectors['meta_icon_size'],
			),
			'meta_spacing'    => array(
				'type'       => 'SLIDER',
				'label'      => esc_html__( 'Space Between', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => $selectors['meta_spacing'],
			),
			'meta_margin'     => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['meta_margin'],
			),
		),
	),
	'price_section'             => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Price', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showPrice' => 'yes' ),
		'subcontrols' => array(
			'priceType'      => array(
				'type'      => 'SELECT',
				'label'     => __( 'Layouts', 'wptravelengine-elementor-widgets' ),
				'default'   => '3',
				'options'   => array(
					'1' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'2' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
					'3' => __( 'Layout 3', 'wptravelengine-elementor-widgets' ),
				),
				'condition' => array( 'cardlayout!' => '3' ),
			),
			'price_bg_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['price_bg_color'],
				'condition' => array( 'priceType' => '1' ),
			),
			'price_tabs'     => array(
				'type' => 'start_controls_tabs',
				'tabs' => array(
					'price_normal' => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Normal', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'price_typography' => array(
								'type'     => \Elementor\Group_Control_Typography::get_type(),
								'selector' => $selectors['price_typography'],
							),
							'price_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['price_color'],
							),
						),
					),
					'price_hover'  => array(
						'type'        => 'start_controls_tab',
						'label'       => __( 'Strikeout', 'wptravelengine-elementor-widgets' ),
						'subcontrols' => array(
							'strike_typography' => array(
								'type'     => \Elementor\Group_Control_Typography::get_type(),
								'selector' => $selectors['strike_typography'],
							),
							'strike_color'      => array(
								'type'      => \Elementor\Controls_Manager::COLOR,
								'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
								'selectors' => $selectors['strike_color'],
							),
						),
					),
				),
			),
		),
	),
	'featured_section'          => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Featured Tag', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showFeaturedRibbon' => 'yes' ),
		'subcontrols' => array(
			'ribbonTypography'  => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['feat_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'ribbonType'        => array(
				'type'    => 'SELECT',
				'label'   => __( 'Layout', 'wptravelengine-elementor-widgets' ),
				'default' => '3',
				'options' => array(
					'3' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'4' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
					'5' => __( 'Layout 3', 'wptravelengine-elementor-widgets' ),
				),
			),
			'ribbonAlignment'   => array(
				'type'    => 'CHOOSE',
				'label'   => __( 'Alignment', 'wptravelengine-elementor-widgets' ),
				'default' => 'center',
				'options' => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-flex eicon-align-start-h',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-flex eicon-align-center-h',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-flex eicon-align-end-h',
					),
				),
			),
			'feat_tag_color'    => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['feat_tag_color'],
			),
			'feat_tag_bg_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['feat_tag_bg_color'],
			),
		),
	),
	'discount_section'          => array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Discount Tag', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showDiscount' => 'yes' ),
		'subcontrols' => array(
			'discountTypography'    => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['discount_typography'],
				'label'    => __( 'Typography', 'wptravelengine-elementor-widgets' ),
			),
			'discountType'          => array(
				'type'    => 'SELECT',
				'label'   => __( 'Layout', 'wptravelengine-elementor-widgets' ),
				'default' => '1',
				'options' => array(
					'1' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'2' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
				),
			),
			'discountAlignment'     => array(
				'type'    => 'CHOOSE',
				'label'   => __( 'Alignment', 'wptravelengine-elementor-widgets' ),
				'default' => 'left',
				'options' => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-flex eicon-align-start-h',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'wptravelengine-elementor-widgets' ),
						'icon'  => 'eicon-flex eicon-align-end-h',
					),
				),
			),
			'discount_tag_color'    => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['discount_tag_color'],
			),
			'discount_tag_bg_color' => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Background Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['discount_tag_bg_color'],
			),
		),
	),
	'rating_section'            => ! class_exists( 'Wte_Trip_Review_Init' ) ? array() : array(
		'type'        => \Elementor\Controls_Manager::TAB_STYLE,
		'label'       => __( 'Rating', 'wptravelengine-elementor-widgets' ),
		'condition'   => array( 'showReviews' => 'yes' ),
		'subcontrols' => array(
			'rating_layout'     => array(
				'label'   => __( 'Layouts', 'wptravelengine-elementor-widgets' ),
				'type'    => 'SELECT',
				'options' => array(
					'1' => __( 'Layout 1', 'wptravelengine-elementor-widgets' ),
					'2' => __( 'Layout 2', 'wptravelengine-elementor-widgets' ),
					'3' => __( 'Layout 3', 'wptravelengine-elementor-widgets' ),
				),
				'default' => '1',
			),
			'rating_position'   => array(
				'type'      => 'SELECT',
				'label'     => esc_html__( 'Position', 'wptravelengine-elementor-widgets' ),
				'options'   => array(
					'top'    => esc_html__( 'On Image', 'wptravelengine-elementor-widgets' ),
					'bottom' => esc_html__( 'On Content', 'wptravelengine-elementor-widgets' ),
				),
				'default'   => 'top',
				'condition' => array( 'cardlayout!' => '3' ),
			),
			'rating_typography' => array(
				'type'     => \Elementor\Group_Control_Typography::get_type(),
				'selector' => $selectors['rating_typography'],
			),
			'rating_color'      => array(
				'type'      => \Elementor\Controls_Manager::COLOR,
				'label'     => esc_html__( 'Color', 'wptravelengine-elementor-widgets' ),
				'selectors' => $selectors['rating_color'],
			),
			'rating_margin'     => array(
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'label'      => __( 'Margin', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => $selectors['rating_margin'],
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
			'slider_arrow_offset'      => array(
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'label'      => esc_html__( 'Offset', 'wptravelengine-elementor-widgets' ),
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => -80,
						'max'  => 80,
						'step' => 1,
					),
					'%'  => array(
						'min' => -15,
						'max' => 100,
					),
				),
				'selectors'  => $selectors['slider_arrow_offset'],
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
		'condition'   => array(
			'enableSlider' => 'yes',
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
			'listby'    => $filter_name,
			'listItems' => 'byid',
		),
		'multiple'      => true,
	);
}

foreach ( $terms_display_settings as $term => $value ) {
	$controls['sorting_filtering']['subcontrols'][ $term ] = $value;
}

return $controls;
