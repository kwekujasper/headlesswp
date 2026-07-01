<?php
/**
 * Plugin Name:       HeadlessWP by KJM
 * Plugin URI:        https://kwekujasper.com/headlesswp
 * Description:       Transform WordPress into a secure, configurable headless CMS for any modern frontend framework (Next.js, Nuxt, Astro, SvelteKit, and more).
 * Version:           1.0.1
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Kweku Jasper Media
 * Author URI:        https://kwekujasper.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       headlesswp
 * Domain Path:       /languages
 *
 * @package HeadlessWP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HEADLESSWP_VERSION', '1.0.1' );
define( 'HEADLESSWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HEADLESSWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HEADLESSWP_PLUGIN_FILE', __FILE__ );
define( 'HEADLESSWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader for HeadlessWP classes.
 */
spl_autoload_register( function ( string $class ) {
	$prefix    = 'HeadlessWP\\';
	$base_dir  = HEADLESSWP_PLUGIN_DIR . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative = substr( $class, $len );
	$file     = $base_dir . 'class-' . strtolower( str_replace( [ '\\', '_' ], [ '/', '-' ], $relative ) ) . '.php';

	if ( file_exists( $file ) ) {
		require $file;
	}
} );

/**
 * Activation hook.
 */
register_activation_hook( __FILE__, function () {
	require_once HEADLESSWP_PLUGIN_DIR . 'includes/class-activator.php';
	HeadlessWP\Activator::activate();
} );

/**
 * Deactivation hook.
 */
register_deactivation_hook( __FILE__, function () {
	require_once HEADLESSWP_PLUGIN_DIR . 'includes/class-deactivator.php';
	HeadlessWP\Deactivator::deactivate();
} );

/**
 * Bootstrap the plugin after all plugins are loaded.
 */
add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'headlesswp', false, dirname( HEADLESSWP_PLUGIN_BASENAME ) . '/languages' );

	require_once HEADLESSWP_PLUGIN_DIR . 'includes/class-plugin.php';
	HeadlessWP\Plugin::get_instance()->run();
} );
