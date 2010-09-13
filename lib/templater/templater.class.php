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
	protected $plugin = array(
		'resource'=>array( ),
		'compiler'=>array( )
	);

	function __construct( )
	{
		$this->pluginDir = array(
			dirname(__FILE__).'/plugin'
		);
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
		$this->loadCompilerHandler( );

		$l        = $this->leftDelimiter;
		$r        = $this->rightDelimiter;
		$quick    = '/(.*?)'.preg_quote($l) .'(.*?)'.preg_quote($r).'(.*)/xms';

		$text     = $data;
		$blocks   = array('foreach');
		$isEnv    = false;
		$buf      = "";
		$cnt      = 0;
		$compiled = "";

		$compilers = array_keys( $this->plugin['compiler'] );

		while(preg_match($quick, $text, $m)){
			// next data
			$text = $m[3];

			// get tag info
			$tagInfo = explode(" ", $m[2],2);
			$tagName = $tagInfo[0];
			$tagArg  = XUtil::arrayGetOr($tagInfo,1,"");

			// if env tag
			if($isEnv == false){

				// out put 
				$compiled.= $m[1];

				// if env start
				if( in_array($tagName, $compilers) && $m[2]{0} != '/' ) {
					// save current tag info
					$envName = $tagName; $envArg = $tagArg;

					// initialize
					$buf = ""; $cnt = 0; 

					// start enviroment
					$isEnv = true;
					continue;
				}else{

					// for compiler
					if( in_array($tagName, $compilers) ) {
						$compiler = $this->getCompilerHandler($tagName);
						$compiled.= $compiler->doCompile( "$tagName $tagArg", $tagArg, "", $this);
					}elseif($tagName{0} == '$'){
						// for $hoge
						$compiler = $this->getCompilerHandler('var');
						$compiled.= $compiler->doCompile("$tagName $tagArg", $tagName, "", $this);
					}
				}
			}

			// if in env
			if($isEnv == true){
				// cnt up if same name of env open
				if($tagName == $envName && $m[2]{0} != '/') $cnt++;
				if($tagName == "/".$envName) $cnt--;
				$buf .= $m[1];

				// if cnt is -1 close
				if( $cnt < 0 ){
					// for compiler
					$compiler = $this->getCompilerHandler($envName);
					$compiled.= $compiler->doCompile( "$envName $envArg", $envArg, $buf, $this);
					$isEnv = false; $buf = ""; $cnt = 0;
				}else{
					$buf .= $l.$m[2].$r;
				}
			}
		}
		$compiled.=$text;
		return $compiled;
	}
}


?>
