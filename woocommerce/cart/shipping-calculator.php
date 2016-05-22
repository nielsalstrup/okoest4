<?php
/**
 * Shipping Calculator
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.8
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly
 
global $woocommerce;

if ( get_option('woocommerce_enable_shipping_calc')=='no' || ! $woocommerce->cart->needs_shipping() ) return;
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<form class="shipping_calculator" action="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" method="post">
	<section class="theme-shipping-calculator-form">
		<p>
			<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state" rel="calc_shipping_state">
				<option value=""><?php _e('Select a country&hellip;', 'woocommerce'); ?></option>
				<?php
					foreach( $woocommerce->countries->get_allowed_countries() as $key => $value )
						echo '<option value="' . $key . '"' . selected( $woocommerce->customer->get_shipping_country(), $key, false ) . '>' . $value . '</option>';
				?>
			</select>
		</p>
		<p>
			<?php
				$current_cc = $woocommerce->customer->get_shipping_country();
				$current_r = $woocommerce->customer->get_shipping_state();

				$states = $woocommerce->countries->get_states( $current_cc );

				if ( is_array( $states ) && empty( $states ) ) {

					// Hidden
					?>
					<input type="hidden" name="calc_shipping_state" id="calc_shipping_state" />
					<?php

				} elseif ( is_array( $states ) ) {

					// Dropdown
					?>
					<span>
						<select name="calc_shipping_state" id="calc_shipping_state"><option value=""><?php _e('Select a state&hellip;', 'woocommerce'); ?></option><?php
							foreach ( $states as $ckey => $cvalue )
								echo '<option value="' . $ckey . '" '.selected( $current_r, $ckey, false ) .'>' . __( $cvalue, 'woocommerce' ) .'</option>';
						?></select>
					</span>
					<?php

				} else {

					// Input
					?>
					<input type="text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php _e('State', 'woocommerce'); ?>" name="calc_shipping_state" id="calc_shipping_state" />
					<?php

				}
			?>
		</p>
		<div class="clear"></div>
		<p>
			<input type="text" value="<?php echo esc_attr( $woocommerce->customer->get_shipping_postcode() ); ?>" placeholder="<?php _e('Postcode/Zip', 'woocommerce'); ?>" title="<?php _e('Postcode', 'woocommerce'); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
		</p>
		<div class="clear"></div>
		<p><button class=" bd-button-10" type="submit" name="calc_shipping" value="1"><?php _e('Update Totals', 'woocommerce'); ?></button></p>
		<?php echo theme_get_wc_nonce_field('cart'); ?>
	</section>
</form>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>