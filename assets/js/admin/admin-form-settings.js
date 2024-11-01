var changed_fields = [];
var radioState = {};
var currencies = [];
var showDec ;
var tempid = -1;
var btnid = 0;
var ATTRIBUTES = ['uid'];
var pid ;

function mg_dragable_radio_list()
{
  jQuery("#mg_custom_list_container").sortable({
		helper		: "clone",
		revert		: true,
		forcePlaceholderSize: true,
		axis		: 'y',
		start: function (e, ui) {
		        },
		update: function (e, ui) {
		          mg_save_custom_list();
		        },
		stop: function(e, ui){
		        },
		received: function(e, ui){
		        }
   });
}

function mg_generate_rand(min, max)
{
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function mg_uid()
{
  var date = new Date();
  var day        = String( date.getDate() );
  var monthIndex = String( date.getMonth() );
  var year       = String( date.getFullYear() );
  var hours      = String( date.getHours() );
  var minutes    = String( date.getMinutes() );
  var seconds    = String( date.getSeconds() );
  var rand_value = year + monthIndex + day + hours + minutes + seconds  + "_" + String(  mg_generate_rand( 1, 1000) );
  return rand_value;
}

function mg_timestamp()
{
  var date = new Date();
  var day        = String( date.getDate() );
  var monthIndex = String( date.getMonth() );
  var year       = String( date.getFullYear() );
  var hours      = String( date.getHours() );
  var minutes    = String( date.getMinutes() );
  var seconds    = String( date.getSeconds() );
  var rand_value = year + monthIndex + day + hours + minutes + seconds;

  return rand_value;
}

function numberExample( thousand, decimal )
{
  var n = 10000; var nf = "";

  if ( jQuery('#showDecimal').text() == 'yes' ){
    showDec = 2;
  }

  jQuery('#sep1').text( thousand );
  jQuery('#sep2').text( decimal );

  nf = n.formatMoney(showDec, thousand, decimal )  ;

  jQuery('#miglanum').text(nf);
}

function mg_keypress()
{
    jQuery('.miglaNAD2').on('keypress', function (e){
         var str = jQuery(this).val();
         var separator = jQuery('#sep2').text();
         var key = String.fromCharCode(e.which);
         console.log(String.fromCharCode(e.which) + e.keycode + e.which );

         if(jQuery('#showDecimal').text() == 'yes'){
         // Allow: backspace, delete, escape, enter
         if (jQuery.inArray( e.which, [ 8, 0, 27, 13]) !== -1 ||
          jQuery.inArray( key, [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ]) !== -1 ||
          ( key == separator )
         )
         {
          if( key == separator  && ( str.indexOf(separator) >= 0 ))
          {
            e.preventDefault();
          }else{
            return;
          }
         }else{
          e.preventDefault();
         }
         }else{
         // Allow: backspace, delete, escape, enter
         if (jQuery.inArray( e.which, [ 8, 0, 27, 13]) !== -1 ||
          jQuery.inArray( key, [ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ]) !== -1
         )
         {

         }else{
          e.preventDefault();
         }
         }
    });
}

function getsymbol( mg_code )
{
    var mg_symbol = mg_code;

    mg_symbol = jQuery('#curr-'+mg_code).find('.curr-symbol').val();

    return mg_symbol;
}

function mg_save_currency()
{
    jQuery('#miglaSetCurrencyButton').click(function(){

      var id = '#' + jQuery(this).attr('id');

      jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,
          data : { action   : "TotalDonationsAjax_update_me",
                    key     : 'migla_thousandSep',
                    value   : jQuery('#thousandSep').val(),
                    valtype : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
          success: function(msg) {
                         // saved(id);
                    },
          error: function(xhr, status, error)
                    {
                      alert(error);
                    },
          complete: function(xhr, status, error)
                    {
                    }
      })  ;

      jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,
          data : { action : "TotalDonationsAjax_update_me",
                    key   : 'migla_decimalSep',
                    value : jQuery('#decimalSep').val(),
                    valtype : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
          success: function(msg) {
                         // saved(id);
                    },
          error: function(xhr, status, error)
                    {
                      alert(error);
                    },
          complete: function(xhr, status, error)
                    {
                    }
      })  ;

      jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,
          data : {  action : "TotalDonationsAjax_update_me",
                    key    : 'migla_curplacement',
                    value  : jQuery('#miglaDefaultPlacement').val(),
                    valtype : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
          success: function(msg) {
                         // saved(id);
          },
          error: function(xhr, status, error)
                    {
                      alert(error);
                    },
          complete: function(xhr, status, error)
                    {
                    }
      })  ;


      var show = "no";
      if( jQuery('#mHideDecimalCheck').is(":checked")  ) { show = "yes"; }

      jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,
          data : {  action : "TotalDonationsAjax_update_me",
                    key    : 'migla_showDecimalSep',
                    value  : show,
                    valtype : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
          success: function(msg) {
                         // saved(id);
          }
      })  ;

      jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : {  action : "TotalDonationsAjax_update_me",
                      key    : 'migla_default_currency',
                      value  : jQuery('#miglaDefaultCurrency').val(),
                      valtype : 'text',
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                  },
            success: function(msg) {
                            saved(id);
                    }
      })  ;

      jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : {  action : "TotalDonationsAjax_update_me",
                      key    : 'migla_symbol_to_show',
                      value  : jQuery('#migla_symbol_type').val(),
                      valtype : 'text',
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                  },
            success: function(msg) {
                            saved(id);
                    }
      })  ;
    });
}

