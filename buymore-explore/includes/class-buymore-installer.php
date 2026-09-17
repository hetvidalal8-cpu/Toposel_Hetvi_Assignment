<?php
/**
 * Activation routine: demo content, menus and a host page.
 *
 * Seeding runs once and is guarded by an option, so deactivating and
 * reactivating the plugin never duplicates content.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Install and uninstall tasks.
 */
class BuyMore_Installer {

	const SEED_FLAG = 'buymore_seeded';
	const PAGE_ID   = 'buymore_page_id';

	/**
	 * Run on activation.
	 *
	 * @return void
	 */
	public static function activate(): void {
		$post_types = new BuyMore_Post_Types();
		$post_types->register_post_types();
		$post_types->register_taxonomy();

		add_option( BuyMore_Settings::OPTION_KEY, BuyMore_Settings::defaults() );

		if ( ! get_option( self::SEED_FLAG ) ) {
			self::seed_terms();
			self::seed_cards();
			self::seed_products();
			self::seed_orders();
			self::seed_menus();
			self::seed_page();

			update_option( self::SEED_FLAG, BUYMORE_VERSION );
		}

		flush_rewrite_rules();
	}

	/**
	 * Run on deactivation.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}

	/**
	 * Audience terms used by the filter tabs.
	 *
	 * @return void
	 */
	private static function seed_terms(): void {
		foreach ( array( 'Men', 'Women' ) as $name ) {
			if ( ! term_exists( $name, BuyMore_Post_Types::AUDIENCE ) ) {
				wp_insert_term( $name, BuyMore_Post_Types::AUDIENCE );
			}
		}
	}

