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

$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');

//periodo
if (empty($Qperiodo) || $Qperiodo == 'otro') {
	$any=  core\ConfigGlobal::any_final_curs('est');
	$Qempiezamin=core\curso_est("inicio",$any,"est");
	$Qempiezamax=core\curso_est("fin",$any,"est");
	$Qperiodo = 'curso_ca';
	$inicio = empty($Qinicio)? $Qempiezamin : $Qinicio;
	$fin = empty($Qfin)? $Qempiezamax : $Qfin;
} else {
	$oPeriodo = new web\Periodo();
	$any=empty($Qyear)? date('Y')+1 : $Qyear;
	$oPeriodo->setAny($any);
	$oPeriodo->setPeriodo($Qperiodo);
	$inicio = $oPeriodo->getF_ini();
	$fin = $oPeriodo->getF_fin();
}

$aWhere = array();
$aOperador = array();

$aWhere['f_acta'] = "'$inicio','$fin'";
$aOperador['f_acta'] = 'BETWEEN';

$titulo = _(sprintf("Lista de actas en el periodo: %s - %s.",$inicio,$fin)); 
$GesActas = new notas\GestorActaDl();

$cActas = $GesActas->getActas($aWhere,$aOperador);

$i=0;
$aActas = array();
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

if (!empty($aActas)) {
	array_multisort($aNivel, SORT_NUMERIC,
				$aFecha, SORT_NUMERIC,
				$aActas);
}


//Periodo
$boton = "<input type='button' value='"._("buscar")."' onclick='fnjs_buscar()' >";
$aOpciones =  array(
					'tot_any' => _("todo el año"),
					'trimestre_1'=>_("primer trimestre"),
					'trimestre_2'=>_("segundo trimestre"),
					'trimestre_3'=>_("tercer trimestre"),
					'trimestre_4'=>_("cuarto trimestre"),
					'separador'=>'---------',
					'curso_ca'=>_("curso ca"),
					'separador1'=>'---------',
					'otro'=>_("otro")
					);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(core\strtoupper_dlb(_("periodo de selección")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setBoton($boton);

$oHashPeriodo = new web\Hash();
$oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHashPeriodo->setCamposNo('!refresh');
$a_camposHiddenP = array(
		);
$oHashPeriodo->setArraycamposHidden($a_camposHiddenP);


$a_campos = ['aActas' => $aActas,
			'titulo' => $titulo,
			'oFormP' => $oFormP,
			'oHashPeriodo' => $oHashPeriodo,
			];

$oView = new core\View('notas/controller');
echo $oView->render('acta_listado_anual.phtml',$a_campos);
