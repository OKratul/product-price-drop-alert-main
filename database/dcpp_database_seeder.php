<?php

class Dcpp_database_seeder {

    public static function option_data_seed() {
        global $wpdb;
    
        $table_name_options = $wpdb->prefix . 'dcpp_options';
        $table_name_smtp = $wpdb->prefix . 'dcpp_mail_smtp';
        $table_mail_template = $wpdb->prefix . 'dcpp_mail_template';
        $table_form_labels = $wpdb->prefix . 'dcpp_form_labels';
    
        // Seed options table
        self::seed_table($table_name_options, [
            'button_position' => 'after_cart_button',
            'user_restrict' => 1,
            'display_subscribe_product' => 1,
            'ask_phone_number' => 0,
            'ask_expected_price' => 0,
            'expected_price_type' => 'value',
            'hide_stock_out_product' => 0,
            'hide_current_price' => 1,
            'ask_last_date' => 0,
            'ask_note' => 0,
            'back_in_stock' => 0,
            'form_restiction' => 'for_all_product',
            'recaptcha' => 0,
            'siteKey' => null,
            'secretKey' => null
        ]);
    
        // Seed SMTP table
        self::seed_table($table_name_smtp, [
            'smtp_host' => null,
            'port' => 25, // Default port value
            'encryption' => 'none',
            'username' => null,
            'password' => null,
        ]);
    
        $template_seeder_data = [
            [
                'template_name' => 'subscription_replay',
                'mail_subject' => 'Thank You For Subscription',
                'mail_body' => 'Dear {user_name}, Thank you for subscribing...',
                'created_at' => current_time('mysql'),
            ],
            [
                'template_name' => 'coupon_mail_template',
                'mail_subject' => 'Coupon Code',
                'mail_body' => 'Dear {user_name}, Thank you for subscribing...',
                'created_at' => current_time('mysql'),
            ]
        ];
    
        // Seed mail template table
        $template_seeder_data = [
            [
                'template_name' => 'subscription_replay',
                'mail_subject' => 'Thank You For Subscription',
                'mail_body' => 'Dear {user_name}, Thank you for subscribing...',
                'created_at' => current_time('mysql'),
            ],
            [
                'template_name' => 'coupon_mail_template',
                'mail_subject' => 'Coupon Code',
                'mail_body' => 'Dear {user_name}, Thank you for subscribing...',
                'created_at' => current_time('mysql'),
            ]
        ];
        
      
        $wpdb->insert($table_mail_template, $template_seeder_data);
       
        // Seed form labels table
        self::seed_table($table_form_labels, [
            'button_color' => '#000000',
            'button_size_width' => '100px',
            'button_size_height' => '40px',
            'button_label' => 'Subscribe',
            'form_title' => 'Subscription Form',
            'name_label' => 'Name',
            'name_placeholder_label' => 'Write Your Full Name',
            'email_address_label' => 'Email',
            'email_address_placeholder' => 'Subscribe with your email...',
            'expected_discount_label' => 'Expected Discount',
            'ex_discount_placeholder' => 'Expected Discount',
            'required_check_price' => 0,
            'additional_note_label' => 'Note',
            'additional_note_placeholder' => 'Note',
            'required_check_note' => 0,
            'custom_css' => null,
        ]);
    }
    

    private static function seed_table($table_name, $data) {
        global $wpdb;

        // Check if the table exists and insert data if needed
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            $row_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            if ($row_count == 0) {
                $wpdb->insert($table_name, $data);

                if ($wpdb->last_error) {
                    error_log('Error inserting initial data into ' . $table_name . ': ' . $wpdb->last_error);
                }
            }
        } else {
            error_log('Table ' . $table_name . ' does not exist.');
        }
    }
}
