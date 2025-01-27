<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_GEOGRAPHY' ) )
{
    class MIGLA_GEOGRAPHY
    {
        var $object_option;

        public function __construct()
        {
            $this->object_option = new MIGLA_OPTION; 
        }

        public function get_countries()
        {
            
        	$countries = array(
        		'AF' => 'Afghanistan',
        		'AX' => 'Aland Islands',
        		'AL' => 'Albania',
        		'DZ' => 'Algeria',
        		'AS' => 'American Samoa',
        		'AD' => 'Andorra',
        		'AO' => 'Angola',
        		'AI' => 'Anguilla',
        		'AQ' => 'Antarctica',
        		'AG' => 'Antigua and Barbuda',
        		'AR' => 'Argentina',
        		'AM' => 'Armenia',
        		'AW' => 'Aruba',
        		'AU' => 'Australia',
        		'AT' => 'Austria',
        		'AZ' => 'Azerbaijan',
        
        		'BS' => 'Bahamas',
        		'BH' => 'Bahrain',
        		'BD' => 'Bangladesh',
        		'BB' => 'Barbados',
        		'BY' => 'Belarus',
        		'BE' => 'Belgium',
        		'BZ' => 'Belize',
        		'BJ' => 'Benin',
        		'BM' => 'Bermuda',
        		'BT' => 'Bhutan',
        		'BO' => 'Bolivia',
        		'BA' => 'Bosnia-Herzegovina',
        		'BW' => 'Botswana',
        		'BV' => 'Bouvet Island',
        		'BR' => 'Brazil',
        		'IO' => 'British Indian Ocean Territory',
        		'BN' => 'Brunei Darussalam',
        		'BG' => 'Bulgaria',
        		'BF' => 'Burkina Faso',
        		'BI' => 'Burundi',
        
        		'KH' => 'Cambodia',
        		'CM' => 'Cameroon',
        		'CA' => 'Canada',
        		'CV' => 'Cape Verde',
        		'KY' => 'Cayman Islands',
        		'CF' => 'Central African Republic',
        		'TD' => 'Chad',
        		'CL' => 'Chile',
        		'CN' => 'China',
        		'CX' => 'Christmas Island',
        		'CC' => 'Cocos (Keeling) Islands',
        		'CO' => 'Colombia',
        		'KM' => 'Comoros',
        		'CG' => 'Congo',
        		'CD' => 'Democratic Republic of Congo',
        		'CG' => 'Congo',
        		'CD' => 'Congo, Dem. Republic',
        		'CK' => 'Cook Islands',
        		'CR' => 'Costa Rica',
        		'HR' => 'Croatia',
        		'CY' => 'Cuba',
        		'CY' => 'Cyprus',
        		'CZ' => 'Czech Republic',
        
        		'DK' => 'Denmark',
        		'DJ' => 'Djibouti',
        		'DM' => 'Dominica',
        		'DO' => 'Dominican Republic',
        
        		'EC' => 'Ecuador',
        		'EG' => 'Egypt',
        		'SV' => 'El Salvador',
        		'GQ' => 'Equatorial Guinea',
        		'ER' => 'Eriteria',
        		'EE' => 'Estonia',
        		'ET' => 'Ethiopia',
        		'EU' => 'European Union',
        
        		'FK' => 'Falkland Islands (Malvinas)',
        		'FO' => 'Faroe Islands',
        		'FJ' => 'Fiji',
        		'FI' => 'Finland',
        		'FR' => 'France',
        		'GF' => 'French Guiana',
        		'PF' => 'French Polynesia',
        		'TF' => 'French Southern Territories',
        
        		'GA' => 'Gabon',
        		'GM' => 'Gambia',
        		'GE' => 'Georgia',
        		'DE' => 'Germany',
        		'GH' => 'Ghana',
        		'GI' => 'Gibraltar',
        		'GB' => 'Great Britain',
        		'GR' => 'Greece',
        		'GL' => 'Greenland',
        		'GD' => 'Grenada',
        		'GP' => 'Guadeloupe',
        		'GU' => 'Guam',
        		'GT' => 'Guatemala',
        		'GG' => 'Guernsey',
        		'GN' => 'Guinea',
        		'GW' => 'Guinea Bissau',
        		'GY' => 'Guyana',
        
        		'HT' => 'Haiti',
        		'HM' => 'Heard Island / McDonald Islands',
        		'VA' => 'Holy See (Vatican)',
        		'HN' => 'Honduras',
        		'HK' => 'Hong Kong',
        		'HU' => 'Hungary',
        
        		'IS' => 'Iceland',
        		'IN' => 'India',
        		'ID' => 'Indonesia',
        		'IE' => 'Ireland',
        		'IM' => 'Isle of Man',
        		'IL' => 'Israel',
        		'IT' => 'Italy',
        		'CI' => 'Ivory Coast',
        
        		'JM' => 'Jamaica',
        		'JP' => 'Japan',
        		'JE' => 'Jersey',
        		'JO' => 'Jordan',
        
        		'KZ' => 'Kazakhstan',
        		'KE' => 'Kenya',
        		'KI' => 'Kiribati',
        		'KR' => 'South Korea',
        		'KP' => 'North Korea',
        		'KW' => 'Kuwait',
        		'KG' => 'Kyrgyzstan',
        
        		'LA' => 'Laos',
        		'LV' => 'Latvia',
        		'LB' => 'Lebanon',
        		'LS' => 'Lesotho',
        		'LI' => 'Liechtenstein',
        		'LT' => 'Lithuania',
        		'LU' => 'Luxembourg',
        
        		'MO' => 'Macao',
        		'MK' => 'Macedonia',
        		'MG' => 'Madagascar',
        		'MW' => 'Malawi',
        		'MY' => 'Malaysia',
        		'MV' => 'Maldives',
        		'ML' => 'Mali',
        		'MT' => 'Malta',
        		'MH' => 'Marshall Islands',
        		'MQ' => 'Martinique',
        		'MR' => 'Mauritania',
        		'MU' => 'Mauritius',
        		'YT' => 'Mayotte',
        		'MX' => 'Mexico',
        		'FM' => 'Micronesia, Federated States of',
        		'MD' => 'Moldova, Republic of',
        		'MC' => 'Monaco',
        		'MN' => 'Mongolia',
        		'ME' => 'Montenegro',
        		'MS' => 'Montserrat',
        		'MA' => 'Morocco',
        		'MZ' => 'Mozambique',
        
        		'NA' => 'Namibia',
        		'NR' => 'Nauru',
        		'NP' => 'Nepal',
        		'NL' => 'Netherlands',
        		'AN' => 'Netherlands Antilles',
        		'NC' => 'New Calendonia',
        		'NZ' => 'New Zealand',
        		'NI' => 'Nicaragua',
        		'NE' => 'Niger',
        		'NG' => 'Nigeria',
        		'NU' => 'Niue',
        		'NF' => 'Norfolk Island',
        		'MP' => 'Northern Mariana Islands',
        		'NO' => 'Norway',
        
        		'OM' => 'Oman',
        
        		'PK' => 'Pakistan',
        		'PW' => 'Palau',
        		'PS' => 'Palestine',
        		'PA' => 'Panama',
        		'PY' => 'Paraguay',
        		'PG' => 'Papua New Guinea',
        		'PE' => 'Peru',
        		'PH' => 'Philippines',
        		'PN' => 'Pitcairn',
        		'PL' => 'Poland',
        		'PT' => 'Portugal',
        		'PR' => 'Puerto Rico',
        
        		'QA' => 'Qatar',
        
        		'RE' => 'Reunion',
        		'RO' => 'Romania',
        		'RS' => 'Republic of Serbia',
        		'RU' => 'Russian Federation',
        		'RW' => 'Rwanda',
        
        		'SH' => 'Saint Helena',
        		'KN' => 'Saint Kitts and Nevis',
        		'LC' => 'Saint Lucia',
        		'PM' => 'Saint Pierre and Miquelon',
        		'VC' => 'Saint Vincent / Grenadines',
        		'WS' => 'Samoa',
        		'SM' => 'San Marino',
        		'ST' => 'Sao Tome and Principe',
        		'SA' => 'Saudi Arabia',
        		'SN' => 'Senegal',
        		'SC' => 'Seychelles',
        		'SL' => 'Sierra Leone',
        		'SG' => 'Singapore',
        		'SK' => 'Slovakia',
        		'SI' => 'Slovenia',
        		'SB' => 'Solomon Islands',
        		'SO' => 'Somalia',
        		'ZA' => 'South Africa',
        		'GS' => 'South Georgia / South Sandwich',
        		'ES' => 'Spain',
        		'LK' => 'Sri Lanka',
        		'SR' => 'Suriname',
        		'SJ' => 'Svalbard and Jan Mayen',
        		'SZ' => 'Swaziland',
        		'SE' => 'Sweden',
        		'CH' => 'Switzerland',
        
        		'TW' => 'Taiwan',
        		'TJ' => 'Tajikistan',
        		'TZ' => 'Tanzania, United Republic of',
        		'TH' => 'Thailand',
        		'TL' => 'Timor-Leste',
        		'TG' => 'Togo',
        		'TK' => 'Tokelau',
        		'TO' => 'Tonga',
        		'TT' => 'Trinidad and Tobago',
        		'TN' => 'Tunisia',
        		'TR' => 'Turkey',
        		'TM' => 'Turkmenistan',
        		'TC' => 'Turks and Caicos Islands',
        		'TV' => 'Tuvalu',
        
        		'UG' => 'Uganda',
        		'UA' => 'Ukraine',
        		'AE' => 'United Arab Emirates',
        		'GB' => 'United Kingdom',
        		'US' => 'United States',
        		'UM' => 'US Minor Outlying Islands',
        		'UY' => 'Uruguay',
        		'UZ' => 'Uzbekistan',
        
        		'VU' => 'Vanuatu',
        		'VE' => 'Venezuela',
        		'VN' => 'Vietnam',
        		'VG' => 'Virgin Islands, British',
        		'VI' => 'Virgin Islands, U.S.',
        
        		'WF' => 'Wallis and Futuna',
        		'EH' => 'Western Sahara',
        
        		'YE' => 'Yemen',
        
        		'ZM' => 'Zambia',
                        'ZW' => 'Zimbabwe'
        	);
        
        	return $countries;
        
        }
        
        public function get_USA_states()
        {
            
        	$states = array(
        		'AL' => 'Alabama',
        		'AK' => 'Alaska',
        		'AS' => 'American Samoa',
        		'AZ' => 'Arizona',
        		'AR' => 'Arkansas',
        		'CA' => 'California',
        		'CO' => 'Colorado',
        		'CT' => 'Connecticut',
        		'DE' => 'Delaware',
        		'DC' => 'District of Columbia',
        		'FM' => 'Federated States of Micronesia',
        		'FL' => 'Florida',
        		'GA' => 'Georgia',
        		'GU' => 'Guam',
        		'HI' => 'Hawaii',
        		'ID' => 'Idaho',
        		'IL' => 'Illinois',
        		'IN' => 'Indiana',
        		'IA' => 'Iowa',
        		'KS' => 'Kansas',
        		'KY' => 'Kentucky',
        		'LA' => 'Louisiana',
        		'ME' => 'Maine',
        		'MH' => 'Marshall Islands',
        		'MD' => 'Maryland',
        		'MA' => 'Massachusetts',
        		'MI' => 'Michigan',
        		'MN' => 'Minnesota',
        		'MS' => 'Mississippi',
        		'MO' => 'Missouri',
        		'MT' => 'Montana',
        		'NE' => 'Nebraska',
        		'NV' => 'Nevada',
        		'NH' => 'New Hampshire',
        		'NJ' => 'New Jersey',
        		'NM' => 'New Mexico',
        		'NY' => 'New York',
        		'NC' => 'North Carolina',
        		'ND' => 'North Dakota',
        		'MP' => 'Northern Mariana Islands',
        		'OH' => 'Ohio',
        		'OK' => 'Oklahoma',
        		'OR' => 'Oregon',
        		'PW' => 'Palau',
        		'PA' => 'Pennsylvania',
        		'PR' => 'Puerto Rico',
        		'RI' => 'Rhode Island',
        		'SC' => 'South Carolina',
        		'SD' => 'South Dakota',
        		'TN' => 'Tennessee',
        		'TX' => 'Texas',
        		'UT' => 'Utah',
        		'VT' => 'Vermont',
        		'VI' => 'Virgin Islands',
        		'VA' => 'Virginia',
        		'WA' => 'Washington',
        		'WV' => 'West Virginia',
        		'WI' => 'Wisconsin',
        		'WY' => 'Wyoming',
        		'AA' => 'Armed Forces Americas',
        		'AE' => 'Armed Forces',
        		'AP' => 'Armed Forces Pacific'
        	);
        
        	return $states;

        }
        
        public function get_CA_provinces()
        {
            
        	$provinces = array(
        		'AB' => 'Alberta',
        		'BC' => 'British Columbia',
        		'MB' => 'Manitoba',
        		'NB' => 'New Brunswick',
        		'NL' => 'Newfoundland and Labrador',
        		'NT' => 'Northwest Territories',
        		'NS' => 'Nova Scotia',
        		'NU' => 'Nunavut',
        		'ON' => 'Ontario',
        		'PE' => 'Prince Edward Island',
        		'QC' => 'Quebec',
        		'SK' => 'Saskatchewan',
        		'YT' => 'Yukon'
        	);
        
        	return $provinces;

        }
        
        public function get_state_code( $state )
        {
            
        	$states = $this->get_USA_states();
        	$code = '';
        	
        	foreach( $states as $key => $value )
        	{
        		if( $state == $value )
        		{
        			$code = $key;
        			break;
        		}
        	}
        	
        	return $code;
        
        }
        
        public function get_country_code( $country )
        {
            
        	$countries = $this->get_countries();
        	$code = '';
        	
        	foreach( $countries as $key => $value )
        	{
        		if( $country == $value )
        		{
        			$code = $key;
        			break;
        		}
        	}
        	
        	return $code;
        
        }
        
        public function get_country_name( $code )
        {
            
        	$countries = $this->get_countries();
        	$name = '';
        	
        	foreach( $countries as $key => $value )
        	{
        		if( $code == $key )
        		{
        			$name = $value;
        			break;
        		}
        	}
        	
        	return $name;
        
        }      
        
        public function get_state_name( $code )
        {
            
        	$states = $this->get_USA_states();
        	$name = '';
        	
        	foreach( $states as $key => $value )
        	{
        		if( $code == $key )
        		{
        			$name = $value;
        			break;
        		}
        	}
        	
        	return $name;
        
        }              
        
        public function get_province_code( $province )
        {
            
        	$provinces = $this->get_CA_provinces();
        	$code = '';
        	
        	foreach( $provinces as $key => $value )
        	{
        		if( $province == $value )
        		{
        			$code = $key;
        			break;
        		}
        	}
        	
        	return $code;
        
        }	    

        public function get_default_country()
        {
            return  $this->object_option->get_option( 'migla_default_country' );
        }        
   }
}
?>