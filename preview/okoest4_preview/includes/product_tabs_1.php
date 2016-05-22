<?php
function theme_tab_reviews_2() {
    global $woocommerce;
    remove_filter('comments_template', array($woocommerce, 'comments_template_loader'));
    remove_filter('comments_template', array('WC_Template_Loader', 'comments_template_loader'));
    if (comments_open()) {
        comments_template('/product_reviews_1.php');
    }
}

function theme_tab_additional_information_2() {
    global $product;
    $show_attr = ( get_option( 'woocommerce_enable_dimension_product_attributes' ) === 'yes');
    if ( $product->has_attributes() || ( $show_attr && $product->has_dimensions() ) || ( $show_attr && $product->has_weight() ) ) {
        $heading = apply_filters('woocommerce_product_additional_information_heading', __('Additional Information', 'woocommerce'));
        echo '<h2>' . $heading . '</h2>';
        $product->list_attributes();
    }
}

function theme_tab_description_2() {
    global $post;
    if ( $post->post_content ) {
        $heading = apply_filters('woocommerce_product_description_heading', __('Product Description', 'woocommerce'));
        echo '<h2>' . $heading . '</h2>';
        the_content();
    }
}
?>