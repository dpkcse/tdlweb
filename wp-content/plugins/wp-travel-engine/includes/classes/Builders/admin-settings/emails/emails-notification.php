<?php
/**
 * Admin Email Notification Settings.
 *
 * @since 6.5.0
 */

use WPTravelEngine\Core\Models\Settings\Options;
use WPTravelEngine\Helpers\Translators;


$email_tags = wptravelengine_all_email_tags();

return apply_filters(
	'emails-notification',
	array(
		'title'    => __( 'Notifications', 'wp-travel-engine' ),
		'order'    => 1,
		'id'       => 'emails_notification',
		'doc_link' => 'https://docs.wptravelengine.com/article/email-personalization-tags/',
		'icon'     => 'email',
		'fields'   => apply_filters(
			'emails-notification-fields',
			array(
				array(
					'field_type' => 'EMAILS_NOTIFICATION',
					'name'       => 'email_notification',
					'data'       => array(
						'all_email_tags'               => $email_tags['all'],
						'customer_email_tags'          => $email_tags['customer'],
						'subject_email_tags'           => $email_tags['subject'],
						'nonce_test_mail'              => wp_create_nonce( 'wptravelengine_test_email_nonce' ),
						'nonce_email_template_preview' => wp_create_nonce( 'wptravelengine_email_template_preview' ),
						'nonce_update_template'        => wp_create_nonce( 'wte_update_email_templates' ),
						'updated_template'             => wptravelengine_toggled( Options::get( 'wte_update_mail_template' ) ),
						'available_languages'          => Translators::get_available_languages( 'translatepress' ),
						'default_language'             => Translators::get_default_language( 'translatepress' ),
						'current_language'             => Translators::get_translatepress_language(),
						'deprecated_email_tags'        => wptravelengine_deprecated_email_tags(),
					),
				),
			)
		),
	),
);
