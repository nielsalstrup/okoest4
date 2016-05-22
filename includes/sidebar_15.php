<?php
theme_register_sidebar('Area-3',  __('WP Bottom Widget Widget Area', THEME_NS));

function theme_block_7_15($title = '', $content = '', $class = '', $id = ''){
    ob_start();
?>
    <div class=" bd-block-7 <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
<div class="bd-container-inner">
    <?php if (!theme_is_empty_html($title)){ ?>
    
    <div class=" bd-blockheader bd-tagstyles">
        <h4><?php echo $title; ?></h4>
    </div>
    
<?php } ?>
    <div class=" bd-blockcontent bd-tagstyles bd-custom-bulletlist <?php if (theme_is_search_widget($id)) echo ' shape-only'; ?>">
<?php echo $content; ?>
</div>
</div>
</div>
<?php
    return ob_get_clean();
}
?>