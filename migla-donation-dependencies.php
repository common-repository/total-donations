<?php
if ( !defined( 'ABSPATH' ) ) exit;

if (!function_exists('TotalDonations_donate_plugins_loaded')){
    function TotalDonations_donate_plugins_loaded(){
        load_plugin_textdomain( 'migla-donation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
}

if (!function_exists('TotalDonations_Stripe_Listener')){
    function TotalDonations_Stripe_Listener()
    {
        $objO = new MIGLA_OPTION;

    	if( isset( $_GET['sl'] ) )
    	{
    	    if( sanitize_text_field($_GET['sl']) == $objO::st_get_option( 'migla_listen' ) )
    	    {
        	    include_once plugin_dir_path( __FILE__ ). 'gateways/stripe/migla_class_webhook.php';

        		$obj = new migla_stripe_webhook_handler;
        		$obj->migla_stripe_webhook_frontend();

        		http_response_code(200);
                exit();
    	    }else{
        		http_response_code(404);
                exit();
    	    }
    	}
    }
}

if (!function_exists('TotalDonations_PayPal_Listener')){
    function TotalDonations_PayPal_Listener()
    {
        $objO = new MIGLA_OPTION;
        
    	if( isset( $_GET['pl'] ) )
    	{
    	    if( sanitize_text_field($_GET['pl']) == $objO::st_get_option( 'migla_listen' ) )
    	    {
        	    include_once plugin_dir_path( __FILE__ ). 'gateways/paypal/migla_class_ipn.php';
     
        		$obj_ipn_handler = new migla_front_ipn_handler;
        		$obj_ipn_handler->migla_paypal_ipn_frontend();

        	    http_response_code(200); 
                exit();
    	    }else{
        		http_response_code(404); 
                exit();	    
    	    }
    	}
    }
}
?>
