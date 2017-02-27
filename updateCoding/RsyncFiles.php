<?php
/**
 * Created by PhpStorm.
 * User: caizixin
 * Date: 17/2/27
 * Time: 下午4:28
 */

require_once('UpdateFiles.php');

class RsyncFiles extends UpdateFiles
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
		$machine = $this->config['machine'];
		$type = 'rsync';

		$commandList = [];
		foreach($this->fileList as $file)
		{
			$command = "sudo {$type} {$from}/{$file} work@{$machine}:{$to}/{$file}";
			$commandList[] = $command;
		}

		return $commandList;
	}

}
