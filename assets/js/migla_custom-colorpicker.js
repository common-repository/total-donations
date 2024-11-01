jQuery(function($){
    $( '.migla-color-field' ).each(function(){
        $(this).wpColorPicker( {
		          change: function(event, ui){
		                    var theColor = ui.color.toString();
		                    var myID = jQuery(this).attr("id");
		                    $('#'+myID).closest(".widget").addClass("widget-dirty");
		                    $('#'+myID).closest(".widget").find('.widget-control-save').removeAttr("disabled");
		                  }//change
        });
    });
});

    ( function( $ ){
        function initColorPicker( widget ) {
            $( '.migla-color-field' ).each(function(){
            	$(this).wpColorPicker( {
		          change: function(event, ui){
		                    var theColor = ui.color.toString();
		                    var myID = jQuery(this).attr("id");
		                    $('#'+myID).closest(".widget").addClass("widget-dirty");
		                    $('#'+myID).closest(".widget").find('.widget-control-save').removeAttr("disabled");
		                  }//change
            	});
            });
        }
 
        function onFormUpdate( event, widget ) {
            initColorPicker( widget );
        }
 
        $( document ).on( 'widget-added widget-updated', onFormUpdate );
 
        $( document ).ready( function() {
            $( '.widget-inside:has(.migla-color-field)' ).each( function () {
                initColorPicker( $( this ) );
            } );
        } );
 
    }( jQuery ) );    