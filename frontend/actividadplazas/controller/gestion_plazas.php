<?php
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
use function frontend\shared\helpers\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$campos = [
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'sactividad2' => (string)filter_input(INPUT_POST, 'sactividad2'),
];

$data = PostRequest::getDataFromUrl('/src/actividadplazas/gestion_plazas_data', $campos);
$payload = is_array($data) ? $data : [];

$a_cabeceras = $payload['a_cabeceras'] ?? [];
$a_valores = $payload['a_valores'] ?? [];
$Qid_tipo_activ = (string)($payload['id_tipo_activ'] ?? '');
$Qyear = (string)($payload['year'] ?? '');
$Qperiodo = (string)($payload['periodo'] ?? '');
$Qempiezamin = (string)($payload['empiezamin'] ?? '');
$Qempiezamax = (string)($payload['empiezamax'] ?? '');
$extendida = (bool)($payload['extendida'] ?? false);

$apiBase = AppUrlConfig::getApiBaseUrl();
$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($apiBase . '/src/actividadplazas/gestion_plazas_update');
$oHashUpdate->setCamposForm('data!colName');
$UpdateUrl = $apiBase . '/src/actividadplazas/gestion_plazas_update' . $oHashUpdate->linkSinVal();

$oTabla = new TablaEditable();
$oTabla->setId_tabla('gestion_plazas');
$oTabla->setUpdateUrl($UpdateUrl);
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones([]);
$oTabla->setDatos($a_valores);

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
$titulo = strtoupper_dlb(_("periodo de selección de actividades"));
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
$CamposForm = 'empiezamax!empiezamin!iactividad_val!iasistentes_val!id_tipo_activ!periodo!year';
if ($extendida) {
    $CamposForm .= '!extendida';
}
$oHash->setCamposForm($CamposForm);
$oHash->setCamposNo('!refresh');
$oHash->setArraycamposHidden([
    'id_tipo_activ' => $Qid_tipo_activ,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oTabla' => $oTabla,
    'oFormP' => $oFormP,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\actividadplazas\\controller');
$oView->renderizar('gestion_plazas.phtml', $a_campos);
