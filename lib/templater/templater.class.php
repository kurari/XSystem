<?php
/**
 * Templater
 *
 */

/**
 * store object
 */
require_once 'store/store.class.php';

/**
 * Exception
 */
require_once 'exception/exception.class.php';
class XTemplaterException extends XException{ }

/**
 * Resource handler
 */
require_once 'templater/resource.class.php';

/**
 * Compile Logic
 */
require_once 'templater/templaterCompile.class.php';
class XTemplater extends XBase
{
	/**
	 * left delimiter
	 * @var string
	 */
	protected $leftDelimiter  = '{{';

	/**
	 * right delimiter
	 * @var string
	 */
	protected $rightDelimiter = '}}';

	/**
	 * plugin directory
	 * @var string 
	 */
	protected $pluginDir = "";

	/**
	 * regexp
	 * @var array
	 */
	protected $quick = array(
		'resource'=>'([^:]+)://(.*)'
	);

	/**
	 * plugins 
	 * @var array
	 */
	public $plugin = array(
		'resource'=>array( ),
		'compiler'=>array( )
	);

	function __construct( )
	{
		$this->pluginDir = array(
			dirname(__FILE__).'/plugin'
		);
	}

	public function getDelimiter( )
	{
		return array( $this->leftDelimiter, $this->rightDelimiter );
	}

	/**
	 * load compiler
	 */
	function loadCompilerHandler( ){
		foreach($this->pluginDir as $d ){
			foreach(glob("$d/compiler.*.plugin.class.php") as $file){
				require_once $file;
				$arr = explode('.',basename($file),3);
				$type = $arr[1];
				$class = 'XTemplaterCompiler'.ucfirst($type);
				if(!class_exists($class)) throw new XTemplaterCompilerException('compiler %s is not defined', $type);

				$o = new $class( $this );
				$this->registerCompilerHandler( $type, array($o,'compile') );
			}
		}
	}

	/**
	 * gettin compile handler
	 *
	 * @param string name
	 */
	function getCompilerHandler( $type )
	{
		if( isset($this->plugin['compiler'][$type]) ) {
			return $this->plugin['compiler'][$type];
		}
	}
	/**
	 * gettin resource handler
	 *
	 * @param string name
	 */
	function getResourceHandler( $type )
	{
		// if alrady loaded handler exists
		if( isset($this->plugin['resource'][$type]) ) {
			return $this->plugin['resource'][$type];
		}

		// info
		$fileName = "resource.$type.plugin.class.php";
		$class    = "XTemplaterResource".ucfirst($type);
		$file     = false;

		// search in plugin directorys 
		foreach($this->pluginDir as $d ) {
			$file = XUtil::makePath($d,$fileName);
			if(file_exists($file)) break;
		}

		// if not found
		if( $file == false ) return false;

		require_once $file;

		// if class not found
		if(!class_exists($class)) return false;

		// create instance
		$o = new $class($this);

		// save as resource handler
		return $this->registerResourceHandler( 
			$type,
			array($o,'get'),
			array($o,'hasCache'),
			array($o,'getCache'),
			array($o,'putCache'),
			array($o,'gcCache')
		);
	}

	/**
	 * register resource handler
	 *
	 * @param string name
	 * @param callback get
	 * @param callback hasCache
	 * @param callback putCache
	 * @param callback getCachs
	 * @param callback gcCachs
	 */
	function registerResourceHandler( $type, $get, $hasCache, $putCache, $getCache, $gcCache ) {
		return $this->plugin['resource'][$type] = XTemplaterResource::factory( $get, $hasCache, $putCache, $getCache, $gcCache );
	}

	/**
	 * register compiler handler
	 *
	 * @param string name
	 * @param callback compile
	 */
	function registerCompilerHandler( $type, $compile ) {
		return $this->plugin['compiler'][$type] = XTemplaterCompiler::factory( $compile );
	}

	/**
	 * resource getter
	 *
	 * @param string path file:///home/kurari/tpl.html
	 *                    for mat is  <type>://<path>
	 */
	function getResource( $path ) {
		// split resource type and path
		$res = explode( '://', $path, 2);

		// if it was not correct
		if(empty($res[1])) throw new XTemplaterException('Resource path incorrect (%s)',$path);

		$type = $res[0];
		$path = $res[1];

		if(false == $handler = $this->getResourceHandler( $type )) throw new XTemplaterException('Resource type %s is not registerd',$type);

		return $handler->doGet($path, $this);
	}

	/**
	 * compile
	 */
	function compile( $data )
	{
		$C = new XTemplaterComplie( $this );
		$C->compile( $data );
		return $C->fetch( );
	}
}


?>
