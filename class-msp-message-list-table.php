<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class MSP_Message_List_Table extends WP_List_Table {

    function __construct() {
        parent::__construct(array(
            'singular' => 'message',
            'plural'   => 'messages',
            'ajax'     => false
        ));
    }

    function get_columns() {
        return array(
            'cb'      => '<input type="checkbox" />',
            'name'    => 'Name',
            'email'   => 'Email',
			'subject' => 'Subject',
            'message' => 'Message',
            'time'    => 'Time',
            'spam'    => 'Spam'
        );
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="message[]" value="%s" />', $item->id);
    }

    function column_name($item) {
        $edit_link = admin_url('admin.php?page=edit-message&message_id=' . $item->id);
        return sprintf('<a href="%s">%s</a>', $edit_link, $item->name);
    }

    function column_email($item) {
        return $item->email;
    }
	
	function column_subject($item) {
        return $item->subject;
    }

    function column_message($item) {
        return $item->message;
    }

    function column_time($item) {
        return $item->time;
    }

    function column_spam($item) {
        return $item->spam ? 'Yes' : 'No';
    }

    function get_bulk_actions() {
        return array(
            'delete' => 'Delete',
            'spam' => 'Mark as Spam',
            'not_spam' => 'Mark as Not Spam'
        );
    }

    function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'messages';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['message']) ? $_REQUEST['message'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }

        if ('spam' === $this->current_action()) {
            $ids = isset($_REQUEST['message']) ? $_REQUEST['message'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("UPDATE $table_name SET spam = 1 WHERE id IN($ids)");
            }
        }

        if ('not_spam' === $this->current_action()) {
            $ids = isset($_REQUEST['message']) ? $_REQUEST['message'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("UPDATE $table_name SET spam = 0 WHERE id IN($ids)");
            }
        }
    }

    function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'messages';
        $per_page = 10;

        $this->process_bulk_action();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $search = isset($_REQUEST['s']) ? $_REQUEST['s'] : '';
        $spam = isset($_GET['spam']) ? intval($_GET['spam']) : 0;

        if ($search) {
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE (name LIKE '%%%s%%' OR email LIKE '%%%s%%' OR message LIKE '%%%s%%') AND spam = %d", $search, $search, $search, $spam);
        } else {
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE spam = %d", $spam);
        }

        $total_items = $wpdb->query($sql);
        $current_page = $this->get_pagenum();
        $sql .= " LIMIT $per_page OFFSET " . (($current_page - 1) * $per_page);

        $this->items = $wpdb->get_results($sql);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ));
    }
}

?>
