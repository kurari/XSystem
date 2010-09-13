<?php

class XStoreArray extends XStore {

	private $_vars = array();

	function __construct( $default, $option ){
		$this->_vars = $default;
	}

	function has($key){
		return isset($this->_vars[$key]) ? true: false;
	}

	function get($key){
		if(is_array($key)){
			$ret = array();
			foreach($key	as $k) {
				$ret[$k] = $this->get($k);
			}
			return $ret;
		}
		return $this->_vars[$key];
	}

	function set($key, $value){
		$this->_vars[$key] = $value;
	}


}
?>
