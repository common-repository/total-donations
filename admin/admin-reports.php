<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ( !defined( 'ABSPATH' ) ) exit;

if ( is_user_logged_in() )
{
    $objO = new MIGLA_OPTION;
    $report_download = false;

    if( isset($_GET['xls']) && sanitize_text_field($_GET['xls']) == $objO->get_option( 'migla_listen' ) ){
        $report_download =  true;
    }
    if( isset($_GET['csv']) && sanitize_text_field($_GET['csv']) == $objO->get_option( 'migla_listen' ) ){
        $report_download =  true;
    }

    if( $report_download )
    {
    	if (ob_get_length()) ob_end_clean();

    		$start_date_for_load = '';
    		$end_date_for_load = '';
     		$filename = "Report_";

    	if( isset($_GET['sd']) && !empty( sanitize_text_field($_GET['sd']) ) ){
    			$start_date_for_load =  sanitize_text_field($_GET['sd']);
    			$arr = explode( "/", $start_date_for_load );

    			if( count($arr) == 3 ){
    				$start_date_for_load = $arr[2]."-".$arr[0]."-".$arr[1];
    			}
    			$filename .= $start_date_for_load;
    		}

    	if( isset($_GET['ed']) && !empty( sanitize_text_field( $_GET['ed'] ) ) ){
    			$end_date_for_load = sanitize_text_field($_GET['ed']);
    			$arr = explode( "/", $end_date_for_load );

    			if( count($arr) == 3 ){
    				$end_date_for_load = $arr[2]."-".$arr[0]."-".$arr[1];
    			}

    		    if(!empty($filename)){
        		    $filename .= "_to_";
    			}
    			$filename .= $end_date_for_load;
    		}

        if( isset($_GET['xls']) && sanitize_text_field($_GET['xls']) == $objO->get_option( 'migla_listen' ) ){
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=".$filename.".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            $left = '';
            $right = '';
            $delimiter = "\t";
            $newline = "\r\n";
        }
        if( isset($_GET['csv']) && sanitize_text_field($_GET['csv']) == $objO->get_option( 'migla_listen' ) ){
            header('Content-Type: text/csv; charset=utf-8');
        	header('Content-Disposition: attachment; filename='.$filename.'.csv');

            $left = '"';
            $right = '"';
            $delimiter = ";";
            $newline = "\r\n";
        }

    	$origin_locale = get_locale();

    	$objL = new MIGLA_LOCAL;

        $status = 1;

        if( isset($_GET['p']) && $_GET['p'] == 'all' )
        {
            $status = '';
        }

        $objD = new CLASS_MIGLA_DONATION;
        $objM = new MIGLA_MONEY;
        $objG = new MIGLA_GEOGRAPHY;
        $objF = new CLASS_MIGLA_FORM;

        $donations = $objD->get_donation_detail_bydate(
        	        							$start_date_for_load,
        			                          	$end_date_for_load,
        			                          	'',
        			                          	'1',
        			                          	'DESC',
        			                          	'date_created',
        			                          	$status,
        			                          	0
        			                          );

        $custom_label = array();
        $k = 0;
        $custom_field = array();

        $rec = 0;
        $colnum = 0;
        $content = '';
        $header = array();
        $header_map = array();
        $manual_map = array();
        $hi = 0;
        $country = '';

        $lines = array();

        $header = $objF->retrive_header_for_export();

        $objCF = new CForm_Fields;
		$field_labels = $objCF->map_custom_field_label();

        if( !empty($donations) )
        {
            $j = 0;
            $colN = count($header);

            foreach( $header as $line )
            {
                $column_name = str_replace("miglad_","", $line);

                if( $column_name == 'date_created' ){
                    $column_name = 'date';
                }else if( isset($field_labels[$column_name]) ){
                    $column_name = $field_labels[$column_name];
                }

                echo $left. str_replace( ";", "", $column_name ) .$right;

               if( $j < $colN - 1 ){
                	echo $delimiter;
               }else{
               }

               $j++;
            }

        	echo $newline;

        foreach($donations as $row )
        {
                	       $donation_id = $row['id'];
                	       $country = $objD->get_column($donation_id, 'country');
                	       $state = $objD->get_donationmeta( 'miglad_state' , $donation_id);
                	       $province = $objD->get_donationmeta( 'miglad_province' , $donation_id);

            	            $m = 0;

            	            foreach( $header as $col )
            	            {
            	                $show_value = '';

            	                if( isset($row[$col]) )
            	                {
            	                    $val = $row[$col];

            	                    if( preg_match( '/^a:\d+:{.*?}$/', $val ) )
                	                {
                                        $arrays = (array)unserialize($val);
                                        $_val = "";
                                        foreach($arrays as $item){
                                            if( !empty($_val) ){
                                                $_val .= " ";
                                            }
                                            $_val .= $item;
                                        }

                                        $show_value = $_val;
                                    }else{

                                        if( $col == 'amount' || $col == 'miglad_amount' )
                                        {
                                            $show_value = number_format($val, 2);
                                        }else if( $col == 'date_created' )
                                        {
                                            $date_array = explode(" ", $val);

                                            if(isset($date_array[0])){
                                                $val = $date_array[0];
                                            }else{
                                            }

            	                            $show_value = $val;

                                        }else if( $col == 'miglad_state' )
                                        {

                                            if( $country == 'United States' ){
                                                $val = $state;
                                            }else{
                                                $val = '';
                                            }

                                            $state = $val;
            	                            $show_value = $val;

                                        }else if( $col == 'miglad_province' )
                                        {

                                            if( $country == 'Canada' ){
                                                $val = $province;
                                            }else{
                                                $val = '';
                                            }

                                            $province = $val;
            	                            $show_value = $val ;

                                        }else if( $col == 'miglad_currency' )
                                        {
                                            if( empty($val) ){

                                                $val = $objM->get_default_currency();
                                            }

                                            $show_value = $val;

                                        }else{

                                            $val = str_replace(","," ", $val);
                                            $val = str_replace("\t"," ", $val);
                                            $val = str_replace("\r"," ", $val);
                                            $val = str_replace("\n"," ", $val);

                                            if( $country == 'Poland' ){
                                                    setlocale(LC_CTYPE, 'cs_CZ');
                                                    $val = iconv('UTF-8', 'ASCII//TRANSLIT', $val);
                                                    setlocale(LC_CTYPE, $origin_locale);
                                            }else{
                                                    $val = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $val);
                                            }

            	                            $show_value = $val;
                                        }
                                    }

            	                }else{

                                    if( $col == 'time' )
                                    {
                                        $val = $row['date_created'];

                                        $date_array = explode(" ", $val);

                                        if(isset($date_array[1]))
                                        {
                                            $val = $date_array[1];
                                        }else{
                                        }

            	                        $show_value = $val;

                                    }else if( $col == 'country_code' )
                                    {
                                        $val = $objG->get_country_code( $country );

                                        $show_value = $val ;

                                    }else if( $col == 'state_code' )
                                    {
                                        $val = '';

                                        if( $country == 'United States' ){
                                            $val = $objG->get_state_code( $state );
                                        }

                                        $show_value = $val ;

                                    }else if( $col == 'province_code' )
                                    {
                                        $val = '';

                                        if( $country == 'Canada' ){
                                            $val = $objG->get_province_code( $province );
                                        }

                                        $show_value = $val ;

                                    }else{
            	                        $val = ' ';
            	                        $show_value = $val;
                                    }
            	                }


            	                echo $left. str_replace(";","", $show_value ) . $right;


            	                if( $m < $colN ){
            	                    echo $delimiter;
            	                }

            	                $m++;
            	            }

            	            echo $newline;

            	            $rec++;
            	        }
        }//if not empty

        $j = 0;

    	exit();
    }
}

