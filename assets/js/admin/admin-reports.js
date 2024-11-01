var oTable;
var removeList = [];
var removeList_detail = {};
var removeDialog_lines = '';
var mg_detail = '';
var mg_drawback = "no";

var openList = [];
var openListObj = {};

function migla_array_remove( arr, search_value ){
	var index = arr.indexOf(search_value);
	if (index >= 0) {
	  return arr.splice( index, 1 );
	}
	return false;
}

function migla_array_find( arr, search_value ){
	var index = arr.indexOf(search_value);
	if (index >= 0) {
	  return true;
	}
	return false;
}

function migla_array_remove_by_idx( array, index ){
	if (index >= 0) {
	  return arr.splice( index, 1 );
	}
	return false;
}

function migla_remove_from_obj(arr, obj) {
  arr.forEach(function(key) {
    delete obj[key];
  });
  return obj;
}

function mg_detail_load( pid )
{
    var str = '';

    str = str + '<tr class="det" id="det_'+pid+'" colspan="9"><td id="td-det-'+pid+'" colspan="10">';
    str = str + '<div class="col-sm-6">';
    str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0">';
    str = str + '<tr><td>'+jQuery('#mg_load_image').html()+'</td></tr>';
	str = str + '</table></div></td></tr>';

    return str;
}

function mg_detail_empty_div( pid )
{
    var str = '';

    str = str + '<tr class="det" id="det_'+pid+'" colspan="9"><td id="td-det-'+pid+'" colspan="10">';
    str = str + '<div class="col-sm-6">';
    str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0">';
    str = str + '<tr><td></td></tr>';
	str = str + '</table></div></td></tr>';

    return str;
}

function mg_detail_show( str_detail, str_extra )
{
    var str = '';
    str = str + '<td colspan="10">';

    str = str + '<div class="col-sm-6">';
    str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0">';
    str = str + str_detail ;
	str = str + '</table></div>';

	str = str + '<div class="col-sm-5">';
    str = str + '<table class="table-hover" cellpadding="5" cellspacing="0" border="0">';
    str = str + str_extra;
	str = str + '</table>';
	str = str + '</div>';

	str = str + '</td>';

    return str;
}

function mg_edit_form_submit()
{
	jQuery("#mg_edit_report_submit").trigger('click');
}

function delay(callback, ms) {
  var timer = 0;
  return function() {
    var context = this, args = arguments;
    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, ms || 0);
  };
}

function migla_fnDrawCallback()
{

	jQuery( 'input' ).on( 'keyup change', delay(function (e) {
  		var p = jQuery(this).parent();
		var col = p.attr("id");
		col = col.slice(1);

		jQuery('#miglaReportTable').DataTable().column( col ).search(
				   jQuery(this).val(),
				   jQuery(this).prop('checked'),
				   jQuery(this).prop('checked')
				).draw().then(function(){

				});

	}, 500));

	jQuery('.sorting').click(function(){
		var n = jQuery('tr.det');
		var m = jQuery('.shown');
		m.removeClass('shown');
		n.remove();
	});

	jQuery('#sdate').datepicker({
				dateFormat : 'mm/dd/yy',
				onSelect: function() {
						jQuery(".ui-datepicker a").removeAttr("href");
						jQuery('#miglaReportTable').DataTable().draw();
					},
	});

	jQuery('#edate').datepicker({
				dateFormat : 'mm/dd/yy',
				onSelect: function() {
						jQuery(".ui-datepicker a").removeAttr("href");
						jQuery('#miglaReportTable').DataTable().draw();
					}
	});

	jQuery('.removeColumn').on('click', function(){

		var row_post_id = jQuery(this).find('.migla_post_id').val();

		var my_opposite_id = jQuery(this).attr('id') + "-Off";
		jQuery( '#'+my_opposite_id ).show();
		jQuery(this).hide();

		var parent = jQuery(this).closest('tr');


			if( !migla_array_find( removeList, row_post_id ) ) {
				removeList.push( row_post_id );

				jQuery(this).addClass('td_removed');
				parent.addClass('pink-highlight');
				parent.addClass('removed');

				var temp = [];
				var nCol = 1;

				parent.find('td').each(function(){
					if( nCol == 1 || nCol == 2 ){

					}else if( nCol == 3 ){
						temp.push( jQuery(this).find('.migla_rec_row').val() );
					}else{
						temp.push( jQuery(this).text() );
					}
					nCol = nCol + 1;
				});

				removeList_detail[row_post_id] = temp;
			}
	});

	jQuery('.removeColumnOff').on('click', function(){

		var row_post_id = jQuery(this).find('.migla_post_id').val();

		var my_opposite_id = jQuery(this).attr('id').replace("-Off","");
		jQuery( '#'+my_opposite_id ).show();
		jQuery(this).hide();

		var parent = jQuery(this).closest('tr');


		    console.log('removed');

			migla_array_remove( removeList,row_post_id);

			jQuery(this).removeClass('td_removed');
			parent.removeClass('pink-highlight');
			parent.removeClass('removed');

			var arrA = [ row_post_id ];
			migla_remove_from_obj(arrA, removeList_detail);
	});
}

