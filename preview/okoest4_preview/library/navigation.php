<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

function theme_get_menu($args = '') {
	$args = wp_parse_args($args, array(
        'source' => 'Pages',
        'depth' => 0,
        'menu' => null,
        'theme_location' => '',
        'responsive_levels' => '',
        'levels' => '',
        'menu_function' => '',
        'menu_item_start_function' => '',
        'menu_item_end_function' => '',
        'submenu_start_function' => '',
        'submenu_end_function' => '',
        'submenu_item_start_function' => '',
        'submenu_item_end_function' => '',
        'class' => '',
    ));
    $args = apply_filters('wp_nav_menu_args', $args);

	$source = &$args['source'];
	$menu = &$args['menu'];
	$class = &$args['class'];
    $theme_location = &$args['theme_location'];

    if ($theme_location != null && is_string($theme_location)) { // theme location

        // remove empty-array-filter for preview theme
        $filter_name = 'pre_option_theme_mods_' . get_option( 'stylesheet' );
        $empty_array_filter_exists = remove_filter($filter_name, '__return_empty_array');

        $locations = get_nav_menu_locations();
        $location = theme_get_array_value($locations, $theme_location);

        if (!$location && !empty($locations)) {
            $location = max(array_values($locations));
        }

        if ($location) {
            $menu = wp_get_nav_menu_object($location);
            if ($menu) {
                $source = 'Custom Menu';
                $class = implode(' ', array($class, 'menu-' . $menu->term_id));
            }
        }

        // restore empty-array-filter
        if ($empty_array_filter_exists) {
            add_filter($filter_name, '__return_empty_array');
        }
    }

	if ($source == 'Custom Menu' && $menu != null) {
		return theme_get_list_menu($args);
	}

	if ($source == 'Pages') {
		return theme_get_list_pages(array_merge(array('sort_column' => 'menu_order, post_title'), $args));
	}

	if ($source == 'Categories') {
		return theme_get_list_categories(array_merge(array('title_li' => false), $args));
	}

	if ($source == 'Products Categories') {
		return theme_get_list_categories(array_merge(array('title_li' => false, 'taxonomy' => 'product_cat'), $args));
	}
}

function theme_get_top_menu($args = '') {
    $args = wp_parse_args($args, array(
        'menu' => null,
        'theme_location' => '',
        'menu_function' => '',
        'menu_item_start_function' => '',
        'menu_item_end_function' => '',
        'submenu_start_function' => '',
        'submenu_end_function' => '',
        'submenu_item_start_function' => '',
        'submenu_item_end_function' => '',
        'class' => '',
    ));
    $args = apply_filters('wp_nav_menu_args', $args);

    $source = 'Manual';
    $menu = &$args['menu'];
    $class = &$args['class'];
    $theme_location = &$args['theme_location'];

    if ($theme_location != null && is_string($theme_location)) { // theme location
        remove_filter( 'pre_option_theme_mods_' . get_option( 'stylesheet' ), '__return_empty_array' );
        $location = theme_get_array_value(get_nav_menu_locations(), $theme_location);
        if ($location) {
            $menu = wp_get_nav_menu_object($location);
            if ($menu) {
                $source = 'Custom Menu';
                $class = implode(' ', array($class, 'menu-' . $menu->term_id));
            }
        }
        add_filter( 'pre_option_theme_mods_' . get_option( 'stylesheet' ), '__return_empty_array' );
    }

    if ($source == 'Custom Menu' && $menu != null) {
        return theme_get_list_menu($args);
    }

    if ($source == 'Manual') {
        return theme_get_manual_menu($args);
    }
}

function theme_get_dropdown_menu($args = '') {
    $args = wp_parse_args($args,
        array(
            'menu' => null,
            'class' => '',
            'items'=> array(),
            'menu_function' => '',
            'menu_item_start_function' => '',
            'menu_item_end_function' => '',
            'submenu_start_function' => '',
            'submenu_end_function' => '',
            'submenu_item_start_function' => '',
            'submenu_item_end_function' => ''
        ));

    foreach ($args['items'] as $item) {
        $id = $item['id'];
        $title = $item['title'];
        $href = $item['href'];
        $item_id = isset($item['item_id']) ? $item['item_id'] : '' ;
        $parent = $item['parent'];
        $no_menu_trim_title = isset($item['no_menu_trim_title']) && $item['no_menu_trim_title'];
        $items[] = new theme_MenuItem(
            array(
                'id' => $id,
                'attr' => array(
                    'class' => '',
                    'href' => $href,
                    'item_id' => $item_id,
                    'no_menu_trim_title' => $no_menu_trim_title
                ),
                'title' => $title,
                'parent' => $parent,
                'menu_item_start_function' => $args['menu_item_start_function'],
                'menu_item_end_function' => $args['menu_item_end_function'],
                'submenu_item_start_function' => $args['submenu_item_start_function'],
                'submenu_item_end_function' => $args['submenu_item_end_function']
            )
        );
    }
    $walker = new theme_MenuWalker();
    return $walker->walk($items, $args);

}

