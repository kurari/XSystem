<?php
/**
 * XConf Library
 * ----
 * 
 * @author Hajime MATSUMOTO
 */

require_once 'base/exception.class.php';

class XConfException extends XBaseException{ }

/**
 * XConf Class
 */
class XConf
{

	public static function factory($type)
	{
		$file = dirname(__FILE__)."/conf.$type.class.php";
		$class= "XConf".ucfirst($type);

		if( !file_exists( $file ) ) throw new XConfException('file %s not found', $file);
		require_once $file;

		if( !class_exists( $class ) ) throw new XConfException('class %s not found', $class);

		$conf = new $class( );

		return $conf;
	}





}
?>
