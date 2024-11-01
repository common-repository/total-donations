<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_TIME' ) )
{
	class MIGLA_TIME
	{
		public function migla_date_timezone()
		{
			$objO = new MIGLA_OPTION;

		    $php_time_zone = date_default_timezone_get();
		    $default    = $objO->get_option('migla_default_timezone');

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
}
?>