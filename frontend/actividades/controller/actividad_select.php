<?php
/**
 * Lista de actividades que cumplen con los filtros de actividad_que.
 *
 * @param    $que
 *            $status por defecto = 2
 *            $id_tipo_activ
 *            $id_ubi
 *            $periodo
 *            $year
 *            $dl_org
 *            $empiezamin
 *            $empiezamax
 *
 * Si el resultado es mas de 200, pregunta si quieres seguir.
 *
 * La logica de dominio (consultas a repositorios, permisos, tabla de
 * resultados) se ha trasladado al caso de uso
 * `src\actividades\application\ActividadSelectListado` y se consume via
 * PostRequest. Este controlador solo parsea el POST, guarda/restaura el
 * estado de `Posicion` y construye los hashes del formulario, firma `link_spec`
 * y renderiza la tabla (`Lista`).
 *
 * Migrado desde frontend/actividades/controller/actividad_select.php.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use frontend\actividades\helpers\ActividadStatusId;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\security\HashFrontSignedLink;
use frontend\shared\web\Lista;

require_once __DIR__ . '/../helpers/actividades_support.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$Qcontinuar = (string)filter_input(INPUT_POST, 'continuar');
// Solo sirve para esta pagina: importar, publicar, duplicar
$QGstack = (integer)filter_input(INPUT_POST, 'Gstack');
$stackFromPost = isset($_POST['stack']) ? (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : 0;

/** @var string|list<string> $Qid_sel */
$Qid_sel = '';
$Qscroll_id = '';
$Qque = '';
$Qlistar_asistentes = '';
$Qextendida = '';
/** @var array<string, mixed> $aGoBack */
$aGoBack = [];

