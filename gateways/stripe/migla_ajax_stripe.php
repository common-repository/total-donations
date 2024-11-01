<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( '\Stripe\Stripe' ) ){
    include_once Totaldonations_DIR_PATH . 'gateways/stripe/stripe-php-6.43.0/init.php';    
}    

/* Stripe is using these*/
if (!function_exists('migla_stripe_charge')){
    function migla_stripe_charge( $donor_info,
                                  $amount,
                                  $payment_intent,
    							  $form_id
    )
    {
        $message_array = array();
        $success    = '-1';
        $err        = '';
        $is_pass 	  = true;
        $avs_response = '';
        $date_time  = array();
        $post_array = array();
        $map        = (array)$donor_info;
        $array      = array();
        $desc       = '';

        $objO = new MIGLA_OPTION;

        \Stripe\Stripe::setApiKey( migla_getSK() );

        $charge = \Stripe\Charge::all([
            'payment_intent' => $payment_intent,
            'limit' => 3,
        ]);

        $charge_array = $charge->__toArray(true);

        $isPassed = true;
        $avs_level = $objO->get_option( 'migla_avs_level' );

        if( $avs_level == 'low' )
        {
            if( $charge->data[0]->payment_method_details->card->checks->cvc_check == 'pass' )
            {
                $isPassed = true;
                $avs_response = 'cvc:pass';
            }else{
                $isPassed = false;
                $err = 'Transaction is failed due incorrect CVC.';
            }
        }else if( $avs_level == 'medium' )
        {
            if( $charge->data[0]->payment_method_details->card->checks->cvc_check == 'pass'
            && $charge->data[0]->payment_method_details->card->checks->address_postal_code_check == 'pass' )
            {
                $isPassed = true;
                $avs_response = 'cvc:pass;zip:pass';
            }else{
                $isPassed = false;
                $err = 'Transaction is failed due incorrect CVC or Postal Code.';
            }
        }else if( $avs_level == 'high' )
        {
            if( $charge->data[0]->payment_method_details->card->checks->cvc_check == 'pass'
            && $charge->data[0]->payment_method_details->card->checks->address_postal_code_check == 'pass'
            && $charge->data[0]->payment_method_details->card->checks->address_line1_check == 'pass'
            )
            {
                $isPassed = true;
                $avs_response = 'cvc:pass;zip:pass;street:pass';
            }else{
                $isPassed = false;
                $err = 'Transaction is failed due incorrect CVC, Street Address or Postal Code.';
            }
        }

        if( $charge->data[0]->status == 'succeeded' && $isPassed )
        {
            //flip to success
            $success = "1";

            //Save donation to database
            $new_id = migla_saving_donation(
                                        $map,
                                        $charge->data[0]->id,
                                        'Credit Card',
                                        $charge_array,
                                        $avs_response,
                                        $post_array,
                                        'One time (Stripe)',
                                        'Stripe'
                                      );

            $data = migla_check_empty_donation_data( $map );

            $desc = $data['miglad_firstname'] . ' ' . $data['miglad_lastname'] . ' - ' . $data['miglad_campaign_name']  ;

            \Stripe\PaymentIntent::update(
                  $payment_intent,
                  [
                    'description' => $desc,
                    'metadata' => [ 'record_id' => $new_id ,
                                    'campaign' => $data['miglad_campaign_name']
                                ],
                  ]
                );

            $objM = new MIGLA_MONEY();

            $msg  = "[".current_time('mysql') . "] A donation has been made {" . $new_id . "/". $charge->data[0]->id. "}" . "\n";

            if( !empty($data['miglad_firstname']) )
                $msg .= $data['miglad_firstname']." | ";

            if( !empty($data['miglad_lastname']) )
                $msg .= $data['miglad_lastname']." | ";

            if( !empty($amount) )
                $msg .= $objM->get_default_currency() . " ". ($amount/100)." | ";

            if( !empty($data['miglad_email']) )
                $msg .= $data['miglad_email']."\n";

            $msg .= "\n\n";

            $objLog = new MIGLA_LOG();
            $objLog->append($msg);

        }else{
            $success = '-1';

            //Save donation to database
            $new_id = migla_saving_donation(
                                        $map,
                                        $charge->data[0]->id,
                                        'Credit Card',
                                        $charge_array,
                                        $avs_response,
                                        $post_array,
                                        'One time (Stripe)',
                                        'Pending-Stripe'
                                      );

            $msg  = "[".current_time('mysql') . "] A donation attempt by:\n";

            if( !empty($data['miglad_firstname']) )
                $msg .= $data['miglad_firstname']." | ";

            if( !empty($data['miglad_lastname']) )
                $msg .= $data['miglad_lastname']." | ";

            if( !empty($amount) )
                $msg .= ($amount/100) ." | ";

            if( !empty($data['miglad_email']) )
                $msg .= $data['miglad_email']."\n";

            $msg = "Donation save as pending with ID {".$new_id."}. AVS-".$avs_response;

            $msg .= "\n";

            $objLog = new MIGLA_LOG();
            $objLog->append($msg);
        }

        $message_array[0] = $success;
        $message_array[1] = $err ;
      	$message_array[2] = $new_id;
      	$message_array[3] = $map;

        return $message_array;
    }
}

