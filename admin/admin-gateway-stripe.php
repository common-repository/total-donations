<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_menu_gateway_stripe extends MIGLA_SEC
{

	function __construct()
	{
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 9 );
	}

	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Stripe Settings', 'migla-donation' ),
			__( 'Stripe Settings', 'migla-donation' ),
		    'read_gateway',
			'migla_stripe_setting_page',
			array( $this, 'menu_page' )
		);
	}

  function writeme( $str )
  {
     $result =  str_replace( "//" , "/" , $str );
     $result =  str_replace( "[q]" , "'" , $result );
     return $result;
  }

	function menu_page()
	{
	  	if ( is_user_logged_in() )
		{
        	$this->create_token( 'migla_stripe_setting_page', session_id() );
    	  	$this->write_credentials( 'migla_stripe_setting_page', session_id() );

	        $objO = new MIGLA_OPTION;
    	    $objF = new CLASS_MIGLA_FORM;
    		$objL = new MIGLA_LOCAL;

			echo "<div class='wrap'><div class='container-fluid'>";
                echo "<h2 class='migla'>". __("Stripe Settings","migla-donation")."</h2>";


		echo "<div class='row form-horizontal'>";

	    $cc = (array)unserialize($objF->get_meta( 0, 'stripe_tab_info', $objL->get_origin() ));

	    $cc_label = $objF->stripe_tab($cc);

		$showStripe = $objO->get_option('migla_show_stripe');

        $objO = new MIGLA_OPTION;

	$webhook = Totaldonations_DIR_URL."gateways/stripe/migla-stripe-weebhook.php";
?>
<div class='col-sm-12'>
    <section class='panel'><header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a></div>
        <h2 class='panel-title'><i class='fa  fa-cc-stripe'></i><?php echo __("Stripe's Webhook","migla-donation");?></h2></header>
	    <div id='collapseOne' class='panel-body collapse show'>
            <div class='row'>
                <div class='col-sm-3'><label for='mg_stripe_webhook_url2' class='control-label text-right-sm text-center-xs'><?php echo __("Webhook's URL: (Front End)","migla-donation");?></label></div>
                <div class='col-sm-6 col-xs-12'>
	                <input type='text' value='<?php echo esc_html($this->get_current_server_url() . "/index.php?sl=".$objO->get_option('migla_listen'));?>' />
	            </div>
            <div class='col-sm-3'>
                <a><button value='Preview Page' class='btn btn-info obutton' id='miglaStripeWebhook' onclick='window.open("https://dashboard.stripe.com/account/webhooks")'>
                        <i class='fa fa-fw fa-search'></i><?php echo __(" Go to Stripe","migla-donation");?></button>
                </a>
            </div>
        </div>
            <p id='warningEmptyAmounts' >
                <?php echo __("Copy this URL and add it into the webhook area located inside the 'admin panel' on Stripe's website. Please read Stripe's documentation for more detailed information.","migla-donation");?>
                <i class='fa fa-fw fa-caret-up'></i>
            </p>
    </section>
</div>

<?php

    $testSK = $objO->get_option('migla_testSK');
    $testPK = $objO->get_option('migla_testPK');
    $liveSK = $objO->get_option('migla_liveSK');
    $livePK = $objO->get_option('migla_livePK');
    $stripeMode = $objO->get_option('migla_stripemode');
    $webhook_key = $objO->get_option('migla_webhook_key');
?>


<div class='col-sm-12'>
	<section class='panel'>
		<header class='panel-heading'><div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseInfo' aria-expanded='true'></a></div>
			<h2 class='panel-title'><i class='fa fa-cc-stripe'></i><?php echo __("Stripe Info","migla-donation");?></h2>
		</header>
		<div id='collapseInfo' class='panel-body collapse show'>


	<div class='row'>
	<div class='col-sm-3'><label for='migla_testPK' class='control-label text-right-sm text-center-xs'>
		<?php echo __("Test Publishable Key:","migla-donation");?></label>
	</div>
	<div class='col-sm-6 col-xs-12'>
		<input type='text' id='migla_testPK' value='<?php echo esc_html($testPK);?>' class='form-control'>
	</div>
	</div>

	<div class='row'>
		<div class='col-sm-3'><label for='migla_testSK' class='control-label text-right-sm text-center-xs'>
		<?php echo __("Test Secret Key:","migla-donation");?></label>
	</div>
	<div class='col-sm-6 col-xs-12'>
		<input type='text' id='migla_testSK' value='<?php echo esc_html($testSK);?>' class='form-control'>
	</div>
	</div>

	<div class='row'><div class='col-sm-3'><label for='migla_livePK' class='control-label text-right-sm text-center-xs'>
		<?php echo __("Live Publishable Key:","migla-donation");?></label></div><div class='col-sm-6 col-xs-12'>
	<input type='text' id='migla_livePK' value='<?php echo esc_html($livePK);?>' class='form-control'></div></div>

	<div class='row'><div class='col-sm-3'><label for='migla_liveSK' class='control-label text-right-sm text-center-xs'>
		<?php echo __("Live Secret Key:","migla-donation");?></label></div><div class='col-sm-6 col-xs-12'>
	<input type='text' id='migla_liveSK' value='<?php echo esc_html($liveSK);?>' class='form-control'></div></div>


  <div class='row'><div class='col-sm-3'><label for='migla_webhook_key' class='control-label text-right-sm text-center-xs'>
  	<?php echo __("Webhook Key:","migla-donation");?></label></div><div class='col-sm-6 col-xs-12'>
	<input type='text' id='migla_webhook_key' value='<?php echo esc_html($webhook_key);?>' class='form-control'></div></div>


<div class='row'><div class='col-sm-3'></div><div class='col-sm-9'>
<?php

if( $stripeMode == 'test' )
{
?>
  <div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='test' checked ><?php echo __("Test Stripe","migla-donation");?></label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='live' ><?php echo __("Live Stripe","migla-donation");?>
														</label>
													</div>


</div>
<?php
}else{
?>
  <div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='test' ><?php echo __("Testing Stripe","migla-donation");?></label>
													</div>


<div class='radio'>
														<label>
															<input type='radio' name='miglaStripe' value='live' checked ><?php echo __("Live Stripe","migla-donation");?>
														</label>
													</div>


</div>
<?php
}
?>

		</div>
		<div class='row'>
		    <div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>
		    <div class='col-sm-6 center-button'><button id='miglaUpdateStripeKeys' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?></button></div>
		</div>

		</div>
		
		</section>
		</div>

<div class='col-sm-12'>
<section class='panel'>
	<header class='panel-heading'>
		<div class='panel-actions'>
		<a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseEight' aria-expanded='true'></a>
		</div>
		<h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div><?php echo __("Stripe Credit Card Tab","migla-donation");?></h2>
	</header>
	<div id='collapseEight' class='panel-body collapse show'>

	<div class='row'>
		<div class='col-sm-3'>
			<label class='control-label text-right-sm text-center-xs' for='mg_stripe-tab'>
				<?php echo __("Stripe Tab Label:","migla-donation");?>
			</label>
		</div>
		<div class='col-sm-6 col-xs-12'>
			<input type="text" class="form-control" value="<?php echo esc_html($this->writeme($cc_label['tab']));?>" placeholder="Stripe" id="mg_stripe-tab">
		</div>
	</div>

	<div class='row'>
		<div class='col-sm-3'>
			<label class='control-label text-right-sm text-center-xs' for='mg_stripe-tab'>
				<?php echo __("Name on Card:","migla-donation");?>
			</label>
		</div>
		<div class='col-sm-3 col-xs-12'>
			<input type="text" class="form-control" value="<?php echo esc_html($this->writeme($cc_label['cardholder']['label']));?>" placeholder="Stripe" id="mg_stripe-label">
		</div>
		<div class='col-sm-3 col-xs-12'>
			<input type="text" class="form-control" value="<?php echo esc_html($this->writeme($cc_label['cardholder']['placeholder']));?>" placeholder="Stripe" id="mg_stripe-placeholder">
		</div>
	</div>

	<div class='row'>
		<div class='col-sm-3 col-xs-12'>
			<label class='control-label text-right-sm text-center-xs' for='mg_cardnumber-stripe'>
				<?php echo __("Card Number:","migla-donation");?>

				</label>
		</div>
		<div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>
			<input type="text" id="mg_label-card" class="form-control" placeholder="Card Number" value="<?php echo esc_html($this->writeme($cc_label['cardnumber']['label']));?>">
		</div>
		<div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>
			<input type="hidden" id="mg_placeholder-card" value="<?php echo esc_html($this->writeme($cc_label['cardnumber']['placeholder']));?>">
		</div>
	</div>

	<div class='row'>
		<div class='col-sm-3 col-xs-12'>
			<label class='control-label text-right-sm text-center-xs' for='mg_waiting_stripe'><?php echo __("Text displayed while redirecting/processing:","migla-donation");?>
			</label>
		</div>
		<div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>
			<input type="text" name="mg_placeholder_waiting" id="mg_waiting_stripe" class="form-control" placeholder="Just a moment while we process your donation" value="<?php echo esc_html($this->writeme($cc_label['loading_message']));?>">
		</div>
	</div>

	<div class='row'>
		<div class='col-sm-12 center-button'>
			<button value='save' class='btn btn-info pbutton msave' id='miglaSaveCCInfo'><i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?></button>
		</div>
	</div>

	</div>
</section>
</div>

<?php
$btnchoice = $objO->get_option('miglaStripeButtonChoice');

$choice =  array();
$choice['stripeButton'] = "";
$choice['imageUpload'] = "";
$choice['cssButton'] = "";

if( empty($btnchoice) ){
    $choice['stripeButton'] = "checked";
}else{
    $choice[$btnchoice] = "checked";
}

$btnurl = $objO->get_option('migla_stripebuttonurl');
$btnstyle = $objO->get_option('migla_stripecssbtnstyle');
$btnclass = $objO->get_option('migla_stripecssbtnclass');
?>
            <div class='col-sm-12'>
              <section class='panel'>
                <header class='panel-heading'>
                <div class='panel-actions'>
                <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseNine' aria-expanded='true'></a>
                </div>
                <h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div><?php echo __("Stripe Button","migla-donation");?></h2>
                </header>
                <div id='collapseNine' class='panel-body collapse show'>

                  <div class='form-horizontal'>
                    <div class='form-group touching'>
                      <div class='col-sm-3  col-xs-12'>
                        <label class='control-label text-right-sm text-center-xs' for='mg_CSSButtonPicker'><?php echo __("Button","migla-donation");?></label>
                      </div>
                      <div class='col-sm-6 col-xs-12'>
                        <select id='mg_CSSButtonPicker' class='form-control touch-top' name='miglaCSSButtonPicker'>

                            <option <?php if( $btnstyle == 'Default') echo 'selected';?> value='Default'><?php echo __("Your Default Form Button","migla-donation");?></option>
                            <option <?php if( $btnstyle == 'Default_Stripe') echo 'selected';?> value='Default_Stripe'><?php echo __("Stripe Default Button","migla-donation");?></option>
                            <option <?php if( $btnstyle == 'Grey') echo 'selected';?> value='Grey'><?php echo __("Grey Button","migla-donation");?></option>
                        </select>
                      </div>
                      <div class='col-sm-3'></div>
                    </div>

                    <div class='form-group touching'>
                      <div class='col-sm-3  col-xs-12'>
                        <label for='mg_CSSButtonText' class='control-label text-right-sm text-center-xs'><?php echo __("Button Text","migla-donation");?></label>
                      </div>
                      <div class='col-sm-6 col-xs-12'>
                        <input id='mg_CSSButtonText' type='text' value='<?php echo esc_html($cc_label['button']);?>' placeholder='Donate Now' class='form-control touch-middle'>
                      </div>
                      <div class='col-sm-3'></div>
                    </div>

                    <div class='form-group touching'>
                      <div class='col-sm-3  col-xs-12'>
                        <label for='mg_CSSButtonClass' class='control-label text-right-sm text-center-xs'><?php echo __("Add CSS class (theme button only)","migla-donation");?></label>
                      </div>
                      <div class='col-sm-6 col-xs-12'>
                        <input id='mg_CSSButtonClass' type='text' value='<?php echo esc_html($btnclass);?>' required='' placeholder='enter your css class here' title='' class='form-control touch-bottom' name=''>
                      </div>
                      <div class='col-sm-3'>
                      </div>
                    </div>

                    <div class='form-group touching'>
                        <div class='col-sm-3 col-xs-12'></div>
                        <div class='col-sm-6 center-button'>
                            <button value='save' class='btn btn-info pbutton' id='migla-save-stripe-btn'><i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?></button>
                        </div>
                    </div>      
                          
                  </div>

              </section>
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

$obj = new migla_menu_gateway_stripe();
?>