<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\profesores\helpers\ProfesoresPermSupport;
use frontend\profesores\helpers\ProfesoresPostInput;
use frontend\profesores\helpers\ProfesoresUrlSigning;
use frontend\profesores\helpers\ProfesoresPayload;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$sel = ProfesoresPostInput::idFromSelPost();
$id_nom = $sel['id_nom'];
$Qid_tabla = $sel['id_tabla'];

ListNavSupport::restoreSelectionFromStackPost();

$navIdentity = $id_nom > 0 ? ['id_nom' => $id_nom, 'id_tabla' => $Qid_tabla] : [];
$navState = ListNavSupport::buildReturnParametrosFromPost();
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildSelectionStatePatchFromPost());

$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qdepende = (string)filter_input(INPUT_POST, 'depende');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qprint = (int)filter_input(INPUT_POST, 'print');

$oPerm = ProfesoresPermSupport::oPerm();
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

$goCosasLinkSpecs = ProfesoresUrlSigning::goCosasLinkSpecs($data['go_cosas_link_specs'] ?? null);
$fichaSelfLinkSpec = $data['ficha_self_link_spec'] ?? null;
unset($data['go_cosas_link_specs'], $data['ficha_self_link_spec']);

$data['go_cosas'] = ProfesoresUrlSigning::goCosasFromSpecs($fichaSelfLinkSpec, $goCosasLinkSpecs);

$Qprint = !empty($data['use_print_phtml']) ? 1 : 0;
unset($data['use_print_phtml']);

echo $oPosicion->mostrarNavAtras(1);

$viewVars = ProfesoresPayload::fichaViewVars($data);

$oView = new ViewNewPhtml('frontend\profesores\controller');
if (!empty($Qprint)) {
    $oView->renderizar('ficha_profesor_stgr.print.phtml', $viewVars);
} else {
    $oView->renderizar('ficha_profesor_stgr.phtml', $viewVars);
}
