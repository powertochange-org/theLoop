<?php 
class WebService{

	static function send($method, $server, $function, $dat){
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_ENCODING,  '');
		curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_TIMEOUT, 120); //seconds todo change?
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
		$headers = array();
		$headers[] = 'Content-Type: application/json';
		if('POST' == $method){
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($dat));
			$headers[] = 'Content-Length: '.strlen(json_encode($dat));
		} else {
			curl_setopt($handle, CURLOPT_POST, false);
			curl_setopt($handle, CURLOPT_POSTFIELDS, null);
			$function .= '?';
			$f = true;
			foreach($dat as $k => $v){
				if($f){
					$f = false;
				}else{
					$function .= '&';
				}
				$function .= "$k=".json_encode($v);
			}
		}
		curl_setopt($handle, CURLOPT_URL, "$server/$function");
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	
		$result = curl_exec($handle);
		$lines = explode("\r\n",$result);
		if(2 > count(explode(' ', $lines[0]))){
			error_log("WebService Error:");
			error_log($result);
			error_log(curl_error($handle));
			return array(
				'httpCode' => 500,
				'body' => 'Error',
				'result' => $result,
				'request' => array('path' => $function, 'data' => $dat)
			);
		}
		$code = explode(' ', $lines[0])[1];
		
		$flag = false;
		$sBody = '';
		foreach($lines as $l){
			if('' == $l){
				$flag = true;
			}else if($flag){
				$sBody = $l;
			}
		}
		$body = json_decode($sBody, true);
		if(is_null($body)){
			$body['Message'] = 'Compilation Error?';
		}
		return array(
			'httpCode' => $code,
			'body' => $body,
			'request' => array('path' => $function, 'data' => $dat)
		);
	}
}
