<?php
/**
 * Controlador encargado de 
 * 
 */
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
use profesores\model\entity as profesores;
use personas\model\entity as personas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)  filter_input(INPUT_POST, 'que');
$Qid_nom = (integer)  filter_input(INPUT_POST, 'id_nom');
$Qid_asignatura = (integer)  filter_input(INPUT_POST, 'id_asignatura');

switch ($Qque) {
	case 'posibles_opcionales':
		// todas las opcionales 
		$aWhere=array();
		$aOperador=array();
		$aWhere['status']='t';
		$aWhere['id_nivel']='3000,5000';
		$aOperador['id_nivel']='BETWEEN';
		$aWhere['_ordre']='nombre_corto';
		$GesAsignaturas = new asignaturas\GestorAsignatura();
		$cOpcionales = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);
		// Asignaturas opcionales superadas
		$GesNotas = new notas\GestorNota();
		$cSuperadas = $GesNotas->getNotas(array('superada'=>'t'));
		$cond='';
		$c=0;
		foreach ($cSuperadas as $Nota) {
			if ($c >0 ) $cond.='|';
			$c++;
			$cond.=$Nota->getId_situacion();
		}
		$aWhere=array();
		$aOperador=array();
		$aWhere['id_situacion']=$cond;
		$aOperador['id_situacion']='~';
		$aWhere['id_nom']=$Qid_nom;
		$aWhere['id_asignatura']=3000;
		$aOperador['id_asignatura']='>';
		$GesPersonaNotas = new notas\GestorPersonaNota();
		$cAsignaturasOpSuperadas = $GesPersonaNotas->getPersonaNotas($aWhere,$aOperador);
		$aOpSuperadas=array();
		foreach($cAsignaturasOpSuperadas as $oAsignatura) {
			$id_asignatura = $oAsignatura->getId_asignatura();
			$aOpSuperadas[$id_asignatura]=$id_asignatura;
		}
		// asignaturas opcionales posibles
		$aFaltan=array();
		foreach ($cOpcionales as $oAsignatura) {
			$id_asignatura = $oAsignatura->getId_asignatura();
			$nombre_corto = $oAsignatura->getNombre_corto();
			if (array_key_exists($id_asignatura,$aOpSuperadas)) continue;
			$aFaltan[$id_asignatura]=$nombre_corto;
		}

		$oDesplPosiblesOpcionales = new web\Desplegable();
		$oDesplPosiblesOpcionales->setNombre('id_asignatura');
		$oDesplPosiblesOpcionales->setOpciones($aFaltan);
//		$oDesplPosiblesOpcionales->setOpcion_sel($Qid_asignatura);
		$oDesplPosiblesOpcionales->setBlanco(1);
		echo $oDesplPosiblesOpcionales->desplegable();
		break;

	case 'posibles_preceptores':
		$GesProfes = new profesores\GestorProfesor();
		$cProfesores= $GesProfes->getProfesores();
		$aProfesores=array();
		$msg_err = '';
		foreach ($cProfesores as $oProfesor) {
			$id_nom=$oProfesor->getId_nom();
			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
				continue;
			}
			$ap_nom=$oPersona->getApellidosNombre();
			$aProfesores[$id_nom]=$ap_nom;
		}
		uasort($aProfesores,'core\strsinacentocmp');
		
		$oDesplProfesores = new web\Desplegable();
		$oDesplProfesores->setOpciones($aProfesores);
		$oDesplProfesores->setBlanco(1);
		$oDesplProfesores->setNombre('id_preceptor');
		echo $oDesplProfesores->desplegable();
	break;

}