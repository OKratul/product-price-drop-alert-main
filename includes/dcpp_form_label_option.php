<?php

class Dcpp_form_label_option {

    public function __construct() {
        add_action('admin_post_save_form_label_data', [$this, 'save_form_label_data']);
    }

    public function save_form_label_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dcpp_form_labels';

        // Check if the form has been submitted and nonce is set
        if (!isset($_POST['form_label_data_nonce']) || !wp_verify_nonce($_POST['form_label_data_nonce'], 'save_form_labels_data')) {
            wp_die('Nonce verification failed');
        }

        // Sanitize and retrieve form data
        $button_color = isset($_POST['button_color']) ? sanitize_hex_color($_POST['button_color']) : '';
        $button_height = isset($_POST['button_height']) ? intval($_POST['button_height']) : 0;
        $button_width = isset($_POST['button_width']) ? intval($_POST['button_width']) : 0;
        $button_label = isset($_POST['button_label']) ? sanitize_text_field($_POST['button_label']) : '';
        $subscribe_form_title = isset($_POST['subscribe_form_title']) ? sanitize_text_field($_POST['subscribe_form_title']) : '';
        $first_name_label = isset($_POST['first_name_label']) ? sanitize_text_field($_POST['first_name_label']) : '';
        $name_place_holder_label = isset($_POST['name_place_holder_label']) ? sanitize_text_field($_POST['name_place_holder_label']) : '';
        $email_label = isset($_POST['email_label']) ? sanitize_text_field($_POST['email_label']) : '';
        $email_placeholder_label = isset($_POST['email_placeholder_label']) ? sanitize_text_field($_POST['email_placeholder_label']) : '';
        $ex_discount_label = isset($_POST['ex_discount_label']) ? sanitize_text_field($_POST['ex_discount_label']) : '';
        $note_label = isset($_POST['note_label']) ? sanitize_text_field($_POST['note_label']) : '';
        $note_placeholder_label = isset($_POST['note_placeholder_label']) ? sanitize_text_field($_POST['note_placeholder_label']) : '';
        $custom_css = isset($_POST['custom_css']) ? wp_strip_all_tags($_POST['custom_css']) : '';
        $ex_discount_placeholder = isset($_POST['ex_discount_placeholder']) ? sanitize_text_field($_POST['ex_discount_placeholder']) : '';
        $required_check_discount = isset($_POST['required_check_discount']) ? filter_var($_POST['required_check_discount'], FILTER_VALIDATE_BOOLEAN) : 0;
        $required_check_note = isset($_POST['required_check_note']) ? filter_var($_POST['required_check_note'], FILTER_VALIDATE_BOOLEAN) : 0;
        $css_class = isset($_POST['css_class']) ? sanitize_text_field($_POST['css_class']) : '';
        $css_id = isset($_POST['css_id']) ? sanitize_text_field($_POST['css_id']) : '';

        // Retrieve the data to check if it exists (based on unique ID or other criteria)
        $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d LIMIT 1", 1)); // Assuming ID = 1 for now

        if ($data) {
            // Update the existing data if it exists
            $updated = $wpdb->update(
                $table_name,
                array(
                    'button_color' => $button_color,
                    'button_size_width' => $button_width,
                    'button_size_height' => $button_height,
                    'button_label' => $button_label,
                    'form_title' => $subscribe_form_title,
                    'name_label' => $first_name_label,
                    'name_placeholder_label' => $name_place_holder_label,
                    'email_address_label' => $email_label,
                    'email_address_placeholder' => $email_placeholder_label,
                    'expected_discount_label' => $ex_discount_label,
                    'ex_discount_placeholder' => $ex_discount_placeholder,
                    'additional_note_label' => $note_label,
                    'additional_note_placeholder' => $note_placeholder_label,
                    'required_check_price' => $required_check_discount,
                    'required_check_note' => $required_check_note,
                    'css_class' => $css_class,
                    'css_id' => $css_id,
                    'custom_css' => $custom_css,
                ),
                array('id' => $data->id) // Update based on ID
            );

            if (false === $updated) {
                wp_die('Error updating form data: ' . $wpdb->last_error);
            } else {
                echo 'Data updated successfully';
            }

        } else {
            // Insert the new data if no data exists
            $inserted = $wpdb->insert(
                $table_name,
                array(
                    'button_color' => $button_color,
                    'button_size_width' => $button_width,
                    'button_size_height' => $button_height,
                    'button_label' => $button_label,
                    'form_title' => $subscribe_form_title,
                    'name_label' => $first_name_label,
                    'name_placeholder_label' => $name_place_holder_label,
                    'email_address_label' => $email_label,
                    'email_address_placeholder' => $email_placeholder_label,
                    'expected_discount_label' => $ex_discount_label,
                    'ex_discount_placeholder' => $ex_discount_placeholder,
                    'additional_note_label' => $note_label,
                    'additional_note_placeholder' => $note_placeholder_label,
                    'required_check_price' => $required_check_discount,
                    'required_check_note' => $required_check_note,
                    'css_class' => $css_class,
                    'css_id' => $css_id,
                    'custom_css' => $custom_css,
                )
            );

            if (false === $inserted) {
                wp_die('Error inserting form data: ' . $wpdb->last_error);
            } else {
                echo 'Data inserted successfully';
            }
        }

        // Redirect back to the form page with a success message
        $redirect_url = add_query_arg('success', 'true', wp_get_referer());
        wp_safe_redirect($redirect_url);
        exit; // Always call exit after redirect to stop further execution
    }
}

new Dcpp_form_label_option();
