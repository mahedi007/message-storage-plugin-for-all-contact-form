<?php
/*
Plugin Name: Message Storage Plugin for Contact forms
Description: A plugin to store contact form messages and email addresses.
Version: 1.0.1
Author: Mahedi Hasan
Author URI: https://mahedi.whizbd.com
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function msp_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'messages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        name tinytext NOT NULL,
        email text NOT NULL,
        subject text NOT NULL,
        message text NOT NULL,
        spam tinyint(1) DEFAULT 0 NOT NULL,
        first_name tinytext NOT NULL,
        last_name tinytext NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'msp_create_table');

// Hook into Contact Form 7 submission
function msp_cf7_store_data($contact_form) {
    $submission = WPCF7_Submission::get_instance();

    if ($submission) {
        $posted_data = $submission->get_posted_data();
        $name = sanitize_text_field($posted_data['your-name']);
        $email = sanitize_email($posted_data['your-email']);
        $subject = sanitize_text_field($posted_data['your-subject']); // Ensure your form has a 'your-subject' field
        $message = sanitize_textarea_field($posted_data['your-message']);

        global $wpdb;
        $table_name = $wpdb->prefix . 'messages';

        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time('mysql'),
                'name' => $name,
                'email' => $email,
                'subject' => $subject, // Add subject here
                'message' => $message,
            )
        );
    }
}
add_action('wpcf7_mail_sent', 'msp_cf7_store_data');

// Hook into WPForms submission
function msp_wpforms_store_data($fields, $entry, $form_data) {
    msp_log('WPForms submission received.');

    $name = '';
    $email = '';
    $subject = '';
    $message = '';

    foreach ($fields as $field) {
        msp_log('Field type: ' . $field['type']);
        msp_log('Field value: ' . $field['value']);
        
        if ($field['type'] == 'name') {
            $name = sanitize_text_field($field['value']);
        } elseif ($field['type'] == 'email') {
            $email = sanitize_email($field['value']);
        } elseif ($field['type'] == 'text' && strpos($field['label'], 'Subject') !== false) {
            $subject = sanitize_text_field($field['value']);
        } elseif ($field['type'] == 'textarea') {
            $message = sanitize_textarea_field($field['value']);
        }
    }

    msp_log('Name: ' . $name);
    msp_log('Email: ' . $email);
    msp_log('Subject: ' . $subject);
    msp_log('Message: ' . $message);

    global $wpdb;
    $table_name = $wpdb->prefix . 'messages';

    $wpdb->insert(
        $table_name,
        array(
            'time' => current_time('mysql'),
            'name' => $name,
            'email' => $email,
            'subject' => $subject, // Add subject here
            'message' => $message,
        )
    );

    msp_log('Insert result: ' . $wpdb->last_error);
}
add_action('wpforms_process_complete', 'msp_wpforms_store_data', 10, 3);

function msp_admin_menu() {
    add_menu_page(
        'Messages',
        'Messages',
        'manage_options',
        'message-storage',
        'msp_admin_page',
        'dashicons-email-alt',
        6
    );
    add_submenu_page(
        'message-storage',
        'Inbox',
        'Inbox',
        'manage_options',
        'message-storage',
        'msp_admin_page'
    );
    add_submenu_page(
        'message-storage',
        'Address Book',
        'Address Book',
        'manage_options',
        'address-book',
        'msp_address_book_page'
    );
    add_submenu_page(
        null,
        'Edit Message',
        'Edit Message',
        'manage_options',
        'edit-message',
        'msp_edit_message_page'
    );
    add_submenu_page(
        null,
        'Edit Address',
        'Edit Address',
        'manage_options',
        'edit-address',
        'msp_edit_address_page'
    );
}
add_action('admin_menu', 'msp_admin_menu');

include plugin_dir_path(__FILE__) . 'class-msp-message-list-table.php';
include plugin_dir_path(__FILE__) . 'admin-page.php';
include plugin_dir_path(__FILE__) . 'class-msp-address-book-list-table.php';
include plugin_dir_path(__FILE__) . 'address-book-page.php';
include plugin_dir_path(__FILE__) . 'edit-message.php';
include plugin_dir_path(__FILE__) . 'edit-address.php';

?>
