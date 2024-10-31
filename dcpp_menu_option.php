<?php

class Dcpp_Menu_Option {

    public function __construct() {
        add_action('admin_menu', array($this, 'dcpp_admin_menu_option'));
        require_once(plugin_dir_path(__FILE__) . 'views/dcpp_notification_alert_form.php');

        $short_code_name = 'dcpp_customer_form';
        add_shortcode($short_code_name, array(new Dcpp_notification_alert_form(), 'dcpp_customer_alert_modal'));
    }

    public function dcpp_admin_menu_option() {
        add_menu_page(
            'Price Alert Notifications',            // Page title
            'Price Alert Notifications',            // Menu title
            'manage_options',                       // Capability
            'dcpp_price_alert_notifications',       // Menu slug for the first submenu page
            array($this, 'dcpp_price_alert_options_function'), // Callback function
            'dashicons-admin-generic',              // Icon URL or Dashicon class
            9                                       // Position
        );


        add_submenu_page(
            'dcpp_price_alert_notifications',       // Parent slug
            'General Options',                         // Page title
            'General Options',                         // Menu title
            'manage_options',                       // Capability
            'dcpp_form_options',                    // Menu slug
            array($this, 'dcpp_customize_options')   // Callback function
        );

        add_submenu_page(
            'dcpp_price_alert_notifications',       // Parent slug
            'SMTP Mail setting',                    // Page title
            'Dcpp SMTP Mail Options',               // Menu title
            'manage_options',                       // Capability
            'dcpp_smtp_mail_options',               // Menu slug
            array($this, 'dcpp_smtp_mail_options')  // Callback function
        );

        add_submenu_page(
            'dcpp_price_alert_notifications',       // Parent slug
            'Mail Template setting',                // Page title
            'Mail Template Settings',               // Menu title
            'manage_options',                       // Capability
            'dcpp_smtp_mail_template_options',      // Menu slug
            array($this, 'dcpp_mail_template_settings') // Callback function
        );


      
        add_submenu_page(
            'dcpp_price_alert_notifications',
            'Labels',
            'Labels',
            'manage_options',
            'dcpp_manage_labels',
            array($this, 'dcpp_manage_labels')
        );


    }

    public function dcpp_price_alert_options_function() {
        $short_code_name = 'dcpp_customer_form';
        echo "<h3>Use this Short Code: [{$short_code_name}]</h3>";
        require_once(plugin_dir_path(__FILE__) . 'views/dcpp_user_alert_data.php');
    }

    public function dcpp_customize_options() {
        require_once(plugin_dir_path(__FILE__) . 'views/componant/dcpp_plugin_navbar.php');
        $navbar = new dcpp_plugin_navbar;

        require_once(plugin_dir_path(__FILE__) . 'views/componant/dcpp_price_alert_options.php');
        $button_position = new dcpp_price_alert_options;

        echo $navbar->dcpp_navbar();
        echo $button_position->dcpp_button_position_option();
    }

    public function dcpp_smtp_mail_options() {
        require_once(plugin_dir_path(__FILE__) . 'views/componant/dcpp_plugin_navbar.php');
        require_once(plugin_dir_path(__FILE__) . 'views/componant/dcpp_smtp_config_option.php');
        $navbar = new dcpp_plugin_navbar;
        $smtp_form = new Dcpp_smtp_config_option;

        echo $navbar->dcpp_navbar();
        echo $smtp_form->dcpp_smtp_form();
    }

    public function dcpp_mail_template_settings() {
        require_once(plugin_dir_path(__FILE__) . 'views/componant/dcpp_plugin_navbar.php');
        require_once(plugin_dir_path(__FILE__).'views/componant/dcpp_mail_template_settings_form.php');
        $navbar = new dcpp_plugin_navbar;
        $mail_template = new dcpp_mail_template_settings_form();

        echo $navbar->dcpp_navbar();
        echo $mail_template->dcpp_mail_template_form();


    }

    public function dcpp_manage_labels(){
        require_once(plugin_dir_path(__FILE__) . 'views/componant/dcpp_plugin_navbar.php');
        $navbar = new dcpp_plugin_navbar;
        $navbar->dcpp_navbar();
      
        require_once(plugin_dir_path(__FILE__) . 'views/dcpp_labels_option.php');
        $label_form = new dcpp_labels_options();
        $label_form->dcpp_labels_form();
    }

   

}
