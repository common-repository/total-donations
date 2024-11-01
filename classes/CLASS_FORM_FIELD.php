<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CForm_Fields' ) )
{
class CForm_Fields
{
	static function form_fields()
	{
		
		$fields = array (
		        '0' => array (
		            'title' => 'Donation Information',
		            'child' =>  array(
		                       '0' => array( 'type'=>'notype',
		                       				 'id'=>'amount', 
		                       				 'label'=>'How much would you like to donate?', 
		                       				 'status'=>'3', 
		                       				 'code' => 'miglad_', 
		                           			 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                           			),
		                       '1' => array( 'type'=>'select',
		                       				 'id'=>'campaign', 
		                       				 'label'=>'Would you like to donate this to a specific campaign?', 
		                       				 'status'=>'3', 
		                       				 'code' => 'miglad_', 
		                           			 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                           			 ),
		                       '2' => array( 'type'=>'notype',
		                       				 'id'=>'repeating', 
		                       				 'label'=>'Is this a recurring donation?', 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                           			 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                           			 ),
		                       '3' => array( 'type'=>'checkbox',
		                       				 'id'=>'mg_add_to_milist', 
		                       				 'label'=>'Add to mailing list?', 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                           			 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                           			 )
		                     	),
		            'toggle' => '-1'
		        ),
		        '1' => array (
		            'title' => 'Donor Information',
		            'child' => array(
		                       '0' => array( 'type'=>'text',
		                       				 'id'=>'firstname', 
		                       				 'label'=>'First Name', 
		                       				 'status'=>'3', 
		                       				 'code' => 'miglad_', 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 ),
		                       '1' => array( 'type'=>'text',
		                       				 'id'=>'lastname', 
		                       				 'label'=>'Last Name', 
		                       				 'status'=>'3', 
		                       				 'code' => 'miglad_', 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 ),
		                       '2' => array( 'type'=>'text',
		                       				 'id'=>'address', 
		                       				 'label'=>'Address', 
		                       				 'status'=>'1' , 
		                       				 'code' => 'miglad_', 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 ),
		                       '3' => array( 'type'=>'select',
		                       				 'id'=>'country', 
		                       				 'label'=>'Country', 
		                       				 'status'=>'1' , 
		                       				 'code' => 'miglad_', 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 ),
		                       '4' => array( 'type'=>'text',
		                       				 'id'=>'city', 
		                       				 'label'=>'City', 
		                       				 'status'=>'1' , 
		                       				 'code' => 'miglad_', 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 ),
		                       '5' => array( 'type'=>'text',
		                       				 'id'=>'postalcode', 
		                       				 'label'=>'Postal Code', 
		                       				 'status'=>'1' , 
		                       				 'code' => 'miglad_', 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 ),
		                       '6' => array( 'type'=>'checkbox',
		                       				 'id'=>'anonymous', 
		                       				 'label'=>'Anonymous?', 
		                       				 'status'=>'1' , 
		                       				 'code' => 'miglad_', 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 ),
		                       '7' => array( 'type'=>'text',
		                       				 'id'=>'email', 
		                       				 'label'=>'Email', 
		                       				 'status'=>'3' , 
		                       				 'code' => 'miglad_' , 
		                       				 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                       				 )
		                     ),
		            'toggle' => '-1'
		        ),
		        '2' => array (
		            'title' => 'Is this in honor of someone?',
		            'child' => array(
		                       '0' => array( 'type'=>'checkbox',
		                       				 'id'=>'memorialgift', 
		                       				 'label'=>"Is this a Memorial Gift?", 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                           			 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                           			 ),
		                       '1' => array( 'type'=>'text',
		                       				 'id'=>'honoreename', 
		                       				 'label'=>"Honoree[q]s Name", 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                            		 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                            		 ),
		                       '2' => array( 'type'=>'text',
		                       				 'id'=>'honoreeemail', 
		                       				 'label'=>"Honoree[q]s Email", 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                            		 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                            		 ),
		                       '3' => array( 'type'=>'textarea',
		                       				 'id'=>'honoreeletter', 
		                       				 'label'=>"Write a custom note to the Honoree here", 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                            		 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                            		 ),
		                       '4' => array( 'type'=>'text',
		                       				 'id'=>'honoreeaddress', 
		                       				 'label'=>"Honoree[q]s Address", 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                            		 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                            		 ),
		                       '5' => array( 'type'=>'text',
		                       				 'id'=>'honoreecountry', 
		                       				 'label'=>"Honoree[q]s Country", 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                            		 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                            		 ),
		                       '6' => array( 'type'=>'text',
		                       				 'id'=>'honoreecity', 
		                       				 'label'=>'Honoree[q]s City', 
		                       				 'status'=>'1' , 
		                       				 'code' => 'miglad_', 
		                             		 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                             		 ),
		                       '7' => array( 'type'=>'text',
		                       				 'id'=>'honoreepostalcode', 
		                       				 'label'=>'Honoree[q]s Postal Code', 
		                       				 'status'=>'1' , 
		                       				 'code' => 'miglad_', 
		                             		 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                             		 )         
		                     ),
		            'toggle' => '1'

		        ),
		        '3' => array (
		            'title' => 'Is this a matching gift?',
		            'child' => array(
		                       '0' => array( 'type'=>'text',
		                       				 'id'=>'employer', 
		                       				 'label'=>'Employer[q]s Name', 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                           			 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                           			 ),
		                       '1' => array( 'type'=>'text',
		                       				 'id'=>'occupation', 
		                       				 'label'=>'Occupation', 
		                       				 'status'=>'1', 
		                       				 'code' => 'miglad_', 
		                           			 'uid' => ("f".date("Ymdhis"). "_" . rand()) 
		                           			 )
		                     ),
		            'toggle' => '1'
		        )        
		     );  

		   return $fields;
	}	

	static function form_fields_for_translate1($structs)
	{
		$fields = array();
		$i = 0;

		foreach( $structs as $struct ){
			$fields[$i]['title'] = $struct['title'];
			
			if(isset($struct['child'])){
    			$children = $struct['child'];
    			$fields[$i]['child'] = array();
    			$childs = array();
    			$j = 0;
    
    			foreach( $children as $child ){
    				$childs[$j]['id'] = $child['id'];
    				$childs[$j]['uid'] = $child['uid'];
    				$childs[$j]['label'] = $child['label'];
    				$j++;
    			}
    
    			$fields[$i]['child'] = $childs;
			}
			$i++;
		}

		return $fields;
	}

	static function form_fields_for_translate()
	{
		$structs = self::form_fields();
		$fields = array();
		$i = 0;

		foreach( $structs as $struct ){
			$fields[$i]['title'] = $struct['title'];
			
			$children = $struct['child'];
			$fields[$i]['child'] = array();
			$childs = array();
			$j = 0;

			foreach( $children as $child ){
				$childs[$j]['id'] = $child['uid'];
				$childs[$j]['uid'] = $child['uid'];
				$childs[$j]['label'] = $child['label'];
				$j++;
			}

			$fields[$i]['child'] = $childs;

			$i++;
		}
	}

	static function get_default_form_fields()
	{
		$fields = array( "miglad_amount" 	=> '',
						 "miglad_campaign" 	=> '',
						 "miglad_repeating" => '',
						 "miglad_mg_add_to_milist" => '',
						 "miglad_firstname" => '',
						 "miglad_lastname" => '',
						 "miglad_address" => '',
						 "miglad_country" => '',
						 "miglad_city" => '',
						 "miglad_postalcode" => '',
						 "miglad_anonymous" => '',
						 "miglad_email" => '',
						 "miglad_memorialgift" => '',
						 "miglad_honoreename" => '',
						 "miglad_honoreeemail" => '',
						 "miglad_honoreeletter" => '',						 
						 "miglad_honoreeaddress" => '',		
						 "miglad_honoreecountry" => '',	
						 "miglad_honoreecity" => '',	
						 "miglad_honoreepostalcode" => '',
						 "miglad_employer" => '',	
						 "miglad_occupation" => '',
						 "miglad_date" => ''					 
						);
						
		return $fields;
	}	
	
	static function get_default_donation_fields()
	{
		$fields = array( "miglad_amount" 	=> '',
						 "miglad_campaign" 	=> '',
						 "miglad_repeating" => 'no',
						 "miglad_mg_add_to_milist" => '',
						 "miglad_firstname" => '',
						 "miglad_lastname" 	=> '',
						 "miglad_address" 	=> '',
						 "miglad_country"	=> '',
						 "miglad_state"	=> '',
						 "miglad_province"	=> '',
						 "miglad_city" 		=> '',
						 "miglad_postalcode" 	=> '',
						 "miglad_anonymous" 	=> 'no',
						 "miglad_email" 		=> '',
						 "miglad_memorialgift" 	=> '',
						 "miglad_honoreename" 	=> '',
						 "miglad_honoreeemail" 	=> '',
						 "miglad_honoreeletter" => '',						 
						 "miglad_honoreeaddress" => '',		
						 "miglad_honoreecountry" => '',	
						 "miglad_honoreecity" 	=> '',	
						 "miglad_honoreepostalcode" => '',
						 "miglad_employer" 		=> '',	
						 "miglad_occupation" 	=> '',
						 
						 'miglad_session_id'	=> '',

						 "miglad_transactionType"	=> '',
						 "miglad_transactionId"		=> '',
						 'miglad_paymentmethod'		=> '',
						 'miglad_paymentdata'		=> '',
						 'miglad_avs_response_text'	=> '',
						 
						 'miglad_customer_created'	=> '',
						 'miglad_customer_id'		=> '',
						 'miglad_subscription_type'	=> '',
						 'miglad_subscription_id'	=> '',
						 'miglad_date'				=> '',
						 'miglad_time'				=> '',
						 'miglad_timezone'			=> '',
						 'miglad_charge_dispute'	=> '',
						 
						 'miglad_form_id'			=> '',
						 'miglad_language'			=> get_locale()
						);
						
		return $fields;
	}	
	
	static function get_email_testing_form_fields()
	{
		$fields = array( "miglad_amount" 	=> '10000',
						 "miglad_campaign" 	=> 'General Donation',
						 "miglad_repeating" => 'TDxxx;1;month;Every Month',
						 "miglad_mg_add_to_milist" => 'no',
						 "miglad_firstname" => 'John',
						 "miglad_lastname" => 'Doe',
						 "miglad_address" => 'Street 1001',
						 "miglad_country" => 'United States',
						 "miglad_state" => 'California',
						 "miglad_province" => '',
						 "miglad_city" => 'Los Angeles',
						 "miglad_postalcode" => '9001',
						 "miglad_anonymous" => 'yes',
						 "miglad_email" => '',
						 
						 "miglad_memorialgift" => 'no',
						 "miglad_honoreename" => 'Jane Doe',
						 "miglad_honoreeemail" => '',
						 "miglad_honoreeletter" => 'Thank you for supporting us',						 
						 "miglad_honoreeaddress" => '2271  Duke Street',		
						 "miglad_honoreecountry" => 'Canada',	
						 "miglad_honoreecity" => 'Montreal',	
						 "miglad_honoreestate" => '',
						 "miglad_honoreeprovince" => 'Quebec',
						 "miglad_honoreepostalcode" => 'H3C 5K4',
						 
						 "miglad_employer" => '',	
						 "miglad_occupation" => '',
						 "miglad_date" => ''					 
						);
						
		return $fields;
	}
	
	public function map_custom_field_label()
	{
	    global $wpdb;
		    
		    $sql = "SELECT structure, id from ". $wpdb->prefix."migla_form";
		    
		    $data = $wpdb->get_results( $sql, ARRAY_A );
		    
		    $custom_fields = array();
		    
		    if( !empty($data) ){
		        foreach( $data as $row ){
		            
		            if( !empty($row['structure']) ){
		                
		                $structure = (array)unserialize($row['structure']);
		                
		                foreach( $structure as $section ){
		                    if( isset($section['child']) && !empty($section['child']) )
		                    {
		                        $children = $section['child'];
		                        
    		                    foreach( $children as $formfield ){
    		                        if( $formfield['code'] == "miglac_" ){
    		                            $custom_fields[($formfield['uid'])] = $formfield['label'];
    		                        }
    		                    }
		                    }
		                }//foreach section
		            }//
		        }
		    }
		    
        return $custom_fields;
	}	
}
}
?>