// Si vengo de vuelta con el parametro 'continuar', los datos no estan en el POST,
// sino en $Posicion. Le paso la referencia del stack donde esta la informacion.
if (!empty($Qcontinuar) && $Qcontinuar === 'si' && ($QGstack !== 0)) {
    $oPosicion->goStack($QGstack);
    $Qmodo = $oPosicion->getParametro('modo');
    $Qstatus = $oPosicion->getParametro('status');
    $Qid_tipo_activ = $oPosicion->getParametro('id_tipo_activ');
    $Qfiltro_lugar = $oPosicion->getParametro('filtro_lugar');
    $Qid_ubi = $oPosicion->getParametro('id_ubi');
    $Qnom_activ = $oPosicion->getParametro('nom_activ');
    $Qperiodo = $oPosicion->getParametro('periodo');
    $Qyear = $oPosicion->getParametro('year');
    $Qdl_org = $oPosicion->getParametro('dl_org');
    $Qempiezamin = $oPosicion->getParametro('empiezamin');
    $Qempiezamax = $oPosicion->getParametro('empiezamax');
    $Qfases_on = $oPosicion->getParametro('fases_on');
    $Qfases_off = $oPosicion->getParametro('fases_off');
    $Qpublicado = $oPosicion->getParametro('publicado');
    $Qque = tessera_imprimir_string($oPosicion->getParametro('que') ?? $Qque);
    $Qlistar_asistentes = tessera_imprimir_string($oPosicion->getParametro('listar_asistentes') ?? $Qlistar_asistentes);
    $restoredSel = list_nav_id_sel_for_lista($oPosicion->getParametro('id_sel'));
    if (!list_nav_id_sel_is_empty($restoredSel)) {
        $Qid_sel = $restoredSel;
    }
    $restoredScroll = $oPosicion->getParametro('scroll_id');
    if (is_scalar($restoredScroll) && (string) $restoredScroll !== '') {
        $Qscroll_id = (string) $restoredScroll;
    }
    $oPosicion->olvidar($QGstack);

    if (empty($Qperiodo)) {
        $Qperiodo = 'actual';
    }
    $Qssfsv = '';
    $Qsasistentes = '';
    $Qsactividad = '';
    $Qsactividad2 = '';
} else {
    $Qid_sel = list_nav_id_sel_from_post();
    $Qscroll_id = list_nav_scroll_id_from_post();

    $Qque = (string)filter_input(INPUT_POST, 'que');
    $Qlistar_asistentes = (string)filter_input(INPUT_POST, 'listar_asistentes');
    $Qextendida = (string)filter_input(INPUT_POST, 'extendida');

    $Qmodo = (string)filter_input(INPUT_POST, 'modo');
    $Qstatus = (integer)filter_input(INPUT_POST, 'status');
    $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
    $Qfiltro_lugar = (string)filter_input(INPUT_POST, 'filtro_lugar');
    $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
    $Qnom_activ = (string)filter_input(INPUT_POST, 'nom_activ');
    $Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
    $Qyear = (string)filter_input(INPUT_POST, 'year');
    $Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
    $Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
    $Qfases_on = (array)filter_input(INPUT_POST, 'fases_on', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qfases_off = (array)filter_input(INPUT_POST, 'fases_off', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qpublicado = (integer)filter_input(INPUT_POST, 'publicado');

    if ($stackFromPost !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stackFromPost)) {
            $restoredSel = list_nav_id_sel_for_lista($oPosicion2->getParametro('id_sel'));
            if (!list_nav_id_sel_is_empty($restoredSel)) {
                $Qid_sel = $restoredSel;
            }
            $restoredScroll = $oPosicion2->getParametro('scroll_id');
            if (is_scalar($restoredScroll) && (string) $restoredScroll !== '') {
                $Qscroll_id = (string) $restoredScroll;
            }
            $Qmodo = tessera_imprimir_string($oPosicion2->getParametro('modo') ?? $Qmodo);
            $Qstatus = tessera_imprimir_int($oPosicion2->getParametro('status'), $Qstatus);
            $Qid_tipo_activ = tessera_imprimir_string($oPosicion2->getParametro('id_tipo_activ') ?? $Qid_tipo_activ);
            $Qfiltro_lugar = tessera_imprimir_string($oPosicion2->getParametro('filtro_lugar') ?? $Qfiltro_lugar);
            $Qid_ubi = tessera_imprimir_int($oPosicion2->getParametro('id_ubi'), $Qid_ubi);
            $Qnom_activ = tessera_imprimir_string($oPosicion2->getParametro('nom_activ') ?? $Qnom_activ);
            $Qperiodo = tessera_imprimir_string($oPosicion2->getParametro('periodo') ?? $Qperiodo);
            $Qyear = tessera_imprimir_string($oPosicion2->getParametro('year') ?? $Qyear);
            $Qdl_org = tessera_imprimir_string($oPosicion2->getParametro('dl_org') ?? $Qdl_org);
            $Qempiezamin = tessera_imprimir_string($oPosicion2->getParametro('empiezamin') ?? $Qempiezamin);
            $Qempiezamax = tessera_imprimir_string($oPosicion2->getParametro('empiezamax') ?? $Qempiezamax);
            $Qfases_on = is_array($oPosicion2->getParametro('fases_on')) ? $oPosicion2->getParametro('fases_on') : $Qfases_on;
            $Qfases_off = is_array($oPosicion2->getParametro('fases_off')) ? $oPosicion2->getParametro('fases_off') : $Qfases_off;
            $Qpublicado = tessera_imprimir_int($oPosicion2->getParametro('publicado'), $Qpublicado);
            $oPosicion2->olvidar($stackFromPost);
        }
    }

    if (empty($Qperiodo)) {
        $Qperiodo = 'actual';
    }

    $Qstatus = empty($Qstatus) ? ActividadStatusId::ACTUAL : $Qstatus;

    $Qssfsv = (string)filter_input(INPUT_POST, 'ssfsv');
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $Qsactividad2 = (string)filter_input(INPUT_POST, 'sactividad2');

    $aGoBack = [
        'modo' => $Qmodo,
        'que' => $Qque,
        'listar_asistentes' => $Qlistar_asistentes,
        'id_tipo_activ' => $Qid_tipo_activ,
        'filtro_lugar' => $Qfiltro_lugar,
        'id_ubi' => $Qid_ubi,
        'nom_activ' => $Qnom_activ,
        'periodo' => $Qperiodo,
        'year' => $Qyear,
        'dl_org' => $Qdl_org,
        'status' => $Qstatus,
        'empiezamin' => $Qempiezamin,
        'empiezamax' => $Qempiezamax,
        'fases_on' => $Qfases_on,
        'fases_off' => $Qfases_off,
        'publicado' => $Qpublicado,
        'extendida' => $Qextendida,
    ];
}

$actividadSelectReturn = list_nav_build_actividad_select_return_parametros([
    'modo' => $Qmodo,
    'que' => $Qque,
    'listar_asistentes' => $Qlistar_asistentes,
    'status' => $Qstatus,
    'id_tipo_activ' => $Qid_tipo_activ,
    'filtro_lugar' => $Qfiltro_lugar,
    'id_ubi' => $Qid_ubi,
    'nom_activ' => $Qnom_activ,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'fases_on' => $Qfases_on,
    'fases_off' => $Qfases_off,
    'publicado' => $Qpublicado,
    'extendida' => $Qextendida,
    'ssfsv' => $Qssfsv,
    'sasistentes' => $Qsasistentes,
    'sactividad' => $Qsactividad,
    'sactividad2' => $Qsactividad2,
    'id_sel' => $Qid_sel,
    'scroll_id' => $Qscroll_id,
]);

if ($stackFromPost !== 0) {
    list_nav_boot_list_page_after_stack_return($oPosicion, $stackFromPost);
} else {
    list_nav_boot_recordar($oPosicion);
}
list_nav_persist_recordar_entry($oPosicion, $actividadSelectReturn);

list_nav_persist_selection_on_list_page(
    $oPosicion,
    $Qid_sel,
    $Qscroll_id,
    $stackFromPost !== 0,
);
if ($aGoBack !== []) {
    list_nav_persist_actividad_que_parent($oPosicion, $aGoBack);
}

