<?
$content = file_get_contents('url.txt');
$urlList = explode("\n", $content);
$count = 0;
$host = 'http://112.124.58.194/';
while(1)
{

	foreach($urlList as $uri)
	{
		if(empty($uri))
		{
			continue;
		}
		$url = $host. $uri;
		var_dump($url);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		$ret = curl_exec($ch);
		if(strpos($ret, 'HTTP/1.1 200 OK') !== 0)
		{
			echo $url. " get fail \n";
		}
	}
	die;

}   
?>
