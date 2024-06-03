<?php

function msp_edit_message_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'messages';
    $message_id = intval($_GET['message_id']);

    if ($_POST['update_message']) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $message = sanitize_textarea_field($_POST['message']);
        $spam = isset($_POST['spam']) ? 1 : 0;

        $wpdb->update(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'message' => $message,
                'spam' => $spam
            ),
            array('id' => $message_id)
        );

        echo '<div class="updated"><p>Message updated.</p></div>';
    }

    $message = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $message_id));

    ?>
    <div class="wrap">
        <h1>Edit Message</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Name</th>
                    <td><input type="text" name="name" value="<?php echo esc_attr($message->name); ?>" class="regular-text" readonly /></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=address-book&s=' . urlencode($message->email)); ?>">
                            <?php echo esc_html($message->email); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <th>Message</th>
                    <td><textarea name="message" class="large-text" readonly><?php echo esc_textarea($message->message); ?></textarea></td>
                </tr>
                <tr>
                    <th>Spam</th>
                    <td><input type="checkbox" name="spam" <?php checked($message->spam, 1); ?> /></td>
                </tr>
            </table>
            <input type="hidden" name="update_message" value="1" />
            <input type="submit" class="button button-primary" value="Update Message" />
        </form>
    </div>
    <?php
}
?>
