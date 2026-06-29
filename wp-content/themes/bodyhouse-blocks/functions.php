<?php
/**
 * Body House Blocks — fonctions du thème.
 *
 * Thème de blocs (FSE) reproduisant le blog Body House sans Elementor.
 *
 * @package bodyhouse-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BODYHOUSE_BLOCKS_VERSION', '1.0.0' );

/**
 * Supports de thème.
 */
function bodyhouse_blocks_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_editor_style( 'assets/css/bodyhouse.css' );
}
add_action( 'after_setup_theme', 'bodyhouse_blocks_setup' );

/**
 * Feuille de style principale (front).
 */
function bodyhouse_blocks_styles() {
	wp_enqueue_style(
		'bodyhouse-blocks-main',
		get_theme_file_uri( 'assets/css/bodyhouse.css' ),
		array(),
		BODYHOUSE_BLOCKS_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'bodyhouse_blocks_styles' );

/**
 * Script de l'effet de zoom au scroll sur l'image du hero.
 */
function bodyhouse_blocks_scripts() {
	wp_enqueue_script(
		'bodyhouse-blocks-hero',
		get_theme_file_uri( 'assets/js/hero.js' ),
		array(),
		BODYHOUSE_BLOCKS_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'bodyhouse_blocks_scripts' );

/**
 * Enregistre la catégorie de compositions (patterns) du thème.
 */
function bodyhouse_blocks_pattern_category() {
	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'bodyhouse',
			array( 'label' => __( 'Body House', 'bodyhouse-blocks' ) )
		);
	}
}
add_action( 'init', 'bodyhouse_blocks_pattern_category' );

/**
 * Génère le markup d'une carte de thématique (section « Explorez par thèmes »).
 *
 * @param string $img_url URL de l'image.
 * @param string $title   Titre de la thématique.
 * @param string $link    URL de la catégorie.
 * @param string $desc    Description.
 * @return string HTML de la carte.
 */
function bodyhouse_theme_card( $img_url, $title, $link, $desc ) {
	$img_url = esc_url( $img_url );
	$link    = esc_url( $link );
	$title_e = wp_kses( $title, array() );
	$desc_e  = esc_html( $desc );

	return <<<HTML
<div class="bh-theme-card"><a class="bh-theme-card__img" href="{$link}"><img src="{$img_url}" alt="{$title_e}" loading="lazy"/></a>
<h3 class="bh-theme-card__title"><a href="{$link}">{$title_e}</a></h3>
<p class="bh-theme-card__desc">{$desc_e}</p>
<a class="bh-theme-card__btn" href="{$link}">Découvrir <span aria-hidden="true">→</span></a></div>
HTML;
}

/**
 * Génère une ligne de thématique (liste « Explorez par thèmes ») :
 * vignette + titre + description à gauche, lien « Découvrir → » à droite.
 *
 * @param string $img_url URL de la vignette.
 * @param string $title   Titre de la thématique.
 * @param string $link    URL de la catégorie.
 * @param string $desc    Description.
 * @return string HTML de la ligne.
 */
function bodyhouse_theme_row( $img_url, $title, $link, $desc ) {
	$img_url = esc_url( $img_url );
	$link    = esc_url( $link );
	$title_e = wp_kses( $title, array() );
	$desc_e  = esc_html( $desc );

	return <<<HTML
<a class="bh-theme-row" href="{$link}">
	<span class="bh-theme-row__img"><img src="{$img_url}" alt="{$title_e}" loading="lazy"/></span>
	<span class="bh-theme-row__text">
		<span class="bh-theme-row__title">{$title_e}</span>
		<span class="bh-theme-row__desc">{$desc_e}</span>
	</span>
	<span class="bh-theme-row__cta">Découvrir <span aria-hidden="true">→</span></span>
</a>
HTML;
}

