<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function themler_shortcode_single_row($atts, $content='', $tag='') {
    global $is_column_in_row;
    $is_column_in_row = true;
    $result = do_shortcode($content);
    $is_column_in_row = false;
    return ShortcodesUtility::makeShortcode($tag, $result, $atts);
}

function themler_shortcode_single_column($atts, $content='', $tag='') {
    switch($tag) {
        case 'one_half':
            $atts['width'] = "12";
            break;
        case 'one_third':
            $atts['width'] = "8";
            break;
        case 'two_third':
            $atts['width'] = "16";
            break;
        case 'one_fourth':
            $atts['width'] = "6";
            break;
        case 'three_fourth':
            $atts['width'] = "18";
            break;
        case 'full_width':
            $atts['width'] = "24";
            break;
    }

    global $is_column_in_row;
    if (isset($is_column_in_row) && $is_column_in_row)
        return ShortcodesUtility::makeShortcode($tag, $content, $atts);

    if (!is_array($atts)) {
        $atts = array();
    }
    $last = isset($atts['last']) ? $atts['last'] : false;

    $new_atts = array();
    foreach($atts as $key => $value) {
        if (is_numeric($key) && 'last' === $value)
            $last = true;
        else
            $new_atts[$key] = $value;
    }

    $row_atts = 'vertical_align="' . (isset($atts['vertical_align']) ? $atts['vertical_align'] : '') . '"' .
        ' auto_height="' . (isset($atts['auto_height']) ? $atts['auto_height'] : '') . '"' .
        ' collapse_spacing="' . (isset($atts['collapse_spacing']) ? $atts['collapse_spacing'] : '') . '"';

    remove_shortcode($tag, 'themler_shortcode_single_column');
    $content = '<!--Column--><' . $row_atts . '>' . ShortcodesUtility::makeShortcode('column', do_shortcode($content), $new_atts) . '<!--/Column' . ($last ? 'Last' : '') . '-->';
    add_shortcode($tag, 'themler_shortcode_single_column');
    return $content;
}

global $old_rows_stack, $old_columns_atts;
$old_rows_stack = array();



function themler_old_column_shortcode_collect_width($atts, $content, $tag) {
    global $old_rows_stack;
    if (count($old_rows_stack) > 0) {
        $old_rows_stack[count($old_rows_stack) - 1][] = $atts;
    }
    return '';
}

function themler_old_column_shortcode_set_width($atts, $content, $tag) {
    global $old_row_data;
    $result = ShortcodesUtility::makeShortcode($tag, $content, $old_row_data[0]);
    $old_row_data = array_splice($old_row_data, 1);
    return $result;
}

function themler_old_row_shortcode($atts, $content, $tag) {
    global $old_rows_stack, $old_row_data;

    $old_rows_stack[] = array();

    themler_save_original_shortcodes(array('#^column(_\d+)?$#' => 'themler_old_column_shortcode_collect_width'));
    do_shortcode($content);
    themler_restore_original_shortcodes();

    $old_row_data = array_pop($old_rows_stack);
    $items = &$old_row_data;

    $supports = get_theme_support('themler-core');
    if (is_array($supports) && in_array(array('grid-columns-12'), $supports)) {
        foreach (array('width', 'width_sm', 'width_lg', 'width_xs') as $prop) {

            $sum = 0;
            $odds = array();
            $evens = array();
            $difference = 0;

            $len = count($items);

            for ($i = 0; $i < $len; $i++) {
                if (!isset($items[$i][$prop])) {
                    continue;
                }

                $value = intval($items[$i][$prop]);

                if ($value) {
                    if (!isset($max_value_index) || $value > intval($items[$max_value_index][$prop])) {
                        $max_value_index = $i;
                    }

                    if ($value % 2 === 0) {
                        $evens[$i] = $value;
                    } else {
                        $odds[$i] = $value;
                    }

                    $sum += intval($items[$i][$prop]);
                }
            }

            if ($sum && isset($max_value_index)) {
                for ($i = 0; $i < $len; $i++) {
                    if (!isset($items[$i][$prop])) {
                        continue;
                    }

                    $value = intval($items[$i][$prop]);

                    if (isset($odds[$i])) {
                        if ($odds[$i] === 1) {
                            $items[$i][$prop] = 1;
                            $difference -= 0.5;
                        } else {
                            $items[$i][$prop] = floor($value / 2);
                            $difference += 0.5;
                        }
                    } else if (isset($evens[$i])) {
                        $items[$i][$prop] = $value / 2;
                    }
                }

                if ($difference > 0 && intval($difference) == $difference) {
                    $items[$max_value_index][$prop] = intval($items[$max_value_index][$prop]) + $difference;
                }
            }
        }
    }

    themler_save_original_shortcodes(array('#^column(_\d+)?$#' => 'themler_old_column_shortcode_set_width'));
    $content = do_shortcode($content);
    themler_restore_original_shortcodes();

    return ShortcodesUtility::makeShortcode(str_replace('row', 'columns', $tag), do_shortcode($content), $atts);
}

