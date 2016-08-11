<?php
class AException extends Exception
{
	
}

class BException extends Exception
{
	
}
$name = 'caizixin';
try
{
if(strlen($name) > 2)
{
	throw new AException('sdf');
}
}
catch(Exception $e)
{
	echo $e->getMessage();
}
