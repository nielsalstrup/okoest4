<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once(dirname(__FILE__) . '/post-types.php');
require_once(dirname(__FILE__) . '/admin-page.php');
require_once(dirname(__FILE__) . '/actions/actions.php');

define('UPAGE_DOMAIN', 'upage.io');

function upage_get_editor_domain() {
    $domain = isset($_REQUEST['domain'])
        ? urldecode($_REQUEST['domain'])
        : 'http://' . UPAGE_DOMAIN;

    $domain = preg_replace('#(.*)\/$#', '$1', $domain);
    if (is_ssl()) {
        $domain = str_replace('http://', 'https://', $domain);
    } else {
        $domain = str_replace('https://', 'http://', $domain);
    }
    return $domain;
}

function upage_get_editor_link() {
    return add_query_arg(array(
        'page' => 'upage_editor',
        'domain' => urlencode(upage_get_editor_domain()),
        'p' => $_GET['post']
    ), admin_url() . 'themes.php');
}

function upage_edit_post_link_set_domain($link) {
    $domain = upage_get_editor_domain();
    if (strpos($domain, UPAGE_DOMAIN) === false) {
        $link = add_query_arg(array('domain' => urlencode($domain)), $link);
    }
    return $link;
}
add_filter('get_edit_post_link', 'upage_edit_post_link_set_domain');

function upage_update_post_set_domain_field() {
    $domain = upage_get_editor_domain();
    if (strpos($domain, UPAGE_DOMAIN) === false) {
        printf('<input type="hidden" name="domain" value="%s" />', $domain);
    }
}
add_action('edit_form_top', 'upage_update_post_set_domain_field');

