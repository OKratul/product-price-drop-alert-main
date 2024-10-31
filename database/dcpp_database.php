<?php

class dcpp_database {

    public static function dcpp_create_database_table() {

        global $wpdb;

        $table_name = $wpdb->prefix . 'dcpp_price_alert_notification';
        $table_name_options = $wpdb->prefix . 'dcpp_options';
        $table_name_smtp = $wpdb->prefix . 'dcpp_mail_smtp';
        $table_name_manage_option = $wpdb->prefix . 'dcpp_manage_options';
        $table_mail_template = $wpdb->prefix . 'dcpp_mail_template';
        $table_name_labels = $wpdb->prefix . 'dcpp_form_labels';

        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id BIGINT(20) UNSIGNED NOT NULL,
                user_id BIGINT(20) UNSIGNED NULL,
                name TEXT NULL,
                email VARCHAR(255) NOT NULL,
                phone_number VARCHAR(20) NULL,
                expected_price DECIMAL(10, 2) NULL,
                last_wait_date DATE NULL,
                note TEXT NULL,
                status TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES {$wpdb->prefix}posts(ID)
            ) $charset_collate;";

            $sql_options = "CREATE TABLE $table_name_options (
                id INT AUTO_INCREMENT PRIMARY KEY,
                button_position VARCHAR(50) NOT NULL,
                user_restrict BOOLEAN NOT NULL DEFAULT 0,
                display_subscribe_product BOOLEAN NOT NULL DEFAULT 0,
                ask_phone_number BOOLEAN NOT NULL DEFAULT 0,
                ask_expected_price BOOLEAN NOT NULL DEFAULT 0,
                expected_price_type TEXT NULL,
                hide_stock_out_product BOOLEAN NOT NULL DEFAULT 0,
                hide_current_price BOOLEAN NOT NULL DEFAULT 0,
                ask_last_date BOOLEAN NOT NULL DEFAULT 0,
                ask_note BOOLEAN NOT NULL DEFAULT 0,
                back_in_stock BOOLEAN NOT NULL DEFAULT 0,
                form_restiction TEXT NOT NULL,
                recaptcha BOOLEAN NOT NULL DEFAULT 0,
                siteKey TEXT NULL,
                secretKey TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) $charset_collate;";

            $sql_smtp_mail = "CREATE TABLE $table_name_smtp (
                id INT AUTO_INCREMENT PRIMARY KEY,
                smtp_host VARCHAR(255) NULL,
                port INT NOT NULL,
                encryption VARCHAR(50) NULL,
                username VARCHAR(255) NULL,
                password VARCHAR(255) NULL
            ) $charset_collate;"; // Added charset_collate

            $sql_mail_template = "CREATE TABLE $table_mail_template (
                id INT AUTO_INCREMENT PRIMARY KEY,
                template_name TEXT NOT NULL,
                mail_subject VARCHAR(255) NOT NULL,
                mail_body TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME ON UPDATE CURRENT_TIMESTAMP
            ) $charset_collate;"; // Added charset_collate

            $sql_form_labels = "CREATE TABLE $table_name_labels (
                id INT AUTO_INCREMENT PRIMARY KEY,
                button_color VARCHAR(20),
                button_size_width VARCHAR(10),
                button_size_height VARCHAR(10),
                button_label TEXT,
                form_title TEXT,
                name_label TEXT,
                name_placeholder_label TEXT,
                email_address_label TEXT,
                email_address_placeholder TEXT,
                expected_discount_label TEXT,
                ex_discount_placeholder TEXT,
                required_check_price BOOLEAN NOT NULL DEFAULT 0,
                additional_note_label TEXT,
                additional_note_placeholder TEXT,
                required_check_note BOOLEAN NOT NULL DEFAULT 0,
                css_class TEXT NULL,
                css_id TEXT NULL,
                custom_css TEXT
            ) $charset_collate;";


            $sql_magae_option = "CREATE TABLE $table_name_manage_option (
                id INT AUTO_INCREMENT PRIMARY KEY,
                notification_id INT,
                product_id INT,
                discount_amount VARCHAR(100),
                coupon_code TEXT,
                status TEXT,
                expire_date DATETIME DEFAULT CURRENT_TIMESTAMP
            ) $charset_collate;"; // Added charset_collate

            // Run all SQL queries
            dbDelta($sql);
            dbDelta($sql_options);
            dbDelta($sql_smtp_mail);
            dbDelta($sql_mail_template);
            dbDelta($sql_form_labels);
            dbDelta($sql_magae_option);

        } else {
            // Debug: Uncomment this line only for debugging
            // error_log('Table ' . $table_name . ' already exists.');
        }
    }
}
