<?php
function theme_vmenu_block_13($title = '', $content = '', $class = '', $id = '') {
?>
    <div class="data-control-id-3500 bd-block-13 <?php echo $class; ?>" data-block-id="<?php echo $id; ?>">
        <?php if (!theme_is_empty_html($title)){ ?>
            
            <div class="data-control-id-3467 bd-blockheader bd-tagstyles">
                <h4><?php echo $title; ?></h4>
            </div>
            
        <?php } ?>

        <?php echo $content; ?>
    </div>
<?php
}