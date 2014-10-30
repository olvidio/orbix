<?php
namespace profesores\model;
use core;
/**
 * GestorProfesorLatin
 *
 * Classe per gestionar la llista d'objectes de la clase ProfesorLatin
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */

class GestorProfesorLatin Extends core\ClaseGestor {
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
		$this->setNomTabla('d_profesor_latin');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus ProfesorLatin
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus ProfesorLatin
	 */
	function getProfesoresLatinQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oProfesorLatinSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorProfesorLatin.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom']);
			$oProfesorLatin= new ProfesorLatin($a_pkey);
			$oProfesorLatin->setAllAtributes($aDades);
			$oProfesorLatinSet->add($oProfesorLatin);
		}
		return $oProfesorLatinSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus ProfesorLatin
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus ProfesorLatin
	 */
	function getProfesoresLatin($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oProfesorLatinSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL') unset($aWhere[$camp]);
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
			$sClauError = 'GestorProfesorLatin.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorProfesorLatin.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom']);
			$oProfesorLatin= new ProfesorLatin($a_pkey);
			$oProfesorLatin->setAllAtributes($aDades);
			$oProfesorLatinSet->add($oProfesorLatin);
		}
		return $oProfesorLatinSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
