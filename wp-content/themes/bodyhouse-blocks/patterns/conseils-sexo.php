<?php
/**
 * Title: Page Conseils Sexo
 * Slug: bodyhouse/conseils-sexo
 * Categories: bodyhouse
 * Inserter: true
 *
 * Reproduction pixel-perfect de https://bodyhouse.fr/blog/conseils-sexo/
 * en blocs Gutenberg natifs. Les URLs d'images pointent vers les assets du thème.
 *
 * @package bodyhouse-blocks
 */

$img = get_theme_file_uri( 'assets/images/' );
?>
<!-- wp:group {"tagName":"section","className":"bh-hero","style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"8%","right":"8%"}}},"backgroundColor":"text","textColor":"white","layout":{"type":"constrained","contentSize":"1140px"}} -->
<section class="wp-block-group bh-hero has-text-color has-white-color has-text-background-color has-background" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:columns {"verticalAlignment":"center","className":"bh-hero__cols"} -->
<div class="wp-block-columns are-vertically-aligned-center bh-hero__cols"><!-- wp:column {"verticalAlignment":"center","width":"50%","className":"bh-hero__textcol"} -->
<div class="wp-block-column is-vertically-aligned-center bh-hero__textcol" style="flex-basis:50%"><!-- wp:html -->
<nav class="bh-hero__breadcrumb" aria-label="Fil d'ariane"><a href="https://bodyhouse.fr/blog/">Body House</a><span class="bh-hero__breadcrumb-sep" aria-hidden="true">—</span><span class="bh-hero__breadcrumb-current">Nos conseils sexo</span></nav>
<!-- /wp:html -->

<!-- wp:heading {"level":1,"className":"bh-hero__title","fontSize":"hero","fontFamily":"heading"} -->
<h1 class="wp-block-heading bh-hero__title has-heading-font-family has-hero-font-size">Nos conseils<br><span class="bh-hero__title-accent">plaisir et sexualité</span></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"bh-hero__intro","style":{"spacing":{"margin":{"top":"24px","bottom":"32px"}}},"fontSize":"large"} -->
<p class="bh-hero__intro has-large-font-size" style="margin-top:24px;margin-bottom:32px">Parce que le plaisir, ça s'apprend, ça s'explore et ça se vit pleinement. Ici, on répond à vos questions avec sincérité, sans jugement et sans tabou.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"bh-hero__btns"} -->
<div class="wp-block-buttons bh-hero__btns"><!-- wp:button {"backgroundColor":"white","textColor":"text","className":"bh-btn bh-btn--light","style":{"border":{"radius":"2px"}}} -->
<div class="wp-block-button bh-btn bh-btn--light"><a class="wp-block-button__link has-text-color has-text-text-color has-white-background-color has-background wp-element-button" href="#explorer" style="border-radius:2px">Explorer les articles</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"bh-btn bh-btn--outline","style":{"border":{"radius":"2px","width":"1px","color":"#ffffff"}}} -->
<div class="wp-block-button bh-btn bh-btn--outline"><a class="wp-block-button__link has-border-color wp-element-button" href="#themes" style="border-color:#ffffff;border-width:1px;border-radius:2px">Par thématiques</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"stretch","width":"50%","className":"bh-hero__imgcol"} -->
<div class="wp-block-column is-vertically-aligned-stretch bh-hero__imgcol" style="flex-basis:50%"><!-- wp:html -->
<div class="bh-hero__media" style="background-image:url('<?php echo esc_url( $img . 'intime-femme.jpg' ); ?>')"></div>
<div class="bh-quote-card">
<p class="bh-quote-card__text">"En magasin comme ici, on partage le même savoir : celui qui libère, qui rassure et qui fait du bien. Sans tabou, avec expertise."</p>
<p class="bh-quote-card__author">Max &amp; Anne-Laure | Fondateurs Body House</p>
</div>
<!-- /wp:html --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"bh-latest","anchor":"explorer","style":{"spacing":{"padding":{"top":"60px","bottom":"60px","left":"8%","right":"8%"}}},"layout":{"type":"constrained","contentSize":"1140px"}} -->
<section class="wp-block-group bh-latest" id="explorer" style="padding-top:60px;padding-right:8%;padding-bottom:60px;padding-left:8%"><!-- wp:paragraph {"className":"bh-overline"} -->
<p class="bh-overline">À la une</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"bh-section-title","fontSize":"title"} -->
<h2 class="wp-block-heading bh-section-title has-title-font-size">Les derniers articles</h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"bh-latest__grid","style":{"spacing":{"margin":{"top":"35px"}}}} -->
<div class="wp-block-columns bh-latest__grid" style="margin-top:35px"><!-- wp:column {"width":"58%"} -->
<div class="wp-block-column" style="flex-basis:58%"><!-- wp:query {"queryId":10,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"bh-card-feature"} -->
<div class="wp-block-query bh-card-feature"><!-- wp:post-template -->
<!-- wp:post-featured-image {"isLink":true,"height":"625px"} /-->

