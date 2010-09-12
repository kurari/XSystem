<?php
/**
 * Base Object Test
 */
class BaseTest extends XTestCase
{
	public $Base;

	function init( )
	{
		$this->Base = new XBase();
	}

	function testSetValue( )
	{
		$data = "data";
		$this->assertEquala('data', $data,'input output not much');
	}

	function testGetValue( )
	{
		$data = "data";
		echo $XTS;
		$this->assertEquala('data', $data,'input output not much');
	}
}
?>
