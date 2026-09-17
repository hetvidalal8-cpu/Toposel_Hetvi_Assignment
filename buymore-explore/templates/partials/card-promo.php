<?php
/**
 * Promo / feature card.
 *
 * One template covers the three coloured promo tiles and the wide feature
 * tile; the differences are the slot it sits in and the chosen style.
 *
 * @package BuyMore_Explore
 * @var array<string,mixed> $data Holds 'card' (WP_Post|null) and 'slot'.
 */

defined( 'ABSPATH' ) || exit;

$bm_card = $data['card'] ?? null;
$bm_slot = isset( $data['slot'] ) ? sanitize_html_class( (string) $data['slot'] ) : '';

if ( ! $bm_card instanceof WP_Post || '' === $bm_slot ) {
	return;
}

$bm_style = (string) get_post_meta( $bm_card->ID, '_buymore_style', true );
$bm_style = in_array( $bm_style, array( 'promo', 'feature' ), true ) ? $bm_style : 'promo';

$bm_eyebrow   = (string) get_post_meta( $bm_card->ID, '_buymore_eyebrow', true );
$bm_subtitle  = (string) get_post_meta( $bm_card->ID, '_buymore_subtitle', true );
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
<article class="bm-card bm-card--<?php echo esc_attr( $bm_style ); ?>"
	data-slot="<?php echo esc_attr( $bm_slot ); ?>"
	<?php echo $bm_style_attr ? 'style="' . esc_attr( $bm_style_attr ) . '"' : ''; ?>>

	<?php echo buymore_card_image( (int) $bm_card->ID ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>

	<div class="bm-card__body">
		<?php if ( $bm_eyebrow ) : ?>
			<p class="bm-card__eyebrow"><?php echo esc_html( $bm_eyebrow ); ?></p>
		<?php endif; ?>

		<h2 class="bm-card__title"><?php echo esc_html( get_the_title( $bm_card ) ); ?></h2>

		<?php if ( $bm_subtitle ) : ?>
			<p class="bm-card__subtitle"><?php echo esc_html( $bm_subtitle ); ?></p>
		<?php endif; ?>

		<?php if ( $bm_cta_label ) : ?>
			<a class="bm-card__cta" href="<?php echo esc_url( $bm_cta_url ); ?>">
				<?php echo esc_html( $bm_cta_label ); ?>
			</a>
		<?php endif; ?>
	</div>

	<?php if ( ! $bm_cta_label ) : ?>
		<a class="bm-card__jump" href="<?php echo esc_url( $bm_cta_url ); ?>">
			<?php buymore_the_icon( 'arrow-up', 16 ); ?>
			<span class="screen-reader-text">
				<?php
				printf(
					/* translators: %s: card title. */
					esc_html__( 'Open %s', 'buymore-explore' ),
					esc_html( get_the_title( $bm_card ) )
				);
				?>
			</span>
		</a>
	<?php endif; ?>
</article>
