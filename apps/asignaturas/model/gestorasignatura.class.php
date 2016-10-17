<?php
namespace asignaturas\model;
use core;
use web;
/**
 * GestorAsignatura
 *
 * Classe per gestionar la llista d'objectes de la clase Asignatura
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/11/2010
 */

class GestorAsignatura Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorAsignatura
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('xa_asignaturas');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna JSON llista d'Asignaturas
	 *
	 * @param string sQuery la query a executar.
	 * @return object Json 
	 */
	function getJsonAsignaturas($aWhere) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sCondi = '';
		foreach ($aWhere as $camp => $val) {
			if ($camp == 'nombre_asig' && !empty($val)) {
				$sCondi .= "WHERE nombre_asig ILIKE '%$val%'";
			}
			if ($camp == 'id' && !empty($val)) {
				if (!empty($sCondi)) {
					$sCondi .= " AND id_asignatura = $val";
				} else {
					$sCondi .= "WHERE id_asignatura = $val";
				}
			}
		}
		$sOrdre = " ORDER BY id_nivel";
		$sLimit = " LIMIT 25";
		$sQuery = "SELECT DISTINCT id_asignatura,nombre_asig,id_nivel FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorAsignatura.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$json = '[';
		$i = 0;
		foreach ($oDbl->query($sQuery) as $aClave) {
			$i++;
			$id_asignatura = $aClave[0];
			$nombre_asig = $aClave[1];
			$json .= ($i > 1)? ',' : ''; 
			$json .= "{\"id\":\"$id_asignatura\",\"name\":\"$nombre_asig\"}";
		}
		$json .= ']';
		return $json;
	}

	/**
	 * retorna un array del tipus: id_asignatura => credits
	 *
	 * @return array id_asignatura => credits
	 */
	function getArrayAsignaturasCreditos() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oTipoCentroSet = new core\Set();
		$sQuery="SELECT id_asignatura, creditos FROM $nom_tabla order by id_nivel";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorAsignatura.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aOpciones=array();
		foreach ($oDbl->query($sQuery) as $aClave) {
			$clave=$aClave[0];
			$val=$aClave[1];
			$aOpciones[$clave]=$val;
		}
		return $aOpciones;
	}

	/**
	 * retorna un objecte del tipus Desplegable
	 * Les posibles asignatures
	 *
	 * @return object del tipus Desplegable
	 */
	function getListaAsignaturas() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oTipoCentroSet = new core\Set();
		$sQuery="SELECT id_asignatura, nombre_corto FROM $nom_tabla order by id_nivel";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorAsignatura.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aOpciones=array();
		foreach ($oDbl->query($sQuery) as $aClave) {
			$clave=$aClave[0];
			$val=$aClave[1];
			$aOpciones[$clave]=$val;
		}
		return new web\Desplegable('',$aOpciones,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus Asignatura
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Asignatura
	 */
	function getAsignaturasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oAsignaturaSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorAsignatura.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_asignatura' => $aDades['id_asignatura']);
			$oAsignatura= new Asignatura($a_pkey);
			$oAsignatura->setAllAtributes($aDades);
			$oAsignaturaSet->add($oAsignatura);
		}
		return $oAsignaturaSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Asignatura
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Asignatura
	 */
	function getAsignaturas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oAsignaturaSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp === '_ordre') continue;
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
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorAsignatura.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorAsignatura.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_asignatura' => $aDades['id_asignatura']);
			$oAsignatura= new Asignatura($a_pkey);
			$oAsignatura->setAllAtributes($aDades);
			$oAsignaturaSet->add($oAsignatura);
		}
		return $oAsignaturaSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
