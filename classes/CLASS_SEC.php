<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_SEC' ) )
{
    class MIGLA_SEC
    {
		protected function create_token( $page_name, $session )
		{
			if( !isset($_SESSION[$session]) || empty($_SESSION[$session]) )
			{
				if( !isset($_SESSION[$session][$page_name])
					|| empty($_SESSION[$session][$page_name]) )
				{
					$_SESSION[$session][$page_name] = $this->random_string(32);
				}else{

				}
			}else{
				if( !isset($_SESSION[$session][$page_name])
					|| empty($_SESSION[$session][$page_name]) )
				{
					$_SESSION[$session][$page_name] = $this->random_string(32);
				}else{

				}
			}

			return $_SESSION[$session][$page_name];

		}

		protected function session_logout()
		{
		    if (session_status() == PHP_SESSION_ACTIVE)
		    {
		        if(isset($_SESSION[(session_id())]))
		        {
		            unset($_SESSION[(session_id())]);
		        }
		    }
		}

		protected function write_token($page_name, $session)
		{
		?>
			<input type="hidden" id="__migla_auth_token" value=<?php echo $this->create_token( $page_name, $session )?>>
		<?php
		}

		protected function write_token_owner($page_name)
		{
		?>
			<input type="hidden" id="__migla_auth_owner" value=<?php echo $page_name ;?>>
		<?php
		}

		protected function write_session_id()
		{
		?>
			<input type="hidden" id="__migla_session" value=<?php echo session_id(); ?>>
		<?php
		}

		protected function write_credentials($page_name, $session)
		{
			$this->write_token($page_name, $session);
			$this->write_token_owner($page_name);
			$this->write_session_id();
		}

		public function is_this_the_owner($page_name, $token, $session)
		{
			if( !isset($_SESSION[$session][$page_name])
					|| empty($_SESSION[$session][$page_name]) )
			{
				return false;
			}else{
				if( $_SESSION[$session][$page_name] == $token && session_id() == $session )
				{
				    return true;
				}else{
					return false;
				}
			}
		}

		public function is_option_available($key)
		{
			return ( in_array( $key, $this->available_backend_options() ) );
		}

		private function available_backend_options()
		{
			$list = array(
				//authorize
				'migla_authorize_api_key',	//t
				'migla_authorize_trans_key', //t
				'migla_payment_authorize',	 //t

				'miglaAuthorizeButtonChoice',//t
				'migla_authorizebuttonurl',	 //t
				'migla_authorizecssbtnstyle',//t
				'migla_authorizecssbtntext', //t
				'migla_authorizecssbtnclass',//t

				'migla_wait_authorize',      //t
				'migla_campaign_order',

				//campaign

				//theme
				'migla_circle_settings', //ar
				'migla_circle_textalign', //t
				'migla_circle_text1', //t
				'migla_circle_text2', //t
				'migla_circle_text3', //t
				
				'migla_2ndbgcolor', //t
				'migla_2ndbgcolorb', //t
				'migla_bglevelcolor', //t
				'migla_borderlevelcolor', //t
				'migla_borderlevel', //t
				'migla_bglevelcoloractive', //t
				'migla_tabcolor', //t
				'migla_borderRadius', //t
				'migla_wellboxshadow', //t
				'migla_progbar_info', //t
				'migla_bar_color',  //t
				'migla_progressbar_background', //t
				'migla_bar_style_effect', //Ar

				//Form
				"migla_none_rec_radiobtn_text", //t
				'migla_thousandSep', //t
				'migla_decimalSep',  //t
				'migla_curplacement',  //t
				'migla_showDecimalSep', //t
				'migla_default_currency', //t
				'migla_default_country',  //t
				'migla_hideUndesignated', //t
				'migla_show_bar',  //t
				'migla_showed_campaign_multiform', //Ar
				'migla_constantcontact_list1' , //Ar
				'migla_constantcontact_list2' , //Ar
				'migla_mailchimp_list', //Ar
				'migla_constantcontact_apikey' ,
				'migla_constantcontact_token',
				'migla_mailchimp_apikey' ,

				//Captcha, security
				'migla_gateways_order',  //Ar
				'migla_captcha_secret_key',
				'migla_captcha_site_key',
				'migla_use_captcha',
				'migla_credit_card_avs',
				'migla_avs_level',
				'migla_credit_card_validator',
				'migla_verifySSL',
				'migla_gateways_order',
				'migla_listen',

				'migla_undesignLabel',

				//paypal
				'migla_paypal_method',
				'migla_paypal_emails',
				'migla_paypal_fec',
				'migla_paypal_payment',

				'miglaPaypalButtonChoice',
				'migla_paypalbuttonurl',
				'migla_paypalbutton',
				'migla_paypalcssbtnstyle',
				'migla_paypalcssbtntext',
				'migla_paypalcssbtnclass',

				'migla_paypalpro_username',
				'migla_paypalpro_password',
				'migla_paypal_pro_type',
				'migla_paypalpro_recurring',
				
				'migla_express_checkout_listener',
				
				'migla_paypalpro_signature',
				'migla_paypalflow_vendor',
				'migla_paypalflow_user',
				'migla_paypalflow_password',
				'migla_paypal_pro_type',
				'migla_paypalflow_partner',
				'migla_paypalpro_cc_info',
				
				'migla_ipn_chatback',
				
				'migla_paypalitem',
				'migla_paymentcmd',

				//Strupe
				'migla_liveSK',
				'migla_livePK',
				'migla_testSK',
				'migla_testPK',
				'migla_stripemode',
        		'migla_webhook_key',

				'miglaStripeButtonChoice',
				'migla_stripecssbtnstyle',
				'migla_stripecssbtnclass',
				'migla_stripebuttonurl',
                'migla_stripecssbtnclass',

				//Offlines

				//Receipt && Emails
				'migla_thank_you_page',
				'migla_disable_honoree_email',

		        'migla_use_PHPMailer',
		        'migla_smtp_host',
		        'migla_smtp_user',
		        'migla_smtp_password',
		        'migla_smtp_authenticated',
		        'migla_smtp_secure',
		        'migla_smtp_port',

				//Security Tehcnical
				'migla_use_nonce',
				'migla_delete_settings',
				'migla_allow_cors',
				'migla_script_load_js_pos',
				'migla_script_load_css_pos',

				'recent_donation_dashboard',

				'migla_mail_list_choice',
				'migla_constant_contact_list',

				'migla_allowed_capabilities',
				'migla_allowed_users',

				'recurring_plans',
				"migla_min_amount",
				"migla_symbol_to_show"
			);

			return $list;
		}

		public function get_user_capability()
		{
		    global $current_user;
		    $objO = new MIGLA_OPTION;

		    $curr_caps      	= $current_user->caps;
		    $curr_caps_key    	= array_keys($curr_caps);
		    $cur_is_allowed   	= false;
		    $allowed_cap_curr 	= 'administrator';
		    $ok_found     		= false;
		    $get_allowed_caps 	= (array)$objO->get_option( 'migla_allowed_capabilities' );
		    $list       		= (array)$objO->get_option('migla_allowed_users');

		    if( in_array( $current_user->ID , $list ) )
		    {
		      $cur_is_allowed = true;
		      for( $k = 0 ; $k < count($curr_caps_key) && !$ok_found ; $k++ )
		      {
		        if( $curr_caps[$curr_caps_key[$k]] == '1' || $curr_caps[$curr_caps_key[$k]] == true )
		        {
		          if( in_array( $curr_caps_key[$k] , $get_allowed_caps ) )
		          {
		            $allowed_cap_curr = $curr_caps_key[$k];
		            $ok_found = true;
		          }
		        }
		      }//for
		    }

		    return $allowed_cap_curr ;
		}

		public function authenticate_user()
		{
		  $objO = new MIGLA_OPTION;

		  $users      = (array)unserialize($objO->get_option('migla_allowed_users'));

		  $has_privilege  = in_array( get_current_user_id(), $users );

		  $pass = $has_privilege || current_user_can( 'manage_options' ) ;

		  return $pass;
		}

		public function page_list()
		{
		    $list = array("toplevel_page_migla_donation_menu_page",
		                  'migla_campaign_page',
		                  'migla_donation_form_options_page',
		                  'migla_donation_settings_page',
		                  'migla_offline_donations_page',
		                  'migla_reports_page',
		                  'migla_donation_custom_theme',
		                  'migla_donation_paypal_settings_page',
		                  'migla_offline_settings_page',
		                  'migla_stripe_setting_page',
		                  'migla_donation_authorize_settings_page',
		                  'migla_donation_system_status_page',
		                  'migla_translate_page',
		                  'migla_donation_front_settings_page'
		            );
		}

    	public function file_get_contents_curl( $url, $access_token )
    	{
            $args = array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $access_token,
                    ),
                );
                
            $response = wp_remote_get( $url, $args );
            $data = json_decode( wp_remote_retrieve_body($response), true );          
    
            return $data;
        }
    
        public function file_get_content( $path )
        {
            $cert_content = file_get_contents( $path );
            $res = openssl_x509_read( $cert_content );
            $data = openssl_x509_parse( $res );
    
            return $data;
        }
    
        public function is_serialized( $input )
        {
                $res = false;
    
                if( preg_match( '/^a:\d+:{.*?}$/', $input ) ){
                    $res = true;
                }
    
                return $res;
        }
    
        public function random_string($length)
        {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                return $randomString;
        }
    
        public function remove_array_by_key($array, $key_remove)
        {
              foreach($array as $key => $value)
              {
                if($key == $key_remove)
                    unset($array[$key]);
              }
    
              return $array;
        }
    
        public function get_current_url()
        {
          if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
              $http = 'https';
          }else{
              $http = 'http';
          }
    
          $currentUrl = $http . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
          return $currentUrl;
        }
    
        public function get_current_server_url()
        {
          if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
              $http = 'https';
          }else{
              $http = 'http';
          }
    
          $currentUrl = $http . '://' . $_SERVER['SERVER_NAME'];
    
          return $currentUrl;
        }
    
        public function redirect($url, $statusCode = 303)
        {
               header('Location: ' . $url, true, $statusCode);
               die();
        }

    }//class
}
?>