var del;
var updatedList = [];
var changed_fields = [];
var radioState = {};
var currencies = [];
var showDec ;
var tempid = -1;
var btnid = 0;
var new_form_id = '';
var decSep = '.';
var thouSep = ',';
var showSep = 'no';
var radioState2 = [];

function countAll()
{
  var c = 0;

  jQuery('li.formfield').each(function(){
      c = c + 1;
  });

  return c;
}

function mg_update_order()
{
    var cmp_list = [];

    jQuery(".formfield_campaign").each(function(){
        cmp_list.push(jQuery(this).find(".cmp_id").val());
    });

    jQuery.ajax({
        type  : "post",
        url   :  miglaAdminAjax.ajaxurl,
        data  : {   action: "TotalDonationsAjax_campaign_sort",
                    campaign_list : cmp_list,
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                   },
        success: function()
                    {
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

function mg_campaign_draggable()
{
    jQuery(".mg_campaign_list").sortable({
        placeholder: ".formfield li",
        helper    : "clone",
        revert    : true,
        forcePlaceholderSize: true,
        axis    : 'y',
        start: function (e, ui) {         
                },
        update: function (e, ui) {
                },
        stop: function(e, ui){
                  if( ui.item.attr("data-showed") == "yes"){
                    ui.item.find(".mg_shown_cmp").attr("checked", true);
                  }else{
                    ui.item.find(".mg_hide_cmp").attr("checked", true);                    
                  }
                },
        received: function(e, ui){
                }
    }).bind('sortstop', function (event, ui) {
        mg_update_order();
    });
}

function mg_add_campaign()
{
    jQuery('#miglaAddCampaign').click(function(){
        var name    = change_to_html( jQuery('#mName').val() );
        var target  = jQuery('#mAmount').val();
        var form_id = '';
        var cmp_id = '';

        if( jQuery.trim(name) === ''  )
        {
            alert('Please fill in the campaign name');
            canceled( '#miglaAddCampaign' );
        }else{

            var list = mg_get_campaignStructure();

            jQuery.ajax({
                type  : "post",
                url   :  miglaAdminAjax.ajaxurl,
                data  : {   action: "TotalDonationsAjax_new_campaign",
                            cmp_name : name ,
                            cmp_target : target,
                            campaign_list  : list,
                            auth_token : jQuery('#__migla_auth_token').val(),
                            auth_owner : jQuery('#__migla_auth_owner').val(),
                            auth_session : jQuery('#__migla_session').val()
                        },
                  success: function( respnewcmp )
                          {
                            var message = respnewcmp.split(",");
                            form_id = message[0];
                            cmp_id = message[1];

                            var line_str = mg_add_campaign_lines( name , target, form_id, cmp_id );

                            if( countAll() < 0 ){
                                jQuery('ul.mg_campaign_list').empty();
                            }

                            jQuery(line_str).prependTo( jQuery('ul.mg_campaign_list') ); 

                            jQuery(".mg_shown_cmp").click(function(){
                              if( jQuery(this).is(":checked") ){
                                jQuery(this).closest(".formfield").attr("data-showed", "yes");
                              }else{
                                jQuery(this).closest(".formfield").attr("data-showed", "no");
                              }
                            });

                            jQuery(".mg_hide_cmp").click(function(){
                              if( jQuery(this).is(":checked") ){
                                jQuery(this).closest(".formfield").attr("data-showed", "no");
                              }else{
                                jQuery(this).closest(".formfield").attr("data-showed", "yes");
                              }
                            });                                

                          },
                  error: function(xhr, status, error)
                          {
                              console.log( error );
                          },
                  complete : function(xhr, status, error)
                            {
                              
                            }
            }); //outer ajax

            saved('#miglaAddCampaign');           

       }//trim

    });//clicked
}

function mg_save_campaign()
{
    jQuery("#miglaSaveCampaign").click(function(){
        var changeList = [];

        jQuery(".formfield_campaign").each(function(){
            var temp = {};
            temp.cmp_id     = jQuery(this).find(".cmp_id").val();
            temp.cmp_name   = jQuery(this).find(".cmp_label").val();
            temp.cmp_target = jQuery(this).find(".cmp_target").val();
            temp.cmp_shown  = jQuery(this).find(".cmp_shown").val();

            temp.new_name   = jQuery(this).find(".labelChange").val();
            temp.new_target = jQuery(this).find(".targetChange").val();

            var _status = '1';

            jQuery(this).find(".statusShow").each(function(){
                if(jQuery(this).is(":checked")){
                    _status = jQuery(this).val();
                }
            })

            temp.new_shown = _status;

            changeList.push(temp);
        });

        console.log(changeList);

        var list = mg_get_campaignStructure();

        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : {  action          : "TotalDonationsAjax_update_campaigns",
                      changed_values  : changeList,
                      campaign_list   : list,
                      auth_token      : jQuery('#__migla_auth_token').val(),
                      auth_owner      : jQuery('#__migla_auth_owner').val(),
                      auth_session    : jQuery('#__migla_session').val()
                    },
            success: function(resp){
                        console.log(resp);
                      },
            error : function(xhr, status, error){
                        console.log(error);
                    },
            complete : function(xhr, status, error){
                        saved("#miglaSaveCampaign");
                    }
        });
    });
}

function mg_remove_campaign()
{
    jQuery('.removeCampaignField').click( function(){

        console.log('click');

        jQuery('.removeCampaignField').each(function(){
          jQuery(this).removeClass('disabled');
        });

        jQuery('.mg_campaign_list').find('li.formfield').each(function(){
          jQuery(this).removeClass('cmp_will_delete');
        });

        var myParent = jQuery(this).closest('li');

        myParent.addClass("cmp_will_delete");
    });

      jQuery('#confirm-delete').on('show.bs.modal', function(e) {
        jQuery('.btn-danger').show();

        var will_be_deleted = jQuery('.mg_campaign_list')
                                .find('li.cmp_will_delete')
                                .find("input[name='id']").val();

        jQuery('#mg_cmp_del_id').val(will_be_deleted);

      });

      jQuery('.mg_campaign_remove_cancel').click( function(e) {
      });

      jQuery('#mg_campaign_remove').click(function(){
          console.log("deletion confirm");

          var del_cmp = jQuery('.cmp_will_delete');
          var cmpID = jQuery('#mg_cmp_del_id').val();
          var list = mg_get_campaignStructure();

          jQuery.ajax({
                  type  : "post",
                  url   :  miglaAdminAjax.ajaxurl,
                  data  : {
                            action        : "TotalDonationsAjax_remove_campaign",
                            cmp_id        : cmpID,
                            campaign_list : list,
                            auth_token    : jQuery('#__migla_auth_token').val(),
                            auth_owner    : jQuery('#__migla_auth_owner').val(),
                            auth_session  : jQuery('#__migla_session').val()
                        },
                  success: function(msg) {
                          },
                  error: function(xhr, status, error)
                            {
                              console.log(error);
                            },
                  complete: function(xhr, status, error)
                            {
                            }
            });

            del_cmp.remove();
            jQuery(".mg_campaign_remove_cancel").trigger('click');
      });
}

function mg_get_campaignStructure()
{
   var fields = [];
   var i = 0;
   
    jQuery('li.formfield_campaign').each(function(){
        fields.push(jQuery(this).find(".cmp_id").val());
    });

    console.log(fields);
    return fields;
}

function mg_add_campaign_lines( label, target, form_id, id )
{

   var newComer = "";
   if( target === '' ){
    target = 0;
   }

   var admin_url = jQuery('#mg_page_admin').val() + '&form';
   admin_url = admin_url + "=" + form_id + "&cmp=" + id;

   var lbl = change_to_html( label );

    newComer = newComer + "<li class='ui-state-default formfield clearfix formfield_campaign' data-showed='yes'>";
    newComer = newComer + "<input type='hidden' name='oldlabel' value='"+lbl+"' />";
    newComer = newComer + "<input type='hidden' class='cmp_label' name='label' value='"+lbl+"' />";
    newComer = newComer + "<input type='hidden' class='cmp_target' name='target' value='"+target+"' />";
    newComer = newComer + "<input type='hidden' class='cmp_shown' name='show'  value='1' />";
    newComer = newComer + "<input type='hidden' class='cmp_formid' name='form_id' value='"+form_id+"' />";
    newComer = newComer + "<input type='hidden' class='cmp_id' name='id' value='"+id+"' />";
    newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Campaign</label></div>";
    newComer = newComer + "<div class='col-sm-2 col-xs-12'><input type='text' class='labelChange' name='' placeholder='";
    newComer = newComer + lbl + "' value='" + lbl + "' /></div>";

    newComer = newComer + "<div class='col-sm-1 hidden-xs'><label  class='control-label'>Target</label></div>";
    newComer = newComer + "<div class='col-sm-2 col-xs-12'><input type='text' class='targetChange miglaNAD' name='' placeholder='";
    newComer = newComer + target + "' value='" + target + "' /></div>";


    newComer = newComer + "<div class='col-sm-2 col-xs-12'>";
    newComer = newComer + '<input type="text" value="[totaldonations_progressbar id=&#39;2&#39;]" class="mg_label-shortcode">';
    newComer = newComer + "</div>";

    newComer = newComer + "<div class='col-sm-2 col-xs-12 row'>";
    newComer = newComer + '<input type="text" value="[totaldonations_circlebar id=&#39;2&#39;]" class="mg_label-shortcode">';
    newComer = newComer + "</div>";

     var c = countAll();
     c = c + 1;
     newComer = newComer + "<div class='control-radio-sortable col-sm-2 col-xs-12'>";
     newComer = newComer + "<span><label><input type='radio' name='r-"+c+"' value='1' checked='checked' class='statusShow mg_shown_cmp'> Show </label></span>";
     newComer = newComer + "<span><label><input type='radio' name='r-"+c+"' value='-1' class='statusShow mg_hide_cmp'> Deactivate </label></span>";

     newComer = newComer + "<span><button class='removeCampaignField' data-toggle='modal' data-target='#confirm-delete'><i class='fa fa-fw fa-trash'></i></button></span>";
     newComer = newComer + "</div>";

     newComer = newComer + "</li>";

   return newComer;
}

/*****
FORM SETTINGS 
***************/
function mg_restore_form()
{
    jQuery("#miglaRestore").click(function(){

        console.log("restoring");

        jQuery("#miglaRestore").prop("disabled", true);
        jQuery("#mg-restore-cancel").prop("disabled", true);

        jQuery.ajax({
            type : "post",
            url  :  miglaAdminAjax.ajaxurl,
            data :  { action  : "TotalDonationsAjax_restore_form",
                      form_id : jQuery("#mg_form_id").val(),
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                    },
            success: function() {
                    },
            error : function(xhr, status, error){
                        console.log(error);
                        jQuery("#miglaRestore").prop("disabled", false);
                        jQuery("#mg-restore-cancel").prop("disabled", false);

                    },
            complete : function(xhr, status, error){
                            location.reload();
                       }
        });


    });
}

/**Amount**/
function mg_save_amount_list()
{
 		var amount_levels_array = [];

		jQuery('.mg_amount_level').each(function(){
		      var amount_levels = {};
			    var aValue          = jQuery(this).find('.mg_amount_level_value').val();
				  var aPerkValue    = change_to_html( jQuery(this).find('.mg_amount_level_perk').html() );
				  amount_levels.amount = aValue ;
				  amount_levels.perk   = aPerkValue ;
				  amount_levels_array.push(amount_levels);
		});

    var changeList = [];
    var temp = {};

    temp.table = "form";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = "amounts";
    temp.val = amount_levels_array;
    temp.coltype = "%s";
    temp.valtype = "array";

    changeList.push(temp);

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,
        data : {  action : "TotalDonationsAjax_update_mulval_CForm",
                  list   : changeList,
                  auth_token : jQuery('#__migla_auth_token').val(),
                  auth_owner : jQuery('#__migla_auth_owner').val(),
                  auth_session : jQuery('#__migla_session').val()
                },
        success: function(){
                  },
        error : function(xhr, status, error){
                    console.log(error);
                },
        complete : function(xhr, status, error){
                }
    });
}

