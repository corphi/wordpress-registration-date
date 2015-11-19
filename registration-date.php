<?php
/*
Plugin Name: Registration Date
Plugin URI: http://github.com/corphi/wordpress-registration-date
Description: Add the registration date column to the users table. If you don’t have PHP 5.3 or later, your WordPress will explode.
Version: 0.3
Author: Philipp Cordes
Text Domain: registration-date
Domain Path: /languages/
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'I’m a plugin.' );
}

/**
 * Load localized strings for the plugin.
 *
 * @see http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
 */
function registration_date_load_textdomain()
{
	remove_action( 'init', __FUNCTION__ );

	$domain = 'registration-date';
	// Filter known from load_plugin_textdomain().
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	load_textdomain( $domain, WP_LANG_DIR . "/registration-date/$domain-$locale.mo" );
	load_plugin_textdomain( $domain, false, basename( __DIR__ ) . '/languages/' );
}
add_action( 'init' , 'registration_date_load_textdomain' );


if ( ! function_exists( 'registration_date_trigger_error' ) ) {
	/**
	 * Show an error message.
	 * 
	 * @see http://www.squarepenguin.com/wordpress/?p=6 Inspiration
	 * 
	 * @param string $message
	 * @param int    $type    optional
	 * @return bool
	 */
	function registration_date_trigger_error( $message, $level = E_USER_ERROR )
	{
		if ( isset( $_GET['action'] ) && 'error_scrape' === $_GET['action'] ) {
			echo $message;
			return true;
		}

		return trigger_error( $message, $level );
	}
}

// Check for suitable environment
if ( ! defined( 'PHP_VERSION_ID' ) || PHP_VERSION_ID < 50400 ) {
	// Version too low
	registration_date_load_textdomain();
	registration_date_trigger_error(
		sprintf(
			__( 'You need at least PHP 5.4 to use Registration Date. Your are using %s.', 'registration-date' ),
			PHP_VERSION
		),
		E_USER_ERROR
	);
}

// Load required library shy-wordpress
if ( ! function_exists( 'shy_wordpress_autoloader' ) ) {
	if ( ! include_once __DIR__ . '/use/shy-wordpress/src/autoloader.php' ) {
		registration_date_load_textdomain();
		registration_date_trigger_error(
			__( 'Couldn’t load required library “shy-wordpress”. Reinstalling the plugin may solve this problem.', 'registration-date' ),
			E_USER_ERROR
		);
		return;
	}
}

// Register our autoloader
if ( ! include_once __DIR__ . '/src/autoloader.php' ) {
	registration_date_load_textdomain();
	registration_date_trigger_error(
		__( 'The plugin is incomplete. Reinstalling it may solve this problem.', 'registration-date' ),
		E_USER_ERROR
	);
	return;
}

return new \Corphi\WordPress\RegistrationDatePlugin();


// Dummy calls for translation to include metadata in translation files
__( 'Registration Date', 'registration-date' );
__( 'Add the registration date column to the users table. If you don’t have PHP 5.3 or later, your WordPress will explode.', 'registration-date' );
