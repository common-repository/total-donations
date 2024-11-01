<?php
if (session_status() == PHP_SESSION_NONE){
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

/*Campaign List*/
$ajax_campaign_list = array( "TotalDonationsAjax_new_campaign",
                              "TotalDonationsAjax_update_campaigns",
                              "TotalDonationsAjax_remove_campaign",
                              "TotalDonationsAjax_campaign_sort",

                              "TotalDonationsAjax_restore_form",
                              "TotalDonationsAjax_update_mulval_CForm",
                              "TotalDonationsAjax_update_formfields",
                              "TotalDonationsAjax_update_formopt",

                              "TotalDonationsAjax_update_multival",
                              "TotalDonationsAjax_delete_postmeta",
                              "TotalDonationsAjax_update_formgroup",
                              "TotalDonationsAjax_update_amountlevel_Form",
                              "TotalDonationsAjax_update_newformgroup"
                        );

if(!function_exists('TotalDonationsAjax_update_amountlevel_Form')){
function TotalDonationsAjax_update_amountlevel_Form()
{
    $objSEC = new MIGLA_SEC;
    $res = 'start';

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        global $wpdb;
        
        if(isset($_POST['valuelist'])){
            $list = (array)$_POST['valuelist'];
            $i = 0;
            foreach( $list as $row ){
                if(isset($row['amount']) && isset($row['perk']) ){
                    $list[$i]['amount'] = sanitize_text_field($row['amount']);    
                    $list[$i]['perk'] = sanitize_text_field($row['perk']);  
                    $i++;
                }
            }
            
            if($i > 0){
                $wpdb->update(  $wpdb->prefix . "migla_form",
                            array( "amounts" => serialize($list) ),
                            array( "form_id" => 0 ),
                            array( "%s" ),
                            array( "%d" )
                        );
                        
                $res = 'updated';
            }
        }
    }
    
    echo $res;
    die();
}
}

if (!function_exists('TotalDonationsAjax_new_campaign'))
{
function TotalDonationsAjax_new_campaign()
{
    $objSEC = new MIGLA_SEC;
    $res = '';

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $objCampaign = new MIGLA_CAMPAIGN;
        $cmpName    = sanitize_text_field($_POST['cmp_name']);
        $cmpTarget  = sanitize_text_field($_POST['cmp_target']);

        if(isset($_POST['campaign_list'])){
            $cmpList = (array)$_POST['campaign_list'];
        }else{
            $cmpList = array();
        }

        $res = $objCampaign->create_campaign( $cmpName,
                                $cmpTarget ,
                                true,
                                get_locale()
                            );

        $resA = explode(',', $res);

        $objO = new MIGLA_OPTION;

        if( $objSEC->is_option_available('migla_campaign_order') )
        {
            $list  =  $cmpList;
            $_list = array();

            $_list[0] = sanitize_text_field($resA[1]);
            $i = 1;

            foreach( $list as $li ){
                $_list[$i] = sanitize_text_field($li);
                $i++;
            }

            $objO->update( 'migla_campaign_order', $_list , 'array' );          
        }
    }

    echo $res;
    die();
}
}

if (!function_exists('TotalDonationsAjax_update_campaigns')){
function TotalDonationsAjax_update_campaigns()
{
    $objO = new MIGLA_OPTION;
    $objSEC = new MIGLA_SEC;
    $objL = new MIGLA_LOCAL;

    $resp = "";

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $list = (array)$_POST['changed_values'];
        $obj = new MIGLA_CAMPAIGN;

        foreach($list as $row)
        {
            $isChanged = false;
            $cmpName = sanitize_text_field($row['cmp_name']);
            $cmpNewName = sanitize_text_field($row['new_name']);

            $cmpTarget = sanitize_text_field($row['cmp_target']);
            $cmpNewTarget = sanitize_text_field($row['new_target']);

            $cmpShown = sanitize_text_field($row['cmp_shown']);
            $cmpNewShown = sanitize_text_field($row['new_shown']);

            if( strcmp( $cmpName, $cmpNewName) != 0 )
            {
                $isChanged = true;
            }else{
                if( $cmpTarget != $cmpNewTarget )
                {
                    $isChanged = true;
                }else{
                    if( $cmpShown != $cmpNewShown )
                    {
                        $isChanged = true;
                    }
                }
            }

            if( $isChanged )
            {
                $languages = $objL->get_languages();

                foreach($languages as $code => $array ){
                    $names[$code] = $cmpNewName;
                }

                $obj->save_campaign( sanitize_text_field($row['cmp_id']),
                                $names,
                                $cmpNewTarget,
                                $cmpNewShown
                            );
            }

        }//foreach
        
        $cmpList = array();

        if(isset($_POST['campaign_list'])){
            $cmpList = (array)$_POST['campaign_list'];

            $i = 0;
            $_list = array();
            
            foreach( $cmpList as $li ){
                $_list[$i] = sanitize_text_field($li);
                $i++;
            }

            if( $objSEC->is_option_available( 'migla_campaign_order'  ) ){
                $objO->update( 'migla_campaign_order', $_list, 'array' );
            }            
        }
    }

    echo $resp;
    die();
}
}

if (!function_exists('TotalDonationsAjax_remove_campaign')){
function TotalDonationsAjax_remove_campaign()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {

        $objCampaign = new MIGLA_CAMPAIGN;

        $res = $objCampaign->remove_campaign( sanitize_text_field($_POST['cmp_id']) );

        echo $res[0].','.$res[1];

        $objO = new MIGLA_OPTION;
        
        $cmpList = (array)$_POST['campaign_list'];
        $i = 0;
        $_list  = array();
        
        foreach( $cmpList as $li ){
            $_list[$i] = sanitize_text_field($li);
            $i++;
        }        

        if( $objSEC->is_option_available('migla_campaign_order') ){
            $objO->update( 'migla_campaign_order', $_list, 'array' );
        }
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_campaign_sort')){
function TotalDonationsAjax_campaign_sort()
{
    $objO = new MIGLA_OPTION;
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        if(isset($_POST['campaign_list']))
        {
            $cmpList = (array)$_POST['campaign_list'];
            $i = 0;
            $_list = array();
            
            foreach( $cmpList as $li ){
                $_list[$i] = sanitize_text_field($li);
                $i++;
            }   
            
            if( $objSEC->is_option_available('migla_campaign_order') )
            {
                $objO->update( 'migla_campaign_order', $_list, 'array' );
            }

        }
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_restore_form')){
function TotalDonationsAjax_restore_form()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        //1.Update the field structure
        $obj = new CLASS_MIGLA_FORM;

        $obj_fields = new CForm_Fields;
        $fields = $obj_fields->form_fields();

        //update form structure
        $obj->update_form( sanitize_text_field($_POST['form_id']),
                              "structure",
                              $fields,
                              "%s",
                              "array"
                );

        //update form field meta
        $obj->restore_form_meta( sanitize_text_field($_POST['form_id']),
                                 "fields",
                                 $fields,
                                 "array"
                                );
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_mulval_CForm')){
function TotalDonationsAjax_update_mulval_CForm()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $obj = new CLASS_MIGLA_FORM;
        $list = (array)$_POST['list'];
          
        if( count( $list ) > 0 )
        {
            foreach( $list as $item )
            {
                $post = (array)$item;
                $formid = sanitize_text_field($post['form_id']);
                $key = sanitize_text_field($post['key']);
                $value = sanitize_text_field($post['val']);
                $valtype = sanitize_text_field($post['valtype']);  

                if( $post['table'] == "form" )
                {
                    $coltype = sanitize_text_field($post['coltype']);

                    $obj->update_form($formid,
                                    $key,
                                    $value,
                                    $coltype,
                                    $valtype
                                );
                }else{

                  $obj->update_form_meta( $formid,
                                          sanitize_text_field($post['language']),
                                          $key,
                                          $value,
                                          $valtype
                                    );
                }
            }//each item
        }//if list not empty
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_formfields')){
function TotalDonationsAjax_update_formfields()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        //1.Update the field structure
        $obj = new CLASS_MIGLA_FORM;
        
        $value_type = sanitize_text_field($_POST['type']);
        $form_id = sanitize_text_field($_POST['form_id']);
        $column_type = sanitize_text_field($_POST['coltype']);
        $key = sanitize_text_field($_POST['key']);
          
        if( $value_type == "array" || $value_type == "json" ){
            $value = $_POST['val'];            
        }else{
            $value = sanitize_text_field($_POST['val']);
        }
        
        $obj->update_form( $form_id,
                            $key,
                            $value,
                            $column_type,
                            $value_type
                        );

        $newStruct = (array)$_POST['val'];

        $trans_fields = $obj->get_specific_metainfo( $form_id, 'all', 'fields' );

        $new_title = array();
        $k = 0;
        foreach( $newStruct as $sections )
        {
            $new_title[$k] = $sections['title'];
            $k++;
        }

        $titles = array();
        $labels = array();
        $labels_uid = array();
        $mergeval = array();


        if( !empty($trans_fields) )
        {
            foreach($trans_fields as $lang => $array ) //for each language
            {
                $i = 0;

                $sections = $array['fields'];

                foreach( $sections as $sect )
                {
                    if(isset($new_title[$i])){
                        $titles[$i] = $new_title[$i];
                    }else{
                         $titles[$i] = $sect['title'];
                    }

                    if( isset($sect['child']) && !empty($sect['child']) )
                    {
                        $children = (array)$sect['child'];
                        $k = 0;

                        foreach($children as $child)
                        {
                            $labels_uid[$k] = $child['uid'];
                            $labels[($child['uid'])] = $child['label'];
                            $k++;
                        }
                    }

                    $i++;
                }//foreach sections

                //fill new one
                $j = 0;
                foreach( $newStruct as $sections )
                {
                    $mergeval[$j]['title'] = $titles[$j];

                    if( isset($sections['child']) ){
                        if( !empty($sections['child']) ){

                            $children = (array)$sections['child'];
                            $k = 0;
                            $mergeval[$j]['child'] = array();

                            foreach($children as $child){
                                $uid = $child['uid'];
                                $id = $child['id'];
                                $lbl = $child['label'];

                                $mergeval[$j]['child'][$k]['uid'] = $uid;
                                $mergeval[$j]['child'][$k]['id'] = $id;

                                $mergeval[$j]['child'][$k]['label'] = $lbl;

                                $k++;
                            }
                        }
                    }

                    $j++;
                }

                //save
                $obj->update_form_meta( $form_id,
                                        $lang,
                                        'fields',
                                        $mergeval,
                                        'array'
                                        );
            }
        }

    }

    echo json_encode($mergeval);

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_formopt')){
function TotalDonationsAjax_update_formopt()
{
    global $wpdb;    
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {

        $objO = new MIGLA_OPTION;

        if( $objSEC->is_option_available('migla_hideUndesignated') ){
            $objO->update( 'migla_hideUndesignated',
                            sanitize_text_field($_POST['hideUndesignated']),
                            'string'
                        );
        }

        if( $objSEC->is_option_available('migla_show_bar') ){
            $objO->update( 'migla_show_bar',
                            sanitize_text_field($_POST['showbar']),
                            'string'
                        );
        }

        $objL = new MIGLA_LOCAL;
        $UndesignatedLabels = array();

        $languages = $objL->get_languages();

        foreach($languages as $code => $array ){
            $UndesignatedLabels[$code] = sanitize_text_field($_POST['undesignated_default']);
        }

        if( $objSEC->is_option_available('migla_undesignLabel') ){
            $objO->update( 'migla_undesignLabel',
                            $UndesignatedLabels,
                            'array'
                        );
        }

        $sql = "UPDATE {$wpdb->prefix}migla_campaign SET multi_list = '0'";
        $wpdb->get_results($sql);

        if( !empty($_POST['showCampaign']) )
        {
            $arrays = (array)$_POST['showCampaign'];

            foreach( $arrays as $id ){
                $wpdb->update( "{$wpdb->prefix}migla_campaign",
                                array( "multi_list" => '1' ),
                                array( "id" => $id ),
                                array( '%s' ),
                                array( '%d' )
                );

            }//foreach

            if( $objSEC->is_option_available('migla_campaign_order') ){
                $objO->update( 'migla_campaign_order',
                               $arrays ,
                                'array'
                            );
            }

        }//if not empty campaign
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_multival')){
function TotalDonationsAjax_update_multival()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) ){
        $obj = new CLASS_MIGLA_FORM;
        $key = sanitize_text_field($_POST['uid']);
        $list_value = array();

        foreach((array)$_POST['listVal'] as $list){
            if(isset($list['lVal']) && isset($list['lLbl'])){
                $temp = array('lval' => "", 'lLbl' => "");
                
                $temp['lVal'] = sanitize_text_field($list['lVal']);
                $temp['lLbl'] = sanitize_text_field($list['lLbl']);
                
                $list_value[] = $temp;
            }
        }

        $obj->update_form_meta( sanitize_text_field($_POST['formid']),
                                sanitize_text_field($_POST['language']),
                                $key,
                                $list_value,
                                'array'
                                );
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_delete_postmeta')){
function TotalDonationsAjax_delete_postmeta()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
       $id  = sanitize_text_field($_POST['id']);
       $key = sanitize_text_field($_POST['key']);

       global $wpdb;
       $sql = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = %s and post_id = %d" ;

       $wpdb->query( $wpdb->prepare( $sql, $key, $id ) );
    }

   die();
}
}

