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
		if($this->doHas($key))
			return $this->_vars[$key];
		return false;
	}

	public function doSet($key, $value){
		$this->_vars[$key] = $value;
	}
}
?>
