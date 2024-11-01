<?php
if ( !defined( 'ABSPATH' ) ) exit;

/* Get all ajax files for each gateway */
$dir = Totaldonations_DIR_PATH . 'gateways/*';  

foreach(glob($dir) as $file)  
{
    if( filetype($file) == 'dir' )
    {
        $gateway_dir = $file.'/*';
                
        foreach(glob( $gateway_dir ) as $file)  {  
            if( strpos( $file, 'migla_ajax' ) !== false  )
            {
                include_once $file;
            }
        }
    }
}

if (!function_exists('migla_getSK')){
    function migla_getSK()
    {
        $objO = new MIGLA_OPTION;
    
        if( $objO->get_option('migla_stripemode') == 'test' )
        {
            $SK = $objO->get_option('migla_testSK');
        }else{
            $SK = $objO->get_option('migla_liveSK');
        }
    
       return $SK;
    }
}

if (!function_exists('migla_getPK')){
    function migla_getPK()
    {
        $objO = new MIGLA_OPTION;
    
        if( $objO->get_option('migla_stripemode') == 'test' )
        {
            $PK = $objO->get_option('migla_testPK');
        }else{
            $PK = $objO->get_option('migla_livePK');
        }
    
      return $PK;
    }
}

if (!function_exists('migla_saving_donation')){
    function migla_saving_donation( $map,
                                    $trans_id,
                                    $paymentmethod,
                                    $paymentdata,
                                    $avs_response_text,
                                    $post_array,
                                    $trans_type,
                                    $gateway
                                  )
    {
    
        $data = migla_check_empty_donation_data( $map );
    
        $Donation_OBJ = new CLASS_MIGLA_DONATION;
    
        $isRepeat = 'no';
    
        $rec_id = $Donation_OBJ->create_donation( $data['miglad_email'],
    									$data['miglad_firstname'],
    									$data['miglad_lastname'],
    									$data['miglad_amount'],
    									$data['miglad_campaign'],
    									$data['miglad_country'],
    									$data['miglad_anonymous'],
    									$isRepeat,
    									$data['miglad_mg_add_to_milist'],
    									$gateway,
    									$data['miglad_session_id']
    									);
    
      	$data['miglad_paymentmethod'] = $paymentmethod;
      	$data['miglad_paymentdata'] = $paymentdata ;
      	$data['miglad_transactionId'] = $trans_id ;
      	$data['miglad_transactionType'] = $trans_type;
      	$data['miglad_avs_response_text'] = $avs_response_text ;
    
        foreach( $data as $key => $val )
        {
            if( is_array($val) )
            {
                $val = serialize($val);
            }
    
            $Donation_OBJ->create_donation_meta( $rec_id, $key, $val);
        }
    
        $objM = new MIGLA_MONEY;
        $Donation_OBJ->create_donation_meta( $rec_id, 'miglad_currency', $objM->get_default_currency() );
    
        return $rec_id;
    }
}

if (!function_exists('migla_check_empty_donation_data')){
    function migla_check_empty_donation_data( $map )
    {
        $obj = new CForm_Fields;
    
        $data = $obj->get_default_donation_fields();
    
        foreach( (array)$map as $row )
        {
            $data[($row[0])] = $row[1];
        }
    
        return $data;
    }
}

if (!function_exists('migla_sanitize_donor_info')){
    function migla_sanitize_donor_info($values)
    {
        $map = array();
        
        foreach((array)$values as $row)
        {
            if(isset($row[0]) && isset($row[1])){
                $temp = array();
                $temp[0] = sanitize_text_field($row[0]);
                $temp[1] = sanitize_text_field($row[1]);
            
                $map[] = $temp;
            }
        }
        
        return $map;
    }
}

if (!function_exists('migla_save_date')){
    function migla_save_date( $new_id )
    {
      $date_time_timezone = migla_date_timezone();
    
      add_post_meta( $new_id, 'miglad_timezone', $date_time_timezone['timezone'] );
      add_post_meta( $new_id, 'miglad_date', $date_time_timezone['date'] );
      add_post_meta( $new_id, 'miglad_time', $date_time_timezone['time'] );
    
    }
}

if (!function_exists('migla_date_timezone')){
    function migla_date_timezone()
    {
        $php_time_zone = date_default_timezone_get();
        $default    = get_option('migla_default_timezone');
    
        $time = "";
        $date = "";
        $timezone   = "";
    
        if( $default == 'Server Time' )
        {
    
            $gmt_offset = -get_option( 'gmt_offset' );
    
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
}

if (!function_exists('migla_trim_sql_xss')){
    function migla_trim_sql_xss( $string )
    {
        $safeout = str_replace("'"," ", $string );
        $safeout = htmlspecialchars( $safeout );
        $safeout = strip_tags( $safeout );
    
        return $safeout;
    }
}
?>