if (!function_exists('TotalDonationsAjax_update_formgroup')){
function TotalDonationsAjax_update_formgroup()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        //1.Update the field structure
        $obj = new CLASS_MIGLA_FORM;

        $value_type = sanitize_text_field($_POST['type']);
        $form_id    = sanitize_text_field($_POST['form_id']);
        $column_type = sanitize_text_field($_POST['coltype']);
        $key        = sanitize_text_field($_POST['key']);
          
        if( $value_type == "array" || $value_type == "json" ){
            $value = $_POST['val'];            
        }else{
            $value = sanitize_text_field($_POST['val']);
        }
        
        $obj->update_form( $form_id,
                            $key,
                            $value,
                            $column_type,
                            $value_type
                        );                

        $newStruct  = (array)$_POST['val'];
        $pos        = $_POST['newpos'];
        $inserted   = $newStruct[$pos];

        $trans_fields = $obj->get_specific_metainfo( $form_id, 'all', 'fields' );

        if( !empty($trans_fields) ){
            foreach($trans_fields as $lang => $array ){
                $original = (array)unserialize($array['fields']);
                $mergeval = array_splice( $original, $pos, 0, $inserted );

                //save
                $obj-> update_form_meta( $form_id, $lang, 'fields', $mergeval, 'array' );
            }
        }
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_newformgroup')){
function TotalDonationsAjax_update_newformgroup()
{
    $objSEC = new MIGLA_SEC;
    global $wpdb;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        //1.Update the field structure
        $obj = new CLASS_MIGLA_FORM;

        $value_type = sanitize_text_field($_POST['type']);
        $form_id    = sanitize_text_field($_POST['form_id']);
        $column_type = sanitize_text_field($_POST['coltype']);
        $key        = sanitize_text_field($_POST['key']);
          
        $value = $_POST['val'];   
        
        $obj->update_form( $form_id,
                            $key,
                            $value,
                            $column_type,
                            $value_type
                        );                

        $newStruct  = (array)$_POST['val'];

        //save
        $wpdb->update( $wpdb->prefix . "migla_form_meta",
                array( "meta_value" => serialize($newStruct)),
                array( "form_id" => $form_id,
                        "meta_key" => "fields"
                        ),
                array( "%s" ),
                array( "%d", "%s" )
            );

    }

    die();
}
}

/*Frontend List*/
$ajax_frontend_list = array( "TotalDonationsAjax_update_user_access",
                            "TotalDonationsAjax_update_me"
                        );

if (!function_exists('TotalDonationsAjax_update_me')){
function TotalDonationsAjax_update_me()
{
    $objO = new MIGLA_OPTION;
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
  		if( $objSEC->is_option_available(sanitize_text_field($_POST['key'])) )
  		{
            $value_type = sanitize_text_field($_POST['valtype']);
            $key = sanitize_text_field($_POST['key']);
              
            if( $value_type == "array" || $value_type == "json" ){
                $value = $_POST['value'];            
            }else{
                $value = sanitize_text_field($_POST['value']);
            }  		    
  		    
		    $objO->update( $key,
	    				    $value,
    	    				$value_type
	    				 );
  		}
    }

   die();
}
}

if (!function_exists('TotalDonationsAjax_update_user_access')){
function TotalDonationsAjax_update_user_access()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
  		$uObj = (array)$_POST['td_users'];

  	    foreach($uObj as $uTD)
  	    {
      		$role = sanitize_text_field($uTD['role']);
  	    	$user = sanitize_text_field($uTD['userid']);
  	    	
  		    if( $role == 'no_role' ){
                $u = new WP_User( $user );
                $u->remove_role( 'totaldonation-accountant' );

  		    }else if( strpos($role, 'totaldonation') >= 0 )
  		    {
  		        $u = new WP_User( $user );
                $u->add_role('contributor');
  		        $u->add_role( $role );
  		    }
  	    }
    }

    die();
}
}

//THEME
$ajax_theme_list = array( 'TotalDonationsAjax_update_form_theme',
                          'TotalDonationsAjax_update_progressBar_theme',
                          'TotalDonationsAjax_update_circle_layout',
                          'TotalDonationsAjax_reset_theme',
                          'TotalDonationsAjax_reset_progressbar',
                          'TotalDonationsAjax_reset_circle'
                        );

