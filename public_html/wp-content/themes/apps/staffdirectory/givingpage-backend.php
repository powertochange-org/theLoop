<?php
include get_stylesheet_directory() .'/functions/so_api.php';
include get_stylesheet_directory().'/functions/webservice.php';

class Givingpage{
	public static $prefix = 'advgp_';
	
	static function getStrings(){
		$locale = 'en-US';
		if (array_key_exists('locale', $_POST)){
			$locale = $_POST['locale'];
		}
		$r = WebService::send(get_option(self::$prefix.'soServer').'/PTC_ClientScriptHelper.asmx', 'GetStrings', array('keys' => $_POST['keys'], 'locale' => $locale));
		wp_send_json($r['body']);
	}
	
	static function getInfo(){
		require_once(get_stylesheet_directory().'/functions/functions.php');
		//$pc = getFieldEmployee('staff_account');
		$pc = '810550';
		//fe/male
		wp_send_json(array('r' => array('pc' => $pc, 'gender' => 'married')));
	}
	
	static function setInfo(){
	}
	
	static function openProject(){
	}
	
	static function init(){
		add_action('wp_ajax_'.self::$prefix.'GetStrings', 'Givingpage::getStrings');
		add_action('wp_ajax_'.self::$prefix.'GetInfo', 'Givingpage::getInfo');
		add_action('wp_ajax_'.self::$prefix.'SetInfo', 'Givingpage::setInfo');
		add_action('wp_ajax_'.self::$prefix.'OpenProject', 'Givingpage::openProject');
	}
}

Givingpage::init();