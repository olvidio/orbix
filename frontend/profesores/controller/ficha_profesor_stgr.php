<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/profesores_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$sel = profesores_id_from_sel_post();
$id_nom = $sel['id_nom'];
$Qid_tabla = $sel['id_tabla'];

$stackRaw = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
$stack = is_string($stackRaw) ? (int) $stackRaw : 0;
if ($stack !== 0) {
    $oPosicion2 = new frontend\shared\web\Posicion();
    if ($oPosicion2->goStack((string) $stack)) {
        $oPosicion2->olvidar((string) $stack);
    }
}

$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qdepende = (string)filter_input(INPUT_POST, 'depende');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qprint = (int)filter_input(INPUT_POST, 'print');

$oPerm = profesores_o_perm();
if ($oPerm !== null && $oPerm->have_perm_oficina('est')) {
    $Qpermiso = '3';
}

$data = PostRequest::getDataFromUrl('/src/profesores/ficha_profesor_stgr', [
    'id_nom' => $id_nom,
    'id_tabla' => $Qid_tabla,
    'print' => $Qprint,
    'obj_pau' => $Qobj_pau,
    'permiso' => $Qpermiso,
    'depende' => $Qdepende,
]);
if (!empty($data['error'])) {
    exit($data['error']);
}

$goCosasLinkSpecs = profesores_go_cosas_link_specs($data['go_cosas_link_specs'] ?? null);
$fichaSelfLinkSpec = $data['ficha_self_link_spec'] ?? null;
unset($data['go_cosas_link_specs'], $data['ficha_self_link_spec']);

$data['go_cosas'] = profesores_go_cosas_from_specs($fichaSelfLinkSpec, $goCosasLinkSpecs);

$Qprint = !empty($data['use_print_phtml']) ? 1 : 0;
unset($data['use_print_phtml']);

echo $oPosicion->mostrar_left_slide(1);

$viewVars = profesores_ficha_view_vars($data);

$oView = new ViewNewPhtml('frontend\profesores\controller');
if (!empty($Qprint)) {
    $oView->renderizar('ficha_profesor_stgr.print.phtml', $viewVars);
} else {
    $oView->renderizar('ficha_profesor_stgr.phtml', $viewVars);
}
