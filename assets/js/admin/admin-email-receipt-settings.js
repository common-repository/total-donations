var meta_image_frame;
var is_enabled_thankyouemail;
var is_enabled_attachment;
var is_enabled_honoreeemail;
var is_enabled_offlineemail;
var is_authenticated;
var is_PHPMailer;

function mg_get_thankyou_trans_content()
{
    if (jQuery("#wp-migla_thankyou_trans_editor-wrap").hasClass("tmce-active"))
  {
         return tinyMCE.get('migla_thankyou_trans_editor').getContent();
    }else if (jQuery("#wp-migla_thankyou_trans_editor-wrap").hasClass("html-active"))
  {
        return jQuery('#migla_thankyou_trans_editor').val();
    }
}

function migla_get_editor( id )
{
    if (jQuery("#"+id+"-wrap").hasClass("tmce-active"))
    {
          return tinyMCE.activeEditor.getContent();
    }else if (jQuery("#"+id+"-wrap").hasClass("html-active"))
    {
          return jQuery("#"+id).val();
    }
}

function mg_get_thanks_email_content()
{
    if (jQuery("#wp-migla_ThanksEmail_editor-wrap").hasClass("tmce-active"))
  {
        return tinyMCE.get('migla_ThanksEmail_editor').getContent();

    }else if (jQuery("#wp-migla_ThanksEmail_editor-wrap").hasClass("html-active"))
  {
        return jQuery('#migla_ThanksEmail_editor').val();
    }
}

function mg_add_notify()
{
    jQuery("#migla-add-notify-btn").click(function(){
        var str = "";

        if( jQuery("#miglaNotifEmails").val() !== '' )
        {
            str = str + "<li><div class='col-sm-3'></div>";
            str = str + "<div class='col-sm-6 col-xs-12'>";
            str = str + "<input type='hidden' class='li-notif-email' value='";
            str = str + jQuery("#miglaNotifEmails").val() + "'>" + jQuery("#miglaNotifEmails").val() + "</div>";
            str = str + "<div class='col-sm-3 spacer'><button class='remove-notify btn rbutton'><i class='fa fa-trash'></i></button>";
            str = str + "</div>";
            str = str + "</li>";

            jQuery(str).appendTo( jQuery("#mg-list_notifies") );

            jQuery(".remove-notify").click(function(){
                jQuery(this).closest("li").remove();
            })

          var emails = [];

          jQuery(".li-notif-email").each(function(){
            emails.push( jQuery(this).val() );
          });

          console.log(emails);

          jQuery.ajax({
              type  : "post",
              url   :  miglaAdminAjax.ajaxurl,
              data  : { action     : "TotalDonationsAjax_save_email_part3",
                        email_id   : jQuery("#migla_email_id").val(),
                        form_id    : jQuery("#migla_form_id").val(),
                        notifies   : emails,
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                      },
              success: function( new_id ) {
                          jQuery("#migla_email_id").val(new_id);
                          console.log(new_id);
                        },
              error: function(xhr, status, error)
                  {
                    alert(error);
                  },
              complete: function(xhr, status, error)
                  {
                  }
          });

        }else{
        }
    })
}

function mg_remove_notify()
{
    jQuery(".remove-notify").click(function(){

        jQuery(this).closest("li").remove();
          var emails = [];

          jQuery(".li-notif-email").each(function(){
            emails.push( jQuery(this).val() );
          });

          console.log(emails);

          jQuery.ajax({
              type  : "post",
              url   :  miglaAdminAjax.ajaxurl,
              data  : { action     : "TotalDonationsAjax_save_email_part3",
                        email_id   : jQuery("#migla_email_id").val(),
                        form_id    : jQuery("#migla_form_id").val(),
                        notifies   : emails,
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                      },
              success: function( new_id ) {
                          jQuery("#migla_email_id").val(new_id);
                          console.log(new_id);
                        },
              error: function(xhr, status, error)
                  {
                    alert(error);
                  },
              complete: function(xhr, status, error)
                  {
                  }
          });
    })
}

