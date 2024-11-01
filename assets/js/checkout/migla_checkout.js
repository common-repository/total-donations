var mdata = [];
var migla_postdata = {};
var state_code = {} ;
var province_code = {} ;
var warning = ["", "", "", "", ""];
var plan_info = ['No', 0, 'no', 'no'];

var migla_sessionid;
var amount;
var cleanAmount;
var token_;

var stripe_response;
var migla_message;
var migla_language;
var migla_form_id;

var tab_bgcolor;
var tab_border;
var tab_color_active;
var tab_color_notactive ;

var stripeCard;
var stripe;

var theExecuter = "";
var isRepeat;

function mg_init()
{
    if( jQuery('#miglaCustomAmount').length > 0 )
    {
      jQuery('#miglaCustomAmount').val('0');
      
      mg_custom_amount_handler();
      mg_custom_amount_label();
    }

    mg_toogled();
    mg_tabs();

    mg_amountlabel_clicked();

    mg_positivenum_keyup();

    mg_select2_init();
    
    
}

function mg_init_global_data()
{
    isRepeat = 'no';
    theExecuter = "";
    
    plan_info = ['No', 0, 'no', 'no'];
    mdata = [];
    
    migla_postdata['amount'] ='';
    migla_postdata['miglad_firstname'] ='';
    migla_postdata['miglad_lastname'] ='';
    migla_postdata['miglad_email'] ='';
    migla_postdata['miglad_address'] ='';
    migla_postdata['miglad_city'] ='';
    migla_postdata['miglad_postalcode'] ='';
    migla_postdata['miglad_country'] ='';
    migla_postdata['miglad_province'] ='';    
    migla_postdata['miglad_state'] ='';
    migla_postdata['miglad_campaign'] ='';
    migla_postdata['miglad_campaign_name'] ='';
    migla_postdata['miglad_session_id'] ='';      
    migla_postdata['miglad_form_id'] ='';      
}

function mg_tabs()
{
  tab_bgcolor = jQuery('.mg_tab-content').find('.mg_active').css('background-color');
  tab_border  = jQuery('.mg_tab-content').find('.mg_active').css('border');

  jQuery('.mg_nav-tabs').find('li').each(function(){
     if( jQuery(this).hasClass('mg_active') ){
         tab_color_active = jQuery(this).find('a').css('background-color');
     }else{
         tab_color_notactive = jQuery(this).find('a').css('background-color');
     }
  });

  jQuery('.mg_nav li').click(function(){

     var id = jQuery(this).find('a').attr('id');
     var $this = jQuery(this);

         jQuery('.mg_nav li').each(function(){
              jQuery(this).removeClass('mg_active');
         });

     jQuery('#'+id).closest('li').addClass('mg_active') ;

     if( id == '_sectionstripe' ){
         jQuery('.mg_tab-content').find('#sectionstripe').addClass('mg_active');
         jQuery('.mg_tab-content').find('#sectionstripe').css('background-color', tab_bgcolor);
         jQuery('.mg_tab-content').find('#sectionstripe').css('border', tab_border);

         jQuery('#_sectionstripe').css('background-color', tab_color_active );
         jQuery('#_sectionpaypal').css('background-color', tab_color_notactive );

         jQuery('.mg_tab-content').find('#sectionpaypal').removeClass('mg_active');

     }else if( id == '_sectionpaypal' ){

         jQuery('.mg_tab-content').find('#sectionpaypal').addClass('mg_active');
         jQuery('.mg_tab-content').find('#sectionpaypal').css('background-color', tab_bgcolor);
         jQuery('.mg_tab-content').find('#sectionpaypal').css('border', tab_border);

         jQuery('.mg_tab-content').find('#sectionstripe').removeClass('mg_active');

         jQuery('#_sectionpaypal').css('background-color', tab_color_active );
         jQuery('#_sectionstripe').css('background-color', tab_color_notactive );

     }
  });
}

function mg_custom_amount_handler()
{
  jQuery('.migla_amount_choice').click(function(){
    if( jQuery(this).val() == 'custom' ){
      jQuery('#miglaCustomAmount').focus();
      jQuery('#miglaCustomAmount').val('');
    }
  });

  jQuery('#miglaCustomAmount').focus(function(){
    jQuery('.migla_custom_amount').attr("checked", "checked");
    
    if( jQuery('#miglaCustomAmount').val() <= 0 )
    {
      jQuery('#miglaCustomAmount').val('');    
    }

    if( jQuery('.migla_amount_lbl').length > 0 )
    {
      var thecustom= jQuery('.miglaCustomAmount').find('.migla_amount_lbl');
      thecustom.trigger('click');
    }
  });

  jQuery('#miglaCustomAmount').click(function(){
    jQuery('.migla_custom_amount').attr("checked", "checked");
    
    if( jQuery('#miglaCustomAmount').val() <= 0 )
    {
      jQuery('#miglaCustomAmount').val('');    
    }
  });
}

function mg_custom_amount_label()
{
  jQuery('.migla_amount_lbl').click(function(){
    jQuery('.migla_amount_lbl').each(function(){
      jQuery(this).css('background-color', jQuery('#mg_level_color').val());
    });

    jQuery(this).css('background-color', jQuery('#mg_level_active_color').val());
  });
}

function mg_positivenum_keyup()
{
  jQuery('.migla_positive_number_only').on('keyup', function (e){
     var mg_current_value     =  jQuery(this).val();
     var mg_current_array_value   = mg_current_value.split('');
     var mg_new_val       = '';
     var mg_decimal       = jQuery('#miglaDecimalSep').val();
     var mg_thousand        = jQuery('#miglaThousandSep').val();
     var mg_count_decimal = 0 ; var mg_count_thousand = 0 ;

     for( var i = 0; i < mg_current_array_value.length ; i = i + 1 )
     {
       if( mg_current_array_value[i] == '0' || mg_current_array_value[i] == '1' || mg_current_array_value[i] == '2' ||
         mg_current_array_value[i] == '3' || mg_current_array_value[i] == '4' || mg_current_array_value[i] == '5' ||
         mg_current_array_value[i] == '6' || mg_current_array_value[i] == '7' || mg_current_array_value[i] == '8' ||
         mg_current_array_value[i] == '9'
       )
       {
         mg_new_val = mg_new_val + mg_current_array_value[i];

       }else if( mg_current_array_value[i] == mg_decimal && jQuery('#miglaShowDecimal').val() == 'yes' )
       {
           mg_count_decimal = mg_count_decimal + 1;
         if( mg_count_decimal == 1 )
         {
           mg_new_val = mg_new_val + mg_current_array_value[i];
         }
       }
     }

      jQuery(this).val( mg_new_val );
     });

    jQuery('.miglaNAD2').on('keypress', function (e){
      var str = jQuery(this).val();
      var separator = jQuery('#miglaDecimalSep').val();
      var key = String.fromCharCode(e.which);

      // Allow: backspace, delete, escape, enter
      if (jQuery.inArray( e.which, [ 8, 0, 27, 13]) !== -1 ||
         jQuery.inArray( key, [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ]) !== -1 ||
         ( key == separator )
      )
      {
         if( key == separator  ){

            if(jQuery('#miglaShowDecimal').val()=='yes'){
              if( ( str.indexOf(separator) >= 0 ) ){
                e.preventDefault();
              }else{
                return;
              }
            }else{
               e.preventDefault();
            }
         }

      }else{
         e.preventDefault();
      }
   });  
}

function mg_select2_init()
{
    jQuery("#miglad_country").on("change",function(){
        var mg_country = jQuery('#miglad_country').val();
      
          if( mg_country == 'Canada')
          {
            jQuery('#miglad_state-div').hide();
            jQuery('#miglad_province-div').show();
          }else if( mg_country == 'United States')
          {
            jQuery('#miglad_state-div').show();
            jQuery('#miglad_province-div').hide();
          }else{
            jQuery('#miglad_state-div').hide();
            jQuery('#miglad_province-div').hide();      
          }        
    });

    jQuery("#miglad_honoreecountry").on("change",function(){
      var mg_country = jQuery("#miglad_honoreecountry").val();
      
      if( mg_country == 'Canada')
      {
        jQuery('#miglad_honoreestate-div').hide();
        jQuery('#miglad_honoreeprovince-div').show();
      }else if( mg_country == 'United States')
      {
        jQuery('#miglad_honoreestate-div').show();
        jQuery('#miglad_honoreeprovince-div').hide();
      }else{
        jQuery('#miglad_honoreestate-div').hide();
        jQuery('#miglad_honoreeprovince-div').hide();     
      }
      
    });
    
    console.log('hola');
}

function mg_toogled()
{
  //Toggle
  jQuery('.mtoggle').each(function(){
    jQuery(this).prop("checked", false);
  });

  jQuery('.mtoggle').click(function(){
    var p = jQuery(this).closest('.migla-panel');
    p.find('.migla-panel-body').toggle();
  });  
}

function mg_amountlabel_clicked()
{
  jQuery('.migla_amount_lbl').click(function(){

    jQuery('.amt-btn').each(function(){
       jQuery(this).removeClass('selected');
    });
    
    jQuery('.migla_amount_lbl').each(function(){
        jQuery(this).css('background-color', jQuery('#mg_level_color').val() );
    });
    
    jQuery('.migla_amount_choice').each(function(){
       jQuery(this).removeClass('mg_amount_checked');
    });

    var parent = jQuery(this).closest('.amt-btn');
    parent.addClass('selected');

    //mg_level_active_color
    jQuery(this).css('background-color', jQuery('#mg_level_active_color').val() );

    jQuery(this).find('.migla_amount_choice').addClass('mg_amount_checked');

  });//label
}

