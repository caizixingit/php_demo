<?php
class Security {
	public static function encrypt($input, $key) {
		$size = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_ECB);
		$input = Security::pkcs5_pad($input, $size);
		$td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		mcrypt_generic_init($td, $key, $iv);
		$data = mcrypt_generic($td, $input);
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);
		$data = base64_encode($data);
		return $data;
	}

	private static function pkcs5_pad ($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	public static function decrypt($sStr, $sKey) {
		$decrypted= mcrypt_decrypt(
				MCRYPT_3DES,
				$sKey,
				base64_decode($sStr),
				MCRYPT_MODE_ECB
				);

		$dec_s = strlen($decrypted);
		$padding = ord($decrypted[$dec_s-1]);
		$decrypted = substr($decrypted, 0, -$padding);
		return $decrypted;
	}	
}


echo MCRYPT_3DES;
echo "\n";
$key = "8a8b791220316ea4af9c98cd";
$data = "fasdgasdfgasdfgasdf";

$value = Security::encrypt($data , $key );
echo $value.'<br/>';
echo Security::decrypt($value, $key );
