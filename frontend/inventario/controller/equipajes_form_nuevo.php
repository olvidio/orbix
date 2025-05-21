<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');
$Qnom_equip = (string)filter_input(INPUT_POST, 'nom_equip');

// posibles tipos de documento
$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/inventario/controller/equipajes_lista_activ_sel.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['id_cdc' => $Qid_cdc, 'sel' => $a_sel]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$nombre_ubi = $data['nombre_ubi'];
$ini = $data['ini'];
$fin = $data['fin'];
$ids_activ = $data['ids_activ'];

$nom_equipaje = htmlspecialchars($nombre_ubi) . " ($ini - $fin)";

$oHashForm = new Hash();
$oHashForm->setCamposForm('nom_equipaje');
$oHashForm->setArrayCamposHidden([
    'lugar' => $nombre_ubi,
    'f_ini' => $ini,
    'f_fin' => $fin,
    'id_ubi_activ' => $Qid_cdc,
    'ids_activ' => $ids_activ,
]);

$a_campos = [
    'oHashForm' => $oHashForm,
    'nom_equipaje' => $nom_equipaje,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_form_nuevo.phtml', $a_campos);