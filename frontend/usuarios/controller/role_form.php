<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;


require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$Qid_role = (string)filter_input(INPUT_POST, 'id_role');

$restored = ListNavSupport::restoreSelectionFromStackPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();
/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!ListNavSupport::idSelIsEmpty($restored['id_sel'])) {
    $a_sel = is_array($restored['id_sel']) ? $restored['id_sel'] : [$restored['id_sel']];
}
if (!empty($a_sel)) {
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'grupmenu_del') {
        $Qid_role = (string)UsuariosPostInput::idFromSelItem(UsuariosPostInput::selFirstItem($a_sel));
    }
}

$navIdentity = $Qid_role !== '' && $Qid_role !== '0' ? ['id_role' => $Qid_role] : [];
$navState = ListNavSupport::mergeSelectionForRecordar(
    ListNavSupport::buildReturnParametrosFromPost(),
    $Qid_sel,
    $Qscroll_id,
);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::buildSelectionStatePatchFromPost(),
);

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/role_info', ['id_role' => $Qid_role]));
$roleForm = UsuariosPayload::roleFormFromPayload($data);

$oDesplPau = new Desplegable();
$oDesplPau->setNombre('pau');
$oDesplPau->setBlanco(TRUE);
$oDesplPau->setOpciones($roleForm['aOpcionesPau']);
$oDesplPau->setOpcion_sel($roleForm['pau']);

$oTabla = new Lista();
$oTabla->setId_tabla('role_grupmenu');
$oTabla->setCabeceras($roleForm['cabeceras']);
$oTabla->setBotones($roleForm['botones']);
$oTabla->setDatos($roleForm['valores']);

$oHash = new HashFront();
$oHash->setCamposForm('que!role!sf!sv!pau!dmz');
$oHash->setcamposNo('sf!sv!dmz!refresh');
$oHash->setArraycamposHidden(['id_role' => $Qid_role]);

$oHash1 = new HashFront();
$oHash1->setCamposForm('que!sel');
$oHash1->setcamposNo('scroll_id!refresh');
$oHash1->setArraycamposHidden(['id_role' => $Qid_role]);

$txt_guardar = _("guardar datos rol");
$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'Qid_role' => $Qid_role,
    'role' => $roleForm['role'],
    'txt_sfsv' => $roleForm['txt_sfsv'],
    'permiso' => $roleForm['permiso'],
    'chk_sf' => $roleForm['chk_sf'],
    'chk_sv' => $roleForm['chk_sv'],
    'oDesplPau' => $oDesplPau,
    'chk_dmz' => $roleForm['chk_dmz'],
    'txt_guardar' => $txt_guardar,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('role_form.phtml', $a_campos);
