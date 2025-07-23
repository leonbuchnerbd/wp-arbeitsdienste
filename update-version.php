#!/usr/bin/env php
<?php
/**
 * Einfaches Version-Update Script für Arbeitsdienste Plugin
 * 
 * Verwendung:
 * php update-version.php           # Aktuelle Version anzeigen
 * php update-version.php 2.2       # Version auf 2.2 setzen
 * php update-version.php 2.2 --release  # Version setzen UND GitHub Release erstellen
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

// Parameter prüfen
$createRelease = in_array('--release', $argv);

// Wenn neue Version als Argument übergeben
if (isset($argv[1]) && $argv[1] !== '--release') {
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
    
    if ($createRelease) {
        echo "\n🚀 Erstelle automatischen Release...\n";
        createGitHubRelease($newVersion);
    } else {
        echo "\n🚀 Nächste Schritte:\n";
        echo "git add .\n";
        echo "git commit -m \"🚀 Version $newVersion\"\n";
        echo "git tag $newVersion\n";
        echo "git push origin main --tags\n";
        echo "\n💡 Für automatischen Release: php update-version.php $newVersion --release\n";
    }
} else {
    echo "\n💡 Verwendung:\n";
    echo "  php update-version.php 2.2           # Version setzen\n";
    echo "  php update-version.php 2.2 --release # Version setzen + GitHub Release\n";
}

/**
 * Erstellt automatisch einen GitHub Release
 */
function createGitHubRelease($version) {
    // Git-Befehle ausführen
    echo "1. 📝 Git add und commit...\n";
    exec('git add .', $output, $return);
    if ($return !== 0) {
        die("❌ Git add fehlgeschlagen!\n");
    }
    
    exec("git commit -m \"🚀 Version $version\"", $output, $return);
    if ($return !== 0 && $return !== 1) { // 1 = nichts zu committen
        echo "⚠️  Git commit: " . implode("\n", $output) . "\n";
    } else {
        echo "✅ Git commit erfolgreich\n";
    }
    
    echo "2. � Git push...\n";
    exec('git push origin main', $output, $return);
    if ($return !== 0) {
        die("❌ Git push fehlgeschlagen!\n");
    }
    echo "✅ Git push erfolgreich\n";
    
    echo "3. 🏷️ Git tag erstellen...\n";
    exec("git tag $version", $output, $return);
    if ($return !== 0) {
        echo "⚠️  Tag existiert bereits oder Fehler\n";
    } else {
        echo "✅ Git tag erstellt\n";
    }
    
    exec("git push origin $version", $output, $return);
    if ($return !== 0) {
        die("❌ Git push tags fehlgeschlagen!\n");
    }
    echo "✅ Git tags gepusht\n";
    
    // GitHub Release URL generieren
    $releaseBody = urlencode("## ✨ Neue Features:\n- 🔄 Auto-Update System\n- 📧 Verbesserte E-Mail-Integration\n- 🎯 ID-Anzeige auf Kacheln\n- 🔴 Rote Anmelden-Buttons\n\n## 🛠️ Technische Verbesserungen:\n- GitHub API Integration\n- WordPress Plugin Update Hooks\n- Bessere Fehlerbehandlung\n\n## 📦 Installation:\nPlugin automatisch über WordPress Admin updaten oder ZIP herunterladen.");
    
    $releaseUrl = "https://github.com/leonbuchnerbd/wp-arbeitsdienste/releases/new?tag=$version&title=Version%20$version&body=$releaseBody";
    
    echo "\n🌐 GitHub Release erstellen:\n";
    echo "$releaseUrl\n\n";
    
    // Versuche Browser zu öffnen (Windows)
    if (PHP_OS_FAMILY === 'Windows') {
        echo "🔥 Öffne Browser automatisch...\n";
        exec("start \"\" \"$releaseUrl\"");
    }
    
    echo "✅ Release-Prozess abgeschlossen!\n";
    echo "💡 Nach dem GitHub Release werden alle WordPress-Installationen automatisch benachrichtigt!\n";
}
