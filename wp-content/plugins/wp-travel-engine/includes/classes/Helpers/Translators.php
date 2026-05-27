<?php
/**
 * Translation helper class.
 *
 * @since 6.5.1
 * @package WPTravelEngine
 */

namespace WPTravelEngine\Helpers;

class Translators {

	public function __construct() {
		add_filter(
			'wpml_pre_save_pro_translation',
			function ( $postarr, $job ) {

				if ( WP_TRAVEL_ENGINE_POST_TYPE !== ( $postarr['post_type'] ?? false ) ) {
					return $postarr;
				}

				return static::wpml_pre_save_pro_translation( $postarr, $job );
			},
			10,
			2
		);

		/**
		 * This filter is used to modify the attributes and extradata of the translation units for WPML Advanced Translation Editor.
		 *
		 * @since 6.6.9
		 */
		add_filter( 'wpml_tm_adjust_translation_job', array( __CLASS__, 'wpml_ate_translation_adjust' ) );

		// Set the language after booking created if TranslatePress is active.
		add_action( 'wptravelengine_after_booking_created', array( __CLASS__, 'set_language_after_booking_created' ) );
	}

	/**
	 * Set the language after booking created.
	 *
	 * @param int $booking_id The booking ID.
	 * @return void
	 * @since 6.7.9
	 */
	public static function set_language_after_booking_created( int $booking_id ) {
		$language = static::get_translatepress_language();
		if ( $language ) {
			update_post_meta( $booking_id, 'wp_travel_engine_booking_language', $language );
		}
	}

	public static function wpml_pre_save_pro_translation( $postarr, $job ) {

		$original_trip_id = $job->original_doc_id;
		$post_metas       = get_post_meta( $original_trip_id );

		$meta_input = $postarr['meta_input'] ?? array();
		foreach ( $post_metas as $key => $value ) {
			if ( preg_match( '#^_wpml_#', $key ) ) {
				continue;
			}

			$meta_input[ $key ] = maybe_unserialize( $value[0] );
		}

		$postarr['meta_input'] = $meta_input;

		return $postarr;
	}

	/**
	 * @return array[]|false|mixed|null
	 */
	public static function get_current_language() {
		if ( function_exists( 'pll_current_language' ) ) {
			$language = pll_current_language();

			return $language ? array( $language => array() ) : false;
		}

		return apply_filters( 'wpml_active_languages', null, array() );
	}

	/**
	 * Checks if WPML multilingual is active.
	 *
	 * @return bool True if WPML multilingual mode is active, false otherwise.
	 * @since 6.6.6
	 */
	public static function is_wpml_multilingual_active(): bool {
		return defined( 'WPML_ST_VERSION' ) && apply_filters( 'wpml_current_language', null ) !== apply_filters( 'wpml_default_language', null );
	}

	/**
	 * Summary of set_wpml_language.
	 *
	 * @param string|null $lang
	 * @return void
	 * @since 6.6.6
	 */
	public static function set_wpml_language( $lang = null ): void {
		do_action( 'wpml_switch_language', $lang ?? apply_filters( 'wpml_current_language', null ) );
	}

	/**
	 * This function is used to save the translations to the wpml string.
	 *
	 * @param array|string $translations Translations to save.
	 * @param string       $base_context Name of the wpml string.
	 * @since 6.6.6
	 */
	public static function save_wpml_translation( $translations, $base_context ): void {
		if ( ! function_exists( 'icl_update_string_translation' ) ) {
			return;
		}

		$lang = apply_filters( 'wpml_current_language', null );

		if ( is_string( $translations ) ) {
			icl_update_string_translation( $base_context, $lang, $translations, 10 );
			return;
		}

		foreach ( $translations as $key => $translation ) {
			if ( is_array( $translation ) ) {
				$current_context = $base_context . '[' . $key . ']';
				static::save_wpml_translation( $translation, $current_context );
			} else {
				icl_update_string_translation( $base_context . $key, $lang, $translation, 10 );
			}
		}
	}

