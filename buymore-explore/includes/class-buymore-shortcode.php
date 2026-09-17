<?php
/**
 * The [buymore_dashboard] shortcode.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Renders the dashboard.
 */
class BuyMore_Shortcode {

	const TAG = 'buymore_dashboard';

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_shortcode( self::TAG, array( $this, 'render' ) );
	}

	/**
	 * Build the dashboard markup.
	 *
	 * @param array<string,string>|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render( $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'audience' => '',
				'limit'    => 12,
			),
			is_array( $atts ) ? $atts : array(),
			self::TAG
		);

		$assets = buymore()->get( 'assets' );

		if ( $assets instanceof BuyMore_Assets ) {
			$assets->enqueue();
		}

		// A query parameter keeps the tabs usable with JavaScript switched off.
		$requested = isset( $_GET['bm_audience'] ) ? sanitize_title( wp_unslash( (string) $_GET['bm_audience'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display filter.
		$audience  = $requested ?: sanitize_title( (string) $atts['audience'] );

		if ( '' !== $audience && ! term_exists( $audience, BuyMore_Post_Types::AUDIENCE ) ) {
			$audience = '';
		}

		return BuyMore_Template::render(
			'dashboard',
			array(
				'audience' => $audience,
				'limit'    => max( 2, absint( $atts['limit'] ) ),
				'cards'    => BuyMore_Query::cards(),
				'products' => BuyMore_Query::products( $audience, max( 2, absint( $atts['limit'] ) ) ),
				'orders'   => BuyMore_Query::orders(),
			)
		);
	}
}

/**
 * Theme helper: print the dashboard from a template file.
 *
 * @param array<string,string> $atts Same attributes as the shortcode.
 * @return void
 */
function buymore_dashboard( array $atts = array() ): void {
	add_filter( 'buymore_enqueue_assets', '__return_true' );

	$shortcode = buymore()->get( 'shortcode' );

	if ( $shortcode instanceof BuyMore_Shortcode ) {
		echo $shortcode->render( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Templates escape their own output.
	}
}
