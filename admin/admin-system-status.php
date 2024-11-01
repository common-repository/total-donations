<?php
if ( !defined( 'ABSPATH' ) ) exit;

class migla_system_status_class extends MIGLA_SEC
{

  function __construct(  )
  {
    add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 11 );
  }

  function menu_item() {
    add_submenu_page(
      'migla_donation_menu_page',
      __( 'System Status & Logs', 'migla-donation' ),
      __( 'System Status & Logs', 'migla-donation' ),
      'read_logs',
      'migla_donation_system_status_page',
      array( $this, 'menu_page' )
    );
  }

function menu_page()
{
  if ( is_user_logged_in() )
  {
    $this->create_token( 'migla_donation_system_status_page', session_id() );
    $this->write_credentials( 'migla_donation_system_status_page', session_id() );

    $x[0] = get_option('migla_thousandSep');
    $x[1] = get_option('migla_decimalSep');
    $showSep = get_option('migla_showDecimalSep');
    $numDecimal = 0;

    global $wpdb;

    $curl_info = curl_version();
    $objO = new MIGLA_OPTION;
  ?>

   <div class='wrap'>
        <div class='container-fluid'>
            <h2 class='migla'><?php echo __(" System Status","migla-donation");?></h2>

    <div class='row'>
    <div class='col-sm-12'>
        <div class='form-horizontal'>
            <ul class='nav nav-pills'>
                <li class="active mg-li-tab"><a data-toggle='tab' href='#section1' class="active show">
                  <?php echo __("System Information","migla-donation");?></a></li>
                <li class="mg-li-tab"><a data-toggle='tab' href='#section2'>
                  <?php echo __("Logs","migla-donation");?></a></li>

            </ul>

            <div class='tab-content nav-pills-tabs' >

            <div id='section1' class='tab-pane  active'>
              <section class='panel'>
                 <header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                  <div class='panel-actions'>
                    <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapse1' aria-expanded='true'></a>
                    </div>
                    <h2 class='panel-title'>
                      <i class='fa fa-cogs'></i><?php echo __("System Info","migla-donation");?></h2>
                </header>

                    <div id='collapse1' class='panel-body collapse show'>

                      <div class='row'>
                        <div class='col-sm-2'><label for='miglaServerName' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                        <?php echo __("Plugin Version:","migla-donation");?></label>
                        </div>
                        <div class='col-sm-10'><label><?php echo Totaldonations_VERSION;?></label></div>
                      </div>

                      <div class='row'>
                        <div class='col-sm-2'><label for='miglaServerName' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                        <?php echo __("Server Name:","migla-donation");?></label>
                        </div>
                        <div class='col-sm-10'><label><?php echo esc_html($_SERVER['SERVER_NAME']);?></label></div>
                      </div>

                      <div class='row'>
                        <div class='col-sm-2'><label for='miglaServerName' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                        <?php echo __("Server Software:","migla-donation");?></label></div>
                        <div class='col-sm-10'><label><?php echo esc_html($_SERVER['SERVER_SOFTWARE']);?></label></div>
                      </div>

                      <div class='row'>
                          <div class='col-sm-2'>
                            <label for='miglaServerName' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                              <?php echo __("PHP Version:","migla-donation");?></label></div>
                          <div class='col-sm-10'><label><?php echo phpversion();?></label></div>
                       </div>'

                      <div class='row'>
                           <div class='col-sm-2'>
                            <label for='miglaCurl_SSL' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                           <?php echo __("Curl SSL:","migla-donation");?>
                           </label></div>
                           <div class='col-sm-10'><label><?php echo esc_html($curl_info['ssl_version']);?></label></div>
                       </div>

                      <div class='row'>
                           <div class='col-sm-2'>
                           <label for='migla_dbname' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                           <?php echo __("Database:","migla-donation");?></label></div>
                           <div class='col-sm-10'><label><?php echo $wpdb->dbname;?></label></div>
                       </div>'

                      <div class='row'>
                           <div class='col-sm-2'>
                           <label for='migla_dbprefix' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                           <?php echo __("Database Prefix:","migla-donation");?></label></div>
                           <div class='col-sm-10'><label ><?php echo $wpdb->prefix;?></label></div>
                       </div>'

                      <?php
                      $stripeWebhook = $this->get_current_server_url() . "/index.php?sl=".$objO->get_option('migla_listen');
                      ?>

                     <div class='row'>
                         <div class='col-sm-2'>
                            <label for='migla_tables' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                              <?php echo __("URL used by Stripe Webhooks","migla-donation");?></label>
                          </div>
                         <div class='col-sm-10'><label><?php echo esc_html($stripeWebhook);?></label></div>
                     </div>

                      <?php
                      $paypalIPN = $this->get_current_server_url() . "/index.php?pl=".$objO->get_option('migla_listen');
                      ?>

                     <div class='row'>
                         <div class='col-sm-2'>
                          <label for='migla_tables' class='miglaServerStatus control-label text-right-sm text-center-xs'>
                            <?php echo __("URL used by PayPal IPN Listener","migla-donation");?></label></div>
                         <div class='col-sm-10'><label><?php echo $paypalIPN;?></label></div>
                     </div>

                     <div class='row'>
                         <div class='col-sm-2'><label for='migla_childtheme' class=' miglaServerStatus control-label text-right-sm text-center-xs'>
                           <?php echo __("Is child theme used:","migla-donation");?></label></div>
                          <?php
                             if(is_child_theme())
                             {  ?>
                               <div class='col-sm-10'><label><?php echo __("Yes","migla-donation");?></label></div>
                          <?php   }else{  ?>
                               <div class='col-sm-10'><label><?php echo __("No","migla-donation");?></label></div>
                           <?php  }
                             ?>
                     </div>

                     <div class='row'>
                        <div class='col-sm-2'>
                          <label class='miglaServerStatus control-label text-right-sm text-center-xs'>
                            <?php echo __("Plugin Directory","migla-donation");?>
                          </label>
                        </div>
                        <div class='col-sm-10'>
                          <label><?php echo Totaldonations_PLUGIN_DIR;?></label>
                        </div>
                     </div>

                     <div class='row'>
                         <div class='col-sm-2'>
                          <label class='miglaServerStatus control-label text-right-sm text-center-xs'>
                            <?php echo __("Path directory","migla-donation");?>
                          </label>
                          </div>
                         <div class='col-sm-10'><label><?php echo Totaldonations_DIR_PATH;?></label></div>
                     </div>

                  </div>
                </section>
            </div>

            <div id='section2' class='tab-pane' >
            <?php
                $filelist = array();
                if ($handle = opendir(".")) {
                  while ($entry = readdir($handle)) {
                      if (is_file($entry))
                      {
                        if( !(strpos( $entry , "log") === false) )
                        {
                          $filelist[] = $entry;
                        }
                      }
                  }
                  closedir($handle);
                }


                $objLog = new MIGLA_LOG();
                $open_file = '';
                $files = $objLog->scan();

                if( isset($_GET['lname']) && !empty($_GET['lname']) )
                {
                    $open_file = $_GET['lname'];
                }else{
                    if(!empty($files)){
                        $open_file = $files[0];
                    }
                }

                $objEmailLog = new MIGLA_LOG("email-");
                $email_log = '';
                $Emailfiles = $objEmailLog->scan();

                if( isset($_GET['elname']) && !empty(sanitize_text_field($_GET['elname']) ) )
                {
                    $email_log = sanitize_text_field($_GET['elname']);
                }else{
                    if(!empty($Emailfiles)){
                        $email_log = $Emailfiles[0];
                    }
                }


                ?>

                <section class="panel">
                    <header class='panel-heading'>
                        <div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapse7' aria-expanded='true'></a></div>
                        <div class='panel-title'><i class='fa fa-cc-stripe' aria-hidden='true'></i><?php echo __( $open_file,"migla-donation");?></div>

                    </header>
                    <div id="collapse7" class="panel-body mg_error_log_div">
                       <div id="mg_error_log_donation">
                    <?php
                    if(!empty($open_file))
                    {
                        $file = $objLog->dir_log . $open_file;
                        $block =1024*1024;
                        if ($fh = fopen( $file, "r")) {
                            $left='';
                            while (!feof($fh))
                            {
                               $temp = fread($fh, $block);
                               $fgetslines = explode("\n",$temp);
                               $fgetslines[0]=$left.$fgetslines[0];

                               if(!feof($fh) )$left = array_pop($lines);

                               foreach ($fgetslines as $k => $line) {
                                   echo $line . "<br>";
                                }
                             }
                        }
                        fclose($fh);
                    }
                    ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="">
                            <form method="GET" action="<?php echo get_admin_url()."admin.php?page=migla_donation_system_status_page"; ?>">
                                <input type="hidden" name="page" value="<?php echo sanitize_text_field($_GET['page']);?>">
                                <select name="lname"  style="width:200px;display: inline-block;">
                                    <option value="">None</option>
                                    <?php
                                    if(!empty($files)){
                                        foreach($files as $file){
                                        ?>
                                            <option value="<?php echo esc_attr($file);?>"><?php echo $file;?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            <button type="submit" class='btn btn-default'><?php echo __("Open log","migla-donation");?></button>
                            </form>
                        </div>
                    </div>
               </section>

                <section class="panel">
                    <header class='panel-heading'>
                        <div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapse7' aria-expanded='true'></a></div>
                        <div class='panel-title'><i class='fa fa-cc-stripe' aria-hidden='true'></i><?php echo __( $email_log,"migla-donation");?></div>

                    </header>
                    <div id="collapse7" class="panel-body mg_error_log_div">
                       <div id="mg_error_log_donation">
                    <?php
                    if(!empty($email_log))
                    {
                        $file = $objEmailLog->dir_log . $email_log;
                        $block =1024*1024;
                        if ($fh = fopen( $file, "r")) {
                            $left='';
                            while (!feof($fh))
                            {
                               $temp = fread($fh, $block);
                               $fgetslines = explode("\n",$temp);
                               $fgetslines[0]=$left.$fgetslines[0];

                               if(!feof($fh) )$left = array_pop($lines);

                               foreach ($fgetslines as $k => $line) {
                                   echo $line . "<br>";
                                }
                             }
                        }
                        fclose($fh);
                    }
                    ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="">
                            <form method="GET" action="<?php echo get_admin_url()."admin.php?page=migla_donation_system_status_page"; ?>">
                                <input type="hidden" name="page" value="<?php echo sanitize_text_field($_GET['page']);?>">
                                <select name="elname"  style="width:200px;display: inline-block;">
                                    <option value="">None</option>
                                    <?php
                                    if(!empty($Emailfiles)){
                                        foreach($Emailfiles as $file){
                                        ?>
                                            <option value="<?php echo $file;?>"><?php echo $file;?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            <button type="submit" class='btn btn-default'><?php echo __("Open log","migla-donation");?></button>
                            </form>
                        </div>
                   </div>
               </section>

                <section class="panel">
                    <header class='panel-heading'>
                        <div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapse7' aria-expanded='true'></a></div>
                        <div class='panel-title'><i class='fa fa-cc-stripe' aria-hidden='true'></i><?php echo __( "Stripe Log","migla-donation");?></div>

                    </header>
                    <div id="collapse7" class="panel-body mg_error_log_div">
                       <div id="mg_error_log_donation">
                    <?php
                    $objStripeLog = new MIGLA_LOG("");
                    $stripe_log = "td_stripe.log";
                    
                    if(!empty($stripe_log))
                    {
                        $file = $objStripeLog->dir_log . $stripe_log;
                        $block =1024*1024;
                        if ($fh = fopen( $file, "r")) {
                            $left='';
                            while (!feof($fh))
                            {
                               $temp = fread($fh, $block);
                               $fgetslines = explode("\n",$temp);
                               $fgetslines[0]=$left.$fgetslines[0];

                               if(!feof($fh) )$left = array_pop($lines);

                               foreach ($fgetslines as $k => $line) {
                                   echo $line . "<br>";
                                }
                             }
                        }
                        fclose($fh);
                    }
                    ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                   </div>
               </section>


            <?php
                if( count($filelist) > 0 )
                {
                  for( $i=0; $i < count($filelist); $i++ )
                  {
                    $file = $filelist[$i];

                    if( filesize( $file ) == 0 )
                    {
                    }else{
                        $block =1024*1024;
                        if ($fh = fopen($filelist[$i], "r"))
                        {
                        ?>
                        <section class="panel">
                          <header class='panel-heading'>
                            <div class='panel-actions'><a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapse2-<?php echo $i;?>' aria-expanded='true'></a></div>
                            <div class='panel-title'><i class='fa fa-check-circle' aria-hidden='true'></i><?php echo esc_attr($filelist[$i]);?></div>
                          </header>
                            <div id="collapse2-<?php echo $i;?>" class="panel-body mg_error_log_div">
                              <input type='hidden' id='mg_filename_<?php echo esc_attr($i);?>' value='<?php echo esc_attr($filelist[$i]);?>'>
                              <div id="mg_error_log_div_<?php echo esc_attr($i);?>">
                              <?php
                                $left='';
                                while (!feof($fh))
                                {
                                   $temp = fread($fh, $block);
                                   $fgetslines = explode("\n",$temp);
                                   $fgetslines[0]=$left.$fgetslines[0];

                                    foreach ($fgetslines as $k => $line) {
                                       echo $line . "<br>";
                                    }
                                 }
                                ?>
                              </div>
                            </div>
                          <div class="panel-footer"></div>
                        </section>
                      <?php
                        }
                        fclose($fh);
                    }
                  }
                }

            ?>

               </div>

           </div>
       </div><!--horizontal-->
    </div>
    </div><!--row-->

    <!--wrap-fluid-->
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

$obj = new migla_system_status_class();
?>