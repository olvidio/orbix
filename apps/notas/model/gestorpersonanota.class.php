<?php
namespace notas\model;
use core;
/**
 * GestorPersonaNota
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaNota
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

class GestorPersonaNota Extends core\ClaseGestor {
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
		$this->setNomTabla('e_notas');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	/**
	 * retorna l'array d'objectes de tipus PersonaNota. Només les aprovades.
	 *
	 * @param integer id_nom  de la persona.
	 * @return array Una col·lecció d'objectes de tipus PersonaNota
	 */
	function getPersonaNotasSuperadas($id_nom) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oPersonaNotaSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		
		$gesNotas = new GestorNotas();
		$cNotas = $gesNotas->getNotas(array('superda'=>'t'));
		$superadas_txt = '';
		foreach ($cNotas as $oNota) {
			$id_situacion = $oNota->getId_situacion();
			$superadas_txt .= !empty($superadas_txt)? '|' : '';
			$superadas_txt .= $id_situacion;
		}

		$sQry = "SELECT * FROM  $nom_tabla
				WHERE id_nom=$id_nom AND id_situacion ~ '$superadas_txt'
				";
		if (($oDblSt = $oDbl->query($sQry)) === false) {
			$sClauError = 'GestorPersonaNota.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQry,PDO::FETCH_ASSOC) as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom'],
							'id_asignatura' => $aDades['id_asignatura']);
			$oPersonaNota= new PersonaNota($a_pkey);
			$oPersonaNota->setAllAtributes($aDades);
			$oPersonaNotaSet->add($oPersonaNota);
		}
		return $oPersonaNotaSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus PersonaNota
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus PersonaNota
	 */
	function getPersonaNotasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oPersonaNotaSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersonaNota.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom'],
							'id_nivel' => $aDades['id_nivel']);
			$oPersonaNota= new PersonaNota($a_pkey);
			$oPersonaNota->setAllAtributes($aDades);
			$oPersonaNotaSet->add($oPersonaNota);
		}
		return $oPersonaNotaSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus PersonaNota
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus PersonaNota
	 */
	function getPersonaNotas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oPersonaNotaSet = new core\Set();
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
			$sClauError = 'GestorPersonaNota.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorPersonaNota.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom'],
							'id_nivel' => $aDades['id_nivel']);
			$oPersonaNota= new PersonaNota($a_pkey);
			$oPersonaNota->setAllAtributes($aDades);
			$oPersonaNotaSet->add($oPersonaNota);
		}
		return $oPersonaNotaSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
