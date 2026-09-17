<?php
/**
 * Favourites strip: a scrollable row of the remaining products.
 *
 * @package BuyMore_Explore
 * @var array<string,mixed> $data Holds 'card' (WP_Post|null) and 'products' (WP_Post[]).
 */

defined( 'ABSPATH' ) || exit;

$bm_card     = $data['card'] ?? null;
$bm_products = isset( $data['products'] ) && is_array( $data['products'] ) ? $data['products'] : array();

if ( ! $bm_card instanceof WP_Post ) {
	return;
}

$bm_cta_label = (string) get_post_meta( $bm_card->ID, '_buymore_cta_label', true );
$bm_cta_url   = (string) get_post_meta( $bm_card->ID, '_buymore_cta_url', true );
$bm_cta_url   = $bm_cta_url ? $bm_cta_url : '#';

$bm_style_attr = buymore_style_vars(
	array(
		'--bm-card-bg' => sanitize_hex_color( (string) get_post_meta( $bm_card->ID, '_buymore_bg', true ) ),
		'--bm-card-fg' => sanitize_hex_color( (string) get_post_meta( $bm_card->ID, '_buymore_fg', true ) ),
	)
);
?>
<section class="bm-card bm-card--favourites" data-slot="favourites" data-bm-favourites
	<?php echo $bm_style_attr ? 'style="' . esc_attr( $bm_style_attr ) . '"' : ''; ?>>

	<header class="bm-favourites__head">
		<h2 class="bm-favourites__title"><?php echo esc_html( get_the_title( $bm_card ) ); ?></h2>

		<div class="bm-favourites__nav">
			<button type="button" class="bm-iconbtn bm-iconbtn--sm" data-bm-scroll="-1">
				<?php buymore_the_icon( 'chev-left', 16 ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Previous favourites', 'buymore-explore' ); ?></span>
			</button>
			<button type="button" class="bm-iconbtn bm-iconbtn--sm" data-bm-scroll="1">
				<?php buymore_the_icon( 'chev-right', 16 ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'More favourites', 'buymore-explore' ); ?></span>
			</button>
		</div>
	</header>

	<?php if ( $bm_products ) : ?>
		<ul class="bm-favourites__track" data-bm-track>
			<?php foreach ( $bm_products as $bm_product ) : ?>
				<?php
				$bm_link  = (string) get_post_meta( $bm_product->ID, '_buymore_link', true );
				$bm_tile  = buymore_style_vars(
					array( '--bm-card-bg' => sanitize_hex_color( (string) get_post_meta( $bm_product->ID, '_buymore_bg', true ) ) )
				);
				$bm_title = get_the_title( $bm_product );
				?>
				<li class="bm-favourites__item" data-bm-searchable data-name="<?php echo esc_attr( strtolower( $bm_title ) ); ?>"
					<?php echo $bm_tile ? 'style="' . esc_attr( $bm_tile ) . '"' : ''; ?>>
					<a href="<?php echo esc_url( $bm_link ? $bm_link : '#' ); ?>">
						<?php echo buymore_card_image( (int) $bm_product->ID, 'buymore-thumb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
						<span class="screen-reader-text"><?php echo esc_html( $bm_title ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<p class="bm-empty"><?php esc_html_e( 'Publish more products to fill this strip.', 'buymore-explore' ); ?></p>
	<?php endif; ?>

	<?php if ( $bm_cta_label ) : ?>
		<a class="bm-favourites__more" href="<?php echo esc_url( $bm_cta_url ); ?>">
			<?php echo esc_html( $bm_cta_label ); ?>
		</a>
	<?php endif; ?>
</section>
