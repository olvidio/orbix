<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$Qid_role = (string)filter_input(INPUT_POST, 'id_role');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_role = (string)usuarios_id_from_sel_item(usuarios_sel_first_item($a_sel));
}

$data = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/role_grupmenu_info', ['id_role' => $Qid_role]));
$lista = usuarios_lista_from_payload($data);
$role = tessera_imprimir_string($data['role'] ?? '');

$oTabla = new Lista();
$oTabla->setId_tabla('grupmenu');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($lista['valores']);

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setcamposNo('scroll_id');
$oHash->setArraycamposHidden([
    'id_role' => $Qid_role,
    'que' => 'add_grupmenu',
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'role' => $role,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('role_grupmenu.phtml', $a_campos);
