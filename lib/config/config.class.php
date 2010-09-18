<?php
/**
 * XConf Library
 * ----
 * 
 * @author Hajime MATSUMOTO
 */

require_once 'base/exception.class.php';

class XConfigException extends XBaseException{ }

/**
 * XConfig Class
 */
class XConfig
{

	public static function factory($type)
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
