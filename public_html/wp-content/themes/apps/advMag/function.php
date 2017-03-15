<?php

require(get_stylesheet_directory().'\functions\se_api.php');

class AdvMag{
	private static $prefix = 'advmag_';
	private static $api = null; 
	
	public static function getDonors(){
		require_once(get_stylesheet_directory().'\functions\functions.php');
		self::$api = new API(null, null, null, 30, true);
		//todo limit who ie only CS
		$pc = getFieldEmployee('staff_account');
		$pc = '810500';
		//https=>//seapi.powertochange.org/accounts?advancedFind=%7B%22table%22%3A%22A01%22%2C%22isDistinct%22%3Atrue%2C%22selectionCriteria%22%3A%5B%7B%22group%22%3A1%2C%22table%22%3A%22T04%22%2C%22operator%22%3A%22EQ%22%2C%22field%22%3A%22ProjectCode%22%2C%22isJoin%22%3Afalse%2C%22value%22%3A%22810550%22%7D%2C%7B%22isJoin%22%3Atrue%2C%22table%22%3A%22A01%22%2C%22value%22%3A%22T01%22%2C%22field%22%3Anull%2C%22operator%22%3Anull%7D%2C%7B%22isJoin%22%3Atrue%2C%22table%22%3A%22T01%22%2C%22field%22%3Anull%2C%22operator%22%3Anull%2C%22value%22%3A%22T04%22%7D%2C%7B%22group%22%3A1%2C%22table%22%3A%22T01%22%2C%22operator%22%3A%22ON_OR_BEFORE%22%2C%22field%22%3A%22Date%22%2C%22isJoin%22%3Afalse%2C%22value%22%3A%222016-03-15%22%7D%5D%2C%22viewId%22%3Anull%7D&offset=0&limit=9
		$af = array(
			'table' => 'A01',
			'isDistinct' => true,
			'selectionCriteria' => array(
				array(
					'group'=>1,
					'table' => 'T04',
					'operator' => 'EQ',
					'field' => 'ProjectCode',
					'isJoin' => false,
					'value' => $pc
				),
				array(
					'isJoin' => true,
					'table' => 'A01',
					'value' => 'T01',
					'field' => null,
					'operator' => null
				),
				array(
					'isJoin' => true,
					'table' => 'T01',
					'field' => null,
					'operator' => null,
					'value' => 'T04'
				),
				array(
					'group' => 1,
					'table' => 'T01',
					'operator' => 'ON_OR_BEFORE',
					'field' => 'Date',
					'isJoin'=> false,
					'value'=> date("Y-m-d",strtotime("-1 year"))
				)
			),
			'viewId' => null
		);
		$table = array();
		if($pc){
			$accounts = self::$api->verifySend('/accounts?advancedFind='.urlencode(json_encode($af)), 'ALL');
			foreach($accounts as $a){
				$c = self::getCodes($a['Id']);
				$table[] = array(
					'id' => $a['Id'],
					'name' => $a['FormattedName'],
					'method' => $c['method'],
					'address' => self::getAddress($a),
					'invalid' => self::getInvalid($a['Id'])
				);
			}
		}
		header('Content-Type: application/json');
		die(json_encode(array('r' => $table)));
	}
	
	private function getInvalid($id){
		$c = self::$api->getCode($id, 'MAILIMIT');
		if('INVALID' == $c){
			return 'invalid';
		}
		return '';
	}
	
	private static function getCodes($id){
		return array('language' => 'English', 'method' => '');
	}
	
	private static function getAddress($account){
		$address = self::$api->verifySend("/accounts/$account[Id]/addresses/$account[A02AddressId]")['body'];
		return array(
			'id' => $account['A02AddressId'],
			'line4' => (is_null($address['line4']) ? '' : $address['line4']),
			'line1' => (is_null($address['line1']) ? '' : $address['line1']),
			'line2' => (is_null($address['line2']) ? '' : $address['line2']),
			'line3' => (is_null($address['line3']) ? '' : $address['line3']),
			'city' => (is_null($address['city']) ? '' : $address['city']),
			'state' => (is_null($address['state']) ? '' : $address['state']),
			'postalCode' => (is_null($address['postalCode']) ? '' : $address['postalCode']),
			'country' => (is_null($address['country']) ? '' : $address['country'])
		);
	}
	
	public static function init(){
		add_action('wp_ajax_'.self::$prefix.'getDonors', 'AdvMag::getDonors');
	}
}

AdvMag::init();
