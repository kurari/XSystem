<?php
/**
 * XTemplater Plugin
 * ----------------------------------
 * Plugin : Compiler
 * Type   : var
 * ----------------------------------
 * 
 * @author Hajime 
 * @exception XTemplaterResourceException
 */
class XTemplaterCompilerVar extends XTemplaterCompiler
{
	function compile( $tag, $args, $text, $Tpl )
	{
		$arr = explode('|', $tag);
		$var = $this->compileVar(trim($arr[0]));

		$output = "<?php echo $var; ?>";

		return $output;
	}
}
?>
