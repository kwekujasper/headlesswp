<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Removes all plugin options from the database.
 *
 * @package HeadlessWP
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$option_keys = [
	'headlesswp_enabled',
	'headlesswp_frontend_url',
	'headlesswp_noindex',
	'headlesswp_preserve_slugs',
	'headlesswp_disable_rss',
	'headlesswp_disable_search',
	'headlesswp_disable_comments',
	'headlesswp_disable_author_archives',
	'headlesswp_disable_date_archives',
	'headlesswp_allowed_origins',
	'headlesswp_maintenance_mode',
	'headlesswp_xmlrpc_enabled',
	'headlesswp_robots_txt',
];

foreach ( $option_keys as $key ) {
	delete_option( $key );
}

delete_transient( 'headlesswp_health_cache' );
