<?php
/**
 * A single large product tile: image, swatches, save button and price row.
 *
 * @package BuyMore_Explore
 * @var array<string,mixed> $data Holds 'product' (WP_Post) and 'index'.
 */

defined( 'ABSPATH' ) || exit;

$bm_product = $data['product'] ?? null;

if ( ! $bm_product instanceof WP_Post ) {
	return;
}

$bm_eyebrow  = (string) get_post_meta( $bm_product->ID, '_buymore_eyebrow', true );
$bm_badge    = (string) get_post_meta( $bm_product->ID, '_buymore_badge', true );
$bm_price    = buymore_format_price( (string) get_post_meta( $bm_product->ID, '_buymore_price', true ) );
$bm_link     = (string) get_post_meta( $bm_product->ID, '_buymore_link', true );
$bm_link     = $bm_link ? $bm_link : '#';
$bm_swatches = buymore_parse_swatches( (string) get_post_meta( $bm_product->ID, '_buymore_swatches', true ) );
$bm_title    = get_the_title( $bm_product );

$bm_style_attr = buymore_style_vars(
	array(
		'--bm-card-bg' => sanitize_hex_color( (string) get_post_meta( $bm_product->ID, '_buymore_bg', true ) ),
	)
);
?>
<article class="bm-product" data-bm-searchable data-name="<?php echo esc_attr( strtolower( $bm_title ) ); ?>"
	<?php echo $bm_style_attr ? 'style="' . esc_attr( $bm_style_attr ) . '"' : ''; ?>>

	<div class="bm-product__media">
		<a class="bm-product__imglink" href="<?php echo esc_url( $bm_link ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo buymore_card_image( (int) $bm_product->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
		</a>

		<?php if ( $bm_swatches ) : ?>
			<ul class="bm-swatches">
				<?php foreach ( $bm_swatches as $bm_color ) : ?>
					<li class="bm-swatches__dot" style="--bm-swatch:<?php echo esc_attr( $bm_color ); ?>"></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<button type="button" class="bm-save" data-bm-save aria-pressed="false">
			<?php buymore_the_icon( 'heart', 16 ); ?>
			<span class="screen-reader-text">
				<?php
				printf(
					/* translators: %s: product name. */
					esc_html__( 'Save %s', 'buymore-explore' ),
					esc_html( $bm_title )
				);
				?>
			</span>
		</button>

		<?php if ( $bm_badge ) : ?>
			<span class="bm-product__badge"><?php echo esc_html( $bm_badge ); ?></span>
		<?php endif; ?>
	</div>

	<div class="bm-product__meta">
		<div class="bm-product__text">
			<?php if ( $bm_eyebrow ) : ?>
				<p class="bm-product__eyebrow"><?php echo esc_html( $bm_eyebrow ); ?></p>
			<?php endif; ?>

			<h3 class="bm-product__name">
				<a href="<?php echo esc_url( $bm_link ); ?>"><?php echo esc_html( $bm_title ); ?></a>
			</h3>
		</div>

		<?php if ( $bm_price ) : ?>
			<span class="bm-product__price"><?php echo esc_html( $bm_price ); ?></span>
		<?php endif; ?>
	</div>
</article>
