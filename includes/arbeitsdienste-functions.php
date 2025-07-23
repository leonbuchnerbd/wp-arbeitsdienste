<?php
/**
 * Hilfsfunktionen für das Arbeitsdienste Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Formatiert die Zeitanzeige basierend auf dem Zeittyp
 */
function arbeitsdienste_format_time($zeittyp, $start_zeit, $end_zeit) {
    switch($zeittyp) {
        case 'ganzer_tag':
            return 'Ganztägig';
        case 'zeitraum':
            if ($start_zeit && $end_zeit) {
                return $start_zeit . ' - ' . $end_zeit . ' Uhr';
            }
            return '';
        case 'ab_zeit':
            if ($start_zeit) {
                return 'ab ' . $start_zeit . ' Uhr';
            }
            return '';
        default:
            return 'Ganztägig';
    }
}

/**
 * Formatiert das Datum für die deutsche Anzeige
 */
function arbeitsdienste_format_date($datum) {
    if (empty($datum)) {
        return '';
    }
    
    $timestamp = strtotime($datum);
    if ($timestamp === false) {
        return $datum;
    }
    
    return date('d.m.Y', $timestamp);
}

/**
 * Generiert eine E-Mail-URL für die Anmeldung
 */
function arbeitsdienste_generate_email_url($post_id) {
    $verantwortlicher_email = get_post_meta($post_id, 'verantwortlicher_email', true);
    
    if (empty($verantwortlicher_email)) {
        return '';
    }
    
    $title = get_the_title($post_id);
    $arbeitsdienst_id = get_post_meta($post_id, 'arbeitsdienst_id', true);
    $datum = get_post_meta($post_id, 'datum', true);
    $zeittyp = get_post_meta($post_id, 'zeittyp', true);
    $start_zeit = get_post_meta($post_id, 'start_zeit', true);
    $end_zeit = get_post_meta($post_id, 'end_zeit', true);
    $arbeitskreis = get_post_meta($post_id, 'arbeitskreis', true);
    $verantwortlicher = get_post_meta($post_id, 'verantwortlicher', true);
    $treffpunkt = get_post_meta($post_id, 'treffpunkt', true);
    
    $zeit_anzeige = arbeitsdienste_format_time($zeittyp, $start_zeit, $end_zeit);
    $datum_formatiert = arbeitsdienste_format_date($datum);
    
    $betreff = 'Anmeldung Arbeitsdienst: ' . $title;
    
    $nachricht = "Hallo {$verantwortlicher},\n\n";
    $nachricht .= "hiermit möchte ich mich für den folgenden Arbeitsdienst anmelden:\n\n";
    $nachricht .= "Arbeitsdienst: {$title}\n";
    
    if ($arbeitsdienst_id) {
        $nachricht .= "ID: {$arbeitsdienst_id}\n";
    }
    
    if ($datum_formatiert) {
        $nachricht .= "Datum: {$datum_formatiert}\n";
    }
    
    if ($zeit_anzeige && $zeittyp !== 'ganzer_tag') {
        $nachricht .= "Zeit: {$zeit_anzeige}\n";
    }
    
    if ($arbeitskreis) {
        $nachricht .= "Arbeitskreis: {$arbeitskreis}\n";
    }
    
    if ($treffpunkt) {
        $nachricht .= "Treffpunkt: {$treffpunkt}\n";
    }
    
    $nachricht .= "\nMein Name: [Bitte eintragen]\n";
    $nachricht .= "Meine Telefonnummer: [Bitte eintragen]\n\n";
    $nachricht .= "Vielen Dank!\n\n";
    $nachricht .= "Mit freundlichen Grüßen";
    
    $mailto_url = 'mailto:' . $verantwortlicher_email . 
                  '?subject=' . rawurlencode($betreff) . 
                  '&body=' . rawurlencode($nachricht);
    
    return $mailto_url;
}

/**
 * Validiert eine Arbeitsdienst-ID auf Eindeutigkeit
 */
function arbeitsdienste_validate_id($arbeitsdienst_id, $current_post_id = 0) {
    if (empty($arbeitsdienst_id)) {
        return true; // Leere ID ist erlaubt
    }
    
    $existing_posts = get_posts(array(
        'post_type' => 'arbeitsdienste',
        'meta_query' => array(
            array(
                'key' => 'arbeitsdienst_id',
                'value' => $arbeitsdienst_id,
                'compare' => '='
            )
        ),
        'exclude' => array($current_post_id),
        'posts_per_page' => 1
    ));
    
    return empty($existing_posts);
}

/**
 * Fügt Admin-Notices hinzu
 */
function arbeitsdienste_add_admin_notice($message, $type = 'success') {
    add_action('admin_notices', function() use ($message, $type) {
        echo '<div class="notice notice-' . esc_attr($type) . ' arbeitsdienste-notice is-dismissible">';
        echo '<p>' . esc_html($message) . '</p>';
        echo '</div>';
    });
}

/**
 * Bereinigt Post-Meta-Daten beim Löschen
 */
function arbeitsdienste_cleanup_meta($post_id) {
    if (get_post_type($post_id) !== 'arbeitsdienste') {
        return;
    }
    
    $meta_keys = array(
        'arbeitsdienst_id',
        'arbeitskreis',
        'datum',
        'zeittyp',
        'start_zeit',
        'end_zeit',
        'verantwortlicher',
        'verantwortlicher_email',
        'benoetigte_helfer',
        'treffpunkt'
    );
    
    foreach ($meta_keys as $key) {
        delete_post_meta($post_id, $key);
    }
}
add_action('before_delete_post', 'arbeitsdienste_cleanup_meta');

/**
 * Registriert Custom Fields für REST API (falls benötigt)
 */
function arbeitsdienste_register_rest_fields() {
    $meta_fields = array(
        'arbeitsdienst_id',
        'arbeitskreis',
        'datum',
        'zeittyp',
        'start_zeit',
        'end_zeit',
        'verantwortlicher',
        'verantwortlicher_email',
        'benoetigte_helfer',
        'treffpunkt'
    );
    
    foreach ($meta_fields as $field) {
        register_rest_field('arbeitsdienste', $field, array(
            'get_callback' => function($post) use ($field) {
                return get_post_meta($post['id'], $field, true);
            },
            'update_callback' => function($value, $post) use ($field) {
                return update_post_meta($post->ID, $field, $value);
            }
        ));
    }
}
add_action('rest_api_init', 'arbeitsdienste_register_rest_fields');
