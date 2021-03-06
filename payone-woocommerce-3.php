<?php

/**
 * Plugin Name: PAYONE Payment for WooCommerce
 * Version: 1.7.1
 * Plugin URI: https://www.payone.com/
 * Description: Integration of PAYONE payment into your WooCommerce store.
 * Author: PAYONE GmbH
 * Author URI: https://www.payone.com/
 * Requires at least: 5.0
 * Tested up to: 5.5
 */

defined( 'ABSPATH' ) or die( 'Direct access not allowed' );

define( 'PAYONE_PLUGIN_VERSION', '1.7.1' );
define( 'PAYONE_PLUGIN_PATH', __DIR__ );
define( 'PAYONE_VIEW_PATH', PAYONE_PLUGIN_PATH . '/views' );

require_once 'src/autoload.php';

$payonePlugin = null;
add_action( 'woocommerce_loaded', function() {
    global $payonePlugin;

    $payonePlugin = new \Payone\Plugin();
    $payonePlugin->init();
} );
