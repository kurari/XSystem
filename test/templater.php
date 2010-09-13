<?php
/**
 * Templater
 */
require_once 'templater/templater.class.php';

class TemplaterTest extends XTestCase
{
	public $handler;
	public $dirname;

	function init( )
	{
		$this->dirname = dirname(__FILE__).'/data';
	}

	function testGetResourceFileFalse( )
	{
		$handler = new XTemplater( );
		try {
			$data = $handler->getResource("file://$this->dirname/notexists.tpl");
		} catch ( Exception $e ) {
			$this->assertEquals('XTemplaterResourceException',get_class($e) );
			return false;
		}
		$this->assertTrue( false, "No Exception Occured" );
	}

	function testGetResourceFile( )
	{
		$handler = new XTemplater( );
		$data = $handler->getResource("file://$this->dirname/test.tpl");
		$this->assertEquals('{{$title}}',$data, $data);
	}

	function estCompile( )
	{
		$handler = new XTemplater( );
		$data = $handler->getResource("file://$this->dirname/test.tpl");
		$data = $handler->compile($data);
		$this->assertEquals('<?php echo $store->get(\'title\'); ?>',$data, $data);
	}

	function testBlockCompile( )
	{
		$handler = new XTemplater( );
		$data = $handler->getResource("file://$this->dirname/block.tpl");
		$data = $handler->compile($data);
		$this->assertEquals('<?php echo $store->get(\'title\'); ?>',$data, $data);
	}
}
?>
