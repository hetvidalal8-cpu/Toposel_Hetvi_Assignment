<?php
/**
 * Site homepage. The BuyMore plugin supplies the dashboard and its assets.
 *
 * @package BuyMore_Home
 */

defined( 'ABSPATH' ) || exit;

get_header();

// The plugin registers this shortcode. When active, it outputs the full dashboard.
if ( shortcode_exists( 'buymore_dashboard' ) ) {
	echo do_shortcode( '[buymore_dashboard]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shortcode controls its markup.
} elseif ( current_user_can( 'activate_plugins' ) ) {
	// Administrators get installation guidance instead of a blank homepage.
	?>
	<main class="buymore-home-message">
		<h1><?php esc_html_e( 'Activate BuyMore Explore Dashboard', 'buymore-home' ); ?></h1>
		<p><?php esc_html_e( 'Install and activate the included buymore-explore plugin to show the dashboard on this homepage.', 'buymore-home' ); ?></p>
	</main>
	<?php
} else {
	?>
	<main class="buymore-home-message">
		<h1><?php esc_html_e( 'Dashboard unavailable', 'buymore-home' ); ?></h1>
	</main>
	<?php
}

get_footer();
