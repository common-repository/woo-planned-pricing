<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://catsplugins.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Planned_Pricing
 * @subpackage Woocommerce_Planned_Pricing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Planned_Pricing
 * @subpackage Woocommerce_Planned_Pricing/public
 * @author     Nicholas To <togiang88@gmail.com>
 */
class Woocommerce_Planned_Pricing_Public {
	
	/**
	 * Store products data after add to cart
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public $products_data;

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Planned_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Planned_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-planned-pricing-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Planned_Pricing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Planned_Pricing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-planned-pricing-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * get current price and update it base on the product price range (if set)
	 *
	 * @since    1.0.0
	 */
	public function wpp_update_cart_item_price_range($cart_item_data, $product_id, $variation_id) {

		//var_dump($cart_item_data);

		if ( empty($cart_item_data) ) {
			$cart_item_data['cart_item_data'] = 'remove';
		}

		if ( 'default' == $cart_item_data ) {
			$cart_item_data = array();
		}

		return $cart_item_data;
		
	}

/*	public function wpp_update_cart_item_price_from_session($session_data, $values, $key) {

		global $woocommerce;
		//$woocommerce->cart->add_to_cart(82, 10);
		echo '<pre>'; var_dump($values); die;
		return $session_data;
		
	}*/


	public function wpp_add_to_cart_cb($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
		
		global $woocommerce;

		if ( isset($cart_item_data['cart_item_data']) && 'remove' == $cart_item_data['cart_item_data'] ) {
			$woocommerce->cart->remove_cart_item($cart_item_key);
		}

	}

	public function wpp_template_redirect() {

		global $woocommerce;

		// check exist $_POST data
		if ( isset($_POST) && isset($_POST['add-to-cart']) && !empty($_POST['add-to-cart']) ) {

			$price_ranges = array();
			$units_sold = 0;
			$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
			$products_data = array();
			$product_id = $_POST['add-to-cart'];
			
			// get product price ranges if set
			$price_ranges = get_post_meta( $product_id, 'wpp_meta', true );

			// get number of product sold
			$units_sold = get_post_meta( $product_id, 'total_sales', true );

			// check and set price base on ranges price for each product before display on cart items
			if ( !empty($price_ranges) && is_array($price_ranges) && empty($this->products_data) ) {

				$qty_left = $quantity;
				$k = 0;
				
				foreach ($price_ranges as $key => $range) {
					
					if (!empty($range) && is_array($range)) {

						//if ( $qty_left == $quantity ) { // first range checking

	/*						if ( $units_sold < $range['wpp_product_from'] ) {

								$products_need = $range['wpp_product_from'] - $units_sold;

								if ( $qty_left >= $products_need ) {

									$qty_left = $qty_left - $products_need;
									$product_data[]['quantity'] = $products_need;
									$product_data[]['price'] = 'default';
									continue;
								}

								$product_data[]['quantity'] = $qty_left;
								$product_data[]['price'] = 'default';
								break;

							} */

							if ( $units_sold <= $range['wpp_product_to'] ) { // check if current number of products sold is in the range

								// get the left products is permitted in the range
								$products_left = $range['wpp_product_to'] - $units_sold;

								if ( $products_left > 0 ) {

									if ( $qty_left >= $products_left ) {

										$qty_left = $qty_left - $products_left;
									
										$products_data[$key]['quantity'] = $products_left;
										$products_data[$key]['price'] = $range['wpp_product_price'];
										$products_data[$key]['cart_item_data'] = $range['wpp_product_price'];

										$units_sold = $range['wpp_product_to']; 
										$k = $key;

										continue;
									} else {

										$products_data[$key]['quantity'] = $qty_left;
										$products_data[$key]['price'] = $range['wpp_product_price'];
										$products_data[$key]['cart_item_data'] = $range['wpp_product_price'];
										$k = $key;
										break;
									}						
								}

								if ( $products_left == 0 ) {
									$units_sold = $range['wpp_product_to'];
								}
							}
						//} elseif ( 0 <= $qty_left ) {

						//}
					}
				}

				if ( $qty_left > 0 ) {
					$products_data[$k+1]['quantity'] = $qty_left;
					$products_data[$k+1]['price'] = 'default';
					$products_data[$k+1]['cart_item_data'] = 'default';
				}		
			}

			foreach ($products_data as $key => $product) {

				$cart_item_data['price_range'] = isset($product['cart_item_data']) ? $product['cart_item_data'] : array();

				$woocommerce->cart->add_to_cart($_POST['add-to-cart'], $product['quantity'], 0, array(), $cart_item_data);

			}
		}

	}

	public function wpp_update_cart_item_price($cart_object) {
		
		foreach ($cart_object->cart_contents  as $key => $value) {
			
			if (isset($value['price_range']) && 'default' != $value['price_range']) {
				$value['data']->price = $value['price_range'];
			}
		}
	}

}
