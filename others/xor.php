<?php

function generateSalt($cost = 13)
{
	$cost = (int) $cost;
	if ($cost < 4 || $cost > 31) {
		throw new InvalidParamException('Cost must be between 4 and 31.');
	}

	// Get a 20-byte random string
	//$rand = $this->generateRandomKey(20);
	$rand = 'abcdefghijklmnopqrst';
	// Form the prefix that specifies Blowfish (bcrypt) algorithm and cost parameter.
	$salt = sprintf("$2y$%02d$", $cost);
	var_dump($salt);
	// Append the random salt data in the required base64 format.
	$salt .= str_replace('+', '.', substr(base64_encode($rand), 0, 22));

var_dump(strlen($salt));
	return $salt;
}

$salt = generateSalt(13);
$type = PASSWORD_BCRYPT;
$hash = password_hash('caizixin', $type);

//var_dump(password_verify('caizixin', $hash));

$a = 'hello word';
var_dump(pack('a*', $a));

