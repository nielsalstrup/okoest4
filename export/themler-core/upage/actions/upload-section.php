<?php

function upage_upload_sections() {

    if (!isset($_REQUEST['sections']) || !is_array($_REQUEST['sections'])) {
        return array(
            'status' => 'error',
            'message' => 'sections parameter missing',
        );
    }

    $result = array();

    $sections_data = $_REQUEST['sections'];
    foreach($sections_data as $section) {
        $html = $section['html'];
        $name = isset($section['name']) ? $section['name'] : 'Section';
        $mediaId = isset($section['mediaId']) ? $section['mediaId'] : 0;
        $thumbnailMediaId = isset($section['thumbnail']) && isset($section['thumbnail']['mediaId']) && is_numeric($section['thumbnail']['mediaId']) ? $section['thumbnail']['mediaId'] : 0;

        $post_date = gmdate('Y-m-d H:i:s', time() + get_option('gmt_offset') * 3600);

        $insert_data = array(
            'post_type' => 'upage_section',
            'post_title' => $name,
            'post_content' => $html,
            'post_name' => sanitize_title_with_dashes($name),
            'post_date' => $post_date,
            'post_date_gmt' => get_gmt_from_date($post_date),
            'post_status' => 'publish'
        );

        if ($mediaId) {
            $insert_data['ID'] = $mediaId;
            $new_section_id = wp_update_post($insert_data);
        } else {
            $new_section_id = wp_insert_post($insert_data);
        }
        if ($thumbnailMediaId) {
            update_post_meta($new_section_id, '_thumbnail_id', $thumbnailMediaId);
        }

        $result[] = array(
            'section_id' => $new_section_id
        );
    }

    return array(
        'result' => 'done',
        'data' => $result,
    );
}
upage_add_action('upage_upload_sections');