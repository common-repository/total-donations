var decimalSep;
var thousandSep;
var decimalNum;
var showDec;
var off       = [];
var on        = [];
var offArray    = [];
var onArray     = [];

var data_retrieval  = [];
var campaigns   = {};
var mg_online_total = 0;
var mg_all_total  = 0;
var total_online_this_month = 0;
var total_all_this_month = 0;
var theoutput   = [];

function calMonth( month )
{
    var m;
   switch ( Number(month) ) {
      case 1:
          m = "Jan";
          break;
      case 2:
          m = "Feb";
          break;
      case 3:
          m = "March";
          break;
      case 4:
          m = "April";
          break;
      case 5:
          m = "May";
          break;
      case 6:
          m = "June";
          break;
      case 7:
          m = "July";
          break;
      case 8:
          m = "Aug";
          break;
      case 9:
          m = "Sept";
          break;
      case 10:
          m = "Oct";
          break;
      case 11:
          m = "Nov";
          break;
      case 12:
          m = "Dec";
          break;
    }

    return m;
}

    function mg_clean_undefined( x )
    {
        var str = '';
        if( (typeof x === 'undefined') )
        {
          return str ;
        }else{
          return x;
        }
    }

    function mg_recentItem( tdate, ttime, firstname, lastname,amount,address, city, state, province, country,repeat, anon)
    {
        name    = mg_clean_undefined( firstname + ' ' + lastname );
        amount  = mg_clean_undefined( amount );
        address = mg_clean_undefined( address );
        city    = mg_clean_undefined( city );
        state   = mg_clean_undefined( state );
        province= mg_clean_undefined( province );
        country = mg_clean_undefined( country );

        var province_state = state;
        if( state === ''){
           if( province === '' ){

           }else{
             province_state = province;
           }
        }

        str = "";
        str = str + "<div class='timeline-item'><div class='row'><div class='col-xs-3 date'>";
        str = str + "<span class=''>" + jQuery("#symbol").val() + "</span>";
        str = str + tdate;
        str = str + "<br> <small class='text-navy'>"+ ttime +"</small> </div>";
        str = str + "<div class='col-xs-8 content'><p class='m-b-xs'>";
        str = str + "<strong>"+ amount +"</strong>";
        str = str + "<span class='donorname'>" + name + "</span></p>";

        if( address !== '' ){
           str = str + address + '<br>';
        }

        if( province_state !== '' ){
           str = str + province_state + '<br>';
        }

        if( city !== '' ){
           str = str + city + ', ';
        }

        if( country !== '' ){
           str = str + country + '<br>';
        }

        str = str + "Anonynmous : ";
        str = str + " <strong>" + anon + "</strong>";

        str = str + "</div></div></div>";

        return str;
      }

    function getcampaigns( num, name, percent, status, target, amt, type)
    {
        var stat    = "open";
        var statclass   = 'label-success';

        if( status == '0' || status == '-1' )
        {
          stat    = "closed";
          statclass   = 'label-warning';
        }

        var lbl = name.replace("[q]", "'");

        var str = "";
        str = str + "<tr><td>" + num + "</td><td>"+lbl+"</td>";
        str = str + "<td><span class='label " + statclass + "'>" + stat + "</span></td>";

        if( Number(target) !== 0 ){
            str = str + "<td><div class='progress progress-sm progress-half-rounded m-none mt-xs light mg_percentage'>";
            str = str + "<div style='width: " + percent + "%;' aria-valuemax='100' aria-valuemin='0' aria-valuenow='60' role='progressbar'";
            str = str + "class='progress-bar progress-bar-primary'>" + percent + "%</div></div></td>";
        }else{
          if( jQuery('#placement').html() == 'before' ){
            str = str + "<td><div class='undeclared-campaign'> Raised " + jQuery('#symbol').html() + amt + "</div></td>";
          }else{
            str = str + "<td><div class='undeclared-campaign'> Raised "  + amt + jQuery('#symbol').html() + "</div></td>";
          }
        }

        str = str + "</tr>";

        return str;
    }

    function mg_draw_chart(online, offline)
    {
        var major           = [];
        var amount_online   = [] ;

        for(key1 in online){
            major.push( online[key1]['label'] );
            amount_online.push( online[key1]['amount'] );
        }

        var lineChartData  =
        {
            labels    : major,
            datasets  : [{
                          label: "Online",
                          backgroundColor:  "rgba(43,170,177,0.3)",
                          data: amount_online
                        }]

          };

          return lineChartData;
      }

    function campaignPrototype(campaign, percent, status)
    {
        this.campaign = campaign;
        this.percent = percent;
        this.status = status;
    }

    function mg_graphs()
    {
        jQuery(".wf-dashboard-graph-attacks").click(function(){

            jQuery("#mg_graph-overlay").removeClass('hideme');
            jQuery("#body-graph").fadeTo(100, 0.1);
            jQuery(".mg-canvas-graph").addClass("hideme");

            
            jQuery(".li-graph").removeClass('mg_active');
            jQuery(this).closest(".li-graph").addClass('mg_active');

            var myaction = '';
            var mycanvas = '';

            jQuery(".li-graph").removeClass('mg_active');
            jQuery(this).closest(".li-graph").addClass('mg_active');

            if( jQuery(this).attr('id') == 'graph-1y' )
            {
                myaction = "TotalDonationsAjax_1y_GraphData";
                mycanvas = 'canvas-A';
            }else if( jQuery(this).attr('id') == 'graph-6m' )
            {
                myaction =  "TotalDonationsAjax_6m_GraphData";
                mycanvas = 'canvas-B';
            }else{
                myaction =  "TotalDonationsAjax_1m_GraphData";
                mycanvas = 'canvas-C';
            }

        jQuery.ajax({
            type  : "post",
            url   :  miglaAdminAjax.ajaxurl,
            data  : { action  : myaction,
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()

                     },
            success : function(resp)
                    {
                        console.log(resp);

                        var d  = JSON.parse(resp);
                        var on     = d[0];

                        console.log(d[0]);

                        if( on.length == 1 && on[0].amount == 0 )
                        {
                            jQuery('<p>No data to display</p>').insertAfter('#migla-donation-title');
                            jQuery(".mg-canvas-graph").addClass("hideme");
                        }else{
                          
                            var linechart = mg_draw_chart(on, []);

                            var ctx = document.getElementById('mg-'+mycanvas);

                            var myLineChart = new Chart(ctx, {
                                type: 'line',
                                data: linechart,
                                options: {
                                    scales: {
                                        yAxes: [{
                                            stacked: true
                                        }]
                                    }
                                }
                            });                         

                            jQuery('#' + mycanvas).removeClass("hideme");

                        }//else
                    },
            error : function(xhr, status, error){

                    },
            complete : function(xhr, status, error){
                            setTimeout( function(){
                                    jQuery("#mg_graph-overlay").addClass("hideme");
                                    jQuery("#body-graph").fadeTo(600, 1);
                                },
                                750
                            );   
                    }
        });

        });
    }  

    function mg_graph_init()
    {
      jQuery.ajax({
            type  : "post",
            url   :  miglaAdminAjax.ajaxurl,
            data  : { action  : "TotalDonationsAjax_1m_GraphData",
                      auth_token : jQuery('#__migla_auth_token').val(),
                      auth_owner : jQuery('#__migla_auth_owner').val(),
                      auth_session : jQuery('#__migla_session').val()
                    },
            success : function(resp)
                    {
                        console.log(resp);

                        var d  = JSON.parse(resp);
                        var on     = d[0];
                        var off    = d[1];

                        if( on.length == 1 && off.length == 1 && on[0].amount == 0 && off[0].amount == 0 )
                        {
                            jQuery('<p>No data to display</p>').insertAfter('#migla-donation-title');
                            jQuery(".mg-canvas-graph").addClass("hideme");
                        }else{
                            var linechart = mg_draw_chart(on, off);
                            var ctx = document.getElementById('mg-canvas-A');

                            var myLineChart = new Chart(ctx, {
                                type: 'line',
                                data: linechart,
                                options: {
                                    scales: {
                                        yAxes: [{
                                            stacked: true
                                        }]
                                    }
                                }
                            });                         

                            jQuery('#canvas-A').removeClass("hideme");

                        }
 
                    },
            error : function(xhr, status, error){
                    },
            complete : function(xhr, status, error){
                        setTimeout( function(){
                                jQuery("#mg_graph-overlay").addClass("hideme");
                            },
                            750
                        );

                    }
        });
    }

    function mg_countTotal()
    {
        jQuery(".mg-total-bytime").click(function(){

            var thouSeparator = jQuery("#thousandSep").val();
            var decSeparator  = jQuery("#decimalSep").val();
            var decshow       = jQuery("#showDecimal").val();
            var symbol        = jQuery("#symbol").val();

            jQuery("#mg_all_amount").html( symbol + " 0.00" );

            jQuery("#mg_total-overlay").removeClass("hideme");
            jQuery("#counttotal-body").fadeTo(600, 0.0);

            var my_action = "";
            var my_id = jQuery(this).attr("id");

            jQuery('.li-total-choice').removeClass('mg_active');
            jQuery(this).closest('li').addClass('mg_active');

            if( my_id == 'mg_amount_month' )
            {
                my_action = "TotalDonationsAjax_getThisMonth_total";
            }else if( my_id == 'mg_amount_7day'  )
            {
                my_action = "TotalDonationsAjax_get7days_total";
            }else if( my_id == 'mg_amount_today'  )
            {
                my_action = "TotalDonationsAjax_getToday_total";
            }

            var tot_all = 0.0;
            var tot_on = 0.0 ;
            var wc_total = 0.0;

            jQuery.ajax({
                type  : "post",
                url   :  miglaAdminAjax.ajaxurl,
                data  : { action  : my_action,
                          auth_token : jQuery('#__migla_auth_token').val(),
                          auth_owner : jQuery('#__migla_auth_owner').val(),
                          auth_session : jQuery('#__migla_session').val()
                        },
                success : function(resp)
                        {
                            console.log(resp);

                            var totals = JSON.parse(resp);
                            
                            var numDec = 2;
                            if(showDec == 'no'){
                                numDec = 0;    
                            }

                            tot_all = (totals[2]).formatMoney( numDec, thouSeparator, decSeparator) ;
                            tot_on = (totals[0]).formatMoney( numDec, thouSeparator, decSeparator) ;
                            wc_total = (totals[3]).formatMoney( numDec, thouSeparator, decSeparator) ;

                            console.log("#"+my_id+"_title");

                        },
                error : function(xhr, status, error)
                        {

                        },
                complete : function(xhr, status, error)
                        {
                            setTimeout( function(){
                                    jQuery("#mg_total-overlay").addClass("hideme");

                                    jQuery("#mg_all_amount").html( symbol + " " + tot_all );

                                    jQuery("#counttotal-body").fadeTo(600, 1);

                                    jQuery(".title-totals").html( jQuery("#"+my_id+"_title").val() );

                                },
                                650
                            );
                        }

            });

        });
    }

    function mg_recent_donations()
    {
        jQuery("#mg-recentSelector").click(function(){
            if(  jQuery("#recent-multiSelector").is(":visible") )
            {
                jQuery("#recent-multiSelector").hide();
            }else{
                jQuery("#recent-multiSelector").show();
            }
        });
        
        jQuery(".multiselect-list-radio").click(function(){
            jQuery("#mg_rcd-overlay").removeClass('hideme');
            jQuery("#rcd-body").fadeTo(100, 0.05);
            
            mg_recent_retrieval(jQuery(this).val());
        });        
    }

