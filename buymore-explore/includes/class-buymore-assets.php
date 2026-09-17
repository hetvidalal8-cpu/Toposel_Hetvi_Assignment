<?php
/**
 * Stylesheet and script registration.
 *
 * Assets are only sent to pages that actually render the dashboard, and the
 * palette is injected as CSS custom properties from the settings screen.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Front-end assets.
 */
class BuyMore_Assets {

	const HANDLE = 'buymore-explore';

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register' ) );
	}

	/**
	 * Register and conditionally enqueue.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_style( self::HANDLE, BUYMORE_URL . 'assets/css/buymore.css', array(), BUYMORE_VERSION );
		wp_register_script( self::HANDLE, BUYMORE_URL . 'assets/js/buymore.js', array(), BUYMORE_VERSION, true );

		wp_localize_script(
			self::HANDLE,
			'buymoreConfig',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( BuyMore_Ajax::NONCE ),
				'action'  => BuyMore_Ajax::ACTION,
				'strings' => array(
					'empty' => __( 'Nothing in this category yet. Add a product and tag it to see it here.', 'buymore-explore' ),
					'error' => __( 'Could not load products. Please try again.', 'buymore-explore' ),
				),
			)
		);

		if ( $this->is_needed() ) {
			$this->enqueue();
		}
	}

	/**
	 * Enqueue the dashboard assets plus the palette variables.
	 *
	 * Safe to call from the shortcode as a late fallback.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		wp_enqueue_style( self::HANDLE );
		wp_enqueue_script( self::HANDLE );
		wp_add_inline_style( self::HANDLE, $this->palette_css() );
	}

	/**
	 * Build the runtime palette from saved settings.
	 *
	 * @return string
	 */
	private function palette_css(): string {
		$accent = sanitize_hex_color( (string) buymore_get_option( 'accent' ) ) ?: '#1f57ef';
		$canvas = sanitize_hex_color( (string) buymore_get_option( 'canvas' ) ) ?: '#d9ecd0';

		return sprintf(
			'.bm-app{--bm-accent:%s;--bm-canvas:%s;}',
			$accent,
			$canvas
		);
	}

	/**
	 * Does the current request render the dashboard?
	 *
	 * @return bool
	 */
	private function is_needed(): bool {
		$needed = false;

		if ( is_singular() ) {
			$post = get_post();

			if ( $post instanceof WP_Post && has_shortcode( (string) $post->post_content, BuyMore_Shortcode::TAG ) ) {
				$needed = true;
			}
		}

		/**
		 * Force the dashboard assets to load, for themes that call
		 * buymore_dashboard() directly from a template file.
		 *
		 * @param bool $needed Whether to enqueue.
		 */
		return (bool) apply_filters( 'buymore_enqueue_assets', $needed );
	}
}