function toTimestamp(strDate){
 var datum = Date.parse(strDate);
 return datum/1000;
}

function mg_unselect()
{
	jQuery('#miglaUnselect').click(function(){

        var filteredrows = oTable.fnGetNodes();

        console.log(filteredrows);

        for ( var i = 0; i < filteredrows.length; i = i + 1 )
        {
            var parent = filteredrows[i];

            if( jQuery(parent).hasClass('removed') )
            {
	            jQuery(parent).removeClass('pink-highlight');
	            jQuery(parent).removeClass('removed');

	            jQuery(parent).find('.removeColumn').show();
	            jQuery(parent).find('.removeColumnOff').hide();

	            jQuery(parent).find('.removeColumn').removeClass('td_removed');

	            var row_post_id = jQuery(parent).find('.removeColumn').find('.migla_post_id').val();

	            migla_array_remove( removeList,row_post_id);
				var arrA = [ row_post_id ];
				migla_remove_from_obj(arrA, removeList_detail);
            }
        }

	});
}

function mg_tempremove_dataTable()
{
	var filteredrows = oTable.fnGetNodes();


        for ( var i = 0; i < filteredrows.length; i = i + 1 )
        {
            var parent = filteredrows[i];

            if( jQuery(parent).hasClass('removed') )
            {
            	oTable.fnDeleteRow(filteredrows[i]);
            }
        }

}

function mg_confirm_delete()
{
    jQuery('#confirm-delete').on('show.bs.modal', function(e) {
	    jQuery('.btn-danger').show();
	    jQuery('.model-body-list').empty();

	    var nRow = 0;

		removeList.forEach(function(item) {

	    	var str = '<p>';
	        str = str + item + ': ';

	        var nCol = 1;

	        removeList_detail[item].forEach(function(itemDetail) {
		        if( nCol == 3 || nCol == 4 || nCol == 6 ){
		        	str = str + itemDetail + ' ';
		    	}
		        nCol = nCol + 1;
	        })

	        str = str + '</p>';

	        jQuery(str).appendTo( jQuery('.model-body-list') );

	        nRow = nRow + 1;
	    });

	    if( nRow <= 0 ){
	    	jQuery("<p>No Records found</p>").appendTo( jQuery('.model-body-list') );
	    }
	});


    jQuery('#mg_report_remove_cancel').click( function(e) {
    });

    jQuery('#mg_report_remove').click(function(){
        console.log("deletion confirm");

        jQuery.ajax({
            type  : "post",
            url   :  miglaAdminAjax.ajaxurl,
            data  : {
                            action  	: "TotalDonationsAjax_bulk_remove_donations",
                            remove_list : removeList,
                            auth_token 	: jQuery('#__migla_auth_token').val(),
                            auth_owner 	: jQuery('#__migla_auth_owner').val(),
                            auth_session : jQuery('#__migla_session').val()
                    },
            success: function(msg) {
                  	        removeList.length = 0;
        					removeList_detail.length = 0;
                          },
            error: function(xhr, status, error)
                            {
                              console.log(error);
                            },
            complete: function(xhr, status, error)
                            {
                            	mg_tempremove_dataTable();
                            }
        });

        jQuery("#mg_report_remove_cancel").trigger('click');

    });
}

