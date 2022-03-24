<?php 

use profesores\model\entity\GestorProfesor;
use profesores\model\entity\GestorProfesorDocenciaStgr;
use asignaturas\model\entity\GestorAsignatura;
use web\Lista;
use core\ConfigGlobal;
use personas\model\entity\PersonaDl;

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

$gesAsignaturas = new GestorAsignatura();
$cAsignaturas = $gesAsignaturas->getAsignaturas();
$a_asignaturas = [];
foreach ($cAsignaturas as $oAsignatura) {
	$a_asignaturas[$oAsignatura->getId_asignatura()] = $oAsignatura->getNombre_corto();
}

if (ConfigGlobal::mi_ambito() === 'rstgr') {
	$a_cabeceras[] = _("dl");
}

$a_cabeceras[] = _("Apellidos, nombre");
$a_cabeceras[] = _("incio curso");
$a_cabeceras[] = _("asignatura");
$a_cabeceras[] = _("modo");
$a_cabeceras[] = _("acta");

$a_valores = [];

$gesProfesor = new GestorProfesor();
$a_nomProfesor = $gesProfesor->getListaProfesoresConDl();

$gesProfesorDocenciaStgr = new GestorProfesorDocenciaStgr();
$p = 0;
foreach ($a_nomProfesor as $id_nom => $aClave) {
	$ap_nom = $aClave['ap_nom'];
	$dl = $aClave['dl'];
	$cProfesorDocenciaStgr = $gesProfesorDocenciaStgr->getProfesorDocenciasStgr(['id_nom' => $id_nom]);
	foreach ($cProfesorDocenciaStgr as $oProfesorDocenciaStgr) {
		$p++;
		$id_asignatura = $oProfesorDocenciaStgr->getId_asignatura();
		$nom_asignatura = $a_asignaturas[$id_asignatura];
		
		$array_tipo = $oProfesorDocenciaStgr->getDatosTipo()->getLista();
		$tipo = $oProfesorDocenciaStgr->getTipo();
		$modo = empty($tipo)? '' : $array_tipo[$tipo];
		
		$curso_inicio = $oProfesorDocenciaStgr->getCurso_inicio();
		$acta = $oProfesorDocenciaStgr->getActa();
	
		if (ConfigGlobal::mi_ambito() === 'rstgr') {
			$a_valores[$p][0]=$dl;
		}
		$a_valores[$p][1]=$ap_nom;
		$a_valores[$p][2]=$curso_inicio;
		$a_valores[$p][3]=$nom_asignatura;
		$a_valores[$p][4]=$modo;
		$a_valores[$p][5]=$acta;
	}
}

$oTabla = new Lista();
$oTabla->setId_tabla('tabla_docencia');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

echo $oTabla->mostrar_tabla();
