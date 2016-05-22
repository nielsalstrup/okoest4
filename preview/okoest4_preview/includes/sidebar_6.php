<?php
function theme_block_footer_6_6($title = '', $content = '', $class = '', $id = ''){
?>
    <div class="data-control-id-2693 bd-block-6 <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
<div class="bd-container-inner">
    <?php if (!theme_is_empty_html($title)){ ?>
    
    <div class="data-control-id-2660 bd-blockheader bd-tagstyles">
        <h4><?php echo $title; ?></h4>
    </div>
    
<?php } ?>
    <div class="data-control-id-2692 bd-blockcontent bd-tagstyles <?php if (theme_is_search_widget($id)) echo ' shape-only'; ?>">
<?php echo $content; ?>
</div>
</div>
</div>
<?php
}
?>