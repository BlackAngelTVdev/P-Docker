<?php
/**
 * Template d'article unique.
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
				?>
				<div class="meta" style="color:var(--text-dim);margin-bottom:18px;">
					<?php echo esc_html( get_the_date() ); ?> · <?php the_author(); ?>
				</div>
				<?php
				the_content();
			endwhile;
			?>
		</div>
	</div>
</section>

<?php
get_footer();