<?php

use frontend\actividadestudios\helpers\CaPosiblesPayload;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Periodo;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * Esta página sirve para calcular los créditos cursables para cada alumno en cada ca.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        5/3/03.
 *
 */

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$obj_pau = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'obj_pau'));
$Qgrupo_estudios = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'grupo_estudios'));
$Qtexto = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'texto'));
$Qref = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'ref'));
$Qidca = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'idca'));
$Qca_estudios = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'ca_estudios'));
$Qca_repaso = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'ca_repaso'));
$Qca_todos = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'ca_todos'));

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$navState = [];
foreach ([
    'obj_pau', 'grupo_estudios', 'texto', 'ref', 'idca', 'ca_estudios', 'ca_repaso', 'ca_todos',
    'id_ctr_agd', 'id_ctr_n', 'na', 'periodo', 'year', 'empiezamin', 'empiezamax',
] as $key) {
    $raw = filter_input(INPUT_POST, $key);
    if (is_scalar($raw) && (string) $raw !== '') {
        $navState[$key] = (string) $raw;
    }
}

if (empty($a_sel)) {
    $Qid_ctr_agd = (integer)filter_input(INPUT_POST, 'id_ctr_agd');
    $Qid_ctr_n = (integer)filter_input(INPUT_POST, 'id_ctr_n');
    $Qna = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'na'));
    $Qyear = (integer)filter_input(INPUT_POST, 'year');
    $Qperiodo = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'periodo'));
    $Qempiezamin = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'empiezamin'));
    $Qempiezamax = \frontend\shared\helpers\PayloadCoercion::string(filter_input(INPUT_POST, 'empiezamax'));

    if (empty($Qid_ctr_agd) && empty($Qid_ctr_n)) {
        $msg_txt = _("debe seleccionar un centro o grupo de centros");
        exit($msg_txt);
    }

    if (empty($Qperiodo)) {
        $Qperiodo = 'curso_ca';
    }

    $navState = array_merge($navState, [
        'id_ctr_agd' => $Qid_ctr_agd,
        'id_ctr_n' => $Qid_ctr_n,
        'na' => $Qna,
        'periodo' => $Qperiodo,
        'year' => $Qyear,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
        'grupo_estudios' => $Qgrupo_estudios,
        'ref' => $Qref,
        'ca_estudios' => $Qca_estudios,
        'ca_repaso' => $Qca_repaso,
        'ca_todos' => $Qca_todos,
    ]);
}

$navState = ListNavSupport::mergeSelectionIntoReturnParametros($navState, $Qid_sel, $Qscroll_id);

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);

$queState = [];
foreach ([
    'na', 'id_ctr_n', 'id_ctr_agd', 'periodo', 'year', 'empiezamin', 'empiezamax',
    'ref', 'grupo_estudios', 'ca_estudios', 'ca_repaso', 'ca_todos',
    'iasistentes_val', 'actividad_val',
] as $key) {
    if (!array_key_exists($key, $navState)) {
        continue;
    }
    $queState[$key] = $navState[$key];
}
$parent = $oPosicion->nav()->peek(1);
if ($queState !== [] && $parent !== null && str_contains((string) ($parent['url'] ?? ''), 'ca_posibles_que.php')) {
    ListNavSupport::syncNavStateAt($oPosicion, 1, $queState);
}

$data = PostRequest::getDataFromUrl(
    '/src/actividadestudios/ca_posibles_data',
    PostRequest::requestPayloadForHash(),
    false,
);
if (!empty($data['error'])) {
    echo PostRequest::stripInternalCallProvenance(\frontend\shared\helpers\PayloadCoercion::string($data['error']));
    return;
}
$caPosibles = CaPosiblesPayload::fromPayload(ActividadestudiosRenderSupport::stringKeyRow($data));

if ($caPosibles['modo'] === 'lista') {
    if ($caPosibles['msg_txt'] !== '') {
        echo "<div class='no_print'>" . $caPosibles['msg_txt'] . '</div>';
    }
    $a_campos = ['oPosicion' => $oPosicion,
        'msg_txt' => $caPosibles['msg_txt'],
        'titulo' => $caPosibles['titulo'],
        'stgr' => $caPosibles['stgr'],
        'aActividades' => $caPosibles['aActividades'],
        'pagina' => $caPosibles['pagina'],
    ];
    $oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
    $oView->renderizar('ca_posibles_lista.phtml', $a_campos);
} else {
    if ($caPosibles['msg_txt'] !== '') {
        echo "<div class='no_print'>" . $caPosibles['msg_txt'] . '</div>';
    }
    if ($caPosibles['filas'] === []) {
        echo '<p class="comentario">' . _('No hay datos para mostrar.') . '</p>';
    }
    foreach ($caPosibles['filas'] as $fila) {
        $a_campos = ['oPosicion' => $oPosicion,
            'msg_txt' => $fila['msg_txt'],
            'texto' => $fila['texto'],
            'nc_bienio' => $fila['nc_bienio'],
            'nc_cuadrienio1' => $fila['nc_cuadrienio1'],
            'nc_cuadrienio2' => $fila['nc_cuadrienio2'],
            'nc_cuadrienio' => $fila['nc_cuadrienio'],
            'nc_repaso' => $fila['nc_repaso'],
            'nc_ce' => $fila['nc_ce'],
            'nc_otros' => $fila['nc_otros'],
            'stgr' => $fila['stgr'],
            'ctr' => $fila['ctr'],
            'ref' => $fila['ref'],
            'height' => $fila['height'],
            'cPersonas' => $fila['cPersonas'],
            'aActividades' => $fila['aActividades'],
        ];
        $oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
        $oView->renderizar('ca_posibles_cuadro.phtml', $a_campos);
    }
}
