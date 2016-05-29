<?php

function upage_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts);

    $id = $atts['id'];
    if (!is_numeric($id)) {
        return '';
    }

    $post = get_post($id);
    if ($post->post_type !== 'upage_section') {
        return '';
    }

    return $post->post_content;
}
ShortcodesUtility::addShortcode('upage_section', 'upage_section_shortcode');

function upage_collect_sections_ids($atts) {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts);

    $id = $atts['id'];
    if (is_numeric($id)) {
        global $upage_section_ids;
        $upage_section_ids[] = $id;
    }
}

function upage_force_sql_query($content) {
    global $upage_section_ids;
    $upage_section_ids = array();
    themler_render_shortcode($content, 'upage_section', 'upage_collect_sections_ids');
    if ($upage_section_ids) {
        get_posts(array(
            'post__in' => $upage_section_ids,
            'post_type' => 'upage_section',
            'numberposts' => -1,
        ));
    }
    return $content;
}
add_filter('the_content', 'upage_force_sql_query', 0);