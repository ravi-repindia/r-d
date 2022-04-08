<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'N11DT5tBYVuG4GP6zwU9RY1CuOWZlYdyhr04zZ3fRtKnOe1PmvWHOe1xhhJpGSS72LEFHjxZCTtdbDbASdYjzg==');
define('SECURE_AUTH_KEY',  'I+fPC1GGOZ16G2dcd7OB0K+5LVHOH/5iUddCWKhn4nJS7osUQqMjoS80OOO7NWI8ofPqDuc2vqH3Ovk+Ovs5Lg==');
define('LOGGED_IN_KEY',    'WYBIdgzqCySeh12sipD97F87+J1EmY+E6NO52rf1bH49UtuyblprUvC5QMhZHFwhURlk4F/B2+OOsQeM544yrw==');
define('NONCE_KEY',        'vsktHNkNM567IZpjlJAzs8mKQCXs3mc6luNvtqzFNW/7f9Hz1SH+U4hqStdsfJpqwVFW20A45n/lqna21Je8rw==');
define('AUTH_SALT',        'BAHgHMPuffzdNbcEc94dcfCMbOC089Cc479xizl0r5uIo8xIY7UUd2q355MRvfyL/9odFo3KtntwRWruozSkvQ==');
define('SECURE_AUTH_SALT', 'qil9s4dcjBpASbBDdvo9PD+g1aTB791CzRRArj+kEo1iTsMF5q8jeA9BxgoJBHTyyl0H1307pj11LUo4zD1Alw==');
define('LOGGED_IN_SALT',   'qiBmrXh+nRluKr9IjIozybisgpdC/83lCfbckX//n9GOfP0pj8ThIxVPcp9mub9bsibDlfLV7v3qtUdn7aJkJw==');
define('NONCE_SALT',       '/QBryG7yjlAtT3VaxRPX/1YF/EYs8uc7jjw8EfkYlDWpw212x7bWu7IM3ap0hggzAbKIa+YuuNAtV0tl5lrCQg==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
