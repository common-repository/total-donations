<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_form_settings_class extends MIGLA_SEC
{
    function __construct()
    {
        add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 2 );
    }

    function menu_item()
    {
        add_submenu_page(
          'migla_donation_menu_page',
          __( 'Form Options', 'migla-donation' ),
          __( 'Form Options', 'migla-donation' ),
          'read_form',
          'migla_donation_form_options_page',
          array( $this, 'menu_page' )
        );
    }

  function menu_page()
  {
      $this->menu_page_form();
  }

  function menu_page_form()
  {
    if ( is_user_logged_in() )
    {
        $this->create_token( 'migla_donation_form_options_page', session_id() );
        $this->write_credentials( 'migla_donation_form_options_page', session_id() );


    $objForm = new CLASS_MIGLA_FORM;
    $FORM = $objForm->get_info( 0, get_locale() );


    if( empty($FORM) )
    {
    ?>
      <div class='wrap'>
        <div class='container-fluid'>

          <h2 class='migla'><?php echo __(" Form options","migla-donation");?></h2>


            <div class='row'>
              <div class='col-sm-12'>
              </div>
            </div>

        </div>
      </div>
    <?php
    }else{

      ?>
      <div class='wrap'>
        <div class='container-fluid'>

        <h2 class='migla'><?php echo __(" Form options","migla-donation");?></h2>

          <div class='row'>
          <div class='col-sm-12'>
          <?php
          $objM = new MIGLA_MONEY;
          $objG = new MIGLA_GEOGRAPHY;
          $objO = new MIGLA_OPTION;

          $dec_sep = $objM->get_default_decimal_separator();
          $tho_sep = $objM->get_default_thousand_separator();
          $showSep = $objM->get_show_decimal();
          $symbolType = $objM->get_symbol_to_show();
          
          $numDecimal = 0;

          $obj = new CLASS_MIGLA_FORM;
          $trans_fields = $obj->get_specific_metainfo(  0, 'all', 'fields' );
          ?>
          <input type='hidden' id='mg_thousand_separator' value='<?php echo esc_html($tho_sep);?>'>
          <input type='hidden' id='mg_decimal_separator' value='<?php echo esc_html($dec_sep);?>'>
          <input type='hidden' id='mg_show_separator' value='<?php echo esc_html($showSep);?>'>
          <input type='hidden' id='mg_symbol_to_show' value='<?php echo esc_html($symbolType);?>'>
          <input type="hidden" id="mg_current_language" value="<?php echo esc_html(get_locale()); ?>">
          <input type='hidden' id='mg_page' value='home'>

      <div class='form-horizontal'>

        <div class='tab-content nav-pills-tabs'>

        <?php
          $curSymbol  = $objM->get_currency_symbol2();

          $hide_custom_amount = $FORM["hideCustomAmount"];
          $ctext = $FORM["custom_amount_text"];

        ?>
        <div id='section2' class='tab-pane active' >
            <div class='row'>
                <div class='col-sm-6'>
                  <?php
                      $currencies =  $objM->get_avaliable_currencies();
                      $icon       = '';
                      $def_currency = $objM->get_default_currency();

                      foreach( $currencies as $code => $array ){
                      ?>
                      <div class="currency-code" id="curr-<?php echo esc_attr($code);?>" style="display:none;">
                          <input type="hidden" class="curr-symbol" value="<?php if($array['faicon'] == '') echo esc_attr($array['symbol']); else echo "<i class='fa ".esc_attr($array['faicon'])."'></i>";?>">
                      </div>
                      <?php
                      }

                  ?>
                   <section class='panel'>
                      <header class='panel-heading'><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                        <div class='panel-actions'>
                          <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseTwo' aria-expanded='true'></a>
                        </div>
                        <h2 class='panel-title'><i class='fa fa-fw fa-money'></i>
                          <?php echo __("Default Currency Selection","migla-donation");?>
                        </h2>
                      </header>
                      <div id='collapseTwo' class='panel-body collaspe show'>
                      <div class='row'>
                        <div class='col-sm-3 col-xs-12'>
                          <label for='miglaDefaultCurrency' class='control-label text-right-sm text-center-xs'><?php echo __("Set currency","migla-donation");?></label>
                        </div>
                            <div class='col-sm-6 col-xs-12'>
                              <select id='miglaDefaultCurrency' name='miglaDefaultCurrency'>
                              <?php
                              foreach ( (array)$currencies as $key => $value )
                              {
                                if ( strcmp($def_currency,$currencies[$key]['code'] ) == 0 )
                                {
                                ?>
                                    <option value='<?php echo esc_html($currencies[$key]["code"]);?>' selected='selected' >
                                      <?php echo $currencies[$key]['code'];?>
                                     </option>
                                <?php
                                    if( $currencies[$key]['faicon']!='' ){
                                      $icon = "<i class='fa ".$currencies[$key]['faicon']."'></i>";
                                    }else{
                                      $icon = $currencies[$key]['symbol'];
                                    }
                                }else{ 
                                ?>
                                  <option value='<?php echo esc_html($currencies[$key]['code']);?>'>
                                    <?php echo $currencies[$key]['code'];?>
                                  </option>
                                  <?php
                                }
                              }
                              ?>
                              </select>
                            </div>
                      <?php
                        if( strcmp($showSep,"yes")==0 ){
                          $numDecimal = 2;
                        }

                        $num = number_format("10000", $numDecimal, $dec_sep, $tho_sep);
                        $placement = $objM->get_symbol_position();

                        if($symbolType=="icon"){
                            
                        }else{
                            $icon = $def_currency;
                        }

                        if( strtolower( $placement ) == 'before' )
                        {
                          $before = $icon;
                          $after ='';
                        }else{
                          $before = '';
                          $after = $icon;
                        }
                        ?>
                        <div style='display:none' id='sep1'><?php echo esc_html($tho_sep);?></div>
                        <div style='display:none' id='sep2'><?php echo esc_html($dec_sep);?></div>
                        <div style='display:none' id='placement'><?php echo esc_html($placement);?></div>
                        <div style='display:none' id='showDecimal'><?php echo esc_html($showSep);?></div>
                        <div style='display:none' id='icon'><?php echo esc_html($icon);?></div>

                        <div class='col-sm-3 hidden-xs' id='currencyIcon'>
                            <label id='miglabefore'><?php echo $before;?></label>
                            <label id='miglanum'><?php echo esc_html($num);?></label>
                            <label id='miglaafter'><?php echo $after;?></label>
                        </div>
                      
                      </div>

                      <div class='row'>
                        <div class='col-sm-3 col-xs-12'>
                          <label for='migla_symbol_type' class='control-label text-right-sm text-center-xs'>
                            <?php echo __("Sign Symbol","migla-donation");?>
                          </label>
                        </div>
                        <div class='col-sm-6 col-xs-12'>
                          <select id='migla_symbol_type'>
                            <option value='icon' <?php if($symbolType=="icon") echo "selected";?> >
                                <?php echo __("Currency Icon","migla-donation");?></option>
                            <option value='3-letter-code' <?php if($symbolType=="3-letter-code") echo "selected";?>>
                                <?php echo __("3 Letter Code","migla-donation");?></option>
                          </select>
                        </div>
                      </div>
                      <div class='row'>
                        <div class='col-sm-3 col-xs-12'>
                          <label for='miglaDefaultPlacement' class='control-label text-right-sm text-center-xs'>
                            <?php echo __("Sign Location","migla-donation");?>
                          </label>
                        </div>
                        <div class='col-sm-6 col-xs-12'>
                            <select name='miglaDefaultplacement' id='miglaDefaultPlacement'>
                              <option value='before' <?php if(strtolower( $placement )=='before') echo "selected";?>>
                                <?php echo __("Before","migla-donation");?>
                              </option>
                              <option value='after' <?php if(strtolower( $placement )=='after') echo "selected";?>>
                                <?php echo __("After","migla-donation");?>
                              </option>
                            </select>
                        </div>
                      </div>

                    <div class='row'>
                      <div class='col-sm-3 col-xs-12'>
                        <label for='thousandSep' class='control-label text-right-sm text-center-xs'>
                          <?php echo __("Separators","migla-donation");?>
                          </label>
                        </div>
                      <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>
                        <input type='text' placeholder='Thousands' class=' form-control' id='thousandSep' value="<?php echo esc_html($tho_sep);?>">
                      </div>

                      <div class='col-sm-3 col-xs-12 text-right-sm text-center-xs'>
                        <input type='text' placeholder='Decimal' class=' form-control' id='decimalSep' value="<?php echo esc_html($dec_sep);?>">
                      </div>
                    </div>
                        <?php
                        $checkShowSep = "";

                        if( strcmp($showSep , "yes" ) == 0 )
                        {
                          $checkShowSep = "checked";
                        }
                        ?>

                    <div class='row'>
                      <div class='col-sm-3 col-xs-12'>
                        <label  for='mHideDecimalCheck' class='control-label text-right-sm text-center-xs'>
                          <?php echo __("Show Decimal Place","migla-donation");?></label>
                      </div>
                      <div class='col-sm-6 col-xs-12 text-left-sm text-center-xs'>
                        <label class='checkbox-inline'>
                          <input type='checkbox' name='mHideDecimalCheck' id='mHideDecimalCheck' <?php echo esc_attr($checkShowSep);?>>
                          <?php echo __("check this if you want donors to be able to add decimal places","migla-donation");?>
                        </label>
                      </div>
                      <div class='col-sm-3 hidden-xs'></div>
                    </div>


                    <div class='row'>
                      <div class='col-sm-12 center-button'>
                          <button value='save' class='btn btn-info pbutton msave' id='miglaSetCurrencyButton'>
                            <i class='fa fa-fw fa-save'></i><?php echo __(" save","migla-donation");?>
                          </button>
                      </div>
                    </div>

                    </div>
                  </section>

                </div>

                <div class='col-sm-6'>
                    <section class='panel'>
                      <header class='panel-heading'>
                        <div class='panel-actions'>
                          <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseMinAmount' aria-expanded='true'></a>
                        </div>
                        <h2 class='panel-title'><i class='fa fa-fw fa-money'></i><?php echo __("Minimum Amount","migla-donation");?>
                        </h2>
                      </header>
                        <div id='collapseMinAmount' class='panel-body collaspe show'>
                            <div class="row">
                                <div class="col-sm-3 col-xs-12">
                                    <label for="mg-min-amount" class="control-label text-right-sm text-center-xs">
                                    <?php echo __("Minimum Amount to be accepted","migla-donation");?>
                                    </label>
                                </div>
                                <div class="col-sm-6 col-xs-12 text-right-sm text-center-xs">
                                <input type="text" placeholder="" class="form-control" id="mg-min-amount" value="<?php echo esc_html($objO->get_option("migla_min_amount"));?>">
                              </div>
                                <div class="col-sm-3 col-xs-12"></div>
                            </div>
                            <div class='row'>
                                <div class='col-sm-12 center-button'>
                                  <button value='save' class='btn btn-info pbutton' id='mg-set-min-amount-btn'><i class='fa fa-fw fa-save'></i>
                                  <?php echo __(" save","migla-donation");?>
                                  </button>
                                </div>
                          </div>
                        </div>
                    </section>
                </div>

                <div class='col-sm-6'>
                 <?php
                $countries =  $objG->get_countries();
                ?>

                   <section class='panel'>
                      <header class='panel-heading'>
                        <div class='panel-actions'>
                          <a class='fa fa-caret-down' data-toggle='collapse' data-parent='.panel' href='#collapseThree' aria-expanded='true'></a>
                        </div>
                        <h2 class='panel-title'><i class='fa fa-fw fa-flag'></i>
                          <?php echo __("Default Country Section","migla-donation");?>
                        </h2>
                      </header>
                        <div id='collapseThree' class='panel-body collapse show'>
                            <div class='row'>
                              <div class='col-sm-3 col-xs-12'>
                                <label for='miglaDefaultCountry' class='control-label text-right-sm text-center-xs'>
                                  <?php echo __("Set country","migla-donation");?>
                                  </label>
                                </div>
                                <div class='col-sm-6 col-xs-12'>
                                  <select id='miglaDefaultCountry' name='miglaDefaultCountry'>
                              <?php
                                 foreach ( (array) $countries as $key => $value )
                                 {
                                    if ( $value == $objG->get_default_country() )
                                    { ?>
                                      <option value='<?php echo esc_html($value);?>' selected ><?php echo esc_html($value);?></option>
                                    <?php
                                    }else{  ?>
                                      <option value='<?php echo esc_html($value);?>'><?php echo esc_html($value);?></option>
                                  <?php
                                    }
                                 }
                              ?>
                                </select>
                              </div>
                              <div class='col-sm-3 hidden-xs'></div>
                            </div>
                            <div class='row'>
                                <div class='col-sm-12 center-button'>
                                  <button value='save' class='btn btn-info pbutton' id='miglaSetCountryButton'><i class='fa fa-fw fa-save'></i>
                                  <?php echo __(" save","migla-donation");?>
                                  </button>
                                </div>
                          </div>
                        </div>
                    </section>
            </div>
            </div>

        </div><!--section2-->

    </div>
    </div>
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
}

$obj = new migla_form_settings_class();
?>