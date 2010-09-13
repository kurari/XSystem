<?php
/**
 * XLogger
 */
define('XLOG_EMERG',    0);     /* System is unusable */
define('XLOG_ALERT',    1);     /* Immediate action required */
define('XLOG_CRIT',     2);     /* Critical conditions */
define('XLOG_ERR',      3);     /* Error conditions */
define('XLOG_WARNING',  4);     /* Warning conditions */
define('XLOG_NOTICE',   5);     /* Normal but significant */
define('XLOG_INFO',     6);     /* Informational */
define('XLOG_DEBUG',    7);     /* Debug-level messages */

define('XLOG_ALL',      0xffffffff);    /* All messages */
define('XLOG_NONE',     0x00000000);    /* No message */

class XLog {

	/**
	 * If Logger Opend
	 * @var boolian
	 */
	protected $_opend = false;

	/**
	 * name of logger
	 * @var string
	 */
	protected $_name = "default";

	/**
	 * Error Leve Name Dictionary
	 * @var array
	 */
	private $_dict = array();

	/**
	 * End Of Line
	 * @var string
	 */
	protected $_eol = "\r";

	/**
	 * Format replace
	 * @var array
	 */
	protected $_format_map = array(
		'%{name}'      => '%1$s',
		'%{timestamp}' => '%2$s',
		'%{level}'     => '%3$s',
		'%{message}'   => '%4$s'
	);

	/**
	 * log mask
	 * @var int
	 */
	protected $_mask = 0;

	/**
	 * observing observers
	 * @var array
	 */
	private $_listeners = array();


	/**
	 * create mask MAX
	 */
	public static function MAX($level)
	{
		return ((1 << ($level + 1)) - 1);
	}

	/**
	 * create mask MIN
	 */
	public static function MIN($level)
	{
		return XLOG_ALL ^ ((1 << $level) - 1);
	}

	/**
	 * create mask 
	 */
	public static function MASK($level)
	{
		return (1 << $level);
	}

	/**
	 * if is musk
	 */
	public function _isMasked($level)
	{
		return (XLog::MASK($level) & $this->_mask);
	}

	public function levelToName( $level ){
		static $dict;
		if(empty($dict)) $dict = array_flip($this->dict);
		return $dict[$level];
	}

	public function nameToLevel( $name ){
		return $this->dict[$name];
	}

	/**
	 * construct
	 */
	public function __construct( ){
		$this->dict = array(
			'emerg'   => XLOG_EMERG,
			'alert'   => XLOG_ALERT,
			'crit'    => XLOG_CRIT,
			'err'     => XLOG_ERR,
			'warning' => XLOG_WARNING,
			'notice'  => XLOG_NOTICE,
			'info'    => XLOG_INFO,
			'debug'   => XLOG_DEBUG
		);
	}

	/**
	 * log handler creation
	 */
	public static function factory( $handler, $name = 'default', $conf = array(), $level = XLOG_DEBUG )
	{
		$class = "XLog".ucfirst(strtolower($handler));
		require_once "log/log.".strtolower($handler).".class.php";
		$handler = new $class( $name, $conf, $level );
		return $handler;
	}

	/**
	 * observer creation
	 */
	public static function factoryObserver( $handler = 'dump', $conf = array(), $level = XLOG_DEBUG )
	{
		$class = "XLogObserver".ucfirst(strtolower($handler));
		require_once "log/observer.".strtolower($handler).".class.php";
		$handler = new $class( $conf, $level );
		return $handler;
	}

	/**
	 * must over write in log handlers
	 */ 
	public function log( $message, $level = XLOG_WARNING ){
		return false;
	}

	/**
	 * using magic method of __call to log methods
	 * such as emerg alert crit ....
	 */ 
	public function __call( $func, $args ){
		if(in_array($func, array('emerg','alert','crit','err','warning','notice','info','debug'))){
			$message = array_shift($args);
			$message = vsprintf($message, $args);
			return $this->log( $message, $this->nameToLevel($func) );
		}
		parent::__call( $func, $args );
	}

	/**
	 * format log strings
	 */ 
	public function _format( $format, $timestamp, $level, $message )
	{
		return sprintf($format, $this->_name, $timestamp, $this->levelToName($level),$message);
	}

	/**
	 * notify for listeners
	 */
	public function _notify( $event )
	{
		foreach( $this->_listeners as $observer ) {
			if( $event['level'] <= $observer->level ) $observer->notify( $event );
		}
	}

	/**
	 * attach observer
	 */ 
	public function attach( $observer ) 
	{
		$this->_listeners[] = $observer;
	}

	/**
	 * dettach observer
	 */ 
	public function dettach( $observer ) 
	{
		foreach($this->_listeners as $k=>$o) {
			if($o == $observer){
				unset($this->_listeners[$k]);
			}
		}
	}
}
?>
