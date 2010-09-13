<?php
/**
 * Base Exception
 */
class XBaseException extends Exception {

	function __construct( $format ){
		$args = func_get_args( );
		array_shift( $args );
		$message = vsprintf($format, $args);
		parent::__construct( $message);
	}
}
?>
