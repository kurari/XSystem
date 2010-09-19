<?php
/**
 * XSytem test
 */
require_once 'class/XSystem.class.php';
require_once 'class/XMessage.class.php';

class XSystemTest extends XTestCase
{
	public $dirname;
	public $FW;

	function init( )
	{
		$this->dirname = dirname(__FILE__).'/data';
		$this->FW = XSystem::factory( XUtil::makePath($this->dirname, "site.ini"),array('root'=>$this->dirname) );
	}

	function testFactory( )
	{
		$this->assertEquals('XSystem', get_class($this->FW));
	}

	function testLogFactory()
	{
		$this->assertTrue(is_a($this->FW->Log, 'XLog'));
	}

	function testDoApplication()
	{
		$Request = new XMessage( );
		$Request->set('type', 'display');
		$Request->set('app', 'system.default');
		$Request->set('function', 'run');
		$Request->set('data', array());
		$Res = $this->FW->accept( $Request );
		$Res->dump();
	}

}
?>
