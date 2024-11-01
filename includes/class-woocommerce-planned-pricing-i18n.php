<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://catsplugins.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Planned_Pricing
 * @subpackage Woocommerce_Planned_Pricing/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Planned_Pricing
 * @subpackage Woocommerce_Planned_Pricing/includes
 * @author     Nicholas To <togiang88@gmail.com>
 */
class Woocommerce_Planned_Pricing_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-planned-pricing',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
