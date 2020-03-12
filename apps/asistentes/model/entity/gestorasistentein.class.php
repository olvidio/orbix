<?php
namespace asistentes\model\entity;
use core;
use core\ConfigGlobal;
/**
 * GestorAsistenteIn
 *
 * Classe per gestionar la llista d'objectes de la clase AsistenteIn
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorAsistenteIn Extends GestorAsistentePub {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus AsistenteIn
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus AsistenteIn
	 */
	function getAsistentesIn($aWhere=array(),$aOperators=array()) {
	    // En la misma tabla también están los que son AsistenteOut de mi dl
	    // Para saber los AsistentesIn debo quitar los que provienen de mi esquema.
	    $id_esquema = ConfigGlobal::mi_id_schema();
	    
	    $aWhere['id_schema'] = $id_esquema;
	    $aOperators['id_schema'] = '!=';
		return  $this->getAsistentesPub($aWhere,$aOperators);
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
