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
    // Nonce-Feld für Sicherheit
    wp_nonce_field('arbeitsdienste_meta_nonce', 'arbeitsdienste_meta_nonce');
    
    $arbeitsdienst_id = get_post_meta($post->ID, 'arbeitsdienst_id', true);
    $arbeitskreis = get_post_meta($post->ID, 'arbeitskreis', true);
    $datum = get_post_meta($post->ID, 'datum', true);
    $zeittyp = get_post_meta($post->ID, 'zeittyp', true) ?: 'ganzer_tag';
    $start_zeit = get_post_meta($post->ID, 'start_zeit', true);
    $end_zeit = get_post_meta($post->ID, 'end_zeit', true);
    $verantwortlicher = get_post_meta($post->ID, 'verantwortlicher', true);
    $verantwortlicher_email = get_post_meta($post->ID, 'verantwortlicher_email', true);
    $benoetigte_helfer = get_post_meta($post->ID, 'benoetigte_helfer', true);
    $treffpunkt = get_post_meta($post->ID, 'treffpunkt', true);

    echo '<table style="width: 100%;">';
    
    echo '<tr>';
    echo '<td style="width: 50%; padding-right: 20px; vertical-align: top;">';
    echo '<label><strong>Arbeitsdienst-ID:</strong></label><br>';
    echo '<input type="text" name="arbeitsdienst_id" value="' . esc_attr($arbeitsdienst_id) . '" style="width:100%" placeholder="z.B. AD2025-001"><br><br>';
    
    echo '<label><strong>Arbeitskreis:</strong></label><br>';
    echo '<input type="text" name="arbeitskreis" value="' . esc_attr($arbeitskreis) . '" style="width:100%"><br><br>';
    
    echo '<label><strong>Datum:</strong></label><br>';
    echo '<input type="date" name="datum" value="' . esc_attr($datum) . '" style="width:100%"><br><br>';
    
    echo '<label><strong>Zeittyp:</strong></label><br>';
    echo '<select name="zeittyp" id="zeittyp" style="width:100%">';
    echo '<option value="ganzer_tag"' . selected($zeittyp, 'ganzer_tag', false) . '>Ganzer Tag</option>';
    echo '<option value="zeitraum"' . selected($zeittyp, 'zeitraum', false) . '>Zeitraum (von-bis)</option>';
    echo '<option value="ab_zeit"' . selected($zeittyp, 'ab_zeit', false) . '>Ab bestimmter Uhrzeit</option>';
    echo '</select><br><br>';
    
    echo '<div id="zeit_details" style="display: none;">';
    echo '<label><strong>Startzeit:</strong></label><br>';
    echo '<input type="time" name="start_zeit" value="' . esc_attr($start_zeit) . '" style="width:100%"><br><br>';
    echo '<div id="end_zeit_container">';
    echo '<label><strong>Endzeit:</strong></label><br>';
    echo '<input type="time" name="end_zeit" value="' . esc_attr($end_zeit) . '" style="width:100%"><br><br>';
    echo '</div>';
    echo '</div>';
    
    echo '</td>';
    echo '<td style="width: 50%; vertical-align: top;">';
    
    echo '<label><strong>Hauptverantwortlicher:</strong></label><br>';
    echo '<input type="text" name="verantwortlicher" value="' . esc_attr($verantwortlicher) . '" style="width:100%"><br><br>';
    
    echo '<label><strong>E-Mail des Verantwortlichen:</strong></label><br>';
    echo '<input type="email" name="verantwortlicher_email" value="' . esc_attr($verantwortlicher_email) . '" style="width:100%" placeholder="name@example.com"><br><br>';
    
    echo '<label><strong>Benötigte Helfer:</strong></label><br>';
    echo '<input type="number" name="benoetigte_helfer" value="' . esc_attr($benoetigte_helfer) . '" style="width:100%" min="1"><br><br>';
    
    echo '<label><strong>Treffpunkt:</strong></label><br>';
    echo '<input type="text" name="treffpunkt" value="' . esc_attr($treffpunkt) . '" style="width:100%" placeholder="z.B. Vereinsheim, Bauhof"><br><br>';
    
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    
    // JavaScript für die Zeit-Felder
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        var zeittyp = document.getElementById("zeittyp");
        var zeitDetails = document.getElementById("zeit_details");
        var endZeitContainer = document.getElementById("end_zeit_container");
        
        function toggleZeitFields() {
            var value = zeittyp.value;
            if (value === "ganzer_tag") {
                zeitDetails.style.display = "none";
            } else {
                zeitDetails.style.display = "block";
                if (value === "ab_zeit") {
                    endZeitContainer.style.display = "none";
                } else {
                    endZeitContainer.style.display = "block";
                }
            }
        }
        
        // Touch-optimized event handling
        if ("ontouchstart" in window) {
            zeittyp.addEventListener("touchend", function(e) {
                setTimeout(toggleZeitFields, 100); // Small delay for touch devices
            });
        }
        
        zeittyp.addEventListener("change", toggleZeitFields);
        zeittyp.addEventListener("input", toggleZeitFields); // For better mobile support
        toggleZeitFields(); // Initial call
        
        // Mobile-specific optimizations
        if (window.innerWidth <= 768) {
            // Add mobile-specific classes
            if (zeitDetails) {
                zeitDetails.classList.add("mobile-zeit-details");
            }
            
            // Improve touch targets on mobile
            var allInputs = document.querySelectorAll("#arbeitsdienste_details input, #arbeitsdienste_details select");
            allInputs.forEach(function(input) {
                input.style.minHeight = "44px";
                input.style.fontSize = "16px"; // Prevents zoom on iOS
            });
        }
        
        // Handle screen orientation change
        window.addEventListener("orientationchange", function() {
            setTimeout(function() {
                toggleZeitFields();
                // Recalculate layout if needed
                if (window.innerWidth <= 768) {
                    var metaTable = document.querySelector(".arbeitsdienste-meta-table");
                    if (metaTable) {
                        metaTable.style.display = "block";
                    }
                }
            }, 100);
        });
    });
    </script>';
    
    // Mobile-optimized CSS for admin
    echo '<style>
    @media (max-width: 768px) {
        #arbeitsdienste_details .inside {
            padding: 10px;
        }
        
        #arbeitsdienste_details table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        #arbeitsdienste_details td {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        #zeit_details.mobile-zeit-details {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }
        
        #zeit_details.mobile-zeit-details label {
            color: #495057;
            font-weight: 600;
        }
    }
    
    @media (max-width: 480px) {
        #arbeitsdienste_details .inside {
            padding: 5px;
        }
    }
    </style>';
}

