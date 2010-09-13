<?php
/**
 * XStore
 * ------------------------
 * Type : array
 * ------------------------
 * 
 *
 */
class XStoreArray extends XStore implements XStoreInterface
{

	private $_vars = array();

	function __construct( $default, $option ){
		$this->_vars = $default;
	}

	public function doHas($key){
		return isset($this->_vars[$key]) ? true: false;
	}

	public function doGet($key){
		return $this->_vars[$key];
	}

	public function doSet($key, $value){
		$this->_vars[$key] = $value;
	}
}
?>
