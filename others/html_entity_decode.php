<?php
$str = "Bill &amp; &#039;Steve&#039;";
echo html_entity_decode($str, ENT_COMPAT); // 只转换双引号
echo "\n";
echo html_entity_decode($str, ENT_QUOTES); // 转换双引号和单引号
echo "\n";
echo html_entity_decode($str, ENT_NOQUOTES); // 不转换任何引号
echo "\n\n";


$str = "Bill & 'Steve'";
$str = "Bill &amp; &#039;Steve&#039;";
echo htmlspecialchars_decode($str, ENT_COMPAT); // 只转换双引号
echo "\n";
echo htmlspecialchars_decode($str, ENT_QUOTES); // 转换双引号和单引号
echo "\n";
echo htmlspecialchars_decode($str, ENT_NOQUOTES); // 不转换任何引号

echo "\n\n";

$str = "Bill & 'Steve'";
echo htmlspecialchars($str, ENT_COMPAT); // 只转换双引号
echo "\n";
echo htmlspecialchars($str, ENT_QUOTES); // 转换双引号和单引号
echo "\n";
echo htmlspecialchars($str, ENT_NOQUOTES); // 不转换任何引号