if (!function_exists('TotalDonationsAjax_update_form_theme')){
function TotalDonationsAjax_update_form_theme()
{
    $objO = new MIGLA_OPTION;
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        if( $objSEC->is_option_available( 'migla_2ndbgcolor' ) ){
            $objO->update( 'migla_2ndbgcolor',
                            sanitize_text_field($_POST['backgroundcolor']),
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_2ndbgcolorb' ) ){
            $objO->update( 'migla_2ndbgcolorb',
                            sanitize_text_field($_POST['panelborder']),
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_bglevelcolor' ) ){
            $objO->update( 'migla_bglevelcolor',
                            sanitize_text_field($_POST['bglevelcolor']),
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_borderlevelcolor' ) ){
            $objO->update( 'migla_borderlevelcolor',
                            sanitize_text_field($_POST['borderlevelcolor']),
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_borderlevel' ) ){
            $objO->update( 'migla_borderlevel',
                            sanitize_text_field($_POST['borderlevelWidth']),
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_bglevelcoloractive' ) ){
            $objO->update( 'migla_bglevelcoloractive',
                            sanitize_text_field($_POST['bglevelcoloractive']),
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_tabcolor' ) ){
            $objO->update( 'migla_tabcolor',
                            sanitize_text_field($_POST['tabcolor']),
                           'text'
                        );
        }
    }

    echo "done";
    die();
}
}

if (!function_exists('TotalDonationsAjax_update_progressBar_theme')){
function TotalDonationsAjax_update_progressBar_theme()
{
    $objO = new MIGLA_OPTION;
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        if( $objSEC->is_option_available( 'migla_borderRadius' ) )
        {
            $objO->update( 'migla_borderRadius',
                            sanitize_text_field($_POST['borderRadius']),
                           'text'
                        );
        }

        if( $objSEC->is_option_available( 'migla_wellboxshadow' ) )
        {
            $objO->update( 'migla_wellboxshadow',
                            sanitize_text_field($_POST['wellboxshadow']),
                           'text'
                        );
        }

        if( $objSEC->is_option_available( 'migla_progbar_info' ) )
        {
            $objO->update( 'migla_progbar_info',
                            sanitize_text_field($_POST['progbar_info']),
                           'text'
                        );
        }

        if( $objSEC->is_option_available( 'migla_bar_color' ) )
        {
            $objO->update( 'migla_bar_color',
                            sanitize_text_field($_POST['bar_color']),
                           'text'
                        );
        }

        if( $objSEC->is_option_available( 'migla_progressbar_background' ) )
        {
            $objO->update( 'migla_progressbar_background',
                            sanitize_text_field($_POST['progressbar_background']),
                           'text'
                        );
        }

        if( $objSEC->is_option_available( 'migla_progressbar_background' ) )
        {
            $effects = (array)$_POST['styleEffects'];

            $effects_Array = array( 'Stripes' => sanitize_text_field($effects[0]),
                                    'Pulse' => sanitize_text_field($effects[1]),
                                    'AnimatedStripes' => sanitize_text_field($effects[2]),
                                    'Percentage' => sanitize_text_field($effects[3])
                                );

            $objO->update( 'migla_bar_style_effect' , $effects_Array, 'array' );        
        }
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_reset_theme')){
function TotalDonationsAjax_reset_theme()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
       //THEME SETTINGS
        $objO = new MIGLA_OPTION;

        if( $objSEC->is_option_available('migla_tabcolor') ){
            $objO->update('migla_tabcolor', '#eeeeee', 'text');
        }

        if( $objSEC->is_option_available('migla_2ndbgcolor') ){
            $objO->update( 'migla_2ndbgcolor' , '#FAFAFA,1', 'text' );
        }

        if( $objSEC->is_option_available('migla_2ndbgcolorb') ){
            $objO->update( 'migla_2ndbgcolorb' , '#DDDDDD,1,1', 'text' );
        }

        if( $objSEC->is_option_available('migla_borderRadius') ){
            $objO->update( 'migla_borderRadius' , '8,8,8,8', 'text' );
        }

        if( $objSEC->is_option_available('migla_bglevelcolor') ){
            $objO->update( 'migla_bglevelcolor', '#eeeeee', 'text' );
        }

        if( $objSEC->is_option_available('migla_bglevelcoloractive') ){
            $objO->update( 'migla_bglevelcoloractive', '#ba9cb5', 'text' );
        }

        if( $objSEC->is_option_available('migla_borderlevelcolor') ){
            $objO->update( 'migla_borderlevelcolor', '#b0b0b0', 'text' );
        }

        if( $objSEC->is_option_available('migla_borderlevel') ){
            $objO->update( 'migla_borderlevel', '1', 'text' );
        }
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_reset_progressbar')){
function TotalDonationsAjax_reset_progressbar()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
       //THEME SETTINGS
        $objO = new MIGLA_OPTION;

        $barinfo = "We have collected [total] of our [target] target. It is [percentage] of our goal for the [campaign] campaign";

        if( $objSEC->is_option_available('migla_progbar_info') ){
            $objO->update( 'migla_progbar_info', $barinfo, 'text' );
        }
        if( $objSEC->is_option_available('migla_bar_color') ){
            $objO->update( 'migla_bar_color' , '#428bca,1', 'text' );
        }
        if( $objSEC->is_option_available('migla_progressbar_background') ){
            $objO->update( 'migla_progressbar_background', '#bec7d3,1', 'text' );
        }
        if( $objSEC->is_option_available('migla_wellboxshadow') ){
            $objO->update( 'migla_wellboxshadow', '#969899,1, 1,1,1,1', 'text' );
        }
        if( $objSEC->is_option_available('migla_borderRadius') ){
            $objO->update( 'migla_borderRadius', '8,8,8,8', 'text' );
        }

        $arr = array( 'Stripes' => 'yes',
                        'Pulse' => 'yes',
                        'AnimatedStripes' => 'yes',
                        'Percentage' => 'yes'
                    );

        if( $objSEC->is_option_available('migla_bar_style_effect') ){
            $objO->update( 'migla_bar_style_effect' , $arr, 'array' );
        }
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_reset_circle')){
function TotalDonationsAjax_reset_circle()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
       //THEME SETTINGS
        $objO = new MIGLA_OPTION;

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

        if( $objSEC->is_option_available('migla_circle_settings') ){
            $objO->update( 'migla_circle_settings', $circle, 'array' );
        }
        if( $objSEC->is_option_available('migla_circle_text1') ){
            $objO->update( 'migla_circle_text1', 'Total', 'text' );
        }
        if( $objSEC->is_option_available('migla_circle_text2') ){
            $objO->update( 'migla_circle_text2', 'Target', 'text' );
        }
        if( $objSEC->is_option_available('migla_circle_text3') ){    
            $objO->update( 'migla_circle_text3', 'Donor', 'text' );
        }
        if( $objSEC->is_option_available('migla_circle_textalign') ){
            $objO->update( 'migla_circle_textalign', 'left_right', 'text' );
        }
    }

    die();
}
}

if(!function_exists('TotalDonationsAjax_update_circle_layout')){
function TotalDonationsAjax_update_circle_layout()
{
    $objO = new MIGLA_OPTION;
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( $_POST['auth_owner'], $_POST['auth_token'], $_POST['auth_session']) )
    {
        if( $objSEC->is_option_available( 'migla_circle_textalign' ) )
        {
            $objO->update( 'migla_circle_textalign',
                            $_POST['circle_textalign'],
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_circle_text1' ) )
        {
            $objO->update( 'migla_circle_text1',
                            $_POST['circle_text1'],
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_circle_text2' ) )
        {
            $objO->update( 'migla_circle_text2',
                            $_POST['circle_text2'],
                           'text'
                        );
        }
        if( $objSEC->is_option_available( 'migla_circle_text3' ) )
        {
            $objO->update( 'migla_circle_text3',
                            $_POST['circle_text3'],
                           'text'
                        );
        }
    }

    die();
}
}

//Dashboard
$ajax_dashboard_list = array( "TotalDonationsAjax_1y_GraphData",
                              "TotalDonationsAjax_1m_GraphData",
                              "TotalDonationsAjax_6m_GraphData",
                              "TotalDonationsAjax_getThisMonth_total",
                              "TotalDonationsAjax_get7days_total",
                              "TotalDonationsAjax_getToday_total",
                              "TotalDonationsAjax_thismonth_donations",
                              "TotalDonationsAjax_last2weeks_donations",
                              "TotalDonationsAjax_last_donations"
                        );

if (!function_exists('TotalDonationsAjax_1m_GraphData')){
function TotalDonationsAjax_1m_GraphData()
{
                            $objSEC = new MIGLA_SEC;

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $offlines = array( array('label' => 'week1', 'amount' => 0),
                                                    array('label' => 'week2', 'amount' => 0),
                                                    array('label' => 'week3', 'amount' => 0),
                                                    array('label' => 'week4', 'amount' => 0),
                                                  );
                                $onlines = array( array('label' => 'week1', 'amount' => 0),
                                                    array('label' => 'week2', 'amount' => 0),
                                                    array('label' => 'week3', 'amount' => 0),
                                                    array('label' => 'week4', 'amount' => 0),
                                                  );

                                $objD = new CLASS_MIGLA_DONATION;

                                $result = $objD->get_recent_donation( 30,
                                                                  'DAY',
                                                                  '',
                                                                  '1',
                                                                  '1',
                                                                  '',
                                                                  'asc',
                                                                  'date_created',
                                                                  1
                                                );

                                $result2 = $objD->get_recent_donation( 30,
                                                                  'DAY',
                                                                  '',
                                                                  '1',
                                                                  '2',
                                                                  '',
                                                                  'asc',
                                                                  'date_created',
                                                                  1
                                                );

                                    if( !empty($result) )
                                    {
                                        foreach($result as $row )
                                        {
                                            $week_num = 0;

                                            $donationDate = substr( $row['date_created'] , 0, 10 );
                                            $time2 = strtotime($donationDate);
                                            $time1 = strtotime(date('Y-m-d'));

                                            $diff = $time1 - $time2;

                                            $day = date('d', $diff);

                                            if( $day <= 7 ){
                                                $week_num = 3;
                                            }else if( $day <= 14 ){
                                                $week_num = 2;
                                            }else if( $day <= 21 ){
                                                $week_num = 1;
                                            }else if( $day <= 28 ){
                                                $week_num = 0;
                                            }

                                            $onlines[$week_num]['amount'] += (float)$row['amount'];
                                        }
                                    }

                                    if( !empty($result2) )
                                    {
                                        foreach($result2 as $row )
                                        {
                                            $week_num = 0;

                                            $donationDate = substr( $row['date_created'] , 0, 10 );
                                            $time2 = strtotime($donationDate);
                                            $time1 = strtotime(date('Y-m-d'));

                                            $diff = $time1 - $time2;

                                            $day = date('d', $diff);

                                            if( $day <= 7 ){
                                                $week_num = 3;
                                            }else if( $day <= 14 ){
                                                $week_num = 2;
                                            }else if( $day <= 21 ){
                                                $week_num = 1;
                                            }else if( $day <= 28 ){
                                                $week_num = 0;
                                            }

                                            $offlines[$week_num]['amount'] += (float)$row['amount'];
                                        }
                                    }


                                $resp = array();
                                $resp[0] = $onlines;
                                $resp[1] = $offlines;
                                $resp[2] = count($result) . count($result2);


                              echo json_encode($resp);
                            }

                            die();
}
}

if (!function_exists('TotalDonationsAjax_6m_GraphData')){
function TotalDonationsAjax_6m_GraphData()
{
                            $objSEC = new MIGLA_SEC;

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $labels = array('','','','','','');

                                $y = date('Y');
                                $m = date('m');
                                $c = 5;

                                $labels[$c] = $y.'-'.$m;
                                $c--;

                                while( $c >= 0 ){
                                    if( intval($m) <= 1 ){
                                        $y = intval($y) - 1;
                                        $m = '12';
                                        $labels[$c] = $y.'-'.$m;
                                    }else{
                                        $m = intval($m) - 1;
                                        if(strlen($m) <= 1 ) $m = '0'.$m;
                                        $labels[$c] = $y.'-'.$m;
                                    }

                                    $c--;
                                }


                                $i = 0;
                                foreach( $labels as $lbl ){
                                    $onlines[$i]['label'] = $lbl;
                                    $onlines[$i]['amount'] = 0;
                                    $offlines[$i]['label'] = $lbl;
                                    $offlines[$i]['amount'] = 0;
                                    $i++;
                                }

                                $objD = new CLASS_MIGLA_DONATION;

                                $result = $objD->get_recent_donation( 6,
                                                                  'MONTH',
                                                                  '',
                                                                  '1',
                                                                  '1',
                                                                  '',
                                                                  'ASC',
                                                                  'date_created',
                                                                  1
                                                );

                                $result2 = $objD->get_recent_donation( 6,
                                                                  'MONTH',
                                                                  '',
                                                                  '1',
                                                                  '2',
                                                                  '',
                                                                  'asc',
                                                                  'date_created',
                                                                  1
                                                );

                                if( !empty($result) )
                                {
                                    foreach($result as $row )
                                    {
                                        $date = substr( $row['date_created'], 0, 7 );

                                        $idx = 0;

                                        if( array_search($date, $labels) != false ) $idx = array_search($date, $labels);

                                        $onlines[$idx]['amount'] += (float)$row['amount'];
                                    }
                                }


                                if( !empty($result2) )
                                {
                                    foreach($result2 as $row )
                                    {
                                        $date = substr( $row['date_created'], 0, 7 );

                                        $idx = 0;

                                        if( array_search($date, $labels) != false ) $idx = array_search($date, $labels);

                                        $offlines[$idx]['amount'] += (float)$row['amount'];
                                    }
                                }

                                $resp = array();
                                $resp[0] = $onlines;
                                $resp[1] = $offlines;

                              echo json_encode($resp);
                          }

                          die();
}
}

if (!function_exists('TotalDonationsAjax_1y_GraphData')){
function TotalDonationsAjax_1y_GraphData()
{
                            $objSEC = new MIGLA_SEC;

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $labels = array('','','','','','','','','','','','');

                                $y = date('Y');
                                $m = date('m');
                                $c = 11;

                                $labels[$c] = $y.'-'.$m;
                                $c--;

                                while( $c >= 0 ){
                                    if( intval($m) <= 1 ){
                                        $y = intval($y) - 1;
                                        $m = '12';
                                        $labels[$c] = $y.'-'.$m;
                                    }else{
                                        $m = intval($m) - 1;
                                        if(strlen($m) <= 1 ) $m = '0'.$m;
                                        $labels[$c] = $y.'-'.$m;
                                    }

                                    $c--;
                                }

                                $i = 0;
                                foreach( $labels as $lbl ){
                                    $onlines[$i]['label'] = $lbl;
                                    $onlines[$i]['amount'] = 0;
                                    $offlines[$i]['label'] = $lbl;
                                    $offlines[$i]['amount'] = 0;
                                    $i++;
                                }

                                $objD = new CLASS_MIGLA_DONATION;


                                $result = $objD->get_recent_donation( 12,
                                                                  'MONTH',
                                                                  '',
                                                                  '1',
                                                                  '1',
                                                                  '',
                                                                  'asc',
                                                                  'date_created',
                                                                  1
                                                );

                                $result2 = $objD->get_recent_donation( 12,
                                                                  'MONTH',
                                                                  '',
                                                                  '1',
                                                                  '2',
                                                                  '',
                                                                  'asc',
                                                                  'date_created',
                                                                  1
                                                );


                                if( !empty($result) )
                                {
                                    foreach($result as $row )
                                    {
                                        $date = substr( $row['date_created'], 0, 7 );

                                        $idx = 0;

                                        if( array_search($date, $labels) != false ) $idx = array_search($date, $labels);

                                        $onlines[$idx]['amount'] += (float)$row['amount'];
                                    }
                                }


                                if( !empty($result2) )
                                {
                                    foreach($result2 as $row )
                                    {
                                        $date = substr( $row['date_created'], 0, 7 );

                                        $idx = 0;

                                        if( array_search($date, $labels) != false ) $idx = array_search($date, $labels);

                                        $offlines[$idx]['amount'] += (float)$row['amount'];
                                    }
                                }


                                $resp = array();
                                $resp[0] = $onlines;
                                $resp[1] = $offlines;

                              echo json_encode($resp);
                          }

                          die();
                        }
}

if (!function_exists('TotalDonationsAjax_get7days_total')){
function TotalDonationsAjax_get7days_total()
{
                            $objSEC = new MIGLA_SEC;
                            $result = array(0, 0, 0, 0);

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $objD = new CLASS_MIGLA_DONATION;

                                $total_online = $objD->get_total_donation_by_date( '',
                                                                            '',
                                                                            '7',
                                                                            '1',
                                                                            1
                                                  );

                                $total_offline = $objD->get_total_donation_by_date( '',
                                                                            '',
                                                                            '7',
                                                                            '2',
                                                                            1
                                                  );

                                $now  = strtotime( date('Y-m-d') );
                                $day7 = $now - ( 7 * 86400 );
                                $sdate = date( 'Y-m-d', $day7 );

                                $wc_total = $objD->get_wc_total_startfrom( $sdate );

                                $result = array( '0' => (float)$total_online,
                                              '1' => (float)$total_offline,
                                              '2' => (float)$total_online + (float)$total_offline + (float)$wc_total,
                                              '3' => (float)$wc_total
                                       );

                                echo json_encode($result);
                            }

                            die();
                        }
}

if (!function_exists('TotalDonationsAjax_getThisMonth_total')){
function TotalDonationsAjax_getThisMonth_total()
{
                            $objSEC = new MIGLA_SEC;
                            $result = array(0, 0, 0, 0);

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $objD = new CLASS_MIGLA_DONATION;

                                $total_online = $objD->get_total_donation_by_date(
                                                                  date('Y'),
                                                                  date('m'),
                                                                  '',
                                                                  '1',
                                                                  1 );

                                $total_offline = $objD->get_total_donation_by_date(
                                                                  date('Y'),
                                                                  date('m'),
                                                                  '',
                                                                  '2',
                                                                  1 );

                                $wc_total = $objD->get_wc_total_this_month();

                                $result = array( '0' => (float)$total_online,
                                              '1' => (float)$total_offline,
                                              '2' => (float) $total_online + (float) $total_offline + (float)$wc_total,
                                              '3' => (float)$wc_total
                                       );

                                echo json_encode($result);
                            }
                            die();
                        }
}

if (!function_exists('TotalDonationsAjax_getToday_total')){
function TotalDonationsAjax_getToday_total()
{
                            $objSEC = new MIGLA_SEC;
                            $result = array(0, 0, 0, 0);

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $objD = new CLASS_MIGLA_DONATION;

                                $total_online = $objD->get_total_bydate( '',
                                                            '',
                                                        date('Y-m-d'),
                                                        '1',
                                                        '',
                                                        '',
                                                        1
                                                    );

                                $total_offline = $objD->get_total_bydate( '',
                                                            '',
                                                        date('Y-m-d'),
                                                        '2',
                                                        '',
                                                        '',
                                                        1
                                                    );

                                $wc_total = $objD->get_wc_total_startfrom( date('Y-m-d') );

                                $result = array( '0' => (float)$total_online,
                                              '1' => (float)$total_offline,
                                              '2' => (float) $total_online + (float) $total_offline + (float)$wc_total,
                                              '3' => (float)$wc_total
                                       );

                                echo json_encode($result);
                            }

                            die();
                        }
}

if (!function_exists('TotalDonationsAjax_thismonth_donations')){
function TotalDonationsAjax_thismonth_donations()
{
                            $objSEC = new MIGLA_SEC;

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $objD = new CLASS_MIGLA_DONATION;

                                $result = $objD->get_recent_donation_by_timediff( date('Y'),
                                                                    date('m'),
                                                                    '',
                                                                    '',
                                                                    '1',
                                                                    '',
                                                                    'DESC',
                                                                    'date_created',
                                                                    1
                                            );

                                $resp = array();

                                $objM = new MIGLA_MONEY;

                                $i = 0;

                                if(!empty($result))
                                {
                                    foreach($result as $row)
                                    {
                                        $resp[$i]['firstname']      = $row['firstname'];
                                        $resp[$i]['lastname']       = $row['lastname'];
                                        $resp[$i]['amount']         = $row['amount'];
                                        $resp[$i]['repeating']      = $row['repeating'];
                                        $resp[$i]['anonymous']      = $row['anonymous'];

                                        $datetime               = $row['date_created'];
                                        $dateandtime            = explode(" ", $datetime);

                                        $resp[$i]['date']           = '';
                                        $resp[$i]['time']           = '';

                                        if( !empty($dateandtime)  && isset($dateandtime[0]) && isset($dateandtime[1]) ){
                                            $resp[$i]['date']           = $dateandtime[0];
                                            $resp[$i]['time']           = $dateandtime[1];
                                        }

                                        $resp[$i]['country']        = $row['country'];
                                        $resp[$i]['state']          = '';
                                        $resp[$i]['province']       = '';

                                        $resp[$i]['address'] = $objD->get_donationmeta( 'miglad_address', $row['id']);

                                        if( $resp[$i]['country'] == 'United States' ){
                                            $resp[$i]['state'] = $objD->get_donationmeta( 'miglad_state', $row['id']);
                                        }else if( $resp[$i]['country'] == 'Canada' ){
                                            $resp[$i]['province'] = $objD->get_donationmeta( 'miglad_province', $row['id']);
                                        }

                                        $i++;

                                    }//foreach
                                }

                                $objO = new MIGLA_OPTION;

                                if( $objSEC->is_option_available('recent_donation_dashboard') )
                                {
                                    $objO->update( 'recent_donation_dashboard',
                                                    $_POST['day_value'],
                                                    'string'
                                                );
                                }

                                echo json_encode($resp);
                            }

                            die();
}
}

