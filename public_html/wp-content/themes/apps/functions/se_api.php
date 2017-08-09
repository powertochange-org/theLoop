<?php 
class API{

	static $option_prefix = 'ptc_se_';

	private $server;
	private $login;
	private $timeout;
	private $storeToken;
	private $token = null;
	private $curl = array();
	private $mh = null;
	
	function API($server=null, $username=null, $password=null, $timeout=30, $storeToken=false){
		if(is_null($server)){
			$s = get_option(self::$option_prefix.'server');
			if($s){
				$this->server = $s;
			} else {
				http_response_code(500);
				die("Missing options");
			}
		} else{
			$this->server = $server;
		}
		if(is_null($username)){
			$s = get_option(self::$option_prefix.'username');
			if($s){
				$username = $s;
			} else {
				http_response_code(500);
				die("Missing options");
			}
		}
		if(is_null($password)){
			$s = get_option(self::$option_prefix.'password');
			if($s){
				$password = $s;
			} else {
				http_response_code(500);
				die("Missing options");
			}
		}
		$this->timeout = $timeout;
		$this->login = "$username:$password";
		$this->storeToken = $storeToken;
		if($this->storeToken){
			$t = get_option(self::$option_prefix.'token_'.$this->server);
			if($t){
				$this->token = $t;
			}
		}
	}
	
	static $title_no = 0;
	static $title_tax = 1;
	static $tax_titles = array('Dr.', 'Rev.', 'Pastor', 'Lt. Colonel', 'Major', 'Professor');
	static $title_yes = 2;
	function getFullName($account, $title=2){
		if(!is_array($account)){
			$account = $this->verifySend("/accounts/$account/", 'GET')['body'];
		}
		if('I' == $account['type'] || 'SP' == $account['type']){
			$name = '';
			switch($title){
			case API::$title_tax:
				if(!in_array($account['title'], API::$tax_titles)){
					break;
				}
				//fall through
			case API::$title_tax:
				$name = "$account[title] ";
				break;
			case API::$title_no;
				break;
			}
			return 	$name.$account['firstName'].' '.($account['middleName'] ? $account['middleName'].' '  : '').$account['lastName'].($account['suffix'] ? ' '.$account['suffix'] : '');
		}
		if('F' == $account['type']){
			return $this->getFullName($account['spouses'][0], $title).' and '.$this->getFullName($account['spouses'][1], $title);
		}
		return $account['name'];
	}
	
	function getTypeOption($type){
		$items = array();
		foreach($this->verifySend("/codetypes/$type/values?isActive=1", 'ALL') as $i){
			if($i['isActive']){
				$items[] = $i;
			}
		}
		return tranList($items);
	}
	
	function setCode($account, $code, $value, $multi=false){
		if($multi){
			return $this->verifySend("/accounts/$account/codes", 'POST', array('type' => $code, 'value' => $value, 'isActive' => 'true'));
		}
		foreach($this->verifySend("/accounts/$account/codes", 'ALL') as $c){
			if($code == $c['type']){
				$postdata = array();
				if(!$c['isActive']){
					$postdata['isActive'] = 'true';
				}
				if($value != $c['value']){
					$postdata['value'] = $value;
				}
				if(0 < count($postdata)){
					return $this->verifySend("/accounts/$account/codes/$c[id]", 'POST', $postdata);
				}
				return;
			}
		}
		return $this->verifySend("/accounts/$account/codes", 'POST', array('type' => $code, 'value' => $value, 'isActive' => 'true'));
	}
	
	function getCode($account, $code, $entity='accounts'){
		foreach($this->verifySend("/$entity/$account/codes?isActive=1", 'ALL') as $c){
			if($code == $c['type'] && $c['isActive']){
				return $c['value'];
			}
		}
		return '';
	}
	
	function deleteCode($account, $code){
		foreach($this->verifySend("/accounts/$account/codes?isActive=1", 'ALL') as $c){
			if($code == $c['type'] && $c['isActive']){
				return $this->verifySend("/accounts/$account/codes/$c[id]", 'DELETE');
			}
		}
	}
	
