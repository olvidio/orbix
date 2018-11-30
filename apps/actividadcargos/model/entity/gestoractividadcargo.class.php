<?php
namespace actividadcargos\model\entity;
use actividades\model\entity as actividades;
use asistentes\model\entity as asistentes;
use core;
/**
 * GestorActividadCargo
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadCargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */

class GestorActividadCargo Extends core\ClaseGestor {
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
		$this->setNomTabla('d_cargos_activ_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus ActividadCargo
	 *
	 * @param integer id_nom. de la persona
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus ActividadCargo
	 */
	function getActividadCargosDeAsistente($aWhereNom,$aWhere=array(),$aOperators=array()) {
		// seleccionar las actividades segun los criterios de búsqueda.
		$GesActividades = new actividades\GestorActividad();
		$aListaIds = $GesActividades->getArrayIds($aWhere,$aOperators);
	
		$cCargos = $this->getActividadCargos($aWhereNom);
		// descarto los que no estan.
		$cCargosOk = array();
		foreach ($cCargos as $oActividadCargo) {
			$id_activ = $oActividadCargo->getId_activ();
			if (in_array($id_activ,$aListaIds)) {
				$oActividad = new actividades\Actividad($id_activ);
				$oF_ini = $oActividad->getF_ini();
				$f_ini_iso = $oF_ini->format('Y-m-d'); 
				$oActividadCargo->DBCarregar();
				$cCargosOk[$f_ini_iso] = $oActividadCargo;
			}
		}
		ksort($cCargosOk);
		return $cCargosOk;
	}


	/**
	 * retorna l'array d'objectes tipus CargoOAsistente
	 *
	 * @param integer id_nom
	 * @return array Una col·lecció d'arrays: id_activ,id_nom,propio,id_cargo;
	 */
	function getCargoOAsistente($iid_nom,$aWhereAct=array(),$aOperadorAct=array()) {
		$oDbl = $this->getoDbl();
		$GesAsistente = new asistentes\gestorAsistente();
	   	$cAsistentes = $GesAsistente->getActividadesDeAsistente(array('id_nom'=>$iid_nom),$aWhereAct,$aOperadorAct);
		$cCargos = $this->getActividadCargos(array('id_nom'=>$iid_nom));
		// seleccionar las actividades segun los criterios de búsqueda.
		$GesActividades = new actividades\GestorActividad();
		$aListaIds = $GesActividades->getArrayIds($aWhereAct,$aOperadorAct);
		// descarto los que no estan.
		$cActividadesOk = array();
		foreach ($cCargos as $oCargo) {
			$id_activ = $oCargo->getId_activ();
			if (in_array($id_activ,$aListaIds)) {
				$oActividad = new actividades\Actividad($id_activ);
				$oF_ini = $oActividad->getF_ini();
				$f_ini_iso = $oF_ini->format('Y-m-d'); 
				$cActividadesOk[$id_activ] = $oCargo;
			}
		}
		// lista de id_activ ordenados.
		$aAsis = array();
		foreach ($cAsistentes as $f_ini_iso=>$oAsistente) {
			$id_activ = $oAsistente->getId_activ();
			$propio = $oAsistente->getPropio();
			$aAsis[$id_activ] = array('id_activ'=>$id_activ,'id_nom'=>$iid_nom,'propio'=>$propio);
		}
		// Añado los cargos
		foreach ($cActividadesOk as $id_activ=>$oCargo) {
			$oCargo = $cActividadesOk[$id_activ];
			$id_cargo = $oCargo->getId_cargo();
			if (array_key_exists ( $id_activ , $aAsis)) { // Añado al primero el id_cargo del segundo.
				$aAsis[$id_activ]['id_cargo'] = $id_cargo;
			} else { // añado la actividad
				$aAsis[$id_activ] = array('id_activ'=>$id_activ,'id_nom'=>$iid_nom,'propio'=>'f','id_cargo'=>$id_cargo);
			}
		}
		return $aAsis;
	}

	/**
	 * retorna l'array d'objectes de tipus ActividadCargo
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus ActividadCargo
	 */
	function getActividadCargosQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oActividadCargoSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorActividadCargo.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oActividadCargo= new ActividadCargo($a_pkey);
			$oActividadCargo->setAllAtributes($aDades);
			$oActividadCargoSet->add($oActividadCargo);
		}
		return $oActividadCargoSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus ActividadCargo
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus ActividadCargo
	 */
	function getActividadCargos($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oActividadCargoSet = new core\Set();
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
		//echo "query $sQry <br>";
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorActividadCargo.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorActividadCargo.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oActividadCargo= new ActividadCargo($a_pkey);
			$oActividadCargo->setAllAtributes($aDades);
			$oActividadCargoSet->add($oActividadCargo);
		}
		return $oActividadCargoSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
