<?php
add_action( 'widgets_init', 'totaldonations_circle_widget' );

function totaldonations_circle_widget() 
{
	register_widget( 'Totaldonations_circle_Widget' );
}

class totaldonations_circle_widget extends WP_Widget 
{

	function __construct(){
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'totaldonations_circle_widget', 'description' => __('A widget that displays a circle progress bar for Total Donations', 'localization') );

		 /* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'totaldonations_circle_widget' );

		/* Create the widget. */
		WP_Widget::__construct( 'totaldonations_circle_widget', __('Total Donations - Circle Widget','localization'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) 
	{

        include_once Totaldonations_DIR_PATH . '/frontend/migla_functions.php';

	    wp_enqueue_script( 'mg-circle-progress-js', Totaldonations_DIR_URL.'assets/plugins/others/circle-progress.js',
			            array(	'jquery-ui-core',
								'jquery-ui-sortable',
								'jquery-ui-draggable',
								'jquery-ui-droppable',
								'jquery',
								'media-upload',
								'thickbox'
								)
						);

	    wp_enqueue_script( 'migla-circle-progress-js', Totaldonations_DIR_URL.'assets/plugins/others/migla-circle-progress.js',
			            array(	'jquery-ui-core',
								'jquery-ui-sortable',
								'jquery-ui-draggable',
								'jquery-ui-droppable',
								'jquery',
								'media-upload',
								'thickbox'
								)
						);

		extract( $args );

        $title = apply_filters('widget_title', $instance['title'] );
	    $campaign = $instance['campaign'];
        $style = $instance['belowHTML']; 
		$style_above = $instance['aboveHTML'];
        $link = $instance['link'];
        $linkurl = $instance['linkurl'];
        $btnclass = $instance['btnclass'];
	    $btnstyle = $instance['btnstyle'];
        $btntext = $instance['btntext'];
        $text_align = $instance['text_align'];    
        $text1 = $instance['text1'];  
        $text2 = $instance['text2'];  
        $text3 = $instance['text3'];    
        $fontsize = $instance['fontsize'];
        $circle_setting = array();		
	    $circle_setting['size'] = $instance['circle_size']; 
		$circle_setting['start_angle'] = $instance['circle_start_angle'];
		$circle_setting['thickness']   = $instance['circle_thickness'];
		$circle_setting['reverse']     = $instance['circle_reverse'];
	    $circle_setting['fill']      = $instance['circle_fill'];
	    $circle_setting['line_cap']  = $instance['circle_line_cap'];
	    $circle_setting['animation'] = $instance['circle_animation'];
		$circle_setting['inside'] = $instance['circle_inside'];
	
        echo $before_widget;

    	if( $text_align == 'no' )  
    		$is_text = 'no';
    	else
    		$is_text = 'yes';
		
	    ?>
        <h3 class='widget-title'>
            <?php echo $title. "<br>";?>
        </h3>
      
	    <?php
		$is_text = 'no';
		if( $text_align != 'no' )
			 $is_text = 'yes'; 
		   
		if( $fontsize == '' ) 
			$fontsize = 12; 

		$class2 = "";
		
		if( $btnstyle == 'GreyButton' ){
			$class2 = ' mg-btn-grey';	
		}
        
        migla_circleprogress_widget( $campaign, 
                                    '', 
                                    $link, 
                                    $btntext, 
                                    $btntext, 
                                    $text_align,
                                    $text1, 
                                    $text2, 
                                    $text3, 
                                    $fontsize , 
                                    $circle_setting,
                                    $style_above,
                                    $style
                                );


        if( $link == 'on'){
        ?>
            <p><a href="<?php echo esc_url($linkurl); ?>"><button class="<?php echo esc_html($btnclass . $class2); ?>"><?php echo esc_html($btntext); ?></button></a></p>
        <?php
        }
        ?>
       
        
        <?php		
        echo $after_widget;
	}


	function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;

        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['campaign'] = strip_tags( $new_instance['campaign'] );

        $instance['belowHTML'] =  $new_instance['belowHTML'] ; $instance['aboveHTML'] =  $new_instance['aboveHTML'] ;		
        $instance['link'] =  strip_tags( $new_instance['link'] ) ;	
        $instance['linkurl'] =  strip_tags( $new_instance['linkurl'] ) ;	
        $instance['btnclass'] =  strip_tags( $new_instance['btnclass'] );
        $instance['btnstyle'] =  strip_tags( $new_instance['btnstyle'] );
        $instance['btntext'] = $new_instance['btntext'];
        $instance['text_align'] = $new_instance['text_align'];
        $instance['text1'] = $new_instance['text1'];
        $instance['text2'] = $new_instance['text2'];
        $instance['text3'] = $new_instance['text3'];
        $instance['fontsize'] = $new_instance['fontsize'];
	    $instance['circle_size'] = $new_instance['circle_size']; 
		$instance['circle_thickness'] = $new_instance['circle_thickness']; 
		$instance['circle_start_angle'] = $new_instance['circle_start_angle']; 
	    $instance['circle_fill'] = $new_instance['circle_fill'];
		$instance['circle_reverse'] = $new_instance['circle_reverse'];
	    $instance['circle_line_cap'] = $new_instance['circle_line_cap'];
	    $instance['circle_animation'] = $new_instance['circle_animation'];
		$instance['circle_inside'] = $new_instance['circle_inside'];

 	    return $instance;
	}
	
 
	function form( $instance ) 
	{
    	if( $instance ) { 

     	} else { 
		   
	       $instance['title'] = "Total Donations Progress Bar"; 
	       $instance['campaign'] = ''; 
	       $instance['belowHTML'] = ''; 
	       $instance['aboveHTML'] = ''; 
	       $instance['link'] = ''; 
	       $instance['linkurl'] = ''; 
	       $instance['btnclass'] = ''; 
	       $instance['btnstyle'] = '';  
	       $instance['btntext'] = ''; 
	       $instance['text_align'] = 'right_left'; 
	       $instance['text1'] = 'Current';
	       $instance['text2'] = 'Target' ;
	       $instance['text3'] = 'Backer';
	       $instance['fontsize'] = 20;
		   $instance['circle_size'] = 100;   
			$instance['circle_thickness'] = 10; 
			$instance['circle_start_angle'] = 10; 
		    $instance['circle_fill'] = '#428bca';
			$instance['circle_reverse'] = 'no';
		    $instance['circle_line_cap'] = 'round';
		    $instance['circle_animation'] = 'back_forth';
			$instance['circle_inside'] = 'percentage';   
	}
?>
	<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title of the circle progress bar:', 'localization') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

    <p>
        <label ><?php _e('Choose a campaign to show :', 'localization') ?></label>   
   
	<?php
	    $obj = new MIGLA_CAMPAIGN;
	    $campaigns = $obj->get_all_info(get_locale());
	    ?>
	     <select class='widefat migla_select_campaign' name='<?php echo $this->get_field_name( 'campaign' );?>' id='<?php echo $this->get_field_id( 'campaign' );?>'>

	    <?php    
	    $b = "";
	    $i = 0;   

	    if( !empty($campaigns) )
	    {
	        foreach ( $campaigns as $cmp ) 
		    { 
	    	    if( $cmp['shown']=='1' )
	    	    {
	                $c1_name = esc_html__( $cmp['name'] );
	                $c_name = str_replace( "[q]", "'", $c1_name );
	    
	                if( $cmp['id'] == $instance['campaign']  ){
	    		    ?>      
	    		        <option value='<?php echo $cmp['id'];?>' selected=selected ><?php echo $c_name;?></option>
	    		    <?php      
	                }else{
	    		    ?>
	    		        <option value='<?php echo $cmp['id'];?>'><?php echo $c_name;?></option>
	                <?php
	                }
	    	   }
	            $i++;
		    }  
	      }	   
	      ?>
	      </select> 
	</p>

    <?php       
    	if(empty($instance['fontsize']))  $instance['fontsize'] = "24";
    ?>
        <p>
        <div><label>Font Size: <small>(The Percentage inside the circle)</small></label>
        <input input='text' class='widefat' type='number' min='9' max='40' id="<?php echo $this->get_field_id( 'fontzise' ); ?>" name="<?php echo $this->get_field_name( 'fontsize' ); ?>" value="<?php echo $instance['fontsize']; ?>"></input></div> 
        </p>

	 <p>
	 <label><?php echo __("Circle Size in Pixels :","migla-donation");?></label>
	 <input class='widefat' id='<?php echo $this->get_field_id( 'circle_size' );?>' name='<?php echo $this->get_field_name( 'circle_size' );?>' type='number' min='10' max='500' value='<?php echo $instance['circle_size'];?>'>
	 </p>

	 <p>
	 <label><?php echo __("Circle Thickness:","migla-donation");?></label>
	 <input class='widefat' id='<?php echo $this->get_field_id( 'circle_thickness' );?>' name='<?php echo $this->get_field_name( 'circle_thickness' );?>' type='number' min='10' max='500' value='<?php echo $instance['circle_thickness'];?>'>
	 </p>

	 <p>
	 <label><?php echo __("Circle Start Angle :","migla-donation");?></label>
	 <input class='widefat' id='<?php echo $this->get_field_id( 'circle_start_angle' );?>' name='<?php echo $this->get_field_name( 'circle_start_angle' );?>' type='number' min='10' max='500' value='<?php echo $instance['circle_start_angle'];?>'>
	 </p>

	 <p>
	 <label><?php echo __("Reverse:","migla-donation");?></label>
	 	<select id='<?php echo $this->get_field_id( 'circle_reverse' );?>' name='<?php echo $this->get_field_name( 'circle_reverse' );?>' class='widefat'>
	 		<option value='yes' <?php if($instance['circle_reverse'] == 'yes' ) echo 'selected'; ?>><?php echo __("Yes","migla-donation");?></option>
	 		<option value='no' <?php if($instance['circle_reverse'] == 'no' ) echo 'selected'; ?>><?php echo __("No","migla-donation");?></option>
	 	</select>
	 </p>	 

	 <p>
	 <label><?php echo __("Line Cap:","migla-donation");?></label>
	 	<select id='<?php echo $this->get_field_id( 'circle_line_cap' );?>' name='<?php echo $this->get_field_name( 'circle_line_cap' );?>' class='widefat'>
	 		<option value='butt' <?php if($instance['circle_line_cap'] == 'butt') echo 'selected'; ?>><?php echo __("Butt","migla-donation");?></option>
	 		<option value='round' <?php if($instance['circle_line_cap'] == 'round' ) echo 'selected'; ?>><?php echo __("Round","migla-donation");?></option>
	 	</select>
	 </p>	 

	 <p>
	 <label><?php echo __('Fill:','migla-donation');?></label>
	 <input maxlength='7' size='7' class='migla-circle-colorfield widefat' type='text' id='<?php echo $this->get_field_id( 'circle_fill' );?>' name='<?php echo $this->get_field_name( 'circle_fill' );?>' value='<?php echo $instance['circle_fill'];?>'>
	 </p>

	 <p>
	 <label><?php echo __("Animation:","migla-donation");?></label>
	 	<select id='<?php echo $this->get_field_id( 'circle_animation' );?>' name='<?php echo $this->get_field_name( 'circle_animation' );?>' class='widefat'>
	 		<option value='' <?php if($instance['circle_animation'] == '') echo 'selected'; ?>><?php echo __("None","migla-donation");?></option>
	 		<option value='normal' <?php if($instance['circle_animation'] == 'normal' ) echo 'selected'; ?>><?php echo __("Normal","migla-donation");?></option>
	 		<option value='back_forth' <?php if($instance['circle_animation'] == 'back_forth' ) echo 'selected'; ?>><?php echo __("Back and Forth","migla-donation");?></option>
	 	</select>
	 </p>	 	 

	<p>
		<label ><?php echo __("Inside Text:","migla-donation");?></label>
	    <select id='<?php echo $this->get_field_id( 'circle_inside' );?>' name='<?php echo $this->get_field_name( 'circle_inside' );?>' class='widefat'>
			<option value='none' <?php if($instance['circle_inside'] == 'none' ) echo 'selected'; ?>><?php echo __("None","migla-donation");?></option>
			<option value='percentage' <?php if($instance['circle_inside'] == 'percentage' ) echo 'selected'; ?>><?php echo __("Donation Percentage","migla-donation");?></option>
		</select>
	</p>
			
       <br><br>
      <?php if( isset($instance['link']) && $instance['link'] == 'on'){  ?> 
        <div><input type="checkbox" checked="checked" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }else{  ?> 
        <div><input type="checkbox" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }  ?> 


        <br><div><label>Link URL</small></label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'linkurl' ); ?>" name="<?php echo $this->get_field_name( 'linkurl' ); ?>" value="<?php echo $instance['linkurl']; ?>"></input></div>  
        <br>    

