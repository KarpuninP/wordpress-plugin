<?php
/**
 * Display the settings page.
 */
function ccf_settings_page() {
    ?>
    <div class="wrap">
        <div>
            <h1>Contact Form Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ccf_settings_group');
                do_settings_sections('ccf_settings_page');
                submit_button();
                ?>
            </form>
        </div>

        <div>
            <h2>Thank You for Installing Our Plugin!</h2>
            <p>We appreciate you choosing our Custom Contact by Pasha. This plugin is designed to integrate seamlessly with HubSpot, log messages, and offer a customizable contact form for your website. </p>

            <p><strong>Important:</strong> This plugin requires jQuery and Gutenberg. If Gutenberg is not active in your theme, our plugin will automatically enable it to ensure full functionality.</p>
            <p>If you have any questions or need support, please contact us.</p>

            <h2>Instructions</h2>
            <div class="instructions">
                <div>
                    <h3>Step 1</h3>
                    <p>Configure the settings on this page.
                        Admin Email & HubSpot API Key</p>
                </div>
                <div>
                    <h3>Step 2</h3>
                    <p>Add the shortcode to any page or post. <b>[custom_contact_form]</b></p>
                </div>
                <div>
                    <h3>Step 3</h3>
                    <p>Check the logs for any errors or issues.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="wrap">
        <div>
            <h2>Preview Shortcode for testing</h2>
            <?php echo do_shortcode('[custom_contact_form]'); ?>
        </div>

        <div>
            <h2>Logs</h2>
            <textarea rows="20" cols="50" readonly>
                <?php
                $log_file = plugin_dir_path(__FILE__) . 'records.log';
                if (file_exists($log_file)) {
                    echo file_get_contents($log_file) ;
                } else {
                    echo 'Sorry, no posts have been made yet.';
                }
                ?>
            </textarea>
        </div>
    </div>
    <?php
    require_once plugin_dir_path(__FILE__) . 'form-handler.php';
}

/**
 * Enqueue admin styles.
 */
function ccf_enqueue_admin_styles() {
    wp_enqueue_style('ccf-admin-styles', plugin_dir_url(__FILE__) . 'ccf-admin-styles.css');
}
add_action('admin_enqueue_scripts', 'ccf_enqueue_admin_styles');

/**
 * Register settings.
 */
function ccf_register_settings() {
    register_setting('ccf_settings_group', 'ccf_admin_email');
    register_setting('ccf_settings_group', 'ccf_hubspot_api_key');

    add_settings_section('ccf_settings_section', '', null, 'ccf_settings_page');

    add_settings_field('ccf_admin_email', 'Admin Email', 'ccf_admin_email_field', 'ccf_settings_page', 'ccf_settings_section');
    add_settings_field('ccf_hubspot_api_key', 'HubSpot API Key', 'ccf_hubspot_api_key_field', 'ccf_settings_page', 'ccf_settings_section');
}
add_action('admin_init', 'ccf_register_settings');

/**
 * Render the admin email field.
 */
function ccf_admin_email_field() {
    $admin_email = get_option('ccf_admin_email');
    echo '<input type="email" name="ccf_admin_email" value="' . esc_attr($admin_email) . '">';
}

/**
 * Render the HubSpot API key field.
 */
function ccf_hubspot_api_key_field() {
    $hubspot_api_key = get_option('ccf_hubspot_api_key');
    echo '<input type="text" name="ccf_hubspot_api_key" value="' . esc_attr($hubspot_api_key) . '">';
}

/**
 * Add the settings page to the admin menu.
 */
function ccf_add_admin_menu() {
    add_menu_page(
        'Contact Form Settings',
        'Contact Form',
        'manage_options',
        'ccf_settings_page',
        'ccf_settings_page'
    );
}
add_action('admin_menu', 'ccf_add_admin_menu');
?>
