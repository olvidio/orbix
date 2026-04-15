<?php

use frontend\shared\PostRequest;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$Qid_zona_new = (string)filter_input(INPUT_POST, 'id_zona_new');
$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAsel = empty($QAsel) ? [] : $QAsel;

$data = PostRequest::getDataFromUrl('/src/zonassacd/zona_ctr_ajax', [
    'que' => $Qque,
    'id_zona' => $Qid_zona,
    'id_zona_new' => $Qid_zona_new,
    'sel' => $QAsel,
]);

if (($data['tipo'] ?? '') === 'tabla') {
    $oTabla = new Lista();
    $oTabla->setId_tabla($data['id_tabla']);
    $oTabla->setCabeceras($data['a_cabeceras']);
    $oTabla->setBotones($data['a_botones']);
    $oTabla->setDatos($data['a_valores']);
    echo $oTabla->mostrar_tabla();
    return;
}
echo $data['mensaje'] ?? '';
