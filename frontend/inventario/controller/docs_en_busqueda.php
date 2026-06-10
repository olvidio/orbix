<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

// muestra los documentos en búsqueda.
$url_backend = '/src/inventario/lista_docs_en_busqueda';
$data = PostRequest::getDataFromUrl($url_backend);
$payload = inventario_post_payload($data);

$a_valores = actividades_lista_datos($payload['a_valores'] ?? []);

$a_cabeceras = [
    ucfirst(_("centro - lugar")),
    ucfirst(_("documento")),
    ucfirst(_("núm. reg.")),
];
$oTabla = new Lista();
$oTabla->setId_tabla('doc_busqueda');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('docs_en_busqueda.phtml', $a_campos);