function themler_save_original_shortcodes($funcs) {
    global $themler_shortcodes_stack, $shortcode_tags;

    $themler_shortcodes_stack[] = $shortcode_tags; // save original shortcodes
    $shortcode_tags = array();

    foreach(array_merge($themler_shortcodes_stack[count($themler_shortcodes_stack) - 1], ShortcodesUtility::$shortcodes) as $tag => $func) {
        foreach($funcs as $regex => $fn) {
            if (preg_match($regex, $tag)) {
                add_shortcode($tag, $fn);
                break;
            }
        }
    }
}

function themler_restore_original_shortcodes() {
    global $themler_shortcodes_stack, $shortcode_tags;
    $shortcode_tags = array_pop($themler_shortcodes_stack); // restore original shortcodes
}

function themler_render_shortcode($content, $tag, $func) {
    themler_save_original_shortcodes(array(
        "#^$tag$#" => $func
    ));
    $ret = do_shortcode($content);
    themler_restore_original_shortcodes();
    return $ret;
}

function themler_old_row_filter($content) {
    if (!ShortcodesUtility::$enable_filters)
        return $content;

    $add_shortcodes = array();
    foreach(ShortcodesUtility::$shortcodes as $tag => $func) {
        if (preg_match('#^row(_\d+)?$#', $tag))
            $add_shortcodes[str_replace('row', 'columns', $tag)] = $func;
        if (preg_match('#^columns(_\d+)?$#', $tag))
            $add_shortcodes[str_replace('columns', 'row', $tag)] = $func;
    }
    ShortcodesUtility::$shortcodes = array_merge(ShortcodesUtility::$shortcodes, $add_shortcodes);

    themler_save_original_shortcodes(array('#^row(_\d+)?$#' => 'themler_old_row_shortcode'));
    $content = do_shortcode($content);
    themler_restore_original_shortcodes();

    return $content;
}

function themler_column_filter($content) {
    if (!ShortcodesUtility::$enable_filters)
        return $content;


    themler_save_original_shortcodes(array(
        '#^column(_\d+)?$#' => 'themler_shortcode_single_column',
        '#^row(_\d+)?$#' => 'themler_shortcode_single_row',
        '#^columns(_\d+)?$#' => 'themler_shortcode_single_row',
    ));
    add_shortcode('one_half', 'themler_shortcode_single_column');
    add_shortcode('one_third', 'themler_shortcode_single_column');
    add_shortcode('two_third', 'themler_shortcode_single_column');
    add_shortcode('one_fourth', 'themler_shortcode_single_column');
    add_shortcode('three_fourth', 'themler_shortcode_single_column');
    add_shortcode('full_width', 'themler_shortcode_single_column');

    $content = do_shortcode($content);
    themler_restore_original_shortcodes();

    $content = preg_replace('/(<!--\/Column)(?:Last){0,1}(-->)(?!.*<!--\/Column)/s', '$1Last$2', $content, 1); // add 'last' for the last column
    $GLOBALS['inRow'] = false;
    return preg_replace_callback('/<!--Column--><([^>]*?)>(.*?)<!--\/Column(Last){0,1}-->/s', 'themler_column_filter_callback', $content);
}

function themler_column_filter_callback($matches) {
    $result = '';
    if (!$GLOBALS['inRow']) {
        $result .= '[row ' . $matches[1] . ']';
        $GLOBALS['inRow'] = true;
    }
    $result .= $matches[2];
    if (isset($matches[3])) {
        $result .= '[/row]';
        $GLOBALS['inRow'] = false;
    }
    return $result;
}

// [box css="" full_width="yes|no" content_width="yes|no"]content with shortcodes[/box]
function themler_shortcode_box($atts, $content = '') {
    $atts = shortcode_atts(array(
        'css' => '',
        'content_width' => 'yes',
        'class_names' => ''
    ), $atts);

    $css = esc_attr($atts['css']);
    $content_width = $atts['content_width'] === 'yes';
    $class_names = esc_attr($atts['class_names']);

    $result = '<div';
    if ($class_names !== '') {
        $result .= ' class="' . $class_names . '"';
    }
    if ($css !== '') {
        $result .= ' style="' . $css . '"';
    }
    $result .= '>';
    if ($content_width) {
        $result .= '<div class="bd-container-inner">';
    }
    $result .= do_shortcode($content);
    if ($content_width) {
        $result .= '</div>';
    }
    $result .= '</div>';
    return $result;
}
add_shortcode('box', 'themler_shortcode_box');