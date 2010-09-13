<?php
/**
 * Logger  Test
 */
require_once 'log/log.class.php';

class LogTest extends XTestCase
{
	public $handler;
	public $dirname;

	function init( )
	{
		$this->dirname = dirname(__FILE__).'/data';
	}


	function testFactory( )
	{
		$handler = XLog::factory(
			'file', 
			'default', 
			array(
				'append'   => 0,
				'dirname'  => $this->dirname,
				'filename' => 'test.log'
			), 
			XLOG_WARNING 
		);
		$this->assertEquala('XLogFile', get_class($handler), 'Object Type unmuch');
	}

	function testLogWrite( )
	{
		$handler = XLog::factory(
			'file', 
			'default', 
			array(
				'append'   => 0,
				'dirname'  => $this->dirname,
				'filename' => 'test.log'
			), 
			XLOG_DEBUG
		);
		$handler->info("TEST");
		$handler->notice("TEST");
		$handler->debug("TEST");
		$handler->warning("TEST");
		$handler->err("TEST");
		$handler->crit("TEST");
		$handler->close();
		$this->assertEquala(6, count(file( XUtil::makePath($this->dirname, 'test.log'))), 'Log Write Failed');
	}

	function testLogMask( )
	{
		$handler = XLog::factory(
			'file', 
			'default', 
			array(
				'append'   => 0,
				'dirname'  => $this->dirname,
				'filename' => 'test1.log'
			), 
			XLOG_WARNING 
		);
		$handler->open( );
		$handler->info("TEST");
		$handler->notice("TEST");
		$handler->debug("TEST");
		$handler->warning("TEST");
		$handler->err("TEST");
		$handler->crit("TEST");
		$handler->close( );

		$this->assertEquala(3, count(file( XUtil::makePath($this->dirname, 'test1.log') )), 'Log Write Failed');
	}

	function testObserver( )
	{
		$handler = XLog::factory(
			'file', 
			'default', 
			array(
				'append'   => 0,
				'dirname'  => $this->dirname,
				'filename' => 'test1.log'
			), 
			XLOG_WARNING 
		);

		$var = array();
		$observer = XLog::factoryObserver( 'var', array('var'=>&$var), XLOG_ERR );
		$handler->attach( $observer );
			
		$handler->open( );
		$handler->info("TEST");
		$handler->notice("TEST");
		$handler->debug("TEST");
		$handler->warning("TEST");
		$handler->err("TEST");
		$handler->crit("TEST");
		$handler->close( );
		$this->assertEquala(2, count( $var ), 'Observer Failed');
	}



}
?>
