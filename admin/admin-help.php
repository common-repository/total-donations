<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_help_class extends MIGLA_SEC
{

	function __construct()
	{
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12 );
    }

	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			__( 'Help', 'migla-donation' ),
			__( 'Help', 'migla-donation' ),
			'read_help',
			'migla_donation_help',
			array( $this, 'menu_page' )
		);
	}

	function menu_page()
	{
	    $this->create_token( 'migla_donation_help', session_id() );
    	$this->write_credentials( 'migla_donation_help',session_id() );

		if ( ! current_user_can( 'manage_options' ) )
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'migla-donation' ) );
		}else{

		?>
 		<div class='wrap'>
 			<div class='container-fluid'>
                <h2 class='migla'><?php echo esc_html__('Help', 'migla-donation');?></h2>
				<div class="row">
					<div class='col-md-6 col-lg-6 col-xl-12'>
						<section class='panel panel-featured-left panel-featured-primary'>
							<div class='panel-body'>
								<div class='widget-summary'>
			                      <h2 class='panel-title'><?php echo __("Documentation","migla-donation ");?></h2>

								
									<br><br>
									 <i class='fa fa-fw fa-plane'></i>&nbsp;<?php echo __("Visit here  ","migla-donation");?><a href='http://totaldonations.com/knowledgebase'><?php echo __("for complete documentation ","migla-donation");?></a>

									<br><br>
									<i class='fa fa-fw fa-question'></i>&nbsp;<?php echo __("Visit ","migla-donation");?><a href='http://totaldonations.com/knowledgebase_category/shortcodes/'><?php echo __(" for shortcode examples and arguments","migla-donation");?></a>
									<br><br>
								</div>
							</div>
						</section>
					</div>
				</div>

            <!--wrap fluid-->
            </div>
         </div>
		<?php
		}
    }
}
$obj = new migla_help_class();
?>
