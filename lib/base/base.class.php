<?php
/**
 * XSystem
 * ---
 * @file class/base.class.php
 * @author Hajime MATSUMOTO
 */
require_once 'base/exception.class.php';

class XNoMethodException extends XBaseException { }

/**
 * XBase Object
 *
 */
class XBase {

	function __call( $name, $args )
	{
		throw new XNoMethodException("$name is not defined ".print_r($args,true));
	}

}
?>
