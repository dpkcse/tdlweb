<?php
/**
 * Email Translation Manager for TranslatePress.
 *
 * @package WPTravelEngine\Email
 * @since 6.7.9
 */

namespace WPTravelEngine\Email\TranslationManager;

use WPTravelEngine\Email\Email;
use WPTravelEngine\Traits\Singleton;
use WPTravelEngine\Email\BookingEmail;
use WPTravelEngine\Helpers\Translators;

/**
 * TranslatePress class.
 *
 * @since 6.7.9
 */
class TranslatePress {

	use Singleton;

	/**
	 * Text domain for string registration.
	 */
	private const TEXT_DOMAIN = 'wp-travel-engine';

	/**
	 * Constructor.
	 * Registers hooks for email translation workflow.
	 *
	 * @since 6.7.9
	 */
	public function __construct() {
		add_filter( 'wptravelengine_email_subject', array( $this, 'translate_subject' ), 10, 2 );
		add_filter( 'wptravelengine_email_content', array( $this, 'translate_body' ), 10, 2 );
	}

	/**
	 * Filter: translate email subject before sending.
	 *
	 * @param string $subject Raw subject with template tags.
	 * @param Email  $email   Email instance.
	 * @return string Translated subject, or original if no translation found.
	 * @since 6.7.9
	 */
	public function translate_subject( string $subject, Email $email ): string {
		$translated = self::find_translation( $subject, $email, 'subject' );
		return $translated ?? $subject;
	}

	/**
	 * Filter: translate email body before sending.
	 *
	 * @param string $body  Full body including header/footer.
	 * @param Email  $email Email instance.
	 * @return string Translated body (with header/footer), or original body unchanged.
	 * @since 6.7.9
	 */
	public function translate_body( string $body, Email $email ): string {
		$content    = $email->get( 'content' );
		$translated = self::find_translation( $content, $email, 'content' );

		if ( $translated === null ) {
			return $body;
		}

		$use_new_header_footer = wptravelengine_toggled( get_option( 'wte_update_mail_template', false ) );

		$translated_body  = '';
		$translated_body .= $use_new_header_footer ? wte_get_template_html( 'template-emails/email-header.php' ) : wte_get_template_html( 'emails/email-header.php' );
		/**
		 * wpautop is used to format mainly for per-trip-emails due to it contain post content.
		 */
		$translated_body .= wpautop( $translated, false );
		$translated_body .= $use_new_header_footer ? wte_get_template_html( 'template-emails/email-footer.php' ) : wte_get_template_html( 'emails/email-footer.php' );

		return $translated_body;
	}

	/**
	 * Filter: prepare email notification settings for GET requests.
	 *
	 * @param array $email_notification Array with 'admin' and 'customer' template lists.
	 * @param mixed $request            REST request object.
	 * @return array Modified email notification array.
	 * @since 6.7.9
	 */
	public static function prepare_email_notification_settings( array $email_notification, $request ): array {
		if ( ! Translators::is_translatepress_active() ) {
			return $email_notification;
		}

		$default_language = Translators::get_default_language( 'translatepress' );
		$language         = $request->get_param( 'email_notification' )['_language'] ?? $request->get_param( 'language' ) ?? $default_language;

		$admin_templates    = $email_notification['admin'] ?? array();
		$customer_templates = $email_notification['customer'] ?? array();

		if ( $language === $default_language ) {
			foreach ( $admin_templates as $template ) {
				if ( isset( $template['id'], $template['subject'], $template['content'] ) ) {
					self::maybe_register_originals( $template['id'], 'admin', $template['subject'], $template['content'] );
				}
			}
			foreach ( $customer_templates as $template ) {
				if ( isset( $template['id'], $template['subject'], $template['content'] ) ) {
					self::maybe_register_originals( $template['id'], 'customer', $template['subject'], $template['content'] );
				}
			}

			return array(
				'admin'    => $admin_templates,
				'customer' => $customer_templates,
			);
		}

		$trp_data = self::init_translatepress_components();
		if ( ! $trp_data ) {
			return $email_notification;
		}

		// Collect every context key across all templates and recipients in one pass.
		$all_contexts = array();
		foreach ( $admin_templates as $template ) {
			if ( isset( $template['id'] ) ) {
				$all_contexts[] = "wte_email_{$template['id']}_admin_subject";
				$all_contexts[] = "wte_email_{$template['id']}_admin_content";
			}
		}
		foreach ( $customer_templates as $template ) {
			if ( isset( $template['id'] ) ) {
				$all_contexts[] = "wte_email_{$template['id']}_customer_subject";
				$all_contexts[] = "wte_email_{$template['id']}_customer_content";
			}
		}

		// Single query for all templates — 1 query instead of 2×N.
		$translations = self::batch_get_translations( $all_contexts, $language, $trp_data );

		$apply = static function ( array $templates, string $recipient ) use ( $translations ): array {
			return array_map(
				static function ( $template ) use ( $recipient, $translations ) {
					if ( isset( $template['id'] ) ) {
						$subject = $translations[ "wte_email_{$template['id']}_{$recipient}_subject" ] ?? null;
						$content = $translations[ "wte_email_{$template['id']}_{$recipient}_content" ] ?? null;
						if ( $subject && $content ) {
							$template['subject'] = $subject;
							$template['content'] = $content;
						}
					}
					return $template;
				},
				$templates
			);
		};

		return array(
			'admin'     => $apply( $admin_templates, 'admin' ),
			'customer'  => $apply( $customer_templates, 'customer' ),
			'_language' => $language,
		);
	}

