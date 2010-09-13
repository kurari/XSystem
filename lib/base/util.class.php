<?php
/**
 * X Utility
 */
dirname(__FILE__).'/base.class.php';

class XUtil {

	public static function makePath( ){
		$args = func_get_args( );
		return implode('/', $args);
	}

	public static	function addIncludePath( ){
		$args = func_get_args( );
		$args[] = get_include_path( );
		set_include_path(implode(PATH_SEPARATOR, $args));
	}

	public static function autoLoad( $name ) {
		if(preg_match('/X([A-Z][a-z]+)([A-Z][a-z]+)/', $name, $m)){
			$dir = strtolower($m[1]);
			$name = strtolower($m[2]).'.class.php';
			require_once XUtil::makePath($dir,$name);
			return true;
		}elseif(preg_match('/X(.*)/', $name, $m)){
			$file = strtolower($m[1]).'.class.php';
			require_once $file;
		}
	}

	public static function arrayGetOr( $array, $key, $default)
	{
		return isset($array[$key]) ? $array[$key]: $default;
	}

	public static function safeArray( $array, $default ) {
		$new = array();
		foreach($default as $k=>$v){
			$new[$k] = self::arrayGetOr( $array, $k, $v );
		}
		return $new;
	}
}
?>
