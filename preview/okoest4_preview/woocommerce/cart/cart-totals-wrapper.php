<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

remove_filter('woocommerce_locate_template', 'theme_set_wc_template_path');
ob_start();
woocommerce_cart_totals();
add_filter('woocommerce_locate_template', 'theme_set_wc_template_path', 10, 2);
$table = str_replace(
    array(
        'class="cart_totals',
        '<table cellspacing="0"',
        '<h2>'.__('Cart Totals', 'woocommerce').'</h2>',
        '<tbody>',
        '</tbody>'
    ),
    array(
        'class="data-control-id-2894 bd-shoppingcartgrandtotal-1 cart_totals cart-totals grand-totals',
        '<table cellspacing="0" class="data-control-id-2861 bd-table-4"',
        '',
        '',
        ''
    ),
    ob_get_clean()
);
$table = preg_replace('#(<tr class="cart-subtotal">.*<\/tr>)#Us', '<thead>$1</thead>', $table); // add thead
$table = preg_replace('#<tr class="(order-total|total)">(.*)<\/tr>#Us', '<tfoot><tr class="data-control-id-2893 bd-container-34 bd-tagstyles">$2</tr></tfoot>', $table); // add tfoot
$table = preg_replace('#(<thead>.*<\/thead>)(.*)(<tfoot>.*<\/tfoot>)#Us', '$1 $3<tbody>$2</tbody>', $table); // add tbody
echo $table;