function mg_approve_donation()
{
    jQuery('.mg-approve-btn').click(function(){

        var pid = jQuery(this).attr('name');
        var updated_data = [];

        jQuery('.mg-editable-'+pid).each(function(){
            updated_data.push([ jQuery(this).attr('name'), jQuery(this).val() ]);
        });

        jQuery.ajax({
					   type 	: "post",
					   url 		:  miglaAdminAjax.ajaxurl,
					   data 	:  {
										action	: 'TotalDonationsAjax_approve_donation' ,
										post_id	: pid,
										data_send: updated_data,
					                    auth_token : jQuery('#__migla_auth_token').val(),
					                    auth_owner : jQuery('#__migla_auth_owner').val(),
					                    auth_session : jQuery('#__migla_session').val()
									},
			success	 : function( report_resp )
        			   {
        			    },
			error    : function(xhr, status, error){

						},
			complete : function(xhr, status, error){
							 console.log('ok');
						}
		});//ajax
    });
}

function mg_detail_handler()
{

	jQuery('#miglaReportTable tbody').on('click', '.details-control', function(){

    		var tr = jQuery(this).closest('tr');

    		var get_my_id = jQuery(this).attr('id');
    		var my_opposite_id = get_my_id.replace( 'open', 'close' );

    		var row_num = jQuery('#'+get_my_id+'row').val();
    		var row_post_id = jQuery('#'+get_my_id+'pid').val();


            if( !jQuery('#'+my_opposite_id).is(':visible') )
            {
        		if( jQuery('#det_' + row_post_id ).length >= 1 )
        		{
        		}else{

        			jQuery('#'+my_opposite_id).show();
        			jQuery(this).hide();

        			jQuery( mg_detail_load( row_post_id ) ).insertAfter(tr);
        		    console.log( "Showing my loader" );

        		    var detail_report = "";

                		jQuery.ajax({
                					   type 	: "post",
                					   url 		:  miglaAdminAjax.ajaxurl,
                					   data 	:  {
                										action	: 'TotalDonationsAjax_get_detail' ,
                										post_id	: row_post_id,
                										filter	: jQuery('#migla_filter').val(),
                										post_page : 'migla_reports_page',
                					                    auth_token : jQuery('#__migla_auth_token').val(),
                					                    auth_owner : jQuery('#__migla_auth_owner').val(),
                					                    auth_session : jQuery('#__migla_session').val()
                									},
                					   success	: function( report_resp )
                        						 {
                        						 	detail_report = JSON.parse(report_resp);

                                                    if( jQuery('#td-det-'+row_post_id).length > 0 ){
                            							jQuery('#td-det-'+row_post_id).remove();
                                                    }

                        							tr.addClass( 'shown' );
                        							tr.addClass( 'selectedrow' );

                        							console.log( "Ajax Success" );
                        						 },
                						 error : function(xhr, status, error){
                							        console.log('Ajax Error');
                						 		},
                						 complete : function(xhr, status, error)
                						        {

                							        console.log('Ajax Complete');


                							        if( detail_report !== "" ){
                            							    jQuery( mg_detail_show( detail_report[0], detail_report[1] ) ).appendTo( jQuery('#det_'+row_post_id) );
                            							    console.log(detail_report);
                            							    console.log('Fill Detail?');
                							        }

                							        mg_approve_donation();
                        							mg_resend_emails();
                						 		}
                			});
        		}
            }

	});

	jQuery('#miglaReportTable tbody').on('click', '.close-details-control', function(){

		var get_my_id = jQuery(this).attr('id');
		var my_opposite_id = get_my_id.replace( 'close' , 'open');
		var my_detail = get_my_id.replace( 'td-close-' , '#det_');


        if( !jQuery('#'+my_opposite_id).is(":visible") ){

    		jQuery('#'+my_opposite_id).show();
    		jQuery(this).hide();
    		jQuery(my_detail).remove();

    		var who_call = jQuery(this).closest('tr');
    		who_call.removeClass( 'shown' );
    		who_call.removeClass( 'selectedrow' );
        }

	})
}

