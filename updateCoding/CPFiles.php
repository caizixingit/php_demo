<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 17/2/27
 * Time: 下午4:27
 */
require_once('UpdateFiles.php');

class CPFiles extends UpdateFiles
{

	public function getCommand()
	{
		$this->commandList = $this->makeCommand();

		return implode("\n", $this->commandList). "\n";
	}

	public function doCommand()
	{
		$this->commandList = $this->makeCommand();
		foreach($this->commandList as $command)
		{
			var_dump($command);
			//shell_exec($command);
		}
	}

	public function makeCommand()
	{
		$from = $this->config['from'];
		$to = $this->config['to'];
		$type = 'cp';

		$commandList = [];
		foreach($this->fileList as $file)
		{
			$command = "sudo {$type} {$from}/{$file} {$to}/{$file}";
			$commandList[] = $command;
		}

		return $commandList;
	}

}
