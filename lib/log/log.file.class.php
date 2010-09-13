<?php
require_once 'log/log.class.php';
require_once 'base/util.class.php';

class XLogFile extends XLog
{
	/**
	 * for file register
	 * @var string
	 */
	private $_filename = "php.log";

	/**
	 * dirname
	 * @var string
	 */
	private $_dirname = ".";

	/**
	 * line format
	 * @var string
	 */
	private $_line_format = '%{timestamp} %{level} [%{name}] %{message}';

	/**
	 * time format
	 * @var string
	 */
	private $_date_format = '%b %d %H:%M:%S';

	/**
	 * log file point
	 * @var fp
	 */
	private $_fp = false;

	/**
	 * append ?
	 * @var boolian
	 */
	private $_append = true;

	function __construct( $name, $conf, $level )
	{
		parent::__construct( );

		$this->_append   = isset($conf['append']) && $conf['append'] == 1 ? true: false;
		$this->_dirname  = XUtil::arrayGetOr($conf,'dirname',$this->_dirname);
		$this->_filename = XUtil::arrayGetOr($conf,'filename',$this->_filename);
		$this->_name     = $name;
		$this->_eol      = strstr(PHP_OS,'WIN') ? "\r\n": "\n";
		$this->_mask     = $this->MAX($level);


		// if script is down close fp
		register_shutdown_function( array($this,'close') );
	}

	public function log( $message, $level = XLOG_WARNING ){
		if( $this->_opend === false ) $this->open( );

		// if level if less than masked level ignore
		if( !$this->_isMasked( $level ) ) return false;


		// create log line 
		$line = $this->_format( 
			$this->_line_format, 
			strftime($this->_date_format), 
			$level, 
			$message
		).$this->_eol;

		// write log
		fwrite( $this->_fp, $line );

		$this->_notify( array('level'=>$level, 'message'=>$message, 'message-pre'=>$line) );
	}

	public function open( )
	{
		// if logger is not opend now open
		if($this->_opend === false) {
			$this->_line_format = str_replace(array_keys($this->_format_map),$this->_format_map, $this->_line_format);
			$file = XUtil::makePath($this->_dirname,$this->_filename);
			$this->_fp = fopen($file, $this->_append ? 'a': 'w');
			flock($this->_fp, LOCK_EX);
		}
		$this->_opend = true;
	}

	public function close( )
	{
		// log close
		if($this->_opend === true){
			flock($this->_fp, LOCK_UN);
			fclose($this->_fp);
		}
		$this->_opend = false;
	}

}
?>
