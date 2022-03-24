<?php
namespace notas\model\entity;
use core;
use web;
/**
 * GestorNota
 *
 * Classe per gestionar la llista d'objectes de la clase Nota
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

class GestorNota Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('e_notas_situacion');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	/**
	 * retorna un array amb
	 * Els posibles tipus de nota superada
	 *
	 *@param string sWhere condicion con el WHERE.
	 * @return array llista de id_situacion
	 */
	function getArrayNotasSuperadas($bsuperada='t') {
		$oDbl = $this->getoDbl();
		$sQuery="SELECT id_situacion
				FROM e_notas_situacion 
				WHERE superada = '$bsuperada'
				ORDER BY id_situacion";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorNota.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $row) {
			$aDades[] = $row['id_situacion'];
		}
		return $aDades;
	}

	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles tipus de nota
	 *
	 *@param string sWhere condicion con el WHERE.
	 * @return array Una Llista
	 */
	function getListaNotas($sWhere='') {
		$oDbl = $this->getoDbl();
		$sQuery="SELECT id_situacion, descripcion
				FROM e_notas_situacion $sWhere
				ORDER BY id_situacion";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorNota.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}


	/**
	 * retorna l'array d'objectes de tipus Nota
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Nota
	 */
	function getNotasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oNotaSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorNota.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_situacion' => $aDades['id_situacion']);
			$oNota= new Nota($a_pkey);
			$oNotaSet->add($oNota);
		}
		return $oNotaSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Nota
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Nota
	 */
	function getNotas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oNotaSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
			if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
			if ($sOperador == 'TXT') unset($aWhere[$camp]);
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
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorNota.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorNota.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_situacion' => $aDades['id_situacion']);
			$oNota= new Nota($a_pkey);
			$oNotaSet->add($oNota);
		}
		return $oNotaSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
