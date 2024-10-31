<?php

// Woocommerce My Account Page 

class Dcpp_Options_Handler {

    public function __construct() {
             global $wpdb;

        $table = $wpdb->prefix . 'dcpp_options';

        $option = $wpdb->get_row("SELECT * FROM $table LIMIT 1");
        // Register custom endpoint
        add_action('init', array($this, 'dcpp_custom_endpoint'));

        // Add new menu items to the My Account menu
        add_filter('woocommerce_account_menu_items', array($this, 'dcpp_add_my_account_menu_items'));

        // Display content for the custom endpoint
        add_action('woocommerce_account_subscribed-product_endpoint', array($this, 'dcpp_custom_endpoint_content'));

        if($option->form_restiction == 'specific_product' &&  $option->hide_current_price == 1){

            add_action('wp', array($this,'check_form_restriction_on_product_page'));
    
        }

        // add_action('template_redirect', array($this,'add_notification_button_position'));
        // Flush rewrite rules on plugin activation
        register_activation_hook(__FILE__, array($this, 'dcpp_flush_rewrite_rules'));
        // Flush rewrite rules on plugin deactivation
        register_deactivation_hook(__FILE__, array($this, 'dcpp_flush_rewrite_rules'));
    }

    public function dcpp_custom_endpoint() {
        add_rewrite_endpoint('subscribed-product', EP_ROOT | EP_PAGES);
    }

    public function dcpp_add_my_account_menu_items($items) {
        $items['subscribed-product'] = __('Subscribed Products', 'text-domain');
        return $items;
    }

    public function dcpp_custom_endpoint_content() {

        global $wpdb;
        $table_name = $wpdb->prefix . 'dcpp_price_alert_notification';
        $current_user_id = get_current_user_id();
    
        // Capture the search term
        $search_term = isset($_GET['search_term']) ? sanitize_text_field($_GET['search_term']) : '';
    
        // Prepare the basic query to get the subscribed data for the current user
        $query = "SELECT * FROM $table_name WHERE user_id = %d";
    
        // Modify the query to include a search term for product name or SKU
        if (!empty($search_term)) {
            $query .= " AND product_id IN (
                SELECT ID FROM {$wpdb->prefix}posts 
                WHERE post_type = 'product' 
                AND (post_title LIKE %s OR ID IN (
                    SELECT post_id FROM {$wpdb->prefix}postmeta 
                    WHERE meta_key = '_sku' AND meta_value LIKE %s
                ))
            )";
            $prepare_args = [$current_user_id, '%' . $wpdb->esc_like($search_term) . '%', '%' . $wpdb->esc_like($search_term) . '%'];
        } else {
            $prepare_args = [$current_user_id];
        }
    
        // Get the subscribed data from the database for the current user
        $subscribe_data = $wpdb->get_results(
            $wpdb->prepare($query, ...$prepare_args)
        );
    
        echo '<h3>' . __('Subscribed Products', 'text-domain') . '</h3>';
        ?>
    
        <form method="get" action="">
            <input type="text" name="search_term" value="<?php echo esc_attr($search_term); ?>" placeholder="Search by Product Name or SKU...">
            <input type="submit" value="Search">
        </form>
    
        <?php
        // Check if any subscription data exists
        if (!empty($subscribe_data)) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead>
                    <tr>
                        <th>' . __('Product Image', 'text-domain') . '</th>
                        <th>' . __('Product Name', 'text-domain') . '</th>
                        <th>' . __('SKU', 'text-domain') . '</th>
                        <th>' . __('Current Price', 'text-domain') . '</th>
                        <th>' . __('Alert Price', 'text-domain') . '</th>
                        <th>' . __('Subscription Date', 'text-domain') . '</th>
                        <th>' . __('Action', 'text-domain') . '</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
    
            foreach ($subscribe_data as $data) {
                // Get WooCommerce product object
                $product = wc_get_product($data->product_id);
    
                if ($product && is_object($product)) {
                    // Get product details
                    $product_image = $product->get_image('thumbnail');  // Get product image
                    $product_name = $product->get_name();  // Get product name
                    $product_sku = $product->get_sku();  // Get product SKU
                    $product_price = $product->get_price();  // Get current product price
                    $alert_price = $data->alert_price;  // Get the alert price
                    $subscription_date = date('F j, Y', strtotime($data->created_at));  // Format the subscription date
    
                    echo '<tr>';
                    echo '<td>' . $product_image . '</td>';  // Display product image
                    echo '<td>' . esc_html($product_name) . '</td>';  // Display product name
                    echo '<td>' . esc_html($product_sku) . '</td>';  // Display product SKU
                    echo '<td>' . wc_price($product_price) . '</td>';  // Display current price with currency formatting
                    echo '<td>' . wc_price($alert_price) . '</td>';  // Display alert price with currency formatting
                    echo '<td>' . esc_html($subscription_date) . '</td>';  // Display subscription date
                    echo '<td><button type="button" class="btn btn-primary btn-sm">Unsubscribe</button></td>';
                    echo '</tr>';
                } else {
                    // Handle case where product no longer exists
                    echo '<tr>';
                    echo '<td colspan="7">' . __('Product not found', 'text-domain') . '</td>';
                    echo '</tr>';
                }
            }
    