function mg_clean_text( dirty )
{
    var _dirty = new String(dirty);
    var clean ;

    clean = _dirty.replace(/</gi,"");
    clean = clean.replace(/>/gi,"");
    clean = clean.replace(/!/gi,"");
    clean = clean.replace(/&amp/gi,"");
    clean = clean.replace(/&/gi,"");
    clean = clean.replace(/#/gi,"");
    clean = clean.replace(/"/gi,"");
    clean = clean.replace(/'/gi,"");
    return clean;
}

function mg_is_email_valid(str)
{
     var pattern =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/;
     return pattern.test(str);  // returns a boolean
}

function get_data_on_form(migla_form_id, migla_sessionid, migla_language)
{
    var isVal = true;
    var isAllFieldsFilled = true;
    warning = ["", "", "", "", ""];
    isCardHolderNameEmpty = false;
    
    //Check Amount
    cleanAmount = mg_get_amount();
        
    item = [ 'miglad_amount', cleanAmount ]; 
    mdata.push( item );
    migla_postdata['amount'] = cleanAmount;
    
    if( !isNaN(Number(cleanAmount)) ){
        if(Number(cleanAmount) > 0){
        }else{
             warning[2] = jQuery('#mg_warning3').text();
             isVal = false;
        }
    }
    
    var minAmount = jQuery('#miglaMinAmount').val();
    
    if( Number(cleanAmount) < minAmount ){
        warning[2] = jQuery('#mg_warning3').text();
        isVal = false;
    } 
    
    //SESSION ID
    item = [ 'miglad_session_id_', migla_sessionid ]; 
    mdata.push( item );
    migla_postdata['miglad_session_id'] = migla_sessionid;
    
    item = [ 'miglad_session_id', migla_sessionid ]; 
    mdata.push( item );
        
    //CAMPAIGN
    var campaign = jQuery('#miglad_campaign').val();
    item = [ 'miglad_campaign', campaign ]; 
    mdata.push( item );
    migla_postdata['miglad_campaign'] = campaign;
        
    var campaignName = jQuery('#migla_donation_form-'+migla_form_id).find('select[name=campaign] option:selected').text();
    item = [ 'miglad_campaign_name', campaignName ]; 
    mdata.push( item );
    migla_postdata['miglad_campaign_name'] = campaignName;
    
    //LANGUAGE
    console.log('language '+ migla_language);
    item = [ 'miglad_language', migla_language]; 
    mdata.push( item );

    //FORMID
    item = [ 'miglad_form_id', migla_form_id ]; 
    mdata.push( item );
    migla_postdata['miglad_form_id'] = migla_form_id;
    
    var country = '';
    
    //saves on migla_postdata
    jQuery('#migla_donation_form-' + migla_form_id).find('.migla-panel').each(function(){
        var toggle = jQuery(this).find('.mtoggle');
      
        if( (toggle.length < 1) || ( (toggle.length > 0) && toggle.is(':checked') ) )
        {
            jQuery(this).find('.migla_rdiv_field').each(function(){
                
                var isMandatory = false;
                
                if( jQuery(this).hasClass('migla_rdiv_field_mandatory') ){
                    isMandatory = true;
                }
                
                var whoami = jQuery(this).find('.idfield_key').val();  
                var val_id = jQuery(this).find('.idfield').val();
                var val = "";
             
                if( whoami == 'miglad_amount' || 
                    whoami == 'miglad_camount' || 
                    whoami == 'miglad_campaign' || 
                    typeof whoami === 'undefined'
                )
                {
                    
                }else if( whoami == 'miglad_repeating' )
                {
                    var val = '';
                
                    if(  jQuery(this).find('.idfield').hasClass('idfield_radio') )
                    {
                        var checked_plan = jQuery('input[name="miglad_repeating"]:checked').attr('id');
                        
                        plan_info = jQuery('#info'+checked_plan).val()
                        plan_info = plan_info.split(";");
                        
                        isRepeat = plan_info[0];
                        
                        val = plan_info;
                        
                    }else{
                        
                        if( jQuery('#miglad_repeating').length > 0 
                            && jQuery('#miglad_repeating').is(":checked") )
                        {
                            plan_info = jQuery(this).find('#infomiglad_repeating').val();
                            plan_info = plan_info.split(";");
    
                            isRepeat = plan_info[0];
                        
                            val = plan_info; 
                            console.log(plan_info);
                        }
                      
                    } //Wich type is repeating
                  
                    val    = mg_clean_text( val ) ;
                    whoami = mg_clean_text( whoami) ;
                    
                    var temp = [ whoami , val ];
                    
                    mdata.push(temp);   
                    
                }else{
                    
                   if( jQuery(this).find('.idfield').hasClass('idfield_radio') )
                    {
                        if( jQuery("input[name='"+val_id+"']").length > 0 ){
                            val = jQuery("input[name='"+val_id+"']:checked").val() ;
                        }else{
                            val = '';
                        }
                    }else if( jQuery(this).find('.idfield').hasClass('idfield_multicheckbox') )
                    {
                        val = '';
                        var temp = [];
                        
                        if( jQuery("input[name='"+val_id+"']").length > 0 )
                        {
                            jQuery("input[name='"+val_id+"']:checked").each(function(){
                                temp.push( jQuery(this).val());
                            });
                      
                            val = temp;
                            
                            if (Array.isArray(temp) && temp.length && isMandatory)
                            {
                                warning[0] = jQuery('#mg_warning1').text();
                                isVal = false;
                                jQuery('#'+whoami).addClass('pink-highlight');
                            }
                        }
                      
                    }else if( jQuery(this).find('.idfield').hasClass('idfield_checkbox') )
                    {
                        val = '';
                        
                        if( jQuery('#'+val_id).length > 0 )
                        {                
                            if( jQuery('#'+val_id).is(":checked") )
                            {
                                val = 'yes';//jQuery('#'+val_id).val();
                            }else{
                                val = 'no';
                                
                                if(isMandatory)
                                {
                                    warning[0] = jQuery('#mg_warning1').text();
                                    isVal = false;
                                    jQuery('#'+whoami).addClass('pink-highlight');                                   
                                }
                            }
                        }   
                      
                    }else{
                      
                        val = '';
                      
                        if( jQuery('#'+val_id).length > 0 ){ 
                            val = jQuery('#'+val_id).val();
                        }
                        
                        if( val == '' && isMandatory){
                            warning[0] = jQuery('#mg_warning1').text();
                            isVal = false;
                            jQuery('#'+whoami).addClass('pink-highlight');   
                        }
                    }
    
                    val    = mg_clean_text( val ) ;
                    whoami = mg_clean_text( whoami) ;
                    
                    var temp = [ whoami , val ];
                    
                    mdata.push(temp);          
                }    
                
                if( whoami == 'miglad_email' ){
                    if( !mg_is_email_valid( val ) ){
                        isVal = false;
                        warning[1] = jQuery('#mg_warning2').text();
                        jQuery('#'+whoami).addClass('pink-highlight');  
                    }                    
                }

                //Special
                if( whoami == 'miglad_state' || whoami == 'miglad_province' || whoami == 'miglad_country' 
                    || whoami == 'miglad_firstname' || whoami == 'miglad_lastname' 
                    || whoami == 'miglad_email' || whoami == 'miglad_address' || whoami == 'miglad_postalcode'
                    || whoami == 'miglad_city' || whoami == 'miglad_campaign'
                    || whoami == 'miglad_employer' || whoami == 'miglad_occupation'
                    || whoami == 'miglad_memorialgift' 
                    )
                {
                    migla_postdata[whoami] = val;
                }    
    
            }); //migla_rdiv_field
        }
    });

    if( migla_postdata['miglad_country'] == 'Canada' ){
        migla_postdata['miglad_state'] == '';
    }else
    if( migla_postdata['miglad_country'] == 'United States' ){
        migla_postdata['miglad_province'] == '';
    }else{
        migla_postdata['miglad_state'] == '';
        migla_postdata['miglad_province'] == '';
    }

    if( theExecuter == "stripe" && jQuery('#card_name').val() == "" ){
        warning[3] = "Please fill Card Holder Name";
        isVal = false;
        jQuery('#card-errors').html( "Please fill Card Holder Name" );
    }

    return isVal;
}

function mg_succes_url( session_id, record_id, gateway )
{
    var migla_thank_you_page = jQuery('#migla_thankyou_url').val();
    
    if ( migla_thank_you_page.search( "\\?" ) < 0 )
    {
      migla_thank_you_page = migla_thank_you_page + "?";
    }else{
      migla_thank_you_page = migla_thank_you_page + "&";
    }
    
    migla_thank_you_page = migla_thank_you_page + 'pid=' + record_id;
    migla_thank_you_page = migla_thank_you_page + '&gtw=' + gateway;
    migla_thank_you_page = migla_thank_you_page + '&sid=' + session_id;
     
    return migla_thank_you_page;
}

function migla_after_effect_click( thisID, formID )
{
    jQuery('#'+thisID).hide();
    
    if( jQuery('#'+thisID).hasClass('stripecheckoutbtn') )
    {
        jQuery('#mg_wait_stripe_'+formID).show();
        
    }else if( jQuery('#'+thisID).hasClass('paypalstdcheckoutbtn') )
    {
        jQuery('#mg_wait_paypal_'+formID).show();
    }
}

function migla_after_cancel_click( thisID, formID )
{
    jQuery('#'+thisID).show();

    if( jQuery('#'+thisID).hasClass('stripecheckoutbtn') ){
           jQuery('#mg_wait_stripe_'+formID).hide();
    }
}

function mg_sendtoPaypal()
{
    setTimeout(function(){ 
        jQuery( '#migla-hidden-form' ).submit();
    }, 1000);    
}

function mg_change_paypal_item_id( hiddenForm, post_id )
{
    if( post_id >= 0 )
    {
        hiddenForm.find( 'input[name="custom"]' ).val( 'post_' + post_id );
      
        $url = hiddenForm.find( 'input[name="return"]' ).val();
        $url = $url + '&pid=' + post_id + '&sid=' + miglaAdminAjax.sid;
      
        hiddenForm.find( 'input[name="return"]' ).val($url);
    }
    
    mg_sendtoPaypal();
}

function migla_execute_paypal( migla_postdata, 
                               mdata, 
                               cleanAmount, 
                               migla_sessionid, 
                               isRepeat
                            )
{
    var hiddenForm = jQuery('#migla-hidden-form');
              
    hiddenForm.find('input[name="email"]').val( migla_postdata['miglad_email'] );
    hiddenForm.find('input[name="custom"]').val( migla_sessionid );
    hiddenForm.find('input[name="amount"]').val( cleanAmount );
       
    var mg_country_code = mg_get_country_code( migla_postdata['miglad_country'] );
    var lang_code = mg_get_language(mg_country_code);
    var mg_state_code = '';
    var mg_province_code = '';

    hiddenForm.find('input[name="first_name"]').val( migla_postdata['miglad_firstname'] );
    hiddenForm.find('input[name="last_name"]').val( migla_postdata['miglad_lastname'] );
    hiddenForm.find('input[name="address1"]').val( migla_postdata['miglad_address'] );
    hiddenForm.find('input[name="city"]').val( migla_postdata['miglad_city'] );
    hiddenForm.find('input[name="zip"]').val( migla_postdata['miglad_postalcode'] );
    hiddenForm.find('input[name="country"]').val( mg_country_code );
    //hiddenForm.find('input[name="lc"]').val( lang_code );

    if( migla_postdata['miglad_country'] == 'Canada' )
    {
       hiddenForm.find('input[name="state"]').val( mg_province_code );
       
    }else if( migla_postdata['miglad_country'] == 'United States' )
    {
       hiddenForm.find('input[name="state"]').val(  mg_state_code  );
    }
    
    hiddenForm.find('input[name="os2"]').val( migla_postdata['miglad_campaign_name'] );

    hiddenForm.find( 'input[name="src"]' ).val('0');
    hiddenForm.find( 'input[name="p3"]' ).remove();
    hiddenForm.find( 'input[name="t3"]' ).remove();
    hiddenForm.find( 'input[name="a3"]' ).remove();

    jQuery.ajax({
        type : "post",
        url  :  miglaAdminAjax.ajaxurl,
        data :  {   action    : 'miglaA_checkout',
                    donorinfo : mdata,
                    session   : miglaAdminAjax.sid,
                    nonce     : miglaAdminAjax.nonce,
                    form_id   : jQuery('#migla_form_id').val()
                },
        success : function( post_id ) 
                {
                    console.log("PostID:"+post_id);
                    
                    setTimeout(function(){ 
                        mg_change_paypal_item_id( hiddenForm, post_id );
                    }, 2000);
                },
        error: function(xhr, status, error)
                {
                    console.log( error );
                },
        complete : function(xhr, status, error)
                    {
                    }                 
    });
}

function mg_donation_process()
{
    console.log("3rd call");
   
    //checkout
    jQuery('.miglacheckout').click(function(){
        
        mg_init_global_data();
      
        jQuery(this).addClass('hideme');
      
        var item      = [];
        var formID    = migla_form_id;
    
        migla_form_id = jQuery(this).attr('name');
    
        mdata.length    = 0;
        migla_language  = jQuery('#migla_language').val();
        migla_sessionid = miglaAdminAjax.sid;
        repeating       = 'no';
        anonymous       = 'no';
        theExecuter     = "";
        
        if( jQuery(this).hasClass('stripecheckoutbtn') )
        {
            theExecuter = "stripe";
        }else if( jQuery(this).hasClass('paypalstdcheckoutbtn') )
        {
            theExecuter = "paypal"; 
        }
        
        var count_error = 0;

        jQuery.ajax({
            type : "post",
            url  :  miglaAdminAjax.ajaxurl,
            data :  {   action    : 'miglaA_count_error_logged'
                    },
            success : function( count_client_error ) 
                    {
                        console.log("How many error " + count_client_error);
                        count_error = count_client_error;
                        jQuery("#miglaErrorCount").val(count_client_error);  
                    },
            error: function(xhr, status, error)
                    {
                        console.log( error );
                    },
            complete : function(xhr, status, error)
                    {
                    }   
        });
        
    if( Number( jQuery("#miglaErrorCount").val() ) >= 10 ){
        jQuery(".paypalstdcheckoutbtn").remove();
        jQuery(".stripecheckoutbtn").remove();

        jQuery('#card-errors').html( "Hello. It seems that you have been trying to donate multiple times and have not succeeded. Please contact us for help with this issue. We will contact you soon. Cheers." );
    }else{        
        
        if( get_data_on_form(migla_form_id, migla_sessionid, migla_language) )
        {
            migla_after_effect_click( jQuery(this).attr('id'), migla_form_id);

            var thisID = jQuery(this).attr('id');
            
            if( jQuery(this).hasClass('stripecheckoutbtn') )
            {
                jQuery.ajax({
                        type : "post",
                        url  :  miglaAdminAjax.ajaxurl,
                        data :  {   action    : 'miglaA_stripe_create_payment_intent',
                                    amount    : migla_postdata['amount'],
                                    session   : miglaAdminAjax.sid,
                                    nonce     : miglaAdminAjax.nonce,
                                    form_id   : jQuery('#migla_form_id').val()
                              },
                        success : function( client_secret ) 
                                {
                                    jQuery('.client-secret').val( client_secret );
                                    
                                    jQuery('#migla_stripecheckout_' + migla_form_id).removeClass('.miglacheckout');
                                },
                        error: function(xhr, status, error)
                                    {
                                        console.log( error );
                                    },
                        complete : function(xhr, status, error)
                                    {
                                    }                 
                    }).then(function(){
                        console.log('Then');
                        
                        var cardHolder = jQuery('#card_name').val();
                        
                        var donorEmail = '';
                        var donorAddress = ''; 
                        var donorCountry = ''; 
                        var donorCity = ''; 
                        var donorPostalCode = ''; 
                        
                        var myAddressObj = {
                            "country" : "",
                            "city"    : "",
                            "line1"   : "",
                            "postal_code" : ""
                        };
                        
                        var ifAddressExist = false;
                        
                        if( jQuery('#miglad_address').length > 0 ){
                            myAddressObj.line1 = migla_postdata['miglad_address'];
                            ifAddressExist = true;
                        }else{
                            delete myAddressObj.line1;
                        }
                        
                        if( jQuery('#miglad_country').length > 0 ){
                            myAddressObj.country = mg_get_country_code( migla_postdata['miglad_country'] );
                        }else{
                            delete myAddressObj.country;
                        }
                        
                        if( jQuery('#miglad_city').length > 0 ){
                            myAddressObj.city = migla_postdata['miglad_city'];
                        }else{
                            delete myAddressObj.city;
                        }
                        
                        if( jQuery('#miglad_postalcode').length > 0 ){
                            myAddressObj.postal_code = migla_postdata['miglad_postalcode'];
                        }else{
                            delete myAddressObj.postal_code;
                        }
                        
                        var myBillingObj = {
                            "name"  : cardHolder,
                            "address" : "",
                            "email" : ""
                        };
                        
                        if(ifAddressExist){
                            myBillingObj.address = myAddressObj;
                        }else{
                            delete myBillingObj.address;
                        }
                        
                        if( jQuery('#miglad_email').length > 0 ){
                            myBillingObj.email = migla_postdata['miglad_email'];
                        }else{
                            delete myBillingObj.email;
                        }                   
                        
                        stripe.handleCardPayment(
                            jQuery('.client-secret').val(), 
                            stripeCard, {
                              payment_method_data: {
                                billing_details: myBillingObj
                              }
                            }
                          ).then(function(response) {
    
                            if (response.error) 
                            {
                                jQuery('#card-errors').html( response.error.message );
                                migla_after_cancel_click( thisID, migla_form_id );
                              
                                jQuery.ajax({
                                    type : "post",
                                    url :  miglaAdminAjax.ajaxurl,
                                    data :  {   action  : "miglaA_client_logged" ,
                                                          client_status     : "error",
                                                          client_message    : response.error.message
                                                        },
                                            success: function( stripemsg ) 
                                                        {       
                                                        },
                                            complete: function(xhr, error, status){
                                                        }
                                });                                 
                              
                            }else if (response.paymentIntent && response.paymentIntent.status === 'succeeded')
                            {
                                mg_stripeTokenHandler(response)
                            }
                        });//then
                        
                        
                });//Ajax 
                    
            }else if( jQuery(this).hasClass('paypalstdcheckoutbtn') )
            {
                migla_execute_paypal( migla_postdata, mdata, migla_postdata['amount'], migla_sessionid, isRepeat );
            }
            
        }else{
                
           var warn = warning[0];
    
           if( warning[1] != "" && warn != ""){
             warn = warn + "\n" + warning[1];
           }
    
           if( warning[1] != "" && warn == ""){
             warn = warn +  warning[1];
           }
    
           if( warning[2] != "" && warn != ""){
             warn = warn + "\n" + warning[2];
           }
    
           if( warning[2] != "" && warn == ""){
             warn = warn +  warning[2];
           }
           
           if(isCardHolderNameEmpty){
               warn = warn + "\n" + warning[3];
           }
    
           alert(warn);
           
           migla_after_cancel_click( jQuery(this).attr('id'), migla_form_id );  
        }
        
    }
        
    jQuery(this).removeClass('hideme');
  
  }); //Donate Button Clicked
  
}

function mg_get_amount()
{
    var clean_amount = 0;
    
    if( jQuery('#migla_donation_form-'+migla_form_id).find('.idfield_amount').hasClass('idfield_amount_button') )
    {
          
        jQuery('#migla_donation_form-'+migla_form_id).find('.amt-btn').each(function(){
                if( jQuery(this).hasClass('selected') ){
                    clean_amount =jQuery(this).find(".RadioInlineAmount").val();
                }
        });
          
    }else{
        clean_amount = jQuery('#migla_donation_form-'+migla_form_id).find("input[name=miglad_amount]:checked").val();
    }

    if( clean_amount == 'custom' ){
      clean_amount = jQuery("#miglaCustomAmount").val() ;
    }

    clean_amount = clean_amount.replace( jQuery('#miglaThousandSep').val() ,"");
    clean_amount = clean_amount.replace( jQuery('#miglaDecimalSep').val() ,".");    
    
    return clean_amount;
}

function mg_get_country_code(country)
{
    var All_Country = mg_all_countries();
    var country_code = '';
    
    for(key in All_Country) {
        if( country == All_Country[key] ){
           country_code = key; 
        }
    }
    
    return country_code;
}

function mg_all_countries()
{
    var countries = {
    	'AF' : 'Afghanistan',
            		'AX' : 'Aland Islands',
            		'AL' : 'Albania',
            		'DZ' : 'Algeria',
            		'AS' : 'American Samoa',
            		'AD' : 'Andorra',
            		'AO' : 'Angola',
            		'AI' : 'Anguilla',
            		'AQ' : 'Antarctica',
            		'AG' : 'Antigua and Barbuda',
            		'AR' : 'Argentina',
            		'AM' : 'Armenia',
            		'AW' : 'Aruba',
            		'AU' : 'Australia',
            		'AT' : 'Austria',
            		'AZ' : 'Azerbaijan',
            
            		'BS' : 'Bahamas',
            		'BH' : 'Bahrain',
            		'BD' : 'Bangladesh',
            		'BB' : 'Barbados',
            		'BY' : 'Belarus',
            		'BE' : 'Belgium',
            		'BZ' : 'Belize',
            		'BJ' : 'Benin',
            		'BM' : 'Bermuda',
            		'BT' : 'Bhutan',
            		'BO' : 'Bolivia',
            		'BA' : 'Bosnia-Herzegovina',
            		'BW' : 'Botswana',
            		'BV' : 'Bouvet Island',
            		'BR' : 'Brazil',
            		'IO' : 'British Indian Ocean Territory',
            		'BN' : 'Brunei Darussalam',
            		'BG' : 'Bulgaria',
            		'BF' : 'Burkina Faso',
            		'BI' : 'Burundi',
            
            		'KH' : 'Cambodia',
            		'CM' : 'Cameroon',
            		'CA' : 'Canada',
            		'CV' : 'Cape Verde',
            		'KY' : 'Cayman Islands',
            		'CF' : 'Central African Republic',
            		'TD' : 'Chad',
            		'CL' : 'Chile',
            		'CN' : 'China',
            		'CX' : 'Christmas Island',
            		'CC' : 'Cocos (Keeling) Islands',
            		'CO' : 'Colombia',
            		'KM' : 'Comoros',
            		'CG' : 'Congo',
            		'CD' : 'Democratic Republic of Congo',
            		'CG' : 'Congo',
            		'CD' : 'Congo, Dem. Republic',
            		'CK' : 'Cook Islands',
            		'CR' : 'Costa Rica',
            		'HR' : 'Croatia',
            		'CY' : 'Cuba',
            		'CY' : 'Cyprus',
            		'CZ' : 'Czech Republic',
            
            		'DK' : 'Denmark',
            		'DJ' : 'Djibouti',
            		'DM' : 'Dominica',
            		'DO' : 'Dominican Republic',
            
            		'EC' : 'Ecuador',
            		'EG' : 'Egypt',
            		'SV' : 'El Salvador',
            		'GQ' : 'Equatorial Guinea',
            		'ER' : 'Eriteria',
            		'EE' : 'Estonia',
            		'ET' : 'Ethiopia',
            		'EU' : 'European Union',
            
            		'FK' : 'Falkland Islands (Malvinas)',
            		'FO' : 'Faroe Islands',
            		'FJ' : 'Fiji',
            		'FI' : 'Finland',
            		'FR' : 'France',
            		'GF' : 'French Guiana',
            		'PF' : 'French Polynesia',
            		'TF' : 'French Southern Territories',
            
            		'GA' : 'Gabon',
            		'GM' : 'Gambia',
            		'GE' : 'Georgia',
            		'DE' : 'Germany',
            		'GH' : 'Ghana',
            		'GI' : 'Gibraltar',
            		'GB' : 'Great Britain',
            		'GR' : 'Greece',
            		'GL' : 'Greenland',
            		'GD' : 'Grenada',
            		'GP' : 'Guadeloupe',
            		'GU' : 'Guam',
            		'GT' : 'Guatemala',
            		'GG' : 'Guernsey',
            		'GN' : 'Guinea',
            		'GW' : 'Guinea Bissau',
            		'GY' : 'Guyana',
            
            		'HT' : 'Haiti',
            		'HM' : 'Heard Island / McDonald Islands',
            		'VA' : 'Holy See (Vatican)',
            		'HN' : 'Honduras',
            		'HK' : 'Hong Kong',
            		'HU' : 'Hungary',
            
            		'IS' : 'Iceland',
            		'IN' : 'India',
            		'ID' : 'Indonesia',
            		'IE' : 'Ireland',
            		'IM' : 'Isle of Man',
            		'IL' : 'Israel',
            		'IT' : 'Italy',
            		'CI' : 'Ivory Coast',
            
            		'JM' : 'Jamaica',
            		'JP' : 'Japan',
            		'JE' : 'Jersey',
            		'JO' : 'Jordan',
            
            		'KZ' : 'Kazakhstan',
            		'KE' : 'Kenya',
            		'KI' : 'Kiribati',
            		'KR' : 'Korea, Republic of',
            		'KW' : 'Kuwait',
            		'KG' : 'Kyrgyzstan',
            
            		'LA' : 'Laos',
            		'LV' : 'Latvia',
            		'LB' : 'Lebanon',
            		'LS' : 'Lesotho',
            		'LI' : 'Liechtenstein',
            		'LT' : 'Lithuania',
            		'LU' : 'Luxembourg',
            
            		'MO' : 'Macao',
            		'MK' : 'Macedonia',
            		'MG' : 'Madagascar',
            		'MW' : 'Malawi',
            		'MY' : 'Malaysia',
            		'MV' : 'Maldives',
            		'ML' : 'Mali',
            		'MT' : 'Malta',
            		'MH' : 'Marshall Islands',
            		'MQ' : 'Martinique',
            		'MR' : 'Mauritania',
            		'MU' : 'Mauritius',
            		'YT' : 'Mayotte',
            		'MX' : 'Mexico',
            		'FM' : 'Micronesia, Federated States of',
            		'MD' : 'Moldova, Republic of',
            		'MC' : 'Monaco',
            		'MN' : 'Mongolia',
            		'ME' : 'Montenegro',
            		'MS' : 'Montserrat',
            		'MA' : 'Morocco',
            		'MZ' : 'Mozambique',
            
            		'NA' : 'Namibia',
            		'NR' : 'Nauru',
            		'NP' : 'Nepal',
            		'NL' : 'Netherlands',
            		'AN' : 'Netherlands Antilles',
            		'NC' : 'New Calendonia',
            		'NZ' : 'New Zealand',
            		'NI' : 'Nicaragua',
            		'NE' : 'Niger',
            		'NG' : 'Nigeria',
            		'NU' : 'Niue',
            		'NF' : 'Norfolk Island',
            		'MP' : 'Northern Mariana Islands',
            		'NO' : 'Norway',
            
            		'OM' : 'Oman',
            
            		'PK' : 'Pakistan',
            		'PW' : 'Palau',
            		'PS' : 'Palestine',
            		'PA' : 'Panama',
            		'PY' : 'Paraguay',
            		'PG' : 'Papua New Guinea',
            		'PE' : 'Peru',
            		'PH' : 'Philippines',
            		'PN' : 'Pitcairn',
            		'PL' : 'Poland',
            		'PT' : 'Portugal',
            		'PR' : 'Puerto Rico',
            
            		'QA' : 'Qatar',
            
            		'RE' : 'Reunion',
            		'RO' : 'Romania',
            		'RS' : 'Republic of Serbia',
            		'RU' : 'Russian Federation',
            		'RW' : 'Rwanda',
            
            		'SH' : 'Saint Helena',
            		'KN' : 'Saint Kitts and Nevis',
            		'LC' : 'Saint Lucia',
            		'PM' : 'Saint Pierre and Miquelon',
            		'VC' : 'Saint Vincent / Grenadines',
            		'WS' : 'Samoa',
            		'SM' : 'San Marino',
            		'ST' : 'Sao Tome and Principe',
            		'SA' : 'Saudi Arabia',
            		'SN' : 'Senegal',
            		'SC' : 'Seychelles',
            		'SL' : 'Sierra Leone',
            		'SG' : 'Singapore',
            		'SK' : 'Slovakia',
            		'SI' : 'Slovenia',
            		'SB' : 'Solomon Islands',
            		'SO' : 'Somalia',
            		'ZA' : 'South Africa',
            		'GS' : 'South Georgia / South Sandwich',
            		'ES' : 'Spain',
            		'LK' : 'Sri Lanka',
            		'SR' : 'Suriname',
            		'SJ' : 'Svalbard and Jan Mayen',
            		'SZ' : 'Swaziland',
            		'SE' : 'Sweden',
            		'CH' : 'Switzerland',
            
            		'TW' : 'Taiwan',
            		'TJ' : 'Tajikistan',
            		'TZ' : 'Tanzania, United Republic of',
            		'TH' : 'Thailand',
            		'TL' : 'Timor-Leste',
            		'TG' : 'Togo',
            		'TK' : 'Tokelau',
            		'TO' : 'Tonga',
            		'TT' : 'Trinidad and Tobago',
            		'TN' : 'Tunisia',
            		'TR' : 'Turkey',
            		'TM' : 'Turkmenistan',
            		'TC' : 'Turks and Caicos Islands',
            		'TV' : 'Tuvalu',
            
            		'UG' : 'Uganda',
            		'UA' : 'Ukraine',
            		'AE' : 'United Arab Emirates',
            		'GB' : 'United Kingdom',
            		'US' : 'United States',
            		'UM' : 'US Minor Outlying Islands',
            		'UY' : 'Uruguay',
            		'UZ' : 'Uzbekistan',
            
            		'VU' : 'Vanuatu',
            		'VE' : 'Venezuela',
            		'VN' : 'Vietnam',
            		'VG' : 'Virgin Islands, British',
            		'VI' : 'Virgin Islands, U.S.',
            
            		'WF' : 'Wallis and Futuna',
            		'EH' : 'Western Sahara',
            
            		'YE' : 'Yemen',
            
            		'ZM' : 'Zambia',
                            'ZW' : 'Zimbabwe'
    	};
	
	return countries;
}

function mg_stripe_init()
{
  stripe = Stripe( miglaAdminAjax.stripe_PK );

  // Create an instance of Elements.
  var elements = stripe.elements();

  // Custom styling can be passed to options when creating an Element.
  // (Note that this demo uses a wider set of styles than the guide below.)
  var style = {
    base: {
      color: '#32325d',
      fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
      fontSmoothing: 'antialiased',
      fontSize: '16px',
      '::placeholder': {
        color: '#aab7c4'
      }
    },
    invalid: {
      color: '#fa755a',
      iconColor: '#fa755a'
    }
  };

  // Create an instance of the card Element.
  stripeCard = elements.create('card', {style: style});

  // Add an instance of the card Element into the `card-element` <div>.
  stripeCard.mount('#card-element');

  // Handle real-time validation errors from the card Element.
  stripeCard.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = '';
    }
  });
  
  console.log('StripeCard is created');
}

