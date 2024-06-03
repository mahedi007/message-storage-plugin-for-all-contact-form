<?php

function msp_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'messages';

    $inbox_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE spam = 0");
    $spam_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE spam = 1");

    $tab = isset($_GET['spam']) ? intval($_GET['spam']) : 0;

    echo '<div class="wrap">';
    echo '<h1>Stored Messages</h1>';
    echo '<ul class="subsubsub">';
    echo '<li><a href="' . admin_url('admin.php?page=message-storage&spam=0') . '"' . ($tab === 0 ? ' class="current"' : '') . '>Inbox (' . $inbox_count . ')</a> | </li>';
    echo '<li><a href="' . admin_url('admin.php?page=message-storage&spam=1') . '"' . ($tab === 1 ? ' class="current"' : '') . '>Spam (' . $spam_count . ')</a></li>';
    echo '</ul>';

    $messageListTable = new MSP_Message_List_Table();
    $messageListTable->prepare_items();
    echo '<form method="post">';
    $messageListTable->search_box('search', 'search_id');
    $messageListTable->display();
    echo '</form>';

    echo '</div>';
}

?>