function mg_update_column_handler()
{
	jQuery(".updateColumn").click(function(){
			var recid = jQuery(this).find(".migla_post_id").val();
			var frmid = jQuery(this).find(".migla_frm_id").val();

			jQuery("#migla_edit_form_post_id").val(recid);
			jQuery("#migla_edit_form_form_id").val(frmid);

		mg_edit_form_submit();
	})

}

function mg_get_data_for_update()
{
		var updatedFields = [];

		jQuery(".mg-editdata-row").each(function(){

		    var edit_id = jQuery(this).find(".input_id").val();
		    var edit_type = jQuery(this).find(".input_type").val();
		    var edit_val = '';

		    if( edit_id == "country" )
		    {
        		edit_val = jQuery(this).find("#"+edit_id).val();

                var temp = [ edit_id, edit_val ];
        		updatedFields.push(temp);

                var state_id = 'miglad_state';
                var province_id = 'miglad_province';
                var state_val = '';
                var province_val = '';

                if( edit_val == 'Canada' )
                {
                    province_val = jQuery(this).find("#province-"+edit_id).val();
                }else if( edit_val == 'United States' )
                {
                    state_val = jQuery(this).find("#state-"+edit_id).val();
                }

                updatedFields.push([state_id, state_val]);
                updatedFields.push([province_id, province_val]);

            }else if( edit_id == "miglad_honoreecountry" )
		    {
        		edit_val = jQuery(this).find("#"+edit_id).val();

                var temp = [ edit_id, edit_val ];
        		updatedFields.push(temp);

                var state_id = 'miglad_honoreestate';
                var province_id = 'miglad_honoreeprovince';
                var state_val = '';
                var province_val = '';

                if( edit_val == 'Canada' )
                {
                    province_val = jQuery(this).find("#province-"+edit_id).val();
                }else if( edit_val == 'United States' )
                {
                    state_val = jQuery(this).find("#state-"+edit_id).val();
                }

                updatedFields.push([state_id, state_val]);
                updatedFields.push([province_id, province_val]);

		    }else{
        		if( edit_type == 'checkbox' )
        		{
        			if( jQuery(this).find('.input_edit').is(":checked") ){
        				edit_val = 'yes';
        			}else{
        				edit_val = 'no';
        			}
        		}else if( edit_type == 'radio'  )
        		{
        		    var val = '';

                    if( jQuery("input[name='"+edit_id+"']").length > 0 ){
                        val = jQuery("input[name='"+edit_id+"']:checked").val() ;
                    }else{
                        val = '';
                    }

                    updatedFields.push([edit_id, val]);

                }else if( edit_type == 'multiplecheckbox' ){    
                    var temp = [];
                        
                        if( jQuery("input[name='"+edit_id+"']").length > 0 )
                        {
                            jQuery("input[name='"+edit_id+"']:checked").each(function(){
                                temp.push( jQuery(this).val());
                            });
                        }

                    updatedFields.push([edit_id, temp, "array"]);  
                      
        		}else{
        		//text dropdown
        			edit_val = jQuery(this).find('.input_edit').val();

        		    updatedFields.push([ edit_id, edit_val ]);
        		}
		    }
		});

		console.log(updatedFields);

	return updatedFields;
}


