<?php
/**
 * Templater Resource Plugin Handler
 *
 */
class XTemplaterResourceException extends XTemplaterException{ };

class XTemplaterResource 
{
	/**
	 * handlers
	 * @array
	 */
	private $_handlers = array();

	/**
	 * create resource handler
	 *
	 * @param callback get
	 * @param callback getExpiredTime
	 * @param callback hasCache
	 * @param callback putCache
	 * @param callback getCachs
	 * @param callback gcCachs
	 */
	public static function factory( $get, $getExpiredTime, $hasCache, $putCache, $getCache, $gcCache )
	{
		$object = new XTemplaterResource( );
		$object->setHandler('get', $get);
		$object->setHandler('getExpiredTime', $getExpiredTime);
		$object->setHandler('hasCache', $hasCache);
		$object->setHandler('putCache', $putCache);
		$object->setHandler('getCache', $getCache);
		$object->setHandler('gcCache', $gcCache);
		return $object;
	}


	/**
	 * set handler
	 *
	 * @param string name
	 * @param string callback
	 */
	public function setHandler( $name, $callback )
	{
		$this->_handlers[$name] = $callback;
	}


	/**
	 * throw to handlers
	 */
	public function doGet( )
	{
		$args = func_get_args( );
		return call_user_func_array($this->_handlers['get'], $args);
	}
	/**
	 * throw to handlers
	 */
	public function getExpiredTime( )
	{
		$args = func_get_args( );
		return call_user_func_array($this->_handlers['getExpiredTime'], $args);
	}
}
?>
