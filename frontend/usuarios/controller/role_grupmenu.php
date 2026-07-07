<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qid_role = (string)filter_input(INPUT_POST, 'id_role');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $Qid_role = (string)UsuariosPostInput::idFromSelItem(UsuariosPostInput::selFirstItem($a_sel));
}

$navIdentity = $Qid_role !== '' && $Qid_role !== '0' ? ['id_role' => $Qid_role] : [];
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    ListNavSupport::buildReturnParametrosFromPost(),
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    array_merge(
        $Qid_role !== '' && $Qid_role !== '0' ? ['id_role' => $Qid_role] : [],
        ListNavSupport::buildSelectionStatePatchFromPost(),
    ),
);

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/role_grupmenu_info', ['id_role' => $Qid_role]));
$lista = UsuariosPayload::listaFromPayload($data);
$role = \frontend\shared\helpers\PayloadCoercion::string($data['role'] ?? '');

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
