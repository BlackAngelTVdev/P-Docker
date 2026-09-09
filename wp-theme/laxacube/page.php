<?php
/**
 * Template de page statique.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="content-page">
	<div class="container">
		<div class="prose">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</section>

<?php
get_footer();