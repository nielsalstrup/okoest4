<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function theme_get_project() {
    header('Content-Type: application/javascript');
    header("Pragma: no-cache");

    $base_template_dir = get_template_directory();
    $base_template_name = get_template() . '';
    $template_name = $base_template_name . '_preview';

    $project = get_theme_project($base_template_dir);
    $project_data = $project['project_data'];

    $user = wp_get_current_user();
    $uid = (int) $user->ID;
    $templates = theme_get_templates(false);
    $domain = theme_get_domain();
?>
    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';

    var templateInfo = <?php echo json_encode(array(
        'login_page' => wp_login_url(theme_get_editor_link() . (empty($domain) ? '' : '&domain=' . urlencode($domain))),
        'user' => $uid,
        'nonce' => wp_create_nonce('theme_template_export'),
        'importer_nonce' => (!theme_is_converted() && theme_content_exists() ? wp_create_nonce('theme_content_importer') : ''),
        'template_url' => esc_url( get_template_directory_uri() ) ,
        'admin_url' => admin_url(),
        'ajax_url' => admin_url('admin-ajax.php'),
        'pages_url' => admin_url('edit.php?post_type=page'),
        'home_url' =>  home_url(),
        'cms_version' => get_wp_versions(),
        'base_template_name' => $base_template_name,
        'template_name' => $template_name,
        'templates' => $templates['urls'],
        'page_url' => $templates['page_url'],
        'used_template_files' => $templates['used_files'],
        'template_types' => $templates['types'],
        'cssJsSources' => theme_get_cache(get_template_directory()),
        'md5Hashes' => theme_get_hashes(get_template_directory()),
        'projectData' => $project_data,
        'woocommerce_enabled' => theme_woocommerce_enabled(),
        'maxRequestSize' => getMaxRequestSize(),
        'preview_nonce' => theme_get_preview_nonce(),
        'plugin_version' => CoreUpdateHelper::getPreparedVersion(),
        'active_plugins' => theme_get_plugins_info(),
    )); ?>;
<?php
}
theme_add_export_action('theme_get_project');