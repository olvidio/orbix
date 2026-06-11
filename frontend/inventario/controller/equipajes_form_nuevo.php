<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
FrontBootstrap::boot();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');
$Qnom_equip = (string)filter_input(INPUT_POST, 'nom_equip');

$url_backend = '/src/inventario/equipajes_lista_activ_sel';
$a_campos_backend = [
    'id_cdc' => $Qid_cdc,
    'sel' => $a_sel,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$view = inventario_equipajes_form_nuevo_from_payload(inventario_post_payload($data));

$nombre_ubi = $view['nombre_ubi'];
$ini = $view['ini'];
$fin = $view['fin'];
$ids_activ = $view['ids_activ'];

$nom_equipaje = htmlspecialchars($nombre_ubi, ENT_QUOTES, 'UTF-8') . " ($ini - $fin)";

$oHashForm = new HashFront();
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

ajax_json_render_phtml('frontend\inventario\controller', 'equipajes_form_nuevo.phtml', $a_campos);
