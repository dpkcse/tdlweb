<?php
/**
 * Performance General Tab Settings.
 *
 * @since 6.2.0
 * @since 6.7.8 Remove Lazy Loading settings.
 */

return apply_filters(
	'performance_general_settings',
	array(
		'title'  => __( 'General', 'wp-travel-engine' ),
		'order'  => 5,
		'id'     => 'performance-general',
		'fields' => array(
			array(
				'divider'    => true,
				'label'      => __( 'Optimized Loading<span class="beta-tag"></span>', 'wp-travel-engine' ),
				'help'       => __( 'This feature adds conditional loading of the assets to improve the loading speed.', 'wp-travel-engine' ),
				'field_type' => 'SWITCH',
				'name'       => 'enable_optimized_loading',
				'isBeta'     => true,
			),
		),
	)
);
