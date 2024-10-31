<?php

class Dcpp_Custom_Form {

    public function __construct() {
        add_action('admin_init', array($this, 'dcpp_custom_form_settings'));
        add_action('admin_menu', array($this, 'dcpp_custom_form_menu'));
    }

    public function dcpp_custom_form_settings() {
        register_setting(
            'custom_forms_options_group',
            'custom_forms',
            array($this, 'sanitize_custom_forms')
        );

        add_settings_section(
            'custom_forms_section',
            'Define Your Forms',
            array($this, 'custom_forms_section_callback'),
            'custom-forms'
        );

        add_settings_field(
            'custom_forms',
            'Forms',
            array($this, 'dcpp_custom_forms_callback'),
            'custom-forms',
            'custom_forms_section'
        );
    }

    public function custom_forms_section_callback() {
        echo '<p>Enter the details for your custom forms below.</p>';
        ?>
        <div id="form-builder"></div>
    <?php
    }

    public function dcpp_custom_forms_callback() {
        $custom_forms = get_option('custom_forms', array());
       
    }
    
    

    public function sanitize_custom_forms($input) {
        $output = array();
        foreach ($input as $form) {
            $output[] = array(
                'form_name' => sanitize_text_field($form['form_name']),
                'fields' => wp_json_encode(json_decode($form['fields'], true)), // Ensure valid JSON
            );
        }
        return $output;
    }

    public function dcpp_custom_form_page() {
        require_once(plugin_dir_path(__FILE__) . 'componant/dcpp_plugin_navbar.php');
        $dcpp_navbar = new Dcpp_Plugin_Navbar;

        echo $dcpp_navbar->dcpp_navbar();
        echo "<h1>Custom Forms</h1>";
        ?>
        <div class="wrap">
            <form method="post" action="options.php">
                <?php settings_fields('custom_forms_options_group'); ?>
                <?php do_settings_sections('custom-forms'); ?>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new Dcpp_Custom_Form();
