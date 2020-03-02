<?php
namespace ubis\model\entity;
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
	    // para la sf (comienza por 2).
	    if ( substr($id_ubi, 0, 1) == 2 ) {
    		$gesCentro = new GestorCentroEllas();
	    } else {
    		$gesCentro = new GestorCentroEllos();
	    }
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
