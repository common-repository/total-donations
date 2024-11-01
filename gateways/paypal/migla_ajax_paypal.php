<?php
if ( !defined( 'ABSPATH' ) ) exit;

if (!function_exists('miglaA_checkout')){
    function miglaA_checkout()
    {
    	$message = -1;
    	
    	try{
        	$objO = new MIGLA_OPTION;
          	
          	$nonce = sanitize_text_field($_POST['nonce']);
        
          	if( !wp_verify_nonce( $nonce, 'migla-security-nonce' ) )
            {
                $message = __('Nonce is not recognize', 'migla-donation');
          	}else{
                // Repack the Default Field Post
                $map = migla_sanitize_donor_info((array)$_POST['donorinfo']);
                
                $new_id = migla_saving_donation( $map, 
                                        '', 
                                        'Paypal',
                                        '',
                                        '',
                                        '',
                                        'Paypal',
                                        'Pending-Paypal'
                                    );
                                      
                $message = $new_id;                      
          	}
        
            $msg  = "[".current_time('mysql') . "] Pending PayPal donation {" . $new_id . "}" . "\n"; 
        
            $objLog = new MIGLA_LOG();
            $objLog->append($msg);  
            
    	}catch(Exception $e) {
    	    $message = 'Message: ' . $e->getMessage();   
    	}
    	
      	echo $message;
      	die();
    }
}

$paypal_ajax_gateways_callers = array( "miglaA_checkout" );

foreach( $paypal_ajax_gateways_callers as $paypal_ajax_gateway_call ){
    add_action("wp_ajax_" . $paypal_ajax_gateway_call, $paypal_ajax_gateway_call );
    add_action("wp_ajax_nopriv_" . $paypal_ajax_gateway_call, $paypal_ajax_gateway_call );
} 
?>