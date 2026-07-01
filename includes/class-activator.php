<?php
/**
 * Fired during plugin activation.
 *
 * @package HeadlessWP
 */

namespace HeadlessWP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 *
 * Sets default options on first activation.
 */
class Activator {

	/**
	 * Run activation routines.
	 */
	public static function activate(): void {
		self::set_defaults();
		flush_rewrite_rules();
	}

	/**
	 * Set default plugin options if they don't already exist.
	 */
	private static function set_defaults(): void {
		$defaults = [
			'headlesswp_enabled'                 => '0',
			'headlesswp_frontend_url'            => '',
			'headlesswp_noindex'                 => '0',
			'headlesswp_preserve_slugs'          => '1',
			'headlesswp_post_path_prefix'        => '',
			'headlesswp_disable_rss'             => '0',
			'headlesswp_disable_search'          => '0',
			'headlesswp_disable_comments'        => '0',
			'headlesswp_disable_author_archives' => '0',
			'headlesswp_disable_date_archives'   => '0',
			'headlesswp_allowed_origins'         => '',
			'headlesswp_maintenance_mode'        => '0',
			'headlesswp_xmlrpc_enabled'          => '1',
			'headlesswp_robots_txt'              => '0',
		];

		foreach ( $defaults as $key => $value ) {
			if ( get_option( $key ) === false ) {
				add_option( $key, $value );
			}
		}
	}
}
