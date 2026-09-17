<?php
/**
 * Sidebar: primary menu, quick actions, recent orders, sign out.
 *
 * @package BuyMore_Explore
 * @var array<string,mixed> $data Holds 'orders'.
 */

defined( 'ABSPATH' ) || exit;

$bm_orders     = isset( $data['orders'] ) && is_array( $data['orders'] ) ? $data['orders'] : array();
$bm_logout_url = (string) buymore_get_option( 'logout_url' );
$bm_logout_url = $bm_logout_url ? $bm_logout_url : wp_logout_url( home_url( '/' ) );
$bm_orders_url = (string) buymore_get_option( 'orders_link_url' );
?>
<aside class="bm-sidebar" id="bm-sidebar" data-bm-sidebar>
	<div class="bm-sidebar__inner">
		<button type="button" class="bm-iconbtn bm-sidebar__close" data-bm-nav-close>
			<?php buymore_the_icon( 'close', 18 ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Hide navigation', 'buymore-explore' ); ?></span>
		</button>

		<nav class="bm-nav" aria-label="<?php esc_attr_e( 'Store sections', 'buymore-explore' ); ?>">
			<?php
			if ( has_nav_menu( 'buymore_sidebar' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'buymore_sidebar',
						'container'      => false,
						'menu_class'     => 'bm-nav__list',
						'depth'          => 1,
						'walker'         => new BuyMore_Menu_Walker(),
						'fallback_cb'    => false,
					)
				);
			} else {
				printf(
					'<p class="bm-empty">%s</p>',
					esc_html__( 'Add a menu to the “BuyMore — sidebar navigation” location in Appearance → Menus.', 'buymore-explore' )
				);
			}
			?>
		</nav>

		<?php if ( has_nav_menu( 'buymore_quick_actions' ) ) : ?>
			<div class="bm-sidebar__block">
				<h2 class="bm-sidebar__title"><?php echo esc_html( (string) buymore_get_option( 'quick_actions_title' ) ); ?></h2>

				<nav class="bm-nav bm-nav--quiet" aria-label="<?php esc_attr_e( 'Quick actions', 'buymore-explore' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'buymore_quick_actions',
							'container'      => false,
							'menu_class'     => 'bm-nav__list',
							'depth'          => 1,
							'walker'         => new BuyMore_Menu_Walker(),
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>
			</div>
		<?php endif; ?>

		<?php if ( $bm_orders ) : ?>
			<div class="bm-sidebar__block">
				<h2 class="bm-sidebar__title">
					<?php echo esc_html( (string) buymore_get_option( 'orders_title' ) ); ?>
					<span class="bm-sidebar__count"><?php echo esc_html( (string) BuyMore_Query::order_count() ); ?></span>
				</h2>

				<ul class="bm-orders">
					<?php foreach ( $bm_orders as $bm_order ) : ?>
						<?php
						$bm_order_link  = (string) get_post_meta( $bm_order->ID, '_buymore_link', true );
						$bm_order_label = (string) get_post_meta( $bm_order->ID, '_buymore_order_label', true );
						?>
						<li class="bm-orders__item">
							<span class="bm-orders__thumb">
								<?php
								if ( has_post_thumbnail( $bm_order->ID ) ) {
									echo get_the_post_thumbnail( $bm_order->ID, 'buymore-thumb', array( 'alt' => '', 'loading' => 'lazy' ) );
								}
								?>
							</span>
							<span class="bm-orders__text">
								<span class="bm-orders__name"><?php echo esc_html( get_the_title( $bm_order ) ); ?></span>
								<?php if ( $bm_order_label ) : ?>
									<a class="bm-orders__link" href="<?php echo esc_url( $bm_order_link ? $bm_order_link : '#' ); ?>">
										<?php echo esc_html( $bm_order_label ); ?>
									</a>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( buymore_get_option( 'orders_link_label' ) ) : ?>
					<a class="bm-sidebar__more" href="<?php echo esc_url( $bm_orders_url ? $bm_orders_url : '#' ); ?>">
						<?php echo esc_html( (string) buymore_get_option( 'orders_link_label' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<a class="bm-signout" href="<?php echo esc_url( $bm_logout_url ); ?>">
			<?php buymore_the_icon( 'logout', 18 ); ?>
			<span><?php echo esc_html( (string) buymore_get_option( 'logout_label' ) ); ?></span>
		</a>
	</div>
</aside>