if (!function_exists('TotalDonationsAjax_last_donations')){
function TotalDonationsAjax_last_donations()
{
                            $objSEC = new MIGLA_SEC;

                            if( $objSEC->is_this_the_owner( $_POST['auth_owner'], $_POST['auth_token'], $_POST['auth_session'])  )
                            {
                                $objD = new CLASS_MIGLA_DONATION;

                                $result = $objD->get_recent_donation( '', //time
                                                                '', //period
                                                                $_POST['day_count'], //limit
                                                                '1', //complete
                                                                '1', //donation type
                                                                '', //campaign
                                                                'DESC',
                                                                'date_created',
                                                                1 //sttaus
                                            );

                                $objM = new MIGLA_MONEY;

                                $resp = array();
                                $i = 0;

                                if(!empty($result))
                                {
                                    foreach($result as $row)
                                    {
                                        $resp[$i]['firstname']      = $row['firstname'];
                                        $resp[$i]['lastname']       = $row['lastname'];
                                        $resp[$i]['amount']         = $row['amount'];
                                        $resp[$i]['repeating']      = $row['repeating'];
                                        $resp[$i]['anonymous']      = $row['anonymous'];

                                        $datetime               = $row['date_created'];
                                        $dateandtime            = explode(" ", $datetime);

                                        $resp[$i]['date']           = '';
                                        $resp[$i]['time']           = '';

                                        if( !empty($dateandtime)  && isset($dateandtime[0]) && isset($dateandtime[1]) ){
                                            $resp[$i]['date']           = $dateandtime[0];
                                            $resp[$i]['time']           = $dateandtime[1];
                                        }

                                        $resp[$i]['country']        = $row['country'];
                                        $resp[$i]['state']          = '';
                                        $resp[$i]['province']       = '';

                                        $resp[$i]['address'] = $objD->get_donationmeta( 'miglad_address', $row['id']);

                                        if( $resp[$i]['country'] == 'United States' ){
                                            $resp[$i]['state'] = $objD->get_donationmeta( 'miglad_state', $row['id']);
                                        }else if( $resp[$i]['country'] == 'Canada' ){
                                            $resp[$i]['province'] = $objD->get_donationmeta( 'miglad_province', $row['id']);
                                        }

                                        $i++;

                                    }//foreach
                                }

                                $objO = new MIGLA_OPTION;

                                if( $objSEC->is_option_available('recent_donation_dashboard') )
                                {
                                    $objO->update( 'recent_donation_dashboard',
                                                    $_POST['day_value'],
                                                    'string'
                                                );
                                }

                                echo json_encode($resp);
                            }
                            die();
                        }
}