	function setNumber($account, $type, $value){
		foreach($this->verifySend("/accounts/$account/numbers", 'ALL') as $n){
			if($type == $n['type'] && $n['isActive']){
				$postdata = array();
				if($value != $n['value']){
					$postdata['value'] = $value;
				}
				if(0 < count($postdata)){
					return $this->verifySend("/accounts/$account/numbers/$n[id]", 'POST', $postdata);
				}
				return;
			}
		}
		return $this->verifySend("/accounts/$account/numbers", 'POST', array('type' => $type, 'value' => $value, 'isActive' => 'true'));
	}
	
	function getNumber($account, $type, $entity='accounts'){
		foreach($this->verifySend("/$entity/$account/numbers?isActive=1", 'ALL') as $n){
			if($type == $n['type'] && $n['isActive']){
				return $n['value'];
			}
		}
		return null;
	}
	
	function deleteNumber($account, $type){
		foreach($this->verifySend("/accounts/$account/numbers?isActive=1", 'ALL') as $n){
			if($type == $n['type'] && $n['isActive']){
				return $this->verifySend("/accounts/$account/numbers/$n[id]", 'DELETE');
			}
		}
	}
	
	function setNote($entity, $id, $type, $shortComment, $longComment, $multi=false){
		if($multi){
			return $this->verifySend("/$entity/$id/notes", 'POST', array('type' => $type, 'shortComment' => $shortComment, 'longComment' => $longComment));
		}
		foreach($this->verifySend("/$entity/$id/notes", 'ALL') as $n){
			if($type == $n['type']){
				$postdata = array();
				if($n['shortComment'] != $shortComment){
					$postdata['shortComment'] = $shortComment;
				}
				if($n['longComment'] != $longComment){
					$postdata['longComment'] = $longComment;
				}
				if(0 < count($postdata)){
					return $this->verifySend("/$entity/$id/notes/$n[id]", 'POST', $postdata);
				}
				return;
			}
		}
		return $this->verifySend("/$entity/$id/notes", 'POST', array('type' => $type, 'shortComment' => $shortComment, 'longComment' => $longComment));
	}
	
	private function login(){
		if(is_null($this->login)){
			return false;
		}
		if(1 > count($this->curl)){
			$this->curl[] = API::getCurlObject("$this->server/login", $this->timeout);
		}
		curl_setopt($this->curl[0], CURLOPT_URL, "$this->server/login");
		curl_setopt($this->curl[0], CURLOPT_USERPWD, $this->login);  
		curl_setopt($this->curl[0], CURLOPT_HEADER, TRUE);
		curl_setopt($this->curl[0], CURLOPT_POST, true);
		curl_setopt($this->curl[0], CURLOPT_POSTFIELDS, array());
		
		$data = API::praseCurlOutput($this->curl[0]);
		if(200 != $data['httpCode']){
			return false;
		}
		return $data['body']['token'];
	}	
		
	private function connect(){
		error_log('SE API Login');
		$this->token = $this->login();

		//check if exist update / insert
		if($this->storeToken){
			$t = get_option(self::$option_prefix.'token_'.$this->server);
			if(false === $t){
				add_option(self::$option_prefix.'token_'.$this->server, $this->token, '', 'no');
			} else {
				update_option(self::$option_prefix.'token_'.$this->server, $this->token);
			}
		}

		$r = $this->send(array('path' => '/sessions', 'method' => 'POST'));
		if('200' == $r['httpCode']){
			return true;
		}
		return $r['body'];
	}
	
