<?php

    class dcpp_pagination{


        function get_paginated_data($per_page = 10, $current_page = 1) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'dcpp_price_alert_notification';
        
            // Calculate the offset for the current page
            $offset = ($current_page - 1) * $per_page;
        
            // Get total number of rows in the table
            $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name  WHERE status IS NULL");
        
            // Fetch the rows for the current page
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE status IS NULL LIMIT %d OFFSET %d",
                    $per_page,
                    $offset
                )
            );
        
            return [
                'data' => $results,
                'total_items' => $total_items,
                'per_page' => $per_page,
                'current_page' => $current_page,
            ];
        }


        function display_pagination($total_items, $per_page, $current_page) {
            $total_pages = ceil($total_items / $per_page);
        
            if ($total_pages <= 1) {
                return; // No need to display pagination if there is only one page
            }
        
            $page_base_url = remove_query_arg('paged'); // Remove current page param to build base URL
        
            echo '<nav aria-label="Page navigation">';
            echo '<ul class="pagination justify-content-center">';
        
            // Display "Previous" link
            if ($current_page > 1) {
                echo '<li class="page-item">
                        <a class="page-link" href="' . esc_url(add_query_arg('paged', $current_page - 1, $page_base_url)) . '" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                      </li>';
            }
        
            // Display page numbers
            for ($i = 1; $i <= $total_pages; $i++) {
                $active_class = ($i === $current_page) ? 'active' : '';
                echo '<li class="page-item ' . $active_class . '">
                        <a class="page-link" href="' . esc_url(add_query_arg('paged', $i, $page_base_url)) . '">' . $i . '</a>
                      </li>';
            }
        
            // Display "Next" link
            if ($current_page < $total_pages) {
                echo '<li class="page-item">
                        <a class="page-link" href="' . esc_url(add_query_arg('paged', $current_page + 1, $page_base_url)) . '" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                      </li>';
            }
        
            echo '</ul>';
            echo '</nav>';
        }

    }