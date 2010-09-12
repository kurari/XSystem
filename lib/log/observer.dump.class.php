<?php
/**
 * Observer 
 */
class XLogObserverDump extends XLog
{
	/**
	 * line format
	 * @var string
	 */
	public $level;

	function __construct( $conf, $level )
	{
		$this->level = $level;
	}


	function notify( $event )
	{
		var_dump( $event['message-pre']);
	}
}
?>
