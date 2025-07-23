<?php
/**
 * Test-Script für Auto-Updater
 * Aufruf: php test-updater.php
 */

// GitHub API Test ohne WordPress
function test_github_api() {
    $request_uri = 'https://api.github.com/repos/leonbuchnerbd/wp-arbeitsdienste/releases/latest';
    
    echo "🔍 Teste GitHub API...\n";
    echo "URL: $request_uri\n\n";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $request_uri,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'WordPress-Arbeitsdienste-Plugin/1.0',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 15
    ]);
    
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);
    
    echo "HTTP Status: $http_code\n";
    
    if ($error) {
        echo "❌ cURL Fehler: $error\n";
        return false;
    }
    
    if ($http_code === 200) {
        $data = json_decode($response, true);
        if ($data && isset($data['tag_name'])) {
            echo "✅ GitHub API OK\n";
            echo "Neueste Version: " . $data['tag_name'] . "\n";
            echo "Release URL: " . $data['html_url'] . "\n";
            
            if (isset($data['assets']) && is_array($data['assets'])) {
                echo "Assets gefunden: " . count($data['assets']) . "\n";
                foreach ($data['assets'] as $asset) {
                    if (strpos($asset['name'], '.zip') !== false) {
                        echo "ZIP-Asset: " . $asset['name'] . "\n";
                        echo "Download: " . $asset['browser_download_url'] . "\n";
                    }
                }
            } else {
                echo "⚠️ Keine Assets gefunden - verwende zipball_url\n";
                echo "Zipball URL: " . $data['zipball_url'] . "\n";
            }
            return $data;
        }
    }
    
    echo "❌ GitHub API Fehler\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
    return false;
}

// Version vergleichen
function test_version_comparison() {
    echo "\n🔢 Teste Version-Vergleich...\n";
    
    // Aktuelle Version laden
    $version_file = __DIR__ . '/includes/version.php';
    if (file_exists($version_file)) {
        include $version_file;
        $current_version = defined('ARBEITSDIENSTE_PLUGIN_VERSION') ? ARBEITSDIENSTE_PLUGIN_VERSION : '1.0.0';
    } else {
        $current_version = '1.0.0';
    }
    
    echo "Aktuelle Version: $current_version\n";
    
    $github_data = test_github_api();
    if ($github_data && isset($github_data['tag_name'])) {
        $remote_version = $github_data['tag_name'];
        echo "GitHub Version: $remote_version\n";
        
        $needs_update = version_compare($current_version, $remote_version, '<');
        echo "Update verfügbar: " . ($needs_update ? '✅ JA' : '❌ NEIN') . "\n";
        
        return $needs_update;
    }
    
    return false;
}

// Plugin-Datei prüfen
function test_plugin_structure() {
    echo "\n📁 Teste Plugin-Struktur...\n";
    
    $plugin_file = __DIR__ . '/arbeitsdienste-plugin.php';
    if (file_exists($plugin_file)) {
        echo "✅ Haupt-Plugin-Datei gefunden\n";
        
        $content = file_get_contents($plugin_file);
        if (strpos($content, 'ArbeitsdiensteAutoUpdater') !== false) {
            echo "✅ Auto-Updater wird geladen\n";
        } else {
            echo "❌ Auto-Updater wird NICHT geladen\n";
        }
        
        // Plugin-Header prüfen
        if (preg_match('/Version:\s*([0-9.]+)/', $content, $matches)) {
            echo "Plugin-Header Version: " . $matches[1] . "\n";
        }
    } else {
        echo "❌ Haupt-Plugin-Datei nicht gefunden\n";
    }
    
    $auto_updater = __DIR__ . '/includes/auto-updater.php';
    if (file_exists($auto_updater)) {
        echo "✅ Auto-Updater-Datei gefunden\n";
    } else {
        echo "❌ Auto-Updater-Datei nicht gefunden\n";
    }
}

// WordPress-Plugin-Slug testen
function test_plugin_slug() {
    echo "\n🏷️ Teste Plugin-Slug...\n";
    
    $plugin_file = __DIR__ . '/arbeitsdienste-plugin.php';
    $plugin_slug = plugin_basename($plugin_file);
    
    // Simuliere plugin_basename Funktion
    $plugin_slug = str_replace(DIRECTORY_SEPARATOR, '/', $plugin_file);
    $plugin_dir = str_replace(DIRECTORY_SEPARATOR, '/', WP_PLUGIN_DIR ?? dirname(__DIR__));
    $plugin_slug = str_replace($plugin_dir . '/', '', $plugin_slug);
    
    echo "Plugin Slug: $plugin_slug\n";
    echo "Plugin Dir: " . dirname($plugin_slug) . "\n";
}

// Haupttest ausführen
echo "🎯 Arbeitsdienste Auto-Updater Test\n";
echo "====================================\n\n";

test_plugin_structure();
test_github_api();
test_version_comparison();

echo "\n✅ Test abgeschlossen!\n";
echo "\nZum Debuggen in WordPress:\n";
echo "- Besuche: /wp-admin/plugins.php?debug_updater=1\n";
echo "- Prüfe WordPress Update-Transients\n";
echo "- Kontrolliere Plugin-Aktivierung\n";
