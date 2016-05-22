<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('THEME_NAME', "Billion Themler theme");
global $wp_version;
define('WP_VERSION', $wp_version);
define('THEME_NS', 'default');
define('THEME_LANGS_FOLDER', '/languages');
header('X-UA-Compatible: IE=edge');

remove_action('wp_head', 'wp_generator');

load_theme_textdomain(THEME_NS, get_template_directory() . THEME_LANGS_FOLDER);

if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding(get_bloginfo('charset'));
}
if (function_exists('mb_regex_encoding')) {
	mb_regex_encoding(get_bloginfo('charset'));
}

function theme_is_customizer() {
    return isset($_GET['wp_customize']) && isset($_GET['theme']);
}

function theme_is_preview() {
    return isset($_GET['preview']) && isset($_GET['template'])
    || theme_is_customizer(); // since 4.3
}

function theme_can_view_preview() {
    return theme_is_preview() && current_user_can('switch_themes');
}

function theme_is_export_action() {
    if (!current_user_can('switch_themes') || !isset($_REQUEST['action'])) {
        return false;
    }
    return strpos($_REQUEST['action'], 'theme_') === 0;
}

function theme_woocommerce_enabled() {
    global $woocommerce;
    return $woocommerce != null;
}

theme_include_lib('logging.php', 'export');
theme_include_lib('defaults.php');
theme_include_lib('misc.php');
theme_include_lib('wrappers.php');
theme_include_lib('sidebars.php');
theme_include_lib('navigation.php');
if (!class_exists('ShortcodesUtility')) {
    if (theme_can_view_preview()) {
        theme_include_lib('shortcodes.php', 'export/themler-core/shortcodes');
    } else if (theme_is_export_action()) {
        theme_include_lib('shortcodes.php', '../' . get_template() . '_preview/export/themler-core/shortcodes');
    }
}
theme_include_lib('shortcodes-styles.php');
theme_include_lib('widgets.php');
theme_include_lib('rating.php');
theme_include_lib('post_templates.php');
theme_include_lib('class-tgm-plugin-activation.php');

if (get_option('theme_show_comments_anywhere')) {
    global $withcomments;
    $withcomments = true;
}

add_action( 'wp_enqueue_scripts', 'theme_manage_woocommerce_styles', 99 );
function theme_manage_woocommerce_styles() {
	remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
    wp_dequeue_script( 'wc-single-product' );
}
add_action('wp_enqueue_scripts', 'theme_update_scripts_and_styles', 1000);
add_action('wp_head', 'theme_update_posts_styles', 1000);
add_action('wp_head', 'theme_header_image_script');
add_action('media_upload_image_header', 'wp_media_upload_handler');

function theme_header_rel_link() {
	if (theme_get_option('theme_header_clickable')):
		?><link rel='header_link' href='<?php echo esc_url(theme_get_option('theme_header_link')); ?>' /><?php
	endif;
}
add_action('wp_head', 'theme_header_rel_link');
add_action('login_head', 'theme_header_rel_link');

add_action('init', 'theme_editor_auto_login');
add_action('wp',   'theme_change_cart_page_template');

add_filter('locale', 'set_new_locale');

add_theme_support('post-thumbnails');
add_theme_support('nav-menus');
add_theme_support('automatic-feed-links');
add_theme_support('post-formats', array('aside', 'gallery'));
add_theme_support('woocommerce');
add_theme_support('title-tag');
add_theme_support('themler-core', array('grid-columns-12'));

if ( ! function_exists( '_wp_render_title_tag' ) ) {
    function theme_slug_render_title() {
        ?>
        <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php
    }
    add_action( 'wp_head', 'theme_slug_render_title' );

    function theme_wp_title( $title, $sep ) {
        global $paged, $page;
        if ( is_feed() ) {
            return $title;
        }
        if ( $paged >= 2 || $page >= 2 ) {
            $title = "$title $sep " . sprintf( __( 'Page %s', THEME_NS ), max( $paged, $page ) );
        }
        return $title;
    }
    add_filter( 'wp_title', 'theme_wp_title', 10, 2 );
}



function theme_autoinclude($folder){
    $path = get_stylesheet_directory() . '/' . $folder;

    if (!is_dir($path)) {
        return ;
    }

    if ($handle = opendir($path)) {
        while (($name = readdir($handle)) !== false) {
            if (!preg_match("#.php$#", $name)) {
                continue;
            }
            locate_template(array($folder . '/' . $name), true);
        }
        closedir($handle);
    }
}

theme_autoinclude('includes');

global $theme_error_messages;
$theme_error_messages = array();

function theme_add_error($message) {
    global $theme_error_messages;
    $theme_error_messages[] = $message;
}

function theme_add_errors() {
    global $theme_error_messages;
    foreach($theme_error_messages as $message) {
        echo '<div class="error"><p>' . $message . '</p></div>';
    }
}
add_action('admin_notices', 'theme_add_errors');

function theme_header_image_script() {
	$theme_header_image = theme_get_meta_option(get_queried_object_id(), 'theme_header_image');
	if ($theme_header_image) {
		?>
		<style>
			.bd-header {
				background-image : url(<?php echo $theme_header_image; ?>) !important;
				background-position : center center !important;
			}
		</style>
		<?php
	}
}

function theme_has_header_image() {
	return (bool) theme_get_meta_option(get_queried_object_id(), 'theme_header_image');
}
function theme_show_flash() {
	return (bool) theme_get_meta_option(get_queried_object_id(), 'theme_header_image_with_flash');
}

function theme_wc_get_product_schema() {
    if (function_exists('woocommerce_get_product_schema'))
        return woocommerce_get_product_schema();
    return 'http://schema.org/Product';
}

function theme_get_wc_template_path() {
    global $woocommerce;
    $template_path = method_exists($woocommerce, 'template_path') ? $woocommerce->template_path() : $woocommerce->template_url;
    return get_stylesheet_directory() . '/' . $template_path;
}

function theme_set_wc_template_path($located, $template_name) {
    // support old WC versions
    if ($template_name === 'cart/totals.php' || $template_name === 'cart/cart-totals.php') {
        $located = theme_get_wc_template_path() . 'cart/cart-totals-wrapper.php';
    }
    return $located;
}
add_filter('woocommerce_locate_template', 'theme_set_wc_template_path', 10, 2);

function theme_add_to_cart_message($message) {
    return str_replace('button wc-forward', '', $message);
}
add_filter('woocommerce_add_error', 'theme_add_to_cart_message');
add_filter('woocommerce_add_success', 'theme_add_to_cart_message');

function theme_add_preview_args($link) {
    if (theme_is_customizer()) {
        $theme = isset($_GET['theme']) ? $_GET['theme'] : '';
        $nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';
        $original = isset($_GET['original']) ? $_GET['original'] : '';
        return add_query_arg(array('preview' => 1, 'theme' => $theme, 'wp_customize' => 'on', 'nonce' => $nonce, 'original' => $original), $link);
    } else {
        $template = isset($_GET['template']) ? $_GET['template'] : '';
        $stylesheet = isset($_GET['stylesheet']) ? $_GET['stylesheet'] : '';
        return add_query_arg(array('preview' => 1, 'template' => $template, 'stylesheet' => $stylesheet, 'preview_iframe' => 1), $link);
    }
}

function theme_remove_preview_args($link) {
    return remove_query_arg(array(
        'preview', 'preview_iframe', 'template', 'stylesheet', 'page', 'TB_iframe', // preview-theme
        'original', 'theme', 'wp_customize', 'nonce', // wp-customize
        'custom_template', 'custom_page', 'default' // custom templates
    ), $link);
}
add_filter('themler_remove_preview_args', 'theme_remove_preview_args');

function theme_woocommerce_params($params) {
    if (isset($params['ajax_url']))
        $params['ajax_url'] = theme_add_preview_args($params['ajax_url']);
    if (isset($params['checkout_url']))
        $params['checkout_url'] = theme_add_preview_args($params['checkout_url']);
    if (isset($params['redirect']))
        $params['redirect'] = theme_add_preview_args($params['redirect']);
    return $params;
}