	/**
	 * Auto register admin strings.
	 *
	 * @return void
	 * @since 6.6.6
	 */
	public static function register_wpml_admin_strings() {
		if ( function_exists( 'wpml_st_parse_config' ) ) {
			$config_file = WP_TRAVEL_ENGINE_BASE_PATH . '/wpml-config.xml';

			if ( file_exists( $config_file ) ) {
				$config_hash = md5( serialize( $config_file ) );
				delete_transient( 'wpml_admin_text_import:parse_config:' . $config_hash );
				wpml_st_parse_config( $config_file );
			}
		}
	}

	/**
	 * This function is used to modify the attributes and extradata of the translation units for WPML Advanced Translation Editor.
	 *
	 * @since 6.6.9
	 * @param array $translation_units Translation units.
	 * @return array Translation units.
	 */
	public static function wpml_ate_translation_adjust( $translation_units ) {
		foreach ( $translation_units as $key => $value ) {
			if ( ! isset( $value['attributes']['id'] ) || strpos( $value['attributes']['id'], 'field-wp_travel_engine_setting' ) === false ) {
				continue;
			}
			$translation_units[ $key ]['attributes']['resname'] = '';
			$translation_units[ $key ]['extradata']['unit']     = '';
		}
		return $translation_units;
	}

	/**
	 * Checks if TranslatePress is active.
	 *
	 * @return bool True if TranslatePress is active, false otherwise.
	 * @since 6.7.9
	 */
	public static function is_translatepress_active(): bool {
		return function_exists( 'trp_translate' ) && class_exists( 'TRP_Translate_Press' );
	}

	/**
	 * Get available languages for translation with their display labels.
	 * Currently supports TranslatePress. Can be extended for other plugins via filter.
	 *
	 * @return array List of ['code' => string, 'label' => string] pairs.
	 * @since 6.7.9
	 */
	public static function get_available_languages( $plugin = 'translatepress' ): array {
		$languages = array();
		if ( 'translatepress' === $plugin && static::is_translatepress_active() ) {
			$trp = \TRP_Translate_Press::get_trp_instance();
			if ( $trp ) {
				$settings  = $trp->get_component( 'settings' )->get_settings();
				$codes     = $settings['translation-languages'] ?? array();
				$trp_langs = $trp->get_component( 'languages' );

				$trp_languages = $trp_langs->get_language_names( $codes );
				foreach ( $trp_languages as $code => $name ) {
					$languages[] = array(
						'code'  => $code,
						'label' => $name,
					);
				}
			}
		}

		return apply_filters( 'wptravelengine_available_languages', $languages );
	}

	/**
	 * Get default language code.
	 * Currently supports TranslatePress. Can be extended for other plugins via filter.
	 *
	 * @return string Default language code (e.g., 'en_US').
	 * @since 6.7.9
	 */
	public static function get_default_language( $plugin = 'translatepress' ): string {
		$language = get_locale();

		if ( 'translatepress' === $plugin && static::is_translatepress_active() ) {
			$trp = \TRP_Translate_Press::get_trp_instance();
			if ( $trp ) {
				$settings = $trp->get_component( 'settings' )->get_settings();
				$language = $settings['default-language'] ?? get_locale();
			}
		}

		return apply_filters( 'wptravelengine_default_language', $language );
	}

	/**
	 * Get the current TranslatePress language.
	 *
	 * @return string|null Current language code or null if TranslatePress is not active.
	 * @since 6.7.9
	 */
	public static function get_translatepress_language(): ?string {
		if ( ! static::is_translatepress_active() || ! function_exists( 'trp_get_locale' ) ) {
			return null;
		}

		return trp_get_locale() ?? null;
	}

	/**
	 * Set the TranslatePress language.
	 *
	 * @param string $language_code Language code.
	 * @return void
	 * @since 6.7.9
	 */
	public static function set_translatepress_language( $language_code = null ): void {
		if ( ! static::is_translatepress_active() || ! $language_code || ! function_exists( 'trp_switch_language' ) ) {
			return;
		}

		trp_switch_language( $language_code );
	}
}
