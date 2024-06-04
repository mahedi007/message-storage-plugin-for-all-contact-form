<?php

function msp_address_book_page() {
    echo '<div class="wrap">';
    echo '<h1>Address Book</h1>';

    $addressBookTable = new MSP_Address_Book_List_Table();
    $addressBookTable->prepare_items();

    echo '<form method="get">';
    echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '" />';
    $addressBookTable->search_box('Search Emails', 'search_id');
    $addressBookTable->display();
    echo '</form>';

    echo '</div>';
}

?>
