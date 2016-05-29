<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

remove_filter('woocommerce_locate_template', 'theme_shipping_calculator_set_path');
ob_start();
woocommerce_shipping_calculator();
add_filter('woocommerce_locate_template', 'theme_shipping_calculator_set_path', 10, 2);

// remove h2
$shipping_calculator = str_replace(
	array(
		'<h2><a href="#" class="shipping-calculator-button',
		'&darr;</span></a></h2>',
	),
	array(
		'<a href="#" class="shipping-calculator-button',
		'&darr;</span></a>',
	),
	ob_get_clean()
);

$shipping_calculator = str_replace(
	array(
		'class="button',
		'class="shipping-calculator-button',
	),
	array(
		' class="bd-button-10',
		' class="shipping-calculator-button data-control-id-1460450 bd-button-10',
	),
	$shipping_calculator
);
echo $shipping_calculator;