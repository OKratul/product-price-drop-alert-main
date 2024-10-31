<?php 

class Dcpp_woocommerce_custom_field {

    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'dcpp_custom_field']);
        add_action('woocommerce_process_product_meta', [$this, 'handle_custom_field_data']);
    }

    public function dcpp_custom_field() {
        global $woocommerce, $post;

        echo '<div class="options_group">';

        // Custom Field 1
        woocommerce_wp_text_input(
            array(
                'id' => '_purches_price',
                'label' => __('Purchase Price', 'woocommerce'),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => __('Enter purchase price here.', 'woocommerce')
            )
        );

        // Custom Field 2
        woocommerce_wp_text_input(
            array(
                'id' => '_last_selling_price',
                'label' => __('Last Selling Price', 'woocommerce'),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => __('Enter last selling price here.', 'woocommerce')
            )
        );

        echo '</div>';
    }

    public function handle_custom_field_data($post_id) {
        // Save Purchase Price
        $purchase_price = sanitize_text_field($_POST['_purches_price']);
        update_post_meta($post_id, '_purches_price', $purchase_price);

        // Save Last Selling Price
        $last_selling_price = sanitize_text_field($_POST['_last_selling_price']);
        update_post_meta($post_id, '_last_selling_price', $last_selling_price);
    }
}
