<?php

function upage_section_placeholder($atts, $content = '') {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts);

    $id = $atts['id'];
    return "%SECTION$id%";
}

function upage_split_content($content) {
    $content = themler_render_shortcode($content, 'upage_section', 'upage_section_placeholder');

    $parts = preg_split('#(%SECTION\d*%)#', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $result = array(
        array(
            'type' => 'section',
            'id' => 'start',
        )
    );
    foreach($parts as $part) {
        if (preg_match('#^%SECTION(\d*)%$#', $part, $m)) {
            $result[] = array(
                'type' => 'section',
                'id' => $m[1],
            );
        } else {
            $result[] = array(
                'type' => 'text',
                'text' => $part,
            );
        }
    }
    return $result;
}

function upage_generate_content($old, $new) {
    $old = upage_split_content($old);
    $new = upage_split_content($new);
    $new = array_filter($new, create_function('$a', 'return $a["type"] === "section";'));

    $presents_ids = array();
    foreach($new as $part) {
        $presents_ids[$part['id']] = true;
    }

    $blocks = array();
    $last_id = 'start';
    foreach($old as $part) {
        if ($part['type'] !== 'section') {
            $blocks[$last_id][] = $part;
        } if (isset($presents_ids[$part['id']])) {
            $last_id = $part['id'];
            $blocks[$last_id] = array($part);
        }
    }

    $result = '';
    foreach($new as $part) {
        $id = $part['id'];
        if (!isset($blocks[$id])) {
            // it's a new section
            $result .= "[upage_section id=$id]\n";
        } else {
            foreach ($blocks[$id] as $old_part) {
                if ($old_part['type'] !== 'section') {
                    $result .= $old_part['text'];
                } else if ($id !== 'start') {
                    $result .= "[upage_section id=$id]";
                }
            }
        }
    }
    return $result;
}

function upage_update_post() {

    if (!isset($_REQUEST['id']) || !isset($_REQUEST['content'])) {
        return array(
            'status' => 'error',
            'message' => 'post parameter missing',
        );
    }

    $id = $_REQUEST['id'];
    $content = $_REQUEST['content'];

    $post = get_post($id);
    if (!$post) {
        return array(
            'result' => 'error',
            'message' => 'There is no post with id=' . $id,
        );
    }

    $new_content = upage_generate_content($post->post_content, $content);

    wp_update_post(array(
        'ID' => $id,
        'post_content' => $new_content,
    ));

    return array(
        'result' => 'done',
    );
}
upage_add_action('upage_update_post');