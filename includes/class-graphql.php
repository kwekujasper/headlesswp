<?php
/**
 * Exposes select HeadlessWP settings over WPGraphQL so the frontend can
 * read them at request time instead of needing a duplicated, manually
 * synced config value.
 *
 * @package HeadlessWP
 */

namespace HeadlessWP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Graphql
 */
class Graphql {

	public function __construct( private Settings $settings ) {}

	public function register_hooks(): void {
		add_action( 'graphql_register_types', [ $this, 'register_fields' ] );
	}

	/**
	 * Adds a postPathPrefix field to the GeneralSettings GraphQL type.
	 * No-ops if WPGraphQL isn't active.
	 */
	public function register_fields(): void {
		if ( ! function_exists( 'register_graphql_field' ) ) {
			return;
		}

		register_graphql_field(
			'GeneralSettings',
			'postPathPrefix',
			[
				'type'        => 'String',
				'description' => __( 'Path segment HeadlessWP prepends to single-post redirects (e.g. "post"), or empty for root-level post URLs.', 'headlesswp' ),
				'resolve'     => fn() => $this->settings->post_path_prefix(),
			]
		);
	}
}
