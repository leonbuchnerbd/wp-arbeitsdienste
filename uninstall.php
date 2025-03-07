<?php
// Sicherheitsprüfung: Verhindert direkten Zugriff auf die Datei
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// 1️⃣ Alle Arbeitsdienst-Beiträge entfernen
$arbeitsdienste = get_posts(array(
    'post_type'      => 'arbeitsdienste',
    'numberposts'    => -1,
    'post_status'    => 'any'
));

foreach ($arbeitsdienste as $dienst) {
    wp_delete_post($dienst->ID, true); // true = endgültig löschen (kein Papierkorb)
}

// 2️⃣ Alle Meta-Daten der Arbeitsdienste entfernen
global $wpdb;
$wpdb->query("DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT ID FROM wp_posts)");

// 3️⃣ Plugin-spezifische Optionen löschen (z. B. API-URL, falls verwendet)
delete_option('arbeitsdienste_api_url');

// 4️⃣ Sicherstellen, dass keine Reste des Custom Post Type (CPT) bleiben
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'arbeitsdienste'");

// 5️⃣ Falls Transienten für Caching verwendet wurden, diese auch löschen
global $wpdb;
$wpdb->query("DELETE FROM wp_options WHERE option_name LIKE '_transient_arbeitsdienste_%'");

// Optional: Custom Taxonomien oder weitere Daten löschen, falls nötig
