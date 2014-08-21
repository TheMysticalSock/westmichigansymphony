<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wsso_db');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'GXs`T+M Qa+Ay4`xyE4_~/vW,1&`z1)^}V;e,:WJixqZ-_-i(l_!8SIp=F^wo4+b');
define('SECURE_AUTH_KEY',  '6#|<)l>,~^q=|Qd]-FXLmRo4Fg@:)~bgRcC:g>tQ)-im~?u=Ra,>b=IQ/>4tS.]w');
define('LOGGED_IN_KEY',    'M=4>.WT:e~vTjVd#fK,loc-F=U,wOtLxAYjxD`nNhs0s@UEUR0`QnB$g-@FB3d(=');
define('NONCE_KEY',        '&5y}Pu-~w]Vjce~k=gCm3.3huAH:WNvboDVl hAvx?]Se/3qLL8Z9*bI2|<IFMs-');
define('AUTH_SALT',        'i,2|U_QkI|,$9`]UZ>3P_*{;UVA^vjC5Vnii1AJy5ijDTk6T~dqLyz[(m_mDyZ~4');
define('SECURE_AUTH_SALT', '.e6>Y)r4XRPUbUlRfmXU-^6sV0k=:?^:-meWg[6yk-w#oj@WC} ^+W[m66CW7*UY');
define('LOGGED_IN_SALT',   'qROVdC6UgTQ|CuIXd:.[(Pa;k+G(-TXl}J1Or&v0URrV)C0%-s|as)2G=D&VYe6B');
define('NONCE_SALT',       'm?)1Cv)C-Bg9D/T+gh{FTf5@j&x=Z5u/M5aA){HR/$cdEM*T1@OWGWZ4*JV<`Fp@');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
