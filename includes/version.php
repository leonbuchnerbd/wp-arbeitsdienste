<?php
/**
 * Plugin Version Definition
 */

if (!defined('ABSPATH') && !defined('ARBEITSDIENSTE_VERSION_STANDALONE')) {
    // Wenn nicht in WordPress und nicht als standalone ausgeführt
    define('ARBEITSDIENSTE_VERSION_STANDALONE', true);
}

// Plugin Version (temporär auf 2.7 für Demo des Auto-Updates)
define('ARBEITSDIENSTE_PLUGIN_VERSION', '2.11');

// Wenn als standalone Script ausgeführt, Version ausgeben
if (defined('ARBEITSDIENSTE_VERSION_STANDALONE') || (php_sapi_name() === 'cli' && basename($_SERVER['SCRIPT_NAME']) === 'version.php')) {
    echo ARBEITSDIENSTE_PLUGIN_VERSION . "\n";
    exit;
}
?>
