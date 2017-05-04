<?php

require(get_stylesheet_directory().'/functions/se_api.php');

class AdvMag{
	private static $prefix = 'advmag_';
	private static $api = null; 
	
	public static function getDonors(){
		
		$table = array();
		foreach(self::getAccounts()as $a){
			$table[] = array(
				'id' => $a['Id'],
				'name' => $a['FormattedName'],
				'lang' => self::$api->getCode($a['Id'], 'DDCLANG')
			);
		}
		header('Content-Type: application/json');
		die(json_encode(array('r' => $table)));
	}
	
	public static function sendInfo(){
		$id = null;
		foreach(self::getAccounts()as $a){
			if($_REQUEST['id'] == $a['Id']){
				$id = $a['Id'];
				break;
			}
		}
		if(is_null($id)){
			http_response_code(400);
			die();
		}
		if(array_key_exists('DDCLANG', $_REQUEST)){
			if($_REQUEST['DDCLANG']){
				self::$api->setCode($id, 'DDCLANG', $_REQUEST['DDCLANG']);
			} else {
				self::$api->deleteCode($id, 'DDCLANG');
			}
		}
		
	}
	
	private function getInvalid($id){
		$c = self::$api->getCode($id, 'MAILIMIT');
		if('INVALID' == $c){
			return 'invalid';
		}
		return '';
	}
	
	private static function getAccounts(){
		require_once(get_stylesheet_directory().'/functions/functions.php');
		self::$api = new API(null, null, null, 30, true);
		//limit who ie only CS
		$pc = getFieldEmployee('staff_account');
		$pc = '100400';
		if('' == $pc || is_null($pc) || ('8' != $pc[0] && '9' != $pc[0])){
			return array();
		}
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
					'operator' => 'ON_OR_AFTER',
					'field' => 'Date',
					'isJoin'=> false,
					'value'=> date("Y-m-d",strtotime("-24 month"))
				)
			),
			'viewId' => null
		);
		return self::$api->verifySend('/accounts?advancedFind='.urlencode(json_encode($af)), 'ALL');
	}
	
	public static function init(){
		add_action('wp_ajax_'.self::$prefix.'getDonors', 'AdvMag::getDonors');
		add_action('wp_ajax_'.self::$prefix.'sendInfo', 'AdvMag::sendInfo');
	}
}

AdvMag::init();
