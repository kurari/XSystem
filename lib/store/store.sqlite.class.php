<?php
/**
 * XStore
 * ------------------------
 * Type : sqlite
 * ------------------------
 *
 * backend using sqlite store 
 *
 */
class XStoreSqliteException extends XStoreException { }


class XStoreSqlite extends XStore implements XStoreInterface
{
	const INITIAL_SQL = 'CREATE TABLE store (key PRIMARY KEY, value, time TIMESTAMP DEFAULT CURRENT_TIMESTAMP)';
	const INSERT_SQL  = 'REPLACE INTO store (key, value, time) VALUES (\'%s\', \'%s\', \'%s\')';
	const GET_SQL     = 'SELECT value,time FROM store WHERE key=\'%s\'';
	const DELETE_SQL  = 'DELETE FROM store WHERE key=\'%s\'';
	const ALL_GET_SQL = 'SELECT key,value FROM store';

	private $_vars = array();
	private $con = false;
	private $file = '';
	private $valueTime = array();
	private $cache = array();

	function __construct( $default, $option ){
		$option = XUtil::safeArray( $option, array('file'=>false) );

		/* for required config check*/
		if($option['file'] === false) {
			throw new XStoreSqliteException('store type sqlite require option["file"]');
		}
		$this->file = $option['file'];
		$this->init( );
	}

	function connect( )
	{
		if( $this->con  === false ){
			$this->con = sqlite_open( $this->file );
			try {
				sqlite_query( $this->con, self::INITIAL_SQL );
			}catch(Exception $e){
			}
		}
		return $this->con;
	}

	public function drop( )
	{
		unlink( $this->file );
	}

	public function init( )
	{
		$con = $this->connect( );

	}

	public function dump( )
	{
		$res = sqlite_query( $con, self::ALL_GET_SQL );
		foreach(sqlite_fetch_all($res, SQLITE_ASSOC) as $row)
		{
			$this->cache[$row['name']] = unserialize($row['name']);
			$this->valueTime[$row['name']] = $row['time'];
		}
		var_dump($this->cache);
	}

	public function getTime( $key )
	{
		if(!isset($this->valueTime[$key]))
			$this->doGet($key);
		if(!isset($this->valueTime[$key]))
			return false;

		return $this->valueTime[$key];
	}

	public function doHas($key){
		return false !== $this->doGet($key) ? true: false;
	}

	public function doGet($key){
		if(isset($this->cache[$key])) return $this->cache[$key];

		$con = $this->connect( );
		$res = sqlite_query( $con, sprintf(self::GET_SQL,
			sqlite_escape_string($key)
		));
		$arr = sqlite_fetch_array($res, SQLITE_ASSOC);
		if(empty($arr)) return false;

		$this->valueTime[$key] = $arr['time'];
		$this->cache[$key] = unserialize($arr['value']);
		return $this->cache[$key];
	}

	public function doSet($key, $value){
		$con = $this->connect( );
		sqlite_query( $con, sprintf(self::INSERT_SQL, 
			sqlite_escape_string($key),
			sqlite_escape_string(serialize($value)),
			time( )
		));
		unset($this->cache[$key]);
		unset($this->valueTime[$key]);
	}

	public function doDelete( $key )
	{
		$con = $this->connect( );
		sqlite_query( $con, sprintf(self::DELETE_SQL, 
			sqlite_escape_string($key)
		));
		unset($this->cache[$key]);
		unset($this->valueTime[$key]);
	}
}
?>
