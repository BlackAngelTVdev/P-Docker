<?php
/**
 * Template 404 élégant.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="content-page" style="text-align:center;padding:120px 0;">
	<div class="container">
		<div class="num grad-text" style="font-family:var(--font-display);font-size:6rem;font-weight:700;line-height:1;">404</div>
		<h2 style="margin:20px 0 10px;"><?php esc_html_e( 'Cette page s’est perdue dans le cloud', 'aurora' ); ?></h2>
		<p style="color:var(--text-dim);margin-bottom:30px;"><?php esc_html_e( 'La page que vous cherchez n’existe pas (ou plus).', 'aurora' ); ?></p>
		<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Retour à l’accueil', 'aurora' ); ?> →</a>
	</div>
</section>

<?php
get_footer();