<?php
use asignaturas\model\entity as asignaturas;
use notas\model\entity as notas;
use personas\model\entity as personas;

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
	// Busco fin_bienio, cuadrienio
	$cFin = $GesNotas->getPersonaNotas(array('id_nom'=>$id_nom, 'id_nivel'=>9990),array('id_nivel' => '>'));
	$fin_bienio = false;
	$fin_cuadrienio = false;
	foreach ($cFin as $oPersonaNota) {
		$id_asignatura = $oPersonaNota->getId_asignatura();
		if ($id_asignatura == 9999) {
			$fin_bienio = true;
		}
		if ($id_asignatura == 9998) {
			$fin_cuadrienio = true;
		}
	}
	
	$cNotas = $GesNotas->getPersonaNotasSuperadas($id_nom,'t');
	$aAprobadas=array();
	foreach ($cNotas as $oPersonaNota) {
		//extract($oPersonaNota->getTot());
		$id_asignatura = $oPersonaNota->getId_asignatura();
		$id_nivel = $oPersonaNota->getId_nivel();
		$id_situacion = $oPersonaNota->getId_situacion();

		if ($id_asignatura > 3000) {
			$id_nivel_asig = $id_nivel;
		} else {
			$id_nivel_asig = $a_Asig_nivel[$id_asignatura];
		}
		$n=$id_nivel_asig;
		$oNota = new notas\Nota($id_situacion);
		$aAprobadas[$n]['nota']= ($oNota->getSuperada() == 't')? '' : 2;
	}


	$a=4; // 1: id_tabla, 2: stgr, 3: centro, 4: ap_nom.
	foreach ($cAsignaturas as $oAsignatura) {
		$a++;
		$id_nivel = $oAsignatura->getId_nivel();
		if (!empty($aAprobadas[$id_nivel])) {
			$a_valores[$p][$a] = $aAprobadas[$id_nivel]['nota'];
		} else {
			$a_valores[$p][$a] = 1;
		}
		// borro las pendientes si ya está aprobado el bienio o cuadrienio
		if ($fin_bienio && $id_nivel < 2000) {
			$a_valores[$p][$a] = '';
		}
		if ($fin_cuadrienio) {
			$a_valores[$p][$a] = '';
		}
	}
}
$oTabla = new web\Lista();
$oTabla->setId_tabla("pendientes");
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

// ------------------- html --------------

?>
<p>
	1: <?= _("pendiente") ?>
	2: <?= _("cursada") ?>
</p>
<br>
<?= $oTabla->mostrar_tabla_html(); ?>

