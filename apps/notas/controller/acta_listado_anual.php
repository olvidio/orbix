<?php
/*
En diciembre, hay que mandar las actas del curso anterior a Comisión, ordenadas según el orden académico; es decir, primero Introducción a la filosofía, luego Philosophia naturæ I, etc...
Hasta ahora, yo me manejaba con Access y tenía una manera de saber qué acta iba después de otra según este orden. Ahora no lo sé. Hay cerca de cien actas cada curso; es un engorro ir buscando qué acta va después de otra. Sugiero hacer una consulta o algo que te indique el orden de las actas de cada dl según ese criterio.
 */




use asignaturas\model\entity as asignaturas;
use core\ConfigGlobal;
use notas\model\entity as notas;
use web\Hash;
use web\Lista;
use web\Posicion;
use function core\curso_est;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	
// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$aWhere = array();
$aOperador = array();

$mes=date('m');
if ($mes>9) { $any=date('Y')+1; } else { $any=date("Y"); }
$inicurs_ca=curso_est("inicio",$any);
$fincurs_ca=curso_est("fin",$any);
$txt_curso = "$inicurs_ca - $fincurs_ca";

$aWhere['f_acta'] = "'$inicurs_ca','$fincurs_ca'";
$aOperador['f_acta'] = 'BETWEEN';

$titulo=ucfirst(sprintf(_("lista de actas del curso %s"),$txt_curso));
$GesActas = new notas\GestorActaDl();

$cActas = $GesActas->getActas($aWhere,$aOperador);

$i=0;
//$aActas = array();
foreach ($cActas as $oActa) {
	$i++;
	$acta=$oActa->getActa();
	$f_acta=$oActa->getF_acta();
	$id_asignatura=$oActa->getId_asignatura();

	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto = $oAsignatura->getNombre_corto();
	// puede ser una asignatura fantasma (que no exista)
	if ($nombre_corto === NULL) {
		$nombre_corto = "???";
		$id_nivel = 0;
	} else {
		$id_nivel = $oAsignatura->getId_nivel();
	}

	$aActas[$i]['id_nivel']=$id_nivel;
	$aActas[$i]['acta']=$acta;
	$aActas[$i]['f_acta']=$f_acta;
	$aActas[$i]['nombre_corto']=$nombre_corto;

	// fecha en ISO
	$oFecha = new web\DateTimeLocal();
	$oFecha->setFromFormat('j/m/Y', $f_acta);
	
	$aNivel[$i] = $id_nivel;
	$aFecha[$i] = $oFecha->format('Y-m-d');
}

array_multisort($aNivel, SORT_NUMERIC,
				$aFecha, SORT_NUMERIC,
				$aActas);


$a_campos = ['aActas' => $aActas,
			];

$oView = new core\View('notas/controller');
echo $oView->render('acta_listado_anual.phtml',$a_campos);