	/**
	 * Skip email notification save in option key.
	 *
	 * @param mixed $request REST request object.
	 * @return bool True to skip email notification save, false to proceed normally.
	 * @since 6.7.9
	 */
	public static function skip_email_notification_save( $request ): bool {
		if ( ! Translators::is_translatepress_active() ) {
			return false;
		}

		$email_notification = $request->get_param( 'email_notification' );
		if ( ! is_array( $email_notification ) ) {
			return false;
		}

		$default_language = Translators::get_default_language( 'translatepress' );
		$language         = $email_notification['_language'] ?? $default_language;

		$template_groups = array(
			'admin'    => $email_notification['admin'] ?? array(),
			'customer' => $email_notification['customer'] ?? array(),
		);

		if ( $language === $default_language ) {
			foreach ( $template_groups as $recipient => $templates ) {
				if ( ! is_array( $templates ) ) {
					continue;
				}
				foreach ( $templates as $template ) {
					if ( isset( $template['id'], $template['subject'], $template['content'] ) ) {
						self::maybe_register_originals( $template['id'], $recipient, $template['subject'], $template['content'] );
					}
				}
			}
			return false;
		}

		foreach ( $template_groups as $recipient => $templates ) {
			if ( ! is_array( $templates ) ) {
				continue;
			}
			foreach ( $templates as $template ) {
				if ( isset( $template['id'], $template['subject'], $template['content'] ) ) {
					self::update_template_translation( $template['id'], $recipient, $template['subject'], $template['content'], $language );
				}
			}
		}

		return true;
	}

	/**
	 * Sync default-language originals into the TRP originals table.
	 *
	 * @param string $template_id Template ID (e.g. 'booking_confirmation').
	 * @param string $recipient   'admin' or 'customer'.
	 * @param string $subject     Default-language subject string.
	 * @param string $content     Default-language content string.
	 * @param string $domain      Text domain to register strings under (default: 'wp-travel-engine').
	 * @return void
	 * @since 6.7.9
	 */
	public static function maybe_register_originals( string $template_id, string $recipient, string $subject, string $content, string $domain = self::TEXT_DOMAIN ): void {
		if ( ! Translators::is_translatepress_active() ) {
			return;
		}

		$trp_data = self::init_translatepress_components();
		if ( ! $trp_data ) {
			return;
		}

		global $wpdb;

		$originals_table = $trp_data['originals_table'];

		foreach (
			array(
				"wte_email_{$template_id}_{$recipient}_subject" => $subject,
				"wte_email_{$template_id}_{$recipient}_content" => $content,
			) as $context => $string
		) {
			if ( empty( trim( $string ) ) ) {
				continue;
			}

			$existing_row = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT id, original FROM %i WHERE context = %s AND domain = %s',
					$originals_table,
					$context,
					$domain
				)
			);

