<?php // Modified for WPH (Installs Hebrew translation)
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
define('DB_NAME', 'amirdota_amirblog');

/** MySQL database username */
define('DB_USER', 'amirdota_amir');

/** MySQL database password */
define('DB_PASSWORD', '009900a');

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
define('AUTH_KEY',         'r624`7!n+% @|AaQKj2S&y91Yl);0lsB+i;&!xrm0kv2EV--?%!eZ*6o>aM/86A`');
define('SECURE_AUTH_KEY',  '#>=|92_k{MnHJ!p$9FMZ[R<@+PW8zv.?YUSWSwC7#T^t^ygQQEz^uW`-3#0E+$-#');
define('LOGGED_IN_KEY',    'L;+&QwS5&mu!^su]+4_4eDo$`k!bNM0Z-|>HpS&D*!X9^-BiFqz#F2<gwPhxJ;+-');
define('NONCE_KEY',        'KqNcK!3}$kmL)==tY/eASFdQ|dsY{/Ml!g:bQi|TM|pkpC4Bx3^T}ogN_;%Sd,=|');
define('AUTH_SALT',        'Er+E3y(~Wr]sCB@:+?*V`L>@|:w}YRX[ A6r3o@d(RPW9SSg]r~5SX4,V/Y~<$Rz');
define('SECURE_AUTH_SALT', 'kid#I=#em>kyja>g](O^*m%>Y-zSjsH>l|$H2? z&5JpRwocQ4THcNa9pkq|Tkl`');
define('LOGGED_IN_SALT',   'DUOi8|pZ:@5Zr|zDy&#A-+9ujA>BiIn@~juN$%zhY OO~9,Vx>(@9~$ceVi%y{Gn');
define('NONCE_SALT',       '880B(cJKP.^*kk+Ymua*LH$c]ez%T1F|0:`XE/zt+h(wfL?T+DWbew-[SUUruIyt');

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
 * Change this to localize WordPress. By default, the Hebrew locale 
 * is used. To use another locale, a corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define ('WPLANG', 'he_IL');

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
