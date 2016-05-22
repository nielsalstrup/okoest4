<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $theme_options,
	   $theme_default_options, $theme_templates_options, $theme_template_type_priority,
	   $theme_template_query;

$theme_templates_options = array();
$theme_selectable_templates = array();
$theme_template_type_priority = array();
$theme_template_query = array();

function theme_add_template_option($type, $name, $caption, $type_priority = 10) {
    $caption = esc_attr(urldecode($caption));
    if (!$caption) {
		$caption = $type;
	}
    global $theme_templates_options, $theme_template_type_priority;
    $theme_template_type_priority[$type] = $type_priority;
    $theme_templates_options[$type][$name] = __($caption, THEME_NS);
}

function theme_add_template_query_option($type, $name, $caption) {
	global $theme_template_query;
	$caption = esc_attr(urldecode($caption));
	if (!$caption) {
		$caption = $type;
	}
	$theme_template_query[$name] = __($caption, THEME_NS);
}

theme_include_lib('templates_options.php');

$theme_menu_source_options = array(
	'Pages'      => __('Pages', THEME_NS),
	'Categories' => __('Categories', THEME_NS),
	'Products Categories' => __('Products Categories', THEME_NS)
);

$theme_sidebars_style_options = array(
	'block'  => __('Block style', THEME_NS),
	'post'   => __('Post style', THEME_NS),
	'simple' => __('Simple text', THEME_NS)
);

$theme_heading_options = array(
	'h1'  => __('<H1>', THEME_NS),
	'h2'  => __('<H2>', THEME_NS),
	'h3'  => __('<H3>', THEME_NS),
	'h4'  => __('<H4>', THEME_NS),
	'h5'  => __('<H5>', THEME_NS),
	'h6'  => __('<H6>', THEME_NS),
	'div' => __('<DIV>', THEME_NS),
);

$theme_widget_show_on = array(
	'all'           => __('All pages', THEME_NS),
	'selected'      => __('Selected pages', THEME_NS),
	'none_selected' => __('All pages except selected', THEME_NS),
);

$theme_search_modes = array(
    'post'    => __('Posts', THEME_NS),
	'product' => __('Products', THEME_NS),
    'all'    => __('All', THEME_NS),
);

$theme_options = array(
    array(
        'name' => __('Templates', THEME_NS),
        'type' => 'heading'
    )
);

function theme_compare_template_names($a, $b) {
    global $theme_template_type_priority;
    if ($theme_template_type_priority[$a] === $theme_template_type_priority[$b])
        return strnatcasecmp($a, $b);
    return $theme_template_type_priority[$b] - $theme_template_type_priority[$a];
}
uksort($theme_templates_options, 'theme_compare_template_names');

foreach($theme_templates_options as $template => $list) {
	natsort($list);
    $theme_options[] = array(
        'id'      => 'theme_template_' . get_option('stylesheet') . '_' . sanitize_title_with_dashes($template),
        'name'    => __($template, THEME_NS),
        'type'    => 'select',
        'options' => $list
    );
}

$theme_options[] = array(
	'name' => __('Templates blog pages', THEME_NS),
	'type' => 'heading',
	'desc' => __('Comma separated list of id\'s. Keep empty to use default.<br>Used as sample data for preview-theme.', THEME_NS),
);

foreach($theme_template_query as $template => $caption) {
	$theme_options[] = array(
		'id' => 'theme_template_' . $template . '_query_ids',
		'name' => $caption,
		'type' => 'text',
	);
}

