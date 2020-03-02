<?php
namespace actividadcargos\model;

use core\ConfigGlobal;
use core\Set;

/**
 * GestorCargoOAsistente
 *
 * Classe per gestionar la llista d'objectes de la clase CargoOAsistente
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorCargoOAsistente {
	/* ATRIBUTS ----------------------------------------------------------------- */

    /**
     * conexión a l a base de datos PDO
     *
     * @var \PDO
     */
    private $oDbl;

    /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
        $this->oDbl = $GLOBALS['oDBE'];
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	/**
	 * retorna l'array d'objectes tipus CargoOAsistente
	 *
	 * @param integer id_nom
	 * @return array Una col·lecció d'objectes de tipus CargoOAsistente
	 */
	function getCargoOAsistente($iid_nom) {
		$oDbl = $this->oDbl;
		
		$oCargoOAsistenteSet = new Set();
		// lista de id_activ ordenados, primero los propios.
		$sQuery="SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_dl WHERE id_nom=$iid_nom
					UNION ALL
		        SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_out WHERE id_nom=$iid_nom
					UNION ALL
				SELECT id_activ,'f' as propio,id_cargo FROM d_cargos_activ_dl WHERE id_nom=$iid_nom
				ORDER BY 1,2 DESC";
		  
		if (($oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorCargoOAsistente.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aRepe=array();
		$c=0;
		foreach ($oDbl->query($sQuery) as $aDades) {
			if (in_array($aDades['id_activ'],$aRepe)) { // si está repetido, el primero tiene propio=true.
				// Añado al primero el id_cargo del segundo.
				$Obj =$oCargoOAsistenteSet->getElement($c-1);
				$Obj->setId_cargo($aDades['id_cargo']);
				$oCargoOAsistenteSet->setElement($c-1,$Obj);
				continue;
			}
			$oCargoOAsistente= new CargoOAsistente($aDades['id_activ']);
			$oCargoOAsistente->setId_nom($iid_nom);
			$oCargoOAsistente->setPropio($aDades['propio']);
			$oCargoOAsistenteSet->add($oCargoOAsistente);
			$aRepe[]=$aDades['id_activ'];
			$c++;
		}
		return $oCargoOAsistenteSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/
		
	/* METODES GET i SET --------------------------------------------------------*/

}