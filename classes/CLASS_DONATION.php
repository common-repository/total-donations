<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'CLASS_MIGLA_DONATION' ) )
{
	class CLASS_MIGLA_DONATION
	{
		public function create_donation( $email, 
									$firstname, 
									$lastname,
									$amount,
									$campaign,
									$country,
									$anon,
									$repeat,
									$mailist,
									$gateway,
									$session
									)
		{
			global $wpdb;
			
			$gmt = get_option( 'gmt_offset' );
			$datetime = date("Y-m-d H:i:s");
			$timestamp = time();
            
            //$wpdb->show_errors();
            
            if( substr( strtolower( $gateway ), 0, 7 ) == 'pending' )
            {
                $status = 2;
            }else{
                $status = 1;
            }
            

            $wpdb->insert( "{$wpdb->prefix}migla_donation",
		            array(
		            		"email"	=>  $email, //1,
		            		"status" => $status,
							"firstname" => $firstname, //2
			                "lastname"   => $lastname, //3
		                  	"amount"  => $amount, //4
		                  	"campaign" => $campaign,
		                  	"country"  => $country, //5
			                "anonymous" => $anon, //6
			                "repeating" => $repeat, //7
			                "mailist" => $mailist, //8
			                "gateway" => $gateway, //9
			                "session_id" => $session, //10
			                "date_created" => $datetime, //11
			                "gmt" => $gmt,//12
			                "timestamp" => $timestamp//13
			            ),
			        array( '%s', //1
			                '%d',
			                '%s', //2
			                '%s', //3
			                '%f', //4
			                '%s', //5
			                '%s', //6
			                '%s', //7
			                '%s', //8
			                '%s', //9
			                '%s', //10
			                '%s', //11 
			                '%s', //12
			                '%d' //13
			                )
		  	);
		  	
		  	$recid = $wpdb->insert_id;
            
            $m = "";
            if($recid > 0){
                $m = "successfully saved ID : " . $recid;
            }else{
                $m = "Unsuccessful saving attempt:". $wpdb->last_error.".";

            }
            
			return $recid;
		}	 
		
		public function create_donation_meta( $donation_id,
		                                $metakey,
		                                $metaval
									)
		{
			global $wpdb;
			
			$m = "";

            if( $this->if_donationmeta_exist( $metakey, $donation_id ) )
            {
                $wpdb->update( "{$wpdb->prefix}migla_donation_meta",
		            array( "meta_value" => $metaval ),
			        array( "donation_id" => $donation_id,
			                "meta_key"	=>  $metakey
			                ),
			        array( '%s' ),
			        array( '%d', '%s' )
		  	    );
		  	    
		  	    $m .= "Update ".$metakey.",";
                
            }else{
                
                $wpdb->insert( "{$wpdb->prefix}migla_donation_meta",
		            array( "meta_value" => $metaval,
		                    "donation_id" => $donation_id,
			                "meta_key"	=> $metakey
			                ),
			        array( '%s', '%d', '%s' )
		  	    );    

		  	    $m .= "Insert ".$metakey.",";
		  	    
            }
		}	
		
		public function update_column( $columnUpdates, 
		                                $keyValues, 
		                                $columnTypes, 
		                                $keyTypes )
		{
		    global $wpdb;
		    
		    $wpdb->update( "{$wpdb->prefix}migla_donation",
		            $columnUpdates ,
			        $keyValues,
			        $columnTypes,
			        $keyTypes
		  	    );
		}
		
		public static function st_update_column( $columnUpdates, 
		                                $keyValues, 
		                                $columnTypes, 
		                                $keyTypes )
		{
		    global $wpdb;
		    
		    $wpdb->update( "{$wpdb->prefix}migla_donation",
		            $columnUpdates ,
			        $keyValues,
			        $columnTypes,
			        $keyTypes
		  	    );
		}			
		
		public function update_meta( $donation_id, $metakey, $metavalue )
		{
		    global $wpdb;
		    
		    $resp = '';
		    
			$sql = "SELECT id FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = %s";
			$sql .= " AND donation_id = %d";

			$metaid = $wpdb->get_var( $wpdb->prepare( $sql, $metakey, $donation_id ) );
		    
		    
		    if( $metaid > 0 )
		    {
    		    $wpdb->update( "{$wpdb->prefix}migla_donation_meta",
    		            array( "meta_value" => $metavalue ),
    			        array( "id" => $metaid ),
    			        array( '%s' ),
    			        array( '%d' )
    		  	);

    		  	
    		  	$resp .= 'Update-' .$metaid. ' '. $metakey.": ".$metavalue;
    		  	
		    }else{
		        
    		    $wpdb->insert( "{$wpdb->prefix}migla_donation_meta",
    		            array( "donation_id" => $donation_id,
    		                    "meta_value" => $metavalue,
    			                "meta_key"	=>  $metakey
    			              ),
    			        array( '%d', '%s', '%s' )
    		  	);

    		  	$resp .= '. Insert '.$metakey.": ".$metavalue;
		        
		    }
		    
		    return $resp;
		}		
		
		public function if_donation_exist( $col, $colval, $coltype )
		{
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_donation";
			$sql .= " WHERE ".$col." = " . $coltype;

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $colval ) );

			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
		}

		public function if_donationmeta_exist( $key, $donation_id )
		{
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = %s";
			$sql .= " AND donation_id = %d";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $key, $donation_id ) );

			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
		}
		

		public function get_donationmeta( $meta_key, $donation_id)
		{
			global $wpdb;

			$sql = "SELECT meta_value FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = %s";
			$sql .= " AND donation_id = %d";

            $meta_value = '';
            $resultSetData = array();
			    

			    $resultSetData = $wpdb->get_results(
			                        $wpdb->prepare($sql, $meta_key, $donation_id )
			                        , ARRAY_A);
			                        
                if(!empty($resultSetData)){
                    foreach( $resultSetData as $row ){
                        $meta_value = $row['meta_value'];
                    }
                }//if not empty
		
		      return $meta_value;
		}		

		public function get_any_donationmeta( $return_value,
		                                      $meta_key,
		                                      $meta_value, 
		                                      $meta_keytype,
		                                      $meta_valtype,
		                                      $order_by,
		                                      $order,
		                                      $limit
		 )
		{
			global $wpdb;

			$sql = "SELECT ".$return_value." FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = ". $meta_keytype;
			$sql .= " AND meta_value = ". $meta_valtype;
			$sql .= " ORDER BY " . $order_by . " " . $order;
			
			if(!empty($limit)){
			    $sql .= " limit 0," . $limit;
			}
			
            $value = '';
            $resultSetData = array();
			    

			$resultSetData = $wpdb->get_results(
			                        $wpdb->prepare($sql, $meta_key, $meta_value )
			                        , ARRAY_A);
			                        
            if(!empty($resultSetData)){
                foreach( $resultSetData as $row ){
                    $value = $row[$return_value];
                }
            }//if not empty
		
		    return $value ;
		}	
		
		public static function st_get_any_donationmeta( $return_value,
		                                      $meta_key,
		                                      $meta_value, 
		                                      $meta_keytype,
		                                      $meta_valtype,
		                                      $order_by,
		                                      $order,
		                                      $limit
		 )
		{
			global $wpdb;

			$sql = "SELECT ".$return_value." FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = ". $meta_keytype;
			$sql .= " AND meta_value = ". $meta_valtype;
			$sql .= " ORDER BY " . $order_by . " " . $order;
			
			if(!empty($limit)){
			    $sql .= " limit 0," . $limit;
			}
			
            $value = '';
            $resultSetData = array();
			    

			$resultSetData = $wpdb->get_results(
			                        $wpdb->prepare($sql, $meta_key, $meta_value )
			                        , ARRAY_A);
			                        
            if(!empty($resultSetData)){
                foreach( $resultSetData as $row ){
                    $value = $row[$return_value];
                }
            }//if not empty
		
		    return $value ;
		}			
		
		public function is_metavalue_exist( $meta_key, $meta_value )
		{
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT donation_id FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = %s";
			$sql .= " AND meta_value = %s";

 			$id = $wpdb->get_var( $wpdb->prepare( $sql, $meta_key, $meta_value ) );

			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
		}
		
		public function getid_metavalue( $meta_key, $meta_value )
		{
			global $wpdb;

			$sql = "SELECT donation_id FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = %s";
			$sql .= " AND meta_value = %s";

 			$id = $wpdb->get_var( $wpdb->prepare( $sql, $meta_key, $meta_value ) );

			return $id;
		}			
		
		public function count_record_bymetavalue( $meta_key, $meta_value )
		{
			global $wpdb;

            $count = 0;

			$sql = "SELECT count(*) as total FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = %s";
			$sql .= " AND meta_value = %s";

 			$count = $wpdb->get_var( $wpdb->prepare( $sql, $meta_key, $meta_value ) );

			return $count ;
		}		
		
		public function get_recent_donation( $time, 
		                                    $period,
		                                    $limit,
		                                    $complete,
		                                    $donation_type,
		                                    $campaign,
		                                    $order,
		                                    $orderBy,
		                                    $status
		)
		{
            global $wpdb;

            $params_count = 0;
            
            if( $complete == '1'){
                //donation only
                
                $sql = "SELECT * FROM {$wpdb->prefix}migla_donation";

                if( !empty($status) )
                {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }

                    $sql .= " status = " . $status;
                    $params_count++;
                }
                
                //Time Period
                if( !empty($time) && !empty($period) )
                {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= "TIMESTAMPDIFF(".$period.", `date_created`, NOW() ) <= " ;
                    $sql .= $time;
                    $params_count++;
                }
                
                //Type of Donation
                if( $donation_type == "1")
                {
                      if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway != 'Offline'";  
                    
                }else if( $donation_type == "2")
                {
                     if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway = 'Offline'";  
                    
                }else{
                }
                
                if( !empty($order) && !empty($orderBy) ){
                    $sql .= " ORDER BY " . $orderBy . " ".$order;
                }
                
                //Number of Rec
                if( !empty($limit) && $limit > 0 ){
                    $sql .= " limit 0," . $limit;
                }
                
            }else if( $complete == '2'){
                //donation meta only
                
                $sql = "SELECT * FROM {$wpdb->prefix}migla_donation_meta";
                
                //Time Period
                if( !empty($time) && !empty($period) )
                {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= "TIMESTAMPDIFF(".$period.", `date_created`, NOW() ) <= " ;
                    $sql .= $time;
                    $params_count++;
                }
                
                //Type of Donation
                if( $donation_type == "1")
                {
                      if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway != 'Offline'";  
                    
                }else if( $donation_type == "2")
                {
                     if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway = 'Offline'";  
                    
                }else{
                }
                
                if( !empty($order) && !empty($orderBy) ){
                    $sql .= " ORDER BY " . $orderBy . " ".$order;
                }
                
                //Number of Rec
                if( !empty($limit) && $limit > 0 ){
                    $sql .= " limit 0," . $limit;
                }                
                
            }else{
                //all
                
                $sql = "SELECT * FROM {$wpdb->prefix}migla_donation";
                $sql .= " INNER JOIN {$wpdb->prefix}migla_donation_meta";
                $sql .= " ON {$wpdb->prefix}migla_donation.id = ";
                $sql .= " {$wpdb->prefix}migla_donation_meta.donation_id ";
                //Time Period
                if( !empty($time) && !empty($period) )
                {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= "TIMESTAMPDIFF(".$period.", `date_created`, NOW() ) <= " ;
                    $sql .= $time;
                    $params_count++;
                }
                
                //Type of Donation
                if( $donation_type == "1")
                {
                      if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway != 'Offline'";  
                    
                }else if( $donation_type == "2")
                {
                     if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway = 'Offline'";  
                    
                }else{
                }
                
                if( !empty($order) && !empty($orderBy) ){
                    $sql .= " ORDER BY " . $orderBy . " ".$order;
                }
                
                //Number of Rec
                if( !empty($limit) && $limit > 0 ){
                    $sql .= " limit 0," . $limit;
                }       
                
            }

            $result = array();
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            return $result;
		}
		
		public function get_recent_donation_by_timediff( $year,
		                                    $month,
		                                    $exactdate,
		                                    $complete,
		                                    $donation_type,
		                                    $campaign,
		                                    $order,
		                                    $orderBy,
		                                    $status
		)
		{
            global $wpdb;

            $params_count = 0;
            
                $sql = "SELECT * FROM {$wpdb->prefix}migla_donation";

                if( !empty($status) )
                {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }

                    $sql .= " status = " . $status;
                    $params_count++;
                }
                
                //Time Period
                if( !empty($year) )
                {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    if( !empty($month) )
                    {
                        $sql .= "`date_created` like '".$year."-".$month."%'";
                    }else{
                        $sql .= "`date_created` like '".$year. "%'";
                    }
                    
                    $params_count++;
                    
                }else if( !empty($exactdate) )
                {
                     if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= "`date_created` like '".$exactdate."%'";

                    $params_count++;
                }
                
                //Type of Donation
                if( $donation_type == "1")
                {
                      if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway != 'Offline'";  
                    
                }else if( $donation_type == "2")
                {
                     if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
                    
                    $sql .= " gateway = 'Offline'";  
                    
                }else{
                }
                
                if( !empty($order) && !empty($orderBy) ){
                    $sql .= " ORDER BY " . $orderBy . " ".$order;
                }
                
                //Number of Rec
                if( !empty($limit) && $limit > 0 ){
                    $sql .= " limit 0," . $limit;
                }

            $result = array();
            $result = $wpdb->get_results($sql, ARRAY_A);
            
            return $result;
		}		
		
		public function get_donation_bydate( $startDate, 
		                                    $endDate,
		                                    $limit,
		                                    $donation_type,
		                                    $order,
		                                    $orderBy,
		                                    $status
		)
		{
            global $wpdb;

            $params_count = 0;

            $sql1 = "SELECT * FROM {$wpdb->prefix}migla_donation";

            $sql2 = "SELECT * FROM {$wpdb->prefix}migla_donation";
            $sql2 .= " INNER JOIN {$wpdb->prefix}migla_donation_meta";
            $sql2 .= " ON {$wpdb->prefix}migla_donation.id = ";
            $sql2 .= " {$wpdb->prefix}migla_donation_meta.donation_id ";

            if( $startDate == $endDate ){
                $sql1 .= " WHERE `date_created` like '" . $startDate ."%'";                
                $params_count++;
            }else            
            if( !empty($startDate) && !empty($endDate) )
            {
                $sql1 .= " WHERE date(date_created) >= date('" . $startDate ."')";
                $sql1 .= " AND date(date_created) <= date('" . $endDate ."')";
                
                $params_count++;
                
            }else if( !empty($startDate) && empty($endDate) )
            {
                $sql1 .= " WHERE date(date_created) >= date('" . $startDate ."')";
                
                $params_count++;
                
            }else if( empty($startDate) && !empty($endDate) )
            {
                $sql1 .= " WHERE date(date_created) <= date('" . $endDate ."')";
                $params_count++;
            }
            
            if($donation_type =='1')
            {
                if($params_count > 0){
                    $sql1 .= " AND gateway NOT LIKE '%Offline'";
                }else{
                    $sql1 .= " WHERE gateway NOT LIKE '%Offline'";  
                }

                $params_count++;
            }else if($donation_type =='2')
            {
                if($params_count > 0){
                    $sql1 .= " AND gateway = 'Offline'";
                }else{
                    $sql1 .= " WHERE gateway = 'Offline'";  
                }               

                $params_count++;
            }else if($donation_type =='3')
            {
                if($params_count > 0){
                    $sql1 .= " AND (gateway = 'Offline' OR gateway = 'Pending-Offline' )";
                }else{
                    $sql1 .= " WHERE (gateway = 'Offline' OR gateway = 'Pending-Offline' )";  
                }               

                $params_count++;
            }
            
            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql1 .= " AND ";
                    }else{
                        $sql1 .= " WHERE ";
                    }

                    $sql1 .= " status = " . $status;
                    $params_count++;
            }
            
            if(!empty($orderBy)){
                $sql1 .= " ORDER BY " . $orderBy . " " . $order;
            }

            $result = array();
            $result = $wpdb->get_results($sql1, ARRAY_A);
            
            return $result;
		}		
		
        public function get_donation_bydate2( $startDate, 
                                            $endDate,
                                            $limit,
                                            $donation_type,
                                            $order,
                                            $orderBy,
                                            $status,
                                            $isRepeat
        )
        {
            global $wpdb;

            $params_count = 0;

            $sql1 = "SELECT * FROM {$wpdb->prefix}migla_donation";

            $sql2 = "SELECT * FROM {$wpdb->prefix}migla_donation";
            $sql2 .= " INNER JOIN {$wpdb->prefix}migla_donation_meta";
            $sql2 .= " ON {$wpdb->prefix}migla_donation.id = ";
            $sql2 .= " {$wpdb->prefix}migla_donation_meta.donation_id ";

            if( $startDate == $endDate ){
                $sql1 .= " WHERE `date_created` like '" . $startDate ."%'";                
                $params_count++;
            }else            
            if( !empty($startDate) && !empty($endDate) )
            {
                $sql1 .= " WHERE `date_created` >= '" . $startDate ."'";
                $sql1 .= " AND `date_created` <= '" . $endDate ."'";
                
                $params_count++;
                
            }else if( !empty($startDate) && empty($endDate) )
            {
                $sql1 .= " WHERE `date_created` >= '" . $startDate ."'";
                
                $params_count++;
                
            }else if( empty($startDate) && !empty($endDate) )
            {
                $sql1 .= " WHERE `date_created` <= '" . $endDate ."'";
                $params_count++;
            }
            
            if($params_count > 0){
                    $sql1 .= " AND repeating = '".$isRepeat."'";
            }else{
                    $sql1 .= " WHERE repeating = '".$isRepeat."'";
            }

            if($donation_type =='1')
            {
                if($params_count > 0){
                    $sql1 .= " AND gateway != 'Offline'";
                }else{
                    $sql1 .= " WHERE gateway != 'Offline'";  
                }

                $params_count++;
            }else if($donation_type =='2')
            {
                if($params_count > 0){
                    $sql1 .= " AND gateway = 'Offline'";
                }else{
                    $sql1 .= " WHERE gateway = 'Offline'";  
                }               

                $params_count++;
            }
            
            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql1 .= " AND ";
                    }else{
                        $sql1 .= " WHERE ";
                    }

                    $sql1 .= " status = " . $status;
                    $params_count++;
            }
            
            if(!empty($orderBy)){
                $sql1 .= " ORDER BY " . $orderBy . " " . $order;
            }

            $result = array();
            $result = $wpdb->get_results($sql1, ARRAY_A);
            
            return $result;
        }   

		public function get_donation_detail_bydate( $startDate, 
		                                    $endDate,
		                                    $limit,
		                                    $donation_type,
		                                    $order,
		                                    $orderBy,
		                                    $status,
		                                    $squared
		)
		{
            global $wpdb;

            $params_count = 0;

            $sql1 = "SELECT * FROM {$wpdb->prefix}migla_donation";

            if( $startDate == $endDate ){
                $sql1 .= " WHERE date_created like '" . $startDate ."%'";                
                $params_count++;
            }else            
            if( !empty($startDate) && !empty($endDate) )
            {
                $sql1 .= " WHERE date(date_created) >= date('" . $startDate ."')";
                $sql1 .= " AND date(date_created) <= date('" . $endDate ."')";
                
                $params_count++;
                
            }else if( !empty($startDate) && empty($endDate) )
            {
                $sql1 .= " WHERE date(date_created) >= date('" . $startDate ."')";
                
                $params_count++;
                
            }else if( empty($startDate) && !empty($endDate) )
            {
                $sql1 .= " WHERE date(date_created) <= date('" . $endDate ."')";
                $params_count++;
            }
            
            if($donation_type =='1')
            {
                if($params_count > 0){
                    $sql1 .= " AND gateway != 'Offline'";
                }else{
                    $sql1 .= " WHERE gateway != 'Offline'";  
                }

                $params_count++;
            }else if($donation_type =='2')
            {
                if($params_count > 0){
                    $sql1 .= " AND gateway = 'Offline'";
                }else{
                    $sql1 .= " WHERE gateway = 'Offline'";  
                }               

                $params_count++;
            }
            
            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql1 .= " AND ";
                    }else{
                        $sql1 .= " WHERE ";
                    }

                    $sql1 .= " status = " . $status;
                    $params_count++;
            }
            
            if( !empty($orderBy) ){
                    $sql1 .= " ORDER BY " . $orderBy . " ". $order;
            }

            $result = array();
            $result = $wpdb->get_results($sql1, ARRAY_A);
            
            
            $data = array();
            $headers = array();
            $i = 0;
            $n_row = 0;

            if( $squared == 1 ){
                
                if( !empty($result) ){
                
                    $tempData = array();
                
                    foreach( $result as $rs ){
                        $id = $rs['id'];
                        $tempData = $this->get_detail( $id, $status);
                        
                        $keys = array_keys( $tempData );
                        
                        $headers = array_merge($headers, $keys);
                        
                        $data[$n_row] = $tempData;
                        $n_row++;
                    }

                }
            }else{
                if( !empty($result) ){
                    foreach( $result as $rs ){
                        $id = $rs['id'];
                        $data[$n_row] = $this->get_detail( $id, $status);
                        $n_row++;
                    }
                }
            }
            
            return $data;
		}
		
		public function get_total_donation( $time,  $period, $status )
		{
		    global $wpdb;
            
            $total = 0.0;
		      
		      $sql = "SELECT SUM(amount) FROM {$wpdb->prefix}migla_donation";
		      $sql .= " WHERE ";
		      $sql .= "TIMESTAMPDIFF(".$period.", `date_created`, NOW() ) <= " ;
		      $sql .= $period;

            if( !empty($status) )
            {
                $sql .= " AND ";
                $sql .= " status = " . $status;
            }

            $total = $wpdb->get_var($sql);

            return $total;
		}
		
		
		public function get_total_donation_by_date( $year, 
		                                            $month, 
		                                            $day, 
		                                            $donationType, 
		                                            $status )
		{
		      global $wpdb;

              $params_count = 0;
		      
		      $sql = "SELECT SUM(amount) FROM {$wpdb->prefix}migla_donation";
	
		      if( empty($year) && empty($month) && empty($day) )
		      {
    		  }else{
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
    		          
                if( empty($day) ){
                    $sql .= " `date_created`";

                    if( !empty($year) )
                    {
                        if( !empty($month) ){
                            $sql .= " like '".$year."-".$month."-%'";
                        }else{
                            $sql .= " like '".$year."-%'";
                        }
                    }else{
                        $sql .= " like '%-".$month."-%'";
                    }
                }else{
                    if( !empty($year) )
                    {
                        if( !empty($month) ){
                            $sql .= " like '".$year."-%-".$day."'";
                        }else{
                            $sql .= " like '".$year."-".$month."-".$date."'";
                        }
                    }else{

                        $sql .= "TIMESTAMPDIFF( DAY, `date_created`, NOW() ) <= " ;
                        $sql .= $day;
                    }
                }                
        		
        	    $params_count++;
    		  }
    		  
    		    if( $donationType == '1' )
    		    {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
    		      
    		        $sql .= " gateway != 'Offline'";
    		        
    		        $params_count++;
    		    
    		    }else if( $donationType == '2' )
    		    {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }
    		        $sql .= " gateway = 'Offline'";
    		        
    		        $params_count++;
    		    }
		      
            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }

                    $sql .= " status = " . $status;
                    $params_count++;
            }

		      $amt = $wpdb->get_var($sql);
		      
		      return $amt;
		}

		public function get_total_bydate( $startDate, 
		                                    $endDate,
		                                    $exactDate,
		                                    $donation_type,
		                                    $order,
		                                    $orderBy,
		                                    $status
		)
		{
            global $wpdb;

            $params_count = 0;

            $sql = "SELECT sum(amount) FROM {$wpdb->prefix}migla_donation";

            if(!empty($exactDate))
            {
                $sql .= " WHERE `date_created` like '" . $exactDate ." %'";
                
                $params_count++;
                 
            }else if( empty($startDate) && empty($endDate) )
            {
                $sql .= " WHERE `date_created` >= '" . $startDate ."'";
                $sql .= " AND `date_created` <= '" . $endDate ."'";
                
                $params_count++;
                
            }else if( !empty($startDate) && empty($endDate) )
            {
                $sql .= " WHERE `date_created` >= '" . $startDate ."'";
                
                $params_count++;
                
            }else if( empty($startDate) && !empty($endDate) )
            {
                $sql .= " WHERE `date_created` <= '" . $endDate ."'";
                $params_count++;
            }
            
            if($donation_type =='1')
            {
                if($params_count > 0){
                    $sql .= " AND gateway != 'Offline'";
                }else{
                    $sql .= " WHERE gateway != 'Offline'";  
                }

                $params_count++;

            }else if($donation_type =='2')
            {
                if($params_count > 0){
                    $sql .= " AND gateway = 'Offline'";
                }else{
                    $sql .= " WHERE gateway = 'Offline'";  
                }               

                $params_count++;
            }
            
            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }

                    $sql .= " status = " . $status;
            }

            $result = array();
            $amount = $wpdb->get_var($sql);
            
            if(empty( $amount ))  $amount = 0.0;
            
            return $amount;
		}	

		public function get_total_donation_by_campaign( $year, $month, $day, $cmp, $status )
		{
		      global $wpdb;

              $params_count = 0;
		      
		      $sql = "SELECT SUM(amount) FROM {$wpdb->prefix}migla_donation";
	
		      if( empty($year) && empty($month) && empty($day) )
		      {
		         $sql .= " WHERE `campaign` = " . $cmp;   

                 $params_count++;
    		  }else{
    		      
    		      $sql .= " WHERE `date_created`";
    		      
    		      if( empty($day) ){
    		          $sql .= " like '".$year."-".$month."%'";
    		      }else{
    		          $sql .= " like '".$year."-".$month."-".$day."%'";
    		      }
    		      
    		      $sql .= " AND `campaign` = " . $cmp;   

                  $params_count++;
		      }

            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }

                    $sql .= " status = " . $status;
                    $params_count++;
            }              
		      
		      $amt = $wpdb->get_var($sql);
		      
		      return $amt;
		}

		public function get_count_donation_by_campaign( $year, $month, $day, $cmp, $status )
		{
		      global $wpdb;

              $params_count = 0;
		      
		      $sql = "SELECT count(*) FROM {$wpdb->prefix}migla_donation";
	
		      if( empty($year) && empty($month) && empty($day) )
		      {
		         $sql .= " WHERE `campaign` = " . $cmp;  
                 $params_count++; 
    		  }else{
    		      
    		      $sql .= " WHERE `date_created`";
    		      
    		      if( empty($day) ){
    		          $sql .= " like '".$year."-".$month."%'";
    		      }else{
    		          $sql .= " like '".$year."-".$month."-".$day."%'";
    		      }
    		      
    		      $sql .= " AND `campaign` = " . $cmp; 
                  $params_count++;  
		      }
		      
            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }

                    $sql .= " status = " . $status;
                    $params_count++;
            } 

		      $amt = $wpdb->get_var($sql);
		      
		      return $amt;
		}
		
        public function get_column( $id, $colName )
        {
              global $wpdb;
              
              $sql = "SELECT ".$colName." FROM {$wpdb->prefix}migla_donation";
              $sql .= " WHERE id = %d";

              $col = $wpdb->get_var($wpdb->prepare(
                        $sql, $id )
                    );

            return $col;
        } 
        
        public function get_column_by_session( $session_id, $colName )
        {
            global $wpdb;
              
            $sql = "SELECT ".$colName." FROM {$wpdb->prefix}migla_donation";
            $sql .= " WHERE session_id = %s";
            $sql .= " AND status = 1";
            $sql .= " ORDER BY date_created ASC";
            
            $col = $wpdb->get_var($wpdb->prepare(
                        $sql, $session_id )
                    );

            return $col;
        }            
		
        public function get_column_by_other_column( $colName, 
                                                    $colVal, 
                                                    $coltype,
                                                    $SearchName )
        {
            global $wpdb;
              
            $sql = "SELECT ". $SearchName ." FROM {$wpdb->prefix}migla_donation";
            $sql .= " WHERE ". $colName ." = " . $coltype;
            $sql .= " AND status = 1";
            $sql .= " ORDER BY date_created ASC";
            
            $col = $wpdb->get_var($wpdb->prepare(
                        $sql, $colVal )
                    );

            return $col;
        }   
         
        public function get_list_by_metadata( $meta_key, $meta_value )
        {
            global $wpdb;
              
            $sql = "SELECT * FROM {$wpdb->prefix}migla_donation_meta";
            $sql .= " WHERE meta_key = %s ";
            $sql .= " AND meta_value = %s ";
            $sql .= " ORDER by donation_id ASC";

            $data = $wpdb->get_results($wpdb->prepare(
                        $sql, $meta_key, $meta_value ), ARRAY_A
                    );

            return $data;
        } 
		
        public function get_list_by_metadata2( $meta_key, $meta_value )
        {
            global $wpdb;
              
            $sql = "SELECT * FROM {$wpdb->prefix}migla_donation_meta";
            $sql .= " WHERE meta_key = %s ";
            $sql .= " AND meta_value = %s ";
            $sql .= " ORDER by donation_id DESC";

            $data = $wpdb->get_results($wpdb->prepare(
                        $sql, $meta_key, $meta_value ), ARRAY_A
                    );

            return $data;
        } 		
		
        public function get_detail( $id, $status )
        {

            global $wpdb;

            $params_count = 0;
            $donation = array();
            $meta = array();
            $data = array();

            $sql1 = "SELECT * FROM {$wpdb->prefix}migla_donation";
            $sql1 .= " WHERE id = %d";

            $params_count++;

            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql1 .= " AND ";
                    }else{
                        $sql1 .= " WHERE ";
                    }

                    $sql1 .= " status = " . $status;
                    $params_count++;
            }             

            $donation = $wpdb->get_results( $wpdb->prepare(
                            $sql1, $id 
                        ), ARRAY_A);

            if(!empty($donation)){
                foreach($donation as $row){
                    foreach((array)$row as $col => $val){
                        $data[$col] = $val;
                    }   
                }
            }

            $sql2 = "SELECT meta_key, meta_value FROM {$wpdb->prefix}migla_donation";
            $sql2 .= " INNER JOIN {$wpdb->prefix}migla_donation_meta";
            $sql2 .= " ON {$wpdb->prefix}migla_donation.id = ";
            $sql2 .= " {$wpdb->prefix}migla_donation_meta.donation_id ";
            $sql2 .= " WHERE {$wpdb->prefix}migla_donation.id = %d";

            $meta = $wpdb->get_results( $wpdb->prepare(
                            $sql2, $id 
                        ), ARRAY_A);

            if(!empty($meta)){
                foreach($meta as $row){
                    $data[($row['meta_key'])] = $row['meta_value'];
                }
            }

            return $data;
        }
        
        public function get_top_donor( $array_group, $array_col, $numrec, $status )
        {
            global $wpdb;
            
            $params_count = 0;

            $sql = "select ";
            
            $col = "";
            
            if( !empty($array_col) ){
                foreach( $array_col as $colName ){
                    $col .= $colName.",";
                }
            }
            
            $col .= " SUM(`amount`) as total";

            $group = "";
            
            if( !empty($array_group) ){
                foreach( $array_group as $name ){
                    if( !empty($group) ) $group .= " , ";
                    $group .= $name ;
                }
            }
            
            $sql .= $col . " FROM {$wpdb->prefix}migla_donation ";
            $sql .= " WHERE `anonymous` != 'yes'";

            $params_count++;

            if( !empty($status) )
            {
                    if( $params_count > 0 ){
                        $sql .= " AND ";
                    }else{
                        $sql .= " WHERE ";
                    }

                    $sql .= " status = " . $status;
                    $params_count++;
            } 
            

            $sql .= " GROUP BY ". $group;
            $sql .= " ORDER BY `total` DESC";
            $sql .= " LIMIT 0,". $numrec;


            $res = $wpdb->get_results($sql, ARRAY_A);
            
            return $res;
            
        }
        
        public function get_forms_has_donation()
        {
            global $wpdb;
              
            $sql = "SELECT meta_value FROM {$wpdb->prefix}migla_donation_meta";
            $sql .= " WHERE meta_key = %s";

            $data = array();

            $data = $wpdb->get_results($wpdb->prepare(
                        $sql, 'miglad_form_id' ), ARRAY_A
                    );
                    
            $forms = array();
            $i = 0;
                    
            foreach( $data as $row ){
                if(!in_array($row['meta_value'], $forms)){
                    $forms[$i] = $row['meta_value'];
                    $i++;
                }
            }

            return $forms;
        } 
        
        public function check_donationmeta( $key, $donation_id, $value )
		{
			global $wpdb;

			$sql  = "SELECT id FROM {$wpdb->prefix}migla_donation_meta";
			$sql .= " WHERE meta_key = %s";
			$sql .= " AND meta_value = %s";

            if( !empty($donation_id) ){
                $sql .= " AND donation_id = %d";
            }

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $key, $value, $donation_id ) );

			return $id;
		}
		
        public function add_ongoing_recurring( $init_id, $trans_id )
        {
            $data = $this->get_detail( $init_id, '' );
            $new_id = '';
            
            if( !empty($data) )
            {
                $email = '';
                $firstname = ''; 
				$lastname = ''; 
				$amount = ''; 
				$campaign = ''; 
				$country = ''; 
				$anon = ''; 
				$repeat = ''; 
				$mailist = ''; 
				$gateway = ''; 
									
                if(isset($data['email'])) $email = $data['email'];
                if(isset($data['firstname'])) $firstname = $data['firstname'];
                if(isset($data['lastname'])) $lastname = $data['lastname'];
                if(isset($data['amount'])) $amount = $data['amount'];
                if(isset($data['campaign'])) $campaign = $data['campaign'];
                if(isset($data['country'])) $country = $data['country'];
                if(isset($data['anonymous'])) $anon = $data['anonymous'];
                if(isset($data['repeating'])) $repeat = $data['repeating'];
                if(isset($data['mailist'])) $mailist = $data['mailist'];
                if(isset($data['gateway'])) $gateway = $data['gateway'];
                
                $new_id = $this->create_donation( $email, 
									$firstname, 
									$lastname,
									$amount,
									$campaign,
									$country,
									$anon,
									$repeat,
									$mailist,
									$gateway,
									''
									);
					
				$not_duplicate = array(  
				                    'id',
				                    'status',
				                    'email' ,
                                    'firstname' , 
                    				'lastname' , 
                    				'amount' , 
                    				'campaign' , 
                    				'country' , 
                    				'anonymous' , 
                    				'repeating' , 
                    				'mailist' , 
                    				'gateway' ,
                    				'date_created',
                    				'gmt',
                    				'session_id',
                    				'timestamp',
                    				'miglad_transactionId',
                    				'miglad_date',
                    				'miglad_time',
                    				'miglad_session_id_',
                    				'miglad_session_id'
        				    );	
									
				foreach($data as  $metakey =>  $metaval)
				{	
				    if( !in_array(  $metakey, $not_duplicate ) )
				    {
		                $this->create_donation_meta( $new_id,
                		                             $metakey,
                		                             $metaval
                									);	
				    }
				}//for
				
				$this->create_donation_meta( $new_id,
                		                     'miglad_transactionId',
                		                      $trans_id
                						);	
            }
            
            return $new_id;
        }
        
        public function get_wc_total()
        {
            global $wpdb;
            
            $sql = "SELECT sum(meta_value) FROM ".$wpdb->prefix."postmeta inner join ".$wpdb->prefix."posts ";
            $sql .= " on ID = post_id";
            $sql .= " where post_status in ( 'wc-processing', 'wc-completed' ) and meta_key = '_order_total'";
            $sql .= " and post_date >= '2019-07-01'";
            
            $total = $wpdb->get_var(  $sql );
            
            return $total;
        }
        
        public function get_wc_total_startfrom( $sdate )
        {
            global $wpdb;
            
            $sql = "SELECT sum(meta_value) FROM ".$wpdb->prefix."postmeta inner join ".$wpdb->prefix."posts ";
            $sql .= " on ID = post_id";
            $sql .= " where post_status in ( 'wc-processing', 'wc-completed' ) and meta_key = '_order_total'";
            $sql .= " and post_date >= %s";
            
            $total = $wpdb->get_var( $wpdb->prepare( $sql, $sdate) );
            
            return $total;
        }        
        
        public function get_wc_total_this_month()
        {
            global $wpdb;
            
            $sql = "SELECT sum(meta_value) FROM ".$wpdb->prefix."postmeta inner join ".$wpdb->prefix."posts ";
            $sql .= " on ID = post_id";
            $sql .= " where post_status in ( 'wc-processing', 'wc-completed' ) and meta_key = '_order_total'";
            $sql .= " and post_date like '" . date('Y-m') . "%'";
            
            $total = $wpdb->get_var(  $sql );
            
            return $total;
        }        
		
	}//END OF CLASS
}