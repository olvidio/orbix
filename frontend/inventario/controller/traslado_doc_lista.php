<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;
use frontend\shared\helpers\ListNavSupport;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qid_lugar = (integer)filter_input(INPUT_POST, 'id_lugar');

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());



// muestra los ctr que tienen el documento.
$url_backend = '/src/inventario/lista_docs_de_ctr';
$a_campos_backend = [
    'id_ubi' => $Qid_ubi,
    'id_lugar' => $Qid_lugar
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$a_valores = ActividadesListaSupport::datos($payload['a_valores'] ?? []);

$a_cabeceras[] = ucfirst(_("documento"));
$a_cabeceras[] = ucfirst(_("observaciones"));

$a_botones[] = array('txt' => _('marcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\")");
$a_botones[] = array('txt' => _('desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\")");


$oLista = new Lista();
$oLista->setId_tabla('doc_ajax');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setBotones($a_botones);

AjaxJsonSupport::html($oLista->mostrar_tabla_html());



