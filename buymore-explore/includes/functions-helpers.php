<?php
/**
 * Small template helpers shared by the front-end partials.
 *
 * @package BuyMore_Explore
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Read a single dashboard setting with its default applied.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Fallback when the option is empty.
 * @return mixed
 */
function buymore_get_option( string $key, $default = '' ) {
	$options = get_option( BuyMore_Settings::OPTION_KEY, array() );
	$value   = is_array( $options ) && isset( $options[ $key ] ) ? $options[ $key ] : '';

	if ( '' === $value || null === $value ) {
		$defaults = BuyMore_Settings::defaults();
		$value    = $defaults[ $key ] ?? $default;
	}

	return $value;
}

/**
 * Inline SVG icon set.
 *
 * Icons are inlined (rather than loaded as an icon font or sprite request) so
 * the dashboard renders in a single HTTP response and icons inherit currentColor.
 *
 * @param string $name  Icon key.
 * @param int    $size  Pixel size for width and height.
 * @param array  $attrs Extra attributes, e.g. array( 'class' => 'x' ).
 * @return string Escaped-safe SVG markup.
 */
function buymore_icon( string $name, int $size = 18, array $attrs = array() ): string {
	$paths = array(
		'popular'     => '<path d="M12 3.6 14 8.5l5.3.4-4 3.4 1.2 5.1L12 14.7l-4.5 2.7 1.2-5.1-4-3.4 5.3-.4z"/>',
		'explore'     => '<circle cx="12" cy="12" r="8.5"/><path d="m15 9-2.1 4.9L8 16l2.1-4.9z"/>',
		'clothing'    => '<path d="M9 4h6l4 3-2.5 2.6V20H7.5V9.6L5 7z"/>',
		'gift'        => '<path d="M4 9h16v3H4zm1 3h14v8H5zm7-8c1.7 0 2.5 1 2.5 2.5S13 9 12 9s-2.5-.5-2.5-2.5S10.3 4 12 4z"/><path d="M12 9v11"/>',
		'inspiration' => '<path d="M9.5 18h5m-4.5 3h4M12 3a6 6 0 0 1 3.6 10.8c-.6.5-1 1.2-1.1 2H9.5c-.1-.8-.5-1.5-1.1-2A6 6 0 0 1 12 3z"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'member'      => '<circle cx="10" cy="8.5" r="3.5"/><path d="M4 20c0-3.3 2.7-5.5 6-5.5 1 0 2 .2 2.8.6M17 14v6m-3-3h6"/>',
		'logout'      => '<path d="M14 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7M10 12h9m0 0-3-3m3 3-3 3"/>',
		'cart'        => '<path d="M4 5h2.2l1.9 9.4a2 2 0 0 0 2 1.6h6.7a2 2 0 0 0 2-1.5L20 8H7"/><circle cx="10" cy="19.5" r="1.3"/><circle cx="17" cy="19.5" r="1.3"/>',
		'search'      => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/>',
		'filters'     => '<path d="M4 7h10m3 0h3M4 12h3m3 0h10M4 17h10m3 0h3"/><circle cx="15.5" cy="7" r="1.8"/><circle cx="8.5" cy="12" r="1.8"/><circle cx="15.5" cy="17" r="1.8"/>',
		'heart'       => '<path d="M12 19.5s-7-4.3-7-9A3.9 3.9 0 0 1 12 8a3.9 3.9 0 0 1 7 2.5c0 4.7-7 9-7 9z"/>',
		'arrow-up'    => '<path d="M8 16 16 8m0 0H9.5m6.5 0v6.5"/>',
		'chev-left'   => '<path d="m14 8-4 4 4 4"/>',
		'chev-right'  => '<path d="m10 8 4 4-4 4"/>',
		'menu'        => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'       => '<path d="m6 6 12 12M18 6 6 18"/>',
		'man'         => '<circle cx="12" cy="6.5" r="2.8"/><path d="M8 20v-5.5L6.5 12 8.8 10h6.4l2.3 2-1.5 2.5V20"/>',
		'woman'       => '<circle cx="12" cy="6.5" r="2.8"/><path d="M9 20l1-6H8l2.5-4h3L16 14h-2l1 6z"/>',
		'grid'        => '<rect x="4" y="4" width="7" height="7" rx="2"/><rect x="13" y="4" width="7" height="7" rx="2"/><rect x="4" y="13" width="7" height="7" rx="2"/><rect x="13" y="13" width="7" height="7" rx="2"/>',
		'tag'         => '<path d="M4 10.5V5a1 1 0 0 1 1-1h5.5l9 9-6.5 6.5z"/><circle cx="8" cy="8" r="1.2"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		$name = 'grid';
	}

	$attr_string = '';
	foreach ( $attrs as $attr_key => $attr_value ) {
		$attr_string .= sprintf( ' %s="%s"', esc_attr( $attr_key ), esc_attr( (string) $attr_value ) );
	}

	return sprintf(
		'<svg class="bm-icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"%2$s>%3$s</svg>',
		absint( $size ),
		$attr_string,
		$paths[ $name ]
	);
}

/**
 * Print an icon.
 *
 * @param string $name  Icon key.
 * @param int    $size  Pixel size.
 * @param array  $attrs Extra attributes.
 * @return void
 */
function buymore_the_icon( string $name, int $size = 18, array $attrs = array() ): void {
	echo buymore_icon( $name, $size, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from a fixed allow-list above.
}

/**
 * Format a stored price for display.
 *
 * Stored as a plain number so it stays sortable; the currency symbol comes
 * from the settings screen.
 *
 * @param string $raw Raw meta value.
 * @return string
 */
function buymore_format_price( string $raw ): string {
	$raw = trim( $raw );

	if ( '' === $raw ) {
		return '';
	}

	if ( is_numeric( $raw ) ) {
		$decimals = ( (float) $raw === floor( (float) $raw ) ) ? 0 : 2;
		$raw      = number_format_i18n( (float) $raw, $decimals );
	}

	return (string) buymore_get_option( 'currency', '$' ) . $raw;
}

/**
 * Build a CSS custom-property string from a key => value map, skipping empties.
 *
 * @param array<string,string> $vars Map of custom property names to values.
 * @return string Ready for a style attribute.
 */
function buymore_style_vars( array $vars ): string {
	$parts = array();

	foreach ( $vars as $name => $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			continue;
		}

		$parts[] = sprintf( '%s:%s', $name, $value );
	}

	return implode( ';', $parts );
}

/**
 * Split a comma separated colour list into sanitised hex values.
 *
 * @param string $raw Raw meta value, e.g. "#ffd166, #1f1f1f".
 * @return string[]
 */
function buymore_parse_swatches( string $raw ): array {
	$colors = array();

	foreach ( explode( ',', $raw ) as $color ) {
		$color = sanitize_hex_color( trim( $color ) );

		if ( $color ) {
			$colors[] = $color;
		}
	}

	return array_slice( $colors, 0, 4 );
}

/**
 * Card image markup with a graceful fallback when no featured image is set.
 *
 * @param int    $post_id Post ID.
 * @param string $size    Registered image size.
 * @return string
 */
function buymore_card_image( int $post_id, string $size = 'buymore-card' ): string {
	if ( ! has_post_thumbnail( $post_id ) ) {
		return '<span class="bm-card__placeholder" aria-hidden="true"></span>';
	}

	return get_the_post_thumbnail(
		$post_id,
		$size,
		array(
			'class'    => 'bm-card__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
			'alt'      => esc_attr( (string) get_post_meta( $post_id, '_buymore_image_alt', true ) ) ?: esc_attr( get_the_title( $post_id ) ),
		)
	);
}
