<?php
/**
 * Toolbar: section heading, audience tabs, filters and search.
 *
 * The tabs are links so the filter works without JavaScript; the script
 * intercepts them and swaps the tiles over AJAX instead.
 *
 * @package BuyMore_Explore
 * @var array<string,mixed> $data Holds 'audience'.
 */

defined( 'ABSPATH' ) || exit;

$bm_current   = isset( $data['audience'] ) ? (string) $data['audience'] : '';
$bm_audiences = BuyMore_Query::audiences();
$bm_base      = remove_query_arg( 'bm_audience' );
?>
<div class="bm-toolbar">
	<h1 class="bm-toolbar__title"><?php echo esc_html( (string) buymore_get_option( 'page_title' ) ); ?></h1>

	<nav class="bm-tabs" aria-label="<?php esc_attr_e( 'Filter products by audience', 'buymore-explore' ); ?>">
		<a class="bm-tabs__tab<?php echo '' === $bm_current ? ' is-active' : ''; ?>"
			href="<?php echo esc_url( $bm_base ); ?>"
			data-bm-tab=""
			<?php echo '' === $bm_current ? 'aria-current="true"' : ''; ?>>
			<?php buymore_the_icon( 'grid', 15 ); ?>
			<span><?php echo esc_html( (string) buymore_get_option( 'filter_all_label' ) ); ?></span>
		</a>

		<?php foreach ( $bm_audiences as $bm_term ) : ?>
			<?php $bm_is_current = ( $bm_current === $bm_term->slug ); ?>
			<a class="bm-tabs__tab<?php echo $bm_is_current ? ' is-active' : ''; ?>"
				href="<?php echo esc_url( add_query_arg( 'bm_audience', $bm_term->slug, $bm_base ) ); ?>"
				data-bm-tab="<?php echo esc_attr( $bm_term->slug ); ?>"
				<?php echo $bm_is_current ? 'aria-current="true"' : ''; ?>>
				<?php buymore_the_icon( BuyMore_Query::audience_icon( $bm_term ), 15 ); ?>
				<span><?php echo esc_html( $bm_term->name ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>

	<div class="bm-toolbar__end">
		<button type="button" class="bm-chip" data-bm-filters aria-pressed="false">
			<?php buymore_the_icon( 'filters', 16 ); ?>
			<span><?php echo esc_html( (string) buymore_get_option( 'filters_label' ) ); ?></span>
		</button>

		<div class="bm-search" data-bm-search>
			<label class="screen-reader-text" for="bm-search-input"><?php esc_html_e( 'Search products', 'buymore-explore' ); ?></label>
			<input type="search" id="bm-search-input" class="bm-search__input" data-bm-search-input
				placeholder="<?php echo esc_attr( (string) buymore_get_option( 'search_placeholder' ) ); ?>">
			<button type="button" class="bm-iconbtn bm-search__btn" data-bm-search-toggle aria-expanded="false">
				<?php buymore_the_icon( 'search', 17 ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Search products', 'buymore-explore' ); ?></span>
			</button>
		</div>
	</div>
</div>
