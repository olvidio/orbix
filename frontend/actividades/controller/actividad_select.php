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
 * estado de `Posicion` y construye los hashes del formulario.
 *
 * Migrado desde frontend/actividades/controller/actividad_select.php.
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use src\actividades\domain\value_objects\StatusId;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$Qcontinuar = (string)filter_input(INPUT_POST, 'continuar');
// Solo sirve para esta pagina: importar, publicar, duplicar
$QGstack = (integer)filter_input(INPUT_POST, 'Gstack');
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
} else {
    $stack = '';
}

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
    $Qid_sel = $oPosicion->getParametro('id_sel');
    $Qscroll_id = $oPosicion->getParametro('scroll_id');
    $oPosicion->olvidar($QGstack);

    if (empty($Qperiodo)) {
        $Qperiodo = 'actual';
    }
    $Qssfsv = '';
    $Qsasistentes = '';
    $Qsactividad = '';
    $Qsactividad2 = '';
} else {
    $Qid_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
    if ($stack !== '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
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

    $Qstatus = empty($Qstatus) ? StatusId::ACTUAL : $Qstatus;

    $Qssfsv = (string)filter_input(INPUT_POST, 'ssfsv');
    $Qsasistentes = (string)filter_input(INPUT_POST, 'sasistentes');
    $Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
    $Qsactividad2 = (string)filter_input(INPUT_POST, 'sactividad2');

    $aGoBack = [
        'modo' => $Qmodo,
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
    ];
    $oPosicion->setParametros($aGoBack, 1);
}

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
    'sel' => $Qid_sel,
    'scroll_id' => $Qscroll_id,
    'stack_go' => $oPosicion->getStack(),
]);

// Si el listado es demasiado grande, el backend nos devuelve un bloque
// HTML para pedir confirmacion al usuario.
if (!empty($data['html_advertencia'])) {
    echo $data['html_advertencia'];
    die();
}

$html_tabla = (string)($data['html_tabla'] ?? '');
$resultado = (string)($data['resultado'] ?? '');
$perm_nueva = (bool)($data['perm_nueva'] ?? false);
$mod = (string)($data['mod'] ?? '');
$obj_pau = (string)($data['obj_pau'] ?? 'Actividad');
$aTiposActiv = (array)($data['aTiposActiv'] ?? []);
$extendida = (bool)($data['extendida'] ?? false);
$id_tipo_activ_efectivo = (string)($data['id_tipo_activ_efectivo'] ?? $Qid_tipo_activ);

$oHash = new Hash();
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

$oHashSel = new Hash();
$oHashSel->setCamposForm('!mod!queSel!id_dossier');
$oHashSel->setcamposNo('continuar!sel!scroll_id!fases_on!fases_off');
$a_camposHiddenSel = [
    'obj_pau' => $obj_pau,
    'pau' => 'a',
    'permiso' => '3',
    'Gstack' => $oPosicion->getStack(),
];
$oHashSel->setArraycamposHidden($a_camposHiddenSel);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHashSel' => $oHashSel,
    'aTiposActiv' => $aTiposActiv,
    'resultado' => $resultado,
    'perm_nueva' => $perm_nueva,
    'mod' => $mod,
    'html_tabla' => $html_tabla,
];

$oView = new ViewNewPhtml('frontend\actividades\controller');
$oView->renderizar('actividad_select.phtml', $a_campos);
