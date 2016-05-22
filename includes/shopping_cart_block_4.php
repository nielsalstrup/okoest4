<?php
function theme_shopping_cart_block_4($class, $attributes, $title, $content) {
?>
    
    <div class=" bd-block-4 <?php echo $class; ?>" <?php echo $attributes; ?>>
        <div class="bd-container-inner">
            <?php if (!theme_is_empty_html($title)){ ?>
<div class=" bd-blockheader bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-image bd-custom-table">
    <h4><?php echo $title; ?></h4>
</div>
<?php }?>
            <div class=" bd-blockcontent bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-image bd-custom-table">
    <?php echo $content; ?>
</div>
        </div>
    </div>
    
<?php
}