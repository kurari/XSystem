<?php

class XStore 
{
	public static function factory($type, $default, $option = array())
	{
		$file = 'store/store.'.strtolower($type).'.class.php';
		$class = 'XStore'.ucfirst(strtolower($type));
		require_once $file;
		$o = new $class( $default, $option );
		return $o;
	}

	public function setIf( $key, $value = false) {

		if(is_array($key)) {
		 	foreach($key as $k=>$v) {
				$this->setIf($k, $v);
			}
			return true;
		}
		

		if( !$this->has($key) ) $this->set($key, $value);
	}

	public function __get($key){
		return $this->get($key);
	}

}
?>
