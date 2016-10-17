<?php
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
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************
	require_once ('classes/personas/p_n_agd_gestor.class');
	require_once ('classes/personas/e_notas_gestor.class');
	require_once ('classes/personas/e_notas_situacion.class');
	require_once ('classes/activ-personas/xa_asignaturas_gestor.class');
	require_once ('classes/web/listas.class');

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//include_once(ConfigGlobal::$dir_programas.'/func_web.php');  

// Asignaturas posibles:
$GesAsignaturas = new GestorAsignatura();
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


$aWhere=array();
$aOperador=array();
$aWhere['fichero'] = 'A';
$aWhere['stgr'] = 'b|c1|c2';
$aWhere['_ordre'] = 'stgr,apellido1,nom';

$aOperador['stgr'] = '~';

$GesPersonas = new GestorPersonaNAgd();
$cPersonas = $GesPersonas->getPersonasNAgd($aWhere,$aOperador);
$p=0;
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
	$GesNotas = new GestorPersonaNota();
	$aWhere=array();
	$aOperador=array();
	$aWhere['id_nom'] = $id_nom;
	$aWhere['id_nivel'] = '1100,2500';
	$aOperador['id_nivel']='BETWEEN';
	$cNotas = $GesNotas->getPersonaNotas($aWhere,$aOperador);
	$aAprobadas=array();
	foreach ($cNotas as $oPersonaNota) {
		extract($oPersonaNota->getTot());
		$oAsig = new Asignatura($id_asignatura);
		if ($oAsig->getStatus() != 't') continue;
		if ($id_asignatura > 3000) {
			$id_nivel_asig = $id_nivel;
		} else {
			$id_nivel_asig = $oAsig->getId_nivel();
		}
		$n=$id_nivel_asig;
		/*
		$aAprobadas[$n]['id_nivel_asig']= $id_nivel_asig;
		$aAprobadas[$n]['id_nivel']= $id_nivel;
		$aAprobadas[$n]['id_asignatura']= $id_asignatura;
		$aAprobadas[$n]['nombre_corto']= $oAsig->getNombre_corto();
		$aAprobadas[$n]['fecha']= $f_acta;
		*/
		$oNota = new Nota($id_situacion);
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
$oTabla = new Lista();
$oTabla->setId_tabla("pendientes");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla_html();
?>

