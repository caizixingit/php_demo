<?php
require_once('CPFiles.php');
require_once('RsyncFiles.php');
require_once('UpdateFiles.php');

$config = [
	'app' => [
		'from' => '/data/work/gpb/gpb_server.trunk',
		'to' => '/data/work/gpb/gpb_server',
		'file' => 'app_file.txt',
		'machines' => [
			'10.174.92.249',
			'10.25.92.228'
		]
	],
	'backend' => [
		'from' => '/data/work/gpb/gpb_backend_server.trunk',
		'to' => '/data/work/gpb/gpb_backend_server',
		'file' => 'backend_file.txt',
	],
	'pc' => [
		'from' => '/data/work/gpb/gpb_pc.trunk',
		'to' => '/data/work/gpb/gpb_pc',
		'file' => 'pc_file.txt',
	],

];

class UpdateCoding
{
	private $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function doUpdateCoding()
	{
		$obj = new CPFiles($this->config);
		$obj->doCommand();

		if(isset($this->config['machines']))
		{
			foreach($this->config['machines'] as $machine)
			{
				$config = $this->config;
				$config['machine'] = $machine;
				$obj = new RsyncFiles($config);
				$obj->doCommand();
			}
		}
	}
}
$params = getopt("t:");
$type = $params['t'];
if(!isset($config[$type]))
{
	echo 'type error';
	exit;
}

$obj = new UpdateCoding($config[$params['t']]);
$obj->doUpdateCoding();



