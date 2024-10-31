<?php 


class Dcpp_handle_manage_option {

    public function __construct() {
        add_action("admin_post_dcpp_manage_option", array($this, "dcpp_handle_manage_option"));
    }

    public function dcpp_handle_manage_option() {

        // Nonce check
        if (!isset($_POST['dcpp_manage_option_nonce']) || !wp_verify_nonce($_POST['dcpp_manage_option_nonce'], 'dcpp_manage_option_action')) {
            wp_die('Invalid request.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'dcpp_manage_options';

        // Sanitize form data
        $notification_id = isset( $_POST['notification_id'] ) ? sanitize_text_field($_POST['notification_id'] ) : '';
        $product_id = isset( $_POST['product_id'] ) ? sanitize_text_field($_POST['product_id'] ) : '' ; // Fixed typo here
        $manage_option = isset( $_POST['manage_option'] ) ? sanitize_text_field($_POST['manage_option'] ) : '';
        $discount_type = isset( $_POST['discount_type'] ) ? sanitize_text_field($_POST['discount_type'] ) : '';
        $coupon_amount = isset( $_POST['coupon_amount'] ) ? sanitize_text_field($_POST['coupon_amount'] ) : '';
        $expiry_date = isset( $_POST['expiry_date'] ) ? sanitize_text_field($_POST['expiry_date'] ) : '';
        $allow_free_shipping = isset( $_POST['allow_free_shipping'] ) ? 1 : 0;
        $allow_specific_email = isset( $_POST['allow_specific_email'] ) ? 1 : 0;

        $notification_data_table = $wpdb->prefix . 'dcpp_price_alert_notification';

        // Fetch notification data with prepared statement (missing placeholder fixed)
        $notification_data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $notification_data_table WHERE id = %d",
                $notification_id
            )
        );

        // Check if data exists before accessing
        if (empty($notification_data)) {
            wp_die('No notification data found.');
        }

        $user_name = $notification_data[0]->name;
        $user_email = $notification_data[0]->email;
        $product = wc_get_product($product_id);

        // Generate coupon code
        $coupon_code = substr($user_name ? $user_name : $user_email, 0, 3) . '-' . substr($product->get_name(), 0, 3) . '-' . $coupon_amount . '_'. rand(100, 9999);

        // Manage the option
        if ($manage_option == "Send Email With Discount Coupon Code") {

            $notificationTable = $wpdb->prefix ."dcpp_price_alert_notification";
            $wpdb->insert($table_name, array(
                'notification_id' => $notification_id,
                'product_id' => $product_id,
                'discount_amount' => $coupon_amount,
                'expire_date' => $expiry_date,
                'coupon_code' => $coupon_code,
                'status' => $manage_option,
            ));

            $wpdb->update($notificationTable,array( 'status' => $manage_option ), array('id' => $notification_id)) ;

            $this->make_coupon_code($user_email,$coupon_code,$discount_type,$coupon_amount, $expiry_date,$product_id);

            $this->sent_coupon_mail($notification_id,$coupon_code,$discount_type, $coupon_amount,$expiry_date, $product_id);


        } else if ( $manage_option == "Send Email When I Drop Price" || $manage_option == "Quarantine" ) {

            $notificationTable = $wpdb->prefix ."dcpp_price_alert_notification";

            $data = array('status' =>  $manage_option);
            $where = array('id' => $notification_id) ;
            // Insert or update status only
            $wpdb->update($notificationTable,$data, $where) ;


        }else if ( $manage_option == "Remove" ) {

            $wpdb->insert($table_name, array(
                'status' => $manage_option,
            ), array('notification_id' => $notification_id));

            $deleteTable =$wpdb->prefix. "dcpp_price_alert_notification";

            $where = array('id'=> $notification_id );

            $wpdb->delete($deleteTable,$where);

        }else{
            wp_die('Invalid manage option or discount type provided.');
        }

        // Redirect and exit to avoid further execution
        wp_redirect(admin_url('admin.php?page=dcpp_price_alert_notifications&status=success'));

        exit;
    }


    public function make_coupon_code($user_email, $coupon_code, $discount_type, $coupon_amount, $expiry_date, $product_id) {

        // Create a new WooCommerce coupon object
        $coupon = new WC_Coupon();
    
        // Set coupon code and properties
        $coupon->set_code($coupon_code); // Coupon code
        $coupon->set_amount($coupon_amount); // Discount amount
        $coupon->set_discount_type($discount_type); // Discount type (e.g. 'percent', 'fixed_cart', 'fixed_product')
        $coupon->set_date_expires($expiry_date); // Expiry date for the coupon
    
        // Restrict coupon to a specific email address
        $coupon->set_email_restrictions(array($user_email)); // Only this email can use the coupon
    
        // Restrict the coupon to a specific product
        $coupon->set_product_ids(array($product_id));
    
        // Optional: Set other properties
        $coupon->set_usage_limit(1); // Limit coupon usage to 1
        $coupon->set_individual_use(true); // Ensure this coupon cannot be used with other coupons
    
        // Save the coupon
        $coupon->save();
    
        return $coupon->get_code(); // Return the generated coupon code
    }
    
    
    private function sent_coupon_mail($notification_id, $coupon_code, $discount_type, $coupon_amount, $expiry_date, $product_id) {


        global $wpdb;
    
        $notification_data_table = $wpdb->prefix . 'dcpp_price_alert_notification';
    
        // Fetch notification data with a prepared statement
        $notification_data = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $notification_data_table WHERE id = %d",
                $notification_id
            )
        );
    
        // Ensure notification data is found
        if (empty($notification_data)) {
            wp_die('No notification data found.');
        }
    
        $user_data = array(
            'name' => $notification_data[0]->name,
            'email' => $notification_data[0]->email,
            'phone_number' => $notification_data[0]->phone_number,
            'expected_price' => $notification_data[0]->expected_price,
            'expire_date' => $expiry_date,
            'product_id' => $product_id,
            'coupon_code' => $coupon_code,
            'discount_type' => $discount_type,
            'coupon_amount' => $coupon_amount,
        );
    
        $table_name = $wpdb->prefix . 'dcpp_mail_template';
        $mail_template = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE template_name = %s", 'coupon_mail_template')
        );
    
        // Get recipient email and default subject
        $to = $user_data['email'];
        $subject = $mail_template ? $mail_template->mail_subject : 'Your Discount Coupon Code';
    
        // Ensure mail template exists before sending
        if ($mail_template) {
            require_once(plugin_dir_path(__DIR__) . 'mail_template/dcpp_coupon_mail_template.php');
            require_once (plugin_dir_path(__DIR__). 'mailFunction/dcpp_config_mail.php');

            $message_template = new Dcpp_coupon_mail_template;  

            // Prepare the email message content
            $message = $message_template->dcpp_coupon_mail($user_data);
            $headers = array('Content-Type: text/html; charset=UTF-8');
    
            // Send the email
            $mail_sent = wp_mail($to, $subject, $message, $headers);
    
            if ($mail_sent) {
                error_log('Message has been sent to ' . $to);
            } else {
                error_log('Message could not be sent to ' . $to);
                wp_die('Message could not be sent. Please try again.');
            }
    
        } else {
            wp_die('Mail template not found.');
        }
    }
    



}
