<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;

require_once 'frontend/shared/global_header_front.inc';

echo "<script>fnjs_left_side_hide()</script>";

if (isset($_POST['stack'])) {
    $stack2 = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 !== '') {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack2)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack2);
        }
    }
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_nom = (int)strtok($a_sel[0], '#');
    $id_tabla = (string)strtok('#');
} else {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

$data = PostRequest::getDataFromUrl('/src/notas/tessera_copiar_select_data', [
    'id_nom' => $id_nom,
]);

$nom = $data['nom'] ?? '';
$aPosibles = $data['posibles_personas'] ?? [];

$oDesplPersonas = new Desplegable();
$oDesplPersonas->setNombre('id_nom_dst');
$oDesplPersonas->setBlanco('true');
$oDesplPersonas->setOpciones($aPosibles);

$oHash = new HashFront();
$oHash->setCamposForm('id_nom_dst');
$oHash->setArraycamposHidden(['id_nom_org' => $id_nom]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'nom' => $nom,
    'oDesplPersonas' => $oDesplPersonas,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('tessera_copiar_select.phtml', $a_campos);
