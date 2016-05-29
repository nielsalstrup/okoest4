<?php

function upage_upload_image() {

    if (!current_user_can('upload_files')) {
        return array(
            'status' => 'error',
            'message' => __('You do not have permission to upload files.'),
        );
    }

    if (!isset($_POST['html-upload']) || empty($_FILES)) {
        return array(
            'status' => 'error',
            'message' => 'Invalid parameters',
        );
    }

    $post_id = 0;
    if (isset($_REQUEST['post_id'])) {
        $post_id = absint($_REQUEST['post_id']);
        if (!get_post($post_id) || !current_user_can('edit_post', $post_id))
            $post_id = 0;
    }

    check_admin_referer('media-form');

    $upload_id = media_handle_upload('async-upload', $post_id);

    if (is_wp_error($upload_id)) {
        return array(
            'status' => 'error',
            'message' => $upload_id->get_error_message(),
        );
    }
    $image = wp_get_attachment_image_src($upload_id, 'full');

    return array(
        'status' => 'done',
        'upload_id' => $upload_id,
        'image_url' => $image[0],
    );
}
upage_add_action('upage_upload_image');