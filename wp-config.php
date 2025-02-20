<?php
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
 * This has been slightly modified (to read environment variables) for use in Docker.
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// IMPORTANT: this file needs to stay in-sync with https://github.com/WordPress/WordPress/blob/master/wp-config-sample.php
// (it gets parsed by the upstream wizard in https://github.com/WordPress/WordPress/blob/f27cb65e1ef25d11b535695a660e7282b98eb742/wp-admin/setup-config.php#L356-L392)

// a helper function to lookup "env_FILE", "env", then fallback
if ( ! function_exists( 'getenv_docker' ) ) {
	// https://github.com/docker-library/wordpress/issues/588 (WP-CLI will load this file 2x)
	function getenv_docker( $env, $default ) {
		if ($fileEnv = getenv($env . '_FILE')) {
			return rtrim( file_get_contents( $fileEnv ), "\r\n" );
		}
		else if ( ($val = getenv( $env ) ) !== false) {
			return $val;
		}
		else {
			return $default;
		}
	}
}

/** The name of the database for WordPress */
define( 'DB_NAME', getenv_docker( 'WORDPRESS_DB_NAME', 'wordpress' ) );
define( 'DB_USER', getenv_docker( 'WORDPRESS_DB_USER', 'example username' ) );
define( 'DB_PASSWORD', getenv_docker( 'WORDPRESS_DB_PASSWORD', 'example password' ) );
define( 'DB_HOST', getenv_docker( 'WORDPRESS_DB_HOST', 'mysql' ) );
define( 'DB_COLLATE', getenv_docker( 'WORDPRESS_DB_COLLATE', '' ) );
define( 'DB_CHARSET', getenv_docker( 'WORDPRESS_DB_CHARSET', 'utf8' ) );

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
define( 'AUTH_KEY',         getenv_docker( 'WORDPRESS_AUTH_KEY',         'put your unique phrase here' ) );
define( 'SECURE_AUTH_KEY',  getenv_docker( 'WORDPRESS_SECURE_AUTH_KEY',  'put your unique phrase here' ) );
define( 'LOGGED_IN_KEY',    getenv_docker( 'WORDPRESS_LOGGED_IN_KEY',    'put your unique phrase here' ) );
define( 'NONCE_KEY',        getenv_docker( 'WORDPRESS_NONCE_KEY',        'put your unique phrase here' ) );
define( 'AUTH_SALT',        getenv_docker( 'WORDPRESS_AUTH_SALT',        'put your unique phrase here' ) );
define( 'SECURE_AUTH_SALT', getenv_docker( 'WORDPRESS_SECURE_AUTH_SALT', 'put your unique phrase here' ) );
define( 'LOGGED_IN_SALT',   getenv_docker( 'WORDPRESS_LOGGED_IN_SALT',   'put your unique phrase here' ) );
define( 'NONCE_SALT',       getenv_docker( 'WORDPRESS_NONCE_SALT',       'put your unique phrase here' ) );
// (See also https://wordpress.stackexchange.com/a/152905/199287)

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv_docker('WORDPRESS_TABLE_PREFIX', 'wp_');

if ( getenv_docker( 'IS_DOCKER_DEV', true ) ) {
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_DISPLAY', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );

	define( 'WP_ENVIRONMENT_TYPE', 'development' );
	define( 'WP_DEVELOPMENT_MODE', 'all' );

	// Only run this off-prod for safety.
	if ( $configExtra = getenv_docker( 'WORDPRESS_CONFIG_EXTRA', '' ) ) {
		eval($configExtra);
	}
} else {
	define( 'WP_DEBUG', false );
	define( 'WP_DEBUG_DISPLAY', false );
	define( 'WP_DEBUG_LOG', true );

	define( 'WP_ENVIRONMENT_TYPE', 'production' );
}

// Use crontab cron, not AJAX
define( 'DISABLE_WP_CRON', true );

// Placeholder for the disabled ini_set function.
if ( ! function_exists('ini_set') ) {
	function ini_set( $varname, $newvalue ) {
		return;
	}
}

if ( ! function_exists('error_reporting') ) {
	function error_reporting( $error_level = null ) {
		if ( 'development' === WP_ENVIRONMENT_TYPE ){
			return 341;
		} else {
			return 0;
		}
	}
}

// If we're behind a proxy server and using HTTPS, we need to alert WordPress of that fact
// see also https://wordpress.org/support/article/administration-over-ssl/#using-a-reverse-proxy
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && false !== strpos( $_SERVER['HTTP_X_FORWARDED_PROTO'], 'https' ) ) {
	$_SERVER['HTTPS'] = 'on';
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
