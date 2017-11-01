<?php
include get_stylesheet_directory().'/functions/Array2XML.php';

/** This is for asp storefront soap api see http://manual.aspdotnetstorefront.com/c-144-wsi.aspx **/

class SO_API{

	static function getProduct($id){
		return self::send(array(
			'GetProduct' => array(
				'@attributes' => array(
					'ID' => $id,
					'IncludeVariants' => false,
					'IncludeImages' => false
				)
			)
		))->GetProduct->Product;
	}
	
	static function updateProduct($id, $data){
		return self::send(array(
			'Product' => array_merge(array(
				'@attributes' => array(
					'ID' => $id,
					'Action' => 'Update'
				)),
				$data
			)
		));
	}
	
	static function send($data){
		$url = get_option(Givingpage::$prefix.'soServer').'/ipx.asmx?WSDL';
		$command = array_merge(
			array('@attributes' => array(
				'Verbose' => defined('WP_DEBUG') && true === WP_DEBUG,
				'AutoLazyAdd' => false
			)), 
			$data
		);
		
		$xml = Array2XML::createXML('AspDotNetStorefrontImport', $command);
		$xml_command = $xml->saveXML();
		
		$request = new SoapClient($url, array('connection_timeout' => 30, 'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP));
		$parameters = array(
			'AuthenticationEMail' => get_option(Givingpage::$prefix.'soServer_User'),
			'AuthenticationPassword' => get_option(Givingpage::$prefix.'soServer_Pass'),
			'XmlInputRequestString' => $xml_command
		);
		
		$result = $request->DoItUsernamePwd($parameters);
		return simplexml_load_string($result->DoItUsernamePwdResult);
	}
	
	static function xmlEncodeSpecial($str){
		return str_replace(
			array('&', '<', '>', '\\\'', '\''), 
			array('&#38;', '&#60;' , '&#62;', '&#39;', '&#39;'), 
			$str
		);
	}
	
	static function xmlDecodeSpecial($str){
		return str_replace(
			array('&#38;', '&#60;' , '&#62;', '&#39;'), 
			array('&', '<', '>', '\''), 
			$str
		);
	}
}