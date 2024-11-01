function cleanIt( dirty )
{
    var _dirty = dirty;
    var clean ;

    clean = _dirty.replace(/\//gi,"//");
    clean = clean.replace(/"/gi,"[q]");
    clean = clean.replace(/'/gi,"[q]");
    return clean;
}

function mg_cc_info()
{
        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
               data : { action : "TotalDonationsAjax_update_paypal_tab",
                        choice1 : jQuery('#mg_paypalpro-radio1').val(),
                        choice2 : jQuery('#mg_paypalpro-radio2').val(),
                        tab    : jQuery('#mg_tab-paypalpro').val(),
                        cardholder_label : jQuery('#mg_name-paypal').val(),
                        cardholder_placeholder : jQuery('#mg_placeholder-name').val(),
                        cardholder_lastplaceholder : jQuery('#mg_lname-paypal').val(),
                        cardnumber_label : jQuery('#mg_cardnumber-paypal').val(),
                        cardnumber_placeholder : jQuery('#mg_placeholder-card').val(),
                        cardcvc_label : jQuery('#mg_cvc-paypal').val(),
                        cardcvc_placeholder : jQuery('#mg_placeholder-CVC').val(),
                        buttontext : jQuery('#mg_CSSButtonText').val(),
                        message    : jQuery('#mg_waiting_paypal').val(),
                        message_pro    : jQuery('#mg_waiting_paypalpro').val(),
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                    },
            success: function(msg) {

                    },
            error: function(xhr, status, error)
                  {
                    alert(error);
                  },
            complete: function(xhr, status, error)
                  {
                      console.log('paypal tab');
                  }
        });
}

function mg_save_cc_info()
{
    jQuery('#miglaSaveCCInfo').click(function(){
        mg_cc_info();
        saved('#miglaSaveCCInfo');
    })
}

function mg_paypal_button()
{
    jQuery('#migla-save-paypal-btn').click(function(){

         mg_cc_info();

        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : {  action  : "TotalDonationsAjax_update_paypal_button",
                      btn_choice : 'cssButton',
                      btnlang    : 'en_US',
                      btnurl     : '',
                      buttonstyle: jQuery('#mg_CSSButtonPicker').val(),
                      btnclass   : jQuery('#mg_CSSButtonClass').val(),

                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                    },
            success: function(){
                    },
            error: function(xhr, status, error)
                    {
                        alert(error);
                    },
            complete: function(xhr, status, error)
                    {
                        saved('#migla-save-paypal-btn');
                    }
        });
    });
}

function mg_upload_media()
{
   jQuery('#miglaUploadpaypalBtn').click(function() {
       formfield = jQuery('#mg_upload_image').attr('name');
       tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
       return false;
    });

    window.send_to_editor = function(html) {
       imgurl = jQuery('img',html).attr('src');
       jQuery('#mg_upload_image').val(imgurl);
       tb_remove();
    }
}

jQuery(document).ready(function() {

console.log("Start");

    jQuery('#miglaUpdatePaypalAccSettings').click(function() {

        jQuery.ajax({
            type  : "post",
            url   :  miglaAdminAjax.ajaxurl,
            data  : { action : "TotalDonationsAjax_update_me",
                    key    : 'migla_paypal_emails',
                    value  : jQuery('#miglaPaypalEmails').val(),
                    valtype : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
            success: function(msg) {
                    },
            error: function(xhr, status, error)
                    {
                      alert(error);
                    },
            complete: function(xhr, status, error)
                    {
                    }
        });

        var isChatBack = 'no';

        if(jQuery('#migla_ipn_chatback').is(":checked"))
        {
            isChatBack = 'yes';
        }

        jQuery.ajax({
            type  : "post",
            url   :  miglaAdminAjax.ajaxurl,
            data  : { action : 'TotalDonationsAjax_update_me',
                    key      : 'migla_ipn_chatback',
                    value    : isChatBack,
                    valtype  : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
            success: function(msg) {
                    },
            error: function(xhr, status, error)
                    {
                      alert(error);
                    },
            complete: function(xhr, status, error)
                    {
                    }
        });

        jQuery.ajax({
            type  : "post",
            url   :  miglaAdminAjax.ajaxurl,
            data  : { action : 'TotalDonationsAjax_update_me',
                    key      : 'migla_paypal_payment',
                    value    : jQuery('#mg_payment').val(),
                    valtype  : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
            success: function(msg) {
                    },
            error: function(xhr, status, error)
                    {
                      alert(error);
                    },
            complete: function(xhr, status, error)
                    {
                    }
        });

        saved('#miglaUpdatePaypalAccSettings');

    });

    mg_upload_media();

    mg_paypal_button();

console.log("End");

}); //Document Ready
