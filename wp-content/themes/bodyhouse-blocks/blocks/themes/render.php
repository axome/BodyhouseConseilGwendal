<?php
/**
 * Rendu du bloc Thématiques (ACF).
 *
 * @package bodyhouse-blocks
 *
 * @var array $block Données du bloc.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$overline = get_field( 'overline' ) ?: 'NOS THÉMATIQUES';
$title    = get_field( 'title' ) ?: 'Explorez par thèmes';
$rows     = get_field( 'rows' );

$anchor = ! empty( $block['anchor'] ) ? esc_attr( $block['anchor'] ) : 'themes';
$class  = 'bh-themes wp-block-group has-cream-background-color has-background';
if ( ! empty( $block['className'] ) ) {
	$class .= ' ' . $block['className'];
}
?>
<section class="<?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $anchor ); ?>" style="padding-top:80px;padding-right:14%;padding-bottom:80px;padding-left:14%">
	<p class="bh-overline"><?php echo esc_html( $overline ); ?></p>
	<h2 class="wp-block-heading bh-section-title has-title-font-size"><?php echo esc_html( $title ); ?></h2>

	<div class="bh-themes__list">
		<?php
		if ( $rows ) {
			foreach ( $rows as $row ) {
				$image = $row['image'] ?? '';
				// Résout l'image quelle que soit sa forme : ID média, tableau ACF, ou URL.
				if ( is_numeric( $image ) ) {
					$image = wp_get_attachment_image_url( (int) $image, 'large' );
				} elseif ( is_array( $image ) ) {
					$image = $image['url'] ?? '';
				}

				// bodyhouse_theme_row() gère l'échappement de chaque champ.
				echo bodyhouse_theme_row( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$image,
					$row['title'] ?? '',
					$row['url'] ?? '',
					$row['desc'] ?? ''
				);
			}
		} elseif ( ! empty( $block['data']['is_preview'] ) || is_admin() ) {
			echo '<p style="opacity:.6">Ajoutez des thématiques dans le panneau de droite.</p>';
		}
		?>
	</div>
</section>
