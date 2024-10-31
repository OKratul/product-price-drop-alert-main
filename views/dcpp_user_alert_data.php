<?php

class Dcpp_user_alert_data {

    public function __construct() {
        $this->dcpp_data_table();
    }
    public function dcpp_data_table() {
        
        require_once(plugin_dir_path(__DIR__) . 'views/componant/dcpp_plugin_navbar.php');
        require_once(plugin_dir_path(__DIR__) . 'pagination/dcpp_pagination.php');

        require_once(plugin_dir_path(__FILE__) . 'componant/dcpp_data_manage_option.php');
    
        $manage_form = new dcpp_data_manage_option();
        $pagination = new dcpp_pagination;
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'dcpp_price_alert_notification';
        $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $per_page = 5;
    
        // Capture the search term from the form
        $search_term = isset($_GET['search_term']) ? sanitize_text_field($_GET['search_term']) : '';
    
        // Base query for fetching data
        $query = "SELECT * FROM $table_name WHERE status IS NULL";
    
        // Modify the query to include search by product name, SKU, customer name, email, phone number, and date
        if (!empty($search_term)) {
            $query .= " AND (
                product_id IN (SELECT ID FROM {$wpdb->prefix}posts WHERE post_title LIKE %s OR ID IN (SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_sku' AND meta_value LIKE %s)) 
                OR name LIKE %s 
                OR email LIKE %s 
                OR phone_number LIKE %s 
                OR DATE(created_at) = %s
            )";
            $search_like_term = '%' . $wpdb->esc_like($search_term) . '%';
            $prepare_args = [
                $search_like_term,   // Product Name or SKU
                $search_like_term,   // SKU
                $search_like_term,   // Customer Name
                $search_like_term,   // Email
                $search_like_term,   // Phone Number
                $search_term         // Date (exact match)
            ];
        } else {
            $prepare_args = [];
        }
    
        // Paginate results
        $paginated_data = $pagination->get_paginated_data($per_page, $current_page, $query, $prepare_args);
        $datas = $paginated_data['data'];
        $total_items = $paginated_data['total_items'];
    
        // Navbar and form
        $navbar = new dcpp_plugin_navbar;
        echo $navbar->dcpp_navbar();
    
        echo '<div class="wrap">';
        echo '<h1>'. _e("Price Drop Alert Notificatins","product_price_drop_alert") .'</h1>';
       
        // Table data
        if ($datas) {
            ?>
    
    
            <form method="get" action="">
                <input type="hidden" name="page" value="dcpp_price_alert_notifications">
                <input type="text" name="search_term" value="<?php echo esc_attr($search_term); ?>" placeholder="Search by Product Name, SKU, Customer Name, Email, Phone Number or Date...">
                <input type="submit" value="Search">
            </form>
        <?php
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead>
                    <tr>
                        <th style="width:50px">ID</th>
                        <th style="width:180px">Product</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Expected Price</th>
                        <th> Status </th>
                        <th>Final Date</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>';
            echo '<tbody>';
    
            foreach ($datas as $data) {
                // Get product details
                $product = wc_get_product($data->product_id);
                $purchase_price = get_post_meta($data->product_id, '_purches_price', true);
                $last_selling_price = get_post_meta($data->product_id, '_last_selling_price', true);
    
                if ($product && is_object($product)) {
                    $product_name = $product->get_name();
                    $product_price = $product->get_price();
                    $product_sku = $product->get_sku();
                    $product_image = $product->get_image();
    
                    echo '<tr>';
                    echo '<td>' . $data->product_id . '</td>'; // Product ID
                    echo '<td>';
                    if ($product_image) {
                        // Extract the image URL from the image HTML
                        preg_match('@src="([^"]+)"@', $product_image, $match);
                        $product_image_url = isset($match[1]) ? $match[1] : '';
                        if ($product_image_url) {
                            echo '<img target="_blank" src="' . esc_url($product_image_url) . '" alt="' . esc_attr($product_name) . '" style="max-width: 100px; max-height: 100px;" >';
                        }
                        // Link to product edit page
                        echo '<br><a href="' . admin_url('post.php?post=' . $data->product_id . '&action=edit') . '">' . $product_name . '</a><br>';
                        echo 'Sell Price: ' . wc_price($product_price) . '<br>';
                        echo 'SKU: ' . $product_sku . '<br>';
                    } else {
                        echo 'Product not found';
                    }
                    echo '</td>';
                    echo '<td>' . $data->name . '</td>'; // Customer Name
                    echo '<td>' . $data->email . '</td>'; // Email
                    echo '<td>' . $data->phone_number . '</td>'; // Phone Number
                    echo '<td>' . $data->expected_price . '<br>';
                    echo '<td>'. $data->status . '</td>';
                    if ($purchase_price) {
                        echo 'Purchase Price: ' . $purchase_price . '<br>';
                    }
                    
                    if ($last_selling_price) {
                        echo 'Last Selling Price: ' . $last_selling_price;
                    }
                    
                    echo '</td>';
                     // Expected Price
                    echo '<td>' . $data->last_wait_date . '</td>'; // Final Date
                    echo '<td>' . $data->created_at . '</td>'; // Created At
                    echo '<td>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#manage-option-modal-' . $data->id . '">
                                     Manage 
                                </button>
                            </td>';
                    echo '</tr>';

                    $manage_form->manage_option_modal($data->id,$product_name,$product_price,$data->product_id);

                } else {
                    echo '<tr>';
                    echo '<td>' . $data->product_id . '</td>'; // Product ID
                    echo '<td>Product not found</td>'; // Placeholder for product not found
                    echo '<td>' . $data->name . '</td>'; // Customer Name
                    echo '<td>' . $data->email . '</td>'; // Email
                    echo '<td>' . $data->phone_number . '</td>'; // Phone Number
                    echo '<td>' . $data->expected_price . '</td>'; // Expected Price
                    echo '<td>' . $data->last_wait_date . '</td>'; // Final Date
                    echo '<td>' . $data->created_at . '</td>'; // Created At
                    echo '<td><button class="btn btn-success btn-sm">Send Mail</button></td>';
                    echo '</tr>';
                }

                
            }
    
            echo '</tbody></table>';
            ?>
            <div class="dcpp_pagination">
                <?php $pagination->display_pagination($total_items, $per_page, $current_page); ?>
            </div>
             
            <?php
        } else {
            echo 'No data found in the table.';
        }
    
        echo '</div>';



    }
    
}

new Dcpp_user_alert_data();