	private function send($requests){
		if(array_key_exists('path', $requests)){
			if(1 > count($this->curl)){
				$this->curl[] = API::getCurlObject("$this->server/login", $this->timeout);
			}
			API::setUpCurl($this->curl[0], $requests, $this->server, $this->token);
			$path = is_array($requests) ? $requests['path'] :  $requests;
			$r = API::praseCurlOutput($this->curl[0], true, $path);
			$r['request'] = $requests;
			return $r;
		}
		if(1 == count($requests)){
			if(is_array($requests[0])){
				return array($this->send($requests[0]));
			}
			return array($this->send(array('path' => $requests[0])));
		}
		if(is_null($this->mh)){
			$this->mh = curl_multi_init();
		}
		while(count($this->curl) < count($requests)){
			$this->curl[] = API::getCurlObject("$this->server/login", $this->timeout);
		}
		for($i = 0; $i < count($requests); $i ++){
			API::setUpCurl($this->curl[$i], $requests[$i], $this->server, $this->token);
			curl_multi_add_handle($this->mh,$this->curl[$i]);
		}
	
		do {
			curl_multi_exec($this->mh, $running);
			curl_multi_select($this->mh);
		} while ($running > 0);
		
		$results = array();
		for($i = 0; $i < count($requests); $i ++){
			$r = API::praseCurlOutput(curl_multi_getcontent($this->curl[$i]), false);
			
			if('403' == $r['httpCode']){
				$r = verifySend($requests[$i]);
			}
			$results[] = $r;
			curl_multi_remove_handle($this->mh, $this->curl[$i]);
		}
		return $results;
	}
	
	private static function getCurlObject($path, $timeout){
		$handle = curl_init($path);
		curl_setopt($handle, CURLOPT_ENCODING,  '');
		curl_setopt($handle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); //todo change
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
		return $handle;
	}
	
	private static function setUpCurl($handle, $request, $server, $token){
		if(is_array($request)){
			$path = $request['path'];
			$method = array_key_exists('method', $request) && !is_null($request['method']) ? $request['method'] : 'GET';
			$dat = array_key_exists('data', $request) && !is_null($request['data']) ? $request['data'] : array();
		} else {
			$path = $request;
			$method = 'GET';
			$dat = array();
		}
		curl_setopt($handle, CURLOPT_URL, $server.$path);
		$headers = array("Authorization: Bearer $token");
		if('POST' == $method){
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($dat));
			$headers[] = 'Content-Type: application/json';
			$headers[] = 'Content-Length: '.strlen(json_encode($dat));
		} else {
			curl_setopt($handle, CURLOPT_POST, false);
			curl_setopt($handle, CURLOPT_POSTFIELDS, null);
		}
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	}
	
	private static function praseCurlOutput($handle, $exec=true, $path=''){
		if($exec){
			$result = curl_exec($handle);
		} else {
			$result = $handle;
		}
		$lines = explode("\r\n",$result);
		if(2 > count(explode(' ', $lines[0]))){
			return array(
				'httpCode' => '500',
				'body' => json_decode('Timed out?', true)
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
		return array(
			'httpCode' => $code,
			'body' => json_decode($sBody, true)
		);
	}
	
	function verifySend($path, $method='GET', $data=null){
		if(is_array($path) && 0 == count($path)){
			return array();
		}
		if(is_null($this->token)){
			$this->connect();
		}
		
		if(is_array($path)){
			$r = array();
			$returns = $this->send($path);
			for($i = 0; $i < count($path); $i ++){
				if('403' == $returns[$i]['httpCode']){
					$this->connect();
					$returns[$i] = $this->send($path[$i]);
				} else if ('500' == $returns[$i]['httpCode']){
					error_log("API 500: $path[$i]");
					$returns[$i] = $this->send(array('path' => $path[$i]));
				}
			}
			return $returns;
		} else if ('ALL' == $method) {
			$lFirst = '?';
			if(false !== strpos($path, '?')){
				$lFirst = '&';
			}
			$lFirst .= "all=true";
			return $this->verifySend("$path$lFirst")['body']['items'];

		} else {
			$r = $this->send(array('path' => $path, 'method' => $method, 'data' => $data));
			if('403' == $r['httpCode']){
				$this->connect();
				$r = $this->send(array('path' => $path, 'method' => $method, 'data' => $data));
			} else if ('500' == $r['httpCode']){
				error_log("API 500: $path");
				$r = $this->send(array('path' => $path, 'method' => $method, 'data' => $data));
			}
			return $r;
		}
	}
	
	function check(){
		return $this->login();
	}
}
