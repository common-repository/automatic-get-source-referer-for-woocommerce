<?php
/*
Plugin Name: Automatic Get Source Referer for WooCommerce
Plugin URI: https://lucroporsegundo.com
Description: Automatically get the origin source/referer url when user order something.
Version: 1.0
Author: 3ree
Author URI: https://3ree.org
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Set cookie for new users
    add_action( 'init', 'AGSRW_setCookie'); // Run when WordPress loads
    function AGSRW_setCookie() {
        
        $cookie = sanitize_text_field($_SERVER["HTTP_REFERER"]); // Get URL the user came to your site for
        
        if ( !is_admin() && !isset($_COOKIE['agsrw_origin'])) { // If not an admin or if cookie doesn't exist already
            setcookie( 'agsrw_origin', $cookie, time()+86400, COOKIEPATH, COOKIE_DOMAIN, false); // Set cookie for 24 hours
        }
    }

    // Check cookie when order made and add to order
    add_action('woocommerce_checkout_update_order_meta', 'AGSRW_addRef'); // Run when an order is made in WooCommerce
    function AGSRW_addRef( $order_id ){

        $ref_url = $_COOKIE['agsrw_origin']; // Get the cookie

        if(filter_var($ref_url, FILTER_VALIDATE_URL)) update_post_meta($order_id, 'agsrw_origin', esc_url($ref_url)); // Add to order meta
    }

} else {

    function AGSRW_noWC() {
        $class = 'notice notice-error';
        $message = __( 'This plugin works only with WooCommerce installed. Please, install it.', 'automatic-get-source-referer-for-woocommerce' );
     
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
    }
    add_action( 'admin_notices', 'AGSRW_noWC' );

}