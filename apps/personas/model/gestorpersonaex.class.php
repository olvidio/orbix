<?php
namespace personas\model;
use core;
/**
 * GestorPersonaEx
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorPersonaEx Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	private $sApeNom="apellido1||
	case when nx2 = '' or nx2 isnull then ' ' else ' '||nx2||' ' end 
	||
	case when apellido2 = '' or apellido2 isnull then '' else ''||apellido2||'' end 
	||', '||
	case when trato isnull or trato = '' then '' else trato||' ' end 
	||COALESCE(apel_fam, nom)||
	case when nx1 = '' or nx1 isnull then '' else ' '||nx1||' ' end 
	";

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBR'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('p_de_paso_ex');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles Sacd
	 *
	 * @param string sdonde (condición del sql. debe empezar por AND).
	 * @return array Una Llista
	 */
	function getListaSacd($sdonde='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT id_nom, ".$this->sApeNom." as ape_nom
		   	FROM $nom_tabla
		   	WHERE situacion='A' AND sacd='t' $sdonde
		   	ORDER by apellido1,apellido2,nom";
		//echo "qry: $sQuery<br>";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersonaEx.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new Desplegable('',$oDblSt,'',true);
	}
	
	/**
	 * retorna una llista id_nom=>apellidosNombre
	 *
	 * @param string sTabla
	 * @return array Una Llista.
	 */
	function getListaPersonasTabla($sTabla='personas',$sTipo='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sdonde= empty($sTipo)? '' : "AND id_tabla='$sTipo'";
		$sQuery="SELECT id_tabla,id_nom, ".$this->sApeNom." as ape_nom
		   	FROM ".$sTabla." p
		   	WHERE situacion='A' $sdonde
		   	ORDER by apellido1";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersonaEx.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aLista=array();
		foreach ($oDbl->query($sQuery) as $aDades) {
			$id_nom=$aDades['id_nom'];
			switch($aDades['id_tabla']) {
				case 'p':
				case 'pa':
				case 'pn':
					$oPersonaEx = new PersonaEx($id_nom);
					$ctr = $oPersonaEx->getDl();
					break;
				case 'n':
				case 'a':
				case 's':
					$ctr=_("no hauria de sortir això");
					break;
			}
			$aLista[$id_nom]=$aDades['ape_nom'] ." ($ctr)";
		}
		return $aLista;
	}
	function getListaPersonasTabla2($sTabla='personas',$sTipo='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sdonde= empty($sTipo)? '' : "AND id_tabla='$sTipo'";
		$sQuery="SELECT id_nom, ".$this->sApeNom." as ape_nom
		   	FROM ".$sTabla." p
		   	WHERE situacion='A' $sdonde
		   	ORDER by apellido1";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersona.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return $oDblSt;
	}



	/**
	 * retorna l'array d'objectes de tipus PersonaEx
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus PersonaEx
	 */
	function getPersonasExQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oPersonaExSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersonaEx.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom']);
			$oPersonaEx= new PersonaEx($a_pkey);
			$oPersonaEx->setAllAtributes($aDades);
			$oPersonaExSet->add($oPersonaEx);
		}
		return $oPersonaExSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus PersonaEx
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus PersonaEx
	 */
	function getPersonasEx($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oPersonaExSet = new core\Set();
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
			$sClauError = 'GestorPersonaEx.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorPersonaEx.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom']);
			$oPersonaEx= new PersonaEx($a_pkey);
			$oPersonaEx->setAllAtributes($aDades);
			$oPersonaExSet->add($oPersonaEx);
		}
		return $oPersonaExSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
