<?php
/** Fallback template for pages other than the front page. */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main class="buymore-home-message">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	}
	?>
</main>
<?php
get_footer();
