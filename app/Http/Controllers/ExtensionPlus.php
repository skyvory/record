<?php

namespace App\Http\Controllers;
use Log;

trait ExtensionPlus
{
	public function saveRemoteImage($url, $target_directory) {
		$is_success = false;
		for ($iteration=0; $iteration < 3; $iteration++) { 
			$is_success = $this->processSaveRemoteImage($url, $target_directory);
			if($is_success) {
				break;
			}
		}
		if($is_success)
			return true;
		else {
			Log::error('Fail to save image after ' . $iteration . ' attempts.');
			return false;
		}
	}

	protected function requestRemoteData($url) {
		$header = null;
		$body = null;
		try{
			$agent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
			$ch = curl_init();
			if(false == $ch) {
				throw new \Exception('failed to initialize');
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
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

			// header and body using received header size
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$traditional_header = substr($raw, 0, $header_size);
			$traditional_body = substr($raw, $header_size);
			// anticipating extra header from proxy using explode instead of header size
			list($header, $body) = explode("\r\n\r\n", $raw, 2);

			curl_close ($ch);
		}
		catch(\Exception $e) {
			Log::error(sprintf(
				'curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()));
			// trigger_error(sprintf(
			// 	'curl failed with error #%d: %s',
			// 	$e->getCode(), $e->getMessage()),
			// E_USER_ERROR);
			return false;
		}

		return array('header' => $header, 'body' => $body);
	}

	public function processSaveRemoteImage($url, $target_directory) {
		$image_data = $this->requestRemoteData($url);
		if($image_data == false) {
			return false;
		}
		$header = $image_data['header'];
		$body = $image_data['body'];

		$source_image_size = $this->parseHeaderContentLength($header);

		$is_image_write_required = false;
		// Check if same file exist and archive if exist but with different size
		if(file_exists($target_directory)) {
			if($source_image_size == filesize($target_directory)) {
				// Exact same file no need to write file
				$is_image_write_required = false;
			}
			else {
				// Rename old file for archive
				$existing_image_date = exif_imagetype($target_directory) != false ? date('YmdHis', filemtime($target_directory)) : 'ArchivedOn' . date('YmdHiS');
				$path_parts = pathinfo($target_directory);
				$archive_target_directory = $path_parts['dirname'] . '\\' . $path_parts['filename'] . '_' . $existing_image_date . '.' . $path_parts['extension'];
				rename($target_directory, $archive_target_directory);

				$is_image_write_required = true;
			}
		}
		else {
			$is_image_write_required = true;
		}

		if($is_image_write_required) {
			$fp = fopen($target_directory, 'w+');
			$written_byte = fwrite($fp, $body);
			fclose($fp);

			// Check if saved image has the same size as the source
			$save_success = false;
			if($source_image_size == filesize($target_directory) && $source_image_size == $written_byte) {
				// Check if saved image is valid as an image
				if(exif_imagetype($target_directory) != false) {
					$save_success = true;
				}
			}

			return $save_success;
		}

		return true;

	}

	protected function parseHeaderContentLength($header) {
		$content_length = -1;
		preg_match('/Content-Length: (\d+)/', $header, $match);
		if(isset($match[1])) {
			$content_length = (int)$match[1];
		}
		return $content_length;
	}

	public function saveRemoteImage_LEGACY($url, $target_directory) {
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
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
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