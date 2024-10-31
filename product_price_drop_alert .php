<?php
/**
 * Plugin Name:       Product Price Drop Alert
 * Plugin URI:        https://nomoremrniceguy.xyz
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            Omar Khaiyam
 * Author URI:        https://nomoremrniceguy.xyz/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product_price_drop_alert
 * Domain Path:       /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define your plugin class
class Dcpp_Product_Price_Drop_Alert {

    public function __construct() {
        // Load necessary files and classes
        $this->load_includes();

        // Register hooks for initialization
        add_action('plugins_loaded', array($this, 'initialize'));

        // Admin Dashboard Style and Scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'),1);

        // Frontend Styles and Scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
        add_action('wp_enqueue_scripts', array($this, 'dcpp_recaptcha_script'), 30);
        add_action('plugin_loaded', array($this,'dcpp_load_text_domain'));

        // Instantiate necessary classes
        $this->initialize_classes();
        add_action('woocommerce_before_single_product', array($this, 'add_notification_button_position'));
    }

    public function dcpp_load_text_domain() {
        load_plugin_textdomain('product_price_drop_alert', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    private function load_includes() {
        require_once(plugin_dir_path(__FILE__) . 'database/dcpp_database.php');
        require_once(plugin_dir_path(__FILE__) . 'dcpp_menu_option.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_user_alert_form_submit.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_options.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_user_alert_mail_form.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_mial_template_form_handel.php');
        require_once(plugin_dir_path(__FILE__) . 'database/dcpp_database_seeder.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_add_checkbox_woocommerce.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_add_checkbox_woocommerce_category.php');
        require_once(plugin_dir_path(__FILE__) . 'options/dcpp_options_handler.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_form_label_option.php');
        require_once(plugin_dir_path(__FILE__) . 'includes/dcpp_handle_manage_option.php');

    }

    private function initialize_classes() {
        new Dcpp_Menu_Option();
        new Dcpp_User_Alert_Form_Submit();
        new Dcpp_Options();
        new Dcpp_user_alert_mail_form();
        new Dcpp_mial_template_form_handel();
        new Dcpp_handle_manage_option();
       

        global $wpdb;
        $option_table = $wpdb->prefix . 'dcpp_options';
        $option = $wpdb->get_row("SELECT * FROM $option_table LIMIT 1");

        $check_box = new Dcpp_add_checkbox_woocommerce();

        if ($option) {
            if ($option->form_restiction == 'specific_product') {
                $check_box->check_box_product_selection();
            } elseif ($option->form_restiction == 'specific_category') {
                $check_box->check_box_category_selection();
            }
        }

        $seeder = new Dcpp_database_seeder;
        $seeder->option_data_seed();
       
    }

    public function initialize() {
        require_once(plugin_dir_path(__FILE__) . 'options/dcpp_options_handler.php');
        global $wpdb;
        $table = $wpdb->prefix . 'dcpp_options';
        $options = $wpdb->get_row("SELECT * FROM $table LIMIT 1");

        if ($options && $options->display_subscribe_product == 1) {
            new Dcpp_Options_Handler();
        }
    }

    public function enqueue_scripts_styles() {
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', [], '4.3.1');
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js', array('jquery'), '4.3.1', true);
        wp_enqueue_script('my-plugin-script', plugins_url('/js/script.js', __FILE__), array('jquery'), null, true);

    }

    public function dcpp_recaptcha_script() {
        wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, true);
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_style('my-plugin-style', plugins_url('/admin/css/dcpp_form_style.css', __FILE__));
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', [], '4.3.1');
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js', array('jquery'), '4.3.1', true);
  
     
        wp_enqueue_script(
            'dcpp_custom_script', 
            plugin_dir_url(__FILE__) . 'asset/dcpp_price_drop_notification_data.js',
            array('jquery'), // Ensure it loads after jQuery, add more dependencies if needed
            null, // Version of the script, null for no version
            true // Load script in footer
        );
    }

    public function add_notification_button_position() {
        global $wpdb;
        $table = $wpdb->prefix . 'dcpp_options';
        $options = $wpdb->get_row("SELECT * FROM $table LIMIT 1");
    
        $button_position = $options->button_position;

        global $post;
            
        $product_id = $post->ID;
        $form_restriction_value = get_post_meta($product_id, 'form_restiction', true);
    
        if (is_product()) {
            $product_id = get_the_ID(); // Get the current product ID
            $product = wc_get_product($product_id); // Get the WC_Product object
            $categories = wp_get_post_terms($product_id,'product_cat');

            if (!$product) {
                error_log('Error: Unable to retrieve WC_Product object.');
                return;
            }
    
            $is_in_stock = $product->is_in_stock(); // Check if the product is in stock
    
            if($options->button_position !='short_code'){
                if( $options->form_restiction == 'specific_product' && $form_restriction_value == 'yes'){
                    if ($options->user_restrict == 1) {
                        if ($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)) {
                            // Add the button if the product is in stock or if hiding stock-out products is not enabled
                            add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        } else {
                            // Remove the button if the product is out of stock and hiding stock-out products is enabled
                            remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        }
                    } elseif ($options->user_restrict == 0 && is_user_logged_in()) {
                        if($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)){
                            add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        }else{
                            remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        }
                    }
                }elseif($options->form_restiction == 'specific_category'){
                
                    foreach ($categories as $category) {
                        $term_meta = get_term_meta($category->term_id, 'allow_subscription_category', true);
                        if ($term_meta === '1') {
                            if ($options->user_restrict == 1) {
                                if ($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)) {
                                    // Add the button if the product is in stock or if hiding stock-out products is not enabled
                                    add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                                } else {
                                    // Remove the button if the product is out of stock and hiding stock-out products is enabled
                                    remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                                }
                            } elseif ($options->user_restrict == 0 && is_user_logged_in()) {
                                if($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)){
                                    add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                                }else{
                                    remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                                }
                            }
                        }
                    }
                }elseif($options->form_restiction == 'for_all_product'){

                    if ($options->user_restrict == 1) {
                        if ($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)) {
                            // Add the button if the product is in stock or if hiding stock-out products is not enabled
                            add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        } else {
                            // Remove the button if the product is out of stock and hiding stock-out products is enabled
                            remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        }
                    } elseif ($options->user_restrict == 0 && is_user_logged_in()) {
                        if($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)){
                            add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        }else{
                            remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
                        }
                    }

                }
            }
        }
    }
    

    public function dcpp_display_modal_button() {
        if (is_product()) {
            echo do_shortcode('[dcpp_customer_form]');
        }
    }
}

// Instantiate the main plugin class
new Dcpp_Product_Price_Drop_Alert();

// Register activation hook
register_activation_hook(__FILE__, array('Dcpp_Database', 'dcpp_create_database_table'));