if (!function_exists('miglaA_stripeCharge_stripejs')){
    function miglaA_stripeCharge_stripejs()
    {

        $success = "-1";
        $message = "";
        $message_code = '';
        $map_array = array();
        $result  = array();

        $err_api_connection  = "";
        $err_invalid_request = "";
        $err_api       = "";
        $err_card            = "";
        $err_authentication  = "";
        $err_error       = "";
        $err_exception       = "";
        $return = array();

        $token = array();

        $card_info = array();
        $checker_message      = array('', '', '');

        $objO = new MIGLA_OPTION;

        $is_pass = true;

        if( $objO->get_option('migla_credit_card_avs') == 'no'  )
        {
            $is_pass = true;
        }

        if( $is_pass )
        {
            try{
                //Check the nonce
                $nonce = sanitize_text_field($_POST['nonce']);
                
                if( !wp_verify_nonce( $nonce, 'migla-security-nonce' ) )
                {
                    $message = __('Nonce is not recognize', 'migla-donation');
                }else{
                    
                    $map = migla_sanitize_donor_info((array)$_POST['donorinfo']);
                    $amount = sanitize_text_field($_POST['amount']);
                    $paymentIntent = sanitize_text_field($_POST['payment_intent']);
                    $formId = sanitize_text_field($_POST['form_id']);
                    
          			$result = migla_stripe_charge(  $map,
                                                    $amount,
                                                    $paymentIntent,
          										    $formId
                                                );

          			$success    = $result[0];
          			$message    = $result[1];
          			$pid	    = $result[2];
          			$map_array  = $result[3];

        			if( $success == '1' )
    				{
    				    $message_code = '1';

                        $data = migla_check_empty_donation_data( $map );

                        $objE = new MIGLA_EMAIL;

                        $objE->email_procedure( $formId,
                                                $pid,
                                                $data,
                                                $data['miglad_language'] );
    				}

                }//IF NONCE

             } catch (\Stripe\Error\ApiConnection $e)
            {
                // Network problem, perhaps try again.
                $message_code .= '1';

                $e_json   = $e->getJsonBody();
                $err_stripe = $e_json['error'];
                $err_api_connection  = $err_stripe['message'];

            } catch (\Stripe\Error\InvalidRequest $e)
            {
                // You screwed up in your programming. Shouldn't happen!
                $message_code .= '2';

                $e_json   = $e->getJsonBody();
                $err_stripe = $e_json['error'];
                $err_invalid_request = $err_stripe['message'];

            } catch (\Stripe\Error\Api $e)
            {
                // Stripe's servers are down!
                $message_code .= '3';

                $e_json   = $e->getJsonBody();
                $err_stripe = $e_json['error'];
                $err_api = $err_stripe['message'];

            } catch (\Stripe\Error\Card $e)
            {
                // Card was declined.
                $message_code .= '4';

                $e_json   = $e->getJsonBody();
                $err_stripe = $e_json['error'];
                $err_card  = $err_stripe['message'];

            }

            $message .=   $err_api_connection  ;
            $message .=   $err_invalid_request ;
            $message .=   $err_api       ;
            $message .=   $err_card            ;
            $message .=   $err_authentication  ;
            $message .=   $err_error       ;
            $message .=   $err_exception       ;

          	$return[0] = $message_code ;
          	$return[1] = $message;
          	$return[2] = $pid;
        }

        echo json_encode($return);
    	die();
    }
}