if (!function_exists('TotalDonationsAjax_last2weeks_donations')){
function TotalDonationsAjax_last2weeks_donations()
{
                            $objSEC = new MIGLA_SEC;

                            if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
                            {
                                $objD = new CLASS_MIGLA_DONATION;

                                $result = $objD->get_recent_donation( '2',
                                                                  'WEEK',
                                                                  '',
                                                                  1,
                                                                  '1',
                                                                  '',
                                                                  'DESC',
                                                                  'date_created',
                                                                  1
                                                        );


                                $objM = new MIGLA_MONEY;

                                $resp = array();
                                $i = 0;

                                if(!empty($result))
                                {
                                    foreach($result as $row)
                                    {
                                        $resp[$i]['firstname']      = $row['firstname'];
                                        $resp[$i]['lastname']       = $row['lastname'];
                                        $resp[$i]['amount']         = $row['amount'];
                                        $resp[$i]['repeating']      = $row['repeating'];
                                        $resp[$i]['anonymous']      = $row['anonymous'];

                                        $datetime               = $row['date_created'];
                                        $dateandtime            = explode(" ", $datetime);

                                        $resp[$i]['date']           = '';
                                        $resp[$i]['time']           = '';

                                        if( !empty($dateandtime)  && isset($dateandtime[0]) && isset($dateandtime[1]) ){
                                            $resp[$i]['date']           = $dateandtime[0];
                                            $resp[$i]['time']           = $dateandtime[1];
                                        }

                                        $resp[$i]['country']        = $row['country'];
                                        $resp[$i]['state']          = '';
                                        $resp[$i]['province']       = '';

                                        $resp[$i]['address'] = $objD->get_donationmeta( 'miglad_address', $row['id']);

                                        if( $resp[$i]['country'] == 'United States' ){
                                            $resp[$i]['state'] = $objD->get_donationmeta( 'miglad_state', $row['id']);
                                        }else if( $resp[$i]['country'] == 'Canada' ){
                                            $resp[$i]['province'] = $objD->get_donationmeta( 'miglad_province', $row['id']);
                                        }

                                        $i++;

                                    }//foreach
                                }

                                $objO = new MIGLA_OPTION;

                                if( $objSEC->is_option_available('recent_donation_dashboard') )
                                {
                                    $objO->update( 'recent_donation_dashboard',
                                                    $_POST['day_value'],
                                                    'string'
                                                );
                                }

                                echo json_encode($resp);
                            }

                            die();
                        }
}

//Email
$ajax_email_list = array( 'TotalDonationsAjax_save_email_part1',
                          "TotalDonationsAjax_save_email_part2",
                          "TotalDonationsAjax_save_email_part3",
                          "TotalDonationsAjax_save_smtp",
                          "TotalDonationsAjax_test_email",
                          "TotalDonationsAjax_translate_redirect",
                          "TotalDonationsAjax_get_thank_you_page_url",
                          "TotalDonationsAjax_setup_emailsent"
                  );

if (!function_exists('TotalDonationsAjax_save_email_part1')){
function TotalDonationsAjax_save_email_part1()
{
    $objSEC = new MIGLA_SEC;
    $resp = "";

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $objE = new MIGLA_EMAIL;
        $emails = (array) $_POST['notifies'];
        $notify_email = array();
        
        foreach($emails as $email){
            $notify_email[] = sanitize_text_field($email);    
        }

        $id = $objE->create_email( sanitize_text_field($_POST['form_id']),
                                    sanitize_text_field($_POST['replyTo']),
                                    sanitize_text_field($_POST['replyToName']),
                                    sanitize_text_field($_POST['is_pdf_on']),
                                    $notify_email,
                                    sanitize_text_field($_POST['is_thankyou_email']),
                                    sanitize_text_field($_POST['is_honoree_email']),
                                    sanitize_text_field($_POST['is_offline_email'])
                                );

        $resp = "TES";
    }

    echo $resp;
    die();
}
}

if (!function_exists('TotalDonationsAjax_save_email_part2')){
function TotalDonationsAjax_save_email_part2()
{
    $objSEC = new MIGLA_SEC;
    $email_id = "";
    $response = "";

    try{
        if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
        {
            $objE = new MIGLA_EMAIL;
    
            $email_id = sanitize_text_field($_POST['email_id']);
            $email_type = sanitize_text_field($_POST['email_type']);
            $language = sanitize_text_field($_POST['language']);
            
            $emails = (array)$_POST['notify_emails'];
            $notify_email = array();
            
            foreach($emails as $email){
                $notify_email[] = sanitize_text_field($email);    
            }                          
    
            if( empty($email_id) )
            {
                $email_id = $objE->create_email( sanitize_text_field($_POST['form_id']),
                                                sanitize_text_field($_POST['replyTo']),
                                                sanitize_text_field($_POST['replyToName']),
                                                sanitize_text_field($_POST['is_pdf_on']),
                                                $notify_email,
                                                sanitize_text_field($_POST['is_thankyou_email']),
                                                sanitize_text_field($_POST['is_honoree_email']),
                                                sanitize_text_field($_POST['is_offline_email'])
                                            );
            }
    
            $columns = array( "email_id" => $email_id,
                            "language"  => $language,
                            "type"      => $email_type,
                            "body"      => $_POST['email_body'],
                            "subject"   => sanitize_text_field($_POST['email_subject']),
                            "custom_message" => '',
                            "repeating" => '',
                            "anonymous" => sanitize_text_field($_POST['email_anonymous'])
                        );
    
            $columns_type = array( '%d',
                                    '%s',
                                    '%s',
                                    '%s',
                                    '%s',
                                    '%s',
                                    '%s',
                                    '%s'
                                );
    
            $objE->insert_meta_bylang( $email_id,
                                        $email_type,
                                        $language,
                                        $columns,
                                        $columns_type
                                    );
        
            $response = $email_id;
        }
    }catch(Exception $ex){
        $response = $e->errorMessage();
    }
    
    echo $response;
    die();
}
}

if (!function_exists('TotalDonationsAjax_save_email_part3')){
function TotalDonationsAjax_save_email_part3()
{
    $id = "";
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $objE = new MIGLA_EMAIL;
        $id = sanitize_text_field($_POST['email_id']);
        $form_id = sanitize_text_field($_POST['form_id']);
        $emails = (array)$_POST['notifies'];
        $notify_email = array();
        
        foreach($emails as $email){
            $notify_email[] = sanitize_text_field($email);    
        }     

        if( !empty($_POST['email_id']) )
        {
            $id = $objE->insert_column_by_id( $id,
                                            array( "notify_emails" => serialize($notify_email) ),
                                            array( '%s' )
                                        );
        }else{

            $id = $objE->init_column_by_id( $form_id,
                                            array( "form_id"   => $form_id,
                                                    "reply_to"      => '',
                                                    "reply_to_name" => '',
                                                    "attachment"    => '0',
                                                    "notify_emails" => serialize($notify_email),
                                                    "is_thankyou_email" => '1',
                                                    "is_honoree_email"  => '0'
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
        }

    }
    
    echo $id ;
    die();
}
}

if( !function_exists('TotalDonationsAjax_setup_emailsent') )
{
function TotalDonationsAjax_setup_emailsent()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        global $wpdb;
        
        $wpdb->update( $wpdb->prefix ."migla_email" , 
                        array( "is_thankyou_email" => $_POST['value'] ), 
                        array( "form_id" => $_POST['form'] ), 
                        array( "%s" ), 
                        array( "%d" ) 
                    );
    }
    
    die();
}
}

