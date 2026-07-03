<?php

namespace frontend\planning\controller;

use frontend\planning\helpers\PlanningPayload;
use frontend\planning\support\PlanningRenderer;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Periodo;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\FuncTablasSupport;

/**
 * Planning (calendario) de actividades de un grupo de casas en un
 * periodo dado. Se invoca por AJAX desde `planning_casa_select.phtml`.
 *
 * Devuelve HTML plano (tabla grande); el cliente usa `fnjs_extract_html_from_ajax_body`.
 */
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();

try {
    $Qmodelo = (int)filter_input(INPUT_POST, 'modelo');
    $Qpropuesta_calendario = (string)filter_input(INPUT_POST, 'propuesta_calendario');
    $Qyear = (int)filter_input(INPUT_POST, 'year');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

    $oPeriodo = Periodo::conCalendarioDesdeBackend(throwOnError: true);
    $oPeriodo->setDefaultAny('next');
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $oIniPlanning = $oPeriodo->getF_ini();
    $oFinPlanning = $oPeriodo->getF_fin();

    $Qdd = 3;
    $mod = 0;
    $nueva = 0;
    if (\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpropuesta_calendario)) {
        $mod = 1;
        $nueva = 1;
    }

    $doble = $Qmodelo !== 2 ? 1 : 0;
    $interval = (int)$oFinPlanning->diff($oIniPlanning)->format('%m');
    if ($interval < 2) {
        $doble = 0;
    }

    $cabecera_title = ucfirst(_("casas"));
    $cabecera = ucfirst(_("calendario de casas"));

    $payloadVer = $_POST;
    $payloadVer['f_ini_iso'] = (string)$oPeriodo->getF_ini_iso();
    $payloadVer['f_fin_iso'] = (string)$oPeriodo->getF_fin_iso();

    $d = PostRequest::getDataFromUrl('/src/planning/planning_casa_ver_data', $payloadVer, false);
    if (isset($d['error']) && is_string($d['error']) && $d['error'] !== '') {
        echo PostRequest::stripInternalCallProvenance($d['error']);
        exit;
    }

    $a_actividades = PlanningPayload::actividadesMap($d['a_actividades'] ?? null);
    $casa_periodos_por_ubi = PlanningPayload::casaPeriodosPorUbi($d['casa_periodos_por_ubi'] ?? null);

    $goLeyenda = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/planning/controller/leyenda.php?' . http_build_query(['id_item' => 1]));

    $estilos = PlanningPayload::calendarioEstilos();
    $css = $estilos['css'];

    $oPlanning = new PlanningRenderer();
    $oPlanning->setColorColumnaUno($estilos['colorColumnaUno']);
    $oPlanning->setColorColumnaDos($estilos['colorColumnaDos']);
    $oPlanning->setColorColumnaDomingo($estilos['colorColumnaDomingo']);
    $oPlanning->setTable_border($estilos['table_border']);
    $oPlanning->setDd($Qdd);
    $oPlanning->setCabecera($cabecera);
    $oPlanning->setInicio($oIniPlanning);
    $oPlanning->setFin($oFinPlanning);
    $oPlanning->setActividades($a_actividades);
    $oPlanning->setMod($mod);
    $oPlanning->setNueva($nueva);
    $oPlanning->setDoble($doble);
    $oPlanning->setCasaPeriodosPorUbi($casa_periodos_por_ubi);

    $a_campos = [
        'oPlanning' => $oPlanning,
        'goLeyenda' => $goLeyenda,
        'cabecera_title' => $cabecera_title,
        'css' => $css,
    ];

    $oView = new ViewNewPhtml('frontend\planning\controller');
    $oView->renderizar('planning_casa_ver.phtml', $a_campos);
} catch (\Throwable $e) {
    echo '<div class="alert">' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
}