function mg_set_email_part1()
{
        var listemails = [];

        jQuery(".li-notif-email").each(function(){
            listemails.push( jQuery(this).val() );
        });

        jQuery.ajax({
            type : 'post',
            url  :  miglaAdminAjax.ajaxurl,
            data : {  action     : 'TotalDonationsAjax_save_email_part1',
                      form_id    : jQuery('#migla_form_id').val(),
                      replyTo    :  jQuery('#miglaReplyToTxt').val(),
                      replyToName        : jQuery('#miglaReplyToNameTxt').val(),
                      is_thankyou_email  : is_enabled_thankyouemail,
                      is_honoree_email   : '0',
                      is_pdf_on          : '0',
                      is_offline_email   : '0',
                      notifies           : listemails,
                      auth_token         : jQuery('#__migla_auth_token').val(),
                      auth_owner         : jQuery('#__migla_auth_owner').val(),
                      auth_session       : jQuery('#__migla_session').val()
                  },
            success: function( resp ) {
                       jQuery('#migla_email_id').val( resp );
                      },
            error: function(xhr, status, error)
                    {

                    },
            complete: function(xhr, status, error)
                    {
                    }
        });
}

function mg_set_emails()
{
    jQuery("#migla-emailsets-btn").click(function(){
        mg_set_email_part1();
        saved("#migla-emailsets-btn");
    });

    jQuery('#migla_emailbody_btn').click(function(){

        if( jQuery("#migla_email_id").val() === '' )
        {
            var isThankEmailSet = '0';
            var emails = [];
            
            if( jQuery("#mg-isThankEMail").is(":checked") ){
                isThankEmailSet = '1';
            }

            jQuery(".li-notif-email").each(function(){
                emails.push( jQuery(this).val() );
            });

            jQuery.ajax({
                type : "post",
                url :  miglaAdminAjax.ajaxurl,
                data : { action     : "TotalDonationsAjax_save_email_part2",
                         email_id    : jQuery("#migla_email_id").val(),
                         language    : jQuery('#migla_language').val(),
                         email_type  : 'thankyou',
                         email_subject   : jQuery('#migla_thankSbj').val(),
                         email_body      : mg_get_thanks_email_content(),
                         email_repeating : "",
                         email_anonymous : jQuery('#migla_thankAnon').val(),

                         form_id        : jQuery("#migla_form_id").val(),
                         replyTo        : jQuery('#miglaReplyToTxt').val(),
                         replyToName    : jQuery('#miglaReplyToNameTxt').val(),
                         notify_emails  : emails,
                         is_thankyou_email  : isThankEmailSet,
                         is_honoree_email   : '0',
                         is_pdf_on : '0',
                         
                         auth_token : jQuery('#__migla_auth_token').val(),
                         auth_owner : jQuery('#__migla_auth_owner').val(),
                         auth_session : jQuery('#__migla_session').val()
                        },
                success: function(email_id){
                            jQuery("#migla_email_id").val(email_id);
                          },
                error: function(xhr, status, error)
                        {
                            alert(error);
                        },
                complete: function(xhr, status, error)
                        {
                            saved('#migla_emailbody_btn');
                        }
            });
          
        }else{

            jQuery.ajax({
                type : "post",
                url :  miglaAdminAjax.ajaxurl,
                data : {  action     : "TotalDonationsAjax_save_email_part2",
                          email_id    : jQuery("#migla_email_id").val(),
                          language    : jQuery('#migla_language').val(),
                          email_type  : 'thankyou',
                          email_subject   : jQuery('#migla_thankSbj').val(),
                          email_body      : mg_get_thanks_email_content(),
                          email_repeating : jQuery('#migla_thankRepeat').val(),
                          email_anonymous : jQuery('#migla_thankAnon').val(),
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
                            saved('#migla_emailbody_btn');
                        }
            });
            
        }//else
    });//'#miglaThankEmail')
}

