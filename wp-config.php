<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

define( 'WP_MEMORY_LIMIT', '256M' );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', "d03f8db9" );

/** Database username */
define( 'DB_USER', "d03f8db9" );

/** Database password */
define( 'DB_PASSWORD', "NHQoRPcKDkAR7XhuDDqV" );

/** Database hostname */
define( 'DB_HOST', "localhost" );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',         'Zl3ueVcxDvXpeHK^>9WqY<*MTJi,]z[fU@:=+JO/tP4qErVKTUIT/@lNN1S@Pv)R' );
define( 'SECURE_AUTH_KEY',  '7hn+NqnQ%X01}}JeG?u|[;DH7.LuRf5v+7.(yy&oe$;7PUQ?A;rHUnVZ5!NG&CD-' );
define( 'LOGGED_IN_KEY',    'K/#&U!%yVExdC^pf|d%T2^vd`}UuOt,+(2we*_dS3WC8bC8)6r3(S+3c,c|McM,Y' );
define( 'NONCE_KEY',        '^Q|HC_`U5;5j.zB71#*YqV2IAZzgOGF/Wj08d<T9pZ,M,xD]26v%Y87V([6%=|_!' );
define( 'AUTH_SALT',        'Px1AX6I[=tQ^$Xi9-kn;dkAA|Lb>/H^[G^-if%q7$WSh/*BeSnQP6&pba@5,9q?[' );
define( 'SECURE_AUTH_SALT', ';u7M}e._WMZhsF`S=FzWrwmF#P$r-re:nQ0@EQ#J:Za`5$.YRvXy5G{(-tF7!{(Q' );
define( 'LOGGED_IN_SALT',   'fL$RtD>g^(L|hD4qE+/f?eR}aU&e(WDD-`G(1;&6}G*-I}Cmd$H=#=wL7!r(;4%k' );
define( 'NONCE_SALT',       '4^RA=8UNs*7nH83t0M(K{9u?ECq4&$,`#AXHP0rZ`@%(^@W^5tu4Dl<7!AE/A,Te' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'vh9td_';
define( 'WP_POST_REVISIONS', 10 );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(__FILE__) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
