<?php 

class dcpp_config_mail{

    public function __construct(){

        add_action('phpmailer_init', [$this,'mail_config']);

    }

    public function mail_config(){


        global $phpmailer;
        global $wpdb;
        $table_name = $wpdb->prefix . 'dcpp_mail_smtp';
        $mail_config = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1",'ARRAY_A',0);

        // echo '<pre>';
        // print_r($mail_config['host']);
        // echo '</pre>';

        // die();

        $phpmailer->isSMTP();
        $phpmailer->Host = $mail_config['smtp_host'];
        $phpmailer->Port = $mail_config['port'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $mail_config['username'];
        $phpmailer->Password = $mail_config['password'];
        $phpmailer->SMTPSecure = $mail_config['encryption'];


    }

}

new dcpp_config_mail();