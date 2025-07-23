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

    $html = '<div class="arbeitsdienste-container">';
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $arbeitsdienst_id = get_post_meta($post_id, 'arbeitsdienst_id', true);
        $arbeitskreis = get_post_meta($post_id, 'arbeitskreis', true);
        $datum = get_post_meta($post_id, 'datum', true);
        $zeittyp = get_post_meta($post_id, 'zeittyp', true);
        $start_zeit = get_post_meta($post_id, 'start_zeit', true);
        $end_zeit = get_post_meta($post_id, 'end_zeit', true);
        $verantwortlicher = get_post_meta($post_id, 'verantwortlicher', true);
        $verantwortlicher_email = get_post_meta($post_id, 'verantwortlicher_email', true);
        $benoetigte_helfer = get_post_meta($post_id, 'benoetigte_helfer', true);
        $treffpunkt = get_post_meta($post_id, 'treffpunkt', true);

        // Zeit-String formatieren
        $zeit_anzeige = '';
        switch($zeittyp) {
            case 'ganzer_tag':
                $zeit_anzeige = 'Ganztägig';
                break;
            case 'zeitraum':
                $zeit_anzeige = $start_zeit . ' - ' . $end_zeit . ' Uhr';
                break;
            case 'ab_zeit':
                $zeit_anzeige = 'ab ' . $start_zeit . ' Uhr';
                break;
            default:
                $zeit_anzeige = 'Ganztägig';
        }

        // Datum formatieren
        $datum_formatiert = date('d.m.Y', strtotime($datum));
        
        // E-Mail-Adresse bestimmen (aus Settings oder individuell)
        $final_email = $verantwortlicher_email;
        if (empty($final_email)) {
            $final_email = get_option('arbeitsdienste_default_email', get_option('admin_email'));
        }
        
        // E-Mail-Inhalt aus Einstellungen generieren
        if (function_exists('arbeitsdienste_generate_email_content')) {
            $email_data = arbeitsdienste_generate_email_content($post_id);
            $email_betreff = $email_data['subject'];
            $email_text = $email_data['body'];
        } else {
            // Template aus Settings laden
            $template = get_option('arbeitsdienste_email_template');
            $subject_prefix = get_option('arbeitsdienste_email_subject_prefix', 'Anmeldung Arbeitsdienst');
            
            // Fallback-Template wenn leer
            if (empty($template)) {
                $template = "Hallo,\n\nhiermit möchte ich mich für den folgenden Arbeitsdienst anmelden:\n\nID: {id}\nTitel: {titel}\nDatum: {datum}\nZeit: {zeit}\nArbeitskreis: {arbeitskreis}\nTreffpunkt: {treffpunkt}\n\nMeine Kontaktdaten:\nName: [Bitte eintragen]\nTelefon: [Bitte eintragen]\nE-Mail: [Bitte eintragen]\n\nVielen Dank für die Organisation!\n\nMit freundlichen Grüßen";
            }
            
            // Platzhalter ersetzen
            $placeholders = [
                '{id}' => $arbeitsdienst_id,
                '{titel}' => get_the_title(),
                '{datum}' => $datum_formatiert,
                '{zeit}' => $zeit_anzeige,
                '{arbeitskreis}' => $arbeitskreis ?: '-',
                '{verantwortlicher}' => $verantwortlicher ?: '',
                '{treffpunkt}' => $treffpunkt ?: '',
                '{max_helfer}' => $benoetigte_helfer ?: '',
                '{beschreibung}' => strip_tags(get_the_content()) ?: ''
            ];
            
            $email_text = str_replace(array_keys($placeholders), array_values($placeholders), $template);
            $email_betreff = $subject_prefix . ': ' . get_the_title();
        }

        $html .= '<div class="arbeitsdienst-kachel">';
        
        // Kopfzeile mit Titel und ID
        $html .= '<div class="arbeitsdienst-header">';
        $html .= '<h2>' . get_the_title() . '</h2>';
        if ($arbeitsdienst_id) {
            $html .= '<div class="arbeitsdienst-id">ID: ' . esc_html($arbeitsdienst_id) . '</div>';
        }
        $html .= '</div>';
        
        $html .= '<div class="arbeitsdienst-content">';
        
        // Datum ist Pflichtfeld und sollte immer angezeigt werden
        if ($datum) {
            $html .= '<div class="arbeitsdienst-datum">Datum: ' . esc_html($datum_formatiert) . '</div>';
        }
        
        // Zeit nur anzeigen wenn nicht ganztägig
        if ($zeit_anzeige && $zeittyp !== 'ganzer_tag') {
            $html .= '<div class="arbeitsdienst-zeit">Zeit: ' . esc_html($zeit_anzeige) . '</div>';
        }
        
        if ($arbeitskreis) {
            $html .= '<div class="arbeitsdienst-arbeitskreis">Arbeitskreis: ' . esc_html($arbeitskreis) . '</div>';
        }
        
        if ($verantwortlicher) {
            $html .= '<div class="arbeitsdienst-verantwortlicher">Verantwortlich: ' . esc_html($verantwortlicher) . '</div>';
        }
        
        if ($treffpunkt) {
            $html .= '<div class="arbeitsdienst-treffpunkt">Treffpunkt: ' . esc_html($treffpunkt) . '</div>';
        }
        
        if ($benoetigte_helfer) {
            $html .= '<div class="arbeitsdienst-helfer">Helfer benötigt: ' . esc_html($benoetigte_helfer) . '</div>';
        }
        
        // Beschreibung falls vorhanden
        if (get_the_content()) {
            $html .= '<div class="arbeitsdienst-beschreibung">' . wp_trim_words(get_the_content(), 25) . '</div>';
        }
        
        $html .= '</div>'; // Ende arbeitsdienst-content
        
        // Anmelde-Button - nur dieser ist klickbar für E-Mail
        $html .= '<div class="arbeitsdienst-anmelden" onclick="openEmailClient(this)" data-email="' . esc_attr($final_email) . '" data-subject="' . esc_attr($email_betreff) . '" data-body="' . esc_attr($email_text) . '">Anmelden</div>';
        
        $html .= '</div>'; // Ende arbeitsdienst-kachel
    }

    $html .= '</div>';
    
    // JavaScript für E-Mail-Client mit Touch-Optimierung
    $html .= '<script>
    function openEmailClient(element) {
        var email = element.getAttribute("data-email");
        var subject = decodeURIComponent(element.getAttribute("data-subject"));
        var body = decodeURIComponent(element.getAttribute("data-body"));
        
        if (email) {
            var mailtoLink = "mailto:" + email + "?subject=" + subject + "&body=" + body;
            
            // Mobile-optimized link opening
            if (window.navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/)) {
                // For mobile devices, try different approaches
                var link = document.createElement("a");
                link.href = mailtoLink;
                link.style.display = "none";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                // For desktop
                window.location.href = mailtoLink;
            }
        }
    }
    
    // Add touch feedback for mobile devices
    document.addEventListener("DOMContentLoaded", function() {
        var kacheln = document.querySelectorAll(".arbeitsdienst-kachel");
        
        if ("ontouchstart" in window) {
            kacheln.forEach(function(kachel) {
                // Add touch class for styling
                kachel.classList.add("touch-device");
                
                // Improve touch feedback
                kachel.addEventListener("touchstart", function() {
                    this.style.transform = "translateY(-1px)";
                    this.style.transition = "transform 0.1s ease";
                });
                
                kachel.addEventListener("touchend", function() {
                    var self = this;
                    setTimeout(function() {
                        self.style.transform = "";
                        self.style.transition = "all 0.3s ease";
                    }, 100);
                });
                
                // Handle touch cancel
                kachel.addEventListener("touchcancel", function() {
                    this.style.transform = "";
                    this.style.transition = "all 0.3s ease";
                });
            });
        }
        
        // Handle orientation change for responsive layout
        window.addEventListener("orientationchange", function() {
            setTimeout(function() {
                // Force layout recalculation
                var container = document.querySelector(".arbeitsdienste-container");
                if (container) {
                    container.style.display = "none";
                    container.offsetHeight; // Trigger reflow
                    container.style.display = "grid";
                }
            }, 100);
        });
        
        // Optimize for accessibility
        kacheln.forEach(function(kachel) {
            // Add keyboard support
            kachel.setAttribute("tabindex", "0");
            kachel.setAttribute("role", "button");
            kachel.setAttribute("aria-label", "Klicken um sich für diesen Arbeitsdienst anzumelden");
            
            // Handle keyboard events
            kachel.addEventListener("keydown", function(e) {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    openEmailClient(this);
                }
            });
        });
    });
    </script>';
    
    // Mobile-optimized CSS
    $html .= '<style>
    /* Touch device optimizations */
    .touch-device.arbeitsdienst-kachel {
        cursor: pointer;
        -webkit-tap-highlight-color: rgba(0, 123, 186, 0.3);
        tap-highlight-color: rgba(0, 123, 186, 0.3);
    }
    
    .touch-device.arbeitsdienst-kachel:active {
        transform: translateY(1px);
        box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    }
    
    /* Accessibility improvements */
    .arbeitsdienst-kachel:focus {
        outline: 2px solid #007cba;
        outline-offset: 2px;
    }
    
    /* Responsive text scaling */
    @media (max-width: 768px) {
        .arbeitsdienste-container {
            font-size: 14px;
        }
    }
    
    @media (max-width: 480px) {
        .arbeitsdienste-container {
            font-size: 13px;
        }
        
        .arbeitsdienst-kachel strong {
            font-size: 13px;
        }
    }
    
    /* High DPI display optimizations */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .arbeitsdienst-kachel {
            border-width: 0.5px;
        }
    }
    </style>';

    wp_reset_postdata();
    return $html;
}
add_shortcode('arbeitsdienste', 'arbeitsdienste_shortcode');