if (!function_exists('miglaA_stripe_create_payment_intent')){
    function miglaA_stripe_create_payment_intent()
    {
        $objO = new MIGLA_OPTION;

        //Check the nonce
        $nonce = sanitize_text_field($_POST['nonce']);
        
        if( !wp_verify_nonce( $nonce, 'migla-security-nonce' ) )
        {
            $message = __('Nonce is not recognize', 'migla-donation');
        }else{
            \Stripe\Stripe::setApiKey( migla_getSK() );
            
            $amount = sanitize_text_field($_POST['amount']);

            $intent = \Stripe\PaymentIntent::create([
                    'amount' => $amount * 100,
                    'currency' => $objO->get_option('migla_default_currency')
                ]);

            $message = $intent->client_secret;
        }

        echo $message;
        die();
    }
}

if (!function_exists('migla_getUserIP')){
    function migla_getUserIP() 
    {    
      if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
      return $ip;
    }
}

if (!function_exists('miglaA_client_logged')){
    function miglaA_client_logged()
    {
        global $wpdb;
        
        try{
            
            $t = time();
            $timestamp = date("Y-m-d h:i:s",$t);
            $ipaddress = migla_getUserIP();
        
    	    $wpdb->insert( "{$wpdb->prefix}migla_client_log",
    		            array(  "timestamp" => time(),
    		            		"ipaddress" => $ipaddress,
    		            		"status"    => sanitize_text_field($_POST['client_status']),
    		            		"message"   => sanitize_text_field($_POST['client_message'])
    		            ),
    		            array( '%s', '%s', '%s', '%s' )
    	   
    	            );    
        }catch(Exception $e) {
            $msg = 'Message: ' .$e->getMessage();
            $objLog = new MIGLA_LOG();
            $objLog->append($msg);
        }   	            
    	
    	echo   $ipaddress;          
        die();
    }
}

if (!function_exists('miglaA_count_error_logged')){
    function miglaA_count_error_logged()
    {
    	$timestamp = time(); //strtotime(date("Y-m-d h:i:s"));
    	$count_error = 0;    
    	
    	global $wpdb;
    	
    	$sql = "SELECT * FROM {$wpdb->prefix}migla_client_log where ipaddress = %s order by timestamp DESC LIMIT 0,10";
        
        $ipaddress = migla_getUserIP();
                
    	try{
            $result = $wpdb->get_results( $wpdb->prepare( $sql, $ipaddress ), ARRAY_A );

    		if(!empty($result))
    		{
    		    foreach($result as $row)
    		    {
    		        $ts = $row['timestamp'] ;
    		        //in 360 mins
    		        if( intval($timestamp - $ts) <= 21600 && ($row['status'] == 'error') )
    		        {
    		            $count_error++;
    		        }else{
    		        }
    		                
    		    }//foreach
    		}
    		
    		if( $count_error >= 10 )
    		{
                $msg = "Hey IP " . $ipaddress . " has been lockout";
                $objLog = new MIGLA_LOG();
                $objLog->append($msg);
    		} 
    		
        }catch(Exception $e) {
            $msg = 'Message: ' .$e->getMessage();
            $objLog = new MIGLA_LOG();
            $objLog->append($msg);
        }    
    	
    	echo $count_error;
    	die();	       
    }
}

$stripe_ajax_gateways_callers = array(
      "miglaA_stripeCharge_stripejs",
      "miglaA_stripe_create_payment_intent",
      "miglaA_client_logged",
      "miglaA_count_error_logged"
);

foreach( $stripe_ajax_gateways_callers as $stripe_ajax_gateway_call )
{
    add_action("wp_ajax_".$stripe_ajax_gateway_call, $stripe_ajax_gateway_call );
    add_action("wp_ajax_nopriv_".$stripe_ajax_gateway_call, $stripe_ajax_gateway_call );
}
?>
