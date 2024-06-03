<?php

function msp_edit_address_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'messages';
    $address_id = intval($_GET['address_id']);

    if ($_POST['update_address']) {
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);

        $wpdb->update(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name'  => $last_name
            ),
            array('id' => $address_id)
        );

        echo '<div class="updated"><p>Address updated.</p></div>';
    }

    $address = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $address_id));

    ?>
    <div class="wrap">
        <h1>Edit Address</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Email</th>
                    <td><?php echo esc_html($address->email); ?></td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td><input type="text" name="first_name" value="<?php echo esc_attr($address->first_name); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><input type="text" name="last_name" value="<?php echo esc_attr($address->last_name); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <input type="hidden" name="update_address" value="1" />
            <input type="submit" class="button button-primary" value="Update Address" />
        </form>
    </div>
    <?php
}

?>
