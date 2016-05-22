<?php
function theme_block_footer_2_3($title = '', $content = '', $class = '', $id = ''){
?>
    <div class=" bd-block-2 <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
<div class="bd-container-inner">
    <?php if (!theme_is_empty_html($title)){ ?>
    
    <div class=" bd-blockheader bd-tagstyles">
        <h4><?php echo $title; ?></h4>
    </div>
    
<?php } ?>
    <div class=" bd-blockcontent bd-tagstyles bd-custom-bulletlist bd-custom-orderedlist <?php if (theme_is_search_widget($id)) echo ' shape-only'; ?>">
<?php echo $content; ?>
</div>
</div>
</div>
<?php
}
?>