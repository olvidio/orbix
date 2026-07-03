<?php

use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Pantalla principal del modulo `actividadplazas`.
 *
 * Muestra el cuadro calendario de plazas (totales, concedidas y pedidas)
 * por dl del grupo de estudios. Obtiene los datos via
 * `/src/actividadplazas/gestion_plazas_data` y monta la `frontend\shared\web\TablaEditable`
 * cuyas ediciones inline se envian a `/src/actividadplazas/gestion_plazas_update`
 * (text/plain, contrato de TablaEditable).
 *
 * Migrada desde `apps/actividadplazas/controller/gestion_plazas.php` +
 * `apps/actividadplazas/controller/gestion_plazas_ajax.php` siguiendo
 * `refactor.md`. Sin `use src\...`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;
use frontend\shared\web\TablaEditable;
use frontend\shared\FrontBootstrap;
use frontend\actividadplazas\helpers\ActividadplazasPostInput;
use frontend\actividadplazas\helpers\ActividadplazasPayload;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$stackFromPost = ActividadplazasPostInput::stackFromPost() ?? 0;
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$campos = ActividadplazasPostInput::gestionPlazasRequestCampos($oPosicion, $stackFromPost);
$Qscroll_id = \frontend\shared\helpers\ListNavSupport::scrollIdFromPost();

$payload = ActividadplazasPayload::gestionPlazasFromPayload(
    PostRequest::getDataFromUrl('/src/actividadplazas/gestion_plazas_data', $campos)
);

$Qid_tipo_activ = $payload['id_tipo_activ'];
$Qyear = $payload['year'];
$Qperiodo = $payload['periodo'];
$Qempiezamin = $payload['empiezamin'];
$Qempiezamax = $payload['empiezamax'];
$extendida = $payload['extendida'];

$aValores = $payload['a_valores'];
if ($Qscroll_id !== '') {
    $aValores['scroll_id'] = $Qscroll_id;
}

$oPosicion->setParametros([
    'id_tipo_activ' => $Qid_tipo_activ,
    'year' => $Qyear,
    'periodo' => $Qperiodo,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'sasistentes' => $campos['sasistentes'],
    'sactividad' => $campos['sactividad'],
    'sactividad2' => $campos['sactividad2'],
    'extendida' => $extendida ? '1' : '',
    'scroll_id' => $Qscroll_id,
], 0);

$apiBase = AppUrlConfig::getApiBaseUrl();
$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($apiBase . '/src/actividadplazas/gestion_plazas_update');
$oHashUpdate->setCamposForm('data!colName');
$UpdateUrl = $apiBase . '/src/actividadplazas/gestion_plazas_update' . $oHashUpdate->linkSinVal();

$oTabla = new TablaEditable();
$oTabla->setId_tabla('gestion_plazas');
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($payload['a_cabeceras']);
$oTabla->setBotones([]);
$oTabla->setDatos($aValores);

$boton = "<input type='button' value='" . _("buscar") . "' onclick='fnjs_buscar()' >";
$aOpciones = [
    'tot_any' => _("todo el año"),
    'trimestre_1' => _("primer trimestre"),
    'trimestre_2' => _("segundo trimestre"),
    'trimestre_3' => _("tercer trimestre"),
    'trimestre_4' => _("cuarto trimestre"),
    'separador' => '---------',
    'curso_ca' => _("curso ca"),
    'curso_crt' => _("curso crt"),
    'separador1' => '---------',
    'otro' => _("otro"),
];
$titulo = \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("periodo de selección de actividades"));
$titulo .= " (" . _("en estado actual") . ")";
$oFormP = new PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo($titulo);
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setEmpiezaMax($Qempiezamax);
$oFormP->setBoton($boton);

$oHash = new HashFront();
$oHash->setCamposForm(
    'empiezamax!empiezamin!iactividad_val!iasistentes_val!id_tipo_activ!periodo!year!sasistentes!sactividad!sactividad2!extendida'
);
$oHash->setCamposNo('!refresh!scroll_id');
$oHash->setArraycamposHidden([
    'id_tipo_activ' => $Qid_tipo_activ,
    'sasistentes' => $campos['sasistentes'],
    'sactividad' => $campos['sactividad'],
    'sactividad2' => $campos['sactividad2'],
    'extendida' => $extendida ? '1' : '',
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'oFormP' => $oFormP,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('gestion_plazas.phtml', $a_campos);
