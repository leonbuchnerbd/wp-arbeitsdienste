<?php
// Sicherheitsprüfung: Direkter Zugriff auf die Datei verhindern
if (!defined('ABSPATH')) {
    exit;
}

// Menüpunkt im Admin-Bereich hinzufügen
function arbeitsdienste_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=arbeitsdienste',  // Untermenü von "Arbeitsdienste" (CPT)
        'Arbeitsdienst Verwaltung',          // Seitentitel
        'Arbeitsdienst Verwaltung',          // Menü-Name
        'manage_options',                    // Berechtigung
        'arbeitsdienste_admin',              // Slug der Seite
        'arbeitsdienste_admin_page'          // Callback-Funktion zur Anzeige der Seite
    );
}
add_action('admin_menu', 'arbeitsdienste_add_admin_menu');

// CSV-Export-Handler
function arbeitsdienste_handle_export() {
    if (isset($_GET['action']) && $_GET['action'] === 'export_csv' && 
        isset($_GET['page']) && $_GET['page'] === 'arbeitsdienste_admin' && 
        current_user_can('manage_options')) {
        
        arbeitsdienste_export_csv();
        exit;
    }
}
add_action('admin_init', 'arbeitsdienste_handle_export');

// CSV-Export-Funktion
function arbeitsdienste_export_csv() {
    $arbeitsdienste = get_posts(array(
        'post_type' => 'arbeitsdienste',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
        'meta_key' => 'datum',
        'order' => 'ASC'
    ));

    $filename = 'arbeitsdienste_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // UTF-8 BOM für korrekte Umlaute in Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Header-Zeile
    fputcsv($output, array(
        'ID',
        'Arbeitsdienst-ID',
        'Titel',
        'Beschreibung',
        'Datum',
        'Zeittyp',
        'Startzeit',
        'Endzeit',
        'Arbeitskreis',
        'Verantwortlicher',
        'E-Mail Verantwortlicher',
        'Benötigte Helfer',
        'Treffpunkt'
    ), ';');
    
    foreach ($arbeitsdienste as $dienst) {
        $arbeitsdienst_id = get_post_meta($dienst->ID, 'arbeitsdienst_id', true);
        $arbeitskreis = get_post_meta($dienst->ID, 'arbeitskreis', true);
        $datum = get_post_meta($dienst->ID, 'datum', true);
        $zeittyp = get_post_meta($dienst->ID, 'zeittyp', true);
        $start_zeit = get_post_meta($dienst->ID, 'start_zeit', true);
        $end_zeit = get_post_meta($dienst->ID, 'end_zeit', true);
        $verantwortlicher = get_post_meta($dienst->ID, 'verantwortlicher', true);
        $verantwortlicher_email = get_post_meta($dienst->ID, 'verantwortlicher_email', true);
        $benoetigte_helfer = get_post_meta($dienst->ID, 'benoetigte_helfer', true);
        $treffpunkt = get_post_meta($dienst->ID, 'treffpunkt', true);
        
        // Zeittyp-Text formatieren
        $zeittyp_text = '';
        switch($zeittyp) {
            case 'ganzer_tag':
                $zeittyp_text = 'Ganzer Tag';
                break;
            case 'zeitraum':
                $zeittyp_text = 'Zeitraum';
                break;
            case 'ab_zeit':
                $zeittyp_text = 'Ab Uhrzeit';
                break;
        }
        
        fputcsv($output, array(
            $dienst->ID,
            $arbeitsdienst_id,
            $dienst->post_title,
            strip_tags($dienst->post_content),
            $datum,
            $zeittyp_text,
            $start_zeit,
            $end_zeit,
            $arbeitskreis,
            $verantwortlicher,
            $verantwortlicher_email,
            $benoetigte_helfer,
            $treffpunkt
        ), ';');
    }
    
    fclose($output);
}

// Anzeige der Admin-Seite mit Tabelle der Arbeitsdienste
function arbeitsdienste_admin_page() {
    ?>
    <div class="wrap">
        <h1>Arbeitsdienst Verwaltung</h1>
        
        <div style="margin-bottom: 20px;">
            <a href="<?php echo admin_url('admin.php?page=arbeitsdienste_admin&action=export_csv'); ?>" class="button button-primary">
                <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                Als CSV exportieren
            </a>
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Arbeitsdienst-ID</th>
                    <th>Aufgabe</th>
                    <th>Datum</th>
                    <th>Zeit</th>
                    <th>Arbeitskreis</th>
                    <th>Verantwortlicher</th>
                    <th>Benötigte Helfer</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $arbeitsdienste = get_posts(array(
                    'post_type' => 'arbeitsdienste',
                    'posts_per_page' => -1,
                    'orderby' => 'meta_value',
                    'meta_key' => 'datum',
                    'order' => 'ASC'
                ));

                if (!empty($arbeitsdienste)) {
                    foreach ($arbeitsdienste as $dienst) {
                        $arbeitsdienst_id = get_post_meta($dienst->ID, 'arbeitsdienst_id', true);
                        $arbeitskreis = get_post_meta($dienst->ID, 'arbeitskreis', true);
                        $datum = get_post_meta($dienst->ID, 'datum', true);
                        $zeittyp = get_post_meta($dienst->ID, 'zeittyp', true);
                        $start_zeit = get_post_meta($dienst->ID, 'start_zeit', true);
                        $end_zeit = get_post_meta($dienst->ID, 'end_zeit', true);
                        $verantwortlicher = get_post_meta($dienst->ID, 'verantwortlicher', true);
                        $benoetigte_helfer = get_post_meta($dienst->ID, 'benoetigte_helfer', true);
                        
                        // Zeit-String formatieren
                        $zeit_anzeige = '';
                        switch($zeittyp) {
                            case 'ganzer_tag':
                                $zeit_anzeige = 'Ganztägig';
                                break;
                            case 'zeitraum':
                                $zeit_anzeige = $start_zeit . ' - ' . $end_zeit;
                                break;
                            case 'ab_zeit':
                                $zeit_anzeige = 'ab ' . $start_zeit;
                                break;
                            default:
                                $zeit_anzeige = 'Ganztägig';
                        }

                        echo '<tr>';
                        echo '<td>' . esc_html($dienst->ID) . '</td>';
                        echo '<td>' . esc_html($arbeitsdienst_id) . '</td>';
                        echo '<td>' . esc_html($dienst->post_title) . '</td>';
                        echo '<td>' . esc_html(date('d.m.Y', strtotime($datum))) . '</td>';
                        echo '<td>' . esc_html($zeit_anzeige) . '</td>';
                        echo '<td>' . esc_html($arbeitskreis) . '</td>';
                        echo '<td>' . esc_html($verantwortlicher) . '</td>';
                        echo '<td>' . esc_html($benoetigte_helfer) . '</td>';
                        echo '<td>
                                <a href="' . get_edit_post_link($dienst->ID) . '" class="button button-primary">Bearbeiten</a>
                                <a href="' . get_delete_post_link($dienst->ID) . '" class="button button-danger">Löschen</a>
                              </td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="9">Keine Arbeitsdienste gefunden.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
