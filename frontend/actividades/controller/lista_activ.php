<?php
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

require_once __DIR__ . '/../helpers/actividades_support.php';
use frontend\shared\web\Periodo;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qcontinuar = (string)filter_input(INPUT_POST, 'continuar');
$QGstack = (integer)filter_input(INPUT_POST, 'Gstack');
$stackRaw = filter_input(INPUT_POST, 'stack', FILTER_VALIDATE_INT);
$stack = is_int($stackRaw) ? $stackRaw : 0;

// Si vuelvo con el parametro 'continuar', los datos no estan en el POST sino
// en `oPosicion`. Me paso la referencia del stack donde esta la informacion.
if (!empty($Qcontinuar) && $Qcontinuar === 'si' && ($QGstack !== 0)) {
    $oPosicion->goStack($QGstack);

    $Qque = $oPosicion->getParametro('que');
    $Qid_tipo_activ = $oPosicion->getParametro('id_tipo_activ');
    $Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
    $Qid_ubi = $oPosicion->getParametro('id_ubi');
    $Qperiodo = $oPosicion->getParametro('periodo');
    $Qyear = $oPosicion->getParametro('year');
    $Qdl_org = $oPosicion->getParametro('dl_org');
    $Qempiezamin = $oPosicion->getParametro('empiezamin');
    $Qempiezamax = $oPosicion->getParametro('empiezamax');
    $Qstatus = $oPosicion->getParametro('status');
    $Qc_activ = $oPosicion->getParametro('c_activ');
    $Qasist = $oPosicion->getParametro('asist');
    $Qseccion = $oPosicion->getParametro('seccion');

    $oPosicion->olvidar($QGstack);

    $Qssfsv = '';
    $Qsasistentes = '';
    $Qsactividad = '';
    $Qsnom_tipo = '';
} else {
    // Si vengo por medio de Posicion, borro la ultima.
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $oPosicion2->olvidar($stack);
        }
    }
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
    ];
    $oPosicion->setParametros($aGoBack, 1);
}

$tituloPrevio = '';
if ($Qque === 'list_active_inv_sg' || $Qque === 'list_activ_sr') {
    // Viene del formulario que_lista_activ_sg.php / que_lista_activ_sr.
    $tituloPrevio = (string)filter_input(INPUT_POST, 'titulo');
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

$a_valores = actividades_lista_valores_from_payload($data['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('lista_activ');
$oTabla->setCabeceras(actividades_lista_cabeceras($data['a_cabeceras'] ?? []));
$oTabla->setBotones([]);
$oTabla->setDatos($a_valores);
$html_tabla = $oTabla->mostrar_tabla();

$titulo = tessera_imprimir_string($data['titulo'] ?? '');

$a_campos = [
    'oPosicion' => $oPosicion,
    'titulo' => $titulo,
    'html_tabla' => $html_tabla,
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('lista_activ.phtml', $a_campos);
