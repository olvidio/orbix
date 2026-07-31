<?php

use frontend\actividades\helpers\ActividadesPayload;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

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

use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$Qcontinuar = (string)filter_input(INPUT_POST, 'continuar');
/** @var string|list<string> $Qid_sel */
$Qid_sel = \frontend\shared\helpers\ListNavSupport::idSelFromPost();
$Qscroll_id = \frontend\shared\helpers\ListNavSupport::scrollIdFromPost();

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

$actividadSelectReturn = \frontend\shared\helpers\ListNavSupport::buildActividadSelectReturnParametros([
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

$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $actividadSelectReturn,
);

$oPosicion->nav()->updateStateAt(1, \frontend\shared\helpers\ListNavSupport::buildActividadQueReturnParametros($aGoBack));

$selForApi = \frontend\shared\helpers\ListNavSupport::idSelIsEmpty($Qid_sel)
    ? []
    : (is_array($Qid_sel) ? $Qid_sel : [\frontend\shared\helpers\PayloadCoercion::string($Qid_sel)]);

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
]);

// Confirmación si hay demasiadas filas: el API devuelve `advertencia_demasiadas` (specs); firmamos aquí.
if (!empty($data['advertencia_demasiadas']) && is_array($data['advertencia_demasiadas'])) {
    $ad = $data['advertencia_demasiadas'];
    $continuarQuery = array_merge(['continuar' => 'si'], $actividadSelectReturn);
    $go_avant = HashFrontSignedLink::fromSpec([
        'path' => 'frontend/actividades/controller/actividad_select.php',
        'query' => $continuarQuery,
    ]);
    $numActiv = \frontend\shared\helpers\PayloadCoercion::int($ad['num_actividades'] ?? 0);
    $html_advertencia = '<h2>' . sprintf(_("son %s actividades a mostrar. ¿Seguro que quiere continuar?."), $numActiv) . '</h2>';
    $html_advertencia .= "<input type='button' onclick=fnjs_update_div('#main','" . $go_avant . "') value=" . _("continuar") . ">";
    $html_advertencia .= "<input type='button' onclick=\"fnjs_nav_atras(1);return false;\" value=" . _("volver") . ">";
    echo $html_advertencia;
    die();
}

$a_valores = ActividadesPayload::listaValoresFromPayload($data['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('actividad_select');
$oTabla->setCabeceras(ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? []));
$oTabla->setBotones(ActividadesListaSupport::botones($data['a_botones'] ?? []));
$oTabla->setDatos($a_valores);
$html_tabla = $oTabla->mostrar_tabla();
unset($data['a_cabeceras'], $data['a_botones'], $data['a_valores']);
$resultado = \frontend\shared\helpers\PayloadCoercion::string($data['resultado'] ?? '');
$perm_nueva = (bool) ($data['perm_nueva'] ?? false);
$mod = \frontend\shared\helpers\PayloadCoercion::string($data['mod'] ?? '');
$obj_pau = \frontend\shared\helpers\PayloadCoercion::string($data['obj_pau'] ?? 'Actividad');
$aTiposActiv = ActividadesListaSupport::datos($data['aTiposActiv'] ?? []);
$extendida = (bool) ($data['extendida'] ?? false);
$id_tipo_activ_efectivo = \frontend\shared\helpers\PayloadCoercion::string($data['id_tipo_activ_efectivo'] ?? $Qid_tipo_activ);

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
];
$oHashSel->setArraycamposHidden($a_camposHiddenSel);

$id_sel_value = is_array($Qid_sel)
    ? \frontend\shared\helpers\PayloadCoercion::string($Qid_sel[0] ?? '')
    : \frontend\shared\helpers\PayloadCoercion::string($Qid_sel);

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