class migla_reports_class extends MIGLA_SEC
{
    public $Money;
    private $current_user_caps;

	function __construct()
	{
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 5 );
	}

	function menu_item()
	{
		add_submenu_page(
			   'migla_donation_menu_page',
			   __( 'Online Donations', 'migla-donation' ),
			   __( 'Online Donations', 'migla-donation' ),
			   'read_reports',
			   'migla_reports_page',
			   array( $this, 'menu_page' )
		);

	}

	function menu_page()
	{
	    $data = get_userdata( get_current_user_id() );

        if ( is_object( $data) ) {
            $this->current_user_caps = $data->allcaps;
        }

	  	if ( is_user_logged_in() )
		{
	        $this->create_token( 'migla_reports_page', session_id() );
    	    $this->write_credentials( 'migla_reports_page', session_id() );
		    $this->Money = new MIGLA_MONEY;

            $objO = new MIGLA_OPTION;

			if( isset($_GET['pid']) && isset($_GET['fid']) && isset($this->current_user_caps['edit_reports']) )
			{
				$this->get_edit_form( sanitize_text_field($_GET['pid']), sanitize_text_field($_GET['fid']) );
			}else if( isset($_GET['rep']) && sanitize_text_field($_GET['rep']) == 'yes' )
			{
				$this->get_display_report();
			}else{
				$this->home_report();
			}

        }else{
            $error = "<div class='wrap'><div class='container-fluid'>";
            $error .= "<h2 class='migla'>";
            $error .= __("You do not have sufficient permissions to access this page. Please contact your web administrator","migla-donation"). "</h2>";
            $error .= "</div></div>";

            wp_die( __( $error , 'migla-donation' ) );
        }

	}

    function home_report()
    {
     ?>
     <div class='wrap'>
        <div class='container-fluid'>
            <input type="hidden" id="mg_page" value="home">

        	<h2 class='migla'><?php echo __( "Online Donations","migla-donation");?></h2>
            <div class='row form-horizontal'>
            	<div class='col-md-6 col-lg-6 col-xl-12'>
                    <form id="mg_report_filter_form" action='<?php echo get_admin_url(). "admin.php?page=migla_reports_page"; ?>' method="GET">
                	<?php
                		if( isset($_GET['page']) ){
                		?>
                			<input type="hidden" name="page" value="<?php echo esc_html($_GET['page']);?>">
                		<?php
                		}
                	?>
                		<input type="hidden" id="migla_in_start_date" name="start_date">
                		<input type="hidden" id="migla_in_end_date" name="end_date">
                		<input type="hidden" name="rep" value="yes">
                		<input type="hidden" id='mg_frm_post_period' name="p" value="active">
                	</form>
            	<section class="panel panel-featured-left panel-featured-primary">
            		<header class="panel-heading"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            		    <div class="panel-actions"><a class="fa fa-caret-down" data-toggle="collapse" data-parent=".panel" href="#collapseOne" aria-expanded="true"></a>
            		    </div>
            		    <h2 class="panel-title"><i class="fa fa-arrow-right"></i><?php echo __("Choose the Date Range to Load","migla-donation");?></h2>
            		</header>
            		<div id="collapseOne" class="panel-body collapse show">
            			<div class="widget-summary">
            				<div class="widget-summary-col-icon">
            					<div class="summary-icon bg-color-teal"><i class="fa fa-fw fa-calendar"></i></div>
            				</div>
            				<div class="widget-summary-col">
            					<div class="summary">
            						<h4 class="title"> <?php echo __("Pick a date range for the reports:","migla-donation");?></h4>
            	    				<div class="info">
            							<div data-plugin-datepicker='' class='input-daterange input-group migla-date-range-picker mg_date-range-filter'>
            			                <span class='input-group-addon migla-date-range-icon'>
            			                    <i class='fa fa-calendar'></i></span>
                            			<input type='text' class='form-control' placeholder='mm/dd/yyyy' id='migla_start_date'>
                            				<span class='input-group-addon migla-to-date'>to</span>
                            			<input type='text' class='form-control' placeholder='mm/dd/yyyy' id='migla_end_date'>
            		                    </div>
                                    	<button class="mbutton btn" id="mg_report_filter_submit"><i class="fa fa-filter"></i><?php echo __(" Load This Date Range","migla-donation");?></button>
                                    <?php
        							if(  isset($this->current_user_caps['approve_donation'])  )
        							{
        							?>
                                        <label for="mg-show-pending"><input type="checkbox" id="mg-show-pending"> <?php echo __(" Show pending payments?","migla-donation");?></label>
                                    <?php
        							}
                                    ?>
                                    </div>
            					</div>
            				</div>
            			</div>
            		</div>

            	</section>
            	</div>
            	<form id='migla_report_form' action='<?php echo get_admin_url(). "admin.php?page=migla_reports_page"; ?>' method='post' style='display:none'>
                	<input type='hidden' id='mg_frm_choice' name='migla_export_choice' value='' >
                	<input type='hidden' id='mg_frm_post_type' name='post_type' value='' >
                	<input type='hidden' id='mg_frm_post_id' name='post_id' value='' >
                	<input type='hidden' id='mg_frm_filtered' name='is_filtered' value='' >
                	<input id='mg_page_submit' class='button' type='submit' />
            	</form>
            </div>
        </div>
    </div>
    <?php
    }

    function month_name( $m ){
        $name = '';

        switch ($m)
        {
            case 1 :
                $name = 'January';
                break;
            case 2 :
                $name = 'February';
                break;
            case 3 :
                $name = 'March';
                break;
            case 4 :
                $name = 'April';
                break;
            case 5 :
                $name = 'May';
                break;
            case 6 :
                $name = 'Juny';
                break;
            case 7 :
                $name = 'July';
                break;
            case 8 :
                $name = 'August';
                break;
            case 9 :
                $name = 'September';
                break;
            case 10 :
                $name = 'October';
                break;
            case 11 :
                $name = 'November';
                break;
            case 12 :
                $name = 'December';
                break;
        }

        return $name;
    }

    function num_days( $m ){
        $name = 30;

        switch ($m)
        {
            case 1 :
                $name = 31;
                break;
            case 2 :
                $name = 29;
                break;
            case 3 :
                $name = 31;
                break;
            case 4 :
                $name = 30;
                break;
            case 5 :
                $name = 31;
                break;
            case 6 :
                $name = 30;
                break;
            case 7 :
                $name = 31;
                break;
            case 8 :
                $name = 31;
                break;
            case 9 :
                $name = 30;
                break;
            case 10 :
                $name = 31;
                break;
            case 11 :
                $name = 30;
                break;
            case 12 :
                $name = 31;
                break;
        }

        return $name;
    }

	function get_display_report()
	{
		global $wpdb;
		$table_row 	= array();
		$row 		= 0;
		$filter 	= '';
		$filter_id 	= array();
		$y = 0;

		$total_amount = 0.0;
		$table_total = 0.0;
        $td_total = 0.0;

		$objG = new MIGLA_GEOGRAPHY;

		if( isset($_GET['start_date']) && !empty( sanitize_text_field($_GET['start_date']) ) ){
			$start_date_for_load =  sanitize_text_field($_GET['start_date']);
			$arr = explode( "/", $start_date_for_load );
			$start_date_for_load = $arr[2]."-".$arr[0]."-".$arr[1];
		}else{
			$start_date_for_load = '';
		}

		if( isset($_GET['end_date']) && !empty( sanitize_text_field($_GET['end_date']) ) ){
			$end_date_for_load = sanitize_text_field($_GET['end_date']);
			$arr = explode( "/", $end_date_for_load );
			$end_date_for_load = $arr[2]."-".$arr[0]."-".$arr[1];
		}else{
			$end_date_for_load = '';
		}

		$obj = new CLASS_MIGLA_DONATION;

        $status = 1;
        $pending = '';

		if( isset( $_GET['p'] ) )
		{
		    if( sanitize_text_field( $_GET['p'] ) == 'all' ){
		        $status = '';
		    }else{

		    }

		    $pending = sanitize_text_field($_GET['p']);
		}


        	$donations = $obj->get_donation_bydate(
        							$start_date_for_load,
		                          	$end_date_for_load,
		                          	'',
		                          	'1',
		                          	'DESC',
		                          	'date_created',
		                          	$status
		                          );


    	$objM = new MIGLA_MONEY;

        $role_delete = isset($this->current_user_caps['delete_reports']);
        $role_edit = isset($this->current_user_caps['edit_reports']);

        $span = 4;

        if( !$role_delete ){
            $span = $span - 1;
        }
        if( !$role_edit ){
            $span = $span - 1;
        }

	?>

<div class='wrap'>
	<div class='container-fluid'>

    	<input type='hidden' id='mg_page' value='report'>
    	<div id='mg_load_image' style='display:none !important'><img src='<?php echo Totaldonations_DIR_URL."assets/images/gif/blue-loader.gif";?>'></div>

    	<h2 class='migla'><?php echo __( "Online Donations","migla-donation");?></h2>

    	<div class="row">
    		<div class="col-sm-6">
    			<a class="mg_go-back"><i class="fa fa-fw fa-arrow-left"></i><?php echo __('Go back to Reports Filter', 'migla-donation');?></a>
    		</div>
    		<div class="col-sm-6"></div>
    	</div>
        <br>
        <br>

    <div class='tab-content nav-pills-tabs'>

        <div id='section1' class='tab-pane active'>

        	<div class='row form-horizontal'>

        		<div class='col-sm-12'>
        			<section class='panel' id="report-table-div">
        				<header class='panel-heading'>
        					<div class='panel-actions'>
        					    <a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseTbl' aria-expanded='true'></a>
        					</div>
        					<h1 class='panel-title'><?php echo __('Reports', 'migla-donation');?></h1>
        				</header>
        				<div id='collapseTbl' class='panel-body collapse show'>
        					 <div id='datatable-default_wrapper' class='dataTables_wrapper no-footer'>
        						<div class='table-responsive'>
        							<table id='miglaReportTable' class='display' cellspacing='0' width='100%'>
            							<thead>
            							    <tr>
            							<th class='' style='<?php if( !$role_delete) echo "display:none";?>'><?php echo __("Delete","migla-donation");?></th>
            							<th class='' style='<?php if( !$role_edit) echo "display:none";?>'><?php echo __("Update","migla-donation");?></th>
            							<th class='detailsHeader' style='width:15px;'><?php echo __("Detail","migla-donation");?></th>
            							<th class='th_date'><?php echo __("Date","migla-donation");?></th>
            							<th class=''><?php echo __("FirstName","migla-donation");?></th>
            							<th class=''><?php echo __("LastName","migla-donation");?></th>
            							<th class=''><?php echo __("Campaign","migla-donation");?></th>
            							<th class=''><?php echo __("Amount","migla-donation");?></th>
            							<th class=''><?php echo __("Country","migla-donation");?></th>
            							<th class=''><?php echo __("Transaction","migla-donation");?></th>
            							<th class='' style='display:none;'></th>
                          <th class='' style='display:none;'></th>
                          <th class='' style='display:none;'></th>
            							</tr>
            						</thead>
            						<tfoot>
            						   <tr>
            							<th id='f0' colspan='<?php echo esc_attr($span);?>'></th>
            						   <th class="td_foot" id='f<?php echo esc_attr($span);?>'>
            								<input type="text" placeholder="<?php echo __("FirstName","migla-donation");?>" name="<?php echo __("FirstName","migla-donation");?>" />
            							</th>
            						   <th class="td_foot" id='f<?php echo esc_attr($span+1);?>'>
            								<input type="text" placeholder=" <?php echo __("LastName","migla-donation");?>" name=" <?php echo __("LastName","migla-donation");?>" />
            						   </th>
            						   <th class="td_foot" id='f<?php echo esc_attr($span+2);?>'>
            								<input type="text" placeholder="<?php echo __("Campaign","migla-donation");?>" name="<?php echo __("Campaign","migla-donation");?>" />
            						   </th>
            						   <th class="td_foot" id='f<?php echo esc_attr($span+3);?>'>
            								<input type="text" placeholder="<?php echo __("Amount","migla-donation");?>" name="<?php echo __("Amount","migla-donation");?>" />
            						   </th>
            						   <th class="td_foot" id='f<?php echo esc_attr($span+4);?>'>
            								<input type="text" placeholder="<?php echo __("Country","migla-donation");?>" name="<?php echo __("Country","migla-donation");?>" />
            						   </th>
            						   <th class="td_foot" id='f<?php echo esc_attr($span+5);?>'>
            								<input type="text" placeholder="<?php echo __("Transaction","migla-donation");?>" name="<?php echo __("Transaction","migla-donation");?>" />
            						   </th>
            						    <th style='display:none;' id='f<?php echo esc_attr($span+6);?>'></th>
            						    <th style='display:none;' id='f<?php echo esc_attr($span+6);?>'></th>
                                        <th style='display:none;' id='f<?php echo esc_attr($span+8);?>'></th>
            						   </tr>
                                    </tfoot>
            						<tbody>
            						<?php
            						$objC = new MIGLA_CAMPAIGN;
            						$objCmp = $objC->get_all_info_orderby( get_locale() );

            					if( !empty($donations) )
            		            {
            		                    $lineNumber = 0;
                		                foreach($donations as $row)
                		                {
                		                    $donation_date = $row['date_created'];

            								$cmp = $row['campaign'];
            								if($cmp == 0)
            								{
            								    $cmp_name = $objC->get_undesignated();
            								    $formID = 0;
            								}else{
                								    if(isset($objCmp[$cmp])){
                								        $cmp_name = $objCmp[$cmp]['name'];
                								        $formID = $obj->get_donationmeta( 'miglad_form_id', $row['id']);
                								    }else{
                								        $cmp_name = $objC->get_undesignated();
                								        $cmp = 0;
                								        $formID = 0 ;
                								    }
            								}

                                    	?>
            							<tr>

            							<?php
            							if( $role_delete )
            							{
            							?>
            							<td>
            							    <div class="removeColumn" id="removeCol<?php echo esc_attr($row['id']);?>">
            									<input class="migla_post_id" type="hidden" value="<?php echo esc_html($row['id']);?>"/>
            									<i class='fa fa-trash'></i>
            								</div>
            								<div class="removeColumnOff" style="display:none;" id="removeCol<?php echo esc_attr($row['id']);?>-Off">
            									<input class="migla_post_id" type="hidden" value="<?php echo esc_html($row['id']);?>"/>
            									<i class='fa fa-trash'></i>
            								</div>
            							</td>
            							<?php
            							}else{
            							?><td style="display:none;"><i class="fa fa-times-circle-o"></i></td>
            							<?php
            							}
            							?>

            							<?php
            							if( $role_edit )
            							{
            							?>
            						    <td class="updateColumn" id="updateCol<?php echo esc_attr($row['id']);?>" style="text-align:center;cursor:pointer;">
            								<input class="migla_post_id" type="hidden" value="<?php echo esc_html($row['id']);?>"/>
            											<?php ?>
            								<input class="migla_cmp_id" type="hidden" value="<?php echo esc_html($cmp);?>"/>
            								<input class="migla_frm_id" type="hidden" value="<?php echo esc_html($formID);?>"/>
            								<i class='fa fa-wrench updateColumn-i'></i>
            							</td>
            							<?php
            							}else{
            							?><td style="display:none;"><i class="fa fa-times-circle-o"></i></td>
            							<?php
            							}
            							?>

            							<td id="migla_record_row_<?php echo esc_attr($lineNumber);?>" class="">
            								<div id="<?php echo 'td-open-'.esc_attr($row['id']);?>" class="details-control"></div>
            								<div id="<?php echo 'td-close-'.esc_attr($row['id']);?>" class="close-details-control" style="display:none;"></div>

            								<input id="<?php echo 'td-open-'.esc_attr($row['id']).'row';?>" class="migla_rec_row" type="hidden" value="<?php echo esc_html($lineNumber);?>"/>

            								<input id="<?php echo 'td-open-'.esc_attr($row['id']).'pid';?>" class="migla_post_id" type="hidden" value="<?php echo esc_html($row['id']);?>"/>

            							</td>
            							<td><?php echo esc_html(date( get_option('date_format') . " g:i A" , strtotime( $row['date_created'] ) )); ?></td>
            							<td><?php echo esc_html($row['firstname']);?></td>
            							<td><?php echo esc_html($row['lastname']);?></td>
            							<td><?php echo esc_html($cmp_name);?></td>

            							<td><?php
            								    $res = $objM->full_format( $row['amount'], 2);

                                                $currency = $obj->get_donationmeta( 'miglad_currency', $row['id']);

                                                if( !empty($currency) ){
                                                    echo esc_html($res[0]) . '<span class="pull-right">'.$currency.'</span>';
                                                }else{
            								        echo esc_html($res[0]) . '<span class="pull-right">'.$res[1].'</span>';
                                                }

                                                if( $row['status'] == 1 ){
            								        $table_total +=  $row['amount'] ;
            								        $td_total +=  $row['amount'] ;
                                                }
            								    ?></td>
            							<td><?php echo esc_html($row['country']);?></td>
            							<td><?php echo esc_html(ucfirst( $row['gateway'] ));?></td>

                                  <td style='display:none;'>
                                    <?php
                                     if( $row['status'] == 1 ){
                                      echo esc_html($row['amount']);
                                    }else{
                                      echo 0;
                                    }
                                    ?>
                                  </td>
                                  <td style='display:none;'>0</td>
                                  <td style='display:none;'><?php echo esc_html(strtotime ($row['date_created']));?></td>
            			     </tr>
            				    <?php
            						  $lineNumber++;
            						}
            				}else{
            				}
	                           ?>
            				</tbody>
        				</table>

                        <!--tblfooter-->
                        <?php
                        	$icon = $this->Money->get_currency_symbol();
                        	$thousandSep 	= $this->Money->get_default_thousand_separator();
                        	$decimalSep 	= $this->Money->get_default_decimal_separator();
                            $placement 		= $this->Money->get_symbol_position();
                        	$showDecimal 	= $this->Money->get_show_decimal();

                            $objF = new CLASS_MIGLA_DONATION;
                        	$curtotal_show = number_format( $table_total, 2, $decimalSep, $thousandSep);

                        	$tdtotal_show = number_format( $td_total, 2, $decimalSep, $thousandSep);

                        	$before = '';
                        	$after = '';

                        	if( $placement == 'before' ){
                        		$before = $icon;
                        	}else{
                        		$after = $icon;
                        	}

                        ?>

                            <input type="hidden" id='thousand_separator' value="<?php echo esc_html($thousandSep);?>" />
                            <input type="hidden" id='decimal_separator' value="<?php echo esc_html($decimalSep);?>" />
                            <input type="hidden" id='decimal_placement' value="<?php echo esc_html($placement);?>" />
                            <input type="hidden" id='show_decimal' value="<?php echo esc_html($showDecimal);?>" />
                        	<input type="hidden" id='symbol' value="<?php echo esc_html($icon);?>" />
                        	<input type="hidden" id="migla_amount_before" value="<?php echo esc_html($before);?>">
                        	<input type="hidden" id="migla_amount_after" value="<?php echo esc_html($after);?>">

                            <div class='row datatables-footer'>
                            	<div class='col-sm-12 col-md-6'>
                            	<?php
                            	if(  isset($this->current_user_caps['delete_reports'])  )
                            	{
                            	?>
                            		<button class='btn rbutton' id='miglaRemove'  data-toggle="modal" data-target="#confirm-delete"><i class='fa fa-fw fa-times'></i><?php echo __("REMOVE ","migla-donation");?></button>
                            		<button class='btn mbutton' id='miglaUnselect'><i class='fa fa-fw fa-square-o '></i><?php echo __(" Unselect All ","migla-donation");?></button>
                            	<?php
                            	}
                            	?>
                            	</div>
                            	<div class='col-sm-12 col-md-6'>
                            	</div>
                            </div>
                            <!--tblfooter-->

        						</div>
        					</div>
        				</div>
        			</section>
        		</div>
        	</div>

            <!-- Grand Total start here -->
            <?php
            $sql_in =	join(",", $filter_id);
            $objO = new MIGLA_OPTION;
            $symbolType     = $objM->get_symbol_to_show();

            ?>

            <input type="hidden" id="miglaPageTotal" value="0">
            <input type="hidden" id="miglaWCTotal" value="0">
            <input type="hidden" id="miglaTDTotal" value="0">

            <div class="row">
            	<div class="col-sm-12 col-md-4">
            	<div class="tabs">
            		<ul class="nav nav-tabs nav-justified">
            			<li class="active">
            				<a class="text-center" data-toggle="tab" href="#thisreport" aria-expanded=""><i class="fa fa-star"></i> <?php echo __("This Report","migla-donation");?> </a>
            			</li>

            		</ul>

                	<div class="tab-content">
                		<div class="tab-pane  active" id="thisreport">
                			<div class="widget-summary">
                    			<div class="widget-summary-col-icon">
                    				<div class="summary-icon bg-primary <?php if($symbolType=='3-letter-code') echo "mg_country-code";?>"><?php echo $icon;?></div>
                    			</div>
                			    <div class="widget-summary-col">
                        			<div class="summary">
                        				<h4 class="title"><?php echo __(" Grand Total", "migla-donation");?></h4>
                        			    <div class="info">
                        				<strong class="amount" id="miglaOnTotalAmount2">
                        					<?php echo $before;?><label id="miglaOnTotalAmount2-number"><?php echo esc_html($curtotal_show);?></label><?php echo $after;?>
                        				</strong>
                        				<span class="text-primary"></span>
                        				</div>
                        				<div class="widget-footer-2"></div>
                        			</div>
                    			</div>
                            </div>
                        </div>

                        <div id="all" class="tab-pane">
                            <div class="widget-summary">
                				<div class="widget-summary-col-icon">
                					<div class="summary-icon bg-color-teal">
                					    <i class="fa fa-check"></i>
                					</div>
                				</div>
                				<div class="widget-summary-col">
                					<div class="summary">
                						<h4 class="title"><?php echo __(" Online Donations","migla-donation");?></h4>
                						<div class="info">
                						    <strong class="amount" id="miglaOnTotalAmount">
                							    <?php echo esc_html($before);?>
                							    <label id="miglaOnTotalAmount-number">
                								    <?php echo esc_html($tdtotal_show);?>
                								    <img style="display:none;" src="<?php echo Totaldonations_DIR_URL. 'assets/images/loading.gif';?>" >
                								</label>
                								<?php echo esc_html($after);?>
                							</strong>
                					    </div>
                					</div>
                				</div>
                		    </div>
                		</div>

                	</div>

            	</div>
            	</div>
                <!-- Grand Total end here -->
            	<!-- Date Range starts here -->

            <div class="col-sm-12 col-md-4">
            	<ul class="nav nav-tabs nav-justified mg_fake-tab">
            			</ul>
                  <div class="mg_active-date-reports">

            	<div class="tab-content">
            		<div class="tab-pane active" id="mg_date-range-reports">
            			<div class="widget-summary">
            			<div class="widget-summary-col-icon">
            				<div class="summary-icon bg-info">
            				<i class="fa fa-fw fa-calendar"></i>
            				</div>
            			</div>
            				<div class="widget-summary-col">
            			<div class="summary">
            				<h4 class="title"><?php echo __("Date Range Currently Displayed:","migla-donation");?></h4>
            			<div class="info">
            				<strong>
            					<label id="migla_date-range">
            					<?php
            					 if( isset($_GET['start_date']) && isset($_GET['end_date']) )
            					 {
                                    $startDate = sanitize_text_field($_GET['start_date']);
                                    $endDate = sanitize_text_field($_GET['end_date']);

              						if( $startDate !== '' &&  $endDate !== ''){
              							echo esc_html($startDate);
              							echo __(" to ","miglad-donation");
              							echo esc_html($endDate);
              						}else if( $startDate !== '' ){
              							echo __("Start from: ","migla-donation");
              							echo esc_html($startDate);
              						}else{
              							echo __("Below: ","migla-donation");
              							echo esc_html($endDate);
              						}
            					 }
            					 ?>
            					</label>
            				</strong>
            				<span class="text-primary"></span>
            				<?php
                        	if( isset($this->current_user_caps['export_reports']) )
                        	{
                        	?>  <div class="alignright"><a href="<?php echo get_admin_url(). "admin.php?page=migla_reports_page&csv=" . esc_html($objO->get_option( 'migla_listen' )."&p=".$pending."&sd=".$start_date_for_load."&ed=".$end_date_for_load); ?>" id="exportTable" class="export mbutton btn">
            				        <?php echo __("CSV Export","charitas");?></a>
            				    </div>
            			    <?php
                        	}
            			    ?>
            				</div>
            				<div class="widget-footer-2"></div>
            			</div>
                  </div>
              </div>
          </div>

            <div class="tab-pane">
            </div>
            </div>
          </div>
          </div>
            <!-- date range filter ends here -->
            </div>
        </div>
    </div>

        <div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
            <div class='modal-dialog mg_reports-delete'>
                <div class='modal-content'>

                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'>
    					<i class='fa fa-times'></i></button>
                        <h4 class='modal-title' id='myModalLabel'><?php echo __("Confirm Delete","migla-donation");?></h4>
                    </div>

    			<div class='modal-wrap clearfix'>
               		<div class='modal-alert'>
    					<i class='fa fa-times-circle'></i>
    				</div>

    			    <div class='modal-body'>
    	                <p><?php echo __("Are you sure you want to delete this? This cannot be undone","migla-donation");?></p>
    	                <div class='model-body-list'>

    	                </div>
    	            </div>
    			</div>

                    <div class='modal-footer'>
                        <button type='button' id='mg_report_remove_cancel' class='btn btn-default mbutton' data-dismiss='modal'><?php echo __("Cancel","migla-donation");?></button>
                        <button type='button' id='mg_report_remove' class='btn btn-danger danger rbutton' ><?php echo __("Delete","migla-donation");?></button>

                    </div>
                </div>
            </div>
        </div>

    <!--wrap fluid-->
        </div>
    </div>

	<form id="mg_report_filter_form" action='<?php echo get_admin_url(). "admin.php?page=migla_reports_page"; ?>' method="GET">
	<?php
		if( isset($_GET['page']) ){
		?>
			<input type="hidden" name="page" value="<?php echo esc_html($_GET['page']);?>">
		<?php
		}
	?>
	</form>

	<form id="mg_report_load_filter_form" action='<?php echo get_admin_url(). "admin.php?page=migla_reports_page"; ?>' method="GET">
	<?php
		if( isset($_GET['page']) ){
		?>
			<input type="hidden" name="page" value="<?php echo esc_html( $_GET['page'] );?>">
		<?php
		}
	?>
		<input type="hidden" name="start_date" value="<?php echo esc_html( $start_date_for_load );?>">
		<input type="hidden" name="end_date" value="<?php echo esc_html( $end_date_for_load );?>">
		<input type="hidden" name="migla_load_report" value="yes">
	</form>

	<form id='migla_edit_report_form' action='<?php echo get_admin_url(). "admin.php?page=migla_reports_page"; ?>' method='get' style='display:none'>
		<input type="hidden" name="page" value="<?php echo esc_html( $_GET['page'] );?>">
		<input type='hidden' id='migla_edit_form_post_id' name='pid' value='' >
		<input type='hidden' id='migla_edit_form_form_id' name='fid' value='' >
		<input type="hidden" name="sd" value="<?php echo esc_html( $_GET['start_date'] );?>">
		<input type="hidden" name="ed" value="<?php echo esc_html( $_GET['end_date'] );?>">
		<input id='mg_edit_report_submit' class='button' type='submit' />
	</form>

	<?php
	}

    function remove_array_by_key($array, $key_remove)
    {
        // Loop to find empty elements and
        // unset the empty elements
        foreach($array as $key => $value)
            if($key == $key_remove)
                unset($array[$key]);

        return $array;
    }

	function get_edit_form( $post_id, $form_id )
	{

		$objD = new CLASS_MIGLA_DONATION;
		$objC = new MIGLA_CAMPAIGN;
		$objF = new CLASS_MIGLA_FORM;

	    $Donation = $objD->get_detail( $post_id, 1 );

        if(isset($Donation['miglad_form_id']))
        {
            $form_id = $Donation['miglad_form_id'];
        }else{
            $form_id = "";
        }

        if(isset($Donation['campaign']))
        {
            $campaign_id = $Donation['campaign'];
        }else{
            $campaign_id = "";
        }

        if(isset($Donation['miglad_language']))
        {
            $_language_saved = $Donation['miglad_language'];
        }else{
            $_language_saved = "";
        }

		$FormStructure = $objF->get_column( $form_id, 'structure' );

		$Campaign = $objC->get_info_by_campaign_id( $campaign_id,
		                                          get_locale() );

	    $customVal = $objF->get_specific_meta_customval( $form_id,
	                                                    $_language_saved );

		?>
		<div class="wrap">
		<div class="container-fluid">
		<h2 class="migla"><?php echo __("Edit Online Donations", "migla-donation");?> 	</h2>

		<div class='row'>
		<div class='col-sm-6'>
			<a class='mg_go-back'>
			<i class='fa fa-fw fa-arrow-left'></i> <?php echo __("Go back to Online Donations", "migla-donation");?></a>
		</div>
		<div class='col-sm-6 text-right'>

		</div>
		</div>
		<br><br>

		<input type="hidden" id="mg_record_id" value="<?php echo esc_html( $post_id );?>">
		<input type="hidden" id="mg_form_id" value="<?php echo esc_html( $form_id );?>">
		<input type='hidden' id='mg_page' value='edit'>

	    	<?php

			$hidden_fields = array( 'miglad_paymentdata','miglad_customer_created',
									'miglad_subscription_type','miglad_paymentdata',
									'miglad_avs_response_text', 'miglad_avs_response_code',
									'miglad_province', 'miglad_state',
									'miglad_raw_data', 'payment_status',
									"repeating", "miglad_mg_add_to_milist"
								);

			$nomodif = array( "amount",
			                    "mailist",
			                    "anonymous",
			                    "repeating",
			                    "miglad_mg_add_to_milist",
			                    "campaign"
			                    );

			$formcols = array( "firstname",
			                    "lastname",
			                    "email",
			                    "amount",
			                    "mailist",
			                    "country",
			                    "anonymous",
			                    "repeating",
			                    "campaign");

			if( !empty($FormStructure) )
			{
			    $Structure = (array)unserialize($FormStructure);

			    $date_created = $objD->get_column( $post_id, 'date_created' );
			    $gtw = $objD->get_column( $post_id, 'gateway' );

			    $txn_id = $objD->get_donationmeta( 'miglad_transactionId', $post_id );
			    $cust_id = $objD->get_donationmeta( 'miglad_customer_id', $post_id );
			    $sub_id = $objD->get_donationmeta( 'miglad_subscription_id', $post_id );

			?>
            <section class="panel">
            	<header class='panel-heading'>
    				<div class='panel-actions'>
    					<a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseEditFormOnline' aria-expanded='true'></a>
    				</div>
    				<h2 class='panel-title'>
    					<div class='dashicons dashicons-edit'></div>
    					<?php echo __("Edit Online Donations","migla-donation");?>
    				<span class="panel-subtitle">
                    <?php echo __( "Donation ID", "migla-donation");?>
                        <?php echo esc_html(" ".$post_id);?>&nbsp; Form ID <?php echo esc_html($form_id);?></span> </h2>
    			</header>
    			<div id="collapseEditFormOnline" class="panel-body">

    			    <div class='row'>
                        <div class="col-sm-3 text-right">
                            <?php echo __( "Donation Date", "migla-donation");?>
                        </div>
                        <div class="col-sm-6">
                            <?php
                            echo esc_html(date( get_option('date_format') . " g:i A" , strtotime($date_created) ) . " ". get_option('timezone_string') . " | ". ucfirst($gtw) . " ");
                            ?>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <?php
                    if(!empty($txn_id) || !empty($sub_id) || !empty($cust_id) ){
                    ?>
    			    <div class='row'>
                        <div class="col-sm-3 text-right"><?php echo __( "Transaction ", "migla-donation");?></div>
                        <div class="col-sm-6">
                            <?php echo esc_html($txn_id); ?>
                            <?php
                            if( !empty($cust_id) ){
                            ?> 
                                <span class="panel-subtitle"><?php echo " | ". __( " Customer: ", "migla-donation") . esc_html($cust_id);?></span>
                            <?php
                            }
                            ?>
                            <?php
                            if( !empty($cust_id) ){
                                ?> <span class="panel-subtitle"><?php
                                echo " | ".  __( " Subscription: ", "migla-donation") . esc_html($sub_id);
                                ?></span><?php
                            }
                            ?>

                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <?php
                    } ?>

    		      <?php
    		      foreach( $Structure as $sections )
    		      {
    		      ?>
                    <div class='row'>
                        <div class="col-sm-3 text-right"><h3><?php echo __( esc_html($sections['title']), "migla-donation");?> </h3></div>
                        <div class="col-sm-6"></div>
                        <div class="col-sm-3"></div>
                    </div>
    		      <?php
    		            if( isset($sections['child']) && !empty($sections['child']) )
    		            {
        		            $children = (array)$sections['child'];

    		                foreach($children as $child)
    		                {
    		                        if( $child['code'] == 'miglad_' )
    		                        {
    		                            if( in_array( $child['id'], $formcols)  ){
    		                                $keycode = $child['id'];
    		                            }else{
    		                                $keycode = $child['code'].$child['id'];
    		                            }
    		                        }else{
    		                            $keycode = $child['uid'];
    		                        }

    		                        $savedValue = '';

    		                        if( isset($Donation[$keycode]) ){

    		                            if( $keycode == 'campaign' )
    		                            {
    		                                if(isset($Campaign['name'])){

    		                                    $savedValue = $Campaign['name'];


    		                                }else if($campaign_id == 0 || $campaign_id == '0'){
    		                                    $savedValue =  $objC->get_undesignated();
    		                                }else{
    		                                    $savedValue = $campaign_id;
    		                                }
    		                            }else if( $keycode == '' )
    		                            {

    		                            }else{
    		                                $savedValue = $Donation[$keycode];
    		                            }

    		                            $Donation = $this->remove_array_by_key($Donation, $keycode);
    		                        }else{

    		                        }

    		                        $is_nomodif = in_array( $keycode, $nomodif);

    		                        if(in_array( $keycode,$hidden_fields))
    		                        {

    		                        }else{
    		                    ?>
    		                    <div class='row '>
    				        	    <div class=" <?php if(!$is_nomodif) echo "mg-editdata-row" ?>">

    				                <input type="hidden" class="input_id" value="<?php echo esc_html( $keycode );?>">
    				                <input type="hidden" class="input_type" value="<?php echo esc_html( $child['type'] );?>">
    					            <div class="col-sm-3 col-xs-12">
    						        <label class='control-label text-right-sm text-center-xs'>
    						            <?php echo esc_html(str_replace("[q]","'", $child['label'])); ?>
    						        </label>
    						        </div>

    						        <?php
    						        if( $is_nomodif ){
    						            if($keycode == "amount"){
    						                $objM = new MIGLA_MONEY;
    						                $money = $objM->full_format( $savedValue, 2);

    						                $savedValue = $money[1]. " ". $money[0];
    						            }
    						        ?>
    						        <label class="col-sm-6 col-xs-12 mg_value-edit"><?php echo esc_html($savedValue);?></label>
    						        <?php
    						        }else if( $keycode == "country" )
    						        {
    						            $objG       = new MIGLA_GEOGRAPHY;
    						            $countries  = $objG->get_countries();
    						            $states     = $objG->get_USA_states();
    						            $provinces  = $objG->get_CA_provinces();

    						            $donor_state = $objD->get_donationmeta( "miglad_state", $post_id );
    						            $donor_province = $objD->get_donationmeta( "miglad_province", $post_id );

    						            ?>

    						            <div class="col-sm-6 col-xs-12">
    						            <div>
    		    				         <select id="<?php echo esc_attr( $keycode );?>" class="input_edit country-dd">
    		    				             <option value=""><?php echo __("Please Choose","migla-donation");?></option>
        						            <?php
        						            foreach( $countries as $code => $name ){
        						            ?>
        	                                  <option value="<?php echo esc_html( $name );?>" <?php if($savedValue == $name) echo "selected='selected'";?>>
        	                                      <?php echo esc_html( $name );?>
        	                                  </option>
        						            <?php
        						            } ?>
    						            </select>
    						            </div>
    						            <br/>
    						            <div id="<?php echo esc_attr($keycode)."-st";?>" class="div-state <?php if($savedValue!='United States') echo 'hideme';?>" >

    		    				         <select id="state-<?php echo esc_attr($keycode);?>" class="state-dd">
    		    				             <option value=""><?php echo __("Please Choose","migla-donation");?></option>
        						            <?php
        						            foreach( $states as $code => $name ){
        						            ?>
        	                                  <option value="<?php echo esc_html($name);?>" <?php if($donor_state == $name) echo "selected='selected'";?>>
        	                                      <?php echo esc_html($name);?>
        	                                  </option>
        						            <?php
        						            } ?>
    						            </select>
    						            </div>
    						            </br>
    						            <div id="<?php echo esc_attr($keycode)."-pr";?>" class="div-province <?php if($savedValue!='Canada') echo 'hideme';?>">
    		    				          <select id="province-<?php echo esc_attr($keycode);?>" class="province-dd">
    		    				            <option value=""><?php echo __("Please Choose","migla-donation");?></option>
        						            <?php
        						            foreach( $provinces as $code => $name )
                                            {
        						            ?>
                    	                        <option value="<?php echo esc_html($name);?>" <?php if($donor_province == $name) echo "selected";?>>
                    	                           <?php echo esc_html($name);?>
                    	                        </option>
        						            <?php
        						            } 
                                            ?>
    						              </select>
    						            </div>
    						        </div>

    						        <?php

    						        }else if( $keycode == "miglad_honoreecountry" )
    						        {
    						            $objG       = new MIGLA_GEOGRAPHY;
    						            $countries  = $objG->get_countries();
    						            $states     = $objG->get_USA_states();
    						            $provinces  = $objG->get_CA_provinces();

    						            $donor_state = $objD->get_donationmeta( "miglad_honoreestate", $post_id );
    						            $donor_province = $objD->get_donationmeta( "miglad_honoreeprovince", $post_id );
    						            ?>

    						            <div class="col-sm-6 col-xs-12">
    						            <div>
    		    				         <select id="<?php echo esc_attr($keycode);?>" class="input_edit country-dd">
    		    				            <option value=""><?php echo __("Please Choose","migla-donation");?></option>
        						            <?php
        						            foreach( $countries as $code => $name ){
        						            ?>
        	                                  <option value="<?php echo esc_attr($name);?>" <?php if($savedValue == $name) echo "selected='selected'";?>>
        	                                      <?php echo esc_html($name);?>
        	                                  </option>
        						            <?php
        						            } 
                                            ?>
    						            </select>
    						            </div>
    						            <br/>
    						            <div id="<?php echo esc_attr($keycode)."-st";?>" class="div-state <?php if($savedValue!='United States') echo 'hideme';?>" >

    		    				         <select id="state-<?php echo esc_attr($keycode);?>" class="state-dd">
    		    				             <option value=""><?php echo __("Please Choose","migla-donation");?></option>
        						            <?php
        						            foreach( $states as $code => $name ){
        						            ?>
                    	                      <option value="<?php echo esc_html($name);?>" <?php if($donor_state == $name) echo "selected='selected'";?>>
                    	                         <?php echo esc_html($name);?>
                    	                      </option>
        						            <?php
        						            } ?>
    						            </select>
    						            </div>
    						            </br>
    						            <div id="<?php echo esc_attr($keycode)."-pr";?>" class="div-province <?php if($savedValue!='Canada') echo 'hideme';?>">
        		    				        <select id="province-<?php echo esc_attr($keycode);?>" class="province-dd">
        		    				            <option value=""><?php echo __("Please Choose","migla-donation");?></option>
            						            <?php
            						            foreach( $provinces as $code => $name ){
            						            ?>
                    	                         <option value="<?php echo esc_html($name);?>" <?php if($donor_province == $name) echo "selected";?>>
                    	                            <?php echo esc_html($name);?>
                    	                         </option>
            						            <?php
            						            } ?>
        						            </select>
    						            </div>
    						        </div>

    						        <?php
    						        }else{
    			                        if( $child['type'] == 'checkbox' )
    			                        {
    			                        ?>
    			                        <div class="col-sm-6 col-xs-12">
        			                        <label class=" checkbox-inline">
        		    				             <input type='checkbox' class='input_edit' <?php if($savedValue=='yes') echo "checked"; ?>>
        		    				        </label>
    		    				        </div>
    			                        <?php
    			                        }else if( $child['type'] == 'multiplecheckbox' )
    			                        {
    			                            $array_values = array();

                                                

    			                            if(!empty($savedValue))
    			                            {
    			                                $array_values = explode(",",$savedValue);
    			                            }
    			                        ?>
    			                        <div class="col-sm-6 col-xs-12">
    		    				             <?php
    		    				             if( isset($customVal[('#'.$keycode)]) )
    		    				             {
    		    				                $lists = (array)unserialize($customVal[('#'.$keycode)]);
    		    				                
                                                foreach($lists as $list)
                                                {
    		    				                ?>
                                                <div class="radio">
                                                <label class="radio">
        		    				             <input type='checkbox' name="<?php echo esc_attr($keycode);?>" class='input_edit' value="<?php echo esc_html($list['lVal']);?>" <?php if( in_array($list['lVal'],$array_values) ) echo 'checked';?>>
        		    				                   <?php echo esc_html($list['lLbl']);?>
                                                </label>
                                                </div>

    		    				                <?php
    		    				                }//for
    		    				             }
    		    				             ?>
    		    				        </div>
    			                        <?php
    			                        }else if( $child['type'] == 'radio' )
    			                        {
    			                            $array_values = array();

    			                            if(!empty($savedValue)){
    			                               $array_values = explode(",",$savedValue);
    			                            }
    			                        ?>
    			                        <div class="col-sm-6 col-xs-12">
    		    				             <?php
    		    				             if( isset($customVal[('#'.$keycode)]) )
    		    				             {
    		    				                $lists = (array)unserialize($customVal[('#'.$keycode)]);
    		    				                ?>
                                                <div class="radio">
    		    				                    <label>
        		    				                    <input type='radio' name="<?php echo esc_attr($keycode);?>" class='input_edit' value=""><?php echo __("None","migla-donation");?>
        		    				                </label>
                                                </div>
    		    				                 <?php
    		    				                 foreach($lists as $list){
    		    				                 ?>
                                                <div class="radio">
    		    				                  <label>
        		    				                  <input type='radio' name="<?php echo esc_attr($keycode);?>" class='input_edit' value="<?php echo esc_html($list['lVal']);?>" <?php if( in_array($list['lVal'],$array_values) ) echo 'checked';?>>
        		    				                  <?php echo esc_html($list['lLbl']);?>
                                                    </label>
                                                </div>
    		    				                 <?php
    		    				                 }
    		    				             }
    		    				             ?>
    		    				        </div>
    			                        <?php
    			                        }else if( $child['type'] == 'select' ){
    			                        ?>
    			                       <div class="col-sm-6 col-xs-12">
    		    				             <select class="input_edit">
    		    				                <option value=""><?php echo __("None","migla-donation");?></option>
        		    				            <?php
        		    				            if( isset($customVal[('#'.$keycode)]) )
        		    				            {
        		    				                $lists = (array)unserialize($customVal[('#'.$keycode)]);

        		    				                foreach($lists as $list){
        		    				                ?>
        		    				                  <option value="<?php echo esc_attr($list['lVal']);?>" <?php if($list['lVal']==$savedValue) echo 'selected'?>><?php echo esc_html($list['lLbl']);?></option>
        		    				                <?php
        		    				                }
        		    				            }
        		    				            ?>
    		    				             </select>
    		    				        </div>
    			                        <?php
    			                        }else{
    		    				        ?>
    		    				        <div class="col-sm-6 col-xs-12">
    		    				            <input type='text' class='input_edit form-control' value='<?php echo esc_html($savedValue);?>'>
    		    				        </div>
    		    				        <?php
    			                        }
    						        }
    						        ?>
    						        <div class="col-sm-3 col-xs-hidden"></div>
    						        </div>
    						    </div>
    		                    <?php
    		                        }
    		                }//each child
    		            }
    		        }//foreach form sections
    		        ?>

    	        	<div class="row col-sm-12">
    	        		<div class="col-sm-3"></div>
    	        		<div class="col-sm-6 text-center">
    	        			<button id="mg_edit_form_update_btn" class="btn btn_info pbutton">
    	        				<i class="fa fa-fw fa-save"></i><?php echo __(' save','migla-donation');?></i>
    	        			</button>
    	        		</div>
    	        		<div class="col-sm-3"></div>
    	        	</div>
    	        </div>
	    </section>
			<?php


			}//if structure exist.
			else{
			}
	    	?>

	    <!--
	        </div>
		</section>
        -->

		</div>
		</div><!-- wrap container -->

        <form id="mg_report_filter_form" action='<?php echo get_admin_url(). "admin.php?page=migla_reports_page"; ?>' method="GET">
    	<?php
    		if( isset($_GET['page']) ){
    		?>
    			<input type="hidden" name="page" value="<?php echo esc_html($_GET['page']);?>">
    		<?php
    		}
    	?>
    		<input type="hidden" id="migla_in_start_date" name="start_date" value="<?php if(isset($_GET['sd'])) echo esc_html($_GET['sd']);?>">
    		<input type="hidden" id="migla_in_end_date" name="end_date" value="<?php if(isset($_GET['ed'])) echo esc_html($_GET['ed']);?>">
    		<input type="hidden" name="rep" value="yes">
    	</form>
	<?php
	}
}


$obj = new migla_reports_class();
?>