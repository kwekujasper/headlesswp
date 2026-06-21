<?php
/**
 * Admin interface — settings page, menus, and asset enqueueing.
 *
 * @package HeadlessWP
 */

namespace HeadlessWP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin
 */
class Admin {

	public function __construct(
		private Settings $settings,
		private Health   $health
	) {}

	public function register_hooks(): void {
		add_action( 'admin_menu',             [ $this, 'add_menu_page' ] );
		add_action( 'admin_enqueue_scripts',  [ $this, 'enqueue_assets' ] );
		add_action( 'admin_notices',          [ $this, 'maybe_show_notices' ] );
		add_filter( 'plugin_action_links_' . HEADLESSWP_PLUGIN_BASENAME, [ $this, 'add_action_links' ] );

		// Tools: handle form submissions.
		add_action( 'admin_post_headlesswp_flush_permalinks', [ $this, 'handle_flush_permalinks' ] );
		add_action( 'admin_post_headlesswp_export_settings',  [ $this, 'handle_export_settings' ] );
		add_action( 'admin_post_headlesswp_import_settings',  [ $this, 'handle_import_settings' ] );
		add_action( 'admin_post_headlesswp_reset_settings',   [ $this, 'handle_reset_settings' ] );
	}

	/**
	 * Register the Settings > HeadlessWP submenu.
	 */
	public function add_menu_page(): void {
		add_options_page(
			__( 'HeadlessWP Settings', 'headlesswp' ),
			__( 'HeadlessWP', 'headlesswp' ),
			'manage_options',
			'headlesswp',
			[ $this, 'render_settings_page' ]
		);
	}

	/**
	 * Enqueue admin CSS and JS only on the plugin page.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( string $hook ): void {
		// Dashboard widget assets.
		if ( 'index.php' === $hook ) {
			wp_enqueue_style(
				'headlesswp-admin',
				HEADLESSWP_PLUGIN_URL . 'assets/css/admin.css',
				[],
				HEADLESSWP_VERSION
			);
			return;
		}

		if ( 'settings_page_headlesswp' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'headlesswp-admin',
			HEADLESSWP_PLUGIN_URL . 'assets/css/admin.css',
			[],
			HEADLESSWP_VERSION
		);

		wp_enqueue_script(
			'headlesswp-admin',
			HEADLESSWP_PLUGIN_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			HEADLESSWP_VERSION,
			true
		);

		wp_localize_script( 'headlesswp-admin', 'headlesswpAdmin', [
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'healthNonce' => wp_create_nonce( 'headlesswp_health_nonce' ),
			'i18n'        => [
				'checking'    => __( 'Checking…', 'headlesswp' ),
				'runCheck'    => __( 'Run Check', 'headlesswp' ),
				'pass'        => __( 'Pass', 'headlesswp' ),
				'fail'        => __( 'Fail', 'headlesswp' ),
				'info'        => __( 'Info', 'headlesswp' ),
				'clearCache'  => __( 'Clear Cache', 'headlesswp' ),
				'cacheCleared'=> __( 'Cache cleared.', 'headlesswp' ),
				'error'       => __( 'An error occurred.', 'headlesswp' ),
			],
		] );
	}

	/**
	 * Show an admin notice when headless mode is active without a frontend URL.
	 */
	public function maybe_show_notices(): void {
		$screen = get_current_screen();
		if ( ! $screen || 'settings_page_headlesswp' === $screen->id ) {
			return;
		}

		if ( $this->settings->is_headless() && empty( $this->settings->frontend_url() ) ) {
			echo '<div class="notice notice-warning is-dismissible"><p>';
			echo wp_kses_post( sprintf(
				/* translators: %s: link to settings page */
				__( '<strong>HeadlessWP:</strong> Headless mode is active but no Frontend URL is configured. <a href="%s">Configure now</a>.', 'headlesswp' ),
				esc_url( admin_url( 'options-general.php?page=headlesswp' ) )
			) );
			echo '</p></div>';
		}
	}

