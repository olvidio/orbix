<?php

use frontend\actividades\helpers\ActividadesPayload;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

/**
 * Pantalla que muestra el listado de actividades filtradas. Se usa como
 * `action` del formulario de `actividad_que` (cuando que=list_activ/list_activ_compl)
 * y de `lista_activ_que` (sr/sg).
 *
 * Los datos y el HTML de la tabla se obtienen via PostRequest al endpoint
 * backend /src/actividades/lista_activ_datos (JSON con `a_cabeceras` / `a_valores` y
 * `link_spec` en celdas). Este controlador firma enlaces, monta `Lista` y renderiza.
 *
 * Migrado desde src/actividades/infrastructure/ui/http/controllers/lista_activ.php
 * (que servia HTML directamente, violando la separacion de capas).
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use frontend\actividades\helpers\ActividadStatusId;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;

use frontend\shared\web\Periodo;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qstatus = (integer)filter_input(INPUT_POST, 'status');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qfiltro_lugar = (string)filter_input(INPUT_POST, 'filtro_lugar');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

if (empty($Qperiodo)) {
    $Qperiodo = 'actual';
}

$Qc_activ = (array)filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qasist = (array)filter_input(INPUT_POST, 'asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qseccion = (array)filter_input(INPUT_POST, 'seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (empty($Qstatus)) {
    $Qa_status = (array)filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qstatus = empty($Qa_status) ? ActividadStatusId::ACTUAL : $Qa_status;
}

$Qssfsv = (string)filter_input(INPUT_POST, 'ssfsv');
$Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qsnom_tipo = (string)filter_input(INPUT_POST, 'snom_tipo');
$Qtit_list_grupo = (string)filter_input(INPUT_POST, 'tit_list_grupo');

$aGoBack = [
    'que' => $Qque,
    'status' => $Qstatus,
    'id_tipo_activ' => $Qid_tipo_activ,
    'filtro_lugar' => $Qfiltro_lugar,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'c_activ' => $Qc_activ,
    'asist' => $Qasist,
    'seccion' => $Qseccion,
    'ssfsv' => $Qssfsv,
    'sasistentes' => $Qsasistentes,
    'sactividad' => $Qsactividad,
    'snom_tipo' => $Qsnom_tipo,
    'tit_list_grupo' => $Qtit_list_grupo,
    'id_sel' => $Qid_sel,
    'scroll_id' => $Qscroll_id,
];

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ListNavSupport::buildListaActivReturnParametros($aGoBack),
);

ListNavSupport::syncListaActivParent($oPosicion, $aGoBack);

$tituloPrevio = '';
if ($Qque === 'list_active_inv_sg' || $Qque === 'list_activ_sr') {
    // Viene del formulario que_lista_activ_sg.php / que_lista_activ_sr.
    $tituloPrevio = (string)filter_input(INPUT_POST, 'titulo');
    if ($tituloPrevio === '') {
        $tituloPrevio = $Qtit_list_grupo;
    }
}

// periodo por defecto
if (empty($Qempiezamin)) {
    $Qperiodo = 'curso';

    $oPeriodo = new Periodo();
    $oPeriodo->setPeriodo('curso');

    $Qempiezamin = $oPeriodo->getF_ini()->getFromLocal();
    $Qempiezamax = $oPeriodo->getF_fin()->getFromLocal();
}

$data = PostRequest::getDataFromUrl('/src/actividades/lista_activ_datos', [
    'que' => $Qque,
    'status' => $Qstatus,
    'id_tipo_activ' => $Qid_tipo_activ,
    'filtro_lugar' => $Qfiltro_lugar,
    'id_ubi' => $Qid_ubi,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'c_activ' => $Qc_activ,
    'asist' => $Qasist,
    'seccion' => $Qseccion,
    'ssfsv' => $Qssfsv,
    'sasistentes' => $Qsasistentes,
    'sactividad' => $Qsactividad,
    'snom_tipo' => $Qsnom_tipo,
    'titulo' => $tituloPrevio,
]);

$a_valores = ActividadesPayload::listaValoresFromPayload($data['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('lista_activ');
$oTabla->setCabeceras(ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? []));
$oTabla->setBotones([]);
$oTabla->setDatos($a_valores);
$html_tabla = $oTabla->mostrar_tabla();

$titulo = PayloadCoercion::string($data['titulo'] ?? '');

$a_campos = [
    'oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'html_tabla' => $html_tabla,
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('lista_activ.phtml', $a_campos);