function mg_stripeTokenHandler(response) 
{
    if (response.error) 
    {
        // show the errors on the form
        jQuery('#card-errors').html( response.error.message );
        
        jQuery('#miglastripecheckout').show();
        jQuery('.mg_wait').hide();
        
        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data :  {   action  : "miglaA_client_logged" ,
                        client_status     : "error",
                        client_message    : response.error.message
                    },
            success: function( stripemsg ) 
                    {       
                    },
            complete: function(xhr, error, status){
                    }
        });            

    }else{
        var stripemsg_decode = []; 

        jQuery.ajax({
                    type : "post",
                    url :  miglaAdminAjax.ajaxurl,
                    data :  { action    : "miglaA_stripeCharge_stripejs" ,
                                stripeToken : '',
                                amount    : (migla_postdata['amount'] * 100),
                                donorinfo : mdata,
                                session   : miglaAdminAjax.sid,
                                nonce     : miglaAdminAjax.nonce,
                                response_data : response,
                                form_id   : migla_form_id,
                                payment_intent : response.paymentIntent.id
                            },
                    success: function( stripemsg ) 
                            {       
                                stripemsg_decode = JSON.parse(stripemsg);
                                
                                if( stripemsg_decode[0] == '1' ){
                                    var url = mg_succes_url( migla_sessionid, stripemsg_decode[2], 's');
                                    window.location.replace(url);  
                                }else{
                                    jQuery('#card-errors').html( stripemsg_decode[1] );
                                    migla_after_cancel_click( 'miglastripecheckout', migla_form_id );  
                                }
                            },
                    error: function(xhr, error, status)
                            {
                               
                            },
                    complete: function(xhr, error, status)
                            {
                                        
                            }
        });
        
    }//If Response Error
}

