<?php
/**
 * Auto-Updater für Arbeitsdienste Plugin via GitHub
 */

if (!defined('ABSPATH')) {
    exit;
}

class ArbeitsdiensteAutoUpdater {
    
    private $plugin_slug;
    private $plugin_file;
    private $version;
    private $github_username;
    private $github_repo;
    
    public function __construct($plugin_file, $github_username, $github_repo) {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->version = ARBEITSDIENSTE_PLUGIN_VERSION;
        $this->github_username = $github_username;
        $this->github_repo = $github_repo;
        
        add_filter('pre_set_site_transient_update_plugins', array($this, 'modify_transient'), 10, 1);
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
        
        // Debug-Informationen in WordPress-Admin
        add_action('admin_notices', array($this, 'update_notice'));
        add_action('admin_init', array($this, 'debug_info'));
    }
    
    /**
     * Debug-Informationen
     */
    public function debug_info() {
        if (current_user_can('update_plugins') && isset($_GET['debug_updater'])) {
            echo '<div class="notice notice-info"><p>';
            echo '<strong>🔧 Auto-Updater Debug:</strong><br>';
            echo 'Plugin Slug: ' . $this->plugin_slug . '<br>';
            echo 'Plugin Directory: ' . dirname($this->plugin_slug) . '<br>';
            echo 'Aktuelle Version: ' . $this->version . '<br>';
            echo 'GitHub Repo: ' . $this->github_username . '/' . $this->github_repo . '<br>';
            
            $remote_data = $this->get_repository_info();
            if ($remote_data) {
                echo 'GitHub Version: ' . $remote_data['tag_name'] . '<br>';
                echo 'Download URL: ' . $remote_data['zipball_url'] . '<br>';
                
                // Zeige Update-Status
                $needs_update = version_compare($this->version, $remote_data['tag_name'], '<');
                echo '<strong>Update verfügbar: ' . ($needs_update ? '✅ JA' : '❌ NEIN') . '</strong><br>';
                
                // Zeige Transient-Info
                $transient = get_site_transient('update_plugins');
                if (isset($transient->response[$this->plugin_slug])) {
                    echo '✅ Plugin ist in Update-Transient registriert<br>';
                } else {
                    echo '❌ Plugin ist NICHT in Update-Transient registriert<br>';
                }
                
                if (isset($transient->checked[$this->plugin_slug])) {
                    echo 'Checked Version: ' . $transient->checked[$this->plugin_slug] . '<br>';
                } else {
                    echo '❌ Plugin ist nicht in checked Liste<br>';
                }
                
                // Cache-Clearing-Optionen
                echo '<hr>';
                echo '<strong>Cache-Management:</strong><br>';
                if (isset($_GET['clear_cache'])) {
                    delete_transient('arbeitsdienste_github_release_' . md5(sprintf('https://api.github.com/repos/%s/%s/releases/latest', $this->github_username, $this->github_repo)));
                    delete_site_transient('update_plugins');
                    echo '✅ Cache geleert! <a href="' . remove_query_arg('clear_cache') . '">Seite neu laden</a><br>';
                } else {
                    echo '<a href="' . add_query_arg('clear_cache', '1') . '" class="button">🗑️ Cache leeren</a><br>';
                }
                
                if (isset($_GET['force_update_check'])) {
                    wp_update_plugins();
                    echo '✅ Update-Check erzwungen! <a href="' . remove_query_arg('force_update_check') . '">Seite neu laden</a><br>';
                } else {
                    echo '<a href="' . add_query_arg('force_update_check', '1') . '" class="button">🔄 Update-Check erzwingen</a><br>';
                }
            } else {
                echo '❌ GitHub API Fehler oder keine Releases gefunden<br>';
            }
            echo '</p></div>';
        }
    }
    
    /**
     * GitHub API URL für Releases
     */
    private function get_repository_info() {
        $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', 
            $this->github_username, 
            $this->github_repo
        );
        
        // Cache für 1 Stunde
        $cache_key = 'arbeitsdienste_github_release_' . md5($request_uri);
        $cached_data = get_transient($cache_key);
        
        if ($cached_data !== false) {
            return $cached_data;
        }
        
        $request = wp_remote_get($request_uri, array(
            'timeout' => 15,
            'user-agent' => 'WordPress-Arbeitsdienste-Plugin/1.0'
        ));
        
