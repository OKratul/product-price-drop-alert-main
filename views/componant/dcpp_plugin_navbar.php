<?php


class dcpp_plugin_navbar{

    public function dcpp_navbar() {
        $current_page = isset($_GET['page']) ? $_GET['page'] : '';

        ?>
        <div class="nav-tab-wrapper">
            <a href="?page=dcpp_price_alert_notifications" class="nav-tab <?php echo ($current_page === 'dcpp_price_alert_notifications') ? 'nav-tab-active' : ''; ?>">Notifications</a>

            <a href="?page=dcpp_form_options" class="nav-tab <?php echo ($current_page === 'dcpp_form_options') ? 'nav-tab-active' : ''; ?>">General Options</a>

            <a href="?page=dcpp_smtp_mail_options" class="nav-tab <?php echo ($current_page === 'dcpp_smtp_mail_options') ? 'nav-tab-active' : ''; ?>">SMTP Settings</a>

            <a href="?page=dcpp_smtp_mail_template_options" class="nav-tab <?php echo ($current_page === 'dcpp_smtp_mail_template_options') ? 'nav-tab-active' : ''; ?>">Mail Template</a>

            <a href="?page=dcpp_manage_labels" class="nav-tab <?php echo ($current_page === 'dcpp_manage_labels') ? 'nav-tab-active' : ''; ?>">Labels</a>
            
        </div>
        <?php
    }

}