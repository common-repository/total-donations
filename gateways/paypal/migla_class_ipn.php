<?php
class migla_front_ipn_handler
{
    public function logged_file( $msg )
    {
        $objLog = new MIGLA_LOG();
        $objLog->append($msg);        
    }

	static function migla_date_timezone()
	{
	    $php_time_zone = date_default_timezone_get();
	    $default    = $this->OPTION->get_option('migla_default_timezone');

	    $time = ""; 
	    $date = "";
	    $timezone   = "";    

	    if( $default == 'Server Time' )
	    {

	        $gmt_offset = -$this->OPTION->get_option( 'gmt_offset' );

	        if ($gmt_offset > 0)
	        {
	          $time_zone = 'Etc/GMT+' . $gmt_offset;
	        }else{
	          $time_zone = 'Etc/GMT' . $gmt_offset;
	        }

	        date_default_timezone_set( $time_zone );
	      
	        $time = date('H:i:s');
	        $date = date('m')."/".date('d')."/".date('Y');

	    }else{

	        date_default_timezone_set( $default );
	        
	        $time = date('H:i:s');
	        $date = date('m')."/".date('d')."/".date('Y');

	        $timezone   = $default;
	    }

	    date_default_timezone_set( $php_time_zone );
	    
	    $date_time_timezone = array(
	                            'timezone'  => $timezone,
	                            'date'      => $date,
	                            'time'      => $time
	                          ); 

	    return $date_time_timezone;
	}

	public function migla_paypal_ipn_frontend()
	{
	    $chat_back_url  =  'https://www.paypal.com/cgi-bin/webscr';
	    $host_header    =  "Host: www.paypal.com\r\n";
	    $session_id     = '';
	   
	   	$objO = new MIGLA_OPTION; 
	   	
	   	$isChatBack = $objO->get_option('migla_ipn_chatback') ;
		
		$pass = true;
		
		if( $isChatBack == 'yes' ){
		    $pass = $this->authenticate();
		}

        if($pass)
        {
    		$this->migla_handle_verified_ipn( $_POST ); 
        }else{

           if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
                $http = 'https';
            }else{
                $http = 'http';
            }
        
            $currentUrl = $http . '://' . $_SERVER['SERVER_NAME'] ;
   
            if ( wp_redirect( $currentUrl ) ) {
                exit;
            }
        }
        
	}

	public function authenticate()
	{
        // STEP 1: read POST data
        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
        // Instead, read raw POST data from the input stream.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        $is_valid = false;
        
        $objO = new MIGLA_OPTION;

        foreach ($raw_post_array as $keyval) {
          $keyval = explode ('=', $keyval);
          if (count($keyval) == 2)
            $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
          $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
          if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
            $value = urlencode(stripslashes($value));
          } else {
            $value = urlencode($value);
          }
          $req .= "&$key=$value";
        }
        
        // Step 2: POST IPN data back to PayPal to validate
        if ( "sandbox" == $objO->get_option( 'migla_paypal_payment' ) ) 
		{
		    $chatback_url = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr";
		}else{
		    $chatback_url = 'https://ipnpb.paypal.com/cgi-bin/webscr';
		}	  
        		
        $res = wp_remote_post( $chatback_url, array(
                        'sslverify' => false, // this is true by default!
                        'body' => $req 
                    ) );
                    
        if ( !($res) ) 
        {
          $this->logged_file( "[".current_time('mysql') . "] Got " . curl_error($ch) . " when processing IPN data\n");
          $is_valid = false;
          exit;
        }else{
            if (strcmp ($res, "VERIFIED") == 0) {
              // The IPN is verified, process it
              $this->logged_file( "[".current_time('mysql') . "] IPN is verified when processing IPN data\n");
              $is_valid = true; 
            } else if (strcmp ($res, "INVALID") == 0) {
              // IPN invalid, log for manual investigation
              $this->logged_file( "[".current_time('mysql') . "] IPN is Invalid when processing IPN data\n");
              $is_valid = false; 
            }
        }
        
        return $is_valid;                    
	}

	public function migla_handle_verified_ipn( $post )
	{
	    $is_saved_by_session = false;
	    $post_id = '';
	    $session_id = '';
	  	$msg = '';
        
        $objD = new CLASS_MIGLA_DONATION;
            
        $payment_status = sanitize_text_field($post['payment_status']);
        $post_custom = sanitize_text_field($post['custom']);
        $trans_id = sanitize_text_field($post['txn_id']);  

	    if ( "Completed" == $payment_status || "completed" == $payment_status )
	    {
            if( strpos( $post_custom, 'pos') >= 0 )
			{
				$post_id = substr( $post_custom, 5, (strlen( $post_custom ) - 5) );
			
			}else if( strpos( $post_custom, 'migla') >= 0 )
			{
			    $objO = new MIGLA_OPTION;
			    
			    $post_id = $objO->get_option( $post_custom );
			}else{
				$is_saved_by_session = true;
				$session_id = $post_custom;
				
				//Get Post ID from name and session
				$post_id = $objD->get_column_by_session( $session_id, 'id' );
				
				if( !empty($post_id) ){
				    $is_saved_by_session = false;
				}else{
				}
			}
				
            $is_existed = false;
 
            if( $is_saved_by_session )
            {
                $is_existed = $objD->if_donation_exist( 'session_id', $session_id, '%s' );
            }else{
                $is_existed = $objD->if_donation_exist( 'id', $post_id, '%d' );
            }

		  	    //ONE TME
        		$columnUpdates = array( "status" => 1,
        				                "gateway" => "Paypal" 
        				            );
                
                $columnTypes = array( "%d", '%s' );
				    
				if( $is_saved_by_session ){
                	$keyValues = array( "session_id" => $session_id );
                	$keyTypes = array( "%s" );
				}else{
                	$keyValues = array( "id" => $post_id );
                	$keyTypes = array( "%d" );				        
				}

                $objD->update_column( $columnUpdates, $keyValues, $columnTypes, $keyTypes );
                
                if( $is_saved_by_session ){
                    $post_id = $objD->get_column_by_session( $session_id, 'id' );
                }
                
                $objD->update_meta( $post_id, 'miglad_transactionId', $trans_id );
                
                $msg .= "[".current_time('mysql') . "] PayPal updating for { ID: " . $post_id . " / ". $trans_id ." }\n";
                
                $data = $objD->get_detail( $post_id, '' );
                
                $data['miglad_firstname'] = $data['firstname'];
                $data['miglad_lastname'] = $data['lastname'];
                $data['miglad_amount'] = $data['amount'];
                $data['miglad_campaign'] = $data['campaign'];
                $data['miglad_email'] = $data['email'];
                $data['miglad_country'] = $data['country'];
                $data['miglad_anonymous'] = $data['anonymous'];
                $data['miglad_repeating'] = $data['repeating'];
                $data['miglad_date'] = $data['date_created'];
 
                $msg .= "Firstname : ".$data['firstname']. " | ";
                $msg .= "Lastname: ". $data['lastname'] . " | ";
                $msg .= "Email: ". $data['email'] . "\n";
                
                $objLog = new MIGLA_LOG();
                $objLog->append($msg);  
    
                $objE = new MIGLA_EMAIL;

                $objE->email_procedure( $data['miglad_form_id'], 
                                        $post_id, 
                                        $data, 
                                        $data['miglad_language'] );   
                                                
	    } // If $payment_status
	}
	
}//END OF CLASS

?>