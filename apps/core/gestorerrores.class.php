<?php
namespace core;
/**
 * Classe para manejar los errores
 *
 * @package delegación
 * @subpackage model
 * @author 
 * @version 1.0
 * @created 21/9/2010
 */

class gestorErrores {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aDades de Actividad
	 *
	 * @var array
	 */
	 private $aDades;

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {

	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	function muestraMensaje($sClauError,$goto) {
		//$_SESSION['oGestorErrores']->addErrorAppLastError($oDBSt, $sClauError, $file);
		$txt=$this->leerErrorAppLastError();
		$err=$oDBSt->errorInfo();
		if (strstr($txt, 'duplicate key')) {
			echo _("Ya existe un registro con esta información");
		} else {
			echo "\n dd".$txt."\n $sClauError <br>";
		}
		$oPosicion = new web\Posicion();
		$seguir = $oPosicion->link_a($goto,0);
		//$seguir=link_a($goto,0);
		echo "<br><span class='link' onclick=fnjs_update_div('#main',$seguir)>"._("continuar")."</span>";


	}

	function leerErrorAppLastError(&$oDBSt, $sClauError,$line, $file) {
		$user=ConfigGlobal::mi_usuario();
		$ahora=date("d/m/Y H:i:s");
		$err=$oDBSt->errorInfo();
		$txt="\n".$ahora." - ".$user."->>  ".$err[2]."\n $sClauError en linea $line de: $file\n";
		
		$filename = ConfigGlobal::$directorio.'/log/errores.log';
		$trimmed = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$linea2=array_pop($trimmed);
		$linea1=array_pop($trimmed);
		return $linea1."\n".$linea2;
			/*
		if (!$handle = fopen($filename, 'a')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
		// Write $somecontent to our opened file.
		if ($txt=fread($handle) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
		fclose($handle);
		*/
	}
	function addErrorAppLastError(&$oDBSt, $sClauError,$line, $file) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$user = ConfigGlobal::mi_usuario();
		$esquema = ConfigGlobal::mi_region_dl();
		$ahora = date("d/m/Y H:i:s");
		$server = $oDBSt->getAttribute(constant("\PDO::ATTR_SERVER_INFO"));
		$err = $oDBSt->errorInfo();
		$txt = "\n# ".$ahora." - ".$user."[$esquema]$ip  ($server)";
		$txt.= "\n\t->>  ".$err[2]."\n $sClauError en linea $line de: $file\n";
		
		$filename = ConfigGlobal::$directorio.'/log/errores.log';
		if (!$handle = fopen($filename, 'a')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
		// Write $somecontent to our opened file.
		if (fwrite($handle, $txt) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
		fclose($handle);
	}
	function addError($err='',$sClauError,$line, $file) {
		$user=ConfigGlobal::mi_usuario();
		$ahora=date("d/m/Y H:i:s");
		$txt="\n".$ahora." - ".$user."->>  ".$err."\n $sClauError en linea $line de: $file\n";
		
		$filename = ConfigGlobal::$directorio.'/log/errores.log';
		if (!$handle = fopen($filename, 'a')) {
			 echo "Cannot open file ($filename)";
			 exit;
		}
		// Write $somecontent to our opened file.
		if (fwrite($handle, $txt) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
		fclose($handle);
	}
}
?>
