<?php

// require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Dcpp_User_Alert_Form_Submit {


    public function __construct() {
        add_action('admin_post_dcpp_user_alert_form_submit', [$this, 'handle_dcpp_user_alert_form']);
        add_action('admin_post_nopriv_dcpp_user_alert_form_submit', [$this, 'handle_dcpp_user_alert_form']);
    }

    public function handle_dcpp_user_alert_form() {

        require_once (plugin_dir_path(__DIR__). 'mailFunction/dcpp_config_mail.php');

        global $wpdb;

        // Fetch secretKey from the database
        $table_name = $wpdb->prefix . 'dcpp_options';
        $secret_key = $wpdb->get_row("SELECT secretKey FROM $table_name LIMIT 1");
        $dcpp_recaptcha = $wpdb->get_row("SELECT recaptcha FROM $table_name LIMIT 1");

        if (!$secret_key) {
            wp_die('Options not found.');
        }

        $secret_key = $secret_key->secretKey; // Access secretKey property

        // Verify nonce
        if (!isset($_POST['dcpp_user_alert_nonce']) || !wp_verify_nonce($_POST['dcpp_user_alert_nonce'], 'dcpp_user_alert_form')) {
            wp_die('Nonce verification failed');
        }

        // Validate reCAPTCHA
        if ($dcpp_recaptcha->recaptcha == 1) {
            if (isset($_POST['g-recaptcha-response'])) {
                $recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);

                $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
                    'body' => [
                        'secret' => $secret_key,
                        'response' => $recaptcha_response,
                    ]
                ]);

                $body = json_decode(wp_remote_retrieve_body($response));

                if (!$body->success) {
                    wp_die('reCAPTCHA verification failed. Please try again.');
                }
            } else {
                wp_die('reCAPTCHA response is missing.');
            }
        }

        // Validate and sanitize form data
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null;
        $price = isset($_POST['price']) ? floatval($_POST['price']) : null;
        $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : null;
        $note = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';
        $post_id = intval($_POST['post_id']);

        $user_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null,
            'price' => isset($_POST['price']) ? floatval($_POST['price']) : null,
            'date' => isset($_POST['date']) ? sanitize_text_field($_POST['date']) : null,
            'note' => isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '',
            'post_id' => intval($_POST['post_id'])
        );

        if(is_user_logged_in()){
            $user_id =  get_current_user_id();
        }else{
            $user_id = null ;
        }

        // Process form data
        $table_name = $wpdb->prefix . 'dcpp_price_alert_notification';

        $result = $wpdb->insert($table_name, [
            'product_id' => $post_id,
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone,
            'expected_price' => $price,
            'last_wait_date' => $date,
            'note' => $note,
            'user_id' => $user_id,
        ]);

        if (false === $result) {
            $error_message = $wpdb->last_error;
            error_log("Database insert error: $error_message");
            wp_die('There was an error with your submission. Please try again.');
        }

        // Redirect or display a success message
        $redirect_url = add_query_arg('submission', 'success', wp_get_referer());
        $table_name = $wpdb->prefix . 'dcpp_mail_smtp';
        $smtp_options = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");
    
       

        $to = $email;

        // Get Site Logo And image Url
        $table_name = $wpdb->prefix . 'dcpp_mail_template';
        $mail_template = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE template_name = %s", 'subscription_replay')
        );

       require_once(plugin_dir_path(__DIR__).'mail_template/dcpp_mail_template.php');

//      Check mail template than sent thankyou mail to subscriber 
        if($mail_template){
            $subject = $mail_template->mail_subject;
            $message = new dcpp_mail_template;
            $headers = array('Content-Type: text/html; charset=UTF-8');
        }
    
        $mail_sent = wp_mail($to,$subject, $message->dcpp_tnq_mail($user_data), $headers);
    
        if ($mail_sent) {
            error_log('Message has been sent to ' . $email);
        } else {
            error_log('Message could not be sent to ' . $email);
            wp_die('Message could not be sent. Please try again.');
        }

        wp_redirect($redirect_url);
        exit;
    }

    
    public function enqueue_scripts_styles() {
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');

        // Enqueue Bootstrap JS with jQuery dependency
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', ['jquery'], null, true);
    }
}

new Dcpp_User_Alert_Form_Submit();
