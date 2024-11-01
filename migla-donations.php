<?php
/*
 Plugin Name: Total Donations
 Plugin URI: https://totaldonations.com/
 Text Domain: migla-donation
 Domain Path: /languages
 Description: A plugin for accepting donations.
 Version: 3.0.8
 Author: Binti Brindamour and Astried Silvanie
 Author URI: https://totaldonations.com/
 License: GPL2

 {Plugin Name} is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.

 {Plugin Name} is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with {Plugin Name}. If not, see {License URI}.
 */

if ( !defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path( __FILE__ ) . '/migla-donation-class.php';
require_once plugin_dir_path( __FILE__ ) . '/migla-donation-dependencies.php';

add_action( 'plugins_loaded', array ( 'TotalDonations', 'init' ), 9 );
add_action( 'plugins_loaded', 'TotalDonations_donate_plugins_loaded', 11);

add_action( 'init', 'TotalDonations_PayPal_Listener', 10);
add_action( 'init', 'TotalDonations_Stripe_Listener', 10);

register_activation_hook( __FILE__, array ( 'TotalDonations', 'donation_active_trigger' ) );
register_deactivation_hook( __FILE__, array ( 'TotalDonations', 'donation_deactived_trigger' ) );

if( isset( $_GET['sl'] ) ){
    do_action( 'TotalDonations_Stripe_Listener' );
}
if( isset( $_GET['pl'] ) ){
    do_action( 'TotalDonations_PayPal_Listener' );
}

//This is a clearing for security session on wp-admin
add_action('wp_logout', 'TotalDonations_session_logout' );

if (!function_exists('TotalDonations_session_logout')){
	function TotalDonations_session_logout()
	{
	    if (session_status() == PHP_SESSION_ACTIVE)
		{
		   if(isset($_SESSION[(session_id())]))
			 {
			    unset($_SESSION[(session_id())]);
		   }
		}
	}
}
?>