function mg_amount_settings()
{
  jQuery('#mg_amount_settings').click(function(){

        var label       = jQuery('#mg-undesignated-default').val();
        var replaceSTR  = /'/g;
        var label_saved = label.replace(replaceSTR, "[q]");

        var hideUndesignatedval = 'no';

        if ( jQuery('#mHideUndesignatedCheck').is(":checked") ){
          hideUndesignatedval = 'yes';
        }

        var showbarVal = 'yes';

        if( jQuery('#mHideProgressBarCheck').is(':checked') ) {
          showbarVal = 'no';
        }

        var campaign_that_show = [];

        jQuery('.mg_showedcampaign_group').each(function(){
            jQuery(this).find('.mg_c_isListed').val('1');
            campaign_that_show.push(  jQuery(this).find('.mg_c_id').val() );
        });

        console.log(campaign_that_show);

        jQuery.ajax({
            type : "post",
            url  :  miglaAdminAjax.ajaxurl,
            data :  {   action                : "TotalDonationsAjax_update_formopt",
                        undesignated_default  : label_saved,
                        hideUndesignated      : hideUndesignatedval,
                        showbar               : showbarVal,
                        showCampaign          : campaign_that_show,
                        language : jQuery("#mg_current_language").val(),
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                    },
            success: function(msg) {
               jQuery('#mg_oldUnLabel').val(label);
            }
        }); //ajax

        var changeList = [];
        var temp = {};

        temp.table = "form_meta";
        temp.form_id = jQuery("#mg_form_id").val();
        temp.key = "warning_1";
        temp.val = jQuery('#mg-errorgeneral-default').val();
        temp.valtype = "text";
        temp.language = jQuery("#mg_current_language").val();

        changeList.push(temp);

        temp = {};
        temp.table = "form_meta";
        temp.form_id = jQuery("#mg_form_id").val();
        temp.key = "warning_2";
        temp.val = jQuery('#mg-erroremail-default').val();
        temp.valtype = "text";
        temp.language = jQuery("#mg_current_language").val();

        changeList.push(temp);

        temp = {};
        temp.table = "form_meta";
        temp.form_id = jQuery("#mg_form_id").val();
        temp.key = "warning_3";
        temp.val = jQuery('#mg-erroramount-default').val();
        temp.valtype = "text";
        temp.language = jQuery("#mg_current_language").val();

        changeList.push(temp);

        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : {  action : "TotalDonationsAjax_update_mulval_CForm",
                      list   : changeList,
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                    },
            success: function(){
                      },
            error : function(xhr, status, error){
                        console.log(error);
                    },
            complete : function(xhr, status, error){
                    }
        });

    var val_custom_amount = 'no';

    if ( jQuery('#mHideHideCustomCheck').is(":checked") ){
        val_custom_amount = 'yes';
    }

    var changeList = [];
    var temp = {};

    temp.table = "form";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = "hideCustomAmount";
    temp.val = val_custom_amount ;
    temp.coltype = "%s";
    temp.valtype = "text";

    changeList.push(temp);

    temp = {};
    temp.table = "form_meta";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = 'custom_amount_text';
    temp.val = jQuery('#mg_custom_amount_text').val();
    temp.language = jQuery("#mg_current_language").val();
    temp.valtype = "text";

    changeList.push(temp);

    temp = {};
    temp.table = "form";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = "amountBoxType";
    temp.val = jQuery('#migla_amount_box_type').val();
    temp.coltype = "%s";
    temp.valtype = "text";

    changeList.push(temp);

    temp = {};
    temp.table = "form";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = "buttonType";
    temp.val = jQuery('#mg_amount_btn_type').val() ;
    temp.coltype = "%s";
    temp.valtype = "text";

    changeList.push(temp);

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,
        data : {  action : "TotalDonationsAjax_update_mulval_CForm",
                  list   : changeList,
                  auth_token : jQuery('#__migla_auth_token').val(),
                  auth_owner : jQuery('#__migla_auth_owner').val(),
                  auth_session : jQuery('#__migla_session').val()
                },
        success: function(){
                  },
        error : function(xhr, status, error){
                    console.log(error);
                },
        complete : function(xhr, status, error){
                }
    });

    saved('#mg_amount_settings');
  });
}

