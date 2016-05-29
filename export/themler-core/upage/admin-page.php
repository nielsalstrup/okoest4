<?php

global $upage_screenshots;
$upage_screenshots = array();

function upage_section_screenshot_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts);

    $id = $atts['id'];
    $img_src = upage_get_section_thumbnail($id, 'full');

    if ($img_src) {
        global $upage_screenshots;
        $upage_screenshots[] = sprintf('<div><a href="#" class="upage-editor"><img src="%s"></a></div>', $img_src);
    }
    return '';
}

function upage_screenshorts($post) {
    if ($post->post_type == 'page' || $post->post_type == 'post') {
        global $upage_screenshots;
        $upage_screenshots = array();

        themler_render_shortcode($post->post_content, 'upage_section', 'upage_section_screenshot_shortcode');
?>
        <div id="upage-preview">
            <?php echo implode('', $upage_screenshots); ?>
        </div>
<?php
    }
}
add_action('edit_form_after_title', 'upage_screenshorts', 100);

function upage_preview_styles() {
?>
    <style>
        #upage-preview a img {
            max-width: 100%;
        }
    </style>
<?php
}
add_action('admin_head', 'upage_preview_styles');