jQuery(document).ready( function(){
    mg_init();
    mg_donation_process();
    
    var str_amount = 0;

    jQuery(".amt-btn").each(function(){
        if(jQuery(this).hasClass("selected")){
            str_amount = jQuery(this).find(".RadioInlineAmount").val();
            jQuery('.mg-donation-amount').text("Amount   : " + jQuery("#miglaDefaultCurrency").val() + " " + str_amount);    
        }
    });
    
    jQuery(".amt-btn").click(function(){
        var str_amount1 = jQuery(this).find(".RadioInlineAmount").val();
        jQuery('.mg-donation-amount').text("Amount   : "+ jQuery("#miglaDefaultCurrency").val() + " " + str_amount1);            
    })
    
    jQuery('#miglaCustomAmount').keyup(function(){
        var str_amount2 = jQuery(this).val();
        jQuery('.mg-donation-amount').text("Amount   : "+ jQuery("#miglaDefaultCurrency").val() + " " + str_amount2);            
        
    });    
   
    if( jQuery('#sectionstripe').length > 0 ){
        mg_stripe_init();

        var form = document.getElementById('mg-stripe-payment-form');
      
        form.addEventListener('submit', function(ev) {
            ev.preventDefault();
            return false;
        });
    }

    console.log('end');
});

