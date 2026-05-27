<?php
define( 'WP_CACHE', true );




// Added by AirLift
// Added by AirLift
 // Added by AirLift
 // Added by AirLift
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tourgmpn_wp345' );
/** Database username */
define( 'DB_USER', 'tourgmpn_wp345' );
/** Database password */
define( 'DB_PASSWORD', 'vJx(M@6-(Y(9SpW0' );
/** Database hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'mojxlesnjheiaa5qpnamxnvgu071iyg5hffjugthyvpf36bljqcnqunc8eijep5l' );
define( 'SECURE_AUTH_KEY',  '1mbt0tsduahm7ndbmu4hikuoyytylabrus67cibo5nbjsf9cn8wxtjvfdygddb3b' );
define( 'LOGGED_IN_KEY',    'eacdtezkhlasejcpg348aj0aimfsgxz2ddgsqsdvdnlsuegwpkspcacccl6mqs9q' );
define( 'NONCE_KEY',        'ybx0onfihztjzw8oc4ur7thwhpqisdvibt53c2vkovefs1iiptzcljoiphwhdm9d' );
define( 'AUTH_SALT',        'mlt5tvrngg7vokkcpgjl2zr1un88msznttwebmcoifntutlqghqhn3dpflxqfmph' );
define( 'SECURE_AUTH_SALT', 'pxvvakekxeuldsilswzpq50uu6qnsieerkfcq65fdcfreeagpwkpcha61bzfjwzk' );
define( 'LOGGED_IN_SALT',   '98ornefu0su2yhahuscofadq4zulcr11pqaqydfaf1wqzeeixtx8jorp8bzxpatc' );
define( 'NONCE_SALT',       'h0eyufz8tdmf7kkfoq1rjyzbtgtfe7oluiwtjcglj06nrbawxfphyik3kdc3ddeb' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpml_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );
/* Add any custom values between this line and the "stop editing" line. */
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';