<?php
/**
 * WordPress Auto-Update Debug Script
 * Muss im WordPress Admin-Bereich ausgeführt werden
 */

// Nur in WordPress ausführen
if (!defined('ABSPATH')) {
    die('Dieses Script muss in WordPress ausgeführt werden!');
}

// Nur für Administratoren
if (!current_user_can('update_plugins')) {
    die('Keine Berechtigung!');
}

echo '<div style="padding: 20px; font-family: monospace; background: #f1f1f1; margin: 20px;">';
echo '<h2>🔧 Arbeitsdienste Auto-Update Debug</h2>';

// Plugin-Informationen
$plugin_file = dirname(__DIR__) . '/arbeitsdienste-plugin.php';
$plugin_slug = plugin_basename($plugin_file);

echo '<h3>📋 Plugin-Informationen</h3>';
echo 'Plugin-Datei: ' . $plugin_file . '<br>';
echo 'Plugin-Slug: ' . $plugin_slug . '<br>';
echo 'Plugin-Verzeichnis: ' . dirname($plugin_slug) . '<br>';
echo 'Aktuelle Version: ' . ARBEITSDIENSTE_PLUGIN_VERSION . '<br>';

// Update-Transient prüfen
echo '<h3>🔄 WordPress Update-Transient</h3>';
$transient = get_site_transient('update_plugins');

if ($transient && isset($transient->checked)) {
    echo 'Update-Transient vorhanden: ✅<br>';
    echo 'Anzahl checked Plugins: ' . count($transient->checked) . '<br>';
    
    if (isset($transient->checked[$plugin_slug])) {
        echo 'Unser Plugin in checked: ✅ (Version: ' . $transient->checked[$plugin_slug] . ')<br>';
    } else {
        echo 'Unser Plugin in checked: ❌<br>';
        echo 'Checked Plugins:<br>';
        foreach ($transient->checked as $slug => $version) {
            echo '- ' . $slug . ' (v' . $version . ')<br>';
        }
    }
    
    if (isset($transient->response[$plugin_slug])) {
        echo 'Update verfügbar: ✅<br>';
        $update_info = $transient->response[$plugin_slug];
        echo 'Neue Version: ' . $update_info->new_version . '<br>';
        echo 'Download-URL: ' . $update_info->package . '<br>';
    } else {
        echo 'Update verfügbar: ❌<br>';
    }
} else {
    echo 'Update-Transient fehlt: ❌<br>';
}

// GitHub API Test
echo '<h3>🐙 GitHub API Test</h3>';
$github_url = 'https://api.github.com/repos/leonbuchnerbd/wp-arbeitsdienste/releases/latest';

$request = wp_remote_get($github_url, array(
    'timeout' => 15,
    'user-agent' => 'WordPress-Arbeitsdienste-Plugin/1.0'
));

if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
    $data = json_decode(wp_remote_retrieve_body($request), true);
    if ($data && isset($data['tag_name'])) {
        echo 'GitHub API: ✅<br>';
        echo 'Neueste Version: ' . $data['tag_name'] . '<br>';
        echo 'Release-URL: ' . $data['html_url'] . '<br>';
        
        $needs_update = version_compare(ARBEITSDIENSTE_PLUGIN_VERSION, $data['tag_name'], '<');
        echo '<strong>Update erforderlich: ' . ($needs_update ? '✅ JA' : '❌ NEIN') . '</strong><br>';
    } else {
        echo 'GitHub API: ❌ Ungültige Antwort<br>';
    }
} else {
    echo 'GitHub API: ❌ Fehler<br>';
    if (is_wp_error($request)) {
        echo 'Fehler: ' . $request->get_error_message() . '<br>';
    }
}

// Auto-Updater-Status prüfen
echo '<h3>🤖 Auto-Updater Status</h3>';
if (class_exists('ArbeitsdiensteAutoUpdater')) {
    echo 'Auto-Updater-Klasse: ✅ Geladen<br>';
} else {
    echo 'Auto-Updater-Klasse: ❌ Nicht gefunden<br>';
}

// Hook-Status prüfen
echo '<h3>🪝 WordPress Hooks</h3>';
$hooks_to_check = [
    'pre_set_site_transient_update_plugins',
    'plugins_api',
    'upgrader_post_install'
];

foreach ($hooks_to_check as $hook) {
    $callbacks = $GLOBALS['wp_filter'][$hook] ?? null;
    if ($callbacks) {
        echo 'Hook "' . $hook . '": ✅ (' . count($callbacks->callbacks) . ' Callbacks)<br>';
    } else {
        echo 'Hook "' . $hook . '": ❌ Nicht registriert<br>';
    }
}

// Transient manuell triggern
echo '<h3>🔄 Manueller Update-Check</h3>';
if (isset($_GET['force_check'])) {
    echo 'Führe manuellen Update-Check durch...<br>';
    delete_site_transient('update_plugins');
    wp_update_plugins();
    echo 'Update-Check ausgeführt! <a href="' . remove_query_arg('force_check') . '">Seite neu laden</a><br>';
} else {
    echo '<a href="' . add_query_arg('force_check', '1') . '">🔄 Update-Check erzwingen</a><br>';
}

echo '</div>';
?>
