<?php
    function theme_sidebar_area_2() {
        $theme_hide_sidebar_area = true;
        ob_start();
?>
        <div class="data-control-id-1445220 bd-layoutcontainer-10 bd-columns
    
    ">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row">
                <div class="data-control-id-1445222 bd-columnwrapper-24 
 col-md-12">
    <div class="bd-layoutcolumn-24 bd-column" ><div class="bd-vertical-align-wrapper"><div class="data-control-id-1445214 bd-layoutcontainer-8 bd-columns
    
    ">
    <div class="bd-container-inner">
        <div class="container-fluid">
            <div class="row">
                <div class="data-control-id-1445216 bd-columnwrapper-19 
 col-lg-6
 col-md-6">
    <div class="bd-layoutcolumn-19 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar('Area-2', '9_14');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'Area-2', 'data-control-id-1406853 bd-sidebar-14 clearfix', '');
?></div></div>
</div>
	
		<div class="data-control-id-1445218 bd-columnwrapper-20 
 col-lg-6
 col-md-6">
    <div class="bd-layoutcolumn-20 bd-column" ><div class="bd-vertical-align-wrapper"><?php
    ob_start();
    theme_print_sidebar('Area-4', '11_16');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'Area-4', 'data-control-id-1445352 bd-sidebar-16 clearfix', '');
?></div></div>
</div>
            </div>
        </div>
    </div>
</div></div></div>
</div>
            </div>
        </div>
    </div>
</div>
        <?php $area_content = trim(ob_get_clean()); ?>

        <?php if (theme_is_preview()): ?>
            <?php $hide = 
 $theme_hide_sidebar_area ||
                !strlen(trim(preg_replace('/<!-- empty::begin -->[\s\S]*?<!-- empty::end -->/', '', $area_content))); /* no other controls */ ?>

            <aside class="bd-sidebararea-2-column data-control-id-1406769 bd-flex-vertical bd-flex-fixed<?php if ($hide) echo ' hidden bd-hidden-sidebar'; ?>">
                <div class="bd-sidebararea-2 bd-flex-wide">
                    
                    <?php echo $area_content ?>
                    
                </div>
            </aside>
        <?php else: ?>
            <?php if ($area_content
 && !$theme_hide_sidebar_area): ?>
                <aside class="bd-sidebararea-2-column data-control-id-1406769 bd-flex-vertical bd-flex-fixed">
                    <div class="bd-sidebararea-2 bd-flex-wide">
                        
                        <?php echo $area_content ?>
                        
                    </div>
                </aside>
            <?php endif; ?>
        <?php endif; ?>
<?php
    }
?>