function mg_3code_country(input)
{
    var allcountry = {
    'AF' : 'AFG',
    'AX' : 'ALA',
    'AL' : 'ALB',
    'DZ' : 'DZA',
    'AS' : 'ASM',
    'AD' : 'AND',
    'AO' : 'AGO',
    'AI' : 'AIA',
    'AQ' : 'ATA',
    'AG' : 'ATG',
    'AR' : 'ARG',
    'AM' : 'ARM',
    'AW' : 'ABW',
    'AU' : 'AUS',
    'AT' : 'AUT',
    'AZ' : 'AZE',
    
    'BS' : 'BHS',
    'BH' : 'BHR',
    'BD' : 'BGD',
    'BB' : 'BRB',
    'BY' : 'BLR',
    'BE' : 'BEL',
    'BZ' : 'BLZ',
    'BJ' : 'BEN',
    'BM' : 'BMU',
    'BT' : 'BTN',
    'BO' : 'BOL',
    'BA' : 'BIH',
    'BW' : 'BWA',
    'BV' : 'BVT',
    'BR' : 'BRA',
    'VG' : 'VGB',
    'IO' : 'IOT',
    'BN' : 'BRN',
    'BG' : 'BGR',
    'BF' : 'BFA',
    'BI' : 'BDI',
    
    'KH' : 'KHM',
    'CM' : 'CMR',
    'CA' : 'CAN',
    'CV' : 'CPV',
    'KY' : 'CYM',
    'CF' : 'CAF',
    'TD' : 'TCD',
    'CL' : 'CHL',
    'CN' : 'CHN',
    'HK' : 'HKG',
    'MO' : 'MAC',
    'CX' : 'CXR',
    'CC' : 'CCK',
    'CO' : 'COL',
    'KM' : 'COM',
    'CG' : 'COG',
    'CD' : 'COD',
    'CK' : 'COK',
    'CR' : 'CRI',
    'CI' : 'CIV',
    'HR' : 'HRV',
    'CU' : 'CUB',
    'CY' : 'CYP',
    'CZ' : 'CZE',
    
    'DK' : 'DNK',
    'DJ' : 'DJI',
    'DM' : 'DMA',
    'DO' : 'DOM',
    
    'EC' : 'ECU',
    'EG' : 'EGY',
    'SV' : 'SLV',
    'GQ' : 'GNQ',
    'ER' : 'ERI',
    'EE' : 'EST',
    'ET' : 'ETH',
    
    'FK' : 'FLK',
    'FO' : 'FRO',
    'FJ' : 'FJI',
    'FI' : 'FIN',
    'FR' : 'FRA',
    'GF' : 'GUF',
    'PF' : 'PYF',
    'TF' : 'ATF',
    
    'GA' : 'GAB',
    'GM' : 'GMB',
    'GE' : 'GEO',
    'DE' : 'DEU',
    'GH' : 'GHA',
    'GI' : 'GIB',
    'GR' : 'GRC',
    'GL' : 'GRL',
    'GD' : 'GRD',
    'GP' : 'GLP',
    'GU' : 'GUM',
    'GT' : 'GTM',
    'GG' : 'GGY',
    'GN' : 'GIN',
    'GW' : 'GNB',
    'GY' : 'GUY',
    
    'HT' : 'HTI',
    'HM' : 'HMD',
    'VA' : 'VAT',
    'HN' : 'HND',
    'HU' : 'HUN',
    'IS' : 'ISL',
    
    'IN' : 'IND',
    'ID' : 'IDN',
    'IR' : 'IRN',
    'IQ' : 'IRQ',
    'IE' : 'IRL',
    'IM' : 'IMN',
    'IL' : 'ISR',
    'IT' : 'ITA',
    
    'JM' : 'JAM',
    'JP' : 'JPN',
    'JE' : 'JEY',
    'JO' : 'JOR',
    
    'KZ' : 'KAZ',
    'KE' : 'KEN',
    'KI' : 'KIR',
    'KP' : 'PRK',
    'KR' : 'KOR',
    'KW' : 'KWT',
    'KG' : 'KGZ',
    
    'LA' : 'LAO',
    'LV' : 'LVA',
    'LB' : 'LBN',
    'LS' : 'LSO',
    'LR' : 'LBR',
    'LY' : 'LBY',
    'LI' : 'LIE',
    'LT' : 'LTU',
    'LU' : 'LUX',
    
    'MK' : 'MKD',
    'MG' : 'MDG',
    'MW' : 'MWI',
    'MY' : 'MYS',
    'MV' : 'MDV',
    'ML' : 'MLI',
    'MT' : 'MLT',
    'MH' : 'MHL',
    'MQ' : 'MTQ',
    'MR' : 'MRT',
    'MU' : 'MUS',
    'YT' : 'MYT',
    'MX' : 'MEX',
    'FM' : 'FSM',
    'MD' : 'MDA',
    'MC' : 'MCO',
    'MN' : 'MNG',
    'ME' : 'MNE',
    'MS' : 'MSR',
    'MA' : 'MAR',
    'MZ' : 'MOZ',
    'MM' : 'MMR',
    
    'NA' : 'NAM',
    'NR' : 'NRU',
    'NP' : 'NPL',
    'NL' : 'NLD',
    'AN' : 'ANT',
    'NC' : 'NCL',
    'NZ' : 'NZL',
    'NI' : 'NIC',
    'NE' : 'NER',
    'NG' : 'NGA',
    'NU' : 'NIU',
    'NF' : 'NFK',
    'MP' : 'MNP',
    'NO' : 'NOR',
    
    'OM' : 'OMN',
    
    'PK' : 'PAK',
    'PW' : 'PLW',
    'PS' : 'PSE',
    'PA' : 'PAN',
    'PG' : 'PNG',
    'PY' : 'PRY',
    'PE' : 'PER',
    'PH' : 'PHL',
    'PN' : 'PCN',
    'PL' : 'POL',
    'PT' : 'PRT',
    'PR' : 'PRI',
    
    'QA' : 'QAT',
    
    'RE' : 'REU',
    'RO' : 'ROU',
    'RU' : 'RUS',
    'RW' : 'RWA',
    
    'BL' : 'BLM',
    'SH' : 'SHN',
    'KN' : 'KNA',
    'LC' : 'LCA',
    'MF' : 'MAF',
    'PM' : 'SPM',
    'VC' : 'VCT',
    'WS' : 'WSM',
    'SM' : 'SMR',
    'ST' : 'STP',
    'SA' : 'SAU',
    'SN' : 'SEN',
    'RS' : 'SRB',
    'SC' : 'SYC',
    'SL' : 'SLE',
    'SG' : 'SGP',
    'SK' : 'SVK',
    'SI' : 'SVN',
    'SB' : 'SLB',
    'SO' : 'SOM',
    'ZA' : 'ZAF',
    'GS' : 'SGS',
    'SS' : 'SSD',
    'ES' : 'ESP',
    'LK' : 'LKA',
    'SD' : 'SDN',
    'SR' : 'SUR',
    'SJ' : 'SJM',
    'SZ' : 'SWZ',
    'SE' : 'SWE',
    'CH' : 'CHE',
    'SY' : 'SYR',
    
    'TW' : 'TWN',
    'TJ' : 'TJK',
    'TZ' : 'TZA',
    'TH' : 'THA',
    'TL' : 'TLS',
    'TG' : 'TGO',
    'TK' : 'TKL',
    'TO' : 'TON',
    'TT' : 'TTO',
    'TN' : 'TUN',
    'TR' : 'TUR',
    'TM' : 'TKM',
    'TC' : 'TCA',
    'TV' : 'TUV',
    
    'UG' : 'UGA',
    'UA' : 'UKR',
    'AE' : 'ARE',
    'GB' : 'GBR',
    'US' : 'USA',
    'UM' : 'UMI',
    'UY' : 'URY',
    'UZ' : 'UZB',
    
    'VU' : 'VUT',
    'VE' : 'VEN',
    'VN' : 'VNM',
    'VI' : 'VIR',
    
    'WF' : 'WLF',
    'EH' : 'ESH',
    
    'YE' : 'YEM',
    'ZM' : 'ZMB',
    'ZW' : 'ZWE'
    };
    
    var country_code = input;
    
    for(key2 in allcountry) {
        if( input == key2 ){
           country_code =  allcountry[key2]; 
        }
    }
    console.log( country_code);
    return country_code;
}

