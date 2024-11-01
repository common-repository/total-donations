<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_campaign_menu_class extends MIGLA_SEC
{
	function __construct()
	{
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 1 );
	}

	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Campaigns', 'migla-donation' ),
			__( 'Campaigns', 'migla-donation' ),
			'read_campaign',
			'migla_campaign_page',
			array( $this, 'menu_page' )
		);
	}

	function menu_page()
	{
    if (  is_user_logged_in() )
    {
      $this->create_token( 'migla_campaign_page', session_id() );
      $this->write_credentials( 'migla_campaign_page', session_id() );

      if( isset( $_GET['form'] ) && sanitize_text_field($_GET['form']) != '' && !isset($_GET['lang']) )
  		{
  			$this->menu_form( sanitize_text_field($_GET['form']) );
  		?>
  			<form id='migla_form_back' action='<?php echo get_admin_url()."admin.php?page=migla_campaign_page";?>' method='GET' style='display:none!important'>
  				<input type='hidden' name='page' value="migla_campaign_page" >
  				<input style='display:none!important' id='mg_submit_back' class='button' type='submit'/>
  			</form>
  		<?php

  		}else{
  				$this->menu_campaign();
  				?>
  				<form id='migla_form_campaign' action='<?php echo get_admin_url()."admin.php?page=migla_campaign_page";?>' method='GET' style='display:none!important'>
  				<input type='hidden' name='page' value='migla_campaign_page' >
  				<input type='hidden' id='mg_form_id_send' name="form" value="<?php if(isset($_GET['form'])) echo esc_html($_GET['form']);?>" />
  				<input style='display:none!important' id='mg_submit_form' class='button' type='submit'/>
  				</form>
  				<?php
  		}

    }else{
         $error = "<div class='wrap'><div class='container-fluid'>";
         $error .= "<h2 class='migla'>";
         $error .= __("You do not have sufficient permissions to access this page. Please contact your web administrator","migla-donation"). "</h2>";
         $error .= "</div></div>";

         wp_die( __( $error , 'migla-donation' ) );
    }
  }

  function menu_campaign()
  {
    $objM = new MIGLA_MONEY;

  ?>
      <input type='hidden' id='mg_page_admin' value='<?php echo get_admin_url()."admin.php?page=migla_campaign_page";?>'>

    <div class='wrap'>
      <div class='container-fluid'>

              <h2 class='migla'><?php echo __('Campaign', 'migla-donation');?></h2>

      <input type="hidden" id="migla_page" value="home">

  <div class=' form-horizontal'>


    <ul class="nav nav-pills">
        <li class="active mg-li-tab">
              <a class="mg-li-a-tab active show" data-toggle="tab" href="#section1" aria-expanded="true"><?php echo __("Campaigns","migla-donation");?></a>
        </li>
        <li class="mg-li-tab">
              <a class="mg-li-a-tab" data-toggle="tab" href="#section2" aria-expanded="false">
                <?php echo __("Edit Form","migla-donations");?></a>
        </li>
    </ul>


         <div class='tab-content nav-pills-tabs'>


        <div id='section1' class='tab-pane active' >


      <div class='row'>




  <div class='col-sm-12'>
  <section class='panel'>
    <header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <div class='panel-actions'>
        <a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
      </div>
      <h2 class='panel-title'>
        <div class='dashicons dashicons-plus'></div>
        <?php echo __("Add New Campaigns","migla-donation");?>
      </h2>
    </header>

  <div class='panel-body collapse show' id='collapseOne'>

    <div class='row'>
      <div class='col-sm-3'>
        <label for='mName' class='miglaCampaignNameLabel  control-label  text-right-sm text-center-xs'><?php echo __('Campaign Name', 'migla-donation');?>
        </label>
      </div>
      <div class='col-sm-6 col-xs-12'>
        <span class='input-group input-group-control'>
          <span class='input-group-addon '><i class='fa fa-medkit  fa-fw'></i></span>
                  <input type='text' id='mName' placeholder='<?php echo __("Name","migla-donation");?>' class='form-control' />
                </span>
              </div>
              <div class='col-sm-3 hidden-xs'></div>

              <div class='col-sm-12 col-xs-12'>
                <div class='help-control-center'>
                  <?php echo __('Enter the name of the Campaign (e.g. Bulid a School)','migla-donation');?>
                </div>
              </div>
          </div>

    <div class='row'>
      <div class='col-sm-3'>
        <label for='mAmount'  class='miglaCampaignTargetLabel control-label text-right-sm text-center-xs migla_positive_number_only'>
          <?php echo __('Donation Target','migla-donation') ;?>
        </label>
      </div>
      <div class='col-sm-6 col-xs-12'>
        <span class='input-group input-group-control'>
          <span class='input-group-addon'>
              <?php echo $objM->get_currency_symbol();?>
            </span>
            <input type='text' class='form-control miglaNAD' placeholder='0' id='mAmount'>
          </span>
        </div>
        <div class='col-sm-3 hidden-xs'></div>

        <div class='col-sm-12 col-xs-12'>
          <div  class='help-control-center'>
            <?php echo __("No currency symbol. Leave blank if you don't want the progress bar.","migla-donation");?>
          </div>
        </div>
      </div>

    <p>
      <button id='miglaAddCampaign' class='btn btn-info pbutton miglaAddCampaign' value='save'>
        <i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?>
      </button>
    </p>

  </div>

  </section>
  <br>
</div>



<div class='col-sm-12'>
  <section class='panel'>
    <header class='panel-heading'>
      <div class='panel-actions'>
        <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a>
      </div>

      <h2 class='panel-title'>
        <div class='dashicons dashicons-list-view'></div>
        <?php echo __("List of Available Campaigns","migla-donation");?>
      </h2>
    </header>

  <div id='collapseTwo' class='panel-body collapse show'>
    <ul class='row mg_campaign_list'>

<?php
  $obj 	= new MIGLA_CAMPAIGN;
  $data 	= $obj->get_all_info_orderby( get_locale() );
  $idk 	= 0;

      $objO = new MIGLA_OPTION;

      $cmp_order = $objO->get_option('migla_campaign_order');

      $order = array();
      $j = 0;

      if( !empty($cmp_order) )
      {
          $order_list = (array)unserialize($cmp_order);

          foreach( $order_list as $order_row ){
              if( isset($data[($order_row)]) ){
                $order[$j] = (array)$data[($order_row)];
                $j++;
              }
          }

      }

  if( empty($data) )
  { ?>
    <li class='empty-campaign-li'>
      <?php echo __('You do not have any campaigns yet. Add some above','migla-donation');?> <i class='fa fa-fw fa-caret-up'></i>
    </li>
  <?php
  }else{

    if( !empty($order) )
    {
      $data = $order;
    }

    foreach( (array)$data as $d )
    {
      $cmpid      = $d['id'];
      $nama 	    = $d['name'];
      $target 	= $d['target'];
      $showed 	= $d['shown'];
      $post_id 	= $d['form_id'];

      ?>

    <li class='ui-state-default formfield clearfix formfield_campaign' data-showed="<?php if( $showed == '1' ) echo "yes";?>">
        <input type='hidden' name='label' class="cmp_label" value="<?php echo esc_html($nama);?>" />
        <input type='hidden' name='target' class="cmp_target" value="<?php echo esc_html($target);?>" />
        <input type='hidden' name='show' class="cmp_shown" value="<?php echo esc_html($showed);?>" />
        <input type='hidden' name='form_id' class="cmp_formid"  value="<?php echo esc_html($post_id);?>" />
        <input type='hidden' name='id' class="cmp_id" value="<?php echo esc_html($cmpid);?>" />

        <div class='col-sm-1 hidden-xs'>
            <label class='control-label'>
              <?php echo __('Campaign','migla-donation');
              ?>
            </label>
        </div>

        <div class='col-sm-2 col-xs-12'>
          <input type="text" class="labelChange" name="" placeholder="" value="<?php echo esc_html(str_replace("[q]","'",$nama)); ?>" /></div>

        <div class='col-sm-1 hidden-xs'>
            <label class='control-label'>
              <?php echo __('Target','migla-donation');?>
            </label>
          </div>

        <div class='col-sm-2 col-xs-12'>
        <input type='text' class='targetChange miglaNAD' name='' placeholder='' value="<?php echo esc_html($target);?>" />
      </div>

        <div class='col-sm-2 col-xs-12'>
            <input type="text" value="[totaldonations_progressbar id='<?php echo esc_attr($cmpid);?>']" class="mg_label-shortcode" onclick="this.setSelectionRange(0, this.value.length)">
        </div>
        
    <?php
      $show = '';
        $hide = "";
        $da 	= "";
        $class 	= "";

      if( $showed == '1' ){
          $show = "checked";
      }else if( $showed == '0' ){
          $hide = "checked";
      }else{
          $da = "checked";
        $class ="pink-highlight" ;
      }
      ?>


        <div class="col-sm-2 col-xs-12 row">
            <input type="text" value="[totaldonations_circlebar id='<?php echo esc_attr($cmpid);?>']" class="mg_label-shortcode" onclick="this.setSelectionRange(0, this.value.length)">
        </div>

        <div class='control-radio-sortable col-sm-2 col-xs-12'>

        <span>
          <label>
            <input type='radio' class="statusShow mg_shown_cmp" name='cmp-shown-<?php echo esc_attr($idk);?>' value='1' <?php echo esc_attr($show);?> class='cmp-shown'/>
              <?php echo __(" Show","migla-donation");?>
          </label>
        </span>

        <span>
          <label>
            <input type='radio' class="statusShow mg_hide_cmp" name='cmp-shown-<?php echo esc_attr($idk);?>' value='-1' class='<?php echo esc_attr($class);?> cmp-shown' <?php echo esc_attr($da);?> />
              <?php echo __(" Deactived","migla-donation");?>
          </label>
        </span>

        <span>
          <button class='removeCampaignField' data-toggle="modal" data-target="#confirm-delete">
            <i class='fa fa-fw fa-trash'></i>
          </button>
        </span>

        </div>
       </li>
    <?php
      $idk++;

    }//foreach
  }//else
  ?>

  </ul>

    <div class='row'>
      <div class='col-sm-6'>
        <button value='save' class='btn btn-info pbutton' id='miglaSaveCampaign'>
          <i class='fa fa-fw fa-save'></i>
          <?php echo __(' update list of campaigns','migla-donation');?>
        </button>
      </div>
    </div>

    </div>
  </section>
  </div><!--col-sm-12-->

  </div> </div>



  <div id='section2' class='tab-pane' >

  <div class="row">

        <div class='col-sm-6'>
  <section class='panel'>
    <header class='panel-heading'>
      <div class='panel-actions'>
        <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseMulti' aria-expanded='true'></a>
      </div>

          <h2 class='panel-title'>
            <div class='dashicons dashicons-list-view'></div>
            <?php echo __("Multi-Campaign Form","migla-donation");?>
          </h2>
    </header>

    <div id='collapseMulti' class='panel-body collapse show'>
      <div class="row">
        <div class="col-sm-3"><label class="control-label text-right-sm text-center-xs" for="mHideUndesignatedCheck">
            <?php echo __('Edit the form here:  ','migla-donation'); ?>
                      </label>
                  </div>

      <div class="col-sm-3">
          <a id='form_0' class='mg_a-form-per-campaign-options mbutton edit_custom-fields-list' href='<?php echo get_admin_url()."admin.php?page=migla_campaign_page&form=0&cmp=0";?>'>
            <?php echo __('Multi-campaign Form ','migla-donation');
              ?>
            </a>
      </div>

      <br><br><br><br>

      </div>
    </div>
  </section>
</div>



  </div>


  </div> </div>

</div><!--form-horizontal-->


<div class='modal fade' id='confirm-delete' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='true'>
      <div class='modal-dialog'>
          <div class='modal-content'>

              <div class='modal-header'>
                  <button type='button' class='close mg_campaign_remove_cancel' data-dismiss='modal' aria-hidden='true' data-target='#confirm-delete'>
                    <i class='fa fa-times'></i>
                  </button>
                  <h4 class='modal-title' id='myModalLabel'>
                    <?php echo __("Confirm Delete","migla-donation");?>
                </h4>
              </div>

              <input type="hidden" id="mg_cmp_del_id" value="" />

      <div class='modal-wrap clearfix'>
          <div class='modal-alert'><i class='fa fa-times-circle'></i></div>
        <div class='modal-body'>
              <p><?php echo __("Deleting this campaign will also delete any changes you've made on its unique form. The donation data will be not deleted.", "migla-donation")?></p>
          </div>
      </div>

              <div class='modal-footer'>
                  <button type='button' class='mg_campaign_remove_cancel btn btn-default mbutton' data-dismiss='modal'>
                    <?php echo __("Cancel","migla-donation");?>
                  </button>
                  <button type='button' id='mg_campaign_remove' class='btn btn-danger danger rbutton'>
                    <?php echo __("Delete","migla-donation");?>
                  </button>
              </div>

          </div>
      </div>
  </div>

</div>
</div><!-- container-fluild  -->
 <?php
 }

  function menu_form( $form_id )
  {

  $objForm = new CLASS_MIGLA_FORM;
  $FORM = $objForm->get_info( $form_id , get_locale() );

      $objM = new MIGLA_MONEY;

      $symbol 		= $objM->get_currency_symbol();
      $thousandSep 	= $objM->get_default_thousand_separator();
      $decimalSep 	= $objM->get_default_decimal_separator();
      $placement 	= $objM->get_symbol_position();
      $showDecimal 	= $objM->get_show_decimal();

      $objO = new MIGLA_OPTION;

  ?>

  <input type='hidden' id='mg_thousand_separator' value='<?php echo esc_html($thousandSep);?>'>
  <input type='hidden' id='mg_decimal_separator' value='<?php echo esc_html($decimalSep);?>'>
  <input type='hidden' id='mg_show_separator' value='<?php echo esc_html($showDecimal);?>'>
  <input type='hidden' id='mg_page_admin' value='<?php echo get_admin_url()."admin.php?page=migla_campaign_page";?>'>

  <?php
  if( empty($FORM) )
  {
    ?>
    <div class='wrap'>
      <div class='container-fluid'>

        <h2 class='migla'><?php echo __(" Form options","migla-donation");?></h2>

      </div>
    </div>


    <?php
      }else{

    ?>

  <input type="hidden" id="mg_current_language" value="<?php echo esc_html(get_locale()); ?>">
  <input type='hidden' id='migla_page' value='form'>
  <input type='hidden' id='mg_form_id' value='<?php echo esc_html($form_id);?>'>
  <input type="hidden" id="trans-GroupRemove" value="<?php echo __("Groups that have fields in them cannot be deleted. Move the fields to a different group or delete them before deleting the group ", "migla-donation");?>">
  <input type="hidden" id="trans-DuplicateAlert" value="<?php echo __("data can not be empty or duplicate title !", "migla-donation");?>">

<div class='wrap'>
  <div class='container-fluid'>

  <h2 class='migla'>
  <?php
  if( $form_id == 0  )
  {
      echo __(" Multi-Campaign Options ","migla-donation");
  }else{
      $objC  = new MIGLA_CAMPAIGN;
      $objL  = new MIGLA_LOCAL;
      $objM  = new MIGLA_MONEY;
      $lang  = $objL->get_origin_language();
      $names = $objC->get_name( sanitize_text_field($_GET['cmp']), $lang );

      if( isset($names[$lang]) )
      {
          $name = $names[$lang];
      }else{
          $name = $_GET['cmp'];
      }

      echo __(" Form options Campaign: ".$name,"migla-donation");
  }
  ?></h2>

  <div class='row form-group'>
    <div class='col-sm-12'>
      <a class='mg_go-back from-translate' href="<?php echo get_admin_url()."admin.php?page=migla_campaign_page"?>"><i class='fa fa-fw fa-arrow-left'></i>
      <?php echo __(" Go back to Main Campaign Page", "migla-donation");?></a>
    </div>
  </div>

  <div class='row'>
    <div class='col-sm-12'>
        <div class='form-horizontal'>

          <ul class='nav nav-pills'>
            <li class='active' >
              <a data-toggle='tab' href='#section1' class="active show">
                <?php echo __("Misc Form Options","migla-donation");?>
              </a>
            </li>
            <li >
              <a data-toggle='tab' href='#section3'>
                <?php echo __("Form Settings","migla-donation");?></a>
            </li>
            <li class='migla_button-translation'>
            <?php
            if( $form_id == 0  ){
            ?>
             <ul class="shortcode-copy"><li class="shortcode-label"> Form Shortcode:</li><li><input type="text" value="[totaldonations]" class='mg_label-shortcode' onclick='this.setSelectionRange(0, this.value.length)'></li>
                        </ul> 
            <?php
            }else{
            ?>
              <input type="text" value="[totaldonations id='<?php echo esc_html($_GET['cmp']);?>']" class='mg_label-shortcode' onclick='this.setSelectionRange(0, this.value.length)'>
            <?php
            }
            ?>
              </li >
        </ul>

      <div class='tab-content nav-pills-tabs'>

      <?php

        $amounts    = $FORM["amounts"] ;

        $curSymbol  = $objM->get_currency_symbol();

        $hide_custom_amount = $FORM["hideCustomAmount"];
        $ctext = $FORM["custom_amount_text"];

      ?>

<div id='section1' class='tab-pane  active'>

  <section class='panel'>
      <header class='panel-heading'>
        <div class='panel-actions'>
          <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
        </div>
        <h2 class='panel-title'>
          <span><?php $curSymbol;?></span>
          <?php echo __("Misc Form Options","migla-donation");?>
        </h2>
      </header>

    <div id='collapseOne' class='panel-body collapse show'>


  <?php
  if( $form_id == 0 )
  {
      $undesign_label = (array)unserialize( $objO->get_option("migla_undesignLabel") );

      if(isset($undesign_label[(get_locale())]))
      {
          $lbl_val = $undesign_label[(get_locale())];
      }else{
          $lbl_val = "";
      }

      $label_value = str_replace( '[q]', '"', $lbl_val );
      $hidelabel = $objO->get_option('migla_hideUndesignated');

      if( $hidelabel == '' || $hidelabel == false ){
        $hidelabel = 'no';
      }
  ?>

      <div class="row">
        <div class="col-sm-3">
          <label class="miglaCampaignTargetLabel control-label  text-right-sm text-center-xs" for="mg-undesignated-default">
            <?php echo __("Undesignated Category Label:","migla-donation");?>
          </label>
        </div>
        <div class="col-sm-6 col-xs-12">
          <input type="text" id="mg-undesignated-default" placeholder="<?php echo esc_attr($label_value);?>" class="form-control " value="<?php echo esc_html($label_value);?>">
        </div>
        <div class="col-sm-3 hidden-xs"></div>
      </div>

      <div class="row">
        <div class="col-sm-3 col-xs-12">
          <label class="control-label text-right-sm text-center-xs" for="mHideUndesignatedCheck">
            <?php echo __("Hide Undesignated Category on Form:","migla-donation");?>
          </label>
        </div>
        <div class="col-sm-9 col-xs-12 text-left-sm text-center-xs">
        <?php
        if(  $hidelabel == "yes" ){  ?>
          <label class="checkbox-inline" for="mHideUndesignatedCheck">
            <input type="checkbox" name="mHideUndesignatedCheck" id="mHideUndesignatedCheck" checked="checked">
              <?php echo __("Check this if you want your donors to only be able to donate towards a campaign ","migla-donation");?>
           </label>
        <?php
        } else{ ?>
          <label class="checkbox-inline" for="mHideUndesignatedCheck">
            <input type="checkbox" name="mHideUndesignatedCheck" id="mHideUndesignatedCheck" >
              <?php echo __("Check this if you want your donors to only be able to donate towards a campaign ","migla-donation");?>
          </label>
        <?php
        }
        ?>
        </div>
      </div>


      <div class='row'>

        <div class='col-sm-3'>
          <label class='miglaErrorAmountLabel control-label  text-right-sm text-center-xs' for='mg-erroramount-default'>
          <?php echo __("Choose which campaigns you allow on the campaign switcher","migla-donation");?>
          </label>
        </div>

        <div class='col-sm-6 col-xs-12'>
          <div class='row'>
            <div class='col-sm-5 col-xs-12'>
            <?php
              $obj  = new MIGLA_CAMPAIGN;
              $data   = $obj->get_all_info_orderby( get_locale() );
              $idk  = 0;

                  $objO = new MIGLA_OPTION;

                  $cmp_order = $objO->get_option('migla_campaign_order');

                  $order = array();
                  $j = 0;

                  if( !empty($cmp_order) )
                  {
                      $order_list = (array)unserialize($cmp_order);

                      foreach( $order_list as $order_row ){
                          if( isset($data[($order_row)]) ){
                            $order[$j] = (array)$data[($order_row)];
                            $j++;
                          }
                      }

                  }

            if( empty($data) )
            {
                echo __("No Campaigns to show. Add some on the main campaign page", "migla-donation");
            }else{
              ?>
              <div class='list-group' id='mg_all_campaign_list'>
                <a class='list-group-item active'>
                  <?php echo __("Add All Campaigns","migla-donation");?><input id='mg_add_all_campaign_chooser' title='toggle all' class='mg_add_all_campaign_chooser all pull-right' type='checkbox'>
                </a>
              <?php
              $count = 0;

              if( !empty($order) )
              {
                $data = $order;
              }

              foreach( $data as $d )
              {

                if( $d['shown'] == '1' && $d['multi_list'] != '1' )
                {
                ?>
                  <a class='list-group-item'><?php echo esc_html(str_ireplace("[q]","'",$d['name']));?>
                      <input class='pull-right' type='checkbox'>
                      <div class='mg_showedcampaign_group_div'>
                        <input type='hidden' class='mg_c_id' value='<?php echo esc_html($d['id']);?>'>
                        <input type='hidden' class='mg_c_isListed' value='<?php echo esc_html($d['multi_list']);?>'>
                        <input type='hidden' class='mg_c_name' value='<?php echo esc_html($d['name']);?>'>
                        <input type='hidden' class='mg_c_show' value='<?php echo esc_html($d['shown']);?>'>
                        <input type='hidden' class='mg_c_formid' value='<?php echo esc_html($d['form_id']);?>'>
                      </div>
                  </a>
                <?php
                }
                ?>

              <?php
              }//foreach $data as $d
              ?>
              </div>
     <?php  }  ?>

            </div><!--col-sm-5-->

            <div class='col-md-2 v-center'>
              <button title='Send to list 2' class='btn btn-default center-block mg_add'>
                <i class='dashicons dashicons-arrow-right-alt2'></i></button>
              <button title='Send to list 1' class='btn btn-default center-block mg_remove'>
                <i class='dashicons dashicons-arrow-left-alt2'></i></button>
            </div>

            <input type='hidden' id='mg_add_warning' value='<?php echo __("Choose an item from all campaign","migla-donation");?>'>

            <div class='col-sm-5 col-xs-12'>
              <div class='list-group' id='mg_showed_campaign_list'>
                <a class='list-group-item active'><?php echo __("Remove All Campaigns","migla-donation");?>
                  <input title='toggle all' id='mg_remove_all_campaign_chooser' class='mg_remove_all_campaign_chooser pull-right' type='checkbox'>
                </a>
                <?php
                if( empty($order) )
                {
                }else{
                  foreach( $order  as $d )
                  {
                      if( $d['shown'] == '1' && $d['multi_list'] == '1' ){
                  ?>
                    <a class='list-group-item mg_showedcampaign_group'>
                        <?php echo str_ireplace("[q]","'",$d['name']);?>
                        <input class='pull-right' type='checkbox'>
                        <div class='mg_showedcampaign_group_div'>
                          <input type='hidden' class='mg_c_id' value='<?php echo esc_html($d['id']);?>'>
                          <input type='hidden' class='mg_c_isListed' value='<?php echo esc_html($d['multi_list']);?>'>
                          <input type='hidden' class='mg_c_name' value='<?php echo esc_html($d['name']);?>'>
                          <input type='hidden' class='mg_c_show' value='<?php echo esc_html($d['shown']);?>'>
                          <input type='hidden' class='mg_c_formid' value='<?php echo esc_html($d['form_id']);?>'>
                        </div>
                    </a>
                  <?php
                      }
                  }
                }
                ?>
              </div>
            </div>


          </div><!--row-->
        </div><!--col-sm-6-->

        <div class="col-sm-3 col-xs-hidden"></div>
      </div>

  <?php
  }
  ?>

      <div class='row'>
        <div class='col-sm-3 col-xs-12'>
          <label for='mHideHideCustomCheck' class='control-label text-right-sm text-center-xs'>
            <?php echo __("Hide Custom Amount on Form:","migla-donation");?>
          </label>
        </div>

        <div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>
          <label for='mHideHideCustomCheck' class='checkbox-inline'>
          <?php
          if( $hide_custom_amount  == 'yes'  )
          {
          ?>
            <input type='checkbox' id='mHideHideCustomCheck' name='mHideHideCustomCheck' checked>
          <?php
          }else{  ?>
                <input type='checkbox' id='mHideHideCustomCheck' name='mHideHideCustomCheck'>
          <?php
          } ?>

          <?php echo __("Check this if you want your donors not to be able to choose a custom amount","migla-donation");?>
          </label>
        </div>

       <div class='col-sm-3 col-xs-12'></div>
    </div>

    <?php
    if( $hide_custom_amount == 'yes' )
    {  ?>
      <div class='row' id='mg_div_custom_amount_text' style='display:none !important'>
    <?php }else{  ?>
      <div class='row' id='mg_div_custom_amount_text'>
    <?php  }
    ?>

   <div class='col-sm-3 col-xs-12'>
      <label for='mHideHideCustomText' class='control-label text-right-sm text-center-xs'>
        <?php echo __("Custom Amount Text:","migla-donation");?></label>
   </div>
   <div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>
   <input type='text' id='mg_custom_amount_text' value='<?php echo esc_html($ctext);?>'></div></div>

   <div class='row'>
      <div class='col-sm-3 col-xs-12'>
        <label for='mg_amount_btn_type' class='control-label text-right-sm text-center-xs'>
        <?php echo __("Choose the style of the giving level amounts:","migla-donation");?>
        </label>
      </div>
      <div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>
        <select id='mg_amount_btn_type'>
           <?php  if( $FORM["buttonType"] == 'button' )
           {  ?>
            <option value='radio'>Radio Button</option>
            <option value='button' selected>Button</option>
           <?php  }else{  ?>
            <option value='radio' selected>Radio Button</option>
            <option value='button'>Button</option>
           <?php  }  ?>
        </select>
      </div>
      <div class='col-sm-3 col-xs-12'></div>
   </div>

  <div class='row'>
      <div class='col-sm-3 col-xs-12'>
        <label for='mg_amount_box_type' class='control-label text-right-sm text-center-xs'>
          <?php echo __("Choose the length of the giving level amount boxes:","migla-donation");?>
      </label></div>

      <div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>

         <select id='migla_amount_box_type'>
         <?php
         if( $FORM["amountBoxType"] == 'box' )
         {  ?>
          <option value='fill'>Fill Form</option>
          <option value='box' selected>Box</option>
         <?php  }else{  ?>
          <option value='fill' selected>Fill Form</option>
          <option value='box'>Box</option>
         <?php
         }  ?>
         </select>
      </div>
      <div class='col-sm-3 col-xs-12'></div>
  </div>

  <div class='row'>
    <div class='col-sm-12 center-button'>
      <button id='mg_amount_settings' class='btn btn-info pbutton msave' value='save'>
        <i class='fa fa-fw fa-save'></i>
        <?php echo __(" save","migla-donation");?>
      </button>
    </div>
  </div>
  </section>

  <section class='panel'>
    <header class='panel-heading'>
      <div class='panel-actions'>
        <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwenty' aria-expanded='true'></a>
      </div>
        <h2 class='panel-title'>
          <span><?php echo $curSymbol;?></span>
            <?php echo __("Giving Levels","migla-donation");?>
            <span class='panel-subtitle'>
              <?php echo __(" Drag and drop the amounts into the order you want","migla-donation");?>
          </span>
        </h2>
    </header>

    <div id='collapseTwenty' class='panel-body collapse show'>

    <div class='row'>

      <div class='col-sm-3 col-xs-12'>
        <label for='miglaAddAmount' class='control-label text-right-sm text-center-xs'>
        <?php echo __("Add a suggested giving level","migla-donation");?>
        </label>
      </div>

      <div class='col-sm-6 col-xs-12'>
        <span class='input-group input-group-control'>
          <span id='curSymbol' class='input-group-addon'>
            <?php echo $curSymbol;?>
          </span>
          <input type='text' class='form-control migla_positive_number_only' placeholder='0' id='miglaAddAmount'>
        </span>
      </div>

    </div>

    <div class='row'>
      <div class='col-sm-3 col-xs-12'>
        <label class='control-label text-right-sm text-center-xs' for='miglaAddAmount'>
        <?php echo __("Add a giving level description (optional)","migla-donation");?>
        </label>
      </div>
      <div class='col-sm-6 col-xs-12'>
        <input type='text' id='miglaAmountPerk' class='form-control' placeholder='e.g. This amount will provide enough food for...'>
      </div>
      <div class='center-button col-sm-12'>
        <button id='miglaAddAmountButton' class='btn btn-info pbutton msave' value='save'>
          <?php echo __(" Add","migla-donation");?></button>
    </div>
    </div>

    <div id='miglaAmountTable'>
    <?php
    if( !empty( $amounts ) )
    {
        if( isset($amounts[0]) && !empty($amounts[0]) ){

            foreach( $amounts as $amt )
            {
              $valLabel = $amt['amount'] ;
              $valPerk = $amt['perk'];
    
                if( $objM->get_show_decimal() == 'yes' ){
                    $valLabel = str_replace(".", $objM->get_default_thousand_separator() , $valLabel  );
                }else{
                    $digit = explode( ".", $valLabel  ) ;
                    $valLabel = $digit[0];
                }
              ?>
              <p class='mg_amount_level'>
                 <input class='mg_amount_level_value' type=hidden value='<?php echo esc_html($amt['amount']);?>' />
                 <label><?php echo esc_html($valLabel);?></label>
                 <label class='mg_amount_level_perk'><?php echo esc_html($valPerk);?></label>
    
                 <button name='miglaAmounts' class='miglaRemoveLevel obutton'>
                  <i class='fa fa-times'></i></button>
              </p>
    
              <?php
            }//foreach

        }else{
                    
        }
    }//if empty amounts[0]
    ?>

    <p id='warningEmptyAmounts' style='display:none'>
      <?php echo __("No amounts have been added. Add some amounts above.","migla-donation");?>
        <i class='fa fa-fw fa-caret-up'></i>
    </p>

  </div>
</section>

</div>
<!-----------------------------------------------section 1-->


      <?php
          $warning1 = $FORM["warning_1"]; //Please insert all the required fields
          $warning2 = $FORM["warning_2"]; //Please insert correct email
          $warning3 = $FORM["warning_3"]; //please fill in a valid amount

          $custAmountText = $FORM['custom_amount_text'];
        ?>

        <div id="section3" class="tab-pane">
        <div class="row">

        <input type="hidden" id="mg_oldUnLabel" value="<?php echo esc_html($label_value);?>">

        <div class="col-sm-12">
          <section class="panel">
          <header class="panel-heading">
            <div class="panel-actions">
              <a aria-expanded="true" href="#collapseFive" data-parent=".panel" data-toggle="collapse" class="fa fa-caret-down "></a>
            </div>
            <h2 class="panel-title"><i class="fa fa-fw fa-bullhorn"></i>
              <?php echo __("Form Options","migla-donation");?>
            </h2>
          </header>

          <div id="collapseFive" class="panel-body collapse show">

          <div class="row">
            <div class="col-sm-3">
              <label class="miglaErrorGeneralLabel control-label  text-right-sm text-center-xs" for="mg-errorgeneral-default">
                <?php echo __("Error Message Label for the General Fields:","migla-donation");?>
              </label>
            </div>

            <div class="col-sm-6 col-xs-12">
              <input type="text" id="mg-errorgeneral-default" class="form-control " value="<?php echo esc_html($warning1);?>" placeholder="<?php echo esc_attr($warning1);?>">
            </div>
            <div class="col-sm-3 hidden-xs"></div>
          </div>

          <div class="row">
            <div class="col-sm-3">
              <label class="miglaErrorEmailLabel control-label  text-right-sm text-center-xs" for="mg-erroremail-default">
                <?php echo __("Error Message Label for the Email Field:","migla-donation");?>
              </label>
            </div>
            <div class="col-sm-6 col-xs-12">
              <input type="text" id="mg-erroremail-default" class="form-control " value="<?php echo esc_html($warning2);?>" placeholder="<?php echo esc_attr($warning2);?>">
            </div>
            <div class="col-sm-3 hidden-xs"></div>
          </div>

          <div class="row">
            <div class="col-sm-3">
              <label class="miglaErrorAmountLabel control-label  text-right-sm text-center-xs" for="mg-erroramount-default">
              <?php echo __("Error Message Label for the Amount:","migla-donation");?>
              </label>
              </div>
              <div class="col-sm-6 col-xs-12">
                <input type="text" id="mg-erroramount-default" class="form-control " value="<?php echo esc_html($warning3);?>" placeholder="<?php echo esc_attr($warning3);?>">
              </div>
              <div class="col-sm-3 hidden-xs"></div>
          </div>


          <div class='row'>
            <div class='col-sm-12 center-button'>
              <button id='migla_form_settings_btn' class='btn btn-info pbutton msave' value='save'><i class='fa fa-fw fa-save'></i>
                <?php echo __(" save","migla-donation");?>
              </button>
            </div>
          </div>


          </div> <!-- collapse-->

          </section>
        </div>

                <!----------- SECTION 3 PART 2 -->

        <div class='col-sm-12 hidden-xs'>
          <section class='panel mg_form-fields-default-language'>
            <header class='panel-heading'>
              <div class='panel-actions'>
                <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseFour' aria-expanded='true'></a>
              </div>
              <h2 class='panel-title'><i class='fa fa-fw fa-check-square-o'></i>
              <?php echo __("Form Fields","migla-donation");?>
              <span class='panel-subtitle'>
                <?php echo __("Drag and drop fields and groups or add new ones","migla-donation");?>
              </span>
              </h2>
            </header>

            <div id='collapseFour' class='panel-body collapse show'>

              <input id="trans-Show" type="hidden" value="<?php echo esc_html(__(" Show","migla-donation"));?>"> 
              <input id="trans-Hide" type="hidden" value="<?php echo esc_html(__(" Hide","migla-donation"));?>"> 
              <input id="trans-Mandatory" type="hidden" value="<?php echo esc_html(__(" Mandatory","migla-donation"));?>"> 
              <input id="trans-Cancel" type="hidden" value="<?php echo esc_html(__(" Cancel","migla-donation"));?>"> 
              <input id="trans-SaveField" type="hidden" value="<?php echo esc_html(__(" Save Field","migla-donation"));?>"> 

              <div class='row'>
                <div class='col-sm-12 groupbutton'>
                  <button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormTop'>
                    <i class='fa fa-fw fa-save'></i><?php echo __(" save form","migla-donation");?>
                  </button>

                  <button class='btn btn-info obutton mAddGroup' value='add'><i class='fa fa-fw fa-plus-square-o'></i>
                    <?php echo __("Add Group","migla-donation");?></button>
                </div>

                <div id='divAddGroup' class='col-sm-12'  style='display:none'>
                  <div class='addAgroup'>
                    <div class='row'>

                      <div class='col-sm-4'>
                        <div class='row'>
                          <div class='col-sm-2'> <i class='fa fa-bars bar-icon-styling'></i></div>
                          <div class='col-sm-10'>
                            <input type='text' id='labelNewGroup' placeholder='<?php echo __("insert new header for group","migla-donation");?>' />
                          </div>
                        </div>
                      </div>

                      <div class='col-sm-4'>
                        <div class='col-sm-5'>
                          <input type='checkbox' id='t' class='toggle' id='toggleNewGroup' />
                          <label><?php echo __("Toggle","migla-donation");?></label>
                        </div>
                      </div>

                      <div class='col-sm-4 addfield-button-control alignright'>
                        <button type='button' class='btn btn-default mbutton' id='cancelAddGroup'>
                          <?php echo __("Cancel","migla-donation");?></button>
                          <button type='button' class='btn btn-info inputFieldbtn pbutton' id='saveAddGroup'>
                            <i class='fa fa-fw fa-save'></i><?php echo __(" Save Group","migla-donation");?>
                          </button>
                      </div>

                    </div>
                  </div>
                </div><!--divAddGroup-->

               <div class='col-sm-12'>
                 <ul class='containers'>
                  <?php
                  $id = 0; $i = 0;

                  if( !empty( $FORM['structure'] ) )
                  {
                    $formStruct = $FORM['structure'] ;

                    foreach ( $formStruct as $f )
                    {
                        $title = "";
                        $title = str_replace( "[q]", "'", $f["title"] );
                    ?>
                    <li class="title formheader">
                      <div class="row">

                        <div class="col-sm-4">
                          <div class="row">
                            <div class="col-sm-2">
                              <i class="fa fa-bars bar-icon-styling"></i>
                            </div>
                            <div class="col-sm-10">
                              <input type="text" class="titleChange" placeholder="<?php echo esc_attr($title);?>" name="grouptitle" value="<?php echo esc_html($title);?>">
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-4">
                      <?php
                        if( $f["toggle"] == "-1" )
                        {
                      ?>
                          <div class="col-sm-5">
                              <label>
                              <input type="checkbox" id="t<?php echo esc_html($id);?>"  class="toggle" disabled>
                                <?php echo __("Toggle","migla-donation");?>
                              </label>
                          </div>
                      <?php
                        }else if( $f["toggle"] == "1" )
                        {
                      ?>
                          <div class="col-sm-5">
                              <label>
                              <input type="checkbox" id="t<?php echo esc_html($id);?>"  class="toggle" checked>
                                <?php echo __("Toggle","migla-donation");?>
                              </label>

                          </div>
                      <?php
                        }else{
                      ?>
                          <div class="col-sm-5">
                              <label>
                              <input type="checkbox" id="t<?php echo esc_html($id);?>"  class="toggle">
                                <?php echo __("Toggle","migla-donation");?>
                              </label>

                          </div>
                      <?php
                        }


                      ?>

                        <button value='add' class='btn btn-info obutton mAddField addfield-button-control'>
                          <i class='fa fa-fw fa-plus-square-o'></i>
                          <?php echo __("Add Field","migla-donation");?>
                        </button>

                      </div>

                      <div class='col-sm-4 text-right-sm text-right-xs divDelGroup'>
                        <button class='rbutton btn btn-danger mDeleteGroup pull-right'>
                          <i class='fa fa-fw fa-trash'></i>
                          <?php echo __("Delete Group","migla-donation");?>
                        </button>
                      </div>


                      </div><!--row-->

                      <input class='mHiddenTitle' type='hidden' name='title' value='<?php echo esc_html($f['title']);?>' />

                      <?php
                      $ulId = str_replace(" ","", $f['title']);
                      ?>

                      <ul class='rows' id='<?php echo esc_html($ulId);?>' >

                      <?php
                       if ( isset($f['child']) && count( (array)$f['child'] ) > 0 )
                       {
                          $j = -1;

                          foreach ( (array)$f['child'] as $c )
                          {
                            if( $c['id'] == 'repeating' || $c['id'] == 'mg_add_to_milist' )
                            {

                            }else{
                            $j++;
                            $arrShow = array("", "", "", "", "", "", "", "");
                            ?>

                            <li class='ui-state-default formfield clearfix'>

                              <input class='mHiddenLabel' type='hidden' name='label' value='<?php echo esc_html($f['child'][$j]['label']);?>' />
                              <input class="old_type" type='hidden' name='type' value='<?php echo esc_html($f['child'][$j]['type']);?>' />
                              <input class="old_id" type='hidden' name='id' value='<?php echo esc_html($f['child'][$j]['id']);?>' />
                              <input class="old_code" type='hidden' name='code' value='<?php echo esc_html($f['child'][$j]['code']);?>' />
                              <input class="old_status" type='hidden' name='status' value='<?php echo esc_html($f['child'][$j]['status']);?>' />

                              <?php
                              if ( array_key_exists("uid", $f['child'][$j] ) ){
                              ?>
                                  <input class="old_uid" type='hidden' name='uid' value='<?php echo esc_html($f['child'][$j]['uid']);?>' />
                              <?php
                              }

                                $myuid = "#" . $f['child'][$j]['uid'];
                                $_listval = "";

                                if( isset($FORM[$myuid]) )
                                {
                                  $_list = (array)unserialize( $FORM[$myuid] );
                                  if( empty($_list) ){
                                    $_listval = "empty";
                                  }else{
                                    foreach($_list as $row){
                                        $_listval .= $row['lVal']."::".$row['lLbl'].";";
                                    }
                                  }
                                }
                              ?>
                              <input type='hidden' id="<?php echo esc_attr($f['child'][$j]['uid']);?>" value='<?php echo esc_html($_listval);?>' />
                              <?php

                              if( strcmp( $f['child'][$j]['code'],"miglad_" ) == 0 )
                              {
                                $disabled = "disabled";
                                $op = "disabled";
                                $field_id = $f['child'][$j]['id'];
                                $field_id = str_ireplace( 'honoree', 'H' , $field_id );

                                if( $field_id == 'mg_add_to_milist' )
                                {
                                   $field_id = 'mail list';
                                }
                              }else{
                                $disabled = "";
                                $op = "";
                                $field_id = 'Label';
                              }

                              $label = str_replace( "[q]", "'", $f['child'][$j]['label']);
                            ?>

                            <div class='clabel col-sm-1 hidden-xs'>
                              <label class='control-label'><?php echo __($field_id,"migla-donation");?></label>
                            </div>
                            <div class='col-sm-3 col-xs-12'>
                              <input type='text' name='labelChange' class='labelChange' value='<?php echo esc_html($label);?>' />
                            </div>

                          <?php

                            $display_editval = "style='display:none;'";

                             if( (string)$f['child'][$j]['type'] == "text" ){
                                $arrShow[0] = "selected=selected";
                             }else
                             if( (string)$f['child'][$j]['type'] == "checkbox" ){
                                $arrShow[1] = "selected=selected";
                             }else
                             if( (string)$f['child'][$j]['type'] == "textarea" ){
                                $arrShow[2] = "selected=selected";
                             }else
                             if( (string)$f['child'][$j]['type'] == "select" ){
                                $arrShow[3] = "selected=selected";
                                 $display_editval = "";
                             }else
                             if( (string)$f['child'][$j]['type'] == "radio" ){
                                $arrShow[4] = "selected=selected";
                                $display_editval = "";
                             }else
                             if( (string)$f['child'][$j]['type'] == "multiplecheckbox" ){
                                $arrShow[5] = "selected=selected";
                                $display_editval = "";
                             }else
                             if( (string)$f['child'][$j]['type'] == "notype" ){
                                $arrShow[7] = "selected=selected";
                             }
                          ?>

                            <div class='ctype col-sm-2 col-xs-12'>

                               <select name='typeChange' class='typeChange' id='s<?php echo esc_attr($f['child'][$j]['id']);?>' <?php echo esc_attr($disabled);?> >

                                    <?php
                                    if( strcmp( $f['child'][$j]['code'],"miglad_" ) == 0 )
                                    {  ?>
                                       <option value='notype' <?php echo esc_attr($arrShow[7]);?> >
                                    <?php
                                    }
                                    ?>

                                    <?php echo __("no type","migla-donation");?>
                                    </option>
                                  <option value='text' <?php echo esc_attr($arrShow[0]);?> >
                                    <?php echo __("text","migla-donation");?>
                                    </option>
                                  <option value='checkbox' <?php echo esc_attr($arrShow[1]);?> >
                                    <?php echo __("checkbox","migla-donation");?>
                                    </option>
                                  <option value='textarea' <?php echo esc_attr($arrShow[2]);?> >
                                    <?php echo __("textarea","migla-donation");?>
                                    </option>
                                  <option value='select' <?php echo esc_attr($arrShow[3]);?> >
                                    <?php echo __("select","migla-donation");?>
                                    </option>
                                  <option value='radio' <?php echo esc_attr($arrShow[4]);?> >
                                    <?php echo __("radio","migla-donation");?>
                                    </option>
                                  <option value='multiplecheckbox' <?php echo esc_attr($arrShow[5]);?> >
                                    <?php echo __("multiple checkbox","migla-donation");?>
                                    </option>
                               </select>

                            </div><!--col ctype-->


                            <?php
                            if($f['child'][$j]['code'] == 'miglac_'){
                            ?>
                                <div class="col-sm-2 col-xs-12 mg-multival-div">
                                      <button  <?php echo $display_editval; ?> class="mbutton edit_select_value btn" data-toggle="modal" target="#mg_multival_modal" data-myuid="<?php echo esc_attr($f['child'][$j]['uid']);?>">
                                        <?php echo __("Enter Values","migla-donation");?></button>
                                </div>
                            <?php
                            }
                            ?>
                            <div class='ccode' style='display:none'>
                              <?php echo esc_html($f['child'][$j]['code']);?>
                            </div>

                            <div class='control-radio-sortable col-sm-4 col-xs-12'>
                            <?php
                              $iid    = $f['child'][$j]['id'];
                              $cekid  = $f['child'][$j]['id'];

                              if( $cekid == 'amount' ){
                                ?>
                                  <span>
                                    <label class='<?php ?>'>
                                      <input type='radio' name='<?php echo esc_attr($iid."st");?>' value='2' checked='checked' />
                                      <?php echo __(" Mandatory","migla-donation");?></label>
                                  </span>
                                <?php
                              }else if( $cekid == 'firstname'
                                  || $cekid == 'lastname'
                                  || $cekid == 'email' || $cekid == 'campaign'
                                )
                              {
                              ?>
                                <span>
                                  <label class='<?php ?>'>
                                    <input type='radio' name='<?php echo esc_attr($iid."st");?>'  value='1'/>
                                      <?php echo __(" Show","migla-donation");?></label></span>
                                <span>
                                  <label class='<?php ?>'>
                                    <input type='radio' name='<?php echo esc_attr($iid."st");?>' value='0'/>
                                      <?php echo __(" Hide","migla-donation");?></label></span>
                                <span>
                                  <label class='<?php ?>'>
                                    <input type='radio' name='<?php echo esc_attr($iid."st");?>' value='2' checked='checked'/>
                                    <?php echo __(" Mandatory","migla-donation");?></label>
                                </span>
                              <?php
                              }else{

                                if( strcmp( $c['status'],"0") == 0 )
                                {
                                ?>
                                  <span>
                                    <label>
                                      <input type='radio' name='<?php echo esc_attr($iid."st");?>'  value='1' />
                                        <?php echo __(" Show","migla-donation");?></label></span>
                                  <span>
                                    <label>
                                      <input type='radio' name='<?php echo esc_attr($iid."st");?>' value='0' checked='checked' />
                                        <?php echo __(" Hide","migla-donation");?></label></span>
                                  <span>
                                    <label>
                                      <input type='radio' name='<?php echo esc_attr($iid."st");?>' value='2' />
                                      <?php echo __(" Mandatory","migla-donation");?></label>
                                  </span>

                                <?php
                                }else if( strcmp( $c['status'],"1") == 0)
                                {
                                ?>
                                   <span>
                                    <label>
                                      <input type='radio' name='<?php echo esc_attr($iid);?>st'  value='1' checked='checked' />
                                        <?php echo __(" Show","migla-donation");?></label></span>
                                  <span>
                                    <label>
                                      <input type='radio' name='<?php echo esc_attr($iid);?>st' value='0' />
                                        <?php echo __(" Hide","migla-donation");?></label></span>
                                  <span>
                                    <label>
                                      <input type='radio' name='<?php echo esc_attr($iid);?>st' value='2' />
                                      <?php echo __(" Mandatory","migla-donation");?></label>
                                  </span>
                                <?php
                                }else if( strcmp( $c['status'],"2") == 0
                                          || strcmp( $c['status'],"3") == 0 )
                                {
                                ?>
                                    <span>
                                      <label>
                                        <input type='radio' name='<?php echo esc_attr($iid);?>st'  value='1' />
                                          <?php echo __(" Show","migla-donation");?></label></span>
                                    <span>
                                      <label>
                                        <input type='radio' name='<?php echo esc_attr($iid);?>st' value='0' />
                                          <?php echo __(" Hide","migla-donation");?></label></span>
                                    <span>
                                      <label>
                                        <input type='radio' name='<?php echo esc_attr($iid);?>st' value='2' checked='checked' />
                                        <?php echo __(" Mandatory","migla-donation");?></label>
                                    </span>
                              <?php
                                }

                              }//if $cekid
                              ?>

                              <span>
                                <button class='removeField <?php echo esc_attr($op);?>' <?php echo esc_attr($disabled);?> >
                                <i class='fa fa-fw fa-trash'></i></button>
                              </span>

                            </div><!--control-radio-sortable-->

                          </li>
                          <?php

                            $i++;

                          }//If Free Version
                          }//foreach
                          ?>

                        <?php
                        }
                        ?>

                      </ul>

                    </li>

                <?php
                      $id++;
                    }
                    ?>


                  <?php
                  } ?>
                 </ul>

                <div class='row'>
                  <div class='col-sm-6'>
                    <button value='save' class='btn btn-info pbutton miglaSaveForm' id='miglaSaveFormBottom'>
                      <i class='fa fa-fw fa-save'></i>
                        <?php echo __("  save form","migla-donation");?>
                      </button>
                  </div>
                  <div class='col-sm-6'>
                    <button id='miglaResetForm' class='btn btn-info rbutton pull-right' value='reset' data-toggle='modal' data-target='#confirm-reset'>
                      <i class='fa fa-fw fa-refresh'></i>
                      <?php echo __("  Restore to Default","migla-donation");?>
                    </button>
                  </div>
                </div>

               </div>

              </div>

            </div><!--collapse-->
          </section>
        </div>

        </div>
        </div><!--row before section3-->
        <!--------------------------------------------------------section3-->

      </div><!--tab content-->

    </div>
  </div>
</div>

<?php
}//else
?>


    <div class='modal fade' id='mg_multival_modal' tabindex='-1' role='dialog' data-backdrop='true' style="top:25%!important;">
        <div class='modal-dialog'>

          <div class='modal-content wrapper-overlay'>

          <div class="mg-overlay">
              <div id='mg_multival_modal-overlay' class="mg-loading hideme">Loading&#8230;</div>


                <div class='modal-header'>
                    <button data-target='#mg_multival_modal' aria-hidden='true' data-dismiss='modal' class='close' type='button'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title'><?php echo __(" Edit Values","migla-donation");?> </h4>
                </div>

        <div class='modal-wrap clearfix'>
           <div class='modal-body'>
          <div class='form-horizontal'>

          <input type='hidden' value='' id='mg_addval_uid' />
          <div class='form-group '>

            <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'><label class='control-label' for='mg_add_value'><?php echo __("Value","migla-donation");?></label>
            </div>
            <div class='col-sm-6 col-xs-12'>
             <input type='text' id='mg_add_value'><span class='help-control'><?php echo __("The value stored in your database","migla-donation");?></span>
            </div>

             <div class='col-sm-3 hidden-xs'></div></div>

            <div class='form-group '>

          <div class='col-sm-3 col-xs-12  text-right-sm text-center-xs'>   <label class='control-label' for='mg_add_label'><?php echo __("Label","migla-donation");?></label></div>

          <div class='col-sm-6 col-xs-12'> <input type='text' id='mg_add_label'><span class='help-control'><?php echo __("What the user sees on the form","migla-donation");?></span>
          </div>
          <div class='col-sm-3'> <button type='button' class='btn btn-info obutton' id='miglaAddCustomValueForm'><i class='fa fa-plus'></i><?php echo __(" Add","migla-donation");?></button>
          </div>
          </div>


         <div class='form-group '>
             <hr><div class='help-control-center'><?php echo __("You can drag the list item to reorganize. Here are the available list values:","migla-donation");?></div><br>

         <div id='mg-multival-spinner' class='col-sm-12 col-xs-12 text-center-sm'><i class='fa fa-fw fa-spinner fa-spin'></i></div>

          <div class='col-sm-12 col-xs-12 text-center-sm' id='mg_custom_list_container'>
                </div>

          </div>
          </div> <!--Touching-->
          </div><!--body-->

        </div>

        <div class='modal-footer'>
          <button type='button' class='btn btn-default mbutton' data-dismiss='modal'><?php echo __("Cancel","migla-donation");?></button> <button type='button' class='btn btn-info obutton' id='miglaAddCustomValues'><i class='fa fa-check'></i><?php echo __("great, I'm done","migla-donation");?></button>
        </div>

                  </div>

        </div><!--modalcontent-->


    </div>

    </div><!--End of modal-->

    <div class='modal fade' id='confirm-reset' tabindex='-1' role='dialog' aria-labelledby='miglaWarning' aria-hidden='true' data-backdrop='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>

                <div class='modal-header'>


                    <button type='button' id='mg-restore-cancel' class='close' data-dismiss='modal' aria-hidden='true' data-target='#confirm-reset'><i class='fa fa-times'></i></button>
                    <h4 class='modal-title' id='miglaConfirm'><?php echo __(" Confirm Restore","migla-donation");?></h4>
                </div>

                <div class='modal-wrap clearfix'>

                           <div class='modal-alert'>
                                            <i class='fa fa-times-circle'></i>
                                          </div>

                   <div class='modal-body'>
                 <p><?php echo __("Are you sure you want to restore to default fields? This cannot be undone","migla-donation");?></p>
                                </div>

                </div>

                <div class='modal-footer'>
                    <button type='button' class='btn btn-default mbutton' data-dismiss='modal'><?php echo __("Cancel","migla-donation");?></button>
                    <button type='button' class='btn btn-danger danger rbutton' id='miglaRestore'><i class='fa fa-fw fa-refresh'></i><?php echo __("Restore to default","migla-donation");?></button>

                </div>
            </div>
        </div>
    </div>

    </div><!--fluid-->
  </div><!--wrap-->

  <?php
}//form per campaign page

}//ENDOFCLASS

$obj = new migla_campaign_menu_class();
?>