<?php
/**
 * Admin settings page template.
 *
 * @package HeadlessWP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Resolve active tab.
$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification

$tabs = [
	'general'  => __( 'General', 'headlesswp' ),
	'seo'      => __( 'SEO', 'headlesswp' ),
	'features' => __( 'Features', 'headlesswp' ),
	'api'      => __( 'API & CORS', 'headlesswp' ),
	'health'   => __( 'Health', 'headlesswp' ),
	'tools'    => __( 'Tools', 'headlesswp' ),
];

// Notices.
if ( isset( $_GET['flushed'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Permalink structure flushed.', 'headlesswp' ) . '</p></div>';
endif;
if ( isset( $_GET['imported'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings imported successfully.', 'headlesswp' ) . '</p></div>';
endif;
if ( isset( $_GET['import_error'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification
	echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Import failed. Please upload a valid HeadlessWP JSON file.', 'headlesswp' ) . '</p></div>';
endif;
if ( isset( $_GET['reset'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings have been reset to defaults.', 'headlesswp' ) . '</p></div>';
endif;
if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'headlesswp' ) . '</p></div>';
endif;
?>

<div class="wrap headlesswp-wrap">

	<div class="headlesswp-header">
		<div class="headlesswp-header-inner">
			<span class="headlesswp-logo">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" width="36" height="36" aria-hidden="true">
					<circle cx="18" cy="18" r="18" fill="#1a1a2e"/>
					<path d="M10 12 L18 8 L26 12 L26 24 L18 28 L10 24 Z" fill="none" stroke="#4f46e5" stroke-width="2"/>
					<line x1="18" y1="8" x2="18" y2="28" stroke="#4f46e5" stroke-width="1" stroke-dasharray="2,2"/>
					<circle cx="18" cy="18" r="3" fill="#4f46e5"/>
				</svg>
			</span>
			<h1><?php esc_html_e( 'HeadlessWP by KJM', 'headlesswp' ); ?></h1>
			<span class="headlesswp-version">v<?php echo esc_html( HEADLESSWP_VERSION ); ?></span>
			<?php if ( $settings->is_headless() ) : ?>
				<span class="headlesswp-badge headlesswp-badge--active"><?php esc_html_e( 'Headless Active', 'headlesswp' ); ?></span>
			<?php else : ?>
				<span class="headlesswp-badge headlesswp-badge--inactive"><?php esc_html_e( 'Headless Inactive', 'headlesswp' ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<nav class="headlesswp-nav-tab-wrapper nav-tab-wrapper">
		<?php foreach ( $tabs as $slug => $label ) : ?>
			<a href="<?php echo esc_url( admin_url( 'options-general.php?page=headlesswp&tab=' . $slug ) ); ?>"
			   class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<div class="headlesswp-tab-content">

		<?php if ( 'general' === $active_tab ) : ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'headlesswp_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Headless Mode', 'headlesswp' ); ?></th>
					<td>
						<label class="headlesswp-toggle">
							<input type="checkbox" name="headlesswp_enabled" value="1"
								<?php checked( $settings->get( 'headlesswp_enabled' ), '1' ); ?> />
							<span class="headlesswp-toggle__slider"></span>
						</label>
						<p class="description"><?php esc_html_e( 'Redirect all frontend requests to the external frontend. API, admin, and AJAX endpoints are always preserved.', 'headlesswp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="headlesswp_frontend_url"><?php esc_html_e( 'Frontend URL', 'headlesswp' ); ?></label>
					</th>
					<td>
						<input type="url" id="headlesswp_frontend_url" name="headlesswp_frontend_url"
							value="<?php echo esc_attr( $settings->get( 'headlesswp_frontend_url' ) ); ?>"
							class="regular-text" placeholder="https://plus233.com" />
						<p class="description"><?php esc_html_e( 'The URL of your Next.js, Nuxt, Astro, or other frontend. Example: https://plus233.com', 'headlesswp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Preserve Slugs', 'headlesswp' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="headlesswp_preserve_slugs" value="1"
								<?php checked( $settings->get( 'headlesswp_preserve_slugs', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Append request path to frontend URL on redirect.', 'headlesswp' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Example: /my-post on WordPress redirects to plus233.com/my-post', 'headlesswp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="headlesswp_post_path_prefix"><?php esc_html_e( 'Post Path Prefix', 'headlesswp' ); ?></label>
					</th>
					<td>
						<input type="text" id="headlesswp_post_path_prefix" name="headlesswp_post_path_prefix"
							value="<?php echo esc_attr( $settings->get( 'headlesswp_post_path_prefix' ) ); ?>"
							class="regular-text" placeholder="post" />
						<p class="description"><?php esc_html_e( 'Optional path segment prepended to single-post redirects. Leave blank to redirect straight to the frontend root (e.g. plus233.com/my-post). Set to "post" to redirect to plus233.com/post/my-post instead. Only applies when Preserve Slugs is enabled.', 'headlesswp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Maintenance Mode', 'headlesswp' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="headlesswp_maintenance_mode" value="1"
								<?php checked( $settings->get( 'headlesswp_maintenance_mode' ), '1' ); ?> />
							<?php esc_html_e( 'Show maintenance page instead of redirecting.', 'headlesswp' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Use when your frontend is temporarily down to avoid redirect loops.', 'headlesswp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'XML-RPC', 'headlesswp' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="headlesswp_xmlrpc_enabled" value="1"
								<?php checked( $settings->get( 'headlesswp_xmlrpc_enabled', '1' ), '1' ); ?> />
							<?php esc_html_e( 'Keep XML-RPC enabled (recommended for Jetpack / mobile apps).', 'headlesswp' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save General Settings', 'headlesswp' ) ); ?>
		</form>

		<?php elseif ( 'seo' === $active_tab ) : ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'headlesswp_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Add Noindex Header', 'headlesswp' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="headlesswp_noindex" value="1"
								<?php checked( $settings->get( 'headlesswp_noindex' ), '1' ); ?> />
							<?php esc_html_e( 'Send X-Robots-Tag: noindex, nofollow on all WordPress responses.', 'headlesswp' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Prevents search engines from indexing the WordPress backend URL, since content is served by the frontend.', 'headlesswp' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Override robots.txt', 'headlesswp' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="headlesswp_robots_txt" value="1"
								<?php checked( $settings->get( 'headlesswp_robots_txt' ), '1' ); ?> />
							<?php esc_html_e( 'Replace WordPress robots.txt with "Disallow: /" and a sitemap pointer to the frontend.', 'headlesswp' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save SEO Settings', 'headlesswp' ) ); ?>
		</form>

		<?php elseif ( 'features' === $active_tab ) : ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'headlesswp_settings' ); ?>
			<p class="description" style="margin-bottom:16px"><?php esc_html_e( 'These toggles are applied only when Headless Mode is active.', 'headlesswp' ); ?></p>
			<table class="form-table" role="presentation">
				<?php
				$feature_options = [
					'headlesswp_disable_rss'             => __( 'Disable RSS / Feeds', 'headlesswp' ),
					'headlesswp_disable_search'          => __( 'Disable Frontend Search', 'headlesswp' ),
					'headlesswp_disable_comments'        => __( 'Disable Comments', 'headlesswp' ),
					'headlesswp_disable_author_archives' => __( 'Disable Author Archives', 'headlesswp' ),
					'headlesswp_disable_date_archives'   => __( 'Disable Date Archives', 'headlesswp' ),
				];
				foreach ( $feature_options as $key => $label ) :
				?>
				<tr>
					<th scope="row"><?php echo esc_html( $label ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="1"
								<?php checked( $settings->get( $key ), '1' ); ?> />
							<?php esc_html_e( 'Enable', 'headlesswp' ); ?>
						</label>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button( __( 'Save Feature Settings', 'headlesswp' ) ); ?>
		</form>

		<?php elseif ( 'api' === $active_tab ) : ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'headlesswp_settings' ); ?>
			<h2><?php esc_html_e( 'CORS Settings', 'headlesswp' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="headlesswp_allowed_origins"><?php esc_html_e( 'Allowed Origins', 'headlesswp' ); ?></label>
					</th>
					<td>
						<textarea id="headlesswp_allowed_origins" name="headlesswp_allowed_origins"
							rows="6" class="large-text code"
							placeholder="https://plus233.com&#10;https://app.plus233.com"><?php echo esc_textarea( $settings->get( 'headlesswp_allowed_origins' ) ); ?></textarea>
						<p class="description"><?php esc_html_e( 'One URL per line. These domains will receive Access-Control-Allow-Origin headers. Enter * on its own line to allow all origins (not recommended for production).', 'headlesswp' ); ?></p>
					</td>
				</tr>
			</table>
			<div class="headlesswp-info-box">
				<strong><?php esc_html_e( 'Headers sent for allowed origins:', 'headlesswp' ); ?></strong>
				<pre>Access-Control-Allow-Origin: &lt;origin&gt;
Access-Control-Allow-Credentials: true
Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS
Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce, X-Requested-With</pre>
			</div>
			<?php submit_button( __( 'Save CORS Settings', 'headlesswp' ) ); ?>
		</form>

		<?php elseif ( 'health' === $active_tab ) : ?>
		<div class="headlesswp-health">
			<p><?php esc_html_e( 'Health checks verify that WordPress API endpoints and your frontend are reachable. Results are cached for 5 minutes.', 'headlesswp' ); ?></p>
			<p>
				<button type="button" id="headlesswp-run-check" class="button button-primary">
					<?php esc_html_e( 'Run Check', 'headlesswp' ); ?>
				</button>
				<button type="button" id="headlesswp-clear-cache" class="button button-secondary">
					<?php esc_html_e( 'Clear Cache', 'headlesswp' ); ?>
				</button>
			</p>
			<div id="headlesswp-health-results">
				<?php
				$results = $health->get_cached_results();
				include HEADLESSWP_PLUGIN_DIR . 'templates/health-widget.php';
				?>
			</div>
		</div>

		<?php elseif ( 'tools' === $active_tab ) : ?>
		<div class="headlesswp-tools">

			<div class="headlesswp-tool-card">
				<h3><?php esc_html_e( 'Test Frontend', 'headlesswp' ); ?></h3>
				<p><?php esc_html_e( 'Open your configured frontend URL in a new tab.', 'headlesswp' ); ?></p>
				<?php $frontend_url = $settings->frontend_url(); ?>
				<?php if ( $frontend_url ) : ?>
					<a href="<?php echo esc_url( $frontend_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
						<?php esc_html_e( 'Open Frontend', 'headlesswp' ); ?>
					</a>
				<?php else : ?>
					<p class="description"><?php esc_html_e( 'No frontend URL configured.', 'headlesswp' ); ?></p>
				<?php endif; ?>
			</div>

			<div class="headlesswp-tool-card">
				<h3><?php esc_html_e( 'Flush Permalinks', 'headlesswp' ); ?></h3>
				<p><?php esc_html_e( 'Regenerate WordPress rewrite rules. Run after changing settings.', 'headlesswp' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="headlesswp_flush_permalinks" />
					<?php wp_nonce_field( 'headlesswp_tools_nonce' ); ?>
					<?php submit_button( __( 'Flush Permalinks', 'headlesswp' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>

			<div class="headlesswp-tool-card">
				<h3><?php esc_html_e( 'Export Settings', 'headlesswp' ); ?></h3>
				<p><?php esc_html_e( 'Download all plugin settings as a JSON file.', 'headlesswp' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="headlesswp_export_settings" />
					<?php wp_nonce_field( 'headlesswp_tools_nonce' ); ?>
					<?php submit_button( __( 'Export Settings', 'headlesswp' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>

			<div class="headlesswp-tool-card">
				<h3><?php esc_html_e( 'Import Settings', 'headlesswp' ); ?></h3>
				<p><?php esc_html_e( 'Upload a HeadlessWP JSON settings file to restore settings.', 'headlesswp' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
					<input type="hidden" name="action" value="headlesswp_import_settings" />
					<?php wp_nonce_field( 'headlesswp_tools_nonce' ); ?>
					<input type="file" name="headlesswp_import_file" accept=".json" style="margin-bottom:8px;display:block;" />
					<?php submit_button( __( 'Import Settings', 'headlesswp' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>

			<div class="headlesswp-tool-card headlesswp-tool-card--danger">
				<h3><?php esc_html_e( 'Reset Settings', 'headlesswp' ); ?></h3>
				<p><?php esc_html_e( 'Restore all HeadlessWP settings to their factory defaults. This cannot be undone.', 'headlesswp' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
					onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure? This will reset all HeadlessWP settings to defaults.', 'headlesswp' ) ); ?>')">
					<input type="hidden" name="action" value="headlesswp_reset_settings" />
					<?php wp_nonce_field( 'headlesswp_tools_nonce' ); ?>
					<?php submit_button( __( 'Reset Settings', 'headlesswp' ), 'delete', 'submit', false ); ?>
				</form>
			</div>

		</div>
		<?php endif; ?>

	</div><!-- .headlesswp-tab-content -->

	<div class="headlesswp-footer">
		<p>
			<?php
			printf(
				/* translators: 1: plugin name, 2: author link */
				esc_html__( '%1$s — crafted by %2$s', 'headlesswp' ),
				'<strong>HeadlessWP by KJM</strong>',
				'<a href="https://kwekujasper.com" target="_blank" rel="noopener noreferrer">Kweku Jasper Media</a>'
			);
			?>
		</p>
	</div>

</div><!-- .headlesswp-wrap -->
