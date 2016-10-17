<?php
namespace profesores\model;
use core;
use web;
use personas\model as personas;
use asistentes\model as asistentes;
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

class GestorProfesorActividad Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles professors de la dl 
	 * más los asistentes a actividades de otras dl o de paso que sean profesores
	 *
	 * @param array id_activ de las actividades seleccionadas (solo para profesores de paso).
	 * @return array Una Llista
	 */
	function getListaProfesoresActividad($aId_activ=array()) {

		// Profesores de la Dl
		$gesProfesoresDl = new GestorProfesor();
		$aProfesoresDl = $gesProfesoresDl->getListaProfesoresDl();
		// asistentes de otras dl que son profesores
		//$gesProfesoresOtrasDl = new GestorAsistentesIn(); 

		// asistentes de paso que son profesores
		$gesAsistentesIn = new asistentes\GestorAsistenteIn();
		$aProfesoresEx = array();
		$aAp1 = array();
		$aAp2 = array();
		$aNom = array();
		foreach($gesAsistentesIn->getListaAsistentesDistintos($aId_activ) as $id_nom) {
			$oPersona = personas\Persona::NewPersona($id_nom);
			$obj_persona = get_class($oPersona);
			$obj_persona = str_replace("personas\\model\\",'',$obj_persona);
			if ($obj_persona == 'PersonaDl') continue;
			// solo puede ser PersonaEx o PersonaIN;
			$profesor_stgr = $oPersona->getProfesor_stgr();
			if ($profesor_stgr == false) continue;

			$ap_nom=$oPersona->getApellidosNombre();
			//$ctr_dl=$oPersona->getCentro_o_dl();

			$aProfesoresEx[] = array('id_nom'=>$id_nom,'ap_nom'=>$ap_nom);
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
		$multisort_args[] = &$aProfesoresEx;   // finally add the source array, by reference
		call_user_func_array("array_multisort", $multisort_args);
		$aOpciones=array();
		foreach ($aProfesoresEx as $aClave) {
			$clave=$aClave['id_nom'];
			$val=$aClave['ap_nom'];
			$aOpciones[$clave]=$val;
		}
			
		$AllOpciones = $aOpciones + array("----------") + $aProfesoresDl;

		return new web\Desplegable('',$AllOpciones,'',true);
	}


	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
