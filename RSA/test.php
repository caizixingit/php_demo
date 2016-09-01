<?php
/** 
 * 公钥加密 
 * 
 * @param string 明文 
 * @param string 证书文件（.crt） 
 * @return string 密文（base64编码） 
 */  
function publickey_encodeing($sourcestr, $fileName)  
{  
	$key_content = file_get_contents($fileName);  
	$pubkeyid    = openssl_get_publickey($key_content);  

	if (openssl_public_encrypt($sourcestr, $crypttext, $pubkeyid))  
	{  
		return base64_encode("".$crypttext);  
	}  
}  
/** 
 * 私钥解密 
 * 
 * @param string 密文（二进制格式且base64编码） 
 * @param string 密钥文件（.pem / .key） 
 * @param string 密文是否来源于JS的RSA加密 
 * @return string 明文 
 */  
function privatekey_decodeing($crypttext, $fileName, $fromjs = FALSE)  
{  
	$key_content = file_get_contents($fileName);  
	$prikeyid    = openssl_get_privatekey($key_content);  
	$crypttext   = base64_decode($crypttext);  
	$padding = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;  
	if (openssl_private_decrypt($crypttext, $sourcestr, $prikeyid, $padding))  
	{  
		return $fromjs ? rtrim(strrev($sourcestr), "/0") : "".$sourcestr;  
	}  
	return ;  
}  
//JS->PHP 测试  
//$txt_en = $_POST['password'];  
$txt_en = 
"841d58879a8f19a8f5ffea79ebb418a4b052174355a9ded373ae470809a1a88ba28679779a6702629fcd2c7f1493473eff4b990a7fb4578d0e788f95c6e71d12ae370a51f2319fa95b8e810868cd93ee064f097609afa814ade1871c58591cffc24bc43a8d76581ed98c495d1e4a0e24329acf28f7acbc180b88ff3b798b51ec";
$txt_en = base64_encode(pack("H*", $txt_en));  
$file = 'rsa_private_key.pem';  
$txt_de = privatekey_decodeing($txt_en, $file, TRUE);  
var_dump($txt_de);  
//PHP->PHP 测试  
$data = "汉字:1a2b3c";  
$file2 = 'rsa_private_key.pem';  
$file1 = 'rsa_public_key.pem';  
$a = publickey_encodeing($data, $file1);  
$b = privatekey_decodeing($a, $file2);  
var_dump($b);  
