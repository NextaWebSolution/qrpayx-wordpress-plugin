<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://qrpayx.com/
 * @since      1.0.0
 *
 * @package    qrpayx_payment_woocommerce
 * @subpackage qrpayx_payment_woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    qrpayx_payment_woocommerce
 * @subpackage qrpayx_payment_woocommerce/public
 * @author     Qr PayX <contact@qrpayX.com>
 */
class qrpayx_payment_woocommerce_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in qrpayx_payment_woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The qrpayx_payment_woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/qrpayx-payment-woocommerce-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in qrpayx_payment_woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The qrpayx_payment_woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/qrpayx-payment-woocommerce-public.js', array('jquery'), $this->version, false);
	}


	public function woocommerce_payment_gateways($methods)
	{
		$methods[] = 'UPI_Payment_Gateway';
		return $methods;
	}

	public function template_redirect()
	{
		if ( isset($_GET['remark1'])) {
			$id = $_GET['remark1'];
			$order = wc_get_order($id);
			$order_key = $order->get_order_key();
			$url = home_url("checkout/order-received/$id/?key=$order_key");

			wp_redirect($url);
			//var_dump($_GET);
			die;
		}
	}
}

add_action('plugins_loaded', function () {

	class UPI_Payment_Gateway extends WC_Payment_Gateway
	{

		public function __construct()
		{

			$this->id = 'upi-payment'; // payment gateway ID
			$this->icon = ''; // payment gateway icon
			$this->has_fields = false; // for custom credit card form
			$this->title = __('QrPayX Gateway', 'text-domain'); // vertical tab title
			$this->method_title = __('QrPayX Gateway', 'text-domain'); // payment method name
			$this->method_description = __('QrPayX UPI Payment allow you to accept payments using Upi', 'text-domain'); // payment method description


			// load backend options fields
			$this->init_form_fields();

			// load the settings.
			$this->init_settings();
			$this->title = $this->get_option('title');
			$this->description = $this->get_option('description');
			$this->enabled = $this->get_option('enabled');
			//$this->test_mode = 'yes' === $this->get_option( 'test_mode' );
			//$this->private_key = $this->test_mode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
			$this->publish_key =  $this->get_option('publish_key');
			$this->default_email =  $this->get_option('default_email');
			$this->secret_key =  $this->get_option('secret_key');
			$this->upiid =  $this->get_option('upiid');

			add_action('woocommerce_api_' . $this->id, array($this, 'check_h_payment_response'));

			// Action hook to saves the settings
			if (is_admin()) {
				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
			}
		}

		public function check_h_payment_response()
		{
			update_option('aw_upi', $_REQUEST);
			if (isset($_REQUEST['status'])) {
			    

				$id =$_REQUEST['remark1'];
				$cxrbhaiorder=$_REQUEST['order_id'];
				$order = wc_get_order($id);
				if ($_REQUEST['status'] == 'SUCCESS') {
					$order->payment_complete();
					$order->reduce_order_stock();


					$order->add_order_note('UPI Payment completed!', true);
					$order->add_order_note('UPI Payment completed order id: ' . $cxrbhaiorder, true);
					header('Location:'.$this->get_return_url($order));
					exit();
				} else {
					$order->update_status('failed');
					$order->add_order_note('UPI Payment Failed!', true);
					header('Location: '.$order->get_checkout_payment_url());
					exit();
				}
			}
			 header('Location: '.home_url());
		}

		public function init_form_fields()
		{
			$this->form_fields = array(
				'enabled' => array(
					'title'       => __('Enable/Disable', 'text-domain'),
					'label'       => __('Enable UPI Gateway', 'text-domain'),
					'type'        => 'checkbox',
					'description' => __('This enable the UPI gateway which allow to accept payment through UPI.', 'text-domain'),
					'default'     => 'no',
					'desc_tip'    => true
				),
				'title' => array(
					'title'       => __('Title', 'text-domain'),
					'type'        => 'text',
					'description' => __('This controls the title which the user sees during checkout.', 'text-domain'),
					'default'     => __('UPI Payment', 'text-domain'),
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => __('Description', 'text-domain'),
					'type'        => 'textarea',
					'description' => __('This controls the description which the user sees during checkout.', 'text-domain'),
					'default'     => __('UPI.', 'text-domain'),
				),

				'publish_key' => array(
					'title'       => __('API Key', 'text-domain'),
					'type'        => 'text'
				),
				
				'secret_key' => array(
					'title'       => __('secret', 'text-domain'),
					'type'        => 'text'
				),
				
				'upiid' => array(
					'title'       => __('upi id', 'text-domain'),
					'type'        => 'text'
				),

				'default_email' => array(
					'title'       => __('Default Email', 'text-domain'),
					'type'        => 'text',
					'description' => __('Default email is used when user is not logged in and making payment.', 'text-domain'),
				),

				'ipn' => array(
					'title' => 'Webhook URL',
					'type' => 'hidden',
					'description' => 'Goto <a href="https://qrpayx.com" target="_blank">UPI Gateway > API Credentials > Webhooks</a> and click on "API & Docs" > "API Credentials" and enter the following URL in webhooks: ' . site_url('wc-api/upi-payment')
				),
				'account_details' => array(
					'type' => 'hidden',
					'description' => '<img src="' . plugin_dir_url(__FILE__) . 'upi-image.jpg"/>',
				),

			);
		}

		public function genrate_aw_image_html()
		{
			echo 123;
		}


public function process_payment($order_id)
{
    global $woocommerce;

    // Get order details
    $order = wc_get_order($order_id);
    $total = $order->get_total();
    $key = $this->publish_key;
    $default_email = $this->default_email;
    $secret = $this->secret_key;
    $cxrupiid = $this->upiid;

    $p_name = '';
    foreach ($order->get_items() as $item_id => $item) {
        $product_name = $item->get_name();
        $p_name .= $product_name . ',';
    }

    $url = $order->get_checkout_order_received_url();
    $order_key = $order->get_order_key();

    $cxrurl = home_url("checkout/order-received/$order_id/?key=$order_key&order_id=$order_id&test");
    $url=site_url('wc-api/upi-payment');
    $billing_email = $order->get_billing_email();
    if (!$billing_email || $billing_email == '' || $billing_email == null) {
        $billing_email = $default_email;
    }
$cxrorderid = uniqid().time();
    $data = array(
        'user_token' => $key,
        'amount' => $total,
        'order_id' => $cxrorderid,
        'customer_email' => $billing_email,
        'customer_mobile' => 1234567890,
        'redirect_url' => $url,
        'remark1' => $order_id,
        'remark2' => $cxrurl,
        'secret' => $secret,
        'upiid' => $cxrupiid
        
    );

    $response = wp_remote_post("http://pay.qrpayx.com/apiv3/create_order.php", array(
        'body' => $data,
        'method' => 'POST'
    ));

    if (!is_wp_error($response)) {
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] === 'SUCCESS') {
            $id = $body['order_id'];
            update_post_meta($order_id, 'upi_order_id', $id);
            $payment_url = $body['payment_link'];

            $order->add_order_note('Payment through UPI pending', false);

            // Empty cart
            $woocommerce->cart->empty_cart();

            // Redirect to the thank you page
            return array(
                'result' => 'success',
                'redirect' => $payment_url
            );
        } else {
            $error_message = isset($body['msg']) ? $body['msg'] . " - " . $body['field_name'] : 'Please try again.';
            wc_add_notice($error_message, 'error');
            return;
        }
    } else {
        wc_add_notice('Connection error.', 'error');
        return;
    }
}

	}
});