function theme_review_form_params($params) {
    $params['i18n_required_rating_text'] = esc_attr__('Please select a rating', 'woocommerce');
    $params['i18n_required_comment_text'] = esc_attr__('Please type a comment', 'woocommerce');
    $params['review_rating_required'] = get_option('woocommerce_review_rating_required');
    return $params;
}
add_filter('woocommerce_params', 'theme_review_form_params');

if (theme_can_view_preview()) {
    add_filter('woocommerce_params', 'theme_woocommerce_params');
    add_filter('wc_checkout_params', 'theme_woocommerce_params');
    add_filter('wc_cart_params', 'theme_woocommerce_params');
    add_filter('wc_cart_fragments_params', 'theme_woocommerce_params');
    add_filter('wc_add_to_cart_params', 'theme_woocommerce_params');
    add_filter('woocommerce_payment_successful_result', 'theme_woocommerce_params');
}

if (!theme_is_customizer()) {
    theme_include_lib('theme-customizer.php', 'export');
}

function theme_register_thumbnail_size() {
    $width = absint(theme_get_option('theme_metadata_thumbnail_width'));
    $height = absint(theme_get_option('theme_metadata_thumbnail_height'));
    if ($width && $height) {
        foreach(array('thumbnail', 'medium', 'large') as $_size) {
            $w = absint(get_option($_size . '_size_w'));
            if (abs($w - $width) <= 100)
                return;
        }
        add_image_size('post_image_thumbnail', $width, $height);
    }
}
add_action('after_setup_theme', 'theme_register_thumbnail_size');

function theme_sidebars_compare($sidebar1, $sidebar2) {

    $priority1 = theme_get_array_value($sidebar1, 'priority', 10000);
    $priority2 = theme_get_array_value($sidebar2, 'priority', 10000);
    if ($priority1 !== $priority2) {
        return $priority1 < $priority2 ? -1 : 1;
    }

    $name1 = theme_get_array_value($sidebar1, 'name', '');
    $name2 = theme_get_array_value($sidebar2, 'name', '');
    return strnatcmp($name1, $name2);
}
uasort($wp_registered_sidebars, 'theme_sidebars_compare');


