<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TotalDonations' ) )
{
	class TotalDonations
	{
	    static function init_path()
		{
			if( ! defined( 'Totaldonations_VERSION' ) )
				define( 'Totaldonations_VERSION', '3.0.8' );

			if( ! defined( 'Totaldonations_FREE' ) )
				define( 'Totaldonations_FREE', 'yes' );

			if( ! defined( 'Totaldonations_FILE' ) )
				define( 'Totaldonations_FILE', __FILE__ );

			if( ! defined( 'Totaldonations_DIR_URL' ) )
				define( 'Totaldonations_DIR_URL', plugin_dir_url( __FILE__ ) );

			if( ! defined( 'Totaldonations_DIR_PATH' ) )
				define( 'Totaldonations_DIR_PATH', plugin_dir_path( __FILE__ ) );

			if( ! defined( 'Totaldonations_PLUGIN_DIR' ) )
				define( 'Totaldonations_PLUGIN_DIR' , plugin_dir_url( __FILE__ )   );
		}

	    static function init()
	    {
	    	//Call Defined Path
	  		self::init_path();

	  		//get current language
	        $init_language = get_locale();

	        //Include all clases
	        include_once 'classes/CLASS_SEC.php';

			include_once 'classes/CLASS_TIME.php';
			include_once 'classes/CLASS_LOCAL.php';
			include_once 'classes/CLASS_GEOGRAPHY.php';
			include_once 'classes/CLASS_MONEY.php';
			include_once 'classes/CLASS_OPTIONS.php';
			include_once 'classes/CLASS_LOG.php';

			include_once 'classes/CLASS_FORM_FIELD.php';
			include_once 'classes/CLASS_FORM.php';
			include_once 'classes/CLASS_CAMPAIGN.php';
			include_once 'classes/CLASS_EMAIL.php';
			include_once 'classes/CLASS_REDIRECT.php';
			include_once 'classes/CLASS_DONATION.php';

			//Inlcude Ajax for Gateways
			include_once Totaldonations_DIR_PATH . 'ajax/migla_ajax_gateways_main.php';

			if(is_admin())
			{
				//If this is wp-admin
	                    include_once Totaldonations_DIR_PATH . 'ajax/migla_ajax.php';

		                include_once 'admin/admin-dashboard.php';
		                include_once 'admin/admin-campaign.php';
		                include_once 'admin/admin-reports.php';
		                include_once 'admin/admin-gateway-paypal.php';
		                include_once 'admin/admin-gateway-stripe.php';
		                include_once 'admin/admin-email.php';
						include_once 'admin/admin-form-options.php';
						include_once 'admin/admin-custom-theme.php';
						include_once 'admin/admin-security-frontend.php';
						include_once 'admin/admin-system-status.php';
						include_once 'admin/admin-help.php';
						include_once 'admin/admin-gotofeature.php';
						
	        	add_action( 'admin_enqueue_scripts', array( __CLASS__ , 'load_admin_scripts') );
	        	add_action('admin_notices', array( __CLASS__ , 'author_admin_notice') );	 

			}else{
				//If this is FrontEnd
				include_once Totaldonations_DIR_PATH . 'classes/CLASS_FRONTEND.php';

				include_once Totaldonations_DIR_PATH . 'frontend/shortcodes/migla_sc_form.php';
				include_once Totaldonations_DIR_PATH . 'frontend/shortcodes/migla_sc_progress_bar.php';
				include_once Totaldonations_DIR_PATH . 'frontend/shortcodes/migla_sc_circle.php';
				include_once Totaldonations_DIR_PATH . 'frontend/shortcodes/migla_sc_thankyou_page.php';
			}

			include_once Totaldonations_DIR_PATH . 'frontend/widgets/migla-bar-widget.php';
			include_once Totaldonations_DIR_PATH . 'frontend/widgets/migla-circle-widget.php';
	    }

		static function author_admin_notice(){
		    
			$objO = new MIGLA_OPTION;
      		$paypalemails = $objO->get_option('migla_paypal_emails');

      		$paypal_dismiss = $objO->get_option('migla_paypal_isdismiss');
      		$stripe_dismiss = $objO->get_option('migla_stripe_isdismiss');

      		if($paypal_dismiss=="yes")
      		{
      		}else{
	      		if( empty($paypalemails) ){
				    echo '<div class="notice notice-info migla_watermark migla_dismiss migla_paypal-dissmiss">
				        <p><strong>TotalDonations</strong> - Your PayPal account information hasn\'t been filled in yet. Please fill in the details <a href="'.get_admin_url().'admin.php?page=migla_donation_paypal_settings_page">here</a> to start collecting donations.</p>
				        <button type="button" class="migla_notice-dismiss migla_paypal-dismiss-btn"></button>
				        </div>';    			
	      		}
      		}

      		if($stripe_dismiss=="yes")
      		{
      		}else{
	      		$testSK = $objO->get_option('migla_testSK');
			    $testPK = $objO->get_option('migla_testPK');
			    $liveSK = $objO->get_option('migla_liveSK');
			    $livePK = $objO->get_option('migla_livePK');
			    $stripeMode = $objO->get_option('migla_stripemode');

			    if( $stripeMode == "test" ){
			    	if( empty($testSK) || empty($testPK) ){
						echo '<div class="notice notice-info migla_dismiss migla_watermark migla_stripe-dissmiss">
					        <p><strong>TotalDonations</strong> - Stripe is in \'test\' mode. Please set it to \'live\' mode in order to collect donations. Your Stripe account information hasn\'t been filled in yet. Please fill in the details <a href="'.get_admin_url().'admin.php?page=migla_stripe_setting_page">here</a> to start collecting donations.</p>
					        <button type="button" class="migla_notice-dismiss migla_stripe-dismiss-btn"></button>
					        </div>';  
			    	}else{
			    		echo '<div class="notice notice-info migla_dismiss migla_watermark migla_stripe-dissmiss">
					        <p><strong>TotalDonations</strong> - Stripe is in test mode. Please set it to live  mode in order to collect donations.</p>
					        <button type="button" class="migla_notice-dismiss migla_stripe-dismiss-btn"></button>
					        </div>';  
			    	}
			    }else{
					if( empty($liveSK) || empty($livePK) ){
						echo '<div class="notice notice-info migla_dismiss migla_watermark migla_stripe-dissmiss">
					        <p><strong>TotalDonations</strong> - Your Stripe account information hasn\'t been filled in yet. Please fill in the details <a href="'.get_admin_url().'admin.php?page=migla_stripe_setting_page">here</a> to start collecting donations.</p>
					        <button type="button" class="migla_notice-dismiss migla_stripe-dismiss-btn"></button>
					        </div>';  
			    	}
			    }//test mode

			}
		}

	    static function donation_active_trigger( $networkwide )
		{
			    global $wpdb;

			    if( function_exists('is_multisite') && is_multisite() )
				{
			        if ($networkwide)
					{
			            $old_blog 	= $wpdb->blogid;

			            $blogids 	= $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			            foreach ($blogids as $blog_id)
						{
			                switch_to_blog($blog_id);
			                self::donation_active();
			            }
			            switch_to_blog($old_blog);
			            return;
			        }
			    }

				self::donation_active();
		}

	    static function donation_active()
		{
			//create tables
	        self::tables_creation();

	        //include some classes for intialization variable and data
			include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_MONEY.php";
			include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_TIME.php";
			include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_GEOGRAPHY.php";
			include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_LOCAL.php";
		    include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_OPTIONS.php";

			include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_FORM_FIELD.php";
			include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_FORM.php";
			include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_EMAIL.php";
            include_once plugin_dir_path( __FILE__ ) . "classes/CLASS_REDIRECT.php";

		    $objL = new MIGLA_LOCAL;
			$objO = new MIGLA_OPTION;
			$objForm  = new CLASS_MIGLA_FORM;
			$objEmail = new MIGLA_EMAIL;
			$objRd    = new MIGLA_REDIRECT();

			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		    $password = substr( str_shuffle( $chars ), 0, 8 );

					$objO::init_sitecode( 'migla_listen', $password, 'text' );
					$languages = $objL::st_get_languages();
					$saved = array();

					foreach( $languages as $lang => $arrays )
					{
					    $saved[$lang] = "General Donation";
					}

					$objO::init( 'migla_undesignLabel', serialize($saved), 'text' );
					$objO::init( 'migla_hideUndesignated', 'no', 'text');

					//THEME SETTINGS
					$objO::init( 'migla_tabcolor', '#eeeeee', 'text' );
					$objO::init( 'migla_2ndbgcolor' , '#fafafa,1', 'text' );
					$objO::init( 'migla_2ndbgcolorb' , '#eeeeee,1,1', 'text' );
					$objO::init( 'migla_borderRadius' , '8,8,8,8', 'text' );

					$barinfo = "We have collected [total] of our [target] target. ";
					$barinfo .= "It is [percentage] of our goal for the [campaign] campaign";

					$objO::init( 'migla_progbar_info', $barinfo, 'text' );
					$objO::init( 'migla_bar_color' , '#428bca,1', 'text' );
					$objO::init( 'migla_progressbar_background', '#bec7d3,1', 'text' );
					$objO::init( 'migla_wellboxshadow', '#969899,1, 1,1,1,1', 'text' );

					$objO::init( 'migla_bglevelcoloractive', '#ba9cb5', 'text' );
					$objO::init( 'migla_bglevelcolor', '#eeeeee' , 'text' );
					$objO::init( 'migla_borderlevelcolor', '#b0b0b0', 'text' );
					$objO::init( 'migla_borderlevel', '1', 'text' );

					 $arr = array( 'Stripes' => 'yes',
					 				'Pulse' => 'yes',
					 				'AnimatedStripes' => 'yes',
					 				'Percentage' => 'yes' );

					$objO::init( 'migla_bar_style_effect' , $arr, 'array' );

			        $circle = array();
			        $circle['size'] = 250;
			        $circle['start_angle'] = 0; 
			        $circle['thickness'] = 20;
			        $circle['reverse'] = 'yes';
			        $circle['line_cap'] = 'round';
			        $circle['fill'] = '#428bca';
			        $circle['animation'] = 'back_forth';
			        $circle['inside'] = 'percentage';
			        $circle['inner_font_size'] = '32';
			        
			        $objO::init( 'migla_circle_settings', $circle, 'array' );

			        $objO::init( 'migla_circle_text1', 'Total', 'text' );
			        $objO::init( 'migla_circle_text2', 'Target', 'text' );
			        $objO::init( 'migla_circle_text3', 'Donor', 'text' );
			        $objO::init( 'migla_circle_textalign', 'left_right', 'text' );
        
					$objO::init( 'migla_default_country', 'United States', 'text' );;
					$objO::init( 'migla_default_currency' , 'USD', 'text' );
					$objO::init( 'migla_thousandSep' , ',', 'text' );;
					$objO::init( 'migla_decimalSep' , '.', 'text' );
					$objO::init( 'migla_curplacement' , 'before', 'text' );
					$objO::init( 'migla_showDecimalSep' , 'yes', 'text' );
					$objO::init( 'migla_symbol_to_show' , 'icon', 'text' );
					$objO::init( 'migla_min_amount' , '0', 'text' );

					$objO::init( 'migla_default_timezone', 'Server Time', 'text' );

					$objO::init( 'migla_avs_level', 'low', 'text' );
					$objO::init( 'migla_credit_card_avs', 'no', 'text' );
			   	    $objO::init( 'migla_credit_card_validator', 'no', 'text' );

					$objForm::init_form( true , get_locale() );
					$objEmail::init_email();

					$objL::set_origin_language( get_locale() , 'yes' );

	                $objO::init( 'migla_smtp_host', '', 'text' );
	                $objO::init( 'migla_smtp_user', '', 'text' );
	                $objO::init( 'migla_smtp_password', '', 'text' );
	                $objO::init( 'migla_smtp_authenticated', '', 'text' );
	                $objO::init( 'migla_smtp_secure', '', 'text' );
	                $objO::init( 'migla_smtp_port', '', 'text' );
	                $objO::init( 'migla_use_PHPMailer', 'no', 'text' );

	        //Gateways
	        $gtw_orders = array( array("paypal", true) );        
	        $objO::init( 'migla_gateways_order', serialize($gtw_orders), 'text' );        	

	        //PayPal Settings and Data
	        $objO::init( 'miglaPaypalButtonChoice', 'cssButton', 'text' );
	        $objO::init( 'migla_paypalbutton', 'en_US', 'text' );
	        $objO::init( 'migla_paypalcssbtnstyle', 'Grey', 'text' );
	        $objO::init( 'migla_paypalcssbtnclass', '', 'text' );
	        $objO::init( 'migla_paypalbuttonurl', '', 'text' );

	        $objO::init( 'migla_paypal_emails', '', 'text' );
	        $objO::init( 'migla_paypal_payment', 'sandbox', 'text' );

	        //Stripe Settings and Data             
			$objO::init( 'miglaStripeButtonChoice', 'Grey', 'text' );
			$objO::init( 'migla_stripebuttonurl', '', 'text' );
			$objO::init( 'migla_stripecssbtnstyle', '', 'text' );
			$objO::init( 'migla_stripecssbtnclass', '', 'text' );

		    $objO::init( 'migla_testSK', '', 'text' );
		    $objO::init( 'migla_testPK', '', 'text' );
		    $objO::init( 'migla_liveSK', '', 'text' );
		    $objO::init( 'migla_livePK', '', 'text' );
		    $objO::init( 'migla_stripemode', 'test', 'text' );
		    $objO::init( 'migla_webhook_key', '', 'text' );

            $preview_id = wp_insert_post(array('post_title'=>'Totaldonations Preview Thank You Page', 
                                        'post_type'=>'page',
                                        'post_content' => "[totaldonations_thank_you_page]"
                                    )
                                );
                                
            $objO::init("migla_preview_page", $preview_id, "text");
            
            $redirect_content = "Dear [firstname] [lastname],<br>";
            $redirect_content .= "Thank you for your donation [amount] for our campaign [campaign]<br>";
            $redirect_content .= "Sincerely<br>";
            
            $objRd::init_redirect( 0, get_locale(), $redirect_content, $preview_id);        
    
			self::add_custom_role();
	    }

	    static function donation_deactived_trigger( $networkwide )
		{
			    global $wpdb;

			    if( function_exists('is_multisite') && is_multisite() )
				{
			        if ($networkwide)
					{
			            $old_blog 	= $wpdb->blogid;

			            $blogids 	= $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			            foreach ($blogids as $blog_id)
						{
			                switch_to_blog($blog_id);
			                self::donation_deactived();
			            }
			            switch_to_blog($old_blog);
			            return;
			        }
			    }else{
			        self::donation_deactived();
				}
		}

		static function donation_deactived()
		{
			self::remove_custom_role();
	    }

		static function add_custom_role()
		{
	            add_role( 'totaldonation-accountant', 'TotalDonations Accountant',
	                        array( 'read' => true )
	                   	);

	            $roles = array( 'administrator' );

	            foreach( $roles as $user_role )
	            {
	                $role = get_role( $user_role );

	                $role->add_cap('read_dashboard', true);
	                $role->add_cap('read_campaign', true);
	                $role->add_cap('read_form', true);
	                $role->add_cap('read_email_receipt', true);
	                $role->add_cap('read_customize_themes', true);
	                $role->add_cap('read_reports', true);
	                $role->add_cap('read_gateway', true);
	                $role->add_cap('read_security_frontend', true);
	                $role->add_cap('read_logs', true);
	                $role->add_cap('read_translation', true);
	                $role->add_cap('read_help', true);

	                $role->add_cap('delete_reports', true);
	                $role->add_cap('edit_reports', true);
	                $role->add_cap('export_reports', true);

	                $role->add_cap('approve_donation', true);
	                $role->add_cap('manage_data', true);
	            }

	            $roles_2nd = array('totaldonation-accountant');

	            foreach( $roles_2nd as $user_role )
	            {
	                $role = get_role( $user_role );

	                $role->add_cap('read_dashboard', true);
	                $role->add_cap('read_reports', true);
	                $role->add_cap('export_reports', true);

	                $role->remove_cap('read_campaign');
	                $role->remove_cap('read_form');
	                $role->remove_cap('read_email_receipt');
	                $role->remove_cap('read_customize_themes');
	                $role->remove_cap('read_gateway');
	                $role->remove_cap('read_security_frontend');
	                $role->remove_cap('read_logs');
	                $role->remove_cap('read_translation');
	                $role->remove_cap('read_help');

	                $role->remove_cap('delete_reports');
	                $role->remove_cap('edit_reports');

	                $role->remove_cap('approve_donation');
	                $role->remove_cap('manage_data');
	            }
		}

		static function remove_custom_role()
		{
			remove_role( 'totaldonation-accountant' );

			$roles = array( 'administrator' );

			foreach( $roles as $user_role )
			{
							$role = get_role( $user_role );

							$role->remove_cap('read_dashboard');
							$role->remove_cap('read_reports');
							$role->remove_cap('export_reports');

							$role->remove_cap('read_campaign');
							$role->remove_cap('read_form');
							$role->remove_cap('read_email_receipt');
							$role->remove_cap('read_customize_themes');
							$role->remove_cap('read_gateway');
							$role->remove_cap('read_security_frontend');
							$role->remove_cap('read_logs');
							$role->remove_cap('read_translation');
							$role->remove_cap('read_help');

							$role->remove_cap('delete_reports');
							$role->remove_cap('edit_reports');

							$role->remove_cap('approve_donation');
							$role->remove_cap('manage_data');
			}
		}

	    static function tables_creation()
		{
				global $wpdb;
				$charset_collate 	= $wpdb->get_charset_collate();
				
	        //table campaign
				$table_campaign	= $wpdb->prefix . 'migla_campaign';

				$sql_cmp = "CREATE TABLE IF NOT EXISTS $table_campaign(";
				$sql_cmp .= " id int(11) NOT NULL AUTO_INCREMENT,";
				$sql_cmp .= " target double default 0,";
				$sql_cmp .= " name text default '',";
				$sql_cmp .= " shown char(3) default '1',";
				$sql_cmp .= " multi_list char(1) default '0',";
				$sql_cmp .= " form_id int default 0,";
				$sql_cmp .= " PRIMARY KEY (id)";
				$sql_cmp .= " )$charset_collate;";

				//table form
				$table_form	= $wpdb->prefix . 'migla_form';

				$sql_form = "CREATE TABLE IF NOT EXISTS $table_form(";
				$sql_form .= " id int(11) NOT NULL AUTO_INCREMENT,";
				$sql_form .= " form_id int(11) DEFAULT 0,";
				$sql_form .= " structure TEXT default '',";
				$sql_form .= " amounts TEXT default '',";
				$sql_form .= " hideCustomAmount char(3) DEFAULT 'no',";
				$sql_form .= " amountBoxType varchar(20) default '',";
				$sql_form .= " buttonType varchar(20) default '',";
				$sql_form .= " language_origin VARCHAR(10) default 'en_US',";
				$sql_form .= " PRIMARY KEY (id)";
				$sql_form .= " )$charset_collate;";

				$table_form_meta 	= $wpdb->prefix . 'migla_form_meta';

				$sql_form_meta = "CREATE TABLE IF NOT EXISTS $table_form_meta(";
				$sql_form_meta .= " id int(11) NOT NULL AUTO_INCREMENT,";
				$sql_form_meta .= " form_id int(11) DEFAULT 0,";
				$sql_form_meta .= " language varchar(6) default '',";
				$sql_form_meta .= " meta_key varchar(255) default '',";
				$sql_form_meta .= " meta_value text default '',";
				$sql_form_meta .= " PRIMARY KEY (id)";
				$sql_form_meta .= " )$charset_collate;";

				$table_form_field 	= $wpdb->prefix . 'migla_form_field';

				$sql_form_field = "CREATE TABLE IF NOT EXISTS $table_form_field(";
				$sql_form_field .= " id int(11) NOT NULL AUTO_INCREMENT,";
				$sql_form_field .= " form_id int(11) DEFAULT 0,";
				$sql_form_field .= " language varchar(6) default '',";
				$sql_form_field .= " meta_key varchar(255) default '',";
				$sql_form_field .= " meta_value text default '',";
				$sql_form_field .= " PRIMARY KEY (id)";
				$sql_form_field .= " )$charset_collate;";

				//This is a table for saving multilanguage redirect page
				$table_redirect 	= $wpdb->prefix . 'migla_redirect';
				$sql_redirect = "CREATE TABLE IF NOT EXISTS $table_redirect (";
				$sql_redirect .= " id int(11) NOT NULL AUTO_INCREMENT,";
				$sql_redirect .= "form_id INT NULL,";
				$sql_redirect .= " language varchar(7) NOT NULL,";
				$sql_redirect .= " content text NULL,";
				$sql_redirect .= " pageid int NULL,";
				$sql_redirect .= " PRIMARY KEY (id)";
				$sql_redirect .= " ) $charset_collate;";

				//This is a table for saving multilanguage email
				$table_email 	= $wpdb->prefix . 'migla_email';

				$sql_email = "CREATE TABLE IF NOT EXISTS $table_email(";
				$sql_email .= "id int(11) NOT NULL AUTO_INCREMENT,";
				$sql_email .= "form_id INT(11) NOT NULL,";
				$sql_email .= "reply_to VARCHAR(100) NULL,";
				$sql_email .= "reply_to_name VARCHAR(100) NULL,";
				$sql_email .= "attachment CHAR(1) default '0',";
				$sql_email .= "notify_emails TEXT NULL,";
				$sql_email .= "is_thankyou_email CHAR(1) default '1',";
				$sql_email .= "is_honoree_email CHAR(1) default '0',";
				$sql_email .= "is_offline_sent CHAR(1) NOT NULL DEFAULT '0',";
				$sql_email .= "PRIMARY KEY (id)";
				$sql_email .= ")$charset_collate;";

	      //metadata for emails
	      $table_emailmeta 	= $wpdb->prefix . 'migla_email_meta';

				$sql_emailmeta = "CREATE TABLE IF NOT EXISTS $table_emailmeta(";
				$sql_emailmeta .= "id int(11) NOT NULL AUTO_INCREMENT,";
				$sql_emailmeta .= "email_id INT(11) NOT NULL,";
				$sql_emailmeta .= "language varchar(7) NULL,";
				$sql_emailmeta .= "type VARCHAR(20) NULL,";
				$sql_emailmeta .= "body text NULL,";
				$sql_emailmeta .= "subject text NULL,";
				$sql_emailmeta .= "custom_message text NULL,";
				$sql_emailmeta .= "repeating text NULL,";
				$sql_emailmeta .= "anonymous text NULL,";
				$sql_emailmeta .= "signature text NULL,";

				$sql_emailmeta .= "PRIMARY KEY (id)";
				$sql_emailmeta .= ")$charset_collate;";

	      //tables for saving donations
	      $sql_donation = "CREATE TABLE IF NOT EXISTS "
				              . $wpdb->prefix . "migla_donation(";
	            $sql_donation .= "id bigint(20) NOT NULL AUTO_INCREMENT,";
	            $sql_donation .= "status INT NOT NULL DEFAULT 1,";
	            $sql_donation .= "email varchar(255) NOT NULL,";
	            $sql_donation .= "firstname mediumtext NOT NULL,";
	            $sql_donation .= "lastname mediumtext NOT NULL,";
	            $sql_donation .= "amount DECIMAL(13,4) NOT NULL,";
	            $sql_donation .= "campaign varchar(50) NOT NULL,";
	            $sql_donation .= "country mediumtext NOT NULL,";
	            $sql_donation .= "anonymous char(3) NOT NULL,";
	            $sql_donation .= "repeating varchar(30) NOT NULL,";
	            $sql_donation .= "mailist char(3) NOT NULL,";
	            $sql_donation .= "gateway varchar(20) NOT NULL,";
	            $sql_donation .= "date_created datetime NOT NULL,";
	            $sql_donation .= "gmt varchar(5) NOT NULL,";
	            $sql_donation .= "session_id TEXT NOT NULL,";
	            $sql_donation .= "timestamp INT NOT NULL,";
	            $sql_donation .= "PRIMARY KEY (id)";
	            $sql_donation .= ")$charset_collate;";

	      //Table for saving metadata of a donation
				$sql_donation_meta = "CREATE TABLE IF NOT EXISTS ";
				$sql_donation_meta .= $wpdb->prefix . "migla_donation_meta(";
				$sql_donation_meta .= " id bigint(20) NOT NULL AUTO_INCREMENT,";
				$sql_donation_meta .= " donation_id bigint(20) NOT NULL,";
				$sql_donation_meta .= " meta_key varchar(255) NOT NULL,";
				$sql_donation_meta .= " meta_value text NULL,";
				$sql_donation_meta .= " PRIMARY KEY (id)";
				$sql_donation_meta .= " )$charset_collate;";


							$sql_language = "CREATE TABLE IF NOT EXISTS ";
							$sql_language .= $wpdb->prefix . "migla_languages(";
							$sql_language .= " id bigint(20) NOT NULL AUTO_INCREMENT,";
							$sql_language .= " language VARCHAR(20) DEFAULT '".get_locale()."',";
							$sql_language .= " is_origin CHAR(3) DEFAULT 'no',";
							$sql_language .= " PRIMARY KEY (id)";
							$sql_language .= " )$charset_collate;";

							$sql_option = "CREATE TABLE IF NOT EXISTS ";
							$sql_option .= $wpdb->prefix . "migla_options(";
							$sql_option .= " id bigint(20) NOT NULL AUTO_INCREMENT,";
							$sql_option .= " option_name VARCHAR(255) NOT NULL,";
							$sql_option .= " option_value TEXT NOT NULL,";
							$sql_option .= " PRIMARY KEY (id)";
							$sql_option .= " )$charset_collate;";
	        
	        $sql_client_log = "CREATE TABLE IF NOT EXISTS ";
			$sql_client_log .= $wpdb->prefix . "migla_client_log(";
			$sql_client_log .= " id bigint(20) NOT NULL AUTO_INCREMENT,";
			$sql_client_log .= " timestamp VARCHAR(30) NULL,";
			$sql_client_log .= " ipaddress VARCHAR(30) NULL,";
			$sql_client_log .= " status varchar(50) NULL,";
			$sql_client_log .= " message text NULL,";
			$sql_client_log .= " PRIMARY KEY (id)";
			$sql_client_log .= " )$charset_collate;";
				
	    	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	      	dbDelta( $sql_cmp );
			dbDelta( $sql_form );
			dbDelta( $sql_form_meta );
			dbDelta( $sql_form_field );
		    dbDelta( $sql_donation );
	      	dbDelta( $sql_donation_meta );
	      	dbDelta( $sql_email );
	      	dbDelta( $sql_emailmeta);
	      	dbDelta( $sql_redirect );
			dbDelta( $sql_language );
			dbDelta( $sql_option );
			dbDelta( $sql_client_log );
			
	    }

		static function load_admin_scripts($hook)
		{
			    $screen = get_current_screen();
			    $ajax_url =  admin_url( 'admin-ajax.php' );

			    if( $screen->id == 'widgets'  )
			    {
			      //No widgets
			    }

			    $migla_is_in_the_hook = ( $hook == ("toplevel_page_migla_donation_menu_page") || ( strpos( $hook, 'migla'  ) !== false )  );

			    $version = date ( "njYHi", time() );

			    //Totaldonations script that shows everywhere
			    wp_enqueue_style( 'migla_custom', Totaldonations_DIR_URL.'assets/css/migla_custom.css', array(), $version );

			    wp_enqueue_script( 'migla_custom', Totaldonations_DIR_URL.'assets/js/migla_custom-script.js', 
			    					array('jquery', 'wp-color-picker'), 
			    					$version);	

				wp_localize_script( 'migla_custom', 'miglaAdminAjax',
			            array( 'ajaxurl' => $ajax_url,
			                   'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                )
			    		);			    

			    if( $migla_is_in_the_hook )
			    {                    
			      	wp_enqueue_script( 'jquery' );
			      	wp_enqueue_script( 'jquery-ui-core' );

			      	wp_enqueue_style( 'miglabootstrap', Totaldonations_DIR_URL.'assets/plugins/bootstrap-4.0/bootstrap.min.css', array(), $version );
			      
			      	wp_enqueue_style( 'miglabootstrap-toggle', Totaldonations_DIR_URL.'assets/plugins/bootstrap-4.0/bootstrap-toggle.min.css', array(), $version );
			      
			      	wp_enqueue_style( 'miglafontawesome', Totaldonations_DIR_URL.'assets/css/font-awesome/css/font-awesome.css', array(), $version );			      
			      	wp_enqueue_style( 'miglaadmin', Totaldonations_DIR_URL.'assets/css/migla-admin.css', array(), $version  );

			      	wp_enqueue_script( 'migla_generic-function', Totaldonations_DIR_URL.'assets/js/migla_generic-function.js', array(), $version);

			      	wp_enqueue_script( 'miglarespond', Totaldonations_DIR_URL.'assets/plugins/others/respond.min.js', array(), $version);

			      	wp_enqueue_script( 'miglabootstrap', Totaldonations_DIR_URL.'assets/plugins/bootstrap-4.0/js/bootstrap.min.js', array(), $version);
			      	wp_enqueue_script( 'miglabootstrap-toggle', Totaldonations_DIR_URL.'assets/plugins/bootstrap-4.0/js/bootstrap-toggle.min.js', array(), $version);

			      if( $hook == ("toplevel_page_migla_donation_menu_page")  )
			      {
			        wp_enqueue_script( 'migla-chart.js', Totaldonations_DIR_URL.'assets/plugins/chart.js/Chart.js' );
			        wp_enqueue_style( 'migla-chart', Totaldonations_DIR_URL.'assets/plugins/chart.js/Chart.css' );

			        wp_enqueue_script( 'migla-main-js', Totaldonations_DIR_URL.'assets/js/admin/admin-dashboard.js' );

			        wp_localize_script( 'migla-main-js', 'miglaAdminAjax',
			            array( 'ajaxurl' => $ajax_url,
			                   'nonce' => ''
			                )
			              );
			      }

			      if( strpos(  $hook , 'migla_campaign_page') !== false )
			      {
			        wp_enqueue_script( 'migla-campaign-js', Totaldonations_DIR_URL.'assets/js/admin/admin-campaign.js' ,
			                    array( 'jquery-ui-core',
			                        'jquery-ui-sortable',
			                        'jquery-ui-draggable',
			                        'jquery-ui-droppable',
			                        'jquery',
			                        'media-upload',
			                        'thickbox'
			                    )
			        );

			         wp_localize_script( 'migla-campaign-js', 'miglaAdminAjax',
			            array( 'ajaxurl' => $ajax_url,
			                   'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                )
			         );
			      }

			      if( strpos($hook, 'migla_donation_front_settings_page') !== false  )
			      {
			            wp_enqueue_script( 'migla_frontend', Totaldonations_DIR_URL.'assets/js/admin/admin-frontend.js',
			              array(	'jquery-ui-core',
			                        'jquery-ui-sortable',
			                        'jquery-ui-draggable',
			                        'jquery-ui-droppable',
			                        'jquery'
			                        )
			            );

			         wp_localize_script( 'migla_frontend', 'miglaAdminAjax',
			            array( 'ajaxurl' => $ajax_url,
			                 'nonce' 	=> wp_create_nonce( 'migla-donate-nonce' )
			              )
			          );
			      }

			      if( strpos( $hook , 'migla_donation_system_status_page') !== false )
			      {

			          wp_enqueue_script( 'migla_system_status', Totaldonations_DIR_URL.'assets/js/admin/admin-system-status.js');

			          wp_localize_script( 'migla_system_status', 'miglaAdminAjax',
			                      array( 'ajaxurl' => $ajax_url,
			                           'nonce' 	=> wp_create_nonce( 'migla-donate-nonce' )
			                        )
			                    );
			      }

			      if( strpos( $hook , 'migla_stripe_setting_page' ) !== false )
			      {
			            wp_enqueue_script('media-upload');
			            wp_enqueue_script('thickbox');
			            wp_enqueue_style('thickbox');
			            wp_enqueue_media();

			              wp_enqueue_script( 'migla-stripe-settings-js', Totaldonations_DIR_URL.'assets/js/admin/admin-gateway-stripe.js' ,
			                    array(	'jquery-ui-core',
			                        'jquery-ui-sortable',
			                        'jquery-ui-draggable',
			                        'jquery-ui-droppable',
			                        'jquery',
			                        'media-upload',
			                        'thickbox'
			                        )
			                    );

			          wp_localize_script( 'migla-stripe-settings-js', 'miglaAdminAjax',
			                    array( 'ajaxurl' => $ajax_url,
			                            'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                      )
			          );

			      }

			        if( strpos( $hook , 'migla_donation_paypal_settings_page' ) !== false )
			        {
			            wp_enqueue_script('media-upload');
			            wp_enqueue_script('thickbox');
			            wp_enqueue_style('thickbox');
			            wp_enqueue_media();

			            wp_enqueue_script( 'migla-paypal-settings-js', Totaldonations_DIR_URL.'assets/js/admin/admin-gateway-paypal.js' ,
			                    array(	'jquery-ui-core',
			                        'jquery-ui-sortable',
			                        'jquery-ui-draggable',
			                        'jquery-ui-droppable',
			                        'jquery',
			                        'media-upload',
			                        'thickbox'
			                        )
			                    );

			          wp_localize_script  ( 'migla-paypal-settings-js', 'miglaAdminAjax',
			                    array( 'ajaxurl' => $ajax_url,
			                                        'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                          )
			                          );

			        }

			      if( strpos(  $hook , 'migla_donation_settings_page' ) !== false  )
			      {
			          wp_enqueue_script( 'migla-settings-js', Totaldonations_DIR_URL.'assets/js/admin/admin-email-receipt-settings.js',
			                          array(	'jquery-ui-core',
			                              'jquery-ui-sortable',
			                              'jquery-ui-draggable',
			                              'jquery-ui-droppable',
			                              'jquery',
			                              'media-upload',
			                              'thickbox'
			                          )
			          );

			          wp_localize_script( 'migla-settings-js', 'miglaAdminAjax',
			                    array( 'ajaxurl' =>  $ajax_url,
			                                'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                        )
			          );
			      }

			      if( strpos(  $hook , 'migla_donation_help' ) !== false )
			      {
			          wp_enqueue_script( 'migla-help-js', Totaldonations_DIR_URL.'assets/js/admin/admin-help.js');

			          wp_localize_script( 'migla-help-js', 'miglaAdminAjax',
			                        array( 'ajaxurl' =>  admin_url( 'admin-ajax.php' ) ,
			                              'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                        )
			          );
			      }

			      if( strpos( $hook , 'migla_donation_custom_theme') !== false )
			      {
			          	wp_enqueue_style( 'wp-color-picker' );

			          	wp_enqueue_script( 'migla-color-themes-js', Totaldonations_DIR_URL.'assets/js/admin/admin-custom-themes.js',
			                array('jquery', 'wp-color-picker' )
			          	);


			          	wp_localize_script( 'migla-color-themes-js', 'miglaAdminAjax',
			                array(	'ajaxurl' => $ajax_url,
			                        'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                      	)
			          	);
			      }

			      if( strpos(  $hook , 'migla_reports_page' ) !== false)
			      {
			                wp_enqueue_script( 'migla-reports-js', Totaldonations_DIR_URL.'assets/js/admin/admin-reports.js',
			                    array( 	'jquery',
			                        'jquery-ui-core',
			                        'jquery-ui-datepicker'
			                      )
			                );

			                wp_localize_script( 'migla-reports-js', 'miglaAdminAjax',
			            array( 'ajaxurl' =>  $ajax_url,
			                        'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                )
			          );

			            wp_enqueue_script( 'migla-report-js', Totaldonations_DIR_URL.'assets/plugins/datatable/jquery.dataTables.min.js' );

			        wp_enqueue_style( 'migla-dataTables2-css' , Totaldonations_DIR_URL.'assets/css/extra.css' );
			       }

			      if( strpos(  $hook , 'migla_donation_form_options_page' ) !== false )
			      {
			        wp_enqueue_script('media-upload');
			        wp_enqueue_script('thickbox');
			        wp_enqueue_style('thickbox');

			        wp_enqueue_script( 'migla-form-settings-js',  Totaldonations_DIR_URL.'assets/js/admin/admin-form-settings.js' ,
			                    array(	'jquery-ui-core',
			                        'jquery-ui-sortable',
			                        'jquery-ui-draggable',
			                        'jquery-ui-droppable',
			                        'jquery',
			                        'media-upload',
			                        'thickbox'
			                      )
			                  );

			        wp_localize_script( 'migla-form-settings-js', 'miglaAdminAjax',
			                  array( 'ajaxurl' =>  $ajax_url,
			                              'nonce' => wp_create_nonce( 'migla-donate-nonce' )
			                      )
			        );

			      }

			    }//If is the Hook
			}
	}
}
?>
