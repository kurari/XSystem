<?php
/**
 * Store  Test
 */
require_once 'store/store.class.php';

class StoreTest extends XTestCase
{
	public $handler;
	public $dirname;

	function init( )
	{
		$this->dirname = dirname(__FILE__).'/data';
	}


	function testFactory( )
	{
		$store = XStore::factory( 'array', array());
		$this->assertEquals('XStoreArray', get_class($store), 'Object Type unmuch');

		$store = XStore::factory( 'sqlite', array(), array('file'=>$this->dirname."/store.db"));
		$this->assertEquals('XStoreSqlite', get_class($store), 'Object Type unmuch');
	}

	function testSqlite( )
	{
		$store = XStore::factory( 'sqlite', array(), array('file'=>$this->dirname."/store.db"));
		$this->assertEquals('XStoreSqlite', get_class($store), 'Object Type unmuch');
		$store->drop( );

		$store->set('test',array("aaa","bbb","ccc"));
		$store->set('test2',$GLOBALS);

		$this->assertNotNull( $store->getTime('test'), "Can't get Time" );
		$this->assertNotNull( $store->get('test2'), "Can't get test2" );

		$store->delete('test2');
		$this->assertFalse( $store->get('test2'), "Can't delete test2" );
		$this->assertEquals( $store->get('test'), array("aaa","bbb","ccc"));

		$data = $store->get('test');
		$this->assertEquals( $data[2], "ccc");
	}


}
?>
