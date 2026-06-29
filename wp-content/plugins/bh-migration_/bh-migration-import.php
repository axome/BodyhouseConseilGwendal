<?php

/**
 * Plugin Name: BH Migration - Import
 * Description: Importe les articles et Comment Choisir depuis un fichier JSON exporté par BH Migration Export.
 * Version: 1.0.0
 *
 * Usage : Activer le plugin puis aller sur /wp-admin/tools.php?page=bh-migration-import
 */

if (! defined('ABSPATH')) {
    exit;
}

class BH_Migration_Import
{

    private $id_map = [];
    private $media_map = [];
    private $category_map = [];
    private $log = [];

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'handle_import']);
    }

    public function add_menu()
    {
        add_management_page(
            'BH Migration Import',
            'BH Migration Import',
            'manage_options',
            'bh-migration-import',
            [$this, 'render_page']
        );
    }

    public function render_page()
    {
?>
        <div class="wrap">
            <h1>BH Migration - Import</h1>
            <p>Uploadez le fichier JSON généré par le plugin d'export.</p>

            <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 4px;">
                <strong>Prérequis :</strong>
                <ul style="list-style: disc; padding-left: 20px;">
                    <li>Le Custom Post Type <code>how-to-choose</code> doit être enregistré sur ce site (thème enfant ou plugin)</li>
                    <li>Elementor doit être installé et activé</li>
                    <li>Yoast SEO doit être installé si vous importez les données SEO</li>
                </ul>
            </div>

            <form method="post" enctype="multipart/form-data" style="margin-top: 20px;">
                <?php wp_nonce_field('bh_migration_import', 'bh_import_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th>Fichier JSON d'export</th>
                        <td><input type="file" name="import_file" accept=".json" required></td>
                    </tr>
                    <tr>
                        <th>Options</th>
                        <td>
                            <label>
                                <input type="checkbox" name="import_media" value="1" checked>
                                Télécharger et importer les médias depuis le serveur source
                            </label><br>
                            <label>
                                <input type="checkbox" name="skip_existing" value="1" checked>
                                Ignorer les articles déjà existants (même slug)
                            </label><br>
                            <label>
                                <input type="checkbox" name="dry_run" value="1">
                                Mode simulation (aucune modification, juste un rapport)
                            </label>
                        </td>
                    </tr>
                </table>

                <input type="submit" name="bh_do_import" class="button button-primary" value="Lancer l'import">
            </form>

            <?php $this->display_log(); ?>
        </div>
<?php
    }

    public function handle_import()
    {
        if (! isset($_POST['bh_do_import'])) {
            return;
        }
        if (! wp_verify_nonce($_POST['bh_import_nonce'] ?? '', 'bh_migration_import')) {
            wp_die('Nonce invalide.');
        }
        if (! current_user_can('manage_options')) {
            wp_die('Permissions insuffisantes.');
        }

        if (empty($_FILES['import_file']['tmp_name'])) {
            $this->log[] = ['error', 'Aucun fichier fourni.'];
            return;
        }

        $json = file_get_contents($_FILES['import_file']['tmp_name']);
        $data = json_decode($json, true);

        if (! $data || ! isset($data['posts'])) {
            $this->log[] = ['error', 'Fichier JSON invalide ou corrompu.'];
            return;
        }

        $import_media = ! empty($_POST['import_media']);
        $skip_existing = ! empty($_POST['skip_existing']);
        $dry_run = ! empty($_POST['dry_run']);

        $this->log[] = ['info', sprintf(
            'Import depuis %s — %d posts, %d médias, %d catégories',
            $data['meta']['source_url'] ?? 'inconnu',
            $data['meta']['total_posts'] ?? 0,
            $data['meta']['total_media'] ?? 0,
            $data['meta']['total_categories'] ?? 0
        )];

        if ($dry_run) {
            $this->log[] = ['info', 'MODE SIMULATION — aucune modification ne sera effectuée.'];
        }

        $this->import_categories($data['categories'] ?? [], $dry_run);

        if ($import_media) {
            $this->import_media($data['media'] ?? [], $dry_run);
        }

        $this->import_posts($data['posts'] ?? [], $skip_existing, $dry_run);

        $this->log[] = ['success', 'Import terminé.'];

        set_transient('bh_migration_log', $this->log, 300);
    }

    private function import_categories($categories, $dry_run)
    {
        if (empty($categories)) return;

        usort($categories, function ($a, $b) {
            return ($a['parent_slug'] === null ? 0 : 1) - ($b['parent_slug'] === null ? 0 : 1);
        });

        foreach ($categories as $cat) {
            $existing = get_term_by('slug', $cat['slug'], 'category');
            if ($existing) {
                $this->category_map[$cat['term_id']] = $existing->term_id;
                $this->log[] = ['info', "Catégorie existante : {$cat['name']} (slug: {$cat['slug']})"];
                continue;
            }

            if ($dry_run) {
                $this->log[] = ['info', "[SIM] Créerait catégorie : {$cat['name']}"];
                continue;
            }

            $parent_id = 0;
            if ($cat['parent_slug']) {
                $parent = get_term_by('slug', $cat['parent_slug'], 'category');
                if ($parent) {
                    $parent_id = $parent->term_id;
                }
            }

            $result = wp_insert_term($cat['name'], 'category', [
                'slug' => $cat['slug'],
                'description' => $cat['description'] ?? '',
                'parent' => $parent_id,
            ]);

            if (is_wp_error($result)) {
                $this->log[] = ['error', "Erreur catégorie {$cat['name']} : {$result->get_error_message()}"];
            } else {
                $this->category_map[$cat['term_id']] = $result['term_id'];
                $this->log[] = ['success', "Catégorie créée : {$cat['name']}"];
            }
        }
    }

    private function import_media($media_list, $dry_run)
    {
        if (empty($media_list)) return;

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        foreach ($media_list as $media) {
            $existing = $this->find_existing_media($media['filename']);
            if ($existing) {
                $this->media_map[$media['source_id']] = $existing;
                $this->log[] = ['info', "Média existant : {$media['filename']}"];
                continue;
            }

            if ($dry_run) {
                $this->log[] = ['info', "[SIM] Téléchargerait : {$media['url']}"];
                continue;
            }

            $tmp_file = download_url($media['url'], 60);
            if (is_wp_error($tmp_file)) {
                $this->log[] = ['error', "Erreur téléchargement {$media['filename']} : {$tmp_file->get_error_message()}"];
                continue;
            }

            $file_array = [
                'name' => $media['filename'],
                'tmp_name' => $tmp_file,
            ];

            $attachment_id = media_handle_sideload($file_array, 0, $media['title'] ?? '');

            if (is_wp_error($attachment_id)) {
                @unlink($tmp_file);
                $this->log[] = ['error', "Erreur import {$media['filename']} : {$attachment_id->get_error_message()}"];
                continue;
            }

            if (! empty($media['alt_text'])) {
                update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($media['alt_text']));
            }
            if (! empty($media['caption'])) {
                wp_update_post(['ID' => $attachment_id, 'post_excerpt' => $media['caption']]);
            }

            $this->media_map[$media['source_id']] = $attachment_id;
            $this->log[] = ['success', "Média importé : {$media['filename']} (ID: {$attachment_id})"];
        }
    }

    private function import_posts($posts, $skip_existing, $dry_run)
    {
        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($posts as $post_data) {
            if ($skip_existing) {
                $existing = get_page_by_path($post_data['slug'], OBJECT, $post_data['post_type']);
                if ($existing) {
                    $skipped++;
                    $this->log[] = ['info', "Ignoré (existe déjà) : {$post_data['title']}"];
                    continue;
                }
            }

            if ($dry_run) {
                $this->log[] = ['info', "[SIM] Importerait : [{$post_data['post_type']}] {$post_data['title']}"];
                $imported++;
                continue;
            }

            $content = $this->fix_encoding($this->rewrite_content_media($post_data['content']));
            $title = $this->fix_encoding($post_data['title']);
            $excerpt = $this->fix_encoding($post_data['excerpt'] ?? '');
            $elementor_meta = $this->fix_elementor_encoding($this->rewrite_elementor_media($post_data['elementor_meta'] ?? []));

            $author_id = $this->resolve_author($post_data['author_email'] ?? '');

            $new_post_id = wp_insert_post([
                'post_type' => $post_data['post_type'],
                'post_title' => sanitize_text_field($title),
                'post_name' => sanitize_title($post_data['slug']),
                'post_content' => $content,
                'post_excerpt' => $excerpt,
                'post_status' => $post_data['status'],
                'post_date' => $post_data['date'],
                'post_date_gmt' => $post_data['date_gmt'],
                'post_author' => $author_id,
            ], true);

            if (is_wp_error($new_post_id)) {
                $errors++;
                $this->log[] = ['error', "Erreur import '{$post_data['title']}' : {$new_post_id->get_error_message()}"];
                continue;
            }

            if (! empty($post_data['featured_image_id']) && isset($this->media_map[$post_data['featured_image_id']])) {
                set_post_thumbnail($new_post_id, $this->media_map[$post_data['featured_image_id']]);
            }

            $this->assign_categories($new_post_id, $post_data['categories'] ?? []);

            foreach ($elementor_meta as $key => $value) {
                update_post_meta($new_post_id, $key, $value);
            }

            foreach (($post_data['yoast_meta'] ?? []) as $key => $value) {
                update_post_meta($new_post_id, $key, $value);
            }

            foreach (($post_data['other_meta'] ?? []) as $key => $value) {
                if ($key === '_thumbnail_id') continue;
                update_post_meta($new_post_id, $key, $value);
            }

            $this->id_map[$post_data['source_id']] = $new_post_id;
            $imported++;
            $this->log[] = ['success', "Importé : [{$post_data['post_type']}] {$post_data['title']} (ID: {$new_post_id})"];
        }

        $this->log[] = ['info', "Résumé : {$imported} importés, {$skipped} ignorés, {$errors} erreurs"];
    }

    private function rewrite_content_media($content)
    {
        foreach ($this->media_map as $old_id => $new_id) {
            $content = str_replace("wp-image-{$old_id}", "wp-image-{$new_id}", $content);

            $old_url = $this->get_source_media_url($old_id);
            $new_url = wp_get_attachment_url($new_id);
            if ($old_url && $new_url) {
                $content = str_replace($old_url, $new_url, $content);
            }
        }
        return $content;
    }

    private function rewrite_elementor_media($elementor_meta)
    {
        if (empty($elementor_meta)) return $elementor_meta;

        $json = wp_json_encode($elementor_meta);

        foreach ($this->media_map as $old_id => $new_id) {
            $new_url = wp_get_attachment_url($new_id);
            if (! $new_url) continue;

            $json = preg_replace(
                '/"id"\s*:\s*' . $old_id . '/',
                '"id":' . $new_id,
                $json
            );
        }

        return json_decode($json, true) ?? $elementor_meta;
    }

    private function assign_categories($post_id, $categories)
    {
        $cat_ids = [];
        foreach ($categories as $cat) {
            if (isset($this->category_map[$cat['term_id']])) {
                $cat_ids[] = $this->category_map[$cat['term_id']];
            } else {
                $existing = get_term_by('slug', $cat['slug'], 'category');
                if ($existing) {
                    $cat_ids[] = $existing->term_id;
                }
            }
        }
        if (! empty($cat_ids)) {
            wp_set_post_categories($post_id, $cat_ids);
        }
    }

    private function resolve_author($email)
    {
        if ($email) {
            $user = get_user_by('email', $email);
            if ($user) return $user->ID;
        }
        return get_current_user_id();
    }

    private function find_existing_media($filename)
    {
        global $wpdb;
        $attachment = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s LIMIT 1",
            '%' . $wpdb->esc_like($filename)
        ));
        return $attachment ? (int) $attachment : null;
    }

    private function get_source_media_url($source_id)
    {
        return null;
    }

    private function fix_encoding($text)
    {
        if (empty($text)) return $text;

        $text = preg_replace_callback('/u(00[0-9a-fA-F]{2}|20[0-9a-fA-F]{2})/', function ($m) {
            $char = json_decode('"\\u' . $m[1] . '"');
            return $char !== null ? $char : $m[0];
        }, $text);

        return $text;
    }

    private function fix_elementor_encoding($data)
    {
        if (empty($data)) return $data;

        // _elementor_data is a JSON string — decode it, fix text values, re-encode
        if (isset($data['_elementor_data']) && is_string($data['_elementor_data'])) {
            $elements = json_decode($data['_elementor_data'], true);
            if (is_array($elements)) {
                $elements = $this->fix_elementor_elements_recursive($elements);
                $data['_elementor_data'] = wp_json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        return $data;
    }

    private function fix_elementor_elements_recursive($elements)
    {
        foreach ($elements as &$element) {
            // Fix text content in settings (editor, title, description, text fields)
            if (isset($element['settings']) && is_array($element['settings'])) {
                $text_keys = [
                    'editor',
                    'title',
                    'title_text',
                    'description',
                    'description_text',
                    'text',
                    'content',
                    'caption',
                    'inner_text',
                    'prefix',
                    'suffix',
                    'before_text',
                    'after_text',
                    'highlighted_text',
                    'rotating_text',
                    'html',
                    'alert_title',
                    'alert_description',
                    'tab_title',
                    'tab_content',
                    'testimonial_content',
                    'testimonial_name',
                    'testimonial_job',
                    'blockquote_content',
                    'author_name',
                    'tweet',
                    'heading'
                ];

                foreach ($element['settings'] as $key => &$value) {
                    if (is_string($value) && (in_array($key, $text_keys) || strpos($key, 'text') !== false || strpos($key, 'title') !== false || strpos($key, 'description') !== false || strpos($key, 'content') !== false || strpos($key, 'editor') !== false)) {
                        $value = $this->fix_encoding($value);
                    }
                }
                unset($value);
            }

            // Recurse into child elements
            if (isset($element['elements']) && is_array($element['elements'])) {
                $element['elements'] = $this->fix_elementor_elements_recursive($element['elements']);
            }
        }
        unset($element);

        return $elements;
    }

    private function display_log()
    {
        $log = get_transient('bh_migration_log');
        if (empty($log)) return;

        delete_transient('bh_migration_log');

        echo '<div style="margin-top: 20px;">';
        echo '<h2>Rapport d\'import</h2>';
        echo '<div style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; max-height: 500px; overflow-y: auto; font-family: monospace; font-size: 13px;">';

        foreach ($log as $entry) {
            $type = $entry[0];
            $msg = esc_html($entry[1]);
            $colors = [
                'success' => '#28a745',
                'error' => '#dc3545',
                'info' => '#6c757d',
            ];
            $color = $colors[$type] ?? '#333';
            $prefix = [
                'success' => '✓',
                'error' => '✗',
                'info' => '→',
            ];
            echo "<div style=\"color: {$color}; margin: 2px 0;\">{$prefix[$type]} {$msg}</div>";
        }

        echo '</div></div>';
    }
}

new BH_Migration_Import();
