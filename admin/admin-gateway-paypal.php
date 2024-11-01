<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_menu_gateway_paypal extends MIGLA_SEC
{
  function __construct()
  {
    add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 7 );
  }
  
  function menu_item()
  {
    add_submenu_page(
      'migla_donation_menu_page',
      __( 'Paypal Settings', 'migla-donation' ),
      __( 'Paypal Settings', 'migla-donation' ),
      'read_gateway',
      'migla_donation_paypal_settings_page',
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
      $this->create_token( 'migla_donation_paypal_settings_page', session_id() );
      $this->write_credentials( 'migla_donation_paypal_settings_page', session_id() );


    $objO = new MIGLA_OPTION;

    $payment['sandbox'] = '';  
    $payment['paypal']  = '';
    $paymentMethod      = $objO->get_option( 'migla_paypal_payment' ) ;
    $payment[ $paymentMethod ] = 'selected';   
    $pEmail             = $objO->get_option( 'migla_paypal_emails' ) ;
    $pEmailName         = $objO->get_option( 'migla_paypal_emailsname' ) ;

    $objO = new MIGLA_OPTION;
    $objF = new CLASS_MIGLA_FORM;
    $objL = new MIGLA_LOCAL;
    $cc = (array)unserialize($objF->get_meta( 0, 'paypal_tab_info', $objL->get_origin() ));

    $paypal_type = $objO->get_option('migla_paypal_pro_type');  

    $tb = $objF->paypal_tab($cc);
    ?>
    <div class='wrap'>
      <div class='container-fluid'>  
        <h2 class='migla'><?php echo __("Paypal Settings","migla-donation");?></h2>

        <div class='row form-horizontal'>
          <div class='col-sm-12'>
             <section class='panel'>
                <header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                  <div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a>
                  </div>
                    <h2 class='panel-title'><i class='fa fa-paypal'></i>
                      <?php echo __("Account Settings","migla-donation");?>   
                      <span class="panel-subtitle"><?php echo __("Your PayPal Account Settings ","migla-donation");?></span>
                    </h2>
                </header>
                <div id='collapseTwo' class='panel-body collapse show'>
                    <!--collapse-->
                    <div class='row'>
                        <div class='col-sm-3'>
                          <label for='miglaPaypalEmails' class='control-label text-right-sm text-center-xs'>
                            <?php echo __("Business Email","migla-donation");?>
                          </label>
                        </div>
                        <div class='col-sm-6 col-xs-12'>
                          <input type='text' id='miglaPaypalEmails' value='<?php echo esc_html($pEmail);?>' class='form-control'>
                        </div>
                   
                        <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'><?php echo __("The PayPal address you use for accepting donations. ","migla-donation");?>
                        
                        </span>
                    </div>

                    <?php
                    $objO = new MIGLA_OPTION;

                    ?>

                    <div class='row'>
                      <div class='col-sm-3'>
                        <label for='miglaPaypalListener' class='control-label text-right-sm text-center-xs'>
                          <?php echo __("IPN Listener:","migla-donation");?>
                          </label>
                        </div>
                        <div class='col-sm-6 col-xs-12'>
                            <input type='text' value='<?php echo esc_html($this->get_current_server_url()) . "/index.php?pl=".esc_attr($objO->get_option('migla_listen'));?>' />
                        </div>
                    </div>       

                    <div class='row'>
                          <div class='col-sm-3'>
                            <label for='migla_ipn_chatback' class='control-label text-right-sm text-center-xs'>
                                <?php echo __("IPN ChatBack:","migla-donation");?></label>
                          </div>
                          <div class='col-sm-9 col-xs-12'>
                            <?php

                            if( $objO->get_option('migla_ipn_chatback') == 'yes' ){
                              $chk_chatback = "checked";
                            }else{
                              $chk_chatback = "";
                            }

                            ?>

                            <label class='checkbox-inline'>
                              <input type='checkbox' id='migla_ipn_chatback' <?php echo esc_attr($chk_chatback);?>> 
                              <?php echo __(" Check this if you'd like to disable chatback with PayPal. See documentation for details","migla-donation");?>
                            </label>
                            <?php
                            if(  gethostbyname ( 'www.paypal.com' ) == 'www.paypal.com' )
                            {  ?>
                                 <span class='help-control col-sm-12   text-right-sm text-center-xs'><em style='color:red;'>
                                  <?php echo __("Warning: Could not resolved PayPal hostname. Chat Back has failed.","migla-donation")?></em> </span>
                            <?php
                            }else{
                              ?>
                                 <span class=' box-success checkbox-inline'> <?php echo __("Success:  Hostname was resolved successfully! ","migla-donation");?></span>
                            <?php
                            }
                          ?>
                        </div>
                    </div>    

                    <?php 
                    if( $paymentMethod == 'sandbox' )
                    { 
                      $snd = "checked";
                      $live = "";
                    }else{
                      $snd = "";
                      $live = "checked";
                    }
                    ?>

                    <div class='row'>
                      <div class='col-sm-3'>
                        <label for='mg_sandbox' class='control-label text-right-sm text-center-xs'>
                          <?php echo __("Type:","migla-donation");?>
                          </label>
                      </div>
                      <div class='col-sm-6'>
                        <select id='mg_payment'>
                          <option value='sandbox' <?php if($paymentMethod=="sandbox") echo "selected"; ?>><?php echo __("Sandbox PayPal","migla-donation");?></option>
                          <option value='paypal' <?php if($paymentMethod=="paypal") echo "selected"; ?>><?php echo __("PayPal","migla-donation");?></option>
                        </select>
                      </div>
                    </div>

                    <div class='row'>
                      <div class='col-sm-3 col-xs-12'></div>
                      <div class='col-sm-6 center-button'>
                        <button id='miglaUpdatePaypalAccSettings' class='btn btn-info pbutton' value='save'>
                          <i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?>
                        </button>
                      </div>
                    </div>


                    <!--collapse-->
                  </div>
              </section>
            </div>
            
            
            <div class='col-sm-12'>
             <section class='panel'>
                <header class='panel-heading'>
                  <div class='panel-actions'>
                    <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a>
                  </div>
                    <h2 class='panel-title'><i class='fa fa-paypal'></i>
                      <?php echo __("Tab Text","migla-donation");?> <span class="panel-subtitle">
               <?php echo __("The tab label and notification text on the frontend form ","migla-donation");?>         </span></h2>
                </header>
                <div id='collapseThree' class='panel-body collapse show'> 
                  <!--collapse-->

                    <div class='row'> 
                      <div class='col-sm-3 col-xs-12'>
                        <label class='control-label text-right-sm text-center-xs' for='mg_tab-paypalpro'><?php echo __("Tab Name","migla-donation");?></label>
                      </div>
                      <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>
                          <input type="text" name="mg_tab-paypalpro" id="mg_tab-paypalpro" class="form-control" placeholder="Tab Name" value="<?php echo esc_html($this->writeme($tb['tab']));?>">
                      </div>
                    </div>                  

                    <div class='row'>
                        <div class='col-sm-3 col-xs-12'>
                          <label class='control-label text-right-sm text-center-xs' for='mg_waiting_paypal'>
                            <?php echo __("Text displayed while redirecting/processing.","migla-donation");?>
                          </label>
                        </div>
                        <div class='col-sm-6 col-xs-12 text-right-sm text-center-xs'>
                          <input type="text" name="mg_waiting_paypal" id="mg_waiting_paypal" class="form-control" placeholder="Just a moment while we redirect you to PayPal" value="<?php echo esc_html($this->writeme($tb['loading_message']));?>">
                        </div>
                        <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'></div>
                    </div>
    
                    <div class='row'>
                        <div class="col-sm-3"></div>
                        <div class='col-sm-6 center-button'>
                          <button value='save' class='btn btn-info pbutton msave' id='miglaSaveCCInfo'><i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?>
                          </button>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>

                  <!--collapse-->       
                  </div>
              </section>
            </div>

    <?php
        $btnchoice = $objO->get_option('miglaPaypalButtonChoice');
        
        $choice =  array();
        $choice['paypalButton'] = ""; 
        $choice['imageUpload'] = ""; 
        $choice['cssButton'] = "";
        
        if( empty($btnchoice) ){
            $choice['paypalButton'] = "checked"; 
        }else{
            $choice[$btnchoice] = "checked"; 
        }
        
        if( $choice['paypalButton'] = "checked" )
        {
            $btntext1 = $tb['button'];
            $btntext2 = "";
            $btnlang = $objO->get_option('migla_paypalbutton');  
        }else if( $choice['cssButton'] = "checked" ){
            $btntext1 = "";
            $btntext2 = $tb['button'];
        }else{
            $btntext1 = "";
            $btntext2 = "";
        }
        
        if(empty($btnlang )){
            $btnlang = 'english';
        }
        
        $btnurl = $objO->get_option('migla_paypalbuttonurl');
        $btnstyle = $objO->get_option('migla_paypalcssbtnstyle');
        $btnclass = $objO->get_option('migla_paypalcssbtnclass');
    ?>
          
            <div class='col-sm-12'>
              <section class='panel'>
                <header class='panel-heading'>
                <div class='panel-actions'>
                <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseNine' aria-expanded='true'></a>
                </div>
                <h2 class='panel-title'><div class='dashicons dashicons-admin-appearance'></div><?php echo __("PayPal Button","migla-donation");?></h2>
                </header>
                <div id='collapseNine' class='panel-body collapse show'>
                  <div class='form-horizontal'>

                    <div class='form-group touching'>
                      <div class='col-sm-3  col-xs-12'>
                        <label class='control-label text-right-sm text-center-xs' for='mg_CSSButtonPicker'><?php echo __("Button","migla-donation");?></label>
                      </div>
                      <div class='col-sm-6 col-xs-12'>
                        <select id='mg_CSSButtonPicker' class='form-control touch-top' name='miglaCSSButtonPicker'>
                        <?php 
                        if( $btnstyle == 'Default'){ ?>
                          <option selected='selected' value='Default'><?php echo __("Your Default Form Button","migla-donation");?></option>
                          <option value='Grey'><?php echo __("Grey Button","migla-donation");?></option>
                        <?php  
                        }else{  ?>
                          <option value='Default'><?php echo __("Your Default Form Button","migla-donation");?></option>
                          <option selected='selected' value='Grey'><?php echo __("Grey Button","migla-donation");?></option>
                        <?php
                        }  ?>
                        </select>
                      </div>
                      <div class='col-sm-3'></div>
                    </div>

                    <div class='form-group touching'>
                      <div class='col-sm-3  col-xs-12'>
                        <label for='mg_CSSButtonText' class='control-label text-right-sm text-center-xs'><?php echo __("Button Text","migla-donation");?></label>
                      </div>
                      <div class='col-sm-6 col-xs-12'> 
                        <input id='mg_CSSButtonText' type='text' value='<?php echo esc_html($tb['button']);?>' required='' placeholder='Donate Now' title='' class='form-control touch-middle' name=''>
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
                      <div class='col-sm-3'></div>
                    </div>
                    
                     <div class='form-group '>
                      <div class='col-sm-3 col-xs-12'></div>
                      <div class='col-sm-6 center-button'> 
                        <button value='save' class='btn btn-info pbutton' id='migla-save-paypal-btn'><i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?></button>
                      </div>
                      <div class='col-sm-3'></div>
                    </div>

                  </div>            
              </section> 
            </div>          
        </div>

        <!--wrap-->
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

$obj_paypal_menu = new migla_menu_gateway_paypal();
?>