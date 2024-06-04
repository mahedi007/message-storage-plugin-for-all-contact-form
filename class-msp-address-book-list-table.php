<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class MSP_Address_Book_List_Table extends WP_List_Table {

    function __construct() {
        parent::__construct(array(
            'singular' => 'address',
            'plural'   => 'addresses',
            'ajax'     => false
        ));
    }

    function get_columns() {
        return array(
            'cb'         => '<input type="checkbox" />',
            'email'      => 'Email',
            'first_name' => 'First Name',
            'last_name'  => 'Last Name'
        );
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="address[]" value="%s" />', $item->id);
    }

    function column_email($item) {
        $edit_link = admin_url('admin.php?page=edit-address&address_id=' . $item->id);
        return sprintf('<a href="%s">%s</a>', $edit_link, $item->email);
    }

    function column_first_name($item) {
        return $item->first_name;
    }

    function column_last_name($item) {
        return $item->last_name;
    }

    function get_bulk_actions() {
        return array(
            'delete' => 'Delete'
        );
    }

    function process_bulk_action() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'messages';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['address']) ? $_REQUEST['address'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
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

        if ($search) {
            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE email LIKE '%%%s%%' OR first_name LIKE '%%%s%%' OR last_name LIKE '%%%s%%'", $search, $search, $search);
        } else {
            $sql = "SELECT * FROM $table_name WHERE email <> ''";
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
