<?php
/**
 * 永続化キャッシュもでるを考え中
 * 単純なハッシュテーブル
 *
 */

/** 
 * XStore Interface
 */
interface  XStoreInterface 
{
	public function doHas($key);
	public function doGet($key);
	public function doSet($key, $value);
}

require_once 'base/exception.class.php';
class XStoreException extends XBaseException { }


class XStore implements XStoreInterface
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

	public function has($key){
		return $this->doHas( $key );
	}

	public function get($key){
		if(is_array($key)){
			$ret = array();
			foreach($key	as $k) {
				$ret[$k] = $this->get($k);
			}
			return $ret;
		}
		return $this->doGet( $key );
	}

	public function set($key, $value){
		$this->doSet($key, $value);
	}
	public function delete($key){
		$this->doDelete($key);
	}

	/**
	 * It Will over-write
	 */
	public function doHas($key){
		return false;
	}

	public function doGet($key){
		return false;
	}

	public function doSet($key, $value){
		return false;
	}
	public function doDelete($key){
		return false;
	}
}
?>
