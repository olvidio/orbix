<?php
namespace ubis\model\entity;
use core;
use web;
use core\ConfigGlobal;
/**
 * GestorCentro
 *
 * Classe per gestionar la llista d'objectes de la clase Centro
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */

class GestorCentroCdc Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorCentro
	 *
	 */
	function __construct() {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('u_centros');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles centres
	 *
	 * @return array
	 */
	function getOpcionesCentrosCdc($sCondicion='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		//$orden = 'nombre_ubi';

		$sWhere="WHERE status = 't' AND cdc='t' ";
		if (!empty($sCondicion)) { $sWhere .= 'AND '.$sCondicion; }
		$sQuery="SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sWhere
				";
		if (($oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorCentroCdc.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$a_ctr = [];
		foreach ($oDbl->query($sQuery) as $row) {
		    $id_ubi = $row['id_ubi'];
		    $nombre_ubi = $row['nombre_ubi'];
		    
		    $a_ctr[$id_ubi] = $nombre_ubi;
		}
		
		return $a_ctr;
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
