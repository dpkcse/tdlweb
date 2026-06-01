<?php
/**
 * Plugin Name: TDL Chauffeur Calendar Sync
 * Description: Companion plugin that syncs Chauffeur Booking System bookings to Google Calendar.
 * Version: 1.1.0
 * Author: TDL
 * Requires PHP: 7.4
 * Text Domain: tdl-chauffeur-calendar-sync
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TDL_CCS_VERSION', '1.1.0' );
define( 'TDL_CCS_FILE', __FILE__ );
define( 'TDL_CCS_DIR', plugin_dir_path( __FILE__ ) );
define( 'TDL_CCS_URL', plugin_dir_url( __FILE__ ) );
define( 'TDL_CCS_OPTION_SETTINGS', 'tdl_ccs_settings' );
define( 'TDL_CCS_OPTION_TOKENS', 'tdl_ccs_google_tokens' );
define( 'TDL_CCS_OPTION_LOGS', 'tdl_ccs_logs' );
define( 'TDL_CCS_EVENT_ID_META', '_tdl_ccs_google_calendar_event_id' );
define( 'TDL_CCS_SYNCED_AT_META', '_tdl_ccs_synced_at' );
define( 'TDL_CCS_SYNC_HASH_META', '_tdl_ccs_last_sync_hash' );

require_once TDL_CCS_DIR . 'includes/class-tdl-ccs-logger.php';
require_once TDL_CCS_DIR . 'includes/class-tdl-ccs-google-auth.php';
require_once TDL_CCS_DIR . 'includes/class-tdl-ccs-booking-mapper.php';
require_once TDL_CCS_DIR . 'includes/class-tdl-ccs-calendar.php';
require_once TDL_CCS_DIR . 'includes/class-tdl-ccs-booking-listener.php';
require_once TDL_CCS_DIR . 'includes/class-tdl-ccs-admin.php';
require_once TDL_CCS_DIR . 'includes/class-tdl-ccs-plugin.php';

register_activation_hook( __FILE__, array( 'TDL_CCS_Plugin', 'activate' ) );

add_action(
	'plugins_loaded',
	static function () {
		TDL_CCS_Plugin::instance()->init();
	}
);
