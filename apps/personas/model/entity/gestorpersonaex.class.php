<?php
namespace personas\model\entity;
/**
 * GestorPersonaEx
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorPersonaEx Extends GestorPersonaGlobal {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBR'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('p_de_paso_ex');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	/**
	 * retorna l'array d'objectes de tipus PersonaEx
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus PersonaEx
	 */
	function getPersonasEx($aWhere=array(),$aOperators=array()) {
		return parent::getPersonasObj($aWhere,$aOperators,'personas\\model\entity\\PersonaEx');
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