if (!empty($Qcontinuar) && $Qcontinuar === 'si' && ($QGstack !== 0)) {
    list_nav_persist_selection_on_list_page($oPosicion, $Qid_sel, $Qscroll_id, false);
}

$selForApi = list_nav_id_sel_is_empty($Qid_sel)
    ? []
    : (is_array($Qid_sel) ? $Qid_sel : [tessera_imprimir_string($Qid_sel)]);

// Delegamos TODA la generacion del listado al caso de uso backend.
$data = PostRequest::getDataFromUrl('/src/actividades/actividad_select_datos', [
    'continuar' => $Qcontinuar,
    'modo' => $Qmodo,
    'status' => $Qstatus,
    'id_tipo_activ' => $Qid_tipo_activ,
    'filtro_lugar' => $Qfiltro_lugar,
    'id_ubi' => $Qid_ubi,
    'nom_activ' => $Qnom_activ,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'fases_on' => $Qfases_on,
    'fases_off' => $Qfases_off,
    'publicado' => $Qpublicado,
    'ssfsv' => $Qssfsv,
    'sasistentes' => $Qsasistentes,
    'sactividad' => $Qsactividad,
    'sactividad2' => $Qsactividad2,
    'sel' => $selForApi,
    'scroll_id' => $Qscroll_id,
    'stack_go' => $oPosicion->getStack(),
]);

// Confirmación si hay demasiadas filas: el API devuelve `advertencia_demasiadas` (specs); firmamos aquí.
if (!empty($data['advertencia_demasiadas']) && is_array($data['advertencia_demasiadas'])) {
    $ad = $data['advertencia_demasiadas'];
    $go_avant = HashFrontSignedLink::tryFromSpec($ad['continuar_link_spec'] ?? null);
    $go_atras = HashFrontSignedLink::tryFromSpec($ad['volver_link_spec'] ?? null);
    $numActiv = tessera_imprimir_int($ad['num_actividades'] ?? 0);
    $html_advertencia = '<h2>' . sprintf(_("son %s actividades a mostrar. ¿Seguro que quiere continuar?."), $numActiv) . '</h2>';
    $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_avant . "') value=" . _("continuar") . ">";
    $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_atras . "') value=" . _("volver") . ">";
    echo $html_advertencia;
    die();
}

$a_valores = actividades_lista_valores_from_payload($data['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('actividad_select');
$oTabla->setCabeceras(actividades_lista_cabeceras($data['a_cabeceras'] ?? []));
$oTabla->setBotones(actividades_lista_botones($data['a_botones'] ?? []));
$oTabla->setDatos($a_valores);
$html_tabla = $oTabla->mostrar_tabla();
unset($data['a_cabeceras'], $data['a_botones'], $data['a_valores']);
$resultado = tessera_imprimir_string($data['resultado'] ?? '');
$perm_nueva = (bool) ($data['perm_nueva'] ?? false);
$mod = tessera_imprimir_string($data['mod'] ?? '');
$obj_pau = tessera_imprimir_string($data['obj_pau'] ?? 'Actividad');
$aTiposActiv = actividades_lista_datos($data['aTiposActiv'] ?? []);
$extendida = (bool) ($data['extendida'] ?? false);
$id_tipo_activ_efectivo = tessera_imprimir_string($data['id_tipo_activ_efectivo'] ?? $Qid_tipo_activ);

$oHash = new HashFront();
$oHash->setUrl('frontend/actividades/controller/actividad_que.php');
$a_camposHidden = [
    'modo' => $Qmodo,
    'id_tipo_activ' => $id_tipo_activ_efectivo,
    'extendida' => $extendida,
    'id_ubi' => $Qid_ubi,
    'nom_activ' => $Qnom_activ,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'status' => $Qstatus,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'filtro_lugar' => $Qfiltro_lugar,
    'fases_on' => $Qfases_on,
    'fases_off' => $Qfases_off,
];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setCamposNo('extendida!modo!id_tipo_activ!id_ubi!nom_activ!periodo!year!dl_org!status!empiezamin!empiezamax!filtro_lugar!fases_on!fases_off');

$oHashSel = new HashFront();
$oHashSel->setCamposForm('!mod!queSel!id_dossier');
$oHashSel->setcamposNo('continuar!sel!scroll_id!fases_on!fases_off!id_sel');
$a_camposHiddenSel = [
    'obj_pau' => $obj_pau,
    'pau' => 'a',
    'permiso' => '3',
    'Gstack' => $oPosicion->getStack(),
];
$oHashSel->setArraycamposHidden($a_camposHiddenSel);

$id_sel_value = is_array($Qid_sel)
    ? tessera_imprimir_string($Qid_sel[0] ?? '')
    : tessera_imprimir_string($Qid_sel);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHashSel' => $oHashSel,
    'aTiposActiv' => $aTiposActiv,
    'resultado' => $resultado,
    'perm_nueva' => $perm_nueva,
    'mod' => $mod,
    'html_tabla' => $html_tabla,
    'id_sel_value' => $id_sel_value,
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('actividad_select.phtml', $a_campos);
