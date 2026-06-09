<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qc_p = (string)filter_input(INPUT_POST, 'c_p');
$Qciudad = (string)filter_input(INPUT_POST, 'ciudad');
$Qpais = (string)filter_input(INPUT_POST, 'pais');

$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/direcciones_tabla', [
    'id_ubi' => $Qid_ubi,
    'obj_dir' => $Qobj_dir,
    'c_p' => $Qc_p,
    'ciudad' => $Qciudad,
    'pais' => $Qpais,
]));
$lista = ubis_lista_from_payload($data);

$oTabla = new Lista();
$oTabla->setId_tabla('direcciones_tabla');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones([]);
$oTabla->setDatos($lista['valores']);

$url_nueva = HashFront::link('frontend/ubis/controller/direcciones_editar.php?' . http_build_query([
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
