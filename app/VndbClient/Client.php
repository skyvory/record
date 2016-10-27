<?php

namespace App\VndbClient;

class Client
{
	private $fp;
	
	public function __construct()
	{
	}
	
	public function isConnected()
	{
		if ($this->fp) {
			return true;
		}
		return false;
	}
	
	public function connect()
	{
		$context_options = [
			'ssl' => [
				'verify_peer' => true,
				'local_cert' => 'isrgrootx1.pem',
				'verify_peer_name' => true,
				'allow_self_signed' => true,
			]
		];
		$context = stream_context_create();
		$target_protocol = 'tls';
		if($target_protocol == 'tcp') {
			$this->fp = stream_socket_client("tcp://api.vndb.org:19534", $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $context);
		}
		else if($target_protocol == 'tls') {
			// var_dump(getenv('OPENSSL_CONF'));
			// $certificateData = array(
			//     "countryName" => "UK",
			//     "stateOrProvinceName" => "Texas",
			//     "localityName" => "Houston",
			//     "organizationName" => "example.com",
			//     "organizationalUnitName" => "Development",
			//     "commonName" => "Skyvory",
			//     "emailAddress" => "re@outlook.sg"
			// );

			// $configArgs = array(
			// 	'config' => 'C:\xampp\php\extras\openssl\openssl.cnf',
			// );

			// $privateKey = openssl_pkey_new($configArgs);
			// while($message = openssl_error_string()){
			//     echo $message.'<br />'.PHP_EOL;
			// }
			// var_dump(openssl_error_string());
			// $certificate = openssl_csr_new($certificateData, $privateKey);
			// $certificate = openssl_csr_sign($certificate, null, $privateKey, 365);


			// $pem_passphrase = 'abracadabra';
			// $pem = array();
			// openssl_x509_export($certificate, $pem[0]);
			// openssl_pkey_export($privateKey, $pem[1], $pem_passphrase);
			// $pem = implode($pem);

			// $pemfile = 'server.pem';
			// file_put_contents($pemfile, $pem);







			// stream_context_set_option($context, 'ssl', 'local_cert', 'isrgrootx1.pem');
			// stream_context_set_option($context, 'ssl', 'local_cert', 'isrgrootx1.pem');
			// stream_context_set_option($context, 'ssl', 'verify_peer', true);
			stream_context_set_option($context, 'ssl', 'verify_peer_name', true);
			stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
			// stream_context_set_option($context, 'ssl', 'passphrase', $pem_passphrase);

			stream_context_set_option($context, 'ssl', 'verify_peer', false);
			// stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
			// stream_context_set_option($context, 'ssl', 'allow_self_signed', false);
			$this->fp = stream_socket_client("tls://api.vndb.org:19535", $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $context);
		}
		
		if (!$this->fp) {
			echo "ERROR: $errstr ($errno)<br />\n";
		}
	}
	
	public function login($username, $password)
	{
		$data = array(
			'protocol' => 1,
			'client' => 'skyvory',
			'clientver' => 0.2,
			'username' => $username,
			'password' => $password
		);
		$response = $this->sendCommand('login', $data);
		if ($response->getType() == 'ok') {
			//echo "Login OK\n";
		} else {
			//echo "Login failed..\n";
		}
	}

	public function sendCommand($command, $data = null)
	{
		$packet = $command;
		if ($data) {
			$packet .= ' ' . json_encode($data);
		}
		//echo "SENDING: [$packet]";
		fwrite($this->fp, $packet . chr(0x04));
		
		$res = $this->getResponse();
		$response = new Response();
		
		if ($res=='ok') {
			$response->setType('ok');
		} else {
			$p = strpos($res, '{');
			if ($p>0) {
				$type = substr($res, 0, $p - 1);
				$response->setType($type);

				$json = substr($res, $p);
				$data = json_decode($json, true);
				$response->setData($data);
			}
		}
		return $response;
	}

	public function getResponse()
	{
		//echo "Waiting for response...\n";
		$buffer = '';
		while (!feof($this->fp)) {
			$c = fgets($this->fp, 2);
			if (ord($c)==0x04) {
				//echo "Received: [$buffer]\n\n";
				return $buffer;
			} else {
				$buffer .= $c;
			}
		}
		return null;
	}
	
	public function getVisualNovelDataById($id)
	{
		$res = $this->sendCommand('get vn basic,anime,details,relations,stats (id = ' . (int)$id . ')');
		return $res;
	}

	public function getReleaseDataById($id)
	{
		$res = $this->sendCommand('get release basic,details,vn,producers (id = ' . (int)$id . ')');
		return $res;
	}
	
	public function getProducerDataById($id)
	{
		$res = $this->sendCommand('get producer basic,details,relations (id = ' . (int)$id . ')');
		return $res;
	}
	
	public function getCharacterDataById($id)
	{
		$res = $this->sendCommand('get character basic,details,meas,traits (id = ' . (int)$id . ')');
		return $res;
	}
}
