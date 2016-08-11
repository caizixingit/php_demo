<?
class FTP
{
	function __construct($config)
	{
		$server = $config['server'];
		$user = $config['user'];
		$pass = $config['pass'];
		$this->ftp = ftp_connect($server);
		$this->login = ftp_login($this->ftp, $user, $pass);
	}

	public function upload($localPath, $remotePath, $mod )
	{
		if(!$this->ftp  || !$this->login)	
		{
			return false;
		}

		$ret = ftp_put($this->ftp, $remotePath, $localPath, $mod, 0);
		if($ret)
			return $ret;
		else
			return false;
	}
}
$config = array(
	'server' => "119.167.209.202",
	'user' => 'caizixin',
	'pass' => 'caizixin.123456',
);
$a = new FTP($config);
$ret = $a->upload("/home/work/1234", "1234.txt", FTP_BINARY);
var_dump($ret);

?>
