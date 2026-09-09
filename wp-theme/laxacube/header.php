<?php
/**
 * En-tête du thème LaxaCube.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$assets = get_template_directory_uri() . '/assets/img/';
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

<header class="site-header">
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>#accueil" title="LaxaCube - Accueil">
		<img src="<?php echo esc_url( $assets . 'logo.png' ); ?>" alt="Logo LaxaCube">
	</a>

	<nav class="main-nav" aria-label="Navigation principale">
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#accueil">Accueil</a></li>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#rejnnoindre">Rejoindre</a></li>
			<li><a href="<?php echo esc_url( home_url( '/wiki/' ) ); ?>">Wiki</a></li>
			<li><a href="<?php echo esc_url( home_url( '/images/' ) ); ?>">Images</a></li>
			<li><a class="ext" href="https://laxacube.craftingstore.net/" target="_blank" rel="noopener">Boutique</a></li>
			<li><a class="ext" href="https://bit.ly/discord-laxacube" target="_blank" rel="noopener">Discord</a></li>
		</ul>
	</nav>
</header>

<?php if ( ! is_front_page() ) : ?>
<div class="page-hero">
	<h1><?php echo esc_html( get_the_title() ? get_the_title() : get_bloginfo( 'name' ) ); ?></h1>
</div>
<?php endif; ?>