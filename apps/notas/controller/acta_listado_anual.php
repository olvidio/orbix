<?php
/*
En diciembre, hay que mandar las actas del curso anterior a Comisión, ordenadas según el orden académico; es decir, primero Introducción a la filosofía, luego Philosophia naturæ I, etc...
Hasta ahora, yo me manejaba con Access y tenía una manera de saber qué acta iba después de otra según este orden. Ahora no lo sé. Hay cerca de cien actas cada curso; es un engorro ir buscando qué acta va después de otra. Sugiero hacer una consulta o algo que te indique el orden de las actas de cada dl según ese criterio.
 */


use asignaturas\model\entity\Asignatura;
use core\ViewPhtml;
use notas\model\entity\GestorActaDl;
use web\DateTimeLocal;
use web\Hash;
use web\Periodo;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');


// valores por defeccto
if (empty($Qperiodo)) {
    $Qperiodo = 'curso_ca';
}

// periodo.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

$aWhere = [];
$aOperador = [];
$aWhere['f_acta'] = "'$inicioIso','$finIso'";
$aOperador['f_acta'] = 'BETWEEN';

$GesActas = new GestorActaDl();
$cActas = $GesActas->getActas($aWhere, $aOperador);

$i = 0;
$aActas = [];
$aFecha = [];
$aNivel = [];
foreach ($cActas as $oActa) {
    $i++;
    $acta = $oActa->getActa();
    $oF_acta = $oActa->getF_acta();
    $f_acta = $oF_acta->getFromLocal();
    $id_asignatura = $oActa->getId_asignatura();

    $oAsignatura = new Asignatura($id_asignatura);
    $nombre_corto = $oAsignatura->getNombre_corto();
    // puede ser una asignatura fantasma (que no exista)
    if ($nombre_corto === NULL) {
        $nombre_corto = "???";
        $id_nivel = 0;
    } else {
        $id_nivel = $oAsignatura->getId_nivel();
    }

    $aActas[$i]['id_nivel'] = $id_nivel;
    $aActas[$i]['acta'] = $acta;
    $aActas[$i]['f_acta'] = $f_acta;
    $aActas[$i]['nombre_corto'] = $nombre_corto;

    $aNivel[$i] = $id_nivel;
    // fecha en ISO
    $aFecha[$i] = $oF_acta->format('Y-m-d');
}

if (!empty($aActas)) {
    array_multisort($aNivel, SORT_NUMERIC,
        $aFecha, SORT_NUMERIC,
        $aActas);
}


//Periodo
$boton = "<input type='button' value='" . _("buscar") . "' onclick='fnjs_buscar()' >";
$aOpciones = array(
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'curso_ca' => _("curso ca"),
    'separador1' => '---------',
    'otro' => _("otro")
);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(core\strtoupper_dlb(_("periodo de selección")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setBoton($boton);

$oHashPeriodo = new Hash();
$oHashPeriodo->setCamposForm('empiezamax!empiezamin!periodo!year!iactividad_val!iasistentes_val');
$oHashPeriodo->setCamposNo('!refresh');
$a_camposHiddenP = [];
$oHashPeriodo->setArraycamposHidden($a_camposHiddenP);

// Convertir las fechas inicio y fin a formato local:
$oF_qini = new DateTimeLocal($inicioIso);
$QinicioLocal = $oF_qini->getFromLocal();
$oF_qfin = new DateTimeLocal($finIso);
$QfinLocal = $oF_qfin->getFromLocal();
$titulo = _(sprintf(_("Lista de actas en el periodo: %s - %s."), $QinicioLocal, $QfinLocal));

$a_campos = ['aActas' => $aActas,
    'titulo' => $titulo,
    'oFormP' => $oFormP,
    'oHashPeriodo' => $oHashPeriodo,
];

$oView = new ViewPhtml('notas/controller');
$oView->renderizar('acta_listado_anual.phtml', $a_campos);
