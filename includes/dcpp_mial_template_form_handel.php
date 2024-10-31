<?php

class Dcpp_mial_template_form_handel{

    public function __construct(){
        add_action('admin_post_dcpp_mail_template',[$this,'handle_mail_template_submit']);
    }


    public function handle_mail_template_submit() {
        // Verify nonce for security
        if (!isset($_POST['dcpp_smtp_nonce_field']) || !wp_verify_nonce($_POST['dcpp_smtp_nonce_field'], 'dcpp_mail_template_nonce')) {
            die('Permission check failed.');
        }
    
        global $wpdb;
        $table_mail_template = $wpdb->prefix . 'dcpp_mail_template';
    
        // Sanitize inputs
        $subject = sanitize_text_field($_POST['mail_subject']);
        $body = wp_kses_post($_POST['mail_body']); // Allow HTML content
        $template_name = sanitize_text_field($_POST['template_name']);
        

        // Check if a mail template already exists
        $tnq_template = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_mail_template WHERE template_name = %s LIMIT 1", 'subscription_replay'));
    
        // Prepare data for insertion or update
        $data = array(
            'template_name'=> $template_name,
            'mail_subject' => $subject,
            'mail_body' => $body,
            'updated_at' => current_time('mysql')
        );

        
    
        if ( $template_name == 'subscription_replay' ) {
            // Update existing template
            $wpdb->update($table_mail_template, $data, array('template_name' => 'subscription_replay'), array('%s', '%s', '%s'), array('%d'));
        } else {
            // Insert new template
            $data['created_at'] = current_time('mysql');
            $wpdb->insert($table_mail_template, $data, array('%s', '%s', '%s', '%s'));
        }


        $coupon_template = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_mail_template WHERE template_name = %s LIMIT 1", 'coupon_mail_template'));
    
        if ( $template_name === 'coupon_mail_template' && !empty($coupon_template)) {
            // Update existing "coupon_mail_template" template
            $wpdb->update($table_mail_template, $data, array('template_name' => 'coupon_mail_template'), array('%s', '%s', '%s'), array('%s'));
        } else {
            // Insert new "coupon_mail_template" template
            $data['created_at'] = current_time('mysql');
            $wpdb->insert($table_mail_template, $data, array('%s', '%s', '%s', '%s'));
        }
        // Redirect with success message
        wp_redirect(admin_url('admin.php?page=dcpp_smtp_mail_template_options&status=success'));
        exit; // Always call exit after wp_redirect
    }
    

}