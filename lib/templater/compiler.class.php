<?php
/**
 * Templater Compiler Plugin Handler
 *
 */
class XTemplaterCompilerException extends XTemplaterException{ };

class XTemplaterCompiler
{
	/**
	 * handlers
	 * @array
	 */
	private $_handlers = array();


	/**
	 * Utility
	 */
	function getOpt( $text )
	{
		$text = $this->compileVar( $text );
		if( preg_match_all('/\s*([^=]+)\s*=\s*( (?:[^"\'(\s]|  "[^"]+" | \([^)]*\) |  \'[^\']+\')+ )\s*/xms',$text,$m) ){
			$opt = array_combine( $m[1], $m[2] );
		}
		$store = XStore::factory('array', $opt);
		return $store;
	}

	/**
	 * Utility
	 */
	function compileVar( $text ) {
		$text = preg_replace('/\$([a-zA-Z0-9_.]+)/', '$store->get("\1")', $text);
		return $text;
	}


	/**
	 * create compiler handler
	 *
	 * @param callback compiler
	 */
	public static function factory( $compile )
	{
		$object = new XTemplaterCompiler( );
		$object->setHandler('compile', $compile);
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
	public function doCompile( )
	{
		$args = func_get_args( );
		return call_user_func_array($this->_handlers['compile'], $args);
	}
}
?>
