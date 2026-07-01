<?php
/**
 * Rendu du bloc Hero (ACF).
 *
 * @package bodyhouse-blocks
 *
 * @var array  $block      Données du bloc.
 * @var bool   $is_preview Aperçu dans l'éditeur.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$crumb_label   = get_field( 'crumb_label' ) ?: 'Body House';
$crumb_url     = get_field( 'crumb_url' ) ?: 'https://bodyhouse.fr/blog/';
$crumb_current = get_field( 'crumb_current' ) ?: 'Nos conseils sexo';
$title         = get_field( 'title' ) ?: 'Nos conseils';
$title_accent  = get_field( 'title_accent' ) ?: 'plaisir et sexualité';
$intro         = get_field( 'intro' );
$btn1_label    = get_field( 'btn1_label' );
$btn1_url      = get_field( 'btn1_url' ) ?: '#';
$btn2_label    = get_field( 'btn2_label' );
$btn2_url      = get_field( 'btn2_url' ) ?: '#';
$image         = get_field( 'image' );
$quote         = get_field( 'quote' );
$quote_author  = get_field( 'quote_author' );

// Image : valeur ACF si renseignée, sinon l'image par défaut du thème.
$image_url = $image ?: get_theme_file_uri( 'assets/images/intime-femme.jpg' );

// id d'ancre éventuel + classes.
$anchor = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
$class  = 'bh-hero wp-block-group has-text-color has-white-color has-text-background-color has-background';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . $block['className'];
}
?>
<section class="<?php echo esc_attr( $class ); ?>"<?php echo $anchor; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<nav class="bh-hero__breadcrumb" aria-label="Fil d'ariane">
		<a href="<?php echo esc_url( $crumb_url ); ?>"><?php echo esc_html( $crumb_label ); ?></a>
		<span class="bh-hero__breadcrumb-sep" aria-hidden="true">—</span>
		<span class="bh-hero__breadcrumb-current"><?php echo esc_html( $crumb_current ); ?></span>
	</nav>

	<div class="wp-block-columns are-vertically-aligned-center bh-hero__cols">
		<div class="wp-block-column is-vertically-aligned-center bh-hero__textcol" style="flex-basis:50%">
			<h1 class="wp-block-heading bh-hero__title has-heading-font-family has-hero-font-size"><?php echo esc_html( $title ); ?><br><span class="bh-hero__title-accent"><?php echo esc_html( $title_accent ); ?></span></h1>

			<?php if ( $intro ) : ?>
				<div class="bh-hero__intro has-large-font-size"><?php echo wp_kses_post( $intro ); ?></div>
			<?php endif; ?>

			<div class="wp-block-buttons bh-hero__btns">
				<?php if ( $btn1_label ) : ?>
					<div class="wp-block-button bh-btn bh-btn--light"><a class="wp-block-button__link has-text-color has-white-background-color has-background wp-element-button" href="<?php echo esc_url( $btn1_url ); ?>" style="border-radius:2px"><?php echo esc_html( $btn1_label ); ?></a></div>
				<?php endif; ?>
				<?php if ( $btn2_label ) : ?>
					<div class="wp-block-button bh-btn bh-btn--outline"><a class="wp-block-button__link has-border-color wp-element-button" href="<?php echo esc_url( $btn2_url ); ?>" style="border-color:#ffffff;border-width:1px;border-radius:2px"><?php echo esc_html( $btn2_label ); ?></a></div>
				<?php endif; ?>
			</div>
		</div>

		<div class="wp-block-column is-vertically-aligned-stretch bh-hero__imgcol" style="flex-basis:50%">
			<div class="bh-hero__media" style="background-image:url('<?php echo esc_url( $image_url ); ?>')"></div>
			<?php if ( $quote ) : ?>
				<div class="bh-quote-card">
					<div class="bh-quote-card__text"><?php echo wp_kses_post( $quote ); ?></div>
					<?php if ( $quote_author ) : ?>
						<p class="bh-quote-card__author"><?php echo esc_html( $quote_author ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
