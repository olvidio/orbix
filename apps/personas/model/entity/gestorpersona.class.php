<?php
namespace personas\model\entity;
use core;
use actividades\model\entity as actividades;
/**
 * GestorPersona
 *
 * Classe per gestionar la llista d'objectes de la clase Persona
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorPersona Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
	}


	/* METODES PUBLICS -----------------------------------------------------------*/


	/**
	 * retorna l'array d'objectes de tipus Persona
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Persona
	 */
	function getPersonas($aWhere=array(),$aOperators=array()) {
		/* Buscar en los tres tipos de asistente: Dl, IN y Out. */
		$a_Clases[] = array('clase'=>'PersonaDl','get'=>'getPersonasDl');
		$a_Clases[] = array('clase'=>'PersonaIn','get'=>'getPersonasIn');
		$a_Clases[] = array('clase'=>'PersonaOut','get'=>'getPersonasOut');
		$a_Clases[] = array('clase'=>'PersonaEx','get'=>'getPersonasEx');
		$namespace = __NAMESPACE__;
		return $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
