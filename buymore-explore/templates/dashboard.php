<?php
/**
 * Dashboard shell.
 *
 * @package BuyMore_Explore
 * @var array<string,mixed> $data Audience, limit, cards, products, orders.
 */

defined( 'ABSPATH' ) || exit;

$bm_cards    = isset( $data['cards'] ) && is_array( $data['cards'] ) ? $data['cards'] : array();
$bm_products = isset( $data['products'] ) && is_array( $data['products'] ) ? $data['products'] : array();
$bm_orders   = isset( $data['orders'] ) && is_array( $data['orders'] ) ? $data['orders'] : array();
$bm_audience = isset( $data['audience'] ) ? (string) $data['audience'] : '';
?>
<div class="bm-app" data-bm-app>
	<div class="bm-shell">

		<?php
		BuyMore_Template::part( 'partials/topbar' );
		?>

		<div class="bm-body">
			<div class="bm-scrim" data-bm-scrim hidden></div>

			<?php
			BuyMore_Template::part( 'partials/sidebar', array( 'orders' => $bm_orders ) );
			?>

			<main class="bm-main" id="bm-main">
				<?php
				BuyMore_Template::part( 'partials/toolbar', array( 'audience' => $bm_audience ) );
				?>

				<div class="bm-grid" data-bm-grid>
					<?php
					BuyMore_Template::part(
						'partials/card-promo',
						array(
							'card' => $bm_cards['promo-a'] ?? null,
							'slot' => 'promo-a',
						)
					);

					BuyMore_Template::part(
						'partials/card-promo',
						array(
							'card' => $bm_cards['promo-b'] ?? null,
							'slot' => 'promo-b',
						)
					);

					BuyMore_Template::part(
						'partials/card-promo',
						array(
							'card' => $bm_cards['promo-c'] ?? null,
							'slot' => 'promo-c',
						)
					);
					?>

					<div class="bm-grid__products" data-bm-products aria-live="polite">
						<?php
						BuyMore_Template::part(
							'partials/product-slots',
							array(
								'products' => $bm_products,
								'audience' => $bm_audience,
							)
						);
						?>
					</div>

					<?php
					BuyMore_Template::part(
						'partials/card-favourites',
						array(
							'card'     => $bm_cards['favourites'] ?? null,
							'products' => array_slice( $bm_products, 2 ),
						)
					);

					BuyMore_Template::part(
						'partials/card-promo',
						array(
							'card' => $bm_cards['feature'] ?? null,
							'slot' => 'feature',
						)
					);
					?>
				</div>
			</main>
		</div>
	</div>
</div>
