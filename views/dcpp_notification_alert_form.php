<?php

class Dcpp_notification_alert_form {

    public function __construct() {
        // Action hook to enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Action hook to display modal
        // add_action('wp_footer', array($this, 'dcpp_customer_alert_modal'));

        // Action hook for form submission handling
        // add_action('admin_post_dcpp_user_alert_form_submit', array($this, 'handle_form_submission'));
    }

    // Method to enqueue necessary scripts and styles
    public function enqueue_scripts() {
        // Enqueue Bootstrap CSS and JS (adjust paths as necessary)
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'), null, true);
    }

    // Method to display the modal form
    public function dcpp_customer_alert_modal() {
        // Get the current post ID (if applicable)
        $post_id = get_queried_object_id();
        global $wpdb;
        $table_name = $wpdb->prefix .'dcpp_options';

        $option = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");

        $tabel_name = $wpdb->prefix ."dcpp_form_labels";

        $label = $wpdb->get_row("SELECT * FROM $tabel_name LIMIT 1");


        ob_start();
        ?>
        <!-- Button trigger modal -->
        <div class="dcpp_modal_button">
           
            <?php
            // Fetch saved values (assuming you retrieve these from the database).
            $button_color = isset($label->button_color) ? $label->button_color : '#000000';
            $button_width = isset($label->button_size_width) ? $label->button_size_width : '100px';
            $button_height = isset($label->button_size_height) ? $label->button_size_height : '40px';
            
            // Apply dynamic styles to the button
           // Apply dynamic styles to the button
            echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModalCenter" style="background-color:' . esc_attr($button_color) . '; width:' . esc_attr($button_width.'px') . '; height:' . esc_attr($button_height.'px') . ';">' . esc_html($label->button_label) . '</button>';

            ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content <?php echo $label->css_class != null ?            $label->css_class : ''; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo $label->form_title ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="dcpp-user-alert-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
                                <input type="hidden" name="action" value="dcpp_user_alert_form_submit">
                                <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
                               
                                <div class="form-group">
                                    <label for="name"><?php echo $label->name_label ?>* </label>
                                    <input type="text" placeholder="<?php echo $label->name_placeholder_label ?>" class="form-control" id="name" name="name" required>
                                </div>
                                  
                                
                                <div class="form-group">
                                    <label for="email"><?php echo $label->email_address_label ?>*</label>
                                    <input type="email" placeholder="<?php echo $label->email_address_placeholder ?>"e class="form-control" id="email" name="email" required>
                                </div>
                             

                                <?php if ( $option->ask_phone_number )   : ?>
                                    <div class="form-group">
                                        <label for="phone">Phone Number*</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                     </div>
                                <?php endif ?>     

                                <?php if ($option ->ask_expected_price == 1) : ?>
                                    <div class="form-group">
                                    <label for="price">
                                        <?php echo $label->expected_discount_label . ($label->required_check_price == 1 ? '*' : ''); ?>
                                    </label>
                                        <input type="number" placeholder="<?php echo $label->ex_discount_placeholder ?>" step="0.01" class="form-control" id="price" name="price" <?php echo $label->required_check_price == 1 ? 'required' : ''; ?>>
                                    </div>
                                <?php endif ?>    
                                
                                <?php if ($option->ask_last_date == 1) : ?>
                                    <div class="form-group">
                                        <label for="date">Date:</label>
                                        <input type="date" class="form-control" id="date" name="date">
                                    </div>
                                <?php endif ?>

                                <?php if($option ->ask_note == 1) : ?>
                                    <div class="form-group">
                                        <label for="note">
                                            <?php echo $label->additional_note_label . ($label->required_check_note == 1 ? '*' : ''); ?>
                                        </label>
                                        <textarea placeholder="<?php echo $label->additional_note_placeholder ?>" <?php $label->required_check_note == 1 ? 'required' : '' ?> class="form-control" id="note" name="note" rows="3"></textarea>
                                    </div>
                                <?php endif ?>    

                                <!-- Recaptcha -->
                                <?php if ($option->recaptcha == 1) : ?>
                                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr($option->siteKey); ?>"></div>
                                <?php endif ?> 

                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                <!-- Nonce Field -->
                                <?php wp_nonce_field('dcpp_user_alert_form', 'dcpp_user_alert_nonce'); ?>
                        </form>   
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        echo ob_get_clean();
    }

    // Method to handle form submission

}
