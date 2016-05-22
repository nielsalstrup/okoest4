<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Plugin_Upgrader')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
}

class WP_Export_Theme_Installer_Skin extends Theme_Installer_Skin {

    public $output = "";

    public function header() {
        $this->done_header = true;
    }

    public function footer() {
        $this->done_footer = true;
    }

    public function feedback($string) {
        if (isset($this->upgrader->strings[$string]))
            $string = $this->upgrader->strings[$string];

        if (strpos($string, '%') !== false) {
            $args = func_get_args();
            $args = array_splice($args, 1);
            if ($args) {
                $args = array_map('strip_tags', $args);
                $args = array_map('esc_html', $args);
                $string = vsprintf($string, $args);
            }
        }
        if (empty($string))
            return;
        $this->output .= "<p>$string</p>";
    }
}

class WP_Export_Upgrader_Skin extends WP_Upgrader_Skin {

    public $output = "";

    public function header() {
        $this->done_header = true;
    }

    public function footer() {
        $this->done_footer = true;
    }

    public function feedback($string) {
        if (isset($this->upgrader->strings[$string]))
            $string = $this->upgrader->strings[$string];

        if (strpos($string, '%') !== false) {
            $args = func_get_args();
            $args = array_splice($args, 1);
            if ($args) {
                $args = array_map('strip_tags', $args);
                $args = array_map('esc_html', $args);
                $string = vsprintf($string, $args);
            }
        }
        if (empty($string))
            return;
        $this->output .= "<p>$string</p>";
    }
}

class CoreUpdateHelper {

    public static $slug = 'themler-core';
    public static $themeDir;
    public static $zipSource;
    public static $pluginSource;
    public static $pluginDest;
    public static $pluginMainFile;

    public static function initialize() {
        self::$themeDir = get_template_directory();
        self::$zipSource = self::$themeDir . '/plugins/' . self::$slug . '.zip';
        self::$pluginSource = self::$themeDir . '/export/' . self::$slug;
        self::$pluginDest = WP_PLUGIN_DIR . '/' . self::$slug;
        self::$pluginMainFile = self::$pluginDest . '/' .  self::$slug . '.php';
    }

    public static function updateFromTheme() {

        self::_createPluginZip();
        if (self::_needPluginUpdate()) {
            self::_updatePlugin();
        }
        self::_updateScriptsAndStyles();
        self::_activatePlugin();
    }

    private static function _createPluginZip() {

        FilesHelper::create_dir(self::$themeDir . '/plugins');
        FilesHelper::remove_file(self::$zipSource);

        require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
        $archive = new PclZip(self::$zipSource);

        if (0 == $archive->create(self::$pluginSource, PCLZIP_OPT_REMOVE_PATH, self::$themeDir . '/export'))
            throw new Exception("Unable to create themler-core zip. Create error: " . $archive->errorInfo(true));
    }

    public static function getInstalledVersion() {
        if (!file_exists(self::$pluginMainFile)) {
            return '';
        }
        $info = get_plugin_data(self::$pluginMainFile);
        return $info ? $info['Version'] : '';
    }

    public static function getPreparedVersion() {
        $info = get_file_data(self::$pluginSource . '/' . self::$slug . '.php', array('Version' => 'Version'));
        return $info['Version'];
    }

    private static function _needPluginUpdate() {
        $new_version = self::getPreparedVersion();
        $version = self::getInstalledVersion();

        return !$version || version_compare($version, $new_version, '<');
    }

    private static function _updatePlugin() {

        try {
            // remove old plugin
            FilesHelper::empty_dir(self::$pluginDest, true);

            // Prepare variables for Plugin_Installer_Skin class.
            $extra = array('slug' => self::$slug);

            $skin_args = array(
                'type' => 'upload',
                'plugin' => self::$pluginDest,
                'extra' => $extra,
            );

            $skin = new WP_Export_Upgrader_Skin($skin_args);
            $upgrader = new Plugin_Upgrader($skin);

            $result = $upgrader->install(self::$zipSource);

            if ($result === false || $result === null) {
                throw new Exception('Core update error: ' . $upgrader->skin->output);
            }

            if (is_wp_error($result)) {
                throw new Exception('Core update error: ' . $upgrader->skin->output . "\n" . $result->get_error_message());
            }
        } catch(PermissionDeniedException $e) {
            $check_folders = theme_get_permissions_check_folders();
            $check_folders[] = WP_PLUGIN_DIR;
            $msg = str_replace('{folders}', '<ol><li>' . implode('</li><li>', $check_folders) . '</li></ol>', $e->getExtendedMessage());

            throw new PluginInstallException("Can't update themler-core plugin.\n" . $msg);
        } catch(Exception $e) {
            throw new PluginInstallException("Can't update themler-core plugin.\n" . $e->getMessage());
        }
    }

    private static function _updateScriptsAndStyles() {
        FilesHelper::create_dir(self::$pluginDest . '/shortcodes/assets');
        FilesHelper::create_dir(self::$pluginDest . '/shortcodes/assets/css');
        FilesHelper::create_dir(self::$pluginDest . '/shortcodes/assets/js');
        FilesHelper::copy_recursive(self::$themeDir . '/bootstrap.css', self::$pluginDest . '/shortcodes/assets/css/bootstrap.css');
        FilesHelper::copy_recursive(self::$themeDir . '/bootstrap.min.css', self::$pluginDest . '/shortcodes/assets/css/bootstrap.min.css');
        FilesHelper::copy_recursive(self::$themeDir . '/script.js', self::$pluginDest . '/shortcodes/assets/js/script.js');
        FilesHelper::copy_recursive(self::$themeDir . '/fonts', self::$pluginDest . '/shortcodes/assets/css/fonts');
    }

    private static function _activatePlugin() {
        if (!is_plugin_active(self::$pluginMainFile)) {
            activate_plugin(self::$pluginMainFile);
        }
    }
}
CoreUpdateHelper::initialize();

class PluginInstallException extends Exception {
    public function getExtendedMessage() {
        return '[PHP_NOT_ERROR]' . parent::getMessage() . '[PHP_NOT_ERROR]<!--' . $this->getTraceAsString() . '-->';
    }
};