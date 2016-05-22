<?php
function theme_shopping_cart_block_4($class, $attributes, $title, $content) {
?>
    
    <div class="data-control-id-2963 bd-block-4 <?php echo $class; ?>" <?php echo $attributes; ?>>
        <div class="bd-container-inner">
            <?php if (!theme_is_empty_html($title)){ ?>
<div class="data-control-id-2930 bd-blockheader bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-image bd-custom-table">
    <h4><?php echo $title; ?></h4>
</div>
<?php }?>
            <div class="data-control-id-2962 bd-blockcontent bd-tagstyles bd-custom-blockquotes bd-custom-button bd-custom-image bd-custom-table">
    <?php echo $content; ?>
</div>
        </div>
    </div>
    
<?php
}