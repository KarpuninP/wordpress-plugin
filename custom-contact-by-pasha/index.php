<?php
/*
Plugin Name: Custom Contact Form by Pasha
Description: A custom contact form plugin that integrates with HubSpot and logs messages. Allows users to submit a contact form with validation and integrates with HubSpot CRM. This is a test task for the SmartApp company
Version: 1.0
Author: Karpunin Pavel
Author URI: https://www.linkedin.com/in/pasha-karpunin-php-developer/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: custom-contact-form

Custom Contact Form is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Custom Contact Form is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Custom Contact Form. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Include required files.
include_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

/**
 * Register the form shortcode.
 */
function ccf_register_form_shortcode() {
    add_shortcode('custom_contact_form', 'ccf_display_form');
}
add_action('init', 'ccf_register_form_shortcode');

/**
 * Display the contact form.
 *
 * @return string HTML markup for the form.
 */
function ccf_display_form() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/form.php';
    return ob_get_clean();
}


/**
 * Enqueue scripts for admin and frontend.
 */
function ccf_enqueue_scripts() {
    if (wp_script_is('jquery', 'enqueued')) {
        wp_enqueue_script('ccf-frontend-script', plugin_dir_url(__FILE__) . 'frontend-script.js', array('jquery'), null, true);
    } else {
        wp_enqueue_script('jquery');
        wp_enqueue_script('ccf-frontend-script', plugin_dir_url(__FILE__) . 'frontend-script.js', array('jquery'), null, true);
    }


    wp_enqueue_style('ccf-frontend-styles', plugin_dir_url(__FILE__) . 'styles.css');
}

add_action('wp_enqueue_scripts', 'ccf_enqueue_scripts'); // For frontend
add_action('admin_enqueue_scripts', 'ccf_enqueue_scripts'); // For admin


/**
 * Enable Gutenberg support if not already enabled.
 */
function ccf_enable_gutenberg_support() {
    if (!current_theme_supports('editor')) {
        add_theme_support('editor');
    }
}
add_action('after_setup_theme', 'ccf_enable_gutenberg_support');