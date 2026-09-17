<?php
/**
 * Plugin container: wires every component to WordPress.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Registers all plugin components.
 */
final class BuyMore_Plugin {

	/**
	 * Component instances, keyed by short name.
	 *
	 * @var array<string,object>
	 */
	private array $components = array();

	/**
	 * Instantiate and register every component.
	 *
	 * @return void
	 */
	public function register(): void {
		load_plugin_textdomain( 'buymore-explore', false, dirname( plugin_basename( BUYMORE_FILE ) ) . '/languages' );

		$this->components = array(
			'post_types' => new BuyMore_Post_Types(),
			'meta_boxes' => new BuyMore_Meta_Boxes(),
			'settings'   => new BuyMore_Settings(),
			'assets'     => new BuyMore_Assets(),
			'ajax'       => new BuyMore_Ajax(),
			'shortcode'  => new BuyMore_Shortcode(),
		);

		foreach ( $this->components as $component ) {
			if ( method_exists( $component, 'hooks' ) ) {
				$component->hooks();
			}
		}

		add_action( 'after_setup_theme', array( $this, 'register_menus' ) );
	}

	/**
	 * Two editable menu locations power the sidebar, so navigation is managed
	 * in Appearance -> Menus rather than hard-coded.
	 *
	 * @return void
	 */
	public function register_menus(): void {
		register_nav_menus(
			array(
				'buymore_sidebar'       => __( 'BuyMore — sidebar navigation', 'buymore-explore' ),
				'buymore_quick_actions' => __( 'BuyMore — quick actions', 'buymore-explore' ),
			)
		);
	}

	/**
	 * Fetch a registered component.
	 *
	 * @param string $key Component key.
	 * @return object|null
	 */
	public function get( string $key ) {
		return $this->components[ $key ] ?? null;
	}
}
