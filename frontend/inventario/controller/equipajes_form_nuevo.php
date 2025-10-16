<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');
$Qnom_equip = (string)filter_input(INPUT_POST, 'nom_equip');

// posibles tipos de documento
$url_backend = '/src/inventario/infrastructure/controllers/equipajes_lista_activ_sel.php';
$a_campos = [
    'id_cdc' => $Qid_cdc,
    'sel' => $a_sel
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos);

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

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_form_nuevo.phtml', $a_campos);