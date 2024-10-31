<?php

class Dcpp_smtp_config_option{


    public function dcpp_smtp_form(){
        global $wpdb;
    
        $table_name = $wpdb->prefix . 'dcpp_mail_smtp';
        $smtp_settings = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");
    
        ?>
        <div class="wrap">
            <h3>SMTP Settings</h3>
            <hr style="width:100%">
            <div style="padding:50px">
               <div>
                    <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="dcpp_smtp_settings">
                        <?php wp_nonce_field('dcpp_smtp_nonce_action', 'dcpp_smtp_nonce_field'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    Auto Mail After Submit
                                </th>
                            </tr>
                            <tr>
                                <th scope="row">SMTP Host</th>
                                <td>
                                    <input type="text" name="smtp_host" value="<?php echo isset($smtp_settings->smtp_host) ? esc_attr($smtp_settings->smtp_host) : ''; ?>" required style="width:40%">
                                </td>
                            </tr>
                            <tr scope="row">
                                <th scope="row">Port</th>
                                <td>
                                    <input type="number" name="port" value="<?php echo isset($smtp_settings->port) ? esc_attr($smtp_settings->port) : ''; ?>" required>
                                <td>
                            </tr>
                            <tr scope="row">
                                <th scope="row">Encryption</th>
                                <td>
                                    <select name="encryption" required>
                                        <option value="none" <?php selected($smtp_settings->encryption, 'none'); ?>>None</option>
                                        <option value="ssl" <?php selected($smtp_settings->encryption, 'ssl'); ?>>SSL</option>
                                        <option value="tls" <?php selected($smtp_settings->encryption, 'tls'); ?>>TLS</option>
                                    </select>
                                </td>
                            </tr>
                            <tr scope="row">
                                <th scope="row">Username</th>
                                <td>
                                    <input type="text" name="username" value="<?php echo isset      ($smtp_settings->username) ? esc_attr($smtp_settings->username) : ''; ?>" required style="width:40%">
                                </td>
                            </tr>
                            <tr scope="row">
                                <th scope="row">Password</th>
                                <td >
                                    <input type="password" name="password" value="<?php echo isset($smtp_settings->password) ? esc_attr($smtp_settings->password) : ''; ?>" required style="width:40%">
                                </td>
                            </tr>
                            <tr scope="row">
                                <td>
                                    <?php submit_button('Save Options'); ?>
                                </td>
                            </tr>
                        </table>
                    </form> 
               </div>
            </div>
        </div>
        <?php
    }
    



}