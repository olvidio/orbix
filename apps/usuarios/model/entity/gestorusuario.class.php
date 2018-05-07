<?php
namespace usuarios\model\entity;
use core;
/**
 * GestorUsuario
 *
 * Classe per gestionar la llista d'objectes de la clase Usuario
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/01/2014
 */

class GestorUsuario Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorUsuario
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDB'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_usuarios');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna una llista id_usuario=>usuario
	 *
	 * @param integer sfsv
	 * @return array Una Llista.
	 */
	function getListaUsuarios($sfsv='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sdonde= empty($sfsv)? '' : "WHERE sfsv='$sfsv'";
		$sQuery = "SELECT id_usuario, usuario FROM $nom_tabla ".$sdonde;
		$sQuery .= "ORDER BY sfsv,usuario";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersona.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aLista=array();
		foreach ($oDbl->query($sQuery) as $aDades) {
			$id_usuario=$aDades['id_usuario'];
			$aLista[$id_usuario]=$aDades['usuario'];
		}
		return $aLista;
	}


	/**
	 * retorna l'array d'objectes de tipus Usuario
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Usuario
	 */
	function getUsuariosQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oUsuarioSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorUsuario.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_usuario' => $aDades['id_usuario']);
			$oUsuario= new Usuario($a_pkey);
			$oUsuario->setAllAtributes($aDades);
			$oUsuarioSet->add($oUsuario);
		}
		return $oUsuarioSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Usuario
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Usuario
	 */
	function getUsuarios($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oUsuarioSet = new core\Set();
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
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorUsuario.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorUsuario.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_usuario' => $aDades['id_usuario']);
			$oUsuario= new Usuario($a_pkey);
			$oUsuario->setAllAtributes($aDades);
			$oUsuarioSet->add($oUsuario);
		}
		return $oUsuarioSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
