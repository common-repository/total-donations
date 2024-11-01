<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Migla_Circle_Progress_Shortcode' ) )
{
class Migla_Circle_Progress_Shortcode
{
	static $progressbar_script;

	public static function init() 
	{
		add_shortcode('totaldonations_circlebar', array(__CLASS__, 'handle_shortcode'));
		
		add_action( 'wp_enqueue_scripts' , array(__CLASS__, 'call_scripts') );
	}

	public static function call_scripts()
	{

	    wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		    	
	    wp_enqueue_script( 'mg-circle-progress-js', Totaldonations_DIR_URL.'assets/plugins/circle-progress/circle-progress.js',
			            array(	'jquery-ui-core',
								'jquery'
								)
						);

	    wp_enqueue_script( 'migla-circle-progress-js', Totaldonations_DIR_URL.'assets/plugins/circle-progress/migla-circle-progress.js',
			            array(	'jquery-ui-core',
								'jquery'
								)
	                    );
	}

	public static function handle_shortcode($atts)
	{

		self::$progressbar_script = true;

		$args = shortcode_atts(
					array(
				        'id' 			=> '', 
				        'button' 		=> 'no',
				        'link'  		=> '',
				        'button_text' 	=> 'Donate Now',
				        'button_class' 	=> ''
			    		), 
					$atts 
		   		); 

		ob_start();

		if( $args['id']=='' )
		{	
			echo 'Add attribute ID or the campaign ID so the circle progress bar can be shown';
			
		}else{
		    
		    include_once Totaldonations_DIR_PATH . "frontend/migla_functions.php";
			
			migla_sc_circle_progressbar( 
							$args['button'], 
							$args['link'],
							$args['button_text'], 
							'yes', 
							$args['id'], 
							$args['button_class'] );
			
		}

		return ob_get_clean();
	}//function

}

Migla_Circle_Progress_Shortcode::init();
}
?>