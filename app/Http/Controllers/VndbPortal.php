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
}
