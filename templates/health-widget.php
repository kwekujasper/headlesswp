<?php
/**
 * Health check results partial — used in dashboard widget and the Health tab.
 *
 * Expects: $results array from Health::get_cached_results() or Health::run_checks().
 *
 * @package HeadlessWP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$check_labels = [
	'wp_api'   => __( 'WordPress REST API', 'headlesswp' ),
	'graphql'  => __( 'GraphQL Endpoint', 'headlesswp' ),
	'frontend' => __( 'Frontend Reachability', 'headlesswp' ),
	'cors'     => __( 'CORS Configuration', 'headlesswp' ),
	'plugin'   => __( 'Plugin Status', 'headlesswp' ),
];
?>
<div class="headlesswp-health-grid">
	<?php foreach ( $check_labels as $key => $label ) :
		if ( ! isset( $results[ $key ] ) ) continue;
		$check  = $results[ $key ];
		$ok     = $check['ok'];
		$detail = $check['detail'];

		if ( true === $ok ) {
			$status_class = 'headlesswp-status--pass';
			$status_icon  = '✓';
			$status_text  = __( 'Pass', 'headlesswp' );
		} elseif ( false === $ok ) {
			$status_class = 'headlesswp-status--fail';
			$status_icon  = '✗';
			$status_text  = __( 'Fail', 'headlesswp' );
		} else {
			$status_class = 'headlesswp-status--info';
			$status_icon  = '●';
			$status_text  = __( 'Info', 'headlesswp' );
		}
	?>
	<div class="headlesswp-health-item">
		<span class="headlesswp-health-label"><?php echo esc_html( $label ); ?></span>
		<span class="headlesswp-health-status <?php echo esc_attr( $status_class ); ?>">
			<span class="headlesswp-status-icon" aria-hidden="true"><?php echo esc_html( $status_icon ); ?></span>
			<?php echo esc_html( $status_text ); ?>
		</span>
		<span class="headlesswp-health-detail"><?php echo esc_html( $detail ); ?></span>
	</div>
	<?php endforeach; ?>
</div>
<?php if ( ! empty( $results['checked_at'] ) ) : ?>
<p class="headlesswp-health-timestamp">
	<?php
	printf(
		/* translators: %s: datetime string */
		esc_html__( 'Last checked: %s', 'headlesswp' ),
		esc_html( $results['checked_at'] )
	);
	?>
</p>
<?php endif; ?>
