<?php
/**
 * Einfache Update-Benachrichtigung für Arbeitsdienste Plugin
 * Zeigt Admin-Notice wenn neue Version verfügbar ist
 */

if (!defined('ABSPATH')) {
    exit;
}

class ArbeitsdiensteSimpleUpdateCheck {
    private $current_version;
    private $github_repo;

    public function __construct($current_version, $github_repo) {
        $this->current_version = $current_version;
        $this->github_repo = $github_repo;

        add_action('admin_notices', array($this, 'check_for_update_notice'));
        add_action('wp_ajax_dismiss_arbeitsdienste_update', array($this, 'dismiss_update_notice'));
    }

    public function check_for_update_notice() {
        // Nur auf Plugin-Seite anzeigen
        $current_screen = get_current_screen();
        if (!$current_screen || $current_screen->id !== 'plugins') {
            return;
        }

        // Prüfen ob Notice bereits dismissed wurde
        if (get_user_meta(get_current_user_id(), 'arbeitsdienste_update_dismissed_' . $this->current_version, true)) {
            return;
        }

        // Cache für Update-Check (24 Stunden)
        $cache_key = 'arbeitsdienste_update_check';
        $latest_version = get_transient($cache_key);

        if ($latest_version === false) {
            $latest_version = $this->get_latest_version();
            if ($latest_version) {
                set_transient($cache_key, $latest_version, DAY_IN_SECONDS);
            }
        }

        if ($latest_version && version_compare($this->current_version, $latest_version, '<')) {
            $this->show_update_notice($latest_version);
        }
    }

    private function get_latest_version() {
        $api_url = "https://api.github.com/repos/{$this->github_repo}/releases/latest";
        
        $response = wp_remote_get($api_url, array(
            'timeout' => 10,
            'headers' => array(
                'User-Agent' => 'WordPress-Plugin-Update-Checker'
            )
        ));

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return isset($data['tag_name']) ? ltrim($data['tag_name'], 'v') : false;
    }

    private function show_update_notice($latest_version) {
        ?>
        <div class="notice notice-warning is-dismissible" id="arbeitsdienste-update-notice">
            <p>
                <strong>🎯 Arbeitsdienste Plugin Update verfügbar!</strong><br>
                Neue Version <strong><?php echo esc_html($latest_version); ?></strong> ist verfügbar 
                (Aktuelle Version: <?php echo esc_html($this->current_version); ?>).
            </p>
            <p>
                <a href="https://github.com/<?php echo esc_attr($this->github_repo); ?>/releases/latest" 
                   class="button button-primary" target="_blank">
                    📥 Neue Version herunterladen
                </a>
                <a href="#" class="button" onclick="dismissArbeitsdiensteUpdate(); return false;">
                    ✕ Benachrichtigung ausblenden
                </a>
            </p>
        </div>

        <script>
        function dismissArbeitsdiensteUpdate() {
            jQuery.post(ajaxurl, {
                action: 'dismiss_arbeitsdienste_update',
                version: '<?php echo esc_js($this->current_version); ?>',
                _ajax_nonce: '<?php echo wp_create_nonce('dismiss_update'); ?>'
            }, function() {
                jQuery('#arbeitsdienste-update-notice').fadeOut();
            });
        }
        </script>
        <?php
    }

    public function dismiss_update_notice() {
        check_ajax_referer('dismiss_update');
        
        $version = sanitize_text_field($_POST['version']);
        update_user_meta(get_current_user_id(), 'arbeitsdienste_update_dismissed_' . $version, true);
        
        wp_die();
    }
}

// Simple Update-Checker als Fallback initialisieren
if (is_admin() && !class_exists('ArbeitsdiensteAutoUpdater')) {
    new ArbeitsdiensteSimpleUpdateCheck(ARBEITSDIENSTE_PLUGIN_VERSION, 'leonbuchnerbd/wp-arbeitsdienste');
}
?>