$theme_options = array_merge($theme_options, array(
	array(
		'name' => __('Search', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'      => 'theme_search_mode',
		'name'    => __('Search mode', THEME_NS),
		'type'    => 'select',
		'options' => $theme_search_modes,
	),
	array(
		'name' => __('Header', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'   => 'theme_header_clickable',
		'name' => __('Make the header clickable', THEME_NS),
		'type' => 'checkbox',
        'desc' => __('Yes', THEME_NS),
	),
	array(
		'id'   => 'theme_header_link',
		'name' => __('Header link', THEME_NS),
		'type' => 'text',
		'depend' => 'theme_header_clickable',
	),
    array(
        'id'   => 'theme_logo_url',
        'name' => __('Logo image url', THEME_NS),
        'type' => 'text',
    ),
    array(
        'id'   => 'theme_logo_link',
        'name' => __('Logo link href', THEME_NS),
        'type' => 'text',
    ),
    array(
        'id'   => 'theme_home_page_title',
        'name' => __('Home Page Title', THEME_NS),
        'desc' => __('Used on the Home Page in the Page Title Control', THEME_NS),
        'type' => 'text',
    ),
	array(
		'id'   => 'theme_page_title_separator',
		'name' => __('Page Title separator', THEME_NS),
		'desc' => __('Used in the Page Title Control', THEME_NS),
		'type' => 'text',
	),
	array(
		'id'   => 'theme_use_document_title',
		'name' => __('Use Document Title as Page Title', THEME_NS),
		'desc' => __('Based on <a href="https://developer.wordpress.org/reference/functions/wp_get_document_title/">wp_get_document_title</a> function', THEME_NS),
		'type' => 'checkbox',
		'show' => create_function('', 'return function_exists("wp_get_document_title");')
	),
	array(
		'name' => __('Bootstrap', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'   => 'theme_disable_bootstrap_scripts',
		'name' => __('Disable bootstrap.js from plugins', THEME_NS),
		'type' => 'checkbox',
		'desc' => __('Use this option to avoid conflicts with theme bootstrap.js', THEME_NS),
	),
	array(
		'name' => __('Navigation Menu', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'   => 'theme_menu_showHome',
		'name' => __('Show home item', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_menu_homeCaption',
		'name' => __('Home item caption', THEME_NS),
		'type' => 'text',
		'depend' => 'theme_menu_showHome',
	),
	array(
		'id'   => 'theme_menu_highlight_active_categories',
		'name' => __('Highlight active categories', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_menu_trim_title',
		'name' => __('Trim long menu items', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_menu_trim_len',
		'name' => __('Limit each item to [N] characters', THEME_NS),
        'desc' =>__('(characters). ', THEME_NS),
		'type' => 'numeric',
		'depend' => 'theme_menu_trim_title',
	),
	array(
		'id'   => 'theme_submenu_trim_len',
		'name' => __('Limit each subitem to [N] characters', THEME_NS),
        'desc' =>__('(characters). ', THEME_NS),
		'type' => 'numeric',
		'depend' => 'theme_menu_trim_title',
	),
	array(
		'id'      => 'theme_menu_source',
		'name'    => __('Default menu source', THEME_NS),
		'type'    => 'select',
		'options' => $theme_menu_source_options,
		'desc'    => __('Displayed when Appearance > Menu > Primary menu is not set', THEME_NS),
	),
    array(
        'id'   => 'theme_use_default_menu',
        'name' => __('Use not stylized menu', THEME_NS),
        'desc' => __('Used standart <a href="http://codex.wordpress.org/Function_Reference/wp_nav_menu">wp_nav_menu</a>, when option is enabled (need for some third-party plugins).', THEME_NS),
        'type' => 'checkbox'
    ),
	array(
		'name' => __('Posts', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'   => 'theme_single_navigation_trim_title',
		'name' => __('Trim long navigation links in single post view', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_single_navigation_trim_len',
		'name' => __('Limit each link to [N] characters', THEME_NS),
        'desc' =>__('(characters). ', THEME_NS),
		'type' => 'numeric',
		'depend' => 'theme_single_navigation_trim_title',
	),
	array(
		'name' => __('Featured Image', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'   => 'theme_metadata_use_featured_image_as_thumbnail',
		'name' => __('Use featured image as thumbnail', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_metadata_thumbnail_auto',
		'name' => __('Use auto thumbnails', THEME_NS),
		'desc' => __('Generate post thumbnails automatically (use the first image from the post gallery)', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_metadata_thumbnail_width',
		'name' => __('Thumbnail width', THEME_NS),
		'desc' => __('(px)', THEME_NS),
		'type' => 'numeric'
	),
	array(
		'id'   => 'theme_metadata_thumbnail_height',
		'name' => __('Thumbnail height', THEME_NS),
		'desc' => __('(px)', THEME_NS),
		'type' => 'numeric'
	),
    array(
        'name' => __('Excerpt', THEME_NS),
        'type' => 'heading'
    ),
    array(
        'id'   => 'theme_metadata_excerpt_auto',
        'name' => __('Use auto excerpts', THEME_NS),
        'desc' => __('Generate post excerpts automatically (When neither more-tag nor post excerpt is used)', THEME_NS),
        'type' => 'checkbox'
    ),
    array(
        'id'   => 'theme_metadata_excerpt_words',
        'name' => __('Excerpt length', THEME_NS),
        'desc' =>__('(words). ', THEME_NS),
        'type' => 'numeric',
        'depend' => 'theme_metadata_excerpt_auto'
    ),
    array(
        'id'   => 'theme_metadata_excerpt_min_remainder',
        'name' => __('Excerpt balance', THEME_NS),
        'desc' =>__('(words). ', THEME_NS),
        'type' => 'numeric',
        'depend' => 'theme_metadata_excerpt_auto'
    ),
	array(
		'id'   => 'theme_metadata_excerpt_strip_shortcodes',
		'name' => __('Remove shortcodes from excerpt', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_metadata_excerpt_use_tag_filter',
		'name' => __('Apply excerpt tag filter', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
    array(
        'id'   => 'theme_metadata_excerpt_allowed_tags',
        'name' => __('Allowed excerpt tags', THEME_NS),
        'type' => 'widetext',
        'depend' => 'theme_metadata_excerpt_use_tag_filter',
    ),
    array(
        'id'   => 'theme_show_morelink',
        'name' => __('Show More Link', THEME_NS),
        'desc' => __('Yes', THEME_NS),
        'type' => 'checkbox'
    ),
    array(
        'id'   => 'theme_morelink_template',
        'name' => __('More Link Template', THEME_NS),
        'desc' => sprintf(__('<strong>ShortTags:</strong><code>%s</code>', THEME_NS), '[url], [text]'),
        'type' => 'widetext',
        'depend' => 'theme_show_morelink',
    ),
    array(
        'name' => __('Shop', THEME_NS),
        'type' => 'heading'
    ),
    array(
        'id' => 'theme_shop_products_per_page',
        'name' => __('Number of products to show', THEME_NS),
        'desc' => __('Select the number of products to show on the pages. Set 0 to show all products.', THEME_NS),
        'type' => 'numeric'
    ),
    array(
        'id' => 'theme_products_newness_period',
        'name' => __('Product newness period', THEME_NS),
        'desc' => __('Select the number of days', THEME_NS),
        'type' => 'numeric'
    ),
	array(
		'name' => __('Pages', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'   => 'theme_show_random_posts_on_404_page',
		'name' => __('Show random posts on 404 page', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_show_random_posts_title_on_404_page',
		'name' => __('Title for random posts', THEME_NS),
		'type' => 'text',
		'depend' => 'theme_show_random_posts_on_404_page',
	),
	array(
		'id'   => 'theme_show_tags_on_404_page',
		'name' => __('Show tags on 404 page', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_show_tags_title_on_404_page',
		'name' => __('Title for tags', THEME_NS),
		'type' => 'text',
		'depend' => 'theme_show_tags_on_404_page',
	),
	array(
		'name' => __('Comments', THEME_NS),
		'type' => 'heading',
	),
	array(
		'id'   => 'theme_allow_comments',
		'name' => __('Allow Comments', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
    array(
        'id'   => 'theme_show_comments_anywhere',
        'name' => __('Show comments anywhere', THEME_NS),
        'desc' => __('Yes', THEME_NS),
        'type' => 'checkbox',
        'depend' => 'theme_allow_comments',
    ),
	array(
		'id'   => 'theme_comment_use_smilies',
		'name' => __('Use smilies in comments', THEME_NS),
		'type' => 'checkbox',
        'desc' => __('Yes', THEME_NS),
		'depend' => 'theme_allow_comments',
	),
	array(
		'name' => __('Footer', THEME_NS),
		'type' => 'heading'
	),
	array(
		'id'     => 'theme_override_default_footer_content',
		'name' => __('Override default theme footer content', THEME_NS),
		'type' => 'checkbox',
		'desc' => __('Yes', THEME_NS),
	),
	array(
		'id'     => 'theme_footer_content',
		'name'   => __('Footer content', THEME_NS),
		'desc'   => sprintf(__('<strong>XHTML:</strong> You can use these tags: <code>%s</code>', THEME_NS), 'a, abbr, acronym, em, b, i, strike, strong, span') . '<br />'
		. sprintf(__('<strong>ShortTags:</strong><code>%s</code>', THEME_NS), '[year], [top], [rss], [login_link], [blog_title], [xhtml], [css], [rss_url], [rss_title]'),
		'type'   => 'textarea',
		'depend' => 'theme_override_default_footer_content',
	),
	array(
		'name' => __('Advertisement', THEME_NS),
		'type' => 'heading',
		'desc' => sprintf(__('Use the %s short code to insert these ads into posts, text widgets or footer', THEME_NS), '<code>[ad]</code>') . '<br/>'
		. '<span>' . __('Example:', THEME_NS) .'</span><code>[ad code=4 align=center]</code>'
	),
	array(
		'id'   => 'theme_ad_code_1',
		'name' => sprintf(__('Ad code #%s:', THEME_NS), 1),
		'type' => 'textarea'
	),
	array(
		'id'   => 'theme_ad_code_2',
		'name' => sprintf(__('Ad code #%s:', THEME_NS), 2),
		'type' => 'textarea'
	),
	array(
		'id'   => 'theme_ad_code_3',
		'name' => sprintf(__('Ad code #%s:', THEME_NS), 3),
		'type' => 'textarea'
	),
	array(
		'id'   => 'theme_ad_code_4',
		'name' => sprintf(__('Ad code #%s:', THEME_NS), 4),
		'type' => 'textarea'
	),
	array(
		'id'   => 'theme_ad_code_5',
		'name' => sprintf(__('Ad code #%s:', THEME_NS), 5),
		'type' => 'textarea'
	),
));

global $theme_page_meta_options;
$theme_page_meta_options = array (
	array(
		'id'   => 'theme_show_page_title',
		'name' => __('Show Title on Page', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_show_in_menu',
		'name' => __('Show in Menu', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_show_as_separator',
		'name' => __('Show as Separator in Menu', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_title_in_menu',
		'name' => __('Title in Menu', THEME_NS),
		'type' => 'widetext'
	),
	array(
		'id'   => 'theme_show_categories',
		'name' => __('Show Custom Categories', THEME_NS),
		'desc' => __('Yes', THEME_NS),
		'type' => 'checkbox'
	),
	array(
		'id'   => 'theme_categories',
		'name' => __('Comma separated list of Category slugs', THEME_NS),
		'type' => 'widetext',
		'desc' => __('Keep empty to show all posts.', THEME_NS),
		'depend' => 'theme_show_categories',
	),
    array(
        'id'   => 'theme_use_wpautop',
        'name' => __('Automatically add paragraphs', THEME_NS),
        'desc' => __('Yes', THEME_NS),
        'type' => 'checkbox'
    )
);

global $theme_widget_meta_options, $theme_widgets_style;
$theme_widget_meta_options = array(
	array(
		'id'      => 'theme_widget_show_on',
		'name'    => __('Show widget on:', THEME_NS),
		'type'    => 'select',
		'options' => $theme_widget_show_on
	),
	array(
		'id'   => 'theme_widget_front_page',
		'name' => '',
		'type' => 'checkbox',
		'desc' => __('Front page', THEME_NS),
		'depend' => 'theme_widget_show_on:selected,none_selected',
	),
	array(
		'id'   => 'theme_widget_single_post',
		'name' => '',
		'type' => 'checkbox',
		'desc' => __('Single posts', THEME_NS),
		'depend' => 'theme_widget_show_on:selected,none_selected',
	),
	array(
		'id'   => 'theme_widget_single_page',
		'name' => '',
		'type' => 'checkbox',
		'desc' => __('Single pages', THEME_NS),
		'depend' => 'theme_widget_show_on:selected,none_selected',
	),
	array(
		'id'   => 'theme_widget_posts_page',
		'name' => '',
		'type' => 'checkbox',
		'desc' => __('Posts page', THEME_NS),
		'depend' => 'theme_widget_show_on:selected,none_selected',
	),
	array(
		'id'   => 'theme_widget_page_ids',
		'name' => '',
		'type' => 'checkbox',
		'desc' => __('Page IDs (comma separated)', THEME_NS),
		'depend' => 'theme_widget_show_on:selected,none_selected',
	),
	array(
		'id'   => 'theme_widget_page_ids_list',
		'name' => '',
		'type' => 'text',
		'desc' => '',
		'depend' => 'theme_widget_page_ids;theme_widget_show_on:selected,none_selected',
	),
);

global $theme_page_header_image_meta_options;
$theme_page_header_image_meta_options = array(
	array(
		'id'   => 'theme_header_image',
		'type' => 'text'
	),
	array(
		'id'   => 'theme_header_image_with_flash',
		'type' => 'checkbox'
	),
);