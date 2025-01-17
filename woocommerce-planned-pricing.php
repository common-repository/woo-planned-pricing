<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://catsplugins.com
 * @since             1.0.0
 * @package           Woocommerce_Planned_Pricing
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Planned Pricing
 * Plugin URI:        http://catsplugins.com
 * Description:       Increase, decrease the product price base on the product unit sold
 * Version:           1.0.0
 * Author:            catsplugins
 * Author URI:        http://catsplugins.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-planned-pricing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-planned-pricing-activator.php
 */
function activate_woocommerce_planned_pricing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-planned-pricing-activator.php';
	Woocommerce_Planned_Pricing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-planned-pricing-deactivator.php
 */
function deactivate_woocommerce_planned_pricing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-planned-pricing-deactivator.php';
	Woocommerce_Planned_Pricing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_planned_pricing' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_planned_pricing' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-planned-pricing.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_planned_pricing() {

	$plugin = new Woocommerce_Planned_Pricing();
	$plugin->run();

}
run_woocommerce_planned_pricing();
