<?php
/**
 * Observer 
 */
class XLogObserverVar extends XLog
{
	/**
	 * line format
	 * @var string
	 */
	public $level;
	public $var = array();

	function __construct( $conf, $level )
	{
		$this->level = $level;
		$this->var =& $conf['var'];
	}


	function notify( $event )
	{
		$this->var[] = $event['message-pre'];
	}
}
?>
