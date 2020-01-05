<?php
namespace ubis\model\entity;

use core\ConfigGlobal;
/**
 * GestorCentroDl
 *
 * Classe per gestionar la llista d'objectes de la clase CentroDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */
class GestorCentroDl Extends  GestorCentro {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorCentroDl
	 *
	 */
	function __construct() {
	    if (ConfigGlobal::is_dmz()) {
	        $oDbl = $GLOBALS['oDBC'];
	        $this->setoDbl($oDbl);
	        $this->setNomTabla('cu_centros_dl');
	    } else {
	        $oDbl = $GLOBALS['oDB'];
	        $this->setoDbl($oDbl);
	        $this->setNomTabla('u_centros_dl');
	    }
	}
	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