function themler_add_upage_button($post) {
    if (!isset($_REQUEST['domain']) || strpos($_REQUEST['domain'], UPAGE_DOMAIN) !== false) return; // TODO

    $type = $post->post_type;
    if ($type === 'post' || $type === 'page') {
        $editor_link = upage_get_editor_link();
?>
        <style>
            #upage-saving-bg {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                background: #000;
                opacity: 0.7;
                z-index: 1000010;
            }

            #upage-saving-content * {
                z-index: 1000111;
            }

            #upage-saving-content {
                position: fixed;
                left: 50%;
                overflow: hidden;
                top: 50%;
                z-index: 1000011;

                width: 200px;
                height: 250px;
                margin-left: -100px;
                margin-top: -200px;
            }

            #upage-saving-content .loader {
                position: absolute;
                left: 50%;
                top: 50%;
                margin-top:-50px;
                margin-left:-50px;
            }

            #upage-saving-content .upage-saving-text {
                position: relative;
                top: 200px;
                font-size: 22px;
                text-align: center;
                color: white;
            }

            #upage-saving-content .upage-saving-wrap {
                position:absolute;
                width:100px;
                height:100px;
                float:left;

                -webkit-animation-name: rotateThis;
                -webkit-animation-duration:3s;
                -webkit-animation-iteration-count:infinite;
                -webkit-animation-timing-function:linear;

                -moz-transform:scale(0.3);
                -o-transform:scale(0.3);
            }

            #upage-saving-content .upage-saving-wrap div {
                width: 10px;
                height: 30px;
                background: white;
                position: absolute;
                top: 35px;
                left: 45px;

                border-radius: 50px;
                -moz-border-radius-bottomleft:50px;
                -moz-border-radius-bottomright:50px;
                -moz-border-radius-topleft:50px;
                -moz-border-radius-topright:50px;
            }

            @-webkit-keyframes rotateThis {
                from { -webkit-transform:scale(0.6) rotate(0deg); }
                to 	 { -webkit-transform:scale(0.6) rotate(360deg); }
            }

            #upage-saving-content .bar1 {
                -o-transform:rotate(0deg) translate(0, -100px);opacity:0.1;
                -moz-transform:rotate(0deg) translate(0, -100px);opacity:0.1;
                -webkit-transform:rotate(0deg) translate(0, -100px);opacity:0.1;
            }
            #upage-saving-content .bar2 {
                -o-transform:rotate(36deg) translate(0, -100px);opacity:0.2;
                -moz-transform:rotate(36deg) translate(0, -100px);opacity:0.2;
                -webkit-transform:rotate(36deg) translate(0, -100px);opacity:0.2;
            }
            #upage-saving-content .bar3 {
                -o-transform:rotate(72deg) translate(0, -100px);opacity:0.3;
                -moz-transform:rotate(72deg) translate(0, -100px);opacity:0.3;
                -webkit-transform:rotate(72deg) translate(0, -100px);opacity:0.3;
            }
            #upage-saving-content .bar4 {
                -o-transform:rotate(108deg) translate(0, -100px);opacity:0.4;
                -moz-transform:rotate(108deg) translate(0, -100px);opacity:0.4;
                -webkit-transform:rotate(108deg) translate(0, -100px);opacity:0.4;
            }
            #upage-saving-content .bar5 {
                -o-transform:rotate(144deg) translate(0, -100px);opacity:0.5;
                -moz-transform:rotate(144deg) translate(0, -100px);opacity:0.5;
                -webkit-transform:rotate(144deg) translate(0, -100px);opacity:0.5;
            }
            #upage-saving-content .bar6 {
                -o-transform:rotate(180deg) translate(0, -100px);opacity:0.6;
                -moz-transform:rotate(180deg) translate(0, -100px);opacity:0.6;
                -webkit-transform:rotate(180deg) translate(0, -100px);opacity:0.6;
            }
            #upage-saving-content .bar7 {
                -o-transform:rotate(216deg) translate(0, -100px);opacity:0.7;
                -moz-transform:rotate(216deg) translate(0, -100px);opacity:0.7;
                -webkit-transform:rotate(216deg) translate(0, -100px);opacity:0.7;
            }
            #upage-saving-content .bar8 {
                -o-transform:rotate(252deg) translate(0, -100px);opacity:0.8;
                -moz-transform:rotate(252deg) translate(0, -100px);opacity:0.8;
                -webkit-transform:rotate(252deg) translate(0, -100px);opacity:0.8;
            }
            #upage-saving-content .bar9 {
                -o-transform:rotate(288deg) translate(0, -100px);opacity:0.9;
                -moz-transform:rotate(288deg) translate(0, -100px);opacity:0.9;
                -webkit-transform:rotate(288deg) translate(0, -100px);opacity:0.9;
            }
            #upage-saving-content .bar10 {
                -o-transform:rotate(324deg) translate(0, -100px);opacity:1;
                -moz-transform:rotate(324deg) translate(0, -100px);opacity:1;
                -webkit-transform:rotate(324deg) translate(0, -100px);opacity:1;
            }
        </style>

        <script>
            jQuery(document).on('click', '.upage-editor', function() {
                jQuery('body')
                    .prepend('<iframe src="<?php echo $editor_link; ?>" id="upage-iframe" width="100%" height="100%" frameborder="0" allowtransparency="true" style="position: fixed;z-index: 9999999;width: 100%;height: 100%;top:0;left:0;bottom: 0;"></iframe>')
                    .append('<style id="upage-editor-styles">body {overflow:hidden;}</style>');
            });

            function onSavingStart() {
                jQuery('body').append(
                    '<div id="upage-saving-wrap">' +
                    '   <div id="upage-saving-bg"></div>' +
                    '   <div id="upage-saving-content">' +
                    '       <div class="loader">' +
                    '           <div class="upage-saving-wrap">' +
                    '               <div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div><div class="bar7"></div><div class="bar8"></div><div class="bar9"></div><div class="bar10"></div>' +
                    '           </div>' +
                    '       </div>' +
                    '       <div class="upage-saving-text">Saving...</div>' +
                    '   </div>' +
                    '</div>');
            }

            function onSavingEnd(hasChanges) {
                if (hasChanges) {
                    window.location.reload();
                    return;
                }

                jQuery('#upage-saving-wrap').remove();
                jQuery('#upage-editor-styles').remove();
                jQuery('#upage-iframe').remove();
            }

            function upageEventListener(event) {
                var data;
                try {
                    data = JSON.parse(event.data);
                } catch(e) {
                    console.warn(e);
                    return;
                }

                if (data.message === 'savingStart') {
                    onSavingStart();
                } else if (data.message === 'savingEnd') {
                    onSavingEnd(data.hasChanges);
                }
            }

            if (window.addEventListener) {
                window.addEventListener("message", upageEventListener);
            } else {
                window.attachEvent("onmessage", upageEventListener); // IE8
            }
        </script>
        <a href="#" id="edit-in-upage" class="button upage-editor"><?php _e('Edit in uPage', 'default'); ?></a>
<?php
    }
}
add_action('themler_edit_form_buttons', 'themler_add_upage_button');

function upage_get_section_thumbnail($section_id, $size) {
    if (!is_numeric($section_id)) {
        return '';
    }

    $post = get_post($section_id);
    if ($post->post_type !== 'upage_section') {
        return '';
    }

    $thumb_id = get_post_thumbnail_id($post->ID);
    if (!$thumb_id) {
        return '';
    }

    $image = wp_get_attachment_image_src($thumb_id, $size);
    return $image[0];
}

function upage_add_editor_page() {
    add_theme_page(__('uPage', 'default'), __('uPage', 'default'), 'edit_themes', 'upage_editor', 'upage_editor');

    // remove submenu from Appearance
    global $submenu;
    if (is_array($submenu['themes.php'])) {
        foreach($submenu['themes.php'] as $key => $value) {
            if (in_array('upage_editor', $value)) {
                unset($submenu['themes.php'][$key]);
                break;
            }
        }
    }
}
add_action('admin_menu', 'upage_add_editor_page');

function upage_editor() {
    require_once(dirname(__FILE__) . '/upage-editor.php');
    die();
}
add_action('load-appearance_page_upage_editor', 'upage_editor');