function theme_register_required_plugins() {

    $plugins = array(
        array(
            'name'               => 'Themler-Core', // The plugin name.
            'slug'               => 'themler-core', // The plugin slug (typically the folder name).
            'source'             => get_template_directory() . '/plugins/themler-core.zip', // The plugin source.
            'required'           => false, // If false, the plugin is only 'recommended' instead of required.
            'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
            'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
            'external_url'       => '', // If set, overrides default API URL and points to an external URL.
            'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        )
    );

    $config = array(
        'id'           => 'tgmpa-themler',         // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'themes.php',            // Parent menu slug.
        'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );
    tgmpa($plugins, $config);
}
add_action('tgmpa_register', 'theme_register_required_plugins');


if (is_admin()) {
	theme_include_lib('options.php');
	theme_include_lib('admins.php');
    theme_include_lib('export.php', 'export');
	function theme_add_option_page() {
		add_theme_page(__('Theme Options', THEME_NS), __('Theme Options', THEME_NS), 'edit_themes', basename(__FILE__), 'theme_print_options');
	}

	add_action('admin_menu', 'theme_add_option_page');
	add_action('sidebar_admin_setup', 'theme_widget_process_control');
	add_filter('widget_update_callback', 'theme_update_widget_additional');
	add_action('add_meta_boxes', 'theme_add_meta_boxes');
	add_action('save_post', 'theme_save_post');

    theme_include_lib('content-importer.php', 'content');
    theme_include_lib('converter.php', 'export');
	return;
}

function theme_remove_customize_preview_signature() {
    global $wp_customize;
    if (isset($wp_customize) && method_exists($wp_customize, 'remove_preview_signature')) {
        remove_action('wp_redirect_status', array($wp_customize, 'wp_redirect_status'), 1000 );

        // wrap WP_CUSTOMIZER_SIGNATURE in html-comments
        $wp_customize->remove_preview_signature();
        add_action('shutdown', create_function('', 'echo "<!-- WP_CUSTOMIZER_SIGNATURE -->";'), 1000);
    }
}
add_action('customize_preview_init', 'theme_remove_customize_preview_signature');

if (theme_can_view_preview() && theme_is_customizer() && isset($_REQUEST['original'])) {
    $original_theme = $_REQUEST['original'];
    $preview_theme = $_REQUEST['theme'];

    $original_func = create_function('$a', "return '$original_theme';");
    $preview_func = create_function('$a', "return '$preview_theme';");

    add_filter('pre_option_template', $original_func);
    add_filter('template', $preview_func);

    add_filter('pre_option_stylesheet', $original_func);
    add_filter('stylesheet', $preview_func);
}

// remove widgets overriding by wp-customizer
global $wp_customize;
if (isset($wp_customize) && property_exists($wp_customize, 'widgets')) {
    remove_action('wp_loaded', array($wp_customize->widgets, 'override_sidebars_widgets_for_theme_switch'));
}

function theme_is_preview_url($location) {
    if (strpos($location, 'wp_customize=') !== false && strpos($location, 'theme=') !== false) {
        return true;
    }
    if (strpos($location, 'preview=') !== false && strpos($location, 'template=') !== false) {
        return true;
    }
    return false;
}

function theme_fix_woocommerce_redirect($location) {
	if (!theme_is_preview()) {
		return $location;
    }

	if (!theme_is_preview_url($location)) {
        return theme_add_preview_args($location);
	}
	return $location;
}
add_filter('wp_redirect', 'theme_fix_woocommerce_redirect', 100);

function theme_fix_preview_redirect($redirect_url) {
    if (theme_is_preview()) {
        $new_redirect_url = add_query_arg('preview', '1', $redirect_url);
        if (theme_is_preview_url($new_redirect_url)) {
            $redirect_url = $new_redirect_url;
            //$redirect_url = '';
        }
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'theme_fix_preview_redirect');

function theme_update_posts_styles() {
	global $wp_query;
	$res = '';
	if(!is_singular()) {
		$post_id = get_queried_object_id();
		if ($post_id == 0 && theme_is_home()) {
            $post_id = get_option('page_for_posts');
        }
		$res .= get_post_meta($post_id, 'theme_head', true);
	}
	while ($wp_query->have_posts()) {
		the_post();
		$post_id = theme_get_the_ID();
		$res .= get_post_meta($post_id, 'theme_head', true);
	}
	if (!empty($res)) {
		echo $res;
	}
	wp_reset_postdata();
}

function theme_get_option($name) {
	global $theme_default_options;
	$result = get_option($name);
	if ($result === false) {
		$result = theme_get_array_value($theme_default_options, $name);
	}
	return $result;
}

function theme_get_widget_meta_option($widget_id, $name) {
	global $theme_default_meta_options;
	if (!preg_match('/^(.*[^-])-([0-9]+)$/', $widget_id, $matches) || !isset($matches[1]) || !isset($matches[2])) {
		return theme_get_array_value($theme_default_meta_options, $name);
	}
	$type = $matches[1];
	$id = $matches[2];
	$wp_widget = get_option('widget_' . $type);
	if (!$wp_widget || !isset($wp_widget[$id])) {
		return theme_get_array_value($theme_default_meta_options, $name);
	}
	if (!isset($wp_widget[$id][$name])) {
		$wp_widget[$id][$name] = theme_get_array_value(get_option($name), $widget_id, theme_get_array_value($theme_default_meta_options, $name));
	}
	return $wp_widget[$id][$name];
}

function theme_set_widget_meta_option($widget_id, $name, $value) {
	if (!preg_match('/^(.*[^-])-([0-9]+)$/', $widget_id, $matches) || !isset($matches[1]) || !isset($matches[2])) {
		return;
	}
	$type = $matches[1];
	$id = $matches[2];
	$wp_widget = get_option('widget_' . $type);
	if (!$wp_widget || !isset($wp_widget[$id])) {
		return;
	}
	$wp_widget[$id][$name] = $value;
	update_option('widget_' . $type, $wp_widget);
}

function theme_get_meta_option($id, $name) {
	global $theme_default_meta_options;
	if (!is_numeric($id)) {
        return theme_get_array_value($theme_default_meta_options, $name);
    }
	$value = get_post_meta($id, '_' . $name, true);
	if ('' === $value) {
		$value = theme_get_array_value(get_option($name), $id, theme_get_array_value($theme_default_meta_options, $name));
		theme_set_meta_option($id, $name, $value);
	}
	return $value;
}

function theme_set_meta_option($id, $name, $value) {
	update_post_meta($id, '_' . $name, $value);
}

function theme_get_post_id() {
	$post_id = theme_get_the_ID();
	if ($post_id != '') {
		$post_id = 'post-' . $post_id;
	}
	return $post_id;
}

function theme_get_the_ID() {
	global $post;
	return $post->ID;
}

function theme_get_post_class() {
	return implode(' ', get_post_class());
}

function theme_include_lib($name, $dir = 'library') {
	locate_template(array($dir . '/' . $name), true);
}

function theme_get_post_thumbnail($args = array()) {
	global $post;

	$size = theme_get_array_value($args, 'size', array(theme_get_option('theme_metadata_thumbnail_width'), theme_get_option('theme_metadata_thumbnail_height')));
	$auto = theme_get_array_value($args, 'auto', theme_get_option('theme_metadata_thumbnail_auto'));
	$featured = theme_get_array_value($args, 'featured', theme_get_option('theme_metadata_use_featured_image_as_thumbnail'));
	$title = esc_attr(theme_get_array_value($args, 'title', get_the_title($post) ));
    $img_class = esc_attr(theme_get_array_value($args, 'img_class', ''));
    $link_class = esc_attr(theme_get_array_value($args, 'class', ''));
    $img_attributes = esc_attr(theme_get_array_value($args, 'img_attributes', ''));
    $attributes = esc_attr(theme_get_array_value($args, 'attributes', ''));

	$result = '';

	if ($featured && (has_post_thumbnail())) {
		theme_ob_start();
		the_post_thumbnail($size, array('alt' => $title, 'title' => $title, 'class' => $img_class));
		$result = theme_ob_get_clean();
	} elseif ($auto) {
		$attachments = get_children(array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));
		if ($attachments) {
			$attachment = array_shift($attachments);
			$img = wp_get_attachment_image_src($attachment->ID, $size);
			if (isset($img[0])) {
				$result = '<img src="' . $img[0] . '" alt="' . $title . '" width="' . $img[1] . '" height="' . $img[2] . '" title="' . $title . '" class="'. $img_class .'" />';
			}
		}
	}
    $result = str_replace('<img', '<img ' . $img_attributes, $result);
	if ($result !== '') {
		$result = '<a href="' . get_permalink($post->ID) . '" title="' . $title . '" class="'. $link_class . '" ' . $attributes .'>' . $result . '</a>';
	}
	return $result;
}

function theme_get_content($args = array()) {
    $post_id = get_queried_object_id();
    $more_tag = theme_get_array_value($args, 'more_tag', __('Continue reading <span class="meta-nav">&rarr;</span>', THEME_NS));

    $ignore_wpautop = theme_get_meta_option($post_id, 'theme_use_wpautop') === '0';
    if ($ignore_wpautop) {
        remove_filter('the_content', 'wpautop');
    }
    theme_ob_start();
    the_content($more_tag);
    $content = theme_ob_get_clean();
    if ($ignore_wpautop) {
        add_filter('the_content', 'wpautop');
    }
    return $content . wp_link_pages(array(
        'before' => '<p><span class="page-navi-outer page-navi-caption"><span class="page-navi-inner">' . __('Pages', THEME_NS) . ': </span></span>',
        'after' => '</p>',
        'link_before' => '<span class="page-navi-outer"><span class="page-navi-inner">',
        'link_after' => '</span></span>',
        'echo' => 0
    ));
}

define('THEME_MORE_TOKEN', '%%theme_more%%');
define('THEME_TAG_TOKEN', '%%theme_tag_token%%');
define('THEME_TOKEN_TYPE_WORD', 0);
define('THEME_TOKEN_TYPE_TAG', 1);
define('THEME_TOKEN_TYPE_SPACE', 2);
define('THEME_TOKEN_TYPE_IGNORE', 3);

if (theme_get_option('theme_metadata_excerpt_strip_shortcodes')) {
    add_filter('get_the_excerpt', 'strip_shortcodes');
}

function theme_create_excerpt($excerpt, $max_tokens_count, $min_remainder, $count_symbols = false) {
    $content_parts = explode(THEME_TAG_TOKEN, str_replace(array('<', '>'), array(THEME_TAG_TOKEN . '<', '>' . THEME_TAG_TOKEN), $excerpt));
    $content = array();
    $tokens_count = 0;
    $style_balance = 0;
    $script_balance = 0;
    foreach ($content_parts as $part) {
        if (theme_strpos($part, '<') !== false || theme_strpos($part, '>') !== false) {
            if ($part === '<style>') {
                $style_balance++;
            } else if ($part === '</style>') {
                $style_balance--;
            } else if ($part === '<script>') {
                $script_balance++;
            } else if ($part === '</script>') {
                $script_balance--;
            }
            $content[] = array(THEME_TOKEN_TYPE_TAG, $part);
        } else {
            $all_chunks = preg_split('/([\s])/u', $part, -1, PREG_SPLIT_DELIM_CAPTURE);
            foreach ($all_chunks as $chunk) {
                if ('' != trim($chunk)) {
                    if ($style_balance > 0 || $script_balance > 0) {
                        $content[] = array(THEME_TOKEN_TYPE_IGNORE, $chunk);
                    } else {
                        $content[] = array(THEME_TOKEN_TYPE_WORD, $chunk);
                        $tokens_count += $count_symbols ? theme_strlen($chunk) : 1;
                    }
                } elseif ($chunk != '') {
                    $content[] = array(THEME_TOKEN_TYPE_SPACE, $chunk);
                }
            }
        }
    }

    if ($max_tokens_count < $tokens_count && $max_tokens_count + $min_remainder <= $tokens_count) {
        $current_count = 0;
        $excerpt = '';
        foreach ($content as $node) {
            if ($node[0] === THEME_TOKEN_TYPE_WORD) {
                $current_count += $count_symbols ? theme_strlen($node[1]) : 1;
            }
            $excerpt .= $node[1];
            if ($current_count >= $max_tokens_count) {
                break;
            }
        }
        return $excerpt;
    }
    return false;
}

function theme_get_excerpt($args = array()) {
    global $post;
    $more_tag = theme_get_array_value($args, 'more_tag', __('Continue reading <span class="meta-nav">&rarr;</span>', THEME_NS));
    $auto = theme_get_array_value($args, 'auto', theme_get_option('theme_metadata_excerpt_auto'));
    $all_words = theme_get_array_value($args, 'all_words', theme_get_option('theme_metadata_excerpt_words'));
    $min_remainder = theme_get_array_value($args, 'min_remainder', theme_get_option('theme_metadata_excerpt_min_remainder'));
    $allowed_tags = theme_get_array_value($args, 'allowed_tags',
        (theme_get_option('theme_metadata_excerpt_use_tag_filter')
            ? explode(',',str_replace(' ', '', theme_get_option('theme_metadata_excerpt_allowed_tags')))
            : null));
    $perma_link = get_permalink($post->ID);
    $show_more_tag = false;
    $tag_disbalance = false;
    if (post_password_required($post)) {
        return get_the_excerpt();
    }
    if ($auto && has_excerpt($post->ID)) {
        $excerpt = get_the_excerpt();
        $show_more_tag = theme_strlen($post->post_content) > 0;
    } else {
        $excerpt = get_the_content(THEME_MORE_TOKEN);
        if (theme_get_option('theme_metadata_excerpt_strip_shortcodes')) {
            $excerpt = strip_shortcodes($excerpt);
        }
        // hack for badly written plugins
        theme_ob_start();
        echo apply_filters('the_content', $excerpt);
        $excerpt = theme_ob_get_clean();
        global $multipage;
        if ($multipage && theme_strpos($excerpt, THEME_MORE_TOKEN) === false) {
            $show_more_tag = true;
        }
        if (theme_is_empty_html($excerpt))
            return $excerpt;
        if ($allowed_tags !== null) {
            $allowed_tags = '<' . implode('><', $allowed_tags) . '>';
            $excerpt = strip_tags($excerpt, $allowed_tags);
        }
        if (theme_strpos($excerpt, THEME_MORE_TOKEN) !== false) {
            $excerpt = str_replace(THEME_MORE_TOKEN, '', $excerpt);
            $show_more_tag = true;
        } elseif ($auto && is_numeric($all_words)) {
            $all_words = intval($all_words);
            $min_remainder = intval($min_remainder);

            $new_excerpt = theme_create_excerpt($excerpt, $all_words, $min_remainder);
            if (is_string($new_excerpt)) {
                $excerpt = $new_excerpt;
                $show_more_tag = true;
                $tag_disbalance = true;
            }
        }
    }
    if ($show_more_tag && theme_get_option('theme_show_morelink')) {
        $excerpt = $excerpt . ' ' . str_replace(array('[url]', '[text]'), array($perma_link, $more_tag), theme_get_option('theme_morelink_template'));
    }
    if ($tag_disbalance) {
        $excerpt = force_balance_tags($excerpt);
    }
    return $excerpt;
}

function theme_get_search() {
	theme_ob_start();
	get_search_form();
	return theme_ob_get_clean();
}

function theme_is_home() {
	return (is_home() && !is_paged());
}

function theme_404_content($args = '') {
    $args = wp_parse_args($args, array(
            'error_title' => __('Not Found', THEME_NS),
            'error_message' => __('Apologies, but the page you requested could not be found. Perhaps searching will help.', THEME_NS)
        )
    );
    extract($args);
    echo '<h4>' . $error_title . '</h4>';
    echo '<p class="center">' . $error_message . '</p>';

    if (theme_get_option('theme_show_random_posts_on_404_page')) {
        theme_ob_start();
        echo '<h4 class="box-title">' . theme_get_option('theme_show_random_posts_title_on_404_page') . '</h4>';
        ?>
        <ul>
            <?php
            global $post;
            $rand_posts = get_posts('numberposts=5&orderby=rand');
            foreach ($rand_posts as $post) :
                ?>
                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
        echo theme_ob_get_clean();
    }
    if (theme_get_option('theme_show_tags_on_404_page')) {
        theme_ob_start();
        echo '<h4 class="box-title">' . theme_get_option('theme_show_tags_title_on_404_page') . '</h4>';
        wp_tag_cloud('smallest=9&largest=22&unit=pt&number=200&format=flat&orderby=name&order=ASC');
        echo theme_ob_get_clean();
    }
}

function theme_get_previous_post_link($format = '&laquo; %link', $link = '%title', $in_same_cat = false, $excluded_categories = '') {
	return theme_get_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, true);
}

function theme_get_next_post_link($format = '%link &raquo;', $link = '%title', $in_same_cat = false, $excluded_categories = '') {
	return theme_get_adjacent_post_link($format, $link, $in_same_cat, $excluded_categories, false);
}

function theme_get_adjacent_image_link($prev = true, $size = 'thumbnail', $text = false) {
	global $post;
	$post = get_post($post);
	$attachments = array_values(get_children(array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID')));

	foreach ($attachments as $k => $attachment)
		if ($attachment->ID == $post->ID)
			break;

	$k = $prev ? $k - 1 : $k + 1;

	if (isset($attachments[$k]))
		return wp_get_attachment_link($attachments[$k]->ID, $size, true, false, $text);
}

function theme_get_previous_image_link($size = 'thumbnail', $text = false) {
	$result = theme_get_adjacent_image_link(true, $size, $text);
	if ($result)
		$result = '&laquo; ' . $result;
	return $result;
}

function theme_get_next_image_link($size = 'thumbnail', $text = false) {
	$result = theme_get_adjacent_image_link(false, $size, $text);
	if ($result)
		$result .= ' &raquo;';
	return $result;
}

function theme_get_adjacent_post_link($format, $link, $in_same_cat = false, $excluded_categories = '', $previous = true) {
	if ($previous && is_attachment())
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

	if (!$post)
		return;

	$title = strip_tags($post->post_title);

	if (empty($post->post_title))
		$title = $previous ? __('Previous Post', THEME_NS) : __('Next Post', THEME_NS);

	$title = apply_filters('the_title', $title, $post->ID);
	$short_title = $title;
	if (theme_get_option('theme_single_navigation_trim_title')) {
		$short_title = theme_trim_long_str($title, theme_get_option('theme_single_navigation_trim_len'));
	}
	$date = mysql2date(get_option('date_format'), $post->post_date);
	$rel = $previous ? 'prev' : 'next';

	$string = '<a href="' . get_permalink($post) . '" title="' . esc_attr($title) . '" rel="' . $rel . '">';
	$link = str_replace('%title', $short_title, $link);
	$link = str_replace('%date', $date, $link);
	$link = $string . $link . '</a>';

	$format = str_replace('%link', $link, $format);

	$adjacent = $previous ? 'previous' : 'next';
	return apply_filters("{$adjacent}_post_link", $format, $link);
}

function theme_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;

    switch ($comment->comment_type) {

    case '' :
?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <?php theme_ob_start(); ?>
            <div class="comment-author vcard">
                <?php echo theme_get_avatar(array('id' => $comment, 'size' => 48)); ?>
                <?php printf(__('%s <span class="says">says:</span>', THEME_NS), sprintf('<cite class="fn">%s</cite>', get_comment_author_link())); ?>
            </div>
            <?php if ($comment->comment_approved == '0') : ?>
            <em><?php _e('Your comment is awaiting moderation.', THEME_NS); ?></em>
            <br />
            <?php endif; ?>

            <div class="comment-meta commentmetadata"><a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                <?php printf(__('%1$s at %2$s', THEME_NS), get_comment_date(), get_comment_time()); ?></a><?php edit_comment_link(__('(Edit)', THEME_NS), ' '); ?>
            </div>

            <div class="comment-body"><?php comment_text(); ?></div>

            <div class="reply">
                <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
            </div>
            <?php echo '<div id="comment-'.get_comment_ID().'">' . theme_ob_get_clean() . '</div>'; ?>
<?php
        break;
    case 'pingback' :
    case 'trackback' :
?>
        <li class="post pingback">
            <?php theme_ob_start(); ?>
            <p><?php _e('Pingback:', THEME_NS); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('(Edit)', THEME_NS), ' '); ?></p>
<?php
            echo '<div class="' . $comment->comment_type . '">' . theme_ob_get_clean() . '</div>';
        break;
    }
}

function theme_replace_attr($subject, $attr, $value) {
    if ($value) {
        return preg_replace("/(" . $attr . "=)\'(.*?)\'/", '$1\'' . $value . '\'', $subject);
    } else {
        return preg_replace("/(" . $attr . "=)\'(.*?)\'/", '', $subject);
    }
}

function theme_get_avatar($args, $remove_size = false) {
	$args = wp_parse_args($args, array(
        'class' => '',
        'attributes' => '',
        'id' => false,
        'size' => 96,
        'default' => '',
        'alt' => false,
        'url' => false
    ));
	$result = get_avatar($args['id'], $args['size'], $args['default'], $args['alt']);
    if ($args['class']) {
        $result = theme_replace_attr($result, 'class', $args['class']);
    }
    if ($remove_size) {
        $result = theme_replace_attr($result, 'height', '');
        $result = theme_replace_attr($result, 'width', '');
    }
    $result = str_replace('<img', '<img ' . $args['attributes'], $result);
	if ($result && $args['url']) {
        $result = '<a href="' . esc_url($args['url']) . '">' . $result . '</a>';
	}
	return $result;
}

function theme_get_next_post() {
	static $ended = false;
	if (!$ended) {
		if (have_posts()) {
			the_post();
			get_template_part('content', get_post_format());
		} else {
			$ended = true;
		}
	}
}

function theme_ob_start() {
	ob_start();
}

function theme_ob_get_clean() {
	return ob_get_clean();
}

function theme_get_path(){
    $template = get_template();
    $theme_root = get_theme_root( $template );
    return $theme_root . '/' . $template;
}

function theme_print_content() {
    global $theme_content_function;
    if (!isset($theme_content_function)) {
        $theme_content_function = 'theme_blog';
    }
    if (function_exists($theme_content_function)) {
        call_user_func($theme_content_function);
        return;
    }
}

function theme_get_image_path($image) {
    $template = get_template();
    $theme_root = get_theme_root($template);
    $template_dir = $theme_root . '/' . $template;
    $image_path = $template_dir . '/' . $image;

    if (theme_is_preview() && !file_exists($image_path)) {
        return $base_image_url = preg_replace('/(.*)(_preview$)/', '$1', get_template_directory_uri()) . '/' . $image;
    }
    return get_template_directory_uri() . '/' . $image;
}
add_filter('theme_image_path', 'theme_get_image_path');

function theme_get_optimal_path($name, $ext){
    $suffix = '.min';
    $file = '/' . $name . '.' . $ext;
    $min_file = '/' . $name . $suffix . '.' . $ext;
    return (isset($_GET['preview']) || !file_exists(theme_get_path() . $min_file) || filemtime(theme_get_path() . $min_file) < filemtime (theme_get_path() . $file))
        ? $file : $min_file;
}

function theme_update_scripts_and_styles() {
    $template_url = str_replace( array( 'http:', 'https:' ), '', get_bloginfo('template_url', 'display'));
    $version = wp_get_theme()->get('Version');
    wp_enqueue_script( 'jquery', false, array(), $version, 'all' );

    wp_register_style( 'theme-bootstrap',  $template_url . theme_get_optimal_path('bootstrap','css'), array(), $version, 'all' );
    wp_enqueue_style("theme-bootstrap");

    if (theme_is_preview() && file_exists(theme_get_path() . '/style.preview.php')) {
        wp_register_style( 'theme-style', $template_url . '/style.preview.php', array('theme-bootstrap'), $version, 'all' );
    } else {
        wp_register_style( 'theme-style', $template_url . theme_get_optimal_path('style','css'), array('theme-bootstrap'), $version, 'all' );
    }
    wp_enqueue_style("theme-style");

    wp_register_script("theme-bootstrap", $template_url . '/bootstrap.min.js', array('jquery'), $version);
    wp_enqueue_script("theme-bootstrap");

    wp_register_script("theme-script", $template_url . '/script.js', array('jquery', 'theme-bootstrap'), $version);
    wp_enqueue_script("theme-script");

    if (theme_is_preview()) {
        wp_register_script("script.preview.js", $template_url . '/script.preview.js', array('jquery'), $version);
        wp_enqueue_script("script.preview.js");
    }

    if (theme_woocommerce_enabled() && is_checkout()) {
        wp_dequeue_script('wc-checkout');
        wp_enqueue_script('wc-checkout', $template_url . '/checkout.min.js', array('jquery'), false, true);
    }

    if (theme_woocommerce_enabled() && is_product() && !theme_wc_disabled_button_supported()) {
        wp_register_script('add-to-cart-button', $template_url . '/woocommerce/add-to-cart-button.js', array('wc-add-to-cart-variation'), false, true);
        wp_enqueue_script('add-to-cart-button');
    }

    if (is_singular() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_dequeue_style('themler-core-bootstrap');
    wp_dequeue_script('themler-core-script');
}

/**
 * Remove other bootstrap scripts according to theme_disable_bootstrap_scripts option
 */
function theme_check_bootstrap() {
    global $wp_scripts;

    if (!is_object($wp_scripts) || !theme_get_option('theme_disable_bootstrap_scripts')) {
        return;
    }

    $to_remove = array();
    foreach($wp_scripts->queue as $handle) {
        if ($handle === 'theme-bootstrap') {
            continue;
        }
        $dependency = $wp_scripts->registered[$handle];
        if (property_exists($dependency, 'src')) {
            $src = $dependency->src;
            if (preg_match('#bootstrap.js|bootstrap.min.js#', $src)) {
                $to_remove[] = $handle;
            }
        }
    }
    if (!empty($to_remove)) {
        echo '<!-- removed scripts: ' . implode(', ', $to_remove) . ' -->';
        foreach($to_remove as $handle) {
            wp_dequeue_script($handle);
        }
    }
}
add_action('wp_print_scripts', 'theme_check_bootstrap');

function theme_editor_auto_login() {
    if(isset($_GET['editor_auto_login'])) {
        $_POST['log'] = $_GET['log'];
        $_POST['pwd'] = $_GET['pwd'];
    }
}

global $wp_locale;
if (isset($wp_locale)) {
    $wp_locale->text_direction = 'ltr';
}

function theme_clear_cart_after_payment() {
    // woocommerce adds action to get_header
    // until we don't use get_header(), call wc_clear_cart_after_payment manually
    if (function_exists('wc_clear_cart_after_payment') && has_action('get_header', 'wc_clear_cart_after_payment')) {
        wc_clear_cart_after_payment();
    }
}
add_action('theme_after_head', 'theme_clear_cart_after_payment');

function theme_get_price_html_to_text() {
    return '<span class="from">' . _x('To:', 'min_price', 'woocommerce') . ' </span>';
}

function theme_price_add_prefix($html) {
    return str_replace('<span class="amount">', '<span class="from">'.__('Price:', 'woocommerce').'</span><span class="amount">', $html);
}

function theme_woocommerce_variable_free_price_html($price, $product) {
    if ( $product->min_variation_price !== $product->max_variation_price ) {
        $full = '<div>' . $product->get_price_html_from_text() . __( 'Free!', 'woocommerce' ) . '</div>'
              . '<div>'. theme_get_price_html_to_text() . woocommerce_price($product->max_variation_price) . '</div>';
        $short = __( 'Free', 'woocommerce' ) . '<span class="amount">-</span>' . woocommerce_price($product->max_variation_price);
    } else {
        $full = __('Free!', 'woocommerce');
        $short = __('Free!', 'woocommerce');
    }
    return sprintf('<ins><full>%s</full><short>%s</short></ins>', $full, $short);
}

function theme_woocommerce_variable_price_html($price, $product) {
    $suffix = method_exists($product, 'get_price_suffix') ? $product->get_price_suffix() : '';

    if ( $product->min_variation_price !== $product->max_variation_price ) {
        $full = '<div>' . $product->get_price_html_from_text() . woocommerce_price($product->get_price()) . '</div>'
              . '<div>' . theme_get_price_html_to_text() . woocommerce_price($product->max_variation_price) . '</div>';
        $short = woocommerce_price($product->get_price()) . '<span class="amount">-</span>' . woocommerce_price($product->max_variation_price);
    } else {
        $short = woocommerce_price($product->get_price());
        $full = theme_price_add_prefix($short);
    }
    return sprintf('<ins><full>%s%s</full><short>%s%s</short></ins>', $full, $suffix, $short, $suffix);
}

function theme_woocommerce_simple_price_html($price, $product) {
    $short_new = woocommerce_price($product->get_price());
    $price = '<ins>'.
        '<short>' . $short_new . '</short>'.
        '<full>' . theme_price_add_prefix($short_new) . '</full></ins>';
    if ($product->is_on_sale() && $product->regular_price) {
        $short_old = is_numeric($product->regular_price) ? woocommerce_price($product->regular_price) : $product->regular_price;
        $price = '<del>'.
            '<short>' . $short_old . '</short>'.
            '<full>' . theme_price_add_prefix($short_old) . '</full></del>' . $price;
    }
    return $price;
}

function theme_woocommerce_variable_sale_price_html($price, $product) {
    $from_text = '';
    if ( ! $product->min_variation_price || $product->min_variation_price !== $product->max_variation_price )
        $from_text .= $product->get_price_html_from_text();
    $from = $product->min_variation_regular_price;
    $to = $product->get_price();
    $suffix = method_exists($product, 'get_price_suffix') ? $product->get_price_suffix() : '';
    return '<del><short>' . ( ( is_numeric( $from ) ) ? woocommerce_price( $from ) : $from ) . '</short>' .
                '<full>' . $from_text . ( ( is_numeric( $from ) ) ? woocommerce_price( $from ) : $from ) . '</full></del>' .

           '<ins><short>' . ( ( is_numeric( $to ) ) ? woocommerce_price( $to ) : $to ) . $suffix . '</short>' .
                '<full>' . $from_text . ( ( is_numeric( $to ) ) ? woocommerce_price( $to ) : $to ) . $suffix . '</full></ins>';
}

function theme_woocommerce_grouped_price_html($price, $product) {
    $child_prices = array();
    foreach ( $product->get_children() as $child_id )
        $child_prices[] = get_post_meta( $child_id, '_price', true );
    $child_prices = array_unique( $child_prices );
    if ( ! empty( $child_prices ) ) {
        $min_price = min( $child_prices );
        $max_price = max( $child_prices );
    } else {
        $min_price = '';
        $max_price = '';
    }
    if (sizeof($child_prices) > 1) {
        $full = '<div>' . $product->get_price_html_from_text() . woocommerce_price($min_price) . '</div>'
              . '<div>' . theme_get_price_html_to_text() . woocommerce_price($max_price) . '</div>';
        $short = woocommerce_price($min_price) . '<span class="amount">-</span>' . woocommerce_price($max_price);
    } else {
        $full = woocommerce_price($product->get_price());
        $short = woocommerce_price($product->get_price());
    }
    $suffix = method_exists($product, 'get_price_suffix') ? $product->get_price_suffix() : '';
    return sprintf('<ins><full>%s%s</full><short>%s%s</short></ins>', $full, $suffix, $short, $suffix);
}

function theme_price_hook($price) {
    $GLOBALS['theme_price'] = $price;
    return $price;
}
add_filter('woocommerce_get_price_html', 'theme_price_hook', 9);

function theme_get_price_data($product) {
    add_filter('woocommerce_grouped_price_html', 'theme_woocommerce_grouped_price_html', 10, 2);
    add_filter('woocommerce_variable_sale_price_html', 'theme_woocommerce_variable_sale_price_html', 10, 2);
    add_filter('woocommerce_sale_price_html', 'theme_woocommerce_simple_price_html', 10, 2);
    add_filter('woocommerce_price_html', 'theme_woocommerce_simple_price_html', 10, 2);
    add_filter('woocommerce_variable_price_html', 'theme_woocommerce_variable_price_html', 10, 2);
    add_filter('woocommerce_variable_free_sale_price_html', 'theme_woocommerce_variable_free_price_html', 10, 2);
    add_filter('woocommerce_variable_free_price_html', 'theme_woocommerce_variable_free_price_html', 10, 2);
    $product->get_price_html();
    $price_data = $GLOBALS['theme_price'];
    remove_filter('woocommerce_grouped_price_html', 'theme_woocommerce_grouped_price_html');
    remove_filter('woocommerce_variable_sale_price_html', 'theme_woocommerce_variable_sale_price_html');
    remove_filter('woocommerce_sale_price_html', 'theme_woocommerce_simple_price_html');
    remove_filter('woocommerce_price_html', 'theme_woocommerce_simple_price_html');
    remove_filter('woocommerce_variable_price_html', 'theme_woocommerce_variable_price_html');
    remove_filter('woocommerce_variable_free_sale_price_html', 'theme_woocommerce_variable_free_price_html');
    remove_filter('woocommerce_variable_free_price_html', 'theme_woocommerce_variable_free_price_html');
    return $price_data;
}

function theme_convert_price_data_to_html($html, $label, $label_attributes, $amount, $amount_attributes) {
    $html = preg_replace($label ? '/<short.*short>/' : '/<full.*full>/', '', $html);
    $html = str_replace('<full>', '', $html);
    $html = str_replace('</full>', '', $html);
    $html = str_replace('<short>', '', $html);
    $html = str_replace('</short>', '', $html);
    if ($label) {
        $html = str_replace('class="from"', $label_attributes . ' class="' . $label . '"', $html);
    }
    $html = str_replace('class="amount"', $amount_attributes . ' class="'. $amount .'"', $html);
    return $html;
}

function theme_price_html($args) {

    $price_data = $args['price_data'];
    $swap_old_regular = $args['swap_old_regular'];
    $show_old_price = $args['show_old_price'];

    $old_price_start = $args['old_price_start'];
    $old_price_end = $args['old_price_end'];
    $old_price_label = $args['old_price_label'];
    $old_price_label_attributes = $args['old_price_label_attributes'];
    $old_price_amount = $args['old_price_amount'];
    $old_price_amount_attributes = $args['old_price_amount_attributes'];

    $regular_price_start = $args['regular_price_start'];
    $regular_price_end = $args['regular_price_end'];
    $regular_price_label = $args['regular_price_label'];
    $regular_price_label_attributes = $args['regular_price_label_attributes'];
    $regular_price_amount = $args['regular_price_amount'];
    $regular_price_amount_attributes = $args['regular_price_amount_attributes'];

    $price_html = $price_data;
    $old_price_html = '';
    if (preg_match('/<ins>(.*)<\/ins>/', $price_data, $ins_matches))
        $price_html = $ins_matches[1];
    if (preg_match('/<del>(.*)<\/del>/', $price_data, $del_matches))
        $old_price_html = $del_matches[1];

    $price_html = $price_html ? $regular_price_start . theme_convert_price_data_to_html($price_html, $regular_price_label, $old_price_label_attributes, $regular_price_amount, $old_price_amount_attributes) . $regular_price_end : '';
    $old_price_html = $old_price_html ? $old_price_start . theme_convert_price_data_to_html($old_price_html, $old_price_label, $regular_price_label_attributes, $old_price_amount, $regular_price_amount_attributes) . $old_price_end : '';

    if (!$show_old_price)
        $old_price_html = '';

    global $product;
    $result = $swap_old_regular ? "$old_price_html$price_html" : "$price_html$old_price_html";
    $result = apply_filters('woocommerce_get_price_html', $result, $product);
    return $result;
}

function theme_shop_products_per_page() {
    $items = theme_get_option( 'theme_shop_products_per_page' );
    return $items == 0 ? -1 : $items;
}
add_filter('loop_shop_per_page', 'theme_shop_products_per_page');

function set_new_locale( $lang ) {
    if (isset($_COOKIE['language'])) {
        return $_COOKIE['language'];
    }
    // return original language
    return $lang;
}

if (!function_exists('get_woocommerce_currencies')) {
    function get_woocommerce_currencies() {
        return array_unique(
            apply_filters( 'woocommerce_currencies',
                array(
                    'AUD' => __( 'Australian Dollars', 'woocommerce' ),
                    'BRL' => __( 'Brazilian Real', 'woocommerce' ),
                    'CAD' => __( 'Canadian Dollars', 'woocommerce' ),
                    'RMB' => __( 'Chinese Yuan', 'woocommerce' ),
                    'CZK' => __( 'Czech Koruna', 'woocommerce' ),
                    'DKK' => __( 'Danish Krone', 'woocommerce' ),
                    'EUR' => __( 'Euros', 'woocommerce' ),
                    'HKD' => __( 'Hong Kong Dollar', 'woocommerce' ),
                    'HUF' => __( 'Hungarian Forint', 'woocommerce' ),
                    'IDR' => __( 'Indonesia Rupiah', 'woocommerce' ),
                    'INR' => __( 'Indian Rupee', 'woocommerce' ),
                    'ILS' => __( 'Israeli Shekel', 'woocommerce' ),
                    'JPY' => __( 'Japanese Yen', 'woocommerce' ),
                    'KRW' => __( 'South Korean Won', 'woocommerce' ),
                    'MYR' => __( 'Malaysian Ringgits', 'woocommerce' ),
                    'MXN' => __( 'Mexican Peso', 'woocommerce' ),
                    'NOK' => __( 'Norwegian Krone', 'woocommerce' ),
                    'NZD' => __( 'New Zealand Dollar', 'woocommerce' ),
                    'PHP' => __( 'Philippine Pesos', 'woocommerce' ),
                    'PLN' => __( 'Polish Zloty', 'woocommerce' ),
                    'GBP' => __( 'Pounds Sterling', 'woocommerce' ),
                    'RON' => __( 'Romanian Leu', 'woocommerce' ),
                    'RUB' => __( 'Russian Ruble', 'woocommerce' ),
                    'SGD' => __( 'Singapore Dollar', 'woocommerce' ),
                    'ZAR' => __( 'South African rand', 'woocommerce' ),
                    'SEK' => __( 'Swedish Krona', 'woocommerce' ),
                    'CHF' => __( 'Swiss Franc', 'woocommerce' ),
                    'TWD' => __( 'Taiwan New Dollars', 'woocommerce' ),
                    'THB' => __( 'Thai Baht', 'woocommerce' ),
                    'TRY' => __( 'Turkish Lira', 'woocommerce' ),
                    'USD' => __( 'US Dollars', 'woocommerce' ),
                )
            )
        );
    }
}

function theme_get_languages() {
    return array('en_US' => __('English', THEME_NS),
                 'fr_FR' => __('French', THEME_NS),
                 'de_DE' => __('German', THEME_NS)
    );
}

function theme_get_woocommerce_currency_full_name($currency) {
    $currencies = get_woocommerce_currencies();
    return $currencies[$currency];
}

function theme_get_language_full_name($language) {
    $languages = theme_get_languages();
    return $languages[$language];
}

add_filter('woocommerce_currency', 'set_new_currency');

function set_new_currency( $currency ) {
    if (isset($_COOKIE['currency'])) {
        $new_currency = $_COOKIE['currency'];
        $currencies = get_woocommerce_currencies();
        if (isset($currencies[$new_currency]))
            $currency = $new_currency;
    }
    return $currency;
}

function theme_get_currency_title($textType, $showLabel, $showArrow) {
    $label = $showLabel ? __('Currency', THEME_NS) : '';
    $currency = get_woocommerce_currency();
    $symbol = get_woocommerce_currency_symbol($currency);

    if ($textType === 'noText')
        $value = $symbol;
    elseif ($textType === 'short')
        $value = $currency;
    elseif ($textType === 'full')
        $value = $symbol . ' ' . theme_get_woocommerce_currency_full_name($currency);

    if ($value && $label)
        $label .= ': ';
    $title = $label . $value;

    $title = '<span>' . $title . '</span>';
    if ($showArrow)
        $title .= ' <span class="caret"></span>';
    return $title;
}

function theme_get_language_title($textType, $showLabel, $showArrow) {
    $label = $showLabel ? __('Language', THEME_NS) : '';
    $language = get_locale();

    if ($textType === 'short')
        $value = preg_replace('/.+_(.+)/', '$1', $language);
    elseif ($textType === 'noText')
        $value = '';
    elseif ($textType === 'full')
        $value = theme_get_language_full_name($language);

    if ($value && $label)
        $label .= ': ';
    $title = $label . $value;
    $title = '<span>' . $title . '</span>';
    if ($showArrow)
        $title .= ' <span class="caret"></span>';
    return $title;
}

function theme_page_title_separator_filter($sep) {
    $sep = get_option('theme_page_title_separator');
    if (false === $sep) {
        $sep = ' ';
    }
    return $sep;
}

function theme_document_title_filter($parts) {
    unset($parts['tagline'], $parts['site']);
    return $parts;
}

function theme_get_page_title() {
    $title = '';

    if (function_exists('wp_get_document_title') && get_option('theme_use_document_title')) {
        add_filter('document_title_separator', 'theme_page_title_separator_filter');
        add_filter('document_title_parts', 'theme_document_title_filter');
        $title = wp_get_document_title();
        remove_filter('document_title_parts', 'theme_document_title_filter');
        remove_filter('document_title_separator', 'theme_page_title_separator_filter');
    }

    if (!$title) {
        $separator = get_option('theme_page_title_separator');
        if (false === $separator) {
            $separator = ' ';
        }
        $title = trim(wp_title($separator, false));
    }

    if (is_home()) {
        $home_title = trim(get_option('theme_home_page_title'));
        if ($home_title)
            $title = $home_title;
    }

    if (!$title) {
        $title = get_bloginfo('name');
    } else if (function_exists('is_shop') && is_shop()) {
        $shop_page = get_post(woocommerce_get_page_id('shop'));
        $title = apply_filters('the_title', ($shop_page_title = get_option('woocommerce_shop_page_title')) ? $shop_page_title : $shop_page->post_title);
    }
    return $title;
}

function theme_change_cart_page_template(){
    if (is_page()){
        global $post;
        if (function_exists('woocommerce_get_page_id') && woocommerce_get_page_id('cart') == $post->ID){
            if ( !is_page_template('cart.php') ) {
                update_post_meta($post->ID, '_wp_page_template', 'cart.php');
            }
        }
    }
}

add_action('woocommerce_before_add_to_cart_button', create_function('', 'echo "%ADD_TO_CART%";'), 100);
add_action('woocommerce_after_add_to_cart_button', create_function('', 'echo "%ADD_TO_CART%";'), 1);

function theme_wc_get_variations() {
    global $_theme_wc_variations;
    if (!isset($_theme_wc_variations)) {
        ob_start();
        woocommerce_template_single_add_to_cart();
        $content = ob_get_clean();
        $show_button = false;

        $parts = explode('%ADD_TO_CART%', $content);
        if (count($parts) === 3 && preg_match('/<button.+button>/U', $parts[1])) {
            $show_button = true;
            $parts[1] = preg_replace('/<button.+button>/U', '', $parts[1]);
        }

        $_theme_wc_variations = array(
            'content' => implode('', $parts),
            'show_button' => $show_button
        );
    }
    return $_theme_wc_variations;
}


function theme_wc_disabled_button_supported() {
    // The cart button will display (disabled) when no selections are made.
    global $woocommerce;
    return version_compare($woocommerce->version, '2.5.0', '>=');
}

function theme_wc_quantity_buttons_supported() {
    // Removed quantity increment/decrement buttons
    global $woocommerce;
    return version_compare($woocommerce->version, '2.3.0', '<');
}

function theme_single_product_buy($class, $attributes, $context) {
    global $product;

    $additional_style = '';
    $additional_script = '';
    if ($product->product_type === 'variable') {

        if (!theme_wc_disabled_button_supported()) {
            $additional_style = ' style="display:none;"';
            $class .= ' wc-add-to-cart';

            $additional_script = <<<EOL
                <script>
                    jQuery(document).on('show_variation', 'form.cart', function() {
                        jQuery('.wc-add-to-cart').slideDown(200);
                    });
                </script>
EOL;
        } else {
            $class .= ' wc-add-to-cart';

            $additional_script = <<<EOL
            <script>
                var btn = jQuery('.wc-add-to-cart.single_add_to_cart_button');
                jQuery('form.cart').on('hide_variation', function() {
                    btn.attr('disabled', 'disabled').attr('title', wc_add_to_cart_variation_params.i18n_make_a_selection_text);
                }).on('show_variation', function(event, variation, purchasable) {
                    if (purchasable) {
                        btn.removeAttr('disabled').removeAttr('title');
                    } else {
                        btn.attr('disabled', 'disabled').attr('title', wc_add_to_cart_variation_params.i18n_unavailable_text);
                    }
                });
            </script>
EOL;
        }
    }
    $variations = theme_wc_get_variations();
    if ($variations['show_button']) {
?>
        <button onclick="jQuery('<?php echo $context; ?> form.cart').submit()" <?php echo $attributes; ?> class="<?php echo $class; ?> single_add_to_cart_button"<?php echo $additional_style; ?>>
            <?php
                echo method_exists($product, 'single_add_to_cart_text')
                    ? $product->single_add_to_cart_text()
                    :  _e('Add to cart', 'woocommerce');
            ?>
        </button>
<?php
    }
    echo $additional_script;
}

function theme_product_buy($class, $attributes) {
    if (!apply_filters('theme_show_single_add_to_cart', true))
        return; // if plugin disabled the button

    global $product_overview_context;
    if (!empty($product_overview_context)) { // this button located in product-overview
        theme_single_product_buy($class, $attributes, $product_overview_context);
        return;
    }

    if (function_exists('woocommerce_template_loop_add_to_cart')) {
        ob_start();
        woocommerce_template_loop_add_to_cart();
        $result = ob_get_clean();
        if (preg_match('#class="([^"]*?)"#', $result, $matches)) {
            $classes = array_diff(explode(' ', $matches[1]), array('button'));
            $classes[] = $class;
            $result = str_replace($matches[0], $attributes . ' class="' . implode(' ', $classes) . '"', $result);
        }
        echo $result;
    }
}

function theme_product_image($product_view, $class, $attributes) {
    if (isset($product_view['image'])) {
        $image = $product_view['image'];
        if (strpos($image, 'class') === false) {
            $image = str_replace('<img', '<img ' . $attributes . ' class=" ' . $class . ' "', $image);
        } else {
            $image = preg_replace('/class([ \t]*)=([ \t]*)([\"\'])/', $attributes . ' class=$3 ' . $class . ' ', $image);
        }
        echo $image;
    }
}

function theme_get_product_thumbnails_data(){
    global $product;
    $attachments_ids = method_exists($product, 'get_gallery_attachment_ids') ? $product->get_gallery_attachment_ids() : array(get_post_thumbnail_id());
    $images = array();
    if ($attachments_ids) {
        foreach ( $attachments_ids as $key => $attachment_id ) {
            if ( get_post_meta( $attachment_id, '_woocommerce_exclude_image', true ) == 1 )
                continue;
            $images[] = array(
                'url' => wp_get_attachment_url($attachment_id),
                'title' => esc_attr(get_the_title($attachment_id)),
                'src' => theme_get_array_value(wp_get_attachment_image_src($attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), false), 0),
                'preview' => theme_get_array_value(wp_get_attachment_image_src($attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), false), 0)
            );
        }
    }
    return $images;
}

