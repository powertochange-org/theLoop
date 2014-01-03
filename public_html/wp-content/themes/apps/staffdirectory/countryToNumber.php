<?php
	//takes the country code from countrycodes.php and converts it into a numeric value that is the countries calling code
	function countryToNumber($country) {
		switch ($country) //load the specified site, or the dashboard by default
		{
		case 'AF': //Afghanistan
			return 93;
		case 'AL': //Albania
			return 355;
		case 'DZ': //Algeria
			return 213;
		case 'AS': //American Samoa
			return 1;
		case 'AD': //Andorra
			return 376;
		case 'AO': //Angola
			return 244;
		case 'AI': //Anguilla
			return 1;
		case 'AG': //Antigua
			return 1;
		case 'AR': //Argentina
			return 54;
		case 'AM': //Armenia
			return 374;
		case 'AW': //Aruba
			return 297;
		case 'AU': //Australia
			return 61;
		case 'AT': //Austria
			return 43;
		case 'AZ': //Azerbaijan
			return 994;
		case 'BH': //Bahrain
			return 973;
		case 'BD': //Bangladesh
			return 880;
		case 'BB': //Barbados
			return 1;
		case 'BY': //Belarus
			return 375;
		case 'BE': //Belgium
			return 32;
		case 'BZ': //Belize
			return 501;
		case 'BJ': //Benin
			return 229;
		case 'BM': //Bermuda
			return 1;
		case 'BT': //Bhutan
			return 975;
		case 'BO': //Bolivia
			return 591;
		case 'BS': //Bonaire, Sint Eustatius and Saba
			return 599; //This country did not have a 2 digit code. So, BS was used
		case 'BA': //Bosnia and Herzegovina
			return 387;
		case 'BW': //Botswana
			return 267;
		case 'BR': //Brazil
			return 55;
		case 'IO': //British Indian Ocean Territory
			return 246;
		case 'BV': //British Virgin Islands
			return 1;
		case 'BN': //Brunei
			return 673;
		case 'BG': //Bulgaria
			return 359;
		case 'BF': //Burkina faso
			return 226;
		case 'BU': //Burma (Myanmar)
			return 95;
		case 'BI': //Burundi
			return 257;
		case 'KH': //Cambodia
			return 855;
		case 'CM': //Cameroon
			return 237;
		case 'CA': //Canada
			return 1;
		case 'CV': //Cape Verde
			return 238;
		case 'KY': //Cayman Islands
			return 1;
		case 'CF': //Central African Republic
			return 236;
		case 'TD': //Chad
			return 235;
		case 'CL': //Chile
			return 56;
		case 'CN': //China
			return 86;
		case 'CO': //Colombia
			return 57;
		case 'KM': //Comoros
			return 269;
		case 'CK': //Cook Islands
			return 682;
		case 'CR': //Costa Rica
			return 506;
		case 'CI': //Côte d'Ivoire
			return 225;
		case 'HR': //Croatia
			return 385;
		case 'CU': //Cuba
			return 53;
		case 'CC': //Curaçao
			return 599;
		case 'CY': //Cyprus
			return 357;
		case 'CZ': //Czech Republic
			return 420;
		case 'DC': //Democratic Republic of the Congo
			return 243;
		case 'DK': //Denmark
			return 45;
		case 'DJ': //Djibouti
			return 253;
		case 'DM': //Dominica
			return 1;
		case 'DO': //Dominican Republic
			return 1;
		case 'EC': //Ecuador
			return 593;
		case 'EG': //Egypt
			return 20;
		case 'SV': //El Salvador
			return 503;
		case 'GQ': //Equatorial Guinea
			return 240;
		case 'ER': //Eritrea
			return 291;
		case 'EE': //Estonia
			return 372;
		case 'ET': //Ethiopia
			return 251;
		case 'FK': //Falkland Islands
			return 500;
		case 'FO': //Faroe Islands
			return 298;
		case 'MI': //Federated States of Micronesia
			return 691;
		case 'FJ': //Fiji
			return 679;
		case 'FI': //Finland
			return 358;
		case 'FR': //France
			return 33;
		case 'GF': //French Guiana
			return 594;
		case 'PF': //French Polynesia
			return 689;
		case 'GA': //Gabon
			return 241;
		case 'GE': //Georgia
			return 995;
		case 'DE': //Germany
			return 49;
		case 'GH': //Ghana
			return 233;
		case 'GI': //Gibraltar
			return 350;
		case 'GR': //Greece
			return 30;
		case 'GL': //Greenland
			return 299;
		case 'GD': //Grenada
			return 1;
		case 'GP': //Guadeloupe
			return 590;
		case 'GU': //Guam
			return 1;
		case 'GT': //Guatemala
			return 502;
		case 'GN': //Guinea
			return 224;
		case 'GW': //Guinea-Bissau
			return 245;
		case 'GY': //Guyana
			return 592;
		case 'HT': //Haiti
			return 509;
		case 'HN': //Honduras
			return 504;
		case 'HK': //Hong Kong
			return 852;
		case 'HU': //Hungary
			return 36;
		case 'IS': //Iceland
			return 354;
		case 'IN': //India
			return 91;
		case 'ID': //Indonesia
			return 62;
		case 'IR': //Iran
			return 98;
		case 'IQ': //Iraq
			return 964;
		case 'IE': //Ireland
			return 353;
		case 'IL': //Israel
			return 972;
		case 'IT': //Italy
			return 39;
		case 'JM': //Jamaica
			return 1;
		case 'JP': //Japan
			return 81;
		case 'JO': //Jordan
			return 962;
		case 'KZ': //Kazakhstan
			return 7;
		case 'KE': //Kenya
			return 254;
		case 'KI': //Kiribati
			return 686;
		case 'KV': //Kosovo
			return 381;
		case 'KW': //Kuwait
			return 965;
		case 'KG': //Kyrgyzstan
			return 996;
		case 'LA': //Laos
			return 856;
		case 'LV': //Latvia
			return 371;
		case 'LB': //Lebanon
			return 961;
		case 'LS': //Lesotho
			return 266;
		case 'LR': //Liberia
			return 231;
		case 'LY': //Libya
			return 218;
		case 'LI': //Liechtenstein
			return 423;
		case 'LT': //Lithuania
			return 370;
		case 'LU': //Luxembourg
			return 352;
		case 'MO': //Macau
			return 853;
		case 'MK': //Macedonia
			return 389;
		case 'MG': //Madagascar
			return 261;
		case 'MW': //Malawi
			return 265;
		case 'MY': //Malaysia
			return 60;
		case 'MV': //Maldives
			return 960;
		case 'ML': //Mali
			return 223;
		case 'MT': //Malta
			return 356;
		case 'MH': //Marshall Islands
			return 692; 
		case 'MQ': //Martinique
			return 596; 
		case 'MR': //Mauritania
			return 222;
		case 'MU': //Mauritius
			return 230;
		case 'YT': //Mayotte
			return 262;
		case 'MX': //Mexico
			return 52;
		case 'MD': //Moldova
			return 373;
		case 'MC': //Monaco
			return 377;
		case 'MN': //Mongolia
			return 976;
		case 'MT': //Montenegro
			return 382;
		case 'MS': //Montserrat
			return 1;
		case 'MA': //Morocco
			return 212;
		case 'MZ': //Mozambique
			return 258;
		case 'NA': //Namibia
			return 264;
		case 'NR': //Nauru
			return 674;
		case 'NP': //Nepal
			return 977;
		case 'NL': //Netherlands
			return 31;
		case 'NC': //New Caledonia
			return 687;
		case 'NZ': //New Zealand
			return 64;
		case 'NI': //Nicaragua
			return 505;
		case 'NE': //Niger
			return 227;
		case 'NG': //Nigeria
			return 234;
		case 'NU': //Niue
			return 683;
		case 'NF': //Norfolk Islands
			return 672;
		case 'NK': //North Korea
			return 850;
		case 'MP': //Northern Mariana Islands
			return 1;
		case 'NO': //Norway
			return 47;
		case 'OM': //Oman
			return 968;
		case 'PK': //Pakistan
			return 92;
		case 'PW': //Palau
			return 680;
		case 'PS': //Palestine
			return 970;
		case 'PA': //Panama
			return 507;
		case 'PG': //Papua New Guinea
			return 675;
		case 'PY': //Paraguay
			return 595;
		case 'PE': //Peru
			return 51;
		case 'PH': //Philippines
			return 63;
		case 'PL': //Poland
			return 48;
		case 'PT': //Portugal
			return 351;
		case 'PR': //Puerto Rico
			return 1;
		case 'QA': //Qatar
			return 974;
		case 'RC': //Republic of the Congo
			return 242;
		case 'RE': //Réunion
			return 262;
		case 'RO': //Romania
			return 40;
		case 'RU': //Russia
			return 7;
		case 'RW': //Rwanda
			return 250;
		case 'NT': //Saint Barthélemy
			return 590;
		case 'SH': //Saint Helena
			return 290;
		case 'KN': //Saint Kitts and Nevis
			return 1;
		case 'RT': //Saint Martin
			return 590;
		case 'PM': //Saint Pierre and Miquelon
			return 508;
		case 'VC': //Saint Vincent and the Grenadines
			return 1;
		case 'WS': //Samoa
			return 685;
		case 'SM': //San Marino
			return 378;
		case 'ST': //São Tomé and Príncipe
			return 239;
		case 'SA': //Saudi Arabia
			return 966;
		case 'SN': //Senegal
			return 221;
		case 'CS': //Serbia
			return 381;
		case 'SC': //Seychelles
			return 248;
		case 'SL': //Sierra Leone
			return 232;
		case 'SG': //Singapore
			return 65;
		case 'AA': //Sint Maarten
			return 599;
		case 'SK': //Slovakia
			return 421;
		case 'SI': //Slovenia
			return 386;
		case 'SB': //Solomon Islands
			return 677;
		case 'SO': //Somalia
			return 252;
		case 'ZA': //South Africa
			return 27;
		case 'KO': //South Korea
			return 82;
		case 'SS': //South Sudan
			return 211;
		case 'ES': //Spain
			return 34;
		case 'LK': //Sri Lanka
			return 94;
		case 'LC': //St. Lucia
			return 1;
		case 'SD': //Sudan
			return 249;
		case 'SR': //Suriname
			return 597;
		case 'WA': //Swaziland
			return 268;
		case 'SE': //Sweden
			return 46;
		case 'CH': //Switzerland
			return 41;
		case 'SY': //Syria
			return 963;
		case 'TW': //Taiwan
			return 886;
		case 'TJ': //Tajikistan
			return 992;
		case 'TZ': //Tanzania
			return 255;
		case 'TH': //Thailand
			return 66;
		case 'TB': //The Bahamas
			return 1;
		case 'GB': //The Gambia
			return 220;
		case 'TL': //Timor-Leste
			return 670;
		case 'TG': //Togo
			return 228;
		case 'TK': //Tokelau
			return 690;
		case 'TO': //Tonga
			return 676;
		case 'TT': //Trinidad and Tobago
			return 1;
		case 'TN': //Tunisia
			return 216;
		case 'TR': //Turkey
			return 90;
		case 'TM': //Turkmenistan
			return 993;
		case 'TC': //Turks and Caicos Islands
			return 1;
		case 'TV': //Tuvalu
			return 688;
		case 'UG': //Uganda
			return 256;
		case 'UA': //Ukraine
			return 380;
		case 'AE': //United Arab Emirates
			return 971;
		case 'UK': //United Kingdom
			return 44;
		case 'US': //United States
			return 1;
		case 'UY': //Uruguay
			return 598;
		case 'UM': //US Virgin Islands
			return 1;
		case 'UZ': //Uzbekistan
			return 998;
		case 'VU': //Vanuatu
			return 678;
		case 'VA': //Vatican City
			return 39;
		case 'VE': //Venezuela
			return 58;
		case 'VN': //Vietnam
			return 84;
		case 'WF': //Wallis and Futuna
			return 681;
		case 'YE': //Yemen
			return 967;
		case 'ZM': //Zambia
			return 260;
		case 'ZW': //Zimbabwe
			return 263;
		default: 
			return '';
		}
	} 
?>