<?php
namespace dossiers\model;
use core;
/**
 * GestorDossier
 *
 * Classe per gestionar la llista d'objectes de la clase Dossier
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorDossier {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorDossier
	 *
	 */
	function __construct() {
		// constructor buit
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus Dossier
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Dossier
	 */
	function getDossiersQuery($sQuery='') {
		$oDbl = $GLOBALS['oDBPC'];
		$oDossierSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorDossier.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('tabla' => $aDades['tabla'],
							'id_pau' => $aDades['id_pau'],
							'id_tipo_dossier' => $aDades['id_tipo_dossier']);
			$oDossier= new Dossier($a_pkey);
			$oDossier->setAllAtributes($aDades);
			$oDossierSet->add($oDossier);
		}
		return $oDossierSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Dossier
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Dossier
	 */
	function getDossiers($aWhere=array(),$aOperators=array()) {
		$oDbl = $GLOBALS['oDBPC'];
		$oDossierSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp === '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores.
			if ($sOperador === 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL') unset($aWhere[$camp]);
		}
		$sCondi = implode(' AND ',$aCondi);
		if ($sCondi!='') $sCondi = " WHERE ".$sCondi;
		if (isset($GLOBALS['oGestorSessioDelegación'])) {
		   	$sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades',$sCondi,$aWhere);
		} else {
			$sLimit='';
		}
		if ($sLimit===false) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM d_dossiers_abiertos ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorDossier.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorDossier.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('tabla' => $aDades['tabla'],
							'id_pau' => $aDades['id_pau'],
							'id_tipo_dossier' => $aDades['id_tipo_dossier']);
			$oDossier= new Dossier($a_pkey);
			$oDossier->setAllAtributes($aDades);
			$oDossierSet->add($oDossier);
		}
		return $oDossierSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
