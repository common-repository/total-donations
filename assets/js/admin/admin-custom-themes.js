function mg_init()
{
    jQuery('.mg-color-field').change(function(){
        var parent    = jQuery(this).closest('div.row');
        jQuery(parent).find('.currentColor').css('background-color', jQuery(this).val() );
    });

      jQuery('.meffects').click(function(){
              var id = jQuery(this).attr('id');
              if( id == "inlineCheckbox1" ){
                jQuery('div.progress').toggleClass("striped");
              }
              if( id == "inlineCheckbox2" ){
                jQuery('div.progress').toggleClass("mg_pulse");
              }
              if( id == "inlineCheckbox3" ){
                jQuery('div.progress').toggleClass("animated-striped");
                jQuery('div.progress').toggleClass("active");
              }
              if( id == "inlineCheckbox4" ){
                jQuery('div.progress').toggleClass("mg_percentage");
              }
      });

      jQuery('.spinner-up').on('click', function() {
        var _parent = jQuery(this).closest('.input-group');

        _parent.find('.spinner-input').val(  parseInt ( _parent.find('.spinner-input').val(), 10) + 1);

        console.log("up");
      });

      jQuery('.spinner-down').on('click', function() {
        var _id = jQuery(this).attr("id");
        _id = _id.replace("spinner-down", "");
        var _parent = jQuery();

        var countNum = parseInt( jQuery('#'+_id).val(), 10);
        
        if( countNum > 0 ){
          countNum = countNum - 1;
        }

        jQuery('#'+_id).val(countNum);

        console.log("down");
      });

}

function mg_hexToRGB(h) {
  let r = 0, g = 0, b = 0;

  // 3 digits
  if (h.length == 4) {
    r = "0x" + h[1] + h[1];
    g = "0x" + h[2] + h[2];
    b = "0x" + h[3] + h[3];

  // 6 digits
  } else if (h.length == 7) {
    r = "0x" + h[1] + h[2];
    g = "0x" + h[3] + h[4];
    b = "0x" + h[5] + h[6];
  }
  
  return "rgb("+ +r + "," + +g + "," + +b + ")";
}

function mg_minicolor()
{
    jQuery('.mg-color-field').each( function() {
      var options = {
          change: function(event, ui){
                    var theColor = ui.color.toString();
                    var myID = jQuery(this).attr("id");

                    if( myID == "migla_barcolor" ){
                      jQuery("#div2previewbar").css("background-color", theColor);  
                    }else if(myID == "migla_wellcolor"){
                      jQuery("#divprogressbar").css("background-color", theColor); 
                    }else if(myID = "migla_wellshadow"){
                      var theStyle = theColor + "";
                      
                    }
                  }//change
        };

      jQuery(this).wpColorPicker(options);
    });
}

function mg_save_form_theme()
{
    jQuery('#migla_save_form').click(function(){

        var ColorCode1 = jQuery('#migla_backgroundcolor').val() + ',1';
        var ColorCode2 = jQuery('#migla_panelborder').val() + ",1," + jQuery('#migla_widthpanelborder').val();
        var ColorCode3 = jQuery('#migla_bglevelcolor').val() ;
        var ColorCode4  = jQuery('#migla_borderlevelcolor').val() ;

        var BorderlevelWidthSpinner = jQuery('#migla_Widthborderlevelcolor').val();
        var ColorCode5 = jQuery('#migla_bglevelcoloractive').val() ;
        var ColorCode6 = jQuery('#migla_tabcolor').val() ;

        jQuery.ajax({
            type : "post",
            url :miglaAdminAjax.ajaxurl,
            data : {    action  : 'TotalDonationsAjax_update_form_theme',
                        backgroundcolor : ColorCode1,
                        panelborder     : ColorCode2,
                        bglevelcolor    : ColorCode3,
                        borderlevelcolor: ColorCode4,

                        borderlevelWidth: BorderlevelWidthSpinner,

                        bglevelcoloractive: ColorCode5,
                        tabcolor : ColorCode6,

                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                    },
            success: function(msg) {
                      console.log(msg);
                    },
            error: function(xhr, status, error)
                    {
                      alert(error);
                    },
            complete: function(xhr, status, error)
                    {
                      saved('#migla_save_form');
                    }
          });
    });
}

