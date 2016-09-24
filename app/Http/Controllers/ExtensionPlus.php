<?php

namespace App\Http\Controllers;

trait ExtensionPlus
{
	public function saveRemoteImage($url, $target_directory) {
		try{
			$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
			$ch = curl_init();
			if(false == $ch) {
				throw new \Exception('failed to initialize');
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// curl_setopt($ch, CURLOPT_FILE, $fp);  
			// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			// curl_setopt($ch, CURLOPT_TIMEOUT, 1000);  
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			$raw = curl_exec($ch);
			if($raw == false) {
				throw new \Exception(curl_error($ch), curl_errno($ch));
			}
			curl_close ($ch);

			$fp = fopen($target_directory, 'x');
			fwrite($fp, $raw);
			fclose($fp);
		}
		catch(\Exception $e) {
			trigger_error(sprintf(
				'curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()),
			E_USER_ERROR);
		}
		return true;
	}
}