	/**
	 * Add "Settings" link on the Plugins list page.
	 *
	 * @param array $links Existing action links.
	 * @return array
	 */
	public function add_action_links( array $links ): array {
		$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=headlesswp' ) ) . '">'
			. esc_html__( 'Settings', 'headlesswp' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Render the main settings page.
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = $this->settings;
		$health   = $this->health;
		include HEADLESSWP_PLUGIN_DIR . 'templates/admin-page.php';
	}

	// -------------------------------------------------------------------------
	// Tool handlers
	// -------------------------------------------------------------------------

	/**
	 * Flush rewrite rules.
	 */
	public function handle_flush_permalinks(): void {
		check_admin_referer( 'headlesswp_tools_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'headlesswp' ) );
		}

		flush_rewrite_rules();
		wp_safe_redirect( add_query_arg( [ 'page' => 'headlesswp', 'tab' => 'tools', 'flushed' => '1' ], admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * Export all plugin settings as a JSON file download.
	 */
	public function handle_export_settings(): void {
		check_admin_referer( 'headlesswp_tools_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'headlesswp' ) );
		}

		$export = [
			'headlesswp_version'                 => HEADLESSWP_VERSION,
			'headlesswp_enabled'                 => $this->settings->get( 'headlesswp_enabled' ),
			'headlesswp_frontend_url'            => $this->settings->get( 'headlesswp_frontend_url' ),
			'headlesswp_noindex'                 => $this->settings->get( 'headlesswp_noindex' ),
			'headlesswp_preserve_slugs'          => $this->settings->get( 'headlesswp_preserve_slugs' ),
			'headlesswp_disable_rss'             => $this->settings->get( 'headlesswp_disable_rss' ),
			'headlesswp_disable_search'          => $this->settings->get( 'headlesswp_disable_search' ),
			'headlesswp_disable_comments'        => $this->settings->get( 'headlesswp_disable_comments' ),
			'headlesswp_disable_author_archives' => $this->settings->get( 'headlesswp_disable_author_archives' ),
			'headlesswp_disable_date_archives'   => $this->settings->get( 'headlesswp_disable_date_archives' ),
			'headlesswp_allowed_origins'         => $this->settings->get( 'headlesswp_allowed_origins' ),
			'headlesswp_maintenance_mode'        => $this->settings->get( 'headlesswp_maintenance_mode' ),
			'headlesswp_xmlrpc_enabled'          => $this->settings->get( 'headlesswp_xmlrpc_enabled' ),
			'headlesswp_robots_txt'              => $this->settings->get( 'headlesswp_robots_txt' ),
		];

		nocache_headers();
		header( 'Content-Type: application/json' );
		header( 'Content-Disposition: attachment; filename="headlesswp-settings-' . gmdate( 'Y-m-d' ) . '.json"' );
		echo wp_json_encode( $export, JSON_PRETTY_PRINT );
		exit;
	}

	/**
	 * Import settings from an uploaded JSON file.
	 */
	public function handle_import_settings(): void {
		check_admin_referer( 'headlesswp_tools_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'headlesswp' ) );
		}

		if ( empty( $_FILES['headlesswp_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( [ 'page' => 'headlesswp', 'tab' => 'tools', 'import_error' => '1' ], admin_url( 'options-general.php' ) ) );
			exit;
		}

		$file_path = sanitize_text_field( wp_unslash( $_FILES['headlesswp_import_file']['tmp_name'] ) );
		$content   = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data      = json_decode( $content, true );

		if ( ! is_array( $data ) ) {
			wp_safe_redirect( add_query_arg( [ 'page' => 'headlesswp', 'tab' => 'tools', 'import_error' => '1' ], admin_url( 'options-general.php' ) ) );
			exit;
		}

		$allowed_keys = [
			'headlesswp_enabled', 'headlesswp_frontend_url', 'headlesswp_noindex',
			'headlesswp_preserve_slugs', 'headlesswp_disable_rss', 'headlesswp_disable_search',
			'headlesswp_disable_comments', 'headlesswp_disable_author_archives',
			'headlesswp_disable_date_archives', 'headlesswp_allowed_origins',
			'headlesswp_maintenance_mode', 'headlesswp_xmlrpc_enabled', 'headlesswp_robots_txt',
		];

		foreach ( $allowed_keys as $key ) {
			if ( isset( $data[ $key ] ) ) {
				update_option( $key, sanitize_text_field( $data[ $key ] ) );
			}
		}

		wp_safe_redirect( add_query_arg( [ 'page' => 'headlesswp', 'tab' => 'tools', 'imported' => '1' ], admin_url( 'options-general.php' ) ) );
		exit;
	}

	/**
	 * Reset all settings to their defaults.
	 */
	public function handle_reset_settings(): void {
		check_admin_referer( 'headlesswp_tools_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'headlesswp' ) );
		}

		$keys = [
			'headlesswp_enabled', 'headlesswp_frontend_url', 'headlesswp_noindex',
			'headlesswp_preserve_slugs', 'headlesswp_disable_rss', 'headlesswp_disable_search',
			'headlesswp_disable_comments', 'headlesswp_disable_author_archives',
			'headlesswp_disable_date_archives', 'headlesswp_allowed_origins',
			'headlesswp_maintenance_mode', 'headlesswp_xmlrpc_enabled', 'headlesswp_robots_txt',
		];

		foreach ( $keys as $key ) {
			delete_option( $key );
		}

		// Re-run activation to restore defaults.
		require_once HEADLESSWP_PLUGIN_DIR . 'includes/class-activator.php';
		Activator::activate();

		wp_safe_redirect( add_query_arg( [ 'page' => 'headlesswp', 'reset' => '1' ], admin_url( 'options-general.php' ) ) );
		exit;
	}
}
