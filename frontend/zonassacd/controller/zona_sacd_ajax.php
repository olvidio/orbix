<?php

use frontend\shared\PostRequest;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
$Qid_zona_new = (string)filter_input(INPUT_POST, 'id_zona_new');
$Qacumular = (int)filter_input(INPUT_POST, 'acumular');
$QAsel = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$QAsel = empty($QAsel) ? [] : $QAsel;

$a_campos_backend = [
    'que' => $Qque,
    'id_zona' => $Qid_zona,
];
if ($Qque === 'update') {
    $a_campos_backend['id_zona_new'] = $Qid_zona_new;
    $a_campos_backend['acumular'] = $Qacumular;
    $a_campos_backend['sel'] = $QAsel;
}

$data = PostRequest::getDataFromUrl('/src/zonassacd/zona_sacd_ajax', $a_campos_backend);

if (($data['tipo'] ?? '') === 'tabla') {
    $oTabla = new Lista();
    $oTabla->setId_tabla($data['id_tabla']);
    $oTabla->setCabeceras($data['a_cabeceras']);
    $oTabla->setBotones($data['a_botones']);
    $oTabla->setDatos($data['a_valores']);
    echo $oTabla->mostrar_tabla();
    return;
}
if (($data['tipo'] ?? '') === 'lista') {
    $oTabla = new Lista();
    $oTabla->setCabeceras($data['a_cabeceras']);
    $oTabla->setDatos($data['a_valores']);
    echo $oTabla->lista();
    return;
}
echo $data['mensaje'] ?? '';
