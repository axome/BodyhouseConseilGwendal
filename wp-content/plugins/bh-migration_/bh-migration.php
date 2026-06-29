<?php
/**
 * Plugin Name: BH Migration
 * Plugin URI: https://arpa3.fr
 * Description: Migration des articles (post) et Comment Choisir (how-to-choose) entre deux instances WordPress avec support Elementor, Yoast SEO et médias.
 * Version: 1.0.0
 * Author: ARPA3
 * Text Domain: bh-migration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BH_MIGRATION_VERSION', '1.0.0' );
define( 'BH_MIGRATION_PATH', plugin_dir_path( __FILE__ ) );

require_once BH_MIGRATION_PATH . 'bh-migration-export.php';
require_once BH_MIGRATION_PATH . 'bh-migration-import.php';