/* custom menu */

function theme_get_list_menu($args = array()) {
    global $wp_query;
    $menu_items = wp_get_nav_menu_items($args['menu']->term_id);
    if (empty($menu_items))
        return '';

    $home_page_id = (int) get_option('page_for_posts');
    $queried_object = $wp_query->get_queried_object();
    $queried_object_id = (int) $wp_query->queried_object_id;
    $active_ID = null;

    $IdToKey = array();
    foreach ((array) $menu_items as $key => $menu_item) {
        $IdToKey[$menu_item->ID] = $key;
        if (function_exists('is_woocommerce')) {
            if (is_woocommerce()) {
                $shop_page = (int)woocommerce_get_page_id('shop');
                if (is_shop() && $menu_item->object_id == $shop_page) {
                    $active_ID = $menu_item->ID;
                    continue;
                }
            }
        }
        if ($menu_item->object_id == $queried_object_id &&
            (
                (!empty($home_page_id) && 'post_type' == $menu_item->type && $wp_query->is_home && $home_page_id == $menu_item->object_id ) ||
                ( 'post_type' == $menu_item->type && $wp_query->is_singular ) ||
                ( 'taxonomy' == $menu_item->type && ( $wp_query->is_category || $wp_query->is_tag || $wp_query->is_tax ))
            )
        ) {
            $active_ID = $menu_item->ID;
        } elseif ('custom' == $menu_item->object) {
            if (theme_is_current_url($menu_item->url)) {
                $active_ID = $menu_item->ID;
            }
        }
    }

    $current_ID = $active_ID;
    while ($current_ID && isset($IdToKey[$current_ID])) {
        $activeIDs[] = $current_ID;
        $current_item = &$menu_items[$IdToKey[$current_ID]];
        $current_item->classes[] = 'active';
        $current_ID = $current_item->menu_item_parent;
    }

    $sorted_menu_items = array();
    foreach ((array) $menu_items as $key => $menu_item) {
        $sorted_menu_items[$menu_item->menu_order] = wp_setup_nav_menu_item($menu_item);
    }

    $items = array();
    foreach ($sorted_menu_items as $el) {
        $id = $el->db_id;
        $title = $el->title;
        $classes = empty($el->classes) ? array() : (array) $el->classes;
        $items[] = new theme_MenuItem(array(
            'id' => $id,
            'active' => in_array('active', $classes),
            'attr' => array(
                'target' => $el->target,
                'rel' => $el->xfn,
                'href' => $el->url,
                'class' => join(' ', apply_filters('nav_menu_css_class', array_filter($classes, create_function('$a', 'return $a && $a !== "active";')), $el))
            ),
            'title' => $title,
            'parent' => $el->menu_item_parent,
            'menu_item_start_function' => $args['menu_item_start_function'],
            'menu_item_end_function' => $args['menu_item_end_function'],
            'submenu_item_start_function' => $args['submenu_item_start_function'],
            'submenu_item_end_function' => $args['submenu_item_end_function']
        ));
    }

    $walker = new theme_MenuWalker();
    $items = apply_filters('wp_nav_menu_objects', $items, $args);
    $items = $walker->walk($items, $args);
    $items = apply_filters('wp_nav_menu_items', $items, $args);
    return apply_filters('wp_nav_menu', $items, $args);
}

