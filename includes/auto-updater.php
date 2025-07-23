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
        
        // Admin-Hinweise für Updates
        add_action('admin_notices', array($this, 'update_notice'));
    }
    
    /**
     * GitHub API URL für Releases
     */
    private function get_repository_info() {
        $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', 
            $this->github_username, 
            $this->github_repo
        );
        
        $request = wp_remote_get($request_uri, array(
            'timeout' => 10,
            'user-agent' => 'WordPress-Plugin-Updater'
        ));
        
        if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
            return json_decode(wp_remote_retrieve_body($request), true);
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
        
        $remote_data = $this->get_repository_info();
        
        if ($remote_data && version_compare($this->version, $remote_data['tag_name'], '<')) {
            $transient->response[$this->plugin_slug] = (object) array(
                'slug' => $this->plugin_slug,
                'plugin' => $this->plugin_slug,
                'new_version' => $remote_data['tag_name'],
                'tested' => '6.6',
                'package' => $remote_data['zipball_url'],
                'url' => $remote_data['html_url']
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
                    'changelog' => $remote_data['body']
                ),
                'download_link' => $remote_data['zipball_url'],
                'tested' => '6.6',
                'requires' => '5.0',
                'last_updated' => $remote_data['published_at']
            );
        }
        
        return $result;
    }
    
    /**
     * Nach Installation aufräumen
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;
        
        $install_directory = plugin_dir_path($this->plugin_file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;
        
        if ($this->plugin_slug) {
            activate_plugin($this->plugin_slug);
        }
        
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
            echo '<p><strong>Arbeitsdienste Plugin:</strong> ';
            echo sprintf(
                'Version %s ist verfügbar. <a href="%s">Jetzt aktualisieren</a>',
                $remote_data['tag_name'],
                admin_url('plugins.php')
            );
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
