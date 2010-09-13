<?php
class XTestRunner
{
	/**
	 * test cases
	 * @var array
	 */
	private $_tesstCases = array();

	function addCase( $target )
	{
		$this->_testCases[] = $target;
	}
	function run( )
	{
		foreach( $this->_testCases as $target )
		{
			$pos = strrpos(basename($target), '.');
			$class = ucfirst(substr(basename($target), 0, $pos)).'Test';
			require_once $target;

			$start = date('Y-m-d G-i-s');
			$TestCase = new $class( );
			$TestCase->init( );
			$result = $TestCase->run( );
			$end = date('Y-m-d G-i-s');

			$name = get_class($TestCase);
			$count = 0;
			$error_count = 0;
			$msg = "";
			foreach($result as $k=>$r){
				$count++;
				if(XUtil::arrayGetOr($r,'status','NG') != "OK") {
					$error_count++;
					$msg = $k.":".XUtil::arrayGetOr($r,'status','NG')."\n".implode("\n", XUtil::arrayGetOr($r,'errors',array()));
				}
			}	

			echo "\n";
			echo "Start->$name start=$start\n";
			echo "$name test=$count errors=$error_count\n";
			echo !empty($msg) ? "$msg\n": "";
			echo "End->$name end=$end\n";
		}
	}
}
?>