function mg_add_amount_level()
{
	jQuery('#miglaAddAmountButton').click(function()
	{
		var newVal   = jQuery('#miglaAddAmount').val();
		var newPerk  = change_to_html( jQuery('#miglaAmountPerk').val() );

		var amount_newline = "<p class='mg_amount_level'>";
		amount_newline = amount_newline + "<input class='mg_amount_level_value' type=hidden value='"+newVal+"' />";
		amount_newline = amount_newline + "<label>"+newVal+"</label>";
		amount_newline = amount_newline + "<label class='mg_amount_level_perk'>"+newPerk+"</label>";
		amount_newline = amount_newline + "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
		amount_newline = amount_newline + "</p>";

		if( newVal == '' || Number(newVal) <= 0.0 )
		{
		    alert('no empty or zero amounts are allowed');
		}else{

		  jQuery(amount_newline).prependTo( jQuery('#miglaAmountTable') );

 		  var amount_levels_array = [];

  		jQuery('.mg_amount_level').each(function(){
  		    var amount_levels = {};
  		    
    			var aValue           = jQuery(this).find('.mg_amount_level_value').val();
    			var aPerkValue       = change_to_html( jQuery(this).find('.mg_amount_level_perk').html() );
    			amount_levels.amount = aValue ;
    			amount_levels.perk   = aPerkValue;
    			
  			 amount_levels_array.push(amount_levels);
  		});

		    console.log(amount_levels_array);

      jQuery.ajax({
                    type : "post",
                    url :  miglaAdminAjax.ajaxurl,
                    data : {  action : "TotalDonationsAjax_update_amountlevel_Form",
                              valuelist   : amount_levels_array,
                              auth_token : jQuery('#__migla_auth_token').val(),
                              auth_owner : jQuery('#__migla_auth_owner').val(),
                              auth_session : jQuery('#__migla_session').val()
                            },
                    success: function(){
                                console.log(resp);  
                                console.log("HERE");    
                            },
                    error : function(xhr, status, error){
                                console.log(error);
                            },
                    complete : function(xhr, status, error){
                                  mg_remove_amount_level();
                                  console.log("DONE"); 
                              }
      });

		}
      mg_remove_amount_level();
	    saved('#miglaAddAmountButton');
	});
}

function mg_draw_level( key, amount )
{
   var str = '';
   var decimal = jQuery('#sep2').text();
   str = str + "<p id='amount"+key+"'>";
   str = str + "<input class='value' type=hidden id='"+ amount +"' value='"+ amount +"' />";
   str = str + "<label>" + amount.replace(".", decimal ) + "</label>";
   str = str + "<button name='miglaAmounts' class='miglaRemoveLevel obutton'><i class='fa fa-times'></i></button>";
   str = str + "</p>";
  return str;
}

function mg_remove_amount_level()
{
	jQuery('.miglaRemoveLevel').click(function(){
		  var parent = jQuery(this).closest('p.mg_amount_level');
		  parent.remove();

		  mg_save_amount_list();

		  if( jQuery('p.mg_amount_level').length < 1 ){
			 jQuery('#warningEmptyAmounts').show();
		  }else{
			 jQuery('#warningEmptyAmounts').hide();
		  }
   });
}

function mg_level_dragged()
{
  jQuery("#miglaAmountTable").sortable({
		helper		: "clone",
		revert		: true,
		forcePlaceholderSize: true,
		axis		  : 'y',
		start: function (e, ui) {
		        },
		update: function (e, ui) {
			         mg_save_amount_list();
		        },
		stop: function(e, ui){
		        },
		received: function(e, ui){
		        }
   });
}//leveldrag

function mg_save_amount_list()
{
      var amount_levels_array = [];

      jQuery('.mg_amount_level').each(function(){
        var amount_levels = {};
          
        var aValue           = jQuery(this).find('.mg_amount_level_value').val();
        var aPerkValue       = change_to_html( jQuery(this).find('.mg_amount_level_perk').html() );
        
        amount_levels.amount = aValue ;
        amount_levels.perk   = aPerkValue;
          
        amount_levels_array.push(amount_levels);
      });

        console.log(amount_levels_array);

      jQuery.ajax({
          type : "post",
          url :  miglaAdminAjax.ajaxurl,
          data : {  action : "TotalDonationsAjax_update_amountlevel_Form",
                              valuelist   : amount_levels_array,
                              auth_token : jQuery('#__migla_auth_token').val(),
                              auth_owner : jQuery('#__migla_auth_owner').val(),
                              auth_session : jQuery('#__migla_session').val()
                            },
          success: function(){
                                console.log(resp);  
                                console.log("HERE");    
                            },
          error : function(xhr, status, error){
                                console.log(error);
                            },
          complete : function(xhr, status, error){
                                  mg_remove_amount_level();
                                  console.log("DONE"); 
                              }
      });
}

function mg_edit_multivalues()
{
   jQuery(".edit_select_value").click(function(){

        var parent = jQuery(this).closest('li.formfield');
        var recId  = "mgval_" + parent.find("input[name='uid']").val();

        jQuery("#mg_id_custom_values_edit").text("");
        jQuery("#mg_id_custom_values_edit").text(recId);
        jQuery('#mg_multival_modal').modal('show');

        console.log(recId);
    });
}

function mg_add_uid_to_modal()
{

    jQuery('[data-toggle="modal"]').on('click', function(e){
        console.log("data-toggle click");

        var jQuerytarget = jQuery(e.target);
        var modalSelector = jQuerytarget.data('target');

        console.log('test'+jQuerytarget.data("myuid"));
        jQuery("#mg_addval_uid").val(jQuerytarget.data("myuid"));
    });

}

