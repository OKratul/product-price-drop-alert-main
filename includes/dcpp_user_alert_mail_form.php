<?php 

class Dcpp_user_alert_mail_form {
    private $table_name_smtp;

    public function __construct() {
        global $wpdb;
        $this->table_name_smtp = $wpdb->prefix . 'dcpp_mail_smtp';

        add_action('admin_post_dcpp_smtp_settings', [$this, 'handle_mail_form_submit']);
    }

    public function handle_mail_form_submit() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dcpp_mail_smtp';
    
        // Check if the user is an admin
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    
        // Verify nonce
        if (!isset($_POST['dcpp_smtp_nonce_field']) || !wp_verify_nonce($_POST['dcpp_smtp_nonce_field'], 'dcpp_smtp_nonce_action')) {
            wp_die('Security check failed.');
        }
    
        // Sanitize and validate form data
        $smtp_host = sanitize_text_field($_POST['smtp_host']);
        $port = intval($_POST['port']);
        $encryption = sanitize_text_field($_POST['encryption']);
        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
    
        // Prepare data for insertion or update
        $data = array(
            'smtp_host' => $smtp_host,
            'port' => $port,
            'encryption' => $encryption,
            'username' => $username,
            'password' => $password,
        );
    
        $format = array('%s', '%d', '%s', '%s', '%s');
    
        // Check if settings already exist
        $existing_settings = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");
    
        if ($existing_settings) {
            // Update existing settings
            $result = $wpdb->update($table_name, $data, array('id' => $existing_settings->id), $format);
        } else {
            // Insert new settings
            $result = $wpdb->insert($table_name, $data, $format);
        }
    
        if ($result === false) {
            // Handle the error
            error_log('Error saving data: ' . $wpdb->last_error);
            wp_die('There was an error saving your settings. Please try again.');
        } else {
            // Redirect after successful submission with a success message
            $redirect_url = add_query_arg('message', 'settings_updated', admin_url('admin.php?page=dcpp_smtp_mail_options'));
            wp_redirect($redirect_url);
            exit;
        }
    }
    
    public function subscribe_tnq_mail() {
        global $wpdb;
    
        require_once(plugin_dir_path(__DIR__) . 'vendor/autoload.php');
      
        $mail = new PHPMailer(true);
    
        $table_name = $wpdb->prefix . 'dcpp_mail_smtp';
        $smtp_options = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");

       
    
        try {
            // Enable verbose debug output
            $mail->SMTPDebug = 2; // Set to 2 to show client and server messages
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer debug level $level; message: $str");
            };
    
            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtp_options->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_options->username;  // Replace with your email
            $mail->Password = $smtp_options->password;  // Replace with your password
            $mail->Port = $smtp_options->port;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          
            // Recipients
            $mail->setFrom('omarkhaiyamratul@gmail.com');
            $mail->addAddress('okratul21@gmail.com');
            $mail->addReplyTo('omarkhaiyamratul@gmail.com', 'Information');
    
            error_log('Message has been sent to ');
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            wp_die("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}

// Initialize the form handler
// new Dcpp_user_alert_mail_form();



