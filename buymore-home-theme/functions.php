<?php
/**
 * Simple theme helpers for the BuyMore Explore Dashboard integration.
 *
 * This file does three small jobs:
 * 1. Loads the theme CSS file.
 * 2. Creates a WordPress Home page with the plugin shortcode.
 * 3. Shows an admin message if the required plugin is not active.
 *
 * @package BuyMore_Home
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue the base theme stylesheet.
 *
 * @return void
 */
function buymore_home_enqueue_assets() {
	// get_stylesheet_uri() points to this theme's style.css file.
	wp_enqueue_style(
		'buymore-home',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'buymore_home_enqueue_assets' );

/**
 * Create a homepage with the plugin shortcode and make it the site front page.
 * The page intentionally remains usable after the plugin is enabled later.
 *
 * @return void
 */
function buymore_home_setup_front_page() {
	// Look for a page whose URL is /home first. This avoids duplicate pages.
	$page = get_page_by_path( 'home' );

	if ( ! $page ) {
		// The shortcode is saved as normal page content. The plugin renders it.
		$page_id = wp_insert_post(
			array(
				'post_title'   => __( 'Home', 'buymore-home' ),
				'post_name'    => 'home',
				'post_content' => '[buymore_dashboard]',
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);
	} else {
		// Reuse the existing Home page if it was already created.
		$page_id = (int) $page->ID;
	}

	if ( $page_id > 0 ) {
		// Tell WordPress to use this page as the website homepage.
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_id );
	}
}
add_action( 'after_switch_theme', 'buymore_home_setup_front_page' );

/**
 * Explain the required plugin to administrators until it is active.
 *
 * @return void
 */

function buymore_home_plugin_notice() {
	// BUYMORE_VERSION is only defined after the plugin has loaded.
	if ( defined( 'BUYMORE_VERSION' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'BuyMore Home needs the BuyMore Explore Dashboard plugin. Copy the included buymore-explore folder into wp-content/plugins, then activate it from Plugins.', 'buymore-home' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'buymore_home_plugin_notice' );
