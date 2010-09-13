<?php
/**
 * Unit Test Tool
 * ----
 * @author Hajime
 */

/**
 * Test Case
 */
class XTestCase {

	public $results     = array(); /* results */
	public $errors      = "";      /* for keep errors */
	public $error_count = 0;       /* count errors */

	function __construct( )
	{
		foreach( get_class_methods( $this ) as $method ) {
			if( substr($method, 0, 4) == "test" ) {
				$this->tests[] = $method;
			}
		}
	}

	function init( )
	{

	}

	function assertEquala( $ok, $value, $message = 'failed')
	{
		if( $ok != $value ){
			$this->error_count++;
			$this->errors[] = $message;
		}
	}
	function assertEquals( $ok, $value, $message = 'failed')
	{
		if( $ok != $value ){
			$this->error_count++;
			$this->errors[] = $message." $ok is not equal $value";
		}
	}
	function assertTrue( $bool, $message = "failed" )
	{
		if( $bool !== true  ) {
			$this->error_count++;
			$this->errors[] = $message;
		}
	}
	function assertFalse( $bool, $message = "failed" )
	{
		if( $bool !== false  ) {
			$this->error_count++;
			$this->errors[] = $message;
		}
	}

	function assertNotNull( $value, $message = "Not Null Failed")
	{
		if($value == null){
			$this->error_count++;
			$this->errors[] = $message;
		}
	}
	function assertNull( $value, $message = " Null Failed")
	{
		if($value !== null){
			$this->error_count++;
			$this->errors[] = $message;
		}
	}


	function run( )
	{
		foreach($this->tests as $test){
			$this->errors      = array();
			$this->error_count = 0;
			try {
				call_user_func(array($this,$test));
				if($this->error_count != 0){
					$this->results[$test]['error_count'] = $this->error_count;
					$this->results[$test]['errors'] = $this->errors;
					$this->results[$test]['status'] = 'F';
				}else{
					$this->results[$test]['status'] = 'OK';
				}
			}catch(Exception $e){
				$this->results[$test]['errors'][] = $e->getMessage().' at '.$e->getFile().' on '.$e->getLine();
			}
		}
		return $this->results;
	}
}
?>
