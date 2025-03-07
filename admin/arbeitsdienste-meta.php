<?php
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

function arbeitsdienste_meta_box_callback($post) {
    $arbeitskreis = get_post_meta($post->ID, 'arbeitskreis', true);
    $datum = get_post_meta($post->ID, 'datum', true);
    $verantwortlicher = get_post_meta($post->ID, 'verantwortlicher', true);
    $benoetigte_helfer = get_post_meta($post->ID, 'benoetigte_helfer', true);

    echo '<label>Arbeitskreis:</label><br>';
    echo '<input type="text" name="arbeitskreis" value="' . esc_attr($arbeitskreis) . '" style="width:100%"><br><br>';
    echo '<label>Datum:</label><br>';
    echo '<input type="date" name="datum" value="' . esc_attr($datum) . '" style="width:100%"><br><br>';
    echo '<label>Hauptverantwortlicher:</label><br>';
    echo '<input type="text" name="verantwortlicher" value="' . esc_attr($verantwortlicher) . '" style="width:100%"><br><br>';
    echo '<label>Benötigte Helfer:</label><br>';
    echo '<input type="number" name="benoetigte_helfer" value="' . esc_attr($benoetigte_helfer) . '" style="width:100%"><br>';
}

function arbeitsdienste_save_meta_boxes($post_id) {
    if (array_key_exists('arbeitskreis', $_POST)) {
        update_post_meta($post_id, 'arbeitskreis', sanitize_text_field($_POST['arbeitskreis']));
    }
    if (array_key_exists('datum', $_POST)) {
        update_post_meta($post_id, 'datum', sanitize_text_field($_POST['datum']));
    }
    if (array_key_exists('verantwortlicher', $_POST)) {
        update_post_meta($post_id, 'verantwortlicher', sanitize_text_field($_POST['verantwortlicher']));
    }
    if (array_key_exists('benoetigte_helfer', $_POST)) {
        update_post_meta($post_id, 'benoetigte_helfer', intval($_POST['benoetigte_helfer']));
    }
}
add_action('save_post', 'arbeitsdienste_save_meta_boxes');