function mg_update_record()
{
	jQuery('#mg_edit_form_update_btn').click(function(){

        var the_update_list =  mg_get_data_for_update();

        console.log(the_update_list);

		var rec_id		= jQuery('#mg_record_id').val();
		var form_id		= jQuery('#migla_form_id').val();

		jQuery.ajax({
			type	: 'post',
			url		: miglaAdminAjax.ajaxurl,
			data	: { action		: 'TotalDonationsAjax_update_report',
						data_send	: the_update_list,
						record_id	: rec_id,
					    auth_token  : jQuery('#__migla_auth_token').val(),
					    auth_owner  : jQuery('#__migla_auth_owner').val(),
					    auth_session : jQuery('#__migla_session').val()
					},
			success	: function(resp){
			            console.log(resp);
					},
			error : function(xhr, status, error){
						alert(error);
					},
			complete : function(xhr, status, error){
						saved('#mg_edit_form_update_btn');
					}
		});

	})
}

function mg_select2_init()
{
    jQuery('#country').click(function(){
        var _country = jQuery('#country').val();

        if(_country == 'Canada'){
            jQuery('#country-pr').show();
            jQuery('#country-st').hide();
        }else if(_country == 'United States'){
            jQuery('#country-st').show();
            jQuery('#country-pr').hide();

        }else{
            jQuery('#country-pr').hide();
            jQuery('#country-st').hide();
        }
    });

    jQuery('#miglad_honoreecountry').click(function(){
	    var Hcountry = jQuery('#miglad_honoreecountry').val();

        if( Hcountry == 'Canada'){
            jQuery('#miglad_honoreecountry-pr').show();
            jQuery('#miglad_honoreecountry-st').hide();
        }else if( Hcountry == 'United States'){
            jQuery('#miglad_honoreecountry-pr').hide();
            jQuery('#miglad_honoreecountry-st').show();
        }else{
            jQuery('#miglad_honoreecountry-pr').hide();
            jQuery('#miglad_honoreecountry-st').hide();
        }
    });
}

function mg_layout_draggable()
{
    jQuery(".formfield_layout").sortable({
        distance:30

    })
}

function mg_advanced_report()
{
    jQuery('#mg-add-map-header-field-btn').click(function(){

        var line = '';

        var custom_header = jQuery('#mg-header-name').val();
        var custom_field = '';
        var custom_field_uid = jQuery('#mg-field-uid').val();

        if( custom_field_uid == 'fill_manual' || custom_field_uid === 'fill_manual' )
        {
            custom_field = jQuery('#mg-field-value').val();
            custom_field_uid = 'manual:' + jQuery('#mg-field-value').val();

        }else if( custom_field_uid == 'logic' || custom_field_uid === 'logic' )
        {
            custom_field = jQuery('#mg-field-value').val();
            custom_field_uid = 'logic:' + jQuery('#mg-field-value').val();

        }else{
            custom_field = jQuery('#mg-field-uid option:selected' ).text();
            custom_field_uid = jQuery('#mg-field-uid').val();
        }

        line = line + '<li class="formfield_row">';
        line = line + '<div class="row map-field">';
        line = line + '<label class="col-sm-3"></label>';
        line = line + '<label class="col-sm-3">';
        line = line + custom_header;
        line = line + '</label>';
        line = line + '<label class="col-sm-3">';
        line = line + custom_field;
        line = line + '</label>';
        line = line + '<label class="col-sm-3"><button class="btn btn-danger remove-field-map"><i class="fa fa-trash"></i></button></label>';
        line = line + '<input type="hidden" class="head-field" value="'+ custom_header +'">';
        line = line + '<input type="hidden" class="value-field" value="'+ custom_field_uid +'">';
        line = line + '</div>';
        line = line + '</li>';

        jQuery(line).appendTo(jQuery('#form-field-map'));

        jQuery('.report-map').show();

    });

    jQuery(".remove-field-map").click(function(){
        jQuery(this).closest('li.formfield_row').remove();
    });

    jQuery('#mg-save-map-btn').click(function(){

        var the_update_list = [];

        jQuery('.map-field').each(function(){
            var temp = {};
            temp.header = jQuery(this).find('.head-field').val();
            temp.field = jQuery(this).find('.value-field').val();

            the_update_list.push( temp );
        });

		jQuery.ajax({
			type	: 'post',
			url		: miglaAdminAjax.ajaxurl,
			data	: { action		: 'TotalDonationsAjax_save_report_layout',
						data_send	: the_update_list,
						report_id	: jQuery('#mg-current-layout-id').val(),
						report_name : jQuery('#mg-lyt-name').val(),
					    auth_token  : jQuery('#__migla_auth_token').val(),
					    auth_owner  : jQuery('#__migla_auth_owner').val(),
					    auth_session: jQuery('#__migla_session').val()
					},
			success	: function(insert_id){
			            jQuery('#mg-current-layout-id').val(insert_id);
			            jQuery('#mg-current-layout').text(insert_id);
					},
			error : function(xhr, status, error){
						alert(error);
					},
			complete : function(xhr, status, error){
						saved('#mg-save-map-btn');
					}
		});

    });

    jQuery('.lyt-remove').click(function(){
        var myid = jQuery(this).attr('name');

		jQuery.ajax({
			type	: 'post',
			url		: miglaAdminAjax.ajaxurl,
			data	: { action		: 'TotalDonationsAjax_delete_report_layout',
						report_id	: myid,
					    auth_token  : jQuery('#__migla_auth_token').val(),
					    auth_owner  : jQuery('#__migla_auth_owner').val(),
					    auth_session: jQuery('#__migla_session').val()
					},
			success	: function(){

					},
			error : function(xhr, status, error){
						alert(error);
					},
			complete : function(xhr, status, error){
			            window.location.href = jQuery('#current-url').val();
					}
		});
    })

    mg_layout_draggable();
}