<!-- wp:post-terms {"term":"category","className":"bh-card__cat"} /-->

<!-- wp:post-title {"isLink":true,"level":2,"fontSize":"x-large","fontFamily":"heading"} /-->

<!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false} /-->

<!-- wp:read-more {"content":"LIRE","className":"bh-readmore"} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"42%"} -->
<div class="wp-block-column" style="flex-basis:42%"><!-- wp:query {"queryId":11,"query":{"perPage":3,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"bh-card-list"} -->
<div class="wp-block-query bh-card-list"><!-- wp:post-template {"style":{"spacing":{"blockGap":"16px"}}} -->
<!-- wp:columns {"verticalAlignment":"center","className":"bh-card-row"} -->
<div class="wp-block-columns are-vertically-aligned-center bh-card-row"><!-- wp:column {"verticalAlignment":"center","width":"30%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:30%"><!-- wp:post-featured-image {"isLink":true,"style":{"border":{"radius":"5px"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"70%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:70%"><!-- wp:post-terms {"term":"category","className":"bh-card__cat"} /-->

<!-- wp:post-title {"isLink":true,"level":2,"fontSize":"large","fontFamily":"heading"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:buttons {"className":"bh-latest__more","layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"32px"}}}} -->
<div class="wp-block-buttons bh-latest__more" style="margin-top:32px"><!-- wp:button {"className":"bh-btn bh-btn--link"} -->
<div class="wp-block-button bh-btn bh-btn--link"><a class="wp-block-button__link wp-element-button" href="/blog/">Voir tous les articles</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"bh-themes","anchor":"themes","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"8%","right":"8%"}}},"backgroundColor":"cream","layout":{"type":"constrained","contentSize":"1140px"}} -->
<section class="wp-block-group bh-themes has-cream-background-color has-background" id="themes" style="padding-top:80px;padding-right:14%;padding-bottom:80px;padding-left:14%"><!-- wp:paragraph {"className":"bh-overline"} -->
<p class="bh-overline">NOS THÉMATIQUES</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"bh-section-title","fontSize":"title"} -->
<h2 class="wp-block-heading bh-section-title has-title-font-size">Explorez par thèmes</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<div class="bh-themes__list">
<?php
echo bodyhouse_theme_row( $img . 'vignette-masturbation-couple.jpg', 'Explorez le plaisir', '/category/articles-explorer-le-plaisir/', "Des techniques concrètes et des étapes claires pour passer à l'action, seul·e, à deux, ou plus…" );
echo bodyhouse_theme_row( $img . 'comprendre-son-corps.jpg', 'Comprendre son corps', '/category/articles-comprendre-son-corps/', 'Anatomie, orgasme, libido… Tout ce que vous avez toujours voulu savoir, enfin expliqué simplement.' );
echo bodyhouse_theme_row( $img . 'vivre-sa-sexualite.jpg', 'Vivre sa sexualité', '/category/articles-vivre-sa-sexualite/', 'Couple, grossesse, image de soi… La sexualité dans toutes ses formes, à toutes les étapes de la vie.' );
echo bodyhouse_theme_row( $img . 'comment-choisir.jpg', 'Comment choisir ?', '/category/articles-comment-choisir/', "Guides d'achat, tests et comparatifs pour trouver le produit qui vous correspond vraiment." );
echo bodyhouse_theme_row( $img . 'raison-petite-wand-rose-vibrante-utilisation-1024x1024.jpg', 'Bien utiliser', '/category/articles-bien-utiliser/', 'Conseils pratiques et bonnes pratiques pour profiter pleinement de chaque produit.' );
echo bodyhouse_theme_row( $img . 'vignette-comment-nettoyer-son-sextoy-1024x1024.jpg', 'Entretien & hygiène', '/category/articles-entretien-hygiene/', 'Nettoyage, rangement, conservation : tout pour garder vos produits impeccables longtemps.' );
?>
</div>
<!-- /wp:html --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"bh-observatory","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"8%","right":"8%"}}},"backgroundColor":"text","textColor":"white","layout":{"type":"constrained","contentSize":"760px"}} -->
<section class="wp-block-group bh-observatory has-text-color has-white-color has-text-background-color has-background" style="padding-top:120px;padding-right:8%;padding-bottom:120px;padding-left:8%"><!-- wp:paragraph {"align":"center","className":"bh-overline bh-observatory__over"} -->
<p class="has-text-align-center bh-overline bh-observatory__over">L'observatoire du plaisir</p>
<!-- /wp:paragraph -->
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"bh-stat","fontSize":"stat","fontFamily":"display"} -->
<p class="has-text-align-center bh-stat has-display-font-family has-stat-font-size">35%</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"bh-stat__label","fontSize":"large"} -->
<p class="has-text-align-center bh-stat__label has-large-font-size">des françaises sont satisfaites sexuellement.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","className":"bh-stat__desc","style":{"spacing":{"margin":{"top":"16px"}}}} -->
<p class="has-text-align-center bh-stat__desc" style="margin-top:16px">Des chiffres issus des grandes études sur la sexualité, réunis et décryptés par Body House pour mieux comprendre nos désirs et nos pratiques.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"48px"}}}} -->
<div class="wp-block-buttons" style="margin-top:48px"><!-- wp:button {"backgroundColor":"white","textColor":"text","className":"bh-btn bh-btn--light","style":{"border":{"radius":"2px"}}} -->
<div class="wp-block-button bh-btn bh-btn--light"><a class="wp-block-button__link has-text-color has-text-text-color has-white-background-color has-background wp-element-button" href="/blog/observatoire/" style="border-radius:2px">Voir tous les chiffres</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"bh-faq","style":{"spacing":{"padding":{"top":"80px","bottom":"80px","left":"8%","right":"8%"}}},"layout":{"type":"constrained","contentSize":"820px"}} -->
<section class="wp-block-group bh-faq" style="padding-top:80px;padding-right:8%;padding-bottom:80px;padding-left:8%"><!-- wp:columns {"verticalAlignment":"center","className":"bh-faq__cols"} -->
<div class="wp-block-columns are-vertically-aligned-center bh-faq__cols"><!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%"><!-- wp:paragraph {"className":"bh-overline"} -->
<p class="bh-overline">Ici, pas de tabous</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"bh-section-title","fontSize":"title"} -->
<h2 class="wp-block-heading bh-section-title has-title-font-size">Vos questions</h2>
<!-- /wp:heading -->

