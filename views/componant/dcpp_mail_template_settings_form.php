<?php 

class dcpp_mail_template_settings_form {

    public function __construct() {}

    public function dcpp_mail_template_form() {
        global $wpdb;
        $table_name = $wpdb->prefix . "dcpp_mail_template";

        // Fetch data for each template
        $tnq_mail_template = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE template_name = %s LIMIT 1", 'subscription_replay'));
        $cupon_mail_template = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE template_name = %s LIMIT 1", 'coupon_mail_template'));

        require_once(ABSPATH . 'wp-load.php');
        ?>
        <div class="wrap">
            <div style="padding:50px">

                <!-- Thankyou Mail Template form Insert -->
                <form id="mailTemplateForm" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                    <input type="hidden" name="action" value="dcpp_mail_template">
                    <?php wp_nonce_field('dcpp_mail_template_nonce', 'dcpp_smtp_nonce_field'); ?>
                    <input type="hidden" name="template_name" value="subscription_replay"> 

                    <h5>Use This template for Form Submission</h5>
                    <table class="form-body" style="width:100%; margin-bottom:100px">
                        <tbody>
                            <tr>
                                <td><label for="subject">Subject:</label></td>
                                <td>
                                    <input style="width:100%;margin-bottom:20px" type="text" 
                                        value="<?php echo !empty($tnq_mail_template->mail_subject) ? esc_attr($tnq_mail_template->mail_subject) : ''; ?>" 
                                        id="subject" name="mail_subject" required>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="body">Body:</label></td>
                                <td>
                                    <h5>Edit the mail template here.</h5>
                                    <h5>Available mail-tags:</h5>
                                    <h6>{user_name}, {user_phone}, {user_expected_price}, {user_note}, {product_name}, {product_price}, {product_page_url}, {product_sku}</h6>
                                    <?php
                                    $content = isset($tnq_mail_template->mail_body) ? $tnq_mail_template->mail_body : '';
                                    $editor_id = 'mail_body';
                                    wp_editor($content, $editor_id, [
                                        'textarea_name' => 'mail_body',
                                        'media_buttons' => true,
                                        'textarea_rows' => 10,
                                        'teeny' => true,
                                        'quicktags' => true,
                                    ]);
                                    ?>
                                </td>
                            </tr>                      
                            <tr>
                                <td colspan="2"><?php submit_button('Save Template'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </form>

                <!-- Coupon Mail Template Insert Form -->
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                    <input type="hidden" name="action" value="dcpp_mail_template">
                    <?php wp_nonce_field('dcpp_mail_template_nonce', 'dcpp_smtp_nonce_field'); ?>
                    <input type="hidden" name="template_name" value="coupon_mail_template"> 

                    <h5>Use this template for Coupon Mail</h5>
                    <table class="form-body" style="width:100%">
                        <tbody>
                            <tr>
                                <td><label for="subject_coupon">Subject:</label></td>
                                <td>
                                    <input style="width:100%;margin-bottom:20px" type="text" 
                                        value="<?php echo !empty($cupon_mail_template->mail_subject) ? esc_attr($cupon_mail_template->mail_subject) : ''; ?>" 
                                        id="subject_coupon" name="mail_subject" required>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="body_coupon">Body:</label></td>
                                <td>
                                    <h5>Edit the mail template here.</h5>
                                    <h5>Available mail-tags:</h5>
                                    <h6>{user_name}, {user_phone}, {user_expected_price}, {product_name}, {product_price}, {product_page_url}, {product_sku}, {coupon_code}, {expire_date}, {coupon_amount}, </h6>
                                    <?php
                                    $content = isset($cupon_mail_template->mail_body) ? $cupon_mail_template->mail_body : '';
                                    $editor_id = 'mail_body_coupon';
                                    wp_editor($content, $editor_id, [
                                        'textarea_name' => 'mail_body',
                                        'media_buttons' => true,
                                        'textarea_rows' => 10,
                                        'teeny' => true,
                                        'quicktags' => true,
                                    ]);
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php submit_button('Save Template'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <?php
    }
}
