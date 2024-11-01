<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_PROGRESSBAR' ) )
{
    class MIGLA_PROGRESSBAR
    {
        function miglahex2RGB($hex) 
        {
            preg_match("/^#{0,1}([0-9a-f]{1,6})$/i",$hex,$match);
            
            if(!isset($match[1]))
                {
                    return false;
                }
        
                if(strlen($match[1]) == 6)
                {
                    list($r, $g, $b) = array($hex[0].$hex[1],$hex[2].$hex[3],$hex[4].$hex[5]);
                }
                elseif(strlen($match[1]) == 3)
                {
                    list($r, $g, $b) = array($hex[0].$hex[0],$hex[1].$hex[1],$hex[2].$hex[2]);
                }
                else if(strlen($match[1]) == 2)
                {
                    list($r, $g, $b) = array($hex[0].$hex[1],$hex[0].$hex[1],$hex[0].$hex[1]);
                }
                else if(strlen($match[1]) == 1)
                {
                    list($r, $g, $b) = array($hex.$hex,$hex.$hex,$hex.$hex);
                }
                else
                {
                    return false;
                }
        
                $color = array();
                $color['r'] = hexdec($r);
                $color['g'] = hexdec($g);
                $color['b'] = hexdec($b);
        
                return $color;
        }
        
        function migla_shortcode_progressbar( $id, $btn , $btntext, $text, $btn_class )
        {
            $obj = new MIGLA_CAMPAIGN;
            $data   = $obj->get_info( $id, get_locale() ); 
            
            if( !empty($data) )
            {
                $objD = new CLASS_MIGLA_DONATION;
                
                $total = $objD->get_total_donation_by_campaign( '','','', $data['id'] , 1);
                
                $percent = 0;
                $target = 1.0;
                
                $campaign_name = '';
                    
                if( isset($data['target']) ){
                    $target = intval($data['target']);    
                }
                    
                $percent = ( $total / $target ) * 100 ;
                            
                $info = get_option('migla_progbar_info', true); 
                
                $remainder = $target - $total ;
                
                $info = get_option('migla_progbar_info'); 
        
                    $symbol = migla_get_curreny_symbol();
                    $thousandSep = get_option('migla_thousandSep');
                    $decimalSep = get_option('migla_decimalSep');
                    $before = ''; 
                    $after = '';
        
                    if( strtolower(get_option('migla_curplacement')) == 'before' ){
                      $before = $symbol;
                    }else{
                      $after = $symbol;     
                    }
                    
                    $showSep = get_option('migla_showDecimalSep');
                    $decSep = 0;
                    
                    if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }
        
                    $total_amount   = $before. number_format( $total , $decSep, $decimalSep, $thousandSep ). $after;
                    
                    $target_amount  = $before. number_format( $target , $decSep, $decimalSep, $thousandSep  ) .$after;
                    
                    $percentStr = $percent . "%";
                    
                    if( $remainder < 0 ){
                       $remainder_text = '';
                    }else{
                       $remainder_text = $before. number_format( $remainder , $decSep, $decimalSep, $thousandSep ). $after;
                    }
        
                    $campaign_name = str_replace("[q]", "'", $data['name'] );
        
                    $placeholder = array( '[total]',
                                        '[target]' ,
                                        '[campaign]', 
                                        '[percentage]', 
                                        '[remainder]' );
                                    
                    $replace = array( $total_amount , 
                                    $target_amount , 
                                    $campaign_name, 
                                    $percentStr , 
                                    $remainder_text  );
                    
                    $content =  str_replace($placeholder, $replace, $info);
                    
                ?>
                <div class='bootstrap-wrapper'>
        
                    <?php
                    if($text == 'yes')
                    {
                    ?>
                        <div class='progress-bar-text'>
                            <p class='progress-bar-text'>
                                <?php echo $content;?>
                            </p>
                        </div>
                       <?php
                    }   
                   
        
                    // Five Row Progress Bar
                    $effects = (array)get_option( 'migla_bar_style_effect' );
                    
                    $effectClasses = "";
                    
                    if( isset($effects['Stripes']) && $effects['Stripes'] == 'yes' ){
                         $effectClasses = $effectClasses . " striped ";
                    }
                    if( isset($effects['Pulse']) && $effects['Pulse'] == 'yes' ){
                         $effectClasses = $effectClasses . " mg_pulse";
                    }
                    if( isset($effects['Animated_stripes']) && $effects['Animated_stripes'] =='yes' ){
                         $effectClasses = $effectClasses . " active animated-striped";
                    }
                    if( isset($effects['Percentage']) && $effects['Percentage'] == 'yes' ){
                         $effectClasses = $effectClasses . " mg_percentage";
                    }
            
                    $borderRadius = explode(",", get_option( 'migla_borderRadius' )); //4spinner
                 
                    $barcolor = explode(",", get_option( 'migla_bar_color' ));  //rgba
                    
                    $progressbar_bg = explode(",", get_option( 'migla_progressbar_background' )); //rgba
                    
                    $boxshadow_color = explode(",", get_option( 'migla_wellboxshadow' )); //rgba 4spinner 
                
                    $style1 = "";
                    $style1 = "";
                    $style1 .= "box-shadow:".$boxshadow_color[2]."px ".$boxshadow_color[3]."px ".$boxshadow_color[4]."px ".$boxshadow_color[5]."px " ;
                    $style1 .= $boxshadow_color[0]." inset !important;";
                    $style1 .= "background-color:".$progressbar_bg[0].";";
            
                    $style1 .= "-webkit-border-top-left-radius:".$borderRadius[0]."px; -webkit-border-top-right-radius: ".$borderRadius[1]."px;";
                    $style1 .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px; -webkit-border-bottom-right-radius:".$borderRadius[3]."px;";
            
                    $style1 .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
                    $style1 .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";
            
                    $style1 .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
                    $style1 .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";
                
                    $stylebar = "background-color:".$barcolor[0].";";
                    ?>
                    
                    <div id='me' class='progress <?php echo $effectClasses;?>' style='<?php echo $style1;?>'> 
                        <div class='progress-bar bar' role='progressbar' aria-valuenow='<?php echo $percent;?>' 
                        aria-valuemin='0' aria-valuemax='100'
                        style='width:<?php echo $percent;?>%;<?php echo $stylebar;?>'>
                        <?php echo $percent;?>%
                        </div>
                    </div>   
            </div>    
                <?php
            }
        }
        
        function migla_draw_progress_bar( $percent )
        {
           $effect = (array)get_option( 'migla_bar_style_effect' );
                // Five Row Progress Bar
                        $effectClasses = "";
                        if( strcmp( $effect['Stripes'] , "yes") == 0){
                          $effectClasses = $effectClasses . " striped";
                        }
                        if( strcmp( $effect['Pulse'] , "yes") == 0){
                          $effectClasses = $effectClasses . " mg_pulse";
                        }
                        if( strcmp( $effect['AnimatedStripes'] ,"yes") == 0){
                          $effectClasses = $effectClasses . " active animated-striped";
                        }
                        if( strcmp( $effect['Percentage'], "yes") == 0 ){
                          $effectClasses = $effectClasses . " mg_percentage";
                        }
        
                $borderRadius = explode(",", get_option( 'migla_borderRadius' )); //4spinner
                $bar_color = explode(",", get_option( 'migla_bar_color' ));  //rgba
                $progressbar_bg = explode(",", get_option( 'migla_progressbar_background' )); //rgba
                $boxshadow_color = explode(",", get_option( 'migla_wellboxshadow' )); //rgba 4spinner 
        
                $style1 = "";
                $style1 .= "box-shadow:".$boxshadow_color[2]."px ".$boxshadow_color[3]."px ".$boxshadow_color[4]."px ".$boxshadow_color[5]."px " ;
                $style1 .= $boxshadow_color[0]." inset !important;";
                $style1 .= "background-color:".$progressbar_bg[0].";";
        
                $style1 .= "-webkit-border-top-left-radius:".$borderRadius[0]."px; -webkit-border-top-right-radius: ".$borderRadius[1]."px;";
                $style1 .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px; -webkit-border-bottom-right-radius:".$borderRadius[3]."px;";
        
                $style1 .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
                $style1 .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";
        
                $style1 .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
                $style1 .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";
            
                $stylebar = "background-color:".$bar_color[0].";";
        
            $output = "";
        
                $output .= "<div id='me' class='progress ".$effectClasses."' style='".$style1."'> ";
                $output .= "<div class='progress-bar bar' role='progressbar' aria-valuenow='20' aria-valuemin='0' aria-valuemax='100'";
                $output .= "style='width:".$percent."%;".$stylebar."'>";
                $output .= $percent . "%";
                $output .= "</div>";
                $output .= "</div>";
        
                return $output;
        }
        
        function migla_build_progressbar(  $cname, $id, $posttype , $linkbtn, $btntext, $text, $btn_class )
        {
            $CData = new migla_database();
        
            $total = 0;
            $total_amount = 0;
            $target = 0; 
            $target_amount = 0;
            $percent = 0.0;
            $percentStr = '';
            $remainder = 0;
            $donors = 0;
            $totals = array();
            
                $totals = $CData->migla_get_totals( $cname , $posttype);
                $total  = $totals[0];
                $donors = $totals[1];
                $target = $CData->migla_get_campaign_target( $cname );
        
            if(  $target != 0 )
            {
                if( $total == 0 )
                {
                      $percent = 0; 
                }else if( $target != 0 ) {
                      $percent = number_format(  ( $total / $target) * 100 , 2);          
                }
                    $remainder = $target - $total ;
                    $info = get_option('migla_progbar_info'); 
        
                    $symbol = migla_get_curreny_symbol();
                    $x = array();
                    $x[0] = get_option('migla_thousandSep');
                    $x[1] = get_option('migla_decimalSep');
                    $before = ''; $after = '';
        
                    if( strtolower(get_option('migla_curplacement')) == 'before' ){
                      $before = $symbol;
                    }else{
                      $after = $symbol;     
                    }
                    
                    $showSep = get_option('migla_showDecimalSep');
                    $decSep = 0;
                    if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }
        
                    $total_amount   = $before. number_format( $total , $decSep, $x[1], $x[0]). $after;
                    $target_amount  = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
                    $percentStr = $percent . "%";
                    if( $remainder < 0 ){
                       $remainder_text = '';
                    }else{
                       $remainder_text = $before. number_format( $remainder , $decSep, $x[1], $x[0]). $after;
                    }
        
                    
                    //codes [target] [total] [percentage] [campaign]
                    $cname2 = str_replace("[q]", "'", $cname);
        
                    $placeholder = array( '[total]','[target]' ,'[campaign]', '[percentage]', '[remainder]' );
                    $replace = array( $total_amount , $target_amount , $cname2, $percentStr , $remainder_text  );
                    $content =  str_replace($placeholder, $replace, $info);
                    $output = "";
                    $output .= "<div class='bootstrap-wrapper'>";
                    if($text == 'yes' || $text == '' )
                    {
                      $output .= "<div class='progress-bar-text'><p class='progress-bar-text'>";
                      $output .= $content;
                      $output .= "</p></div>";
                    }
                    $output .= migla_draw_progress_bar( $percent );
                    
                    $form_id = $CData->migla_get_form_id( $cname );
                    $output .= "<input type='hidden' id='mg_pg_".$form_id."' value=''/>";
                    
                    $output .= "</div>";
        
                if( $linkbtn == "yes")
                {           
                    if( $form_id != '')
                    {
                        $url = get_post_meta( $form_id, 'migla_form_url', true);
                        
                        if( $url == '' || $url == false || $url[0] == '' || empty($url) )
                            $url = get_option('migla_form_url');
                    }else{
                        $url = get_option('migla_form_url');
                    }
                    
                $output .= "<form action='".$url."' method='post'>";
                $output .= "<input type='hidden' name='campaign' value='".$cname."' />";
                $output .= "<input type='hidden' name='thanks' value='widget_bar' />";
                
                if( $btn_class == '')
                    $output .= "<button class='migla_donate_now mg-btn-grey'>".$btntext."</button>";
                else
                    $output .= "<button class='migla_donate_now ".$btn_class."'>".$btntext."</button>";
                    
                $output .= "</form>";           
               }
        
            }else{
                $output = "";
            }
        
            return $output;
        }
        
        
        function migla_draw_all_progress_bar( $c )
        {
        
          $output = "";
          if( $c == '' )
          {
            $campaignArr = (array)get_option('migla_campaign');
            if( empty($campaignArr[0]) ){
            }else{
              foreach( $campaignArr as $key => $value )
              {
                $output = migla_text_progressbar( $campaignArr[$key]['name'], "", "", "no", "no");
              }
            }
        
          }else{
                $output = migla_text_progressbar(  $c, "","", "no", "no");
          }
          echo $output;
        }
        

        function migla_draw_textbarshortcode(  $cname, $button, $buttontext, $text, $btn_class )
        {
            $CData = new migla_database();
            $total_amount   = 0;
            $remainder      = 0;
            $target         = 0; 
            $percent        = 0.0;
            $backers        = 0;        
            
            $the_totals     = $CData->migla_get_totals( $cname, '' );
            $backers        = $the_totals[1];
            
            $total_amount = $the_totals[0];
            $target = $CData->migla_get_campaign_target( $cname );
        
            //if(  $target != 0 ){
            if( $total_amount == 0 )
            {
                $percent = 0;   
                if( $target != 0 )
                    $remainder = $target;
            }else if( $target != 0 ) 
            {
                  $percent = number_format(  ( $total_amount / $target) * 100 , 2); 
                  $remainder = $target -  $total_amount;          
            }
             
                $op = get_option('migla_progbar_info'); 
        
                $symbol = migla_get_curreny_symbol();
                $x = array();
                $x[0] = get_option('migla_thousandSep');
                $x[1] = get_option('migla_decimalSep');
                $before = ''; $after = '';
        
                 if( strtolower(get_option('migla_curplacement')) == 'before' ){
                   $before = $symbol;
                 }else{
                   $after = $symbol;        
                 }
                
                $showSep = get_option('migla_showDecimalSep');
                $decSep = 0;
                if( strcmp($showSep , "yes") == 0 ){ $decSep = 2; }
        
                $total_amount = $before. number_format( $total_amount , $decSep, $x[1], $x[0]). $after;
                $target = $before. number_format( $target , $decSep, $x[1], $x[0]) .$after;
                $percentStr = $percent . "%";
                if( $remainder < 0 ){
                   $remainder_text = '';
                }else{
                   $remainder_text = $before. number_format( $remainder , $decSep, $x[1], $x[0]). $after;
                }
                
                //codes [target] [total] [percentage] [campaign]
                $cname2 = str_replace("[q]", "'", $cname);         
                $form_id = $CData->migla_get_form_id( $cname );   
                $url = get_option('migla_form_url');
                
                    if( $form_id != '')
                    {
                        $url = get_post_meta( $form_id, 'migla_form_url', true);
                        
                        if( $url == '' || $url == false || $url[0] == '' || empty($url) )
                            $url = get_option('migla_form_url');
                    }
                
                $placeholder = array( '#campaign#', '#total#','#target#' , '#percentage#' , '#remainder#', '#backers#' );
                $replace = array(  $cname2, $total_amount , $target , $percentStr , $remainder_text, $backers);
                $content =  str_replace($placeholder, $replace, $text);
        
                $start = $content;
                $pos1 = strpos($start , "#textlink:"); $afterform = "";
        
                if( $pos1 >= 0)
                {
                  $start = substr($start, ( $pos1 + 1) );     
                  $pos2 = strpos( $start , "#");
        
                  $id = rand(); $id = "mgtextlink" . $id;
                  $thecode = substr( $start , 0, $pos2 );
                  $textlink = substr( $thecode , 9 );
                  $thecode =  "#".$thecode."#";
                  $temp = $content;
        
                  $temp2 = "<a style='display:inline;padding:0px;margin:0px !important' href='javascript:{}' onclick='document.getElementById(\"".$id."\").submit(); return false;'>". $textlink."</a>";
        
                  $afterform .= "<form id='".$id."' action='".$url."' method='post' style='display:none inline;padding:0px;margin:0px !important' class='form-inline' role='form'>";
                  $afterform .= "<input type='hidden' name='campaign' value='".$cname."' style='display:inline;padding:0px;margin:0px !important' />";
                  $afterform .= "<input type='hidden' name='thanks' value='widget_bar' />";
                  $afterform .= "</form>";
        
                  $content =  str_replace( $thecode, $temp2, $temp );
                }
        
                $output = "";
                $output .= "<div style='display:inline;' class='wrapper'>";
                $output .= $content;
                $output .= "</div>";
                $output .= $afterform;
        
        
                if( $button == "yes")
                {       
                    $output .= "<form action='".$url."' method='post'>";
                    $output .= "<input type='hidden' name='campaign' value='".$cname."' />";
                    
                    if($btn_class == '')
                        $output .= "<button class='migla_donate_now mg-btn-grey'>".$buttontext."</button>";
                    else
                        $output .= "<button class='migla_donate_now ".$btn_class."'>".$buttontext."</button>";
                        
                    $output .= "</form>";
                }
        
             return $output;
        }    
    }
}
?>