function mg_set_thanks_page()
{
    jQuery('#migla_ThankPage_btn').click(function(){
        jQuery.ajax({
              type  : "post",
              url   :  miglaAdminAjax.ajaxurl,
              data  : { action    : "TotalDonationsAjax_translate_redirect",
                        language  : jQuery('#migla_language').val(),
                        form_id   : jQuery("#migla_form_id").val(),
                        content   : migla_get_editor('wp-migla_thankyoupage_editor'),
                        pageid    : jQuery('#migla_SetThankYouPage').val(),
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
                             saved( '#migla_ThankPage_btn' );
                          }
        });
    });

    jQuery('#miglaThankPage_inlang_btn').click(function(){
        jQuery.ajax({
          type  : "post",
          url   :  miglaAdminAjax.ajaxurl,
          data  : { action    : "TotalDonationsAjax_translate_redirect",
                    language  : jQuery('#miglaThankPage_inlang').val(),
                    content   : migla_get_editor('wp-migla_thankyou_editor2'),
                    pageid    : jQuery('#miglaSetThankYouPage2').val(),
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
          success: function(msg) {
                      saved( '#miglaThankPage_inlang_btn' );
                  },
            error: function(xhr, status, error)
                      {
                        alert(error);
                      },
            complete: function(xhr, status, error)
                      {
                      }
        });
    });

	jQuery('#miglaSetThankYouPageButton').click(function() {
        jQuery.ajax({
            type 	: "post",
            url 	:  miglaAdminAjax.ajaxurl,
            data 	: { action	: "TotalDonationsAjax_update_me",
          					key		  : 'migla_thank_you_page',
                    value	  : jQuery("#miglaSetThankYouPage").val(),
                    valtype : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
				          },
            success: function(msg) {
						saved('#miglaSetThankYouPageButton');
					},
            error	: function(xhr, status, error)
                    {
                      alert(error);
                    },
            complete: function(xhr, status, error)
                    {
                    }
        });
	});

}

function mg_test_emails()
{
    jQuery('#miglaTestEmail').click(function(){
        if( jQuery('#miglaTestEmailAdd').val() == '' ){
            alert("Please input the email address");
        }else{
            jQuery.ajax({
                type : "post",
                url :  miglaAdminAjax.ajaxurl,
                data : {  action    : "TotalDonationsAjax_test_email" ,
                          form_id   : jQuery("#migla_form_id").val(),
                          language  : jQuery("#migla_language").val(),
                          email     : jQuery('#miglaTestEmailAdd').val(),
                          auth_token : jQuery('#__migla_auth_token').val(),
                          auth_owner : jQuery('#__migla_auth_owner').val(),
                          auth_session : jQuery('#__migla_session').val()
                        },
                success: function(msg) {
                            alert(msg);
                        },
                error: function(xhr, status, error)
                          {
                            alert(error);
                          },
                complete: function(xhr, status, error)
                          {
                          }
            });
        }
    });

    jQuery('#miglaTestHEmail').click(function(){
        if( jQuery('#miglaTestHEmailAdd').val() == '' )
        {
            alert("Please input the email address");
        }else{
            jQuery.ajax({
                type  : "post",
                url   :  miglaAdminAjax.ajaxurl,
                data  : {   action    : "TotalDonationsAjax_test_hEmail" ,
                            form_id   : jQuery("#migla_form_id").val(),
                            language  : jQuery("#migla_language").val(),
                            email     : jQuery('#miglaTestHEmailAdd').val(),
                            auth_token : jQuery('#__migla_auth_token').val(),
                            auth_owner : jQuery('#__migla_auth_owner').val(),
                            auth_session : jQuery('#__migla_session').val()
                        },
                success: function(msg)
                          {
                            alert(msg);
                          },
                error: function(xhr, status, error)
                          {
                            alert(error);
                          },
                complete: function(xhr, status, error)
                          {
                          }
            });
        }
    });
}

function mg_set_emailsend_status()
{
  jQuery('.mg-status-email').on('change.bootstrapSwitch', function(e) {
    var form_id = e.target.dataset["name"];
    var is_status = '0';

    if( e.target.checked ){
        is_status = '1';
    }

    jQuery.ajax({
        type  : "post",
        url   : miglaAdminAjax.ajaxurl,
        data  : { action  : 'TotalDonationsAjax_etup_emailsent',
                    form    : form_id ,
                    value   : is_status,
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
              },
        success : function(){
                },
        error   : function(xhr, status, error){
                },
        complete: function(xhr, status, error){
                }
    })

    console.log(form_id);
  });

}

function mg_set_smtp_status()
{
    jQuery('#mg-use-PHPMailer').on('change.bootstrapSwitch', function(e) {
        if( e.target.checked ){
            is_PHPMailer = 'yes';
        }else{
            is_PHPMailer = 'no';
        }
    });

    jQuery('#mg-is-authenticated').on('change.bootstrapSwitch', function(e) {
        if( e.target.checked ){
            is_authenticated = 'yes';
        }else{
            is_authenticated = 'no';
        }
    });
}

function mg_save_smtp()
{
    jQuery('#mg-save-smtp-btn').click(function(){
        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : {  action     : "TotalDonationsAjax_save_smtp",
                      use_PHPMailer : is_PHPMailer,
                      host       : jQuery('#mg-host').val(),
                      user       : jQuery('#mg-user').val(),
                      password   : jQuery('#mg-password').val(),
                      authenticated : is_authenticated,
                      security   : jQuery('#mg-secure').val(),
                      port       : jQuery('#mg-port').val(),
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                  },
            success: function() {
                      },
            error: function(xhr, status, error)
                    {
                      alert(error);
                    },
            complete: function(xhr, status, error)
                {
                    saved('#mg-save-smtp-btn');
                }
        });
    });
}

