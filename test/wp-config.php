<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'testSinglePage');

/** MySQL database username */
define('DB_USER', 'PavloVD');

/** MySQL database password */
define('DB_PASSWORD', 'vdovic8');

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
define('AUTH_KEY',         'o_YfE6O-+rRdb6r(e}h]|L aecQg<^ZE^0M9fFZ@?%E3+4Wt8G3cbQ[LoD2Ey`>i');
define('SECURE_AUTH_KEY',  '_KmeOF~g2h>VueXT-hDkNQuR&wcd{;NI]KHoq/W-q1Y1ebR]C$d>3|LyH8I&UJ;~');
define('LOGGED_IN_KEY',    '8[thbdVhbtRWcX8r*:SQ3AM{jw9oy$8M/L28$t#RkGV,*`cWfHTW,b7uE#b;Xj$^');
define('NONCE_KEY',        ':q-HuRJ}k(LKI3G:AGCj_A:RWsV& FJ^vuoxF! fMo__8x+8.__SHp%+;KkuyjdZ');
define('AUTH_SALT',        'xVG=Z(+/%,t,KHnQu5 gzfQj8NeE,m`I~sRnD@jrv=P=oogQ6^SVJ@D-{F4>ymD[');
define('SECURE_AUTH_SALT', 'P/m-xw{9ll*Hi6NC@+u1t<Vng_V;_- WRP):``guNXj3o..l3R2$.3}]9WfYq?ar');
define('LOGGED_IN_SALT',   'RqgxE!04s]@p|Ej)1sZBk@^0CLrwt6b;VcJf3J~rvUNY0d}.l]iau$)tm+V0Q9Y;');
define('NONCE_SALT',       '$+++YEuII^uu$)Pn++w5A#ZMwC(|H^&u`DP<y)&SupaX ./3YE>1e%tKdTlsC#ob');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
