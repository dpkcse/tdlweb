<?php
/**
 * Checkout.
 *
 * @since 6.2.0
 */

$checkout_fields = array(
	array(
		'label'       => __( 'Booking Confirmation Message', 'wp-travel-engine' ),
		'description' => '',
		'field_type'  => 'TEXTAREA',
		'default'     => 'Thank you for booking the trip. Please check your email for confirmation. Below is your booking detail:',
		'name'        => 'booking_confirmation_msg',
		'divider'     => true,
	),
	array(
		'label'       => __( 'Show Traveller Information Form', 'wp-travel-engine' ),
		'description' => __( 'When enabled, providing information for all travellers will be mandatory.', 'wp-travel-engine' ),
		'field_type'  => 'SWITCH',
		'name'        => 'display_travellers_info',
		'divider'     => true,
	),
	array(
		'label'       => __( 'Show Emergency Contact Form', 'wp-travel-engine' ),
		'description' => __( 'Enable this option to include a section for emergency contact details in the Traveller Information form.', 'wp-travel-engine' ),
		'field_type'  => 'SWITCH',
		'name'        => 'display_emergency_contact',
		'condition'   => 'display_travellers_info === true',
		'divider'     => true,
	),
	array(
		'label'      => __( 'Display Traveller and Emergency Details', 'wp-travel-engine' ),
		'field_type' => 'SELECT_BUTTON',
		'name'       => 'traveller_emergency_details_form',
		'condition'  => 'display_travellers_info === true',
		'options'    => array(
			array(
				'label' => __( 'On Checkout', 'wp-travel-engine' ),
				'value' => 'on_checkout',
			),
			array(
				'label' => __( 'After Checkout', 'wp-travel-engine' ),
				'value' => 'after_checkout',
			),
		),
		'divider'    => true,
	),
	array(
		'label'       => __( 'Collect Traveller Information', 'wp-travel-engine' ),
		'description' => __( 'Choose whether to collect information for all travellers or only the main traveller during checkout.', 'wp-travel-engine' ),
		'field_type'  => 'SELECT_BUTTON',
		'name'        => 'travellers_details_type',
		'condition'   => 'display_travellers_info === true && traveller_emergency_details_form === on_checkout',
		'options'     => array(
			array(
				'label' => __( 'All Travellers', 'wp-travel-engine' ),
				'value' => 'all',
			),
			array(
				'label' => __( 'Only Lead Traveller', 'wp-travel-engine' ),
				'value' => 'only_lead',
			),
		),
		'divider'     => true,
	),
	array(
		'label'       => __( 'Show Additional Note', 'wp-travel-engine' ),
		'description' => __( 'Enable this option to display an additional notes section on the checkout page.', 'wp-travel-engine' ),
		'field_type'  => 'SWITCH',
		'name'        => 'show_additional_note',
		'divider'     => true,
	),
	array(
		'label'       => __( 'Show Coupon Code Field', 'wp-travel-engine' ),
		'description' => __( 'Enable this option to allow customers to apply a coupon code during checkout if the trip has one.', 'wp-travel-engine' ),
		'field_type'  => 'SWITCH',
		'name'        => 'show_discount',
		'divider'     => true,
	),
	array(
		'label'       => __( 'Privacy Policy Notice', 'wp-travel-engine' ),
		'description' => __( 'Enter the privacy policy message to display on the checkout page.', 'wp-travel-engine' ),
		'field_type'  => 'TEXTAREA',
		'name'        => 'privacy_policy_msg',
		'divider'     => true,
	),
	array(
		'label'       => __( 'Footer Copyright', 'wp-travel-engine' ),
		'description' => __( 'Enter the copyright text to display in the footer. Available tags: %1$current_year%, %2$site_name%.', 'wp-travel-engine' ),
		'field_type'  => 'TEXTAREA',
		'name'        => 'footer_copyright',
	),
);

return apply_filters(
	'checkout_settings',
	array(
		'title'  => __( 'Checkout', 'wp-travel-engine' ),
		'order'  => 20,
		'id'     => 'checkout_settings',
		'fields' => $checkout_fields,
	)
);
