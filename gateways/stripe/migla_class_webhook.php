<?php

class migla_stripe_webhook_handler
{
    public function __construct()
    {
        $this->log_file    = Totaldonations_DIR_PATH . "logs/td_stripe.log" ;
        $this->is_debug_on = true;
    }
    
    public function migla_stripe_webhook_frontend()
    {
        include_once Totaldonations_DIR_PATH . 'classes/CLASS_OPTIONS.php';

        $endpoint_secret = MIGLA_OPTION::st_get_option('migla_webhook_key');

        $payload = @file_get_contents('php://input');
        $event_json = json_decode($payload);

        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        if( MIGLA_OPTION::st_get_option('migla_stripemode') == 'test' )
        {
            $SK = MIGLA_OPTION::st_get_option('migla_testSK');
        }else{
            $SK = MIGLA_OPTION::st_get_option('migla_liveSK');
        }

        try {

            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            
            $this->handle_event($event_json, $SK);

        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        http_response_code(200);
    }

    public function handle_event($event_json, $SK)
    {
        if ( $event_json->type == 'charge.succeeded' )
        {
            
        }else if ( $event_json->type == 'customer.subscription.created' )
        {
            
        }else if( $event_json->type == 'invoice.payment_succeeded' ) 
        {
  
        }else if( $event_json->type == 'charge.refunded' ) 
        {
            if ( !class_exists( '\Stripe\Stripe' ) ){
               include_once Totaldonations_DIR_PATH . 'gateways/stripe/stripe-php-6.43.0/init.php';
            }

            \Stripe\Stripe::setApiKey( $SK );
                
            $charge_id = $event_json->data->object->id;
            
            $pid = CLASS_MIGLA_DONATION::st_get_any_donationmeta(   'donation_id',
                                                                      'miglad_transactionId',
                                                                      $charge_id, 
                                                                      '%s',
                                                                      '%s',
                                                                      'id',
                                                                      'ASC',
                                                                      ''
                                        );   
                    
            CLASS_MIGLA_DONATION::st_update_column( array( "status" => 3, "gateway" => "Stripe-Refunded" ), 
                                  array( "id" => $pid ), 
                                  array( "%d", "%s" ), 
                                  array( "%d" ) );

            $message = "Charge [". $charge_id . "] is switch to pending because refunded";
            
            error_log( date('[Y-m-d H:i e] ') . $message ."\n" , 3 , Totaldonations_DIR_PATH . "logs/td_stripe.log" );
            
        }else if ( $event_json->type == 'charge.dispute.created' )
        {
            //Ok get this charge id
            $charge_id = $event_json->data->object->charge;

            $message = "This transaction with id ".$charge_id." is suspected as dispute transaction. Check your stripe account for further action.";

            error_log( date('[Y-m-d H:i e] ') . $message ."\n" , 3 , Totaldonations_DIR_PATH . "logs/td_stripe.log" );

        }else if ( $event_json->type == 'charge.dispute.closed' )
        {
            $charge_id = $event_json->data->object->charge;
            $message = "This transaction with id ".$charge_id." is closed from dispute accusition.";
            error_log( date('[Y-m-d H:i e] ') . $message ."\n" , 3 , Totaldonations_DIR_PATH . "logs/td_stripe.log" );        

        }else if( $event_json->type == 'payment_intent.succeeded' ) //Subscription Made
        {
            
        }else{ // ELSE This will send receipts on succesful invoices

        }

        echo $message;

    } //handle event
}

?>