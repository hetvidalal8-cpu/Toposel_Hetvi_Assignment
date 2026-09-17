<?php
/**
 * Tiny template loader with theme override support.
 *
 * A theme can copy any file from the plugin's /templates directory into
 * /buymore-explore/ in the theme and change it without touching the plugin.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Loads front-end templates.
 */
class BuyMore_Template {

	/**
	 * Render a template file and return its markup.
	 *
	 * @param string              $relative Path relative to /templates, without extension.
	 * @param array<string,mixed> $data     Variables exposed to the template as $data.
	 * @return string
	 */
	public static function render( string $relative, array $data = array() ): string {
		$file = self::locate( $relative );

		if ( ! $file ) {
			return '';
		}

		ob_start();

		/**
		 * Available inside every template as $data.
		 *
		 * @var array<string,mixed> $data
		 */
		include $file;

		return (string) ob_get_clean();
	}

	/**
	 * Print a template.
	 *
	 * @param string              $relative Template path.
	 * @param array<string,mixed> $data     Template data.
	 * @return void
	 */
	public static function part( string $relative, array $data = array() ): void {
		echo self::render( $relative, $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Templates escape their own output.
	}

	/**
	 * Find a template, preferring the active theme.
	 *
	 * @param string $relative Template path without extension.
	 * @return string Absolute path, or '' when missing.
	 */
	private static function locate( string $relative ): string {
		$relative = ltrim( str_replace( array( '..', "\0" ), '', $relative ), '/' );
		$filename = $relative . '.php';

		$theme_file = locate_template( array( 'buymore-explore/' . $filename ) );

		if ( $theme_file ) {
			return $theme_file;
		}

		$plugin_file = BUYMORE_DIR . 'templates/' . $filename;

		return file_exists( $plugin_file ) ? $plugin_file : '';
	}
}
