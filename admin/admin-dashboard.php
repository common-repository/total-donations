<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_top_level_class extends MIGLA_SEC
{
	function __construct()
	{
		add_action( 'admin_menu', array( $this, 'menu_item' ) );
	}

	function menu_item()
	{
        add_menu_page(
			'Total Donations', //page title
			'Total Donations', //menu title
			'read_dashboard', //capability
      		'migla_donation_menu_page', //slug
			array( $this, 'menu_page' ), //function
            Totaldonations_DIR_URL .'assets/images/icons/icon-admin-migla16.png'

	  );
	  do_action( 'migla_donation_menu' );
	}

	function menu_page()
	{

	  	if (  is_user_logged_in() )
		{

	      $this->create_token( 'migla_donation_menu_page', session_id() );
    	  $this->write_credentials( 'migla_donation_menu_page', session_id() );

		  $reporturl = get_admin_url()."admin.php?page=migla_reports_page";
		  $settingpage = get_admin_url()."admin.php?page=migla_donation_settings_page";

		  $objM = new MIGLA_MONEY;

		  $objT = new MIGLA_TIME;
		  $time_array = $objT->migla_date_timezone();

		  $symbol 		= $objM->get_currency_symbol();
		  $thousandSep 	= $objM->get_default_thousand_separator();
		  $decimalSep 	= $objM->get_default_decimal_separator();
		  $placement 	= $objM->get_symbol_position();
		  $showDecimal 	= $objM->get_show_decimal();
		  $symbolType 	= $objM->get_symbol_to_show();
	 	?>
		<div class='wrap'>
			<div class='container-fluid'>
	        <h2 class='migla'><?php echo __("Total Donations Dashboard","migla-donation");?></h2>

	        <input type="hidden" id='symbol' value="<?php echo esc_html( $symbol );?>"/>
	        <input type="hidden" id='currency' value="<?php echo esc_html( $objM->get_default_currency() );?>"/>
	        <input type="hidden" id='datenow' value="<?php echo esc_html( date( 'Y-m-d' ) );?>"/>
	        <input type="hidden" id='timenow' value="<?php echo esc_html( date( 'H:i:s') );?>"/>
	        <input type="hidden" id='thousandSep' value="<?php echo esc_html( $thousandSep );?>"/>
	        <input type="hidden" id='decimalSep' value="<?php echo esc_html( $decimalSep );?>"/>
	        <input type="hidden" id='placement' value="<?php echo esc_html( $placement );?>"/>
			<input type="hidden" id='showDecimal' value="<?php echo esc_html( $showDecimal );?>"/>

		 <?php

		    $objD = new CLASS_MIGLA_DONATION;
		    $objO = new MIGLA_OPTION;
		    $rcd_period = $objO->get_option( 'recent_donation_dashboard' );

		    if( $rcd_period == "thismonth")
		    {
		        $result = $objD->get_recent_donation_by_timediff( date('Y'),
                                            date('m'),
                                            '',
                                            '',
                                            '1',
                                            '',
                                            'DESC',
                                            'date_created',
                                            1
                    );
		    }else if( $rcd_period == "last2weeks" )
		    {
		    	$result = $objD->get_recent_donation( '2',
                                          'WEEK',
                                          '',
                                          1,
                                          '1',
                                          '',
                                          'DESC',
                                          'date_created',
                                          1
                                );
		    }else{
		    	$result = $objD->get_recent_donation( '', //time
			                                '', //period
			                                '10', //limit
			                                '1', //complete
			                                '1', //donation type
			                                '', //campaign
			                                'DESC',
			                                'date_created',
			                                1 //sttaus
	                );

		    }

	    ?>

		<div class='row form-horizontal'>

		<div class='col-lg-6 col-md-12'>
			<section class='panel panel-featured-left panel-featured-primary  wrapper-overlay'>
			    <div id="rcd-body" class='panel-body' style="opacity:0.1;">
			        <input type="hidden" id="last10-h2" value="<?php echo __("Last 10 Online Donations","migla-donation");?>">
			        <input type="hidden" id="last2weeks-h2" value="<?php echo __(" Online Donations Last 2 Weeks","migla-donation");?>">
			        <input type="hidden" id="thismonth-h2"  value="<?php echo __("This Month Online Donations","migla-donation");?>">

					<div class='widget-summary'>
						<h2 class='panel-title h2-rcd'>
						<?php
						if( $rcd_period == "thismonth")
		    			{
		    				echo __("This Month Online Donations","migla-donation");
		    			}else if( $rcd_period == "last2weeks" ){
		    				echo __(" Online Donations Last 2 Weeks","migla-donation");
		    			}else{
							echo __("Last 10 Online Donations","migla-donation");
		    			}
						?>
						</h2>
					<div>
		                    <div class='ibox-title'>
		                        <h5><?php echo __("Timeline","migla-donation");?></h5>
		                           <div class='panel-actions'>
		                           	<strong>
										<div class='btn-group'>
    										<button id="mg-recentSelector" data-toggle='dropdown' class='multiselect dropdown-toggle btn btn-default button' type='button' title='Time Period Graph'>
	    									    <?php echo __("Time Period ","migla-donation");?><b class='caret'></b></button>
											<ul id='recent-multiSelector' class='multiselect-container dropdown-menu pull-right'>
												<li class='multiselect-list' value='last10'>
												    <a href='javascript:void(0);' class='multiselect-list-a' value='last10'>
													<label class='radio multiselect-list-lbl' value='last10'>
													    <input type='radio' class='multiselect-list-radio' name='multiselect' <?php if( $rcd_period == "last10") echo "checked";?> value='last10'><?php echo __("Last 10 Donations","migla-donation");?>
													    </label>
													</a>
												</li>
												<li class='multiselect-list' value='last2weeks'>
												    <a href='javascript:void(0);' class='multiselect-list-a' value='last2weeks'>
													<label class='radio multiselect-list-lbl' value='last2weeks'>
													    <input type='radio' class='multiselect-list-radio' <?php if( $rcd_period == "last2weeks") echo "checked";?> name='multiselect' value='last2weeks'><?php echo __("Last 2 Weeks","migla-donation");?>
													    </label>
													</a>
												</li>
												<li class='multiselect-list' value='thismonth'>
												    <a href='javascript:void(0);' class='multiselect-list-a' value='thismonth'>
													<label class='radio multiselect-list-lbl' value='thismonth'>
													    <input type='radio' class='multiselect-list-radio' <?php if( $rcd_period == "thismonth") echo "checked";?> name='multiselect' value='thismonth'><?php echo __("This Month","migla-donation");?>
													    </label>
													</a>
												</li>
											</ul>
										</div>
		  						</strong> 
		  						</div>
		                    </div>

		                <div class='ibox-content' id="mg-recent-donation-display">
		                <?php
		                if(!empty($result)){
		                    foreach($result as $row)
		                    {
		                        $country = $row['country'];
		                        $address_street = $objD->get_donationmeta( 'miglad_address', $row['id']);
		                        $address_list = array();
		                        $i = 0;	

		                        if(!empty($address_street)){
		                            $address_list[$i] = $address_street;
		                            $i++;
		                        }

		                        if( !empty($country) ){
		                            if( $country == 'United States' ){
										$address_list[$i] = $objD->get_donationmeta( 'miglad_state', $row['id']);
		                            	$i++;
		                            }else if( $country == 'Canada' ){
		                                $address_list[$i] = $objD->get_donationmeta( 'miglad_province', $row['id']);
		                            	$i++;
		                            }
		                        }
		                        
		                        if( $showDecimal == 'no' )
		                        {
		                            $amount = number_format($row['amount'], 0, $decimalSep, $thousandSep);
		                        }else{
		                            $amount = number_format($row['amount'], 2, $decimalSep, $thousandSep);
		                        }
		                    ?>
		                    <div class='timeline-item'>
		                    <div class='row'>
		                        <div class='col-xs-3 date'>
		                            <span class=''><?php echo $symbol;?></span>
		                            <?php
		                            ?>
		                            <br> <small class='text-navy'>
		                                <?php echo esc_html($row['date_created']); ?></small>
		                        </div>
		                        <div class='col-xs-8 content'>
		                            <p class='m-b-xs'>
		                                <strong><?php echo esc_html($amount); ?></strong>
		                                <span class='donorname'>
		                                    <?php if( empty($row['lastname']) ){
		                                        echo esc_html($row['firstname']);
		                                    }else{
		                                        echo esc_html($row['firstname'].' '.$row['lastname']);
		                                    }
		                                    ?>
		                                </span>
		                            </p>
		                            <?php 
		                            if(!empty($address_list)){
		                            	foreach($address_list as $address_row){
		                            		echo esc_html($address_row) . "<br>";
		                            	}
		                            }
		                            ?>
		                            <?php echo esc_html($country);?>
		                            </br>
		                            <?php echo __('Anonymous:' , 'migla-donation');
		                               $anon = $objD->get_donationmeta( 'miglad_anoymous', $row['id'])
		                            ?>
		                            <strong><?php
		                            if($anon == '')
		                            {
		                                echo __('no' , 'migla-donation');
		                            }else{
		                                echo __('yes' , 'migla-donation');
		                            }
		                            ?></strong><br>


		                        </div>
		                    </div>
		                    </div>
		                    <?php
		                    }
		                }
		                ?>
		                </div>
						<span class="<?php if(!empty($result)) echo "hideme"; ?>" id="mg_no-donation-list"><?php echo __(" There are no donations for this date range","migla-donation");?> </span>
						<div class='alignright'>
							<a href='<?php echo esc_html($reporturl);?>'>
								<button type='submit' id='miglaLatestButton' class='obutton btn'><?php echo __(" See All","migla-donation");?></button>
							</a>
						</div>

			    	</div>
				</div>
				</div>

			    <div id='mg_rcd-overlay' class="mg-overlay"><div class="mg-loading">Loading&#8230;</div></div>

			</section>
		</div>

		<?php
		$allTotal = $objD->get_total_donation_by_date( '', '', '', '', 1 );

		$defValue = "0";

		if( $showDecimal == 'yes' ) $defValue = "0".$decimalSep."00";

		if( is_numeric($allTotal) )
		{
            if( $showDecimal == 'no' )
		    {
		        $allTotal = number_format( $allTotal, 0, $decimalSep, $thousandSep );
		    }else{
		        $allTotal = number_format( $allTotal, 2, $decimalSep, $thousandSep );
		    }		    
		}else{
		    $allTotal = $defValue;
		}

		$allOnlineTotal = $objD->get_total_donation_by_date( '', '', '', '1', 1);

        if( $showDecimal == 'no' )
		{
		    $allOnlineTotal = number_format( $allOnlineTotal, 0, $decimalSep, $thousandSep );
		}else{
		    $allOnlineTotal = number_format( $allOnlineTotal, 2, $decimalSep, $thousandSep );
		}	

		$allTotalThisMonth = $objD->get_total_donation_by_date( date('Y'), date('m'), '', '', 1 );

        if( $showDecimal == 'no' )
		{
		    $allTotalThisMonth = number_format( $allTotalThisMonth, 0, $decimalSep, $thousandSep );
		}else{
		    $allTotalThisMonth = number_format( $allTotalThisMonth, 2, $decimalSep, $thousandSep );
		}

		$onlineTotalThisMonth = $objD->get_total_donation_by_date( date('Y'), date('m'), '', '1', 1 );
		
        if( $showDecimal == 'no' )
		{
		    $onlineTotalThisMonth = number_format( $onlineTotalThisMonth, 0, $decimalSep, $thousandSep );
		}else{
		    $onlineTotalThisMonth = number_format( $onlineTotalThisMonth, 2, $decimalSep, $thousandSep );
		}		

		$allTotalToday = $objD->get_total_donation_by_date( date('Y'), date('m'), '', date('d'), '1', 1 );

		?>

		<div class='col-md-6 col-lg-6 col-xl-3'>

		    <input type="hidden" id="TotalOn-ThisMonth" value="<?php echo esc_html($allTotalThisMonth);?>">
		    <input type="hidden" id="TotalOn-Today" value="<?php echo esc_html($allTotalToday);?>">

				<section class='panel panel-featured-left panel-featured-primary wrapper-overlay'>
					<div class='panel-body'>
						<div class='widget-summary'>
							<div class='widget-summary-col-icon'>
								<div class='summary-icon bg-primary <?php if($symbolType=='3-letter-code') echo "mg_country-code";?>'><?php echo $symbol;?></div>
							</div>
							<div class='widget-summary-col'>
								<div class='summary'>
									<h4 class='title'><?php echo __("Total Donations:","migla-donation");?></h4>
									<div class='info'>
										<strong class='amount' id='amount'><?php echo $symbol . " ". esc_html($allTotal);?></strong>
									</div>
							</div>
							<div class='widget-footer'></div>
						</div>
					</div>
				</div>

			</section>

		</div>

		<div class='col-md-6 col-lg-6 col-xl-3'>
			<section class='panel panel-featured-left panel-featured-primary wrapper-overlay'>

			<div id='mg_total-overlay' class="mg-overlay hideme">
	            <div class="mg-loading">Loading&#8230;</div>
	        </div>

				<div class='panel-body' id="counttotal-body">
					<div class='widget-summary'>
						<div class='widget-summary-col-icon'>
							<div class='summary-icon bg-color-teal' style='font-size:3rem'>
								<i class='fa fa-calendar'></i>
							</div>
						</div>
					<div class='widget-summary-col'>
						<div class='summary'>
							<div class='row'>
								<div class='col-sm-5'>

	                	        <input type="hidden" id='mg_amount_month_title' value="<?php echo __("This Month:","migla-donation");?>">
	                	        <input type="hidden" id='mg_amount_7day_title' value="<?php echo __("Past 7 Days:","migla-donation");?>">
	                	        <input type="hidden" id='mg_amount_today_title' value="<?php echo __("Today:","migla-donation");?>">

									<h4 class='title title-totals'>
									    <?php echo __("This Month:","migla-donation");?>
										</h4>

									<div class='info'>
										<strong class='amount' id='mg_all_amount'>
										<?php echo $symbol  ." ". esc_html($allTotalThisMonth);?></strong>
									</div>
								</div>
								<div class='col-sm-7 '>
									<ul class='mg_pagination mg_pagination-sm alignright'>
										<li class='li-total-choice mg_active'><a id='mg_amount_month' class="mg-total-bytime" style="cursor:pointer;">
											<?php echo __("This Month:","migla-donation");?></a></li>
										<li class="li-total-choice"><a id='mg_amount_7day' class='btn mg-total-bytime'>
											<?php echo __("Past 7 Days:","migla-donation");?></a></li>
										<li class="li-total-choice"><a id='mg_amount_today' class="mg-total-bytime" style="cursor:pointer;">
											<?php echo __("Today:","migla-donation");?></a></li>
									</ul>
								</div>
							</div>
						</div>

						<div class='widget-footer'>

						</div>
					</div>
				</div>
			</div>

			</section>

		</div>

	    <div class='col-lg-6 col-md-12'>
			<section class='panel wrapper-overlay'>
				<header class='panel-heading panel-heading-transparent'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<div class='panel-actions'>
						<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
					</div>

					<h2 class='panel-title'><?php echo __("Campaign Progress","migla-donation");?></h2>
				</header>
				<div id='collapseOne' class='panel-body collapse show'>
					<div class='table-responsive'>
						<table class='table table-striped mb-none'>
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo __("Project","migla-donation");?></th>
									<th><?php echo __("Status","migla-donation");?></th>
									<th><?php echo __("Progress","migla-donation");?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$cmpObj = new MIGLA_CAMPAIGN;
								$campaigns = $cmpObj->get_all_info( get_locale() );

	                            $i = 1;

	                            if( !empty($campaigns) )
	                            {
	                                foreach($campaigns as $row)
	                                {
	                                    $stat    = "open";
	                                    $statclass   = 'label-success';

	                                    if( $row['shown'] == '0' || $row['shown'] == '-1' )
	                                    {
	                                      $stat    = "closed";
	                                      $statclass   = 'label-warning';
	                                    }

	                                    $amt = $objD->get_total_donation_by_campaign( '', '', '', $row['id'], 1 );

	                                    $target = $row['target'];

	                                    $percent = 1.0;
	                                ?>
	                                <tr>
	                                    <td><?php echo $i;?></td>
	                                    <td><?php echo $row['name'];?></td>
	                                    <td><span class='label <?php echo $statclass?>'><?php echo $stat;?></span></td>
	                                    <?php
	                                        if( intval($target) > 0.00 )
	                                        {
	                                            $percent = (floatval($amt) / floatval($target)) * 100;
	                                        ?>
	                                            <td>
	                                            <div class='progress progress-sm progress-half-rounded m-none mt-xs light mg_percentage'>
	                                        		<div style='width:<?php echo esc_attr($percent);?>%;' aria-valuemax='100' aria-valuemin='0' aria-valuenow='60' role='progressbar' class='progress-bar progress-bar-primary'>
	                                        		<?php echo esc_html(number_format($percent,1, $decimalSep,$thousandSep));?>%
	                                        		</div>
	                                            </div>
	                                            </td>
	                                        <?php
	                                        }else{
	                                          if( true)
	                                          {  ?>
	                                            <td><div class='undeclared-campaign'> Raised <?php esc_html($amt);?></div></td>
	                                        <?php
	                                          }else{
	                                        ?>
	                                           <td><div class='undeclared-campaign'> Raised <?php esc_html($amt);?></div></td>
	                                            <?php
	                                          }
	                                        }
	                                    ?>
	                                </tr>
	                                <?php
	                                $i++;
	                                }
	                            }
								?>
	 						</tbody>
						</table>
					</div>
				</div>

			    <div id='mg_progress-overlay' class="mg-overlay hideme">
			       <div class="mg-loading">Loading&#8230;</div>
			    </div>

			</section>

		</div>

	    <?php

	    $gmt_offset = -get_option( 'gmt_offset' );

	  	if ($gmt_offset > 0)
	  	{
	            $time_zone = 'ETC/GMT +' . $gmt_offset;
	    }else if($gmt_offset < 0){
	            $time_zone = 'ETC/GMT ' . $gmt_offset;
	    }else{
	            $time_zone = 'ETC/GMT';
	    }

	    $now = esc_html($time_zone) ."<br>". esc_html(date("F jS, Y", strtotime($time_array['date'])))."<br>". esc_html($time_array['time']);

		?>

		<div class='col-md-6 col-lg-6 col-xl-12'>
			<section class='panel panel-featured-left panel-featured-primary'>
				<div class='panel-body'>
					<div class='widget-summary'>
	                    <h3><?php echo $now;?></h3>
						<div class='widget-footer'></div>
					</div>
				</div>
			</section>
		</div>


		<div class='col-lg-6 col-md-12'>
			<section class='panel panel-featured-left panel-featured-primary wrapper-overlay'>
				<div class='panel-body' id="body-graph" style="height:800px;">
					<h2 class='panel-title' id='migla-donation-title'>
						<?php echo __("Donation Graph","migla-donation");?></h2>
					<div class='mg_dashboard-toggle-btns'>
						<ul class='mg_pagination mg_pagination-sm'>
							<li class='mg_active li-graph'><a id='graph-1m' class='wf-dashboard-graph-attacks' data-grouping='1m'><?php echo __("Past 30 days", "migla-donations");?></a></li>				
							<li class='li-graph'><a id='graph-6m' class='wf-dashboard-graph-attacks' data-grouping='6m'><?php echo __("Past 6 Months", "migla-donations");?></a></li> 
							<li class='li-graph'><a id='graph-1y' class='wf-dashboard-graph-attacks' data-grouping='1y'><?php echo __("Past 12 Months", "migla-donations");?></a></li>
						</ul>
					</div>						
						
				<div class='panel-actions'></div>
				<br>
				
					<div id='mg_legend-graph' class='mg_legend'>
					<!--	<ul><li><span class='mg_online-legend'></span><?php echo __("Online Donations","migla-donation");?></li></ul> -->
					</div>
					<div id='canvas-A' class="mg-canvas-graph">
				  		<canvas id='mg-canvas-A' height='450' width='600'></canvas>
				   	</div>
					<div id='canvas-B' class="mg-canvas-graph hideme">
				  		<canvas id='mg-canvas-B' height='450' width='600'></canvas>
				   	</div>
					<div id='canvas-C' class="mg-canvas-graph hideme">
				  		<canvas id='mg-canvas-C' height='450' width='600'></canvas>
				   	</div>
			    </div>
			 	<div id='mg_graph-overlay' class="mg-overlay" height='450' width='600'><div class="mg-loading">Loading&#8230;</div></div>
		    </section>
		</div>

	</div>

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

$obj = new migla_top_level_class();
?>