function mg_resend_emails()
{
    jQuery(".btn-resend").click(function(){

        var rec_id = jQuery(this).attr("name");
        var is_pdf = "no";

        if( jQuery(this).hasClass("send-pdf") ){
            is_pdf = "yes";
        }

        var message = "";

        jQuery('#mg_btn1_'+rec_id).hide();
        jQuery('#mg_loading1_'+rec_id).show();
        jQuery('#mg_mgs1_'+rec_id).text("");

		jQuery.ajax({
			type	: 'post',
			url		: miglaAdminAjax.ajaxurl,
			data	: { action		: 'TotalDonationsAjax_resend_email',
						record_id	: rec_id,
						is_send_pdf : is_pdf,
					    auth_token  : jQuery('#__migla_auth_token').val(),
					    auth_owner  : jQuery('#__migla_auth_owner').val(),
					    auth_session: jQuery('#__migla_session').val()
					},
			success	: function(resp){
			            message = resp;
					},
			error : function(xhr, status, error){
						alert(error);
					},
			complete : function(xhr, status, error){

			            jQuery('#mg_mgs1_'+rec_id).text( message );

					    setTimeout(function(){
					       jQuery('#mg_mgs1_'+rec_id).text( "" );
					    }, 1000);

					    setTimeout(function(){
    					    jQuery('#mg_btn1_'+rec_id).show();
                            jQuery('#mg_loading1_'+rec_id).hide();
					    }, 1000);
					}
		});
    })

    jQuery(".btn-resend-hmail").click(function(){

        var rec_id = jQuery(this).attr("name");
        var message = "";

        jQuery('#mg_btn2_'+rec_id).hide();
        jQuery('#mg_loading2_'+rec_id).show();
        jQuery('#mg_mgs2_'+rec_id).text("");

		jQuery.ajax({
			type	: 'post',
			url		: miglaAdminAjax.ajaxurl,
			data	: { action		: 'TotalDonationsAjax_resend_honoreeemail',
						record_id	: rec_id,
					    auth_token  : jQuery('#__migla_auth_token').val(),
					    auth_owner  : jQuery('#__migla_auth_owner').val(),
					    auth_session: jQuery('#__migla_session').val()
					},
			success	: function(resp){
			            message = resp;
					},
			error : function(xhr, status, error){
						alert(error);
					},
			complete : function(xhr, status, error){

			            jQuery('#mg_mgs2_'+rec_id).text( message );

					    setTimeout(function(){
					       jQuery('#mg_mgs2_'+rec_id).text( "" );
					    }, 1000);

					    setTimeout(function(){
    					    jQuery('#mg_btn2_'+rec_id).show();
                            jQuery('#mg_loading2_'+rec_id).hide();
					    }, 1000);

					}
		});
    })
}

