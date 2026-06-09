<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qid_lugar = (integer)filter_input(INPUT_POST, 'id_lugar');

$oPosicion->recordar();


// muestra los ctr que tienen el documento.
$url_backend = '/src/inventario/lista_docs_de_ctr';
$a_campos_backend = [
    'id_ubi' => $Qid_ubi,
    'id_lugar' => $Qid_lugar
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = inventario_post_payload($data);
$a_valores = actividades_lista_datos($payload['a_valores'] ?? []);

$a_cabeceras[] = ucfirst(_("documento"));
$a_cabeceras[] = ucfirst(_("observaciones"));

$a_botones[] = array('txt' => _('marcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\")");
$a_botones[] = array('txt' => _('desmarcar todos'), 'click' => "fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\")");


$oLista = new Lista();
$oLista->setId_tabla('doc_ajax');
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setBotones($a_botones);

echo $oLista->mostrar_tabla_html();



