<?php
	//takes the country code from countrycodes.php and converts it into a numeric value that is the countries calling code
	function countryToNumber($country) {
		switch ($country) //load the specified site, or the dashboard by default
		{
		case 'AF': //Afghanistan
			return 93;
			break;
		case 'AL': //Albania
			return 355;
			break;
		case 'DZ': //Algeria
			return 213;
			break;	
		case 'AS': //American Samoa
			return 1;
			break;
		case 'AD': //Andorra
			return 376;
			break;
		case 'AO': //Angola
			return 244;
			break;
		case 'AI': //Anguilla
			return 1;
			break;
		case 'AG': //Antigua
			return 1;
			break;
		case 'AR': //Argentina
			return 54;
			break;
		case 'AM': //Armenia
			return 374;
			break;
		case 'AW': //Aruba
			return 297;
			break;
		case 'AU': //Australia
			return 61;
			break;
		case 'AT': //Austria
			return 43;
			break;
		case 'AZ': //Azerbaijan
			return 994;
			break;
		case 'BH': //Bahrain
			return 973;
			break;
		case 'BD': //Bangladesh
			return 880;
			break;
		case 'BB': //Barbados
			return 1;
			break;
		case 'BY': //Belarus
			return 375;
			break;
		case 'BE': //Belgium
			return 32;
			break;
		case 'BZ': //Belize
			return 501;
			break;
		case 'BJ': //Benin
			return 229;
			break;
		case 'BM': //Bermuda
			return 1;
			break;
		case 'BT': //Bhutan
			return 975;
			break;
		case 'BO': //Bolivia
			return 591;
			break;
		case 'BS': //Bonaire, Sint Eustatius and Saba
			return 599; //This country did not have a 2 digit code. So, BS was used
			break;
		case 'BA': //Bosnia and Herzegovina
			return 387;
			break;
		case 'BW': //Botswana
			return 267;
			break;
		case 'BR': //Brazil
			return 55;
			break;
		case 'IO': //British Indian Ocean Territory
			return 246;
			break;
		case 'BV': //British Virgin Islands
			return 1;
			break;
		case 'BN': //Brunei
			return 673;
			break;
		case 'BG': //Bulgaria
			return 359;
			break;
		case 'BF': //Burkina faso
			return 226;
			break;
		case 'BU': //Burma (Myanmar)
			return 95;
			break;
		case 'BI': //Burundi
			return 257;
			break;
		case 'KH': //Cambodia
			return 855;
			break;
		case 'CM': //Cameroon
			return 237;
			break;
		case 'CA': //Canada
			return 1;
			break;
		case 'CV': //Cape Verde
			return 238;
			break;
		case 'KY': //Cayman Islands
			return 1;
			break;
		case 'CF': //Central African Republic
			return 236;
			break;
		case 'TD': //Chad
			return 235;
			break;
		case 'CL': //Chile
			return 56;
			break;
		case 'CN': //China
			return 86;
			break;
		case 'CO': //Colombia
			return 57;
			break;
		case 'KM': //Comoros
			return 269;
			break;
		case 'CK': //Cook Islands
			return 682;
			break;
		case 'CR': //Costa Rica
			return 506;
			break;
		case 'CI': //Côte d'Ivoire
			return 225;
			break;
		case 'HR': //Croatia
			return 385;
			break;
		case 'CU': //Cuba
			return 53;
			break;
		case 'CC': //Curaçao
			return 599;
			break;
		case 'CY': //Cyprus
			return 357;
			break;
		case 'CZ': //Czech Republic
			return 420;
			break;
		case 'DC': //Democratic Republic of the Congo
			return 243;
			break;
		case 'DK': //Denmark
			return 45;
			break;
		case 'DJ': //Djibouti
			return 253;
			break;
		case 'DM': //Dominica
			return 1;
			break;
		case 'DO': //Dominican Republic
			return 1;
			break;
		case 'EC': //Ecuador
			return 593;
			break;
		case 'EG': //Egypt
			return 20;
			break;
		case 'SV': //El Salvador
			return 503;
			break;
		case 'GQ': //Equatorial Guinea
			return 240;
			break;
		case 'ER': //Eritrea
			return 291;
			break;
		case 'EE': //Estonia
			return 372;
			break;
		case 'ET': //Ethiopia
			return 251;
			break;
		case 'FK': //Falkland Islands
			return 500;
			break;
		case 'FO': //Faroe Islands
			return 298;
			break;
		case 'MI': //Federated States of Micronesia
			return 691;
			break;
		case 'FJ': //Fiji
			return 679;
			break;
		case 'FI': //Finland
			return 358;
			break;
		case 'FR': //France
			return 33;
			break;
		case 'GF': //French Guiana
			return 594;
			break;
		case 'PF': //French Polynesia
			return 689;
			break;
		case 'GA': //Gabon
			return 241;
			break;
		case 'GE': //Georgia
			return 995;
			break;
		case 'DE': //Germany
			return 49;
			break;
		case 'GH': //Ghana
			return 233;
			break;
		case 'GI': //Gibraltar
			return 350;
			break;
		case 'GR': //Greece
			return 30;
			break;
		case 'GL': //Greenland
			return 299;
			break;
		case 'GD': //Grenada
			return 1;
			break;
		case 'GP': //Guadeloupe
			return 590;
			break;
		case 'GU': //Guam
			return 1;
			break;
		case 'GT': //Guatemala
			return 502;
			break;
		case 'GN': //Guinea
			return 224;
			break;
		case 'GW': //Guinea-Bissau
			return 245;
			break;
		case 'GY': //Guyana
			return 592;
			break;
		case 'HT': //Haiti
			return 509;
			break;
		case 'HN': //Honduras
			return 504;
			break;
		case 'HK': //Hong Kong
			return 852;
			break;
		case 'HU': //Hungary
			return 36;
			break;
		case 'IS': //Iceland
			return 354;
			break;
		case 'IN': //India
			return 91;
			break;
		case 'ID': //Indonesia
			return 62;
			break;
		case 'IR': //Iran
			return 98;
			break;
		case 'IQ': //Iraq
			return 964;
			break;
		case 'IE': //Ireland
			return 353;
			break;
		case 'IL': //Israel
			return 972;
			break;
		case 'IT': //Italy
			return 39;
			break;
		case 'JM': //Jamaica
			return 1;
			break;
		case 'JP': //Japan
			return 81;
			break;
		case 'JO': //Jordan
			return 962;
			break;
		case 'KZ': //Kazakhstan
			return 7;
			break;
		case 'KE': //Kenya
			return 254;
			break;
		case 'KI': //Kiribati
			return 686;
			break;
		case 'KV': //Kosovo
			return 381;
			break;
		case 'KW': //Kuwait
			return 965;
			break;
		case 'KG': //Kyrgyzstan
			return 996;
			break;
		case 'LA': //Laos
			return 856;
			break;
		case 'LV': //Latvia
			return 371;
			break;
		case 'LB': //Lebanon
			return 961;
			break;
		case 'LS': //Lesotho
			return 266;
			break;
		case 'LR': //Liberia
			return 231;
			break;
		case 'LY': //Libya
			return 218;
			break;
		case 'LI': //Liechtenstein
			return 423;
			break;
		case 'LT': //Lithuania
			return 370;
			break;
		case 'LU': //Luxembourg
			return 352;
			break;
		case 'MO': //Macau
			return 853;
			break;
		case 'MK': //Macedonia
			return 389;
			break;
		case 'MG': //Madagascar
			return 261;
			break;
		case 'MW': //Malawi
			return 265;
			break;
		case 'MY': //Malaysia
			return 60;
			break;
		case 'MV': //Maldives
			return 960;
			break;
		case 'ML': //Mali
			return 223;
			break;
		case 'MT': //Malta
			return 356;
			break;
		case 'MH': //Marshall Islands
			return 692; 
			break;
		case 'MQ': //Martinique
			return 596; 
			break;
		case 'MR': //Mauritania
			return 222;
			break;
		case 'MU': //Mauritius
			return 230;
			break;
		case 'YT': //Mayotte
			return 262;
			break;
		case 'MX': //Mexico
			return 52;
			break;
		case 'MD': //Moldova
			return 373;
			break;
		case 'MC': //Monaco
			return 377;
			break;
		case 'MN': //Mongolia
			return 976;
			break;
		case 'MT': //Montenegro
			return 382;
			break;
		case 'MS': //Montserrat
			return 1;
			break;
		case 'MA': //Morocco
			return 212;
			break;
		case 'MZ': //Mozambique
			return 258;
			break;
		case 'NA': //Namibia
			return 264;
			break;
		case 'NR': //Nauru
			return 674;
			break;
		case 'NP': //Nepal
			return 977;
			break;
		case 'NL': //Netherlands
			return 31;
			break;
		case 'NC': //New Caledonia
			return 687;
			break;
		case 'NZ': //New Zealand
			return 64;
			break;
		case 'NI': //Nicaragua
			return 505;
			break;
		case 'NE': //Niger
			return 227;
			break;
		case 'NG': //Nigeria
			return 234;
			break;
		case 'NU': //Niue
			return 683;
			break;
		case 'NF': //Norfolk Islands
			return 672;
			break;
		case 'NK': //North Korea
			return 850;
			break;
		case 'MP': //Northern Mariana Islands
			return 1;
			break;
		case 'NO': //Norway
			return 47;
			break;
		case 'OM': //Oman
			return 968;
			break;
		case 'PK': //Pakistan
			return 92;
			break;
		case 'PW': //Palau
			return 680;
			break;
		case 'PS': //Palestine
			return 970;
			break;
		case 'PA': //Panama
			return 507;
			break;
		case 'PG': //Papua New Guinea
			return 675;
			break;
		case 'PY': //Paraguay
			return 595;
			break;
		case 'PE': //Peru
			return 51;
			break;
		case 'PH': //Philippines
			return 63;
			break;
		case 'PL': //Poland
			return 48;
			break;
		case 'PT': //Portugal
			return 351;
			break;
		case 'PR': //Puerto Rico
			return 1;
			break;
		case 'QA': //Qatar
			return 974;
			break;
		case 'RC': //Republic of the Congo
			return 242;
			break;
		case 'RE': //Réunion
			return 262;
			break;
		case 'RO': //Romania
			return 40;
			break;
		case 'RU': //Russia
			return 7;
			break;
		case 'RW': //Rwanda
			return 250;
			break;
		case 'NT': //Saint Barthélemy
			return 590;
			break;
		case 'SH': //Saint Helena
			return 290;
			break;
		case 'KN': //Saint Kitts and Nevis
			return 1;
			break;
		case 'RT': //Saint Martin
			return 590;
			break;
		case 'PM': //Saint Pierre and Miquelon
			return 508;
			break;
		case 'VC': //Saint Vincent and the Grenadines
			return 1;
			break;
		case 'WS': //Samoa
			return 685;
			break;
		case 'SM': //San Marino
			return 378;
			break;
		case 'ST': //São Tomé and Príncipe
			return 239;
			break;
		case 'SA': //Saudi Arabia
			return 966;
			break;
		case 'SN': //Senegal
			return 221;
			break;
		case 'CS': //Serbia
			return 381;
			break;
		case 'SC': //Seychelles
			return 248;
			break;
		case 'SL': //Sierra Leone
			return 232;
			break;
		case 'SG': //Singapore
			return 65;
			break;
		case 'AA': //Sint Maarten
			return 599;
			break;
		case 'SK': //Slovakia
			return 421;
			break;
		case 'SI': //Slovenia
			return 386;
			break;
		case 'SB': //Solomon Islands
			return 677;
			break;
		case 'SO': //Somalia
			return 252;
			break;
		case 'ZA': //South Africa
			return 27;
			break; 
		case 'KO': //South Korea
			return 82;
			break;
		case 'SS': //South Sudan
			return 211;
			break;
		case 'ES': //Spain
			return 34;
			break;
		case 'LK': //Sri Lanka
			return 94;
			break;
		case 'LC': //St. Lucia
			return 1;
			break;
		case 'SD': //Sudan
			return 249;
			break;
		case 'SR': //Suriname
			return 597;
			break;
		case 'WA': //Swaziland
			return 268;
			break;
		case 'SE': //Sweden
			return 46;
			break;
		case 'CH': //Switzerland
			return 41;
			break;
		case 'SY': //Syria
			return 963;
			break;
		case 'TW': //Taiwan
			return 886;
			break;
		case 'TJ': //Tajikistan
			return 992;
			break;
		case 'TZ': //Tanzania
			return 255;
			break;
		case 'TH': //Thailand
			return 66;
			break;
		case 'TB': //The Bahamas
			return 1;
			break;
		case 'GB': //The Gambia
			return 220;
			break;
		case 'TL': //Timor-Leste
			return 670;
			break;
		case 'TG': //Togo
			return 228;
			break;
		case 'TK': //Tokelau
			return 690;
			break;
		case 'TO': //Tonga
			return 676;
			break;
		case 'TT': //Trinidad and Tobago
			return 1;
			break;
		case 'TN': //Tunisia
			return 216;
			break;
		case 'TR': //Turkey
			return 90;
			break;
		case 'TM': //Turkmenistan
			return 993;
			break;
		case 'TC': //Turks and Caicos Islands
			return 1;
			break;
		case 'TV': //Tuvalu
			return 688;
			break;
		case 'UG': //Uganda
			return 256;
			break;
		case 'UA': //Ukraine
			return 380;
			break;
		case 'AE': //United Arab Emirates
			return 971;
			break;
		case 'UK': //United Kingdom
			return 44;
			break;
		case 'US': //United States
			return 1;
			break;
		case 'UY': //Uruguay
			return 598;
			break;
		case 'UM': //US Virgin Islands
			return 1;
			break;
		case 'UZ': //Uzbekistan
			return 998;
			break;
		case 'VU': //Vanuatu
			return 678;
			break;
		case 'VA': //Vatican City
			return 39;
			break;
		case 'VE': //Venezuela
			return 58;
			break;
		case 'VN': //Vietnam
			return 84;
			break;
		case 'WF': //Wallis and Futuna
			return 681;
			break;
		case 'YE': //Yemen
			return 967;
			break;
		case 'ZM': //Zambia
			return 260;
			break;
		case 'ZW': //Zimbabwe
			return 263;
			break;
		default: 
			return '';
		}
	} 
?>