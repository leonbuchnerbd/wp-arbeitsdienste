<?php
/**
 * Arbeitsdienste Plugin Settings Page
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add settings page to admin menu
add_action('admin_menu', 'arbeitsdienste_add_settings_page');
function arbeitsdienste_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=arbeitsdienste',
        'Arbeitsdienste Einstellungen',
        'Einstellungen',
        'manage_options',
        'arbeitsdienste-settings',
        'arbeitsdienste_settings_page'
    );
}

// Register settings
add_action('admin_init', 'arbeitsdienste_register_settings');
function arbeitsdienste_register_settings() {
    register_setting('arbeitsdienste_settings', 'arbeitsdienste_default_email');
    register_setting('arbeitsdienste_settings', 'arbeitsdienste_email_subject_prefix');
    register_setting('arbeitsdienste_settings', 'arbeitsdienste_email_template');
    register_setting('arbeitsdienste_settings', 'arbeitsdienste_contact_info');
    
    add_settings_section(
        'arbeitsdienste_email_section',
        'E-Mail Einstellungen',
        'arbeitsdienste_email_section_callback',
        'arbeitsdienste_settings'
    );
    
    add_settings_field(
        'arbeitsdienste_default_email',
        'Standard E-Mail-Adresse',
        'arbeitsdienste_default_email_callback',
        'arbeitsdienste_settings',
        'arbeitsdienste_email_section'
    );
    
    add_settings_field(
        'arbeitsdienste_email_subject_prefix',
        'Betreff-Präfix',
        'arbeitsdienste_email_subject_prefix_callback',
        'arbeitsdienste_settings',
        'arbeitsdienste_email_section'
    );
    
    add_settings_field(
        'arbeitsdienste_email_template',
        'E-Mail Vorlage',
        'arbeitsdienste_email_template_callback',
        'arbeitsdienste_settings',
        'arbeitsdienste_email_section'
    );
    
    add_settings_field(
        'arbeitsdienste_contact_info',
        'Kontakt-Information',
        'arbeitsdienste_contact_info_callback',
        'arbeitsdienste_settings',
        'arbeitsdienste_email_section'
    );
}

// Settings page content
function arbeitsdienste_settings_page() {
    ?>
    <div class="wrap">
        <h1>Arbeitsdienste Einstellungen</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('arbeitsdienste_settings');
            do_settings_sections('arbeitsdienste_settings');
            submit_button();
            ?>
        </form>
        
        <div class="arbeitsdienste-help-section" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-left: 4px solid #007cba;">
            <h3>📋 Verwendung der Platzhalter</h3>
            <p>In der E-Mail-Vorlage können Sie folgende Platzhalter verwenden:</p>
            <ul>
                <li><code>{id}</code> - ID des Arbeitsdienstes</li>
                <li><code>{titel}</code> - Titel des Arbeitsdienstes</li>
                <li><code>{datum}</code> - Datum des Arbeitsdienstes</li>
                <li><code>{zeit}</code> - Zeit des Arbeitsdienstes</li>
                <li><code>{arbeitskreis}</code> - Zuständiger Arbeitskreis</li>
                <li><code>{verantwortlicher}</code> - Verantwortliche Person</li>
                <li><code>{treffpunkt}</code> - Treffpunkt</li>
                <li><code>{max_helfer}</code> - Maximale Anzahl Helfer</li>
                <li><code>{beschreibung}</code> - Beschreibung des Arbeitsdienstes</li>
            </ul>
            
            <h3>🎯 Shortcode Verwendung</h3>
            <p>Verwenden Sie <code>[arbeitsdienste]</code> auf einer Seite oder einem Beitrag, um alle Arbeitsdienste anzuzeigen.</p>
        </div>
    </div>
    
    <style>
    .arbeitsdienste-help-section ul {
        margin-left: 20px;
    }
    .arbeitsdienste-help-section code {
        background: #f0f0f0;
        padding: 2px 4px;
        border-radius: 3px;
        font-family: monospace;
    }
    </style>
    <?php
}

// Section callbacks
function arbeitsdienste_email_section_callback() {
    echo '<p>Konfigurieren Sie hier die E-Mail-Einstellungen für die Arbeitsdienste-Anmeldungen.</p>';
}

// Field callbacks
function arbeitsdienste_default_email_callback() {
    $value = get_option('arbeitsdienste_default_email', get_option('admin_email'));
    echo '<input type="email" name="arbeitsdienste_default_email" value="' . esc_attr($value) . '" style="width: 400px;" />';
    echo '<p class="description">Diese E-Mail-Adresse wird verwendet, wenn bei einem Arbeitsdienst keine spezifische E-Mail-Adresse angegeben ist.</p>';
}

function arbeitsdienste_email_subject_prefix_callback() {
    $value = get_option('arbeitsdienste_email_subject_prefix', 'Anmeldung Arbeitsdienst');
    echo '<input type="text" name="arbeitsdienste_email_subject_prefix" value="' . esc_attr($value) . '" style="width: 400px;" />';
    echo '<p class="description">Dieser Text wird vor den Titel des Arbeitsdienstes in den E-Mail-Betreff eingefügt.</p>';
}

function arbeitsdienste_email_template_callback() {
    $default_template = "Hallo {verantwortlicher},\n\nhiermit möchte ich mich für den folgenden Arbeitsdienst anmelden:\n\nID: {id}\nTitel: {titel}\nDatum: {datum}\nZeit: {zeit}\nArbeitskreis: {arbeitskreis}\nTreffpunkt: {treffpunkt}\n\n\n\nMit freundlichen Grüßen\n\nDEIN NAME";
    
    $value = get_option('arbeitsdienste_email_template', $default_template);
    echo '<textarea name="arbeitsdienste_email_template" rows="15" style="width: 100%; max-width: 600px;">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">Diese Vorlage wird für die E-Mail-Anmeldungen verwendet. Nutzen Sie die Platzhalter aus der Hilfe unten.</p>';
}

function arbeitsdienste_contact_info_callback() {
    $value = get_option('arbeitsdienste_contact_info', '');
    echo '<textarea name="arbeitsdienste_contact_info" rows="5" style="width: 100%; max-width: 600px;" placeholder="Zusätzliche Kontaktinformationen oder Hinweise für die Anmeldung...">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">Diese Information wird in der E-Mail-Vorlage angezeigt (optional).</p>';
}

// Helper function to get email for arbeitsdienst
function arbeitsdienste_get_email($post_id) {
    $email = get_post_meta($post_id, 'verantwortlicher_email', true);
    if (empty($email)) {
        $email = get_option('arbeitsdienste_default_email', get_option('admin_email'));
    }
    return $email;
}

// Helper function to generate email content
function arbeitsdienste_generate_email_content($post_id) {
    $template = get_option('arbeitsdienste_email_template');
    $subject_prefix = get_option('arbeitsdienste_email_subject_prefix', 'Anmeldung Arbeitsdienst');
    
    // Get post data
    $title = get_the_title($post_id);
    $arbeitsdienst_id = get_post_meta($post_id, 'arbeitsdienst_id', true);
    $datum = get_post_meta($post_id, 'datum', true);
    $zeittyp = get_post_meta($post_id, 'zeittyp', true);
    $start_zeit = get_post_meta($post_id, 'start_zeit', true);
    $end_zeit = get_post_meta($post_id, 'end_zeit', true);
    $arbeitskreis = get_post_meta($post_id, 'arbeitskreis', true);
    $verantwortlicher = get_post_meta($post_id, 'verantwortlicher', true);
    $treffpunkt = get_post_meta($post_id, 'treffpunkt', true);
    $max_helfer = get_post_meta($post_id, 'benoetigte_helfer', true);
    $beschreibung = get_post_field('post_content', $post_id);
    
    // Format time
    $zeit_text = '';
    switch($zeittyp) {
        case 'ganzer_tag':
            $zeit_text = 'Ganztägig';
            break;
        case 'zeitraum':
            $zeit_text = $start_zeit . ' - ' . $end_zeit . ' Uhr';
            break;
        case 'ab_zeit':
            $zeit_text = 'ab ' . $start_zeit . ' Uhr';
            break;
        default:
            $zeit_text = 'Ganztägig';
    }
    
    // Format date
    $datum_formatiert = $datum ? date_i18n('d.m.Y', strtotime($datum)) : '';
    
    // Replace placeholders
    $placeholders = [
        '{id}' => $arbeitsdienst_id,
        '{titel}' => $title,
        '{datum}' => $datum_formatiert,
        '{zeit}' => $zeit_text,
        '{arbeitskreis}' => $arbeitskreis,
        '{verantwortlicher}' => $verantwortlicher,
        '{treffpunkt}' => $treffpunkt,
        '{max_helfer}' => $max_helfer,
        '{beschreibung}' => strip_tags($beschreibung)
    ];
    
    $email_body = str_replace(array_keys($placeholders), array_values($placeholders), $template);
    $email_subject = $subject_prefix . ': ' . $title;
    
    return [
        'subject' => $email_subject,
        'body' => $email_body
    ];
}
?>
