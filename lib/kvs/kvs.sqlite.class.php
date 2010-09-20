<?php
/**
 * Key Value Store
 */
class XKeyValueStoreSqlite extends XKeyValueStore
{
	function __construct( $opt )
	{
		parent::__construct($opt);

		if(!file_exists($opt['path'])){
			$db = sqlite_open($opt['path']);
			sqlite_query($db, 'CREATE TABLE data (key, value, created);');
			sqlite_close($db);
		}

		$this->db = sqlite_open($opt['path']);
	}

	function gc( $time, $key = false )
	{
		if( $key === false )
		{
			return sqlite_query($this->db, sprintf('DELETE FROM data WHERE created < %s',sqlite_escape_string($time)));
		}
		return sqlite_query($this->db, sprintf('DELETE FROM data WHERE key = "%s" AND created < %s',
			sqlite_escape_string($key),
			sqlite_escape_string($time)
		));
	}

	function get( $key )
	{
		$res  = sqlite_query($this->db, sprintf('SELECT * FROM data WHERE key = "%s"',sqlite_escape_string($key)));
		$data = sqlite_fetch_array($res, SQLITE_ASSOC);
		return isset($data['value']) ? urldecode($data['value']): false;
	}

	function set( $key, $value )
	{
		$res  = sqlite_query($this->db, sprintf(
			'REPLACE INTO data (key, value, created) VALUES ("%s", "%s", %s)',
			sqlite_escape_string($key),
			urlencode($value),
			time()
		));
	}
}
?>
