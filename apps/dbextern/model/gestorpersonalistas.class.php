<?php
namespace dbextern\model;
use core;

/**
 * GestorPersonaListas
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaListas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/02/2017
 */
class GestorPersonaListas Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorPersonaListas
	 *
	 */
	function __construct() {
		if (!empty($_SESSION['oDBListas']) && $_SESSION['oDBListas'] == 'error') {
			exit(_("No se puede conectar con la base de datos de Listas")); 
		}
		$oDbl = $GLOBALS['oDBListas'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('dbo.q_dl_Estudios_b');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus PersonaListas
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus PersonaListas
	 */
	function getPersonaListasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oPersonaListasSet = new core\Set();
	
	/*
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersonaListas.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
	 * 
	 */
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('Identif' => $aDades['Identif']);
			$oPersonaListas= new PersonaListas($a_pkey);
			$oPersonaListas->setAllAtributes($aDades);
			$oPersonaListasSet->add($oPersonaListas);
		}
		return $oPersonaListasSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus PersonaListas
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus PersonaListas
	 */
	function getPersonaListas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oPersonaListasSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
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
		echo "qry: $sQry<br>";
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorPersonaListas.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorPersonaListas.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('Identif' => $aDades['Identif']);
			$oPersonaListas= new PersonaListas($a_pkey);
			$oPersonaListas->setAllAtributes($aDades);
			$oPersonaListasSet->add($oPersonaListas);
		}
		return $oPersonaListasSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

	
}
