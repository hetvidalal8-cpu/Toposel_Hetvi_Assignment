<?php
/**
 * Nav menu walker that renders the sidebar rows with icons.
 *
 * Editors pick an icon by adding a CSS class such as "icon-explore" to the
 * menu item in Appearance -> Menus, which avoids a second content model just
 * for navigation.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Sidebar menu walker.
 */
class BuyMore_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Start one menu row.
	 *
	 * @param string   $output Walker output, passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Menu args.
	 * @param int      $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$classes = (array) ( $item->classes ?? array() );
		$icon    = 'grid';

		foreach ( $classes as $class ) {
			if ( 0 === strpos( (string) $class, 'icon-' ) ) {
				$icon = substr( (string) $class, 5 );
				break;
			}
		}

		$is_active = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'is-active', $classes, true );

		$output .= sprintf(
			'<li class="bm-nav__row"><a class="bm-nav__link%1$s" href="%2$s"%3$s>%4$s<span class="bm-nav__label">%5$s</span></a></li>',
			$is_active ? ' is-active' : '',
			esc_url( $item->url ? $item->url : '#' ),
			$is_active ? ' aria-current="page"' : '',
			buymore_icon( sanitize_key( $icon ), 18 ),
			esc_html( $item->title )
		);
	}

	/**
	 * Close one menu row.
	 *
	 * @param string   $output Walker output, passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Menu args.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName
		$output .= '';
	}
}
