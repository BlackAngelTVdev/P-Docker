<?php
/**
 * En-tête du thème Aurora.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="nav" id="aurora-nav">
	<div class="container">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="logo">✦</span>
			<span><?php bloginfo( 'name' ); ?></span>
		</a>

		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'nav-menu',
			'fallback_cb'    => 'wp_page_menu',
			'depth'          => 1,
		) );
		?>

		<a class="nav-cta" href="#contact"><?php esc_html_e( 'Démarrer un projet', 'aurora' ); ?></a>
	</div>
</nav>

<?php if ( ! is_front_page() ) : ?>
<div class="page-hero">
	<h1><?php echo esc_html( get_the_title() ? get_the_title() : get_bloginfo( 'name' ) ); ?></h1>
</div>
<?php endif; ?>