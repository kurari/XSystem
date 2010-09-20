<?php
/**
 * XTemplater Plugin
 * ----------------------------------
 * Plugin : Resource
 * Type   : File
 * ----------------------------------
 * 
 * @author Hajime 
 * @exception XTemplaterResourceException
 */
class XTemplaterResourceFile extends XTemplaterResource 
{
	function get( $path, $Tpl )
	{
		try {
			$data = file_get_contents( $path );
		} catch ( Exception $e ) {
			throw new XTemplaterResourceException( $e->getMessage() );
		}
		return trim($data);
	}

	function getExpiredTime( $path, $Tpl )
	{
		return filemtime( $path );
	}

	function hadCache( )
	{
		return false;
	}

	function getCache( )
	{
		return "baka";
	}

	function putCache( )
	{
		return false;
	}

	function gcCache( )
	{
		return false;
	}
}
?>
