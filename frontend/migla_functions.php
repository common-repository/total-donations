<?php
if (!function_exists('migla_shortcode_progressbar')){
function migla_shortcode_progressbar( $id, $btn, $link, $btntext, $text, $btn_class )
{
    $obj = new MIGLA_CAMPAIGN;
    $data   = $obj->get_info( $id, get_locale() );

    if( !empty($data) )
    {
        $objD = new CLASS_MIGLA_DONATION;
        $objO = new MIGLA_OPTION;
        $objM = new MIGLA_MONEY;

        $total = $objD->get_total_donation_by_campaign( '',
                                                        '',
                                                        '',
                                                        $data['id'],
                                                        1
                                                    );

        $percent = 0;
        $target = 1.0;

        $campaign_name = '';

        if( isset($data['target']) ){
            $target = intval($data['target']);
        }

        $percent = ( $total / $target ) * 100 ;

        $reminder = $target - $total ;

        $info = $objO->get_option('migla_progbar_info');

        $symbol = $objM->get_currency_symbol();
        $thousandSep = $objM->get_default_thousand_separator();
        $decimalSep = $objM->get_default_decimal_separator();

        $before_total = '';
        $after_total = '';
        $before_target = '';
        $after_target = '';
        $before_reminder = '';
        $after_reminder = '';

        $res_total = $objM->full_format( $total, 2);
        $res_target = $objM->full_format( $target, 2);
        $res_reminder = $objM->full_format( $reminder, 2);

        $placement = strtolower( $objM->get_symbol_position() );

        if( $placement == 'before' ){
          $before_total = $symbol;
          $before_target = $symbol;
          $before_reminder = $symbol;
        }else{
          $after_total = $symbol;
          $after_target = $symbol;
          $after_reminder = $symbol;
        }

            $total_amount   = $before_total.' '.$res_total[0].' '.$after_total;

            $target_amount  = $before_target.' '.$res_target[0].' '.$after_target;

            $formatted_percentage = number_format( $percent, 2, $decimalSep, $thousandSep  );
            $bar_percentage = number_format( $percent, 2, '.', '' );
            $percentStr = $formatted_percentage . "%";

            if( $reminder < 0 ){
               $reminder_text = '';
            }else{
               $remainder_text = $before_total.' '.$res_reminder[0].' '.$after_total;
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
            $effects = (array)unserialize($objO->get_option( 'migla_bar_style_effect' ));

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

            $borderRadius = explode(",", $objO->get_option( 'migla_borderRadius' )); //4spinner

            $barcolor = explode(",", $objO->get_option( 'migla_bar_color' ));  //rgba

            $progressbar_bg = explode(",", $objO->get_option( 'migla_progressbar_background' )); //rgba

            $boxshadow_color = explode(",", $objO->get_option( 'migla_wellboxshadow' )); //rgba 4spinner

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
                <div class='progress-bar bar' role='progressbar' aria-valuenow='<?php echo $bar_percentage;?>'
                aria-valuemin='0' aria-valuemax='100'
                style='width:<?php echo $bar_percentage;?>%;<?php echo $stylebar;?>'>
                <?php echo $formatted_percentage;?>%
                </div>
            </div>

            <?php
            if( $btn == 'yes' ){
            ?>
                <p>
                    <a href="<?php echo esc_url($link);?>"><button><?php echo __($btntext, "migla-donation");?></button></a>
                </p>
            <?php
            }
            ?>
    </div>
        <?php
    }
}
}

if (!function_exists('migla_sc_circle_progressbar'))
{
function migla_sc_circle_progressbar( $linkbtn, 
                                    $link,
                                    $btntext, 
                                    $text , 
                                    $id, 
                                    $btn_class )
{
    $total_amount   = 0; 
    $target_amount  = 0; 
    $percent        = 0; 
    $total          = 0;
    $target         = 0; 
    $donors         = 0;

    $obj = new MIGLA_CAMPAIGN;
    $data   = $obj->get_info( $id, get_locale() ); 

    $objO = new MIGLA_OPTION;
        
    $init = (array)unserialize( $objO->get_option( 'migla_circle_settings') );
    $info1  = $objO->get_option( 'migla_circle_text1' );
    $info2  = $objO->get_option( 'migla_circle_text2' );
    $info3  = $objO->get_option( 'migla_circle_text3' );     
 
    $align = $objO->get_option( 'migla_circle_textalign');

    if( !empty($data) )
    {
        $objD = new CLASS_MIGLA_DONATION;
        $objM = new MIGLA_MONEY;
        
        $total = $objD->get_total_donation_by_campaign( '', '', '', $id, 1);
        $donors = $objD->get_count_donation_by_campaign( '', '', '', $id, 1);
 
        $percent = 0;
        $target = 1.0;
        
        $campaign_name = '';
            
        if( isset($data['target']) ){
            $target = intval($data['target']);    
        }

        $uid = rand();

        $percent = number_format(  ( (float)$total / (float)$target) * 100 , 4);        
        $percent_val = number_format(  ( (float)$total / (float)$target) , 4);        
        
        $before_total = ''; 
        $after_total = '';
        $before_target = ''; 
        $after_target = '';

        $symbol = $objM->get_currency_symbol();

        $res_total = $objM->full_format( $total, 2);
        $res_target = $objM->full_format( $target, 2);

        if( strtolower( $objM->get_symbol_position() ) == 'before' ){
          $before_total = $symbol; 
          $before_target = $symbol; 
        }else{
          $after_total = $symbol; 
          $after_target = $symbol;
        }

        $percentStr = $percent . "%";

        $campaign_name = str_replace("[q]", "'", $data['name'] );

        $style_circle = "";
        
        if( $align == 'left_right' || $align == 'left_left' )
        {
            $style_circle .= "style='float:left !important;margin-right:40px !important;width:auto;'";
        }else if( $align == 'right_left' || $align == 'right_right' )
        {
            $style_circle .= "style='float:right !important;margin-left:40px !important;width:auto;'";
        }else{
            $style_circle .= "style='float:none !important;width:auto;'";
        }


        $placeholder = array( '[amount]', 
                              '[target]', 
                              '[campaign]', 
                              '[backers]', 
                              '[percentage]'
                        );

        $replace = array( $before_total.' '.$res_total[0].' '.$after_total,
                          $before_target.' '.$res_target[0].' '.$after_target,
                          $campaign_name,
                          $donors,
                          $percentStr
                    );
        ?>
        <div class='bootstrap-wrapper' >
            <div class='mg_circle-text-wrapper'>
            
            <div class='migla_circle_wrapper' id='mg_inpage_box_<?php echo $uid;?>' style="width:100%;">  

            <?php
            if( isset($init[0]) ){
                $init = $init[0];    
            }

            $fontsize = 12;

            if(empty($init))
            {
                    if( !isset($init['size']) || $init['size'] == '' ) {
                           $init['size'] = 100;
                    } 
                    if( !isset($init['start_angle'] ) || $init['start_angle'] == '' ) {
                        $init['start_angle'] = 0; 
                    }
                    if( !isset($init['thickness']) || $init['thickness'] == '' ) {
                        $init['thickness'] = 10;
                    }
                    if( !isset($init['reverse']) || $init['reverse'] == '' ) { 
                        $init['reverse'] = 'yes';
                    }
                    if( !isset($init['line_cap']) || $init['line_cap'] == '' ) { 
                        $init['line_cap'] = 'butt';
                    }
                    if( !isset($init['fill']) || $init['fill'] == '' ) {
                        $init['fill'] = '#00ff00';
                    }
                    if( !isset($init['animation']) || $init['animation'] == '' ) {
                        $init['animation'] = 'none';
                    }
                    if( !isset($init['inside']) || $init['inside'] == '' ) {
                        $init['inside'] = 'none';
                    }
                    if(!isset($init['inner_font_size']) || $init['inner_font_size'] == ''){
                        $init['inner_font_size'] = '12';
                    }                
            }
            
            foreach($init as $setup => $val )
            {
                    if( $setup == 'inner_font_size' ){
                      $setup = 'fontsize';
                      $fontsize = $val;
                    } 
                ?>
                    <input class="<?php echo $setup;?>" type="hidden" value="<?php echo $val;?>">
                <?php
            }                
            
            ?>
            
            <input class="percent" type="hidden" value="<?php echo $percent_val;?>">

            <div class='migla_circle_bar' id='mg_circle_wrap<?php echo $uid;?>'>
                    
                <div id='mg_circle_<?php echo $uid;?>' name='<?php echo $uid;?>' class='migla_inpage_circle_bar' <?php echo $style_circle;?>>
                  <span class='migla_circle_text' style='font-size:<?php echo $fontsize;?>px;line-height:<?php echo $fontsize;?>px;'></span>
                </div>

              <?php 
                if( $align != 'no')
                {
                    $style_barometer = '';
    
                    if( $align == 'left_right' ){
                        $style_barometer = "style='float:left !important;text-align:right !important'";
                    }else if( $align == 'right_left' ){
                        $style_barometer= "style='float:right !important;text-align:left !important'";
                    }else if( $align == 'left_left' ){
                        $style_barometer= "style='float:left !important;text-align:left !important'";    
                    }else if( $align == 'right_right' ){
                        $style_barometer= "style='float:right !important;text-align:right !important'";                
                    }
                ?>
                <div class='mg_text-barometer' <?php echo $style_barometer;?>>
                    <ul>
                          <li class='mg_campaign-raised'>
                          <span class='mg_current'><?php echo $info1;?></span> 
                          <span class='mg_current-amount'><?php echo $before_total.' '.$res_total[0].' '.$after_total;?></span>
                          </li>
                          <li class='mg_campaign-goal'>
                          <span class='mg_target'><?php echo $info2;?></span>
                          <span class='mg_target-amount'><?php echo $before_target.' '.$res_target[0].' '.$after_target;?></span>  
                          </li>
                          <li class='mg_campaign-backers'>
                          <span class='mg_backers'><?php echo $info3;?></span>
                          <span class='mg_backers-amount'><?php echo $donors;?></span>  
                         </li>  
                    </ul>
                </div>
              <?php
                }           
              ?>              
            </div>
          </div>


            <?php
            if( $linkbtn == 'yes' ){
            ?>
                <p>
                    <a href="<?php echo esc_url($link);?>"><button><?php echo __($btntext, "migla-donation");?></button></a>
                </p>
            <?php
            }
            ?>

        </div>
      </div>
  <?php
  }
 ?>

<?php 
}
}

if (!function_exists('miglaHexa2RGB'))
{
function miglaHexa2RGB($hex) 
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
}

if(!function_exists("migla_circleprogress_widget")){
function migla_circleprogress_widget( $campaign, 
                                    $posttype , 
                                    $linkbtn, 
                                    $btntext, 
                                    $text , 
                                    $align,
                                    $info1, 
                                    $info2, 
                                    $info3, 
                                    $fontsize , 
                                    $init,
                                    $style_above,
                                    $style_below
                                     )
{
    $total_amount   = 0; 
    $target_amount  = 0; 
    $percent        = 0; 
    $total          = 0;
    $target         = 0; 
    $donors         = 0;

    $obj = new MIGLA_CAMPAIGN;
    $data   = $obj->get_info( $campaign, get_locale() ); 

    if( !empty($data) )
    {
        $objD = new CLASS_MIGLA_DONATION;
        $objM = new MIGLA_MONEY;
        
        $total = $objD->get_total_donation_by_campaign( '', '', '', $campaign, 1);
        $donors = $objD->get_count_donation_by_campaign( '', '', '', $campaign, 1);
 
        $percent = 0;
        $target = 1.0;
        
        $campaign_name = '';
            
        if( isset($data['target']) ){
            $target = intval($data['target']);    
        }

        $uid = rand();

        $percent = number_format(  ( (float)$total / (float)$target) , 4) * 100;        
        $percent_val = number_format(  ( (float)$total / (float)$target) , 4);        
        
        
        $before_total = ''; 
        $after_total = '';
        $before_target = ''; 
        $after_target = '';

        $res_total = $objM->full_format( $total, 2);
        $res_target = $objM->full_format( $target, 2);

        $symbol = $objM->get_currency_symbol();

        if( strtolower( $objM->get_symbol_position() ) == 'before' ){
          $before_total = $symbol;
          $before_target = $symbol;
        }else{
          $after_total = $symbol;
          $after_target = $symbol; 
        }

        $percentStr = number_format($percent,2) . "%";

        $campaign_name = str_replace("[q]", "'", $data['name'] );

        $style_circle = "";
        
        if( $align == 'left_right' || $align == 'left_left' )
        {
            $style_circle .= "style='float:left !important;margin-right:40px !important;width:auto;'";
        }else if( $align == 'right_left' || $align == 'right_right' )
        {
            $style_circle .= "style='float:right !important;margin-left:40px !important;width:auto;'";
        }else{
            $style_circle .= "style='float:none !important;width:auto;'";
        }


        $placeholder = array( '[amount]', 
                              '[target]', 
                              '[campaign]', 
                              '[backers]', 
                              '[percentage]'
                        );

        $replace = array( $before_total.' '.$res_total[0].' '.$after_total,
                          $before_target.' '.$res_target[0].' '.$after_target,
                          $campaign_name,
                          $donors,
                          $percentStr
                    );

        $style_above = str_replace($placeholder, $replace, $style_above);
        $style_below = str_replace($placeholder, $replace, $style_below);

        ?>
        <p><?php echo $style_above;?></p>

        <div class='bootstrap-wrapper'>
            <div class='mg_circle-text-wrapper'>
            
            <div class='migla_circle_wrapper' id='mg_inpage_box_<?php echo $uid;?>' style='width:100%;'>    

            <?php
            foreach($init as $setup => $val ){
            ?>
                <input class="<?php echo $setup;?>" type="hidden" value="<?php echo $val;?>">
            <?php
            }
            ?>
            
            <input class="percent" type="hidden" value="<?php echo $percent_val;?>">
            <input class="fontsize" type="hidden" value="<?php echo $fontsize;?>">

            <div class='migla_circle_bar' id='mg_circle_wrap<?php echo $uid;?>'>
                    
                <div id='mg_circle_<?php echo $uid;?>'name='<?php echo $uid;?>'  class='migla_inpage_circle_bar' <?php echo $style_circle;?>>
                  <span class='migla_circle_text' style='font-size:<?php echo $fontsize;?>px;'></span>
                </div>

                  <?php 
              if( $align != 'no')
              {
                  $style_barometer = '';
    
                  if( $align == 'left_right' ){
                    $style_barometer = "style='float:left !important;text-align:right !important'";
                  }else if( $align == 'right_left' ){
                    $style_barometer= "style='float:right !important;text-align:left !important'";
                  }else if( $align == 'left_left' ){
                    $style_barometer= "style='float:left !important;text-align:left !important'";    
                  }else if( $align == 'right_right' ){
                    $style_barometer= "style='float:right !important;text-align:right !important'";                
                  }
                
                ?>
                <div class='mg_text-barometer' <?php echo $style_barometer;?> >
                    <ul>
                          <li class='mg_campaign-raised'>
                          <span class='mg_current'><?php echo $info1;?></span> 
                          <span class='mg_current-amount'><?php echo $before_total.' '.$res_total[0].' '.$after_total;?></span>
                          </li>
                          <li class='mg_campaign-goal'>
                          <span class='mg_target'><?php echo $info2;?></span>
                          <span class='mg_target-amount'><?php echo $before_target.' '.$res_target[0].' '.$after_target;?></span>  
                          </li>
                          <li class='mg_campaign-backers'>
                          <span class='mg_backers'><?php echo $info3;?></span>
                          <span class='mg_backers-amount'><?php echo $donors;?></span>  
                         </li>  
                    </ul>
                </div>
              <?php
              }           
              ?>              
            </div>
          </div>
            </div>
          </div>
          
      <p><?php echo $style_below;?></p>
    <?php
    }       
}
}
?>