        <div><label>Add a css class on button: <small>(theme button only)</small></label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'btnclass' ); ?>" name="<?php echo $this->get_field_name( 'btnclass' ); ?>" value="<?php echo $instance['btnclass']; ?>"></input></div>  
  
     <br><label>Choose a button style:</label> 
     <select id="<?php echo $this->get_field_id( 'btnstyle' ); ?>" name="<?php echo $this->get_field_name( 'btnstyle' ); ?>" class="widefat migla_select">
     <?php if( $instance['btnstyle'] == "GreyButton" ) { ?>
 	   <option  value="themeDefault">Your Theme Default</option>
       <option selected="" value="GreyButton">Grey Button</option>
	 <?php }else{ ?>
 	   <option selected="" value="themeDefault">Your Theme Default</option>
       <option value="GreyButton">Grey Button</option>	 
	 <?php } ?>
	 </select>
	 <br><br>

      <p>
	<label for="<?php echo $this->get_field_id( 'btntext' ); ?>"><?php _e('Text on button:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'btntext' ); ?>" name="<?php echo $this->get_field_name( 'btntext' ); ?>" value="<?php echo $instance['btntext']; ?>" />
      </p>

    <p>
        <label ><?php _e('Text Alignment and Orientation:', 'localization') ?></label>    
    
    <select class='widefat migla_select' name='<?php echo $this->get_field_name( 'text_align' );?>' id='<?php echo $this->get_field_id( 'text_align' );?>'>
        <?php
        if( $instance['text_align'] == 'no' ){
        ?>
    	    <option value='no' selected=selected >No Text</option>"
        <?php
        }else{
    	?>
    	    <option value='no' >No Text</option>
        <?php    
        }	
        
        if( $instance['text_align'] == 'left_right' ){
        ?>
    	    <option value='left_right' selected=selected >left circle with right aligned text</option>"
        <?php
        }else{
    	?>
    	    <option value='left_right' >left circle with right aligned text</option>
        <?php    
        }        
 
        if( $instance['text_align'] == 'left_left' ){
        ?>  
	        <option value='left_left' selected=selected >left circle with left aligned text</option>
        <?php  }else{
	    ?>
	        <option value='left_left' >left circle with left aligned text</option>
        <?php
        }	 	  
        
        if( $instance['text_align'] == 'right_left' )
        { ?>
	        <option value='right_left' selected=selected >right circle with left aligned text</option>
        <?php
        }else{
	    ?>
	        <option value='right_left' >right circle with left aligned text</option>
        <?php    
        }	   
        
        if( $instance['text_align'] == 'right_right' )
        { ?>
	        <option value='right_right' selected=selected >right circle with right text</option>
        <?php
        }else{
        ?>    
	        <option value='right_right' >right circle with right aligned text</option>
        <?php
        }
        ?>
        </select> 
    </p>
	  
      <p>
	<label for="<?php echo $this->get_field_id( 'text1' ); ?>"><?php _e('Current Amount Label:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text1' ); ?>" name="<?php echo $this->get_field_name( 'text1' ); ?>" value="<?php echo $instance['text1']; ?>" />
      </p>

      <p>
	<label for="<?php echo $this->get_field_id( 'text2' ); ?>"><?php _e('Target Amount Label:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text2' ); ?>" name="<?php echo $this->get_field_name( 'text2' ); ?>" value="<?php echo $instance['text2']; ?>" />
      </p>
      
      <p>
	<label for="<?php echo $this->get_field_id( 'text3' ); ?>"><?php _e('Total Supporters Label:', 'localization') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text3' ); ?>" name="<?php echo $this->get_field_name( 'text3' ); ?>" value="<?php echo $instance['text3']; ?>" />
      </p>

      <p> <label for="<?php echo $this->get_field_id('aboveHTML'); ?>">Add HTML or Plain Text above:</label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'aboveHTML' ); ?>" name="<?php echo $this->get_field_name( 'aboveHTML' ); ?>"  ><?php echo $instance['aboveHTML']; ?></textarea><small><?php _e('shortcodes allowed: [amount]	[target] [campaign] [backers] [percentage]', 'localization') ?></small></p>

       <p> <label for="<?php echo $this->get_field_id('belowHTML'); ?>">Add HTML or Plain Text below:</label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'belowHTML' ); ?>" name="<?php echo $this->get_field_name( 'belowHTML' ); ?>"  ><?php echo $instance['belowHTML']; ?></textarea><small><?php _e('shortcodes allowed: [amount]	[target] [campaign] [backers] [percentage]', 'localization') ?></small></p>

<script type="text/javascript">

    ( function( $ ){
        function initColorPicker( widget ) {
            widget.find( '.migla-circle-colorfield' ).not('[id*="__i__"]').wpColorPicker( {
                  change: function(event, ui){
                            var theColor = ui.color.toString();
                            var myID = jQuery(this).attr("id");
                            $('#'+myID).closest(".widget").addClass("widget-dirty");
                            $('#'+myID).closest(".widget").find('.widget-control-save').removeAttr("disabled");
                          }//change
            });
        }
 
        function onFormUpdate( event, widget ) {
            initColorPicker( widget );
        }
 
        $( document ).on( 'widget-added widget-updated', onFormUpdate );
 
        $( document ).ready( function() {
            $( '.widget-inside:has(.migla-circle-colorfield)' ).each( function () {
                initColorPicker( $( this ) );
            } );
        } );
 
    }( jQuery ) );

</script>

	<?php
	}
}
?>