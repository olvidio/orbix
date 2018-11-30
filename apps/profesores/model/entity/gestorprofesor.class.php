<?php
namespace profesores\model\entity;
use core;
use web;
use asignaturas\model\entity as asignaturas;
use personas\model\entity as personas;
/**
 * GestorProfesor
 *
 * Classe per gestionar la llista d'objectes de la clase Profesor
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

class GestorProfesor Extends core\ClaseGestor {
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
		$this->setNomTabla('d_profesor_stgr');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	/**
	 * retorna un array
	 * Els posibles professors per una asignatura
	 *
	 * @return array amd dues Llistes: departamento y ampliacion
	 */
	function getListaProfesoresAsignatura($id_asignatura) {
		$oAsignatura = new asignaturas\Asignatura($id_asignatura);
		$id_sector = $oAsignatura->getId_sector();
		$oSector =new asignaturas\Sector($id_sector);
		$id_departamento =$oSector->getId_departamento();
		// Profesores departamento
		$aProfesoresDepartamento = $this->getListaProfesoresDepartamento($id_departamento);
		//profesor ampliacion
		$gesProfesoresAmpliacion = new GestorProfesorAmpliacion();
		$aProfesoresAmpliacion = $gesProfesoresAmpliacion->getListaProfesoresAsignatura($id_asignatura);
		
		$Opciones['departamento'] = $aProfesoresDepartamento;
		$Opciones['ampliacion'] = $aProfesoresAmpliacion;

		return $Opciones;
	
	}
	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles professors per una asignatura
	 *
	 * @return web\Desplegable
	 */
	function getDesplProfesoresAsignatura($id_asignatura) {
		$oAsignatura = new asignaturas\Asignatura($id_asignatura);
		$id_sector = $oAsignatura->getId_sector();
		$oSector =new asignaturas\Sector($id_sector);
		$id_departamento =$oSector->getId_departamento();
		// Profesores departamento
		$aProfesoresDepartamento = $this->getListaProfesoresDepartamento($id_departamento);
		//profesor ampliacion
		$gesProfesoresAmpliacion = new GestorProfesorAmpliacion();
		$aProfesoresAmpliacion = $gesProfesoresAmpliacion->getListaProfesoresAsignatura($id_asignatura);
		
		$AllOpciones = $aProfesoresDepartamento + array("----------") + $aProfesoresAmpliacion;

		return new web\Desplegable('',$AllOpciones,'',true);
	
	}
	/**
	 * retorna un objecte del tipus Array
	 * Els posibles professors per un departament
	 *
	 * @return array Una Llista
	 */
	function getListaProfesoresDepartamento($id_departamento) {
		$gesProfesores = $this->getProfesores(array('id_departamento'=>$id_departamento,'f_cese'=>''),array('f_cese'=>'IS NULL'));
		$aProfesores = array();
		$aAp1 = array();
		$aAp2 = array();
		$aNom = array();
		foreach ($gesProfesores as $oProfesor) {
			$id_nom = $oProfesor->getId_nom();
			$oPersonaDl = new personas\PersonaDl($id_nom);
			// comprobar situación
			$situacion = $oPersonaDl->getSituacion();
			if ($situacion != 'A') { continue; }
			$ap_nom = $oPersonaDl->getApellidosNombre();
			$aProfesores[] = array('id_nom'=>$id_nom,'ap_nom'=>$ap_nom);
			$aAp1[] = $oPersonaDl->getApellido1();
			$aAp2[] = $oPersonaDl->getApellido2();
			$aNom[] = $oPersonaDl->getNom();
		}
		$multisort_args = array(); 
		$multisort_args[] = $aAp1;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = $aAp2;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = $aNom;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = &$aProfesores;   // finally add the source array, by reference
		call_user_func_array("array_multisort", $multisort_args);
		$aOpciones=array();
		foreach ($aProfesores as $aClave) {
			$clave=$aClave['id_nom'];
			$val=$aClave['ap_nom'];
			$aOpciones[$clave]=$val;
		}
		return $aOpciones;
	}
	/**
	 * retorna un objecte del tipus array
	 * Els posibles professors de paso (personaEx + PersonaIN)
	 *
	 * @return array Una Llista
	 */
	function getListaProfesoresPub() {
		$gesPersonaPub = new personas\GestorPersonaPub();
		$cPersonasPub = $gesPersonaPub->getPersonas(array('profesor_stgr' => 't'));

		$aProfesores = array();
		$aAp1 = array();
		$aAp2 = array();
		$aNom = array();
		foreach ($cPersonasPub as $oPersona) {
			$id_nom = $oPersona->getId_nom();
			// comprobar situación
			$situacion = $oPersona->getSituacion();
			if ($situacion != 'A') { continue; }
			$ap_nom = $oPersona->getApellidosNombre();
			$aProfesores[] = array('id_nom'=>$id_nom,'ap_nom'=>$ap_nom);
			$aAp1[] = $oPersona->getApellido1();
			$aAp2[] = $oPersona->getApellido2();
			$aNom[] = $oPersona->getNom();
		}
		$multisort_args = array(); 
		$multisort_args[] = $aAp1;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = $aAp2;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = $aNom;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = &$aProfesores;   // finally add the source array, by reference
		call_user_func_array("array_multisort", $multisort_args);
		$aOpciones=array();
		foreach ($aProfesores as $aClave) {
			$clave=$aClave['id_nom'];
			$val=$aClave['ap_nom'];
			$aOpciones[$clave]=$val;
		}
		return $aOpciones;
	}
	/**
	 * retorna un objecte del tipus array
	 * Els posibles professors de la dl
	 *
	 * @return array Una Llista
	 */
	function getListaProfesoresDl() {

		$gesProfesores = $this->getProfesores(array('f_cese'=>''),array('f_cese'=>'IS NULL'));
		$aProfesores = array();
		$aAp1 = array();
		$aAp2 = array();
		$aNom = array();
		foreach ($gesProfesores as $oProfesor) {
			$id_nom = $oProfesor->getId_nom();
			$oPersonaDl = new personas\PersonaDl($id_nom);
			// comprobar situación
			$situacion = $oPersonaDl->getSituacion();
			if ($situacion != 'A') { continue; }
			$ap_nom = $oPersonaDl->getApellidosNombre();
			$aProfesores[] = array('id_nom'=>$id_nom,'ap_nom'=>$ap_nom);
			$aAp1[] = $oPersonaDl->getApellido1();
			$aAp2[] = $oPersonaDl->getApellido2();
			$aNom[] = $oPersonaDl->getNom();
		}
		$multisort_args = array(); 
		$multisort_args[] = $aAp1;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = $aAp2;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = $aNom;
		$multisort_args[] = SORT_ASC;
		$multisort_args[] = SORT_STRING;
		$multisort_args[] = &$aProfesores;   // finally add the source array, by reference
		call_user_func_array("array_multisort", $multisort_args);
		$aOpciones=array();
		foreach ($aProfesores as $aClave) {
			$clave=$aClave['id_nom'];
			$val=$aClave['ap_nom'];
			$aOpciones[$clave]=$val;
		}
		return $aOpciones;
	}
	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles professors
	 *
	 * @return array Una Llista
	 */
	function getListaProfesores() {

		$gesProfesores = $this->getProfesores(array('f_cese'=>''),array('f_cese'=>'IS NULL'));
		$aProfesores = array();
		foreach ($gesProfesores as $oProfesor) {
			$id_nom = $oProfesor->getId_nom();
			$oPersonaDl = new personas\PersonaDl($id_nom);
			// comprobar situación
			$situacion = $oPersonaDl->getSituacion();
			if ($situacion != 'A') { continue; }
			$ap_nom = $oPersonaDl->getApellidosNombre();
			$aProfesores[$id_nom] = $ap_nom;
		}
		uasort($aProfesores,'core\strsinacentocmp');
		return new web\Desplegable('',$aProfesores,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus Profesor
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Profesor
	 */
	function getProfesoresQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oProfesorSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorProfesor.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item'],
							'id_nom' => $aDades['id_nom'],
							'id_departamento' => $aDades['id_departamento']);
			$oProfesor= new Profesor($a_pkey);
			$oProfesor->setAllAtributes($aDades);
			$oProfesorSet->add($oProfesor);
		}
		return $oProfesorSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Profesor
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Profesor
	 */
	function getProfesores($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oProfesorSet = new core\Set();
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
			$sClauError = 'GestorProfesor.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorProfesor.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item'],
							'id_nom' => $aDades['id_nom'],
							'id_departamento' => $aDades['id_departamento']);
			$oProfesor= new Profesor($a_pkey);
			$oProfesor->setAllAtributes($aDades);
			$oProfesorSet->add($oProfesor);
		}
		return $oProfesorSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
