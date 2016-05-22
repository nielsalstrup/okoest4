<?php
theme_register_sidebar('Area-4',  __('WP Top Right Widget Widget Area', THEME_NS));

function theme_block_11_16($title = '', $content = '', $class = '', $id = ''){
    ob_start();
?>
    <div class="data-control-id-1445287 bd-block-11 <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
<div class="bd-container-inner">
    <?php if (!theme_is_empty_html($title)){ ?>
    
    <div class="data-control-id-1445288 bd-blockheader bd-tagstyles">
        <h4><?php echo $title; ?></h4>
    </div>
    
<?php } ?>
    <div class="data-control-id-1445320 bd-blockcontent bd-tagstyles bd-custom-bulletlist <?php if (theme_is_search_widget($id)) echo ' shape-only'; ?>">
<?php echo $content; ?>
</div>
</div>
</div>
<?php
    return ob_get_clean();
}
?>