<!-- wp:separator {"className":"bh-divider","style":{"spacing":{"margin":{"top":"24px","bottom":"24px"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity bh-divider"/>
<!-- /wp:separator -->

<!-- wp:details {"className":"bh-faq__item"} -->
<details class="wp-block-details bh-faq__item"><summary>Comment sont choisis les sujets traités ?</summary><!-- wp:paragraph -->
<p>Les sujets viennent des vraies questions que vous nous posez en boutique, par email ou sur les réseaux sociaux. On complète avec les thématiques les plus recherchées sur le web et les sujets que nos équipes jugent utiles d'aborder.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><b><u>L'objectif est simple :</u></b> répondre à ce que vous voulez vraiment savoir, pas à ce qu'on pense que vous devriez savoir.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"className":"bh-faq__item"} -->
<details class="wp-block-details bh-faq__item"><summary>Qui écrit les articles chez Body House ?</summary><!-- wp:paragraph -->
<p>Les sujets sont définis en interne par notre équipe, selon nos thématiques du moment et les besoins de nos lecteurs. La rédaction est ensuite confiée à des rédacteurs et rédactrices expert·es, formé·es en sexologie. Chaque article est pensé pour allier rigueur, accessibilité et absence totale de jugement.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"className":"bh-faq__item"} -->
<details class="wp-block-details bh-faq__item"><summary>Les articles Body House sont-ils vérifiés par des professionnels ?</summary><!-- wp:paragraph -->
<p>Oui. Chaque article est rédigé et relu par nos experts internes, formés en sexologie, avec plusieurs années d'expérience terrain acquises en boutique. On s'appuie également sur des sources sérieuses : études scientifiques, données de santé publique et publications spécialisées, toujours citées. Pour toute question de santé personnelle et spécifique, nous recommandons de consulter un professionnel de santé.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"className":"bh-faq__item"} -->
<details class="wp-block-details bh-faq__item"><summary>Comment trouver un article sur un sujet précis ?</summary><!-- wp:paragraph -->
<p>Utilisez la barre de recherche en haut du blog ou parcourez nos thématiques pour trouver rapidement l'article qui répond à votre question.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"className":"bh-faq__item"} -->
<details class="wp-block-details bh-faq__item"><summary>Puis-je vous suggérer un sujet d'article ?</summary><!-- wp:paragraph -->
<p>Oui, et on adore ça. Envoyez-nous votre suggestion par email ou via notre formulaire de contact. Si le sujet correspond à notre ligne éditoriale, on le programme. Vos questions sont notre meilleure source d'inspiration.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"42%","className":"bh-faq__imgcol"} -->
<div class="wp-block-column is-vertically-aligned-center bh-faq__imgcol" style="flex-basis:42%"><!-- wp:html -->
<div class="bh-faq__media" style="background-image:url('<?php echo esc_url( $img . 'intime-femme.jpg' ); ?>')"></div>
<!-- /wp:html --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
