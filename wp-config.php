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
define('DB_NAME', 'ch53f87180');

/** MySQL database username */
define('DB_USER', 'ch53f87180');

/** MySQL database password */
define('DB_PASSWORD', 'aa8834a42a');

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
define('AUTH_KEY',         '~7>3W=`(CynpYcaj6M17!-4t)g7}/])|YyF^g1vASyGed7eAWx/Z9Z)95*]-=z^X');
define('SECURE_AUTH_KEY',  'KYd3rvCRJt%I(%M}?LhRC1*H=AjVI2?4sYcmbhYW4_}Si.1j>_+Z.??JT@*KWS~0');
define('LOGGED_IN_KEY',    'ND@10p$[l(HqvCa{Y,{ V!CFmW>(YNy3i.hU(>y+8+#`1kaQ^OE17,! 2N!6/R/p');
define('NONCE_KEY',        '(lo-x<IX,fH*JI6:Rul;ZBnOMN]V>K+v)}p>{=:>@1*AjBu6:s9CZu@iaeZHP7LB');
define('AUTH_SALT',        'J#q/mD/w(ZZ^*MF*KQ0NY2 Av1PH-bm>>Uz49^RH)L o0[?[eWrX;Ph]+0G%,U@1');
define('SECURE_AUTH_SALT', 'O(m1{h(53d&Y~fDFY,RKR1kd/[<Vr)AT;In4&n)J%1nN!/^:DgPh85uMNJ|;?!oG');
define('LOGGED_IN_SALT',   'S1~u`}d#M^ZcHt9YU`Y>Xs6S3j6 4*6c^$.,(j C]rWE!CQ_C/^[jJI HTmF!gMd');
define('NONCE_SALT',       ':8@qgj_E`o[/M@NCHV05:w`3a&REDQl#4N,}!7`IW: `!d.V]~[20ml|pDnM9gjh');

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
