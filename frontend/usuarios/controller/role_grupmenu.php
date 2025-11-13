<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_role = (string)filter_input(INPUT_POST, 'id_role');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_role = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

/////////// Consulta al backend ///////////////////
$url_backend = '/src/usuarios/infrastructure/controllers/role_grupmenu_info.php';
$a_campos_backend = ['id_role' => $Qid_role];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    exit($data['error']);
}

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];
$role = $data['role'];

$oTabla = new Lista();
$oTabla->setId_tabla('grupmenu');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('sel');
$oHash->setcamposNo('scroll_id');
$a_camposHidden = array(
    'id_role' => $Qid_role,
    'que' => 'add_grupmenu'
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'role' => $role,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('role_grupmenu.phtml', $a_campos);