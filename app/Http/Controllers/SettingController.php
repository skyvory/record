<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use App\Setting;

class SettingController extends Controller
{
    public function storeVndbAuthHash(Request $request) {
    	$this->validate($request, [
    		'vndb_username' => 'required|string',
    		'vndb_password' => 'required|string'
  		]);
	$user = JWTAuth::parseToken()->authenticate();
    	$base64_username = $request->input('vndb_username');
    	$base64_password = $request->input('vndb_password');

    	$username = base64_decode($base64_username);
    	$password = base64_decode($base64_password);

    	$username_encryption_result = $this->encryptCredentialString($username);
    	$password_encryption_result = $this->encryptCredentialString($password);

    	// return $username_encryption_result['ciphertext'];

    	// Write to database
    	$setting = Setting::firstOrCreate(['user_id' => $user->id]);
    	$setting->vndb_username_hash = $username_encryption_result['ciphertext'];
    	$setting->vndb_password_hash = $password_encryption_result['ciphertext'];
	$exec = $setting->save();

	// return $this->decryptCredentialString($username_encryption_result['hash_key'], $username_encryption_result['ciphertext']);

	if($exec) {
		$hash_keys = [
			'username' => $username_encryption_result['hash_key'],
			'password' => $password_encryption_result['hash_key']
		];
		return response()->json(['data' => $hash_keys]);
	}
	return response()->json(['status' => 'error']);
    }

    public function retrieveVndbAuth($username_key_hash, $password_key_hash) {
	$user = JWTAuth::parseToken()->authenticate();
	$setting = Setting::where('user_id', $user->id)->first();
	$username = $this->decryptCredentialString($username_key_hash, $setting->vndb_username_hash);
	$password = $this->decryptCredentialString($password_key_hash, $setting->vndb_password_hash);
	
	$credential = ['username' => $username, 'password' => $password];
	return $credential;
    }

    private function decryptCredentialString($pack_hash, $ciphertext_base64) {
    	$key = pack('H*', $pack_hash);
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$ciphertext_dec = base64_decode($ciphertext_base64);
	$iv_dec = substr($ciphertext_dec, 0, $iv_size);
	$ciphertext_dec = substr($ciphertext_dec, $iv_size);
	$decrypted_text = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
	return $decrypted_text;
    }

    private function encryptCredentialString($credential_string) {
    	$body = $credential_string;
    	$pack_hash = $this->generateRandomHash(base64_encode($credential_string));
    	$key = pack('H*', $pack_hash);
    	$key_size = strlen($key);

	// create a random IV to use with CBC encoding
    	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $body, MCRYPT_MODE_CBC, $iv);
	// prepend the IV for it to be  available for decryption
	$ciphertext = $iv . $ciphertext;
	$ciphertext_base64 = base64_encode($ciphertext);

	$hash_result = array('hash_key' => $pack_hash, 'ciphertext' => $ciphertext_base64);

    	return $hash_result;
    }

    private function generateRandomHash($prehash_string) {
	$prehash = '6f4097b653b5a46bf3e704c292f56c51d0debd834cbb87ce3c9e4d15b79c0f4bc6f99c8b294db3c8aae5aa7bdf1a0435f2ff5aceca5377f8e9c0350e53778206';
	$microtime_random = md5(microtime().rand());
	$presalt = $prehash . $microtime_random . $prehash_string;
	$whirlpool_salt = hash('whirlpool', $presalt);

	$microtime_random = sha1(microtime().rand());
	$presalt = $microtime_random. $prehash_string . $prehash;
	$sha_salt = hash('sha512', $presalt);

	$microtime_random = crc32(microtime() . rand());
	$presalt = $prehash_string . $prehash . $microtime_random;
	$ripemd_salt = hash('ripemd320', $presalt);

	$whirlpool = str_shuffle($whirlpool_salt);
	$sha = str_shuffle($sha_salt);
	$ripemd = str_shuffle($ripemd_salt);
	$triage = str_shuffle($whirlpool . $sha . $ripemd);

	$pack_hash = hash('sha256', $triage);

	return $pack_hash;
    }
}
