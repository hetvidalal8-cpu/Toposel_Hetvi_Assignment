<?php
/**
 * Plugin Name:       BuyMore Explore Dashboard
 * Plugin URI:       https://github.com/hetvidalal8-cpu/Hetvi-Assessment1/tree/main/buymore-explore
 * Description:       Renders the BuyMore "Explore" commerce dashboard as a fully responsive, CMS-driven page. Built with plain PHP, HTML, CSS and JavaScript — no page builder.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Hetvi Dalal
 * Text Domain:       buymore-explore
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

define( 'BUYMORE_VERSION', '1.0.0' );
define( 'BUYMORE_FILE', __FILE__ );
define( 'BUYMORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BUYMORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Class files, loaded in dependency order.
 *
 * Kept as an explicit list rather than an autoloader so the load order is
 * obvious to the next developer reading the plugin.
 */
require_once BUYMORE_DIR . 'includes/functions-helpers.php';
require_once BUYMORE_DIR . 'includes/class-buymore-post-types.php';
require_once BUYMORE_DIR . 'includes/class-buymore-meta-boxes.php';
require_once BUYMORE_DIR . 'includes/class-buymore-settings.php';
require_once BUYMORE_DIR . 'includes/class-buymore-menu-walker.php';
require_once BUYMORE_DIR . 'includes/class-buymore-query.php';
require_once BUYMORE_DIR . 'includes/class-buymore-template.php';
require_once BUYMORE_DIR . 'includes/class-buymore-assets.php';
require_once BUYMORE_DIR . 'includes/class-buymore-ajax.php';
require_once BUYMORE_DIR . 'includes/class-buymore-shortcode.php';
require_once BUYMORE_DIR . 'includes/class-buymore-installer.php';
require_once BUYMORE_DIR . 'includes/class-buymore-plugin.php';

/**
 * Boot the plugin.
 *
 * @return BuyMore_Plugin
 */
function buymore(): BuyMore_Plugin {
	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new BuyMore_Plugin();
		$plugin->register();
	}

	return $plugin;
}
add_action( 'plugins_loaded', 'buymore' );

register_activation_hook( __FILE__, array( 'BuyMore_Installer', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BuyMore_Installer', 'deactivate' ) );
