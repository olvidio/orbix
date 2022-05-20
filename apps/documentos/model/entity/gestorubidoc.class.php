<?php
namespace documentos\model\entity;
use core;
use web\Desplegable;
/**
 * GestorUbiDoc
 *
 * Classe per gestionar la llista d'objectes de la clase UbiDoc
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */

class GestorUbiDoc Extends core\ClaseGestor {
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
		$this->setNomTabla('doc_ubis');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	* retorna l'array d'objectes de tipus UbiDoc, segons tinguin llocs asignats o no
	*
	* @param bool bLugar true és si té llocs, false si no.
	* @return array Una Llista
	*/
	function getUbisDocLugar($bLugar) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oUbiDocSet = new core\Set();
		if ($bLugar === true) {
			$sQry="SELECT DISTINCT u.* FROM $nom_tabla u JOIN doc_lugares USING (id_ubi) ORDER BY nom_ubi";
		} else {
			$sQry="SELECT u.* FROM $nom_tabla u LEFT JOIN doc_lugares USING (id_ubi) WHERE id_lugar IS NULL ORDER BY nom_ubi";
		}
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorUbiDoc.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute()) === false) {
			$sClauError = 'GestorUbiDoc.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_ubi' => $aDades['id_ubi']);
			$oUbiDoc= new UbiDoc($a_pkey);
			$oUbiDoc->setAllAtributes($aDades);
			$oUbiDocSet->add($oUbiDoc);
		}
		return $oUbiDocSet->getTot();
	}

	/**
	 * retorna un objecte del tipus Desplegable
	 * Les posibles ubisDoc.
	 *
	 * @return array Una Llista
	 */
	function getListaUbisDoc() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT id_ubi,nom_ubi FROM $nom_tabla ORDER BY nom_ubi";
		if ( $oDbl->query($sQuery) === false) {
			$sClauError = 'GestorUbiDoc.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aOpciones=array();
		foreach ($oDbl->query($sQuery) as $aClave) {
			$clave=$aClave[0];
			$val=$aClave[1];
			$aOpciones[$clave]=$val;
		}
		return new Desplegable('',$aOpciones,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus UbiDoc
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus UbiDoc
	 */
	function getUbiDocsQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oUbiDocSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorUbiDoc.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_ubi' => $aDades['id_ubi']);
			$oUbiDoc= new UbiDoc($a_pkey);
			$oUbiDoc->setAllAtributes($aDades);
			$oUbiDocSet->add($oUbiDoc);
		}
		return $oUbiDocSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus UbiDoc
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus UbiDoc
	 */
	function getUbiDocs($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oUbiDocSet = new core\Set();
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
		if ($sLimit === FALSE) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClauError = 'GestorUbiDoc.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorUbiDoc.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_ubi' => $aDades['id_ubi']);
			$oUbiDoc= new UbiDoc($a_pkey);
			$oUbiDoc->setAllAtributes($aDades);
			$oUbiDocSet->add($oUbiDoc);
		}
		return $oUbiDocSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