if (!function_exists('TotalDonationsAjax_save_smtp')){
function TotalDonationsAjax_save_smtp()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $objO = new MIGLA_OPTION;

  		if( $objSEC->is_option_available( 'migla_use_PHPMailer' ) )
  		{
		    $objO->update( 'migla_use_PHPMailer',
	    					sanitize_text_field($_POST['use_PHPMailer']),
	    					'text'
	    				);
  		}

  		if( $objSEC->is_option_available( 'migla_smtp_host' ) )
  		{
		    $objO->update( 'migla_smtp_host',
	    					sanitize_text_field($_POST['host']),
	    					'text'
	    				);
  		}

  	    if( $objSEC->is_option_available( 'migla_smtp_user' ) )
  		{
		    $objO->update( 'migla_smtp_user',
	    					sanitize_text_field($_POST['user']),
	    					'text'
	    				);
  		}

  		if( $objSEC->is_option_available( 'migla_smtp_password' ) )
  		{
		    $objO->update( 'migla_smtp_password',
	    					sanitize_text_field($_POST['password']),
	    					'text'
	    				);
  		}

  		if( $objSEC->is_option_available( 'migla_smtp_authenticated' ) )
  		{
		    $objO->update( 'migla_smtp_authenticated',
	    					sanitize_text_field($_POST['authenticated']),
	    					'text'
	    				);
  		}

  		if( $objSEC->is_option_available( 'migla_smtp_secure' ) )
  		{
		    $objO->update( 'migla_smtp_secure',
	    				    sanitize_text_field($_POST['security']),
	    					'text'
	    				);
  		}

  		if( $objSEC->is_option_available( 'migla_smtp_port' ) )
  		{
		    $objO->update( 'migla_smtp_port',
	    					sanitize_text_field($_POST['port']),
	    					'text'
	    				 );
  		}
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_test_email')){
function TotalDonationsAjax_test_email()
{
    $objSEC = new MIGLA_SEC;
    $msg = '';

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $record_id  = time();
        $data       = array();
        $status     = false;        
        $donor_email= sanitize_text_field($_POST['email']);
        $formId     = sanitize_text_field($_POST['form_id']);
        $language   = sanitize_text_field($_POST['language']);
        
        $objFf = new CForm_Fields;
        $data = $objFf->get_email_testing_form_fields();

        $data['miglad_email'] = $donor_email;
        $data['miglad_honoreeemail'] = $donor_email;
        $data['miglad_form_id'] = $formId;
        $data['f20191022105735_180'] = 'Mr.';

        $objE = new MIGLA_EMAIL;
        $status = $objE->email_procedure( $formId,
                                          $record_id,
                                          $data,
                                          $language 
                                    );

        if( $status )
        {
            $msg = '(This test email has been sent to '. $donor_email . ')';
        }else{
            $msg = '(This test email has NOT been sent to '. $donor_email . ')';
        }

        $objEmailLog = new MIGLA_LOG("email-");
        $objEmailLog->append( "[".current_time('mysql') . "] ". $msg );
    }

    echo $msg;
    die();
}
}

if (!function_exists('TotalDonationsAjax_translate_redirect')){
function TotalDonationsAjax_translate_redirect()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $rcd = new MIGLA_REDIRECT;
        $rcd->create_redirect(  sanitize_text_field($_POST['form_id']),
                                sanitize_text_field($_POST['language']),
                                $_POST['content'],
                                sanitize_text_field($_POST['pageid'])
                            );
    }

    die();
}
}

if(!function_exists('TotalDonationsAjax_get_thank_you_page_url')){
    function TotalDonationsAjax_get_thank_you_page_url()
    {
        global $wpdb;
        
        $objO = new MIGLA_OPTION;
        $preview_id = $objO->get_option("migla_preview_page");
        
        if( !empty($preview_id) ){
        }else{
            $preview_id = wp_insert_post(array('post_title'=>'Totaldonations Preview Thank You Page', 
                                        'post_type'=>'page',
                                        'post_content' => "[totaldonations_thank_you_page]"
                                    )
                                );
                                
            $objO->update("migla_preview_page", sanitize_text_field($preview_id), "text");
        }
        
        $results = array( "page" => $preview_id,
                        "url" => get_permalink($preview_id)        
                    );
        
        echo json_encode($results);
        die();
    }
}

//Stripe
$ajax_gtw_list = array( "TotalDonationsAjax_update_stripe_tab",
                        "TotalDonationsAjax_update_stripe_button",
                        "TotalDonationsAjax_update_paypal_button",
                        "TotalDonationsAjax_update_paypal_tab"
                        );

if (!function_exists('TotalDonationsAjax_update_stripe_tab')){
function TotalDonationsAjax_update_stripe_tab()
{
   $objSEC = new MIGLA_SEC;

   if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
   {
        $objL = new MIGLA_LOCAL;
        $objF = new CLASS_MIGLA_FORM;
        $language = $objL->get_origin();
        $stripes = $objF->get_stripe_tab();

        $stripes['tab'] = $_POST['tab'];
        $stripes['cardholder']['label']       = sanitize_text_field($_POST['cardholder_label']);
        $stripes['cardholder']['placeholder'] = sanitize_text_field($_POST['cardholder_placeholder']);
        $stripes['cardnumber']['label']       = sanitize_text_field($_POST['cardnumber_label']);
        $stripes['cardnumber']['placeholder'] = sanitize_text_field($_POST['cardnumber_placeholder']);
        $stripes['button']                    = sanitize_text_field($_POST['buttontext']);
        $stripes['loading_message']           = sanitize_text_field($_POST['message']);

        $objF->update_form_meta( 0,
                                $language,
                                'stripe_tab_info',
                                $stripes,
                                'array'
                            );
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_stripe_button')){
function TotalDonationsAjax_update_stripe_button()
{
    $objSEC = new MIGLA_SEC;
    $objO = new MIGLA_OPTION;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {

        if( $objSEC->is_option_available('migla_stripecssbtnstyle') )
        {
            $objO->update( 'migla_stripecssbtnstyle',
                        sanitize_text_field($_POST["buttonstyle"]),
                        'text'
                    );
            }

         if( $objSEC->is_option_available('migla_stripecssbtnclass') )
        {
            $objO->update( 'migla_stripecssbtnclass',
                    sanitize_text_field($_POST["btnclass"]),
                    'text'
                );
        }

        if( $objSEC->is_option_available('migla_stripebuttonurl') )
        {
            $objO->update( 'migla_stripebuttonurl',
                    sanitize_text_field($_POST["btnurl"]),
                    'text'
                );
        }

        if( $objSEC->is_option_available('miglaStripeButtonChoice') )
        {
            $objO->update( 'miglaStripeButtonChoice',
                sanitize_text_field($_POST["StripeButtonChoice"]),
                'text'
            );
        }
    }

  die();
}
}

if (!function_exists('TotalDonationsAjax_update_paypal_button')){
function TotalDonationsAjax_update_paypal_button()
{
    $objSEC = new MIGLA_SEC;
    $objO = new MIGLA_OPTION;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
  		if( $objSEC->is_option_available('miglaPaypalButtonChoice') )
  		{
		    $objO->update( 'miglaPaypalButtonChoice',
	    					sanitize_text_field($_POST["btn_choice"]),
	    					'text'
	    				);
  		}

  		if( $objSEC->is_option_available('migla_paypalbutton') )
  		{
		    $objO->update( 'migla_paypalbutton',
	    					sanitize_text_field($_POST["btnlang"]),
	    					'text'
	    				);
  		}

    	if( $objSEC->is_option_available('migla_paypalcssbtnstyle') )
      	{
    		$objO->update( 'migla_paypalcssbtnstyle',
    	    				sanitize_text_field($_POST["buttonstyle"]),
    	    				'text'
    	    			);
      	}

        if( $objSEC->is_option_available('migla_paypalcssbtnclass') )
      	{
    		$objO->update( 'migla_paypalcssbtnclass',
    	    				sanitize_text_field($_POST["btnclass"]),
    	    				'text'
    	    			);
      	}

        if( $objSEC->is_option_available('migla_paypalbuttonurl') )
      	{
    		$objO->update( 'migla_paypalbuttonurl',
    	    			    sanitize_text_field($_POST["btnurl"]),
    	    				'text'
    	    			);
      	}
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_paypal_tab')){
function TotalDonationsAjax_update_paypal_tab()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {

    	$objL = new MIGLA_LOCAL;
    	$objF = new CLASS_MIGLA_FORM;    	
    	
    	$language = $objL->get_origin();
    	$paypal = $objF->get_paypal_tab();
    	
    	$paypal['tab'] = sanitize_text_field($_POST['tab']);

		$paypal['methodchoice'][0] = '';
		$paypal['methodchoice'][1] = '';

		$paypal['cardholder']['label'] = '';
		$paypal['cardholder']['placeholder'] = '';
		$paypal['cardholder']['last_placeholder'] = '';

		$paypal['cardnumber']['label'] = '';
		$paypal['cardnumber']['placeholder'] = '';
		$paypal['button'] = $_POST['buttontext'];
		$paypal['loading_message'] = sanitize_text_field($_POST['message']);

    	$objF->update_form_meta( 0,
    							 $language,
    							 'paypal_tab_info',
    							 $paypal,
    							 'array'
    							);
    }

	die();
}
}

//Form Options
$ajax_formoption_list = array();

$ajax_report_list = array( "TotalDonationsAjax_bulk_remove_donations",
                            'TotalDonationsAjax_approve_donation',
                            'TotalDonationsAjax_get_detail',
                            'TotalDonationsAjax_update_report',
                            'TotalDonationsAjax_resend_email'
                    );

if (!function_exists('TotalDonationsAjax_bulk_remove_donations')){
function TotalDonationsAjax_bulk_remove_donations()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
       $array = $_POST['remove_list'];

       global $wpdb;

       foreach ($array as $item )
       {
           $item = sanitize_text_field($item);
           
           $sql = "DELETE FROM {$wpdb->prefix}migla_donation WHERE id = %d" ;
           $wpdb->query(  $wpdb->prepare( $sql, $item ) );

           $sql = "DELETE FROM {$wpdb->prefix}migla_donation_meta WHERE donation_id = %d" ;
           $wpdb->query(  $wpdb->prepare( $sql, $item ) );
       }
    }

   die();
}
}