            echo '</tbody>';
            echo '</table>';
        } else {
            // If no subscriptions found, display a message
            echo '<p>' . __('You have no subscribed products.', 'text-domain') . '</p>';
        }
    }
    
    

    public function dcpp_flush_rewrite_rules() {
        $this->dcpp_custom_endpoint();
        flush_rewrite_rules();
    }



    // If From Restriction is for only specific product 
    function check_form_restriction_on_product_page() {

        if (is_product()) { // Check if it's a single product page
            global $post;
            
            $product_id = $post->ID;
            $form_restriction_value = get_post_meta($product_id, 'form_restiction', true);
    
            if ($form_restriction_value === 'yes') {
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
            }
        }

    }


    // public function add_notification_button_position() {
    //     global $wpdb;
    //     $table = $wpdb->prefix . 'dcpp_options';
    //     $options = $wpdb->get_row("SELECT * FROM $table LIMIT 1");
    
    //     $button_position = $options->button_position;

    //     global $post;
    //     $product_id = $post->ID;
    //     $form_restriction_value = get_post_meta($product_id, 'form_restiction', true);
        
    //     if (is_product()) {
    //         $product_id = get_the_ID(); // Get the current product ID
    //         $product = wc_get_product($product_id); // Get the WC_Product object
    //         $categories = wp_get_post_terms($product_id,'product_cat');

    //         if (!$product) {
    //             error_log('Error: Unable to retrieve WC_Product object.');
    //             return;
    //         }
    
    //         $is_in_stock = $product->is_in_stock(); // Check if the product is in stock
    
    //         if($options->button_position !='short_code'){
    //             if( $options->form_restiction == 'specific_product' && $form_restriction_value == 'yes'){
    //                 if ($options->user_restrict == 1) {
    //                     if ($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)) {
    //                         // Add the button if the product is in stock or if hiding stock-out products is not enabled
    //                         add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     } else {
    //                         // Remove the button if the product is out of stock and hiding stock-out products is enabled
    //                         remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     }
    //                 } elseif ($options->user_restrict == 0 && is_user_logged_in()) {
    //                     if($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)){
    //                         add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     }else{
    //                         remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     }
    //                 }
    //             }elseif($options->form_restiction == 'specific_category'){
                
    //                 foreach ($categories as $category) {
    //                     $term_meta = get_term_meta($category->term_id, 'allow_subscription_category', true);
    //                     if ($term_meta === '1') {
    //                         if ($options->user_restrict == 1) {
    //                             if ($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)) {
    //                                 // Add the button if the product is in stock or if hiding stock-out products is not enabled
    //                                 add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                             } else {
    //                                 // Remove the button if the product is out of stock and hiding stock-out products is enabled
    //                                 remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                             }
    //                         } elseif ($options->user_restrict == 0 && is_user_logged_in()) {
    //                             if($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)){
    //                                 add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                             }else{
    //                                 remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                             }
    //                         }
    //                     }
    //                 }
    //             }elseif($options->form_restiction == 'for_all_product'){

    //                 if ($options->user_restrict == 1) {
    //                     if ($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)) {
    //                         // Add the button if the product is in stock or if hiding stock-out products is not enabled
    //                         add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     } else {
    //                         // Remove the button if the product is out of stock and hiding stock-out products is enabled
    //                         remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     }
    //                 } elseif ($options->user_restrict == 0 && is_user_logged_in()) {
    //                     if($options->hide_stock_out_product == 0 || ($options->hide_stock_out_product == 1 && $is_in_stock)){
    //                         add_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     }else{
    //                         remove_action('woocommerce_single_product_summary', array($this, 'dcpp_display_modal_button'), $button_position);
    //                     }
    //                 }

    //             }
    //         }
    //     }
    // }
    


}

// Instantiate the class
// new Dcpp_Options_Handler();
