<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function is_themler_preview() {
    return isset($_GET['wp_customize']) && isset($_GET['theme']) || isset($_GET['preview']) && isset($_GET['template']);
}

function is_themler_action() {
    if (!isset($_REQUEST['action'])) {
        return false;
    }
    return strpos($_REQUEST['action'], 'theme_') === 0;
}

function themler_theme_inactive_notice() {
    if (current_user_can('activate_plugins')) {
        if (!defined('THEME_NAME') || THEME_NAME !== "Billion Themler theme") {
?>
            <div class="error">
                <p><?php printf(__('<strong>Themler theme is inactive.</strong> Themler Core plugin\'s functionality is restricted.'), '<a href="http://wordpress.org/extend/plugins/woocommerce/">'); ?></p>
            </div>
<?php
        }
    }
}
add_action('admin_notices', 'themler_theme_inactive_notice');

function theme_add_scripts_and_styles() {
    $bootstrap_ext = file_exists(THEMLER_PLUGIN_PATH . 'shortcodes/assets/css/bootstrap.min.css') ? '.min.css' : '.css';
    wp_register_style("themler-core-bootstrap", THEMLER_PLUGIN_URL . 'shortcodes/assets/css/bootstrap' . $bootstrap_ext, array(), THEMLER_PLUGIN_VERSION);
    wp_enqueue_style("themler-core-bootstrap");

    wp_register_script("themler-core-script", THEMLER_PLUGIN_URL . 'shortcodes/assets/js/script.js', array('jquery'), THEMLER_PLUGIN_VERSION);
    wp_enqueue_script("themler-core-script");
}
add_action('wp_enqueue_scripts', 'theme_add_scripts_and_styles');