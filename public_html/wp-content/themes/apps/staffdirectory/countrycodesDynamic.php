<!--contains every country/calling zone in a select box, with some php so we can remember which was selected-->
<select name="phonecountry[]" id="phonecountry[]" length="11">
	<option value="CA" selected="1">Canada (+1)</option>
	<option value="AF" <?php if($phone->country_code == 'AF'){ echo 'selected="1"'; } ?>>Afghanistan (+93)</option>
	<option value="AL" <?php if($phone->country_code == 'AL'){ echo 'selected="1"'; } ?>>Albania (+355)</option>
	<option value="DZ" <?php if($phone->country_code == 'DZ'){ echo 'selected="1"'; } ?>>Algeria (+213)</option>
	<option value="AS" <?php if($phone->country_code == 'AS'){ echo 'selected="1"'; } ?>>American Samoa (+1)</option>
	<option value="AD" <?php if($phone->country_code == 'AD'){ echo 'selected="1"'; } ?>>Andorra (+376)</option>
	<option value="AO" <?php if($phone->country_code == 'AO'){ echo 'selected="1"'; } ?>>Angola (+244)</option>
	<option value="AI" <?php if($phone->country_code == 'AI'){ echo 'selected="1"'; } ?>>Anguilla (+1)</option>
	<option value="AG" <?php if($phone->country_code == 'AG'){ echo 'selected="1"'; } ?>>Antigua (+1)</option>
	<option value="AR" <?php if($phone->country_code == 'AR'){ echo 'selected="1"'; } ?>>Argentina (+54)</option>
	<option value="AM" <?php if($phone->country_code == 'AM'){ echo 'selected="1"'; } ?>>Armenia (+374)</option>
	<option value="AW" <?php if($phone->country_code == 'AW'){ echo 'selected="1"'; } ?>>Aruba (+297)</option>
	<option value="AT" <?php if($phone->country_code == 'AT'){ echo 'selected="1"'; } ?>>Australia (+61)</option>
	<option value="AU" <?php if($phone->country_code == 'AU'){ echo 'selected="1"'; } ?>>Austria (+43)</option>
	<option value="AZ" <?php if($phone->country_code == 'AZ'){ echo 'selected="1"'; } ?>>Azerbaijan (+994)</option>
	<option value="BH" <?php if($phone->country_code == 'BH'){ echo 'selected="1"'; } ?>>Bahrain (+973)</option>
	<option value="BD" <?php if($phone->country_code == 'BD'){ echo 'selected="1"'; } ?>>Bangladesh (+880)</option>
	<option value="BB" <?php if($phone->country_code == 'BB'){ echo 'selected="1"'; } ?>>Barbados (+1)</option>
	<option value="BY" <?php if($phone->country_code == 'BY'){ echo 'selected="1"'; } ?>>Belarus (+375)</option>
	<option value="BE" <?php if($phone->country_code == 'BE'){ echo 'selected="1"'; } ?>>Belgium (+32)</option>
	<option value="BZ" <?php if($phone->country_code == 'BZ'){ echo 'selected="1"'; } ?>>Belize (+501)</option>
	<option value="BJ" <?php if($phone->country_code == 'BJ'){ echo 'selected="1"'; } ?>>Benin (+229)</option>
	<option value="BM" <?php if($phone->country_code == 'BM'){ echo 'selected="1"'; } ?>>Bermuda (+1)</option>
	<option value="BT" <?php if($phone->country_code == 'BT'){ echo 'selected="1"'; } ?>>Bhutan (+975)</option>
	<option value="BO" <?php if($phone->country_code == 'BO'){ echo 'selected="1"'; } ?>>Bolivia (+591)</option>
	<option value="BS" <?php if($phone->country_code == 'BS'){ echo 'selected="1"'; } ?>>Bonaire, Sint Eustatius and Saba (+599)</option>
	<option value="BA" <?php if($phone->country_code == 'BA'){ echo 'selected="1"'; } ?>>Bosnia and Herzegovina (+387)</option>
	<option value="BW" <?php if($phone->country_code == 'BW'){ echo 'selected="1"'; } ?>>Botswana (+267)</option>
	<option value="BR" <?php if($phone->country_code == 'BR'){ echo 'selected="1"'; } ?>>Brazil (+55)</option>
	<option value="IO" <?php if($phone->country_code == 'IO'){ echo 'selected="1"'; } ?>>British Indian Ocean Territory (+246)</option>
	<option value="BV" <?php if($phone->country_code == 'BV'){ echo 'selected="1"'; } ?>>British Virgin Islands (+1)</option>
	<option value="BN" <?php if($phone->country_code == 'BN'){ echo 'selected="1"'; } ?>>Brunei (+673)</option>
	<option value="BG" <?php if($phone->country_code == 'BG'){ echo 'selected="1"'; } ?>>Bulgaria (+359)</option>
	<option value="BF" <?php if($phone->country_code == 'BF'){ echo 'selected="1"'; } ?>>Burkina Faso (+226)</option>
	<option value="BU" <?php if($phone->country_code == 'BU'){ echo 'selected="1"'; } ?>>Burma (Myanmar) (+95)</option>
	<option value="BI" <?php if($phone->country_code == 'BI'){ echo 'selected="1"'; } ?>>Burundi (+257)</option>
	<option value="KH" <?php if($phone->country_code == 'KH'){ echo 'selected="1"'; } ?>>Cambodia (+855)</option>
	<option value="CM" <?php if($phone->country_code == 'CM'){ echo 'selected="1"'; } ?>>Cameroon (+237)</option>
	<option value="CV" <?php if($phone->country_code == 'CV'){ echo 'selected="1"'; } ?>>Cape Verde (+238)</option>
	<option value="KY" <?php if($phone->country_code == 'KY'){ echo 'selected="1"'; } ?>>Cayman Islands (+1)</option>
	<option value="CF" <?php if($phone->country_code == 'CF'){ echo 'selected="1"'; } ?>>Central African Republic (+236)</option>
	<option value="TD" <?php if($phone->country_code == 'TD'){ echo 'selected="1"'; } ?>>Chad (+235)</option>
	<option value="CL" <?php if($phone->country_code == 'CL'){ echo 'selected="1"'; } ?>>Chile (+56)</option>
	<option value="CN" <?php if($phone->country_code == 'CN'){ echo 'selected="1"'; } ?>>China (+86)</option>
	<option value="CO" <?php if($phone->country_code == 'CO'){ echo 'selected="1"'; } ?>>Colombia (+57)</option>
	<option value="KM" <?php if($phone->country_code == 'KM'){ echo 'selected="1"'; } ?>>Comoros (+269)</option>
	<option value="CK" <?php if($phone->country_code == 'CK'){ echo 'selected="1"'; } ?>>Cook Islands (+682)</option>
	<option value="CR" <?php if($phone->country_code == 'CR'){ echo 'selected="1"'; } ?>>Costa Rica (+506)</option>
	<option value="CI" <?php if($phone->country_code == 'CI'){ echo 'selected="1"'; } ?>>Côte d'Ivoire (+225)</option>
	<option value="HR" <?php if($phone->country_code == 'HR'){ echo 'selected="1"'; } ?>>Croatia (+385)</option>
	<option value="CU" <?php if($phone->country_code == 'CU'){ echo 'selected="1"'; } ?>>Cuba (+53)</option>
	<option value="CC" <?php if($phone->country_code == 'CC'){ echo 'selected="1"'; } ?>>Curaçao (+599)</option>
	<option value="CY" <?php if($phone->country_code == 'CY'){ echo 'selected="1"'; } ?>>Cyprus (+357)</option>
	<option value="CZ" <?php if($phone->country_code == 'CZ'){ echo 'selected="1"'; } ?>>Czech Republic (+420)</option>
	<option value="DC" <?php if($phone->country_code == 'DC'){ echo 'selected="1"'; } ?>>Democratic Republic of the Congo (+243)</option>
	<option value="DK" <?php if($phone->country_code == 'DK'){ echo 'selected="1"'; } ?>>Denmark (+45)</option>
	<option value="DJ" <?php if($phone->country_code == 'DJ'){ echo 'selected="1"'; } ?>>Djibouti (+253)</option>
	<option value="DM" <?php if($phone->country_code == 'DM'){ echo 'selected="1"'; } ?>>Dominica (+1)</option>
	<option value="DO" <?php if($phone->country_code == 'DO'){ echo 'selected="1"'; } ?>>Dominican Republic (+1)</option>
	<option value="EC" <?php if($phone->country_code == 'EC'){ echo 'selected="1"'; } ?>>Ecuador (+593)</option>
	<option value="EG" <?php if($phone->country_code == 'EG'){ echo 'selected="1"'; } ?>>Egypt (+20)</option>
	<option value="SV" <?php if($phone->country_code == 'SV'){ echo 'selected="1"'; } ?>>El Salvador (+503)</option>
	<option value="GQ" <?php if($phone->country_code == 'GQ'){ echo 'selected="1"'; } ?>>Equatorial Guinea (+240)</option>
	<option value="ER" <?php if($phone->country_code == 'ER'){ echo 'selected="1"'; } ?>>Eritrea (+291)</option>
	<option value="EE" <?php if($phone->country_code == 'EE'){ echo 'selected="1"'; } ?>>Estonia (+372)</option>
	<option value="ET" <?php if($phone->country_code == 'ET'){ echo 'selected="1"'; } ?>>Ethiopia (+251)</option>
	<option value="FK" <?php if($phone->country_code == 'FK'){ echo 'selected="1"'; } ?>>Falkland Islands (+500)</option>
	<option value="FO" <?php if($phone->country_code == 'FO'){ echo 'selected="1"'; } ?>>Faroe Islands (+298)</option>
	<option value="MI" <?php if($phone->country_code == 'MI'){ echo 'selected="1"'; } ?>>Federated States of Micronesia (+691)</option>
	<option value="FJ" <?php if($phone->country_code == 'FJ'){ echo 'selected="1"'; } ?>>Fiji (+679)</option>
	<option value="FI" <?php if($phone->country_code == 'FI'){ echo 'selected="1"'; } ?>>Finland (+358)</option>
	<option value="FR" <?php if($phone->country_code == 'FR'){ echo 'selected="1"'; } ?>>France (+33)</option>
	<option value="GF" <?php if($phone->country_code == 'GF'){ echo 'selected="1"'; } ?>>French Guiana (+594)</option>
	<option value="PF" <?php if($phone->country_code == 'PF'){ echo 'selected="1"'; } ?>>French Polynesia (+689)</option>
	<option value="GA" <?php if($phone->country_code == 'GA'){ echo 'selected="1"'; } ?>>Gabon (+241)</option>
	<option value="GE" <?php if($phone->country_code == 'GE'){ echo 'selected="1"'; } ?>>Georgia (+995)</option>
	<option value="DE" <?php if($phone->country_code == 'DE'){ echo 'selected="1"'; } ?>>Germany (+49)</option>
	<option value="GH" <?php if($phone->country_code == 'GH'){ echo 'selected="1"'; } ?>>Ghana (+233)</option>
	<option value="GI" <?php if($phone->country_code == 'GI'){ echo 'selected="1"'; } ?>>Gibraltar (+350)</option>
	<option value="GR" <?php if($phone->country_code == 'GR'){ echo 'selected="1"'; } ?>>Greece (+30)</option>
	<option value="GL" <?php if($phone->country_code == 'GL'){ echo 'selected="1"'; } ?>>Greenland (+299)</option>
	<option value="GD" <?php if($phone->country_code == 'GD'){ echo 'selected="1"'; } ?>>Grenada (+1)</option>
	<option value="GP" <?php if($phone->country_code == 'GP'){ echo 'selected="1"'; } ?>>Guadeloupe (+590)</option>
	<option value="GU" <?php if($phone->country_code == 'GU'){ echo 'selected="1"'; } ?>>Guam (+1)</option>
	<option value="GT" <?php if($phone->country_code == 'GT'){ echo 'selected="1"'; } ?>>Guatemala (+502)</option>
	<option value="GN" <?php if($phone->country_code == 'GN'){ echo 'selected="1"'; } ?>>Guinea (+224)</option>
	<option value="GW" <?php if($phone->country_code == 'GW'){ echo 'selected="1"'; } ?>>Guinea-Bissau (+245)</option>
	<option value="GY" <?php if($phone->country_code == 'GY'){ echo 'selected="1"'; } ?>>Guyana (+592)</option>
	<option value="HT" <?php if($phone->country_code == 'HT'){ echo 'selected="1"'; } ?>>Haiti (+509)</option>
	<option value="HN" <?php if($phone->country_code == 'HN'){ echo 'selected="1"'; } ?>>Honduras (+504)</option>
	<option value="HK" <?php if($phone->country_code == 'HK'){ echo 'selected="1"'; } ?>>Hong Kong (+852)</option>
	<option value="HU" <?php if($phone->country_code == 'HU'){ echo 'selected="1"'; } ?>>Hungary (+36)</option>
	<option value="IS" <?php if($phone->country_code == 'IS'){ echo 'selected="1"'; } ?>>Iceland (+354)</option>
	<option value="IN" <?php if($phone->country_code == 'IN'){ echo 'selected="1"'; } ?>>India (+91)</option>
	<option value="ID" <?php if($phone->country_code == 'ID'){ echo 'selected="1"'; } ?>>Indonesia (+62)</option>
	<option value="IR" <?php if($phone->country_code == 'IR'){ echo 'selected="1"'; } ?>>Iran (+98)</option>
	<option value="IQ" <?php if($phone->country_code == 'IQ'){ echo 'selected="1"'; } ?>>Iraq (+964)</option>
	<option value="IE" <?php if($phone->country_code == 'IE'){ echo 'selected="1"'; } ?>>Ireland (+353)</option>
	<option value="IL" <?php if($phone->country_code == 'IL'){ echo 'selected="1"'; } ?>>Israel (+972)</option>
	<option value="IT" <?php if($phone->country_code == 'IT'){ echo 'selected="1"'; } ?>>Italy (+39)</option>
	<option value="JM" <?php if($phone->country_code == 'JM'){ echo 'selected="1"'; } ?>>Jamaica (+1)</option>
	<option value="JP" <?php if($phone->country_code == 'JP'){ echo 'selected="1"'; } ?>>Japan (+81)</option>
	<option value="JO" <?php if($phone->country_code == 'JO'){ echo 'selected="1"'; } ?>>Jordan (+962)</option>
	<option value="KZ" <?php if($phone->country_code == 'KZ'){ echo 'selected="1"'; } ?>>Kazakhstan (+7)</option>
	<option value="KE" <?php if($phone->country_code == 'KE'){ echo 'selected="1"'; } ?>>Kenya (+254)</option>
	<option value="KI" <?php if($phone->country_code == 'KI'){ echo 'selected="1"'; } ?>>Kiribati (+686)</option>
	<option value="KV" <?php if($phone->country_code == 'KV'){ echo 'selected="1"'; } ?>>Kosovo (+381)</option>
	<option value="KW" <?php if($phone->country_code == 'KW'){ echo 'selected="1"'; } ?>>Kuwait (+965)</option>
	<option value="KG" <?php if($phone->country_code == 'KG'){ echo 'selected="1"'; } ?>>Kyrgyzstan (+996)</option>
	<option value="LA" <?php if($phone->country_code == 'LA'){ echo 'selected="1"'; } ?>>Laos (+856)</option>
	<option value="LV" <?php if($phone->country_code == 'LV'){ echo 'selected="1"'; } ?>>Latvia (+371)</option>
	<option value="LB" <?php if($phone->country_code == 'LB'){ echo 'selected="1"'; } ?>>Lebanon (+961)</option>
	<option value="LS" <?php if($phone->country_code == 'LS'){ echo 'selected="1"'; } ?>>Lesotho (+266)</option>
	<option value="LR" <?php if($phone->country_code == 'LR'){ echo 'selected="1"'; } ?>>Liberia (+231)</option>
	<option value="LY" <?php if($phone->country_code == 'LY'){ echo 'selected="1"'; } ?>>Libya (+218)</option>
	<option value="LI" <?php if($phone->country_code == 'LI'){ echo 'selected="1"'; } ?>>Liechtenstein (+423)</option>
	<option value="LT" <?php if($phone->country_code == 'LT'){ echo 'selected="1"'; } ?>>Lithuania (+370)</option>
	<option value="LU" <?php if($phone->country_code == 'LU'){ echo 'selected="1"'; } ?>>Luxembourg (+352)</option>
	<option value="MO" <?php if($phone->country_code == 'MO'){ echo 'selected="1"'; } ?>>Macau (+853)</option>
	<option value="MK" <?php if($phone->country_code == 'MK'){ echo 'selected="1"'; } ?>>Macedonia (+389)</option>
	<option value="MG" <?php if($phone->country_code == 'MG'){ echo 'selected="1"'; } ?>>Madagascar (+261)</option>
	<option value="MW" <?php if($phone->country_code == 'MW'){ echo 'selected="1"'; } ?>>Malawi (+265)</option>
	<option value="MY" <?php if($phone->country_code == 'MY'){ echo 'selected="1"'; } ?>>Malaysia (+60)</option>
	<option value="MV" <?php if($phone->country_code == 'MV'){ echo 'selected="1"'; } ?>>Maldives (+960)</option>
	<option value="ML" <?php if($phone->country_code == 'ML'){ echo 'selected="1"'; } ?>>Mali (+223)</option>
	<option value="MT" <?php if($phone->country_code == 'MT'){ echo 'selected="1"'; } ?>>Malta (+356)</option>
	<option value="MH" <?php if($phone->country_code == 'MH'){ echo 'selected="1"'; } ?>>Marshall Islands (+692)</option>
	<option value="MQ" <?php if($phone->country_code == 'MQ'){ echo 'selected="1"'; } ?>>Martinique (+596)</option>
	<option value="MR" <?php if($phone->country_code == 'MR'){ echo 'selected="1"'; } ?>>Mauritania (+222)</option>
	<option value="MU" <?php if($phone->country_code == 'MU'){ echo 'selected="1"'; } ?>>Mauritius (+230)</option>
	<option value="YT" <?php if($phone->country_code == 'YT'){ echo 'selected="1"'; } ?>>Mayotte (+262)</option>
	<option value="MX" <?php if($phone->country_code == 'MX'){ echo 'selected="1"'; } ?>>Mexico (+52)</option>
	<option value="MD" <?php if($phone->country_code == 'MD'){ echo 'selected="1"'; } ?>>Moldova (+373)</option>
	<option value="MC" <?php if($phone->country_code == 'MC'){ echo 'selected="1"'; } ?>>Monaco (+377)</option>
	<option value="MN" <?php if($phone->country_code == 'MN'){ echo 'selected="1"'; } ?>>Mongolia (+976)</option>
	<option value="MT" <?php if($phone->country_code == 'MT'){ echo 'selected="1"'; } ?>>Montenegro (+382)</option>
	<option value="MS" <?php if($phone->country_code == 'MS'){ echo 'selected="1"'; } ?>>Montserrat (+1)</option>
	<option value="MA" <?php if($phone->country_code == 'MA'){ echo 'selected="1"'; } ?>>Morocco (+212)</option>
	<option value="MZ" <?php if($phone->country_code == 'MZ'){ echo 'selected="1"'; } ?>>Mozambique (+258)</option>
	<option value="NA" <?php if($phone->country_code == 'NA'){ echo 'selected="1"'; } ?>>Namibia (+264)</option>
	<option value="NR" <?php if($phone->country_code == 'NR'){ echo 'selected="1"'; } ?>>Nauru (+674)</option>
	<option value="NP" <?php if($phone->country_code == 'NP'){ echo 'selected="1"'; } ?>>Nepal (+977)</option>
	<option value="NL" <?php if($phone->country_code == 'NL'){ echo 'selected="1"'; } ?>>Netherlands (+31)</option>
	<option value="NC" <?php if($phone->country_code == 'NC'){ echo 'selected="1"'; } ?>>New Caledonia (+687)</option>
	<option value="NZ" <?php if($phone->country_code == 'NZ'){ echo 'selected="1"'; } ?>>New Zealand (+64)</option>
	<option value="NI" <?php if($phone->country_code == 'NI'){ echo 'selected="1"'; } ?>>Nicaragua (+505)</option>
	<option value="NE" <?php if($phone->country_code == 'NE'){ echo 'selected="1"'; } ?>>Niger (+227)</option>
	<option value="NG" <?php if($phone->country_code == 'NG'){ echo 'selected="1"'; } ?>>Nigeria (+234)</option>
	<option value="NU" <?php if($phone->country_code == 'NU'){ echo 'selected="1"'; } ?>>Niue (+683)</option>
	<option value="NF" <?php if($phone->country_code == 'NF'){ echo 'selected="1"'; } ?>>Norfolk Island (+672)</option>
	<option value="NK" <?php if($phone->country_code == 'NK'){ echo 'selected="1"'; } ?>>North Korea (+850)</option>
	<option value="MP" <?php if($phone->country_code == 'MP'){ echo 'selected="1"'; } ?>>Northern Mariana Islands (+1)</option>
	<option value="NO" <?php if($phone->country_code == 'NO'){ echo 'selected="1"'; } ?>>Norway (+47)</option>
	<option value="OM" <?php if($phone->country_code == 'OM'){ echo 'selected="1"'; } ?>>Oman (+968)</option>
	<option value="PK" <?php if($phone->country_code == 'PK'){ echo 'selected="1"'; } ?>>Pakistan (+92)</option>
	<option value="PW" <?php if($phone->country_code == 'PW'){ echo 'selected="1"'; } ?>>Palau (+680)</option>
	<option value="PS" <?php if($phone->country_code == 'PS'){ echo 'selected="1"'; } ?>>Palestine (+970)</option>
	<option value="PA" <?php if($phone->country_code == 'PA'){ echo 'selected="1"'; } ?>>Panama (+507)</option>
	<option value="PG" <?php if($phone->country_code == 'PG'){ echo 'selected="1"'; } ?>>Papua New Guinea (+675)</option>
	<option value="PY" <?php if($phone->country_code == 'PY'){ echo 'selected="1"'; } ?>>Paraguay (+595)</option>
	<option value="PE" <?php if($phone->country_code == 'PE'){ echo 'selected="1"'; } ?>>Peru (+51)</option>
	<option value="PH" <?php if($phone->country_code == 'PH'){ echo 'selected="1"'; } ?>>Philippines (+63)</option>
	<option value="PL" <?php if($phone->country_code == 'PL'){ echo 'selected="1"'; } ?>>Poland (+48)</option>
	<option value="PT" <?php if($phone->country_code == 'PT'){ echo 'selected="1"'; } ?>>Portugal (+351)</option>
	<option value="PR" <?php if($phone->country_code == 'PR'){ echo 'selected="1"'; } ?>>Puerto Rico (+1)</option>
	<option value="QA" <?php if($phone->country_code == 'QA'){ echo 'selected="1"'; } ?>>Qatar (+974)</option>
	<option value="RC" <?php if($phone->country_code == 'RC'){ echo 'selected="1"'; } ?>>Republic of the Congo (+242)</option>
	<option value="RE" <?php if($phone->country_code == 'RE'){ echo 'selected="1"'; } ?>>Réunion (+262)</option>
	<option value="RO" <?php if($phone->country_code == 'RO'){ echo 'selected="1"'; } ?>>Romania (+40)</option>
	<option value="RU" <?php if($phone->country_code == 'RU'){ echo 'selected="1"'; } ?>>Russia (+7)</option>
	<option value="RW" <?php if($phone->country_code == 'RW'){ echo 'selected="1"'; } ?>>Rwanda (+250)</option>
	<option value="NT" <?php if($phone->country_code == 'NT'){ echo 'selected="1"'; } ?>>Saint Barthélemy (+590)</option>
	<option value="SH" <?php if($phone->country_code == 'SH'){ echo 'selected="1"'; } ?>>Saint Helena (+290)</option>
	<option value="KN" <?php if($phone->country_code == 'KN'){ echo 'selected="1"'; } ?>>Saint Kitts and Nevis (+1)</option>
	<option value="RT" <?php if($phone->country_code == 'RT'){ echo 'selected="1"'; } ?>>Saint Martin (+590)</option>
	<option value="PM" <?php if($phone->country_code == 'PM'){ echo 'selected="1"'; } ?>>Saint Pierre and Miquelon (+508)</option>
	<option value="VC" <?php if($phone->country_code == 'VC'){ echo 'selected="1"'; } ?>>Saint Vincent and the Grenadines (+1)</option>
	<option value="WS" <?php if($phone->country_code == 'WS'){ echo 'selected="1"'; } ?>>Samoa (+685)</option>
	<option value="SM" <?php if($phone->country_code == 'SM'){ echo 'selected="1"'; } ?>>San Marino (+378)</option>
	<option value="ST" <?php if($phone->country_code == 'ST'){ echo 'selected="1"'; } ?>>São Tomé and Príncipe (+239)</option>
	<option value="SA" <?php if($phone->country_code == 'SA'){ echo 'selected="1"'; } ?>>Saudi Arabia (+966)</option>
	<option value="SN" <?php if($phone->country_code == 'SN'){ echo 'selected="1"'; } ?>>Senegal (+221)</option>
	<option value="CS" <?php if($phone->country_code == 'CS'){ echo 'selected="1"'; } ?>>Serbia (+381)</option>
	<option value="SC" <?php if($phone->country_code == 'SC'){ echo 'selected="1"'; } ?>>Seychelles (+248)</option>
	<option value="SL" <?php if($phone->country_code == 'SL'){ echo 'selected="1"'; } ?>>Sierra Leone (+232)</option>
	<option value="SG" <?php if($phone->country_code == 'SG'){ echo 'selected="1"'; } ?>>Singapore (+65)</option>
	<option value="AA" <?php if($phone->country_code == 'AA'){ echo 'selected="1"'; } ?>>Sint Maarten (+599)</option>
	<option value="SK" <?php if($phone->country_code == 'SK'){ echo 'selected="1"'; } ?>>Slovakia (+421)</option>
	<option value="SI" <?php if($phone->country_code == 'SI'){ echo 'selected="1"'; } ?>>Slovenia (+386)</option>
	<option value="SB" <?php if($phone->country_code == 'SB'){ echo 'selected="1"'; } ?>>Solomon Islands (+677)</option>
	<option value="SO" <?php if($phone->country_code == 'SO'){ echo 'selected="1"'; } ?>>Somalia (+252)</option>
	<option value="ZA" <?php if($phone->country_code == 'ZA'){ echo 'selected="1"'; } ?>>South Africa (+27)</option>
	<option value="KO" <?php if($phone->country_code == 'KO'){ echo 'selected="1"'; } ?>>South Korea (+82)</option>
	<option value="SS" <?php if($phone->country_code == 'SS'){ echo 'selected="1"'; } ?>>South Sudan (+211)</option>
	<option value="ES" <?php if($phone->country_code == 'ES'){ echo 'selected="1"'; } ?>>Spain (+34)</option>
	<option value="LK" <?php if($phone->country_code == 'LK'){ echo 'selected="1"'; } ?>>Sri Lanka (+94)</option>
	<option value="LC" <?php if($phone->country_code == 'LC'){ echo 'selected="1"'; } ?>>St. Lucia (+1)</option>
	<option value="SD" <?php if($phone->country_code == 'SD'){ echo 'selected="1"'; } ?>>Sudan (+249)</option>
	<option value="SR" <?php if($phone->country_code == 'SR'){ echo 'selected="1"'; } ?>>Suriname (+597)</option>
	<option value="WA" <?php if($phone->country_code == 'WA'){ echo 'selected="1"'; } ?>>Swaziland (+268)</option>
	<option value="SE" <?php if($phone->country_code == 'SE'){ echo 'selected="1"'; } ?>>Sweden (+46)</option>
	<option value="CH" <?php if($phone->country_code == 'CH'){ echo 'selected="1"'; } ?>>Switzerland (+41)</option>
	<option value="SY" <?php if($phone->country_code == 'SY'){ echo 'selected="1"'; } ?>>Syria (+963)</option>
	<option value="TW" <?php if($phone->country_code == 'TW'){ echo 'selected="1"'; } ?>>Taiwan (+886)</option>
	<option value="TJ" <?php if($phone->country_code == 'TJ'){ echo 'selected="1"'; } ?>>Tajikistan (+992)</option>
	<option value="TZ" <?php if($phone->country_code == 'TZ'){ echo 'selected="1"'; } ?>>Tanzania (+255)</option>
	<option value="TH" <?php if($phone->country_code == 'TH'){ echo 'selected="1"'; } ?>>Thailand (+66)</option>
	<option value="TB" <?php if($phone->country_code == 'TB'){ echo 'selected="1"'; } ?>>The Bahamas (+1)</option>
	<option value="GB" <?php if($phone->country_code == 'GB'){ echo 'selected="1"'; } ?>>The Gambia (+220)</option>
	<option value="TL" <?php if($phone->country_code == 'TL'){ echo 'selected="1"'; } ?>>Timor-Leste (+670)</option>
	<option value="TG" <?php if($phone->country_code == 'TG'){ echo 'selected="1"'; } ?>>Togo (+228)</option>
	<option value="TK" <?php if($phone->country_code == 'TK'){ echo 'selected="1"'; } ?>>Tokelau (+690)</option>
	<option value="TO" <?php if($phone->country_code == 'TO'){ echo 'selected="1"'; } ?>>Tonga (+676)</option>
	<option value="TT" <?php if($phone->country_code == 'TT'){ echo 'selected="1"'; } ?>>Trinidad and Tobago (+1)</option>
	<option value="TN" <?php if($phone->country_code == 'TN'){ echo 'selected="1"'; } ?>>Tunisia (+216)</option>
	<option value="TR" <?php if($phone->country_code == 'TR'){ echo 'selected="1"'; } ?>>Turkey (+90)</option>
	<option value="TM" <?php if($phone->country_code == 'TM'){ echo 'selected="1"'; } ?>>Turkmenistan (+993)</option>
	<option value="TC" <?php if($phone->country_code == 'TC'){ echo 'selected="1"'; } ?>>Turks and Caicos Islands (+1)</option>
	<option value="TV" <?php if($phone->country_code == 'TV'){ echo 'selected="1"'; } ?>>Tuvalu (+688)</option>
	<option value="UG" <?php if($phone->country_code == 'UG'){ echo 'selected="1"'; } ?>>Uganda (+256)</option>
	<option value="UA" <?php if($phone->country_code == 'UA'){ echo 'selected="1"'; } ?>>Ukraine (+380)</option>
	<option value="AE" <?php if($phone->country_code == 'AE'){ echo 'selected="1"'; } ?>>United Arab Emirates (+971)</option>
	<option value="UK" <?php if($phone->country_code == 'UK'){ echo 'selected="1"'; } ?>>United Kingdom (+44)</option>
	<option value="US" <?php if($phone->country_code == 'US'){ echo 'selected="1"'; } ?>>United States (+1)</option>
	<option value="UY" <?php if($phone->country_code == 'UY'){ echo 'selected="1"'; } ?>>Uruguay (+598)</option>
	<option value="UM" <?php if($phone->country_code == 'UM'){ echo 'selected="1"'; } ?>>US Virgin Islands (+1)</option>
	<option value="UZ" <?php if($phone->country_code == 'UZ'){ echo 'selected="1"'; } ?>>Uzbekistan (+998)</option>
	<option value="VU" <?php if($phone->country_code == 'VU'){ echo 'selected="1"'; } ?>>Vanuatu (+678)</option>
	<option value="VA" <?php if($phone->country_code == 'VA'){ echo 'selected="1"'; } ?>>Vatican City (+39)</option>
	<option value="VE" <?php if($phone->country_code == 'VE'){ echo 'selected="1"'; } ?>>Venezuela (+58)</option>
	<option value="VN" <?php if($phone->country_code == 'VN'){ echo 'selected="1"'; } ?>>Vietnam (+84)</option>
	<option value="WF" <?php if($phone->country_code == 'WF'){ echo 'selected="1"'; } ?>>Wallis and Futuna (+681)</option>
	<option value="YE" <?php if($phone->country_code == 'YE'){ echo 'selected="1"'; } ?>>Yemen (+967)</option>
	<option value="ZM" <?php if($phone->country_code == 'ZM'){ echo 'selected="1"'; } ?>>Zambia (+260)</option>
	<option value="ZW" <?php if($phone->country_code == 'ZW'){ echo 'selected="1"'; } ?>>Zimbabwe (+263)</option>
</select>