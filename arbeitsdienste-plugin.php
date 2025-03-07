<?php
/**
 * Plugin Name: Arbeitsdienste Plugin
 * Description: Erstellt einen Custom Post Type für Arbeitsdienste und zeigt diese als Kacheln auf der Website an.
 * Version: 1.0
 * Author: Leon Buchner
 */

if (!defined('ABSPATH')) {
    exit; // Sicherheit: Kein direkter Zugriff
}

// CPT (Arbeitsdienste) registrieren
require_once plugin_dir_path(__FILE__) . 'includes/arbeitsdienste-cpt.php';

// Admin-Seiten laden
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/arbeitsdienste-admin.php';
    require_once plugin_dir_path(__FILE__) . 'admin/arbeitsdienste-meta.php';
}

// Shortcode für die Website
require_once plugin_dir_path(__FILE__) . 'public/arbeitsdienste-shortcode.php';

// CSS für Admin-Panel und Frontend laden
function arbeitsdienste_enqueue_styles() {
    wp_enqueue_style('arbeitsdienste-admin-css', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
    wp_enqueue_style('arbeitsdienste-public-css', plugin_dir_url(__FILE__) . 'assets/css/public-style.css');
}
add_action('admin_enqueue_scripts', 'arbeitsdienste_enqueue_styles');
add_action('wp_enqueue_scripts', 'arbeitsdienste_enqueue_styles');
