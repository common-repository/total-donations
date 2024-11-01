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
        var stripesbtn = '';
        stripesbtn = jQuery('#mg_CSSButtonText').val() ;

        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
               data : { action : "TotalDonationsAjax_update_stripe_tab",
                        tab : jQuery('#mg_stripe-tab').val(),
                        cardholder_label : jQuery('#mg_stripe-label').val(),
                        cardholder_placeholder : jQuery('#mg_stripe-placeholder').val(),
                        cardnumber_label : jQuery('#mg_label-card').val() ,
                        cardnumber_placeholder : jQuery('#mg_placeholder-card').val(),
                        buttontext :  stripesbtn,
                        message    : jQuery('#mg_waiting_stripe').val(),
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
                      console.log('Stripe tab');
                  }
        });

    console.log('click');
}

function mg_save_cc_info()
{
    jQuery('#miglaSaveCCInfo').click(function(){
        mg_cc_info();
        saved('#miglaSaveCCInfo');
    })
}

function mg_stripe_keys()
{
 jQuery('#miglaUpdateStripeKeys').click(function(){

     var mg_liveSK = jQuery.trim(jQuery('#migla_liveSK').val());
     var mg_livePK = jQuery.trim(jQuery('#migla_livePK').val());
     var mg_testSK = jQuery.trim(jQuery('#migla_testSK').val());
     var mg_testPK = jQuery.trim(jQuery('#migla_testPK').val());
     var mg_webhook_key = jQuery.trim(jQuery('#migla_webhook_key').val());

    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl,
      data : { action : "TotalDonationsAjax_update_me",
                key   : 'migla_liveSK',
                value : mg_liveSK,
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
    })  ; //ajax

    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl,
      data : {  action : "TotalDonationsAjax_update_me",
                key    : 'migla_livePK',
                value  : mg_livePK,
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
    })  ; //ajax

    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl,
      data : {  action : "TotalDonationsAjax_update_me",
                key    : 'migla_testSK',
                value  : mg_testSK,
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
    })  ; //ajax

    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl,
      data : {  action : "TotalDonationsAjax_update_me",
                key    : 'migla_testPK',
                value   : mg_testPK,
                valtype : 'text' ,
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
    })  ; //ajax


    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl,
      data : {  action : "TotalDonationsAjax_update_me",
                key    : 'migla_stripemode',
                value  : jQuery("input[name='miglaStripe']:checked").val(),
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
    })  ; //ajax

    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl,
      data : {  action : "TotalDonationsAjax_update_me",
                key    : 'migla_webhook_key',
                value  : mg_webhook_key,
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
    })  ; //ajax

    saved('#miglaUpdateStripeKeys');
 });
}

function mg_stripe_button()
{

    jQuery('#migla-save-stripe-btn').click(function(){

        mg_cc_info();

        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : {  action  : "TotalDonationsAjax_update_stripe_button",
                      StripeButtonChoice : 'cssButton',
                      buttonstyle: jQuery('#mg_CSSButtonPicker').val(),
                      btnclass   : jQuery('#mg_CSSButtonClass').val(),
                      btnurl     : '',
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
                    }
        });

        saved('#migla-save-stripe-btn');
    })
}

function mg_upload_media()
{
   jQuery("#miglaUploadstripeBtn").click(function(){
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

jQuery(document).ready( function() {

  mg_stripe_button();
  mg_save_cc_info();
  mg_stripe_keys();
  mg_upload_media();

  console.log('end');
})
