<?php
namespace personas\model;
use core;
use web;
/**
 * GestorPersonaDl
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

abstract class GestorPersonaGlobal Extends core\ClaseGestor {
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
			$sClauError = 'GestorPersonaDl.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}
	/**
	 * retorna una llista id_nom=>apellidosNombre
	 *
	 * @param string sTabla
	 * @return array Una Llista.
	 */
	function getListaPersonas($id_tabla='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($nom_tabla == 'p_de_paso_ex') {
			$Qry_tabla = empty($id_tabla)? '' : "AND id_tabla = '$id_tabla'";
			$sQuery="SELECT id_nom, ".$this->sApeNom." || ' (' || p.dl || ')' as ape_nom
				FROM $nom_tabla p 
				WHERE p.situacion='A' $Qry_tabla
				ORDER by apellido1,apellido2,nom";
			//echo "qry: $sQuery<br>";
		} else {
			$sQuery="SELECT id_nom, ".$this->sApeNom." || ' (' || c.nombre_ubi || ')' as ape_nom
				FROM $nom_tabla p LEFT JOIN u_centros_dl c ON (c.id_ubi=p.id_ctr)
				WHERE p.situacion='A'
				ORDER by apellido1,apellido2,nom";
			//echo "qry: $sQuery<br>";
		}
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersonaDl.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
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
			$sClauError = 'GestorPersonaDl.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aLista=array();
		foreach ($oDbl->query($sQuery) as $aDades) {
			$id_nom=$aDades['id_nom'];
			switch($aDades['id_tabla']) {
				case 'p':
					$sql='SELECT dl FROM vp_de_paso_in
					   		WHERE id_nom='.$id_nom;
					$ctr=$oDbl->query($sql)->fetchColumn();
					break;
				case 'n':
				case 'x':
				case 'a':
				case 's':
					$sql='SELECT nombre_ubi FROM u_centros_dl u JOIN personas_dl p ON (u.id_ubi=p.id_ctr)
					   		WHERE id_nom='.$id_nom;
					$ctr=$oDbl->query($sql)->fetchColumn();
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
	 * retorna l'array d'objectes de tipus PersonaDl
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus PersonaDl
	 */
	function getPersonasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oPersonaDlSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPersonaDl.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom']);
			$oPersonaDl= new PersonaDl($a_pkey);
			$oPersonaDl->setAllAtributes($aDades);
			$oPersonaDlSet->add($oPersonaDl);
		}
		return $oPersonaDlSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus PersonaDl
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus PersonaDl
	 */
	function getPersonas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oPersonaDlSet = new core\Set();
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
			$sClauError = 'GestorPersonaDl.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorPersonaDl.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom']);
			$oPersonaDl= new PersonaDl($a_pkey);
			$oPersonaDl->setAllAtributes($aDades);
			$oPersonaDlSet->add($oPersonaDl);
		}
		return $oPersonaDlSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