function mg_modal_show()
{
    jQuery('#mg_multival_modal').on('show.bs.modal', function(e){
        jQuery('#mg_custom_list_container').empty();
        jQuery("#mg-multival-spinner").show();
        jQuery('#mg_multival_modal-overlay').removeClass('show');

        mg_save_multival();
        console.log('show modal');
    });
}

function mg_modal_shown()
{
    jQuery('#mg_multival_modal').on('shown.bs.modal', function(e){
        var myuid = "#" + jQuery("#mg_addval_uid").val();
        var content = '';

        if( jQuery(myuid).length > 0 && jQuery(myuid).val() !== '' )
        {
            var _list = jQuery(myuid).val() ;
            var _listArray = _list.split(";");

            for( _key in _listArray ){
                if( _listArray[_key] !== '' && (_listArray[_key]).indexOf("::") !== -1 ){
                    var _listpair = (_listArray[_key]).split("::");
                    var listKey = _listpair[0];
                    var listValue = _listpair[1];
                    
                     listKey =  listKey.replace("'", "&#39;");
                     listValue =  listValue.replace("'", "&#39;");

                     listKey =  listKey.replace('"', "&#34;");
                     listValue =  listValue.replace('"', "&#34;");
                    
                    content = content + mg_write_list( listKey, listValue );
                }
            }//for each val in list
        }

        if( content !== '' ){
            jQuery('#mg_custom_list_container').html('');
            jQuery( content ).appendTo( '#mg_custom_list_container' ) ;
        }

        jQuery("#mg-multival-spinner").hide();

        mg_save_multival();
        mg_delete_custom_list();
    })
}

function mg_save_multival()
{
    jQuery('#miglaAddCustomValues').click(function(){
        jQuery("#mg-loading").removeClass("hideme");

        mg_save_custom_list();

        setTimeout( function(){
            jQuery('#mg_multival_modal').find('.close').trigger('click');
            },
            750
        );
    });
}

function change_to_html(inSTR)
{
    var str = inSTR;
    str = str.replace("'", "&#39;");
    str = str.replace('"', "&#34;");
    
    return str;
}

function mg_write_list(inVal, inLbl)
{
    var content = "";
    
    inLbl = change_to_html(inLbl);
    inVal = change_to_html(inVal);
    
    content = content + "<div class='mg_custom_list_row'>";

    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Value</label></div>";
    content = content + "<div class='col-sm-6'><input type='text'  value='" + inVal;
    content = content + "' class='mg_customlist_val form-control'></div>";
    content = content + "<div class='col-sm-3'></div>";
    content = content + "</div>";

    content = content + "<div class='form-group mg_custom_list'><div class='col-sm-3'><label class='control-label' for=''>Label</label></div>";
    content = content + "<div class='col-sm-6'><input type='text'  value='" + inLbl;
    content = content + "' class='form-control touch-bottom mg_customlist_lbl'></div>";
    content = content + "<div class='col-sm-3'><button class='mg_customlist_remove btn obutton alignleft'><i class='fa fa-fw fa-trash'></i> </button></div>";
    content = content + "</div>";

    content = content + "</div>";

    return content;
}

function mg_delete_custom_list()
{
   jQuery('.mg_customlist_remove').bind( "click", function() {
       var par = jQuery(this).closest(".mg_custom_list_row");
       par.remove();

       //save to databse
	   mg_save_custom_list();

       //save temp saved
       mg_temp_mutilval();
   });
}

function mg_temp_mutilval()
{
    var tempVal = "";
    var myuid = "#" + jQuery("#mg_addval_uid").val();

    jQuery('.mg_custom_list_row').each(function(){
        if( tempVal !== "" ) tempVal = tempVal + ";";

        tempVal = tempVal + jQuery(this).find('.mg_customlist_val').val();
        tempVal = tempVal + "::" + jQuery(this).find('.mg_customlist_lbl').val();
    });

    jQuery(myuid).val('');
    jQuery(myuid).val(tempVal);
}

function mg_save_custom_list()
{
    jQuery('#mg_multival_modal-overlay').addClass('show');

    var myDataList = [];
    var myuid = "#" + jQuery("#mg_addval_uid").val();

    if( jQuery('.mg_custom_list_row').length > 0  ){

        var myDataObj = {};
        var listKey = "";
        var listValue = "";

        jQuery('.mg_custom_list_row').each(function(){
            myDataObj = {};

            listKey = jQuery(this).find('.mg_customlist_val').val();
            listValue = jQuery(this).find('.mg_customlist_lbl').val();

            listKey = listKey.replace("'", "&#39;");
            listValue = listValue.replace('"', "&#34;");
            
            myDataObj.lVal = listKey;
            myDataObj.lLbl = listValue;

            myDataList.push(myDataObj);
        });

        jQuery.ajax({
            type : "post",
            url  :  miglaAdminAjax.ajaxurl,
            data : {    action : "TotalDonationsAjax_update_multival",
                        listVal   : myDataList,
                        formid    : jQuery("#mg_form_id").val(),
                        language  : jQuery("#mg_current_language").val(),
                        uid       : myuid,
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                    },
            success: function() {
                        },
            complete: function(xhr, status, err){
                            jQuery('#mg_multival_modal-overlay').removeClass('show');
                        }
        });

    }else{

        jQuery.ajax({
            type : "post",
            url :  miglaAdminAjax.ajaxurl,
            data : { action : "TotalDonationsAjax_update_multival",
                     listVal   : myDataList,
                     formid    : jQuery("#mg_form_id").val(),
                     language  : jQuery("#mg_current_language").val(),
                     uid       : myuid,
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                    },
            success: function() {
                    },
            complete: function(xhr, status, err){
                        jQuery('#mg_multival_modal-overlay').removeClass('show');
                    }
        });

    }

    mg_temp_mutilval();
}

function mg_add_mutilval()
{
    jQuery('#miglaAddCustomValueForm').click(function(){

        jQuery("#mg-multival-spinner").hide();

        if( jQuery( '#mg_custom_list_container' ).find('.mg_custom_list_row'.length) <= 0 )
        {
            jQuery( '#mg_custom_list_container' ).empty();
        };

        var content = mg_write_list( jQuery('#mg_add_value').val(), jQuery('#mg_add_label').val() );

        jQuery( content ).appendTo( '#mg_custom_list_container' ) ;

        mg_delete_custom_list();

        jQuery('#mg_add_value').val('');
        jQuery('#mg_add_label').val('');

        //change temp saved
        mg_temp_mutilval();
    });
}

function mg_multival_drag()
{
  jQuery("#mg_custom_list_container").sortable({
		helper		: "clone",
		revert		: true,
		forcePlaceholderSize: true,
		axis		: 'y',
		start: function (e, ui) {
		        },
		update: function (e, ui) {
		            //save to databse
		            mg_save_custom_list();

		            //save temp saved
		            mg_temp_mutilval();
		        },
		stop: function(e, ui){
		        },
		received: function(e, ui){
		            }
   });
}

