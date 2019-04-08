<?php
namespace cartaspresentacion\model\entity;
use core;
/**
 * GestorCartaPresentacion
 *
 * Classe per gestionar la llista d'objectes de la clase CartaPresentacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 8/4/2019
 */

class GestorCartaPresentacion Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBP'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('du_presentacion');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus CartaPresentacion
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus CartaPresentacion
	 */
	function getCartasPresentacionQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oCartaPresentacionSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorCartaPresentacion.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		$clasename = get_class($this);
		$nomClase = join('', array_slice(explode('\\', $clasename), -1));
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_direccion' => $aDades['id_direccion'],
							'id_ubi' => $aDades['id_ubi']);
			
		    switch ($nomClase) {
		        case 'CartaPresentacionDl':
                    $oCartaPresentacion= new CartaPresentacionDl($a_pkey);
		            break;
		        case 'CartaPresentacionEx':
                    $oCartaPresentacion= new CartaPresentacionEx($a_pkey);
		            break;
		        default:
                    $oCartaPresentacion= new CartaPresentacion($a_pkey);
		    }
			$oCartaPresentacion->setAllAtributes($aDades);
			$oCartaPresentacionSet->add($oCartaPresentacion);
		}
		return $oCartaPresentacionSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus CartaPresentacion
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus CartaPresentacion
	 */
	function getCartasPresentacion($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oCartaPresentacionSet = new core\Set();
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
			$sClauError = 'GestorCartaPresentacion.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorCartaPresentacion.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		
		$clasename = get_class($this);
		$nomClase = join('', array_slice(explode('\\', $clasename), -1));
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_direccion' => $aDades['id_direccion'],
							'id_ubi' => $aDades['id_ubi']);
			
		    switch ($nomClase) {
		        case 'CartaPresentacionDl':
                    $oCartaPresentacion= new CartaPresentacionDl($a_pkey);
		            break;
		        case 'CartaPresentacionEx':
                    $oCartaPresentacion= new CartaPresentacionEx($a_pkey);
		            break;
		        default:
                    $oCartaPresentacion= new CartaPresentacion($a_pkey);
		    }
			$oCartaPresentacion->setAllAtributes($aDades);
			$oCartaPresentacionSet->add($oCartaPresentacion);
		}
		return $oCartaPresentacionSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
