<?php
/**
 * FAQs Schema.
 *
 * @package WPTravelEngine
 * @since 6.7.11
 */

return array(
	'faqs' => array(
		'description' => __( 'FAQs Settings', 'wp-travel-engine' ),
		'type'        => 'object',
		'properties'  => array(
			'items' => array(
				'description' => __( 'FAQ Items', 'wp-travel-engine' ),
				'type'        => 'array',
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'       => array(
							'description' => __( 'FAQ ID', 'wp-travel-engine' ),
							'type'        => 'string',
						),
						'question' => array(
							'description' => __( 'FAQ Question', 'wp-travel-engine' ),
							'type'        => 'string',
						),
						'answer'   => array(
							'description' => __( 'FAQ Answer', 'wp-travel-engine' ),
							'type'        => 'string',
						),
					),
				),
			),
		),
	),
);
