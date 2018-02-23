<?php
namespace core;
/**
 * Set
 *
 * Classe per a gestionar una col·lecció d'objectes.
 *
 * @package delegación
 * @subpackage model
 * @author 
 * @version 1.0
 * @created 22/9/2010
 */
class View {
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
	 * @return GestorActividad
	 *
	 */
	function __construct($namespace) {
		$this->snamespace = $namespace;
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	function render($file, $variables = array()) {

        extract($variables);

        ob_start();
		$dir_apps = ConfigGlobal::$web_path.'/apps';
		$base_dir = $_SERVER['DOCUMENT_ROOT'] . $dir_apps;

		$new_dir = str_replace('controller','view',$this->snamespace);
		$new_dir = str_replace('\\', DIRECTORY_SEPARATOR, $new_dir);

		
		$fileName = $base_dir . DIRECTORY_SEPARATOR . $new_dir. DIRECTORY_SEPARATOR . $file;

		require $fileName;
        $renderedView = ob_get_clean();
        return $renderedView;
    }


}
?>
