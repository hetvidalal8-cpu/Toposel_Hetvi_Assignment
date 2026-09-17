<?php
/**
 * AJAX endpoint behind the audience filter tabs.
 *
 * The tabs work without JavaScript through a plain ?bm_audience= query
 * parameter; this handler only makes the same navigation feel instant.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Filter endpoint.
 */
class BuyMore_Ajax {

	const ACTION = 'buymore_filter_products';
	const NONCE  = 'buymore_filter';

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'wp_ajax_' . self::ACTION, array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( $this, 'handle' ) );
	}

	/**
	 * Return the rendered grid for one audience.
	 *
	 * @return void
	 */
	public function handle(): void {
		check_ajax_referer( self::NONCE, 'nonce' );

		$audience = isset( $_POST['audience'] ) ? sanitize_title( wp_unslash( (string) $_POST['audience'] ) ) : '';

		if ( '' !== $audience && ! term_exists( $audience, BuyMore_Post_Types::AUDIENCE ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Unknown audience.', 'buymore-explore' ) ),
				400
			);
		}

		$products = BuyMore_Query::products( $audience );

		$html = BuyMore_Template::render(
			'partials/product-slots',
			array(
				'products' => $products,
				'audience' => $audience,
			)
		);

		wp_send_json_success(
			array(
				'html'  => $html,
				'count' => count( $products ),
			)
		);
	}
}
