<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_REDIRECT' ) )
{
	class MIGLA_REDIRECT
	{
		public function create_redirect( $form_id, $language, $content, $pageid )
		{
			global $wpdb;

			$id = $this->if_redirect_id_exist($form_id);

			if( $id > 0 )
			{
				$wpdb->update( "{$wpdb->prefix}migla_redirect",
								array( 'form_id' => $form_id,
									 	'language' => $language,
									 	'content' => $content,
									 	'pageid' =>  $pageid
									 	),
								array( 'id' => $id ),
								array( '%d', '%s', '%s', '%d' ),
								array( '%d' )
							);
			}else{
				$wpdb->insert( "{$wpdb->prefix}migla_redirect",
								array( 'form_id' => $form_id,
									 	'language' => $language,
									 	'content' => $content,
									 	'pageid' =>  $pageid
									 	),
								array( '%d', '%s', '%s', '%d' )
							);
			}
		}

		public static function init_redirect( $form_id, $language, $content, $pageid )
		{
			global $wpdb;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_redirect";
			$sql .= " WHERE form_id = %d";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $form_id ) );

			if( $id > 0 )
			{
				$wpdb->update( "{$wpdb->prefix}migla_redirect",
								array( 'form_id' => $form_id,
									 	'language' => $language,
									 	'content' => $content,
									 	'pageid' =>  $pageid
									 	),
								array( 'id' => $id ),
								array( '%d', '%s', '%s', '%d' ),
								array( '%d' )
							);
			}else{
				$wpdb->insert( "{$wpdb->prefix}migla_redirect",
								array( 'form_id' => $form_id,
									 	'language' => $language,
									 	'content' => $content,
									 	'pageid' =>  $pageid
									 	),
								array( '%d', '%s', '%s', '%d' )
							);
			}
		}
		
		public function create_redirect_other_language( $form_id, $redirect_id, $language, $pageid, $content )
		{
			global $wpdb;

			if( !empty($redirect_id) )
			{
				$wpdb->update( "{$wpdb->prefix}migla_redirect",
								array( 'form_id'  => $form_id,
									   'language' => $language,
									   'content'  => $content,
									   'pageid'   => $pageid 
									 ),
								array( 'id' => $redirect_id ),
								array( '%d', '%s', '%s', '%d' ),
								array( '%d' )
							);
			}else{
				$wpdb->insert( "{$wpdb->prefix}migla_redirect",
								array( 'form_id' => $form_id,
									 	'language' => $language,
									 	'content' => $content,
									 	'pageid' =>  $pageid
									 	),
								array( '%d', '%s', '%s', '%d' )
							);
				
				$redirect_id = $wpdb->insert_id;
			}
			
			echo $redirect_id;
			die();
		}		

	    public function if_redirect_id_exist($form_id)
	    {
			global $wpdb;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_redirect";
			$sql .= " WHERE form_id = %d";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $form_id ) );

			return $id;	        
	    }	
	    
	    public function get_column_by_form( $form_id, $column_name, $language )
	    {
			global $wpdb;

			$sql = "SELECT ".$column_name." FROM {$wpdb->prefix}migla_redirect";
			$sql .= " WHERE form_id = %d AND language = %s";

			$value = $wpdb->get_var( $wpdb->prepare( $sql, $form_id, $language ) );

			return $value;	        
	    }		    

	    public function if_redirect_id_exist_by_id($redirect_id)
	    {
			global $wpdb;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_redirect";
			$sql .= " WHERE id = %d";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $form_id ) );

			return $id;	        
	    }
	    
	    public function items()
	    {
	        return array(   'id' => '',
	                        'form_id' => '',
	                        'language' => get_locale(),
	                        'content' => '',
	                        'pageid' => ''
	                    );
	    }
	    
	    public function get_info($form_id, $language)
	    {
	        global $wpdb;
	        
	        $sql = "select * from {$wpdb->prefix}migla_redirect";
	        $sql .= " WHERE form_id = %d";
	        $sql .= " AND language = %s";
	        
	        $resultSet = array();
	        $data = $this->items();
	        
	        $esultSet = $wpdb->get_results( $wpdb->prepare( 
	                                        $sql, 
	                                        $form_id,
	                                        $language ), ARRAY_A );
	                    
	       if( !empty($esultSet) )
	       {
	            foreach( $esultSet as $rs ){
	                $data['id'] = $rs['id'];
	                $data['form_id'] = $rs['form_id'];
	                $data['language'] = $rs['language'];
	                $data['content'] = $rs['content'];
	                $data['pageid'] = $rs['pageid'];
	            }
	       }else{
	           $data['form_id'] = $form_id;
	           $data['language'] = $language;
	       }
	       
	       return $data;
	    }

	}//REDIRECT
}