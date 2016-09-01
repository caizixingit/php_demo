<?php
class Test 
{
	public function resolve($str)
	{
		$result = [];
		preg_match('/\/\*\*([\w\W]*)\*\//', $str, $match);
		if(empty($match[1]))
			return false;

		$content = trim($match[1]);
		$list = explode("\n", $content);
		foreach($list as $line)
		{
			$line = trim($line);
			if(empty($line))
				continue;
			preg_match('/\@?([a-zA-Z]*) ([a-zA-Z]*) ?(\$?[a-zA-Z]*)?/', $line, $match);
			if(empty($match[1]))
				continue;
			$type = $match[1];
			switch($type)
			{
				case 'read';
					$this->handleFile($match, $result);
					break;
				case 'param':
					$this->handleParam($match, $result);
					break;
				case 'return':
					$this->handleReturn($match, $result);
					break;
				case 'throws':
					$this->handleThrows($match, $result);
					break;
				default:
					break;
			}
		}
		return $result;
	}
	private function handleParam($match, &$result)
	{
		$type = $match[2];
		$param = $match[3];
		if(empty($type) || empty($param))
			return false;
		$tmp['param'] = $param;
		$tmp['type'] = $type;
		$result['params'][] = $tmp;
	}

	private function handleReturn($match, &$result)
	{
		$return = $match[2];
		if(empty($return))
			return false;
		$result['return'] = $return;
	}

	private function handleThrows($match, &$result)
	{
		$throws = $match[2];
		if(empty($throws))
			return false;
		$result['throws'] = $throws;
	}

	private function handleFile($match, &$result)
	{
		$file = $match[3];
		if(empty($file))
			return false;
		$result['file'] = $file;

	}
}
$str = "
	/**
	 *	read from file 
	 *	@param string \$str
	 *	@param int \$int
	 *  @return void
	 *	@throws Exception
	 */
";
$obj = new Test();
var_dump($obj->resolve($str));

