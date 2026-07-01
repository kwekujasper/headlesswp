=== HeadlessWP by KJM ===
Contributors: Kweku Jasper Media
Tags: headless, rest-api, graphql, cors, redirect, headless-cms
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transform WordPress into a secure, configurable headless CMS for any modern frontend framework.

== Description ==

**HeadlessWP by KJM** is a production-ready plugin that converts WordPress into a powerful headless CMS while preserving full access to the REST API, GraphQL, admin, AJAX, and cron endpoints.

= Key Features =

* **Headless Mode** — Redirect all frontend traffic to your external frontend (Next.js, Nuxt, Astro, SvelteKit, Gatsby, etc.)
* **Slug Preservation** — `/my-post` on WordPress redirects to `yourfrontend.com/my-post`
* **SEO Protection** — `X-Robots-Tag: noindex, nofollow` header + optional robots.txt override
* **CORS Management** — Configure allowed origins with fine-grained `Access-Control-*` headers
* **Feature Toggles** — Disable RSS, search, comments, author archives, date archives
* **Maintenance Mode** — Show a branded maintenance page when the frontend is unavailable
* **Health Checker** — Dashboard widget that verifies REST API, GraphQL, frontend reachability, and CORS configuration
* **Settings Import/Export** — Back up and restore your configuration as JSON
* **Security Headers** — `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`

= Compatible Frameworks =

* Next.js
* Nuxt
* Astro
* SvelteKit
* Gatsby
* React (any host)
* Mobile applications

= Protected Endpoints (always allowed) =

`/wp-json/*`, `/graphql`, `/wp-admin/*`, `/wp-login.php`, `/wp-cron.php`, `/admin-ajax.php`, `/wp-content/*`, `/wp-includes/*`

== Installation ==

1. Upload the `headlesswp` folder to `/wp-content/plugins/`.
2. Activate the plugin through **Plugins > Installed Plugins**.
3. Go to **Settings > HeadlessWP**.
4. Enter your **Frontend URL** (e.g. `https://yoursite.com`).
5. Enable **Headless Mode**.
6. Optionally configure CORS origins, disable features, and run a health check.

== Frequently Asked Questions ==

= Will this break my REST API? =

No. REST API endpoints (`/wp-json/*`) are always allowed through regardless of headless mode status.

= Does it work with WPGraphQL? =

Yes. The `/graphql` endpoint is preserved. The health checker will also verify GraphQL availability.

= Can I use this on a multisite? =

Multisite support is planned for v1.1. Single-site only for v1.0.

= What happens if my frontend goes down? =

Enable **Maintenance Mode** in General settings. Visitors will see a branded maintenance page instead of a redirect loop.

= Is XML-RPC affected? =

By default XML-RPC remains enabled (useful for Jetpack and mobile apps). You can optionally disable it under General settings.

== Screenshots ==

1. General Settings — toggle headless mode and set frontend URL.
2. API & CORS — configure allowed origins.
3. Features — disable RSS, search, comments, and archives.
4. Health Checker — live status of all endpoints.
5. Tools — export, import, flush, and reset.

== Changelog ==

= 1.0.0 =
* Initial release.
* Headless mode with slug-preserving redirects.
* CORS header management.
* Health checker (REST API, GraphQL, frontend, CORS, plugin).
* Feature toggles (RSS, search, comments, archives).
* Maintenance mode.
* Settings export / import / reset.
* Security headers.
* Translation-ready (POT file included).

== Upgrade Notice ==

= 1.0.0 =
Initial release.
