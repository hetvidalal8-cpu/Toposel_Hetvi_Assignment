<?php
/**
 * Content model: products, promo cards, recent orders and the audience taxonomy.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Registers every custom post type and taxonomy used by the dashboard.
 */
class BuyMore_Post_Types {

	const PRODUCT  = 'buymore_product';
	const CARD     = 'buymore_card';
	const ORDER    = 'buymore_order';
	const AUDIENCE = 'buymore_audience';

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );
		add_action( 'admin_menu', array( $this, 'group_admin_menu' ) );
		add_filter( 'manage_' . self::PRODUCT . '_posts_columns', array( $this, 'product_columns' ) );
		add_action( 'manage_' . self::PRODUCT . '_posts_custom_column', array( $this, 'product_column_content' ), 10, 2 );
	}

	/**
	 * The named grid slots a promo card can occupy.
	 *
	 * The keys double as CSS grid-area names, which is what keeps the layout
	 * editable from the admin without touching the stylesheet.
	 *
	 * @return array<string,string>
	 */
	public static function card_slots(): array {
		return array(
			'promo-a'    => __( 'Left column — top promo (green band in the reference)', 'buymore-explore' ),
			'promo-b'    => __( 'Left column — middle promo (yellow band)', 'buymore-explore' ),
			'promo-c'    => __( 'Left column — bottom promo (portrait tile)', 'buymore-explore' ),
			'favourites' => __( 'Favourites strip', 'buymore-explore' ),
			'feature'    => __( 'Wide feature tile (bottom right)', 'buymore-explore' ),
		);
	}

	/**
	 * Register the three post types.
	 *
	 * @return void
	 */
	public function register_post_types(): void {
		register_post_type(
			self::PRODUCT,
			array(
				'labels'        => $this->labels( __( 'Product', 'buymore-explore' ), __( 'Products', 'buymore-explore' ) ),
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => false,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-tag',
				'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions' ),
				'has_archive'   => false,
				'rewrite'       => false,
				'hierarchical'  => false,
				'query_var'     => false,
				'map_meta_cap'  => true,
				'capability_type' => 'post',
			)
		);

		register_post_type(
			self::CARD,
			array(
				'labels'        => $this->labels( __( 'Promo card', 'buymore-explore' ), __( 'Promo cards', 'buymore-explore' ) ),
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => false,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-format-gallery',
				'supports'      => array( 'title', 'thumbnail', 'page-attributes', 'revisions' ),
				'has_archive'   => false,
				'rewrite'       => false,
				'query_var'     => false,
			)
		);

		register_post_type(
			self::ORDER,
			array(
				'labels'        => $this->labels( __( 'Recent order', 'buymore-explore' ), __( 'Recent orders', 'buymore-explore' ) ),
				'public'        => false,
				'show_ui'       => true,
				'show_in_menu'  => false,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-list-view',
				'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
				'has_archive'   => false,
				'rewrite'       => false,
				'query_var'     => false,
			)
		);
	}

	/**
	 * Audience terms drive the All / Men / Women filter tabs.
	 *
	 * @return void
	 */
	public function register_taxonomy(): void {
		register_taxonomy(
			self::AUDIENCE,
			array( self::PRODUCT, self::CARD ),
			array(
				'labels'            => array(
					'name'          => __( 'Audiences', 'buymore-explore' ),
					'singular_name' => __( 'Audience', 'buymore-explore' ),
					'menu_name'     => __( 'Audiences', 'buymore-explore' ),
					'add_new_item'  => __( 'Add audience', 'buymore-explore' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => false,
				'query_var'         => false,
			)
		);
	}

	/**
	 * Register post meta so values are typed, sanitised and REST-aware.
	 *
	 * @return void
	 */
	public function register_meta(): void {
		$string_meta = array(
			self::PRODUCT => array( '_buymore_eyebrow', '_buymore_price', '_buymore_swatches', '_buymore_badge', '_buymore_bg', '_buymore_link' ),
			self::CARD    => array( '_buymore_slot', '_buymore_eyebrow', '_buymore_subtitle', '_buymore_cta_label', '_buymore_cta_url', '_buymore_bg', '_buymore_fg', '_buymore_style' ),
			self::ORDER   => array( '_buymore_order_label', '_buymore_link' ),
		);

		foreach ( $string_meta as $post_type => $keys ) {
			foreach ( $keys as $key ) {
				register_post_meta(
					$post_type,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'default'           => '',
						'show_in_rest'      => true,
						'sanitize_callback' => 'sanitize_text_field',
						'auth_callback'     => static function (): bool {
							return current_user_can( 'edit_posts' );
						},
					)
				);
			}
		}
	}

	/**
	 * A single crop keeps card images consistent without shipping large files.
	 *
	 * @return void
	 */
	public function register_image_sizes(): void {
		add_image_size( 'buymore-card', 640, 720, true );
		add_image_size( 'buymore-thumb', 160, 160, true );
	}

	/**
	 * One top-level "BuyMore" menu holds every content type, so an editor never
	 * has to hunt through the sidebar.
	 *
	 * @return void
	 */
	public function group_admin_menu(): void {
		add_menu_page(
			__( 'BuyMore dashboard', 'buymore-explore' ),
			__( 'BuyMore', 'buymore-explore' ),
			'edit_posts',
			'buymore',
			'',
			'dashicons-store',
			26
		);

		$children = array(
			array( __( 'Products', 'buymore-explore' ), 'edit.php?post_type=' . self::PRODUCT ),
			array( __( 'Promo cards', 'buymore-explore' ), 'edit.php?post_type=' . self::CARD ),
			array( __( 'Recent orders', 'buymore-explore' ), 'edit.php?post_type=' . self::ORDER ),
			array( __( 'Audiences', 'buymore-explore' ), 'edit-tags.php?taxonomy=' . self::AUDIENCE . '&post_type=' . self::PRODUCT ),
		);

		foreach ( $children as $child ) {
			add_submenu_page( 'buymore', $child[0], $child[0], 'edit_posts', $child[1] );
		}

		remove_submenu_page( 'buymore', 'buymore' );
	}

	/**
	 * Add price and audience columns to the product list table.
	 *
	 * @param array<string,string> $columns Existing columns.
	 * @return array<string,string>
	 */
	public function product_columns( array $columns ): array {
		$reordered = array();

		foreach ( $columns as $key => $label ) {
			$reordered[ $key ] = $label;

			if ( 'title' === $key ) {
				$reordered['buymore_price'] = __( 'Price', 'buymore-explore' );
			}
		}

		return $reordered;
	}

	/**
	 * Render custom column values.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 * @return void
	 */
	public function product_column_content( string $column, int $post_id ): void {
		if ( 'buymore_price' !== $column ) {
			return;
		}

		echo esc_html( buymore_format_price( (string) get_post_meta( $post_id, '_buymore_price', true ) ) );
	}

	/**
	 * Standard label set.
	 *
	 * @param string $singular Singular name.
	 * @param string $plural   Plural name.
	 * @return array<string,string>
	 */
	private function labels( string $singular, string $plural ): array {
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'add_new'            => __( 'Add new', 'buymore-explore' ),
			/* translators: %s: singular post type name. */
			'add_new_item'       => sprintf( __( 'Add new %s', 'buymore-explore' ), strtolower( $singular ) ),
			/* translators: %s: singular post type name. */
			'edit_item'          => sprintf( __( 'Edit %s', 'buymore-explore' ), strtolower( $singular ) ),
			/* translators: %s: plural post type name. */
			'search_items'       => sprintf( __( 'Search %s', 'buymore-explore' ), strtolower( $plural ) ),
			/* translators: %s: plural post type name. */
			'not_found'          => sprintf( __( 'No %s yet', 'buymore-explore' ), strtolower( $plural ) ),
			'all_items'          => $plural,
		);
	}
}
