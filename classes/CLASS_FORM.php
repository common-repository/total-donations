<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CLASS_MIGLA_FORM' ) )
{
	class CLASS_MIGLA_FORM
	{
		var $id;
		var $amounts;
		var $structure;
		var $hideCustomAmount;
		var $amountBoxType;
		var $buttonType;

		var $warning1;
		var $warning2;
		var $warning3;
		var $customAmountText;
		var $formUrl;


		public static function init_form( $is_multi , 
										  $language )
		{
			global $wpdb;
			
			$CForm_fields = new CForm_Fields;
			$structure 	  = $CForm_fields::form_fields();
			$fields       = $CForm_fields::form_fields_for_translate1($structure);

			$sql = "SELECT id FROM {$wpdb->prefix}migla_form";
			$sql .= " WHERE form_id = %d";
			
			$id = 0;

			$id = 	$wpdb->get_var( $wpdb->prepare( $sql, 0 ) );

			if( $id > 0 )
			{
			}else{

				if( empty($language) ) $language = get_locale();

				$amounts = array( array( 'amount' => 10, 'perk' => 'giving level 1' ) );

				$hideCustomAmount = 'no';
				$amountBoxType = 'box';
				$buttonType = 'button';

			  	$wpdb->insert( "{$wpdb->prefix}migla_form",
			            array(
			            		"form_id"	=> 0,
			            		"structure" => serialize( $structure ),
			                  	"amounts"   => serialize( $amounts ),
			                  	"hideCustomAmount"  => $hideCustomAmount,
			                  	"amountBoxType"  => $amountBoxType,
			                  	"buttonType" => $buttonType,
			                  	"language_origin" => get_locale()
			                ),
			            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' )
			  	);

			}

		    $meta_array_2 = array(
		          'stripe_tab_info'  => serialize( self::st_get_stripe_tab() ),
		          'paypal_tab_info'  => serialize( self::st_get_paypal_tab() ),
		          'authorize_tab_info'  => serialize( self::st_get_authorize_tab() ),
		          'offline_tab_info'  => serialize( self::st_get_offline_tab() )
		        );
			
            $meta_array_1 = array( 'warning_1' => 'Please insert all the required fields',
                                 'warning_2' => 'Please insert correct email',
                                 'warning_3' => 'Please fill in a valid amount',
                                 'form_url' => '0',
                                 'custom_amount_text' => 'Custom',
                                 'fields' => serialize( $fields )
                                );
                                
            $meta_array = array_merge($meta_array_1, $meta_array_2);
                            
            //check flag                
		    $meta_set_1 = array(
		          'stripe_tab_info'  => false,
		          'paypal_tab_info'  => false,
		          'authorize_tab_info'  => false,
		          'offline_tab_info'  => false
		        );
		        
            $meta_set_2 = array( 'warning_1' => false,
                                 'warning_2' => false,
                                 'warning_3' => false,
                                 'form_url' => false,
                                 'custom_amount_text' => false,
                                 'fields' => false
                                );
                                
            $meta_set = array_merge($meta_set_1, $meta_set_2);    

            //check all metas
            $sql = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
			$sql .= " WHERE form_id = %d";
			
			$data = array();
			$data = $wpdb->get_results( $wpdb->prepare( $sql, 0), ARRAY_A );
			
			if( !empty($data) ){
			    foreach($data as $datum){
			        $key = $datum['meta_key'];
			        if( isset($meta_set[$key]) ){
			            $meta_set[$key] = true;
			        }
			    }
			}//if
			
			foreach( $meta_set as $key => $isset ){
			    if(!$isset)
			    {
    			  	$wpdb->insert( "{$wpdb->prefix}migla_form_meta",
    			                array(
    			                  "form_id"   => 0,
    			                  "language"  => $language,
    			                  "meta_key"  => $key,
    			                  "meta_value" => $meta_array[$key]
    			                ),
    			            array( '%d', '%s', '%s', '%s' )
    			  	);			        
			    }
			}//foreach

		}
		
		public function get_stripe_tab()
		{
		    $stripes = array();
		    $stripes['tab'] = 'Stripe';
		    $stripes['cardholder']['label'] = 'Name';
		    $stripes['cardholder']['placeholder'] = 'Your Name';		    
		    $stripes['cardnumber']['label'] = 'Card Number';
		    $stripes['cardnumber']['placeholder'] = 'Your Card Number';
		    $stripes['button'] = 'Pay';
		    $stripes['loading_message'] = 'Please wait while we processing your donation';
		    
		    return $stripes;
		}
		
		public function get_authorize_tab()
		{
		    $authorizes = array();
		    $authorizes['tab'] = 'Authorize.Net';     //0
		    
		    $authorizes['cardholder']['label'] = 'Name on card'; //2
		    $authorizes['cardholder']['placeholder'] = 'First Name';             //1
		    $authorizes['cardholder']['last_placeholder'] = 'Last Name';            //3
		    
		    $authorizes['cardnumber']['label'] = 'Card Number';            //5
		    $authorizes['cardnumber']['placeholder'] = 'Your Card Number';        //6
		    $authorizes['cardcvc']['label'] = 'Expiration//CVC';           //7
		    $authorizes['cardcvc']['placeholder'] = 'CVC';		    
		    $authorizes['button'] = 'Pay';		    
		    $authorizes['loading_message'] = 'Please wait while we processing your donation';	
		    
		    return $authorizes;
		}
		
		public function get_offline_tab()
		{
		    $offlines = array();
		    $offlines['tab'] = 'Offline';
		    $offlines['button'] = 'Pay';
		    $offlines['loading_message'] = 'Please wait while we processing your donation';
		    $offlines['after_donation_message'] = 'Please contact our office for more info';
		    
		    return $offlines;
		}
		
		public function get_paypal_tab()
		{
		    $paypals = array();
		    $paypals['tab'] = 'Paypal';     //0
		    $paypals['methodchoice'][0] = 'Pay with Credit Card';     //1
		    $paypals['methodchoice'][1] = 'Pay with paypal account';  //2
		    
		    $paypals['cardholder']['label'] = 'Name on card'; //4
		    $paypals['cardholder']['placeholder'] = 'First Name';            //5
		    $paypals['cardholder']['last_placeholder'] = 'Last Name'; 
		    
		    $paypals['cardnumber']['label'] = 'Card Number';            //7
		    $paypals['cardnumber']['placeholder'] = 'Your Card Number';        //8
		    $paypals['cardcvc']['label'] = 'Expiration//CVC';           //9
		    $paypals['cardnumber']['placeholder'] = 'CVC';		    
		    $paypals['button'] = 'Pay';		    
		    $paypals['loading_message'] = 'Please wait while we processing your donation';
		    
		    return $paypals;
		}
	
		public static function st_get_stripe_tab()
		{
		    $stripes = array();
		    $stripes['tab'] = 'Stripe';
		    $stripes['cardholder']['label'] = 'Name';
		    $stripes['cardholder']['placeholder'] = 'Your Name';		    
		    $stripes['cardnumber']['label'] = 'Card Number';
		    $stripes['cardnumber']['placeholder'] = 'Your Card Number';
		    $stripes['button'] = '&#10084; Donate Now';
		    $stripes['loading_message'] = 'Please wait while we processing your donation';
		    
		    return $stripes;
		}
		
		public static function st_get_authorize_tab()
		{
		    $authorizes = array();
		    $authorizes['tab'] = 'Authorize.Net';     //0
		    
		    $authorizes['cardholder']['label'] = 'Name on card'; //2
		    $authorizes['cardholder']['placeholder'] = 'First Name';             //1
		    $authorizes['cardholder']['last_placeholder'] = 'Last Name';            //3
		    $authorizes['cardholder']['last'] = 'Last Name'; //4
		    
		    $authorizes['cardnumber']['label'] = 'Card Number';            //5
		    $authorizes['cardnumber']['placeholder'] = 'Your Card Number';        //6
		    $authorizes['cardcvc']['label'] = 'Expiration//CVC';           //7
		    $authorizes['cardcvc']['placeholder'] = 'CVC';		    
		    $authorizes['button'] = 'Pay';		    
		    $authorizes['loading_message'] = 'Please wait while we processing your donation';	
		    
		    return $authorizes;
		}
		
		public static function st_get_offline_tab()
		{
		    $offlines = array();
		    $offlines['tab'] = 'Offline';
		    $offlines['button'] = 'Pay';
		    $offlines['loading_message'] = 'Please wait while we processing your donation';
		    $offlines['after_donation_message'] = 'Please contact our office for more info';
		    
		    return $offlines;
		}
		
		public static function st_get_paypal_tab()
		{
		    $paypals = array();
		    $paypals['tab'] = 'Paypal';     //0
		    $paypals['methodchoice'][0] = 'Pay with Credit Card';     //1
		    $paypals['methodchoice'][1] = 'Pay with paypal account';  //2
		    
		    $paypals['cardholder']['label'] = 'Name on card'; //4
		    $paypals['cardholder']['placeholder'] = 'First Name';            //5
		    $paypals['cardholder']['last_placeholder'] = 'Last Name'; 
		    
		    $paypals['cardnumber']['label'] = 'Card Number';            //7
		    $paypals['cardnumber']['placeholder'] = 'Your Card Number';        //8
		    $paypals['cardcvc']['label'] = 'Expiration//CVC';           //9
		    $paypals['cardnumber']['placeholder'] = 'CVC';		    
		    $paypals['button'] = '&#10084; Donate Now';		    
		    $paypals['loading_message'] = 'Please wait while we processing your donation';
		    
		    return $paypals;
		}	
		
		public function init_meta_gateways()
		{
		    $meta_array = array(
		          'stripe_tab_info'  => serialize( $this->get_stripe_tab() ),
		          'paypal_tab_info'  => serialize( $this->get_paypal_tab() ),
		          'authorize_tab_info'  => serialize( $this->get_authorize_tab() ),
		          'offline_tab_info'  => serialize( $this->get_offline_tab() )
		        );
		        
		    return $meta_array;
		}

		public function init_metaset_gateways()
		{
		    $meta_array = array(
		          'stripe_tab_info'  => false,
		          'paypal_tab_info'  => false,
		          'authorize_tab_info'  => false,
		          'offline_tab_info'  => false
		        );
		        
		    return $meta_array;		    
		}

		public function create_form( $structure, 
									$is_multi , 
									$language )
		{
			global $wpdb;

			if( empty($language) ) $language = get_locale();

			$amounts = array( array( 'amount' => 10, 'perk' => 'giving level 1' ) ) ;

			$CForm_fields = new CForm_Fields;
			$structure = $CForm_fields::form_fields();
			$fields = $CForm_fields::form_fields_for_translate1($structure);

			$hideCustomAmount = 'no';
			$amountBoxType = 'box';
			$buttonType = 'button';

		  	$wpdb->insert( "{$wpdb->prefix}migla_form",
		            array(
		            		"form_id"	=> 0,
							"structure" => serialize( $structure ),
			                "amounts"   => serialize( $amounts ),
		                  	"hideCustomAmount"  => $hideCustomAmount,
		                  	"amountBoxType"  => $amountBoxType,
			                "buttonType" => $buttonType,
			                "language_origin" => get_locale()
			            ),
			        array( '%d', '%s', '%s', '%s', '%s','%s', '%s' )
		  	);

		  	$form_id = $wpdb->insert_id;

			$wpdb->update( "{$wpdb->prefix}migla_form",
			            array( "form_id" => $form_id ),
			            array( "id" => $form_id ),
			            array( '%d'),
			            array( '%d')
			  	);

		    $meta_array_2 = $this->init_meta_gateways();
			
            $meta_array_1 = array( 'warning_1' => 'Please insert all the required fields',
                                 'warning_2' => 'Please insert correct email',
                                 'warning_3' => 'Please fill in a valid amount',
                                 'form_url' => '0',
                                 'custom_amount_text' => 'Custom',
                                 'fields' => serialize( $fields )
                                );
                                
            $meta_array = array_merge($meta_array_1, $meta_array_2);
            
            //check flag                
		    $meta_set_1 = $this->init_metaset_gateways();
		        
            $meta_set_2 = array( 'warning_1' => false,
                                 'warning_2' => false,
                                 'warning_3' => false,
                                 'form_url' => false,
                                 'custom_amount_text' => false,
                                 'fields' => false
                                );
                                
            $meta_set = array_merge($meta_set_1, $meta_set_2);  

            //check all metas
            $sql = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
			$sql .= " WHERE form_id = %d";
			
			$data = array();
			$data = $wpdb->get_results( $wpdb->prepare( $sql, $form_id), ARRAY_A );
			
			if( !empty($data) ){
			    foreach($data as $datum){
			        $key = $datum['meta_key'];
			        if( isset($meta_set[$key]) ){
			            $meta_set[$key] = true;
			        }
			    }
			}//if
			
			foreach( $meta_set as $key => $isset ){
			    if(!$isset)
			    {
    			  	$wpdb->insert( "{$wpdb->prefix}migla_form_meta",
    			                array(
    			                  "form_id"   => $form_id,
    			                  "language"  => $language,
    			                  "meta_key"  => $key,
    			                  "meta_value" => $meta_array[$key]
    			                ),
    			            array( '%d', '%s', '%s', '%s' )
    			  	);			        
			    }
			}//foreach

			return $form_id;
		}
		
		public function create_campaign_form( $structure, 
									$is_multi , 
									$language )
		{
			global $wpdb;

			if( empty($language) ) $language = get_locale();

			$amounts = array( array( 'amount' => 10, 'perk' => 'giving level 1' ) ) ;

			$CForm_fields = new CForm_Fields;
			$structure = $CForm_fields::form_fields();
			$fields = $CForm_fields::form_fields_for_translate1($structure);

			$hideCustomAmount = 'no';
			$amountBoxType = 'box';
			$buttonType = 'button';

		  	$wpdb->insert( "{$wpdb->prefix}migla_form",
		            array(
		            		"form_id"	=> 0,
							"structure" => serialize( $structure ),
			                "amounts"   => serialize( $amounts ),
		                  	"hideCustomAmount"  => $hideCustomAmount,
		                  	"amountBoxType"  => $amountBoxType,
			                "buttonType" => $buttonType,
			                "language_origin" => get_locale()
			            ),
			        array( '%d', '%s', '%s', '%s', '%s','%s', '%s' )
		  	);

		  	$form_id = $wpdb->insert_id;

			$wpdb->update( "{$wpdb->prefix}migla_form",
			            array( "form_id" => $form_id ),
			            array( "id" => $form_id ),
			            array( '%d'),
			            array( '%d')
			  	);

		    //$meta_array_2 = $this->init_meta_gateways();
			
            $meta_array_1 = array( 'warning_1' => 'Please insert all the required fields',
                                 'warning_2' => 'Please insert correct email',
                                 'warning_3' => 'Please fill in a valid amount',
                                 'form_url' => '0',
                                 'custom_amount_text' => 'Custom',
                                 'fields' => serialize( $fields )
                                );
                                
            $meta_array = $meta_array_1;
            
            //check flag                
		   // $meta_set_1 = $this->init_metaset_gateways();;
		        
            $meta_set_2 = array( 'warning_1' => false,
                                 'warning_2' => false,
                                 'warning_3' => false,
                                 'form_url' => false,
                                 'custom_amount_text' => false,
                                 'fields' => false
                                );
                                
            $meta_set = $meta_set_2;  

            //check all metas
            $sql = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
			$sql .= " WHERE form_id = %d";
			
			$data = array();
			$data = $wpdb->get_results( $wpdb->prepare( $sql, $form_id), ARRAY_A );
			
			if( !empty($data) ){
			    foreach($data as $datum){
			        $key = $datum['meta_key'];
			        if( isset($meta_set[$key]) ){
			            $meta_set[$key] = true;
			        }
			    }
			}//if
			
			foreach( $meta_set as $key => $isset ){
			    if(!$isset)
			    {
    			  	$wpdb->insert( "{$wpdb->prefix}migla_form_meta",
    			                array(
    			                  "form_id"   => $form_id,
    			                  "language"  => $language,
    			                  "meta_key"  => $key,
    			                  "meta_value" => $meta_array[$key]
    			                ),
    			            array( '%d', '%s', '%s', '%s' )
    			  	);			        
			    }
			}//foreach

			return $form_id;
		}		

		public function update_form( $form_id, $key, $val, $coltype, $type )
		{
			global $wpdb;
			
			$saved = $val;

			if( $type == "array" ){
				$saved = serialize($val);
			}else if( $type == "json" ){	
				$saved = json_encode($val);
			}

			$wpdb->update( "{$wpdb->prefix}migla_form",
			                array(
			                  $key => $saved
			                ),
			                array(
			              	  "form_id"   => $form_id	
			                ),
				            array( $coltype ),
				            array( '%d' )
		  			);

		}

		public function update_form_meta( $form_id, $language, $key, $val, $type )
		{
			global $wpdb;
			
			$saved = $val;
			$res = "";

			if( $type == "array" ){
				$saved = serialize($val);
			}else if( $type == "json" ){	
				$saved = json_encode($val);
			}

			if( $this->if_meta_exist( $form_id, $key, $language ) )
			{
				$res = $wpdb->update( "{$wpdb->prefix}migla_form_meta",
				                array(
				                  "meta_key"  => $key,
				                  "meta_value" => $saved
				                ),
				                array(
				              		"form_id"   => $form_id,
			                  		"language"  => $language,
			                  		"meta_key"  => $key	
				                ),
					            array( '%s', '%s' ),
					            array( '%d', '%s', '%s' )
			  	);
			  	
			  	$res .= "updated";

			}else{

		  		$wpdb->insert( "{$wpdb->prefix}migla_form_meta",
		                array(
		                  "form_id"   	=> $form_id,
		                  "language"  	=> $language,
		                  "meta_key"  	=> $key,
		                  "meta_value" 	=> $saved
		                ),
		            array( '%d', '%s', '%s', '%s' )
		  		);	
		  		
                $res = "new ".$wpdb->insert_id;
			}
			
			return $res;
		}

		public function restore_form_meta( $form_id, $key, $val, $type )
		{
			global $wpdb;
			
			$saved = $val;
			$res = "";

			if( $type == "array" ){
				$saved = serialize($val);
			}else if( $type == "json" ){	
				$saved = json_encode($val);
			}

				$res = $wpdb->update( "{$wpdb->prefix}migla_form_meta",
				                array(
				                  "meta_key"  => $key,
				                  "meta_value" => $saved
				                ),
				                array(
				              		"form_id"   => $form_id,
			                  		"meta_key"  => $key	
				                ),
					            array( '%s', '%s' ),
					            array( '%d', '%s')
			  	);
			  	
			  	$res .= "updated";

			return $res;
		}

		public function get_specific_metainfo( $form_id, $language, $meta )
		{
			global $wpdb;

			$ResultSetMeta = array();
			$ResultSetMetaData = array();

            if( $language == "all" ){

    			$sqlMeta = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
    			$sqlMeta .= " WHERE form_id = %d";
    			$sqlMeta .= " AND meta_key = %s";
    
    			$ResultSetMetaData = $wpdb->get_results( 
    									$wpdb->prepare( $sqlMeta, 
    									        $form_id, 
    									        $meta
    									        )
    									, ARRAY_A
    								);
    
    

                
            }else{    

    			if( empty($language) ) $language = get_locale();
    
    			$sqlMeta = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
    			$sqlMeta .= " WHERE form_id = %d AND language = %s";
    			$sqlMeta .= " AND meta_key = %s";
    
    			$ResultSetMetaData = $wpdb->get_results( 
    									$wpdb->prepare( $sqlMeta, 
    									        $form_id, 
    									        $language,
    									        $meta
    									        )
    									, ARRAY_A
    								);
            }
            
    		if( !empty($ResultSetMetaData) ){
    			foreach( $ResultSetMetaData as $row ){
    			    
    				if( $row['meta_key'] == "fields" ){
    					if( !empty($row['meta_value']) ){
    					   $ResultSetMeta[($row['language'])][($row['meta_key'])] = (array)unserialize($row['meta_value']);
    				    }
    				}else{				
    					$ResultSetMeta[($row['language'])][($row['meta_key'])] = $row['meta_value'];
    				}    			    
    			}
    
    		}//if not empty	            
            
			return $ResultSetMeta;
		}
		
		public function get_specific_meta_customval( $form_id, $language )
		{
			global $wpdb;

			$ResultSetMeta = array();
    		
    		if( empty($language) ) $language = get_locale();
    
    		$sqlMeta = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
    		$sqlMeta .= " WHERE form_id = %d AND language = %s";
    		$sqlMeta .= " AND meta_key like %s";
    
    			$ResultSetMetaData = $wpdb->get_results( 
    									$wpdb->prepare( $sqlMeta, 
    									        $form_id, 
    									        $language,
    									        '#f%'
    									        )
    									, ARRAY_A
    								);
    
    			if( !empty($ResultSetMetaData) ){
    				foreach( $ResultSetMetaData as $row ){
    				    $ResultSetMeta[($row['meta_key'])] = $row['meta_value'];
    				}
    
    			}//if not empty	
    			else{
    				$language_origin = $this->get_column( $form_id, 'language_origin' );

		    		$sqlMeta = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
		    		$sqlMeta .= " WHERE form_id = %d AND language = %s";
		    		$sqlMeta .= " AND meta_key like %s";
		    
		    			$ResultSetMetaData = $wpdb->get_results( 
		    									$wpdb->prepare( $sqlMeta, 
		    									        $form_id, 
		    									        $language_origin,
		    									        '#f%'
		    									        )
		    									, ARRAY_A
		    								);    	

		    		if( !empty($ResultSetMetaData) ){
    					foreach( $ResultSetMetaData as $row ){
    				    	$ResultSetMeta[($row['meta_key'])] = $row['meta_value'];
    					}
    				}									
    			}		


			return $ResultSetMeta;
		}		

		public function get_info( $form_id, $language )
		{
			global $wpdb;

			$Result = array();
			$ResultSet = array();
			$ResultSetMeta = array();
			$ResultSetData = array();
			$ResultSetMetaData = array();
            $CForm_fields = new CForm_Fields;

			$sql = "SELECT * FROM {$wpdb->prefix}migla_form WHERE form_id = %d";
			
			$ResultSetData = $wpdb->get_results( 
								$wpdb->prepare( $sql, $form_id ), ARRAY_A
							);

			if( !empty($ResultSetData) ){
			    
				$ResultSet["form_id"] = $form_id;
				$ResultSet["amounts"] = "";
				$ResultSet["hideCustomAmount"] = 'no';
				$ResultSet["amountBoxType"] = 'box';
				$ResultSet["buttonType"] = 'button';
				$ResultSet["structure"] = "";	
				$ResultSet["campaigns"] = "";		    
			    
				foreach( $ResultSetData as $row ){
					foreach( (array)$row as $col => $val )
					{
						if( $col == "structure" ){
							if( empty($val) ){
								$ResultSet[$col] = $CForm_fields::form_fields();
							}else{
								$ResultSet[$col] = (array)unserialize($val);
							}
						}else if( $col == "amounts" ){
                            if(empty($val)){
							    $ResultSet[$col] = "";
                            }else{
							    $ResultSet[$col] = (array)unserialize($val);
                            }					    
						
						}else{
							$ResultSet[$col] = $val;
						}
					}
				}

			}else{
				
				$amounts = array( array( 'amount' => 10, 'perk' => 'giving level 1' ) 
							) ;

				$ResultSet["form_id"] = $form_id;
				$ResultSet["amounts"] = $amounts;
				$ResultSet["hideCustomAmount"] = 'no';
				$ResultSet["amountBoxType"] = 'box';
				$ResultSet["buttonType"] = 'button';
				$ResultSet["structure"] = $CForm_fields::form_fields();
				$ResultSet["campaigns"] = "";		    
				
			}

			if( empty($language) ) $language = get_locale();

			$sqlMeta = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
			$sqlMeta .= " WHERE form_id = %d AND language = %s";

			$ResultSetMetaData = $wpdb->get_results( 
									$wpdb->prepare( $sqlMeta, $form_id, $language )
									, ARRAY_A
								);


			if( !empty($ResultSetMetaData) ){
			    
				$ResultSetMeta['warning_1'] = '';
				$ResultSetMeta['warning_2'] = '';
				$ResultSetMeta['warning_3'] = '';
				$ResultSetMeta['meta_amounts'] = '';
				$ResultSetMeta['custom_amount_text'] = 'Custom';
				$ResultSetMeta['form_url'] = '';			    
                $ResultSetMeta['fields'] = "";
                
				foreach( $ResultSetMetaData as $row )
				{	
					if( $row['meta_key'] == "fields" )
					{
						$ResultSetMeta[($row['meta_key'])] = (array)unserialize($row['meta_value']);
					}else{				
						$ResultSetMeta[($row['meta_key'])] = $row['meta_value'];
					}
				}

			}else{

				$ResultSetMeta['warning_1'] = 'Please insert all the required fields';
				$ResultSetMeta['warning_2'] = 'Please insert correct email';
				$ResultSetMeta['warning_3'] = 'Please fill in a valid amount';
				$ResultSetMeta['custom_amount_text'] = 'Custom';
				$ResultSetMeta['form_url'] = '';

				$fields = $ResultSet["structure"];
				$ResultSetMeta['fields'] = $fields;
			}			

			$Result = array_merge( $ResultSet, $ResultSetMeta );

            if( !isset($Result['fields']) || empty($Result['fields']) )
            {
                $Result['fields'] = $CForm_fields::form_fields_for_translate1($ResultSet["structure"]);
            }
            
            $meta_gateways = $this->init_meta_gateways();
            
            $sqlMeta = "SELECT * FROM {$wpdb->prefix}migla_form_meta";
			$sqlMeta .= " WHERE form_id = %d AND language = %s";
			$sqlMeta .= " AND meta_key like %s";
			
            $ResultSetGateway =  array();
            $ResultSetGateway = $wpdb->get_results( 
									$wpdb->prepare( $sqlMeta, 0, $language, "%tab_info" )
									, ARRAY_A
								);
            
            if( !empty($ResultSetGateway) )
            {
                foreach( $ResultSetGateway as $record ){
                    if(!empty($record['meta_value']))
                    {
                        $Result[($record['meta_key'])] = $record['meta_value'];
                    }else{
                        $Result[($record['meta_key'])] = (array)unserialize($record['meta_value']);
                    }
                }                
            }

			return $Result;
		}

		public function translate_form( $structure, $fields )
		{
			$i = 0; $j = 0;
            if(!empty($structure) && !empty($fields))
            {
    			foreach( $structure as $sections )
    			{
    			    if( isset( $structure[$i]['title'] ) )
    			    {
    				    $structure[$i]['title'] = $fields[$i]['title'];
    			    }else{
    			        $structure[$i]['title'] = "";
    			    }
    			    
                    if( isset($structure[$i]['child']) ){
        				$childs = $structure[$i]['child'];
        				$j = 0;
        
        				foreach ($childs as $child ) {
        					$structure[$i]['child'][$j]['label'] = $fields[$i]['child'][$j]['label'];
                            $j++;
        				}
                    }
                    
    				$i++;
    			}
            }
			return $structure;
		}

		public function if_exist( $form_id )
		{
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT * FROM {$wpdb->prefix}migla_form WHERE form_id = %d";
			
			$id = $wpdb->get_var( $wpdb->prepare( $sql, $form_id ) );

			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
		}

		public function if_meta_exist( $form_id, $meta_key, $language )
		{
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_form_meta WHERE form_id = %d";
			$sql .= " AND meta_key = %s ";
			$sql .= " AND language = %s";
			
			$id = $wpdb->get_var( $wpdb->prepare( $sql, $form_id, $meta_key, $language ) );

			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
		}

        public function get_column( $id, $colName )
        {
              global $wpdb;
              
              $sql = "SELECT ".$colName." FROM {$wpdb->prefix}migla_form";
              $sql .= " WHERE form_id = %d";

              $col = $wpdb->get_var($wpdb->prepare(
                        $sql, $id )
                    );
              
            return $col;
        } 

		public function update_column( $columnUpdates, $keyValues, $columnTypes, $keyTypes )
		{
		    global $wpdb;
		    
		    $wpdb->update( "{$wpdb->prefix}migla_form",
		            $columnUpdates ,
			        $keyValues,
			        $columnTypes,
			        $keyTypes
		  	    );
		}
		
        public function get_meta( $id, $key, $language )
        {
              global $wpdb;
              
              $sql = "SELECT meta_value FROM {$wpdb->prefix}migla_form_meta";
              $sql .= " WHERE form_id = %d";
              $sql .= " AND meta_key = %s";
              $sql .= " AND language = %s";

              $col = $wpdb->get_var($wpdb->prepare(
                        $sql, $id, $key, $language )
                    );
              
            return $col;
        } 
        
		public function update_meta( $form_id, $language, $key, $value, $valtype )
		{
		    global $wpdb;
		    
		    if( $valtype == 'array' ){
		        $value = serialize($value);
		    }
		    
		    $wpdb->update( "{$wpdb->prefix}migla_form_meta",
		            array( 'meta_value' => $value ),
			        array( 'form_id' => $form_id,
			                'language' => $language,
			                'meta_key' => $key
			                ),
			        array( '%s' ),
			        array( '%d', '%s', '%s' )
		  	    );
		}  
		
		public function get_header_of_custom_field( $field )
		{
		    global $wpdb;
		    
		    $label = $field;
		    
		    $sql = "SELECT structure FROM {$wpdb->prefix}migla_form";
            $sql .= " WHERE structure like %s";

            $col = $wpdb->get_var($wpdb->prepare(
                        $sql, '%'.$field.'%' )
                    );
            
            if( !empty($col) && $col != null && $col != false ){
                $struct = (array)unserialize($col);
                
                foreach($struct as $sect){
                    if( isset($sect['child']) ){
                        $children = (array)$sect['child'];
                        foreach($children as $child){
                            if( isset($child['uid']) && $child['uid'] == $field ){
                                $label = $child['label'];
                            }
                        }
                    }
                }
            }
            
            return $label;        
		}

	    public function stripe_tab($cc)
	    {
		    $tb = array();
		    
		    if( isset($cc['tab']) )
		        $tb['tab'] = $cc['tab'];
		      else
		        $tb['tab'] = 'Stripe';    

		    if( isset( $cc['cardholder']['placeholder'] ) )
		        $tb['cardholder']['placeholder'] = $cc['cardholder']['placeholder'];   
		      else
		          $tb['cardholder']['placeholder'] = 'Your Name';  
		    
		    if( isset( $cc['cardholder']['label'] ) )
		        $tb['cardholder']['label'] = $cc['cardholder']['label'];
		    else
		        $tb['cardholder']['label'] = 'First Name';
		    
		    if( isset( $cc['cardholder']['last_placeholder'] ) )
		        $tb['cardholder']['last_placeholder'] = $cc['cardholder']['last_placeholder'];
		    else
		        $tb['cardholder']['last_placeholder'] = 'Your Last Name';  

		    if( isset( $cc['cardnumber']['label'] ) )
		        $tb['cardnumber']['label'] = $cc['cardnumber']['label'];
		    else
		        $tb['cardnumber']['label'] = 'Card Number';   
		        
		    if( isset( $cc['cardnumber']['placeholder'] ) )
		        $tb['cardnumber']['placeholder'] = $cc['cardnumber']['placeholder'];      
		    else  
		        $tb['cardnumber']['placeholder'] = 'Your Card Number';      
		      
		    if( isset( $cc['cardcvc']['label'] ) )  
		        $tb['cardcvc']['label'] = $cc['cardcvc']['label'];
		    else
		        $tb['cardcvc']['label'] = 'Expiration//CVC';

		    if( isset( $cc['cardcvc']['placeholder'] ) )  
		        $tb['cardcvc']['placeholder'] = $cc['cardcvc']['placeholder'];
		    else
		        $tb['cardcvc']['placeholder'] = 'CVC';		    

		    if( isset($cc['button']) )
		        $tb['button'] = $cc['button'];    
		    else
		        $tb['button'] = 'Pay';    
		    
		    if( isset($cc['loading_message']) )
		        $tb['loading_message'] = $cc['loading_message'];
		    else
		        $tb['loading_message'] = 'Please wait while we processing your donation';

		    return $tb;
	    }

	    public function authorize_tab($Auth)
	    {        
			$tb = array();
			
			if( isset($Auth['tab']) )
	    		$tb['tab'] = $Auth['tab'];
	    	else
	    		$tb['tab'] = 'Authorize.Net'; 
			
			if( isset( $Auth['cardholder']['label'] ) )
			    $tb['cardholder']['label'] = $Auth['cardholder']['label'];
			else
			    $tb['cardholder']['label'] = 'Name';
			
			if( isset( $Auth['cardholder']['placeholder'] ) )
			    $tb['cardholder']['placeholder'] = $Auth['cardholder']['placeholder'];
			else
			    $tb['cardholder']['placeholder'] = 'First Name';
			    
			if( isset( $Auth['cardholder']['last_placeholder'] ) )
			    $tb['cardholder']['last_placeholder'] = $Auth['cardholder']['last_placeholder'];
			else
			    $tb['cardholder']['last_placeholder'] = 'Last Name';	
			    
			if( isset( $Auth['cardnumber']['label'] ) )
			    $tb['cardnumber']['label'] = $Auth['cardnumber']['label'];
			else
			    $tb['cardnumber']['label'] = 'Card Number'; 	
			    
			if( isset( $Auth['cardnumber']['placeholder'] ) )
			    $tb['cardnumber']['placeholder'] = $Auth['cardnumber']['placeholder'];      
			else  
			    $tb['cardnumber']['placeholder'] = 'Your Card Number';      
			  
			if( isset( $Auth['cardcvc']['label'] ) )  
	    		$tb['cardcvc']['label'] = $Auth['cardcvc']['label'];
			else
		    	$tb['cardcvc']['label'] = 'Expiration//CVC';

			if( isset( $Auth['cardcvc']['placeholder'] ) )  
	    		$tb['cardcvc']['placeholder'] = $Auth['cardcvc']['placeholder'];
			else
		    	$tb['cardcvc']['placeholder'] = 'CVC';

	        if( isset($Auth['button']) )
	    		$tb['button'] = $Auth['button'];		
	        else
	    		$tb['button'] = 'Pay';		
			
			if( isset($Auth['loading_message']) )
			    $tb['loading_message'] = $Auth['loading_message'];
			else
	    		$tb['loading_message'] = 'Please wait while we processing your donation';
		
			return $tb;
	    }

	    public function paypal_tab($cc)
	    {    
		    $tb = array();
		    
		    if( isset($cc['tab']) )
		        $tb['tab'] = $cc['tab'];
		      else
		        $tb['tab'] = 'Paypal'; 

		    if( isset($cc['methodchoice'][0]) )
		        $tb['methodchoice'][0] = $cc['methodchoice'][0];     //1
		    else
		        $tb['methodchoice'][0] = 'Pay with Credit Card';     //1
		        
		    if( isset($cc['methodchoice'][1]) )
		        $tb['methodchoice'][1] = $cc['methodchoice'][1];  //2      
		    else
		        $tb['methodchoice'][1] = 'Pay with paypal account';  //2      

		    if( isset( $cc['cardholder']['placeholder'] ) )
		        $tb['cardholder']['placeholder'] = $cc['cardholder']['placeholder'];   
		      else
		          $tb['cardholder']['placeholder'] = 'Your Name';  
		    
		    if( isset( $cc['cardholder']['label'] ) )
		        $tb['cardholder']['label'] = $cc['cardholder']['label'];
		    else
		        $tb['cardholder']['label'] = 'Name';

		    if( isset( $cc['cardholder']['last_placeholder'] ) )
		        $tb['cardholder']['last_placeholder'] = $cc['cardholder']['last_placeholder'];
		    else
		        $tb['cardholder']['last_placeholder'] = 'Last Name';  
		        
		    if( isset( $cc['cardnumber']['label'] ) )
		        $tb['cardnumber']['label'] = $cc['cardnumber']['label'];
		    else
		        $tb['cardnumber']['label'] = 'Card Number';   
		        
		    if( isset( $cc['cardnumber']['placeholder'] ) )
		        $tb['cardnumber']['placeholder'] = $cc['cardnumber']['placeholder'];      
		    else  
		        $tb['cardnumber']['placeholder'] = 'Your Card Number';      
		      
		    if( isset( $cc['cardcvc']['label'] ) )  
		        $tb['cardcvc']['label'] = $cc['cardcvc']['label'];
		    else
		        $tb['cardcvc']['label'] = 'Expiration//CVC';

		    if( isset( $cc['cardcvc']['placeholder'] ) )  
		        $tb['cardcvc']['placeholder'] = $cc['cardcvc']['placeholder'];
		    else
		        $tb['cardcvc']['placeholder'] = 'CVC';

		    if( isset($cc['button']) )
		        $tb['button'] = $cc['button'];    
		    else
		        $tb['button'] = 'Pay';    
		    
		    if( isset($cc['loading_message']) )
		        $tb['loading_message'] = $cc['loading_message'];
		    else
		        $tb['loading_message'] = 'Please wait while we processing your donation';

		    return $tb;
       	}

	    public function offline_tab($offlines)
	    {    	    	
	        $tb = array();

	        if( isset($offlines['tab']) ){
	        	$tb['tab'] = $offlines['tab'] ;
	        }else{
	        	$tb['tab'] = 'Offline';
	        }

	        if( isset($offlines['button']) ){
	        	$tb['button'] = $offlines['button'];
	        }else{
	        	$tb['button'] = 'Pay';
	        }

	        if( isset($offlines['loading_message']) ){
	        	$tb['loading_message'] = $offlines['loading_message'];
	        }else{
	        	$tb['loading_message'] = 'Please wait while we processing your donation';
	        }

	        if( isset($offlines['after_donation_message']) ){
	        	$tb['after_donation_message'] = $offlines['after_donation_message'];
	        }else{
		  		$tb['after_donation_message'] = 'Please contact our office for more info';
		  	}

	        return $tb;
	    }
	    
	    public function retrive_header_for_export()
	    {
	        global $wpdb;
	        
	        $sql = "SELECT DISTINCT meta_key FROM ". $wpdb->prefix . "migla_donation_meta ORDER BY meta_key";
	        
	        $data = $wpdb->get_results( $sql , ARRAY_A );
	        
	        $headers = array( 'date_created', 'time',
	                            'id', 'status', 
	                            'email', 'firstname', 'lastname', 
	                            'miglad_currency', 'amount', 
	                            'campaign', 'miglad_campaign_name',
	                            'miglad_address', 'miglad_city',
	                            'country', 'country_code', 
	                            'miglad_state', 'state_code', 
	                            'miglad_province', 'province_code',
	                            'miglad_postalcode',
	                            'anonymous', 
	                            'repeating', 'miglad_repeating',
	                            'miglad_employer', 'miglad_occupation',
	                            'miglad_memorialgift', 'miglad_honoreename',
	                            'miglad_honoreeemail', 'miglad_honoreeletter',
	                            'miglad_honoreeaddress', 'miglad_honoreecity',
	                            'miglad_honoreecountry', 'miglad_honoreestate',
	                            'miglad_honoreeprovince', 'miglad_honoreepostalcode',
	                            'mailist', 'gateway', 
	                            'gmt', 'session_id', 'timestamp' 
	                            );
	        
	        $k = count($headers);
	        
	        if( !empty($data) ){
	            foreach( $data as $row )
	            {
	                if( $row['meta_key'] == 'miglad_paymentdata' 
	                    || $row['meta_key'] == 'miglad_session_id'
	                    || $row['meta_key'] == 'miglad_session_id_'
	                    || $row['meta_key'] == 'miglad_firstname'
	                    || $row['meta_key'] == 'miglad_lastname'
	                    || $row['meta_key'] == 'miglad_amount'
	                    || $row['meta_key'] == 'miglad_country'
	                    || $row['meta_key'] == 'miglad_campaign'
	                    || $row['meta_key'] == 'miglad_email'
	                    || $row['meta_key'] == 'miglad_form_id'
	                    || $row['meta_key'] == 'miglad_anonymous'
	                    || in_array( $row['meta_key'], $headers )
	                )
	                {
	                }else{
	                    $headers[$k] = $row['meta_key'];
	                    $k++;
	                }
	            }
	        }

            return $headers;
	    }
	    
	    public function retrieve_label( $field_uid )
	    {
	        global $wpdb;
	        
	        $sql = "SELECT structure FROM ". $wpdb->prefix . "migla_form where structure like %s";
	        
	        $structure = $wpdb->get_var( $wpdb->prepare( $sql, '%'. $field_uid.'%' ) );
	        
	        $label = $field_uid;
	        
	        if(!empty($structure))
	        {
	            $structure = (array)unserialize($structure);
	            
	            foreach($structure as $section)
	            {
	                if(isset($section['child']))
	                {
	                    $children = $section['child'];
	                    
	                    foreach( $children as $child )
	                    {
	                        if( $child['uid'] == $field_uid ){
	                            $label = $child['label'];
	                        }
	                    }
	                }
	            }
	        }
	        
	        return $label;
	    }
	    
	    public function retrive_header_for_advanced_export()
	    {
	        global $wpdb;
	        
	        $sql = "SELECT DISTINCT meta_key FROM ". $wpdb->prefix . "migla_donation_meta ORDER BY meta_key";
	        
	        $data = $wpdb->get_results( $sql , ARRAY_A );
	        
	        $headers = array( 'date_created', 'id', 'status', 
	                            'email', 'firstname', 'lastname', 
	                            'miglad_currency', 'amount', 
	                            'campaign', 'miglad_campaign_name',
	                            'miglad_address', 'miglad_city',
	                            'country', 'miglad_state', 'miglad_province',
	                            'miglad_postalcode',
	                            'anonymous', 
	                            'repeating', 'miglad_repeating',
	                            'miglad_employer', 'miglad_occupation',
	                            'miglad_memorialgift', 'miglad_honoreename',
	                            'miglad_honoreeemail', 'miglad_honoreeletter',
	                            'miglad_honoreeaddress', 'miglad_honoreecity',
	                            'miglad_honoreecountry', 'miglad_honoreestate',
	                            'miglad_honoreeprovince', 'miglad_honoreepostalcode',
	                            'mailist', 'gateway', 
	                            'gmt', 'session_id', 'timestamp' 
	                            );
	        
	        $k = count($headers);
	        
	        $labels = array();
	        
	        if( !empty($data) ){
	            foreach( $data as $row )
	            {
	                if( $row['meta_key'] == 'miglad_paymentdata' 
	                    || $row['meta_key'] == 'miglad_session_id'
	                    || $row['meta_key'] == 'miglad_session_id_'
	                    || $row['meta_key'] == 'miglad_firstname'
	                    || $row['meta_key'] == 'miglad_lastname'
	                    || $row['meta_key'] == 'miglad_amount'
	                    || $row['meta_key'] == 'miglad_country'
	                    || $row['meta_key'] == 'miglad_campaign'
	                    || $row['meta_key'] == 'miglad_email'
	                    || $row['meta_key'] == 'miglad_form_id'
	                    || $row['meta_key'] == 'miglad_anonymous'
	                    || in_array( $row['meta_key'], $headers )
	                )
	                {
	                }else{
	                    if( substr($row['meta_key'], 0, 1) == 'f' || substr($row['meta_key'], 0, 2) == '#f' )
	                    {
	                        $metakey = str_replace("#","", $row['meta_key']);
	                        
	                        if( in_array( $metakey, $labels) )
	                        {
	                            $this_label =  $labels[$metakey];
	                        }else{
	                            $this_label = $this->retrieve_label( $metakey );
	                            $labels[$metakey] = $this_label;
	                        }
	                        
	                        $headers[$k] = $this_label . ' =>' . $row['meta_key'];
	                        $k++;
	                        
	                    }else{
	                        $headers[$k] = $row['meta_key'];
	                        $k++;
	                    }
	                }
	            }
	        }

            return $headers;
	    }

	}//EndOfClass
}
?>