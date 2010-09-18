<?php
/**
 * XSytem test
 */
require_once 'class/XSystem.class.php';

class XSystemTest extends XTestCase
{
	public $dirname;
	public $FW;

	function init( )
	{
		$this->dirname = dirname(__FILE__).'/data';
		$this->FW = XSystem::factory( XUtil::makePath($this->dirname, "site.ini") );
	}

	function testFactory( )
	{
		$this->assertEquals('XSystem', get_class($this->FW));
	}

	function testLogFactory()
	{
		$this->assertTrue(is_a($this->FW->Log, 'XLog'));
	}

}
?>
