<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'migla_form_creator' ) )
{
    class migla_form_creator
    {
       public $FORM_ID;
       public $FORM_LANGUAGE;
       public $FORM_FIELDS;
       public $FORM_STRUCTURE;
       public $FORM_META;
       public $FORM;
       public $SESSION;

       public $CONTENT;
       public $FORM_TYPE;

       public $TYPE_AMOUNT_BTN ;
       public $AMOUNTS;
       public $CUSTOM_AMOUNT_TEXT;
       public $HIDE_CUSTOM_AMOUNT;
       public $WARNING_MESSAGE1;
       public $WARNING_MESSAGE2;
       public $WARNING_MESSAGE3;
       public $GATEWAYS;

       public $CAMPAIGN;
       public $CAMPAIGN_ID;
       public $THE_POST;

       public $USE_RECAPTCHA;

       public static $IS_AFORM_EXIST;
       public static $AFORM_EXIST;

       public $DEBUG_MODE;
       public $IS_MULTILANGUAGE;
       public $METHOD;

       public $OPTIONS;
       public $MONEY;
       public $GEOGRAPHY;
       public $BUTTON_CLASS;
       public $IS_PAYPAL;
       public $IS_STRIPE;

        public function __construct( $formid, $session, $cmpid )
        {
            $this->CONTENT    = '';
            $this->FORM_ID    = $formid;
            $this->CAMPAIGN_ID= $cmpid;

            $this->OPTIONS    = new MIGLA_OPTION;
            $this->MONEY      = new MIGLA_MONEY;
            $this->GEOGRAPHY  = new MIGLA_GEOGRAPHY;

            $this->BUTTON_CLASS = "miglacheckout";

            $this->FORM_LANGUAGE  = get_locale();
            $this->SESSION = $session;

            $this->IS_PAYPAL = false;
            $this->IS_STRIPE = false;
        }

        public function trim_sql_xss( $string )
        {
            $safeout = str_replace("'"," ", $string );
            $safeout = htmlspecialchars( $safeout );
            $safeout = strip_tags( $safeout );

            return $safeout;
        }

        public function get_date( $isfull,  $inputdate )
        {

            $default      = $this->OPTIONS->get_option('migla_default_timezone');
            $language     = $this->OPTIONS->get_option('migla_default_datelanguage');
            $date_format  = $this->OPTIONS->get_option('migla_default_dateformat');
            $php_time_zone  = date_default_timezone_get();

            if( $language == false || $language == '' )
              $language = 'en.UTF-8';

            if( $date_format == false || $date_format == '' )
              $date_format = '%B %d %Y' ;

            setlocale(LC_TIME, $language );
            $my_locale = get_locale();

            if( $default == 'Server Time' )
            {
                $gmt_offset = -$this->OPTIONS->get_option( 'gmt_offset' );

                if ($gmt_offset > 0)
                    $time_zone = 'Etc/GMT+' . $gmt_offset;
                else
                    $time_zone = 'Etc/GMT' . $gmt_offset;

                date_default_timezone_set( $time_zone );

            }else
            {
                  date_default_timezone_set( $default );
            }

            $t = date('H:i:s');

            if( $inputdate == '' )
                $d = date('m')."/".date('d')."/".date('Y');
            else
                $d = $inputdate ;

            if( $isfull )
                  $now =  strftime( $date_format , date(strtotime($d)) ) . " " . $t ;
            else
                  $now =  strftime( $date_format , date(strtotime($d)) ) ;

            date_default_timezone_set( $php_time_zone );

            return $now;
        }

        public function get_form_meta( $frm, $key )
        {
            global $wpdb;

            $sql = "select meta_value from {$wpdb->prefix}migla_form_meta";
            $sql .= " where form_id = " . $frm ;
            $sql .= " AND meta_key = '".$key."'";

            $res = $wpdb->get_var($sql);
            return $res;
        }

        public function draw_form()
        {
            $this->FORM = new CLASS_MIGLA_FORM;

            if( $this->FORM->if_exist( $this->FORM_ID ) )
            {
                $formInfo = $this->FORM->get_info( $this->FORM_ID,  $this->FORM_LANGUAGE );

                $this->FORM_META = $formInfo;

                $this->FORM_FIELDS = $formInfo['fields'] ;
                $this->FORM_STRUCTURE = $formInfo['structure'] ;

                $this->TYPE_AMOUNT_BTN    = $formInfo["buttonType"];
                $this->HIDE_CUSTOM_AMOUNT = $formInfo["hideCustomAmount"];
                $this->AMOUNTS            = $formInfo['amounts'];
                $this->CUSTOM_AMOUNT_TEXT = $formInfo['custom_amount_text'];
                $this->TYPE_AMOUNT_BOX    = $formInfo["amountBoxType"];

                $this->WARNING_MESSAGE1 = $formInfo['warning_1'];
                $this->WARNING_MESSAGE2 = $formInfo['warning_2'];
                $this->WARNING_MESSAGE3 = $formInfo['warning_3'];

                foreach( $formInfo as $key => $info )
                {
                    if( strpos( $key, "_tab_info" ) !== false )
                    {
                        $this->GATEWAYS[$key] = (array)unserialize($info);
                    }else if(  strpos( $key, "_panel_info" ) !== false ){
                        $this->GATEWAYS[$key] = $info;
                    }
                }

                $paypal     = $this->FORM->paypal_tab($this->GATEWAYS['paypal_tab_info']);
                $stripe     = $this->FORM->stripe_tab($this->GATEWAYS['stripe_tab_info']);

                $this->GATEWAYS['paypal_tab_info'] = $paypal;
                $this->GATEWAYS['stripe_tab_info'] = $stripe;
                ?>

				<div style="clear:both;" class="bootstrap-wrapper">
					<div id="wrap-migla">
						<div id="migla_donation_form-<?php echo $this->FORM_ID ;?>">
						<div  class="wrapper-migla-overlay">
                        <?php

                        $bgclor2nd        = explode("," , $this->OPTIONS->get_option('migla_2ndbgcolor') );
                        $border           = explode(",", $this->OPTIONS->get_option('migla_2ndbgcolorb') );
                        $borderCSS        = "border: ".$border[2]."px solid ".$border[0].";";
                        $bglevelcolor     = $this->OPTIONS->get_option('migla_bglevelcolor');

                        $this->form_fields( $bgclor2nd , $border, $borderCSS, $bglevelcolor );

                        $this->gateway_tabs( '', $bgclor2nd[0], $borderCSS, $bglevelcolor );
					    ?>
					    </div>
					    <?php

                        $objRd = new MIGLA_REDIRECT;
                        $redirect = $objRd->get_info( $this->FORM_ID, get_locale() );
                        $url = get_permalink($redirect['pageid']);

                        if( empty($url) )
                        {
                            global $wp;
                            $url = home_url(add_query_arg(array(),$wp->request));
                        }

                        $this->migla_modal_box();
                        ?>
                        <form id="migla_to_thankyou_page-<?php echo $this->FORM_ID;?>" class="thankyou_url" action="<?php echo $url;?>">
                          <input type='hidden' name='thanks' value='thanks'>
                          <input type='hidden' name='pid' value=''>
                        </form>
					    </div>

					    <?php
					    if( $this->IS_PAYPAL ){
					       $this->migla_hidden_form( $this->FORM_ID ); 
					    }
					    
					    $this->hidden_input();
					    ?>

					</div>
				</div>
                <?php
            }else{
                echo "Form not found";
            }
        }

        public function hidden_input()
        {
        ?>
            <input type='hidden' id='migla_language' value="<?php echo esc_attr(get_locale());?>">
            <input type='hidden' id='migla_paypal_fec' value="<?php echo esc_attr($this->OPTIONS->get_option('migla_paypal_fec'));?>">
            <input type='hidden' id='migla_form_id' value="<?php echo esc_attr($this->FORM_ID);?>">
            
            <input type='hidden' id='miglaErrorCount' value="">
            
        <?php
            global $wpdb;

            $objG = new MIGLA_GEOGRAPHY;

            $sql = "select pageid from {$wpdb->prefix}migla_redirect where";
            $sql .= " language = %s";

            $redirect_id = $wpdb->get_var( $wpdb->prepare( $sql, $this->FORM_LANGUAGE ) ) ;

            if( $redirect_id == '' )
            {
                $page_id = $this->OPTIONS->get_option('migla_thank_you_page');
            }else{
                $page_id = $redirect_id;
            }

            if( $page_id  == '' || $page_id  == false )
            {
                global $wp;
                ?>
                <input type='hidden' id='migla_thankyou_url' value='<?php echo esc_attr(home_url(add_query_arg(array(),$wp->request)));?>'>
                <?php
            }else{
                ?>
                <input type='hidden' id='migla_thankyou_url' value='<?php echo esc_attr(get_permalink( $page_id ));?>'>
                <?php
            }  ?>

                <input type='hidden' id='miglaShowDecimal' value='<?php echo esc_attr($this->OPTIONS->get_option('migla_showDecimalSep'));?>'>
                <input type='hidden' id='miglaDecimalSep' value='<?php echo esc_attr($this->OPTIONS->get_option('migla_decimalSep'));?>'>
                <input type='hidden' id='miglaThousandSep' value='<?php echo esc_attr($this->OPTIONS->get_option('migla_thousandSep'));?>'>
                <input type='hidden' id='miglaDefaultCurrency' value='<?php echo esc_attr($this->OPTIONS->get_option('migla_default_currency'));?>'>
                <input type='hidden' id='miglaMinAmount' value='<?php echo esc_attr($this->OPTIONS->get_option('migla_min_amount'));?>'>
            <?php
                $countries = $objG->get_countries();
                $idx = 1;
                ?>
                    <ul id="mg_country_codes_list" style="display:none!important">
               <?php
                foreach($countries as $key => $value )
                {
                    ?>
                     <li class="mg_country_li" id="mg_country<?php echo esc_attr($idx);?>" name="<?php echo esc_attr($value);?>">
                        <?php echo esc_attr($key);?>
                        </li>
                <?php
                    $idx++;
                }
                ?></ul><?php

                $states = $objG->get_USA_states();
                $idx = 1;

                ?><ul id="mg_state_codes_list" style="display:none!important"><?php
                foreach($states as $key => $value )
                {
                    ?><li class='mg_state_li' id='mg_state<?php echo esc_attr($idx);?>' name='<?php echo esc_attr($value);?>'>
                        <?php echo esc_attr($key);?></li><?php
                    $idx++;
                }
                ?></ul><?php

                $province = $objG->get_CA_provinces();
                $idx = 1;

                 ?><ul id="mg_province_codes_list" style="display:none!important"><?php
                foreach($province as $key => $value )
                {
                     ?><li class='mg_province_li' id='mg_province<?php echo esc_attr($idx);?>' name='<?php echo esc_attr($value);?>'><?php echo esc_attr($key);?></li><?php
                    $idx++;
                }
                ?></ul><?php

        }

        public function form_fields( $bgclor2nd , $border, $borderCSS, $bglevelcolor )
        {
            $index = 0;

            $frm = new CLASS_MIGLA_FORM;
            $FormStructure = $frm->translate_form( $this->FORM_STRUCTURE,
                                                    $this->FORM_FIELDS );

            $sect_num = 1;    

            foreach ( $FormStructure as $sections )
            {
                $hasChildren = false;
                
                if( isset($sections['child']) ){
                    $children = $sections['child'];
                    
                    foreach ( $children as $child )
                    {
                        if( strcmp( $child['status'], '0' ) == 0)
                        {
                            
                        }else{
                            //Minimal 1 field children
                            $hasChildren = true;
                        }
                    }
                }
                
                if($hasChildren)
                {
                    $the_title = $sections['title'];

                    $lbl = str_replace("[q]","'", $the_title );

                    $classtitle = str_replace(" ","", $the_title );
                    $classtitle = "mg_".$classtitle ;

                    if( $sections['toggle'] == '1' ) //Check the toggle
                    {
                   ?>
                      <section class='migla-panel' style='background-color:<?php echo esc_attr($bgclor2nd[0].";".$borderCSS);?>' >
                        <header class='migla-panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                            <label for="mg_sect-<?php echo $sect_num;?>" class="mg_section-title-label"><h2 class='<?php echo esc_attr($classtitle);?>'> <?php echo esc_attr($lbl) ;?>
                                <input type='checkbox' class='mtoggle' checked='true' id="mg_sect-<?php echo $sect_num;?>" />
                            </h2></label>
                        </header>

                        <div class='migla-panel-body form-horizontal' style='display:none'>
                      <?php
                    }else{
                      ?>
                      <section class='migla-panel' style='background-color:<?php echo esc_attr($bgclor2nd[0].";".$borderCSS);?>' >
                         <header class='migla-panel-heading'>
                            <label><h2 class='<?php echo esc_attr($classtitle);?>'> <?php echo esc_attr($lbl);?> </h2>
                                    </header></label>
                            <div class='migla-panel-body form-horizontal' >
                      <?php
                        }
                        //COntents

                    if( isset($sections['child']) ){
                        $children = $sections['child'];
                        foreach ( $children as $child )
                        {
                            $write_label = $child['label'];

                            if( strcmp( $child['status'], '0' ) == 0)
                            { //as long as they are not shown

                                if ( strcmp( $child['id'], 'campaign' ) == 0 )
                                {
                                    //setting with hide campaign dropdown
                                    $this->migla_onecampaign_section( $this->OPTIONS->get_option('migla_selectedCampaign'),  $write_label , 0 );
                                }
                            }else if ( $child['id'] == 'repeating' || $child['id'] == 'mg_add_to_milist' )
                            {

                            }else{

                                $req = '';

                                if( $child['status'] >= 2 )
                                {
                                    $req = 'required';
                                } //is it mandatory ?

                                if( strcmp( $child['id'], 'amount' ) == 0 )
                                {
                                    $this->migla_amount( $write_label, $this->FORM_ID , $this->CUSTOM_AMOUNT_TEXT );

                                }else if ( strcmp( $child['id'], 'campaign' ) == 0 )
                                {
                                        $postcampaign = $this->CAMPAIGN_ID;

                                        if( $this->FORM_ID == '0' || $this->FORM_ID == '' )
                                        {
                                            $this->migla_campaign_section( $postcampaign, $write_label );
                                        }else{

                                            $this->migla_onecampaign_section( $postcampaign ,  $write_label , 0 );
                                        }

                                }else if( strcmp( $child['id'], 'country' ) == 0  )
                                {
                                    if( !empty( $this->FORM->get_meta( $this->FORM_ID, '#state', $this->FORM_LANGUAGE ) ) )
                                    {
                                        $state_tr = $this->FORM->get_meta( $this->FORM_ID, '#state', $this->FORM_LANGUAGE );
                                    }else if( isset($map['state']) )
                                    {
                                        $state_tr = $map['state'];
                                    }else{
                                        $state_tr = 'State';
                                    }

                                    if( !empty( $this->FORM->get_meta( $this->FORM_ID, '#province', $this->FORM_LANGUAGE ) ) )
                                    {
                                        $province_tr = $this->FORM->get_meta( $this->FORM_ID, '#province', $this->FORM_LANGUAGE );
                                    }else if( isset($map['province']) )
                                    {
                                        $province_tr = $map['province'];
                                    }else{
                                        $province_tr = 'Province';
                                    }

                                    $this->mg_makeInputCountry( $child['id'], $write_label, $child['type'], $child['code'], '', $req, $state_tr, $province_tr );

                                }else if(  strcmp( $child['id'], 'honoreecountry' ) == 0 )
                                {
                                    if( isset($map['state']) )
                                        $state_tr = $map['state'];
                                    else
                                        $state_tr = 'State';

                                    if( isset($map['state']) )
                                        $province_tr = $map['province'];
                                    else
                                        $province_tr = 'Province';

                                    $this->mg_makeInputCountry( $child['id'], $write_label, $child['type'], $child['code'], '', $req, $state_tr, $province_tr );

                                }else if(  strcmp( $child['id'], 'repeating' ) == 0 ){

                                    $this->mg_makeRepeatingTag( $child['id'],
                                                                $write_label,
                                                                $child['type'],
                                                                $child['code'],
                                                                $req );

                                }else if(strcmp( $child['id'], 'anonymous' ) == 0){

                                    $this->mg_anonymous_check( $child['id'], $write_label, $child['type'], $child['code'], "", $req , $child['uid'] );

                                }else{ //not special field

                                    if( $child['type'] == 'text' )
                                    {

                                        $this->mg_makeInputTextTag( $child['id'], $write_label, $child['type'], $child['code'], "", $req, "" , $child['uid'] );


                                    }else if ( $child['type'] == 'checkbox' ){

                                        $this->mg_makeInputCheckTag( $child['id'], $write_label, $child['type'], $child['code'], "", $req , $child['uid'] );

                                    }else if ( $child['type'] == 'textarea' ){

                                        $this->mg_makeInputTextareaTag( $child['id'], $write_label, $child['type'], $child['code'], "", $req , $child['uid']);

                                    }else if( $child['type'] == 'searchable_select' ){

                                        $this->mg_makeInputSearchableDropDownTag( $child['id'], $write_label, $child['type'], $child['code'], "", $req, 0, $child['uid'] );

                                    }else if( $child['type'] == 'select' ){

                                        $this->mg_makeInputDropDownTag( $child['id'], $write_label, $child['type'], $child['code'], "", $req, 0, $child['uid'] );

                                    }else if( $child['type'] == 'radio' ){

                                        $this->mg_makeInputRadioTag( $child['id'], $write_label, $child['type'], $child['code'], "", $req, $child['uid'] );

                                    }else if( $child['type'] == 'multiplecheckbox' ){

                                        $this->mg_makeInputMultiCheckboxTag( $child['id'], $write_label, $child['type'], $child['code'], "", $req, $child['uid'] );
                                    }

                                }//if check who is special
                            }//showb
                        }//children
                    }

                    ?>
                    </div>
                </section>
                <?php
                }//If Has children

                $sect_num++;
            }//foreach sections
        }

        public function migla_modal_box()
        {
            ?>
            <div style='display:none'>
                <div id='mg_warning1'><?php echo esc_attr($this->WARNING_MESSAGE1);?></div>
                <div id='mg_warning2'><?php echo  esc_attr($this->WARNING_MESSAGE2);?></div>
                <div id='mg_warning3'><?php echo  esc_attr($this->WARNING_MESSAGE3);?></div>
            </div>
          <?php
        }

        public function load_tab_content( $id , $bgcolor, $borderCSS )
        {
           if( $id == 'stripe' )
           {
                $this->gateway_stripe();
           }else if( $id == 'paypal' )
           {
                $this->gateway_paypal( true, $bgcolor, $borderCSS );
           }
        }

        /*** Functions to decode the input ***/

        /** single **/
        function mg_makeInputTextTag( $id, $label, $type, $code, $filter, $req, $col, $uid )
        {
            $lbl = str_replace("[q]","'",$label);
            $show_id = '';

            if( strcmp($req, 'required') == 0 )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $mandatory_warning = "<input type='hidden' class='idfield_label' value='".esc_attr($label)."'>";
                $class_mandatory    = "migla_rdiv_field_mandatory";
            }else{
                $info = "";
                $mandatory_warning = "";
                $class_mandatory    = "";
            }

            if( $code == 'miglac_' )
            {
                $show_id =  $uid;
            }else{
                $show_id =  $code.$id;
            }

           ?>
           <div class='form-group'>
           <div class='col-sm-3'>
           <div class='input-group input-group-icon'>
           <label class='mg_control-label'>
            <?php
            echo esc_attr($lbl). $info;
            ?>
           </label>

           </div>
           </div>

           <div class='col-sm-6 col-xs-12 migla_rdiv_field <?php echo esc_attr($class_mandatory);?>'>
            <?php  echo $mandatory_warning; ?>
            <input type='hidden' class='idfield_type' value='textbox'>
            <input type="hidden" class='idfield' value="<?php echo esc_attr($show_id);?>">
            <input type="hidden" class='idfield_key' value="<?php echo esc_attr($show_id);?>">
            <input type="<?php echo  esc_attr($type);?>" autocomplete="off" name="<?php echo esc_attr($show_id);?>" id="<?php echo esc_attr($show_id);?>" placeholder="<?php echo esc_attr($lbl);?>" class='mg_form-control miglaNumAZ <?php echo esc_attr($code." ".$req);?>'/>
           </div>
           <div class='col-sm-3 hidden-xs'></div>
           </div>
           <?php
        }

        function mg_makeInputTextareaTag( $id, $label, $type, $code, $filter, $req, $uid )
        {
            if( strcmp($req, 'required') == 0 )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $mandatory_warning = "<input type='hidden' class='idfield_label' value='".esc_attr($label)."'>";
                $class_mandatory    = "migla_rdiv_field_mandatory";
            }else{
                $info = "";
                $mandatory_warning = "";
                $class_mandatory    = "";
            }

            $lbl = str_replace("[q]","'",$label);
            $show_id = '';

            if( $code == 'miglac_' )
            {
                $show_id =  $uid;
            }else{
                $show_id =  $code.$id;
            }
           ?>

           <div class='form-group'>
           <div class='col-sm-3'>
               <div class='input-group input-group-icon'><label class='mg_control-label'><?php echo esc_attr($lbl). $info;?></label></div>
           </div>

            <div class='col-sm-6 col-xs-12 migla_rdiv_field <?php echo esc_attr($class_mandatory);?>'>
                <?php echo $mandatory_warning; ?>
                <input type='hidden' class='idfield_type' value='textarea'>
                <input type="hidden" class='idfield' value="<?php echo esc_attr($show_id);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($show_id);?>">

               <textarea id='<?php echo esc_attr($show_id);?>' name="<?php echo esc_attr($show_id);?>" class='mg_form-control <?php echo esc_attr($code." ".$req);?>  miglaNumAZ'></textarea>
           </div>
           <div class='col-sm-3 hidden-xs'></div>
           </div>
        <?php
        }

        function mg_makeInputCheckTag( $id, $label, $type, $code, $filter, $req , $uid)
        {
            $lbl = str_replace("[q]","'",$label);

            if( strcmp($req, 'required') == 0 )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $mandatory_warning = "<input type='hidden' class='idfield_label' value='".esc_attr($lbl)."'>";
                $class_mandatory    = "migla_rdiv_field_mandatory";
            }else{
                $info = "";
                $mandatory_warning = "";
                $class_mandatory    = "";
            }

            $show_id = '';

            if( $code == 'miglac_' )
            {
                $show_id =  $uid;
            }else{
                $show_id =  $code.$id;
            }
           ?>

            <div class='form-group'>
               <div class='col-sm-3'>
                   <div class='input-group input-group-icon'><label class='mg_control-label'><?php echo esc_attr($lbl).$info;?></label></div>
               </div>

               <div class='col-sm-6 col-xs-12 migla_rdiv_field <?php echo esc_attr($class_mandatory);?>'>
                <?php echo $mandatory_warning; ?>

                <input type='hidden' class='idfield_type' value='checkbox'>
                <input type="hidden" class='idfield idfield_checkbox' value="<?php echo esc_attr($show_id);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($show_id);?>">

                <div class='checkbox'>
                    <label for='<?php echo esc_attr($uid);?>'>
                        <input type='checkbox' name="<?php echo esc_attr($show_id);?>" id='<?php echo esc_attr($show_id);?>' class='check-control <?php echo esc_attr($code." ".$req);?>' value='yes'/>
                    </label>
                </div>
               </div>
               <div class='col-sm-3 hidden-xs'></div>
            </div>
        <?php
        }

        /*Multi List*/
        function mg_makeInputDropDownTag( $id, $label, $type, $code, $filter, $req, $post_id, $uid )
        {
           $info        = "";
           $first_text  = "<option value=''>".__("Please choose one", "migla-donation")."</option>";
           $options     = array();

            if( strcmp($req, 'required') == 0 )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $mandatory_warning = "<input type='hidden' class='idfield_label' value='".esc_attr($label)."'>";
                $class_mandatory    = "migla_rdiv_field_mandatory";
                 $first_text = '';
            }else{
                $info = "";
                $mandatory_warning = "";
                $class_mandatory    = "";
            }

           $lbl = str_replace("[q]","'",$label);

           ?>
           <div class='form-group'>
            <div class='col-sm-3'>
                <div class='input-group input-group-icon'>
                    <label class='mg_control-label  '><?php echo esc_attr($lbl).$info;?></label>
                </div>
            </div>

           <div class='col-sm-6 col-xs-12 migla_rdiv_field <?php echo esc_attr($class_mandatory);?>'>
            <?php  echo $mandatory_warning;?>
            <input type='hidden' class='idfield_type' value='dropdown'>
        <?php    
            if( $code == 'miglac_' )
            {
        ?>
                <input type="hidden" class='idfield' value="<?php echo esc_attr($uid);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($uid);?>">
        <?php
            }else{
        ?>
                <input type="hidden" class='idfield' value="<?php echo esc_attr($code.$id);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($code.$id);?>">
        <?php
            }

            $data = array();

            if( isset( $this->FORM_META[("#".$uid)] ) ){
                $data = (array)unserialize($this->FORM_META[("#".$uid)]);
            }

            $name_radio = $uid;
            $count = 0;

            if( !empty($data) && count($data)>0 )
            {
            ?>
                <select class='mg_form-control' id='<?php echo esc_attr($uid);?>'  name='<?php echo esc_attr($uid);?>'>
                        <?php echo esc_attr($first_text);

                        foreach($data as $datum)
                        {
                            $Lval = "";
                            $Llbl = "";

                            if(isset($datum['lVal'])){
                                $Lval = $datum['lVal'];
                            }

                            if(isset($datum['lLbl'])){
                                $Llbl = $datum['lLbl'];
                            }

                            ?><option value='<?php echo esc_attr($Lval);?>'>
                                <?php echo esc_attr($Llbl);?>
                            </option><?php
                          }
            ?>  </select>
            <?php
            }
            ?>
            </div>
                <div class='col-sm-3 hidden-xs'></div>
            </div>
        <?php
        }

        function mg_makeInputSearchableDropDownTag( $id, $label, $type, $code, $filter, $req, $post_id, $uid )
        {
           $info        = "";
           $first_text  = "<option value=''>". esc_attr(__("Please choose one", "migla-donation"))."</option>";
           $options     = array();

            if( strcmp($req, 'required') == 0 )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $mandatory_warning = "<input type='hidden' class='idfield_label' value='".esc_attr($label)."'>";
                $class_mandatory    = "migla_rdiv_field_mandatory";
                 $first_text = '';
            }else{
                $info = "";
                $mandatory_warning = "";
                $class_mandatory    = "";
            }

           $lbl = str_replace("[q]","'",$label);

           ?>
           <div class='form-group'>
            <div class='col-sm-3'>
            <div class='input-group input-group-icon'>
                <label class='mg_control-label  '><?php echo esc_attr($lbl).$info;?></label>
            </div>
            </div>

           <div class='col-sm-6 col-xs-12 migla_rdiv_field <?php echo esc_attr($class_mandatory);?>'>
            <?php  echo $mandatory_warning; ?>
            <input type='hidden' class='idfield_type' value='searchabledropdown'>
        <?php    
            if( $code == 'miglac_' )
            {
        ?>
                <input type="hidden" class='idfield' value="<?php echo esc_attr($uid);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($uid);?>">
        <?php
            }else{
        ?>
                <input type="hidden" class='idfield' value="<?php echo esc_attr($code.$id);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($code.$id);?>">
        <?php
            }

            $data = array();

            if( isset( $this->FORM_META[("#".$uid)] ) ){
                $data = (array)unserialize($this->FORM_META[("#".$uid)]);
            }

            $name_radio = $uid;
            $count = 0;

            if( !empty($data) && count($data)>0 )
            {
            ?>
            <select class='mg_form-control migla_select2' id='<?php echo esc_attr($uid);?>'  name='<?php echo esc_attr($uid);?>'>
            <?php echo esc_attr($first_text);
                foreach($data as $datum)
                {
                    $Lval = "";
                    $Llbl = "";

                    if(isset($datum['lVal'])){
                        $Lval = $datum['lVal'];
                    }

                    if(isset($datum['lLbl'])){
                        $Llbl = $datum['lLbl'];
                    }

                    ?><option value='<?php echo esc_attr($Lval);?>'>
                        <?php echo esc_attr($Llbl);?>
                    </option><?php
                  }
            ?></select>
            <?php
            }
            ?>
            </div>
            <div class='col-sm-3 hidden-xs'></div>
        </div>
        <?php
        }

        function mg_makeInputRadioTag( $id, $label, $type, $code, $filter, $req, $uid )
        {
           $lbl = str_replace("[q]","'",$label);
           $first_text = '';

            if( strcmp($req, 'required') == 0 )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $mandatory_warning = "<input type='hidden' class='idfield_label' value='".esc_attr($label)."'>";
                $class_mandatory    = "migla_rdiv_field_mandatory";
                 $first_text = '';
            }else{
                $info = "";
                $mandatory_warning = "";
                $class_mandatory    = "";
            }

            $show_id = '';

            if( $code == 'miglac_' )
            {
                $show_id =  $uid;
            }else{
                $show_id =  $code.$id;
            }

            ?>
            <div class='form-group'>
            <div class='col-sm-3'>
                <div class='input-group input-group-icon'>
                    <label class='mg_control-label'><?php echo esc_attr($lbl). $info; ?></label>
                </div>
           </div>

           <div class='col-sm-6 col-xs-12 migla_rdiv_field <?php echo esc_attr($class_mandatory);?>'>
            <?php  echo $mandatory_warning;  ?>
            <input type='hidden' class='idfield_type' value='radiobutton'>

            <input type="hidden" class='idfield idfield_radio' value="<?php echo esc_attr($show_id);?>">
            <input type="hidden" class='idfield_key' value="<?php echo esc_attr($show_id);?>">

        <?php
           $bglevelcolor = $this->OPTIONS->get_option('migla_bglevelcolor');
           $borderlevelcolor = $this->OPTIONS->get_option('migla_borderlevelcolor');
           $borderlevel = $this->OPTIONS->get_option('migla_borderlevel');
           $borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";";

            $data = array();

            if( isset( $this->FORM_META[("#".$uid)] ) ){
                $data = (array)unserialize($this->FORM_META[("#".$uid)]);
            }

            $name_radio = $uid;
            $count = 0;

            if( !empty($data) && count($data)>0 ){
                foreach($data as $datum)
                {
                    $Lval = "";
                    $Llbl = "";

                    if(isset($datum['lVal'])){
                        $Lval = $datum['lVal'];
                    }

                    if(isset($datum['lLbl'])){
                        $Llbl = $datum['lLbl'];
                        $Llbl = str_replace("\'","'",$Llbl);
                        $Llbl = str_replace("\"",'"',$Llbl);
                    }
                ?>
                    <div class='radio' id='<?php echo esc_attr($uid.$count);?>'  >
                        <label>
                          <input type='radio' <?php if($count==0) echo esc_attr("checked"); ?> value='<?php echo esc_attr($Lval);?>' id='<?php echo esc_attr($name_radio.$count);?>' name='<?php echo esc_attr($name_radio);?>' />
                                <?php echo esc_attr($Llbl);?>
                        </label>
                    </div>

                    <?php
                    $count++;
                }
            }

           ?></div>
           <div class='col-sm-3 hidden-xs'></div>
           </div>
        <?php
        }

        function mg_makeInputMultiCheckboxTag( $id, $label, $type, $code, $filter, $req, $uid )
        {
           $lbl = str_replace("[q]","'",$label);
           $first_text = '';

            if( strcmp($req, 'required') == 0 )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $mandatory_warning = "<input type='hidden' class='idfield_label' value='".esc_attr($label)."'>";
                $class_mandatory    = "migla_rdiv_field_mandatory";
                 $first_text = '';
            }else{
                $info = "";
                $mandatory_warning = "";
                $class_mandatory    = "";
            }

           ?><div class='form-group'>
            <div class='col-sm-3'>
                <div class='input-group input-group-icon'>
                <label class='mg_control-label  '><?php echo esc_attr($lbl). $info;?></label>
                </div>
            </div>
           <div class='col-sm-6 col-xs-12 migla_rdiv_field <?php echo esc_attr($class_mandatory);?>'>
            <?php echo $mandatory_warning;  ?>
            <input type='hidden' class='idfield_type' value='multicheckbox'>
        <?php    
            if( $code == 'miglac_' )
            {
        ?>
                <input type="hidden" class='idfield idfield_multicheckbox' value="<?php echo esc_attr($uid);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($uid);?>">
        <?php
            }else{
        ?>
                <input type="hidden" class='idfield idfield_multicheckbox' value="<?php echo esc_attr($code.$id);?>">
                <input type="hidden" class='idfield_key' value="<?php echo esc_attr($code.$id);?>">
        <?php
            }
        ?>

        <?php
           $bglevelcolor = $this->OPTIONS->get_option('migla_bglevelcolor');
           $borderlevelcolor = $this->OPTIONS->get_option('migla_borderlevelcolor');
           $borderlevel = $this->OPTIONS->get_option('migla_borderlevel');
           $borderCSS = "border: ".$borderlevel."px solid ".$borderlevelcolor.";";

            $data = array();

            if( isset( $this->FORM_META[("#".$uid)] ) ){
                $data = (array)unserialize($this->FORM_META[("#".$uid)]);
            }

            $name_radio = $uid;
            $count = 0;

            if( !empty($data) && count($data)>0 ){
                foreach($data as $datum)
                {
                    $Lval = "";
                    $Llbl = "";

                    if(isset($datum['lVal'])){
                        $Lval = $datum['lVal'];
                    }

                    if(isset($datum['lLbl'])){
                        $Llbl = $datum['lLbl'];
                        $Llbl = str_replace("\'","'",$Llbl);
                        $Llbl = str_replace("\"",'"',$Llbl);
                    }                    
                ?>
                <div class='checkbox' id='<?php echo esc_attr($uid.$count);?>' >
                   <label>
                    <input type='checkbox' class='<?php echo esc_attr($req);?>' id='<?php echo esc_attr($uid.$count);?>' name='<?php echo esc_attr($name_radio);?>' value='<?php echo esc_attr($Lval);?>'><?php echo esc_attr($Llbl);?>
                    </label>
               </div>
               <?php
                }
            }

          ?></div>
           <div class='col-sm-3 hidden-xs'></div></div>
        <?php
        }

        /*Special Fields Repeating, Anonymous, Amount, Country, State, Province, Campaign down here*/
        function mg_makeRepeatingTag( $id, $label, $type, $code, $req)
        {
            //Nothinf to do
        }

        function mg_anonymous_check( $id, $label, $type, $code, $filter, $req , $uid)
        {
            $out = "";
            $info = "";
            $lbl = str_replace("[q]","'",$label);

           ?>
           <div class='form-group'>
            <div class='col-sm-3'>
            <div class='input-group input-group-icon'>
               <label class='mg_control-label'><?php echo esc_attr($lbl).$info; ?></label>
            </div>
            </div>

          <div class='col-sm-6 col-xs-12'>
           <input type='hidden' class='idfield_type' value='checkbox'>
           <input type="hidden" class='idfield idfield_checkbox' value="<?php echo esc_attr($uid);?>">
           <input type="hidden" class='idfield_key' value="<?php echo esc_attr($code.$id);?>">
           <?php
           if( strcmp($req, 'required')==0 )
           {
           ?>
            <div class='checkbox'>
            <label for='<?php echo esc_attr($uid);?>'><input checked name='<?php echo esc_attr($code.$id);?>' type='checkbox' id='<?php echo esc_attr($uid);?>' name='<?php echo esc_attr($uid);?>' class='check-control <?php echo esc_attr($code." ".$req);?>' value='yes'/>
            </label></div>
            <?php
            }else{
            ?>
                <div class='checkbox'>
                <label for='<?php echo esc_attr($uid);?>'><input name='<?php echo esc_attr($code.$id);?>' type='checkbox' id='<?php echo esc_attr($uid);?>' name='<?php echo esc_attr($uid);?>' class='check-control <?php echo esc_attr($code." ".$req);?>' value='yes'/>
                </label></div>
           <?php 
            } ?>

           </div>
           <div class='col-sm-3 hidden-xs'></div></div>
        <?php
        }

        function mg_makeInputCountry( $id, $label, $type, $code,  $filter, $req, $state_tr, $province_tr)
        {            
            $objG = new MIGLA_GEOGRAPHY;
            $out  = "";
            $info = "";
            $is_mandatory = false;

            if( !empty($req) )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $is_mandatory = true;
            }

            $lbl = str_replace("[q]","'",$label);
            ?>
            
            <div class='form-group'>
                <div class='col-sm-3'>
                    <label class='mg_control-label'><?php echo esc_attr($lbl). $info; ?></label>
                </div>
            
                <div class='col-sm-6 col-xs-12 migla_rdiv_field'>
                
                   <input type="hidden" class='idfield idfield_country' value="<?php echo esc_attr($code.$id);?>">
                   <input type="hidden" class='idfield_key' value="<?php echo esc_attr($code.$id);?>">
        
                  <?php $countries =  $objG->get_countries(); ?>
        
                <select class='mg_form-control migla_country selectpicker' id="<?php echo esc_attr($code.$id);?>" name='<?php echo esc_attr($code.$id);?>'>
                <?php
                    if(!$is_mandatory){
                    ?>
                        <option value='' selected='selected' ><?php echo __("Please choose one", "migla-donation");?></option>
                    <?php    
                    }

                   $default_country = $this->OPTIONS->get_option('migla_default_country');

                   foreach ( $countries as $key => $value )
                   {
                        if ( $value == $default_country && ($id == 'country' || $id == 'honoreecountry') )
                        {
                        ?>
                            <option value='<?php echo esc_attr($value);?>' selected='selected' ><?php echo esc_attr($value);?></option>
                        <?php
                        }else{
                        ?>
                            <option value='<?php echo esc_attr($value);?>'><?php echo esc_attr($value);?></option>
                        <?php
                        }
                   }
                   ?>
                </select>
                
                </div>
                <div class='col-sm-3 hidden-xs'></div>
            </div>

           <?php
           if( $id == 'country')
           {
                $this->mg_makeInputState ( 'state',   $state_tr,   'select',  'miglad_', '1', $req , $default_country );
                $this->mg_makeInputProvince( 'province', $province_tr, 'select', 'miglad_', '1', $req, $default_country );
           }else if( $id == 'honoreecountry')
           {
                $this->mg_makeInputState( 'honoreestate',   $state_tr,   'select',  'miglad_', '1', $req, $default_country);
                $this->mg_makeInputProvince( 'honoreeprovince', $province_tr, 'select', 'miglad_', '1', $req, $default_country);
           }

        }

        function mg_makeInputState( $id, $label, $type, $code,  $filter, $req , $country )
        {
            $objG = new MIGLA_GEOGRAPHY;
            $info = "";
            $is_mandatory = false;

            if( !empty($req) )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $is_mandatory = true;
            }

            $lbl = str_replace("[q]","'",$label);

           if( $country == 'United States'){
            ?>
                <div class='form-group migla_state' id='<?php echo esc_attr($code.$id);?>-div'>
            <?php
            }else{
            ?>
                <div class='form-group migla_state' id='<?php echo esc_attr($code.$id);?>-div' style='display:none'>
            <?php
            }
            ?>
                <div class='col-sm-3'>
                    <label class='mg_control-label '><?php echo esc_attr($lbl). $info;?></label>
                </div>            
                <div class='col-sm-6 col-xs-12 migla_rdiv_field'>
                   <input type="hidden" class='idfield idfield_state' value="<?php echo esc_attr($code.$id);?>">
                   <input type="hidden" class='idfield_key' value="<?php echo esc_attr($code.$id);?>">
        
                    <?php
                       $states = $objG->get_USA_states();
                      ?>
                    <select class='mg_form-control migla_state' id='<?php echo esc_attr($code.$id);?>' name='<?php echo esc_attr($code.$id);?>'>

                    <?php    
                        if(!$is_mandatory){
                        ?>
                            <option value='' selected='selected' ><?php echo __("Please choose one", "migla-donation");?></option>
                        <?php    
                        }
            
                        foreach ( $states as $key => $value )
                        { 
                        ?>
                            <option value='<?php echo esc_attr($value);?>'><?php echo esc_attr($value);?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class='col-sm-3 hidden-xs'></div>
                
            </div>
        <?php
        }

        function mg_makeInputProvince( $id, $label, $type, $code,  $filter, $req, $country  )
        {
           $objG = new MIGLA_GEOGRAPHY;

            $info = "";
            $is_mandatory = false;

            if( !empty($req) )
            {
                $info = "<abbr class='mg_asterisk' title='required'> *</abbr>";
                $is_mandatory = true;
            }

            $lbl = str_replace("[q]","'",$label);

            if( $country == 'Canada'){
            ?>
                <div class="form-group migla_province" id='<?php echo esc_attr($code.$id);?>-div'>
            <?php
            }else{
            ?>
                <div class="form-group migla_province" id='<?php echo esc_attr($code.$id);?>-div' style='display:none'>
            <?php
            }
            ?>
                <div class='col-sm-3'>
                    <label class='mg_control-label '><?php echo esc_attr($lbl). $info;?></label>
                </div>
                        
                <div class='col-sm-6 col-xs-12 migla_rdiv_field'>
        
                   <input type="hidden" class='idfield idfield_province' value="<?php echo esc_attr($code.$id);?>">
                   <input type="hidden" class='idfield_key' value="<?php echo esc_attr($code.$id);?>">
        
                    <?php $states = $objG->get_CA_provinces();?>
                    <select class='mg_form-control migla_province' id='<?php echo esc_attr($code.$id);?>' name='<?php echo esc_attr($code.$id);?>'>
                    <?php    
                        if(!$is_mandatory){
                        ?>
                            <option value='' selected='selected' ><?php echo __("Please choose one", "migla-donation");?></option>
                        <?php    
                        }
        
                       foreach ( $states as $key => $value )
                       {
                        ?>
                            <option value='<?php echo esc_attr($value);?>'><?php echo esc_attr($value);?></option>
                       <?php 
                        }
                       ?>
                   </select>
                </div>
                   
                <div class='col-sm-3 hidden-xs'></div>
           </div>
        <?php
        }

        function getCurrencySymbol()
        {
           $code = (string)$this->OPTIONS->get_option(  'migla_default_currency'  );

           $objM = new MIGLA_MONEY;
           $arr  = $objM->get_avaliable_currencies();

           $icon ='';

           foreach ( $arr as $key => $value ) {
             if(  strcmp( $code, $arr[$key]['code'] ) == 0  ){
               $icon = $arr[$key]['symbol'];
               break;
             }
           }
           return $icon;
        }

        function migla_amount( $label, $formid , $custom_amount_text )
        {
            $objO = new MIGLA_MONEY;
            
            $symbol = $objO->get_currency_symbol();

            if( $this->TYPE_AMOUNT_BOX == 'fill' ){
                $amount_box_class = 'mg_giving-levels-text';
            }else{
                $amount_box_class = '';
            }
            
            $show_symbol = $this->OPTIONS->get_option('migla_symbol_to_show');
        
            $x = array();
            $x[0] = $this->OPTIONS->get_option('migla_thousandSep');
            $x[1] = $this->OPTIONS->get_option('migla_decimalSep');

            $min_amount = $this->OPTIONS->get_option('migla_min_amount');
            $showSep = $this->OPTIONS->get_option('migla_showDecimalSep');
            $decSep = 0;

            if( strcmp($showSep , "yes") == 0 )
            {
                $decSep = 2; 
            }else{ 
                $x[1] = '';
                $decSep = 0;
            }

            $placement = $this->OPTIONS->get_option('migla_curplacement');
            
            if( strtolower( $placement ) == 'before' )
            {
              $before = $symbol; 
              $after = "";  
              $toogle='icon-before';
            }else if( strtolower( $placement ) == 'after' )
            {
              $before = ""; 
              $after = $symbol ; 
              $toogle='icon-after';
            }

            $bglevelcolor     = $this->OPTIONS->get_option('migla_bglevelcolor');
            $bglevelcoloractive= $this->OPTIONS->get_option('migla_bglevelcoloractive');
            $borderlevelcolor = $this->OPTIONS->get_option('migla_borderlevelcolor');
            $borderlevel      = $this->OPTIONS->get_option('migla_borderlevel');
            $borderCSS        = "border: ".$borderlevel."px solid ".$borderlevelcolor.";";

            $amounts = $this->AMOUNTS ;

            $isEmpty = true;
            
            if( !empty($amounts) )
            {
                if( isset($amounts[0]) && !empty($amounts[0]) ){
                    $isEmpty = false;
                    $keys_amount = array_keys($amounts);
                }else{
                    
                }
            }

            $count_amount = 0;

            if($isEmpty){
                if( $this->HIDE_CUSTOM_AMOUNT == 'yes' )
                {
                  //Dont write custom amount
                }else
                {
                    if( $this->TYPE_AMOUNT_BTN == 'button' )
                    {
                        $inline_class = 'form-group mg_giving-levels';
                        $checked = '';
                        
                        $input_class = "";
                        if( $show_symbol == '3-letter-code' ){
                            $input_class = "countrycodecurrency";
                        }

                        if(  $count_amount == 0 )
                        {
                            $state          ='mg_amount_checked';
                            $selected       = 'selected';
                            $checked       = 'checked';
                            $active_style   = "background-color:".$bglevelcoloractive.";". $borderCSS;
                        }else{

                            $state='';
                            $selected = '';
                            $checked = '';

                            $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                        }
                        ?>
                        <div class='form-group mg_giving-levels'>
                        <div class='col-sm-12 col-xs-12'>
                            <label class='mg_control-label'><?php echo esc_attr($label);?><abbr class="mg_asterisk" title="required"> *</abbr></label>
                        </div>
                        </div>
                        <div class='<?php echo esc_attr($inline_class);?>'>
                            <div class='col-sm-5 col-xs-12'>
                                <div class='radio-inline amt-btn miglaCustomAmount <?php echo esc_attr($selected.' '.$amount_box_class);?>'>
                                    <label for='miglaCustomAmount<?php echo esc_attr($count_amount);?>' style="<?php echo esc_attr($active_style);?>" class='migla_amount_lbl'>
    
                                    <div style="display:none;">
                                        <input type='radio' <?php echo esc_attr($checked);?> value='custom' id='miglaAmount<?php echo esc_attr($count_amount);?>' name='miglad_amount' class='migla_amount_choice'>
                                    </div>
    
                                    <div><?php echo esc_attr($custom_amount_text);?></div>
                                    <?php
                                    if( strtolower( $placement ) == 'before' )
                                    {
                                        ?><div class='input-group input-group-icon <?php echo $toogle;?>'><span class='input-group-addon mg_symbol-before'>
                                        <span class='icon'><?php echo $before;?></span></span>
                                        <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only RadioInlineAmount<?php echo " ".esc_html($input_class);?>'></div><?php
                                    }else{
                                        ?> <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'>
                                        <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only RadioInlineAmount<?php echo " ".esc_html($input_class);?>'>
                                        <span class='input-group-addon mg_symbol-after'><span class='icon'><?php echo $after;?></span></span>
                                        </div><?php
                                    }
                                    ?>
                                    </label>
                                </div>
                            </div>
                            <div class='col-sm-3 hidden-xs'></div>
                        </div>
                    <?php  
                    }else{
                        $inline_class = 'form-group mg_giving-levels';
                        $checked = '';
                        
                        $input_class = "";
                        if( $show_symbol == '3-letter-code' ){
                            $input_class = "countrycodecurrency";
                        }

                        if(  $count_amount == 0 )
                        {
                            $state          ='mg_amount_checked';
                            $selected       = 'selected';
                            $checked       = 'checked';
                            $active_style   = "background-color:".$bglevelcoloractive.";". $borderCSS;
                        }else{

                            $state='';
                            $selected = '';
                            $checked = '';

                            $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                        }
                        ?>
                        <div class='form-group mg_giving-levels'>
                        <div class='col-sm-12 col-xs-12'>
                            <label class='mg_control-label'><?php echo esc_attr($label);?><abbr class="mg_asterisk" title="required"> *</abbr></label>
                        </div>
                        </div>
                        <div class='<?php echo esc_attr($inline_class);?>'>
                            <div class='col-sm-5 col-xs-12'>
                                <div class='radio-inline miglaCustomAmount' style='display:none'>
                                <label for='miglaCustomAmount<?php echo esc_attr($count_amount);?>' style='background-color:<?php echo esc_attr($bglevelcolor .";". $borderCSS) ;?>'>
                                <input type='radio' <?php echo esc_attr($checked);?> value='custom' id='miglaAmount<?php echo esc_attr($count_amount);?>' name='miglad-amount' class='migla_amount_choice migla_custom_amount'>
                                <div><?php echo esc_attr($custom_amount_text);?></div>
        
                                <?php
                                if( strtolower( $placement ) == 'before' )
                                {
                                ?>
                                    <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'><span class='input-group-addon'>
                                    <span class='icon'><?php echo $before;?></span></span>
                                    <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='<?php echo $input_class;?> migla_positive_number_only'>
                                    </div>
                                <?php
                                    }else{
                                    ?>
                                    <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'>
                                    <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='<?php echo $input_class;?> migla_positive_number_only'>
                                    <span class='input-group-addon'><span class='icon'><?php echo $after;?></span></span></div>
                                <?php }
                                ?>
                                </div>
                            </div>
                            <div class='col-sm-3 hidden-xs'></div>
                        </div>
                    <?php  
                    }

                }    
            }else{
                if( $this->TYPE_AMOUNT_BTN == 'button' )
                {
                ?>
                <input type='hidden' id='mg_level_active_color' value='<?php echo esc_attr($bglevelcoloractive);?>'>
                <input type='hidden' id='mg_level_color' value='<?php echo esc_attr($bglevelcolor);?>'>
                <input type='hidden' id='mg_level_active_border' value='<?php echo esc_attr($borderlevel."px solid ".$borderlevelcolor);?>'>

                <div class='form-group mg_giving-levels'>
                    <div class='col-sm-12 col-xs-12'>
                        <label class='mg_control-label'><?php echo esc_attr($label);?></label>
                    </div>
                    <input type="hidden" class='idfield idfield_amount idfield_amount_button' value="miglad_amount">
                    <div class='col-sm-12 col-xs-12'>
                    <?php
                    foreach( $keys_amount as $key )
                    {
                        $state    = '';  
                        $selected = ''; 
                        $checked = ''; 
                        $active_style = '';
                        $valLabel = (double)$amounts[$key]['amount'] ;
                        $valPerk = $amounts[$key]['perk'];
    
                        $count_amount++;
    
                        if(  $count_amount == 1 )
                        {
                            $state='mg_amount_checked';
                            $selected = 'selected';
                            $checked = 'checked';
                            $active_style = "background-color:".$bglevelcoloractive.";". $borderCSS;
                        }else{
                            $state='';
                            $selected = '';
                            $checked = '';
                            $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                        }
    
                        ?>
                        <div class="radio-inline amt-btn <?php echo esc_attr($selected .' '.$amount_box_class );?>">
                            <label for='miglaAmount<?php echo  esc_attr($count_amount);?>' style="<?php echo esc_attr($active_style);?>" class='migla_amount_lbl' >
                                <input type='hidden' value='<?php echo esc_attr($valLabel);?>' id='RadioInlineAmount-<?php echo esc_attr($count_amount);?>' class='RadioInlineAmount' >
                                <div style="display:none;">
                                    <input type='radio' <?php echo esc_attr($checked);?> value='<?php echo esc_attr($valLabel);?>' id='miglaAmount<?php echo  esc_attr($count_amount);?>' name='miglad_amount' class='migla_amount_choice'>
                                </div>
                                <span class='currency-symbol'><?php echo $before; ?></span>
                                <?php echo esc_attr(number_format( $valLabel, $decSep, $x[1], $x[0]));?>
                                <span class='currency-symbol'> <?php echo $after; ?></span>
            
                                <?php
                                if($valPerk == '' )
                                {
                                ?><span class=''><?php echo esc_attr($valPerk);?></span>
                                <?php 
                                }else{
                                ?><span class='mg_giving-text-perk'><?php echo esc_attr($valPerk);?></span>
                                <?php 
                                } ?>
                            </label>
                        </div>
                        <?php
                    }
                    ?>  
                    </div>
                </div><!-- mg_giving-levels -->
                <?php
                    //Button and with custom
                    if( $this->HIDE_CUSTOM_AMOUNT == 'yes' )
                    {
                        //Dont write custom amount
                    }else
                    {
                        $inline_class = 'form-group mg_giving-levels';
                        $checked = '';
                        
                        $input_class = "";
                        if( $show_symbol == '3-letter-code' ){
                            $input_class = "countrycodecurrency";
                        }

                        if(  $count_amount == 0 )
                        {
                            $state          ='mg_amount_checked';
                            $selected       = 'selected';
                            $checked       = 'checked';
                            $active_style   = "background-color:".$bglevelcoloractive.";". $borderCSS;
                        }else{

                            $state='';
                            $selected = '';
                            $checked = '';

                            $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                        }
                        ?>
                        <div class='<?php echo esc_attr($inline_class);?>'>
                            <div class='col-sm-5 col-xs-12'>
                                <div class='radio-inline amt-btn miglaCustomAmount <?php echo esc_attr($selected.' '.$amount_box_class);?>'>
                                    <label for='miglaCustomAmount<?php echo esc_attr($count_amount);?>' style="<?php echo esc_attr($active_style);?>" class='migla_amount_lbl'>
    
                                    <div style="display:none;">
                                        <input type='radio' <?php echo esc_attr($checked);?> value='custom' id='miglaAmount<?php echo esc_attr($count_amount);?>' name='miglad_amount' class='migla_amount_choice'>
                                    </div>
    
                                    <div><?php echo esc_attr($custom_amount_text);?></div>
                                    <?php
                                    if( strtolower( $placement ) == 'before' )
                                    {
                                        ?><div class='input-group input-group-icon <?php echo $toogle;?>'><span class='input-group-addon mg_symbol-before'>
                                        <span class='icon'><?php echo $before;?></span></span>
                                        <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only RadioInlineAmount<?php echo " ".esc_html($input_class);?>'></div><?php
                                    }else{
                                        ?> <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'>
                                        <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only RadioInlineAmount<?php echo " ".esc_html($input_class);?>'>
                                        <span class='input-group-addon mg_symbol-after'><span class='icon'><?php echo $after;?></span></span>
                                        </div><?php
                                    }
                                    ?>
                                    </label>
                                </div>
                            </div>
                            <div class='col-sm-3 hidden-xs'></div>
                        </div>
                    <?php                        
                    }
                }else{
                ?>
                <input type='hidden' id='mg_level_active_color' value='<?php echo esc_attr($bglevelcoloractive);?>'>
                <input type='hidden' id='mg_level_color' value='<?php echo esc_attr($bglevelcolor);?>'>
                <input type='hidden' id='mg_level_active_border' value='<?php echo esc_attr($borderlevel."px solid ".$borderlevelcolor);?>'>
    
                <div class='form-group mg_giving-levels'>
                    <div class='col-sm-12 col-xs-12'>
                        <label class='mg_control-label'><?php echo esc_attr($label);?></label>
                    </div>
                    <input type="hidden" class='idfield idfield_amount idfield_amount_radio' value="miglad_amount">
                    <div class='col-sm-12 col-xs-12'>
                    <?php
    
                    foreach( $keys_amount as $key )
                    {
                      $state    = '';  $selected = ''; $active_style  = '';
                      $valLabel = (double)$amounts[$key]['amount'] ;
                      $valPerk  = $amounts[$key]['perk'];
    
                     $count_amount++;
    
                      if( $count_amount == 1 )
                      {
                         $state='';
                         $selected = 'selected';
                         $checked = 'checked';
                         $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                      }else{
                         $state     = '';
                         $selected  = '';
                         $checked   = '';
                         $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                      }
    
                      ?>
                      <div class='radio-inline <?php echo esc_attr($selected." ".$amount_box_class);?>'>
                      <label for='miglaAmount<?php echo esc_attr($count_amount);?>' style="<?php echo esc_attr($active_style);?>" >
    
                      <input type='radio' value='<?php echo esc_attr($valLabel);?>' id='miglaAmount<?php echo esc_attr($count_amount);?>' name='miglad_amount' <?php echo esc_attr($checked);?> class='migla_amount_choice <?php echo esc_attr($state);?>' />
                      <span class='currency-symbol'><?php echo $before;?></span>
                      <?php echo esc_attr(number_format( $valLabel, $decSep, $x[1], $x[0]) ) ;?> <span class='currency-symbol'><?php echo $after;?></span>
                      <?php
                      if($valPerk == '' )
                      {
                        ?><span class=''><?php echo esc_attr($valPerk);?></span><?php
                      }else{
                        ?><span class='mg_giving-text-perk'><?php echo esc_attr($valPerk);?></span>
                     <?php  } ?>
    
                        </label></div>
    
                     <?php
                      $count_amount++;
                    }
    
                    ?>
                        </div>
                </div> <!-- mg_giving-levels -->
                <?php
    
                    //Radio button and with custom
                    if( $this->HIDE_CUSTOM_AMOUNT == 'yes' )
                    {
                        //Dont write custom amount
                    }else{
                        if( $count_amount == 0 )
                        {
                                ?> </div></div>
                                <?php
        
                                $state='';
                                $selected = 'selected';
                                $checked = 'checked';
                                $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                                $inline_class = 'form-group mg_giving-levels';
        
                                ?>
                                <div class='form-group mg_giving-levels'>
                                <div class='col-sm-5 col-xs-12'>
                                    <label style='display:none' class='idfield' id='miglad_camount' style="<?php echo esc_attr($active_style);?>"></label>
                                </div>
                                <div class='radio-inline miglaCustomAmount' style='display:none'>
                                <label for='miglaCustomAmount<?php echo esc_attr($count_amount);?>' style='background-color:<?php echo esc_attr($bglevelcolor .";". $borderCSS) ;?>'>
                                <input type='radio' <?php echo esc_attr($checked);?> value='custom' id='miglaAmount<?php echo esc_attr($count_amount);?>' name='miglad-amount' class='migla_amount_choice migla_custom_amount'>
                                <div><?php echo esc_attr($custom_amount_text);?></div>
        
                                <?php
                                if( strtolower( $placement ) == 'before' )
                                {
                                ?>
                                    <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'><span class='input-group-addon'>
                                    <span class='icon'><?php echo $before;?></span></span>
                                    <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only'>
                                    </div>
                                <?php
                                    }else{
                                    ?>
                                    <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'>
                                    <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only'>
                                    <span class='input-group-addon'><span class='icon'><?php echo $after;?></span></span></div>
                                <?php }
                                ?>
                                </div></div>
                                <div class='col-sm-3 hidden-xs'></div></div>
                                <?php
                        }else{
        
                                 $state     = '';
                                 $selected  = '';
                                 $checked   = '';
                                 $active_style = "background-color:".$bglevelcolor.";". $borderCSS;
                                 $inline_class = 'form-group mg_giving-levels';
                            ?>
                                <div class='form-group mg_giving-levels'>
                                <div class='col-sm-5 col-xs-12'>
        
                                <label style='display:none' class='idfield' id='miglad_camount' style="<?php echo esc_attr($active_style);?>"></label>
        
                                <div class='radio-inline miglaCustomAmount' style='display:none'>
                                <label for='miglaCustomAmount<?php echo  $count_amount;?>' style='background-color:<?php echo esc_attr($bglevelcolor.";". $borderCSS);?>'>
                                <input type='radio' <?php echo esc_attr($checked);?> value='custom' id='miglaAmount<?php echo esc_attr($count_amount);?>' name='miglad_amount' class='migla_amount_choice migla_custom_amount'>
                                <div><?php echo esc_attr($custom_amount_text);?></div>
        
                                <?php
                                if( strtolower( $placement ) == 'before' )
                                {
                                ?>
                                    <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'>
                                        <span class='input-group-addon'><span class='icon'><?php echo $before;?></span></span>
                                        <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only'>
                                    </div>
                                    <?php
                                }else{
                                    ?> 
                                    <div class='input-group input-group-icon <?php echo esc_attr($toogle);?>'>
                                        <input type='text' value='0' id='miglaCustomAmount' name='miglad_custom_amount' class='migla_positive_number_only'>
                                        <span class='input-group-addon'><span class='icon'><?php echo $after;?></span></span>
                                    </div>
                                <?php
                                }
                                ?>
                                </div></div>
                                <div class='col-sm-3 hidden-xs'></div></div><!-- end of amounts -->
                        <?php  
                        }                        
                    }//write custom               
                }
            }//if levels not empty
        }

        function migla_campaign_section( $postCampaign , $label )
        {
            $obj = new MIGLA_CAMPAIGN;
            $data = $obj->get_all_info_orderby( get_locale() );

            $cmp_order = $this->OPTIONS->get_option('migla_campaign_order');

            $order = array();
            $j = 0;

            if( !empty($cmp_order) )
            {
                $order_list = (array)unserialize($cmp_order);

                foreach( $order_list as $order_row )
                {
                  if( isset($data[($order_row)]) ){
                    $order[$j] = (array)$data[($order_row)];
                    $j++;
                  }
                }
            }

            $und = (array)unserialize($this->OPTIONS->get_option('migla_undesignLabel'));
            $undesign = "";

            if(!empty($und)){
                if( isset($und[(get_locale())]) ){
                    $undesign = $und[(get_locale())];
                }else{
                    $undesign = "";
                }
            }

            $b = "";
            $i = 0;
            $out2 = "";
            $campaignCount = 0;

           if( empty($order) )
           {
               ?>
               <div class='form-group' style='display:none'>
                    <div class='col-sm-12 col-xs-12'>
                        <label class='mg_control-label  mg_campaign-switcher'><?php echo esc_attr($label);?></label>
                    </div>
                    <div class='col-sm-6 col-xs-12'>
                    <input type="hidden" class='idfield idfield_campaign' value="miglad_campaign">
                    <input type="hidden" class='idfield_key' value="miglad_campaign">
                    <select name='miglad_campaign' class='mg_form-control' id='miglad_campaign' style='display:none'>
                        <option value='0'><?php echo esc_attr($undesign);?></option>
                    </select>
                    </div>
                    <div class='col-sm-3 hidden-xs'></div>
                </div>
                <?php
            }else{

            $b = "";
            $i = 0;
            $out2 = "";
            $campaignCount = $j;

            $out2 .= "<div class='form-group' ><div class='col-sm-12 col-xs-12'><label class='mg_control-label  mg_campaign-switcher'>".esc_attr($label);
            $out2 .= "</label></div><div class='col-sm-6 col-xs-12'>";

            $out2 .= '<input type="hidden" class="idfield idfield_campaign" value="miglad_campaign">';
            $out2 .= '<input type="hidden" class="idfield_key" value="miglad_campaign">';

            $out2 .= "<select name='campaign' class='mg_form-control' id='miglad_campaign' >";

            if( $this->OPTIONS->get_option('migla_hideUndesignated') == 'no' )
            {
                $out2 .= "<option value='0'>". esc_attr($undesign)."</option>";
                $campaignCount++;
            }

            foreach ( $order as $cmp )
            {
                if( $cmp['shown']=="1"
                    && $cmp['multi_list']=="1"
                ){
                          $campaignCount++;
                          $c_name = esc_html__( $cmp['name'] );
                          $c_name = str_replace( "[q]", "'", $c_name );

                          if( strcmp($c_name, $postCampaign) == 0  ){
                    $out2 .= "<option value='".esc_attr($cmp['id'])."' selected >".esc_attr($c_name)."</option>";
                          }else{
                    $out2 .= "<option value='".esc_attr($cmp['id'])."' >".esc_attr($c_name)."</option>";
                          }
               }
                   $i++;
            }

            $out2 .= "</select>";
            $out2 .= "</div>";

            $out2 .= "<div class='col-sm-3 hidden-xs'></div></div>";

            if( $campaignCount > 1 )
            {
               echo $out2;
            }else{
                $b = "";
                $i = 0;
            ?>
            <div class='form-group' style='display:none'>
                <div class='col-sm-12 col-xs-12'><label class='mg_control-label  mg_campaign-switcher'><?php echo esc_attr($label);?></label></div>
                <div class='col-sm-6 col-xs-12'>
                    <input type="hidden" class='idfield idfield_campaign' value="miglad_campaign">
                    <input type="hidden" class='idfield_key' value="miglad_campaign">
                    <select name='campaign' class='mg_form-control' id='miglad_campaign' style='display:none'>
                        <option value='0'><?php echo esc_attr($undesign);?></option>
                   </select>
                </div>
                   <?php
                   if( $show_bar == 'yes' ){
                   ?>
                   <div class='col-sm-12 col-xs-12'><div id='migla_bar'></div></div>
                   <?php
                   }
                   ?>
               <div class='col-sm-3 hidden-xs'></div>
            </div>
            <?php
            }
          }

        }

        function migla_onecampaign_section( $postCampaign , $label , $show )
        {
            $out = "";
            $style = "";

            if( $show == 0 ){
               $style = 'display:none';
            }

            $obj = new MIGLA_CAMPAIGN;
            $campaign = $obj->get_info( $postCampaign, get_locale() );

            ?>

            <div class='form-group' style='<?php echo esc_attr($style);?>'>
                <div class='col-sm-12 col-xs-12'>
                    <label class='mg_control-label'>
                        <?php echo esc_attr($label);?>
                    </label>
               </div>
               <div class='col-sm-6 col-xs-12'>
                <input type="hidden" class="idfield idfield_campaign" value="miglad_campaign">
                <input type="hidden" class="idfield_key" value="miglad_campaign">

                <select name='campaign' class='mg_form-control' id='miglad_campaign' style='<?php echo esc_attr($style);?>'>
                    <option value='<?php echo esc_attr($campaign['id']);?>' selected><?php echo esc_attr($campaign['name']);?></option>
                </select>
                </div>
                <div class='col-sm-3 hidden-xs'></div>
            </div>
        <?php
        }

        public function mg_write_me( $str )
        {
           $result =  str_replace( "//" , "/" , $str );
           $result =  str_replace( "[q]" , "'" , $result );
           return $result;
        }

        /*GATEWAYS*/
        public function gateway_tabs( $gateways, $bgcolor, $borderCSS, $bglevelcolor)
        {
            $out = "";
            $add_class = "";
            $tabs_name   = array();

            $paypal     = $this->GATEWAYS['paypal_tab_info'];
            $stripe     = $this->GATEWAYS['stripe_tab_info'];

            $tabs_name['paypal']     = $paypal['tab'];
            $tabs_name['stripe']     = $stripe['tab'];

            $gateways = (array)unserialize($this->OPTIONS->get_option('migla_gateways_order'));

            $count_the_tabs   = 0;
            $one_gateway_only = "";
            $inactive_color   = $this->OPTIONS->get_option('migla_tabcolor');

            foreach( (array)$gateways as $value  )
            {
               if(  $value[1] == 'true' || $value[1] == 1  )
               {
                  $count_the_tabs++;
                  $one_gateway_only = $value[0];
                  
                  if( $value[0] == 'paypal' ) $this->IS_PAYPAL = true;
               }
            }

            if( $gateways == false || $gateways[0] == '' )
            {
            }else{

                if( $count_the_tabs > 1 )
                {
                    ?>
                    <div class='form-horizontal migla-payment-options' >
                    <ul class='mg_nav mg_nav-tabs'>
                    <?php

                    $gateways_j = 1;

                    foreach( (array)$gateways as $value  )
                    {
                        if( $value[1] == 'true' || $value[1] == 1 )
                        {
                            if( $value[0] == 'paypal' ) $this->IS_PAYPAL = true;

                            if( $gateways_j == 1 ){
                                ?>
                                <li class='mg_active'><a id="_section<?php echo esc_attr($value[0]);?>" style="background-color:<?php echo esc_attr($bgcolor.";".$borderCSS);?>" ><?php echo esc_attr($tabs_name[$value[0]] );?></a></li>
                            <?php
                            }else{
                                ?>
                                <li><a id='_section<?php echo esc_attr($value[0]);?>' style='background-color:<?php echo esc_attr($inactive_color.";".$borderCSS);?>'><?php echo esc_attr($tabs_name[$value[0]]);?></a></li>
                            <?php
                            }
                            $gateways_j++;
                        }
                    }

                    ?>
                    </ul>

                    <div class='mg_tab-content' style='<?php echo esc_attr($borderCSS);?>' >
                    <?php
                    $gateways_i = 1;
                    foreach( (array)$gateways as $value )
                    {
                        if( $value[1] == 'true' || $value[1] == 1 )
                        {
                           if( $gateways_i == 1 )
                           {
                              ?>
                              <div id='section<?php echo esc_attr($value[0]);?>' class='mg_tab-pane mg_active' style='background-color:<?php echo esc_attr($bgcolor);?>' >
                              <?php
                              }else{
                              ?>
                              <div id='section<?php echo esc_attr($value[0]);?>' class='mg_tab-pane' style='background-color:<?php echo esc_attr($bgcolor);?>' >
                              <?php
                              }

                              $this->load_tab_content( $value[0],  $bgcolor, $borderCSS  );
                           ?>

                           </div><?php
                           $gateways_i++;
                        }
                    }

                    ?></div> <!--content -->
                    </div> <!--FORM-->
                    <?php

                }else{ //One Tab
                ?>
                <div class='form-horizontal migla-payment-options' >
                    <div class='mg_tab-content' >
                        <div id='section<?php echo esc_attr($one_gateway_only);?>' class='' style='background-color:<?php echo esc_attr($bgcolor.";".$borderCSS);?>'>
                            <?php echo $this->load_tab_content( $one_gateway_only , $bgcolor, $borderCSS  );?>
                        </div>
                    </div>
                </div>
                <?php
                }
            }
        }

        public function gateway_paypal(  $isTab, $bgcolor, $borderCSS )
        {
            $btnchoice = $this->OPTIONS->get_option('miglaPaypalButtonChoice') ;

            if( $btnchoice == 'paypalButton' || empty($btnchoice) )
            {
                $btnlang = $this->OPTIONS->get_option('migla_paypalbutton');

                $btnicon = Totaldonations_DIR_URL ."assets/images/btn_donate_" . $btnlang .".gif";
            }else if( $btnchoice == 'imageUpload' )
            {
                $btnurl = $this->OPTIONS->get_option('migla_paypalbuttonurl');
                $button_image_url = $btnurl;
            }

            $pm = $this->OPTIONS->get_option('migla_paypal_method');
            $cc_label = $this->GATEWAYS['paypal_tab_info'] ;
            $cc_label = $this->FORM->paypal_tab($cc_label);

            ?>

            <div class='form-group'>
                <div class='col-sm-12 col-xs-12' >
                <?php
                if( $btnchoice == 'paypalButton' || empty($btnchoice) )
                {
                ?>
                    <a class='mg_PayPalButton paypalstdcheckoutbtn <?php echo esc_attr($this->BUTTON_CLASS);?> mbutton' id='miglapaypalcheckout_sTotaldonations_<?php echo esc_attr($this->FORM_ID);?>' name='<?php echo esc_attr($this->FORM_ID);?>' >
                            <img src='<?php echo esc_attr($btnicon) ;?>'></a>
                <?php
                }else if( $btnchoice == 'cssButton' )
                {
                    $btnstyle = "";
                    $btnclass = $this->OPTIONS->get_option('migla_paypalcssbtnclass');

                    if( $this->OPTIONS->get_option('migla_paypalcssbtnstyle')=='Grey' ){
                        $btnstyle='mg-btn-grey';
                    }
                ?>
                    <button id='miglapaypalcheckout_sTotaldonations_<?php echo esc_attr($this->FORM_ID);?>' class='<?php echo esc_attr($this->BUTTON_CLASS);?> <?php echo esc_attr($btnstyle." ". $btnclass);?> paypalstdcheckoutbtn' name='<?php echo esc_attr($this->FORM_ID);?>' ><?php echo esc_attr($cc_label['button']);?></button>

                <?php
                }else{
                ?>
                    <a class='mg_PayPalButton paypalstdcheckoutbtn <?php echo esc_attr($this->BUTTON_CLASS);?> mbutton' id='miglapaypalcheckout_sTotaldonations_<?php echo esc_attr($this->FORM_ID);?>' name='<?php echo esc_attr($this->FORM_ID);?>' >
                            <img src='<?php echo esc_url( $button_image_url ) ;?>'></a>
                <?php
                }
                ?>
                <div class="alignright">
                    <label class="mg-donation-amount"></label>
                </div>
                
                    </div>
                </div>

           <?php  $load = Totaldonations_DIR_URL . 'assets/images/gif/loading.gif' ;  ?>

            <div id='mg_wait_paypal_<?php echo esc_attr($this->FORM_ID);?>' class='mg_wait' style='display:none!important'>
                <?php echo esc_attr($cc_label['loading_message'] . "&nbsp;") ;?>
                <input class="mg-gif-loader" id='mg_load_paypal_<?php echo esc_attr($this->FORM_ID);?>' type='image' src='<?php echo esc_url( $load ) ;?>'>
            </div>
            <div id='mg_wait_paypalpro_<?php echo esc_attr($this->FORM_ID);?>' class='mg_wait' style='display:none!important'>
                <?php echo esc_attr($cc_label['loading_message'] . "&nbsp;" );?>
                <input class="mg-gif-loader" id='mg_load_paypalpro_<?php echo esc_attr($this->FORM_ID);?>' type='image' src='<?php echo esc_url( $load ) ;?>'>
            </div>
        <?php
        }

        public function gateway_stripe()
        {
          $stripe_info = $this->GATEWAYS['stripe_tab_info'];

          $cc_label = $this->FORM->stripe_tab($stripe_info);
          $btnchoice = $this->OPTIONS->get_option('miglaStripeButtonChoice');

            $placeholder = "";
            if(isset($cc_label['cardholder']['placeholder'])){
                $placeholder = $cc_label['cardholder']['placeholder'];
            }
        ?>

        <form id='mg-stripe-payment-form'>
            <input type="hidden" id="" class="client-secret">
            <div class="form-row">
                <label for="card-element">
                  <?php echo esc_attr($cc_label['cardholder']['label']);?>
                </label>
                <div class="form-row">
                    <input id="card_name" class="field stripe_cardholder" placeholder="<?php echo esc_attr($placeholder);?>" />
                </div>
            </div>
            <div class="form-row">
                <label for="card-element">
                  <?php echo esc_attr($cc_label['cardnumber']['label']);?>
                </label>
                <div id="card-element" class="form-row">
                </div>
            </div>

            <div class="form-row">
                <div id="card-errors" role="alert" class="mg-message"></div>
            </div>

            <div class='form-group form-row'>
              <div class='col-sm-12 col-xs-12'>
              <?php
                $load = Totaldonations_DIR_URL . 'assets/images/gif/loading.gif' ;

                    $btnstyle = "";
                    $btntype = $this->OPTIONS->get_option('miglaStripeButtonChoice');

                    if( $btntype == 'Grey' )
                    {
                        $btnstyle='mg-btn-grey';
                    }

                    $btn_text = $cc_label['button'];

                    $btnclass = $this->OPTIONS->get_option('migla_stripecssbtnclass');

                    if( $btntype == 'Grey' || $btntype == 'Default' ){
                 ?>
                   <button id='miglastripecheckout_<?php echo esc_attr($this->FORM_ID);?>' class='<?php echo esc_attr($this->BUTTON_CLASS);?> <?php echo esc_attr($btnstyle." ". $this->OPTIONS->get_option('migla_stripecssbtnclass') );?> stripecheckoutbtn' name='<?php echo esc_attr($this->FORM_ID);?>' ><?php echo esc_attr($btn_text);?></button>
                <?php
                    }else{
                    ?>
                    <button id='migla_stripecheckout_<?php echo esc_attr($this->FORM_ID);?>' class='stripecheckoutbtn stripe-button-el mg_StripeButton <?php echo esc_attr($this->BUTTON_CLASS);?> <?php echo esc_attr($btnclass);?>' name='<?php echo esc_attr($this->FORM_ID);?>' >
                        <span style='display: block; min-height: 30px;'><?php echo esc_attr($btn_text);?></span></button>
                    <?php
                    }
                
               ?>
               
                <div class="alignright">
                    <label class="mg-donation-amount"></label>
                </div>
                </div>
            </div>

            <div class="form-row">
                <div id='mg_wait_stripe_<?php echo esc_attr($this->FORM_ID);?>' class='mg_wait' style='display:none'><?php echo esc_attr($cc_label['loading_message']."&nbsp;");?>
                    <input class="mg-gif-loader" id='mg_load_stripe_<?php echo esc_attr($this->FORM_ID);?>' type='image' src='<?php echo esc_url( $load );?>'/>
                </div>
            </div>

        </form>
        <?php
    }

    function migla_hidden_form( $id )
    {
        $paypalEmail = $this->OPTIONS->get_option( 'migla_paypal_emails' );
        $payPalServer = $this->OPTIONS->get_option('migla_paypal_payment');

        if ($payPalServer == "sandbox")
        {
            $formAction = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        }else{
            $formAction = "https://www.paypal.com/cgi-bin/webscr";
        }

        $notifyUrl = '';
        $successUrl = '';
                
        $objO = new MIGLA_OPTION;
        $objS = new MIGLA_SEC;
        $objRd      = new MIGLA_REDIRECT;
                
                
        $redirect   = $objRd->get_info( $id, get_locale() );
        $successUrl = esc_url( get_permalink($redirect['pageid']) );
        
        if( empty($successUrl) )
        {
            $successUrl = home_url( 'index.php' ). "?";
        }else{
            if( strpos( strval($successUrl), strval("?") ) === false )
            {
                $successUrl .= "?";
            }else{
                $successUrl .= "&";
            }
        }
        
        $successUrl .= "gtw=p";
        $successUrl .= "&sid=" . $this->SESSION;
        
        $notifyUrl  = esc_url( $objS->get_current_server_url()  );
        
        if( strpos( strval($notifyUrl), strval("?") ) === false )
        {
            $notifyUrl .= "/?";
        }else{
            $notifyUrl .= "&";
        }
                
        $token = $this->OPTIONS->get_option( 'migla_listen' );
                
        $notifyUrl .= "pl=" . $token;

        $currency_code = $this->OPTIONS->get_option( 'migla_default_currency' );

        $_item_ = $this->OPTIONS->get_option('migla_paypalitem');
        
        if(  $_item_ == '' || $_item_ == false ){
            $item_name = 'donation';
        }else{
            $item_name = $_item_ ;
        }

            ?>

            <form id='migla-hidden-form' action='<?php echo esc_url( $formAction );?>' method='post' >

            <?php
                $cmd_type = $this->OPTIONS->get_option('migla_paymentcmd');

                if(  $cmd_type == 'payment' ){
                    ?> <input type='hidden' name='cmd' value='_xclick' >

                    <?php
                }else{
                    ?> <input type='hidden' name='cmd' value='_donations' >
                    <?php
                }
            ?>
            <input type='hidden' name='custom' value='<?php echo esc_attr($id);?>' >
            <input type='hidden' name='business' value='<?php echo esc_attr( $paypalEmail ) ;?>' >

            <input type='hidden' name='return' value='<?php echo esc_url( $successUrl ) ;?>' >
            <input type='hidden' name='notify_url' value='<?php echo esc_url( $notifyUrl ) ;?>' >

            <input type='hidden' name='email' value='' >
            <input type='hidden' name='first_name' value='' >
            <input type='hidden' name='last_name' value='' >
            <input type='hidden' name='address1' value='' >
            <input type='hidden' name='address2' value=''>
            <input type='hidden' name='city' value=''>
            <input type='hidden' name='country' value=''>
            <input type='hidden' name='state' value=''>
            <input type='hidden' name='zip' value=''>

            <input type='hidden' name='item_name' value='<?php echo esc_attr( $item_name );?>' >
            <input type='hidden' name='quantity' value='1' />
            <input type='hidden' name='currency_code' value='<?php echo esc_attr( $currency_code );?>' >

            <input name="lc" value = "<?php echo esc_attr(get_locale());?>" type = "hidden">

            <!-- _xclick-subscriptions Items -->
            <input type='hidden' name='p3' value='1'>
            <input type='hidden' name='t3' value='1'>
            <input type='hidden' name='a3' value='1'>
            <input type='hidden' name='src' value='1'> <!--1=Subscription payments recur 0.one time-->
            <input type='hidden' name='sra' value='1'> <!--1=Reattempt failed recurring payments before canceling-->

            <!-- One timeItems -->
            <input type='hidden' name='amount' value='1.00' />

            <!--<input type='hidden' value='2' name='rm'>-->
            <?php
            if( $this->OPTIONS->get_option('migla_paypal_fec') == 'yes')
            {
            ?>
                <input type='hidden' name='on0' value='DisclosureName' >
                <input type='hidden' name='os0' value='' >
                <input type='hidden' name='on1' value='DisclosureEmployerOccupation' >
                <input type='hidden' name='os1' value='' >
            <?php
            }
            ?>

            <input type='hidden' name='on2' value='Campaign' >
            <input type='hidden' name='os2' value='' >

            <input type='submit' id='miglaHiddenSubmit' style='display:none !important' />

        </form>
        <?php
        }
    } // END OF CLASS
}
?>