function mg_save_progressBar_theme()
{
    jQuery('#migla_save_bar').click(function(){

        var border = "";
        border = border + jQuery('#mg_WBRtop-left').val() + ",";
        border = border + jQuery('#migla_WRBtopright').val() + ",";
        border = border + jQuery('#migla_radiusbottomleft').val() + ",";
        border = border + jQuery('#migla_radiusbottomright').val();

        var well = "";
        well = jQuery('#migla_wellshadow').val() + ",1,";
        well = well + jQuery('#migla_hshadow').val() + ",";
        well = well + jQuery('#migla_vshadow').val() + ",";
        well = well + jQuery('#migla_blur').val() + ",";
        well = well + jQuery('#migla_spread').val();

        var ColorCode = jQuery('#migla_barcolor').val() + ',1';
        var ColorCode2 = jQuery('#migla_wellcolor').val() + ',1';

        var barEffects = [];
        barEffects[0] = 'no';
        barEffects[1]   = 'no';
        barEffects[2] = 'no';
        barEffects[3] = 'no';

        if( jQuery("#inlineCheckbox1").is(":checked") ){
          barEffects[0] = 'yes';  
        }

        if( jQuery("#inlineCheckbox2").is(":checked") ){
          barEffects[1] = 'yes';  
        }

        if( jQuery("#inlineCheckbox3").is(":checked") ){
          barEffects[2] = 'yes';  
        }

        if( jQuery("#inlineCheckbox4").is(":checked") ){
          barEffects[3] = 'yes';  
        }

        jQuery.ajax({
            type : "post",
            url :miglaAdminAjax.ajaxurl,
            data : {    action  : "TotalDonationsAjax_update_progressBar_theme",
                        borderRadius   : border,
                        wellboxshadow  : well,
                        progbar_info   : jQuery('#migla_progressbar_text').val(),
                        bar_color      : ColorCode,
                        progressbar_background : ColorCode2,
                        styleEffects : barEffects,
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                    },
               success: function(msg) {
                    },
              error: function(xhr, status, error){

                    },
              complete: function(xhr, status, error){
                        saved('#migla_save_bar');
                      }
        });
    });
}

function mg_save_circle_settings()
{
  jQuery('#migla_save_circle_settings').click(function(e){
      var circle_arr = {};

      circle_arr.size        = Number( jQuery('#migla_circle_size').val() );
      circle_arr.start_angle = Number( jQuery('#migla_circle_start_angle').val() );
      circle_arr.thickness   = Number( jQuery('#migla_circle_thickness').val() );
      circle_arr.inner_font_size   = Number( jQuery('#migla_circle_inner_font_size').val() );

      if( circle_arr.size  > 300 ){ circle_arr.size  = 300; }
      if( circle_arr.size  < 1 ){ circle_arr.size  = 1; }

      if( circle_arr.thickness  > 300 ){ circle_arr.thickness  = 300; }
      if( circle_arr.thickness  < 1 ){ circle_arr.thickness  = 1; }

      if( jQuery('#migla_circle_reverse').is(':checked') ){
        circle_arr.reverse = 'yes';
      }else{
        circle_arr.reverse = 'no';
      }

      circle_arr.line_cap = jQuery('#migla_circle_line_cap').val();
      circle_arr.fill     = jQuery('#migla_circle_fill').val() ;

      circle_arr.animation = jQuery('#migla_circle_animation').val();
      circle_arr.inside = jQuery('#migla_circle_inside').val();

     // alert( JSON.stringify(circle_arr) );
      var Array_Circle = [];
      Array_Circle.push(circle_arr);

    jQuery.ajax({
        type  : "post",
        url   : miglaAdminAjax.ajaxurl, 
        data  : { action  : "TotalDonationsAjax_update_me", 
                  key     : 'migla_circle_settings', 
                  value   : Array_Circle,
                  valtype : 'array',
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

    var textalign = jQuery('input[name=mg_circle-text-align]:checked').val() ;  
  
    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl, 
      data : {  action    : "TotalDonationsAjax_update_circle_layout", 
                circle_textalign : textalign,
                circle_text1     : jQuery('#migla_circle_text1').val(),
                circle_text2     : jQuery('#migla_circle_text2').val(),
                circle_text3     : jQuery('#migla_circle_text3').val(),

                auth_token : jQuery('#__migla_auth_token').val(),
                auth_owner : jQuery('#__migla_auth_owner').val(),
                auth_session : jQuery('#__migla_session').val()
              },
      success: function(msg) {
              },
      error: function(xhr, status, error){

              },
      complete: function(xhr, status, error){
                  
                }
    });

    saved('#migla_save_circle_settings');
    
  });
}

function migla_restore()
{
  jQuery("#migla_restore-btn").click(function(){
    jQuery.ajax({
      type : "post",
      url : miglaAdminAjax.ajaxurl, 
      data : {  action    : "TotalDonationsAjax_reset_" + jQuery("#migla_restore-caller").val(), 
                auth_token : jQuery('#__migla_auth_token').val(),
                auth_owner : jQuery('#__migla_auth_owner').val(),
                auth_session : jQuery('#__migla_session').val()
              },
      success: function(msg) {
              },
      error: function(xhr, status, error){

              },
      complete: function(xhr, status, error){
                  location.reload();
                }
    });
  })
}

jQuery(document).ready(function(){
  mg_init();
  mg_save_form_theme();
  mg_save_progressBar_theme();
  mg_minicolor();
  mg_save_circle_settings();  

  jQuery('[data-toggle="modal"]').on('click', function(e) {
      var jQuerytarget = jQuery(e.target);
      var targetSource = jQuerytarget.data('source');
      jQuery("#migla_restore-caller").val(targetSource);
  });

  jQuery('#confirm-reset').on('show.bs.modal', function(e) {
      migla_restore();
  });  

}); //End of document
