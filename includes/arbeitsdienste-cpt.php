<?php
function arbeitsdienste_register_post_type() {
    $args = array(
        'labels'        => array(
            'name'          => 'Arbeitsdienste',
            'singular_name' => 'Arbeitsdienst',
            'menu_name'     => 'Arbeitsdienste',
            'add_new'       => 'Neuen Arbeitsdienst hinzufügen'
        ),
        'public'        => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-hammer',
        'supports'      => array('title', 'editor', 'custom-fields'),
        'has_archive'   => false,
        'show_in_rest'  => true,
    );

    register_post_type('arbeitsdienste', $args);
}
add_action('init', 'arbeitsdienste_register_post_type');
