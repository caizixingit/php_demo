<?php

try
{ 
	throw new Exception('hh');
}
catch(Exception $e)
{
	echo 'cache';
}

echo 333;
