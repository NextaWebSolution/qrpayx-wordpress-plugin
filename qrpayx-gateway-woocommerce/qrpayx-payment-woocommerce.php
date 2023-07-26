<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://qrpayx.com/
 * @since             1.0.0
 * @package           qrpayx_payment_woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Qr PayX Upi Payment Gateway
 * Plugin URI:        https://qrpayx.com
 * Description:       Get Payment on your own UPI ID without any Transaction charges, with simple subscription.
 * Version:           1.0.0
 * Author:            Qr PayX
 * Author URI:        https://qrpayx.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       qrpayx-gateway-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'qrpayx_payment_woocommerce_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-qrpayx-payment-woocommerce-activator.php
 */
function activate_qrpayx_payment_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qrpayx-payment-woocommerce-activator.php';
	qrpayx_payment_woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-qrpayx-payment-woocommerce-deactivator.php
 */
function deactivate_qrpayx_payment_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qrpayx-payment-woocommerce-deactivator.php';
	qrpayx_payment_woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_qrpayx_payment_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_qrpayx_payment_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-qrpayx-payment-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_qrpayx_payment_woocommerce() {

	$plugin = new qrpayx_payment_woocommerce();
	$plugin->run();

}
run_qrpayx_payment_woocommerce();
