<?php
namespace actividades\model;
use core;
use web;
/**
 * GestorActividadAll
 *
 * Classe per gestionar la llista d'objectes de la clase Actividad
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorActividadAll Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorActividadAll
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_actividades_all');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna si hi ha una activitat coincident en dates de l'altre secció.
	 *
	 * @param object Actividad
	 * @param string salida. 'bool' para que retorne true/false, 'array' para que retorne la lista.
	 * @return bool,array una llista de id_activ.
	 */
	function getCoincidencia($oActividad,$salida='bool') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();

		$iTolerancia = 1;
		$interval = "P$iTolerancia"."D";
		$id_tipo_activ = $oActividad->getId_tipo_activ();
		$id = (string) $id_tipo_activ; // para convertir id_tipo_activ en un string.
		$seccion = ($id[0]=="1")? 2:1; 
		$oFini0 = \DateTime::createFromFormat('d/m/Y', $oActividad->getF_ini());
		$oFini1 = clone $oFini0;
		$oFfin0 = \DateTime::createFromFormat('d/m/Y', $oActividad->getF_fin());
		$oFfin1 = clone $oFfin0;
		$oFini0->sub(new \DateInterval($interval));
		$oFini1->add(new \DateInterval($interval));
		$oFfin0->sub(new \DateInterval($interval));
		$oFfin1->add(new \DateInterval($interval));
		$sql_ini = "f_ini between '".$oFini0->format('j/m/Y')."' and '".$oFini1->format('j/m/Y')."'";
		$sql_fin = "f_fin between '".$oFfin0->format('j/m/Y')."' and '".$oFfin1->format('j/m/Y')."'";
		if ($salida == 'array') {
			$sql = "SELECT id_activ";
		} else {
			$sql = "SELECT count(*)";
		}
		$sql .= " FROM $nom_tabla";
	   	$sql .= " WHERE id_tipo_activ::text ~ '^".$seccion."[45]' ";
		$sql .= " AND $sql_ini";
		$sql .= " AND $sql_fin";

		//echo "sql: $sql<br>";

		if (($oDblSt = $oDbl->query($sql)) === false) {
			$sClauError = 'GestorActividadAll.getCoincidencia';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if ($salida == 'array') {
			foreach ($oDblSt as $aDades) {
				$aActiv[] = $aDades['id_activ'];
			}
			return $aActiv;
		} else {
			return $oDblSt->fetchColumn();
		}
	}

	/**
	 * retorna l'array amb el id_ubi de les activitats sel·leccionades
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array una llista de id_ubi.
	 */
	function getUbis($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		$aUbis = array();
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
		   	$sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador("$nom_tabla",$sCondi,$aWhere);
		} else {
			$sLimit='';
		}
		if ($sLimit===false) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT id_ubi FROM $nom_tabla ".$sCondi." GROUP BY id_ubi".$sOrdre;
		//print_r($aWhere);
		//echo "<br>query: $sQry<br>";
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorActividadAll.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorActividadAll.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$aUbis[] = $aDades['id_ubi'];
		}
		return $aUbis;
	}
		
	/**
	 * retorna una llista id_activ=>Nom_activ
	 *
	 * @param string sTipo
	 * @param string scondicion Condicion adicional a sTipo.
	 * @return array Una Llista.
	 */
	function getListaActividadesDeTipo($sid_tipo='......',$scondicion='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE id_tipo_activ::text ~ '" . $sid_tipo. "' $scondicion
		   ORDER by f_ini";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorActividadAll.ListaDeTipo';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return $oDblSt;
	}

	/**
	 * retorna un Desplegable d'activitats
	 *
	 * @param string scondicion (debe empezar con AND)
	 * @return array Una Llista.
	 */
	function getListaActividadesEstudios($scondicion='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$cond_nivel_stgr = "(nivel_stgr < 6 OR nivel_stgr=11)";
		if (empty($scondicion)) {
			$inicio = date("d/m/Y", mktime(0,0,0,9,1,core\ConfigGlobal::any_final_curs() - 2));
			$scondicion = "AND f_ini > '$inicio'";
		}
		$sQuery="SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE " . $cond_nivel_stgr. " $scondicion
		   ORDER by f_ini";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorActividadAll.ListaActividadesEstudios';
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
	 * retorna l'array de id d'Actividad
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array de id_actividad intger
	 */
	function getArrayIds($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$aListaId = array();
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
		$sQry = "SELECT * FROM $nom_tabla".$sCondi.$sOrdre.$sLimit;
		//echo "<br>query: $sQry<br>";
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorActividadAll.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorActividadAll.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$aListaId[] = $aDades['id_activ'];
		}
		return $aListaId;
	}

	/**
	 * retorna l'array d'objectes de tipus Actividad
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Actividad
	 */
	function getActividadesQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oActividadSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorActividadAll.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_activ' => $aDades['id_activ']);
			$dl = $aDades['dl_org'];
			if ($dl == core\ConfigGlobal::mi_dele()) {
				$oActividad= new ActividadDl($a_pkey);
			} else {
				$oActividad= new ActividadEx($a_pkey);
			}
			$oActividad->setAllAtributes($aDades);
			$oActividadSet->add($oActividad);
		}
		return $oActividadSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Actividad
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Actividad
	 */
	function getActividades($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oActividadSet = new core\Set();
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
		$sQry = "SELECT * FROM $nom_tabla".$sCondi.$sOrdre.$sLimit;
		//echo "<br>query: $sQry<br>";
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorActividadAll.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorActividadAll.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_activ' => $aDades['id_activ']);
			$dl = $aDades['dl_org'];
			if ($dl == core\ConfigGlobal::mi_dele()) {
				$oActividad= new ActividadDl($a_pkey);
			} else {
				$oActividad= new ActividadEx($a_pkey);
			}
			$oActividad->setAllAtributes($aDades);
			$oActividadSet->add($oActividad);
		}
		return $oActividadSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
