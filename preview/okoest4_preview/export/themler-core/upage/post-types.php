<?php

register_post_type(
    'upage_section',
    array(
        'labels' => array(
            'name'                  => __( 'Sections', 'default' ),
            'singular_name'         => _x( 'Section', 'upage_section post type singular name', 'default' ),
            'add_new'               => __( 'Add Section', 'default' ),
            'add_new_item'          => __( 'Add New Section', 'default' ),
            'edit'                  => __( 'Edit', 'default' ),
            'edit_item'             => __( 'Edit Section', 'default' ),
            'new_item'              => __( 'New Section', 'default' ),
            'view'                  => __( 'View Section', 'default' ),
            'view_item'             => __( 'View Section', 'default' ),
            'search_items'          => __( 'Search Sections', 'default' ),
            'not_found'             => __( 'No Sections found', 'default' ),
            'not_found_in_trash'    => __( 'No Sections found in trash', 'default' ),
            'parent'                => __( 'Parent Sections', 'default' ),
            'menu_name'             => _x( 'Sections', 'Admin menu name', 'default' ),
            'featured_image'        => __( 'Screenshot', 'default' ),
            'set_featured_image'    => __( 'Set screenshot', 'default' ),
            'remove_featured_image' => __( 'Remove screenshot', 'default' ),
        ),
        'public'              => false,
        'show_ui'             => true,
        //'capability_type'     => 'upage_section',
        'map_meta_cap'        => true,
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'show_in_menu'        => false, // TODO
        'hierarchical'        => false,
        'show_in_nav_menus'   => false,
        'rewrite'             => false,
        'query_var'           => false,
        'supports'            => array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'revisions'
        ),
        'has_archive'         => false,
    )
);

function upage_section_thumbnail_columns_head($defaults, $post_type) {
    if ($post_type == 'upage_section') {
        $defaults['featured_image'] = '';
    }
    return $defaults;
}

function upage_section_thumbnail_column($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = upage_get_section_thumbnail($post_ID, 'medium');
        if ($post_featured_image) {
            echo '<img src="' . $post_featured_image . '" />';
        }
    }
}
add_filter('manage_posts_columns', 'upage_section_thumbnail_columns_head', 10, 2);
add_action('manage_posts_custom_column', 'upage_section_thumbnail_column', 10, 2);


function upage_shortcode_add_metabox() {
    add_meta_box('upage_shortcode_metabox', __('Shortcode', 'default'), 'upage_shortcode_metabox', 'upage_section', 'side', 'default');
}
add_action('admin_init', 'upage_shortcode_add_metabox');

function upage_shortcode_metabox($post) {
    if ($post->post_type !== 'upage_section') {
        return;
    }

    $value = "[upage_section id={$post->ID}]";
?>
    <input type="text" onClick="this.setSelectionRange(0, this.value.length)" style="width:100%" value="<?php echo $value; ?>">
<?php
}