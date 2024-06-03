<?php

function msp_address_book_page() {
    echo '<div class="wrap">';
    echo '<h1>Address Book</h1>';

    $addressBookListTable = new MSP_Address_Book_List_Table();
    $addressBookListTable->prepare_items();
    echo '<form method="get">';
    $addressBookListTable->search_box('search', 'search_id');
    $addressBookListTable->display();
    echo '</form>';

    echo '</div>';
}

?>