/* pages */
function theme_get_list_pages($args = array()) {
	global $wp_query;
	$pages = get_pages($args);
	if (empty($pages))
		return '';

	$IdToKey = array();
	$currentID = null;

	foreach ($pages as $key => $page) {
		$IdToKey[$page->ID] = $key;
	}

	$frontID = null;
	$blogID = null;

	if ('page' == get_option('show_on_front')) {

		$frontID = get_option('page_on_front');
		if ($frontID && isset($IdToKey[$frontID])) {
			$frontKey = $IdToKey[$frontID];
			$frontPage = $pages[$frontKey];
			unset($pages[$frontKey]);
			$frontPage->post_parent = 0;
			$frontPage->menu_order = 0;
			array_unshift($pages, $frontPage);
			$IdToKey = array();
			foreach ($pages as $key => $page) {
				$IdToKey[$page->ID] = $key;
			}
		}

		if (is_home()) {
			$blogID = get_option('page_for_posts');
			if ($blogID && isset($IdToKey[$blogID])) {
				$currentID = $blogID;
			}
		}
	}

    if (function_exists('is_woocommerce')){
        if ( is_woocommerce() ){
            $shop_page = (int) woocommerce_get_page_id('shop');
            if ( is_shop() ) {
                $currentID = $shop_page;
            }
        }
    }

    if ($wp_query->is_page) {
        $currentID = $wp_query->get_queried_object_id();
    }

	$active_Id = $currentID;
	$activeIDs = array();
	while ($active_Id && isset($IdToKey[$active_Id])) {
		$active = $pages[$IdToKey[$active_Id]];
		if ($active && $active->post_status == 'private')
			break;
		$activeIDs[] = $active->ID;
		$active_Id = $active->post_parent;
	}

	$items = array();
	if (theme_get_option('theme_menu_showHome') && ('page' != get_option('show_on_front') || (!get_option('page_on_front') && !get_option('page_for_posts')))) {
		$title = theme_get_option('theme_menu_homeCaption');
		$active = is_home();
		$items[] = new theme_MenuItem(array(
					'id' => 'home',
					'active' => $active,
					'attr' => array('class' => '', 'href' => get_home_url()),
					'title' => $title,
                    'menu_item_start_function' => $args['menu_item_start_function'],
                    'menu_item_end_function' => $args['menu_item_end_function'],
                    'submenu_item_start_function' => $args['submenu_item_start_function'],
                    'submenu_item_end_function' => $args['submenu_item_end_function']
				));
	}
	foreach ($pages as $page) {
		$id = $page->ID;
		$title = $page->post_title;
		$active = in_array($id, $activeIDs);
		$href = (($frontID && $frontID == $id) ?  home_url() : get_page_link($id));
		$separator = theme_get_meta_option($id, 'theme_show_as_separator');
		if ($separator) {
			$href = '#';
		}
		$items[] = new theme_MenuItem(array(
					'id' => $id,
					'active' => $active,
					'attr' => array('class' => '', 'href' => $href),
					'title' => $title,
					'parent' => $page->post_parent,
                    'menu_item_start_function' => $args['menu_item_start_function'],
                    'menu_item_end_function' => $args['menu_item_end_function'],
                    'submenu_item_start_function' => $args['submenu_item_start_function'],
                    'submenu_item_end_function' => $args['submenu_item_end_function']
				));
	}
	$walker = new theme_MenuWalker();
	return $walker->walk($items, $args);
}

/* categories */

function theme_get_list_categories($args = array()) {
	global $wp_query, $post;
	$categories = get_categories($args);
	if (empty($categories))
		return '';
	$IdToKey = array();
	foreach ($categories as $key => $category) {
		$IdToKey[$category->term_id] = $key;
	}

	$currentID = null;
	if ($wp_query->is_category) {
		$currentID = $wp_query->get_queried_object_id();
	}

    if (function_exists('is_woocommerce')){
        if ( is_woocommerce() ){
            if ( is_product_category() ) {
                if ( $product_cat_slug = get_query_var( 'product_cat' ) ) {
                    $product_cat = get_term_by( 'slug', $product_cat_slug, 'product_cat' );
                    $currentID = $product_cat->term_id;
                }
            }
        }
    }

	$activeIDs = theme_get_category_branch($currentID, $categories, $IdToKey);
	if (theme_get_option('theme_menu_highlight_active_categories') && is_single()) {
		foreach ((get_the_category($post->ID)) as $cat) {
			$activeIDs = array_merge($activeIDs, theme_get_category_branch($cat->term_id, $categories, $IdToKey));
		}
	}
	$items = array();
    $taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category';
	foreach ($categories as $category) {
		$id = $category->term_id;
		$title = $category->name;
		$active = in_array($id, $activeIDs);
		$items[] = new theme_MenuItem(array(
					'id' => $id,
					'active' => $active,
					'attr' => array('class' => '', 'href' => get_term_link( (int) $id, $taxonomy )),
					'title' => $title,
					'parent' => $category->parent,
                    'menu_item_start_function' => $args['menu_item_start_function'],
                    'menu_item_end_function' => $args['menu_item_end_function'],
                    'submenu_item_start_function' => $args['submenu_item_start_function'],
                    'submenu_item_end_function' => $args['submenu_item_end_function']
				));
	}
	$walker = new theme_MenuWalker();
	return $walker->walk($items, $args);
}

/* manual*/