function mg_recent_retrieval(whocallme)
{
    var my_action = "";
    var my_value = "";
    var mycount = 0;

    if( whocallme == "thismonth")
    {
        my_action = "TotalDonationsAjax_thismonth_donations";
        my_value = "thismonth";
        mycount = 30;
    }else if( whocallme == "last2weeks" ){
        my_action = "TotalDonationsAjax_last2weeks_donations";
        my_value = "last2weeks";
        mycount = 14;
    }else{
        my_action = "TotalDonationsAjax_last_donations";
        my_value = "last10";
        mycount = 10;
    }

    jQuery.ajax({
        type  : "post",
        url   :  miglaAdminAjax.ajaxurl,
        data  : { action : my_action,
                          day_count  : mycount,
                          day_value  : my_value,
                          auth_token : jQuery('#__migla_auth_token').val(),
                          auth_owner : jQuery('#__migla_auth_owner').val(),
                          auth_session : jQuery('#__migla_session').val()
                        },
        success : function(resp)
                {
                    jQuery('#mg-recent-donation-display').empty();
                    var list = JSON.parse(resp);
                    var timeline = "";                            
                    var numDec = 2;
                    
                    if(showDec == 'no'){
                      numDec = 0;    
                    }

                    var nRecentRow = 0;

                    for (i in list)
                    {
                      console.log(list[i]);
                      var item = list[i];
                      formatAmount = Number(item.amount);

                      timeline  = timeline  +  mg_recentItem(
                                     item.date,
                                     item.time,
                                     item.firstname,
                                     item.lastname,
                                     formatAmount.formatMoney(numDec,decimalSep,thousandSep),
                                     item.address,
                                     item.city,
                                     item.state,
                                     item.province,
                                     item.country,
                                     item.repeating,
                                     item.anonymous
                                );

                      nRecentRow = nRecentRow + 1;
                    }

                    if( nRecentRow > 0 ){
                      jQuery("#mg_no-donation-list").addClass("hideme");
                      jQuery("#mg_no-donation-list").hide();
                    }else{
                      jQuery("#mg_no-donation-list").removeClass("hideme");
                      jQuery("#mg_no-donation-list").show();
                    }

                    jQuery( timeline ).appendTo( jQuery(".ibox-content") );
                },
        error : function(xhr, status, error)
                {

                },
        complete : function(xhr, status, error)
                {
                    setTimeout( function(){
                            jQuery("#mg_rcd-overlay").addClass("hideme");
                            jQuery("#rcd-body").fadeTo(600, 1);
                            jQuery(".h2-rcd").html( jQuery("#"+whocallme+"-h2").val() );
                        },
                        750
                    );
                }
    });
}

jQuery(document).ready( function(){

    var ajaxData ;
    var ajaxData2;
    var d;
    var list;

    decimalSep    = jQuery('#thousandSep').val();
    thousandSep   = jQuery('#decimalSep').val();
    showDec       = jQuery('#showDecimal').val();

   var before = '';
   var after = '';

    if( jQuery('#placement').val() == 'before' ){
      before = jQuery("#symbol").val();
    }else{
      after = jQuery("#symbol").val();
    }

    //TIMELINE
    setTimeout( function(){
        jQuery("#mg_rcd-overlay").addClass("hideme");
        jQuery("#rcd-body").fadeTo(600, 1);
    }, 600);

    //CAMPAIGNS
    mg_graph_init();
    mg_graphs();

    mg_countTotal();

    mg_recent_donations();

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

    setTimeout( function(){
        jQuery("#mg_graph-overlay").addClass("hideme");
        jQuery("#body-graph").fadeTo(600, 1);
    }, 600);

    console.log("END");
})
