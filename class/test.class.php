<?php
/**
 * Test Case
 */
class XTestCase {


	function __construct( )
	{

	}

	function run( )
	{
		var_dump( get_class_methods( $this ) );

	}
}
?>
