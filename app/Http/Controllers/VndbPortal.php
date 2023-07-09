<?php

namespace App\Http\Controllers;
use App\VndbClient\Client;

trait VndbPortal
{
	private function requestToVndb($auth, $command) {
		
		$context = stream_context_create();
		stream_context_set_option($context, 'ssl', 'verify_peer_name', true);
		stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
		stream_context_set_option($context, 'ssl', 'verify_peer', false);
		// stream_context_set_option($context, 'ssl', 'local_cert', 'C:\xampp\htdocs\record\public\isrgrootx1.pem');
		$socket = stream_socket_client('tls://api.vndb.org:19535', $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);

		// Login start
		$data = array(
			'protocol' => 1,
			'client' => 'skyvory',
			'clientver' => 0.3,
			'username' => $auth['username'],
			'password' => $auth['password']
		);

		$packet = 'login';
		if($data) {
			$packet .= '' . json_encode($data);
		}
		fwrite($socket, $packet . chr(0x04));

		$buffer = '';
		while (!feof($socket)) {
			$c = fgets($socket, 2);
			if(ord($c)==0x04) {
				// return buffer
				break;
			}
			else {
				$buffer .= $c;
			}
		}

		$p = strpos($buffer, '{');
		if($p>0) {
			$type = substr($buffer, 0, $p -1);
			$json = substr($buffer, $p);
			$data = json_decode($json, true);
		}

		// Login end
		// Main request start

		$packet = $command;
		fwrite($socket, $packet . chr(0x04));

		$buffer = '';
		while (!feof($socket)) {
			$c = fgets($socket, 2);
			if(ord($c)==0x04) {
				// return buffer
				break;
			}
			else {
				$buffer .= $c;
			}
		}

		$p = strpos($buffer, '{');
		if($p>0) {
			$type = substr($buffer, 0, $p -1);
			$json = substr($buffer, $p);
			$data = json_decode($json, true);
		}

		return $data;
	}

	protected function searchVn($auth, $params) {
		if(sizeof($params)) {
			if(!isset($params['search_query'])) {
				return 0;
			}
			$command = 'get vn basic,details,anime,relations,tags,stats (search ~ "' . $params['search_query'] . '")';
			$result_object = $this->requestToVndb($auth, $command);
			$result_json = json_decode(json_encode($result_object), true);
			return $result_json;
		}
	}

	protected function searchVn2($vndb_token, $params) {
		if(sizeof($params)) {
			if(!isset($params['search_query'])) {
				return 0;
			}
			$result_object = $this->requestToVndb2($vndb_token, $params['search_query']);
			$result_json = json_decode($result_object, true);
			$result_json = $result_json['results'];
			return $result_json;
		}
	}

	protected function requestToVndb2($vndb_token, $command) {
		$postvars = array(
			"filters" => ["search", "=", $command],
			"fields" => "id, title, alttitle, titles.title, titles.latin, titles.official, titles.main, aliases, released, platforms, image.url, description",
			"results"=> 50,
			"page"=> 1,
			"count"=> true
		);

		$authorization = "Authorization: Token " . $vndb_token;

		$ch = curl_init();
		$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
		$target_url = 'https://api.vndb.org/kana/vn';

		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		// curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		// curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postvars));
		// curl_setopt($ch, CURLOPT_POSTFIELDS, '"filters": ["id", "=", "v17"]');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // response as a string
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			print curl_error($ch);
		}

		// $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// curl_close($ch);

		return $result;

	}
}