        if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
            $data = json_decode(wp_remote_retrieve_body($request), true);
            
            if ($data && isset($data['tag_name'])) {
                // Cache für 1 Stunde
                set_transient($cache_key, $data, HOUR_IN_SECONDS);
                return $data;
            }
        }
        
        return false;
    }
    
    /**
     * Update-Check durchführen
     */
    public function modify_transient($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        // Prüfe ob unser Plugin in der checked Liste ist
        if (!isset($transient->checked[$this->plugin_slug])) {
            return $transient;
        }
        
        $remote_data = $this->get_repository_info();
        
        if ($remote_data && version_compare($this->version, $remote_data['tag_name'], '<')) {
            // Verwende die ZIP-Datei aus dem Release (nicht zipball_url)
            $download_url = '';
            if (isset($remote_data['assets']) && is_array($remote_data['assets'])) {
                foreach ($remote_data['assets'] as $asset) {
                    if (strpos($asset['name'], '.zip') !== false) {
                        $download_url = $asset['browser_download_url'];
                        break;
                    }
                }
            }
            
            // Fallback auf zipball_url wenn keine ZIP-Asset gefunden
            if (empty($download_url)) {
                $download_url = $remote_data['zipball_url'];
            }
            
            $transient->response[$this->plugin_slug] = (object) array(
                'slug' => dirname($this->plugin_slug),
                'plugin' => $this->plugin_slug,
                'new_version' => $remote_data['tag_name'],
                'tested' => '6.6',
                'package' => $download_url,
                'url' => $remote_data['html_url'],
                'id' => $this->plugin_slug
            );
        }
        
        return $transient;
    }
    
    /**
     * Plugin-Informationen für Popup
     */
    public function plugin_popup($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }
        
        if (!isset($args->slug) || $args->slug !== dirname($this->plugin_slug)) {
            return $result;
        }
        
        $remote_data = $this->get_repository_info();
        
        if ($remote_data) {
            return (object) array(
                'name' => 'Arbeitsdienste Plugin',
                'slug' => dirname($this->plugin_slug),
                'version' => $remote_data['tag_name'],
                'author' => 'Leon Buchner',
                'homepage' => $remote_data['html_url'],
                'short_description' => 'Verwaltung von Arbeitsdiensten für Vereine',
                'sections' => array(
                    'description' => 'Plugin zur Verwaltung von Arbeitsdiensten mit E-Mail-Integration und CSV-Export.',
                    'changelog' => isset($remote_data['body']) ? $remote_data['body'] : 'Siehe GitHub Release für Details.'
                ),
                'download_link' => $remote_data['zipball_url'],
                'tested' => '6.6',
                'requires' => '5.0',
                'last_updated' => isset($remote_data['published_at']) ? $remote_data['published_at'] : '',
                'banners' => array()
            );
        }
        
        return $result;
    }
    
    /**
     * Nach Installation aufräumen
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;
        
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_slug) {
            return $result;
        }
        
        $install_directory = plugin_dir_path($this->plugin_file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;
        
        return $result;
    }
    
    /**
     * Update-Hinweis anzeigen
     */
    public function update_notice() {
        if (!current_user_can('update_plugins')) {
            return;
        }
        
        $remote_data = $this->get_repository_info();
        
        if ($remote_data && version_compare($this->version, $remote_data['tag_name'], '<')) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>🎯 Arbeitsdienste Plugin:</strong> ';
            echo sprintf(
                'Version %s ist verfügbar! <a href="%s" class="button button-primary">Jetzt aktualisieren</a> | <a href="%s" target="_blank">Release-Details</a>',
                $remote_data['tag_name'],
                admin_url('plugins.php'),
                $remote_data['html_url']
            );
            echo ' | <a href="' . admin_url('plugins.php?debug_updater=1') . '">Debug-Info</a>';
            echo '</p>';
            echo '</div>';
        }
    }
}

// Auto-Updater initialisieren
if (is_admin()) {
    $plugin_file = dirname(dirname(__FILE__)) . '/arbeitsdienste-plugin.php';
    new ArbeitsdiensteAutoUpdater($plugin_file, 'leonbuchnerbd', 'wp-arbeitsdienste');
}
?>
