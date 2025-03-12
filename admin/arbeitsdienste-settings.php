<?php
// Admin-Menü für die Arbeitsdienst-Einstellungen hinzufügen
function arbeitsdienste_add_settings_menu() {
    add_options_page(
        'Arbeitsdienst Einstellungen',    // Seitentitel
        'Arbeitsdienst Einstellungen',    // Menü-Titel
        'manage_options',                 // Berechtigung
        'arbeitsdienste_settings',        // Seiten-Slug
        'arbeitsdienste_settings_page'    // Callback-Funktion für die Einstellungsseite
    );
}
add_action('admin_menu', 'arbeitsdienste_add_settings_menu');

// Einstellungsseite rendern
function arbeitsdienste_settings_page() {
    ?>
    <div class="wrap">
        <h1>Arbeitsdienst Einstellungen</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('arbeitsdienste_settings_group');
            do_settings_sections('arbeitsdienste_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Einstellungen registrieren
function arbeitsdienste_register_settings() {
    register_setting('arbeitsdienste_settings_group', 'arbeitsdienste_mailto_email');

    add_settings_section(
        'arbeitsdienste_settings_section',
        'Allgemeine Einstellungen',
        null,
        'arbeitsdienste_settings'
    );

    add_settings_field(
        'arbeitsdienste_mailto_email',
        'E-Mail für "Ich helfe gern"-Button',
        'arbeitsdienste_mailto_email_callback',
        'arbeitsdienste_settings',
        'arbeitsdienste_settings_section'
    );
}
add_action('admin_init', 'arbeitsdienste_register_settings');

// Eingabefeld für die Mail-Adresse
function arbeitsdienste_mailto_email_callback() {
    $email = get_option('arbeitsdienste_mailto_email', 'ichhelfegern@narrenzunft-badduerrheim.de');
    echo '<input type="email" name="arbeitsdienste_mailto_email" value="' . esc_attr($email) . '" style="width:400px;">';
}
