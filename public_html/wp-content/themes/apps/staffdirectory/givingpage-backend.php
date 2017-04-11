<?php
include get_stylesheet_directory() .'/functions/so_api.php';
include get_stylesheet_directory().'/functions/webService.php';

class Givingpage{
	public static $prefix = 'advgp_';
	private static $allItems = null;
	
	static function canUse(){
		global $current_user_id;
		require_once(get_stylesheet_directory().'/functions/functions.php');
		return true;
		return '8' == getFieldEmployee('staff_account')[0] || 
			'9' == getFieldEmployee('staff_account');
	}
	
	static function getStrings(){
		$locale = 'en-US';
		if (array_key_exists('locale', $_POST)){
			$locale = $_POST['locale'];
		}
		$r = WebService::send(get_option(self::$prefix.'soServer').'/PTC_ClientScriptHelper.asmx', 'GetStrings', array('keys' => $_POST['keys'], 'locale' => $locale));
		wp_send_json($r['body']);
	}
	
	static function getInfo(){
		global $current_user_id;
		require_once(get_stylesheet_directory().'/functions/functions.php');
		//todo $pc = getFieldEmployee('staff_account');
		$pc = '870220';
		$r = self::openProjectInfo();
		$r['pc'] = $pc;
		$r['gender'] = self::getGender();
		wp_send_json(array('r' => $r));
	}
	
	static function setInfo(){
		global $current_user_id;
		require_once(get_stylesheet_directory().'/functions/functions.php');
		$extensionData = array('edited' => 1);
		$data = array();
		if($_POST['isPic']){
			$info = getimagesize($_POST['pic']);
			if(350 != $info[0] || 350 != $info[1]){
				http_response_code(400);
				die();
			}
			$data['Images'] = array(
				'Medium' => array(
					'@attributes' => array(
						//yah, I know it is encode as png...
						'Extension' => 'jpg'
					),
					'@value' => substr($_POST['pic'], strlen('data:image/png;base64,'))
				),
				'ImageFilenameOverride' => array('@value' => '')
			);
		}
		$pid = self::getProductID();
		if($_POST['closed']){
			//todo change to db
			$data['Name'] = array('@value' => '870220');
			$data['Description'] = array('@value' => '');
			$data['Mappings'] = array(
				'@attributes' => array(
					'AutoCleanup' => true,
					'PreserveExistingRecords' => false
				),
				'Entity' => array('@attributes' => array(
					'EntityType' => 'Category',
					'ID' => self::getAllItems()['inter']
				))
			);
			$data['Images'] = array(
				'Medium' => array(
					'@attributes' => array(
						//yah, I know it is encode as png...
						'Extension' => 'jpg'
					),
					'@value' => base64_encode(file_get_contents(get_option(self::$prefix.'soServer').'/images/product/medium/'.self::getGender().'.jpg'))
				),
				'ImageFilenameOverride' => array('@value' => '')
			);
			$extensionData['logo'] = 0;
		} else {
			
			$p = self::getAllItems()['project'][$pid];
			$oi = self::openProjectInfo();
			
			//set name
			if('<' != $pid['label'][0]){
				$minOFE = WebService::send(get_option(self::$prefix.'soServer').'/PTC_ClientScriptHelper.asmx', 'GetStrings', array('keys' => array('ptc.minOf'), 'locale' => 'en-US'))['body']['d'][0];
				$minOFF = WebService::send(get_option(self::$prefix.'soServer').'/PTC_ClientScriptHelper.asmx', 'GetStrings', array('keys' => array('ptc.minOf'), 'locale' => 'fr-CA'))['body']['d'][0];
			
				$data['Name'] = array(
					'@cdata' => $oi['name']
				);
			}
			
			//set cats
			//first checks to see if changes need to be done;
			$cats = self::getAllCategories();
			$d = false;
			foreach($cats as $c){
				if(!in_array($c, $oi['cats'])){
					$d = true;
					break;
				}
			}
			foreach($oi['cats'] as $c){
				if(!in_array($c, $cats)){
					$d = true;
					break;
				}
			}
			if($d){
				$data['Mappings'] = array(
					'@attributes' => array(
						'AutoCleanup' => true,
						'PreserveExistingRecords' => false
					),
					'Entity' => array()
				);
				foreach($oi['cats'] as $c){
					$data['Mappings']['Entity'][] = array('@attributes' => array(
						'EntityType' => 'Category',
						'ID' => $c
					));
				}
			}
			
			$data['Description'] = array(
				'@cdata' => '<ml><locale name="en-US">'.strip_tags($_POST['des']).'</locale>'.
					'<locale name="fr-CA">'.strip_tags($_POST['desFre']).'</locale></ml>'
			);
		}
		
		//todo eAcks
		
		if(array_key_exists('onetime', $_POST)){
			$extensionData['onetime'] = $_POST['onetime'];
		} else if(array_key_exists('recurring', $_POST)){
			$extensionData['recurring'] = $_POST['recurring'];
		}
		$data['ExtensionData'] = array(
			'@value' => json_encode($extensionData)
		);
		wp_send_json(array('data' => $data, 'return' => SO_API::updateProduct($pid, $data)));
	}
	
