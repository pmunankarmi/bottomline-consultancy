<?php
/** ACF options and versioned structured fields. */
defined('ABSPATH') || exit;
add_action('acf/init', function () {
    if (!bl_acf_ready()) { return; }
    acf_add_options_page(['page_title' => __('Theme Settings', 'bottomline'), 'menu_title' => __('Theme Settings', 'bottomline'), 'menu_slug' => 'bl-settings', 'capability' => 'manage_options', 'redirect' => false]);
    foreach (['clients' => __('Clients', 'bottomline'), 'testimonials' => __('Testimonials', 'bottomline')] as $slug => $title) {
        acf_add_options_sub_page(['page_title' => $title, 'menu_title' => $title, 'menu_slug' => 'bl-' . $slug, 'parent_slug' => 'bl-settings', 'capability' => 'manage_options']);
    }
    $schema = require __DIR__ . '/field-schema.php';
    foreach ($schema as $scope => $fields) {
        if ($scope === 'options') {
            $location = [[['param' => 'options_page', 'operator' => '==', 'value' => 'bl-settings']]];
            $tabs = ['General' => ['site_logo', 'home_client_count', 'home_team_count'], 'Contact and Social' => ['phone', 'email', 'website_label', 'social_links', 'form_services'], 'Branches' => ['branches', 'map_image'], 'CTA' => ['cta'], 'Footer' => ['footer_logo', 'footer_contact_heading', 'footer']];
            $ordered = [];
            foreach ($tabs as $label => $names) {
                $ordered[] = ['key' => 'field_bl_tab_' . sanitize_key($label), 'label' => $label, 'type' => 'tab'];
                foreach ($fields as $field) { if (in_array($field['name'], $names, true)) { $ordered[] = $field; } }
            }
            $fields = $ordered;
        } elseif ($scope === 'team_post') {
            $location = [[['param' => 'post_type', 'operator' => '==', 'value' => 'team']]];
        } elseif ($scope === 'testimonials') {
            $location = [[['param' => 'options_page', 'operator' => '==', 'value' => 'bl-testimonials']]];
        } else {
            // Clients contains both canonical options and page-specific summary/intro fields.
            if ($scope === 'clients') {
                $client_fields = array_values(array_filter($fields, fn($f) => $f['name'] === 'clients'));
                acf_add_local_field_group(['key' => 'group_bl_clients_options', 'title' => __('Shared clients', 'bottomline'), 'fields' => $client_fields, 'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'bl-clients']]]]);
                $fields = array_values(array_filter($fields, fn($f) => $f['name'] !== 'clients'));
            }
            $location = $scope === 'home' ? [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]] : [[['param' => 'page_template', 'operator' => '==', 'value' => 'page-' . $scope . '.php']]];
            $ordered = [];
            foreach ($fields as $field) {
                $ordered[] = ['key' => $field['key'] . '_tab', 'label' => $field['label'], 'type' => 'tab'];
                $ordered[] = $field;
            }
            $fields = $ordered;
        }
        acf_add_local_field_group(['key' => 'group_bl_' . $scope, 'title' => ucfirst(str_replace('_', ' ', $scope)) . ' — ' . __('Content', 'bottomline'), 'fields' => $fields, 'location' => $location, 'hide_on_screen' => ['the_content']]);
    }
});
