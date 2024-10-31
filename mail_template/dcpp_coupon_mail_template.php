<?php 

 class Dcpp_coupon_mail_template{


    
    public function dcpp_coupon_mail($user_data) {

        global $wpdb;

        // Fetch mail template from database
        $table_name = $wpdb->prefix . 'dcpp_mail_template';
        $coupon_template = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE template_name = %s", 'coupon_mail_template'));

        if (!$coupon_template) {
            return 'Mail template not found.';
        }

        // Get logo URL
        $custom_logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_src($custom_logo_id, 'full');
        $logo_url = $logo_url ? $logo_url[0] : '';

        // Ensure product data exists
        $product_id = $user_data['product_id'];
        $product = wc_get_product($product_id);


        // Assign variables from $user_data and $product
        $user_name = sanitize_text_field($user_data['name']);
        $user_phone = sanitize_text_field($user_data['phone_number']);
        $expected_price = floatval($user_data['expected_price']);
        $product_name = $product->get_name();
        $product_price = $product->get_price();
        $product_sku = $product->get_sku();
        $product_url = get_permalink($product_id);
        $coupon_code = $user_data['coupon_code'];
        $coupon_amount = $user_data['coupon_amount'];
        $expire_date = $user_data['expire_date'];
        $discount_type = $user_data['discount_type'];
        

        // Prepare placeholders and their replacements
        $placeholders = [
            '{user_name}',
            '{user_phone}',
            '{user_expected_price}',
            '{product_name}',
            '{product_price}',
            '{product_page_url}',
            '{product_sku}',
            '{site_logo}',
            '{coupon_code}',
            '{coupon_amount}',
            '{expire_date}',
        ];

        $replacements = [
            $user_name,
            $user_phone,
            $expected_price,
            $product_name,
            $product_price,
            $product_url,
            $product_sku,
            $logo_url,
            $coupon_code,
            $coupon_amount,
            $expire_date,
            $discount_type,
        ];

        // Replace placeholders with actual data in the email template body
        $email_body = str_replace($placeholders, $replacements, $coupon_template->mail_body);

        // Start capturing output
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 15px;
                }
                .btn {
                    display: inline-block;
                    font-weight: 400;
                    text-align: center;
                    white-space: nowrap;
                    vertical-align: middle;
                    user-select: none;
                    background-color: #007bff;
                    border: 1px solid #007bff;
                    padding: 0.375rem 0.75rem;
                    font-size: 1rem;
                    line-height: 1.5;
                    border-radius: 0.25rem;
                    color: #fff;
                    text-decoration: none;
                }
                .btn:hover {
                    background-color: #0056b3;
                    border-color: #004085;
                }
            </style>
        </head>
        <body>
            <div class="container">
              <?php echo $email_body; // Output the replaced email body ?>
            </div>
        </body>
        </html>
        <?php
        // End capturing output and return it
        return ob_get_clean();
    }

 }



