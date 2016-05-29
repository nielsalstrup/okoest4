<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly

$user = wp_get_current_user();
$domain = upage_get_editor_domain();

$uid = (int)$user->ID;
$ajax_nonce = wp_create_nonce('upage-upload');
$post_id = $_GET['p'];

$settings = array(
    'actions' => array(
        'uploadImage' => add_query_arg(array('action' => 'upage_upload_image'), admin_url('admin-ajax.php')),
        'uploadSections' => add_query_arg(array('action' => 'upage_upload_sections'), admin_url('admin-ajax.php')),
        'updatePost' => add_query_arg(array('action' => 'upage_update_post'), admin_url('admin-ajax.php')),
    ),
    'ajaxData' => array(
        'uid' => $uid,
        '_ajax_nonce' => $ajax_nonce,
    ),
    'uploadImageOptions' => array(
        'formFileName' => 'async-upload',
        'params' => array(
            'html-upload' => 'Upload',
            'post_id' => $post_id,
            '_wpnonce' => wp_create_nonce('media-form'),
            'uid' => $uid,
            '_ajax_nonce' => $ajax_nonce,
        ),
    ),
    'postId' => $post_id,
);


function upage_editable_section_shortcode($atts, $content = '') {
    global $upage_editable_sections;

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

    $screenshot = upage_get_section_thumbnail($id, 'full');
    $section_attributes = " data-thumbnail=\"$screenshot\" data-media-id=\"$id\"";

    $section_content = $post->post_content;
    $section_content = preg_replace('#</style>\s*<\w+#', '$0 ' . $section_attributes, $section_content);
    $section_content = preg_replace('#$\s*<\w+#',        '$0 ' . $section_attributes, $section_content);

    $upage_editable_sections[] = $section_content;

    return '';
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <script type="text/javascript">
        var upageSettings = <?php echo json_encode($settings, JSON_PRETTY_PRINT); ?>;
    </script>
    <link rel='stylesheet' href="<?php echo THEMLER_PLUGIN_URL; ?>shortcodes/assets/css/bootstrap.css?version=<?php echo THEMLER_PLUGIN_VERSION; ?>" />
    <link rel='stylesheet' href="<?php echo THEMLER_PLUGIN_URL; ?>shortcodes/assets/css/style.css?version=<?php echo THEMLER_PLUGIN_VERSION; ?>" />
    <link rel='stylesheet' href="<?php echo THEMLER_PLUGIN_URL; ?>shortcodes/assets/css/style.ie.css?version=<?php echo THEMLER_PLUGIN_VERSION; ?>" />

    <link rel='stylesheet' href="<?php echo THEMLER_PLUGIN_URL; ?>shortcodes/assets/js/bootstrap.min.js?version=<?php echo THEMLER_PLUGIN_VERSION; ?>" />
    <link rel='stylesheet' href="<?php echo THEMLER_PLUGIN_URL; ?>shortcodes/assets/js/script.js?version=<?php echo THEMLER_PLUGIN_VERSION; ?>" />
    <link rel='stylesheet' href="<?php echo THEMLER_PLUGIN_URL; ?>shortcodes/assets/js/script.ie.js?version=<?php echo THEMLER_PLUGIN_VERSION; ?>" />

    <script type="text/javascript" src="<?php echo THEMLER_PLUGIN_URL; ?>/upage/assets/js/editor.js?version=<?php echo THEMLER_PLUGIN_VERSION; ?>"></script>
    <script type="text/javascript" src="<?php echo THEMLER_PLUGIN_URL; ?>/upage/assets/js/editor-utility.js?version=<?php echo THEMLER_PLUGIN_VERSION; ?>"></script>
    <script type="text/javascript" src="<?php echo THEMLER_PLUGIN_URL; ?>/upage/assets/js/editor-uploader.js?version=<?php echo THEMLER_PLUGIN_VERSION; ?>"></script>

    <script type="text/javascript" src="<?php echo $domain; ?>/Editor/loader.js"></script>

    <style type="text/css">
        body {
            background: none transparent;
        }

        body > [data-thumbnail] { /* hide all sections */
            display: none !important;
        }
    </style>
</head>
<body>
<?php
    $post_ID = $_GET['p'];
    $post = get_post($post_ID);

    if ($post) {

        global $upage_editable_sections;
        $upage_editable_sections = array();

        themler_render_shortcode($post->post_content, 'upage_section', 'upage_editable_section_shortcode');

        echo implode('', $upage_editable_sections);
    }
?>
</body>
</html>