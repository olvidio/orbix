<?php
/**
 * Pantalla `prevision_asistentes`: tabla editable con las plazas
 * previstas por actividad.
 *
 * Construye el filtro de periodo y la `TablaEditable`. La edición
 * inline llama a `/src/casas/ingreso_plazas_previstas_update`.
 *
 * Migrada desde `apps/casas/controller/prevision_asistentes.php` +
 * `prevision_asistentes_ajax.php` siguiendo `refactor.md`.
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Periodo;
use frontend\shared\web\PeriodoQue;
use frontend\shared\web\TablaEditable;

use function frontend\shared\helpers\strtoupper_dlb;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$Qmi_of = (string)filter_input(INPUT_POST, 'mi_of');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$oPeriodo = Periodo::conCalendarioDesdeBackend();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);

$inicioIso = $oPeriodo->getF_ini_iso();
$finIso = $oPeriodo->getF_fin_iso();

$campos = [
    'mi_of' => $Qmi_of,
    'periodo' => $Qperiodo,
    'inicio_iso' => $inicioIso,
    'fin_iso' => $finIso,
];
$data = PostRequest::getDataFromUrl('/src/casas/prevision_asistentes_data', $campos);
$payload = is_array($data) ? $data : [];

$permitido = (bool)($payload['permitido'] ?? true);
if (!$permitido) {
    exit((string)_("No tiene actividades asignadas a su oficina"));
}

$a_cabeceras = $payload['a_cabeceras'] ?? [];
$a_valores = $payload['a_valores'] ?? [];
$mi_of = (string)($payload['mi_of'] ?? '');
$inicio_local = (string)($payload['inicio_local'] ?? '');
$fin_local = (string)($payload['fin_local'] ?? '');

$aOpciones = [
    'tot_any' => _('todo el año'),
    'trimestre_1' => _('primer trimestre'),
    'trimestre_2' => _('segundo trimestre'),
    'trimestre_3' => _('tercer trimestre'),
    'trimestre_4' => _('cuarto trimestre'),
    'separador' => '-------',
    'otro' => _('otro...'),
];
$oFormP = new PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(strtoupper_dlb(_("período del listado del año próximo")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($Qperiodo);
$oFormP->setDesplAnysOpcion_sel($Qyear);
$oFormP->setEmpiezaMax($Qempiezamax);
$oFormP->setEmpiezaMin($Qempiezamin);
$oFormP->setBoton("<input type='button' value='" . _("buscar") . "' onclick='fnjs_buscar()' >");

if ($_SESSION['oConfig']->getGestionCalendario() === 'central') {
    $aOficinas = [
        'sm' => 'sm',
        'nax' => 'nax',
        'agd' => 'agd',
        'sg' => 'sg',
        'sr' => 'sr',
    ];
    $oDesplOficinas = new Desplegable();
    $oDesplOficinas->setNombre('mi_of');
    $oDesplOficinas->setOpciones($aOficinas);
    $oDesplOficinas->setOpcion_sel($mi_of);
    $oDesplOficinas->setBlanco(1);
    $oFormP->setAntes(_('oficina') . '</td><td>' . $oDesplOficinas->desplegable());
}

$web = AppUrlConfig::getPublicAppBaseUrl();

$oTabla = new TablaEditable();
$oTabla->setId_tabla('prevision_asistentes');

$oHashUpdate = new HashFront();
$oHashUpdate->setUrl($web . '/src/casas/ingreso_plazas_previstas_update');
$oHashUpdate->setCamposForm('');
$oTabla->setUpdateUrl($web . '/src/casas/ingreso_plazas_previstas_update' . $oHashUpdate->linkSinVal());
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$oHashFiltro = new HashFront();
$oHashFiltro->setCamposForm('empiezamax!empiezamin!extendida!iactividad_val!iasistentes_val!mi_of!periodo!year');
$oHashFiltro->setCamposNo('!refresh');

if (empty($mi_of)) {
    $titulo = ucfirst((string)_("listado de actividades"));
} else {
    $titulo = ucfirst(sprintf((string)_("listado de actividades de %s"), $mi_of));
}

$titulo .= ' ' . sprintf((string)_("entre %s y %s"), $inicio_local, $fin_local);

$a_campos = [
    'oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'oTabla' => $oTabla,
    'oFormP' => $oFormP,
    'oHash' => $oHashFiltro,
    'url_filtro' => $web . '/frontend/casas/controller/prevision_asistentes.php',
];

$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar('prevision_asistentes.phtml', $a_campos);
