<?php

class Dcpp_add_checkbox_woocommerce {

    // Check Box in Product page if subscription allow
    public function check_box_product_selection() {
        add_action('woocommerce_product_options_general_product_data', array($this, 'check_box_for_specific_product'));
        add_action('woocommerce_process_product_meta', array($this, 'save_custom_checkbox_field'));
    }

    public function check_box_for_specific_product() {
        echo '<div class="options_group">';
        
        woocommerce_wp_checkbox(
            array(
                'id' => 'form_restiction',
                'label' => __('Allow For Price Drop Subscription', 'woocommerce'),
                'description' => __('Enable This Option For Product Price Drop Notification', 'woocommerce'),
                'desc_tip' => true,
            )
        );

        echo '</div>';
    }

    public function save_custom_checkbox_field($post_id) {
        $allow_subs = isset($_POST['form_restiction']) ? 'yes' : 'no';
        update_post_meta($post_id, 'form_restiction', $allow_subs);
    }

    // Check Box in category page if subscription allow
    public function check_box_category_selection() {
        // Hook for adding the checkbox to the category edit page
        add_action('product_cat_edit_form_fields', array($this, 'check_box_for_edit_category'));

        // Hook for adding the checkbox to the category add page
        add_action('product_cat_add_form_fields', array($this, 'check_box_for_add_category_page'));

        // Hook for saving the checkbox value when the category is edited or added
        add_action('edited_product_cat', array($this, 'save_category_checkbox_data'));
        add_action('created_product_cat', array($this, 'save_category_checkbox_data'));
    }

    public function check_box_for_edit_category($term) {
        $term_id = $term->term_id;
        $custom_field_value = get_term_meta($term_id, 'allow_subscription_category', true);

        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="custom_checkbox_field"><?php _e('Allow For Price Drop Subscription'); ?></label></th>
            <td>
                <input type="checkbox" name="allow_subscription_category" id="custom_checkbox_field" value="1" <?php checked($custom_field_value, '1'); ?> />
                <p class="description"><?php _e('Check this box if you want to enable price drop subscription for this category.'); ?></p>
            </td>
        </tr>
        <?php
    }

    public function check_box_for_add_category_page() {
        ?>
        <div class="form-field">
            <label for="custom_checkbox_field"><?php _e('Allow For Price Drop Subscription'); ?></label>
            <input type="checkbox" name="allow_subscription_category" id="custom_checkbox_field" value="1" />
            <p class="description"><?php _e('Check this box if you want to enable price drop subscription for this category.'); ?></p>
        </div>
        <?php
    }

    public function save_category_checkbox_data($term_id) {
        $custom_field_value = isset($_POST['allow_subscription_category']) ? '1' : '';
        update_term_meta($term_id, 'allow_subscription_category', $custom_field_value);
    }
}

// Instantiate the class to hook everything
// new Dcpp_add_checkbox_woocommerce;