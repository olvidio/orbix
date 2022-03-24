<?php 

use profesores\model\entity\GestorProfesor;
use profesores\model\entity\GestorProfesorDocenciaStgr;
use asignaturas\model\entity\GestorAsignatura;
use web\Lista;
use core\ConfigGlobal;
use personas\model\entity\PersonaDl;
use profesores\model\entity\GestorCongreso;
use profesores\model\entity\GestorProfesorCongreso;
use profesores\model\entity\ProfesorCongreso;
use profesores\model\entity\Congreso;
use core\DatosCampo;

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//$gesAsignaturas = new GestorAsignatura();




/*
$a_asignaturas = [];
foreach ($cAsignaturas as $oAsignatura) {
	$a_asignaturas[$oAsignatura->getId_asignatura()] = $oAsignatura->getNombre_corto();
}
*/
if (ConfigGlobal::mi_ambito() === 'rstgr') {
	$a_cabeceras[1] = _("dl");
}

$a_cabeceras[2] = _("Apellidos, nombre");
$a_cabeceras[3] = _("tipo");
$a_cabeceras[4] = _("lugar");
$a_cabeceras[5] = _("inicio");
$a_cabeceras[6] = _("fin");
$a_cabeceras[7] = _("organiza");

$a_valores = [];
$gesProfesor = new GestorProfesor();
$gesProfesorCongresos = new GestorProfesorCongreso();

//$a_profesores = $gesProfesorCongresos->getProfesorCongresos();

$a_nomProfesor = $gesProfesor->getListaProfesoresConDl();

//$gesProfesorDocenciaStgr = new GestorProfesorDocenciaStgr();
$p = 0;
foreach ($a_nomProfesor as $id_nom => $aClave) {
	$ap_nom = $aClave['ap_nom'];
	$dl = $aClave['dl'];
	$cProfesorCongreso = $gesProfesorCongresos->getProfesorCongresos(['id_nom' =>$id_nom]);
	
	$oProfesorCongreso = new ProfesorCongreso();
	
	$tiposCong = new DatosCampo();
	$tiposCong =$oProfesorCongreso->getDatosTipo();
	
	$a_tiposCong=$tiposCong->getLista();
	
	
	
	//$cProfesorDocenciaStgr = $gesProfesorDocenciaStgr->getProfesorDocenciasStgr(['id_nom' => $id_nom]);
	foreach ($cProfesorCongreso as $oProfesorCongreso ) {
		$p++;
		//$oProfesorCongreso = new ProfesorCongreso();
		
		
		$tipo= $a_tiposCong[$oProfesorCongreso->getTipo()];
		$lugar=$oProfesorCongreso->getLugar();
		$inicio=$oProfesorCongreso->getF_ini()->getFromLocal();
		$fin = $oProfesorCongreso->getF_fin()->getFromLocal();
		$organiza = $oProfesorCongreso->getOrganiza();
	   
			
	
		if (ConfigGlobal::mi_ambito() === 'rstgr') {
			$a_valores[$p][1]=$dl;
		}
		$a_valores[$p][2]=$ap_nom;
		$a_valores[$p][3]=$tipo;
		$a_valores[$p][4]=$lugar;
		$a_valores[$p][5]=$inicio;
		$a_valores[$p][6]=$fin;
		$a_valores[$p][7]=$organiza;
	}
}

$oTabla = new Lista();
$oTabla->setId_tabla('tabla_congreso');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

echo $oTabla->mostrar_tabla();