	/**
	 * Insert a post with meta, skipping duplicates by title.
	 *
	 * @param string               $post_type Post type.
	 * @param string               $title     Post title.
	 * @param array<string,string> $meta      Meta values.
	 * @param int                  $order     Menu order.
	 * @param string[]             $audiences Audience term names.
	 * @return int Inserted post ID, or 0 on failure.
	 */
	private static function insert( string $post_type, string $title, array $meta, int $order = 0, array $audiences = array() ): int {
		$existing = get_page_by_path( sanitize_title( $title ), OBJECT, $post_type );

		if ( $existing ) {
			return (int) $existing->ID;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => $post_type,
				'post_title'  => $title,
				'post_name'   => sanitize_title( $title ),
				'post_status' => 'publish',
				'menu_order'  => $order,
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		foreach ( $meta as $key => $value ) {
			if ( '' !== $value ) {
				update_post_meta( (int) $post_id, $key, $value );
			}
		}

		if ( $audiences ) {
			wp_set_object_terms( (int) $post_id, $audiences, BuyMore_Post_Types::AUDIENCE );
		}

		return (int) $post_id;
	}

	/**
	 * The five layout cards from the reference design.
	 *
	 * @return void
	 */
	private static function seed_cards(): void {
		$cards = array(
			array(
				'title' => 'Get up to 50% off',
				'meta'  => array(
					'_buymore_slot'      => 'promo-a',
					'_buymore_style'     => 'promo',
					'_buymore_cta_label' => 'Get discount',
					'_buymore_bg'        => '#a9d86e',
					'_buymore_fg'        => '#17251a',
				),
			),
			array(
				'title' => "Winter's weekend",
				'meta'  => array(
					'_buymore_slot'     => 'promo-b',
					'_buymore_style'    => 'promo',
					'_buymore_subtitle' => 'keep it casual',
					'_buymore_bg'       => '#f6cf4f',
					'_buymore_fg'       => '#1d1a10',
				),
			),
			array(
				'title' => 'Everyday brights',
				'meta'  => array(
					'_buymore_slot'      => 'promo-c',
					'_buymore_style'     => 'feature',
					'_buymore_cta_label' => 'Shop the edit',
					'_buymore_bg'        => '#f2b9a0',
					'_buymore_fg'        => '#ffffff',
				),
			),
			array(
				'title' => 'Favourites',
				'meta'  => array(
					'_buymore_slot'      => 'favourites',
					'_buymore_style'     => 'favourites',
					'_buymore_cta_label' => 'See all',
					'_buymore_bg'        => '#ffffff',
					'_buymore_fg'        => '#16181d',
				),
			),
			array(
				'title' => 'Bring bold fashion',
				'meta'  => array(
					'_buymore_slot'     => 'feature',
					'_buymore_style'    => 'feature',
					'_buymore_subtitle' => 'Layers on layers',
					'_buymore_bg'       => '#3c3a35',
					'_buymore_fg'       => '#ffffff',
				),
			),
		);

		foreach ( $cards as $index => $card ) {
			self::insert( BuyMore_Post_Types::CARD, $card['title'], $card['meta'], $index * 10 );
		}
	}

	/**
	 * Products: the first two fill the large tiles, the rest the favourites strip.
	 *
	 * @return void
	 */
	private static function seed_products(): void {
		$products = array(
			array(
				'title'     => 'WMX Rubber zebra sandal',
				'audiences' => array( 'Women' ),
				'meta'      => array(
					'_buymore_eyebrow'  => 'Our picks',
					'_buymore_price'    => '36',
					'_buymore_swatches' => '#f4c8b4, #ffd84d, #1f1f1f',
					'_buymore_bg'       => '#f6ded5',
				),
			),
			array(
				'title'     => 'Supper skiny jogger in brown',
				'audiences' => array( 'Men' ),
				'meta'      => array(
					'_buymore_eyebrow'  => 'Your choice',
					'_buymore_price'    => '29',
					'_buymore_swatches' => '#ffd84d, #2b2b2b',
					'_buymore_bg'       => '#2b2b2b',
				),
			),
			array(
				'title'     => 'Knot headband in coral',
				'audiences' => array( 'Women' ),
				'meta'      => array(
					'_buymore_price' => '12',
					'_buymore_bg'    => '#f7c8a8',
				),
			),
			array(
				'title'     => 'Colour-block knit vest',
				'audiences' => array( 'Men' ),
				'meta'      => array(
					'_buymore_price' => '48',
					'_buymore_bg'    => '#cfe3b4',
				),
			),
			array(
				'title'     => 'Oversized hooded sweat',
				'audiences' => array( 'Men', 'Women' ),
				'meta'      => array(
					'_buymore_price' => '54',
					'_buymore_bg'    => '#e3ded6',
				),
			),
			array(
				'title'     => 'Slide sandal in blossom',
				'audiences' => array( 'Women' ),
				'meta'      => array(
					'_buymore_price' => '32',
					'_buymore_bg'    => '#f3d9e2',
				),
			),
		);

		foreach ( $products as $index => $product ) {
			self::insert(
				BuyMore_Post_Types::PRODUCT,
				$product['title'],
				$product['meta'],
				$index * 10,
				$product['audiences']
			);
		}
	}

	/**
	 * Sidebar order list.
	 *
	 * @return void
	 */
	private static function seed_orders(): void {
		$orders = array(
			'DXC Nike sneaker'  => 'view order',
			'Outerwear bundle'  => 'view order',
		);

		$index = 0;

		foreach ( $orders as $title => $label ) {
			self::insert(
				BuyMore_Post_Types::ORDER,
				$title,
				array( '_buymore_order_label' => $label ),
				$index * 10
			);

			++$index;
		}
	}

	/**
	 * Create the two sidebar menus and assign them to their locations.
	 *
	 * @return void
	 */
	private static function seed_menus(): void {
		$menus = array(
			'buymore_sidebar'       => array(
				'name'  => 'BuyMore sidebar',
				'items' => array(
					array( 'Popular products', 'icon-popular' ),
					array( 'Explore new', 'icon-explore is-active' ),
					array( 'Clothing and shoes', 'icon-clothing' ),
					array( 'Gifts and living', 'icon-gift' ),
					array( 'Inspiration', 'icon-inspiration' ),
				),
			),
			'buymore_quick_actions' => array(
				'name'  => 'BuyMore quick actions',
				'items' => array(
					array( 'Request for product', 'icon-plus' ),
					array( 'Add member', 'icon-member' ),
				),
			),
		);

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		$locations = is_array( $locations ) ? $locations : array();

		foreach ( $menus as $location => $menu ) {
			$existing = wp_get_nav_menu_object( $menu['name'] );
			$menu_id  = $existing ? (int) $existing->term_id : (int) wp_create_nav_menu( $menu['name'] );

			if ( ! $menu_id || is_wp_error( $menu_id ) ) {
				continue;
			}

			if ( ! $existing ) {
				foreach ( $menu['items'] as $position => $item ) {
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $item[0],
							'menu-item-url'       => home_url( '/' ),
							'menu-item-classes'   => $item[1],
							'menu-item-status'    => 'publish',
							'menu-item-type'      => 'custom',
							'menu-item-position'  => $position + 1,
						)
					);
				}
			}

			$locations[ $location ] = $menu_id;
		}

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Create a page that hosts the shortcode.
	 *
	 * @return void
	 */
	private static function seed_page(): void {
		$existing = get_option( self::PAGE_ID );

		if ( $existing && get_post( (int) $existing ) ) {
			return;
		}

		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_title'   => 'Explore',
				'post_name'    => 'explore',
				'post_status'  => 'publish',
				'post_content' => '<!-- wp:shortcode -->[' . BuyMore_Shortcode::TAG . ']<!-- /wp:shortcode -->',
			),
			true
		);

		if ( ! is_wp_error( $page_id ) ) {
			update_option( self::PAGE_ID, (int) $page_id );
		}
	}
}
