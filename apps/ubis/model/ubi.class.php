<?php
namespace ubis\model;
use core;
/**
 * Classe que implementa l'entitat ubis
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class Ubi {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe vuit. 
	 */
	function __construct() {
	}

	/* METODES PUBLICS ----------------------------------------------------------*/


	public static function NewUbi($id_ubi) { 
		$gesCentro = new GestorCentro;
		$cCentros = $gesCentro->getCentros(array('id_ubi'=>$id_ubi));
		if (count($cCentros) > 0) {
			$oUbi = $cCentros[0];
		} else {
			$oUbi = new Casa($id_ubi);
		}
		return $oUbi;
	}
}
?>