function arbeitsdienste_save_meta_boxes($post_id) {
    // Nonce-Verifikation
    if (!isset($_POST['arbeitsdienste_meta_nonce']) || !wp_verify_nonce($_POST['arbeitsdienste_meta_nonce'], 'arbeitsdienste_meta_nonce')) {
        return;
    }

    // Prüfung für Autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Berechtigungsprüfung
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Validierung der Arbeitsdienst-ID auf Eindeutigkeit
    if (array_key_exists('arbeitsdienst_id', $_POST) && !empty($_POST['arbeitsdienst_id'])) {
        $arbeitsdienst_id = sanitize_text_field($_POST['arbeitsdienst_id']);
        if (!arbeitsdienste_validate_id($arbeitsdienst_id, $post_id)) {
            arbeitsdienste_add_admin_notice('Die Arbeitsdienst-ID "' . $arbeitsdienst_id . '" wird bereits verwendet. Bitte wählen Sie eine andere ID.', 'error');
            return; // Speichern abbrechen wenn ID bereits existiert
        }
        update_post_meta($post_id, 'arbeitsdienst_id', $arbeitsdienst_id);
    }

    // Speichere alle anderen Meta-Felder
    if (array_key_exists('arbeitskreis', $_POST)) {
        update_post_meta($post_id, 'arbeitskreis', sanitize_text_field($_POST['arbeitskreis']));
    }
    if (array_key_exists('datum', $_POST)) {
        update_post_meta($post_id, 'datum', sanitize_text_field($_POST['datum']));
    }
    if (array_key_exists('zeittyp', $_POST)) {
        update_post_meta($post_id, 'zeittyp', sanitize_text_field($_POST['zeittyp']));
    }
    if (array_key_exists('start_zeit', $_POST)) {
        update_post_meta($post_id, 'start_zeit', sanitize_text_field($_POST['start_zeit']));
    }
    if (array_key_exists('end_zeit', $_POST)) {
        update_post_meta($post_id, 'end_zeit', sanitize_text_field($_POST['end_zeit']));
    }
    if (array_key_exists('verantwortlicher', $_POST)) {
        update_post_meta($post_id, 'verantwortlicher', sanitize_text_field($_POST['verantwortlicher']));
    }
    if (array_key_exists('verantwortlicher_email', $_POST)) {
        $email = sanitize_email($_POST['verantwortlicher_email']);
        if (!empty($email) && !is_email($email)) {
            arbeitsdienste_add_admin_notice('Die eingegebene E-Mail-Adresse ist nicht gültig.', 'error');
            return;
        }
        update_post_meta($post_id, 'verantwortlicher_email', $email);
    }
    if (array_key_exists('benoetigte_helfer', $_POST)) {
        $helfer_anzahl = intval($_POST['benoetigte_helfer']);
        if ($helfer_anzahl < 0) {
            $helfer_anzahl = 0;
        }
        update_post_meta($post_id, 'benoetigte_helfer', $helfer_anzahl);
    }
    if (array_key_exists('treffpunkt', $_POST)) {
        update_post_meta($post_id, 'treffpunkt', sanitize_text_field($_POST['treffpunkt']));
    }
}
add_action('save_post', 'arbeitsdienste_save_meta_boxes');