jQuery(document).ready( function(){

  	Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
      var n = this,
          decPlaces     = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
          decSeparator  = decSeparator == undefined ? "." : decSeparator,
          thouSeparator   = thouSeparator == undefined ? "," : thouSeparator,
          sign      = n < 0 ? "-" : "",
          i         = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
          j         = (j = i.length) > 3 ? j % 3 : 0;
      return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
  	};

  	console.log( jQuery('#mg_page').val() );

  	if( jQuery('#mg_page').val() == 'home' )
  	{
		jQuery('#mg_report_filter_submit').click(function(){

			jQuery('#migla_in_start_date').val(jQuery('#migla_start_date').val());
			jQuery('#migla_in_end_date').val(jQuery('#migla_end_date').val());

			if( jQuery('#mg-show-pending').is(':checked') )
			{
				jQuery('#mg_frm_post_period').val('all');
			}else{
				jQuery('#mg_frm_post_period').val('active');
			}

			jQuery('#mg_report_filter_form').submit();
		});

		jQuery('#migla_start_date').datepicker({
				dateFormat 	: 'mm/dd/yy',
				onSelect	: function() {
								jQuery(".ui-datepicker a").removeAttr("href");
							}
		});

		jQuery('#migla_end_date').datepicker({
				dateFormat 	: 'mm/dd/yy',
				onSelect	: function() {
								jQuery(".ui-datepicker a").removeAttr("href");
							}
		});
  	}else if( jQuery('#mg_page').val() == 'report' )
  	{

		jQuery.fn.dataTable.ext.search.push( function( settings, data, dataIndex ){
			var min = toTimestamp( jQuery('#sdate').val() ) ;
			var max = toTimestamp( jQuery('#edate').val() );
			var amount = data[11] ;

				if ( (isNaN( min ) && isNaN( max ) ) || (isNaN( min ) && amount <= max ) ||
					 ( min <= amount   && isNaN( max ) )  ||
					 ( min <= amount   && amount <= max  )
				)
				{
					return true;
				}

			return false;
		});

    	oTable = jQuery('#miglaReportTable').dataTable({
            "scrollX"	: true ,
    		"language"	: {
    				 "lengthMenu": '<label>Show  Entries<select>'+
    				  '<option value="10">10</option>'+
    				 '<option value="20">20</option>'+
    				 '<option value="30">30</option>'+
    				 '<option value="40">40</option>'+
    				 '<option value="50">50</option>'+
    				 '<option value="-1">All</option>'+
    				 '</select></label>'
    			},
    		 "columnDefs": [{
	                "targets": [ 11 ],
	                "visible": false,
	                "searchable": false
            	}],
    	    "order": [[ 3, "desc" ], [ 4, "desc" ], [ 5, "desc" ], [ 6, "desc" ], [ 7, "desc" ], [ 8, "desc" ], [ 9, "desc" ]],
			"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay )
				{

            	},
            "fnDrawCallback": function( oSettings )
    			{

                        var i = 0;
                        var iTotal = 0;
                        var wcTotal = 0;
                        var tdTotal = 0;

            			for ( i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
            			{
            				    var xx = oSettings.aoData[ oSettings.aiDisplay[i] ];
            				    iTotal = iTotal + xx._aData[10] * 1;
            				    wcTotal = wcTotal + xx._aData[11] * 1;
            				    tdTotal = iTotal - wcTotal;
            			}

    			        if( jQuery('#thousand_separator').val() !== '' ){
    				        if( jQuery('#decimal_separator').val() !== '' && jQuery('#show_decimal').val() == 'yes' )
    				        {
    				        	iTotal = iTotal.formatMoney(2, jQuery('#thousand_separator').val(), jQuery('#decimal_separator').val());
    	                        wcTotal = wcTotal.formatMoney(2, jQuery('#thousand_separator').val(), jQuery('#decimal_separator').val());
    	                        tdTotal = tdTotal.formatMoney(2, jQuery('#thousand_separator').val(), jQuery('#decimal_separator').val());
    						}else{
    							iTotal = iTotal.formatMoney(0, jQuery('#thousand_separator').val(), '');
                                wcTotal = wcTotal.formatMoney(0, jQuery('#thousand_separator').val(), '');
                                tdTotal = tdTotal.formatMoney(0, jQuery('#thousand_separator').val(), '');
    						}
    			        }else{
    						if( jQuery('#decimal_separator').val() !== '' && jQuery('#show_decimal').val() == 'yes' )
    				        {
    				        	iTotal = iTotal.formatMoney(2, jQuery('#thousand_separator').val(), jQuery('#decimal_separator').val());
                            	wcTotal = wcTotal.formatMoney(2, jQuery('#thousand_separator').val(), jQuery('#decimal_separator').val());
                            	tdTotal = tdTotal.formatMoney(2, jQuery('#thousand_separator').val(), jQuery('#decimal_separator').val());
    						}else{
    							iTotal = iTotal.formatMoney(0, jQuery('#thousand_separator').val(), '');
                                wcTotal = wcTotal.formatMoney(0, jQuery('#thousand_separator').val(), '');
                                tdTotal = tdTotal.formatMoney(0, jQuery('#thousand_separator').val(), '');
    						}
    			        }

    			        jQuery('#miglaPageTotal').val(iTotal);
                        jQuery('#miglaWCTotal').val(wcTotal);
                        jQuery('#miglaTDTotal').val(tdTotal);

                		jQuery('#miglaOnTotalAmount2-number').text( jQuery('#miglaPageTotal').val() );
                		jQuery('#miglaOnTotal-WC-number').text( jQuery('#miglaWCTotal').val() );
                		jQuery('#miglaOnTotalAmount-number').text( jQuery('#miglaTDTotal').val() );

        				migla_fnDrawCallback();
    			        mg_update_column_handler();
    			        mg_unselect();
                        mg_detail_handler();

    			}//fnDrawCallback
        });// dataTable Drawn

		jQuery('.mg_go-back').click(function(){
			jQuery('#mg_report_filter_form').submit();
		});

        mg_confirm_delete();
		mg_update_column_handler();
		mg_detail_handler();
		mg_unselect();

  	}else if( jQuery('#mg_page').val() == 'edit' )
  	{

		jQuery('.mg_go-back').click(function(){
			jQuery('#mg_report_filter_form').submit();

		});

		mg_update_record();
	    mg_select2_init();

	    var _country = jQuery('#country').val();

        if(_country == 'Canada'){
            jQuery('#country-pr').show();
            jQuery('#country-st').hide();
        }else if(_country == 'United States'){
            jQuery('#country-st').show();
            jQuery('#country-pr').hide();

        }else{
            jQuery('#country-pr').hide();
            jQuery('#country-st').hide();
        }

	    var Hcountry = jQuery('#miglad_honoreecountry').val();

        if( Hcountry == 'Canada'){
            jQuery('#miglad_honoreecountry-pr').show();
            jQuery('#miglad_honoreecountry-st').hide();
        }else if( Hcountry == 'United States'){
            jQuery('#miglad_honoreecountry-pr').hide();
            jQuery('#miglad_honoreecountry-st').show();
        }else{
            jQuery('#miglad_honoreecountry-pr').hide();
            jQuery('#miglad_honoreecountry-st').hide();
        }
  	}

  	console.log('end');
})//document
