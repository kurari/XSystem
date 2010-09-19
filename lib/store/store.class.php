<?php
/**
 * 永続化キャッシュもでるを考え中
 * 単純なハッシュテーブル
 *
 */
require_once 'base/exception.class.php';
class XStoreException extends XBaseException { }


class XStore 
{
	/**
	 * values
	 */
	private $values;

	public static function factory($type, $default = array(), $option = array())
	{
		/*
		$file = 'store/store.'.strtolower($type).'.class.php';
		$class = 'XStore'.ucfirst(strtolower($type));
		require_once $file;
		$o = new $class( $default, $option );
		 */
		$o = new XStore();
		$o->set($default);
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

	public function setSection( $name ) 
	{
		$this->section = $name;
	}

	public function __get($key){
		$key = preg_replace('/([A-Z][^A-Z]+)/e', 'strtolower(".\1")', $key);
		return $this->get($key);
	}

	/**
	 * Set Value
	 *
	 * @param string key
	 * @param mixed value
	 */
	public function set( $key, $value = false )
	{
		if( is_array($key) ) {
			$ret = array();
			foreach($key as $k=>$v) $this->set($k, $v);
			return $ret;
		}
		if( !empty($this->section) ) $key = "$this->section.$key";
		if( false === strpos($key,'.') ){
			return $this->values[$key] = $value;
		}
		$arr = explode('.',$key);
		$fin = array_pop($arr);
		$ref =& $this->values;
		while( $k = array_shift($arr) ) $ref =& $ref[$k];
		$ref[$fin] = $value;
	}

	/**
	 * Has Value
	 *
	 * @param string Key
	 * @return bool
	 */
	public function has( $key ){
		if( !empty($this->section) ) $key = "$this->section.$key";
		if( false === strpos($key,'.') ){
			return isset($this->values[$key]);
		}else{
			$arr = explode('.',$key);
			$fin = array_pop($arr);
			$ref = $this->values;
			while( $k = array_shift($arr) ) $ref = $ref[$k];
			return isset($ref[$fin]);
		}
	}

	/**
	 * Get Values
	 *
	 * you can put more then one args to get multiplly
	 *
	 * @param mixed key one string or array
	 * @return mixed
	 */
	public function get( $key = false) 
	{
		// if key not defined output all
		if(!empty($this->values) && empty($key)) $key = array_keys($this->values);

		// if args are more then 1 multiple get
		elseif( func_num_args() > 1 ) $key = func_get_args();

		// for multiple get
		if( is_array($key) ) {
			$ret = array();
			foreach($key as $k) $ret[$k] = $this->get($k);
			return $ret;
		}

		if( !empty($this->section) ) $key = "$this->section.$key";

		if( false === strpos($key,'.') ){
			if(!isset($this->values[$key])) return false;
			$value = $this->values[$key];
		}else{
			$arr = explode('.',$key);
			$fin = array_pop($arr);
			$ref = $this->values;
			while( $k = array_shift($arr) ) $ref = $ref[$k];
			$value = $ref[$fin];
		}

		// if value is array recurse output
		if( is_array($value) ){
			$ret = array();
			foreach($value as $k=>$v){
				$ret[$k] = $this->get("$key.$k");
			}
			return $ret;
		}
		return $this->outputFilter($key, $value);
	}

	/**
	 * out put filter interface
	 *
	 * @param string
	 * @param mixed
	 */
	public function outputFilter( $key, $value ) {
		return $this->format($value);
	}

	public function format( $format )
	{
		if( is_bool($format) ) return $format;
		if( is_object($format) ) return $format;
		if( is_array($format) ) {
			foreach($format as $k=>$v) {
				$format[$k] = $this->format( $v );
			}
			return $format;
		}
		$text = preg_replace('/\$\{(.*?)\}/e', '$this->get("\1")', $format);
		$args = array_slice(func_get_args( ),1);
		if(!empty($args)) $text = vsprintf($text, $args);
		return $text;
	}

	public function dump( )
	{
		var_dump($this->get());
	}
}
?>
