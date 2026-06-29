<?php
/**
 * Plugin Name: ARPA3 - Bodyhouse Header
 * Description: Synchronize the Bodyhouse Header (Menu mainly).
 * Author: ARPA3 <thomas.d@arpa3.fr>
 * Author URI: www.arpa3.fr
 * Version: 1.0.0
 * Requires PHP: 5.6
 */

use Arpa3\BodyhouseHeader\Admin\HeaderSettingsPage;
use Arpa3\BodyhouseHeader\Data\HeaderDataProvider;

require_once __DIR__ . '/vendor/autoload.php';

$headerSettingsPage = new HeaderSettingsPage();

global $headerDataProvider;
$headerDataProvider = new HeaderDataProvider();

function bh_clear_cache_endpoint() {
    global $headerDataProvider;

    try {
        $headerDataProvider->clearCache();
        return [ 'success' => 'Cache has worked perfectly.' ];
    } catch (Exception $e) {
        return [ 'error' => 'Cache could not complete:' . $e->getMessage() ];
    }
}

add_action('rest_api_init', function () use ($headerDataProvider) {
    register_rest_route('bodyhouse-menu/v1', '/clear-cache', [
        'methods' => 'GET',
        'callback' => 'bh_clear_cache_endpoint'
    ]);
});

add_action('wp_enqueue_scripts', function () use($headerDataProvider) {

    $headerData = $headerDataProvider->getHeaderData();

    $styles = $headerData['styles'] ?? [];;
    $scripts = $headerData['scripts'] ?? [];

    foreach ($styles as $key => $style) {
        wp_enqueue_style('bodyhouse-header-' . $key, $style, null, rand());
    }

    foreach ($scripts as $script) {
        wp_enqueue_script('bodyhouse-header-' . $key, $script, null, rand());
    }

    wp_enqueue_script('bodyhouse-header-customer', plugins_url( 'dist/bodyhouse-header.js', __FILE__ ), null, '1.0.0');
}, 99);


add_action( 'wp_footer', function() use($headerDataProvider) {
    echo $headerDataProvider->getDoofinderContent();
} );

function bh_header_get_content() 
{
    global $headerDataProvider;
    return $headerDataProvider->getMenuContent();
}


