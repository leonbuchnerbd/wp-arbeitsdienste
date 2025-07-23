<?php
/**
 * Schnelltest für Update-Problem
 * In WordPress Admin als PHP ausführen
 */

// Plugin-Info
$plugin_file = __DIR__ . '/arbeitsdienste-plugin.php';
$plugin_slug = plugin_basename($plugin_file);

echo "🔍 PLUGIN UPDATE DEBUG\n";
echo "======================\n";
echo "Plugin-Slug: $plugin_slug\n";
echo "Aktuelle Version: " . ARBEITSDIENSTE_PLUGIN_VERSION . "\n";

// Update-Transient prüfen
$transient = get_site_transient('update_plugins');

echo "\n📦 UPDATE-TRANSIENT:\n";
if (isset($transient->response[$plugin_slug])) {
    $update = $transient->response[$plugin_slug];
    echo "✅ Update verfügbar!\n";
    echo "Neue Version: " . $update->new_version . "\n";
    echo "Download-URL: " . $update->package . "\n";
    echo "Plugin-ID: " . $update->id . "\n";
    
    // URL testen
    echo "\n🌐 URL-TEST:\n";
    $test = wp_remote_head($update->package);
    if (!is_wp_error($test)) {
        echo "✅ Download-URL erreichbar (HTTP " . wp_remote_retrieve_response_code($test) . ")\n";
    } else {
        echo "❌ Download-URL nicht erreichbar: " . $test->get_error_message() . "\n";
    }
    
} else {
    echo "❌ Kein Update im Transient gefunden\n";
    
    if (isset($transient->checked[$plugin_slug])) {
        echo "Plugin checked Version: " . $transient->checked[$plugin_slug] . "\n";
    } else {
        echo "Plugin nicht in checked Liste\n";
    }
}

// GitHub API direkt testen
echo "\n🐙 GITHUB API TEST:\n";
$github_request = wp_remote_get('https://api.github.com/repos/leonbuchnerbd/wp-arbeitsdienste/releases/latest');
if (!is_wp_error($github_request)) {
    $github_data = json_decode(wp_remote_retrieve_body($github_request), true);
    echo "GitHub Version: " . $github_data['tag_name'] . "\n";
    echo "Zipball URL: " . $github_data['zipball_url'] . "\n";
    
    $needs_update = version_compare(ARBEITSDIENSTE_PLUGIN_VERSION, $github_data['tag_name'], '<');
    echo "Update erforderlich: " . ($needs_update ? "JA" : "NEIN") . "\n";
}

// WordPress Update-Hooks prüfen
echo "\n🪝 WORDPRESS HOOKS:\n";
if (has_filter('pre_set_site_transient_update_plugins')) {
    echo "✅ pre_set_site_transient_update_plugins Hook registriert\n";
} else {
    echo "❌ pre_set_site_transient_update_plugins Hook NICHT registriert\n";
}

echo "\n💡 LÖSUNG:\n";
echo "1. Cache leeren: delete_site_transient('update_plugins')\n";
echo "2. Update-Check: wp_update_plugins()\n";
echo "3. Plugins-Seite neu laden\n";
?>
