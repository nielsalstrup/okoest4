<?php
function theme_shopping_cart() {
?>
    <div class="data-control-id-2964 bd-shoppingcart">
<?php
            global $post;
            if (have_posts()) {
                while ( have_posts() ) {
                    the_post();
                    $title = '<a href="' . get_permalink($post->ID) . '" rel="bookmark" title="' . strip_tags(get_the_title()) . '">' . get_the_title() . '</a>';
                    if (!theme_is_empty_html($title)) {
?>
                        <div class="data-control-id-2820 bd-carttitle-1">
    <h2><?php echo $title; ?></h2>
</div>
<?php
                    }
                    $content = theme_get_content();
                    echo $content;
                }
            } else {
                theme_404_content();
            }
?>

        <?php if (theme_woocommerce_enabled()): ?>
            <div class="row">
                <div class="col-md-6">
                    <?php
                        ob_start();
                        woocommerce_shipping_calculator();
                        $content = ob_get_clean();

                        $id = 'shipping-calculator';
                        if (!theme_is_empty_html($content)) {
                            theme_shopping_cart_block_4_1(
                                'data-control-id-2963 bd-block-4',
                                'id="shipping-calculator" ',
                                __('Calculate Shipping', 'woocommerce'),
                                $content
                            );
                        }
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                        ob_start();
                    ?>
                        <?php woocommerce_cart_totals(); ?>
                    <?php
                        $content = ob_get_clean();

                        if (!theme_is_empty_html($content)) {
                            theme_shopping_cart_block_4_1(
                                'data-control-id-2963 bd-block-4',
                                'id="cart-totals" ',
                                __('Cart Totals', 'woocommerce'),
                                $content
                            );
                        }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php
}

function theme_shopping_cart_block_4_1($class, $attributes, $title, $content){
?>
    <?php theme_shopping_cart_block_4($class, $attributes, $title, $content); ?>
<?php
}