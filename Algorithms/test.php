<?php 
error_reporting(E_ALL); 
$a = 'I am test.'; 
$b = & $a; 

$b = 'I will change?';                                                           

echo $a ."\n"; 
echo $b ."\n";