function mg_get_language(country){
	var languages = {
		'AL' : 'en_US',
		'DZ' : 'ar_EG',
		'DZ' : 'en_US',
		'DZ' : 'fr_XC',
		'DZ' : 'es_XC',
		'DZ' : 'zh_XC',
		'AD' : 'en_US',
		'AD' : 'fr_XC',
		'AD' : 'es_XC',
		'AD' : 'zh_XC',
		'AO' : 'en_US',
		'AO' : 'fr_XC',
		'AO' : 'es_XC',
		'AO' : 'zh_XC',
		'AI' : 'en_US',
		'AI' : 'fr_XC',
		'AI' : 'es_XC',
		'AI' : 'zh_XC',
		'AG' : 'en_US',
		'AG' : 'fr_XC',
		'AG' : 'es_XC',
		'AG' : 'zh_XC',
		'AR' : 'es_XC',
		'AR' : 'en_US',
		'AM' : 'en_US',
		'AM' : 'fr_XC',
		'AM' : 'es_XC',
		'AM' : 'zh_XC',
		'AW' : 'en_US',
		'AW' : 'fr_XC',
		'AW' : 'es_XC',
		'AW' : 'zh_XC',
		'AU' : 'en_AU',
		'AT' : 'de_DE',
		'AT' : 'en_US',
		'AZ' : 'en_US',
		'AZ' : 'fr_XC',
		'AZ' : 'es_XC',
		'AZ' : 'zh_XC',
		'BS' : 'en_US',
		'BS' : 'fr_XC',
		'BS' : 'es_XC',
		'BS' : 'zh_XC',
		'BH' : 'ar_EG',
		'BH' : 'en_US',
		'BH' : 'fr_XC',
		'BH' : 'es_XC',
		'BH' : 'zh_XC',
		'BB' : 'en_US',
		'BB' : 'fr_XC',
		'BB' : 'es_XC',
		'BB' : 'zh_XC',
		'BY' : 'en_US',
		'BE' : 'en_US',
		'BE' : 'nl_NL',
		'BE' : 'fr_FR',
		'BZ' : 'es_XC',
		'BZ' : 'en_US',
		'BZ' : 'fr_XC',
		'BZ' : 'zh_XC',
		'BJ' : 'fr_XC',
		'BJ' : 'en_US',
		'BJ' : 'es_XC',
		'BJ' : 'zh_XC',
		'BM' : 'en_US',
		'BM' : 'fr_XC',
		'BM' : 'es_XC',
		'BM' : 'zh_XC',
		'BT' : 'en_US',
		'BO' : 'es_XC',
		'BO' : 'en_US',
		'BO' : 'fr_XC',
		'BO' : 'zh_XC',
		'BA' : 'en_US',
		'BW' : 'en_US',
		'BW' : 'fr_XC',
		'BW' : 'es_XC',
		'BW' : 'zh_XC',
		'BR' : 'pt_BR',
		'BR' : 'en_US',
		'VG' : 'en_US',
		'VG' : 'fr_XC',
		'VG' : 'es_XC',
		'VG' : 'zh_XC',
		'BN' : 'en_US',
		'BG' : 'en_US',
		'BF' : 'fr_XC',
		'BF' : 'en_US',
		'BF' : 'es_XC',
		'BF' : 'zh_XC',
		'BI' : 'fr_XC',
		'BI' : 'en_US',
		'BI' : 'es_XC',
		'BI' : 'zh_XC',
		'KH' : 'en_US',
		'CM' : 'fr_XC',
		'CM' : 'en_US',
		'CA' : 'en_US',
		'CA' : 'fr_CA',
		'CV' : 'en_US',
		'CV' : 'fr_XC',
		'CV' : 'es_XC',
		'CV' : 'zh_XC',
		'KY' : 'en_US',
		'KY' : 'fr_XC',
		'KY' : 'es_XC',
		'KY' : 'zh_XC',
		'TD' : 'fr_XC',
		'TD' : 'en_US',
		'TD' : 'es_XC',
		'TD' : 'zh_XC',
		'CL' : 'es_XC',
		'CL' : 'en_US',
		'CL' : 'fr_XC',
		'CL' : 'zh_XC',
		'CN' : 'zh_CN',
		'C2' : 'zh_XC',
		'C2' : 'en_US',
		'CO' : 'es_XC',
		'CO' : 'en_US',
		'CO' : 'fr_XC',
		'CO' : 'zh_XC',
		'KM' : 'fr_XC',
		'KM' : 'en_US',
		'KM' : 'es_XC',
		'KM' : 'zh_XC',
		'CG' : 'en_US',
		'CG' : 'fr_XC',
		'CG' : 'es_XC',
		'CG' : 'zh_XC',
		'CD' : 'fr_XC',
		'CD' : 'en_US',
		'CD' : 'es_XC',
		'CD' : 'zh_XC',
		'CK' : 'en_US',
		'CK' : 'fr_XC',
		'CK' : 'es_XC',
		'CK' : 'zh_XC',
		'CR' : 'es_XC',
		'CR' : 'en_US',
		'CR' : 'fr_XC',
		'CR' : 'zh_XC',
		'CI' : 'fr_XC',
		'CI' : 'en_US',
		'HR' : 'en_US',
		'CY' : 'en_US',
		'CZ' : 'cs_CZ',
		'CZ' : 'en_US',
		'CZ' : 'fr_XC',
		'CZ' : 'es_XC',
		'CZ' : 'zh_XC',
		'DK' : 'da_DK',
		'DK' : 'en_US',
		'DJ' : 'fr_XC',
		'DJ' : 'en_US',
		'DJ' : 'es_XC',
		'DJ' : 'zh_XC',
		'DM' : 'en_US',
		'DM' : 'fr_XC',
		'DM' : 'es_XC',
		'DM' : 'zh_XC',
		'DO' : 'es_XC',
		'DO' : 'en_US',
		'DO' : 'fr_XC',
		'DO' : 'zh_XC',
		'EC' : 'es_XC',
		'EC' : 'en_US',
		'EC' : 'fr_XC',
		'EC' : 'zh_XC',
		'EG' : 'ar_EG',
		'EG' : 'en_US',
		'EG' : 'fr_XC',
		'EG' : 'es_XC',
		'EG' : 'zh_XC',
		'SV' : 'es_XC',
		'SV' : 'en_US',
		'SV' : 'fr_XC',
		'SV' : 'zh_XC',
		'ER' : 'en_US',
		'ER' : 'fr_XC',
		'ER' : 'es_XC',
		'ER' : 'zh_XC',
		'EE' : 'en_US',
		'EE' : 'ru_RU',
		'EE' : 'fr_XC',
		'EE' : 'es_XC',
		'EE' : 'zh_XC',
		'ET' : 'en_US',
		'ET' : 'fr_XC',
		'ET' : 'es_XC',
		'ET' : 'zh_XC',
		'FK' : 'en_US',
		'FK' : 'fr_XC',
		'FK' : 'es_XC',
		'FK' : 'zh_XC',
		'FO' : 'da_DK',
		'FO' : 'en_US',
		'FO' : 'fr_XC',
		'FO' : 'es_XC',
		'FO' : 'zh_XC',
		'FJ' : 'en_US',
		'FJ' : 'fr_XC',
		'FJ' : 'es_XC',
		'FJ' : 'zh_XC',
		'FI' : 'fi_FI',
		'FI' : 'en_US',
		'FI' : 'fr_XC',
		'FI' : 'es_XC',
		'FI' : 'zh_XC',
		'FR' : 'fr_FR',
		'FR' : 'en_US',
		'GF' : 'en_US',
		'GF' : 'fr_XC',
		'GF' : 'es_XC',
		'GF' : 'zh_XC',
		'PF' : 'en_US',
		'PF' : 'fr_XC',
		'PF' : 'es_XC',
		'PF' : 'zh_XC',
		'GA' : 'fr_XC',
		'GA' : 'en_US',
		'GA' : 'es_XC',
		'GA' : 'zh_XC',
		'GM' : 'en_US',
		'GM' : 'fr_XC',
		'GM' : 'es_XC',
		'GM' : 'zh_XC',
		'GE' : 'en_US',
		'GE' : 'fr_XC',
		'GE' : 'es_XC',
		'GE' : 'zh_XC',
		'DE' : 'de_DE',
		'DE' : 'en_US',
		'GI' : 'en_US',
		'GI' : 'fr_XC',
		'GI' : 'es_XC',
		'GI' : 'zh_XC',
		'GR' : 'el_GR',
		'GR' : 'en_US',
		'GR' : 'fr_XC',
		'GR' : 'es_XC',
		'GR' : 'zh_XC',
		'GL' : 'da_DK',
		'GL' : 'en_US',
		'GL' : 'fr_XC',
		'GL' : 'es_XC',
		'GL' : 'zh_XC',
		'GD' : 'en_US',
		'GD' : 'fr_XC',
		'GD' : 'es_XC',
		'GD' : 'zh_XC',
		'GP' : 'en_US',
		'GP' : 'fr_XC',
		'GP' : 'es_XC',
		'GP' : 'zh_XC',
		'GT' : 'es_XC',
		'GT' : 'en_US',
		'GT' : 'fr_XC',
		'GT' : 'zh_XC',
		'GN' : 'fr_XC',
		'GN' : 'en_US',
		'GN' : 'es_XC',
		'GN' : 'zh_XC',
		'GW' : 'en_US',
		'GW' : 'fr_XC',
		'GW' : 'es_XC',
		'GW' : 'zh_XC',
		'GY' : 'en_US',
		'GY' : 'fr_XC',
		'GY' : 'es_XC',
		'GY' : 'zh_XC',
		'HN' : 'es_XC',
		'HN' : 'en_US',
		'HN' : 'fr_XC',
		'HN' : 'zh_XC',
		'HK' : 'en_GB',
		'HK' : 'zh_HK',
		'HU' : 'hu_HU',
		'HU' : 'en_US',
		'HU' : 'fr_XC',
		'HU' : 'es_XC',
		'HU' : 'zh_XC',
		'IS' : 'en_US',
		'IN' : 'en_IN',
		'ID' : 'id_ID',
		'ID' : 'en_US',
		'IE' : 'en_US',
		'IE' : 'fr_XC',
		'IE' : 'es_XC',
		'IE' : 'zh_XC',
		'IL' : 'he_IL',
		'IL' : 'en_US',
		'IT' : 'it_IT',
		'IT' : 'en_US',
		'JM' : 'es_XC',
		'JM' : 'en_US',
		'JM' : 'fr_XC',
		'JM' : 'zh_XC',
		'JP' : 'ja_JP',
		'JP' : 'en_US',
		'JO' : 'ar_EG',
		'JO' : 'en_US',
		'JO' : 'fr_XC',
		'JO' : 'es_XC',
		'JO' : 'zh_XC',
		'KZ' : 'en_US',
		'KZ' : 'fr_XC',
		'KZ' : 'es_XC',
		'KZ' : 'zh_XC',
		'KE' : 'en_US',
		'KE' : 'fr_XC',
		'KE' : 'es_XC',
		'KE' : 'zh_XC',
		'KI' : 'en_US',
		'KI' : 'fr_XC',
		'KI' : 'es_XC',
		'KI' : 'zh_XC',
		'KW' : 'ar_EG',
		'KW' : 'en_US',
		'KW' : 'fr_XC',
		'KW' : 'es_XC',
		'KW' : 'zh_XC',
		'KG' : 'en_US',
		'KG' : 'fr_XC',
		'KG' : 'es_XC',
		'KG' : 'zh_XC',
		'LA' : 'en_US',
		'LV' : 'en_US',
		'LV' : 'ru_RU',
		'LV' : 'fr_XC',
		'LV' : 'es_XC',
		'LV' : 'zh_XC',
		'LS' : 'en_US',
		'LS' : 'fr_XC',
		'LS' : 'es_XC',
		'LS' : 'zh_XC',
		'LI' : 'en_US',
		'LI' : 'fr_XC',
		'LI' : 'es_XC',
		'LI' : 'zh_XC',
		'LT' : 'en_US',
		'LT' : 'ru_RU',
		'LT' : 'fr_XC',
		'LT' : 'es_XC',
		'LT' : 'zh_XC',
		'LU' : 'en_US',
		'LU' : 'de_DE',
		'LU' : 'fr_XC',
		'LU' : 'es_XC',
		'LU' : 'zh_XC',
		'MK' : 'en_US',
		'MG' : 'en_US',
		'MG' : 'fr_XC',
		'MG' : 'es_XC',
		'MG' : 'zh_XC',
		'MW' : 'en_US',
		'MW' : 'fr_XC',
		'MW' : 'es_XC',
		'MW' : 'zh_XC',
		'MY' : 'en_US',
		'MV' : 'en_US',
		'ML' : 'fr_XC',
		'ML' : 'en_US',
		'ML' : 'es_XC',
		'ML' : 'zh_XC',
		'MT' : 'en_US',
		'MH' : 'en_US',
		'MH' : 'fr_XC',
		'MH' : 'es_XC',
		'MH' : 'zh_XC',
		'MQ' : 'en_US',
		'MQ' : 'fr_XC',
		'MQ' : 'es_XC',
		'MQ' : 'zh_XC',
		'MR' : 'en_US',
		'MR' : 'fr_XC',
		'MR' : 'es_XC',
		'MR' : 'zh_XC',
		'MU' : 'en_US',
		'MU' : 'fr_XC',
		'MU' : 'es_XC',
		'MU' : 'zh_XC',
		'YT' : 'en_US',
		'YT' : 'fr_XC',
		'YT' : 'es_XC',
		'YT' : 'zh_XC',
		'MX' : 'es_XC',
		'MX' : 'en_US',
		'FM' : 'en_US',
		'MD' : 'en_US',
		'MC' : 'fr_XC',
		'MC' : 'en_US',
		'MN' : 'en_US',
		'ME' : 'en_US',
		'MS' : 'en_US',
		'MS' : 'fr_XC',
		'MS' : 'es_XC',
		'MS' : 'zh_XC',
		'MA' : 'ar_EG',
		'MA' : 'en_US',
		'MA' : 'fr_XC',
		'MA' : 'es_XC',
		'MA' : 'zh_XC',
		'MZ' : 'en_US',
		'MZ' : 'fr_XC',
		'MZ' : 'es_XC',
		'MZ' : 'zh_XC',
		'NA' : 'en_US',
		'NA' : 'fr_XC',
		'NA' : 'es_XC',
		'NA' : 'zh_XC',
		'NR' : 'en_US',
		'NR' : 'fr_XC',
		'NR' : 'es_XC',
		'NR' : 'zh_XC',
		'NP' : 'en_US',
		'NL' : 'nl_NL',
		'NL' : 'en_US',
		'NC' : 'en_US',
		'NC' : 'fr_XC',
		'NC' : 'es_XC',
		'NC' : 'zh_XC',
		'NZ' : 'en_US',
		'NZ' : 'fr_XC',
		'NZ' : 'es_XC',
		'NZ' : 'zh_XC',
		'NI' : 'es_XC',
		'NI' : 'en_US',
		'NI' : 'fr_XC',
		'NI' : 'zh_XC',
		'NE' : 'fr_XC',
		'NE' : 'en_US',
		'NE' : 'es_XC',
		'NE' : 'zh_XC',
		'NG' : 'en_US',
		'NU' : 'en_US',
		'NU' : 'fr_XC',
		'NU' : 'es_XC',
		'NU' : 'zh_XC',
		'NF' : 'en_US',
		'NF' : 'fr_XC',
		'NF' : 'es_XC',
		'NF' : 'zh_XC',
		'NO' : 'no_NO',
		'NO' : 'en_US',
		'OM' : 'ar_EG',
		'OM' : 'en_US',
		'OM' : 'fr_XC',
		'OM' : 'es_XC',
		'OM' : 'zh_XC',
		'PW' : 'en_US',
		'PW' : 'fr_XC',
		'PW' : 'es_XC',
		'PW' : 'zh_XC',
		'PA' : 'es_XC',
		'PA' : 'en_US',
		'PA' : 'fr_XC',
		'PA' : 'zh_XC',
		'PG' : 'en_US',
		'PG' : 'fr_XC',
		'PG' : 'es_XC',
		'PG' : 'zh_XC',
		'PY' : 'es_XC',
		'PY' : 'en_US',
		'PE' : 'es_XC',
		'PE' : 'en_US',
		'PE' : 'fr_XC',
		'PE' : 'zh_XC',
		'PH' : 'en_US',
		'PN' : 'en_US',
		'PN' : 'fr_XC',
		'PN' : 'es_XC',
		'PN' : 'zh_XC',
		'PL' : 'pl_PL',
		'PL' : 'en_US',
		'PT' : 'pt_PT',
		'PT' : 'en_US',
		'QA' : 'en_US',
		'QA' : 'fr_XC',
		'QA' : 'es_XC',
		'QA' : 'zh_XC',
		'QA' : 'ar_EG',
		'RE' : 'en_US',
		'RE' : 'fr_XC',
		'RE' : 'es_XC',
		'RE' : 'zh_XC',
		'RO' : 'en_US',
		'RO' : 'fr_XC',
		'RO' : 'es_XC',
		'RO' : 'zh_XC',
		'RU' : 'ru_RU',
		'RU' : 'en_US',
		'RW' : 'fr_XC',
		'RW' : 'en_US',
		'RW' : 'es_XC',
		'RW' : 'zh_XC',
		'WS' : 'en_US',
		'SM' : 'en_US',
		'SM' : 'fr_XC',
		'SM' : 'es_XC',
		'SM' : 'zh_XC',
		'ST' : 'en_US',
		'ST' : 'fr_XC',
		'ST' : 'es_XC',
		'ST' : 'zh_XC',
		'SA' : 'ar_EG',
		'SA' : 'en_US',
		'SA' : 'fr_XC',
		'SA' : 'es_XC',
		'SA' : 'zh_XC',
		'SN' : 'fr_XC',
		'SN' : 'en_US',
		'SN' : 'es_XC',
		'SN' : 'zh_XC',
		'RS' : 'en_US',
		'RS' : 'fr_XC',
		'RS' : 'es_XC',
		'RS' : 'zh_XC',
		'SC' : 'fr_XC',
		'SC' : 'en_US',
		'SC' : 'es_XC',
		'SC' : 'zh_XC',
		'SL' : 'en_US',
		'SL' : 'fr_XC',
		'SL' : 'es_XC',
		'SL' : 'zh_XC',
		'SG' : 'en_GB',
		'SK' : 'sk_SK',
		'SK' : 'en_US',
		'SK' : 'fr_XC',
		'SK' : 'es_XC',
		'SK' : 'zh_XC',
		'SI' : 'en_US',
		'SI' : 'fr_XC',
		'SI' : 'es_XC',
		'SI' : 'zh_XC',
		'SB' : 'en_US',
		'SB' : 'fr_XC',
		'SB' : 'es_XC',
		'SB' : 'zh_XC',
		'SO' : 'en_US',
		'SO' : 'fr_XC',
		'SO' : 'es_XC',
		'SO' : 'zh_XC',
		'ZA' : 'en_US',
		'ZA' : 'fr_XC',
		'ZA' : 'es_XC',
		'ZA' : 'zh_XC',
		'KR' : 'ko_KR',
		'KR' : 'en_US',
		'ES' : 'es_ES',
		'ES' : 'en_US',
		'LK' : 'en_US',
		'SH' : 'en_US',
		'SH' : 'fr_XC',
		'SH' : 'es_XC',
		'SH' : 'zh_XC',
		'KN' : 'en_US',
		'KN' : 'fr_XC',
		'KN' : 'es_XC',
		'KN' : 'zh_XC',
		'LC' : 'en_US',
		'LC' : 'fr_XC',
		'LC' : 'es_XC',
		'LC' : 'zh_XC',
		'PM' : 'en_US',
		'PM' : 'fr_XC',
		'PM' : 'es_XC',
		'PM' : 'zh_XC',
		'VC' : 'en_US',
		'VC' : 'fr_XC',
		'VC' : 'es_XC',
		'VC' : 'zh_XC',
		'SR' : 'en_US',
		'SR' : 'fr_XC',
		'SR' : 'es_XC',
		'SR' : 'zh_XC',
		'SJ' : 'en_US',
		'SJ' : 'fr_XC',
		'SJ' : 'es_XC',
		'SJ' : 'zh_XC',
		'SZ' : 'en_US',
		'SZ' : 'fr_XC',
		'SZ' : 'es_XC',
		'SZ' : 'zh_XC',
		'SE' : 'sv_SE',
		'SE' : 'en_US',
		'CH' : 'de_DE',
		'CH' : 'fr_FR',
		'CH' : 'en_US',
		'TW' : 'zh_TW',
		'TW' : 'en_US',
		'TJ' : 'en_US',
		'TJ' : 'fr_XC',
		'TJ' : 'es_XC',
		'TJ' : 'zh_XC',
		'TZ' : 'en_US',
		'TZ' : 'fr_XC',
		'TZ' : 'es_XC',
		'TZ' : 'zh_XC',
		'TH' : 'th_TH',
		'TH' : 'en_GB',
		'TG' : 'fr_XC',
		'TG' : 'en_US',
		'TG' : 'es_XC',
		'TG' : 'zh_XC',
		'TO' : 'en_US',
		'TT' : 'en_US',
		'TT' : 'fr_XC',
		'TT' : 'es_XC',
		'TT' : 'zh_XC',
		'TN' : 'ar_EG',
		'TN' : 'en_US',
		'TN' : 'fr_XC',
		'TN' : 'es_XC',
		'TN' : 'zh_XC',
		'TM' : 'en_US',
		'TM' : 'fr_XC',
		'TM' : 'es_XC',
		'TM' : 'zh_XC',
		'TC' : 'en_US',
		'TC' : 'fr_XC',
		'TC' : 'es_XC',
		'TC' : 'zh_XC',
		'TV' : 'en_US',
		'TV' : 'fr_XC',
		'TV' : 'es_XC',
		'TV' : 'zh_XC',
		'UG' : 'en_US',
		'UG' : 'fr_XC',
		'UG' : 'es_XC',
		'UG' : 'zh_XC',
		'UA' : 'en_US',
		'UA' : 'ru_RU',
		'UA' : 'fr_XC',
		'UA' : 'es_XC',
		'UA' : 'zh_XC',
		'AE' : 'en_US',
		'AE' : 'fr_XC',
		'AE' : 'es_XC',
		'AE' : 'zh_XC',
		'AE' : 'ar_EG',
		'GB' : 'en_GB',
		'US' : 'en_US',
		'US' : 'fr_XC',
		'US' : 'es_XC',
		'US' : 'zh_XC',
		'UY' : 'es_XC',
		'UY' : 'en_US',
		'UY' : 'fr_XC',
		'UY' : 'zh_XC',
		'VU' : 'en_US',
		'VU' : 'fr_XC',
		'VU' : 'es_XC',
		'VU' : 'zh_XC',
		'VA' : 'en_US',
		'VA' : 'fr_XC',
		'VA' : 'es_XC',
		'VA' : 'zh_XC',
		'VE' : 'es_XC',
		'VE' : 'en_US',
		'VE' : 'fr_XC',
		'VE' : 'zh_XC',
		'VN' : 'en_US',
		'WF' : 'en_US',
		'WF' : 'fr_XC',
		'WF' : 'es_XC',
		'WF' : 'zh_XC',
		'YE' : 'ar_EG',
		'YE' : 'en_US',
		'YE' : 'fr_XC',
		'YE' : 'es_XC',
		'YE' : 'zh_XC',
		'ZM' : 'en_US',
		'ZM' : 'fr_XC',
		'ZM' : 'es_XC',
		'ZM' : 'zh_XC',
		'ZW' : 'en_US'
	}

	var lang_code = jQuery('#migla_language').val();
    
    for(key in languages) {
        if( country == key ){
           lang_code = languages[key]; 
        }
    }
    
    return lang_code;	
}