<?php
/**
 * The two large product tiles.
 *
 * Rendered on page load and again by the AJAX filter, which is why it lives
 * in its own partial with no surrounding markup.
 *
 * @package BuyMore_Explore
 * @var array<string,mixed> $data Holds 'products' (WP_Post[]) and 'audience'.
 */

defined( 'ABSPATH' ) || exit;

$bm_products = isset( $data['products'] ) && is_array( $data['products'] ) ? $data['products'] : array();
$bm_featured = array_slice( $bm_products, 0, 2 );

if ( ! $bm_featured ) {
	printf(
		'<p class="bm-empty bm-empty--grid">%s</p>',
		esc_html__( 'No products match this filter yet.', 'buymore-explore' )
	);

	return;
}

foreach ( $bm_featured as $bm_index => $bm_product ) {
	BuyMore_Template::part(
		'partials/product-card',
		array(
			'product' => $bm_product,
			'index'   => (int) $bm_index,
		)
	);
}
