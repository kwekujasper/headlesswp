<?php
/**
 * Settings accessor — thin wrapper around WordPress Options API.
 *
 * @package HeadlessWP
 */

namespace HeadlessWP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Settings
 */
class Settings {

	/**
	 * Get a typed option value with a fallback default.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Fallback when option is not set.
	 * @return mixed
	 */
	public function get( string $key, mixed $default = '' ): mixed {
		return get_option( $key, $default );
	}

	/**
	 * Convenience: return true when an option is the string '1'.
	 */
	public function is_enabled( string $key ): bool {
		return '1' === (string) $this->get( $key, '0' );
	}

	/**
	 * Return the sanitized frontend URL, or empty string.
	 */
	public function frontend_url(): string {
		return (string) $this->get( 'headlesswp_frontend_url', '' );
	}

	/**
	 * Return the configured path prefix for single-post redirects
	 * (e.g. "post" so a post redirects to /post/my-slug/), with slashes
	 * trimmed. Empty string means posts redirect to the frontend root:
	 * /my-slug/.
	 */
	public function post_path_prefix(): string {
		return trim( (string) $this->get( 'headlesswp_post_path_prefix', '' ), '/' );
	}

	/**
	 * Whether headless mode is currently active.
	 */
	public function is_headless(): bool {
		return $this->is_enabled( 'headlesswp_enabled' );
	}

	/**
	 * Return allowed CORS origins as an array of trimmed strings.
	 *
	 * @return string[]
	 */
	public function allowed_origins(): array {
		$raw = (string) $this->get( 'headlesswp_allowed_origins', '' );
		if ( '' === $raw ) {
			return [];
		}
		return array_filter( array_map( 'trim', explode( "\n", $raw ) ) );
	}

	/**
	 * Register settings with WordPress Settings API (called once on init).
	 */
	public function register_hooks(): void {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Register all plugin options via Settings API.
	 */
	public function register_settings(): void {
		$options = [
			'headlesswp_enabled'                 => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_frontend_url'            => [ 'sanitize_callback' => 'esc_url_raw' ],
			'headlesswp_noindex'                 => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_preserve_slugs'          => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_post_path_prefix'        => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_disable_rss'             => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_disable_search'          => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_disable_comments'        => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_disable_author_archives' => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_disable_date_archives'   => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_allowed_origins'         => [ 'sanitize_callback' => [ $this, 'sanitize_origins' ] ],
			'headlesswp_maintenance_mode'        => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_xmlrpc_enabled'          => [ 'sanitize_callback' => 'sanitize_text_field' ],
			'headlesswp_robots_txt'              => [ 'sanitize_callback' => 'sanitize_text_field' ],
		];

		foreach ( $options as $key => $args ) {
			register_setting( 'headlesswp_settings', $key, $args );
		}
	}

	/**
	 * Sanitize a newline-separated list of URLs.
	 *
	 * @param string $value Raw textarea input.
	 * @return string
	 */
	public function sanitize_origins( string $value ): string {
		$lines = array_filter( array_map( 'trim', explode( "\n", $value ) ) );
		$clean = array_map( 'esc_url_raw', $lines );
		return implode( "\n", $clean );
	}
}
