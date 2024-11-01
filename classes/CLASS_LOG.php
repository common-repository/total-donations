<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_LOG' ) )
{
	class MIGLA_LOG
	{
	    public $current_log;
	    public $dir_log;
	    public $prefix;
	    
	    public function __construct($prefix='')
	    {
	        $get_date = date("Y-m-d");
	        
	        if(empty($prefix))
	        {
	            $this->prefix = "donation-";
	        }else{
	            $this->prefix = $prefix;
	        }
	        
	        $this->dir_log = dirname(__DIR__, 1) . '/logs/';
	        
	        $this->current_log = $this->dir_log . $this->prefix . $get_date. ".log";

	        if( !file_exists($this->current_log) )
	        {
	            $msg = "Start";
	            
                $myfile = fopen( $this->current_log, "w") or die("Unable to open file!");
                
                fwrite($myfile, $msg."\n" );
                fclose($myfile);           
                
	        }else{
	            
	        }
	    }
	    
	    public function append($msg)
	    {
	        $myfile = fopen( $this->current_log, "a") or die("Unable to open file!");
            fwrite($myfile, $msg."\n" );
            fclose($myfile);
	    }
	    
	    public function scan()
	    {
            $files = array();
            $allfiles = scandir( $this->dir_log , 1);
            
            if( !empty($allfiles) )
            {
                foreach( $allfiles as $f)
                {
                    if( substr($f, 0, strlen($this->prefix) ) == $this->prefix )
                    {
                        $files[] = $f;
                    }
                }
            }
            
            return $files;
	    }
	}
}

?>