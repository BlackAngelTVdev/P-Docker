<?php
/**
 * Template générique (liste des articles).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="content-page">
	<div class="container">
		<div class="post-list">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'post-item' ); ?>>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="meta"><?php echo esc_html( get_the_date() ); ?> · <?php the_author(); ?></div>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 32, '…' ) ); ?></p>
						<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Lire la suite', 'aurora' ); ?> →</a>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Aucun article pour le moment.', 'aurora' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
get_footer();