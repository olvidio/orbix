<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use frontend\shared\web\Lista;

require_once("frontend/shared/global_header_front.inc");

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qc_p = (string)filter_input(INPUT_POST, 'c_p');
$Qciudad = (string)filter_input(INPUT_POST, 'ciudad');
$Qpais = (string)filter_input(INPUT_POST, 'pais');

$data = PostRequest::getDataFromUrl('/src/ubis/direcciones_tabla', [
    'id_ubi' => $Qid_ubi,
    'obj_dir' => $Qobj_dir,
    'c_p' => $Qc_p,
    'ciudad' => $Qciudad,
    'pais' => $Qpais,
]);

$oTabla = new Lista();
$oTabla->setId_tabla('direcciones_tabla');
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones([]);
$oTabla->setDatos($data['a_valores']);

$url_nueva = Hash::link('frontend/ubis/controller/direcciones_editar.php?' . http_build_query([
    'mod' => 'nuevo',
    'id_ubi' => $Qid_ubi,
    'obj_dir' => $Qobj_dir,
]));

$a_campos = [
    'oTabla' => $oTabla,
    'url_nueva' => $url_nueva,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('direcciones_tabla.phtml', $a_campos);
