<?php
/**
 * Enregistrement des blocs ACF Pro et de leurs champs (locaux, versionnés dans le thème).
 *
 * Les blocs ne s'enregistrent que si ACF Pro est actif. Chaque bloc a son dossier
 * dans /blocks/<nom>/ avec un block.json (métadonnées) et un render.php (rendu front+éditeur).
 *
 * @package bodyhouse-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enregistre les blocs via block.json (ACF lit "acf" dans le block.json).
 * register_block_type sur un répertoire contenant block.json suffit avec ACF Pro 6+.
 */
function bodyhouse_register_acf_blocks() {
	// ACF Pro requis (fonction présente uniquement si le plugin est actif).
	if ( ! function_exists( 'acf_register_block_type' ) && ! class_exists( 'ACF' ) ) {
		return;
	}

	$blocks = array( 'hero', 'themes' );
	foreach ( $blocks as $block ) {
		$dir = get_theme_file_path( "blocks/{$block}" );
		if ( file_exists( $dir . '/block.json' ) ) {
			register_block_type( $dir );
		}
	}
}
add_action( 'init', 'bodyhouse_register_acf_blocks', 5 );

/**
 * Champs ACF locaux. Définis en PHP pour être versionnés avec le thème
 * (pas de configuration manuelle dans l'admin, pas d'export/import à gérer).
 */
function bodyhouse_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/* ---- Bloc Hero ---------------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_bh_hero',
			'title'    => 'Body House — Hero',
			'fields'   => array(
				array(
					'key'           => 'field_bh_hero_crumb_label',
					'label'         => 'Fil d’ariane — libellé du lien',
					'name'          => 'crumb_label',
					'type'          => 'text',
					'default_value' => 'Body House',
				),
				array(
					'key'           => 'field_bh_hero_crumb_url',
					'label'         => 'Fil d’ariane — URL du lien',
					'name'          => 'crumb_url',
					'type'          => 'url',
					'default_value' => 'https://bodyhouse.fr/blog/',
				),
				array(
					'key'           => 'field_bh_hero_crumb_current',
					'label'         => 'Fil d’ariane — page courante',
					'name'          => 'crumb_current',
					'type'          => 'text',
					'default_value' => 'Nos conseils sexo',
				),
				array(
					'key'           => 'field_bh_hero_title',
					'label'         => 'Titre (1re ligne)',
					'name'          => 'title',
					'type'          => 'text',
					'default_value' => 'Nos conseils',
				),
				array(
					'key'           => 'field_bh_hero_title_accent',
					'label'         => 'Titre accentué (Playfair italique)',
					'name'          => 'title_accent',
					'type'          => 'text',
					'default_value' => 'plaisir et sexualité',
				),
				array(
					'key'           => 'field_bh_hero_intro',
					'label'         => 'Introduction',
					'name'          => 'intro',
					'type'          => 'wysiwyg',
					'tabs'          => 'all',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'default_value' => "Parce que le plaisir, ça s'apprend, ça s'explore et ça se vit pleinement. Ici, on répond à vos questions avec sincérité, sans jugement et sans tabou.",
				),
				array(
					'key'           => 'field_bh_hero_btn1_label',
					'label'         => 'Bouton 1 — libellé',
					'name'          => 'btn1_label',
					'type'          => 'text',
					'default_value' => 'Explorer les articles',
				),
				array(
					'key'           => 'field_bh_hero_btn1_url',
					'label'         => 'Bouton 1 — URL',
					'name'          => 'btn1_url',
					'type'          => 'text',
					'default_value' => '#explorer',
				),
				array(
					'key'           => 'field_bh_hero_btn2_label',
					'label'         => 'Bouton 2 — libellé',
					'name'          => 'btn2_label',
					'type'          => 'text',
					'default_value' => 'Par thématiques',
				),
				array(
					'key'           => 'field_bh_hero_btn2_url',
					'label'         => 'Bouton 2 — URL',
					'name'          => 'btn2_url',
					'type'          => 'text',
					'default_value' => '#themes',
				),
				array(
					'key'           => 'field_bh_hero_image',
					'label'         => 'Image',
					'name'          => 'image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),
				array(
					'key'           => 'field_bh_hero_quote',
					'label'         => 'Citation',
					'name'          => 'quote',
					'type'          => 'wysiwyg',
					'tabs'          => 'all',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'default_value' => 'En magasin comme ici, on partage le même savoir : celui qui libère, qui rassure et qui fait du bien. Sans tabou, avec expertise.',
				),
				array(
					'key'           => 'field_bh_hero_quote_author',
					'label'         => 'Citation — auteur',
					'name'          => 'quote_author',
					'type'          => 'text',
					'default_value' => 'Max & Anne-Laure | Fondateurs Body House',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/bh-hero',
					),
				),
			),
		)
	);

	/* ---- Bloc Thématiques --------------------------------------------- */
	acf_add_local_field_group(
		array(
			'key'      => 'group_bh_themes',
			'title'    => 'Body House — Thématiques',
			'fields'   => array(
				array(
					'key'           => 'field_bh_themes_overline',
					'label'         => 'Surtitre',
					'name'          => 'overline',
					'type'          => 'text',
					'default_value' => 'NOS THÉMATIQUES',
				),
				array(
					'key'           => 'field_bh_themes_title',
					'label'         => 'Titre',
					'name'          => 'title',
					'type'          => 'text',
					'default_value' => 'Explorez par thèmes',
				),
				array(
					'key'          => 'field_bh_themes_rows',
					'label'        => 'Thématiques',
					'name'         => 'rows',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Ajouter une thématique',
					'sub_fields'   => array(
						array(
							'key'           => 'field_bh_themes_row_image',
							'label'         => 'Vignette',
							'name'          => 'image',
							'type'          => 'image',
							'return_format' => 'url',
							'preview_size'  => 'thumbnail',
						),
						array(
							'key'   => 'field_bh_themes_row_title',
							'label' => 'Titre',
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_bh_themes_row_url',
							'label' => 'Lien (URL de la catégorie)',
							'name'  => 'url',
							'type'  => 'text',
						),
						array(
							'key'          => 'field_bh_themes_row_desc',
							'label'        => 'Description',
							'name'         => 'desc',
							'type'         => 'wysiwyg',
							'tabs'         => 'all',
							'toolbar'      => 'basic',
							'media_upload' => 0,
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'block',
						'operator' => '==',
						'value'    => 'acf/bh-themes',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'bodyhouse_register_acf_fields' );
