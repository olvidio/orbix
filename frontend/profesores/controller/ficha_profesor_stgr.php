<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFrontSignedLink;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_nom = (int)strtok($a_sel[0], "#");
    $Qid_tabla = (string)strtok("#");
} else {
    $Qid_pau = (int)filter_input(INPUT_POST, 'id_pau');
    $Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $id_nom = empty($Qid_nom) ? $Qid_pau : $Qid_nom;
    $Qid_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

$stack = (string)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
if ($stack !== '') {
    $oPosicion2 = new frontend\shared\web\Posicion();
    if ($oPosicion2->goStack($stack)) {
        $oPosicion2->olvidar($stack);
    }
}

$Qpermiso = (string)filter_input(INPUT_POST, 'permiso');
$Qdepende = (string)filter_input(INPUT_POST, 'depende');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qprint = (int)filter_input(INPUT_POST, 'print');

if ($_SESSION['oPerm']->have_perm_oficina('est')) {
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

$goCosasLinkSpecs = $data['go_cosas_link_specs'] ?? [];
$fichaSelfLinkSpec = $data['ficha_self_link_spec'] ?? [];
unset($data['go_cosas_link_specs'], $data['ficha_self_link_spec']);

$goTo = HashFrontSignedLink::fromSpec(is_array($fichaSelfLinkSpec) ? $fichaSelfLinkSpec : []);
$go_cosas = [];
foreach (is_array($goCosasLinkSpecs) ? $goCosasLinkSpecs : [] as $key => $spec) {
    if (!is_array($spec)) {
        continue;
    }
    if ($key === 'print') {
        $go_cosas[$key] = HashFrontSignedLink::fromSpec($spec);
        continue;
    }
    $query = isset($spec['query']) && is_array($spec['query']) ? $spec['query'] : [];
    $query['go_to'] = $goTo;
    $spec['query'] = $query;
    $go_cosas[$key] = HashFrontSignedLink::fromSpec($spec);
}
$data['go_cosas'] = $go_cosas;

$Qprint = !empty($data['use_print_phtml']) ? 1 : 0;
unset($data['use_print_phtml']);

echo $oPosicion->mostrar_left_slide(1);

$oView = new ViewNewPhtml('frontend\profesores\controller');
if (!empty($Qprint)) {
    $oView->renderizar('ficha_profesor_stgr.print.phtml', $data);
} else {
    $oView->renderizar('ficha_profesor_stgr.phtml', $data);
}