function mg_form_settings()
{
  jQuery('#migla_form_settings_btn').click(function(){


    var changeList = [];
    var temp = {};

    temp.table = "form_meta";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = "warning_1";
    temp.val = change_to_html( jQuery('#mg-errorgeneral-default').val() );
    temp.valtype = "text";
    temp.language = jQuery("#mg_current_language").val();

    changeList.push(temp);

    temp = {};
    temp.table = "form_meta";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = "warning_2";
    temp.val = change_to_html( jQuery('#mg-erroremail-default').val() );
    temp.valtype = "text";
    temp.language = jQuery("#mg_current_language").val();

    changeList.push(temp);

    temp = {};
    temp.table = "form_meta";
    temp.form_id = jQuery("#mg_form_id").val();
    temp.key = "warning_3";
    temp.val = change_to_html( jQuery('#mg-erroramount-default').val() );
    temp.valtype = "text";
    temp.language = jQuery("#mg_current_language").val();

    changeList.push(temp);

    jQuery.ajax({
        type : "post",
        url :  miglaAdminAjax.ajaxurl,
        data : {  action : "TotalDonationsAjax_update_mulval_CForm",
                  list   : changeList,
                  auth_token : jQuery('#__migla_auth_token').val(),
                  auth_owner : jQuery('#__migla_auth_owner').val(),
                  auth_session : jQuery('#__migla_session').val()
                },
        success: function(){
                  },
        error : function(xhr, status, error){
                    console.log(error);
                },
        complete : function(xhr, status, error){
                }
    });

    saved('#migla_form_settings_btn');

  })
}

function mg_add_form_field()
{
   var currentRow ;
   var currentRowid;

   jQuery('.mAddField').click(function(){

    parent = jQuery(this).closest('.formheader'); //group header
    currentRow = parent.find('ul.rows'); //check the children list

    if( jQuery('body').find('li.justAdded').length > 0 )
    {
    }else{

        mg_disable_formfield();

        var tempid = tempid + 1;
        var parent ;

        var temp = {};
        temp = mg_write_new_field( tempid );

        var newlist = temp.string;

        var newuid = temp.uid;

        jQuery(newlist).prependTo( currentRow );

        if( !parent.find('.mDeleteGroup').hasClass('disabled') ) {
          parent.find('.mDeleteGroup').addClass('disabled');
        }

        jQuery('.cancelAddField').click(function(){

            mg_enable_formfield();

            jQuery('li.formheader').each(function(){
              var currow = jQuery(this).find('ul.rows');

              currow.find('li.justAdded').each(function(){
                jQuery(this).fadeOut('slow').remove()
              });

              tempid = -1;

              if( currow.children('li').length > 0 ){
                parent.find('.mDeleteGroup').removeClass('disabled');
              }

            });

        });

    }//if valid

    jQuery('#saveNewField').click(function(){
        var me = jQuery(this);

        me.data( 'oldtext', me.html() );
        me.text('Saving...'); jQuery("<i class='fa fa-fw fa-spinner fa-spin'></i>" ).prependTo( me );

        var curFormField = jQuery(this).closest('li.justAdded'); // formfield
        var newLabel = curFormField.find('.labelChange');
        var newList = [];

        var isValid = true;
        var BreakException= {};

        try {
           //alert( findDuplicateLabel(  newLabel.val() ) );
           if( newLabel.val() == '' || findDuplicateLabel(  newLabel.val() ) > 1 ){
                isValid = false; throw BreakException;
           }
        } catch(e) {
           if (e!==BreakException) throw e;
        }


        if( isValid )
        {
            jQuery('li.justAdded').each(function(){
                var x = jQuery(this).find("input.labelChange").val();
                n = x.replace(" ","");
                x = n.replace("'","");

               jQuery(this).find(".old_id").val(x);

                jQuery(this).find("input[type=radio]").each(function(){
                    jQuery(this).attr('name', (x+'st') );
                });

                var new_type = jQuery(this).find('.typeChange').val();

                jQuery(this).find(".old_type").val( new_type );

                jQuery(".edit_select_value").click(function(e){
                    e.preventDefault();

                    var parent = jQuery(this).closest('li.formfield');
                    var recId  = "mgval_" + parent.find(".old_uid").val();

                    jQuery("#mg_id_custom_values_edit").text("");
                    jQuery("#mg_id_custom_values_edit").text(recId);
                    jQuery('#mg_add_values').modal('show');

                });

                //Editable/
                if( new_type == 'select' || new_type == 'searchable_select' || new_type == 'radio' || new_type == 'multiplecheckbox' )
                {
                    jQuery(this).find('.edit_select_value').show();
                }else{
                    jQuery(this).find('.edit_select_value').hide();
                }

                mg_edit_multivalues();
                mg_add_uid_to_modal();
                mg_modal_show();
                mg_modal_shown();
                mg_field_type_change();
                
                jQuery(this).removeClass('justAdded');

            });

            jQuery.ajax({
                    type : "post",
                    url : miglaAdminAjax.ajaxurl,
                    data : {  action  : "TotalDonationsAjax_update_formfields",
                              form_id : jQuery("#mg_form_id").val(),
                              key     : 'structure',
                              val     : getFormStructure(),
                              coltype : '%s',
                              type    : 'array',
                              auth_token : jQuery('#__migla_auth_token').val(),
                              auth_owner : jQuery('#__migla_auth_owner').val(),
                              auth_session : jQuery('#__migla_session').val()
                          },
                    success: function(msg) {
                               saved("#saveNewField");
                               jQuery('body').find('.rowsavenewcomer').remove();
                               mg_removeField();

                              },
                    error: function(xhr, status, error)
                            {
                              saved("#saveNewField");
                               jQuery('body').find('.rowsavenewcomer').remove();
                               mg_removeField();

                            },
                    complete: function(xhr, status, error)
                            {
                              saved("#saveNewField");
                            }
            })  ;

        }else{
              alert("No empty values please or duplicate label !");
              canceledLoser( "#saveNewField", "<i class='fa fa-fw fa-save'></i> Save field");
        }

    });

   });//AddNewField
}

function mg_removeField()
{
    jQuery('.removeField').click( function(){

        var parent =  jQuery(this).closest('li');
        var group =  parent.closest('ul.rows');
        group = group.closest('li.formheader');

        //alert(parent.attr('class'));
        if( parent.find("input[name='code']").val() === 'miglad_' )
        {
            alert("You can not remove default field !");
            return false;
        }else{
            var type_ = parent.find("input[name='type']").val();

            if( type_ === 'select' || type_ === 'radio' || type_ === 'multiplecheckbox' )
            {
               var recId  = "mgval_" + parent.find("input[name='uid']").val();

               jQuery.ajax({
                   type : "post",
                   url : miglaAdminAjax.ajaxurl,
                   data : { action: "TotalDonationsAjax_delete_postmeta",
                            key : recId ,
                            id : jQuery('#migla_custom_values_id').val(),
                            auth_token : jQuery('#__migla_auth_token').val(),
                            auth_owner : jQuery('#__migla_auth_owner').val(),
                            auth_session : jQuery('#__migla_session').val()
                          },
                   success: function(msg) {
                   }, asycn : true
               })  ;
            }

            jQuery(this).closest('li').remove();

            jQuery.ajax({
                type : "post",
                url : miglaAdminAjax.ajaxurl,
                data : {  action  : "TotalDonationsAjax_update_formfields",
                          form_id : jQuery("#mg_form_id").val(),
                          key     : 'structure',
                          val     : getFormStructure(),
                          coltype : '%s',
                          type    : 'array',
                          auth_token : jQuery('#__migla_auth_token').val(),
                            auth_owner : jQuery('#__migla_auth_owner').val(),
                            auth_session : jQuery('#__migla_session').val()
                        },
                success: function(msg) {
                            var count = calcChildren( group );

                            if( Number(count) < 1  ){
                              group.find('.mDeleteGroup').removeClass('disabled');
                            }
                        }
            });
        }
     });
}

