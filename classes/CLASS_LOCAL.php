<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'MIGLA_LOCAL' ) )
{
	class MIGLA_LOCAL
	{
		public static function st_get_languages()
		{
			$array_of_languages = array(
				'af'	=>	array(	'af'	,	'Afrikaans'	,	'af'	)	,
				'ak'	=>	array(	'ak'	,	'Akan'	,	'ak'	)	,
				'sq'	=>	array(	'sq'	,	'Albanian'	,	'sq'	)	,
				'am'	=>	array(	'am'	,	'Amharic'	,	'am'	)	,
				'ar'	=>	array(	'ar'	,	'Arabic'	,	'ar'	)	,
				'hy'	=>	array(	'hy'	,	'Armenian'	,	'hy'	)	,
				'rup_MK'	=>	array(	'rup'	,	'Aromanian'	,	'rup_MK'	)	,
				'as'	=>	array(	'as'	,	'Assamese'	,	'as'	)	,
				'az'	=>	array(	'az'	,	'Azerbaijani'	,	'az'	)	,
				'az_TR'	=>	array(	'az-tr'	,	'Azerbaijani'	,	'az_TR'	)	,
				'ba'	=>	array(	'ba'	,	'Bashkir'	,	'ba'	)	,
				'eu'	=>	array(	'eu'	,	'Basque'	,	'eu'	)	,
				'bel'	=>	array(	'bel'	,	'Belarusian'	,	'bel'	)	,
				'bn_BD'	=>	array(	'bn'	,	'Bengali'	,	'bn_BD'	)	,
				'bs_BA'	=>	array(	'bs'	,	'Bosnian'	,	'bs_BA'	)	,
				'bg_BG'	=>	array(	'bg'	,	'Bulgarian'	,	'bg_BG'	)	,
				'my_MM'	=>	array(	'mya'	,	'Burmese'	,	'my_MM'	)	,
				'ca'	=>	array(	'ca'	,	'Catalan'	,	'ca'	)	,
				'bal'	=>	array(	'bal'	,	'Catalan (Balear)'	,	'bal'	)	,
				'zh_CN'	=>	array(	'zh-cn'	,	'Chinese (China)'	,	'zh_CN'	)	,
				'zh_HK'	=>	array(	'zh-hk'	,	'Chinese (Hong Kong)'	,	'zh_HK'	)	,
				'zh_TW'	=>	array(	'zh-tw'	,	'Chinese (Taiwan)'	,	'zh_TW'	)	,
				'co'	=>	array(	'co'	,	'Corsican'	,	'co'	)	,
				'hr'	=>	array(	'hr'	,	'Croatian'	,	'hr'	)	,
				'cs_CZ'	=>	array(	'cs'	,	'Czech'	,	'cs_CZ'	)	,
				'da_DK'	=>	array(	'da'	,	'Danish'	,	'da_DK'	)	,
				'dv'	=>	array(	'dv'	,	'Dhivehi'	,	'dv'	)	,
				'nl_NL'	=>	array(	'nl'	,	'Dutch'	,	'nl_NL'	)	,
				'nl_BE'	=>	array(	'nl-be'	,	'Dutch (Belgium)'	,	'nl_BE'	)	,
				'en_US'	=>	array(	'en'	,	'English'	,	'en_US'	)	,
				'en_AU'	=>	array(	'en-au'	,	'English (Australia)'	,	'en_AU'	)	,
				'en_CA'	=>	array(	'en-ca'	,	'English (Canada)'	,	'en_CA'	)	,
				'en_GB'	=>	array(	'en-gb'	,	'English (UK)'	,	'en_GB'	)	,
				'eo'	=>	array(	'eo'	,	'Esperanto'	,	'eo'	)	,
				'et'	=>	array(	'et'	,	'Estonian'	,	'et'	)	,
				'fo'	=>	array(	'fo'	,	'Faroese'	,	'fo'	)	,
				'fi'	=>	array(	'fi'	,	'Finnish'	,	'fi'	)	,
				'fr_BE'	=>	array(	'fr-be'	,	'French (Belgium)'	,	'fr_BE'	)	,
				'fr_FR'	=>	array(	'fr'	,	'French (France)'	,	'fr_FR'	)	,
				'fy'	=>	array(	'fy'	,	'Frisian'	,	'fy'	)	,
				'fuc'	=>	array(	'fuc'	,	'Fulah'	,	'fuc'	)	,
				'gl_ES'	=>	array(	'gl'	,	'Galician'	,	'gl_ES'	)	,
				'ka_GE'	=>	array(	'ka'	,	'Georgian'	,	'ka_GE'	)	,
				'de_DE'	=>	array(	'de'	,	'German'	,	'de_DE'	)	,
				'de_CH'	=>	array(	'de-ch'	,	'German (Switzerland)'	,	'de_CH'	)	,
				'el'	=>	array(	'el'	,	'Greek'	,	'el'	)	,
				'gn'	=>	array(	'gn'	,	'GuaranÃ­'	,	'gn'	)	,
				'gu_IN'	=>	array(	'gu'	,	'Gujarati'	,	'gu_IN'	)	,
				'haw_US'	=>	array(	'haw'	,	'Hawaiian'	,	'haw_US'	)	,
				'haz'	=>	array(	'haz'	,	'Hazaragi'	,	'haz'	)	,
				'he_IL'	=>	array(	'he'	,	'Hebrew'	,	'he_IL'	)	,
				'hi_IN'	=>	array(	'hi'	,	'Hindi'	,	'hi_IN'	)	,
				'hu_HU'	=>	array(	'hu'	,	'Hungarian'	,	'hu_HU'	)	,
				'is_IS'	=>	array(	'is'	,	'Icelandic'	,	'is_IS'	)	,
				'ido'	=>	array(	'ido'	,	'Ido'	,	'ido'	)	,
				'id_ID'	=>	array(	'id'	,	'Indonesian'	,	'id_ID'	)	,
				'ga'	=>	array(	'ga'	,	'Irish'	,	'ga'	)	,
				'it_IT'	=>	array(	'it'	,	'Italian'	,	'it_IT'	)	,
				'ja'	=>	array(	'ja'	,	'Japanese'	,	'ja'	)	,
				'jv_ID'	=>	array(	'jv'	,	'Javanese'	,	'jv_ID'	)	,
				'kn'	=>	array(	'kn'	,	'Kannada'	,	'kn'	)	,
				'kk'	=>	array(	'kk'	,	'Kazakh'	,	'kk'	)	,
				'km'	=>	array(	'km'	,	'Khmer'	,	'km'	)	,
				'kin'	=>	array(	'kin'	,	'Kinyarwanda'	,	'kin'	)	,
				'ky_KY'	=>	array(	'ky'	,	'Kirghiz'	,	'ky_KY'	)	,
				'ko_KR'	=>	array(	'ko'	,	'Korean'	,	'ko_KR'	)	,
				'ckb'	=>	array(	'ckb'	,	'Kurdish (Sorani)'	,	'ckb'	)	,
				'lo'	=>	array(	'lo'	,	'Lao'	,	'lo'	)	,
				'lv'	=>	array(	'lv'	,	'Latvian'	,	'lv'	)	,
				'li'	=>	array(	'li'	,	'Limburgish'	,	'li'	)	,
				'lin'	=>	array(	'lin'	,	'Lingala'	,	'lin'	)	,
				'lt_LT'	=>	array(	'lt'	,	'Lithuanian'	,	'lt_LT'	)	,
				'lb_LU'	=>	array(	'lb'	,	'Luxembourgish'	,	'lb_LU'	)	,
				'mk_MK'	=>	array(	'mk'	,	'Macedonian'	,	'mk_MK'	)	,
				'mg_MG'	=>	array(	'mg'	,	'Malagasy'	,	'mg_MG'	)	,
				'ms_MY'	=>	array(	'ms'	,	'Malay'	,	'ms_MY'	)	,
				'ml_IN'	=>	array(	'ml'	,	'Malayalam'	,	'ml_IN'	)	,
				'mr'	=>	array(	'mr'	,	'Marathi'	,	'mr'	)	,
				'xmf'	=>	array(	'xmf'	,	'Mingrelian'	,	'xmf'	)	,
				'mn'	=>	array(	'mn'	,	'Mongolian'	,	'mn'	)	,
				'me_ME'	=>	array(	'me'	,	'Montenegrin'	,	'me_ME'	)	,
				'ne_NP'	=>	array(	'ne'	,	'Nepali'	,	'ne_NP'	)	,
				'nb_NO'	=>	array(	'nb'	,	'Norwegian (BokmÃ¥l)'	,	'nb_NO'	)	,
				'nn_NO'	=>	array(	'nn'	,	'Norwegian (Nynorsk)'	,	'nn_NO'	)	,
				'ory'	=>	array(	'ory'	,	'Oriya'	,	'ory'	)	,
				'os'	=>	array(	'os'	,	'Ossetic'	,	'os'	)	,
				'ps'	=>	array(	'ps'	,	'Pashto'	,	'ps'	)	,
				'fa_IR'	=>	array(	'fa'	,	'Persian'	,	'fa_IR'	)	,
				'fa_AF'	=>	array(	'fa-af'	,	'Persian (Afghanistan)'	,	'fa_AF'	)	,
				'pl_PL'	=>	array(	'pl'	,	'Polish'	,	'pl_PL'	)	,
				'pt_BR'	=>	array(	'pt-br'	,	'Portuguese (Brazil)'	,	'pt_BR'	)	,
				'pt_PT'	=>	array(	'pt'	,	'Portuguese (Portugal)'	,	'pt_PT'	)	,
				'pa_IN'	=>	array(	'pa'	,	'Punjabi'	,	'pa_IN'	)	,
				'rhg'	=>	array(	'rhg'	,	'Rohingya'	,	'rhg'	)	,
				'ro_RO'	=>	array(	'ro'	,	'Romanian'	,	'ro_RO'	)	,
				'ru_RU'	=>	array(	'ru'	,	'Russian'	,	'ru_RU'	)	,
				'ru_UA'	=>	array(	'ru-ua'	,	'Russian (Ukraine)'	,	'ru_UA'	)	,
				'rue'	=>	array(	'rue'	,	'Rusyn'	,	'rue'	)	,
				'sah'	=>	array(	'sah'	,	'Sakha'	,	'sah'	)	,
				'sa_IN'	=>	array(	'sa-in'	,	'Sanskrit'	,	'sa_IN'	)	,
				'srd'	=>	array(	'srd'	,	'Sardinian'	,	'srd'	)	,
				'gd'	=>	array(	'gd'	,	'Scottish Gaelic'	,	'gd'	)	,
				'sr_RS'	=>	array(	'sr'	,	'Serbian'	,	'sr_RS'	)	,
				'sd_PK'	=>	array(	'sd'	,	'Sindhi'	,	'sd_PK'	)	,
				'si_LK'	=>	array(	'si'	,	'Sinhala'	,	'si_LK'	)	,
				'sk_SK'	=>	array(	'sk'	,	'Slovak'	,	'sk_SK'	)	,
				'sl_SI'	=>	array(	'sl'	,	'Slovenian'	,	'sl_SI'	)	,
				'so_SO'	=>	array(	'so'	,	'Somali'	,	'so_SO'	)	,
				'azb'	=>	array(	'azb'	,	'South Azerbaijani'	,	'azb'	)	,
				'es_AR'	=>	array(	'es-ar'	,	'Spanish (Argentina)'	,	'es_AR'	)	,
				'es_CL'	=>	array(	'es-cl'	,	'Spanish (Chile)'	,	'es_CL'	)	,
				'es_CO'	=>	array(	'es-co'	,	'Spanish (Colombia)'	,	'es_CO'	)	,
				'es_MX'	=>	array(	'es-mx'	,	'Spanish (Mexico)'	,	'es_MX'	)	,
				'es_PE'	=>	array(	'es-pe'	,	'Spanish (Peru)'	,	'es_PE'	)	,
				'es_PR'	=>	array(	'es-pr'	,	'Spanish (Puerto Rico)'	,	'es_PR'	)	,
				'es_ES'	=>	array(	'es'	,	'Spanish (Spain)'	,	'es_ES'	)	,
				'es_VE'	=>	array(	'es-ve'	,	'Spanish (Venezuela)'	,	'es_VE'	)	,
				'su_ID'	=>	array(	'su'	,	'Sundanese'	,	'su_ID'	)	,
				'sw'	=>	array(	'sw'	,	'Swahili'	,	'sw'	)	,
				'sv_SE'	=>	array(	'sv'	,	'Swedish'	,	'sv_SE'	)	,
				'gsw'	=>	array(	'gsw'	,	'Swiss German'	,	'gsw'	)	,
				'tl'	=>	array(	'tl'	,	'Tagalog'	,	'tl'	)	,
				'tg'	=>	array(	'tg'	,	'Tajik'	,	'tg'	)	,
				'tzm'	=>	array(	'tzm'	,	'Tamazight (Central Atlas)'	,	'tzm'	)	,
				'ta_IN'	=>	array(	'ta'	,	'Tamil'	,	'ta_IN'	)	,
				'ta_LK'	=>	array(	'ta-lk'	,	'Tamil (Sri Lanka)'	,	'ta_LK'	)	,
				'tt_RU'	=>	array(	'tt'	,	'Tatar'	,	'tt_RU'	)	,
				'te'	=>	array(	'te'	,	'Telugu'	,	'te'	)	,
				'th'	=>	array(	'th'	,	'Thai'	,	'th'	)	,
				'bo'	=>	array(	'bo'	,	'Tibetan'	,	'bo'	)	,
				'tir'	=>	array(	'tir'	,	'Tigrinya'	,	'tir'	)	,
				'tr_TR'	=>	array(	'tr'	,	'Turkish'	,	'tr_TR'	)	,
				'tuk'	=>	array(	'tuk'	,	'Turkmen'	,	'tuk'	)	,
				'ug_CN'	=>	array(	'ug'	,	'Uighur'	,	'ug_CN'	)	,
				'uk'	=>	array(	'uk'	,	'Ukrainian'	,	'uk'	)	,
				'ur'	=>	array(	'ur'	,	'Urdu'	,	'ur'	)	,
				'uz_UZ'	=>	array(	'uz'	,	'Uzbek'	,	'uz_UZ'	)	,
				'vi'	=>	array(	'vi'	,	'Vietnamese'	,	'vi'	)	,
				'wa'	=>	array(	'wa'	,	'Walloon'	,	'wa'	)	,
				'cy'	=>	array(	'cy'	,	'Welsh'	,	'cy'	)

								);

			return $array_of_languages;
		}

		public function get_languages()
		{
			$array_of_languages = array(
				'af'	=>	array(	'af'	,	'Afrikaans'	,	'af'	)	,
				'ak'	=>	array(	'ak'	,	'Akan'	,	'ak'	)	,
				'sq'	=>	array(	'sq'	,	'Albanian'	,	'sq'	)	,
				'am'	=>	array(	'am'	,	'Amharic'	,	'am'	)	,
				'ar'	=>	array(	'ar'	,	'Arabic'	,	'ar'	)	,
				'hy'	=>	array(	'hy'	,	'Armenian'	,	'hy'	)	,
				'rup_MK'	=>	array(	'rup'	,	'Aromanian'	,	'rup_MK'	)	,
				'as'	=>	array(	'as'	,	'Assamese'	,	'as'	)	,
				'az'	=>	array(	'az'	,	'Azerbaijani'	,	'az'	)	,
				'az_TR'	=>	array(	'az-tr'	,	'Azerbaijani'	,	'az_TR'	)	,
				'ba'	=>	array(	'ba'	,	'Bashkir'	,	'ba'	)	,
				'eu'	=>	array(	'eu'	,	'Basque'	,	'eu'	)	,
				'bel'	=>	array(	'bel'	,	'Belarusian'	,	'bel'	)	,
				'bn_BD'	=>	array(	'bn'	,	'Bengali'	,	'bn_BD'	)	,
				'bs_BA'	=>	array(	'bs'	,	'Bosnian'	,	'bs_BA'	)	,
				'bg_BG'	=>	array(	'bg'	,	'Bulgarian'	,	'bg_BG'	)	,
				'my_MM'	=>	array(	'mya'	,	'Burmese'	,	'my_MM'	)	,
				'ca'	=>	array(	'ca'	,	'Catalan'	,	'ca'	)	,
				'bal'	=>	array(	'bal'	,	'Catalan (Balear)'	,	'bal'	)	,
				'zh_CN'	=>	array(	'zh-cn'	,	'Chinese (China)'	,	'zh_CN'	)	,
				'zh_HK'	=>	array(	'zh-hk'	,	'Chinese (Hong Kong)'	,	'zh_HK'	)	,
				'zh_TW'	=>	array(	'zh-tw'	,	'Chinese (Taiwan)'	,	'zh_TW'	)	,
				'co'	=>	array(	'co'	,	'Corsican'	,	'co'	)	,
				'hr'	=>	array(	'hr'	,	'Croatian'	,	'hr'	)	,
				'cs_CZ'	=>	array(	'cs'	,	'Czech'	,	'cs_CZ'	)	,
				'da_DK'	=>	array(	'da'	,	'Danish'	,	'da_DK'	)	,
				'dv'	=>	array(	'dv'	,	'Dhivehi'	,	'dv'	)	,
				'nl_NL'	=>	array(	'nl'	,	'Dutch'	,	'nl_NL'	)	,
				'nl_BE'	=>	array(	'nl-be'	,	'Dutch (Belgium)'	,	'nl_BE'	)	,
				'en_US'	=>	array(	'en'	,	'English'	,	'en_US'	)	,
				'en_AU'	=>	array(	'en-au'	,	'English (Australia)'	,	'en_AU'	)	,
				'en_CA'	=>	array(	'en-ca'	,	'English (Canada)'	,	'en_CA'	)	,
				'en_GB'	=>	array(	'en-gb'	,	'English (UK)'	,	'en_GB'	)	,
				'eo'	=>	array(	'eo'	,	'Esperanto'	,	'eo'	)	,
				'et'	=>	array(	'et'	,	'Estonian'	,	'et'	)	,
				'fo'	=>	array(	'fo'	,	'Faroese'	,	'fo'	)	,
				'fi'	=>	array(	'fi'	,	'Finnish'	,	'fi'	)	,
				'fr_BE'	=>	array(	'fr-be'	,	'French (Belgium)'	,	'fr_BE'	)	,
				'fr_FR'	=>	array(	'fr'	,	'French (France)'	,	'fr_FR'	)	,
				'fy'	=>	array(	'fy'	,	'Frisian'	,	'fy'	)	,
				'fuc'	=>	array(	'fuc'	,	'Fulah'	,	'fuc'	)	,
				'gl_ES'	=>	array(	'gl'	,	'Galician'	,	'gl_ES'	)	,
				'ka_GE'	=>	array(	'ka'	,	'Georgian'	,	'ka_GE'	)	,
				'de_DE'	=>	array(	'de'	,	'German'	,	'de_DE'	)	,
				'de_CH'	=>	array(	'de-ch'	,	'German (Switzerland)'	,	'de_CH'	)	,
				'el'	=>	array(	'el'	,	'Greek'	,	'el'	)	,
				'gn'	=>	array(	'gn'	,	'GuaranÃ­'	,	'gn'	)	,
				'gu_IN'	=>	array(	'gu'	,	'Gujarati'	,	'gu_IN'	)	,
				'haw_US'	=>	array(	'haw'	,	'Hawaiian'	,	'haw_US'	)	,
				'haz'	=>	array(	'haz'	,	'Hazaragi'	,	'haz'	)	,
				'he_IL'	=>	array(	'he'	,	'Hebrew'	,	'he_IL'	)	,
				'hi_IN'	=>	array(	'hi'	,	'Hindi'	,	'hi_IN'	)	,
				'hu_HU'	=>	array(	'hu'	,	'Hungarian'	,	'hu_HU'	)	,
				'is_IS'	=>	array(	'is'	,	'Icelandic'	,	'is_IS'	)	,
				'ido'	=>	array(	'ido'	,	'Ido'	,	'ido'	)	,
				'id_ID'	=>	array(	'id'	,	'Indonesian'	,	'id_ID'	)	,
				'ga'	=>	array(	'ga'	,	'Irish'	,	'ga'	)	,
				'it_IT'	=>	array(	'it'	,	'Italian'	,	'it_IT'	)	,
				'ja'	=>	array(	'ja'	,	'Japanese'	,	'ja'	)	,
				'jv_ID'	=>	array(	'jv'	,	'Javanese'	,	'jv_ID'	)	,
				'kn'	=>	array(	'kn'	,	'Kannada'	,	'kn'	)	,
				'kk'	=>	array(	'kk'	,	'Kazakh'	,	'kk'	)	,
				'km'	=>	array(	'km'	,	'Khmer'	,	'km'	)	,
				'kin'	=>	array(	'kin'	,	'Kinyarwanda'	,	'kin'	)	,
				'ky_KY'	=>	array(	'ky'	,	'Kirghiz'	,	'ky_KY'	)	,
				'ko_KR'	=>	array(	'ko'	,	'Korean'	,	'ko_KR'	)	,
				'ckb'	=>	array(	'ckb'	,	'Kurdish (Sorani)'	,	'ckb'	)	,
				'lo'	=>	array(	'lo'	,	'Lao'	,	'lo'	)	,
				'lv'	=>	array(	'lv'	,	'Latvian'	,	'lv'	)	,
				'li'	=>	array(	'li'	,	'Limburgish'	,	'li'	)	,
				'lin'	=>	array(	'lin'	,	'Lingala'	,	'lin'	)	,
				'lt_LT'	=>	array(	'lt'	,	'Lithuanian'	,	'lt_LT'	)	,
				'lb_LU'	=>	array(	'lb'	,	'Luxembourgish'	,	'lb_LU'	)	,
				'mk_MK'	=>	array(	'mk'	,	'Macedonian'	,	'mk_MK'	)	,
				'mg_MG'	=>	array(	'mg'	,	'Malagasy'	,	'mg_MG'	)	,
				'ms_MY'	=>	array(	'ms'	,	'Malay'	,	'ms_MY'	)	,
				'ml_IN'	=>	array(	'ml'	,	'Malayalam'	,	'ml_IN'	)	,
				'mr'	=>	array(	'mr'	,	'Marathi'	,	'mr'	)	,
				'xmf'	=>	array(	'xmf'	,	'Mingrelian'	,	'xmf'	)	,
				'mn'	=>	array(	'mn'	,	'Mongolian'	,	'mn'	)	,
				'me_ME'	=>	array(	'me'	,	'Montenegrin'	,	'me_ME'	)	,
				'ne_NP'	=>	array(	'ne'	,	'Nepali'	,	'ne_NP'	)	,
				'nb_NO'	=>	array(	'nb'	,	'Norwegian (BokmÃ¥l)'	,	'nb_NO'	)	,
				'nn_NO'	=>	array(	'nn'	,	'Norwegian (Nynorsk)'	,	'nn_NO'	)	,
				'ory'	=>	array(	'ory'	,	'Oriya'	,	'ory'	)	,
				'os'	=>	array(	'os'	,	'Ossetic'	,	'os'	)	,
				'ps'	=>	array(	'ps'	,	'Pashto'	,	'ps'	)	,
				'fa_IR'	=>	array(	'fa'	,	'Persian'	,	'fa_IR'	)	,
				'fa_AF'	=>	array(	'fa-af'	,	'Persian (Afghanistan)'	,	'fa_AF'	)	,
				'pl_PL'	=>	array(	'pl'	,	'Polish'	,	'pl_PL'	)	,
				'pt_BR'	=>	array(	'pt-br'	,	'Portuguese (Brazil)'	,	'pt_BR'	)	,
				'pt_PT'	=>	array(	'pt'	,	'Portuguese (Portugal)'	,	'pt_PT'	)	,
				'pa_IN'	=>	array(	'pa'	,	'Punjabi'	,	'pa_IN'	)	,
				'rhg'	=>	array(	'rhg'	,	'Rohingya'	,	'rhg'	)	,
				'ro_RO'	=>	array(	'ro'	,	'Romanian'	,	'ro_RO'	)	,
				'ru_RU'	=>	array(	'ru'	,	'Russian'	,	'ru_RU'	)	,
				'ru_UA'	=>	array(	'ru-ua'	,	'Russian (Ukraine)'	,	'ru_UA'	)	,
				'rue'	=>	array(	'rue'	,	'Rusyn'	,	'rue'	)	,
				'sah'	=>	array(	'sah'	,	'Sakha'	,	'sah'	)	,
				'sa_IN'	=>	array(	'sa-in'	,	'Sanskrit'	,	'sa_IN'	)	,
				'srd'	=>	array(	'srd'	,	'Sardinian'	,	'srd'	)	,
				'gd'	=>	array(	'gd'	,	'Scottish Gaelic'	,	'gd'	)	,
				'sr_RS'	=>	array(	'sr'	,	'Serbian'	,	'sr_RS'	)	,
				'sd_PK'	=>	array(	'sd'	,	'Sindhi'	,	'sd_PK'	)	,
				'si_LK'	=>	array(	'si'	,	'Sinhala'	,	'si_LK'	)	,
				'sk_SK'	=>	array(	'sk'	,	'Slovak'	,	'sk_SK'	)	,
				'sl_SI'	=>	array(	'sl'	,	'Slovenian'	,	'sl_SI'	)	,
				'so_SO'	=>	array(	'so'	,	'Somali'	,	'so_SO'	)	,
				'azb'	=>	array(	'azb'	,	'South Azerbaijani'	,	'azb'	)	,
				'es_AR'	=>	array(	'es-ar'	,	'Spanish (Argentina)'	,	'es_AR'	)	,
				'es_CL'	=>	array(	'es-cl'	,	'Spanish (Chile)'	,	'es_CL'	)	,
				'es_CO'	=>	array(	'es-co'	,	'Spanish (Colombia)'	,	'es_CO'	)	,
				'es_MX'	=>	array(	'es-mx'	,	'Spanish (Mexico)'	,	'es_MX'	)	,
				'es_PE'	=>	array(	'es-pe'	,	'Spanish (Peru)'	,	'es_PE'	)	,
				'es_PR'	=>	array(	'es-pr'	,	'Spanish (Puerto Rico)'	,	'es_PR'	)	,
				'es_ES'	=>	array(	'es'	,	'Spanish (Spain)'	,	'es_ES'	)	,
				'es_VE'	=>	array(	'es-ve'	,	'Spanish (Venezuela)'	,	'es_VE'	)	,
				'su_ID'	=>	array(	'su'	,	'Sundanese'	,	'su_ID'	)	,
				'sw'	=>	array(	'sw'	,	'Swahili'	,	'sw'	)	,
				'sv_SE'	=>	array(	'sv'	,	'Swedish'	,	'sv_SE'	)	,
				'gsw'	=>	array(	'gsw'	,	'Swiss German'	,	'gsw'	)	,
				'tl'	=>	array(	'tl'	,	'Tagalog'	,	'tl'	)	,
				'tg'	=>	array(	'tg'	,	'Tajik'	,	'tg'	)	,
				'tzm'	=>	array(	'tzm'	,	'Tamazight (Central Atlas)'	,	'tzm'	)	,
				'ta_IN'	=>	array(	'ta'	,	'Tamil'	,	'ta_IN'	)	,
				'ta_LK'	=>	array(	'ta-lk'	,	'Tamil (Sri Lanka)'	,	'ta_LK'	)	,
				'tt_RU'	=>	array(	'tt'	,	'Tatar'	,	'tt_RU'	)	,
				'te'	=>	array(	'te'	,	'Telugu'	,	'te'	)	,
				'th'	=>	array(	'th'	,	'Thai'	,	'th'	)	,
				'bo'	=>	array(	'bo'	,	'Tibetan'	,	'bo'	)	,
				'tir'	=>	array(	'tir'	,	'Tigrinya'	,	'tir'	)	,
				'tr_TR'	=>	array(	'tr'	,	'Turkish'	,	'tr_TR'	)	,
				'tuk'	=>	array(	'tuk'	,	'Turkmen'	,	'tuk'	)	,
				'ug_CN'	=>	array(	'ug'	,	'Uighur'	,	'ug_CN'	)	,
				'uk'	=>	array(	'uk'	,	'Ukrainian'	,	'uk'	)	,
				'ur'	=>	array(	'ur'	,	'Urdu'	,	'ur'	)	,
				'uz_UZ'	=>	array(	'uz'	,	'Uzbek'	,	'uz_UZ'	)	,
				'vi'	=>	array(	'vi'	,	'Vietnamese'	,	'vi'	)	,
				'wa'	=>	array(	'wa'	,	'Walloon'	,	'wa'	)	,
				'cy'	=>	array(	'cy'	,	'Welsh'	,	'cy'	)

								);

			return $array_of_languages;
		}

	  public function get_origin_language()
	  {
	    	global $wpdb;

	    	$sql = "select language from {$wpdb->prefix}migla_languages WHERE is_origin = 'yes'";

	    	$origin =  $wpdb->get_var( $sql );

	    	return $origin;
	    }

	  public function get_detail_languages()
	  {
	    	global $wpdb;

	    	$sql = "select * from {$wpdb->prefix}migla_languages";

	    	$results =  $wpdb->get_results( $sql, ARRAY_A );

	    	return $results;
	    }

    public function get_language_codes()
    {
        		$array_of_languages = array(
        			'af'	=>	"",
        			'ak'	=>	"",
        			'sq'	=>	"",
        			'am'	=>	"",
        			'ar'	=>	"",
        			'hy'	=>	"",
        			'rup_MK'	=>	"",
        			'as'	=>	"",
        			'az'	=>	"",
        			'az_TR'	=>	"",
        			'ba'	=>	"",
        			'eu'	=>	"",
        			'bel'	=>	"",
        			'bn_BD'	=>	"",
        			'bs_BA'	=>	"",
        			'bg_BG'	=>	"",
        			'my_MM'	=>	"",
        			'ca'	=>	"",
        			'bal'	=>	"",
        			'zh_CN'	=>	"",
        			'zh_HK'	=>	"",
        			'zh_TW'	=>	"",
        			'co'	=>	"",
        			'hr'	=>	"",
        			'cs_CZ'	=>	"",
        			'da_DK'	=>	"",
        			'dv'	=>	"",
        			'nl_NL'	=>	"",
        			'nl_BE'	=>  "",
        			'en_US'	=>	"",
        			'en_AU'	=>	"",
        			'en_CA'	=>	"",
        			'en_GB'	=>	"",
        			'eo'	=>	"",
        			'et'	=>	"",
        			'fo'	=>	"",
        			'fi'	=>	"",
        			'fr_BE'	=>	"",
        			'fr_FR'	=>	"",
        			'fy'	=>	"",
        			'fuc'	=>	"",
        			'gl_ES'	=>	"",
        			'ka_GE'	=>	"",
        			'de_DE'	=>	"",
        			'de_CH'	=>	"",
        			'el'	=>	"",
        			'gn'	=>	"",
        			'gu_IN'	=> "",
        			'haw_US'=>	"",
        			'haz'	=>	"",
        			'he_IL'	=>	"",
        			'hi_IN'	=>	"",
        			'hu_HU'	=>	"",
        			'is_IS'	=>	"",
        			'ido'	=>	"",
        			'id_ID'	=>	"",
        			'ga'	=>	"",
        			'it_IT'	=>	"",
        			'ja'	=>	"",
        			'jv_ID'	=>	"",
        			'kn'	=>	"",
        			'kk'	=>	"",
        			'km'	=>	"",
        			'kin'	=>	"",
        			'ky_KY'	=>	"",
        			'ko_KR'	=>	"",
        			'ckb'	=>	"",
        			'lo'	=>	"",
        			'lv'	=>	"",
        			'li'	=>	"",
        			'lin'	=>	"",
        			'lt_LT'	=>	"",
        			'lb_LU'	=>	"",
        			'mk_MK'	=>	"",
        			'mg_MG'	=>	"",
        			'ms_MY'	=>	"",
        			'ml_IN'	=>	"",
        			'mr'	=>	"",
        			'xmf'	=>	"",
        			'mn'	=>	"",
        			'me_ME'	=>	"",
        			'ne_NP'	=>	"",
        			'nb_NO'	=>	"",
        			'nn_NO'	=>	"",
        			'ory'	=>	"",
        			'os'	=>	"",
        			'ps'	=>	"",
        			'fa_IR'	=>	"",
        			'fa_AF'	=>	"",
        			'pl_PL'	=>	"",
        			'pt_BR'	=>	"",
        			'pt_PT'	=>	"",
        			'pa_IN'	=>	"",
        			'rhg'	=>	"",
        			'ro_RO'	=>	"",
        			'ru_RU'	=>	"",
        			'ru_UA'	=>	"",
        			'rue'	=>	"",
        			'sah'	=>	"",
        			'sa_IN'	=>	"",
        			'srd'	=>	"",
        			'gd'	=>	"",
        			'sr_RS'	=>	"",
        			'sd_PK'	=>	"",
        			'si_LK'	=>	"",
        			'sk_SK'	=>	"",
        			'sl_SI'	=>	"",
        			'so_SO'	=>	"",
        			'azb'	=>	"",
        			'es_AR'	=>	"",
        			'es_CL'	=>	"",
        			'es_CO'	=>	"",
        			'es_MX'	=>	"",
        			'es_PE'	=>	"",
        			'es_PR'	=>	"",
        			'es_ES'	=>	"",
        			'es_VE'	=>  "",
        			'su_ID'	=>	"",
        			'sw'	=>	"",
        			'sv_SE'	=>	"",
        			'gsw'	=>	"",
        			'tl'	=>	"",
        			'tg'	=>	"",
        			'tzm'	=>	"",
        			'ta_IN'	=>	"",
        			'ta_LK'	=>	"",
        			'tt_RU'	=>	"",
        			'te'	=>	"",
        			'th'	=>	"",
        			'bo'	=>	"",
        			'tir'	=>	"",
        			'tr_TR'	=>	"",
        			'tuk'	=>	"",
        			'ug_CN'	=>	"",
        			'uk'	=>	"",
        			'ur'	=>	"",
        			'uz_UZ'	=>	"",
        			'vi'	=>	"",
        			'wa'	=>	"",
        			'cy'	=>	""

        		);

        	return $array_of_languages;
        }

    public function get_country_from_language( $wp_code )
    {
        	$the_country = '';

        	$languages = $this->get_languages();

        	foreach( $languages as $lang )
        	{
        		if( $lang[2] ==  $wp_code )
        		{
        			$the_country = $lang[1];
        			break;
        		}
        	}

        	return $the_country;
        }

	  public static function set_origin_language( $language, $is_origin )
	  {
            global $wpdb;

					$is_exist = false;

					$sql = "SELECT id FROM {$wpdb->prefix}migla_languages";
					$sql .= " WHERE language = '%s'";

					$id = $wpdb->get_var( $wpdb->prepare( $sql, $language ) );


            if( $id > 0 )
            {
                $wpdb->update( "{$wpdb->prefix}migla_languages",
    		            array(  "language"  => $language,
		            		    "is_origin" => $is_origin
    		            	   ),
    		            array( "id"	=> $id ),
    		            array( '%s', '%s' ),
    		            array( '%d' )
    		  	);

            }else{

		  	    $wpdb->insert( "{$wpdb->prefix}migla_languages",
		            	array(
		            		"language"  => $language,
		            		"is_origin" => $is_origin
		                ),
		            	array( '%s', '%s' )
		  			);
            }
	    }

	  public function insert_into_languages( $language, $is_origin )
	  {
            global $wpdb;

            if( $this->if_language_exist( $language ) )
            {
                $wpdb->update( "{$wpdb->prefix}migla_languages",
    		            array(  "language"  => $language,
		            		    "is_origin" => $is_origin
    		            	   ),
    		            array( "id"	=> $id ),
    		            array( '%s', '%s' ),
    		            array( '%d' )
    		  	);

            }else{

		  	    $wpdb->insert( "{$wpdb->prefix}migla_languages",
		            	array(
		            		"language"  => $language,
		            		"is_origin" => $is_origin
		                ),
		            	array( '%s', '%s' )
		  			);
            }
	    }

	  public function get_origin()
	  {
	        global $wpdb;

              $sql = "SELECT language FROM {$wpdb->prefix}migla_languages";
              $sql .= " WHERE is_origin = %s";

              $col = $wpdb->get_var($wpdb->prepare(
                        $sql, 'yes' )
                    );

            return $col;
	    }

		public function if_language_exist( $language )
		{
			global $wpdb;

			$is_exist = false;

			$sql = "SELECT id FROM {$wpdb->prefix}migla_languages";
			$sql .= " WHERE language = '%s'";

			$id = $wpdb->get_var( $wpdb->prepare( $sql, $language ) );

			if($id > 0){
				$is_exist = true;
			}

			return $is_exist;
		}

		public function translate_non_en_US( $country, $origin, $text )
		{
		    $string = $text;

            if( $country == 'Poland' ){
                $string = $this->translate_polish( $string );
                setlocale(LC_CTYPE, $origin );

            }

            return $string;
		}

		public function translate_polish( $string )
		{
            setlocale(LC_CTYPE, 'cs_CZ');
            $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

            return $string;
		}

		public function translate_germany( $string )
		{
            //setlocale(LC_ALL, 'en_GB');
            $string = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string );

            return $string;
		}
	}
}
?>