if (!function_exists('TotalDonationsAjax_approve_donation')){
function TotalDonationsAjax_approve_donation()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $data   = (array)$_POST['data_send'];
        $rec_id = sanitize_text_field($_POST['post_id']);

        $objD = new CLASS_MIGLA_DONATION;

        foreach( $data as $array )
        {
            $pair = (array)$array;

            if( isset($pair[0]) && isset($pair[1]) )
            {
                $key = sanitize_text_field($pair[0]);
                $val = sanitize_text_field($pair[1]);
                $objD->update_meta( $rec_id, $key, $val );
            }
        }

        $objD->update_column( array( 'status' => 1 ),
                              array( 'id' => $rec_id ),
                              '%d',
                              '%d'
                          );

        $gateway = $objD->get_column( $rec_id, 'gateway' );

        $gateway = str_replace("Pending-", "", $gateway );

        $objD->update_column( array( 'gateway' => $gateway ),
                              array( 'id' => $rec_id ),
                              '%s',
                              '%d'
                          );
    }

    die();
}
}

if (!function_exists('migla_remove_array_by_key')){
function migla_remove_array_by_key($array, $key_remove)
{
  foreach($array as $key => $value)
  {
    if($key == $key_remove)
        unset($array[$key]);
  }

  return $array;
}
}

if (!function_exists('TotalDonationsAjax_get_detail')){
function TotalDonationsAjax_get_detail()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        global $wpdb;

        $id   = sanitize_text_field($_POST['post_id']);

        $objD =  new CLASS_MIGLA_DONATION;
        $objE = new MIGLA_EMAIL;
        $objF =  new CLASS_MIGLA_FORM;
        $objM = new MIGLA_MONEY;

        $detail = $objD->get_detail( $id, '');

        $map = array( 'miglad_email' => 'email',
                      'miglad_firstname' => 'firstname',
                      'miglad_lastname' => 'lastname',
                      'miglad_country' => 'country',
                      'miglad_amount' => 'amount',
                      'miglad_campaign' => 'campaign'
                    );

        $currency = '';
        if( isset( $detail['miglad_currency'] ) ){
            $currency = $detail['miglad_currency'];
        }

        $status = '1';

        if( isset($detail['status']) ){
            $status = $detail['status'];
        }

        $email = "";
        $hemail = "";
        if(isset($detail['email'])){
            $email = $detail['email'];
        }else if(isset($detail['miglad_email'])){
            $email = $detail['miglad_email'];
        }

        if(isset($detail['miglad_honoreeemail'])){
            $hemail = $detail['miglad_honoreeemail'];
        }

        $structF = array();
        $is_sendmail = "";
        $is_sendpdf = "";
        $is_sendhonorary = "";
        $form_id = 0;

        if( isset( $detail['miglad_form_id'] ) )
        {
        if( $objF->if_exist( $detail['miglad_form_id'] ) )
        {
            $form_id = ['miglad_form_id'];

            $structF = (array)unserialize( $objF->get_column( $form_id, 'structure' ) );
            $is_sendmail = $objE->get_column( $form_id, "is_thankyou_email" );
            $is_sendpdf = $objE->get_column( $form_id, "attachment" );
            $is_sendhonorary = $objE->get_column( $form_id, "is_honoree_email" );
        }else{
            $obj_fields = new CForm_Fields;
            $structF = $obj_fields->form_fields();
        }

        }else{
        $obj_fields = new CForm_Fields;
        $structF = $obj_fields->form_fields();
      }

        $isRepeat = false;
        $str_detail = "";
        $j = 0;
        $show_rec = false;

        if(!empty($structF))
        {
        foreach( $structF as $section )
        {
            $str_group = "";

            if( isset( $section['child'] ) )
            {
                $str_group .= '<tr class="reportGroupHeader">';
                $str_group .= '<td colspan="2">';
                $str_group .= str_replace( "[q]", "'", $section['title']);

                if( !$show_rec ){
                  $str_group .= "<span style='float:right;'>Record-".$id."</span>";
                  $show_rec = true;
                }

                $str_group .= '</td>';
                $str_group .= '</tr>';

              $child = (array)$section['child'];
              $has_child = false;
              $str_list = '';
              
              $not_aval = array( 'miglad_repeating' , 'miglad_mg_add_to_milist' );

              foreach( $child as $line )
              {
                if( isset($line['code']) && isset($line['id']) && isset($line['label']) )
                {

                  if( $line['code'] == 'miglac_' )
                  {
                    $key = $line['uid'];
                  }else{
                    $key = $line['code'].$line['id'];
                  }
                  
                  if( !in_array( $key, $not_aval ) )
                  {

                    if( isset($detail[$key]) )
                    {
                    //meta form table
                    $str_list .= '<tr><td>';
                    $str_list .= str_replace( "[q]", "'", $line['label']);
                    $str_list .= '</td><td>';

                    if($key == 'miglad_repeating')
                    {
                       $info = explode(",", $detail[$key]);
                       if( isset($info[3]) ){
                         $str_list .= $info[3];
                       }
                       $isRepeat = true;

                    }else if($key == 'miglad_campaign')
                    {
                        if( isset($detail['miglad_campaign_name']) )
                        {
                            $str_list .= $detail['miglad_campaign_name'];
                        }else{
                            $cmp   = $detail[($map[$key])];

                            $objC  = new MIGLA_CAMPAIGN;

                            $names = (array)unserialize( $objC->get_column( $cmp, 'name' ) );

                            if( isset($names[get_locale()]) ){
                              $str_list .= $names[get_locale()];
                            }else{
                              $str_list .= "";
                            }
                        }

                    }else{

                        $input = $detail[$key];

                        if( $objSEC->is_serialized($input) )
                        {
                            $_val = (array)unserialize($input) ;
                            $shown_val = '';

                            foreach( $_val as $_value ){
                                if( !empty($shown_val) ){
                                    $shown_val .= ', ';
                                }
                                $shown_val .= $_value;
                            }
                        }else{
                            if( $key == 'miglad_amount' )
                            {
                                $res = $objM->full_format( $input, 2);

                                if( empty($currency) ){
                                    $shown_val = $res[0] .' '. $res[1];
                                }else{
                                    $shown_val = $res[0] .' '. $currency;
                                }
                            }else{
                                $shown_val = $input;
                            }
                        }

                        $str_list .= $shown_val;
                    }

                    $str_list .= '</td>';
                    $str_list .= '</tr>';

                    $has_child = true;

                    $detail = migla_remove_array_by_key($detail, $key);

                  }else if( isset( $detail[($map[$key])] ) )
                    {
                    //form table
                    $str_list .= '<tr>';
                    $str_list .= '<td>';
                    $str_list .= str_replace( "[q]", "'", $line['label']);
                    $str_list .= '</td>';
                    $str_list .= '<td>';

                    if($key == 'miglad_campaign')
                    {
                      if( isset($detail['miglad_campaign_name']) )
                      {
                          $str_list .= $detail['miglad_campaign_name'];
                      }else{
                          $cmp   = $detail[($map[$key])];

                          $objC  = new MIGLA_CAMPAIGN;

                          $names = (array)unserialize( $objC->get_column( $cmp, 'name' ) );

                          if( isset($names[get_locale()]) ){
                              $str_list .= $names[get_locale()];
                          }else{
                              $str_list .= "";

                          }

                      }
                    }else{

                        $input = $detail[($map[$key])];

                        if( $objSEC->is_serialized($input) )
                        {
                            $_val = (array)unserialize($input) ;
                            $shown_val = '';

                            foreach( $_val as $_value ){
                                if( !empty($shown_val) ){
                                    $shown_val .= ', ';
                                }
                                $shown_val .= $_value;
                            }
                        }else{
                            if( $key == 'miglad_amount' ){

                                $res = $objM->full_format( $input, 2);

                                if( empty($currency) ){
                                    $shown_val = $res[0] .' '. $res[1];
                                }else{
                                    $shown_val = $res[0] .' '. $currency;
                                }
                            }else{
                                $shown_val = $input;
                            }
                        }

                        $str_list .= $shown_val;
                    }

                    $str_list .= '</td>';
                    $str_list .= '</tr>';


                    $detail = migla_remove_array_by_key($detail, $map[$key]);

                    $has_child = true;

                    }else{
                        //form table
                        $str_list .= '<tr>';
                        $str_list .= '<td>';
                        $str_list .= str_replace( "[q]", "'", $line['label']);
                        $str_list .= '</td>';
                        $str_list .= '<td>';

                        $str_list .= '</td>';
                        $str_list .= '</tr>';
                    }

                    if($key == 'miglad_country')
                    {
                        if(isset($detail[$key])){
                            $country = $detail[$key];
                        }else{
                            $country = $detail[($map[$key])];
                        }

                        if( strpos($country, "Canada") !== FALSE )
                        {
                            if( isset($detail['miglad_province']) ){
                                //meta form table
                                $str_list .= '<tr><td>';
                                $str_list .= __('Province','migla-donation');
                                $str_list .= '</td><td>';
                                $str_list .= $detail['miglad_province'];
                                $str_list .= '</td>';
                                $str_list .= '</tr>';
                            }

                        }else if( strpos($country, "United States") !== FALSE ){

                            if( isset($detail['miglad_state']) ){

                                $str_list .= '<tr><td>';
                                $str_list .= __('State','migla-donation');
                                $str_list .= '</td><td>';
                                $str_list .= $detail['miglad_state'];
                                $str_list .= '</td>';
                                $str_list .= '</tr>';

                            }
                        }

                    }

                  }

                }//if isset code
              }//foreach child

              if( $has_child ){
                $str_detail .= $str_group . $str_list;
              }else{

              }
            }//if isst

            $j++;
          }//forech
        }//notempty

        $str_extradetail = '';

        $trans_lbl = array(
                  'miglad_transactionId' => 'Transaction ID',
                  'miglad_transactionType' => 'Transaction Type',
                  'miglad_paymentmethod' => 'Method'
                );
    
        $trans_detail = array(
                  'miglad_transactionId',
                  'miglad_transactionType',
                  'miglad_paymentmethod'
                );
    
        if(!empty($detail)){
            foreach($detail as $key => $det)
            {
              if(in_array($key, $trans_detail))
              {
                $str_extradetail .= '<tr>';
                $str_extradetail .= '<td>';
                $str_extradetail .=  $trans_lbl[$key];
                $str_extradetail .= '</td>';
                $str_extradetail .= '<td>';
    
                if( $status != 1 && ($key == 'miglad_transactionId' ) )
                {
                    $str_extradetail .= "<input type='text' class='mg-editable-".$id."' name='".$key."' value='".$det."' />";
                }else{
                    $str_extradetail .= $det;
                }
    
                $str_extradetail .= '</td>';
                $str_extradetail .= '</tr>';
              }
            }
        }

        if( $status == 2 || $status == 3 )
        {
            $gtw = $detail['gateway'];

            if( strtolower($gtw) == "pending-offline" ){
                $btn_class = "mg-approve-off-btn";
            }else{
                $btn_class = "mg-approve-btn";
            }

            $str_extradetail .= '<tr>';
            $str_extradetail .= '<td>';
            $str_extradetail .=  '';
            $str_extradetail .= '</td>';
            $str_extradetail .= '<td>';
            $str_extradetail .= '<button class="' . $btn_class . ' btn btn-success" name="'.$id.'">';
            $str_extradetail .= __("Approve", "migla-donation" ).'</button>';
            $str_extradetail .= '</td>';
            $str_extradetail .= '</tr>';
        }

        if( !empty( $email ) && $is_sendmail == "1" ){
                $str_det2 = "";
                $class2 = "";

                if( $is_sendpdf == "1" ){
                    $str_det2 = __(" with Receipt", "migla-donation" );
                    $class2 = "send-pdf";
                }

                $str_extradetail .= '<br>';
                $str_extradetail .= '<tr>';
                $str_extradetail .= '<td colspan="2">';
                $str_extradetail .= '<button id="mg_btn1_'.$id.'" class="btn-resend btn btn-info '. $class2.'" name="'.$id.'">';
                $str_extradetail .= '<i class="fa fa-fw fa-envelope-o"></i>';
                $str_extradetail .= __(" Resend email", "migla-donation" );
                $str_extradetail .= $str_det2;
                $str_extradetail .= '</button>';
                $str_extradetail .= '<span id="mg_mgs1_'.$id.'"></span>';
                $str_extradetail .= '<img id="mg_loading1_'.$id.'" style="display:none;" src="'.Totaldonations_DIR_URL."assets/images/gif/loading-boots.gif".'">';
                $str_extradetail .= '</td>';
                $str_extradetail .= '</tr>';
        }

        if( $is_sendhonorary == "1" && !empty( $hemail ) )
        {
                $str_extradetail .= '<br>';
                $str_extradetail .= '<tr>';
                $str_extradetail .= '<td colspan="2">';
                $str_extradetail .= '<button id="mg_btn2_'.$id.'" class="btn-resend-hmail btn btn-warning" name="'.$id.'">';
                $str_extradetail .= '<i class="fa fa-fw fa-envelope-o"></i>';
                $str_extradetail .= __(" Resend Honoree an email", "migla-donation" );
                $str_extradetail .= '</button>';
                $str_extradetail .= '<span id="mg_mgs2_'.$id.'"></span>';
                $str_extradetail .= '<img id="mg_loading2_'.$id.'" style="display:none;" src="'.Totaldonations_DIR_URL."assets/images/loading-boots.gif".'">';
                $str_extradetail .= '</td>';
                $str_extradetail .= '</tr>';
        }

        $result = array();
        $result[0] = $str_detail;
        $result[1] = $str_extradetail;
        $result[2] = $detail['miglad_form_id'];
        $result[3] =  $objF->if_exist( $detail['miglad_form_id'] );

        echo json_encode($result);
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_update_report')){
function TotalDonationsAjax_update_report()
{
    $objSEC = new MIGLA_SEC;

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $data   = (array)$_POST['data_send'];
        $rec_id = sanitize_text_field($_POST['record_id']);

        $columnUpdates  = array();
        $keyValues      = array( 'id' => $rec_id );
        $columnTypes    = array();
        $keyTypes       = array( '%d' );

      $resp = '';

      $i = 0;

      $columnUpdates = array( 'email' => '',
                    'firstname' => '',
                    'lastname' => '',
                    'country' => '' );

      $default = array( 'email',
                    'firstname',
                    'lastname',
                    'country'
                    /*
                    'amount',
                    'campaign',
                    'anonymous',
                    'repeating',
                    'mailist',
                    'gateway',
                    'session_id',
                    'date_created' */
                );

      $defaultinfo = array( 'email' => array("%s", "text"),
                    'firstname' => array("%s", "text"),
                    'lastname' => array("%s", "text"),
                    'country' => array("%s", "text")
                    /*
                    'amount',
                    'campaign',
                    'anonymous',
                    'repeating',
                    'mailist',
                    'gateway',
                    'session_id',
                    'date_created' */
                );

      $objD = new CLASS_MIGLA_DONATION;

      foreach( $data as $array )
      {
          $pair = (array)$array;

          if( isset($pair[0]) && isset($pair[1]) )
          {
              $key =  sanitize_text_field($pair[0]);

              $is_array = false;

              if(isset($pair[2]) && $pair[2] == "array"){
                $is_array = true;
                $val =  $pair[1];
              }else{
                $val =  sanitize_text_field($pair[1]);                
              }

              if( $key == 'email' ||
                $key == 'firstname' ||
                $key == 'lastname' ||
                $key == 'country'
                )
                {
                    $columnUpdates[$key] = $val;
                    $columnTypes[$i]     = $defaultinfo[$key][0];
                    $i++;

                    $objD->update_meta( $rec_id, 'miglad_'. $key, $val );

                }else{

                    if( $is_array )
                    {
                      $objD->update_meta( $rec_id, $key, serialize($val) );
                    }else{
                      $objD->update_meta( $rec_id, $key, $val );
                    }
                }//is def

          }//isset a pair

      }//foreach

      if( !empty($columnUpdates) )
      {
          $objD->update_column( $columnUpdates,
                                $keyValues,
                                $columnTypes,
                                $keyTypes
                            );
      }

      echo $resp;
    }

    die();
}
}

