<?php
/**
 * Aurora - fonctions du thème.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AURORA_VERSION', '1.0.0' );

/**
 * Support du thème.
 */
function aurora_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	register_nav_menus( array(
		'primary' => __( 'Menu principal', 'aurora' ),
	) );
}
add_action( 'after_setup_theme', 'aurora_setup' );

/**
 * Chargement des styles (CSS du thème) et des polices Google Fonts.
 */
function aurora_scripts() {
	wp_enqueue_style( 'aurora-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap', array(), null );
	wp_enqueue_style( 'aurora-style', get_stylesheet_uri(), array( 'aurora-fonts' ), AURORA_VERSION );
}
add_action( 'wp_enqueue_scripts', 'aurora_scripts' );

/**
 * Meta description simple.
 */
function aurora_meta_description() {
	if ( is_front_page() ) {
		echo '<meta name="description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'aurora_meta_description' );

/**
 * Extraits plus lisibles.
 */
function aurora_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'aurora_excerpt_length' );

function aurora_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'aurora_excerpt_more' );