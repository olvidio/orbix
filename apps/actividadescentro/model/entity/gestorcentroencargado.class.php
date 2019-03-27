<?php
namespace actividadescentro\model\entity;
use core;
use ubis\model\entity\CentroDl;
use actividades\model\entity\ActividadDl;
/**
 * GestorCentroEncargado
 *
 * Classe per gestionar la llista d'objectes de la clase CentroEncargado
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/01/2019
 */

class GestorCentroEncargado Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('da_ctr_encargados');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	/**
	 * retorna un texto con los dias que flatan para la siguiente actividad a partir de la fecha
	 *     que se le pasa como parámetro. (o en negativo para una actividad anterior).
	 *
	 * @param integer id_ubi.
	 * @param date iso. fecha de referencia respecto a la que calcular la diferencia de dias.
	 * @return string dias de diferencia con la próxima/anterior actividad.
	 */
	function getProximasActividadesDeCentro($id_ubi='',$f_ini_act='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQuery = "SELECT nom_activ,f_ini,f_fin,(f_ini - date '".$f_ini_act."') as dif
				FROM a_actividades_dl a JOIN $nom_tabla e USING (id_activ)
				WHERE e.id_ubi=$id_ubi
				ORDER BY abs(f_ini - date '".$f_ini_act."')
				limit 3
				";
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorCentroEncargado.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    $txt_dif="";
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $txt_dif .= " ".$aDades['dif'].";";
	    }
	    return $txt_dif;
	}
	
	/**
	 * retorna l'array d'objectes de tipus Actividad
	 *
	 * @param integer id_ubi.
	 * @param string condicion a añadir (sin where): f_ini BETWEEN '1/1/2010' AND '1/8/2010'.
	 * @return array Una col·lecció d'objectes de tipus ActividadDl
	 */
	function getActividadesDeCentros($iid_ubi='',$scondicion='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $oActividadSet = new core\Set();
	    if (!empty($scondicion)) $scondicion = ' AND '.$scondicion;
	    $sQuery = "SELECT d.id_activ FROM $nom_tabla d JOIN a_actividades_dl a USING (id_activ) WHERE d.id_ubi=$iid_ubi $scondicion ORDER BY f_ini";
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorCentroEncargado.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $a_pkey = array('id_activ' => $aDades['id_activ']);
	        $oActividad= new ActividadDl($a_pkey);
	        $oActividad->setAllAtributes($aDades);
	        $oActividadSet->add($oActividad);
	    }
	    return $oActividadSet->getTot();
	}
	
	/**
	 * retorna l'array d'objectes de tipus Ubi
	 *
	 * @param integer id_actividad.
	 * @return array Una col·lecció d'objectes de tipus CentroDl
	 */
	function getCentrosEncargadosActividad($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $oUbiSet = new core\Set();
	    $sQuery = "SELECT * FROM $nom_tabla d WHERE id_activ=$iid_activ ORDER BY num_orden";
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorCentroEncargado.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $a_pkey = array('id_ubi' => $aDades['id_ubi']);
	        $oUbi= new CentroDl($a_pkey);
	        $oUbi->setAllAtributes($aDades);
	        $oUbiSet->add($oUbi);
	    }
	    return $oUbiSet->getTot();
	}
	
	
	/**
	 * retorna l'array d'objectes de tipus CentroEncargado
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus CentroEncargado
	 */
	function getCentrosEncargadosQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oCentroEncargadoSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorCentroEncargado.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_activ' => $aDades['id_activ'],
							'id_ubi' => $aDades['id_ubi']);
			$oCentroEncargado= new CentroEncargado($a_pkey);
			$oCentroEncargado->setAllAtributes($aDades);
			$oCentroEncargadoSet->add($oCentroEncargado);
		}
		return $oCentroEncargadoSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus CentroEncargado
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus CentroEncargado
	 */
	function getCentrosEncargados($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oCentroEncargadoSet = new core\Set();
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
		if ($sLimit === FALSE) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClauError = 'GestorCentroEncargado.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorCentroEncargado.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_activ' => $aDades['id_activ'],
							'id_ubi' => $aDades['id_ubi']);
			$oCentroEncargado= new CentroEncargado($a_pkey);
			$oCentroEncargado->setAllAtributes($aDades);
			$oCentroEncargadoSet->add($oCentroEncargado);
		}
		return $oCentroEncargadoSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
