<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Periodo;
use frontend\shared\FrontBootstrap;

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

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

$restored = list_nav_restore_selection_from_stack_post();

/** @var string|list<string> $Qid_sel */
$Qid_sel = !list_nav_id_sel_is_empty($restored['id_sel']) ? $restored['id_sel'] : list_nav_id_sel_from_post();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : list_nav_scroll_id_from_post();
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(($aGoBack ?? list_nav_build_return_parametros_from_post()), $Qid_sel, $Qscroll_id));


$obj_pau = tessera_imprimir_string(filter_input(INPUT_POST, 'obj_pau'));
$Qgrupo_estudios = tessera_imprimir_string(filter_input(INPUT_POST, 'grupo_estudios'));
$Qtexto = tessera_imprimir_string(filter_input(INPUT_POST, 'texto'));
$Qref = tessera_imprimir_string(filter_input(INPUT_POST, 'ref'));
$Qidca = tessera_imprimir_string(filter_input(INPUT_POST, 'idca'));
$Qca_estudios = tessera_imprimir_string(filter_input(INPUT_POST, 'ca_estudios'));
$Qca_repaso = tessera_imprimir_string(filter_input(INPUT_POST, 'ca_repaso'));
$Qca_todos = tessera_imprimir_string(filter_input(INPUT_POST, 'ca_todos'));

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (empty($a_sel)) {
    $Qid_ctr_agd = (integer)filter_input(INPUT_POST, 'id_ctr_agd');
    $Qid_ctr_n = (integer)filter_input(INPUT_POST, 'id_ctr_n');
    $Qna = tessera_imprimir_string(filter_input(INPUT_POST, 'na'));
    $Qyear = (integer)filter_input(INPUT_POST, 'year');
    $Qperiodo = tessera_imprimir_string(filter_input(INPUT_POST, 'periodo'));
    $Qempiezamin = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamin'));
    $Qempiezamax = tessera_imprimir_string(filter_input(INPUT_POST, 'empiezamax'));

    if (empty($Qid_ctr_agd) && empty($Qid_ctr_n)) {
        $msg_txt = _("debe seleccionar un centro o grupo de centros");
        exit($msg_txt);
    }

    if (empty($Qperiodo)) {
        $Qperiodo = 'curso_ca';
    }

    $oPeriodo = Periodo::conCalendarioDesdeBackend();
    $oPeriodo->setDefaultAny('next');
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $aGoBack = array(
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
    );
    $oPosicion->setParametros($aGoBack, 1);
}

$data = PostRequest::getDataFromUrl(
    '/src/actividadestudios/ca_posibles_data',
    PostRequest::requestPayloadForHash(),
    false,
);
if (!empty($data['error'])) {
    echo PostRequest::stripInternalCallProvenance(tessera_imprimir_string($data['error']));
    return;
}
$caPosibles = actividadestudios_ca_posibles_from_payload(actividadestudios_post_data($data));

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
