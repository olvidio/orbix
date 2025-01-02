<?php

// INICIO Cabecera global de URL de controlador *********************************

use web\Hash;
use zonassacd\model\entity\GestorZona;
use misas\domain\entity\EncargoDia;
use web\DateTimeLocal;
use web\Desplegable;
use web\PeriodoQue;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
$aOpciones = array(
    'proxima_semana' => _("próxima semana de lunes a domingo"),
    'proximo_mes' => _("próximo mes natural"),
    'otro' => _("otro")
);

$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(core\strtoupper_dlb(_("seleccionar un periodo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel('proxima_semana');
$oFormP->setisDesplAnysVisible(FALSE);

$ohoy = new DateTimeLocal(date('Y-m-d'));
$shoy = $ohoy ->format('d/m/Y');

$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$oGestorZona = new GestorZona();
$oDesplZonas = $oGestorZona->getListaZonas();
$oDesplZonas->setNombre('id_zona');
$oDesplZonas->setAction('fnjs_ver_cuadricula_zona()');

$a_Estados = array(
    EncargoDia::STATUS_PROPUESTA=>'propuesta',
    EncargoDia::STATUS_COMUNICADO_SACD=>'comunicado sacerdotes',
    EncargoDia::STATUS_COMUNICADO_CTR=>'comunicado centros',
);

$oDesplEstados = new Desplegable();
$oDesplEstados->setOpciones($a_Estados);
$oDesplEstados->setNombre('estado');
$oDesplEstados->setAction('fnjs_ver_cuadricula_zona()');

$a_Orden = array(
    'orden' => 'orden',
    'prioridad' => 'prioridad',
    'desc_enc' => 'alfabético',
);

$oDesplOrden = new Desplegable();
$oDesplOrden->setOpciones($a_Orden);
$oDesplOrden->setNombre('orden');
$oDesplOrden->setAction('fnjs_ver_cuadricula_zona()');

$url_nuevo_status = 'apps/misas/controller/nuevo_status.php';
$oHashNuevoStatus = new Hash();
$oHashNuevoStatus->setUrl($url_nuevo_status);
$oHashNuevoStatus->setCamposForm('id_zona!periodo!estado!empiezamin!empiezamax');
$h_nuevo_status = $oHashNuevoStatus->linkSinVal();

$url_ver_cuadricula_zona = 'apps/misas/controller/ver_cuadricula_zona.php';
$oHashZonaStatus = new Hash();
$oHashZonaStatus->setUrl($url_ver_cuadricula_zona);
$oHashZonaStatus->setCamposForm('id_zona!periodo!empiezamin!empiezamax!orden!tipo_plantilla');
$h_zona_status = $oHashZonaStatus->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplZonas' => $oDesplZonas,
    'oDesplEstados' => $oDesplEstados,
    'oDesplOrden' => $oDesplOrden,
    'oFormP' => $oFormP,
    'url_nuevo_status' => $url_nuevo_status,
    'h_nuevo_status' => $h_nuevo_status,
    'url_ver_cuadricula_zona' => $url_ver_cuadricula_zona,
    'h_zona_status' => $h_zona_status,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('cambiar_status.html.twig', $a_campos);