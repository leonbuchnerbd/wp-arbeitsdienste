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

    $html = '<div class="container">';
    while ($query->have_posts()) {
        $query->the_post();
        $arbeitskreis = get_post_meta(get_the_ID(), 'arbeitskreis', true);
        $datum = get_post_meta(get_the_ID(), 'datum', true);
        $verantwortlicher = get_post_meta(get_the_ID(), 'verantwortlicher', true);
        $benoetigte_helfer = get_post_meta(get_the_ID(), 'benoetigte_helfer', true);

        $html .= '<div class="arbeitsdienst-kachel">';
        $html .= '<h2>' . get_the_title() . '</h2>';
        $html .= '<p><strong>Datum:</strong> ' . esc_html($datum) . '</p>';
        $html .= '<p><strong>Arbeitskreis:</strong> ' . esc_html($arbeitskreis) . '</p>';
        $html .= '<p><strong>Hauptverantwortlicher:</strong> ' . esc_html($verantwortlicher) . '</p>';
        $html .= '<p><strong>Benötigte Helfer:</strong> ' . esc_html($benoetigte_helfer) . '</p>';
        $html .= '</div>';
    }

    $html .= '</div>';
    wp_reset_postdata();
    return $html;
}
add_shortcode('arbeitsdienste', 'arbeitsdienste_shortcode');
