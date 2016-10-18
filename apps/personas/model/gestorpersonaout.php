<?php
namespace personas\model;
/**
 * GestorPersonaOut
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaOut
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorPersonaOut extends GestorPersonaGlobal {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDB'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('p_de_paso_out');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus PersonaOut
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus PersonaOut
	 */
	function getPersonasOut($aWhere=array(),$aOperators=array()) {
		return parent::getPersonasObj($aWhere,$aOperators,'peronas\\model\\PersonaOut');

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
        }    
}
