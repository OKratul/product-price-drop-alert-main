<?php

class dcpp_labels_options {

    public function __construct() {
        add_action("admin_enqueue_scripts", array( $this,"enque_custom_css_editor") );
    }

    public function enque_custom_css_editor( ) {

        wp_enqueue_code_editor(array('type' => 'text/css'));
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');

    }
    public function dcpp_labels_form() {

        global $wpdb;
        $table_name = $wpdb->prefix . 'dcpp_form_labels';
        $data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d LIMIT 1", 1)); // Assuming ID = 1 for now

        ?>
        <div class="wrap">
            <div style="padding:50px">
                <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="save_form_label_data">
                    <?php wp_nonce_field('save_form_labels_data', 'form_label_data_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <h4>Subscribe Form Settings</h4>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">Button Color</th>
                            <td>
                                <input type="color" name="button_color" style="width:100px" value="<?php echo isset($data->button_color) ? esc_attr($data->button_color) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Button Size</th>
                            <td>
                                <span>Height</span>
                                <input type="number" placeholder="100px" name="button_height" style="width:100px" value="<?php echo isset($data->button_size_height) ? esc_attr($data->button_size_height) : ''; ?>">
                                <span>Width</span>
                                <input type="number" placeholder="40px" name="button_width" style="width:100px" value="<?php echo isset($data->button_size_width) ? esc_attr($data->button_size_width) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Button Label</th>
                            <td>
                                <input type="text" placeholder="Subscribe" name="button_label" style="width:300px" value="<?php echo isset($data->button_label) ? esc_attr($data->button_label) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Subscribe Form Title</th>
                            <td>
                                <input type="text" name="subscribe_form_title" style="width:50%" value="<?php echo isset($data->form_title) ? esc_attr($data->form_title) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">User Name Label</th>
                            <td>
                                <input type="text" name="first_name_label" style="width:50%" value="<?php echo isset($data->name_label) ? esc_attr($data->name_label) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Name Placeholder Label</th>
                            <td>
                                <input type="text" name="name_place_holder_label" style="width:50%" value="<?php echo isset($data->name_placeholder_label) ? esc_attr($data->name_placeholder_label) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Email Address Label</th>
                            <td>
                                <input type="text" name="email_label" style="width:50%" value="<?php echo isset($data->email_address_label) ? esc_attr($data->email_address_label) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Email Address Placeholder Label</th>
                            <td>
                                <input type="text" name="email_placeholder_label" style="width:50%" value="<?php echo isset($data->email_address_placeholder) ? esc_attr($data->email_address_placeholder) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Expected Discount Label</th>
                            <td>
                                <input type="text" name="ex_discount_label" style="width:30%; margin-right:50px" value="<?php echo isset($data->expected_discount_label) ? esc_attr($data->expected_discount_label) : ''; ?>">
                                <span>Required Field?</span>
                                <input type="checkbox" name="required_check_discount" style="margin-left:10px" <?php echo isset($data->required_check_price) && $data->required_check_price ? 'checked' : ''; ?>>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Expected Discount Placeholder</th>
                            <td>
                                <input type="text" name="ex_discount_placeholder" style="width:50%" value="<?php echo isset($data->ex_discount_placeholder) ? esc_attr($data->ex_discount_placeholder) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Additional Note Label</th>
                            <td>
                                <input type="text" name="note_label" style="width:30%;margin-right:50px" value="<?php echo isset($data->additional_note_label) ? esc_attr($data->additional_note_label) : ''; ?>">
                                <span>Required Field?</span>
                                <input type="checkbox" name="required_check_note" style="margin-left:10px" <?php echo isset($data->required_check_note) && $data->required_check_note ? 'checked' : ''; ?>>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Additional Note Placeholder Label</th>
                            <td>
                                <input type="text" name="note_placeholder_label" style="width:50%" value="<?php echo isset($data->additional_note_placeholder) ? esc_attr($data->additional_note_placeholder) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <h4>Advanced</h4>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">Add CSS Class</th>
                            <td>
                                <input type="text" name="css_class" style="width: 50%;" placeholder=".abcde" value="<?php echo isset($data->css_class) ? esc_attr($data->css_class) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Add CSS ID</th>
                            <td>
                                <input type="text" name="css_id" style="width: 50%;" placeholder="#abcde" value="<?php echo isset($data->css_id) ? esc_attr($data->css_id) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Custom CSS</th>
                            <td>
                                <textarea id="custom_css" name="custom_css" rows="10" style="width:50%"><?php echo isset($data->custom_css) ? esc_textarea($data->custom_css) : ''; ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td><?php submit_button('Save Options'); ?></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
                editorSettings.codemirror = _.extend(
                    {},
                    editorSettings.codemirror, 
                    {
                        mode: 'css',
                        indentUnit: 2,
                        tabSize: 2,
                        lineNumbers: true
                    }
                );
                var editor = wp.codeEditor.initialize($('#custom_css'), editorSettings);
            });
        </script>
        <?php
    }
}

new dcpp_labels_options();

?>