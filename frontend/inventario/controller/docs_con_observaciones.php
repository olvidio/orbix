<?php

use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

// muestra los documentos en búsqueda.
$url_backend = '/src/inventario/lista_docs_con_observaciones';
$data = PostRequest::getDataFromUrl($url_backend);
$payload = InventarioPayload::postPayload($data);

$a_valores = ActividadesListaSupport::datos($payload['a_valores'] ?? []);

$a_cabeceras = [
    ucfirst(_("centro - lugar")),
    ucfirst(_("documento")),
    ucfirst(_("número")),
    ucfirst(_("observaciones")),
    ucfirst(_("observaciones ctr")),
];
$oTabla = new Lista();
$oTabla->setId_tabla('doc_observ');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('docs_con_observaciones.phtml', $a_campos);
