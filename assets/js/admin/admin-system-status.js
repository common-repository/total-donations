jQuery(document).ready(function(){

    jQuery('#migla_check_ssl').click(function(){

      jQuery.ajax({
        type   : 'post',
        url    :  miglaAdminAjax.ajaxurl,
        data   : { action: 'TotalDonationsAjax_check_SSL_out'  },
        success: function( test )
                    {
                       var test_json = JSON.parse(test);
                       var str = '';
                       var key_1 = '';
                       for( key_1 in test_json ){
                         str = str + key_1 + ':' + test_json[key_1] + '<br>';
                       }

                       jQuery('#migla_curl_ssl').text(str);
              },
        error: function(xhr, status, error)
                  {
                    alert(error);
                  },
        complete: function(xhr, status, error)
                  {
                  }
      })  ; //ajax

   });

    jQuery('.mg_export_log').click(function(){
           var filename = jQuery('#mg_filename_' + jQuery(this).attr('name') ).val() + '.txt' ;
           var log_text = jQuery('#mg_error_log_div_' + jQuery(this).attr('name') ).html();

          var csvData = 'data:text/plain;charset=utf-8,' + encodeURIComponent(log_text );
           jQuery(this)
                .attr({
                'download': filename ,
                 'href': csvData,
                'target': '_blank'
            });
    });

    jQuery('.mg_empty_error_log').click(function(){

    jQuery.ajax({
      type   : 'post',
      url    :  miglaAdminAjax.ajaxurl,
      data   : {  action      : 'TotalDonationsAjax_clear_log',
                  file_name   : jQuery('#mg_filename_' + jQuery(this).attr('name') ).val()
                },
      success: function( test )
                {
                },
      error: function(xhr, status, error)
                {
                  alert(error);
                },
      complete: function(xhr, status, error)
                {
                  location.reload();
                }

     })  ; //ajax

    });

    jQuery('.mg_empty_this_log').click(function(){

        jQuery.ajax({
            type   : 'post',
            url    :  miglaAdminAjax.ajaxurl,
            data   : {  action : "TotalDonationsAjax_clear_log",
                        file   : jQuery(this).attr('name'),
                        auth_token   : jQuery('#__migla_auth_token').val(),
                        auth_owner   : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                    },
        success: function( test )
                {
                },
        error: function(xhr, status, error)
                {
                  alert(error);
                },
        complete: function(xhr, status, error)
                {
                  location.reload();
                }

        })  ; //ajax

    });

    jQuery(".mg-li-tab").click(function(){
        jQuery(".mg-li-tab").removeClass("active");
        jQuery(this).addClass("active");
    });
});