function mg_add_group()
{
    jQuery('.mAddGroup').click(function() {
        jQuery('#divAddGroup').toggle();
    });

    jQuery('#cancelAddGroup').click(function() {
        jQuery('#divAddGroup').toggle();
        jQuery('#labelNewGroup').val('');
    });

    jQuery('#saveAddGroup').click(function(){

      jQuery('#divAddGroup').toggle();  

        var title = jQuery('#labelNewGroup').val();

        var title_with_code  = title.replace("'", "/'");
        var ulid = "";
        var newG = "";
        var idGroup = Number(jQuery('ul.containers').children('li').length) + 1;

        var isValid = true;

        if( isValid )
        {

            newG = newG + "<li class='title formheader ui-sortable-handle formheader-new' id='newAddedGroup'>";
            newG = newG + "<div class='row'>";
            newG = newG + "<div class='col-sm-4'>";
            newG = newG + "<div class='row'>";
            newG = newG + "<div class='col-sm-2'> <i class='fa fa-bars bar-icon-styling'></i></div>";
            newG = newG + "<div class='col-sm-10'> ";
            newG = newG + '<input type="text" class="miglaNQ mg_title"  placeholder="'+title_with_code+'" name="grouptitle" value="'+title_with_code+'">';
            newG = newG + "</div>";
            newG = newG + "</div></div>";
    
            newG = newG + "<div class='col-sm-4'>";
            newG = newG + "<div class='col-sm-5'>";
    
            if( jQuery('#toggleNewGroup').is(':checked') ){
                newG = newG + "<input type='checkbox' id='t" + idGroup + "' class='toggle' checked='checked' /><label>Toggle</label>";
            }else{
                newG = newG + "<input type='checkbox' id='t" + idGroup + "' class='toggle' /><label>Toggle</label>";
            }
    
            newG = newG + "</div>";
            newG = newG + "<button value='add' class='btn btn-info obutton mAddField addfield-button-control' style='display:none'>";
            newG = newG + "<i class='fa fa-fw fa-plus-square-o'></i>Add Field</button>";
            newG = newG + "</div>";
    
            newG = newG + "<div class='col-sm-4 text-right-sm text-center-xs divDelGroup'>";
            newG = newG + "<button value='add' class='rbutton btn btn-danger mDeleteGroup pull-right'>";
            newG = newG + "<i class='fa fa-fw fa-trash'></i>Delete Group</button>";
            newG = newG + "</div>";
    
            newG = newG + "</div>";
    
            newG = newG + "<input type='hidden' name='title' value='"+title+"' />";
            newG = newG +"<input type='hidden' name='child' value='NULL' />";
            newG = newG +"<input type='hidden' name='parent_id' value='NULL' />";
            newG = newG +"<input type='hidden' name='depth' value='0' />";
    
            ulid = title.replace(" ", "");
            newG = newG + "<ul class='rows' id='"+ulid+"'>";
    
            newG = newG + "</ul>";
            newG = newG + "</li>";
    
            jQuery(newG).prependTo( jQuery('ul.containers') );
    
            jQuery('#labelNewGroup').val('');
    
            mg_add_form_field();
            mg_delete_group();
    
            jQuery('.titleChange').each(function(){
                var first_text = jQuery(this).val();
                jQuery(this).val( first_text.replace("/'", "'") );
            });

            var fielddata = getFormStructure();

            jQuery.ajax({
               type : "post",
               url : miglaAdminAjax.ajaxurl,
               data : { action: "TotalDonationsAjax_update_newformgroup",
                    		form_id : jQuery("#mg_form_id").val(),
                    		key     : 'structure',
                    		val     : fielddata,
                    		coltype : '%s',
                    		type    : 'array',
                    		newpos  : 0,
                        auth_token : jQuery('#__migla_auth_token').val(),
                        auth_owner : jQuery('#__migla_auth_owner').val(),
                        auth_session : jQuery('#__migla_session').val()
                      },
                success: function(msg) {                          
                            jQuery('.mAddField').show();
                            saved('#saveAddGroup');
                        },
                error : function( xhr, status, error){
                            console.log(error);
                        },
                complete : function( xhr, status, error){
                        }
            });
    
            jQuery(".formheader").each(function(){
                jQuery(this).removeAttr('id');
            });
    
            mg_drag_sections();
        }else{
            alert(jQuery("#trans-DuplicateAlert").val());
            canceled('#saveAddGroup');
        }
    });
}

function mg_delete_group()
{
	jQuery('.mDeleteGroup').click(function(){

	  var parent = jQuery(this).closest('.formheader');

	  if( parent.find('li.formfield').length > 0 ){
			 alert(jQuery("#trans-GroupRemove").val());

    }else{

			parent.remove();

      jQuery.ajax({
          type : "post",
          url : miglaAdminAjax.ajaxurl,
          data : {  action  : "TotalDonationsAjax_update_formfields",
                    form_id : jQuery("#mg_form_id").val(),
                    key     : 'structure',
                    val     : getFormStructure(),
                    coltype : '%s',
                    type    : 'array',
                    auth_token : jQuery('#__migla_auth_token').val(),
                    auth_owner : jQuery('#__migla_auth_owner').val(),
                    auth_session : jQuery('#__migla_session').val()
                  },
          success: function(msg) {
                      var count = calcChildren( group );

                      if( Number(count) < 1  ){
                              group.find('.mDeleteGroup').removeClass('disabled');
                      }
                    }
      });
	  }
	});
}

function mg_save_form()
{
    jQuery('.miglaSaveForm').click(function() {

        var formStruct = getFormStructure();
        var id = '#' + jQuery(this).attr('id');

        if(  isFieldValid() )
        {
            console.log(formStruct);

            jQuery.ajax({
                  type : "post",
                  url  : miglaAdminAjax.ajaxurl,
                  data : {  action  : "TotalDonationsAjax_update_formfields",
                      		  form_id : jQuery("#mg_form_id").val(),
                      		  key     : "structure",
                      		  val     : formStruct,
                      		  coltype : "%s",
                      		  type    : "array",
                              auth_token : jQuery('#__migla_auth_token').val(),
                              auth_owner : jQuery('#__migla_auth_owner').val(),
                              auth_session : jQuery('#__migla_session').val()
                        },
                  success: function( resp )
                          {
                              console.log(JSON.parse(resp));
                          },
                  error : function( xhr, status, error){
                            console.log(error);
                          },
                  complete : function( xhr, status, error){

                          },
            });

            saved(id);
        }else{
          alert("No empty values or duplicate values");
          canceled(id);
        }
    });
}