	private static function getGender(){
		if (-1 != getSpouse()){ 
			return 'married'; // :)
		}
		if('M' == getFieldEmployee('gender')){
			return 'male';
		}
		return 'female';
	}
	
	private static function getAllCategories($pid){
		$cats = array();
		//just staff
		$ai = self::getAllItems();
		$staffC = $ai[$ai['staff']];
		foreach($staffC['cats'] as $c){
			foreach($c['cats'] as $p){
				if($p == $pid){
					$cats[] = $p;
				}
			}
		}	
		return $cats;
	}
	
	private static function openProjectInfo(){
		$name = getName(null, true);
		$minOFE = WebService::send(get_option(self::$prefix.'soServer').'/PTC_ClientScriptHelper.asmx', 'GetStrings', array('keys' => array('ptc.minOf'), 'locale' => 'en-US'))['body']['d'][0];
		$minOFF = WebService::send(get_option(self::$prefix.'soServer').'/PTC_ClientScriptHelper.asmx', 'GetStrings', array('keys' => array('ptc.minOf'), 'locale' => 'fr-CA'))['body']['d'][0];
	
		$info = array('name' => "<ml><locale name=\"en-US\">$minOFE $name</locale>".
				"<locale name=\"fr-CA\">$minOFF $name</locale></ml>"
		);
		$info['cats'] = array(WebService::send(get_option(self::$prefix.'seWebService').'/service.asmx', 
			'GetCategoryFromMinistry', array(
				'ministry' => 'Athletes in Action', //getFieldEmployee('ministry'),
				'department' => '' //getFieldEmployee('department')
			)
		)['body']['d']);
		
		if (-1 != getSpouse()) { 
			$info['cats'][] = WebService::send('http://ws.adv-01d0986.powertochange.local'.'/service.asmx', 
				'GetCategoryFromMinistry', array(
					'ministry' =>  getFieldEmployee('ministry', getSpouse()),
					'department' => getFieldEmployee('department', getSpouse())
				)
			)['body']['d'];
		}
		return $info;
	}
	
	static function init(){
		add_action('wp_ajax_'.self::$prefix.'GetStrings', 'Givingpage::getStrings');
		add_action('wp_ajax_'.self::$prefix.'GetInfo', 'Givingpage::getInfo');
		add_action('wp_ajax_'.self::$prefix.'SetInfo', 'Givingpage::setInfo');
	}
	
	private static function getProductID(){
		//todo $pc = getFieldEmployee('staff_account');
		$pc = '870220';
		foreach(self::getAllItems()['projects'] as $id => $data){
			if($pc == $data['sku']){
				return $id;
			}
		}
	}
	
	private static function getAllItems(){
		if(is_null($allItems)){
			$raw = file_get_contents(get_option(self::$prefix.'soServer').'/jscripts/list.aspx?r='.rand());
			$raw =  strrev(substr($raw, strlen('allItems = ')));
			$p = strpos($raw, '// ');
			$allItems = json_decode(strrev(substr($raw, $p + 3)), true);
		}
		return $allItems;
	}
}

Givingpage::init();