function mg_save_country()
{
    jQuery('#miglaSetCountryButton').click(function() {
      var id = '#' + jQuery(this).attr('id');

      jQuery.ajax({
          type : "post",
          url : miglaAdminAjax.ajaxurl,
          data : {  action : "TotalDonationsAjax_update_me",
                    key    : 'migla_default_country',
                    value  : jQuery('select[name=miglaDefaultCountry] option:selected').text(),
                    valtype : 'text',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                 },
          success: function(msg) {
                          saved(id);
          }
      })  ;
    });
}

function mg_def_currency_change()
{
    jQuery('#miglaDefaultCurrency').change(function(){
       var placement = jQuery("#miglaDefaultPlacement").val();
       var curr_symbol = jQuery("#migla_symbol_type").val();
       var currency = jQuery("#miglaDefaultCurrency").val();

       var icon = "";

       if( curr_symbol == "3-letter-code" ){
          icon = currency;
       }else{
          icon = jQuery("#curr-"+currency).find(".curr-symbol").val();
       }

       if( placement == 'before')
       {
         jQuery('#miglabefore').html(icon);
         jQuery('#miglaafter').html("");
       }else{
         jQuery('#miglaafter').html(icon);
         jQuery('#miglabefore').html("");
       }
    });
}

function mg_def_placement_change()
{
    jQuery('#miglaDefaultPlacement').change(function(){
      var placement = jQuery("#miglaDefaultPlacement").val();
       var curr_symbol = jQuery("#migla_symbol_type").val();
       var currency = jQuery("#miglaDefaultCurrency").val();

       var icon = "";

       if( curr_symbol == "3-letter-code" ){
          icon = currency;
       }else{
          icon = jQuery("#curr-"+currency).find(".curr-symbol").val();
       }

       if( placement == 'before')
       {
         jQuery('#miglabefore').html(icon);
         jQuery('#miglaafter').html("");
       }else{
         jQuery('#miglaafter').html(icon);
         jQuery('#miglabefore').html("");
       }

    });
}

function migla_symbol_type()
{
    jQuery('#migla_symbol_type').change(function(){
       var placement = jQuery("#miglaDefaultPlacement").val();
       var curr_symbol = jQuery("#migla_symbol_type").val();
       var currency = jQuery("#miglaDefaultCurrency").val();

       var icon = "";

       if( curr_symbol == "3-letter-code" ){
          icon = currency;
       }else{
          icon = jQuery("#curr-"+currency).find(".curr-symbol").val();
       }

       if( placement == 'before')
       {
         jQuery('#miglabefore').html(icon);
         jQuery('#miglaafter').html("");
       }else{
         jQuery('#miglaafter').html(icon);
         jQuery('#miglabefore').html("");
       }

    });
}

function mg_min_amount()
{
    jQuery('#mg-set-min-amount-btn').click(function(){
         jQuery.ajax({
            type : "post",
            url  :  miglaAdminAjax.ajaxurl,
            data :  {  action   : "TotalDonationsAjax_update_me",
                        key       : 'migla_min_amount' ,
                        value     : jQuery('#mg-min-amount').val(),
                        valtype   : 'text',
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                    },
            success: function() {

                    },
            error : function(xhr, status, error){
                        console.log(error);
                    },
            complete : function(xhr, status, error){
                            saved('#mg-set-min-amount-btn');
                        }
        });
    });

}

jQuery(document).ready(function(){

    Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator)
        {
            var n = this,
              decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
              decSeparator = decSeparator == undefined ? "." : decSeparator,
              thouSeparator = thouSeparator == undefined ? "," : thouSeparator,
              sign = n < 0 ? "-" : "",
              i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
              j = (j = i.length) > 3 ? j % 3 : 0;
            var result = sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1"
                + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
            return result;
        };

    jQuery.fn.once = function(a, b) {
      return this.each(function() {
          jQuery(this).off(a).on(a,b);
      });
    };

  if( jQuery("#mg_page").val() == "home" )
  {
    console.log("Home");
    showDec = 0;
    if ( jQuery('#showDecimal').text() == 'yes' ){
      showDec = 2;
    }

    jQuery('#thousandSep').val( jQuery('#sep1').text() );
    jQuery('#decimalSep').val( jQuery('#sep2').text() );

    //Save Def Currency button #miglaSetCurrencyButton
    mg_save_currency();

    //Save Def Country button #miglaSetCountryButton
    mg_save_country();

    //If currency CHanges
    mg_def_currency_change();
    migla_symbol_type();
    mg_def_placement_change();

    mg_min_amount();
    mg_keypress();

  }

  console.log("end");
}); //ON READY