function mg_drag_sections()
{
    jQuery("ul.containers").sortable({
        placeholder : "ui-state-highlight-container",
        revert      : true,
        forcePlaceholderSize: true,
        axis        : 'y',
        update: function (e, ui) {
                    console.log("updated");
                },
        start: function (e, ui) {
                }
    }).bind('sortstop', function (event, ui) {
        jQuery("ul.rows").find('input[type="radio"]').each(function() {
            if(  radioState[ jQuery(this).attr('name') ] === jQuery(this).val() )
            {
                jQuery(this).prop('checked', true);
            }
        });
    });

    mg_SetSortableRows(jQuery("ul.rows"));

    function mg_SetSortableRows(rows)
    {
        rows.sortable({
            placeholder : "ui-state-highlight-row",
            connectWith : "ul.rows:not(.containers)",
            containment : "ul.containers",
            helper      : "clone",
            revert      : true,
            forcePlaceholderSize : true,
            axis        : 'y',
            start : function (e, ui) {
                jQuery(this).find('input[type="radio"]').each(function() {
                  if( jQuery(this).is(':checked') ){
                    radioState[ jQuery(this).attr('name') ] = jQuery(this).val();
                  }
                });
            },
            update: function (e, ui) {
                    },
            stop: function(e, ui){
                    },
            received: function(e, ui){
                    }
        }).bind('sortstop', function (event, ui) {
            jQuery(this).find('input[type="radio"]').each(function() {
                if(  radioState[ jQuery(this).attr('name') ] === jQuery(this).val() )
                {
                    jQuery(this).prop('checked', true);
                }
            });
        });
    }
}

function getFormStructure()
{
    var fields = [];
    changed_fields.length = 0;

    jQuery('li.formheader').each(function(){
        var group     = {};
        var thisTitle = "";
  
        if(jQuery(this).hasClass("formheader-new")){
            thisTitle = jQuery(this).find(".mg_title").val();
        }else{
            thisTitle = jQuery(this).find(".titleChange").val();
        }
    
        group.title = thisTitle.replace("'","[q]");

        if ( jQuery(this).find(".toggle").is( ":checked" ) )
        {
            group.toggle = '1';
        }else{
            group.toggle = '0';
        }

        var leaf = -1;
        var children = [];
        var i = 0;
        
        if(jQuery(this).find('li.formfield').length > 0)
        {
          jQuery(this).find('li.formfield').each(function() {
            var child = {};
            var changed = [];
            leaf = leaf + 1;
    
            var lbl = jQuery(this).find(".labelChange").val();
            
            lbl = change_to_html( lbl );
            
            child.label = lbl.replace("'","[q]");
    
            child.code = jQuery(this).find(".old_code").val();
    
            child.id =  jQuery(this).find(".old_id").val();
    
            if( child.code == 'miglac_' )
            {
                var new_id = lbl.replace("'","[q]");
                new_id     = new_id.replace(" ", "");
    
                var old_id = jQuery(this).find(".old_id").val();
    
                if( new_id != old_id )
                {
                    changed[0] = old_id;
                    changed[1] = new_id;
                    changed_fields.push( changed );
                    jQuery(this).find(".old_id").val(new_id);
                    child.id =  new_id;
                }
            }
    
            child.type = jQuery(this).find(".typeChange").val();
    
            var status = "1";
    
            jQuery(this).find("input[type=radio]").each(function(){
               if( jQuery(this).is(':checked') ){
                 status = jQuery(this).val();
               }
            });
    
            child.status = status;
    
            child.uid = jQuery(this).find(".old_uid").val();
    
            if( (child.code == 'miglad_') && (child.status == '2') ){
                child.status = '3'
            }
    
            children.push(child);
          });
                
        }

        group.depth = leaf;
        group.child = children;

        fields.push(group);
   });
   
   console.log(JSON.stringify(fields));
   
   return fields;
}

function mg_sync_fielddata()
{
    jQuery('li.formheader').each(function(){
        jQuery(this).find('li.formfield').each(function() {
            jQuery(this).find('.old_type').val( jQuery(this).find(".typeChange").val() );
        })
    })
}

function mg_load_sync_data()
{
    jQuery('li.formheader').each(function(){
        jQuery(this).find('li.formfield').each(function(){
            jQuery(this).find(".typeChange").val( jQuery(this).find('.old_type').val() );
        });
    })
}

