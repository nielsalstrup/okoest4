<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ShortcodesUtility
{
    public static function atts($defaults, $atts, $additional = array('')) {
        foreach($additional as $prefix) {
            foreach(array('', '_md', '_sm', '_xs') as $mode) {
                $defaults[$prefix . 'css' . $mode] = '';
                $defaults[$prefix . 'typography' . $mode] = '';
            }
            $defaults[$prefix . 'hide'] = '';
        }
        return shortcode_atts($defaults, $atts);
    }

    public static function getBool($value, $default_value = false) {
        if ($value === true || $value === '1' || $value === 'true' || $value === 'yes')
            return true;
        if ($value === false || $value === '0' || $value === 'false' || $value === 'no')
            return false;
        return $default_value;
    }

    public static function escape($text) {
        return esc_attr($text);
    }

    public static $enable_filters = true;
    public static $unautop_storage;

    public static function doShortcode($content, $enable_shortcodes = true, $allow_paragraphs = false) {

        if ($enable_shortcodes) {
            $content = preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $content);
            $content = do_shortcode(shortcode_unautop(trim($content)));
        } else {
            foreach (self::$shortcodes as $tag => $func) {
                remove_shortcode($tag);
            }

            $remove_wpautop = !$allow_paragraphs && has_filter('the_content', 'wpautop');

            if ($remove_wpautop) {
                remove_filter('the_content', 'wpautop');
            }

            self::$enable_filters = false;
            $content = apply_filters('the_content', $content);
            self::$enable_filters = true;

            if ($remove_wpautop) {
                add_filter('the_content', 'wpautop');
            }

            foreach (self::$shortcodes as $tag => $func) {
                add_shortcode($tag, $func);
            }
        }
        return $content;
    }

    public static function addShortcode($tag, $_func) {
        $func = create_function('$atts, $content, $tag',
            'if (isset($atts["_id"])) $content = ShortcodesUtility::$unautop_storage[$atts["_id"]];' .
            'return call_user_func("' . $_func . '", $atts, $content, str_replace("_unautop", "", $tag));');

        self::$shortcodeTypes[$tag] = $func;
        self::$shortcodes[$tag] = $func;

        add_shortcode($tag, $func);
    }

    public static $shortcodes = array();
    public static $shortcodeTypes = array();

    public static function collectShortcodes($content) {
        if (!self::$enable_filters)
            return $content;

        $pattern = '\[\/((' . join('|', array_keys(self::$shortcodeTypes)) . ')_\d+)\]';
        if (preg_match_all("#$pattern#", $content, $matches)) {
            $matches_count = count($matches[0]);
            for($i = 0; $i < $matches_count; $i++) {
                $tag = $matches[1][$i];
                $func = self::$shortcodeTypes[$matches[2][$i]];
                if (!shortcode_exists($tag)) {
                    add_shortcode($tag, $func);
                    self::$shortcodes[$tag] = $func;
                }
            }
        }
        return $content;
    }

    private static $_parentTags;

    public static function collectShortcodeFunc($atts, $content = '', $tag = '') {

        $closed = !isset(self::$_parentTags[$tag]);

        self::$_parentTags[$tag] = true;
        $content = do_shortcode($content);
        if ($closed) {
            unset(self::$_parentTags[$tag]);
        }

        if (empty(self::$_parentTags)) {
            $id = count(self::$unautop_storage);
            self::$unautop_storage[] = $content;
            $atts['_id'] = $id;
            return self::makeShortcode($tag . '_unautop', '', $atts, $closed);
        }

        return self::makeShortcode($tag . '_unautop', $content, $atts, $closed);
    }

    public static function beforeTheContent($content) {
        if (self::$enable_filters) {
            self::$unautop_storage = array();

            global $shortcode_tags;
            $orig_shortcode_tags = $shortcode_tags; // save original shortcodes
            $shortcode_tags = array();

            foreach (self::$shortcodes as $tag => $func) {
                add_shortcode($tag, 'ShortcodesUtility::collectShortcodeFunc');
            }

            self::$_parentTags = array();
            $content = do_shortcode($content);
            $shortcode_tags = $orig_shortcode_tags; // restore original shortcodes

            // replace themler shortcodes with unautop shortcodes
            foreach (self::$shortcodes as $tag => $func) {
                remove_shortcode($tag);
                add_shortcode($tag . '_unautop', $func);
            }
        }
        return $content;
    }

    public static function afterTheContent($content) {
        if (self::$enable_filters) {
            global $shortcode_tags;
            $orig_shortcode_tags = $shortcode_tags; // save original shortcodes
            $shortcode_tags = array();

            foreach (self::$shortcodes as $tag => $func) {
                add_shortcode($tag . '_unautop', 'ShortcodesUtility::restoreUnrenderedShortcodes');
            }

            $content = do_shortcode($content);
            $shortcode_tags = $orig_shortcode_tags; // restore original shortcodes

            // inversely, replace unautop shortcodes with themler shortcodes
            foreach (self::$shortcodes as $tag => $func) {
                remove_shortcode($tag . '_unautop');
                add_shortcode($tag, $func);
            }
        }
        return $content;
    }

    public static function restoreUnrenderedShortcodes($atts, $content = '', $tag = '') {
        if (isset($atts['_id'])) {
            $content = self::$unautop_storage[$atts['_id']];
            unset($atts['_id']);
        }
        return self::makeShortcode(str_replace('_unautop', '', $tag), do_shortcode($content), $atts);
    }

    public static function makeShortcode($tag, $content, $atts, $closed = true) {
        if (!is_array($atts))
            $atts = array();

        $code = "[$tag";
        foreach($atts as $key => $value) {
            if (is_numeric($key)) {
                $code .= " $value";
            } else {
                $code .= " $key=\"$value\"";
            }
        }
        if (!$closed && !$content) {
            return "$code]";
        }
        return "$code]$content" . "[/$tag]";
    }


    private static $_styles = array();

    public static function addResult($content, $styles = '') {
        return '<!--style-->' .  $styles . '<!--/style-->' . $content;
    }

    public static function _resultCollectStylesCallback($matches) {
        self::$_styles[] = $matches[1];
        return '';
    }

    public static function processResult($content) {
        self::$_styles = array();
        $content = preg_replace_callback('#<!--style-->([\s\S]*?)<!--\/style-->#', 'ShortcodesUtility::_resultCollectStylesCallback', $content);
        return array($content, join("\n", self::$_styles));
    }
}