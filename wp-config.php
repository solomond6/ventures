<?php

//Begin Really Simple SSL Load balancing fix
if ((isset($_ENV["HTTPS"]) && ("on" == $_ENV["HTTPS"]))
|| (isset($_SERVER["HTTP_X_FORWARDED_SSL"]) && (strpos($_SERVER["HTTP_X_FORWARDED_SSL"], "1") !== false))
|| (isset($_SERVER["HTTP_X_FORWARDED_SSL"]) && (strpos($_SERVER["HTTP_X_FORWARDED_SSL"], "on") !== false))
|| (isset($_SERVER["HTTP_CF_VISITOR"]) && (strpos($_SERVER["HTTP_CF_VISITOR"], "https") !== false))
|| (isset($_SERVER["HTTP_CLOUDFRONT_FORWARDED_PROTO"]) && (strpos($_SERVER["HTTP_CLOUDFRONT_FORWARDED_PROTO"], "https") !== false))
|| (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && (strpos($_SERVER["HTTP_X_FORWARDED_PROTO"], "https") !== false))
|| (isset($_SERVER["HTTP_X_PROTO"]) && (strpos($_SERVER["HTTP_X_PROTO"], "SSL") !== false))
) {
$_SERVER["HTTPS"] = "on";
}
//END Really Simple SSL
# putenv('TMPDIR=/tmp');
/** Enable W3 Total Cache */
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
define('DB_NAME', 'ventures_wp');
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

define('ADMIN_COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');
define('COOKIEPATH', '');
define('SITECOOKIEPATH', '');

define('WP_ALLOW_MULTISITE', true);

define('MULTISITE', false);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost');
define('PATH_CURRENT_SITE', '/venturesafrica.com/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

define('WP_ALLOW_REPAIR', true);

//define('DISABLE_WP_CRON', 'true');
//define( 'WP_DEBUG', true );
//define( 'WP_DEBUG_LOG', true );
//define( 'WP_DEBUG_DISPLAY', false);
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '-4mQ j%%4]K|=B!Fjp_TtN@^ME,|OHj.FH.Lyv>_/<9}qa`7,o/-C};h-h9] /%E');
define('SECURE_AUTH_KEY',  '&f-Gl|W%*6pJzOQ6s`|B~eK+3xSDh0{gHMP/U*G9iVXPhGnh-/VX-{[@z+!C1iU~');
define('LOGGED_IN_KEY',    '=pZZ;wpecsz&QFTXdV+O jc9y8S--O/K$#l#[WlKf5le@:#i./8Il~tcoYS>{SFP');
define('NONCE_KEY',        'D.[K[VlSh-f5(sDx1Ye-%;g0PW?CH+OyxXf93sX8Ekt9w+g>>kQ`#sdU>_$-pNiZ');
define('AUTH_SALT',        'P}zO,!DNhd<|#_Fa+%L,-d-O/qH> $F+TmS%Wj<G4HCNb{4$+&25l7qE6cb@M%L9');
define('SECURE_AUTH_SALT', 'JmMBe3&pfrW|(7X:kHmA>aETJ&R<N/2q^},Z^Q:F>OE4-@l|5{||jqOzu>cT8[E[');
define('LOGGED_IN_SALT',   'yy=E{hmH+O;+@|J~k[EdmWg`2+yfb+^JlnS4j0CmSu?gV]&-[&ulGb*OkQ]0GI*5');
define('NONCE_SALT',       '<Rpa`Na5d^6)d@{3+-T6Dn*:)ndC#!F8H5/8`AS}LjF;`(f&DI/-8_616ZS@*U+P');
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
$debug_override = isset($_GET['fsucb']);
define('WP_DEBUG', false);
define( 'WP_DEBUG_LOG', false );
define('VENTURES_UNCOMPRESSED_JS', $debug_override);
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
