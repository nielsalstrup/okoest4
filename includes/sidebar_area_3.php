<?php
    function theme_sidebar_area_3() {
        $theme_hide_sidebar_area = true;
        ob_start();
?>
        <?php
    ob_start();
    theme_print_sidebar('Area-3', '7_15');
    $current_sidebar_content = trim(ob_get_clean());

    if (isset($theme_hide_sidebar_area)) {
        $theme_hide_sidebar_area = $theme_hide_sidebar_area && !$current_sidebar_content;
    }

    theme_print_sidebar_content($current_sidebar_content, 'Area-3', ' bd-sidebar-15 hidden-xs clearfix', '');
?>
        <?php $area_content = trim(ob_get_clean()); ?>

        <?php if (theme_is_preview()): ?>
            <?php $hide = 
 $theme_hide_sidebar_area ||
                !strlen(trim(preg_replace('/<!-- empty::begin -->[\s\S]*?<!-- empty::end -->/', '', $area_content))); /* no other controls */ ?>

            <aside class="bd-sidebararea-3-column  bd-flex-vertical bd-flex-fixed<?php if ($hide) echo ' hidden bd-hidden-sidebar'; ?>">
                <div class="bd-sidebararea-3 bd-flex-wide">
                    
                    <?php echo $area_content ?>
                    
                </div>
            </aside>
        <?php else: ?>
            <?php if ($area_content
 && !$theme_hide_sidebar_area): ?>
                <aside class="bd-sidebararea-3-column  bd-flex-vertical bd-flex-fixed">
                    <div class="bd-sidebararea-3 bd-flex-wide">
                        
                        <?php echo $area_content ?>
                        
                    </div>
                </aside>
            <?php endif; ?>
        <?php endif; ?>
<?php
    }
?>