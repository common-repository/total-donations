var migla_circles = [];

jQuery(document).ready( function() { 

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

	jQuery(".migla_inpage_circle_bar").each(function(){
        var myUID = jQuery(this).attr('name') ;
        var myparent = jQuery(this).closest('.migla_circle_wrapper');
        
        var circlesize = myparent.find('.size').val();
        var start_angle = myparent.find('.start_angle').val();
        var circlethickness = myparent.find('.thickness').val();
        var circlereverse = myparent.find('.reverse').val();
        var circlecolor= myparent.find('.fill').val();
        var line_cap = myparent.find('.line_cap').val();
        var circleanimation = myparent.find('.animation').val();
        var inside = myparent.find('.inside').val();
        var percent = myparent.find('.percent').val();
        var fontsize = myparent.find('.fontsize').val();

        var percentageValue = (percent * 100);

        if( fontsize === '') fontsize = '12';
        if( circlecolor === '') circlecolor = '#00FF00';     
        if( circlethickness === '' ) circlethickness = 20;
        if( start_angle === '' ) start_angle = -Math.PI;

        var onReverse = false;

        var circle = jQuery(this);

        if( circlereverse === 'yes' )
        {
            onReverse = true;
        }

        if ( inside == 'percentage' )
        {
            jQuery(this).find('.migla_circle_text').css('line-height', (circlesize*1.0) + 'px');
            jQuery(this).find('.migla_circle_text').html( percentageValue.formatMoney(2) + '<i>%</i>'); 
        }else{
            jQuery(this).find('.migla_circle_text').html(''); 
        }

        if( circleanimation == 'back_forth' )
        {
            if( inside == 'progress'  )
            {
               circle.circleProgress({ 
                        value: percent * 1.0, 
                        fill: { color: circlecolor },
                        size : circlesize,
                        thickness : circlethickness,
                        reverse : circlereverse ,
                        startAngle : start_angle,
                        lineCap    : line_cap
                }).on('circle-animation-progress', function(event, progress, stepValue) {
                    circle.find('.migla_circle_text').html( stepValue.toFixed(2).substr(1) );
                });

            }else{
                circle.circleProgress({ 
                        value: percent * 1.0, 
                        fill: { color: circlecolor },
                        size : circlesize,
                        thickness : circlethickness,
                        reverse : circlereverse ,
                        startAngle : start_angle,
                        lineCap    : line_cap
                })        
            } 

            setTimeout(function() { circle.circleProgress('value', percent * 1.0); }, 1000);
            setTimeout(function() { circle.circleProgress('value', 1.0); }, 1100);
            setTimeout(function() { circle.circleProgress('value', percent * 1.0); }, 2100); 

        }else if( circleanimation == 'normal' )
        {
            if( inside == 'progress'  )
            {
               circle.circleProgress({ 
                        value: percent * 1.0, 
                        fill: { color: circlecolor },
                        size : circlesize,
                        thickness : circlethickness,
                        reverse : circlereverse ,
                        startAngle : start_angle,
                        lineCap    : line_cap
                }).on('circle-animation-progress', function(event, progress, stepValue) {
                    circle.find('.migla_circle_text').html(stepValue.toFixed(2).substr(1));
                });

            }else{
                circle.circleProgress({ 
                        value: percent * 1.0, 
                        fill: { color: circlecolor },
                        size : circlesize,
                        thickness : circlethickness,
                        reverse : circlereverse ,
                        startAngle : start_angle,
                        lineCap    : line_cap
                })        
            } 
        }else{
            if( inside == 'progress'  )
            {
               circle.circleProgress({ 
                        value: percent * 1.0, 
                        fill: { color: circlecolor },
                        size : circlesize,
                        thickness : circlethickness,
                        reverse : circlereverse ,
                        startAngle : start_angle,
                        lineCap    : line_cap,
                        animation : false
                }).on('circle-animation-progress', function(event, progress, stepValue) {
                    circle.find('.migla_circle_text').html(stepValue.toFixed(2).substr(1));
                });

            }else{
                circle.circleProgress({ 
                        value: percent * 1.0, 
                        fill: { color: circlecolor },
                        size : circlesize,
                        thickness : circlethickness,
                        reverse : circlereverse ,
                        startAngle : start_angle,
                        lineCap    : line_cap,
                        animation : false
                })        
            } 
        } 

	}); 

});