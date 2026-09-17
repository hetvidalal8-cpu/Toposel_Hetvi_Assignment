<?php
/**
 * Every database read the front end needs, in one place.
 *
 * Templates call these static methods instead of building WP_Query objects
 * inline, which keeps the partials readable and the queries testable.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Read helpers for the dashboard.
 */
class BuyMore_Query {

	/**
	 * Products, optionally narrowed to one audience term.
	 *
	 * @param string $audience Term slug, or '' for everything.
	 * @param int    $limit    Maximum posts.
	 * @return WP_Post[]
	 */
	public static function products( string $audience = '', int $limit = 12 ): array {
		$args = array(
			'post_type'              => BuyMore_Post_Types::PRODUCT,
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'ignore_sticky_posts'    => true,
		);

		if ( '' !== $audience ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => BuyMore_Post_Types::AUDIENCE,
					'field'    => 'slug',
					'terms'    => sanitize_title( $audience ),
				),
			);
		}

		return get_posts( $args );
	}

	/**
	 * Promo cards keyed by their grid slot.
	 *
	 * @return array<string,WP_Post>
	 */
	public static function cards(): array {
		$posts = get_posts(
			array(
				'post_type'      => BuyMore_Post_Types::CARD,
				'post_status'    => 'publish',
				'posts_per_page' => 20,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'no_found_rows'  => true,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => '_buymore_slot',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		$by_slot = array();

		foreach ( $posts as $post ) {
			$slot = (string) get_post_meta( $post->ID, '_buymore_slot', true );

			if ( '' === $slot || isset( $by_slot[ $slot ] ) ) {
				continue;
			}

			$by_slot[ $slot ] = $post;
		}

		return $by_slot;
	}

	/**
	 * Recent orders for the sidebar list.
	 *
	 * @param int $limit Maximum posts.
	 * @return WP_Post[]
	 */
	public static function orders( int $limit = 3 ): array {
		return get_posts(
			array(
				'post_type'      => BuyMore_Post_Types::ORDER,
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'no_found_rows'  => true,
			)
		);
	}

	/**
	 * Total published orders, used for the headline number.
	 *
	 * @return int
	 */
	public static function order_count(): int {
		$counts = wp_count_posts( BuyMore_Post_Types::ORDER );

		return isset( $counts->publish ) ? (int) $counts->publish : 0;
	}

	/**
	 * Audience terms that actually have products attached.
	 *
	 * @return WP_Term[]
	 */
	public static function audiences(): array {
		$terms = get_terms(
			array(
				'taxonomy'   => BuyMore_Post_Types::AUDIENCE,
				'hide_empty' => true,
				'orderby'    => 'term_order',
			)
		);

		return is_wp_error( $terms ) ? array() : $terms;
	}

	/**
	 * Icon slug for an audience term, so tabs can show a small glyph.
	 *
	 * @param WP_Term $term Term object.
	 * @return string
	 */
	public static function audience_icon( WP_Term $term ): string {
		$map = array(
			'men'   => 'man',
			'women' => 'woman',
		);

		return $map[ $term->slug ] ?? 'tag';
	}
}
