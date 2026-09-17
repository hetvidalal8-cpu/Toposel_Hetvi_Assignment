<?php
/**
 * Removes plugin data when the plugin is deleted from the Plugins screen.
 *
 * Content is deliberately kept: deleting a plugin should not destroy an
 * editor's products. Only the plugin's own options are dropped.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'buymore_settings' );
delete_option( 'buymore_seeded' );
delete_option( 'buymore_page_id' );
