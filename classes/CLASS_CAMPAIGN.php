<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_CAMPAIGN' ) )
{
	class MIGLA_CAMPAIGN
	{

		public function create_campaign(
			$name,
			$target,
			$shown = true,
			$current_language
		){
			global $wpdb;

			//get all languages available
			$objL = new MIGLA_LOCAL;
			$languages = $objL->get_languages();

			//names will be in array
			$names = array();

			//this for multicampaign
			$form_id = 0;
			$cmp_id = 0;

			foreach( $languages as $lang => $values ){
				$names[$lang] = $name;
			}

		  	$wpdb->insert( "{$wpdb->prefix}migla_campaign",
		            	array(
		            		"target" => $target,
		            		"name" 	=> serialize($names),
		            		"shown" => $shown,
		            		"multi_list" => '1',
 		            		"form_id"	=> 0
		                ),
		            	array( '%d', '%s', '%s', '%s','%d' )
		  			);

		  	$cmp_id = $wpdb->insert_id;

				if( Totaldonations_FREE != 'yes'  )
				{
						$CForm_fields = new CForm_Fields;
						$fields = $CForm_fields::form_fields();

			   		$form_object = new CLASS_MIGLA_FORM;
			   		$form_id = $form_object->create_campaign_form( $fields, false, $current_language );

			   		$wpdb->update( "{$wpdb->prefix}migla_campaign",
				            array( "form_id"	=> $form_id ),
				            array( "id"	=> $cmp_id ),
				            array( '%d' ),
				            array( '%d' )
				  	);
				}

	   		$res = $form_id.",".$cmp_id;

		  	return $res;
		}

    public function save_campaign( $id, $name, $target, $shown )
    {
            global $wpdb;

            $wpdb->update( "{$wpdb->prefix}migla_campaign",
		            array(  "name" 	=> serialize($name),
		                    "target" => $target,
		            		"shown" => $shown
		            	   ),
		            array( "id"	=> $id ),
		            array( '%s', '%d', '%s' ),
		            array( '%d' )
		  	);

    }

		public function get_info( $id, $lang )
		{
			global $wpdb;

			$ResultSet = array();
			$ResultSetData = array();

			$sql = "SELECT * FROM {$wpdb->prefix}migla_campaign WHERE id = %d";

			$ResultSetData = $wpdb->get_results(
								$wpdb->prepare( $sql, $id ), ARRAY_A
							);

			if( !empty($ResultSetData) )
			{
				foreach( $ResultSetData as $row )
				{
					foreach($row as $col => $val)
					{
						if( $col == 'name' )
						{
						    if(!empty($val)){
							    $_val = (array)unserialize( $val );
						    }else{
						        $_val = "";
						    }

							$langlocale = get_locale();

							if( isset( $_val[$lang] ) ){
								$ResultSet[$col] = $_val[$lang];
							}else if( isset($_val[$langlocale]) ){
								$ResultSet[$col] = $_val[$langlocale];
							}else{
								$ResultSet[$col] = "";
							}

						}else{
							$ResultSet[$col] = $val;
						}
					}//foreach
				}//foreach
			}

			return $ResultSet;
		}

		public function get_name( $id, $lang )
		{
			global $wpdb;

			$ResultSetData = array();

			$sql = "SELECT name FROM ".$wpdb->prefix."migla_campaign WHERE id = %d";

            $ResultSetData = $wpdb->get_var( $wpdb->prepare($sql, $id) );

            if(!empty($ResultSetData))
            {
                $ResultSetData = (array)unserialize($ResultSetData);

            }

            return $ResultSetData;
		}

		public function get_column( $id, $colname )
		{
			global $wpdb;

			$sql = "SELECT ".$colname." FROM {$wpdb->prefix}migla_campaign WHERE id = %d";

            $colval = $wpdb->get_var( $wpdb->prepare($sql, $id) );

            return $colval;
		}

		public function get_column_by_column( $colby, $colbyval, $colbyvaltype, $colsearch )
		{
			global $wpdb;

			$sql = "SELECT ".$colsearch." FROM {$wpdb->prefix}migla_campaign WHERE ".$colby." = ".$colbyvaltype;

            $colsearchval = $wpdb->get_var( $wpdb->prepare($sql, $colbyval) );

            return $colsearchval;
		}


		public function get_info_by_campaign( $form, $lang )
		{
			global $wpdb;

			$ResultSet = array();
			$ResultSetData = array();

			$sql = "SELECT * FROM {$wpdb->prefix}migla_campaign WHERE";
			$sql .= " form_id = %d";

			$ResultSetData = $wpdb->get_results(
								$wpdb->prepare( $sql, $form )
								, ARRAY_A
							);

			if( !empty($ResultSetData) ){
				foreach( $ResultSetData as $row ){
					foreach($row as $col => $val){
						if( $col == 'name' )
						{
						    if(!empty($val)){
							    $_val = (array)unserialize( $val );
						    }else{
						        $_val = "";
						    }

							$langlocale = get_locale();

							if( isset( $_val[$lang] ) ){
								$ResultSet[$col] = $_val[$lang];
							}else if( isset($_val[$langlocale]) ){
								$ResultSet[$col] = $_val[$langlocale];
							}else{
								$ResultSet[$col] = "";
							}

						}else{
							$ResultSet[$col] = $val;
						}
					}//foreach
				}//foreach
			}

			return $ResultSet;
		}

		public function get_info_by_campaign_id( $campaign, $lang )
		{
			global $wpdb;

			$ResultSet = array();
			$ResultSetData = array();

			$sql = "SELECT * FROM {$wpdb->prefix}migla_campaign WHERE";
			$sql .= " id = %d";

			$ResultSetData = $wpdb->get_results(
								$wpdb->prepare( $sql, $campaign )
								, ARRAY_A
							);

			if( !empty($ResultSetData) ){
				foreach( $ResultSetData as $row ){
					foreach($row as $col => $val){
						if( $col == 'name' )
						{
						    if(!empty($val)){
							    $_val = (array)unserialize( $val );
						    }else{
						        $_val = "";
						    }

							$langlocale = get_locale();

							if( isset( $_val[$lang] ) ){
								$ResultSet[$col] = $_val[$lang];
							}else if( isset($_val[$langlocale]) ){
								$ResultSet[$col] = $_val[$langlocale];
							}else{
								$ResultSet[$col] = "";
							}

						}else{
							$ResultSet[$col] = $val;
						}
					}//foreach
				}//foreach
			}

			return $ResultSet;
		}

		public function get_all_info( $lang )
		{
			global $wpdb;

			$ResultSet = array();
			$ResultSetData = array();

			$sql = "SELECT * FROM {$wpdb->prefix}migla_campaign";

			$ResultSetData = $wpdb->get_results( $sql , ARRAY_A );

			if( !empty($ResultSetData) ){
				foreach( $ResultSetData as $key => $row )
				{
					foreach($row as $col => $val)
					{
						if( $col == 'name' )
						{
							$_val = (array)unserialize( $val );

							$langlocale = get_locale();

							if( isset($_val[$lang]) )
							{
								$ResultSet[$key][$col] = $_val[$lang];
							}else if( isset($_val[$langlocale]) )
							{
								$ResultSet[$key][$col] = $_val[$langlocale];
							}else{
								$ResultSet[$key][$col] = "";
							}

						}else{
							$ResultSet[$key][$col] = $val;
						}
					}//foreach
				}//foreach
			}

			return $ResultSet;
		}

		public function get_all_info_orderby( $lang, $orderSort = 'DESC' )
		{
			global $wpdb;

			$ResultSet = array();
			$ResultSetData = array();

			$sql = "SELECT * FROM {$wpdb->prefix}migla_campaign order by id ". $orderSort;

			$ResultSetData = $wpdb->get_results( $sql , ARRAY_A );

			if( !empty($ResultSetData) ){
				foreach( $ResultSetData as $key => $row )
				{
				    $id = $row['id'];

					foreach($row as $col => $val)
					{
						if( $col == 'name' )
						{
							$_val = (array)unserialize( $val );

							$langlocale = get_locale();

							if( isset($_val[$lang]) )
							{
								$ResultSet[$id][$col] = $_val[$lang];
							}else if( isset($_val[$langlocale]) )
							{
								$ResultSet[$id][$col] = $_val[$langlocale];
							}else{
								$ResultSet[$id][$col] = "";
							}

						}else{
							$ResultSet[$id][$col] = $val;
						}
					}//foreach
				}//foreach
			}

			return $ResultSet;
		}

		public function remove_campaign( $id )
		{
			global $wpdb;

			$sql = "select form_id from {$wpdb->prefix}migla_campaign where id = %d";
			$frm = $wpdb->get_var( $wpdb->prepare($sql, $id) );

			$sql = "delete from {$wpdb->prefix}migla_campaign where id = %d";
			$wpdb->get_results( $wpdb->prepare($sql, $id) );

            if( $frm > 0 )
            {
    			$sql = "delete from {$wpdb->prefix}migla_form_meta where form_id = %d";
    			$wpdb->get_results( $wpdb->prepare($sql, $frm) );

    			$sql = "delete from {$wpdb->prefix}migla_form where form_id = %d";
    			$wpdb->get_results( $wpdb->prepare($sql, $frm) );
            }
		}

		public function if_exist( $id )
		{
			global $wpdb;
			$id = 0;
			$sql = "SELECT id FROM {$wpdb->prefix}migla_campaign WHERE id = %d";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $id ) , ARRAY_A );

			return $id;
		}

        public function get_undesignated()
        {
        	$objO =  new MIGLA_OPTION;
            $undesignatedLabels = $objO->get_option('migla_undesignLabel');
            $unLbl = '';

            if( !empty($undesignatedLabels) && $undesignatedLabels != false )
            {
                $_ubLbl = (array)unserialize($undesignatedLabels);

                if( isset($_ubLbl[(get_locale())]) ){
                    $unLbl = $_ubLbl[(get_locale())];
                }
            }

            return $unLbl;
        }
	}//Class
}
?>
