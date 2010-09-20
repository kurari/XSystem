<?php
/**
 * Key Value Store
 */
class XKeyValueStore 
{
	public static function factory( $type, $option )
	{
		$file = 'kvs/kvs.'.$type.'.class.php';
		require_once $file;

		$class = 'XKeyValueStore'.ucfirst($type);

		$store = new $class( $option );
		return $store;
	}

	public function __construct( $option )
	{

	}
}
?>
