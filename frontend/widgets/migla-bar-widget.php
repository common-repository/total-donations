<?php
add_action( 'widgets_init', 'totaldonations_bar_widget' );

if (!function_exists('totaldonations_bar_widget'))
{
function totaldonations_bar_widget() 
{
	register_widget( 'totaldonations_bar_widget' );
}
}

if ( !class_exists( 'totaldonations_bar_widget' ) )
{
class totaldonations_bar_widget extends WP_Widget
{
	function __construct()
    {
		$widget_ops = array( 'classname' => 'totaldonations_bar_widget', 'description' => __('Displays a progress bar for Total Donations', 'localization') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'totaldonations_bar_widget' );

		WP_Widget::__construct( 'totaldonations_bar_widget', __('Total Donations - Bar Widget','localization'), $widget_ops, $control_ops );
    }

	function widget( $args, $instance )
	{
	    if( !wp_script_is( 'migla-front-end-css', 'queue' )  )
            {
		          if( !wp_script_is( 'mg_progress-bar', 'registered' ) )
		          {
		              wp_register_style( 'mg_progress-bar', Totaldonations_DIR_URL.'assets/css/mg_progress-bar.css' , false, false );
		          }

		          if( !wp_script_is( 'mg_progress-bar', 'queue' ) )
		          {
		              wp_enqueue_style( 'mg_progress-bar' );
		          }

            }

		extract( $args );

		/* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title'], 10, 3 );
	    $campaign = $instance['campaign'];
		$BelowHTML = $instance['belowHTML'];
		$AboveHTML = $instance['aboveHTML'];
        $link = $instance['link'];
        $linkurl = $instance['linkurl'];
        $btnclass = $instance['btnclass'];
	    $btnstyle = $instance['btnstyle'];
        $btntext = $instance['btntext'];

		$borderRadius = array();
		$borderRadius[0] = $instance['border_radius1'];
		$borderRadius[1] = $instance['border_radius2'];
		$borderRadius[2] = $instance['border_radius3'];
		$borderRadius[3] = $instance['border_radius4'];

		$boxshadow_color = array();
		$boxshadow_color[0] = $instance['boxshadow_color1'];
		$boxshadow_color[1] = $instance['boxshadow_color2'];
		$boxshadow_color[2] = $instance['boxshadow_color3'];
		$boxshadow_color[3] = $instance['boxshadow_color4'];
		$boxshadow_color[4] = $instance['boxshadow_color5'];

		$barcolor = $instance['barcolor'];
		$well_background = $instance['well_background'];
		$well_shadows = $instance['well_shadows'];

		$form_id	= $instance['form_id'];

		$effects   = array();

		if( $instance['stripes'] == 'on'){
		    $effects['stripes'] = true;
		}else{
		    $effects['stripes'] = false;
		}

		$effects['pulse'] = false;

		if( $instance['pulse'] == 'on'){ $effects['pulse'] = true; }
		$effects['animated_stripes'] = false;

		if( $instance['animated_stripes'] == 'on'){ $effects['animated_stripes'] = true; }
		$effects['percentage'] = false;
		if( $instance['percentage'] == 'on'){ $effects['percentage'] = true; }

		$form_id = $instance['form_id'];
		$form_url = $instance['form_url'];

        /* Before widget (defined by themes). */
        echo $before_widget;

        ?>

        <h3 class='widget-title'>
            <?php echo $title;?>
        </h3>

	    <?php
    	$total_amount   = 0;
        $target_amount  = 0;
        $percent        = 0;
        $total          = 0;
        $target         = 0;
        $donors         = 0;
        $reminder       = 0;
        $remainder       = 0;
        $remainder_text = "";

        $obj = new MIGLA_CAMPAIGN;
        $objM = new MIGLA_MONEY;
        $objO = new MIGLA_OPTION;

        $data   = $obj->get_info( $campaign, get_locale() );

        if( !empty($data) )
        {
            $objD = new CLASS_MIGLA_DONATION;

            $total = $objD->get_total_donation_by_campaign( '','','', $campaign, 1 );
            $donors = $objD->get_count_donation_by_campaign( '', '', '', $campaign, 1);

            $percent = 0;
            $target = 1.0;

            $campaign_name = '';

            if( isset($data['target']) ){
                $target = intval($data['target']);
            }

            $percent = ( $total / $target ) * 100 ;

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
            $res_reminder = '';//$objM->full_format( $reminder, 2);

            $placement = strtolower( $objM->get_symbol_position() );

            if( $placement == 'before' ){
              $before_total = $symbol;
              $before_target = $symbol;
            }else{
              $after_total = $symbol;
              $after_target = $symbol;
            }

            $total_amount   = $before_total.' '.$res_total[0].' '.$after_total;

            $target_amount  = $before_target.' '.$res_target[0].' '.$after_target;

            $f_percent = number_format( $percent, 2, $decimalSep, $thousandSep  );
            $bar_percent = number_format( $percent, 2, '.', ''  );
            $percentStr = $f_percent . "%";


            $campaign_name = str_replace("[q]", "'", $data['name'] );

            $placeholder = array( '[amount]',
                                '[target]' ,
                                '[campaign]',
                                '[percentage]',
                                '[remainder]',
                                '[backers]'
                                );

            $replace = array( $total_amount ,
                            $target_amount ,
                            $campaign_name,
                            $percentStr ,
                            $remainder_text,
                            $donors
                            );

            $contentBelow =  str_replace($placeholder, $replace, $BelowHTML);
            $contentAbove =  str_replace($placeholder, $replace, $AboveHTML);

        // Five Row Progress Bar

            $effectClasses = "";

            if( $effects['stripes'] ){
                 $effectClasses = $effectClasses . " striped ";
            }
            if( $effects['pulse'] ){
                 $effectClasses = $effectClasses . " mg_pulse";
            }
            if( $effects['animated_stripes']){
                 $effectClasses = $effectClasses . " active animated-striped";
            }
            if( $effects['percentage'] ){
                 $effectClasses = $effectClasses . " mg_percentage";
            }

            $style = "";
            $style .= "box-shadow:".$boxshadow_color[0]."px ".$boxshadow_color[1]."px ";
            $style .= $boxshadow_color[2]."px ".$boxshadow_color[3]."px " ;
            $style .= $boxshadow_color[4]." inset !important;";

            $style .= "background-color:".$well_background.";";

            $style .= "-webkit-border-top-left-radius:".$borderRadius[0]."px;";
            $style .= "-webkit-border-top-right-radius: ".$borderRadius[1]."px;";
            $style .= "-webkit-border-bottom-left-radius: ".$borderRadius[2]."px;";
            $style .=   "-webkit-border-bottom-right-radius:".$borderRadius[3]."px;";

            $style .= "-moz-border-radius-topleft:".$borderRadius[0]."px; -moz-border-radius-topright: ".$borderRadius[1]."px;";
            $style .= "-moz-border-radius-bottomleft: ".$borderRadius[2]."px;-moz-border-radius-bottomright:".$borderRadius[3]."px;";

            $style .= "border-top-left-radius:".$borderRadius[0]."px; border-top-right-radius: ".$borderRadius[1]."px;";
            $style .= "border-bottom-left-radius:  ".$borderRadius[2]."px;border-bottom-right-radius:".$borderRadius[3]."px;";

            $stylebar = "background-color:".$barcolor.";";

        $class2 = "";
        if( $btnstyle == 'GreyButton' )
	    {
            $class2 = ' mg-btn-grey';
	    }

        ?>
        <div class='bootstrap-wrapper'>
           <div class='progress-sidebar'>

            <div class='mg_bar-custom-text'><?php echo $contentAbove;?></div>
            <div id='me' class='progress <?php echo $effectClasses;?>' style='<?php echo $style;?>'>
                <div class='progress-bar bar' role='progressbar' aria-valuenow='20' aria-valuemin='0' aria-valuemax='100' style='width:<?php echo $bar_percent."%;".$stylebar;?>'>
                    <?php echo $f_percent . "%";?>
                </div>
            </div>
            <div class='mg_bar-custom-text'><?php echo $contentBelow;?></div>

            <?php
            
            if($link == 'on'){
            ?>
            <p><a href="<?php echo esc_url($linkurl); ?>"><button class="<?php echo esc_html($btnclass . $class2); ?>"><?php echo esc_html($btntext); ?></button></a></p>
            <?php
            }else{
                
            }

        }

    ?>
                </div>
	    </div>

    <?php
        echo $after_widget;

	}

	function update( $new_instance, $old_instance )
	{
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['campaign'] = strip_tags( $new_instance['campaign'] );

		/* No need to strip tags for.. */
        $instance['belowHTML'] =  $new_instance['belowHTML'] ;
        $instance['aboveHTML'] =  $new_instance['aboveHTML'] ;
        $instance['link'] =  strip_tags( $new_instance['link'] ) ;
        $instance['linkurl'] =  strip_tags( $new_instance['linkurl'] ) ;
        $instance['btnclass'] =  strip_tags( $new_instance['btnclass'] );
        $instance['btnstyle'] =  strip_tags( $new_instance['btnstyle'] );
        $instance['btntext'] = $new_instance['btntext'];

		$instance['border_radius1'] = $new_instance['border_radius1'];
		$instance['border_radius2'] = $new_instance['border_radius2'];
		$instance['border_radius3'] = $new_instance['border_radius3'];
		$instance['border_radius4'] = $new_instance['border_radius4'];

		$instance['boxshadow_color1'] = $new_instance['boxshadow_color1'];
		$instance['boxshadow_color2'] = $new_instance['boxshadow_color2'];
		$instance['boxshadow_color3'] = $new_instance['boxshadow_color3'];
		$instance['boxshadow_color4'] = $new_instance['boxshadow_color4'];
		$instance['boxshadow_color5'] = $new_instance['boxshadow_color5']; //RGBA

		$instance['barcolor']        = $new_instance['barcolor'] ;
		$instance['well_background'] = $new_instance['well_background'];
		$instance['well_shadows']    = $new_instance['well_shadows'] ;

		$instance['stripes']         = $new_instance['stripes'] ;
		$instance['pulse']           = $new_instance['pulse'];
		$instance['animated_stripes'] = $new_instance['animated_stripes'];
		$instance['percentage']       = $new_instance['percentage'];

		return $instance;
	}


	function form( $instance )
	{

     // Check values
    if( $instance )
	{
       $title = esc_attr($instance['title']);
       $campaign = esc_attr($instance['campaign']);
       $belowHTML = $instance['belowHTML'] ;
	   $aboveHTML = $instance['aboveHTML'] ;

       $link = esc_attr($instance['link']);
       $linkurl = esc_attr($instance['linkurl']);
       $btnclass = esc_attr($instance['btnclass']);
       $btnstyle = esc_attr($instance['btnstyle']);
       $btntext = esc_attr($instance['btntext']);

	   $border_radius1 = $instance['border_radius1'];
	   $border_radius2 = $instance['border_radius2'];
	   $border_radius3 = $instance['border_radius3'];
	   $border_radius4 = $instance['border_radius4'];

		$boxshadow_color1 = $instance['boxshadow_color1'];
		$boxshadow_color2 = $instance['boxshadow_color2'];
		$boxshadow_color3 = $instance['boxshadow_color3'];
		$boxshadow_color4 = $instance['boxshadow_color4'];
		$boxshadow_color5 = $instance['boxshadow_color5']; //RGBA

		$barcolor        = $instance['barcolor'] ;
		$well_background = $instance['well_background'];
		$well_shadows    = $instance['well_shadows'] ;

		$stripes          = $instance['stripes'] ;
		$pulse            = $instance['pulse'];
		$animated_stripes = $instance['animated_stripes'];
		$percentage       = $instance['percentage'];
     }

     if( !isset($instance['title']) )
		$title = "Total Donations Progress Bar";
     if( !isset($instance['campaign']) )
		$campaign = '';
     if( !isset($instance['belowHTML']) )
		$belowHTML = '';
	 if( !isset($instance['aboveHTML']) )
		$aboveHTML = '';
     if( !isset($instance['link']) )
		$link = '';
	if( !isset($instance['linkurl']) )
		$linkurl = '';
     if( !isset($instance['btnclass']) )
		$btnclass = '';
     if( !isset($instance['btnstyle']) )
		$btnstyle = '';
     if( !isset($instance['btntext']) )
		$btntext = '';
	 if( !isset($instance['border_radius1']) )
		$border_radius1 = 8;
	 if( !isset($instance['border_radius2']) )
		$border_radius2 = 8;
	 if( !isset($instance['border_radius3']) )
		$border_radius3 = 8;
	 if( !isset($instance['border_radius4']) )
		$border_radius4 = 8;

	if( !isset($instance['boxshadow_color1']) )
		$boxshadow_color1 = 8;
	if( !isset($instance['boxshadow_color2']) )
		$boxshadow_color2 = 8;
	if( !isset($instance['boxshadow_color3']) )
		$boxshadow_color3 = 8;
	if( !isset($instance['boxshadow_color4']) )
		$boxshadow_color4 = 8;
	if( !isset($instance['boxshadow_color5']) )
		$boxshadow_color5 = '#969899'; //RGBA

	if( !isset($instance['barcolor']) )
		$barcolor        = '#428bca';
	if( !isset($instance['well_background']) )
		$well_background = '#bec7d3';
	if( !isset($instance['well_shadows']) )
		$well_shadows    = '#969899';

	if( !isset($instance['stripes']) )
		$stripes          = false;
	if( !isset($instance['pulse']) )
		$pulse            = false;
	if( !isset($instance['animated_stripes']) )
		$animated_stripes = false;
	if( !isset($instance['percentage']) )
		$percentage       = false;

	if( !isset($instance['form_id']) )
		$form_id = '';

	if( !isset($instance['form_url']) )
		$form_url = '';

?>

	<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title of the progress bar:', 'localization') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />
		</p>

<?php
    $obj = new MIGLA_CAMPAIGN;
    $campaigns = $obj->get_all_info(get_locale());
?>

    <p><label ><?php _e('Current Campaign : '  , 'localization') ?></label>
        <label ><?php $c_name = str_replace( "[q]", "'", $campaign ); echo $c_name; ?></label>
    </p>

    <p><label ><?php _e('Choose a campaign to show :', 'localization') ?></label></p>

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

                if( $cmp['id'] == $campaign  ){
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

<p style='display:none'><label ><?php _e('Form ID : '  , 'localization') ?></label>
<label ><input disabled type='text' class='mg_form_id' value='<?php echo $form_id; ?>'
 name='<?php echo $this->get_field_name( 'form_id' ); ?>' id='<?php echo $this->get_field_id( 'form_id' )?>' ></label></p>

<p style='display:none'><label ><?php _e('URL : '  , 'localization') ?></label>
<label class='mg_form_url' name='<?php echo $this->get_field_name( 'form_url' ); ?>' id='<?php echo $this->get_field_id( 'form_url' )?>'>
<?php echo $form_url; ?></label></p>

		<p>
		  <label ><?php _e('Border:', 'localization') ?></label>
		</p>

		<p> <label ><?php _e('Top Left:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius1' ); ?>" name="<?php echo $this->get_field_name( 'border_radius1' ); ?>" value="<?php echo $border_radius1; ?>" /></p>

          <p><label ><?php _e('Top Right:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius2' ); ?>" name="<?php echo $this->get_field_name( 'border_radius2' ); ?>" value="<?php echo $border_radius2; ?>" /></p>

          <p><label ><?php _e('Bottom Left:', 'localization') ?></label>
		  <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius3' ); ?>" name="<?php echo $this->get_field_name( 'border_radius3' ); ?>" value="<?php echo $border_radius3; ?>" />
          </p>

          <p><label ><?php _e('Bottom Right:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'border_radius4' ); ?>" name="<?php echo $this->get_field_name( 'border_radius4' ); ?>" value="<?php echo $border_radius4; ?>" />
		</p>

		<p>
		  <label ><?php _e('Well Box Shadow:', 'localization') ?></label>
		</p>

		<p> <label ><?php _e('H-Shadow:', 'localization') ?></label>
          <input maxlength="3" size="3" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color1' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color1' ); ?>" value="<?php echo $boxshadow_color1; ?>" /></p>

          <p> <label ><?php _e('V-Shadow:', 'localization') ?></label>
          <input maxlength="3" size="3" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color2' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color2' ); ?>" value="<?php echo $boxshadow_color2; ?>" /></p>

         <p> <label ><?php _e('Blur:', 'localization') ?></label>
          <input maxlength="3" size="3" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color3' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color3' ); ?>" value="<?php echo $boxshadow_color3; ?>" /></p>

         <p> <label ><?php _e('Spread:', 'localization') ?></label>
          <input maxlength="2" size="2" type="number" id="<?php echo $this->get_field_id( 'boxshadow_color4' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color4' ); ?>" value="<?php echo $boxshadow_color4; ?>" /></p>

          <p> <label ><?php _e('Well Box Shadow Color:', 'localization') ?></label>
          <input maxlength="7" size="7" type="text" class='migla-color-field widefat' id="<?php echo $this->get_field_id( 'boxshadow_color5' ); ?>" name="<?php echo $this->get_field_name( 'boxshadow_color5' ); ?>" value="<?php echo $boxshadow_color5; ?>" />
		</p>

 		<p>
		  <label ><?php _e('Bar Color:', 'localization') ?></label>
		  <input type="text" maxlength="7" size="7" class='migla-color-field widefat' id="<?php echo $this->get_field_id( 'barcolor' ); ?>" name="<?php echo $this->get_field_name( 'barcolor' ); ?>" value="<?php echo $barcolor; ?>" />
		</p>

 		<p>
		  <label ><?php _e('Well Background:', 'localization') ?></label>
		  <input type="text" maxlength="7" size="7" class='migla-color-field widefat' id="<?php echo $this->get_field_id( 'well_background' ); ?>" name="<?php echo $this->get_field_name( 'well_background' ); ?>" value="<?php echo $well_background; ?>" />
		</p>

 		<p>
		  <label ><?php _e('Well Shadows:', 'localization') ?></label>
		  <input type="text" maxlength="7" size="7" class='migla-color-field widefat' id="<?php echo $this->get_field_id( 'well_shadows' ); ?>" name="<?php echo $this->get_field_name( 'well_shadows' ); ?>" value="<?php echo $well_shadows; ?>" />
		</p>

 		<p>
		  <label ><?php _e('Stripes Effect:', 'localization') ?></label>
                 <?php
         if( $stripes == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'stripes' )."' name='".$this->get_field_name( 'stripes' )."' value='".$stripes."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'stripes' )."' name='".$this->get_field_name( 'stripes' )."' value='".$stripes."' />";
         }
                 ?>
		</p>
 		<p>
		  <label ><?php _e('Animated Stripes Effect (stripes must be on):', 'localization') ?></label>
                <?php
         if( $animated_stripes == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'animated_stripes' )."' name='".$this->get_field_name( 'animated_stripes' )."' value='".$animated_stripes."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'animated_stripes' )."' name='".$this->get_field_name( 'animated_stripes' )."' value='".$animated_stripes."' />";
         }
                 ?>
		</p>
 		<p>
		  <label ><?php _e('Pulse Effect:', 'localization') ?></label>
                <?php
         if( $pulse == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'pulse' )."' name='".$this->get_field_name( 'pulse' )."' value='".$pulse."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'pulse' )."' name='".$this->get_field_name( 'pulse' )."' value='".$pulse."' />";
         }
                 ?>
		</p>
 		<p>
		  <label ><?php _e('Percentage:', 'localization') ?></label>
                <?php
         if( $percentage == 'on' ){
           echo "<input value='on' checked='checked' type='checkbox' id='".$this->get_field_id( 'percentage' )."' name='".$this->get_field_name( 'percentage' )."' value='".$percentage."' />";
         }else{
           echo "<input value='on' type='checkbox' id='".$this->get_field_id( 'percentage' )."' name='".$this->get_field_name( 'percentage' )."' value='".$percentage."' />";
         }
                 ?>
		</p>

       <br><br>
      <?php if( $link == 'on'){  ?>
        <div><input type="checkbox" checked="checked" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }else{  ?>
        <div><input type="checkbox" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
        <label>Add link button ? </label></div>
      <?php }  ?>

        <br><div><label>Link URL</small></label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'linkurl' ); ?>" name="<?php echo $this->get_field_name( 'linkurl' ); ?>" value="<?php echo $linkurl; ?>"></input></div>

        <br><div><label>Add a css class on button: <small>(theme button only)</small></label>
        <input input='text' class='widefat' type='text' id="<?php echo $this->get_field_id( 'btnclass' ); ?>" name="<?php echo $this->get_field_name( 'btnclass' ); ?>" value="<?php echo $btnclass; ?>"></input></div>

     <br><label>Choose a button style:</label>
     <select id="<?php echo $this->get_field_id( 'btnstyle' ); ?>" name="<?php echo $this->get_field_name( 'btnstyle' ); ?>" class="widefat migla_select">
     <?php if( $btnstyle == "GreyButton" ) { ?>
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
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'btntext' ); ?>" name="<?php echo $this->get_field_name( 'btntext' ); ?>" value="<?php echo $btntext; ?>" />
      </p>

      <p> <label for="<?php echo $this->get_field_id('aboveHTML'); ?>"><?php _e('Add HTML or Plain Text above:', 'localization') ?></label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'aboveHTML' ); ?>" name="<?php echo $this->get_field_name( 'aboveHTML' ); ?>"  ><?php echo $aboveHTML; ?></textarea><small><?php _e('shortcodes allowed: [campaign] [percentage] [backers] [amount]', 'localization') ?></small></p>

       <p> <label for="<?php echo $this->get_field_id('belowHTML'); ?>"><?php _e('Add HTML or Plain Text below:', 'localization') ?></label>
       <textarea  class="widefat"  id="<?php echo $this->get_field_id( 'belowHTML' ); ?>" name="<?php echo $this->get_field_name( 'belowHTML' ); ?>"  ><?php echo $belowHTML; ?></textarea><small><?php _e('shortcodes allowed: [campaign] [percentage] [backers] [amount]', 'localization') ?></small></p>
	

<script type="text/javascript">
 
    ( function( $ ){
        function initColorPicker( widget ) {
            widget.find( '.migla-color-field' ).not('[id*="__i__"]').wpColorPicker( {
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
            $( '.widget-inside:has(.migla-color-field)' ).each( function () {
                initColorPicker( $( this ) );
            } );
        } );
 
    }( jQuery ) );
 
</script>

    <?php     
	}
}
}
?>
