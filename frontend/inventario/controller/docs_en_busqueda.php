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
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/infrastructure/controllers/lista_docs_en_busqueda.php'
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
