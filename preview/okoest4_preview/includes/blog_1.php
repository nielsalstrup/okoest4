<?php
function theme_blog_1() {
    global $post;
    $need_reset_query = false;
    if (is_page()) {
        $page_id = get_queried_object_id();
        if (theme_get_meta_option($page_id, 'theme_show_categories')) {
            $need_reset_query = true;
            query_posts(
                wp_parse_args(
                    'category_name=' . theme_get_meta_option($page_id, 'theme_categories'),
                    array(
                        'paged' => get_query_var('paged', get_query_var('page', 1))
                    )
                )
            );
        }
    }

    if (!$need_reset_query && theme_is_preview()) {
        global $theme_current_template_info;
        if (isset($theme_current_template_info)) {
            $template_name = $theme_current_template_info['name'];
            $ids = theme_get_option('theme_template_' . $template_name . '_query_ids');
            if ($ids) {
                $need_reset_query = true;
                $ids = explode(',', $ids);

                query_posts(array(
                    'post_type' => 'any',
                    'post__in' => $ids,
                    'paged' => get_query_var('paged', get_query_var('page', 1)),
                ));
            }
        }
    }
?>
    <div class="data-control-id-1244 bd-blog-1">
        
        
<?php
    if (have_posts()) { ?>
        <div class="data-control-id-1090 bd-grid-4">
          <div class="container-fluid">
            <div class="separated-grid row">
<?php       while (have_posts()) {
            the_post();

            $id = theme_get_post_id();
            $class = theme_get_post_class();
?>
                
                <div class="separated-item-18 col-md-12 ">
                
                    <div class="bd-griditem-18">
                        <article id="<?php echo $id; ?>" class="data-control-id-1166 bd-article-2 clearfix <?php echo $class; if (theme_is_preview()) echo ' bd-post-id-' . theme_get_the_ID(); ?>">
    <?php
if (!is_page() || theme_get_meta_option($post->ID, 'theme_show_page_title')) {
    $title = get_the_title();
    if(!is_singular()) {
        $title = sprintf('<a href="%s" rel="bookmark" title="%s">%s</a>', get_permalink($post->ID), strip_tags($title), $title);;
    }
    if (!theme_is_empty_html($title)) {
?>
<h2 class="data-control-id-1007 bd-postheader-2">
    <?php echo $title; ?>
</h2>
<?php
    }
}
?>
	
		<div class="data-control-id-1024989 bd-layoutbox-2 clearfix">
    <div class="bd-container-inner">
        <div class="data-control-id-1017 bd-posticondate-2">
    <span class="data-control-id-1016 bd-icon bd-icon-34"><span><?php echo get_the_date(); ?></span></span>
</div>
	
		<div class="data-control-id-1026 bd-posticonauthor-3">
    <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" title="<?php echo esc_attr(__('View all posts by ' . get_the_author(), 'default')) ?>">
        <span class="data-control-id-1025 bd-icon bd-icon-36"><span><?php echo get_the_author(); ?></span></span>
    </a>
</div>
    </div>
</div>
	
		<div class="data-control-id-1025097 bd-layoutbox-4 clearfix">
    <div class="bd-container-inner">
        <?php echo theme_get_post_thumbnail(array(
    
    'class' => 'data-control-id-1009 bd-postimage-2',
    'img_class' => 'data-control-id-1008 bd-imagestyles',
    'attributes' => '',
    'img_attributes' => '',
)); ?>
	
		<?php
if (theme_is_preview() && is_singular()) {
    $editor_attrs = 'data-editable-id="post-' . theme_get_the_ID() . '"';
} else {
    $editor_attrs = '';
}
?>
<div class="data-control-id-234738 bd-postcontent-8 bd-tagstyles">
    <div class="bd-container-inner" <?php echo $editor_attrs; ?>>
        <?php echo(is_singular() ? theme_get_content() : theme_get_excerpt()); ?>
    </div>
</div>
	
		<?php if ($post && !is_singular() && !theme_is_empty_html('Continue reading...')): ?>
    <a class="bd-postreadmore-4 bd-button data-control-id-1002005"   title="<?php echo strip_tags('Continue reading...') ?>" href="<?php echo get_permalink($post->ID) ?>"><?php _e('Continue reading...', 'default'); ?></a>
<?php endif; ?>
    </div>
</div>
	
		<div class="data-control-id-1025231 bd-layoutbox-6 clearfix">
    <div class="bd-container-inner">
        <div class="data-control-id-1001968 bd-posticontags-18">
    <?php $tags_list = get_the_tag_list('', ', '); ?>
    <?php if ($tags_list): ?>
    <span class="data-control-id-1001967 bd-icon bd-icon-10"><span><?php echo $tags_list; ?></span></span>
    <?php endif; ?>
</div>
    </div>
</div>
</article>
                        <?php
                        global $withcomments;
                        if (is_singular() || $withcomments){  ?>
                            <?php
    if (theme_get_option('theme_allow_comments')) {
        comments_template('/comments_1.php');
    }
?>
                        <?php } ?>
                    </div>
                </div>
<?php
        }
?>
                </div>
            </div>
        </div>
<?php
        } else {
?>
        <div class="bd-container-inner"><?php theme_404_content(); ?></div>
<?php
    }
?>
        <div class="data-control-id-1128 bd-blogpagination-1">
    <?php
if (is_single()){
    $prev_link = theme_get_next_post_link('%link', '%title &raquo;');
    $next_link = theme_get_previous_post_link('%link', '&laquo; %title');
    if ($prev_link || $next_link) { ?>
<ul class="bd-pagination-12 pagination">
    <?php if ($next_link): ?>
    <li class="bd-paginationitem-12">
        <?php echo $next_link; ?>
    </li>
    <?php endif ?>

    <?php if ($prev_link): ?>
    <li class="bd-paginationitem-12">
        <?php echo $prev_link; ?>
    </li>
    <?php endif ?>
</ul>
<?php
    }
} else {
    global $wp_query;
    if ( $wp_query->max_num_pages > 1 ) {
        echo preg_replace(
            array(
                '/<li(.*current)/',
                '/<ul class=\'page-numbers\'/',
                '/<li>/'
            ),
            array(
                '<li  class="data-control-id-1126 bd-paginationitem-12 active"$1',
                '<ul  class="data-control-id-1127 bd-pagination-12 pagination"',
                '<li  class="data-control-id-1126 bd-paginationitem-12">'
            ),
            paginate_links( array(
                'base' 			=> str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ),
                'format' 		=> '',
                'current' 		=> max( 1, get_query_var('paged') ),
                'total' 		=> $wp_query->max_num_pages,
                'prev_text' 	=> '&larr;',
                'next_text' 	=> '&rarr;',
                'type'			=> 'list',
                'end_size'		=> 3,
                'mid_size'		=> 3
            ) )
        );
    }
}
?>
</div>
    </div>
<?php
    if($need_reset_query){
        wp_reset_query();
    }
}