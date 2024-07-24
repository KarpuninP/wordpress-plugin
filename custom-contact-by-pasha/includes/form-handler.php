<?php
/**
* Function for logging messages
*/
function my_log($message) {
    $log_file = plugin_dir_path(__FILE__) . 'records.log';
    $current_time = date('Y-m-d H:i:s');
    file_put_contents($log_file, $current_time . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

/**
 *  Function to validate and clear form data
 * @return array
 */
function validate_and_sanitize_form_data() {
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);
    $email = sanitize_email($_POST['email']);

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email format']);
        wp_die();
    }

    if (empty($first_name) || empty($last_name) || empty($subject) || empty($message) || empty($email)) {
        wp_send_json_error(['message' => 'All fields are required']);
        wp_die();
    }

    return compact('first_name', 'last_name', 'subject', 'message', 'email');
}

/**
 * Feature for creating a contact in HubSpot
 * @param $first_name
 * @param $last_name
 * @param $email
 * @param $hubspot_api_key
 * @return string
 */
function create_hubspot_contact($first_name, $last_name, $email, $hubspot_api_key) {
    $url = 'https://api.hubapi.com/crm/v3/objects/contacts';
    $body = json_encode([
        'properties' => [
            'email' => $email,
            'firstname' => $first_name,
            'lastname' => $last_name,
        ],
    ]);

    $response = wp_remote_post($url, [
        'body'    => $body,
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $hubspot_api_key,
        ],
    ]);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        my_log('HubSpot API Error: ' . $error_message);
        wp_send_json_error(['message' => 'Failed to create contact']);
        wp_die();
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['id'])) {
        return 'hubspot id: ' . $data['id'];
    } else {
        my_log('HubSpot API Error: Unexpected response structure');
        return 'Error';
    }
}

/**
 * Function for processing AJAX requests
 * @return void
 */
function ccf_form_handler() {
    if (!isset($_POST['ccf_form_nonce_field']) || !wp_verify_nonce($_POST['ccf_form_nonce_field'], 'ccf_form_nonce_action')) {
        wp_send_json_error(['message' => 'Security check failed']);
        wp_die();
    }

    $form_data = validate_and_sanitize_form_data();
    extract($form_data);

    $to = get_option('ccf_admin_email'); // Получение email администратора из опций
    $email_subject = "Contact Form Submission: $subject";
    $email_body = "You have received a new message from the user $first_name $last_name.\n\n".
        "Subject: $subject\n".
        "Message:\n$message\n".
        "Email: $email";
    $email_sent = wp_mail($to, $email_subject, $email_body);;

    $hubspot_api_key = get_option('ccf_hubspot_api_key');
    $hubspot_res = create_hubspot_contact($first_name, $last_name, $email, $hubspot_api_key);

    if ($email_sent) {
        my_log($email_body . PHP_EOL . 'hubspot information: ' . $hubspot_res);
        wp_send_json_success(['message' => 'Message sent successfully']);
    } else {
        my_log('Failed to send message from: ' . $email);
        wp_send_json_error(['message' => 'Failed to send message']);
    }

    wp_die();
}
add_action('wp_ajax_ccf_form_submit', 'ccf_form_handler'); // For authorized users
add_action('wp_ajax_nopriv_ccf_form_submit', 'ccf_form_handler'); // For unauthorized users














