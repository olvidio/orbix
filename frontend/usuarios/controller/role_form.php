<?php

use frontend\actividades\helpers\ActividadesPostInput;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\usuarios\helpers\UsuariosPostInput;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;


require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$Qid_role = (string)filter_input(INPUT_POST, 'id_role');

$Qid_sel = '';
$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $a_sel = $oPosicion2->getParametro('sel');
            $Qid_role = (string)UsuariosPostInput::idFromSelItem(UsuariosPostInput::selFirstItem($a_sel));
            $Qid_sel = ActividadesPostInput::posicionString($oPosicion2->getParametro('id_sel'));
            $Qscroll_id = ActividadesPostInput::posicionInt($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) {
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'grupmenu_del') {
        $Qid_role = (string)UsuariosPostInput::idFromSelItem(UsuariosPostInput::selFirstItem($a_sel));
    }
}
ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionForRecordar(ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));

$oPosicion->setParametros(array('id_role' => $Qid_role), 1);

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