function theme_woocommerce_in_cart_product_thumbnail($image) {
    return str_replace('>', ' style="max-width:none;">', $image);
}
add_filter('woocommerce_in_cart_product_thumbnail', 'theme_woocommerce_in_cart_product_thumbnail');

function theme_do_action($tag, $avoid_hooks = array()) {
    $add_hooks = array();
    foreach($avoid_hooks as $func) {
        if (remove_action($tag, $func[0], $func[1])) {
            $add_hooks[] = $func;
        }
    }
    do_action($tag);
    foreach($add_hooks as $func) {
        add_action($tag, $func[0], $func[1]);
    }
}

if (!has_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart')) {
    add_filter('theme_show_single_add_to_cart', '__return_false');
}

function theme_add_to_cart_fragments($fragments) {
    if (!isset($_GET['preview']))
        return $fragments;
    $new_fragments = array();
    foreach($fragments as $key => $content) {
        if ($key !== 'div.widget_shopping_cart_content') {
            $new_fragments[$key] = preview_theme_ob_filter($content);
        }
    }
    return $new_fragments;
}

function theme_get_wc_nonce_field($action) {
    global $woocommerce;
    return method_exists($woocommerce, 'nonce_field') ? $woocommerce->nonce_field($action, true, false) : wp_nonce_field("woocommerce-$action");
}

