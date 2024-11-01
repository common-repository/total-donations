function mg_tab_drag()
{
    jQuery('#default_payment_section').find("ul.containers").sortable({
      placeholder: "ui-state-highlight-container",
      revert: true,
      forcePlaceholderSize: true,
      axis: 'y',
      update: function (e, ui) {
      },
      start: function (e, ui) {
      }
    }).bind('sortstop', function (event, ui) {
    });

    function SetSortableRows(rows)
    {
        rows.sortable({
            placeholder: "ui-state-highlight-row",
            connectWith: "ul.rows:not(.containers)",
            containment: "ul.containers",
            helper: "clone",
            revert: true,
            forcePlaceholderSize: true,
            axis: 'y',
            start: function (e, ui) {
            },
            update: function (e, ui) {
            },
            stop: function(e, ui){
            },
            received: function(e, ui){
            }
        }).bind('sortstop', function (event, ui) {

        });
    }

    SetSortableRows(jQuery("ul.rows"));

}

function migla_gateways_order()
{
    jQuery('#migla-update-gateways-ord-btn').click(function(){

        var gateways = [];

        jQuery('li.formfield').each(function(){
            var temp = [];
            var key    = jQuery(this).find('.mg_status_gateways').val();

            if( jQuery(this).find('.mg_status_gateways').is(':checked') )
            {
                temp = [ key, true ];
            }else{
                temp = [ key, false ];
            }
            gateways.push( temp );
        });

        console.log(gateways);

        jQuery.ajax({
          type : "post",
          url  :  miglaAdminAjax.ajaxurl,
          data : {  action : "TotalDonationsAjax_update_me",
                    key    : 'migla_gateways_order',
                    value  : gateways,
                    valtype : 'array',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                },
          success: function(msg) {
                        saved('#migla-update-gateways-ord-btn');
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

function mg_security_options()
{
  jQuery('#migla_security_save').click(function(){


      jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,
        data : {  action  : 'TotalDonationsAjax_update_me',
                  key   : 'migla_avs_level',
                  value : jQuery("input[name='migla_credit_card_AVS_levels']:checked").val(),
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

      var cc_avs = 'no';
      if( jQuery('#migla_credit_card_avs').is(':checked') ) cc_avs = 'yes';

      jQuery.ajax({
        type : "post",
        url  : miglaAdminAjax.ajaxurl,
        data : {  action : 'TotalDonationsAjax_update_me',
                  key    : 'migla_credit_card_avs',
                  value  : cc_avs,
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


      saved('#migla_security_save');
  })

}

function mg_user_access()
{
    jQuery('#migla-save-users').click(function(){
        var allowed_users = [];

        jQuery('.mg_li_user').each(function(){

            var temp = {};
            temp.userid = jQuery(this).find('.userid').val();

            jQuery(this).find('.urole').each(function(){
                if(jQuery(this).is(':checked')){
                    temp.role = jQuery(this).val();
                }
            });

            allowed_users.push( temp );
        });

        console.log(allowed_users);

        jQuery.ajax({
            type : "post",
            url : miglaAdminAjax.ajaxurl,
            data : { action    : "TotalDonationsAjax_update_user_access",
                    td_users   : allowed_users,
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                },
          success : function(m) {

                },
          error : function(xhr, status, error)
                    {
                      alert(error);
                    },
          complete: function(xhr, status, error)
                    {
                    }
        });

        saved('#migla-save-users');
    });
}


function mg_loading(loader)
{
    setTimeout( function(){
                jQuery(loader).fadeTo(1000, 0.1);
                jQuery(loader+"-overlay").removeClass("hideme");
              },
              750
    );
}

function mg_unload(loader)
{
    setTimeout( function(){
                jQuery(loader+"-overlay").addClass("hideme");
                jQuery(loader).fadeTo(1000, 1);
              },
              750
    );
}

jQuery(document).ready(function(){

  mg_tab_drag();

  jQuery('#migla_credit_card_avs').click(function(){
       if ( jQuery(this).is(':checked') )
       {
           jQuery('#migla_div_avs_level').show();
       }else{
           jQuery('#migla_div_avs_level').hide();
       }
  });

  jQuery('#migla_use_captcha').click(function(){
      if( jQuery(this).is(':checked') )
         jQuery('.mg_captcha_keys').show();
      else
         jQuery('.mg_captcha_keys').hide();
  });

  migla_gateways_order();
  mg_security_options();
  mg_user_access();

  console.log('end');
})
