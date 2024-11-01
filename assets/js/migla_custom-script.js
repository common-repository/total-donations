jQuery(function($){
  $(".migla_notice-dismiss").click(function(){
    var myCaller = "";
    var myparent = $(this).closest("div.migla_dismiss");

    if($(this).hasClass("migla_paypal-dismiss-btn")){
      myCaller = "paypal";
    }else if($(this).hasClass("migla_stripe-dismiss-btn")){
      myCaller = "stripe";
    }

    if( myCaller != ""  ){
      $.ajax({
          type  : "post",
          url   :  miglaAdminAjax.ajaxurl,
          data  : {   action : "TotalDonationsAjax_dismiss_notice",
                      whois_dismiss : myCaller,
                      nonce : miglaAdminAjax.nonce
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
                        myparent.remove();
                      }
      });
    }

  });

});