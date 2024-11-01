<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_frontend_setting_class extends MIGLA_SEC
{

  function __construct()
  {
      add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 10 );
  }

  function menu_item() {
    add_submenu_page(
      'migla_donation_menu_page',
      __( 'Front End and Securities', 'migla-donation' ),
      __( 'Front End and Securities', 'migla-donation' ),
      'read_security_frontend',
      'migla_donation_front_settings_page',
      array( $this, 'menu_page' )
    );
  }

  function menu_page()
  {
    if ( is_user_logged_in() )
    {
      $this->create_token( 'migla_donation_front_settings_page', session_id() );
      $this->write_credentials( 'migla_donation_front_settings_page', session_id() );


      $objO = new MIGLA_OPTION;
      $order_of_gateways = $objO->get_option('migla_gateways_order');

      $avs_level = $objO->get_option( 'migla_avs_level' );

      global $wpdb;

      $sql = "SELECT ID, display_name FROM $wpdb->users ORDER BY ID";

      $wp_users = $wpdb->get_results($sql);

      $list     = (array)unserialize( $objO->get_option('migla_allowed_users') );

      $minlevel = 10;
    ?>
    <div class='wrap'>
        <div class='container-fluid'>
          <h2 class='migla'><?php echo __("Front End Settings","migla-donation");?></h2>

          <div class='row form-horizontal'>
            <div class='col-sm-12'>
                 <section class='panel'>
                    <header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                      <div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
                      </div>
                        <h2 class='panel-title'><i class='fa fa-paypal'></i><?php echo __("Gateway Order","migla-donation");?></h2>
                      </header>
                        <div id='collapseOne' class='panel-body collapse show'>

                          <div class='row'>
                            <div class='col-sm-3'>
                              <label class='control-label text-right-sm text-center-xs'>
                                <?php echo __("Choose the gateway(s) you'd like to use and their order:","migla-donation");?>
                              </label>
                            </div>

                            <div class='col-sm-6 col-xs-12' id='default_payment_section'>
                              <?php
                              if( $order_of_gateways == 'false' || empty($order_of_gateways) )
                              {
                                $order_of_gateways[0] = array('paypal', false);
                                $order_of_gateways[1] = array('stripe', false);
                                $order_of_gateways[2] = array('authorize', false);
                                $order_of_gateways[3] = array('offline', false);

                              }else{

                                $order_of_gateways = (array)unserialize($order_of_gateways);
                              
                                $gateways = array();
                                $gateways[0] = array('paypal', false);
                                $gateways[1] = array('stripe', false);
                                $gateways[2] = array('authorize', false);
                                $gateways[3] = array('offline', false);
                              
                                foreach( $order_of_gateways as $value ){
                                    if($value[0] == 'paypal'){
                                        $gateways[0][1] = $value[1];    
                                    }
                                    if($value[0] == 'stripe'){
                                        $gateways[1][1] = $value[1];    
                                    }
                                    if($value[0] == 'authorize'){
                                        $gateways[2][1] = $value[1];    
                                    }
                                    if($value[0] == 'offline'){
                                        $gateways[3][1] = $value[1];    
                                    }
                                }
                                
                                $order_of_gateways = $gateways;
                              }
                              
                              ?>
                               <ul class='containers' >
                               <?php
                                foreach( $order_of_gateways as $value )
                                {
                                    if( $value[0] == 'stripe' || $value[0] == 'paypal' )
                                    {
                                        $name = ucfirst( $value[0] );

                                        if( $name == 'Authorize' ){
                                            $name = 'Authorize.net';
                                        }
                                        ?>
                                        <li class='ui-state-default formfield'>
                                          <?php
                                            if( $value[1] == 'true' || $value[1] == 1 )
                                            {
                                            ?>
                                              <div class='row'>
                                                <div class='col-sm-6'>
                                                    <div class='row'>
                                                      <div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='<?php echo esc_html($value[0]);?>' checked><?php echo esc_html($name);?>
                                                      </div>
                                                    </div>
                                                  </div>
                                              </div>
                                              <?php
                                              }else{
                                              ?>
                                              <div class='row'>
                                                <div class='col-sm-6'>
                                                    <div class='row'>
                                                      <div class='col-sm-12'><input type='checkbox' class='mg_status_gateways' value='<?php echo esc_html($value[0]);?>'><?php echo esc_html($name);?>
                                                      </div>
                                                    </div>
                                                  </div>
                                              </div>
                                              <?php
                                              }  ?>
                                        </li>
                                      <?php
                                    }
                                }
                                ?>
                              </ul>

                            </div>
                          </div>

                          <div class='row'>
                            <div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'></div>
                            <div class='col-sm-6 center-button'>
                                <button id='migla-update-gateways-ord-btn' class='btn btn-info pbutton' value='save'><i class='fa fa-fw fa-save'></i>
                                  <?php echo __(" save","migla-donation");?>
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
                  <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseSecurity' aria-expanded='true'></a>
                </div>
                <h2 class='panel-title'><div class=' dashicons dashicons-lock'></div><?php echo __("Security Options","migla-donation");?></h2>
              </header>

              <div id='collapseSecurity' class='panel-body collapse show'>

                  <div class='row'>
                    <div class='col-sm-3'>
                      <div class='control-label text-right-sm text-center-xs'><?php echo __("Force Total Donations to use Address Verification Service:","migla-donation");?></div>
                    </div>
                    <div class='col-sm-6 col-xs-12'>
                   
                          <?php
                            echo __('Address Verification System (AVS) provides additional levels of confirmation that the person using the card is the legitimate owner of the card. This is useful to identify and avoid fraud.', 'migla-donation');

                            ?>

                              <strong style='color: #666;'><?php echo __(" For this to work you have to make the address and postal code fields mandatory on the form.", "migla-donation");?></strong> <br>
                    

                  <div class='row form-group'>
                    
                    <div class='col-sm-6 col-xs-12'><br>

                        <div class='radio'>
                          <label>
                            <input type='radio' id='migla_credit_card_AVS_levels1' name='migla_credit_card_AVS_levels' value='low' <?php if($avs_level=='low') echo 'checked';?>><?php echo __("Low: Check the card CVC only","migla-donation");?>
                          </label>
                        </div>
                        <div class='radio'>
                          <label>
                            <input type='radio' id='migla_credit_card_AVS_levels2' name='migla_credit_card_AVS_levels' value='medium' <?php if($avs_level=='medium') echo 'checked';?>><?php echo __("Medium: Allow partial match of Postal Code and Address Fields","migla-donation");?>
                          </label>
                        </div>
                        <div class='radio'>
                          <label>
                            <input type='radio' id='migla_credit_card_AVS_levels3' name='migla_credit_card_AVS_levels' value='high' <?php if($avs_level=='high') echo 'checked';?>><?php echo __("High: Only allow exact match of both Address and Postal Code Fields","migla-donation");?>
                          </label>
                        </div>
                    </div>
                    <div class='col-sm-3'></div>
                  </div>

                    </div>
                    <div class='col-sm-3'></div>
                  </div>

                  <div class='row'>
                    <div class='col-sm-3'></div>
                    <div class='col-sm-6 col-xs-12 center-button'>
                      <button id='migla_security_save' class='btn btn-info pbutton'>
                          <i class='fa fa-fw fa-save'> </i><?php echo __(" Save","migla-donation");?>
                      </button>
                    </div>
                    <div class='col-sm-3'></div>
                  </div>

              </div>
            </section>
          </div>
          
            <div class='col-sm-12'>
            <section class='panel'>
              <header class='panel-heading'>
                <div class='panel-actions'>
                  <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseAccess' aria-expanded='true'></a>
                </div>
                <h2 class='panel-title'><div class=' dashicons dashicons-lock'></div><?php echo __("Grant User Access:","migla-donation");?>
                  <span class='panel-subtitle'><?php echo __("Only admin users are able to change user access.","migla-donation");?></span>
                </h2>
              </header>

              <div id='collapseAccess' class='panel-body collapse show'>

                <div class='row'>

                    <div class="col-sm-12">
                      <?php
                      foreach ( $wp_users as $user )
                      {
                        $user_id      = (int)$user->ID;
                        $display_name = stripslashes($user->display_name);
                        $u            = get_userdata($user_id);
                        $is_admin     = user_can( $user_id, 'administrator' );

                        $caps         = $u->caps ;

                        $role_none = "";
                        $role_accountant = "";
                        $class_none = "";
                        $class_accountant = "";

                        if( isset($caps["totaldonation-accountant"]) ){
                            $role_accountant = "checked";
                            $class_none = "";
                            $class_accountant = "active";
                        }else{
                            $role_none = "checked";
                            $class_none = "active";
                            $class_accountant = "";
                        }
                      ?>
                        <div class='mg_li_user row' id='<?php echo esc_html('u'.$user_id);?>'>
                             <div class="row col-sm-12">
                                <div class=" col-sm-3">
                                  <label class="control-label">
                                    <input class="userid" type='hidden' value='<?php echo esc_html($user_id);?>'/>
                                    <strong><?php echo esc_html($display_name);?></strong>
                                  </label>
                                </div>
                                <div class="col-sm-6 custom-select">
                                    <?php
                                    if($is_admin)
                                    {
                                    ?>
                                    <div class="btn-group" data-toggle="buttons" for="<?php echo 'urole'.esc_attr($user_id);?>">
                                			<label class="btn btn-success active" for="<?php echo 'urole'.esc_attr($user_id);?>">
                                				<input type="radio"  class="urole" id="<?php echo 'adm-'.esc_attr($user_id);?>" name="<?php echo 'urole'.esc_attr($user_id);?>" value="administrator" checked autocomplete="off">
                                				<span class="fa fa-check"> </span> <?php echo __("Administrator","migla-donation");?>
                                			</label>
                                    </div>
                                    <?php
                                    }else{
                                    ?>
                                  <label class="btn-role" for="<?php echo 'norole-'.esc_attr($user_id);?>">
                                		<input type="radio" class="urole" id="<?php echo 'norole-'.esc_attr($user_id);?>" name="<?php echo 'urole'.esc_attr($user_id);?>" value="no_role" <?php echo esc_attr($role_none);?> autocomplete="off">
                                		<?php echo __("No Role","migla-donation");?>
                                	</label>
                                	<label class="btn-role" for="<?php echo 'acct-'.esc_attr($user_id);?>">
                                		<input type="radio"  class="urole" id="<?php echo 'acct-'.esc_attr($user_id);?>" name="<?php echo 'urole'.esc_attr($user_id);?>" value="totaldonation-accountant" <?php echo esc_attr($role_accountant);?> autocomplete="off">
                                		<?php echo __("TotalDonations Accountant","migla-donation");?>
                                	</label>
                                    <?php
                                    }?>
                                </div>
                            </div>
                        </div>
                      <?php
                      }
                      ?>
                    </div>

                </div>

                <div class='row'>
                    <div class='col-sm-3'></div>
                    <div class='col-sm-6 col-xs-12 center-button'>
                        <button id='migla-save-users' class='btn btn-info pbutton' value='save'>
    					    <i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?>
    				    </button>
                    </div>
                    <div class='col-sm-3'></div>                    
                </div>

            </div>
            </section>


        <!--wrapfluid-->
          </div></div></div>
    
      
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

$obj = new migla_frontend_setting_class();
?>
