<?php
/**
 * LaxaCube - fonctions du thème.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LAXACUBE_VERSION', '1.0.0' );

/**
 * Support du thème.
 */
function laxacube_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'laxacube_setup' );

/**
 * Chargement des styles.
 */
function laxacube_scripts() {
	wp_enqueue_style( 'laxacube-style', get_stylesheet_uri(), array(), LAXACUBE_VERSION );
}
add_action( 'wp_enqueue_scripts', 'laxacube_scripts' );

/**
 * Meta description simple.
 */
function laxacube_meta_description() {
	if ( is_front_page() ) {
		echo '<meta name="description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'laxacube_meta_description' );