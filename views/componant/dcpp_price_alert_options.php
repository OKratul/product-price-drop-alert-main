<?php

class Dcpp_price_alert_options {

    public function dcpp_button_position_option() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'dcpp_options';

        // Fetch the first row
        $option = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");

        // Display success message if settings were updated
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>Settings updated successfully!</p>
            </div>
            <?php
        }

        ?>
        <div class="wrap">
            <h2>General Options</h2>
            <form action="<?php echo admin_url('admin-post.php'); ?>" method="POST">
                <input type="hidden" name="action" value="dcpp_form_submit">

                <?php wp_nonce_field('dcpp_form_options', 'dcpp_form_option_nonce'); ?>

                <table class="form-table">
                    <!-- User Restriction Option -->
                    <tr>
                        <th scope="row">
                            <label for="user_allow">Display Price Drop Notifier For Guest Users</label>
                        </th>
                        <td>
                            <input id="user_allow" name="user_restriction" type="checkbox" value="1" <?php checked($option->user_restrict, 1); ?>>
                        </td>
                    </tr>

                    <!-- Display in My Account Page Option -->
                    <tr>
                        <th scope="row">
                            <label for="display_user_account_page">Display Subscribed Product In Logged In My Account Page</label>
                        </th>
                        <td>
                            <input id="display_user_account_page" type="checkbox" name="display_product_my_account_page" value="1" <?php checked($option->display_subscribe_product, 1); ?>>
                        </td>
                    </tr>

                    <!-- Phone Number Option -->
                    <tr>
                        <th scope="row">
                            <label for="phone_number_field">Ask Phone Number</label>
                        </th>
                        <td>
                            <input id="phone_number_field" name="phone_number_field" type="checkbox" value="1" <?php checked($option->ask_phone_number, 1); ?>>
                        </td>
                    </tr>

                    <!-- Expected Price Option -->
                    <tr>
                        <th scope="row">
                            <label for="expected_price_field">Ask Expected Price</label>
                        </th>
                        <td>
                            <input id="expected_price_field" name="expected_price_field" type="checkbox" value="1" <?php checked($option->ask_expected_price, 1); ?>>
                        </td>
                    </tr>

                    <!-- Expected Price Type Option -->
                    <tr>
                        <th scope="row">
                            <label for="expacted_price_type">Expected Price Type</label>
                        </th>
                        <td>
                            <select id="expacted_price_type" name="expected_price_type">
                                <option value="value" <?php selected($option->expected_price_type, 'value'); ?>>Value</option>
                                <option value="%" <?php selected($option->expected_price_type, '%'); ?>>Percentage</option>
                            </select>
                        </td>
                    </tr>

                    <!-- Button Position Option -->
                    <tr>
                        <th scope="row">
                            <label for="position_select">Select Your Button Position</label>
                        </th>
                        <td>
                            <select id="position_select" name="position_select" class="regular-text" style="width:300px;">
                                <option value="short_code" <?php selected($option->button_position, 'short_code'); ?>>Use Short Code Only</option>
                                <option value="4" <?php selected($option->button_position, '4'); ?>>Before Product Title</option>
                                <option value="5" <?php selected($option->button_position, '5'); ?>>After Product Title</option>
                                <option value="10" <?php selected($option->button_position, '10'); ?>>After Product Price</option>
                                <option value="30" <?php selected($option->button_position, '30'); ?>>After Cart Button</option>
                            </select>
                        </td>
                    </tr>

                    <!-- Hide for Out of Stock Products Option -->
                    <tr>
                        <th scope="row">
                            <label for="hide_for_out_stock_product">Hide Subscription Form When Product Is Out Of Stock</label>
                        </th>
                        <td>
                            <input id="hide_for_out_stock_product" type="checkbox" value="1" name="hide_out_of_stock" <?php checked($option->hide_stock_out_product, 1); ?>>
                        </td>
                    </tr>

                    <!-- Hide Current Price Option -->
                    <tr>
                        <th scope="row">
                            <label for="hide_current_price">Hide Current Price In Single Product Page</label>
                        </th>
                        <td>
                            <input id="hide_current_price" type="checkbox" value="1" name="hide_current_price" <?php checked($option->hide_current_price, 1); ?>>
                        </td>
                    </tr>

                    <!-- Back In Stock Option -->
                    <tr>
                        <th scope="row">
                            <label for="back_in_stock">Back In Stock</label>
                        </th>
                        <td>
                            <input id="back_in_stock" type="checkbox" value="1" name="back_in_stock" <?php checked($option->back_in_stock, 1); ?>>
                        </td>
                    </tr>

                    <!-- Ask for Waiting Time Option -->
                    <tr>
                        <th scope="row">
                            <label for="date_field">Ask For Waiting Time</label>
                        </th>
                        <td>
                            <input id="date_field" name="date_field" type="checkbox" value="1" <?php checked($option->ask_last_date, 1); ?>>
                        </td>
                    </tr>

                    <!-- Ask for Additional Note Option -->
                    <tr>
                        <th scope="row">
                            <label for="note_field">Ask For Additional Note</label>
                        </th>
                        <td>
                            <input id="note_field" name="note_field" type="checkbox" value="1" <?php checked($option->ask_note, 1); ?>>
                        </td>
                    </tr>

                    <!-- Form Restriction Option -->
                    <tr>
                        <th scope="row">
                            <label for="form_restiction">Form Restriction</label>
                        </th>
                        <td>
                            <select id="form_restiction" name="form_restiction" class="regular-text" style="width:300px;">
                                <option value="for_all_product" <?php selected($option->form_restiction, 'for_all_product'); ?>>For All Product</option>
                                <option value="specific_product" <?php selected($option->form_restiction, 'specific_product'); ?>>Specific Product</option>
                                <option value="specific_category" <?php selected($option->form_restiction, 'specific_category'); ?>>Specific Category</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <!-- Google reCAPTCHA Settings -->
                <h2>Google reCAPTCHA Settings</h2>
                <table class="form-table" style="width:50%">
                    <tr>
                        <th scope="row" colspan="2">
                            <label for="enable-recaptcha">Disable reCAPTCHA</label>
                        </th>
                        <td>
                            <input type="checkbox" value="1" name="disable_recaptcha" <?php checked($option->recaptcha, 1); ?>>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label for="dcpp-recaptcha-site-key">Site Key</label>
                        </td>
                        <td>
                            <input id="dcpp-recaptcha-site-key" placeholder="xxxxxxxxxxxxxxxxx" style="width: 100%;" type="text" name="dcpp-recap-site-key" value="<?php echo esc_attr($option->siteKey); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label for="dcpp-recaptcha-secret-key">Secret Key</label>
                        </td>
                        <td>
                            <input id="dcpp-recaptcha-secret-key" placeholder="xxxxxxxxxxxxxxxxx" style="width: 100%;" type="text" name="dcpp-recap-secret-key" value="<?php echo esc_attr($option->secretKey); ?>">
                        </td>
                    </tr>
                </table>

                <!-- Save Options Button -->
                <?php submit_button('Save Options'); ?>
            </form>
        </div>
        <?php
    }
}

// Instantiate the class
new Dcpp_price_alert_options();
?>
