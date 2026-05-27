<?php
/**
 * File Downloader Widget Controls.
 *
 * @since 1.3.9
 * @package wptravelengine-elementor-widgets
 */

$controls = array(
	'rating' => array(
		'type'        => 'control_section',
		'label'       => __( 'General', 'wptravelengine-elementor-widgets' ),
		'subcontrols' => array(
			'shortcode'       => array(
				'label'       => __( 'Shortcode', 'wptravelengine-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter shortcode here', 'wptravelengine-elementor-widgets' ),
				'default'     => '[trip_file_downloads]',
			),
			'hideTitle'       => array(
				'label'        => __( 'Hide Header Text', 'wptravelengine-elementor-widgets' ),
				'type'         => 'SWITCHER',
				'default'      => 'yes',
				'return_value' => 'none',
				'selectors'    => array(
					'{{WRAPPER}} .elementor-widget-container .file-downloadable-wrap .file.downloadable-header' => 'display: {{VALUE}}',
				),
			),
			'hideDescription' => array(
				'label'        => __( 'Hide Header Description', 'wptravelengine-elementor-widgets' ),
				'type'         => 'SWITCHER',
				'default'      => 'yes',
				'return_value' => 'none',
				'selectors'    => array(
					'{{WRAPPER}} .elementor-widget-container .file-downloadable-wrap .file.downloadable-description' => 'display: {{VALUE}}',
				),
			),
		),
	),
);

return $controls;
