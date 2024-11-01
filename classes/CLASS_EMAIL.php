<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_EMAIL' ) )
{
	class MIGLA_EMAIL
	{
        public static function init_email()
        {
            global $wpdb;

            $form_id = 0;
            $reply_to = get_option('admin_email');
            $reply_to_name = "admin";
            $attachment = '0';
            $notify_emails = '';
            $is_thankyou_email = '1';
            $is_honoree_email = '0';

            $notify_emails = array( get_option('admin_email') );
            $notify_these_emails = serialize( $notify_emails );

            $sql = "SELECT id FROM {$wpdb->prefix}migla_email";
            $sql .= " WHERE form_id = %d";

            $id = $wpdb->get_var( $wpdb->prepare( $sql, 0 ) );

            if( $id > 0 )
            {
            }else{

                $wpdb->insert( "{$wpdb->prefix}migla_email",
                                array( "form_id"   => $form_id,
                                        "reply_to" => $reply_to,
                                        "reply_to_name" => $reply_to_name,
                                        "attachment"    => $attachment,
                                        "notify_emails" => $notify_these_emails,
                                        "is_thankyou_email" => $is_thankyou_email,
                                        "is_honoree_email"  => $is_honoree_email,
                                        "is_offline_sent"   => '0'
                                    ),
                               array(  "%d",
                                       "%s",
                                       "%s",
                                       "%s",
                                       "%s",
                                       "%s",
                                       "%s",
                                       "%s"
                                   )
                            );

                $id = $wpdb->insert_id;
           }

            $sql = "SELECT id FROM {$wpdb->prefix}migla_email_meta";
            $sql .= " WHERE email_id = %d and type = 'thankyou'";

            $meta_id = $wpdb->get_var( $wpdb->prepare( $sql, $id ) );

            if( $meta_id > 0 )
            {
            }else{

                $thankyou = "[date],<br>";
                $thankyou .= "[firstname] [lastname],<br><br>";
                $thankyou .= "I would like to thank you for your contribution [amount], which you so generously contributed to [campaign]. <br>";
                $thankyou .= "Your financial helps us continue in our mission. Your help is deeply appreciated and your generosity will make an immediate difference to our cause.<br><br>";
                $thankyou .= "[if_anonymous]";

                $columns = array( 'email_id' => $id,
                                     'subject' => 'Thank You for Your Donation',
                                     'body' => $thankyou,
                                     'language' => get_locale(),
                                     'type' => 'thankyou',
                                     'custom_message' => '',
                                     'repeating' => '',
                                     'anonymous' => 'Your name will not appear in public.<br>',
                                     'signature' => 'Sincerely, <br>Our team'
                                    );

                 $columns_type = array( '%d',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s',
                                        '%s'
                                        );

                $wpdb->insert( "{$wpdb->prefix}migla_email_meta",
                                $columns,
                                $columns_type
                            );
            }
        }

	    public function create_email( $form_id,
	                                    $reply_to,
	                                    $reply_to_name,
	                                    $attachment,
	                                    $notify_emails,
	                                    $is_thankyou_email,
	                                    $is_honoree_email,
	                                    $is_offline_email
	                                   )
	    {
	        global $wpdb;

	        if(!empty($notify_emails)){
	            $notify_these_emails = serialize($notify_emails);
	        }else{
	            $notify_these_emails = '';
	        }

	        $id = $this->if_email_id_exist( "form_id", $form_id, "%d" );

	        if( $id > 0 )
	        {
	            $wpdb->update( "{$wpdb->prefix}migla_email",
    	                        array( "form_id"   => $form_id,
    	                                "reply_to" => $reply_to,
    	                                "reply_to_name" => $reply_to_name,
    	                                "attachment"    => $attachment,
    	                                "notify_emails" => $notify_these_emails,
    	                                "is_thankyou_email" => $is_thankyou_email,
    	                                "is_honoree_email"  => $is_honoree_email,
                                        "is_offline_sent"   => $is_offline_email
    	                            ),
    	                       array( "id" => $id ),
    	                       array(  "%d",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
                                       "%s"
    	                           ),
    	                       array( "%d" )
    	                    );
	        }else{

    	        $wpdb->insert( "{$wpdb->prefix}migla_email",
    	                        array( "form_id"   => $form_id,
    	                                "reply_to" => $reply_to,
    	                                "reply_to_name" => $reply_to_name,
    	                                "attachment"    => $attachment,
    	                                "notify_emails" => $notify_these_emails,
    	                                "is_thankyou_email" => $is_thankyou_email,
    	                                "is_honoree_email"  => $is_honoree_email
    	                            ),
    	                       array(  "%d",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s"
    	                           )
    	                    );

    	        $id = $wpdb->insert_id;
	       }

	       return $id;
	    }

	    public function update_email( $email_id,
	                                  $form_id,
	                                    $reply_to,
	                                    $reply_to_name,
	                                    $attachment,
	                                    $notify_emails,
	                                    $is_thankyou_email,
	                                    $is_honoree_email,
	                                    $is_offline_email
	                                   )
	    {
	        global $wpdb;

	        if(!empty($notify_emails)){
	            $notify_these_emails = serialize($notify_emails);
	        }else{
	            $notify_these_emails = '';
	        }

	        if( !empty($email_id) )
	        {
	            $wpdb->update( "{$wpdb->prefix}migla_email",
    	                        array( "form_id"   => $form_id,
    	                                "reply_to" => $reply_to,
    	                                "reply_to_name" => $reply_to_name,
    	                                "attachment"    => $attachment,
    	                                "notify_emails" => $notify_these_emails,
    	                                "is_thankyou_email" => $is_thankyou_email,
    	                                "is_honoree_email"  => $is_honoree_email,
                                        "is_offline_sent"   => $is_offline_email
    	                            ),
    	                       array( "id" => $email_id ),
    	                       array(  "%d",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
                                       "%s"
    	                           ),
    	                       array( "%d" )
    	                    );
	        }else{

    	        $wpdb->insert( "{$wpdb->prefix}migla_email",
    	                        array( "form_id"   => $form_id,
    	                                "reply_to" => $reply_to,
    	                                "reply_to_name" => $reply_to_name,
    	                                "attachment"    => $attachment,
    	                                "notify_emails" => $notify_these_emails,
    	                                "is_thankyou_email" => $is_thankyou_email,
    	                                "is_honoree_email"  => $is_honoree_email
    	                            ),
    	                       array(  "%d",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s",
    	                               "%s"
    	                           )
    	                    );

    	        $id = $wpdb->insert_id;
	       }

	       return $id;
	    }

        public function insert_column_by_id( $email_id,
                                                $column_values,
                                                $column_types
                                            )
        {
            global $wpdb;

            $state = $wpdb->update( "{$wpdb->prefix}migla_email",
                                $column_values,
                                array( "id" => $email_id ),
                                $column_types,
                                array( "%d" )
                            );

            return $email_id ;
        }

        public function init_column_by_id( $form_id,
                                        $column_values,
                                        $column_types
                                    )
        {
            global $wpdb;

                $wpdb->insert( "{$wpdb->prefix}migla_email",
                                $column_values,
                                $column_types
                            );

                $id = $wpdb->insert_id;

           return $id;
        }

	    public function insert_meta( $email_id,
	                                 $type,
	                                 $columns,
	                                 $columns_type
	                                )
	    {
	        global $wpdb;

	        $id = $this->if_meta_id_exist( $email_id, $type );

            if( $id > 0 )
            {
                $wpdb->update( "{$wpdb->prefix}migla_email_meta",
                                $columns,
                                array( "id" => $id ),
                                $columns_type,
                                array( "%d" )
                            );
            }else{
                $wpdb->insert( "{$wpdb->prefix}migla_email_meta",
                                $columns,
                                $columns_type
                            );
            }

	    }

	    public function insert_meta_bylang( $email_id,
	                                 $type,
	                                 $language,
	                                 $columns,
	                                 $columns_type
	                                )
	    {
	        global $wpdb;

	        $id = $this->if_meta_id_exist_bylang( $email_id, $type, $language );

            if( $id > 0 )
            {
                $wpdb->update( "{$wpdb->prefix}migla_email_meta",
                                $columns,
                                array( "email_id" => $email_id,
                                        "type" => $type
                                        ),
                                $columns_type,
                                array( "%d", "%s" )
                            );
            }else{
                $wpdb->insert( "{$wpdb->prefix}migla_email_meta",
                                $columns,
                                $columns_type
                            );
            }

	    }

	    public function if_meta_exist( $email_id, $type )
	    {
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_email_meta";
			$sql .= " WHERE email_id = %d";
            $sql .= " and type = %s";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $email_id, $type ) );


			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
	    }

	    public function if_meta_id_exist( $email_id, $type )
	    {
			global $wpdb;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_email_meta";
			$sql .= " WHERE email_id = %d";
            $sql .= " and type = %s";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $email_id, $type ) );

			return $id;
	    }

	    public function if_meta_id_exist_bylang( $email_id, $type, $language )
	    {
			global $wpdb;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_email_meta";
			$sql .= " WHERE email_id = %d";
			$sql .= " AND language = %s";
            $sql .= " AND type = %s";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $email_id, $language, $type ) );

			return $id;
	    }

	    public function if_email_exist( $col, $colval, $coltype )
	    {
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_email";
			$sql .= " WHERE ".$col." = " . $coltype;

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $colval ) );

			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
	    }

	    public function if_email_id_exist( $col, $colval, $coltype )
	    {
			global $wpdb;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_email";
			$sql .= " WHERE ".$col." = " . $coltype;

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $colval ) );

			return $id;
	    }

        public function email_meta_list()
        {
            return array( 'meta_id' => '',
                                     'email_id' => '',
                                     'subject' => '',
                                     'body' => '',
                                     'language' => '',
                                     'type' => '',
                                     'custom_message' => '',
                                     'repeating' => 'no',
                                     'anonymous' => 'no',
                                     'signature' => ''
                                    );
        }

        public function H_email_meta_list()
        {
            return array( 'H_meta_id' => '',
                                     'H_email_id' => '',
                                     'H_subject' => '',
                                     'H_body' => '',
                                     'H_language' => '',
                                     'H_type' => '',
                                     'H_custom_message' => '',
                                     'H_repeating' => '',
                                     'H_anonymous' => '',
                                     'H_signature' => ''
                                    );
        }

        public function OA_email_meta_list()
        {
            return array( 'OA_meta_id' => '',
                                     'OA_email_id' => '',
                                     'OA_subject' => '',
                                     'OA_body' => '',
                                     'OA_language' => '',
                                     'OA_type' => '',
                                     'OA_custom_message' => '',
                                     'OA_repeating' => '',
                                     'OA_anonymous' => '',
                                     'OA_signature' => ''
                                    );
        }

        public function email_list()
        {
            return array( 'id' => '',
                                        'form_id'   => '',
                                        'reply_to'  => '',
                                        'reply_to_name'     => '',
                                        'attachment'        => '0',
                                        'notify_emails'     => '',
                                        'is_thankyou_email' => '1',
                                        'is_honoree_email' => '0',
                                        'is_offline_sent'  => '0'
                                    );
        }

        public function get_column( $form_id, $colName )
        {
              global $wpdb;

              $sql = "SELECT ".$colName." FROM {$wpdb->prefix}migla_email";
              $sql .= " WHERE form_id = %d";

              $col = $wpdb->get_var( $wpdb->prepare(
                        $sql, $form_id )
                    );

            return $col;
        }

        public function update_column( $email_id, $colName, $colVal, $colType)
        {
            global $wpdb;

            $wpdb->update( "{$wpdb->prefix}migla_email",
                                array( $colName => $colVal ),
                                array( "id" => $email_id ),
                                array( $colType ),
                                array( "%d" )
                            );
        }

        public function get_email_by_idlanguage($id, $lang)
        {
            global $wpdb;

            $email      = $this->email_list();
            $emailMeta  = $this->email_meta_list();
            $h_emailMeta  = $this->H_email_meta_list();
            $OA_emailMeta = $this->OA_email_meta_list();

            $result = array_merge($email, $emailMeta,  $h_emailMeta, $OA_emailMeta);

            $resultset = array();
            $resultsetMeta = array();

            $sql1 = "select * from {$wpdb->prefix}migla_email";
            $sql1 .= " Where form_id = %d";

            $resultset = $wpdb->get_results( $wpdb->prepare($sql1, $id), ARRAY_A );

            $email_id= '';

            if(!empty($resultset))
            {
                foreach($resultset as $rs){
                    foreach( $rs as $col => $val ){
                        $result[$col] = $val;
                        if($col =='id') $email_id = $val;
                    }
                }

                $sql2 = "select * from {$wpdb->prefix}migla_email_meta";
                $sql2 .= " Where email_id = %d";
                $sql2 .= " AND language = %s";

                $resultsetMeta = $wpdb->get_results( $wpdb->prepare($sql2, $email_id, $lang), ARRAY_A );

                if(!empty($resultsetMeta))
                {
                    foreach( $resultsetMeta as $rs)
                    {
                        if( $rs['type'] == 'honoree' ){
                            $prefix = 'H_';
                        }else if( $rs['type'] == 'offline' ){
                            $prefix = 'O_';
                        }else if( $rs['type'] == 'offline_approval' ){
                            $prefix = 'OA_';

                        }else if( $rs['type'] == 'donor' ){
                            $prefix = 'D_';

                        }else if( $rs['type'] == 'fosterparent' ){
                            $prefix = 'F_';

                        }else{
                            $prefix = '';
                        }

                        foreach( $rs as $col => $val ){
                            if( $col == 'id' ){
                              $col = $prefix.'meta_id';
                            }

                            $result[($prefix.$col)] = $val;
                        }
                    }
                }//if not empty emailmeta

            }//if not empty email

            return $result;
        }

        public function get_email_by_id_lang_type( $id, $lang, $type)
        {
            global $wpdb;

            $email      = $this->email_list();
            $emailMeta  = $this->email_meta_list();

            $result = array_merge($email, $emailMeta );

            $resultset = array();
            $resultsetMeta = array();

            $sql1 = "select * from {$wpdb->prefix}migla_email";
            $sql1 .= " Where form_id = %d";

            $resultset = $wpdb->get_results( $wpdb->prepare($sql1, $id), ARRAY_A );

            $email_id= '';

            if(!empty($resultset))
            {
                foreach($resultset as $rs){
                    foreach( $rs as $col => $val ){
                        $result[$col] = $val;
                        if($col =='id') $email_id = $val;
                    }
                }

                $sql2 = "select * from {$wpdb->prefix}migla_email_meta";
                $sql2 .= " Where email_id = %d";
                $sql2 .= " AND language = %s";
                $sql2 .= " AND type = %s";

                $resultsetMeta = $wpdb->get_results( $wpdb->prepare( $sql2,
                                                                     $email_id,
                                                                     $lang,
                                                                     $type
                                                                 ), ARRAY_A );

                if(!empty($resultsetMeta))
                {
                    foreach( $resultsetMeta as $rs)
                    {
                        if( $rs['type'] == 'honoree' ){
                            $prefix = 'H_';
                        }else{
                            $prefix = '';
                        }

                        foreach( $rs as $col => $val ){
                            if( $col == 'id' ){
                              $col = $prefix.'meta_id';
                            }

                            $result[($prefix.$col)] = $val;
                        }
                    }
                }//if not empty emailmeta

            }//if not empty email

            return $result;
        }

        public function mail_utf8($to, $from_user, $from_email,
                            $subject = '(No subject)', $message = '')
        {
          $from_user = "=?UTF-8?B?".base64_encode($from_user)."?=";
          $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

          $headers = "From: $from_user <$from_email>\r\n".
                   "MIME-Version: 1.0" . "\r\n" .
                   "Content-type: text/html; charset=UTF-8" . "\r\n";

            return mail($to, $subject, $message, $headers);
        }

        public function set_header( $reply_to, $reply_to_name )
        {
            $headers  = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "Reply-To: Admin <" . $reply_to .">\r\n";
            $headers .= "Return-Path: Admin <" . $reply_to .">\r\n";
            $headers .= 'From: '.$reply_to_name.' <'.$reply_to.'>' . "\r\n";

            return $headers;
        }

        public function get_header( $reply_to, $reply_to_name, $uid )
        {
            $eol = PHP_EOL;

            $header = "From: ".$reply_to." <".$reply_to_name.">".$eol;
            $header .= "Reply-To: ".$reply_to.$eol;
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"";

            return $header;
        }

        public function get_the_body(
                                $firstname, //1
                                $lastname, //2
                                $amount, //3
                                $date, //4
                                $body,  //5
                                $repeat, //6
                                $anon, //7
                                $campaign_id, //8
                                $isRepeat,  //9
                                $isAnon, //10
                                $language //11
                                )
        {
            $thebody = "";

            $objM = new MIGLA_MONEY;
            $currency = $objM->get_default_currency();

            $objC = new MIGLA_CAMPAIGN;
            $cmp_name = '';

            if( $campaign_id == 0 )
            {
                $cmp_name = $objC->get_undesignated();
            }else{
                $cmp = $objC->get_info_by_campaign_id( $campaign_id, $language );
                $cmp_name = $cmp['name'];
            }

            $msg_repeating = '';
            $msg_anonymous = '';

            if( $isRepeat == 'yes'){
                  $msg_repeating = $repeat ;
            }
            if( $isAnon == 'yes' ){
              $msg_anonymous = $anon;
            }

            $placeholder = array( '[firstname]',
                                    '[lastname]' ,
                                    '[amount]' ,
                                    '[date]',
                                    '[newline]',
                                    '[currency]',
                                    '[campaign]',
                                    '[if_repeating]',
                                    '[if_anonymous]'
                                    );

            $replace = array( $firstname,
                              $lastname,
                              $amount,
                              $date,
                              '<br>',
                              $currency,
                              $cmp_name,
                              $msg_repeating,
                              $msg_anonymous
                              );

            $thebody = $body ."<br>" ;

            $content  =  str_replace($placeholder, $replace, $thebody );
            $content_1 = $content;
            $content  =  str_replace( "\\" , "" , $content_1 );
            $content  = "<html><body>" . $content . "</body></html>";

            return $content;
        }

        public function get_the_H_body(
                                $firstname, //1
                                $lastname, //2
                                $amount, //3
                                $date, //4
                                $body,  //5
                                $repeat, //6
                                $anon, //7
                                $campaign_id, //8
                                $isRepeat,  //9
                                $isAnon, //10
                                $language, //11
                                $custom_message,
                                $message,
                                $honoree
                                )
        {
            $thebody = "";

            $objM = new MIGLA_MONEY;
            $currency = $objM->get_default_currency();

            $objC = new MIGLA_CAMPAIGN;
            $cmp_name = '';

            if( $campaign_id == 0 )
            {
                $cmp_name = $objC->get_undesignated();
            }else{
                $cmp = $objC->get_info_by_campaign_id( $campaign_id, $language );
                $cmp_name = $cmp['name'];
            }

            $msg_repeating = '';
            $msg_anonymous = '';

            if( $isRepeat == 'yes'){
                  $msg_repeating = $repeat ;
            }
            if( $isAnon == 'yes' ){
              $msg_anonymous = $anon  ;
            }

            $placeholder = array( '[firstname]',
                                    '[lastname]' ,
                                    '[amount]' ,
                                    '[date]',
                                    '[newline]',
                                    '[currency]',
                                    '[campaign]',
                                    '[if_repeating]',
                                    '[if_anonymous]',
                                    '[custom_message]',
                                    '[honoree_name]'
                                    );

            $placeholder2 = array( '[firstname]',
                                    '[lastname]' ,
                                    '[honoree_name]',
                                    '[amount]' ,
                                    '[campaign]',
                                    '[message]'
                                    );

            $replace2 = array( $firstname,
                              $lastname,
                              $honoree,
                              $amount,
                              $cmp_name,
                              $message
                              );

            $customed_message = str_replace($placeholder2, $replace2, $custom_message );

            $replace = array( $firstname,
                              $lastname,
                              $amount,
                              $date,
                              '<br>',
                              $currency,
                              $cmp_name,
                              $msg_repeating,
                              $msg_anonymous,
                              $customed_message,
                              $honoree
                              );

            $thebody = $body ."<br>" ;

            $content  =  str_replace($placeholder, $replace, $thebody );
            $content_1 = $content;
            $content  =  str_replace( "\\" , "" , $content_1 );
            $content  = "<html><body>" . $content . "</body></html>";

            return $content;
        }

        public function get_campaign_name( $campaign )
        {
            $objC = new MIGLA_CAMPAIGN;
            $objL = new MIGLA_LOCAL;

            $cmp_name = '';

            if( $campaign == 0 )
            {
                $cmp_name = $objC->get_undesignated();
            }else{
                $cmp = $objC->get_info_by_campaign_id( $campaign, $objL->get_origin() );
                $cmp_name = $cmp['name'];
            }

            return $cmp_name;
        }

        public function replace_code( $text,
                                    $firstname,
                                    $lastname,
                                    $amount,
                                    $campaign )
        {
            $cmp_name = $this->get_campaign_name( $campaign );

            $placeholder = array( '[firstname]',
                                    '[lastname]' ,
                                    '[amount]' ,
                                    '[date]',
                                    '[campaign]'
                                    );

            $replace = array( $firstname,
                              $lastname,
                              $amount,
                              $cmp_name
                              );

            $text_out = str_replace($placeholder, $replace, $text );

            return $text_out;
        }

        public function format_amount( $amount, $type, $currency )
        {
            $objM = new MIGLA_MONEY;
            $res = $objM->full_format( $amount, $type );

            if( empty($currency) )
            {
               $currency = $objM->get_default_currency();
            }else{
                $currency = $res[1];
            }

            if( empty($currency) ){
                $output = $res[0];
            }else{
                $output = $res[0] . ' '.$currency;
            }

            return $output;
        }

        public function email_procedure( $form_id,
                                         $record_id,
                                         $data,
                                         $language )
        {
            $isThank = $this->get_column( $form_id, 'is_thankyou_email' );

            $isHonoree = $this->get_column( $form_id, 'is_honoree_email' );

            $donationdate = date(get_option('date_format'));

            $msg = '';
            $msg1 = '';

            $objEmailLog = new MIGLA_LOG("email-");

            $donorname = "";

            if( isset($data['miglad_firstname']) ){
                $donorname .= $data['miglad_firstname'] . " ";
            }
            if( isset($data['miglad_lastname']) ){
                $donorname .= $data['miglad_lastname'] . " ";
            }

            $objO = new MIGLA_OPTION;
            $use_PHPMailer = $objO->get_option('migla_use_PHPMailer') == 'yes';

            if( $isThank == '1' )
            {
                $status = $this->send_email( $record_id,
                                                $data,
                                                $language ,
                                                $use_PHPMailer,
                                                $donationdate );

                if( $use_PHPMailer ){
                        $msg1 .= " using PHPMailer ";
                }

                $msg1 .= ' without PDF Receipt';

                if( $status ){
                    $msg .= 'Thank You email has been sent' . $msg1 ." " . $donorname;
                }else{
                    $msg .= 'Thank You email has NOT been sent' . $msg1 ." " . $donorname;
                }

                if( isset($data['miglad_email']) ){
                    $msg .=  ' via '.$data['miglad_email'];
                }

                $NotifEmails = $this->get_column( $form_id, 'notify_emails' );

                if( isset( $data['miglad_amount'] ) ){
                    if( isset($data['miglad_currency']) )
                    {
                        $amount = $this->format_amount( $data['miglad_amount'], 2, $data['miglad_currency'] );
                    }else{
                        $amount = $this->format_amount( $data['miglad_amount'], 2, '' );
                    }

                    $data['miglad_amount'] = $amount;
                }

                if(!empty($NotifEmails)){
                    $emails = (array)unserialize($NotifEmails);
                    foreach( $emails as $nf ){
                         $this->send_notification_mail( $nf, $data, $language );
                    }
                }
            }

            $objEmailLog->append( "[".current_time('mysql') . "] ". $msg );

            return $status;
        }

        public function email_resend_procedure( $form_id,
                                                $record_id,
                                                $isReceipt,
                                                $data,
                                                $language,
                                                $timestamp,
                                                $date )
        {

            if( !empty($timestamp) ){
                $donationdate = date(get_option('date_format'), $timestamp);
            }else if( !empty($date) ){
                $donationdate = $date;
            }else{
                //$donationdate = date(get_option('date_format'));
                $donationdate = date( 'F j, Y ' );
            }

            $msg = '..';
            $msg1 = '';
            $status = false;

            $objO = new MIGLA_OPTION;
            $use_PHPMailer = $objO->get_option('migla_use_PHPMailer') == 'yes';

            $status = $this->send_email( $record_id,
                                                $data,
                                                $language ,
                                                $use_PHPMailer,
                                                $donationdate );
            return $status;
        }

        public function custom_field_sc( $body, $data )
        {
            $left = "";
            $right = "";

            while( strpos($body, '[#' ) > 0 )
            {
                $pos1 = strpos($body, '[#' );

                $left .= substr($body, 0, $pos1);

                $pos2 = strpos($body, '#]' );

                if( $pos2 > 0 )
                {
                    $right = substr($body, $pos2 + 2 );

                    $key = substr($body, $pos1 + 2, $pos2 - $pos1 -2 );

                    $body = $right;

                    if(isset($data[$key]))
                    {
                        $left .= $data[$key];
                    }else{
                        $left .= " ";
                    }
                }//if open and end tag
            }//while seeing custom

            return $left . $right;
        }

        public function send_email( $recid, $data, $language, $isPHPMailer, $donationdate )
        {
            $donor_email = $data['miglad_email'];
            $form_id = $data['miglad_form_id'];

            $mydata = $this->get_email_by_idlanguage($form_id, $language);
            $isRepeat = 'no';

            $date = $donationdate;
            $status = true;

            if( isset($data['miglad_currency']) )
            {
                $amount = $this->format_amount( $data['miglad_amount'], 2, $data['miglad_currency'] );
            }else{
                $amount = $this->format_amount( $data['miglad_amount'], 2, '' );
            }

            $body = $this->get_the_body( $data['miglad_firstname'], //1
                                $data['miglad_lastname'], //2
                                $amount, //3
                                $date, //4
                                $mydata['body'], //5
                                $mydata['repeating'], //6
                                $mydata['anonymous'], //7
                                $data['miglad_campaign'], //8
                                $isRepeat, //9
                                $data['miglad_anonymous'], //10
                                $language //11
                             );

            if( isset($data['miglad_honoreename']) && !empty($data['miglad_honoreename']) ){

                $honor_msg = "This donation is in honor of ". $data['miglad_honoreename'] . ".";

                $body = str_replace( "[if_in_honor]", $honor_msg, $body );
            }else{
                $body = str_replace( "[if_in_honor]", "", $body );
            }

            $subject = $this->replace_code( $mydata['subject'],
                                           $data['miglad_firstname'],
                                           $data['miglad_lastname'],
                                           $amount,
                                           $data['miglad_campaign']
                                           );

            if( $isPHPMailer )
            {
                $status = $this->send_with_PHPMailer( $mydata['reply_to'],
                                                $mydata['reply_to_name'],
                                                false,
                                                '',
                                                $subject,
                                                $body,
                                                $donor_email,
                                                ''
                                            );
            }else{
                $header = $this->set_header( $mydata['reply_to'], $mydata['reply_to_name'] );

                $status = wp_mail($donor_email, $subject, $body, $header );

                if( $status ){
                }else{
                    $status = mail($donor_email, $subject, $body, $header );
                }

            }

            return $status;
        }

        public function send_with_PHPMailer( $reply_to,
                                                $reply_to_name,
                                                $is_pdf_receipt,
                                                $attachment,
                                                $subject,
                                                $body,
                                                $donor_email,
                                                $donor_name
                                            )
        {
            $status = true;

            try{
                global $phpmailer;
                // (Re)create it, if it's gone missing Called from WordPress
                if ( ! ( $phpmailer instanceof PHPMailer ) ) {
                	require_once ABSPATH . WPINC . '/class-phpmailer.php';
                  require_once ABSPATH . WPINC . '/class-smtp.php';
                }

                $phpmailer = new PHPMailer;
                $objO = new MIGLA_OPTION;

                $phpmailer->isSMTP();
                $phpmailer->SMTPDebug  = false;

                $phpmailer->Host = $objO->get_option('migla_smtp_host');
                $phpmailer->Username = $objO->get_option('migla_smtp_user');
                $phpmailer->Password =  $objO->get_option('migla_smtp_password');

                $phpmailer->SMTPAuth = ( $objO->get_option('migla_smtp_authenticated') == 'yes');

                if($phpmailer->SMTPAuth){
                    $phpmailer->SMTPSecure = $objO->get_option('migla_smtp_secure');
                }

                $phpmailer->Port = $objO->get_option('migla_smtp_port');

                $phpmailer->setFrom( $reply_to, $reply_to_name );
                $phpmailer->AddReplyTo( $reply_to, $reply_to_name );

                $phpmailer->addAddress( $donor_email, $donor_name);

                $phpmailer->isHTML(true);

                $phpmailer->Subject = $subject;
                $phpmailer->Body    = $body;

                $status = $phpmailer->send();

            }catch(phpmailerException $e){
                $objEmailLog = new MIGLA_LOG("email-");
                $objEmailLog->append( "[".current_time('mysql') . "] ". $e->errorMessage() );
            } catch (Exception $e){
                $objEmailLog = new MIGLA_LOG("email-");
                $objEmailLog->append( "[".current_time('mysql') . "] ". $e->errorMessage() );
            }

            return $status;
        }

        public function send_notification_mail( $email, $data, $language )
        {
            if(isset($data['miglad_form_id']))
                $form_id = $data['miglad_form_id'];
            else
                $form_id = 0;

            $mydata = $this->get_email_by_idlanguage($form_id, $language);

            $header = $this->set_header( $mydata['reply_to'], $mydata['reply_to_name'] );

            $subject = "You just received a donation";

            $donor      = "";
            $donor_email= "";
            $amount     = "";
            $campaign   = "";

            $body = "<table><tbody>";

            if( isset($data['miglad_firstname']) && !empty($data['miglad_firstname']) )
            {
                $donor .= $data['miglad_firstname'];

                if( isset($data['miglad_lastname']) && !empty($data['miglad_lastname']) )
                {
                    $donor .= ' '.$data['miglad_lastname'];

                }

                $body .= "<tr><td>"."Donor:"."</td><td>".$donor."</td></tr>";
            }

            if( isset($data['miglad_amount']) && !empty($data['miglad_amount']) )
            {
                $amount .= $data['miglad_amount'];
                $body .= "<tr><td>"."Amount:"."</td><td>".$amount."</td></tr>";
            }

            if(isset($data['miglad_campaign']))
            {
                $campaign = $cmp_name = $this->get_campaign_name( $data['miglad_campaign'] );
                $body .= "<tr><td>"."Campaign:"."</td><td>".$campaign."</td></tr>";
            }

            if(isset($data['miglad_email']))
            {
                $donor_email= $data['miglad_email'];
                $body .= "<tr><td>"."Email:"."</td><td>".$donor_email."</td></tr>";
            }

            if(isset($data['miglad_address']))
            {
                $address = $data['miglad_address'];
                $body .= "<tr><td>"."Address:"."</td><td>".$address."</td></tr>";
            }

            $body .= "</tbody></table>";
            $body .= "<br><br>";

            $body .= "<a target='_blank' href='".get_admin_url()."admin.php?page=migla_reports_page&start_date=".date('m')."/".date('d')."/".date('Y')."&end_date&rep=yes'>".__("for more details, click here","migla-donation")."</a>";

            $status = wp_mail( $email, $subject, $body, $header );

            if( $status ){
            }else{
                $status = mail( $email, $subject, $body, $header );
            }

            return $status;
        }

        public function send_change_notification_mail( $email, $data, $language, $extra )
        {
            if(isset($data['miglad_form_id'])){
                $form_id = $data['miglad_form_id'];
            }else{
                $form_id = 0;
            }

            $mydata = $this->get_email_by_idlanguage($form_id, $language);

            $header = $this->set_header( $mydata['reply_to'], $mydata['reply_to_name'] );

            $subject = $extra['subject'];

            $donor      = "";
            $donor_email= "";
            $amount     = "";
            $campaign   = "";

            $body = $extra['content']."<br><table><tbody>";

            if( isset($data['miglad_firstname']) && !empty($data['miglad_firstname']) )
            {
                $donor .= $data['miglad_firstname'];

                if( isset($data['miglad_lastname']) && !empty($data['miglad_lastname']) )
                {
                    $donor .= ' '.$data['miglad_lastname'];

                }

                $body .= "<tr><td>"."Donor:"."</td><td>".$donor."</td></tr>";
            }

            if( isset($data['miglad_amount']) && !empty($data['miglad_amount']) )
            {
                $amount .= $data['miglad_amount'];
                $body .= "<tr><td>"."Amount:"."</td><td>".$amount."</td></tr>";
            }

            if(isset($data['miglad_campaign']))
            {
                $campaign = $cmp_name = $this->get_campaign_name( $data['miglad_campaign'] );
                $body .= "<tr><td>"."Campaign:"."</td><td>".$campaign."</td></tr>";
            }

            if(isset($data['miglad_gateway']))
            {
                $body .= "<tr><td>"."Gateway:"."</td><td>". $data['miglad_gateway'] ."</td></tr>";
            }

            if(isset($data['miglad_email']))
            {
                $donor_email= $data['miglad_email'];
                $body .= "<tr><td>"."Email:"."</td><td>".$donor_email."</td></tr>";
            }

            if(isset($data['miglad_address']))
            {
                $address = $data['miglad_address'];
                $body .= "<tr><td>"."Address:"."</td><td>".$address."</td></tr>";
            }

            $sdate = '';
            if(isset($data['date_created']))
            {
                $dates = explode(" ",$data['date_created']);

                if(isset($dates[0])){
                    $array_dates = explode("-", $dates[0]);
                    $sdate = $array_dates['1'] . '/' . $array_dates['2'] . '/' . $array_dates['0'];
                }
            }

            $body .= "</tbody></table>";
            $body .= "<br><br>";

            $body .= "<a target='_blank' href='".get_admin_url()."admin.php?page=migla_reports_page&start_date=".$sdate."&end_date".$sdate."&rep=yes&p=all'>".__("for more details, click here","migla-donation")."</a>";

            $status = wp_mail( $email, $subject, $body, $header );

            if( $status ){
            }else{
                $status = mail( $email, $subject, $body, $header );
            }

            return $status;
        }

        public function test_mail( $form_id, $language, $email )
        {
            $mydata = $this->get_email_by_idlanguage(0, get_locale());

            $isRepeat = 'yes';
            $isAnon = 'yes';
            $status ='test-';

            $amount = $this->format_amount( 10000, 2, '' );

            $content = $this->get_the_body( 
                                'Jane',
                                'Doe',
                                $amount,
                                date(get_option('date_format')),
                                $mydata['body'],
                                $mydata['repeating'],
                                $mydata['anonymous'],
                                0,
                                'no',
                                'yes',
                                get_locale()
                             );


            $header = $this->set_header( $mydata['reply_to'], $mydata['reply_to_name'] );

            $subject = $this->replace_code( $mydata['subject'],
                                            'Jane',
                                            'Doe',
                                            $amount,
                                            0 );
            $objO = new MIGLA_OPTION;
            $use_PHPMailer = $objO->get_option('migla_use_PHPMailer') == 'yes';
            $status = true;

            if( $use_PHPMailer )
            {
                $status = $this->send_with_PHPMailer( $mydata['reply_to'],
                                                $mydata['reply_to_name'],
                                                false,
                                                '',
                                                $subject,
                                                $content,
                                                $email,
                                                'Jane Doe'
                                            );
            }else{
                $status = wp_mail( $email, $subject, $content, $header );

                if( $status ){

                }else{
                    $status = mail( $email, $subject, $content, $header );
                }
            }

            $NotifEmails = $this->get_column( $form_id, 'notify_emails' );

            if(!empty($NotifEmails))
            {
                $date = date(get_option('date_format'));

                $donor_data = array( 'miglad_firstname' => 'John', //0
                                'miglad_lastname'   => 'Doe', //1
                                'miglad_amount'     => $amount, //8
                                'miglad_email'      => 'john.doe@noname.com', //9
                                'miglad_campaign_name' => 'Save This Earth', //10
                                 'miglad_campaign' => 0, //10
                                 'miglad_date' => $date
                                );

                $emails = (array)unserialize($NotifEmails);

                foreach( $emails as $nf ){
                    $this->send_notification_mail( $nf, $donor_data, $language );
               }
            }

      		return $status;
    	}

	}//END OF CLASS
}
