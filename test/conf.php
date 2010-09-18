<?php
require_once "conf/conf.class.php";

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
		$Conf = XConf::factory('ini');
		$this->assertEquals( get_class($Conf), 'XConfIni');
	}

	function testParse( )
	{
		$Conf = XConf::factory('ini');
		$file = dirname(__FILE__).'/data/test.ini';
		$Container = $Conf->load( $file );

		var_dump($Container->toArray( ));
	}

}
?>
