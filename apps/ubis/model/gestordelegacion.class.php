<?php
namespace ubis\model;
use core;
use web;
/**
 * GestorDelegacion
 *
 * Classe per gestionar la llista d'objectes de la clase Delegacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */

class GestorDelegacion Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorDelegacion
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('xu_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna un objecte del tipus Desplegable
	 *
	 * @param array optional lista de regions.
	 * @return object Una Llista de delegacions i regions per filtrar.
	 */
	function getListaDlURegionesFiltro() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		
			$sQuery="SELECT 'dl|'||dl, nombre_dl||' ('||dl||')'
					FROM $nom_tabla
					UNION 
					SELECT 'r|'||u.region,u.nombre_region||' ('||region||')' 
					FROM xu_region u 
					ORDER BY 2";

		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorRole.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}
	/**
	 * retorna un objecte del tipus Desplegable
	 *
	 * @param boolean si se incluye la dl o no
	 * @return object Una Llista de delegacions i regions.
	 */
	function getListaDelegacionesURegiones($bdl='t') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($bdl == 't') {
			$sQuery="SELECT dl, nombre_dl||' ('||dl||')'
					FROM $nom_tabla
					UNION 
					SELECT u.region,u.nombre_region||' ('||region||')' 
					FROM xu_region u 
					ORDER BY 2";
		} else {
			$sQuery="SELECT dl, nombre_dl||' ('||dl||')'
					FROM $nom_tabla WHERE dl != '".ConfigGlobal::$dele."'
					UNION 
					SELECT u.region,u.nombre_region||' ('||region||')' 
					FROM xu_region u 
					ORDER BY 2";
		}
		//echo "sql: $sQuery<br>";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorRole.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}
	/**
	 * retorna un objecte del tipus Desplegable
	 *
	 * @param array optional lista de regions.
	 * @return object Una Llista de delegacions.
	 */
	function getListaDelegaciones($aRegiones=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		
		$num_regiones=count($aRegiones);
		if ($num_regiones > 0) {
			$sCondicion="WHERE status = 't' AND region = ";
			$sCondicion.=implode(' OR region = ',$aRegiones);
			$sQuery="SELECT u.dl,u.nombre_dl FROM $nom_tabla u 
					$sCondicion
					ORDER BY nombre_dl";
		} else {
			$sQuery="SELECT dl, nombre_dl
					FROM $nom_tabla
					ORDER BY dl";
		}
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorRole.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus Delegacion
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Delegacion
	 */
	function getDelegacionesQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oDelegacionSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorDelegacion.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('dl' => $aDades['dl'],
							'region' => $aDades['region']);
			$oDelegacion= new Delegacion($a_pkey);
			$oDelegacion->setAllAtributes($aDades);
			$oDelegacionSet->add($oDelegacion);
		}
		return $oDelegacionSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Delegacion
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Delegacion
	 */
	function getDelegaciones($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oDelegacionSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp === '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
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
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorDelegacion.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorDelegacion.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('dl' => $aDades['dl'],
							'region' => $aDades['region']);
			$oDelegacion= new Delegacion($a_pkey);
			$oDelegacion->setAllAtributes($aDades);
			$oDelegacionSet->add($oDelegacion);
		}
		return $oDelegacionSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