function theme_get_manual_menu($args = array()){
    $pages = array();

    $page = array();
    if ( ! is_user_logged_in() ) {
        if ( get_option('users_can_register') ){
            $page['href'] = site_url('wp-login.php?action=register', 'login');
            $page['post_title'] = __('Register', THEME_NS);
            $page['id'] = 'register';
        }
    } else {
        $page['href'] = admin_url();
        $page['post_title'] = __('Site Admin', THEME_NS);
        $page['id'] = 'site-admin';
    }
    if (isset($page['href'])) {
        $pages[] = $page;
    }

    $page = array();
    if ( ! is_user_logged_in() ){
        $page['href'] = esc_url( wp_login_url() );
        $page['post_title'] = __('Log in', THEME_NS);
        $page['id'] = 'login';
    } else{
        $page['href'] =  esc_url( wp_logout_url() );
        $page['post_title'] = __('Log out', THEME_NS);
        $page['id'] = 'logout';
    }
    if (isset($page['href'])) {
        $pages[] = $page;
    }

    $items = array();
    foreach ($pages as $page) {
        $id = $page['id'];
        $title = $page['post_title'];
        $href = $page['href'];
        $items[] = new theme_MenuItem(
            array(
                'id' => $id,
                'attr' => array('class' => '', 'href' => $href),
                'title' => $title,
                'parent' => 0,
                'menu_item_start_function' => $args['menu_item_start_function'],
                'menu_item_end_function' => $args['menu_item_end_function'],
                'submenu_item_start_function' => $args['submenu_item_start_function'],
                'submenu_item_end_function' => $args['submenu_item_end_function']
            )
        );
    }
    $walker = new theme_MenuWalker();
    return $walker->walk($items, $args);
}

//Helper, return array( 'id', 'parent_id', ... , 'root_id' )
function theme_get_category_branch($id, $categories, $IdToKey) {
	$result = array();
	while ($id && isset($IdToKey[$id])) {
		$result[] = $id;
		$id = $categories[$IdToKey[$id]]->parent;
	}
	return $result;
}

/* menu item */

class theme_MenuItem {

	var $id;
	var $active;
	var $parent;
	var $attr;
	var $title;
    var $classes;
    var $object_id;
	function theme_MenuItem($args = '') {
		$args = wp_parse_args($args, array(
            'id' => '',
            'active' => false,
            'parent' => 0,
            'attr' => array(),
            'title' => '',
            'menu_item_start_function' => '',
            'menu_item_end_function' => '',
            'submenu_item_start_function' => '',
            'submenu_item_end_function' => ''
        ));

		$this->id = $args['id'];
		$this->object_id = $args['id'];
		$this->active = $args['active'];
		$this->parent = $args['parent'];
		$this->attr = $args['attr'];
		$this->classes = array();
		$this->title = $args['title'];
        $this->menu_item_start_function = $args['menu_item_start_function'];
        $this->menu_item_end_function = $args['menu_item_end_function'];
        $this->submenu_item_start_function = $args['submenu_item_start_function'];
        $this->submenu_item_end_function = $args['submenu_item_end_function'];
	}

    function get_start($level) {
        if ($level == 0) {
            $item_start_function = $this->menu_item_start_function;
        } else {
            $item_start_function = $this->submenu_item_start_function;
        }

        $link_class = implode(' ', $this->classes) . ' ' . ($this->active ? 'active' : '');
        $class = theme_get_array_value($this->attr, 'class', '');
        unset($this->attr['class']);
        $title = apply_filters('the_title', $this->title, $this->id);
        if (!theme_get_array_value($this->attr, 'no_menu_trim_title') && theme_get_option('theme_menu_trim_title')) {
            $title = strip_tags($title, '<span>');
            $title = theme_trim_long_str($title, theme_get_option($level == 0 ? 'theme_menu_trim_len' : 'theme_submenu_trim_len'));
        }

        $output = call_user_func_array($item_start_function, array(
            'class' => $class,
            'title' => $title,
            'attrs' => theme_prepare_attr($this->attr),
            'link_class' => $link_class
        ));

        return str_repeat("\t", $level + 1) . $output . "\n";
	}

	function get_end($level) {
        if ($level == 0){
            $item_end_function = $this->menu_item_end_function;
        } else {
            $item_end_function = $this->submenu_item_end_function;
        }
        $output = call_user_func_array($item_end_function, array());
        return str_repeat("\t", $level + 1) . $output . "\n";
	}

}

/* menu walker */

class theme_MenuWalker {

