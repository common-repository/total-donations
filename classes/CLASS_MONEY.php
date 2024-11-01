<?php
if ( !defined( 'ABSPATH' ) ) exit;


if ( !class_exists( 'MIGLA_MONEY' ) )
{
	class MIGLA_MONEY
	{
        var $object_option;

        public function __construct()
        {
            $this->object_option = new MIGLA_OPTION;
        }

        public function get_default_decimal_separator()
        {
            return $this->object_option->get_option('migla_decimalSep');
        }

        public function get_default_thousand_separator()
        {
            return $this->object_option->get_option('migla_thousandSep');
        }

        public function get_show_decimal()
        {
            return $this->object_option->get_option('migla_showDecimalSep');
        }

        public function get_symbol_position()
        {
            return $this->object_option->get_option( 'migla_curplacement' );
        }

        public function get_symbol_to_show()
        {
            return $this->object_option->get_option( 'migla_symbol_to_show' );
        }
        
        public function get_default_currency()
        {
            return  $this->object_option->get_option( 'migla_default_currency' );
        }

        public function get_avaliable_currencies()
        {

        	$currencies = array(
        		'AUD' => array( 'code' => 'AUD' , 'name' => 'Australian Dollar', 'symbol' => '$', 'faicon' => 'fa-dollar' ),
        		'BRL' => array( 'code' =>'BRL' , 'name' => 'Brazilian Real', 'symbol' => 'R$', 'faicon' => '' ),
        		'CAD' => array( 'code' =>'CAD' , 'name' => 'Canadian Dollar', 'symbol' => '$', 'faicon' => 'fa-dollar'),
        		'CZK' => array( 'code' =>'CZK' , 'name' => 'Czech Koruna', 'symbol' => '&#x4b;&#x10d;', 'faicon' => ''),
        		'DKK' => array( 'code' =>'DKK' , 'name' => 'Danish Krone', 'symbol' => 'kr', 'faicon' => ''),
        		'EUR' => array( 'code' =>'EUR' , 'name' => 'Euro', 'symbol' => '&euro;', 'faicon' => 'fa-eur'),
        		'HKD' => array( 'code' =>'HKD' , 'name' => 'Hong Kong Dollar', 'symbol' => '$', 'faicon' => ''),
        		'HUF' => array( 'code' =>'HUF' , 'name' => 'Hungarian Forint', 'symbol' => 'Ft', 'faicon' => ''),
        		'ILS' => array( 'code' =>'ILS' , 'name' => 'Israeli New Sheqel', 'symbol' => '&#8362;', 'faicon' => 'fa-ils'),
        		'JPY' => array( 'code' =>'JPY' , 'name' => 'Japanese Yen', 'symbol' => '&yen;', 'faicon' => 'fa-jpy'),
        		'MYR' => array( 'code' =>'MYR' , 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'faicon' => ''),
        		'MXN' => array( 'code' =>'MXN' , 'name' => 'Mexican Peso', 'symbol' => '$', 'faicon' => ''),
        		'NOK' => array( 'code' =>'NOK' , 'name' => 'Norwegian Krone', 'symbol' => 'kr', 'faicon' => ''),
        		'NZD' => array( 'code' =>'NZD' , 'name' => 'New Zealand Dollar', 'symbol' => '$', 'faicon' => ''),
        		'PHP' => array( 'code' =>'PHP' , 'name' => 'Philippine Peso', 'symbol' => '&#8369;', 'faicon' => ''),
        		'PLN' => array( 'code' =>'PLN' , 'name' => 'Polish Zloty', 'symbol' => '&#122;&#322;', 'faicon' => ''),
        		'GBP' => array( 'code' =>'GBP' , 'name' => 'Pound Sterling', 'symbol' => '&pound;', 'faicon' => ''),
        		'RUB' => array( 'code' =>'RUB' , 'name' => 'Russian Ruble', 'symbol' => '&#8381;', 'faicon' => ''),
        		'SGD' => array( 'code' =>'SGD' , 'name' => 'Singapore Dollar', 'symbol' => '$', 'faicon' => ''),
        		'SEK' => array( 'code' =>'SEK' , 'name' => 'Swedish Krona', 'symbol' => 'kr', 'faicon' => ''),
        		'CHF' => array( 'code' =>'CHF' , 'name' => 'Swiss Franc', 'symbol' => '&#8355;', 'faicon' => ''),
        		'TWD' => array( 'code' =>'TWD' , 'name' => 'Taiwan New Dollar', 'symbol' => '$', 'faicon' => ''),
        		'THB' => array( 'code' =>'THB' , 'name' => 'Thai Baht', 'symbol' => '&#3647;', 'faicon' => ''),
        		'TRY' => array( 'code' =>'TRY' , 'name' => 'Turkish Lira', 'symbol' => '&#8378;', 'faicon' => 'fa-try'),
        		'USD' => array( 'code' =>'USD' , 'name' => 'U.S. Dollar', 'symbol' => '$', 'faicon' => 'fa-usd'),
                        'NGN' => array( 'code' =>'NGN' , 'name' => 'Nigerian Naira', 'symbol' => '&#x20a6;', 'faicon' => '')
        	);
            return $currencies;

        }

        public function get_currency_symbol()
        {
            $icon 		= '';
            $currencies = $this->get_avaliable_currencies();
            $default 	= $this->get_default_currency();

            if( isset($currencies[$default]) )
            {
                if($this->get_symbol_to_show() == "3-letter-code"){
                    $icon = $default; 
                }else{
                    $icon = $currencies[$default]['symbol']; 
                }
            }else{

            }

            return $icon;
        }

        public function get_currency_symbol2()
        {
            $i 			= '';
            $currencies = $this->get_avaliable_currencies();
            $def 		= $this->get_default_currency();
            $k          = $this->get_symbol_to_show();

    	    foreach ( (array)$currencies as $key => $value )
    	    {
    	        if ( strcmp($def,$currencies[$key]['code'] ) == 0 )
                {
                    if( $k == 'icon' ) {
                        $i = "<i class='fa ".$currencies[$key]['faicon']."'></i> ";
                    }else{
					    $i = $def;
					}
                }
    	    }

            return $i;
        }        

        public function full_format( $number, $type )
        {
            $result = array( 0 => $number, 1 => '');
            $formatted_number = $number;

            $decimal_separator = $this->get_default_decimal_separator();
            $thousand_separator = $this->get_default_thousand_separator();
            $show_decimal = $this->get_show_decimal();

            if( $show_decimal == 'yes' ){
                $formatted_number = number_format( $number, 2, $decimal_separator, $thousand_separator);
            }else{
                $formatted_number = number_format( $number, 0, $decimal_separator, $thousand_separator);
            }

            $result[0] = $formatted_number;

            if( $type == 2 ){
                $result[1] = $this->get_default_currency();
            }

            return $result;
        }
	}
}
?>