if (!function_exists('TotalDonationsAjax_resend_email')){
function TotalDonationsAjax_resend_email()
{
    $objSEC = new MIGLA_SEC;
    $msg = "";

    if( $objSEC->is_this_the_owner( sanitize_text_field($_POST['auth_owner']), sanitize_text_field($_POST['auth_token']), sanitize_text_field($_POST['auth_session']) ) )
    {
        $record_id =  sanitize_text_field($_POST['record_id']);

        $objD = new CLASS_MIGLA_DONATION;
        $data = $objD->get_detail( $record_id, '' );

        $email = '';
        $msg = "";
        $date_created = "";
        $timestamp = "";

        if(isset($data['email'])){
            $data['miglad_email'] = $data['email'];
            $email = $data['email'];
        }

        if( !empty($email) )
        {

            if(isset($data['firstname'])){
                $data['miglad_firstname'] = $data['firstname'];
            }
            if(isset($data['lastname'])){
                $data['miglad_lastname'] = $data['lastname'];
            }
            if(isset($data['amount'])){
                $data['miglad_amount'] = $data['amount'];
            }

            if(isset($data['campaign'])){
                $data['miglad_campaign'] = $data['campaign'];
            }
            if(isset($data['country'])){
                $data['miglad_country'] = $data['country'];
            }
            if(isset($data['anonymous'])){
                $data['miglad_anonymous'] = $data['anonymous'];
            }
            if(isset($data['date_created'])){
                $date_created = $data['date_created'];
            }
            if(isset($data['timestamp'])){
                $timestamp = $data['timestamp'];
            }

            $form_id = 0;

            if( isset($data['miglad_form_id']) )
            {
                if( !empty($data['miglad_form_id']) )
                {
                    $form_id = $data['miglad_form_id'];
                }else{
                }
            }else{
                $data['miglad_form_id'] = 0;
            }

            $objL = new MIGLA_LOCAL;
            $language = $objL->get_origin_language();

            if( isset($data['miglad_language']) )
            {
                if( !empty($data['miglad_language']) )
                {
                    $language = $data['miglad_language'];
                }else{
                    $data['miglad_language'] = $objL->get_origin_language();
                }
            }else{
                $data['miglad_language'] = $objL->get_origin_language();
            }

            $objE = new MIGLA_EMAIL;
            $status = $objE->email_resend_procedure( $form_id,
                                                $record_id,
                                                sanitize_text_field($_POST['is_send_pdf']),
                                                $data,
                                                $language,
                                                $timestamp,
                                                $date_created );

            if( $status ){
                $msg = "Emails sent to " . $email;
            }else{
                $msg = "Emails NOT sent to " . $email;
            }

        }else{
            $msg = "Donor email seems empty.";
        }
    }

    echo $msg;

    die();
}
}

$ajax_custom_script = array("TotalDonationsAjax_dismiss_notice");

if (!function_exists('TotalDonationsAjax_dismiss_notice')){
function TotalDonationsAjax_dismiss_notice()
{
    $msg = "";

    if( !wp_verify_nonce( sanitize_text_field($_POST['nonce']), 'migla-donate-nonce' ) ){
        $msg = "unverified nonce";    
    }else{
        $objO = new MIGLA_OPTION;
        $caller = sanitize_text_field($_POST['whois_dismiss']);

        if( $caller == "paypal" ){
            $objO->update( 'migla_paypal_isdismiss', 'yes', 'text' );
             $msg = "update paypal notice";
        }else if( $caller == "stripe" ){
            $objO->update( 'migla_stripe_isdismiss', 'yes', 'text' );
             $msg = "update stripe notice";
        }        
    }

    echo $msg;
    die();
}
}


$ajax_list = array_merge( $ajax_campaign_list,
                          $ajax_frontend_list,
                          $ajax_theme_list,
                          $ajax_dashboard_list,
                          $ajax_email_list,
                          $ajax_gtw_list ,
                          $ajax_formoption_list,
                          $ajax_report_list,
                          $ajax_custom_script
                         );

if (  is_user_logged_in() )
{
    foreach( $ajax_list as $migla_ajax_call )
    {
       add_action("wp_ajax_" . $migla_ajax_call, $migla_ajax_call );
    }
}

?>
