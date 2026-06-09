<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
$oPosicion = FrontBootstrap::boot();

$Qinventario = (string)filter_input(INPUT_POST, 'inventario');
$Qdl = (string)filter_input(INPUT_POST, 'dl');

$oPosicion->recordar();

$url_backend = '/src/inventario/lista_docs_de_dlb';
$a_campos_backend = ['dl' => $Qdl];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = inventario_post_payload($data);
$view = inventario_doc_de_dlb_from_payload($payload);

$a_valores = $view['a_valores'];
$a_grupos = $view['a_grupos'];

$oTabla = new Lista();
$oTabla->setId_tabla('doc_dlb_tabla');
$oTabla->setDatos($a_valores);
$oTabla->setGrupos($a_grupos);

$oHash = new HashFront();
$oHash->setCamposForm('dl');
$oHash->setArrayCamposHidden(['inventario' => $Qinventario]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('doc_de_dlb.phtml', $a_campos);
