<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( !defined( 'ABSPATH' ) ) exit;

class migla_gotopage_class extends MIGLA_SEC
{
	function __construct()
	{
		add_action( 'migla_donation_menu', array( $this, 'menu_item' ), 12);
	}

	function menu_item() {
		add_submenu_page(
			'migla_donation_menu_page',
			"Upgrade To Full Version",
			"<strong id=\"wfMenuCallout\" style=\"color: #FCB214;\">&hearts; Get More Features</strong>",
			'read_dashboard',
			'migla_goto_page',
			array( $this, 'menu_page' )
		);

		add_filter('clean_url', 'migla_gotopage_class::_patchWordfenceSubmenuCallout', 10, 3);
	}
	function menu_page()
	{
        if (  is_user_logged_in() )
        {
            
        }else{
            $error = "<div class='wrap'><div class='container-fluid'>";
            $error .= "<h2 class='migla'>";
            $error .= __("You do not have sufficient permissions to access this page. Please contact your web administrator","migla-donation"). "</h2>";
            $error .= "</div></div>";
    
            wp_die( __( $error , 'migla-donation' ) );
        }
	}
	public static function _patchWordfenceSubmenuCallout($url, $original_url, $_context){
		if (preg_match('/(?:migla_goto_page)$/i', $url)) {
			remove_filter('clean_url', 'migla_gotopage_class::_patchWordfenceSubmenuCallout', 10);
			return 'https://totaldonations.com/pricing/';
		}
		return $url;
	}
}
$obj = new migla_gotopage_class();
?>