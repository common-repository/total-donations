<?php
class Migla_ProgressBar_Shortcode
{
    static $progressbar_script;

    static function init()
    {
      add_shortcode('totaldonations_progressbar', array(__CLASS__, 'handle_shortcode'));

      add_action('wp_footer', array(__CLASS__, 'print_script'));
    }

    static function handle_shortcode($atts)
    {
        self::$progressbar_script = true;

    	$args = shortcode_atts( 
    		array(
    			'id' 	=> '',
    			'button' => 'no',
                'link'  => '',
    			'button_text' => 'Donate Now',
    			'text' => '',
                'button_class' => ''
    		),
    		$atts
    	);

        ob_start();

        if( $args['id']=='' )
    	{
    		echo __("Add attribute ID or the campaign ID so the progress bar can be shown", "migla-donation");

    	}else{

    	    include_once Totaldonations_DIR_PATH . "frontend/migla_functions.php";

            migla_shortcode_progressbar( $args['id'],
                    $args['button'] ,
                    $args['link'],
                    $args['button_text'],
                    $args['text'],
                    $args['button_class']
                );
    	}

    	return ob_get_clean();

    }

    static function print_script()
    {
        if ( ! self::$progressbar_script )
    	    return;

    	if( !wp_script_is( 'migla-front-end-css', 'queue' )  )
        {
    		if( !wp_script_is( 'mg_progress-bar', 'registered' ) )
    		{
    		    wp_register_style( 'mg_progress-bar', Totaldonations_DIR_URL.'assets/css/mg_progress-bar.css' , false, false );
    		}

    		if( !wp_script_is( 'mg_progress-bar', 'queue' ) )
    		{
    		    wp_enqueue_style( 'mg_progress-bar' );
    		}

        }
    }
}

Migla_ProgressBar_Shortcode::init();
?>
