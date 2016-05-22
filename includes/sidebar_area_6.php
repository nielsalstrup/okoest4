<?php
    function theme_sidebar_area_6() {
        $theme_hide_sidebar_area = true;
        ob_start();
?>
        <div class=" bd-spacer-9 clearfix"></div>
        <?php $area_content = trim(ob_get_clean()); ?>

        <?php if (theme_is_preview()): ?>
            <?php $hide = 
                !strlen(trim(preg_replace('/<!-- empty::begin -->[\s\S]*?<!-- empty::end -->/', '', $area_content))); /* no other controls */ ?>

            <aside class="bd-sidebararea-6-column  bd-flex-vertical bd-flex-fixed<?php if ($hide) echo ' hidden bd-hidden-sidebar'; ?>">
                <div class="bd-sidebararea-6 bd-flex-wide">
                    
                    <?php echo $area_content ?>
                    
                </div>
            </aside>
        <?php else: ?>
            <?php if ($area_content): ?>
                <aside class="bd-sidebararea-6-column  bd-flex-vertical bd-flex-fixed">
                    <div class="bd-sidebararea-6 bd-flex-wide">
                        
                        <?php echo $area_content ?>
                        
                    </div>
                </aside>
            <?php endif; ?>
        <?php endif; ?>
<?php
    }
?>