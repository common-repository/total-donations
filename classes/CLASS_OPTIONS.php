<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_OPTION' ) )
{
	class MIGLA_OPTION
	{
	    public static function init( $option_name, $option_value, $valType )
	    {
	        global $wpdb;

          	$id = '';

					$sql = "SELECT id FROM {$wpdb->prefix}migla_options";
					$sql .= " WHERE option_name = %s";

					$id = $wpdb->get_var( $wpdb->prepare( $sql, $option_name ) );

	        if( $valType == 'array' )
	        {
	            $option_value = serialize($option_value);
	        }

	        if( $id > 0 )
            {
                $wpdb->update( "{$wpdb->prefix}migla_options",
		            array( "option_value" => $option_value ),
			        array( "id" => $id
			                ),
			        array( '%s' ),
			        array( '%d' )
		  	    );
            }else{
                $wpdb->insert( "{$wpdb->prefix}migla_options",
		            array( "option_name" => $option_name,
		                    "option_value" => $option_value
			                ),
			        array( '%s', '%s' )
		  	    );
            }
	    }//endfunction

	    public static function init_sitecode( $option_name, $option_value, $valType )
	    {
	        global $wpdb;

          $id = '';

					$sql = "SELECT id FROM {$wpdb->prefix}migla_options";
					$sql .= " WHERE option_name = %s";

					$id = $wpdb->get_var( $wpdb->prepare( $sql, $option_name ) );

	        if( $valType == 'array' )
	        {
	            $option_value = serialize($option_value);
	        }

	      if( $id > 0 )
        {
        }else{
            $wpdb->insert( "{$wpdb->prefix}migla_options",
		            array( "option_name" => $option_name,
		                    "option_value" => $option_value
			                ),
			          array( '%s', '%s' )
		  	    );
        }
	    }//endfunction

	    public function update( $option_name, $option_value, $valType )
	    {
	        global $wpdb;

	        $id = $this->get_id( $option_name );

	        if( $valType == 'array' ){
	            $option_value = serialize($option_value);
	        }

	        if( $id > 0 )
            {
                $wpdb->update( "{$wpdb->prefix}migla_options",
		            				array( "option_value" => $option_value ),
			        					array( "id" => $id  ),
			        					array( '%s' ),
			        					array( '%d' )
		  	    		);

            }else{

                $wpdb->insert( "{$wpdb->prefix}migla_options",
				            array( "option_name" => $option_name,
				                    "option_value" => $option_value
					                ),
					        	array( '%s', '%s' )
				  	    );
            }
	    }//endfunction

	    public function if_exist($option_name)
	    {
	        global $wpdb;

					$is_exist = false;

					$sql = "SELECT id FROM {$wpdb->prefix}migla_options";
					$sql .= " WHERE option_name = %s";

					$id = $wpdb->get_var( $wpdb->prepare( $sql, $option_name ) );

					if($id > 0){
						$is_exist = true;
					}

					return $is_exist;
	    }

	    public function get_id($option_name)
	    {
	        global $wpdb;

            $id = '';

					$sql = "SELECT id FROM {$wpdb->prefix}migla_options";
					$sql .= " WHERE option_name = %s";

					$id = $wpdb->get_var( $wpdb->prepare( $sql, $option_name ) );

					return $id;
	    }

	    public function get_option( $option_name )
	    {
	        global $wpdb;

            $record = array();
            $value = false;

					$sql = "SELECT option_value FROM {$wpdb->prefix}migla_options";
					$sql .= " WHERE option_name = %s";

					$record = $wpdb->get_results( $wpdb->prepare( $sql, $option_name ), ARRAY_A);

		    		if(!empty($record)){
		    		    foreach($record as $row){
		    		       $value = $row['option_value'];
		    		    }
		    		}

		    		return $value;
	    }

	    public static function st_get_option( $option_name )
	    {
	        global $wpdb;

            $record = array();
            $value = false;

					$sql = "SELECT option_value FROM {$wpdb->prefix}migla_options";
					$sql .= " WHERE option_name = %s";

					$record = $wpdb->get_results( $wpdb->prepare( $sql, $option_name ), ARRAY_A);

		    		if(!empty($record)){
		    		    foreach($record as $row){
		    		       $value = $row['option_value'];
		    		    }
    		}

    		return $value;
	    }

	    public function insert_log( $donation_id, $session, $email_send_status, $thank_you_page, $receipt )
	    {
	        global $wpdb;

	        if(!empty($email_send_status)){
	            $email_send_status = serialize($email_send_status);
	        }

	        $wpdb->insert( "{$wpdb->prefix}migla_donation_log",
		            array( "donation_id" => $donation_id,
		                   "session" => $session,
		                   "email_send_status" => $email_send_status,
		                   "thank_you_page" => $thank_you_page,
		                   "receipt" => $receipt
			                ),
			        array( '%d',
			                '%s',
			                '%s',
			                '%s',
			                '%s' )
		  	    );

	    }

	    public function update_log( $donation_id, $update_columns, $update_types )
	    {
	        global $wpdb;

	        $wpdb->update( "{$wpdb->prefix}migla_donation_log",
		                    $update_columns,
		                    array( "donation_id" => $donation_id ),
		                    $update_types,
		                    array('%d')
		  	    );

	    }

	    public function if_logged($donation_id, $session)
	    {
	        global $wpdb;
	        $id = 0;

					$sql = "SELECT id FROM {$wpdb->prefix}migla_donation_log";
					$sql .= " WHERE";

					$search_value = "";

					if( !empty($donation_id) )
					{
					    $sql .= " donation_id = %d";
		    			$search_value = $donation_id;

					}else if( !empty($session) )
					{
					    $sql .= " session = %s";
		    			$search_value = $session;
					}

					if( !empty($search_value) )
					{
					    $id = $wpdb->get_var( $wpdb->prepare( $sql, $search_value ) );
			    }

	        return $id;
	    }
	}
}
