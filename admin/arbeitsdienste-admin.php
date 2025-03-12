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

// Anzeige der Admin-Seite mit Tabelle der Arbeitsdienste
function arbeitsdienste_admin_page() {
    ?>
    <div class="wrap">
        <h1>Arbeitsdienst Verwaltung</h1>
        <div class="arbeitsdienste-table-wrapper">
            <table class="arbeitsdienste-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Aufgabe</th>
                        <th>Datum</th>
                        <th>Uhrzeit</th>
                        <th>Benötigte Helfer</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $arbeitsdienste = get_posts(array(
                        'post_type'      => 'arbeitsdienste',
                        'posts_per_page' => -1,
                        'orderby'        => 'meta_value',
                        'meta_key'       => 'datum',
                        'order'          => 'ASC'
                    ));

                    if (!empty($arbeitsdienste)) {
                        foreach ($arbeitsdienste as $dienst) {
                            $datum = get_post_meta($dienst->ID, 'datum', true);
                            $time = get_post_meta($dienst->ID, 'uhrzeit', true);
                            $benoetigte_helfer = get_post_meta($dienst->ID, 'benoetigte_helfer', true);

                            echo '<tr>';
                            echo '<td data-label="ID">' . esc_html($dienst->ID) . '</td>';
                            echo '<td data-label="Aufgabe">' . esc_html($dienst->post_title) . '</td>';
                            echo '<td data-label="Datum">' . esc_html($datum) . '</td>';
                            echo '<td data-label="Uhrzeit">' . esc_html($time) . '</td>';
                            echo '<td data-label="Benötigte Helfer">' . esc_html($benoetigte_helfer) . '</td>';
                            echo '<td data-label="Aktionen">
                                    <a href="' . get_edit_post_link($dienst->ID) . '" class="button button-primary">Bearbeiten</a>
                                    <a href="' . get_delete_post_link($dienst->ID) . '" class="button button-danger">Löschen</a>
                                  </td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">Keine Arbeitsdienste gefunden.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>

