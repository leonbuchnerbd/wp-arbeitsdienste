#!/usr/bin/env php
<?php
/**
 * Einfaches Version-Upd    echo "\n🚀 Nächste Schritte:\n";
    echo "git add .\n";
    echo "git commit -m \"🚀 Version $newVersion\"\n";
    echo "git tag $newVersion\n";
    echo "git push origin main --tags\n";cript für Arbeitsdienste Plugin
 * 
 * Verwendung:
 * php update-version.php           # Aktuelle Version anzeigen
 * php update-version.php 2.2       # Version auf 2.2 setzen
 */

$versionFile = __DIR__ . '/includes/version.php';
$pluginFile = __DIR__ . '/arbeitsdienste-plugin.php';

// Aktuelle Version auslesen
if (!file_exists($versionFile)) {
    die("❌ Version-Datei nicht gefunden: $versionFile\n");
}

// Version aus version.php lesen
$versionContent = file_get_contents($versionFile);
if (!preg_match("/define\s*\(\s*['\"]ARBEITSDIENSTE_PLUGIN_VERSION['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)/", $versionContent, $matches)) {
    die("❌ Version in version.php nicht gefunden!\n");
}

$currentVersion = $matches[1];
echo "📋 Aktuelle Version: $currentVersion\n";

// Wenn neue Version als Argument übergeben
if (isset($argv[1])) {
    $newVersion = $argv[1];
    
    if (!preg_match('/^\d+\.\d+(\.\d+)?$/', $newVersion)) {
        die("❌ Ungültige Versionsnummer: $newVersion\n💡 Format: 2.1 oder 2.1.0\n");
    }
    
    echo "⬆️  Aktualisiere Version: $currentVersion → $newVersion\n";
    
    // 1. Version in version.php aktualisieren
    $versionContent = preg_replace(
        "/(define\s*\(\s*['\"]ARBEITSDIENSTE_PLUGIN_VERSION['\"]\s*,\s*['\"])([^'\"]+)(['\"]\s*\))/",
        '${1}' . $newVersion . '${3}',
        $versionContent
    );
    file_put_contents($versionFile, $versionContent);
    echo "✅ Version-Datei aktualisiert: includes/version.php\n";
    
    // 2. Plugin-Header aktualisieren
    $pluginContent = file_get_contents($pluginFile);
    $pluginContent = preg_replace(
        '/(\*\s*Version:\s*)[0-9.]+/',
        '${1}' . $newVersion,
        $pluginContent
    );
    file_put_contents($pluginFile, $pluginContent);
    echo "✅ Plugin-Header aktualisiert: arbeitsdienste-plugin.php\n";
    
    echo "\n🚀 Nächste Schritte:\n";
    echo "git add .\n";
    echo "git commit -m \"🚀 Version $newVersion\"\n";
    echo "git tag $newVersion\n";
    echo "git push origin main --tags\n";
    echo "\n💡 Dann GitHub Release erstellen!\n";
} else {
    echo "\n💡 Verwendung: php update-version.php 2.2\n";
}
