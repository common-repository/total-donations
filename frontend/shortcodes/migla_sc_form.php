<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class Migla_Shortcode
{
	static $add_script;

	static $pk;
	static $ajax_url;
	static $notifyurl;

    static function sanitize($str)
    {
            $str = @strip_tags($str);
            $str = @stripslashes($str);
            
            $invalid_characters = array("$", "%", "#", "<", ">", "|");
            $str = str_replace($invalid_characters, "", $str);
            
            return $str;
    }

	static function handle_shortcode( $atts )
	{
		$args = shortcode_atts(array( 'id' => 0 ),
						        $atts
		                       );

		self::$add_script = true;

		$isThank          = false;
		$content          = "";
		$get_id           = "";
		$message = "";

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

			add_action('wp_footer', array(__CLASS__, 'enqueue_jsscript_footer') );

			ob_start();

			$objC = new MIGLA_CAMPAIGN;

			if( $args['id'] == 0 || $args['id'] == '' || !isset($args['id']) )
			{
				$formid = 0;
			}else{
				$formid = $objC->get_column( $args['id'], 'form_id' );
			}

			$obj_form = new migla_form_creator( $formid,
			                                    session_id(),
			                                    $args['id']
			                                 );
			$obj_form->draw_form();

			return ob_get_clean();
		}
	}

	public static function init()
	{
		add_shortcode('totaldonations', array(__CLASS__, 'handle_shortcode'));

		add_action( 'wp_enqueue_scripts' , array(__CLASS__, 'enqueue_jsscript_footer' ) );
		
		add_action( 'wp_enqueue_scripts' , array(__CLASS__, 'enqueue_stripe_js') );
		
		add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_stylesheet'));
	}

	static function enqueue_stripe_js()
	{
      wp_enqueue_script( 'migla-stripe.js', 'https://js.stripe.com/v3/' );
	}

	static function enqueue_stylesheet()
	{
		if( is_rtl() )
		{
			wp_enqueue_style( 'migla-front-end', Totaldonations_DIR_URL . 'assets/css/migla-frontend.css' );
			wp_enqueue_style( 'migla-front-end-rtl', Totaldonations_DIR_URL . 'assets/css/migla-rtl.css');
		}else{
			wp_enqueue_style( 'migla-front-end', Totaldonations_DIR_URL . 'assets/css/migla-frontend.css' );
		}

		if( !wp_script_is( 'mg_progress-bar', 'registered' ) )
		{
			wp_register_style( 'mg_progress-bar', Totaldonations_DIR_URL.'assets/css/mg_progress-bar.css' , false, false );
		}

		if( !wp_script_is( 'mg_progress-bar', 'queue' ) )
		{
			wp_enqueue_style( 'mg_progress-bar' );
		}

        wp_enqueue_style( 'migla-fontawesome', Totaldonations_DIR_URL . 'assets/css/font-awesome/css/font-awesome.css' );
	}

	static function enqueue_jsscript_footer()
	{
		if ( !self::$add_script )
				return;

		$objO = new MIGLA_OPTION;

		self::$ajax_url =  admin_url( 'admin-ajax.php' );

		$gtw_order = $objO::st_get_option("migla_gateways_order");
        $is_stripe = false;
        $is_paypal = false;
        $is_authorize = false;

        if( !empty($gtw_order) ){
            $_gtw = (array)unserialize($gtw_order);

            foreach( $_gtw as $item ){

                if( $item[0] == "stripe" && $item[1] == "true" ){
                    $is_stripe = true;

                }else if( $item[0] == "paypal" && $item[1] == "true" ){
                    $is_paypal = true;

                }
            }

            if( $is_stripe )
            {
			    if( $objO::st_get_option("migla_stripemode") == "test" )
			    {
				    self::$pk = $objO::st_get_option('migla_testPK');
			    }else{
				    self::$pk = $objO::st_get_option('migla_livePK');
			    }
            }else{
                self::$pk = "-";
            }
        }

        $sessionID = session_id();

		$send_options = array();

		$send_options['ajaxurl'] = self::$ajax_url;

		$send_options['nonce'] = wp_create_nonce('migla-security-nonce');

		$send_options['sid'] = $sessionID;
		$send_options['lst'] = $objO->get_option('migla_listen');


		if($is_stripe){
		    $send_options['stripe_PK'] = self::$pk;
		}

		wp_enqueue_script( 'respond.min.js', Totaldonations_DIR_URL . 'assets/plugins/others/respond.min.js',false, true  );

		wp_enqueue_script( 'migla-checkout-js', Totaldonations_DIR_URL.'assets/js/checkout/migla_checkout.js',
        							array('jquery'),
        							false,
        							true
        					);

		wp_localize_script( 'migla-checkout-js',
			                    'miglaAdminAjax',
								$send_options
								);
								

		wp_enqueue_script( 'migla-boots-nav.js',
							Totaldonations_DIR_URL.'assets/plugins/bootstrap/boot-tabs.js', array('jquery'),
							false,
							true );

		wp_enqueue_script( 'migla-boots-tooltip.js',
							Totaldonations_DIR_URL.'assets/plugins/bootstrap/bootstrap_tooltip.js', array('jquery'),
							false,
							true );
    }

} //End of Migla_Shortcode Class

Migla_Shortcode::init();
?>