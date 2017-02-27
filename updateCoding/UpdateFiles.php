<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 17/2/27
 * Time: 下午4:30
 */

abstract class UpdateFiles
{
	protected $config;
	protected $fileList;
	protected $commandList;

	public function __construct($config)
	{
		$this->config = $config;
		$this->fileList = $this->getFileList();
	}

	public function getFileList()
	{
		$fileList = [];
		$cpFile = $this->config['file'];
		$content = file_get_contents($cpFile);
		$list = explode("\n", $content);
		foreach($list as $file)
		{
			$file = substr($file, strrpos($file, ' '));
			if(empty($file) || strlen($file) <= 1)
			{
				continue;
			}

			$fileList[] = trim($file);
		}
		return $fileList;
	}

	abstract public function getCommand();

	abstract function doCommand();

	abstract function makeCommand();
}