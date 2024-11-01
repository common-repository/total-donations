<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_email_settings_class extends MIGLA_SEC
{

    function __construct()
    {
        add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 3 );
    }

    function menu_item(){
        add_submenu_page(
          'migla_donation_menu_page',
          __( 'Email Settings', 'migla-donation' ),
          __( 'Email Settings', 'migla-donation' ),
          'read_email_receipt',
          'migla_donation_settings_page',
          array( $this, 'menu_page' )
        );
    }

    function menu_page()
    {
      if ( is_user_logged_in() ){
        $this->create_token('migla_donation_settings_page',session_id() );
        $this->write_credentials( 'migla_donation_settings_page', session_id() );

        if( isset($_GET['set']) && sanitize_text_field($_GET['set']) == '1' ){
            $frm = 0;
            if( isset($_GET['frm']) ){
                $frm = sanitize_text_field($_GET['frm']);
            }else{
                $frm = 0;
            }
            $this->email_receipt_page($frm);
        }else{
            $this->home_page();
        }
      }else{
        $error = "<div class='wrap'><div class='container-fluid'>";
        $error .= "<h2 class='migla'>";
        $error .= __("You do not have sufficient permissions to access this page. Please contact your web administrator","migla-donation"). "</h2>";
        $error .= "</div></div>";

        wp_die( __( $error , 'migla-donation' ) );
      }
    }

    function home_page()
    {
        $objC = new MIGLA_CAMPAIGN;
        $objE = new MIGLA_EMAIL;
        
        $is_thanks = $objE->get_column( 0, 'is_thankyou_email' );
        ?>
          <div class='wrap'>
              <div class='container-fluid'>

              <h2 class='migla'><?php echo __( "Set Email & Receipts","migla-donation");?></h2>
                  <input type="hidden" id="migla_page" value="home">

                  <div class="row form-horizontal">
                  <div class='col-sm-12'>
                      <section class='panel'>
                      <header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                          <div class='panel-actions'>
                          <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
                          </div>
                          <h2 class='panel-title'><div class='fa fa-cogs'></div>
                          <?php echo __( "Set Emails & Receipts for these forms","migla-donation");?></h2>
                      </header>

                      <div id='collapseOne' class='panel-body collapse show'>
                          <table class="table table-striped">
                      <thead>
                        <tr>
                          <th><?php echo __( "Thank You Email Template","migla-donation");?></th>
                          <th><?php echo __( "Action","migla-donation");?></th>
                          <th><?php echo __( "On/Off","migla-donation");?></th>
                        </tr>
                      </thead>
                      <tbody>
                      <tr>
                          <td><?php echo __("Multi Campaign Form", "migla-donation")?>
                          </td>
                          <td>
                              <a class="btn btn-default obutton" href="<?php echo get_admin_url()."admin.php?page=migla_donation_settings_page&set=1&frm=0";?>"><i class="fa fa-pencil"></i></a>
                          </td>
                          <td>
                            <label>
                              <input type="checkbox" class="mg-switch" name="form-0" data-name="form-0" <?php if($is_thanks==1) echo 'checked'?> data-toggle="toggle">
                            </label>                           
                          </td>
                      </tr>


                  </tbody>
                </table>
                      </div>

                       </section>
                   </div>
                  </div>
              <?php
                  $objO = new MIGLA_OPTION;
                  $use_PHPMailer = $objO->get_option('migla_use_PHPMailer');
                  $host = $objO->get_option('migla_smtp_host');
                  $user = $objO->get_option('migla_smtp_user');
                  $pass = $objO->get_option('migla_smtp_password');
                  $authenticated = $objO->get_option('migla_smtp_authenticated');
                  $secure = $objO->get_option('migla_smtp_secure');
                  $port = $objO->get_option('migla_smtp_port');
              ?>
                  <div class="row form-horizontal">
                      <div class='col-sm-12'>
                      <section class='panel'>
                      <header class='panel-heading'>
                          <div class='panel-actions'>
                              <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapse2' aria-expanded='true'></a>
                          </div>
                          <h2 class='panel-title'><div class='fa fa-cogs'></div>
                              <?php echo __( "SMTP Settings","migla-donation");?></h2>
                       </header>

                      <input type="hidden" id="mg-use-PHPMailer-val" value="<?php echo esc_html($use_PHPMailer);?>">
                      <input type="hidden" id="mg-is-authenticated-val" value="<?php echo esc_html($authenticated);?>">

                      <div id='collapse2' class='panel-body collapse show'>
                          <div class="row">
                              <div class="col-sm-3">
                              <label class="control-label text-right-sm text-center-xs">
                                  <?php echo __("Use PHPMailer","migla-donation");?>
                              </label>
                          </div>
                              <div class="col-sm-6">
                                  <label class="checkbox-inline"><input type="checkbox" id="mg-use-PHPMailer" <?php if( $use_PHPMailer == 'yes') echo 'checked'?> data-toggle="toggle"></label>
                                  </div>
                              <div class="col-sm-3"></div>
                          </div>

                          <div class="row show_if_use_PHPMailer">
                              <div class="col-sm-3">
                              <label class="control-label text-right-sm text-center-xs">
                                  <?php echo __("Host","migla-donation");?>
                              </label>
                              </div>
                              <div class="col-sm-6">
                                  <input type="text" id="mg-host" class="form-control" value="<?php echo esc_html($host);?>">
                                  </div>
                              <div class="col-sm-3"></div>
                          </div>

                          <div class="row show_if_use_PHPMailer">
                              <div class="col-sm-3">
                              <label class="control-label text-right-sm text-center-xs">
                                  <?php echo __("User","migla-donation");?>
                              </label>
                              </div>
                              <div class="col-sm-6">
                                  <input type="text" id="mg-user" class="form-control" value="<?php echo esc_html($user);?>">
                                  </div>
                              <div class="col-sm-3"></div>
                          </div>

                          <div class="row show_if_use_PHPMailer">
                              <div class="col-sm-3">
                              <label class="control-label text-right-sm text-center-xs">
                                  <?php echo __("Password","migla-donation");?>
                              </label>
                              </div>
                              <div class="col-sm-6">
                                  <input type="password" id="mg-password" class="form-control" value="<?php echo esc_html($pass);?>">
                                  </div>
                              <div class="col-sm-3"></div>
                          </div>

                          <div class="row show_if_use_PHPMailer">
                              <div class="col-sm-3">
                                <label class="control-label text-right-sm text-center-xs"><?php echo __("Authenticated?","migla-donation");?></label>
                              </div>
                              <div class="col-sm-6">
                                <label class="checkbox-inline"> <input type="checkbox" id="mg-is-authenticated" <?php if( $authenticated == 'yes') echo 'checked'?> data-toggle="toggle"></label>
                              </div>
                              <div class="col-sm-3"></div>
                          </div>

                          <div class="row show_if_use_PHPMailer">
                              <div class="col-sm-3">
                              <label class="control-label text-right-sm text-center-xs">
                                  <?php echo __("Security Protocol","migla-donation");?>
                              </label>
                              </div>
                              <div class="col-sm-6">
                                  <select id="mg-secure" class="form-control">
                                      <option value=""><?php echo __("Normal","migla-donation");?></option>
                                      <option value="tls"><?php echo __("TLS","migla-donation");?></option>
                                      <option value="ssl"><?php echo __("SSL","migla-donation");?></option>
                                  </select>
                                  </div>
                              <div class="col-sm-3"></div>

                          </div>

                          <div class="row show_if_use_PHPMailer">
                              <div class="col-sm-3">
                              <label class="control-label text-right-sm text-center-xs">
                                  <?php echo __("Port","migla-donation");?>
                              </label>
                              </div>
                              <div class="col-sm-6">
                                  <input type="text" id="mg-port" class="form-control" value="<?php echo esc_html($port);?>">
                                  </div>
                              <div class="col-sm-3"></div>
                          </div>

                          <div class="row">
                              <div class="col-sm-3 col-xs-hidden"></div>
                              <div class="col-sm-6 text-center">
                                <button id="mg-save-smtp-btn" class="btn btn-info pbutton" value="">
      					                  <i class="fa fa-fw fa-save"></i><?php echo __(" save","migla-donation");?>
                                </button>
                              </div>
                              <div class="col-sm-3 col-xs-hidden"></div>
                          </div>

                      </div>

                       </section>
                      </div>
                  </div>


              <!--wrap-->
              </div>
          </div>
          <?php
    }

    function email_receipt_page($frm)
    {
      $language = get_locale();

      $objE = new MIGLA_EMAIL;
      $objRd = new MIGLA_REDIRECT;
      $objO = new MIGLA_OPTION;
      $objLc = new MIGLA_LOCAL;
        
      $data = $objE->get_email_by_idlanguage($frm, $language);
      $rd = $objRd->get_info($frm, $language);

      $cmp = array();

      if( $frm > 0 )
      {
        $objC = new MIGLA_CAMPAIGN;
        $cmp = $objC->get_info_by_campaign( $frm, $language );
      }

      $prev_id = $objO->get_option("migla_preview_page");
      $preview_url = "";
        
      if(!empty($prev_id)){
        $preview_url = get_permalink( $prev_id );    
      }
    ?>

    <div class='wrap'>
        <div class='container-fluid'>
            <input type="hidden" id="migla_page" value="email_receipt">

            <h2 class='migla'><?php 
                    echo __("Settings for ","migla-donation") ; 
                    if($frm == 0) echo __("Multi Campaign Form ","migla-donation"); 
                    else echo esc_html($cmp['name']);
                    ?>
            </h2>
                <a class='mg_go-back' href='<?php echo get_admin_url()."admin.php?page=migla_donation_settings_page";?>' >
                  <i class='fa fa-fw fa-arrow-left'></i>
                  <?php echo __(" Go back to Main Page", "migla-donation");?>
                </a>
    <div class='form-horizontal'>
    <br>
    <ul class='nav nav-pills'>
      <li class='active' ><a data-toggle='tab' href='#section1' class="active show"><?php echo __("Email Settings","migla-donation");?></a></li>
      <li><a data-toggle='tab' href='#section2'><?php echo __("Redirect/Thank You Page","migla-donation");?></a></li>
    </ul>

    <input type="hidden" id="migla_language" value="<?php echo esc_html($language);?>"/>
    <input type="hidden" id="migla_form_id" value="<?php echo esc_html($frm);?>"/>
    <input type="hidden" id="migla_page" value="email_receipt"/>
    <input type="hidden" id="migla_email_id" value="<?php echo esc_html($data['id']);?>"/>
    <input type="hidden" id="migla_preview_id" value="<?php echo esc_html($prev_id);?>"/>
    
    <div class='tab-content nav-pills-tabs' >

    <div id='section1' class='tab-pane active'>

    <div class='row'>

    <div class='col-sm-12'>
    <section class='panel'>
    <header class='panel-heading'>
        <div class='panel-actions'>
        <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseOne' aria-expanded='true'></a>
        </div>
        <h2 class='panel-title'><div class='fa fa-cogs'></div>
        <?php echo __( "Notifications and Email Settings","migla-donation");?></h2>
    </header>

    <div id='collapseOne' class='panel-body collapse show'>

    <div class='row'>
        <div class='col-sm-3'>
            <label for='miglaNotifEmails' class='control-label text-right-sm text-center-xs'>
            <?php echo __( 'Email(s) to Notify Upon New Donation', 'migla-donation' );?>
            </label>
        </div>
        <div class='col-sm-6 col-xs-12'>
            <input class='form-control' id='miglaNotifEmails' type='text' value=''/>
        </div>
        <div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'>
            <button id='migla-add-notify-btn' class='btn obutton' value='save'><i class="fa fa-fw fa-plus-square-o"></i></button>
        </div>
    </div>
    <div class='row'>
            <ul id="mg-list_notifies">
            <?php
            if( !empty($data['notify_emails']) )
            {
                $emails = (array)unserialize( $data['notify_emails'] );
                foreach( $emails as $email )
                {
                ?>
                <li>
                    <div class='col-sm-3'></div>
                    <div class='col-sm-6 col-xs-12'>
                            <input type='hidden' class='li-notif-email' value='<?php echo esc_html($email);?>'>
                            <?php echo esc_html($email);?>
                    </div>
                    <div class='col-sm-3 spacer'>
                      <button class='remove-notify btn rbutton'><i class='fa fa-trash'></i></button>
                    </div>
                </li>
                <?php
                }
            }
            ?>
            </ul>

    </div>
    <div class='row'>
      <div class='col-sm-3'><label for='miglaReplyToTxt' class='control-label text-right-sm text-center-xs'>
        <?php echo __( 'Email Address: ', 'migla-donation' );?></label>
      </div>
      <div class='col-sm-6 col-xs-12'>
          <input type='text' id='miglaReplyToTxt' placeholder='<?php echo esc_attr($data['reply_to']);?>' value='<?php echo esc_html($data['reply_to']);?>' class='form-control'>
      </div>
      <div class='col-sm-3 col-xs-hidden text-left-sm text-center-xs'></div>
        <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
          <?php echo __("The is the address all your emails will appear from when a donor receives an email","migla-donation");?>
        </span>
    </div>
    <div class='row'>
    <div class='col-sm-3'>
        <label for='miglaReplyToNameTxt' class='control-label text-right-sm text-center-xs'>
          <?php echo __( 'Email Name : ', 'migla-donation' );?>
        </label>
    </div>
    <div class='col-sm-6 col-xs-12'>
        <input type='text' id='miglaReplyToNameTxt' class='form-control' placeholder='<?php echo esc_attr($data['reply_to_name']);?>' value='<?php echo  esc_html($data['reply_to_name']);?>' class='form-control' />
    </div>
    <div class='col-sm-3 col-xs-hidden'></div>
    <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'><?php echo __("This is the name that all of your emails will appear from","migla-donation");?></span>
    </div>

    <?php  $isThankEMail = $data['is_thankyou_email'] == '1';  ?>

    <div class='row'>
      <div class='col-sm-3 col-xs-12'>
          <label for='mg-isThankEMail' class='control-label text-right-sm text-center-xs'><?php echo __("Enable Thank You Emails:","migla-donation");?></label>
      </div>
      <div class='col-sm-9 col-xs-12 text-left-sm text-center-xs'>
          <label class='' for='mg-isThankEMail'>
              <input type="checkbox" data-name="<?php echo $data['id'];?>" id="mg-status-thank-email" <?php if($isThankEMail) echo 'checked'?> data-toggle="toggle">&nbsp;
              <?php echo __(" Switch this off if you'd like no 'thank you email' to be sent to your donors after they donate","migla-donation");?>
          </label>
      </div>
      <div class='col-sm-3 hidden-xs'></div>
    </div>

    <div class="row">
        <div class='col-sm-3 col-xs-hidden'></div>
        <div class="col-sm-6 col-xs-12">
            <button id='migla-emailsets-btn' class='btn btn-info pbutton' value='save'>
            <i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?></button>
        </div>
       <div class='col-sm-3 col-xs-hidden'></div>
    </div>

    </div><!--collapse-->

    </section></div>

    <div class='col-sm-12'>
    <section class='panel'>
    <header class='panel-heading'>
    <div class='panel-actions'>
    <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a>
    </div>
    <h2 class='panel-title'><i class='fa fa-envelope-o'></i>
    <?php echo __("Thank you Email","migla-donation");?>
    </h2>

    </header>
    <div id='collapseTwo' class='panel-body collapse show'>

    <div class='form-horizontal'>
    <div class='form-group touching'>
    <div class='col-sm-3 col-xs-12'><label for='migla_thankSbj' class=' control-label text-right-sm text-center-xs'>
    <?php echo __(" Email Subject:","migla-donation");?></label>
    </div>
    <div class='col-sm-6 col-xs-12'>
    <input type='text' name='migla_thankSbj' id='migla_thankSbj' class='form-control touch-top' title='Please enter subject of email' placeholder='' required='' value='<?php echo esc_html($data['subject']);?>'>
    </div>
    <div class='col-sm-3 hidden-xs'></div></div><div class='form-group touching '><div class='col-sm-3'><label for='miglaThankBody' class=' control-label text-right-sm text-center-xs'><?php echo __(" Thank you Email Text Body: ","migla-donation");?></label>
    </div>
    <div class='col-sm-6 col-xs-12'>
    <?php
      $settings =   array(
          'wpautop' => true,
          'media_buttons' => true,
          'textarea_name' => 'migla_ThanksEmail_editor',
          'textarea_rows' => 50,
          'tinymce' => true
      );
      wp_editor( stripslashes( $data['body'] ) , 'migla_ThanksEmail_editor', $settings  );
    ?>
    </div>
    <div class='col-sm-3'> </div>
    </div>

    <div class='form-group touching '>
        <div class='col-sm-3 col-xs-12'>
            <label for='migla_thankAnon' class='control-label text-right-sm text-center-xs'><?php echo __("Anonymous Donations:","migla-donation");?></label>
        </div>
    <div class='col-sm-6 col-xs-12'>
        <input required='' placeholder='' class='form-control touch-bottom' title='' rows='5' name='migla_thankAnon' id='migla_thankAnon' style='overflow: hidden;' value='<?php echo esc_html($data['anonymous']);?>'>
    </div>

    <div class='col-sm-3 hidden-xs'></div>
    </div>

    <div class='form-group'>
        <div class='col-sm-3 col-xs-12'></div>
            <div class='col-sm-6 col-xs-12'> <br>
            <button id='migla_emailbody_btn' class='btn btn-info savebutton pbutton' value='save'>
              <i class='fa fa-fw fa-save'></i>
              <?php echo __("Save in","migla-donation")." ". esc_html($objLc->get_country_from_language( $language ));?>
            </button>

                <div class='row'><br>
                  <label class='col-sm-12'><br>
                    <?php echo __(" Use the following shortcodes in the email body:","migla-donation");?></label>

                    <div class='col-sm-12'>
                        <code>[firstname]</code><?php echo __(" Donor's First Name", "migla-donation");?><br>
                        <code>[lastname]</code><?php echo __(" Donor's Last Name","migla-donation");?><br>
                        <code>[amount]</code><?php echo __(" Donation Amount","migla-donation");?><br>
                        <code>[date]</code><?php echo __(" Donation date ","migla-donation");?><br>
                        <code>[campaign]</code><?php echo __(" Donation campaign ","migla-donation");?><br>
                        <code>[if_anonymous]</code><?php echo __(" Anonymous Donations message will appear if this donation is anonymous ","migla-donation");?><br>
                    </div>
                </div>
            </div>

         <div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'>

    </div>

    </div>

</div>

    <div class='row'>
      <div class='col-sm-3'>
        <label for='miglaTestEmailAdd' class='control-label text-right-sm text-center-xs'><?php echo __( 'Email address for Test:', 'migla-donation' );?></label>
      </div>
      <div class='col-sm-6 col-xs-12'><input class='form-control' id='miglaTestEmailAdd' type='text' value='' /></div>
      <div class='col-sm-3 col-xs-12 text-left-sm text-center-xs'>
        <button id='miglaTestEmail' class='btn btn-info obutton' value='Send Testing Email'>
          <i class='fa fa-fw fa-envelope-o'></i>
          <?php echo __(" Preview Email","migla-donation");?></button>
      </div>
      <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
        <?php echo __(" Use this to preivew what your donors will see when they donate.","migla-donation");?></span>
    </div>

</div></div>

    </div>
    </div><!--Section1-->

  <div id='section2' class='tab-pane'>
    <div class='row'>
      <div class='col-sm-12'>

    <section class='panel'>
    <header class='panel-heading'>
      <div class='panel-actions'>
          <a class='fa fa-caret-down ' data-toggle='collapse' data-parent='.panel' href='#collapseFive' aria-expanded='true'></a>
      </div>
      <h2 class='panel-title'><div class='fa fa-heart-o'></div><?php echo __("Thank You Page","migla-donation");?><span class='panel-subtitle'>
          <?php echo __("The page that appears after you donate","migla-donation");?></span>
      </h2>
    </header>

    <div id='collapseFive' class='panel-body collapse show'>

    <div class='row'>
  <div class='col-sm-3 col-xs-12'>
  <label for='miglaSetThankYouPage' class='control-label text-right-sm text-center-xs'>
    <?php echo __("Set The Thank You Page Here:","migla-donation");?>
  </label>
  </div>

    <?php
      $pages         = $this->get_all_posts();
      $is_page_exist = false;
      $language      = get_locale();
      $content       = '';
      $page_id       = $rd['pageid'];
    ?>
        <div class='col-sm-6 col-xs-12'>
          <select id='migla_SetThankYouPage'>
          <?php
            foreach( $pages as $key )
            {
                if(  $page_id == $key['id'] )
                {
                    ?>
                    <option value='<?php echo esc_html($key['id']);?>' selected><?php echo  esc_html($key['title']);?></option>
                    <?php
                    $is_page_exist = true;
                }else{
                    ?>
                    <option value='<?php echo esc_html($key['id']);?>' ><?php echo  esc_html($key['title']);?></option>
                    <?php
                }
            }

            if(!$is_page_exist)
            {
                ?>
                <option value=''  selected>
                    <?php echo __("Default (Go to Donation Form)","migla-donation");?></option>
                <?php
            }else{
                ?>
                <option value='' >
                    <?php echo __("Default (Go to Donation Form)","migla-donation");?></option>
                <?php
            }

          ?>
        </select>
      </div>

      <div class='col-sm-3 col-xs-hidden'></div>
      <span class='help-control col-sm-12 col-sm-pull-3  text-right-sm text-center-xs'>
        <?php echo __(" If you set the thank you page to any page other than the default, you must add this shortcode to this page:","migla-donation"). "<code>". __("[totaldonations_thank_you_page]","migla-donation")."</code>";?>
      </span>
    </div>

    <?php
    foreach( $pages as $key )
    {
    ?>
        <input id="mg-url-<?php echo esc_attr($key['id']);?>" type="hidden" value="<?php echo  esc_html($key['url']);?>">
    <?php
    }

    ?>

    <div class='row'>
    <div class='col-sm-12'>

    <div id='content' style='display:none'><?php echo esc_html($rd['content']);?></div>
    <?php
    $settings =   array(
        'wpautop' => true, // use wpautop?
        'media_buttons' => true, // show insert/upload button(s)
        'textarea_name' => 'migla_thankyoupage_editor', // set the textarea name to something different, square brackets [] can be used here
        'textarea_rows' => 30, // rows="..."
        'tinymce' => true
    );
    wp_editor(  stripslashes($rd['content']) , 'migla_thankyoupage_editor', $settings  );

    $objL = new MIGLA_OPTION;

    ?>
    </div>

    <div class='col-sm-12'>
    <span>
      <br>
      <button id='migla_ThankPage_btn' class='btn btn-info pbutton' value='save'>
          <i class='fa fa-fw fa-save'></i><?php echo __(" save in ","migla-donation"). esc_html($objLc->get_country_from_language($language));?>
      </button>

      <div id='migla_urlshortcode' style='display:none'></div>

      <form id='miglaFormPreviewThank' style='display:inline;' action='<?php echo esc_url($preview_url);?>' method='GET' target='_blank'>
          <input type='hidden' name='thanks' value='testThanks' />
          <input type="hidden" name="page_id" value="<?php echo esc_html($prev_id);?>" />
          <input type="hidden" name="src_id" value="<?php echo esc_html($objO->get_option("migla_listen"));?>" />
      </form>

      <button id='miglaThankPagePrev' class='btn btn-info obutton' value='Preview Page'><i class='fa fa-fw fa-search'></i>
        <?php echo __(" Preview","migla-donation");?></button>
    </span> <br><br>
    <?php
        echo "&nbsp;&nbsp;".__("Shortcodes allowed:","migla-donation")." <code>[firstname][lastname][amount][date]</code>";
    ?>

    <br>
    </div>

    </div><!--collapse-->

    </section>

</div>

    </div></div><!--Section2-->



    </div><!--nav-pills-tabs-->
    </div><!--Form Horizontal-->

    </div></div>
   <?php
    }

    function get_all_posts()
    {
        global $wpdb;
        $post_obj = array();
        $post_obj = $wpdb->get_results(
                   $wpdb->prepare(
                     "SELECT ID,post_title  FROM {$wpdb->prefix}posts WHERE post_type = %s" ,
                     'page'
                         )
                   );

        $post_array = array();  $i = 0;

        foreach( $post_obj as $post )
        {
            $post_array[$i]['id'] = $post->ID;
            $post_array[$i]['title'] = $post->post_title;
            $post_array[$i]['url'] = get_permalink($post->ID);
            $i++;
        }

       return $post_array ;
    }

}

$obj = new migla_email_settings_class();
?>
