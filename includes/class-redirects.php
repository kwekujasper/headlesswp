<?php
/**
 * Handles frontend redirect logic when headless mode is active.
 *
 * @package HeadlessWP
 */

namespace HeadlessWP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Redirects
 */
class Redirects {

	public function __construct( private Settings $settings ) {}

	public function register_hooks(): void {
		add_action( 'template_redirect', [ $this, 'maybe_redirect' ], 1 );
		add_action( 'send_headers', [ $this, 'maybe_noindex' ], 1 );

		// Optional robots.txt override.
		add_action( 'do_robots', [ $this, 'maybe_override_robots' ] );
	}

	/**
	 * Redirect frontend requests to the external frontend URL.
	 * API endpoints, admin, AJAX and cron are always allowed through.
	 */
	public function maybe_redirect(): void {
		if ( ! $this->settings->is_headless() ) {
			return;
		}

		if ( $this->is_allowed_request() ) {
			return;
		}

		$frontend_url = $this->settings->frontend_url();

		if ( empty( $frontend_url ) ) {
			// No frontend URL configured — show maintenance notice.
			$this->show_maintenance();
			return;
		}

		if ( $this->settings->is_enabled( 'headlesswp_maintenance_mode' ) ) {
			$this->show_maintenance();
			return;
		}

		$destination = $this->build_destination( $frontend_url );

		wp_redirect( $destination, 301 );
		exit;
	}

	/**
	 * Determine the redirect destination, optionally preserving the request slug/path.
	 */
	private function build_destination( string $frontend_url ): string {
		if ( ! $this->settings->is_enabled( 'headlesswp_preserve_slugs' ) ) {
			return trailingslashit( $frontend_url );
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path        = wp_parse_url( $request_uri, PHP_URL_PATH ) ?? '/';
		$query       = wp_parse_url( $request_uri, PHP_URL_QUERY ) ?? '';

		$destination = rtrim( $frontend_url, '/' ) . $path;
		if ( ! empty( $query ) ) {
			$destination .= '?' . $query;
		}

		return $destination;
	}

	/**
	 * Add X-Robots-Tag noindex header to all WordPress-generated responses.
	 */
	public function maybe_noindex(): void {
		if ( $this->settings->is_enabled( 'headlesswp_noindex' ) ) {
			header( 'X-Robots-Tag: noindex, nofollow', true );
		}
	}

	/**
	 * Optionally override robots.txt output.
	 */
	public function maybe_override_robots(): void {
		if ( ! $this->settings->is_enabled( 'headlesswp_robots_txt' ) ) {
			return;
		}

		echo "User-agent: *\nDisallow: /\n";

		$frontend_url = $this->settings->frontend_url();
		if ( ! empty( $frontend_url ) ) {
			echo 'Sitemap: ' . esc_url( trailingslashit( $frontend_url ) . 'sitemap.xml' ) . "\n";
		}

		exit;
	}

	/**
	 * Show a maintenance page and exit.
	 */
	private function show_maintenance(): void {
		status_header( 503 );
		nocache_headers();

		$template = HEADLESSWP_PLUGIN_DIR . 'templates/maintenance.php';
		if ( file_exists( $template ) ) {
			include $template;
		} else {
			echo '<p>' . esc_html__( 'Frontend temporarily unavailable. Please try again later.', 'headlesswp' ) . '</p>';
		}
		exit;
	}

	/**
	 * Returns true for any request that must bypass frontend redirection.
	 */
	private function is_allowed_request(): bool {
		// WordPress admin.
		if ( is_admin() ) {
			return true;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		$allowed_patterns = [
			'#^/wp-json(/|$)#i',
			'#^/graphql(/|$)#i',
			'#^/wp-admin(/|$)#i',
			'#^/wp-login\.php#i',
			'#^/wp-cron\.php#i',
			'#^/admin-ajax\.php#i',
			'#^/wp-content/#i',
			'#^/wp-includes/#i',
			'#^/favicon\.ico$#i',
			'#^/robots\.txt$#i',
			'#^/sitemap#i',
		];

		foreach ( $allowed_patterns as $pattern ) {
			if ( preg_match( $pattern, $request_uri ) ) {
				return true;
			}
		}

		// REST API via WordPress conditional.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		// AJAX requests.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		// Cron requests.
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return true;
		}

		// XML-RPC.
		if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
			return ! $this->settings->is_enabled( 'headlesswp_xmlrpc_enabled' );
		}

		return false;
	}
}