			if ( ! $existing_row ) {
				$wpdb->insert(
					$originals_table,
					array(
						'original' => $string,
						'domain'   => $domain,
						'context'  => $context,
					),
					array( '%s', '%s', '%s' )
				);
			} elseif ( $existing_row->original !== $string ) {
				$wpdb->update(
					$originals_table,
					array( 'original' => $string ),
					array( 'id' => (int) $existing_row->id ),
					array( '%s' ),
					array( '%d' )
				);
			}
		}
	}

	/**
	 * Save (or update) a translated email template in TranslatePress.
	 *
	 * @param string $template_id Template ID.
	 * @param string $recipient   'admin' or 'customer'.
	 * @param string $subject     Translated subject string.
	 * @param string $content     Translated content HTML.
	 * @param string $language    Target language code (e.g. 'es_ES').
	 * @param string $domain      Text domain to register strings under (default: 'wp-travel-engine').
	 * @return bool True on success, false if TRP is not available.
	 * @since 6.7.9
	 */
	public static function update_template_translation( string $template_id, string $recipient, string $subject, string $content, string $language, string $domain = self::TEXT_DOMAIN ): bool {
		if ( ! Translators::is_translatepress_active() || empty( $language ) ) {
			return false;
		}

		$trp_data = self::init_translatepress_components();
		if ( ! $trp_data ) {
			return false;
		}

		$translation_table = $trp_data['query']->get_gettext_table_name( $language );

		self::update_single_translation( $subject, "wte_email_{$template_id}_{$recipient}_subject", $domain, $translation_table, $trp_data );
		self::update_single_translation( $content, "wte_email_{$template_id}_{$recipient}_content", $domain, $translation_table, $trp_data );

		return true;
	}

	/**
	 * Retrieve a translated email template from TranslatePress.
	 *
	 * @param string $template_id Template ID.
	 * @param string $recipient   'admin' or 'customer'.
	 * @param string $language    Target language code.
	 * @return array{subject:string,content:string}|null Translated pair, or null.
	 * @since 6.7.9
	 */
	public static function get_translated_template( string $template_id, string $recipient, string $language ): ?array {
		if ( ! Translators::is_translatepress_active() ) {
			return null;
		}

		$trp_data = self::init_translatepress_components();
		if ( ! $trp_data ) {
			return null;
		}

		if ( $language === $trp_data['default_language'] ) {
			return null;
		}

		$translation_table = $trp_data['query']->get_gettext_table_name( $language );
		$originals_table   = $trp_data['originals_table'];

		if ( ! self::is_valid_table_name( $translation_table ) ) {
			return null;
		}

		if ( ! self::table_exists( $translation_table ) ) {
			return null;
		}

		global $wpdb;

		$subject_context = "wte_email_{$template_id}_{$recipient}_subject";
		$content_context = "wte_email_{$template_id}_{$recipient}_content";

		$subject = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT t.translated
			 FROM %i t
			 INNER JOIN %i o ON t.original_id = o.id
			 WHERE o.context = %s AND t.status > 0',
				$translation_table,
				$originals_table,
				$subject_context
			)
		);

		$content = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT t.translated
			 FROM %i t
			 INNER JOIN %i o ON t.original_id = o.id
			 WHERE o.context = %s AND t.status > 0',
				$translation_table,
				$originals_table,
				$content_context
			)
		);

		if ( $subject && $content ) {
			return array(
				'subject' => $subject,
				'content' => $content,
			);
		}

		return null;
	}

	/**
	 * Determine the target language for an outgoing email.
	 *
	 * @param Email $email Email instance.
	 * @return string|null Language code or null if undetermined.
	 * @since 6.7.9
	 */
	public static function get_language( Email $email ): ?string {
		if ( $email instanceof BookingEmail && $email->booking && isset( $email->booking->ID ) ) {
			$saved = get_post_meta( $email->booking->ID, 'wp_travel_engine_booking_language', true );
			if ( ! empty( $saved ) ) {
				return $saved;
			}
		}

		return Translators::get_translatepress_language() ?: null;
	}

	/**
	 * Find a translation for a content string using a priority chain:
	 *
	 * @param string $content Raw content / subject (no header/footer).
	 * @param Email  $email   Email instance (used for language + template id).
	 * @param string $type    'subject' or 'content'.
	 * @return string|null Translated string or null if not found.
	 * @since 6.7.9
	 */
	private static function find_translation( string $content, Email $email, string $type ): ?string {
		if ( ! Translators::is_translatepress_active() ) {
			return null;
		}

		if ( empty( trim( $content ) ) ) {
			return null;
		}

		$language = self::get_language( $email );
		if ( ! $language ) {
			return null;
		}

		$trp_data = self::init_translatepress_components();
		if ( ! $trp_data ) {
			return null;
		}

		$template_id = $email->template ?? null;
		if ( $template_id ) {
			$recipient = $email->sendto ?? null;
			$context   = apply_filters(
				'wptravelengine_email_translation_context',
				$recipient
				? "wte_email_{$template_id}_{$recipient}_{$type}"
				: "wte_email_{$template_id}_{$type}",
				$email,
				$type
			);

			$translated = self::lookup_translation_by_context( $context, $language, $trp_data );
			if ( $translated !== null ) {
				return $translated;
			}
		}

		return self::lookup_translation_by_original( $content, $language, $trp_data );
	}

	/**
	 * Fetch multiple translations in a single query.
	 *
	 * @param string[] $contexts  Context keys to look up.
	 * @param string   $language  Target language code.
	 * @param array    $trp_data  Initialised TRP components.
	 * @return array<string,string> Map of context => translated string.
	 * @since 6.7.9
	 */
	private static function batch_get_translations( array $contexts, string $language, array $trp_data ): array {
		if ( empty( $contexts ) || $language === $trp_data['default_language'] ) {
			return array();
		}

		$translation_table = $trp_data['query']->get_gettext_table_name( $language );
		$originals_table   = $trp_data['originals_table'];

		if ( ! self::is_valid_table_name( $translation_table ) || ! self::table_exists( $translation_table ) ) {
			return array();
		}

		global $wpdb;

		$placeholders = implode( ', ', array_fill( 0, count( $contexts ), '%s' ) );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT o.context, t.translated
				 FROM %i t
				 INNER JOIN %i o ON t.original_id = o.id
				 WHERE o.context IN ($placeholders) AND t.status > 0 AND t.translated != ''",
				array_merge( array( $translation_table, $originals_table ), $contexts )
			)
		);

		$map = array();
		foreach ( $rows as $row ) {
			$map[ $row->context ] = $row->translated;
		}

		return $map;
	}

	/**
	 * Query a translation by context key.
	 *
	 * @param string $context  Context key.
	 * @param string $language Target language code.
	 * @param array  $trp_data Initialised TRP components.
	 * @return string|null Translated string or null.
	 * @since 6.7.9
	 */
	private static function lookup_translation_by_context( string $context, string $language, array $trp_data ): ?string {
		global $wpdb;

		if ( $language === $trp_data['default_language'] ) {
			return null;
		}

		$translation_table = $trp_data['query']->get_gettext_table_name( $language );
		$originals_table   = $trp_data['originals_table'];

		if ( ! self::is_valid_table_name( $translation_table ) ) {
			return null;
		}

		if ( ! self::table_exists( $translation_table ) ) {
			return null;
		}

		$translated = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT t.translated
			 FROM %i t
			 INNER JOIN %i o ON t.original_id = o.id
			 WHERE o.context = %s AND t.status > 0 AND t.translated != ''",
				$translation_table,
				$originals_table,
				$context
			)
		);

		return $translated ?: null;
	}

	/**
	 * Query a translation by original string (generic fallback).
	 *
	 * @param string $original The raw original string.
	 * @param string $language Target language code.
	 * @param array  $trp_data Initialised TRP components.
	 * @return string|null Translated string or null.
	 * @since 6.7.9
	 */
	private static function lookup_translation_by_original( string $original, string $language, array $trp_data ): ?string {
		global $wpdb;

		if ( $language === $trp_data['default_language'] ) {
			return null;
		}

		$translation_table = $trp_data['query']->get_gettext_table_name( $language );
		$originals_table   = $trp_data['originals_table'];

		if ( ! self::is_valid_table_name( $translation_table ) ) {
			return null;
		}

		if ( ! self::table_exists( $translation_table ) ) {
			return null;
		}

		$translated = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT t.translated
			 FROM %i t
			 INNER JOIN %i o ON t.original_id = o.id
			 WHERE o.original = %s AND t.status > 0 AND t.translated != ''",
				$translation_table,
				$originals_table,
				$original
			)
		);

		return $translated ?: null;
	}

	/**
	 * Update or insert a single string translation in TranslatePress.
	 *
	 * @param string $translated        Translated string.
	 * @param string $context           Context key.
	 * @param string $domain            Text domain.
	 * @param string $translation_table Translation table name.
	 * @param array  $trp_data          Initialised TRP components.
	 * @return void
	 * @since 6.7.9
	 */
	private static function update_single_translation( string $translated, string $context, string $domain, string $translation_table, array $trp_data ): void {
		global $wpdb;

		if ( ! self::is_valid_table_name( $translation_table ) ) {
			return;
		}

		$originals_table = $trp_data['originals_table'];

		$original_row = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT id, original FROM %i WHERE context = %s AND domain = %s',
				$originals_table,
				$context,
				$domain
			)
		);

		if ( ! $original_row ) {
			return;
		}

		$original_id   = (int) $original_row->id;
		$original_text = $original_row->original;

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT id FROM %i WHERE original_id = %d AND domain = %s',
				$translation_table,
				$original_id,
				$domain
			)
		);

		if ( $existing ) {
			$wpdb->update(
				$translation_table,
				array(
					'translated' => $translated,
					'status'     => 2,
				),
				array(
					'original_id' => $original_id,
					'domain'      => $domain,
				),
				array( '%s', '%d' ),
				array( '%d', '%s' )
			);
		} else {
			$wpdb->insert(
				$translation_table,
				array(
					'original'    => $original_text,
					'translated'  => $translated,
					'domain'      => $domain,
					'status'      => 2,
					'original_id' => $original_id,
					'plural_form' => null,
				),
				array( '%s', '%s', '%s', '%d', '%d', '%s' )
			);
		}
	}

	/**
	 * Bootstrap TranslatePress query / settings components.
	 *
	 * @return array|null Keyed component array or null on failure.
	 * @since 6.7.9
	 */
	private static function init_translatepress_components(): ?array {
		// Separate flag prevents conflating "not yet run" (null) with "ran and failed" (false).
		static $initialized = false;
		static $cache       = null;

		if ( $initialized ) {
			return $cache;
		}

		$initialized = true;

		$trp = \TRP_Translate_Press::get_trp_instance();
		if ( ! $trp ) {
			return null;
		}

		$trp_query             = $trp->get_component( 'query' );
		$settings              = $trp->get_component( 'settings' )->get_settings();
		$gettext_insert_update = $trp_query->get_query_component( 'gettext_insert_update' );

		if ( ! $gettext_insert_update ) {
			return null;
		}

		$originals_table = $trp_query->get_table_name_for_gettext_original_strings();

		if ( ! self::is_valid_table_name( $originals_table ) ) {
			wte_log( 'WTE Email Translation: Invalid table name from TranslatePress: ' . $originals_table );
			return null;
		}

		$cache = array(
			'query'            => $trp_query,
			'settings'         => $settings,
			'insert_update'    => $gettext_insert_update,
			'default_language' => $settings['default-language'],
			'languages'        => $settings['translation-languages'] ?? array(),
			'originals_table'  => $originals_table,
		);

		return $cache;
	}

	/**
	 * Check whether a translation table exists in the database, caching the result per request.
	 *
	 * @param string $table_name Fully-qualified table name.
	 * @return bool
	 * @since 6.7.9
	 */
	private static function table_exists( string $table_name ): bool {
		static $cache = array();
		if ( ! isset( $cache[ $table_name ] ) ) {
			global $wpdb;
			$cache[ $table_name ] = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) === $table_name;
		}
		return $cache[ $table_name ];
	}

	/**
	 * Get a single translated content string for a template (no subject required).
	 *
	 * @param string $template_id Template ID.
	 * @param string $recipient   'admin' or 'customer'.
	 * @param string $language    Target language code.
	 * @return string|null Translated content or null if not found / default language.
	 * @since 6.7.9
	 */
	public static function get_translated_content_only( string $template_id, string $recipient, string $language ): ?string {
		if ( ! Translators::is_translatepress_active() ) {
			return null;
		}

		$trp_data = self::init_translatepress_components();
		if ( ! $trp_data || $language === $trp_data['default_language'] ) {
			return null;
		}

		$context = "wte_email_{$template_id}_{$recipient}_content";
		return self::lookup_translation_by_context( $context, $language, $trp_data );
	}

	/**
	 * Validate a database table name against SQL-injection risks.
	 *
	 * @param string $table_name Table name to validate.
	 * @return bool True if safe.
	 * @since 6.7.9
	 */
	private static function is_valid_table_name( string $table_name ): bool {
		global $wpdb;

		if ( strpos( $table_name, $wpdb->prefix ) !== 0 ) {
			return false;
		}

		$suffix = substr( $table_name, strlen( $wpdb->prefix ) );
		return (bool) preg_match( '/^[a-zA-Z0-9_]+$/', $suffix );
	}
}
