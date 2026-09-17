<?php
/**
 * Top bar: store name, order stat, view switch, cart and account.
 *
 * @package BuyMore_Explore
 */

defined( 'ABSPATH' ) || exit;

$bm_stat = trim( (string) buymore_get_option( 'stat_value' ) );

if ( '' === $bm_stat ) {
	$bm_stat = (string) BuyMore_Query::order_count();
}

$bm_user   = (string) buymore_get_option( 'user_name' );
$bm_avatar = (string) buymore_get_option( 'user_avatar' );
$bm_extra  = absint( buymore_get_option( 'team_extra' ) );
?>
<header class="bm-topbar">
	<div class="bm-topbar__lead">
		<button type="button" class="bm-iconbtn bm-navtoggle" data-bm-nav-toggle aria-expanded="false" aria-controls="bm-sidebar">
			<?php buymore_the_icon( 'menu', 20 ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Show navigation', 'buymore-explore' ); ?></span>
		</button>

		<span class="bm-brand"><?php echo esc_html( (string) buymore_get_option( 'brand_name' ) ); ?></span>
	</div>

	<div class="bm-stat">
		<span class="bm-stat__value"><?php echo esc_html( $bm_stat ); ?></span>
		<span class="bm-stat__text">
			<span class="bm-stat__label"><?php echo esc_html( (string) buymore_get_option( 'stat_label' ) ); ?></span>
			<span class="bm-stat__meta"><?php echo esc_html( (string) buymore_get_option( 'stat_meta' ) ); ?></span>
		</span>
	</div>

	<div class="bm-viewswitch" role="group" aria-label="<?php esc_attr_e( 'Choose a view', 'buymore-explore' ); ?>">
		<button type="button" class="bm-viewswitch__btn is-active" data-bm-view aria-pressed="true">
			<?php echo esc_html( (string) buymore_get_option( 'view_primary' ) ); ?>
		</button>
		<button type="button" class="bm-viewswitch__btn" data-bm-view aria-pressed="false">
			<?php echo esc_html( (string) buymore_get_option( 'view_secondary' ) ); ?>
		</button>
	</div>

	<div class="bm-topbar__end">
		<button type="button" class="bm-chip bm-chip--cart">
			<?php buymore_the_icon( 'cart', 17 ); ?>
			<span><?php echo esc_html( (string) buymore_get_option( 'cart_label' ) ); ?></span>
		</button>

		<?php if ( $bm_extra > 0 ) : ?>
			<div class="bm-team" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: number of team members. */ __( '%d other team members', 'buymore-explore' ), $bm_extra ) ); ?>">
				<span class="bm-team__dot bm-team__dot--1"></span>
				<span class="bm-team__dot bm-team__dot--2"></span>
				<span class="bm-team__count">+<?php echo esc_html( (string) $bm_extra ); ?></span>
			</div>
		<?php endif; ?>

		<div class="bm-account">
			<?php if ( $bm_avatar ) : ?>
				<img class="bm-account__avatar" src="<?php echo esc_url( $bm_avatar ); ?>" alt="" width="32" height="32" loading="lazy" decoding="async">
			<?php else : ?>
				<span class="bm-account__avatar bm-account__avatar--initial" aria-hidden="true"><?php echo esc_html( mb_substr( $bm_user, 0, 1 ) ); ?></span>
			<?php endif; ?>
			<span class="bm-account__name"><?php echo esc_html( $bm_user ); ?></span>
		</div>
	</div>
</header>
