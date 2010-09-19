<?php
/**
 * XConf Library
 * ----
 * 
 * @author Hajime MATSUMOTO
 */

require_once 'base/exception.class.php';
require_once 'store/store.class.php';

class XConfigException extends XBaseException{ }

/**
 * XConfig Class
 */
class XConfig extends XStore
{

	public static function factory($type, $default = false, $options = false)
	{
		$file = dirname(__FILE__)."/config.$type.class.php";
		$class= "XConfig".ucfirst($type);

		if( !file_exists( $file ) ) throw new XConfigException('file %s not found', $file);
		require_once $file;

		if( !class_exists( $class ) ) throw new XConfigException('class %s not found', $class);
		$conf = new $class( );
		return $conf;
	}
}
?>
