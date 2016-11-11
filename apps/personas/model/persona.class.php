<?php
namespace personas\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula pv_personas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
/**
 * Classe que implementa l'entitat pv_personas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class Persona {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 */
	function __construct() {
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	
	public static function NewPersona($id_nom) { 
		$gesPersonaDl = new GestorPersonaDl();
		$cPersonasDl = $gesPersonaDl->getPersonasDl(array('id_nom'=>$id_nom,'situacion'=>'A'));
		if (count($cPersonasDl) > 0) {
			$oPersona = $cPersonasDl[0];
		} else {
			$gesPersonaEx = new GestorPersonaEx();
			$cPersonasEx = $gesPersonaEx->getPersonasEx(array('id_nom'=>$id_nom));
			if (count($cPersonasEx) > 0) {
				$oPersona = $cPersonasEx[0];
			} else {
				$oPersona = new PersonaIn($id_nom);
			}
		}
		return $oPersona;
	}



	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/


}
?>
