<?php
/**
 * Plugin Name: Arbeitsdienste Plugin
 * Description: Erstellt einen Custom Post Type für Arbeitsdienste und zeigt diese als Kacheln auf der Website an. Mit CSV-Export, erweiterten Zeit-Optionen und E-Mail-Integration.
 * Version: 2.5
 * Author: Leon Buchner
 * GitHub Plugin URI: leonbuchnerbd/wp-arbeitsdienste
 * Primary Branch: main
 */

if (!defined('ABSPATH')) {
    exit; // Sicherheit: Kein direkter Zugriff
}

// Plugin-Konstanten definieren
require_once __DIR__ . '/includes/version.php';
define('ARBEITSDIENSTE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ARBEITSDIENSTE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Hilfsfunktionen laden
require_once ARBEITSDIENSTE_PLUGIN_PATH . 'includes/arbeitsdienste-functions.php';

// Auto-Updater laden
require_once ARBEITSDIENSTE_PLUGIN_PATH . 'includes/auto-updater.php';

// CPT (Arbeitsdienste) registrieren
require_once ARBEITSDIENSTE_PLUGIN_PATH . 'includes/arbeitsdienste-cpt.php';

// Admin-Seiten laden
if (is_admin()) {
    require_once ARBEITSDIENSTE_PLUGIN_PATH . 'admin/arbeitsdienste-admin.php';
    require_once ARBEITSDIENSTE_PLUGIN_PATH . 'admin/arbeitsdienste-meta.php';
    require_once ARBEITSDIENSTE_PLUGIN_PATH . 'admin/arbeitsdienste-settings.php';
}

// Shortcode für die Website
require_once ARBEITSDIENSTE_PLUGIN_PATH . 'public/arbeitsdienste-shortcode.php';

// CSS und JavaScript für Admin-Panel und Frontend laden
function arbeitsdienste_enqueue_styles() {
    wp_enqueue_style(
        'arbeitsdienste-admin-css', 
        ARBEITSDIENSTE_PLUGIN_URL . 'assets/css/admin-style.css',
        array(),
        ARBEITSDIENSTE_PLUGIN_VERSION
    );
}

function arbeitsdienste_enqueue_frontend_styles() {
    // Haupt-CSS
    wp_enqueue_style(
        'arbeitsdienste-public-css', 
        ARBEITSDIENSTE_PLUGIN_URL . 'assets/css/public-style.css',
        array(),
        ARBEITSDIENSTE_PLUGIN_VERSION
    );
    
    // Mobile-Optimierungen CSS
    wp_enqueue_style(
        'arbeitsdienste-mobile-css', 
        ARBEITSDIENSTE_PLUGIN_URL . 'assets/css/mobile-optimizations.css',
        array('arbeitsdienste-public-css'),
        ARBEITSDIENSTE_PLUGIN_VERSION,
        'screen' // Nur für Bildschirme, nicht für Print
    );
    
    // Mobile-Optimierungen JavaScript
    wp_enqueue_script(
        'arbeitsdienste-mobile-js',
        ARBEITSDIENSTE_PLUGIN_URL . 'assets/js/mobile-optimizations.js',
        array(),
        ARBEITSDIENSTE_PLUGIN_VERSION,
        true // Im Footer laden
    );
    
    // Viewport Meta-Tag für bessere Mobile-Darstellung
    add_action('wp_head', 'arbeitsdienste_add_viewport_meta');
}

add_action('admin_enqueue_scripts', 'arbeitsdienste_enqueue_styles');
add_action('wp_enqueue_scripts', 'arbeitsdienste_enqueue_frontend_styles');

// Viewport Meta-Tag für bessere Mobile-Darstellung hinzufügen
function arbeitsdienste_add_viewport_meta() {
    if (has_shortcode(get_post()->post_content, 'arbeitsdienste')) {
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">' . "\n";
        echo '<meta name="format-detection" content="telephone=no">' . "\n";
        echo '<meta name="theme-color" content="#007cba">' . "\n";
    }
}

// Mobile-spezifische Body-Klassen hinzufügen
function arbeitsdienste_add_body_classes($classes) {
    global $post;
    
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'arbeitsdienste')) {
        $classes[] = 'has-arbeitsdienste';
        
        // Mobile Detection
        if (wp_is_mobile()) {
            $classes[] = 'arbeitsdienste-mobile';
        }
    }
    
    return $classes;
}
add_filter('body_class', 'arbeitsdienste_add_body_classes');

// Responsive Images Support
function arbeitsdienste_add_responsive_image_support() {
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
}
add_action('after_setup_theme', 'arbeitsdienste_add_responsive_image_support');

// Plugin-Aktivierung
function arbeitsdienste_plugin_activation() {
    // CPT registrieren
    arbeitsdienste_register_post_type();
    
    // Permalinks aktualisieren
    flush_rewrite_rules();
    
    // Standard-Optionen setzen falls nicht vorhanden
    if (!get_option('arbeitsdienste_version')) {
        add_option('arbeitsdienste_version', ARBEITSDIENSTE_PLUGIN_VERSION);
    }
}
register_activation_hook(__FILE__, 'arbeitsdienste_plugin_activation');

// Plugin-Deaktivierung
function arbeitsdienste_plugin_deactivation() {
    // Permalinks zurücksetzen
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'arbeitsdienste_plugin_deactivation');
