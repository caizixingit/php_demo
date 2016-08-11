<?
$content = file_get_contents("minganci.txt");
$arr = explode("\n", $content);
foreach($arr as &$one)
    $one = trim($one);
file_put_contents("Minganci.php","<?\n$". "minganciArr=". var_export($arr, 1). "\n?>");
?>
