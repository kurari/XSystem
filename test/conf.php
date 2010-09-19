<?php
require_once "config/config.class.php";

/**
 * Base Object Test
 */
class ConfTest extends XTestCase
{

	function init( )
	{
	}

	function testFactory( )
	{
		$Conf = XConfig::factory('ini');
		$this->assertEquals( get_class($Conf), 'XConfigIni');
	}

	function testParse( )
	{
		$Conf = XConfig::factory('ini');
		$file = dirname(__FILE__).'/data/test.ini';
		$Conf->load( $file );
		$this->assertTrue($Conf->get('log.option.append'));
	}

}
?>
