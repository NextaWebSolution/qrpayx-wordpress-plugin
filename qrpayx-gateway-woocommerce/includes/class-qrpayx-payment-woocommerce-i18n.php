<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://qrpayx.com/
 * @since      1.0.0
 *
 * @package    qrpayx_payment_woocommerce
 * @subpackage qrpayx_payment_woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    qrpayx_payment_woocommerce
 * @subpackage qrpayx_payment_woocommerce/includes
 * @author     Qr PayX
 */
class qrpayx_payment_woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'qrpayx-payment-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
