<?php

class Dcpp_Options {
    public function __construct() {
        add_action('admin_post_dcpp_form_submit', array($this, 'handle_dcpp_form_field_options'));
    }

    public function handle_dcpp_form_field_options() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'dcpp_options';

        // Verify nonce
        if (!isset($_POST['dcpp_form_option_nonce']) || !wp_verify_nonce($_POST['dcpp_form_option_nonce'], 'dcpp_form_options')) {
            wp_die('Nonce verification failed');
        }

        // Sanitize input fields
        $position_select = isset($_POST['position_select']) ? sanitize_text_field($_POST['position_select']) : '';
        $user_restrict = isset($_POST['user_restriction']) ? 1 : 0;
        $display_product = isset($_POST['display_product_my_account_page']) ? 1 : 0;
        $phone_number_field = isset($_POST['phone_number_field']) ? 1 : 0;
        $expected_price_field = isset($_POST['expected_price_field']) ? 1 : 0;
        $expected_price_type = isset($_POST['expected_price_type']) ? sanitize_text_field($_POST['expected_price_type']) : '';
        $hide_for_stock_out_product = isset($_POST['hide_out_of_stock']) ? 1 : 0;
        $hide_product_current_price = isset($_POST['hide_current_price']) ? 1 : 0;
        $date_field = isset($_POST['date_field']) ? 1 : 0;
        $note_field = isset($_POST['note_field']) ? 1 : 0;
        $back_in_stock = isset($_POST['back_in_stock']) ? 1 : 0;
        $form_restiction = isset($_POST['form_restiction']) ? sanitize_text_field($_POST['form_restiction']) : '';
        $recaptcha = isset($_POST['disable_recaptcha']) ? 1 : 0;
        $siteKey = isset($_POST['dcpp-recap-site-key']) ? sanitize_text_field($_POST['dcpp-recap-site-key']) : '';
        $secretKey = isset($_POST['dcpp-recap-secret-key']) ? sanitize_text_field($_POST['dcpp-recap-secret-key']) : '';

        // Check if an entry already exists
        $existing_entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d LIMIT 1", 1));

        $data = array(
            'button_position' => $position_select,
            'user_restrict' => $user_restrict,
            'display_subscribe_product' => $display_product,
            'ask_phone_number' => $phone_number_field,
            'ask_expected_price' => $expected_price_field,
            'expected_price_type' => $expected_price_type,
            'hide_stock_out_product' => $hide_for_stock_out_product,
            'hide_current_price' => $hide_product_current_price,
            'ask_last_date' => $date_field,
            'ask_note' => $note_field,
            'back_in_stock' => $back_in_stock,
            'form_restiction' => $form_restiction,
            'recaptcha' => $recaptcha,
            'siteKey' => $siteKey,
            'secretKey' => $secretKey,
        );

        $format = array(
            '%s', '%d', '%d', '%d', '%d', '%s', '%d', '%d', '%d', '%d','%d', '%s', '%d', '%s', '%s'
        );

        if ($existing_entry) { 
            // Update existing entry
            $result = $wpdb->update(
                $table_name,
                $data,
                array('id' => $existing_entry->id),
                $format,
                array('%d')
            );

            if ($result !== false) {
                wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=dcpp_form_options')));
                exit;
            } else {
                wp_die('Error updating data: ' . $wpdb->last_error);
            }
        } else {
            // Insert new entry
            $result = $wpdb->insert($table_name, $data, $format);

            if ($result !== false) {
                wp_redirect(add_query_arg('settings-updated', 'true', admin_url('admin.php?page=dcpp_form_options')));
                exit;
            } else {
                wp_die('Error inserting data: ' . $wpdb->last_error);
            }
        }
    }
}