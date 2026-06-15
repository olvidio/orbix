<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
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
            $Qid_role = (string)usuarios_id_from_sel_item(usuarios_sel_first_item($a_sel));
            $Qid_sel = actividades_posicion_string($oPosicion2->getParametro('id_sel'));
            $Qscroll_id = actividades_posicion_int($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) {
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'grupmenu_del') {
        $Qid_role = (string)usuarios_id_from_sel_item(usuarios_sel_first_item($a_sel));
    }
}
list_nav_boot_recordar($oPosicion, $Qrefresh);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(list_nav_build_return_parametros_from_post(), $Qid_sel, isset($Qscroll_id) ? (string) $Qscroll_id : ''));

$oPosicion->setParametros(array('id_role' => $Qid_role), 1);

$data = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/role_info', ['id_role' => $Qid_role]));
$roleForm = usuarios_role_form_from_payload($data);

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
