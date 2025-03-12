<?php
// Meta-Box für Arbeitsdienst-Details hinzufügen
function arbeitsdienste_add_meta_boxes() {
    add_meta_box(
        'arbeitsdienste_details',
        'Arbeitsdienst Details',
        'arbeitsdienste_meta_box_callback',
        'arbeitsdienste',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'arbeitsdienste_add_meta_boxes');

// Meta-Box HTML (Responsives Layout)
function arbeitsdienste_meta_box_callback($post) {
    $datum = get_post_meta($post->ID, 'datum', true);
    $uhrzeit = get_post_meta($post->ID, 'uhrzeit', true);
    $benoetigte_helfer = get_post_meta($post->ID, 'benoetigte_helfer', true);
    $beschreibung = get_the_content(null, false, $post); // Post-Inhalt als Beschreibung

    echo '<div class="arbeitsdienst-meta-box">';
    
    echo '<div class="arbeitsdienst-meta-field">';
    echo '<label for="datum">📅 Datum:</label>';
    echo '<input type="date" id="datum" name="datum" value="' . esc_attr($datum) . '">';
    echo '</div>';

    echo '<div class="arbeitsdienst-meta-field">';
    echo '<label for="uhrzeit">⏰ Uhrzeit:</label>';
    echo '<input type="time" id="uhrzeit" name="uhrzeit" value="' . esc_attr($uhrzeit) . '">';
    echo '</div>';

    echo '<div class="arbeitsdienst-meta-field">';
    echo '<label for="benoetigte_helfer">👥 Benötigte Helfer:</label>';
    echo '<input type="number" id="benoetigte_helfer" name="benoetigte_helfer" value="' . esc_attr($benoetigte_helfer) . '">';
    echo '</div>';

    echo '<div class="arbeitsdienst-meta-field full-width">';
    echo '<label>📝 Beschreibung (aus Post-Inhalt):</label>';
    echo '<textarea disabled>' . esc_textarea($beschreibung) . '</textarea>';
    echo '</div>';

    echo '</div>'; // Ende Meta-Box
}

// Speichern der Meta-Box Daten
function arbeitsdienste_save_meta_boxes($post_id) {
    if (array_key_exists('datum', $_POST)) {
        update_post_meta($post_id, 'datum', sanitize_text_field($_POST['datum']));
    }
    if (array_key_exists('uhrzeit', $_POST)) {
        update_post_meta($post_id, 'uhrzeit', sanitize_text_field($_POST['uhrzeit']));
    }
    if (array_key_exists('benoetigte_helfer', $_POST)) {
        update_post_meta($post_id, 'benoetigte_helfer', intval($_POST['benoetigte_helfer']));
    }
}
add_action('save_post', 'arbeitsdienste_save_meta_boxes');
