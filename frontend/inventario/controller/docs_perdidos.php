<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// muestra los documentos en búsqueda.
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/controller/lista_docs_perdidos.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
//$oHash->setArrayCamposHidden(['id_tipo_doc' => $Qid_tipo_doc]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];

$a_cabeceras = [
    ucfirst(_("centro - lugar")),
    ucfirst(_("documento")),
    ucfirst(_("número")),
    ucfirst(_("fecha perdido")),
];
$oTabla = new Lista();
$oTabla->setId_tabla('doc_perdido');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oTabla' => $oTabla,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('docs_perdidos.phtml', $a_campos);