    var $child_Ids = array();
    var $IdToKey = array();
    var $level = 0;
    var $items;
    var $depth;
    var $args;
    var $menu_function;
    var $menu_item_start_function;
    var $menu_item_end_function;
    var $submenu_start_function;
    var $submenu_end_function;
    var $submenu_item_start_function;
    var $submenu_item_end_function;

    var $responsive_levels;
    var $levels;

	function walk($items = array(), $args = '') {
		$args = wp_parse_args($args, array('depth' => 0, 'class' => '', 'responsive_levels' => '', 'levels' => ''));
		$this->items = &$items;
		$this->depth = (int) $args['depth'];
		$this->menu_function = $args['menu_function'];
		$this->menu_item_start_function = $args['menu_item_start_function'];
		$this->menu_item_end_function   = $args['menu_item_end_function'];
		$this->submenu_start_function = $args['submenu_start_function'];
		$this->submenu_end_function = $args['submenu_end_function'];
		$this->submenu_item_start_function = $args['submenu_item_start_function'];
		$this->submenu_item_end_function   = $args['submenu_item_end_function'];

        $this->responsive_levels = $args['responsive_levels'];
        $this->levels = $args['levels'];

		foreach ($items as $key => $item) {
			$this->IdToKey[$item->id] = $key;
			if (!isset($this->child_Ids[$item->parent])) {
				$this->child_Ids[$item->parent] = array();
			}
			$parent = $item->parent;
			if (!$parent)
				$parent = 0;
			$this->child_Ids[$parent][] = $item->id;
		}

		$output = '';
		if (isset($this->child_Ids[0])) {
			$this->display($output, $this->child_Ids[0]);
		}
		$output = apply_filters('wp_list_pages', $output, $args);
		if (theme_is_empty_html($output))
			return '';
		$return = "\n";

        if (isset($this->menu_function) && strlen($this->menu_function) > 0){
            $return .= call_user_func_array($this->menu_function, array('content' => $output));
        }

        return $return;
	}

	function display(&$output, $child_Ids) {
		if (!is_array($child_Ids))
			return;

		foreach ($child_Ids as $child_Id) {
			if (!isset($this->IdToKey[$child_Id]))
				continue;
			$item = $this->items[$this->IdToKey[$child_Id]];

            $has_sub_items =
                ($this->responsive_levels != 'one level' || $this->levels != 'one level')
                && ($this->depth == 0 || $this->level < $this->depth - 1)
                && isset($this->child_Ids[$item->id])
                && count($this->child_Ids[$item->id]) > 0;

            if ($has_sub_items) {
                $item->attr['class'] .= ' bd-submenu-icon-only';
            }

            $output .= $item->get_start($this->level);

			if ($has_sub_items) {
                $this->level++;

                $output .= str_repeat("\t", $this->level);
                if ( isset($this->submenu_start_function) && strlen($this->submenu_start_function) > 0 ){
                    $output .= call_user_func_array($this->submenu_start_function, array('class' => $item->active ? ' active' : ''));
                }
                $output .=  "\n";

                $this->display($output, $this->child_Ids[$item->id]);

                $output .= str_repeat("\t", $this->level);
                if ( isset($this->submenu_end_function) && strlen($this->submenu_end_function) > 0 ){
                    $output .= call_user_func_array($this->submenu_end_function, array());
                }
                $output .=  "\n";

                $this->level--;
			}
			$output .= $item->get_end($this->level);
		}
	}

}

function theme_get_pages($pages) {
	if (is_admin())
		return $pages;

	$excluded_ids = array();
	foreach ($pages as $page) {
		if (!theme_get_meta_option($page->ID, 'theme_show_in_menu')) {
			$excluded_ids[] = $page->ID;
		}
	}
	$excluded_parent_ids = array();
	foreach ($pages as $page) {

		$title = theme_get_meta_option($page->ID, 'theme_title_in_menu');
		if ($title) {
			$page->post_title = $title;
		}

		if (in_array($page->ID, $excluded_ids)) {
			$excluded_parent_ids[$page->ID] = $page->post_parent;
		}
	}

	$length = count($pages);
	for ($i = 0; $i < $length; $i++) {
		$page = & $pages[$i];
		if (in_array($page->post_parent, $excluded_ids)) {
			$page->post_parent = theme_get_array_value($excluded_parent_ids, $page->post_parent, $page->post_parent);
		}
		if (in_array($page->ID, $excluded_ids)) {
			unset($pages[$i]);
		}
	}

	if (!is_array($pages))
		$pages = (array) $pages;
	$pages = array_values($pages);

	return $pages;
}

add_filter('get_pages', 'theme_get_pages');