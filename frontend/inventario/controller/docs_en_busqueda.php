<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// muestra los documentos en búsqueda.
$url_backend = '/src/inventario/infrastructure/controllers/lista_docs_en_busqueda.php';
$data = PostRequest::getDataFromUrl($url_backend);

$a_valores = $data['a_valores'];

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