function mg_open_preview(){
    window.setTimeout(function(){
        jQuery('#miglaFormPreviewThank').submit();
    }, 1000);    
}

jQuery(document).ready(function() {
    console.log(jQuery("#migla_page").val());

    if( jQuery("#migla_page").val() == "home" )
    {
        //mg_set_emailsend_status();

        if( jQuery('#mg-use-PHPMailer-val').val() == 'yes' ){
            is_PHPMailer = 'yes';
        }else{
            is_PHPMailer = 'no';
        }

        if( jQuery('#mg-is-authenticated-val').val() == 'yes' ){
            is_authenticated = 'yes';
        }else{
            is_authenticated = 'no';
        }

        mg_set_smtp_status();

        mg_save_smtp();
        
        jQuery('.mg-switch').change(function(){
            var is_enabled_thankyouemail = '1';
            var form_id = jQuery(this).attr('name');
            form_id = form_id.replace("form-","");
            
            if( jQuery(this).prop('checked') ){
                is_enabled_thankyouemail = '1';
            }else{
                is_enabled_thankyouemail = '0';
            }
            
            jQuery.ajax({
                type  : "post",
                url   : miglaAdminAjax.ajaxurl,
                data  : { action  : 'TotalDonationsAjax_setup_emailsent',
                            form    : form_id ,
                            value   : is_enabled_thankyouemail,
                            auth_token : jQuery('#__migla_auth_token').val(),
                            auth_owner : jQuery('#__migla_auth_owner').val(),
                            auth_session : jQuery('#__migla_session').val()
                      },
                success : function(){
                        },
                error   : function(xhr, status, error){
                        },
                complete: function(xhr, status, error){
                        }
            })            
        });        

    }else if( jQuery("#migla_page").val() == "email_receipt" )
    {
        mg_add_notify();
        mg_remove_notify();

        mg_set_emails();
        mg_test_emails();
        mg_set_thanks_page();

        if(jQuery('#mg-status-thank-email').is(':checked')){
            is_enabled_thankyouemail = '1';
        }else{
            is_enabled_thankyouemail = '0';
        }

        jQuery('#mg-status-thank-email').change(function(){
            if( jQuery(this).prop('checked') ){
                is_enabled_thankyouemail = '1';
            }else{
                is_enabled_thankyouemail = '0';
            }
        });
        
        jQuery('#miglaThankPagePrev').click(function(){
            if( jQuery("#migla_preview_id").val() == "" || isNaN(jQuery("#migla_preview_id").val()) )
            {
                jQuery.ajax({
                  type   : "post",
                  url    :  miglaAdminAjax.ajaxurl,
                  data   : { action: "TotalDonationsAjax_get_thank_you_page_url",
                              auth_token : jQuery('#__migla_auth_token').val(),
                              auth_owner : jQuery('#__migla_auth_owner').val(),
                              auth_session : jQuery('#__migla_session').val()
                            },
                  success: function( message ) {
                                var encode_message = JSON.parse(message);
                                
                                jQuery("#migla_preview_id").val( encode_message["page"] );
                                jQuery('#miglaFormPreviewThank').attr('action', encode_message["url"] ) ;
                                
                                mg_open_preview();
                            },
                    error: function(xhr, status, error)
                            {
                                alert(error);
                            },
                    complete: function(xhr, status, error)
                              {
                              }
                });
            }else{
                jQuery('#miglaFormPreviewThank').submit();
            }
        });

    }

    console.log("end");
});//document