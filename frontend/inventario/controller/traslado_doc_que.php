<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();


// muestra los ctr que tienen el documento.
$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/src/inventario/controller/lista_de_ctr.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_opciones = $data['a_opciones'];

$oDesplUbis = new Desplegable('id_ubi', $a_opciones, '', true);
$oDesplUbis->setAction('fnjs_busca_docs()');
$oDesplUbisNew = new Desplegable('id_ubi_new', $a_opciones, '', true);

$oHash = new Hash();
$oHash->setCamposForm('id_ubi!id_ubi_new!sel');
$oHash->setCamposNo('sel');

$a_campos = [
    'oHash' => $oHash,
    'oDesplUbis' => $oDesplUbis,
    'oDesplUbisNew' => $oDesplUbisNew,
];

$oView = new ViewPhtml('../frontend/inventario/controller');
$oView->renderizar('traslado_doc_que.phtml', $a_campos);

