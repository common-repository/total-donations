<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Migla_Thank_You_Shortcode' ) )
{
    class Migla_Thank_You_Shortcode
    {
    	static $progressbar_script;

    	static function get_date()
    	{
                  $php_time_zone  = date_default_timezone_get();
                  $default        = get_option('migla_default_timezone');
                  $language       = get_option('migla_default_datelanguage');
                  $date_format    = get_option('migla_default_dateformat');

                  if( $language == false || $language == '' )
                      $language = 'en.UTF-8';

                  if( $date_format == false || $date_format == '' )
                      $date_format = '%B %d %Y' ;

                  setlocale(LC_TIME, $language );
                  $my_locale = get_locale();

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

                  }else{
                      date_default_timezone_set( $default );
                  }

                  $d = date('m')."/".date('d')."/".date('Y');

                  $date =  strftime( $date_format , date(strtotime($d)) ) ;

          return $date;

    	}

    	static function init()
    	{
      	    add_shortcode('totaldonations_thank_you_page', array(__CLASS__, 'handle_shortcode'));
    	}

        static function sanitize($str)
        {
            $str = @strip_tags($str);
            $str = @stripslashes($str);
            
            $invalid_characters = array("$", "%", "#", "<", ">", "|");
            $str = str_replace($invalid_characters, "", $str);
            
            return $str;
        }

    	static function handle_shortcode($atts)
    	{
    	    $message = "";

    	    ob_start();
    	    
    	    if(isset($_GET['gtw']) && isset($_GET['pid']) && isset($_GET['sid']) )
    	    {
    	        
        	    $gtw = self::sanitize( $_GET['gtw'] );
        	    $pid = self::sanitize( $_GET['pid'] );
        	    
        	    $sid = self::sanitize( $_GET['sid'] );
        	    
                $valid = false;

                $objD = new CLASS_MIGLA_DONATION;
                $data = array();

                $sid2 = session_id();
                $valid = $sid == $sid2;

                $myGtw = "";
                if( $gtw == 'p' ){
                    $myGtw = "PayPal";
                }else if( $gtw == 's' ){
                    $myGtw = "Stripe";
                }

                $message .= "Gateway " . $myGtw ." for Record ID " . $pid;

                if($valid)
                {
                    if( $gtw == 'p' ){
                        $data = $objD->get_detail( $pid, '' );
                    }else{
                        $data = $objD->get_detail( $pid, 1 );
                    }

                    if( isset($data['miglad_form_id']) && isset($data['miglad_language']) )
                    {
                        $objRd = new MIGLA_REDIRECT;

                        $rcd = $objRd->get_info( $data['miglad_form_id'], $data['miglad_language']);

                        if( isset($data['firstname']) )
                            $firstname = $data['firstname'];
                        else
                            $firstname = '';

                        if( isset($data['lastname']) )
                            $lastname = $data['lastname'];
                        else
                            $lastname = '';

                        if( isset($data['amount']) )
                            $amount = $data['amount'];
                        else
                            $amount = '';

                        if( isset($data['miglad_campaign_name']) )
                        {
                            $cmp = $data['miglad_campaign_name'];

                        }else if(isset($data['miglad_campaign']))
                        {
                            $objC = new MIGLA_CAMPAIGN;
                            $cmp = '';

                            if( $data['miglad_campaign'] == 0 )
                            {
                                $cmp = $objC->get_undesignated();
                            }else{
                                $cmp = $objC->get_info_by_campaign_id( $data['miglad_campaign'], $data['miglad_language'] );
                                $cmp = $cmp['name'];
                            }
                        }

                        $date = date(get_option('date_format'));

                        $objM = new MIGLA_MONEY;
                        $currency = $objM->get_default_currency();
                        $amount_format = $objM->full_format( $amount, 2);

            			$placeholder = array( '[firstname]',
                                        '[lastname]' ,
                                        '[amount]' ,
                                        '[date]' ,
                                        '[newline]',
                                        '[campaign]',
                                        '[currency]'
                                        );

            			$replace     = array( $firstname ,
                                        $lastname ,
                                        $amount_format[0].' '.$currency ,
                                        $date,
                                        '<br>',
                                        $cmp,
                                        $currency
                                        );

                        $trimquote = $rcd['content'];

                        $page =  str_replace($placeholder, $replace, $trimquote);
                        $page =  str_replace("\'", "'", $page);

    			        echo $page;

    			        $message .= ". " . $firstname . " " . $lastname . " donation goes to " . $cmp;
                    }
                }else{
                    $message = "Undefined session";
                }

                $objLog = new MIGLA_LOG('thankyou-page-');
                $objLog->append( date('[Y-m-d H:i e] '). " ". $message . "\n" );
        	    
    	    }else{
    	       $objO = new MIGLA_OPTION;
    	       
    	       if(isset($_GET['src_id']) && self::sanitize($_GET['src_id']) == $objO->get_option("migla_listen") )
    	       {
                    $objRd = new MIGLA_REDIRECT;
                    $rcd = $objRd->get_info( 0, get_locale());

                    $firstname = 'John';
                    $lastname = 'Doe';
                    $amount = '100';
                    $cmp = "Save The Earth";
                    $date = date(get_option('date_format'));

                    $objM = new MIGLA_MONEY;
                    $currency = $objM->get_default_currency();
                    $amount_format = $objM->full_format( $amount, 2);

            		$placeholder = array( '[firstname]',
                                        '[lastname]' ,
                                        '[amount]' ,
                                        '[date]' ,
                                        '[newline]',
                                        '[campaign]',
                                        '[currency]'
                                        );

            		$replace     = array( $firstname ,
                                        $lastname ,
                                        $amount_format[0].' '.$currency ,
                                        $date,
                                        '<br>',
                                        $cmp,
                                        $currency
                                        );

                    $trimquote = $rcd['content'];

                    $page =  str_replace($placeholder, $replace, $trimquote);
                    $page =  str_replace("\'", "'", $page);

    			    echo $page;    	           
    	       } 
    	    }

            return ob_get_clean();
    	}

    }//END OF CLASSES

    Migla_Thank_You_Shortcode::init();
}
?>