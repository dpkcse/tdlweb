<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }
delete_option( 'tdl_ccs_settings' );
delete_option( 'tdl_ccs_google_tokens' );
delete_option( 'tdl_ccs_logs' );