function theme_get_footer_name($name) {
    for($id = 1; $id <= 4; $id++) {
        if ('footer'.$id === $name) {
            return $name;
        }
    }
    return 'footer1';
}



function theme_custom_menu_filter($args) {
    $args['items_wrap'] = '<ul id="%1$s">%3$s</ul>';
    return $args;
}
add_filter('wp_nav_menu_args', 'theme_custom_menu_filter');

function theme_need_convert_preview_links() {
    if (theme_is_customizer())
        return true;
    if (!function_exists('preview_theme_ob_filter'))
        return true;
    $test_data = '<a href="#">';
    return preview_theme_ob_filter($test_data) === $test_data;
}

if (theme_is_preview()) {
    global $wp_version;
    if (theme_need_convert_preview_links()) {
        function theme_customize_theme_ob_filter($content) {
            return preg_replace_callback("|(<a.*?href=([\"']))(.*?)([\"'].*?>)|", 'theme_customize_theme_ob_filter_callback', $content);
        }
        function theme_customize_theme_ob_filter_callback($matches) {
            if (false !== strpos($matches[3], '/wp-admin/') || false !== strpos($matches[3], '/feed/') || false !== strpos($matches[3], '/trackback/')) {
                return $matches[1] . "#$matches[2] onclick=$matches[2]return false;" . $matches[4];
            }
            if (0 === strpos($matches[3], '#') || false !== strpos($matches[3], '://') && 0 !== strpos($matches[3], home_url()) && 0 !== strpos($matches[3], site_url())) {
                return $matches[0];
            }
            $link = theme_add_preview_args($matches[3]);
            if ( 0 === strpos($link, 'preview=1') )
                $link = "?$link";
            return $matches[1] . esc_attr($link) . $matches[4];
        }
        ob_start('theme_customize_theme_ob_filter');
    } else {
        function theme_preview_theme_ob_filter( $content ) {
            return preg_replace_callback( "|(<a.*?href)(=[\"'])(.*?)([\"'].*?>)|", 'theme_preview_theme_ob_filter_callback', $content );
        }
        function theme_preview_theme_ob_filter_callback($matches) {
            $link = $matches[3];
            if (false !== strpos($link, 'preview=1') || 0 === strpos($link, '#') || 0 === strpos($link, '//') || false !== strpos($link, '://') && 0 !== strpos($link,  home_url())) {
                return $matches[1] . ' ' . $matches[2] . $matches[3] . $matches[4];
            }
            return $matches[0];
        }
        ob_start('theme_preview_theme_ob_filter');
    }
}

