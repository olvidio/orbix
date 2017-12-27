<?php
use asignaturas\model as asignaturas;
use notas\model as notas;
use personas\model as personas;

/**
* Esta página sirve para generar un cuadro con las asignaturas pendientes de todos los alumnos.
*
*
*@package	delegacion
*@subpackage	estudios
*@author	Daniel Serrabou
*@since		24/10/12.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// Asignaturas posibles:
$GesAsignaturas = new asignaturas\GestorAsignatura();
$aWhere=array();
$aOperador=array();
$aWhere['status'] = 't';
$aWhere['id_nivel'] = '1100,2500';
$aOperador['id_nivel']='BETWEEN';
$aWhere['_ordre'] = 'id_nivel';
$cAsignaturas = $GesAsignaturas->getAsignaturas($aWhere,$aOperador);

$a_cabeceras = array();
$a_cabeceras[0] = _("n/a");
$a_cabeceras[1] = _("stgr");
$a_cabeceras[2] = _("centro");
$a_cabeceras[3] = _("apellidos, nombre");
$a=3;
foreach ($cAsignaturas as $oAsignatura) {
	$a++;
	$a_cabeceras[$a] = $oAsignatura->getNombre_corto();
}
//todas
$cAsignaturasTodas = $GesAsignaturas->getAsignaturas(array('_ordre'=>'id_asignatura'));
foreach ($cAsignaturasTodas as $oAsignatura) {
	$id_asignatura = $oAsignatura->getId_asignatura();
	$a_Asig_status[$id_asignatura] = $oAsignatura->getStatus();
	$a_Asig_nivel[$id_asignatura] = $oAsignatura->getId_nivel();
}


$aWhere=array();
$aOperador=array();
$aWhere['situacion'] = 'A';
$aWhere['stgr'] = 'b|c1|c2';
$aWhere['_ordre'] = 'stgr,apellido1,nom';

$aOperador['stgr'] = '~';

$GesPersonas = new personas\GestorPersonaDl();
$cPersonas = $GesPersonas->getPersonasDl($aWhere,$aOperador);
$p=0;
$GesNotas = new notas\GestorPersonaNotaDl();
foreach ($cPersonas as $oPersona) {
	$p++;
	$id_nom = $oPersona->getId_nom();
	$id_tabla = $oPersona->getId_tabla();
	$ap_nom = $oPersona->getApellidosNombre();
	$stgr = $oPersona->getStgr();
	$centro = $oPersona->getCentro_o_dl();

	$a_valores[$p][1] = $id_tabla;
	$a_valores[$p][2] = $stgr;
	$a_valores[$p][3] = $centro;
	$a_valores[$p][4] = $ap_nom;

	// Asignaturas cursadas:
	/*
	$aWhere=array();
	$aOperador=array();
	$aWhere['id_nom'] = $id_nom;
	$aWhere['id_nivel'] = '1100,2500';
	$aOperador['id_nivel']='BETWEEN';
	*/
	$cNotas = $GesNotas->getPersonaNotasSuperadas($id_nom,'t');
	$aAprobadas=array();
	foreach ($cNotas as $oPersonaNota) {
		//extract($oPersonaNota->getTot());
		$id_asignatura = $oPersonaNota->getId_asignatura();
		$id_nivel = $oPersonaNota->getId_nivel();
		$id_situacion = $oPersonaNota->getId_situacion();

		/* 
		 * No se porqué está aqui. Aunque la asignatura esté fuera de uso,
		 * si está aprobada cuenta no?
		 */
		//if ($a_Asig_status[$id_asignatura] != 't') continue;
	
		
		if ($id_asignatura > 3000) {
			$id_nivel_asig = $id_nivel;
		} else {
			$id_nivel_asig = $a_Asig_nivel[$id_asignatura];
		}
		$n=$id_nivel_asig;
		$oNota = new notas\Nota($id_situacion);
		$aAprobadas[$n]['nota']= ($oNota->getSuperada() == 't')? '' : 2;
	}


	$a=4;
	foreach ($cAsignaturas as $oAsignatura) {
		$a++;
		$id_nivel = $oAsignatura->getId_nivel();
		if (!empty($aAprobadas[$id_nivel])) {
			$a_valores[$p][$a] = $aAprobadas[$id_nivel]['nota'];
		} else {
			$a_valores[$p][$a] = 1;
		}
	}
}
$oTabla = new web\Lista();
$oTabla->setId_tabla("pendientes");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla_html();
?>