function mg_field_type_change()
{
  jQuery('.typeChange').click(function(e){

    var p = jQuery(this).closest('li.formfield');
    var type_val = jQuery(this).val() ;
    console.log(type_val);

    p.find("input[name='type']").val(type_val);

    if( type_val    == 'select'
        || type_val == 'radio'
        || type_val == 'multiplecheckbox'
        || type_val == 'searchable_select'
    )
    {
        p.find(".edit_select_value").show();
    }else{
        p.find(".edit_select_value").hide();
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

function mg_generate_uid_field(){
  var date = new Date();
  var day        = String( date.getDate() );
  var monthIndex = String( date.getMonth() );
  var year       = String( date.getFullYear() );
  var hours      = String( date.getHours() );
  var minutes    = String( date.getMinutes() );
  var seconds    = String( date.getSeconds() );

  var dd = "f" + year + monthIndex + day + hours + minutes + seconds  + "_" + String(  mg_generate_rand( 1, 1000) );

  return dd;
}

function countAll()
{
  var c = -1;
  jQuery('li.formfield').each(function(){ c = c + 1; });
  return c;
}

function calcChildren( group )
{
  var count = 0;
  group.find('li.formfield').each(function() {
    count = count + 1;
  });
  return count;
}

function mg_disable_formfield()
{
}

function mg_enable_formfield()
{
}

function findDuplicateTitle( checkvalue )
{
    var trimVal = checkvalue.replace("'", "[q]");
    return jQuery(".mHiddenTitle[value='" + trimVal + "']").length ;
}

function findDuplicateLabel( checkvalue ){
    var trimVal = checkvalue.replace("'", "[q]");
    return jQuery(".mHiddenLabel[value='" + trimVal + "']").length ;
}

function isFieldValid()
{
    var isValid = true;
    var BreakException= {};

    try {
        jQuery('li.formheader').each(function(){
            var row = jQuery(this).find('ul.rows');

            row.find('li.formfield').each(function(){
              var label = jQuery(this).find('.labelChange').val();

              if( label == '' || findDuplicateLabel(label) > 1 ){
                isValid = false; throw BreakException;
              }
            });
        });
    } catch(e) {
        if (e!==BreakException) throw e;
    }

    return isValid;
}

function mg_write_new_field( tempid )
{
   var newComer = "";
   var random_uid =  mg_generate_uid_field();

   newComer = newComer + "<li class='ui-state-default formfield clearfix justAdded'>";

   newComer = newComer + "<input class='mHiddenLabel' type='hidden' value='' />";
   newComer = newComer + "<input class='old_type' type='hidden' value='text' />";
   newComer = newComer + "<input class='old_id' type='hidden' value='' />";
   newComer = newComer + "<input class='old_code' type='hidden' value='miglac_' />";
   newComer = newComer + "<input class='old_status' type='hidden' value='1' />";
   newComer = newComer + "<input class='old_uid' type='hidden' value='" + random_uid + "' />";
   newComer = newComer + "<input type='hidden' id='" + random_uid + "' />";

   newComer = newComer + "<div class='clabel col-sm-1 hidden-xs'><label class='control-label'>Label:</label></div>";
   newComer = newComer + "<div class='col-sm-3 col-xs-12'><input type='text' class='labelChange' placeholder='";
   newComer = newComer + "' value='' /></div>";

   newComer = newComer + "<div class='ctype col-sm-2 col-xs-12'>";

   newComer = newComer + "<select class='typeChange'>";

   newComer = newComer + "<option value='text'>text</option>";
   newComer = newComer + "<option value='checkbox'>checkbox</option>";
   newComer = newComer + "<option value='textarea'>textarea</option>";
   newComer = newComer + "<option value='select'>select</option>";
   newComer = newComer + "<option value='radio'>radio</option>";
   newComer = newComer + "<option value='multiplecheckbox'>multiple checkbox</option>";

   newComer = newComer + "</select>";

   newComer = newComer + "</div>";

    newComer = newComer + "<div class='col-sm-2 col-xs-12'><button class='mbutton edit_select_value' data-toggle='modal' target='#mg_multival_modal' data-myuid='"+random_uid+"' style='display:none;'>Enter Values</button></div>";

   newComer = newComer + "<div class='ccode' style='display:none'>miglac_</div>";

   newComer = newComer + "<div class='control-radio-sortable col-sm-4 col-xs-12'>";

   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='1' checked >"+jQuery('#trans-Show').val()+"</label></span>";
   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='0' >"+jQuery('#trans-Hide').val()+"</label></span>";
   newComer = newComer + "<span><label><input type='radio' name='r"+tempid+"'  value='2' >"+jQuery('#trans-Mandatory').val()+"</label></span>";
   newComer = newComer + "<span><button class='removeField'> <i class='fa fa-fw fa-trash'></i></button></span></div>";


   newComer = newComer + "<div class='row rowsavenewcomer'>";
   newComer = newComer + "<div class='addButton col-sm-12 '>";
   newComer = newComer + "<button id='' class='btn btn-default mbutton cancelAddField' type='button'>"+jQuery('#trans-Cancel').val()+"</button>";
   newComer = newComer + "<button id='saveNewField' class='btn btn-info pbutton AddNewComer' type='button'>";
   newComer = newComer + "<i class='fa fa-fw fa-save'></i>"+jQuery('#trans-SaveField').val()+"</button>";
   newComer = newComer + "</div>";
   newComer = newComer + "</div>";

   newComer = newComer + "</li>";

    var resp = {};

    resp.string = newComer;
    resp.uid = random_uid;

   return resp;
}

/********************************************************************/

function mg_campaign_chooser()
{
  jQuery('.mg_add').click(function(){
      jQuery('.mg_add_all_campaign_chooser').prop("checked",false);

      var items = jQuery("#mg_all_campaign_list input:checked:not('.mg_add_all_campaign_chooser')");

      var n = items.length;

    	if (n > 0) {
            items.each(function(idx,item){
                var choice = jQuery(item);

                choice.prop("checked",false);
      		    choice.parent().addClass('mg_showedcampaign_group');
                choice.parent().appendTo("#mg_showed_campaign_list");
            });
    	}
      else {
    		alert( jQuery('#mg_add_warning').val() );
      }
  });

    jQuery('.mg_remove').click(function(){
      jQuery('.mg_remove_all_campaign_chooser').prop("checked",false);

      var items = jQuery("#mg_showed_campaign_list input:checked:not('.mg_remove_all_campaign_chooser')");

  	    items.each(function(idx,item){
            var choice = jQuery(item);
            choice.prop("checked",false);
  	        choice.parent().removeClass('mg_showedcampaign_group');
            choice.parent().appendTo("#mg_all_campaign_list");
        });
    });

  /* toggle all checkboxes in group */
  jQuery('.mg_add_all_campaign_chooser').click(function(e){

  	e.stopPropagation();
  	var $this = jQuery(this);
      if($this.is(":checked")) {
      	$this.parents('.list-group').find("[type=checkbox]").prop("checked",true);
      }
      else {
      	$this.parents('.list-group').find("[type=checkbox]").prop("checked",false);
          $this.prop("checked",false);
      }
  });

  jQuery('.mg_remove_all_campaign_chooser').click(function(e){

  	e.stopPropagation();
  	var $this = jQuery(this);
      if($this.is(":checked")) {
      	$this.parents('.list-group').find("[type=checkbox]").prop("checked",true);
      }
      else {
      	$this.parents('.list-group').find("[type=checkbox]").prop("checked",false);
          $this.prop("checked",false);
      }
  });

  jQuery('[type=checkbox]').click(function(e){
    e.stopPropagation();
  });

  /* toggle checkbox when list group item is clicked */
  jQuery('.list-group a').click(function(e){

      e.stopPropagation();

    	var $this = jQuery(this).find("[type=checkbox]");
      if($this.is(":checked")) {
      	$this.prop("checked",false);
      }
      else {
      	$this.prop("checked",true);
      }

      if ($this.hasClass("all")) {
      	$this.trigger('click');
      }
  });

}//campaign chooser

jQuery(document).ready(function(){
    console.log('start');

 if( jQuery('#migla_page').val() == "home" )
 {
    console.log("home");

    jQuery('#mName').val("");
    jQuery('#mAmount').val("");

    mg_campaign_draggable();
    mg_add_campaign();
    mg_remove_campaign();
    mg_save_campaign();


    jQuery('#campaign-fa').click(function() {
        jQuery('#panel-mg_add_campaign_lines').toggle();
    });

    jQuery(".cmp-shown").click(function(){
      if( jQuery(this).is(':checked') ){
          var parent = jQuery(this).closest('.formfield');

          if( jQuery(this).val()== "-1" )
          {
            parent.addClass('pink-highlight');
          }else{
            parent.removeClass('pink-highlight');
          }
      }
    });

    jQuery(".mg_shown_cmp").click(function(){
      if( jQuery(this).is(":checked") ){
        jQuery(this).closest(".formfield").attr("data-showed", "yes");
      }else{
        jQuery(this).closest(".formfield").attr("data-showed", "no");
      }
    });

    jQuery(".mg_hide_cmp").click(function(){
      if( jQuery(this).is(":checked") ){
        jQuery(this).closest(".formfield").attr("data-showed", "no");
      }else{
        jQuery(this).closest(".formfield").attr("data-showed", "yes");
      }
    });    
    
    jQuery(".mg-li-tab").click(function(){
        jQuery(".mg-li-tab").removeClass("active");
        jQuery(this).addClass("active");
    });

  }else if( jQuery('#migla_page').val() == "form" )
  {
    console.log("Form");

    jQuery('#miglaAddAmount').val('');
    jQuery('#miglaAmountPerk').val('');

    jQuery(".formheader").find('.titleChange').each(function(){
    });

    if( jQuery('#miglaAmountTable').text() == '' ){
        jQuery('#warningEmptyAmounts').show();
    }
    jQuery('#migla_go_translate_btn').click(function(){
        mg_btn_translate();
    });

    jQuery('#confirm-reset').on('show.bs.modal', function(e) {
        mg_restore_form();
    });

    mg_amount_settings();
    mg_add_amount_level();
    mg_remove_amount_level();
    mg_level_dragged();

    mg_add_mutilval();
    mg_multival_drag()

    mg_form_settings();
    mg_add_form_field();
    mg_removeField();

    mg_add_group();
    mg_delete_group();

    mg_save_form();
    mg_restore_form();

    mg_drag_sections();

    mg_edit_multivalues();
    mg_add_uid_to_modal();
    mg_modal_show();
    mg_modal_shown();

    mg_load_sync_data();
    mg_field_type_change();


    if( jQuery('#mg_form_id').val() == 0 || jQuery('#mg_form_id').val() == '0' ){
        mg_campaign_chooser();
    }

    console.log("End");

  }

    console.log('end');
})