function theme_social_icons_filter($content) {
    $url   = urlencode(theme_remove_preview_args($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
    $title = urlencode(theme_get_page_title());
    return str_replace(array('[DYNAMIC-URL]', '[DYNAMIC-TITLE]'), array($url, $title), $content);
}
add_filter('theme_body', 'theme_social_icons_filter');

function theme_catch_template($template) {
    if (isset($_REQUEST['custom_page'])) {
        $template_file = $_REQUEST['custom_page'];
        if ($template_file === 'default')
            $template_file = 'page.php';
        $custom_template = get_stylesheet_directory() . '/' . $template_file;
        if (file_exists($custom_template)) {
            $template = $custom_template;
        }
    }
    return $template;
}

if (theme_can_view_preview()) {
    foreach(array('index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home', 'frontpage', 'page', 'paged', 'search', 'single', 'singular') as $template) {
        add_filter($template . '_template', 'theme_catch_template');
    }
}

function theme_get_selected_template($type) {
    $type = sanitize_title_with_dashes($type);
    if (theme_can_view_preview() && isset($_GET['custom_template'])) {
        return $_GET['custom_template'];
    }
    return theme_get_option('theme_template_' . get_option('stylesheet') . '_' . $type);
}

global $theme_custom_templates;
$theme_custom_templates = array();

theme_include_lib('templates.php', 'templates');

/*
 * Include the template depends on value stored in database
 *
 * @global array $theme_custom_templates
 *
 * @param string $type The type of template (Home, Products, 404, ect)
 * @param string $default_name Name of the template
 */
function theme_load_template($type, $default_name) {
    global $theme_custom_templates;
    $name = theme_get_selected_template($type);
    if (!$name)
        $name = $default_name;

    $path = theme_get_array_value($theme_custom_templates[$type], $name, $theme_custom_templates[$type][$default_name]);
    require_once(get_template_directory() . '/' . $path);
}

function theme_register_template($type, $name, $path) {
    global $theme_custom_templates;
    if (!isset($theme_custom_templates[$type]))
        $theme_custom_templates[$type] = array();
    $theme_custom_templates[$type][$name] = $path;
}

include 'functions-additional.php';