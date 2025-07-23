<?php
/**
 * WordPress Auto-Update Test - Direkter Update-Test
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
echo '<h2>🧪 Auto-Update Test - Download & Installation</h2>';

$plugin_file = dirname(__DIR__) . '/arbeitsdienste-plugin.php';
$plugin_slug = plugin_basename($plugin_file);

echo '<h3>📋 Plugin-Informationen</h3>';
echo 'Plugin-Slug: ' . $plugin_slug . '<br>';
echo 'Version: ' . ARBEITSDIENSTE_PLUGIN_VERSION . '<br>';

// GitHub Download testen
echo '<h3>📦 Download-Test</h3>';
$github_url = 'https://api.github.com/repos/leonbuchnerbd/wp-arbeitsdienste/zipball/2.8';

echo 'Download-URL: ' . $github_url . '<br>';

// Teste Download mit WordPress HTTP API
echo '<h4>WordPress HTTP Test:</h4>';
$request = wp_remote_get($github_url, array(
    'timeout' => 30,
    'user-agent' => 'WordPress-Arbeitsdienste-Plugin/1.0'
));

if (!is_wp_error($request)) {
    $response_code = wp_remote_retrieve_response_code($request);
    $content_length = wp_remote_retrieve_header($request, 'content-length');
    $content_type = wp_remote_retrieve_header($request, 'content-type');
    
    echo 'HTTP Status: ' . $response_code . '<br>';
    echo 'Content-Type: ' . $content_type . '<br>';
    echo 'Content-Length: ' . number_format($content_length) . ' Bytes<br>';
    
    if ($response_code === 200) {
        echo '✅ Download erfolgreich<br>';
        
        // ZIP-Header prüfen
        $body = wp_remote_retrieve_body($request);
        if (substr($body, 0, 2) === 'PK') {
            echo '✅ ZIP-Datei erkannt (Magic Bytes: PK)<br>';
        } else {
            echo '❌ Keine gültige ZIP-Datei<br>';
            echo 'Erste 20 Bytes: ' . bin2hex(substr($body, 0, 20)) . '<br>';
        }
    } else {
        echo '❌ Download fehlgeschlagen<br>';
    }
} else {
    echo '❌ HTTP Fehler: ' . $request->get_error_message() . '<br>';
}

// Update-Transient testen
echo '<h3>🔄 Update-Transient Test</h3>';

// Transient zurücksetzen
delete_site_transient('update_plugins');

// Neue Version simulieren
$transient = get_site_transient('update_plugins');
if (!$transient) {
    $transient = new stdClass();
    $transient->checked = array();
    $transient->response = array();
}

// Unser Plugin hinzufügen
$transient->checked[$plugin_slug] = '2.7'; // Simuliere alte Version

// Fake Update hinzufügen
$transient->response[$plugin_slug] = (object) array(
    'slug' => dirname($plugin_slug),
    'plugin' => $plugin_slug,
    'new_version' => '2.8',
    'tested' => '6.6',
    'package' => $github_url,
    'url' => 'https://github.com/leonbuchnerbd/wp-arbeitsdienste',
    'id' => $plugin_slug
);

set_site_transient('update_plugins', $transient);

echo '✅ Update-Transient gesetzt<br>';
echo 'Checked Version: 2.7<br>';
echo 'Neue Version: 2.8<br>';
echo 'Download-URL: ' . $github_url . '<br>';

echo '<h3>🔗 Test-Links</h3>';
echo '<a href="' . admin_url('plugins.php') . '" class="button button-primary">Plugins-Seite öffnen</a><br><br>';
echo '<strong>Dort sollten Sie jetzt ein Update für das Arbeitsdienste Plugin sehen!</strong><br>';

// Manueller Update-Trigger
if (isset($_GET['trigger_update'])) {
    echo '<h3>🚀 Manueller Update-Trigger</h3>';
    
    // WordPress Update-System aufrufen
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    
    $upgrader = new Plugin_Upgrader();
    $result = $upgrader->upgrade($plugin_slug);
    
    if ($result) {
        echo '✅ Update erfolgreich!<br>';
    } else {
        echo '❌ Update fehlgeschlagen<br>';
    }
} else {
    echo '<br><a href="' . add_query_arg('trigger_update', '1') . '" class="button button-secondary">🚀 Update manuell auslösen</a><br>';
}

echo '</div>';
?>
