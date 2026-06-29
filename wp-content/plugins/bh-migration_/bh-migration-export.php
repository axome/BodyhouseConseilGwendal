<?php
/**
 * Plugin Name: BH Migration - Export
 * Description: Exporte les articles (post) et Comment Choisir (how-to-choose) avec toutes les métadonnées Elementor, Yoast SEO, catégories et médias.
 * Version: 1.0.0
 *
 * Usage : Activer le plugin puis aller sur /wp-admin/tools.php?page=bh-migration-export
 * Ou via WP-CLI : wp eval-file wp-content/plugins/bh-migration/bh-migration-export.php --export
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_CLI' ) ) {
    exit;
}

class BH_Migration_Export {

    private $export_dir;
    private $media_dir;

    public function __construct() {
        if ( defined( 'WP_CLI' ) ) {
            return;
        }
        add_action( 'admin_menu', [ $this, 'add_menu' ] );
        add_action( 'admin_init', [ $this, 'handle_export' ] );
    }

    public function add_menu() {
        add_management_page(
            'BH Migration Export',
            'BH Migration Export',
            'manage_options',
            'bh-migration-export',
            [ $this, 'render_page' ]
        );
    }

    public function render_page() {
        $counts = $this->get_counts();
        ?>
        <div class="wrap">
            <h1>BH Migration - Export</h1>
            <p>Ce plugin exporte les articles et les "Comment choisir" avec :</p>
            <ul style="list-style: disc; padding-left: 20px;">
                <li>Contenu Elementor (postmeta)</li>
                <li>Données Yoast SEO</li>
                <li>Catégories et taxonomies</li>
                <li>Images à la une et médias intégrés</li>
                <li>Extraits et métadonnées custom</li>
            </ul>

            <h2>Contenu à exporter</h2>
            <table class="widefat" style="max-width: 400px;">
                <tr><td>Articles (post)</td><td><strong><?php echo $counts['post']; ?></strong></td></tr>
                <tr><td>Comment choisir (how-to-choose)</td><td><strong><?php echo $counts['how-to-choose']; ?></strong></td></tr>
                <tr><td>Médias associés (estimation)</td><td><strong><?php echo $counts['media']; ?></strong></td></tr>
            </table>

            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field( 'bh_migration_export', 'bh_export_nonce' ); ?>

                <h3>Options d'export</h3>
                <label>
                    <input type="checkbox" name="include_posts" value="1" checked> Articles (post)
                </label><br>
                <label>
                    <input type="checkbox" name="include_htc" value="1" checked> Comment choisir (how-to-choose)
                </label><br>
                <label>
                    <input type="checkbox" name="include_media" value="1" checked> Télécharger les médias
                </label><br><br>

                <input type="submit" name="bh_do_export" class="button button-primary" value="Lancer l'export">
            </form>
        </div>
        <?php
    }

    private function get_counts() {
        $statuses = [ 'publish', 'draft', 'pending', 'private', 'future', 'trash' ];

        $post_counts = wp_count_posts( 'post' );
        $total_posts = 0;
        foreach ( $statuses as $s ) {
            $total_posts += (int) ( $post_counts->$s ?? 0 );
        }

        $htc_counts = wp_count_posts( 'how-to-choose' );
        $total_htc = 0;
        foreach ( $statuses as $s ) {
            $total_htc += (int) ( $htc_counts->$s ?? 0 );
        }

        $media_count = 0;
        $post_ids = get_posts([
            'post_type' => [ 'post', 'how-to-choose' ],
            'post_status' => 'any',
            'fields' => 'ids',
            'numberposts' => -1,
        ]);
        foreach ( $post_ids as $pid ) {
            if ( has_post_thumbnail( $pid ) ) {
                $media_count++;
            }
        }

        return [
            'post' => $total_posts,
            'how-to-choose' => $total_htc,
            'media' => $media_count,
        ];
    }

    public function handle_export() {
        if ( ! isset( $_POST['bh_do_export'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['bh_export_nonce'] ?? '', 'bh_migration_export' ) ) {
            wp_die( 'Nonce invalide.' );
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Permissions insuffisantes.' );
        }

        $include_posts = ! empty( $_POST['include_posts'] );
        $include_htc = ! empty( $_POST['include_htc'] );
        $include_media = ! empty( $_POST['include_media'] );

        $data = $this->run_export( $include_posts, $include_htc, $include_media );

        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="bh-migration-export-' . date('Y-m-d-His') . '.json"' );
        echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
        exit;
    }

    public function run_export( $include_posts = true, $include_htc = true, $include_media = true ) {
        $post_types = [];
        if ( $include_posts ) $post_types[] = 'post';
        if ( $include_htc ) $post_types[] = 'how-to-choose';

        if ( empty( $post_types ) ) {
            return [ 'error' => 'Aucun type de contenu sélectionné.' ];
        }

        $posts = get_posts([
            'post_type' => $post_types,
            'post_status' => 'any',
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'ASC',
        ]);

        $export = [
            'meta' => [
                'exported_at' => current_time( 'mysql' ),
                'source_url' => home_url(),
                'wp_version' => get_bloginfo( 'version' ),
                'total_posts' => count( $posts ),
            ],
            'categories' => $this->export_categories(),
            'posts' => [],
            'media' => [],
        ];

        $media_ids = [];

        foreach ( $posts as $post ) {
            $post_data = $this->export_single_post( $post );
            $export['posts'][] = $post_data;

            if ( $include_media && ! empty( $post_data['featured_image_id'] ) ) {
                $media_ids[] = $post_data['featured_image_id'];
            }

            if ( $include_media && ! empty( $post_data['content_media_ids'] ) ) {
                $media_ids = array_merge( $media_ids, $post_data['content_media_ids'] );
            }
        }

        $media_ids = array_unique( array_filter( $media_ids ) );

        if ( $include_media && ! empty( $media_ids ) ) {
            foreach ( $media_ids as $media_id ) {
                $media_data = $this->export_media( $media_id );
                if ( $media_data ) {
                    $export['media'][] = $media_data;
                }
            }
        }

        $export['meta']['total_media'] = count( $export['media'] );
        $export['meta']['total_categories'] = count( $export['categories'] );

        return $export;
    }

    private function export_single_post( $post ) {
        $all_meta = get_post_meta( $post->ID );
        $thumbnail_id = get_post_thumbnail_id( $post->ID );
        $categories = wp_get_post_categories( $post->ID, [ 'fields' => 'all' ] );

        $elementor_meta = [];
        $yoast_meta = [];
        $other_meta = [];

        foreach ( $all_meta as $key => $values ) {
            $value = maybe_unserialize( $values[0] );

            if ( strpos( $key, '_elementor' ) === 0 ) {
                $elementor_meta[ $key ] = $value;
            } elseif ( strpos( $key, '_yoast' ) === 0 || strpos( $key, '_wpseo' ) === 0 ) {
                $yoast_meta[ $key ] = $value;
            } elseif ( $key[0] !== '_' || in_array( $key, [ '_thumbnail_id', '_wp_page_template' ] ) ) {
                $other_meta[ $key ] = $value;
            }
        }

        $content_media_ids = $this->extract_media_from_content( $post->post_content );
        $elementor_media_ids = $this->extract_media_from_elementor( $elementor_meta );
        $all_content_media = array_unique( array_merge( $content_media_ids, $elementor_media_ids ) );

        $cat_data = [];
        foreach ( $categories as $cat ) {
            $cat_data[] = [
                'term_id' => $cat->term_id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'parent_slug' => $cat->parent ? get_term( $cat->parent )->slug : null,
            ];
        }

        return [
            'source_id' => $post->ID,
            'post_type' => $post->post_type,
            'title' => $this->fix_encoding( $post->post_title ),
            'slug' => $post->post_name,
            'status' => $post->post_status,
            'content' => $this->fix_encoding( $post->post_content ),
            'excerpt' => $this->fix_encoding( $post->post_excerpt ),
            'date' => $post->post_date,
            'date_gmt' => $post->post_date_gmt,
            'modified' => $post->post_modified,
            'author_email' => get_the_author_meta( 'email', $post->post_author ),
            'featured_image_id' => $thumbnail_id ? (int) $thumbnail_id : null,
            'featured_image_url' => $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : null,
            'categories' => $cat_data,
            'elementor_meta' => $elementor_meta,
            'yoast_meta' => $yoast_meta,
            'other_meta' => $other_meta,
            'content_media_ids' => $all_content_media,
        ];
    }

    private function export_categories() {
        $categories = get_categories([
            'hide_empty' => false,
            'orderby' => 'term_id',
        ]);

        $result = [];
        foreach ( $categories as $cat ) {
            $result[] = [
                'term_id' => $cat->term_id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'description' => $cat->description,
                'parent_slug' => $cat->parent ? get_term( $cat->parent )->slug : null,
            ];
        }
        return $result;
    }

    private function export_media( $attachment_id ) {
        $attachment = get_post( $attachment_id );
        if ( ! $attachment || $attachment->post_type !== 'attachment' ) {
            return null;
        }

        $file_url = wp_get_attachment_url( $attachment_id );
        $file_path = get_attached_file( $attachment_id );
        $metadata = wp_get_attachment_metadata( $attachment_id );

        return [
            'source_id' => $attachment_id,
            'title' => $attachment->post_title,
            'alt_text' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
            'caption' => $attachment->post_excerpt,
            'description' => $attachment->post_content,
            'mime_type' => $attachment->post_mime_type,
            'url' => $file_url,
            'filename' => basename( $file_path ),
            'metadata' => $metadata,
        ];
    }

    private function extract_media_from_content( $content ) {
        $media_ids = [];
        if ( preg_match_all( '/wp-image-(\d+)/', $content, $matches ) ) {
            $media_ids = array_map( 'intval', $matches[1] );
        }
        return $media_ids;
    }

    private function extract_media_from_elementor( $elementor_meta ) {
        $media_ids = [];
        $json = wp_json_encode( $elementor_meta );
        if ( preg_match_all( '/"id"\s*:\s*(\d+)/', $json, $matches ) ) {
            foreach ( $matches[1] as $id ) {
                $id = (int) $id;
                if ( $id > 0 && get_post_type( $id ) === 'attachment' ) {
                    $media_ids[] = $id;
                }
            }
        }
        return array_unique( $media_ids );
    }

    private function fix_encoding( $text ) {
        if ( empty( $text ) ) return $text;

        // The source DB has broken unicode escapes without backslash:
        //   u00e9 (é), u00e8 (è), u00e0 (à), u00a0 (nbsp), u2019 ('), etc.
        // Target: u00XX (Latin-1 Supplement) and u20XX (punctuation/spaces)
        $text = preg_replace_callback( '/u(00[0-9a-fA-F]{2}|20[0-9a-fA-F]{2})/', function( $m ) {
            $char = json_decode( '"\\u' . $m[1] . '"' );
            return $char !== null ? $char : $m[0];
        }, $text );

        return $text;
    }
}

$bh_migration_export = new BH_Migration_Export();

if ( defined( 'WP_CLI' ) && in_array( '--export', $GLOBALS['argv'] ?? [] ) ) {
    $data = $bh_migration_export->run_export( true, true, true );
    $filename = 'bh-migration-export-' . date('Y-m-d-His') . '.json';
    file_put_contents( $filename, json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
    WP_CLI::success( "Export terminé : {$filename} ({$data['meta']['total_posts']} posts, {$data['meta']['total_media']} médias)" );
}
