<?php
function arbeitsdienste_shortcode() {
    $query = new WP_Query(array(
        'post_type'      => 'arbeitsdienste',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value',
        'meta_key'       => 'datum',
        'order'          => 'ASC'
    ));

    if (!$query->have_posts()) {
        return "<p class='text-center text-gray-500'>Keine Arbeitsdienste gefunden.</p>";
    }

    $mailto_email = get_option('arbeitsdienste_mailto_email', 'ichhelfegern@narrenzunft-badduerrheim.de'); // Standard-Wert setzen

    // Responsives Grid-Container
    $html = '<div class="arbeitsdienste-container">';
    
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $title = get_the_title();
        $description = get_the_excerpt();
        $datum = get_post_meta($post_id, 'datum', true);
        $uhrzeit = get_post_meta($post_id, 'uhrzeit', true);
        $benoetigte_helfer = get_post_meta($post_id, 'benoetigte_helfer', true);
        $arbeitsdienst_link = get_permalink($post_id); // Link zur Arbeitsdienst-Detailseite

        // `mailto` E-Mail-Vorlage mit allen Arbeitsdienst-Infos
        $email_subject = rawurlencode("Anmeldung für Arbeitsdienst: $title");
        $email_body = rawurlencode(
            "Hallo,\n\nIch möchte mich für den folgenden Arbeitsdienst anmelden:\n\n" .
            "ID: $post_id\n" .
            "Name: $title\n" .
            "Datum: $datum\n" .
            "Uhrzeit: $uhrzeit Uhr\n\n" .
            "Bitte bestätigt meine Anmeldung.\n\nVielen Dank!\n\nMein Name"
        );

        $mailto_link = "mailto:$mailto_email?subject=$email_subject&body=$email_body";

        // Arbeitsdienst-Kachel
        $html .= '<div class="arbeitsdienst-kachel">';
        $html .= '<a href="' . esc_url($arbeitsdienst_link) . '" class="arbeitsdienst-link">';
        $html .= '<h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800">' . esc_html($title) . '</h2>';
        $html .= '<p class="text-lg sm:text-xl md:text-2xl text-gray-700"><strong>Datum:</strong> ' . esc_html($datum) . '</p>';
        $html .= '<p class="text-lg sm:text-xl md:text-2xl text-gray-700"><strong>Uhrzeit:</strong> ' . esc_html($uhrzeit) . ' Uhr</p>';
        $html .= '<p class="text-lg sm:text-xl md:text-2xl text-gray-700"><strong>Benötigte Helfer:</strong> ' . esc_html($benoetigte_helfer) . '</p>';        
        $html .= '</a>';
        
        // "Ich helfe gern"-Button
        $html .= '<a href="' . esc_url($mailto_link) . '" class="arbeitsdienst-button">Anmelden</a>';
        
        $html .= '</div>';
    }

    $html .= '</div>'; // Ende Container
    wp_reset_postdata();
    
    return $html;
}
add_shortcode('arbeitsdienste', 'arbeitsdienste_shortcode');
