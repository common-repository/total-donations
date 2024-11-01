<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_customize_theme_class extends MIGLA_SEC
{
	function __construct()
    {
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 4 );
	}

	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Customize Theme', 'migla-donation' ),
			__( 'Customize Theme', 'migla-donation' ),
    		'read_customize_themes',
			'migla_donation_custom_theme',
			array( $this, 'menu_page' )
		);
	}

    function hex2RGB($hex)
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

	function menu_page()
	{
	    if ( is_user_logged_in() )
		{

            $this->create_token( 'migla_donation_custom_theme', session_id() );
            $this->write_credentials( 'migla_donation_custom_theme', session_id() );

        $objO = new MIGLA_OPTION;
    	$bgcolor2 = explode(",", $objO->get_option('migla_2ndbgcolor'));
    	$bgcborder = explode(",", $objO->get_option('migla_2ndbgcolorb') );

      	$levelcolor = $objO->get_option('migla_bglevelcolor');
      	$activelevelcolor = $objO->get_option('migla_bglevelcoloractive');
      	$borderlevelcolor = $objO->get_option('migla_borderlevelcolor');
      	$borderlevel = $objO->get_option('migla_borderlevel');

    	$tabcolor = $objO->get_option('migla_tabcolor');

        $progbarInfo = $objO->get_option('migla_progbar_info');

        $borderRadius = explode(",", $objO->get_option( 'migla_borderRadius' )); //4spinner
        $bar_color = explode(",", $objO->get_option( 'migla_bar_color' ));  //rgba
        $progressbar_bg = explode(",", $objO->get_option( 'migla_progressbar_background' )); //rgba
        $boxshadow_color = explode(",", $objO->get_option( 'migla_wellboxshadow' )); //rgba 4spinner

	    $effect = (array)unserialize($objO->get_option( 'migla_bar_style_effect' ));
        $check['yes'] = 'checked';
        $check['no'] = '';

        if(!isset($effect['Stripes'])) $effect['Stripes'] = 'yes';
        if(!isset($effect['Pulse'])) $effect['Pulse'] = 'yes';
        if(!isset($effect['AnimatedStripes'])) $effect['AnimatedStripes'] = 'yes';
        if(!isset($effect['Percentage'])) $effect['Percentage'] = 'yes';

		// Five Row int_ogress Bar
        $effectClasses = "";

        if( strcmp( $effect['Stripes'] , "yes") == 0)
        {
                  $effectClasses = $effectClasses . " striped";
        }

        if( strcmp( $effect['Pulse'] , "yes") == 0)
        {
                  $effectClasses = $effectClasses . " mg_pulse";
        }
        if( strcmp( $effect['AnimatedStripes'] ,"yes") == 0)
        {
                  $effectClasses = $effectClasses . " active animated-striped";
        }
        if( strcmp( $effect['Percentage'], "yes") == 0 )
        {
                  $effectClasses = $effectClasses . " mg_percentage";
        }

        $style1 = "";
        $style1 .= "box-shadow:".$boxshadow_color[2]."px ".$boxshadow_color[3]."px ".$boxshadow_color[4]."px ".$boxshadow_color[5]."px " ;
        $style1 .= $boxshadow_color[0]." inset !important;";

        $style1 .= "background-color:". $progressbar_bg[0].";";

        $style1 .= "-webkit-border-top-left-radius:".$borderRadius[0]."px; -webkit-border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px; -webkit-border-bottom-right-radius:".$borderRadius[3]."px;";

        $style1 .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
        $style1 .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";

        $style1 .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
        $style1 .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";

        $stylebar = "background-color:".$bar_color[0].";";


        ?>
        <div class='wrap'>
        	<div class='container-fluid'>

            <h2 class='migla'><?php echo __("Theme Customization","migla-donation");?></h2>

        	<div class='row form-horizontal'>
        		<div class='col-xs-12'>

            	<section class='panel'>
            		<header class='panel-heading'>
            			<div class='panel-actions'>
            				<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
            			</div>
            			<h2 class='panel-title'>
            				<div class='dashicons dashicons-admin-appearance'></div>
            				<?php echo __('Customize the Form','migla-donation');?>
            					<span class='panel-subtitle'> <?php echo __('Add your color/width or leave blank','migla-donation');?></span>
            			</h2>
            		</header>

            	<div id='collapseOne' class='panel-body collapse show'>

            	<div class='row'>

            		<div class='col-sm-3  col-xs-12'>
            			<label for='migla_backgroundcolor' class='control-label text-right-sm text-center-xs'>
            			<?php echo __('Panel Background:','migla-donation');?>
            			</label>
            		</div>
            		<div class='col-sm-6 col-xs-12'>
            				<input type="text" class="form-control mg-color-field" data-position="top left" data-control="hue" data-opacity='<?php echo esc_attr($bgcolor2[1]);?>' value='<?php echo esc_html($bgcolor2[0]);?>' id='migla_backgroundcolor'>

            		</div>

            		<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>
            			<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
            				<?php echo __("This is the background color of the panel in the form. Default is grey.","migla-donation");?> </span>

            	</div>


            	<div class='row'>

            		<input type='hidden' class='rgba_value' value='<?php echo esc_html($bgcborder[0].",".$bgcborder[1]);?>'>

            		<div class='col-sm-3  col-xs-12'>
            			<label for='migla_panelborder'  class='control-label text-right-sm text-center-xs'>
            				<?php echo __("Panel Border","migla-donation");?>
            			</label>
            		</div>

            		<div class='col-sm-3 col-xs-12'>

            			<input type="text" class="form-control mg-color-field" data-position="top left" data-control="hue"  data-opacity='<?php echo esc_attr($bgcborder[1]);?>' value='<?php echo esc_html($bgcborder[0]);?>' id='migla_panelborder'>

            		</div>

            		<div class='col-sm-1 col-xs-12'>
            	  		<label for='migla_widthpanelborder' class='control-label  text-right-sm text-center-xs'>
            	  			<?php echo __("Width","migla-donation");?>
            	  		</label>
            		</div>

            		<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
            	   		<div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
            				<div class='input-group' style=''>

            				<input id='migla_widthpanelborder' type='text' class='spinner-input form-control migla_positive_number_only' maxlength='2' value='<?php echo esc_html($bgcborder[2]);?>' >

            	     		<div  class='spinner-buttons input-group-btn'>
            	    			<button type='button' class='btn btn-default spinner-up' id='migla_widthpanelborderspinner-up'>
            	    				<i class='fa fa-angle-up'></i>
            					</button>
            					<button type='button' class='btn btn-default spinner-down' id='migla_widthpanelborderspinner-down'>
            						<i class='fa fa-angle-down'></i>
            					</button>
            					</div>
            				</div>

            			</div>

            		</div>

            		<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>

            			<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
            				<?php echo __(" This is the panel's border color and width in the form.","migla-donation");?></span>

            	</div>


            	<div class='row'>

            			<input type='hidden' class='rgba_value' value='<?php echo esc_html($levelcolor);?>'>

            			<div class='col-sm-3  col-xs-12'><label for='migla_bglevelcolor' class='control-label text-right-sm text-center-xs'>
            					<?php echo __("Giving Level Background:","migla-donation");?></label>
            			</div>
            			<div class='col-sm-6 col-xs-12'>

            			 	<input type="text" class="form-control mg-color-field" data-position="top left" data-control="hue"  data-opacity='1.0' autocomplete='off' value='<?php echo esc_html($levelcolor);?>' id='migla_bglevelcolor' name='migla_bglevelcolor'>
            			</div>

            			<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>

            			<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
            			<?php echo __("  This is the background color of the suggested giving level.","migla-donation");?></span>
            	</div>

            	<div class='row'>

            			<input type='hidden' class='rgba_value' value='<?php echo esc_html($borderlevelcolor);?>'>

            			<div class='col-sm-3  col-xs-12'>
            				<label for='migla_borderlevelcolor' class='control-label text-right-sm text-center-xs'>
            					<?php echo __("Giving Level Border","migla-donation");?>
            				</label>
            			</div>

            			<div class='col-sm-3 col-xs-12'>
            			 	<input type='text' class='form-control mg-color-field' data-opacity='1.0' autocomplete='off' value='<?php echo esc_html($borderlevelcolor);?>' id='migla_borderlevelcolor' name='migla_borderlevelcolor'>

            			</div>

            			<div class='col-sm-1 col-xs-12'>
            	  			<label for='migla_Widthborderlevelcolor' class='control-label  text-right-sm text-center-xs'>
            	  				<?php echo __("Width","migla-donation");?></label>
            			</div>

            			<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
            			   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
            					<div class='input-group' style=''>

            					<input type='text' id='migla_Widthborderlevelcolor' class='spinner-input form-control migla_positive_number_only' maxlength='2' value='<?php echo esc_html($borderlevel);?>' >

            				    <div class='spinner-buttons input-group-btn'>
            				    	<button type='button' class='btn btn-default spinner-up' id='migla_Widthborderlevelcolorspinner-up'>
            				    	   <i class='fa fa-angle-up'></i>
            						</button>
            						<button type='button' class='btn btn-default spinner-down' id='migla_Widthborderlevelcolorspinner-down'>
            							<i class='fa fa-angle-down'></i>
            						</button>
            					</div>

            					</div>

            				</div>
            			</div>

            			<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>

            			<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
            				<?php echo __("  This is the panel's border color and width for the suggested giving level. ","migla-donation");?>
            			</span>
            	</div>


            	<div class='row'>

            			<input type='hidden' class='rgba_value' value='<?php echo esc_html($activelevelcolor);?>'>

            			<div class='col-sm-3  col-xs-12'>
            				<label for='migla_bglevelcoloractive' class='control-label text-right-sm text-center-xs'>
            				<?php echo __("Active Giving Level Background:","migla-donation");?></label>
            			</div>

            			<div class='col-sm-6 col-xs-12'>

            			 	<input type='text' class='form-control mg-color-field' data-opacity='1.0' autocomplete='off' style='background-image: none;' value='<?php echo esc_html($activelevelcolor);?>' id='migla_bglevelcoloractive' name='migla_bglevelcoloractive'>

            			 </div>

            			<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>

            			<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
            				<?php echo __("  This is the background color of the suggested giving level when it has been selected.","migla-donation");?>
            				<span style='color:#c7254e;'><?php echo __(" Note: This only works with the button option. ","migla-donation");?> </span>
            			</span>
            	</div>

            	<div class='row'>
            		<input type='hidden' class='rgba_value' value=''>
            		<div class='col-sm-3  col-xs-12'>
                        <label for='migla_tabcolor' class='control-label text-right-sm text-center-xs'>
            			<?php echo __('Inactive Tab Background:','migla-donation');?></label>
            		</div>
            		<div class='col-sm-6 col-xs-12'>
            			 	<input type='text' class='form-control mg-color-field' data-opacity='1.0' autocomplete='off' style='background-image: none;' value='<?php echo esc_html($tabcolor);?>' id='migla_tabcolor'>
            		</div>
            		<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>
            		<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'><?php echo __("This is the background color of the your inactive tab when using more than one gateway","migla-donation");?> </span>
            	</div>

            	<div class='row'>
            		<div class='col-sm-12 center-button'>
            			<button id='migla_save_form' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?></button>
                        <button id="migla_reset-form" class="btn btn-info rbutton" value="reset" data-toggle="modal"  data-source="theme" data-target="#confirm-reset">
                            <i class="fa fa-fw fa-refresh"></i>
                            <?php echo __(" Restore to Default","migla-donation");?></button>
            		</div>
            	</div>

            	</div><!--Collapse-->

            	</section>
            	<br>

                </div><!--col-12-->
                <!--End Section1-->

                <div class='col-xs-12'>
                	<section class='panel'>
                	    <header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'>
                	        	    </a>
                	        </div>
                	       <h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div>
                            <?php echo __(" Customize Progress Bar","migla-donation");?>
                            </h2>
                        </header>
                		<div id='collapseTwo' class='panel-body collapse show'>

                        	<div class='row'>
                            	<div class='col-sm-3  col-xs-12'>
                                    <label for='migla_progressbar_text' class='control-label text-right-sm text-center-xs'>
                            		<?php echo __("Progress Bar info:","migla-donation");?>
                            		</label>
                            	</div>

                            	<div class='col-sm-6 col-xs-12'>
                        	    	<input type='textarea' id='migla_progressbar_text' class='form-control' value='<?php echo esc_html($progbarInfo);?>' cols='50' rows='2'>
                        	    </div>

                        		<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>
                        		    <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
                                        <?php echo __(" This is the information that is above the progress bar. You can use the following shortcodes:","migla-donation");?>
                                            <code>[total]</code><code>[target]</code><code>[campaign]</code>
                                            <code>[percentage]</code><code>[remainder]</code>
                                    </span>
                                </div>

                        	<div class='row '>
                        	  	<div class='form-group touching'>
                        	  		<div class='col-sm-12'>

                        			  	<div class='col-sm-3  col-xs-12'>
                        			  		<label for='mg_WBRtop-left' class='control-label text-right-sm text-center-xs'>
                        					<?php echo __(" Well Border Radius:","migla-donation");?></label>
                        				</div>

                        			  	<div class='col-sm-1'>
                        			  		<label for='mg_WBRtop-left' class='control-label  text-right-sm text-center-xs'>
                        					<?php echo __("top-left","migla-donation");?></label>

                        				</div>

                        				<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                        			   		<div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                        						<div class='input-group' style=''>

                        						<input id='mg_WBRtop-left' type='text' class='spinner-input form-control migla_positive_number_only' maxlength='2' name='topleft' value='<?php echo esc_html($borderRadius[0]);?>' id='migla_radiustopleft'>
                        																<div class='spinner-buttons input-group-btn'>
                        																			<button type='button' class='btn btn-default spinner-up' id='mg_WBRtop-leftspinner-up'>
                        																				<i class='fa fa-angle-up'></i>
                        																			</button>
                        																			<button type='button' class='btn btn-default spinner-down' id='mg_WBRtop-leftspinner-down'>
                        																				<i class='fa fa-angle-down'></i>
                        																			</button>
                        																		</div>
                        																	</div>

                        					</div>

                        				</div>

                        			  	<div class='col-sm-1'>
                        						  <label for='migla_WRBtopright' class='control-label  text-right-sm text-center-xs'>
                        						  	<?php echo __("top-right","migla-donation");?>
                        						  	</label>

                        				</div>

                        				<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                        				   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                        						<div class='input-group' style=''>
                        							<input type='text' class='spinner-input form-control migla_positive_number_only' maxlength='2' name='topright'  value='<?php echo esc_html($borderRadius[1]);?>' id='migla_WRBtopright'>
                        																		<div class='spinner-buttons input-group-btn'>
                        																			<button type='button' class='btn btn-default spinner-up' id='migla_WRBtoprightspinner-up'>
                        																				<i class='fa fa-angle-up'></i>
                        																			</button>
                        																			<button type='button' class='btn btn-default spinner-down' id='migla_WRBtoprightspinner-down'>
                        																				<i class='fa fa-angle-down'></i>
                        																			</button>
                        																		</div>
                        						</div>
                        					</div>
                        				</div>

                        			</div>

                            		<div class='col-sm-3 hidden-xs'></div>
                            	</div>


                          		<div class='form-group touching'>
                        			<div class='col-sm-12'>
                        			  <div class='col-sm-3  col-xs-12'></div>
                        				<div class='col-sm-1'>
                        	  				<label for='migla_radiusbottomleft' class='control-label  text-right-sm text-center-xs'><?php echo __("bottom-left","migla-donation");?></label>
                        				</div>

                        				<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                        				   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
                        						<div style='' class='input-group'>
                          							<input type='text' maxlength='2' class='spinner-input form-control migla_positive_number_only' name='bottomleft'  value='<?php echo esc_html($borderRadius[2]);?>' id='migla_radiusbottomleft'>
                        															<div class='spinner-buttons input-group-btn'>
                        																<button class='btn btn-default spinner-up' type='button' id='migla_radiusbottomleftspinner-up'>
                        																	<i class='fa fa-angle-up'></i>
                        																</button>
                        																<button class='btn btn-default spinner-down' type='button' id='migla_radiusbottomleftspinner-down'>
                        																	<i class='fa fa-angle-down'></i>
                        																</button>
                        															</div>
                        						</div>
                        					</div>

                        				</div>

                        			  <div class='col-sm-1'>
                        				  <label for='migla_radiusbottomright' class='control-label  text-right-sm text-center-xs'>
                        					<?php echo __("bottom-right","migla-donation");?></label>

                        				</div>


                        				<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                        				   <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                        						<div class='input-group' style=''>

                        				  			<input type='text' class='spinner-input form-control migla_positive_number_only' maxlength='2' name='bottomright'  value='<?php echo esc_html($borderRadius[3]);?>' id='migla_radiusbottomright'>
                        															<div class='spinner-buttons input-group-btn'>
                        																<button type='button' class='btn btn-default spinner-up' id='migla_radiusbottomrightspinner-up'>
                        																	<i class='fa fa-angle-up'></i>
                        																</button>
                        																<button type='button' class='btn btn-default spinner-down' id='migla_radiusbottomrightspinner-down'>
                        																	<i class='fa fa-angle-down'></i>
                        																</button>
                        															</div></div>
                        					</div>
                        				</div>

                        				<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>

                        				<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
                        					<?php echo __("This controls the round corners of the bar.","migla-donation");?></span>
                               		</div>
                              </div><!--toucing-->
                        	</div>

                    		<div class='row'>

                    			<input type='hidden' class='rgba_value' value='<?php echo esc_html($bar_color[0].",".$bar_color[1]);?>'>

                    			<div class='col-sm-3  col-xs-12'>
                    				<label for='migla_barcolor' class='control-label text-right-sm text-center-xs'>
                    					<?php echo __("Bar Color:","migla-donation");?>
                    				</label>
                    			</div>

                    			<div class='col-sm-6 col-xs-12'>
                    					<input type='text' class='mg-color-field form-control' value='<?php echo esc_html($bar_color[0]);?>' data-opacity='<?php echo esc_attr($bar_color[1]);?>' autocomplete="off" style="background-image:none;" id='migla_barcolor'>
                    			</div>

                    			<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>
                    				<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
                    					<?php echo __("This is the color of the progress bar. ","migla-donation");?></span>
                    		</div>

                    		<div class='row'>
                    			<input type='hidden' class='rgba_value' value='<?php echo esc_html($progressbar_bg[0].",".$progressbar_bg[1]);?>'>

                    			<div class='col-sm-3  col-xs-12'>
                    				<label for='migla_wellcolor' class='control-label text-right-sm text-center-xs'>
                    					<?php echo __("Well Background:","migla-donation");?>

                    					</label>
                    			</div>

                    			<div class='col-sm-6 col-xs-12'>

                    					<input type='text' class='mg-color-field form-control' value='<?php echo esc_html($progressbar_bg[0]);?>' data-opacity='<?php echo esc_attr($progressbar_bg[1]);?>' autocomplete="off" id='migla_wellcolor'>
                    			</div>

                    			<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>
                    			<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
                    				<?php echo __("This is for the background inlay of the progress bar.","migla-donation");?>

                    				</span>
                    		</div>

                    		<div class='row '>
                    			<input type='hidden' class='rgba_value' value='<?php echo esc_html($boxshadow_color[0].",".$boxshadow_color[1]);?>'>

                    			<div class='form-group touching'>

                    				 <div class='col-sm-12'>
                    				 	<div class='col-sm-3'>
                    				 		<label for='migla_wellshadow' class='control-label text-right-sm text-center-xs'>
                    				 			<?php echo __("Well Box Shadow:","migla-donation");?></label>
                    				 	</div>

                    					<div class='col-sm-6 col-xs-12'>
                    						<input type='text' value='<?php echo esc_html($boxshadow_color[0]);?>' class='mg-color-field form-control' data-opacity='<?php echo esc_attr($boxshadow_color[1]);?>' autocomplete='off' style='background-image: none;' id='migla_wellshadow'>
                    					</div>
                    					<br>
                    				    <div class='col-sm-3'></div>
                    				    <br> <br>

                    			 	</div>
                    			</div>

                      			<div class='form-group touching'>

                    		  		<div class='col-sm-12'>
                    		  			<div class='col-sm-3  col-xs-12'></div>

                      					<div class='col-sm-1'>
                      						<label for='migla_hshadow' class='control-label  text-right-sm text-center-xs'>
                      							<?php echo __("h-shadow","migla-donation");?></label>

                    					</div>

                    					<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                       						<div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
                    							<div style='' class='input-group'>
                    								<input type='text'  maxlength='2' class='spinner-input form-control migla_number_only' name='hshadow' value='<?php echo esc_html($boxshadow_color[2]);?>' id='migla_hshadow'>
                    															<div class='spinner-buttons input-group-btn'>
                    																<button class='btn btn-default spinner-up' type='button' id='migla_hshadowspinner-up'>
                    																	<i class='fa fa-angle-up'></i>
                    																</button>
                    																<button class='btn btn-default spinner-down' type='button' id='migla_hshadowspinner-down'>
                    																	<i class='fa fa-angle-down'></i>
                    																</button>
                    															</div>
                    							</div>
                    						</div>

                    					</div>

                    					<div class='col-sm-1'>
                    	  					<label for='migla_vshadow' class='control-label  text-right-sm text-center-xs'>
                    	  						<?php echo __("v-shadow","migla-donation");?></label>
                    					</div>

                    					<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                    					   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
                    							<div style='' class='input-group'>
                                                    <input id='migla_vshadow' type='text'  maxlength='2' class='spinner-input form-control migla_number_only' name='vshadow' value='<?php echo esc_html($boxshadow_color[3]);?>' id='migla_vshadow'>
                    									<div class='spinner-buttons input-group-btn'>
                    											<button class='btn btn-default spinner-up' type='button' id='migla_vshadowspinner-up'>
                                                                    <i class='fa fa-angle-up'></i>
                    											</button>
                    											<button class='btn btn-default spinner-down' type='button' id='migla_vshadowspinner-down'>
                    												<i class='fa fa-angle-down'></i>
                    											</button>
                    									</div>
                    							</div>
                    						</div>
                    					</div>

                    		    		<div class='col-sm-3 hidden-xs'></div>
                    		    	</div>
                    		    </div>

                    		  	<div class='form-group touching'>
                    				<div class='col-sm-12'>
                    			  		<div class='col-sm-3  col-xs-12'></div>
                    					<div class='col-sm-1'>
                    		  				<label for='migla_blur' class='control-label  text-right-sm text-center-xs'><?php echo __("Blur","migla-donation");?>

                    		  				</label>
                    					</div>

                    					<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                    			   			<div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                    							<div class='input-group' style=''>
                                                    <input type='text' class='spinner-input form-control migla_positive_number_only' maxlength='2' name='blur' value='<?php echo esc_html($boxshadow_color[4]);?>' id='migla_blur'>
                    																		<div class='spinner-buttons input-group-btn'>
                    																			<button type='button' class='btn btn-default spinner-up' id='migla_blurspinner-up'>
                    																				<i class='fa fa-angle-up'></i>
                    																			</button>
                    																			<button type='button' class='btn btn-default spinner-down' id='migla_blurspinner-down'>
                    																				<i class='fa fa-angle-down'></i>
                    																			</button>
                    																		</div>
                    							</div>
                    						</div>
                    					</div>

                    		  			<div class='col-sm-1'>
                    		  				<label for='migla_spread' class='control-label  text-right-sm text-center-xs'>
                    		  				<?php echo __("Spread","migla-donation");?></label>
                    					</div>


                    					<div class='col-sm-2 col-xs-12 text-right-sm text-center-xs'>
                    					   <div data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }' data-plugin-spinner=''>
                    							<div style='' class='input-group'>
                    								<input type='text'  maxlength='2' class='spinner-input form-control migla_number_only' name='spread' value='<?php echo esc_html($boxshadow_color[5]);?>' id='migla_spread'>
                    																	<div class='spinner-buttons input-group-btn'>
                    																		<button class='btn btn-default spinner-up' type='button' id='migla_spreadspinner-up'>
                    																			<i class='fa fa-angle-up'></i>
                    																		</button>
                    																		<button class='btn btn-default spinner-down' type='button' id='migla_spreadspinner-down'>
                    																			<i class='fa fa-angle-down'></i>
                    																		</button>
                    																	</div>
                    							</div>
                    						</div>
                    					</div>

                    					<div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>

                    		        	<span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'><?php echo __("This controls the inlay shadow.","migla-donation");?></span>
                    		       	</div>
                    		    </div>

                    		</div>

                        	<div class='row'>
                        			<div class='col-sm-3  col-xs-12'>
                        				<label for='inlineCheckbox1' class='control-label text-right-sm text-center-xs'><?php echo __("Bar Styling and Effects:","migla-donation");?></label>
                        			</div>

                        			<div class='col-sm-6 col-xs-12'>
                        				<div class='list-group'>
                        					<label class='list-group-item border-check-control '>
                                                <input type='checkbox' id='inlineCheckbox1' value='option1' <?php echo esc_attr($check[ ($effect['Stripes']) ]);?> class='meffects'><?php echo __("Stripes","migla-donation");?>
                                            </label>
                                            <label class='list-group-item border-check-control'>
                                       			<input type='checkbox' id='inlineCheckbox2' value='option2' <?php echo esc_attr($check[ ($effect['Pulse']) ]);?> class='meffects'><?php echo __("Pulse","migla-donation");?>
                                       		</label>

                                       		<label class=' list-group-item border-check-control'>
                        		               <input type='checkbox' id='inlineCheckbox3' value='option3' <?php echo esc_attr($check[ ($effect['AnimatedStripes']) ]);?> class='meffects'>
                        		               	<?php echo __("Animated Stripes","migla-donation");?>
                        		               	<span class='text-muted'><small> <?php echo __("(Stripes must be on)","migla-donation");?></small>
                        		               	</span>
                        		           </label>
                        		           <label class=' list-group-item border-check-control'>
                        		              <input type='checkbox' value='option4' id='inlineCheckbox4' <?php echo esc_attr($check[ ($effect['Percentage']) ]);?> class='meffects'><?php echo __("Percentage","migla-donation");?>
                        		          </label>
                                      	</div>

                        				<span class='help-control col-sm-12 text-center-xs'> <?php echo __("This controls the progress bar's effects and styling.","migla-donation");?>
                        				</span>
                        			</div>
                        	</div>

                    		<div class='row'>
                    			<div class='col-sm-3  col-xs-12'><label class='control-label text-right-sm text-center-xs'><strong>
                    					<?php echo __("Preview:","migla-donation");?></strong></label>
                    			</div>

                    			<div class='col-sm-6 col-xs-12'>
                    				<div class='progress <?php echo esc_attr($effectClasses);?>' id='divprogressbar' style='<?php echo esc_attr($style1);?>' >
                    					<div id='div2previewbar' style='width: 50%;<?php echo esc_attr($stylebar);?>' aria-valuemax='100' aria-valuemin='0' aria-valuenow='20' role='progressbar' class='progress-bar'>50%</div>
                    				</div>
                    			</div>
                    			<div class='col-sm-3  col-xs-12'></div>
                    		</div>

                    		<div class='row'>
                    			<div class='col-sm-12 center-button'>
                                    <button id='migla_save_bar' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i><?php echo __("save","migla-donation");?></button>

                                    <button id="migla_reset-progressbar" class="btn btn-info rbutton" value="reset"  data-source="progressbar" data-toggle="modal" data-target="#confirm-reset">
                                    <i class="fa fa-fw fa-refresh"></i>
                                    <?php echo __(" Restore to Default","migla-donation");?></button>
                    			</div>
                    		</div>

                        <!--collapse-->
                        </div>
                    </section><br>
                </div><!--col12-->
                <!--End Section2-->


                <!-- section 3 -->
                <?php
                    $objO = new MIGLA_OPTION;
                    
                    $circle = unserialize($objO->get_option( 'migla_circle_settings'));

                    if( isset($circle[0]) ){
                        $circle = $circle[0];
                    }

                        if( !isset($circle['size']) || $circle['size'] == '' ) {
                               $circle['size'] = 100;
                        } 
                        if( !isset($circle['start_angle'] ) || $circle['start_angle'] == '' ) {
                            $circle['start_angle'] = 0; 
                        }
                        if( !isset($circle['thickness']) || $circle['thickness'] == '' ) {
                            $circle['thickness'] = 20;
                        }
                        if( !isset($circle['reverse']) || $circle['reverse'] == '' ) { 
                            $circle['reverse'] = 'yes';
                        }
                        if( !isset($circle['line_cap']) || $circle['line_cap'] == '' ) { 
                            $circle['line_cap'] = 'round';
                        }
                        if( !isset($circle['fill']) || $circle['fill'] == '' ) {
                            $circle['fill'] = '#428bca';
                        }
                        if( !isset($circle['animation']) || $circle['animation'] == '' ) {
                            $circle['animation'] = 'none';
                        }
                        if( !isset($circle['inside']) || $circle['inside'] == '' ) {
                            $circle['inside'] = 'none';
                        }
                        if(!isset($circle['inner_font_size']) || $circle['inner_font_size'] == ''){
                            $circle['inner_font_size'] = '18';
                        }

                    $text1  = $objO->get_option( 'migla_circle_text1' );
                    $text2  = $objO->get_option( 'migla_circle_text2' );
                    $text3  = $objO->get_option( 'migla_circle_text3' );     
             
                    $text_align = $objO->get_option( 'migla_circle_textalign');

                    if( $text_align == '' || $text_align == false  ){
                        $text_align = 'left_right';
                    }else{
                        $text_align = $text_align;
                    }
                     
                    $align = array();
                    $align['no'] = '';
                    $align['left_right'] = '';
                    $align['left_left'] = '';
                    $align['right_left'] = '';
                    $align['right_right'] = '';
                     
                    if(isset($align[$text_align])) $align[$text_align] = 'checked';
                ?>

                <div class='col-xs-12'>

                <section class='panel'>
                    <header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><div class='panel-actions'>
                        <a aria-expanded='true' href='#collapseFour' data-parent='.panel' data-toggle='collapse' class='fa fa-caret-down'></a></div>
                        <h2 class='panel-title'>
                            <div class='dashicons dashicons-admin-appearance'></div><?php echo __("Circle Progress Bar", "migla-donation");?></h2>
                    </header>
                
                    <div class='panel-body collapse in' id='collapseFour'>
                
                        <div class='row mg_edit_circle'>
                            <div class='col-sm-3  col-xs-12'>
                                <label class='control-label text-right-sm text-center-xs'>
                                <?php echo __('Circle Text Info Alignment :',"migla-donation");?>                                    
                                </label>
                            </div>
                    
                            <div class='col-sm-6  col-xs-12'>
                                <div class='mg_layout-selector'>
                            
                                    <label class='mg_box'><img alt='No Text' src='<?php echo Totaldonations_DIR_URL."assets/images/circle-no-html-1.png";?>'/>
                                        <br> 
                                    <input <?php echo $align['no'];?> type='radio' value='no' id='mg_no_text' name='mg_circle-text-align'></label>
                                    
                                    <label class='mg_box'><img alt='align left with right text' src='<?php echo Totaldonations_DIR_URL. "assets/images/circle-left-text-right.png";?>' /><br>
                                    <input <?php echo $align['left_right'];?> type='radio' value='left_right' id='mg_left-right' name='mg_circle-text-align'></label>
                                     
                                    <label class='mg_box'><img alt='align left with left text' src='<?php echo Totaldonations_DIR_URL."assets/images/circle-left-text-left.png";?>' /><br>

                                    <input <?php echo $align['left_left'];?> type='radio' value='left_left' id='mg_left-left' name='mg_circle-text-align'></label>
                                    
                                    <label class='mg_box'><img alt='alight right with left text' src='<?php echo Totaldonations_DIR_URL."assets/images/circle-right-text-left.png";?>' /><br>

                                    <input <?php echo $align['right_left'];?> type='radio' value='right_left' id='mg_right-left' name='mg_circle-text-align'></label>
                                    
                                    <label class='mg_box'><img alt='align right with right text' src='<?php echo Totaldonations_DIR_URL."assets/images/circle-right-text-right.png";?>' />
                                    <br>
                                    <input <?php echo $align['right_right'];?> type='radio' value='right_right' id='mg_right-right' name='mg_circle-text-align'></label>
                            
                                </div>
                            </div>
                    
                        </div>  
                        
                        <div class='row mg_edit_circle' id='mg_text_barometer_input'>
                            <div class='col-sm-3  col-xs-12'> 
                                <label for='migla_circle_text1' class='control-label text-right-sm text-center-xs'>
                                <?php echo __('Text Info :', "migla-donation");?></label>
                            </div>
                            <div class='col-sm-2 col-xs-12'>
                               <input id='migla_circle_text1' placeholder='current' type='text' value='<?php echo $text1;?>'>
                            </div>
                            <div class='col-sm-2 col-xs-12'>
                                <input id='migla_circle_text2' placeholder='target' type='text' value='<?php echo $text2;?>'>
                            </div>
                            <div class='col-sm-2 col-xs-12'>
                                <input id='migla_circle_text3' placeholder='total donors' type='text' value='<?php echo $text3;?>'>
                            </div>    
                        </div>


<div class='row mg_edit_circle'>

                            <div class='col-sm-3  col-xs-12'>
                                <label for='migla_circle_size' class='control-label text-right-sm text-center-xs'>
                                    <?php echo __("Size:","migla-donation");?>
                                </label>
                            </div>
                
                            <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>
                                <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                                    <div class='input-group' style=''>
                                        <input id='migla_circle_size' type='text' class='spinner-input form-control migla_positive_number_only' maxlength='3' value='<?php echo $circle['size'];?>'>
                                            <div  class='spinner-buttons input-group-btn'>
                                                <button id='migla_circle_sizespinner-up' type='button' class='btn btn-default spinner-up'>
                                                    <i class='fa fa-angle-up'></i>
                                                </button>
                                                <button type='button' id='migla_circle_sizespinner-down' class='btn btn-default spinner-down' >
                                                    <i class='fa fa-angle-down'></i>
                                                </button>
                                            </div>
                                    </div>
                                </div>

                            </div>
                            <div class='col-sm-1 col-xs-12 text-left-sm text-center-xs'>
                                <label for='migla_circle_thickness' class='control-label text-right-sm text-center-xs'>
                                    <?php echo __("Thickness:","migla-donation");?>
                                    </label>
                            </div>
                            <div class='col-sm-2' data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                                <div class='input-group' style=''>
                                    <input id='migla_circle_thickness' type='text' class='spinner-input form-control migla_positive_number_only' maxlength='3' value='<?php echo $circle['thickness'];?>'>
                                        <div  class='spinner-buttons input-group-btn'>
                                            <button type='button' class='btn btn-default spinner-up' id='migla_circle_thicknessspinner-up'>
                                                <i class='fa fa-angle-up'></i>
                                            </button>
                                            <button type='button' class='btn btn-default spinner-down' id='migla_circle_thicknessspinner-down'>
                                                <i class='fa fa-angle-down'></i>
                                            </button>
                                        </div> 
                                </div>                                              
                            </div> 
                    
                            <div class='col-sm-3 col-xs-hidden'></div>      
                        </div>
                
                        <div class='row mg_edit_circle form-horizontal'>
                        
                            <div class='col-sm-3  col-xs-12'><label for='migla_circle_start_angle' class='control-label text-right-sm text-center-xs'><?php echo __("Start Angle:","migla-donation");?></label>
                            </div>
                    
                            <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>
                               <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                                    <div class='input-group' style=''>
                                    <input id='migla_circle_start_angle' type='text' class='spinner-input form-control' maxlength='3' value='<?php echo $circle['start_angle'];?>' >
                                         <div  class='spinner-buttons input-group-btn'>
                                        <button type='button' class='btn btn-default spinner-up' id='migla_circle_start_anglespinner-up'>
                                        <i class='fa fa-angle-up'></i>
                                                                                                    </button>
                                        <button type='button' class='btn btn-default spinner-down' id='migla_circle_start_anglespinner-down'>
                                                                        <i class='fa fa-angle-down'></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                                
                                </div>
                            </div>
                    
                            <div class='col-sm-1  col-xs-12'>
                                <label for='migla_circle_reverse' class='control-label text-right-sm text-center-xs'><?php echo __("Reverse:","migla-donation");?></label>
                            </div>
                            <?php
                            if( $circle['reverse'] == "yes" )
                            {
                            ?>
                                <div class='col-sm-2  col-xs-12'><label class='checkbox-inline' for='migla_circle_reverse'><input type='checkbox'  id='migla_circle_reverse' checked /><?php echo __("This reverses the direction of the circle","migla-donation");?></label></div>
                            <?php      
                            }else{ ?>
                                <div class='col-sm-2  col-xs-12'><label  class='checkbox-inline' for='migla_circle_reverse'><input type='checkbox' id='migla_circle_reverse' /><?php echo __("This reverses the direction of the circle","migla-donation");?></label></div>
                            <?php
                            }  ?>
                    
                            <div class='col-sm-3 col-xs-hidden'></div>  
                        </div>
                
                
                        <div class='row mg_edit_circle'>
                            <input type='hidden' class='rgba_value' value=''>
                            <div class='col-sm-3  col-xs-12'><label for='migla_circle_fill' class='control-label text-right-sm text-center-xs'><?php echo __('Fill:','migla-donation');?> </label>
                            </div>
                            <div class='col-sm-6 col-xs-12'>
                                <input type="text" class="form-control mg-color-field" data-position="top left" data-control="hue" value='<?php echo $circle['fill'];;?>' id='migla_circle_fill'>
                               
                            </div>
                            <div class='col-sm-3 col-xs-hidden'></div>
                        </div>
                
                        <div class='row mg_edit_circle'>
                            <input type='hidden' class='' value=''>
                            <div class='col-sm-3  col-xs-12'>
                                <label for='migla_circle_inner_font_size' class='control-label text-right-sm text-center-xs'>  <?php echo __('Inner Font Size:','migla-donation');?></label>
                            </div>

                            <div class='col-sm-6 col-xs-12'>
                                <div data-plugin-spinner='' data-plugin-options='{ &quot;value&quot;:0, &quot;min&quot;: 0, &quot;max&quot;: 10 }'>
                                    <div class='input-group' style=''>
                                        <input id='migla_circle_inner_font_size' type='text' class='spinner-input form-control migla_positive_number_only' maxlength='3' value='<?php echo $circle['inner_font_size'];?>' >
                                            <div  class='spinner-buttons input-group-btn'>
                                                <button type='button' class='btn btn-default spinner-up' id='migla_circle_inner_font_sizespinner-up'>
                                                    <i class='fa fa-angle-up'></i>
                                                </button>
                                                <button type='button' class='btn btn-default spinner-down' id='migla_circle_inner_font_sizespinner-down'>
                                                                                    <i class='fa fa-angle-down'></i>
                                                                                </button>
                                                                            </div>
                                            
                                     </div>
                                 </div>
                             </div>
                            <div class='col-sm-3 col-xs-hidden'></div>                
                        </div>
                
                        <div class='row mg_edit_circle'>
                            <div class='col-sm-3  col-xs-12'><label for='migla_circle_line_cap' class='control-label text-right-sm text-center-xs'><?php echo __("Line Cap:","migla-donation");?></label>
                            </div>
                            <?php 
                            if( $circle['line_cap'] == 'butt' ){
                            ?>
                                <div class='col-sm-6  col-xs-12'>
                                    <select id='migla_circle_line_cap'>
                                        <option value='butt' selected><?php echo __("Square","migla-donation");?></option>
                                        <option value='round'><?php echo __("Round","migla-donation");?></option>
                                    </select>
                                </div>
                            <?php
                            }else{  ?>
                                 <div class='col-sm-6  col-xs-12'>
                                    <select id='migla_circle_line_cap'>
                                        <option value='butt'><?php echo __("Square","migla-donation");?></option>
                                        <option value='round' selected><?php echo __("Round","migla-donation");?></option>
                                    </select>
                                </div>
                            <?php
                                }
                            ?>
                            <div class='col-sm-3 col-xs-hidden'></div>  
                        </div>
                
                        <div class='row mg_edit_circle'>
                            <div class='col-sm-3  col-xs-12'><label for='migla_circle_animation' class='control-label text-right-sm text-center-xs'><?php echo __("Animation:","migla-donation");?></label>
                            </div>
                            <?php
                            if( $circle['animation'] == 'none' ){
                            ?>
                               <div class='col-sm-6  col-xs-12'>
                                    <select id='migla_circle_animation'>
                                        <option value='none' selected><?php echo __("None","migla-donation");?></option>
                                        <option value='normal'><?php echo __("Normal","migla-donation");?></option>
                                        <option value='back_forth'><?php echo __("Back and Forth","migla-donation");?></option>
                                    </select>
                               </div>
                            <?php
                                }else if( $circle['animation'] == 'normal' ){
                            ?>
                               <div class='col-sm-6  col-xs-12'>
                                    <select id='migla_circle_animation'>
                                        <option value='none'><?php echo __("None","migla-donation");?></option>
                                        <option value='normal' selected><?php echo __("Normal","migla-donation");?></option>
                                        <option value='back_forth'><?php echo __("Back and Forth","migla-donation");?></option>
                                    </select>
                               </div>
                            <?php
                                }else{
                            ?>
                               <div class='col-sm-6  col-xs-12'>
                                    <select id='migla_circle_animation'>
                                        <option value='none'><?php echo __("None","migla-donation");?></option>
                                        <option value='normal'><?php echo __("Normal","migla-donation");?></option>
                                        <option value='back_forth' selected><?php echo __("Back and Forth","migla-donation");?></option>
                                    </select>
                               </div>
                            <?php    
                            }  ?>

                            <div class='col-sm-3 col-xs-hidden'></div>  
                        </div>
                              
                        <div class='row mg_edit_circle'>
                            <div class='col-sm-3  col-xs-12'><label for='migla_circle_inside' class='control-label text-right-sm text-center-xs'><?php echo __("Inner Value:","migla-donation");?></label></div>

                            <?php  if( $circle['inside'] == 'percentage' ){
                                ?>
                                <div class='col-sm-6  col-xs-12'>
                                    <select id='migla_circle_inside'>
                                       <option value='none'><?php echo __("None","migla-donation");?></option>
                                       <option value='percentage' selected><?php echo __("Donation Percentage","migla-donation");?></option>
                                    </select>
                                </div>                                   
                            <?php   
                            }else{
                            ?>    
                                <div class='col-sm-6  col-xs-12'>
                                    <select id='migla_circle_inside'>
                                           <option value='none' selected><?php echo __("None","migla-donation");?></option>
                                           <option value='percentage'><?php echo __("Donation Percentage","migla-donation");?></option>
                                    </select>
                                </div>                               
                        <?php 
                        } ?>

                        </div>

                        <div class='row'>
                            <div class='col-sm-12  center-button'><button id='migla_save_circle_settings' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?></button>
                            <button id="migla_reset-circle" class="btn btn-info rbutton" value="reset" data-toggle="modal" data-source="circle" data-target="#confirm-reset">
                            <i class="fa fa-fw fa-refresh"></i>
                            <?php echo __(" Restore to Default","migla-donation");?></button>
                        </div>
                        </div>

                <!-- I didn't delete this - Binti -->

                       <!-- <div class='row'>
                            <div class='col-sm-12 center-button'>
                                <button value='save' class='btn btn-info pbutton' id='miglaSaveCircleLayout'><i class='fa fa-fw fa-save'></i> <?php echo __("Save", "migla-donation");?></button>
                            </div>
                        </div>  -->
                    
                    </div>
                
                </section>
                
               
                </div>

                <?php
                $init = array( "size" => 200,
                                    "thickness" => 20, 
                                    "start_angle" => 1,
                                    "reverse" => "no",
                                    "line_cap" => "round",
                                    "fill" => "#428bca",
                                    "animation" => "back_forth",
                                    "inside" => "percentage",
                                    "inner_font_size" => 22 
                                );  

                    $uid = time(); 
                ?>

                <!-- end of section 3 -->

                </div><!--horizontal-->
        
            <div class='modal fade' id='confirm-reset' tabindex='-1' role='dialog' aria-labelledby='miglaWarning' aria-hidden='true' data-backdrop='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <button type='button' id='mg-restore-cancel' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-reset'><i class='fa fa-times'></i></button>
                            <h4 class='modal-title' id='miglaConfirm'><?php echo __(" Confirm Restore","migla-donation");?></h4>
                        </div>
                        <div class='modal-wrap clearfix'>
                            <input type="hidden" id='migla_restore-caller' value="">
                            <div class='modal-alert'>
                                <i class='fa fa-times-circle'></i>
                            </div>

                            <div class='modal-body'>
                                <p><?php echo __("Are you sure you want to restore these settings?","migla-donation");?></p>
                                        </div>

                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-default mbutton' data-dismiss='modal'><?php echo __("Cancel","migla-donation");?></button>
                            <button type='button' class='btn btn-danger danger rbutton' id='migla_restore-btn'><i class='fa fa-fw fa-refresh'></i><?php echo __("Restore to default","migla-donation");?></button>

                        </div>
                    </div>
                </div>
            </div>

        <!--CLOSEWRAP FLUID-->
        	</div>
        </div>

        <?php

        }else{
            $error = "<div class='wrap'><div class='container-fluid'>";
            $error .= "<h2 class='migla'>";
            $error .= __("You do not have sufficient permissions to access this page. Please contact your web administrator","migla-donation"). "</h2>";
            $error .= "</div></div>";

            wp_die( __( $error , 'migla-donation' ) );
        }

	}
}

$obj = new migla_customize_theme_class();
?>