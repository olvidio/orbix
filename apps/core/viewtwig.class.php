<?php
namespace core;

require_once(ConfigGlobal::$dir_libs.'/vendor/autoload.php');
/**
 *
 *
 * @package delegación
 * @subpackage model
 * @author 
 * @version 1.0
 * @created 22/9/2010
 */
class ViewTwig extends \Twig_Environment {
	/* ATRIBUTS ----------------------------------------------------------------- */
	
	/**
	 * Namespace
	 *
	 * @var string
	 */
	 private $snamespace;


	/* CONSTRUCTOR -------------------------------------------------------------- */
	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct($namespace) {
		$this->snamespace = $namespace;
		
		$dir_apps = ConfigGlobal::$web_path.'/apps';
		$base_dir = $_SERVER['DOCUMENT_ROOT'] . $dir_apps;

		// reemplazo controller o model por view
		$patterns = array();
		$patterns[0] = '/controller/';
		$patterns[1] = '/model/';
		$replacements = array();
		$replacements[0] = 'view';
		$replacements[1] = 'view';
		$new_dir = preg_replace($patterns, $replacements, $this->snamespace);
		
		$new_dir = str_replace('\\', DIRECTORY_SEPARATOR, $new_dir);
		
		$dir_templates  = $base_dir . DIRECTORY_SEPARATOR . $new_dir;
		
		$loader = new \Twig_Loader_Filesystem($dir_templates);
		$options = [
		    'cache' => '/path/to/compilation_cache',
		    ];
		$options = [];
		parent::__construct($loader, $options);
		parent::addExtension(new \Twig_Extensions_Extension_I18n());
	}

	/* METODES PUBLICS -----------------------------------------------------------*/

}