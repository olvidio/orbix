<?php

use usuarios\model\entity as usuarios;
use permisos\model\entity as permisos;
use menus\model\entity as menus;
use web\Desplegable;
use usuarios\model\entity\Role;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$obj = 'usuarios\\model\\entity\\Role';

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_role = (string)filter_input(INPUT_POST, 'id_role');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');

$Qid_sel = '';
$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $a_sel = $oPosicion2->getParametro('sel');
            $Qid_role = (integer)strtok($a_sel[0], "#");
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) { //vengo de un checkbox
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque != 'del_grupmenu') { //En el caso de venir de borrar un grupmenu, no hago nada
        $Qid_role = (integer)strtok($a_sel[0], "#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $Qscroll_id, 1);
    }
}
$oPosicion->setParametros(array('id_role' => $Qid_role), 1);

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole = $oMiUsuario->getId_role();
// Sólo puede manipular los roles el superadmin (id_role=1).
$permiso = 0;
if ($miRole == 1) {
    $permiso = 1;
}
// Para admin, puede modificar los grupmenus que tiene cada rol, pero no
// crear ni borrar
if ($miRole == 2) {
    $permiso = 2;
}

$txt_guardar = _("guardar datos rol");
$txt_sfsv = '';
if (!empty($Qid_role)) {
    $que_user = 'guardar';
    $oRole = new usuarios\Role(array('id_role' => $Qid_role));
    $role = $oRole->getRole();
    $sf = $oRole->getSf();
    if (!empty($sf)) {
        $chk_sf = 'checked';
        $txt_sfsv = 'sf';
    } else {
        $chk_sf = '';
    }
    $sv = $oRole->getSv();
    if (!empty($sv)) {
        $chk_sv = 'checked';
        $txt_sfsv .= empty($txt_sfsv) ? 'sv' : ',sv';
    } else {
        $chk_sv = '';
    }
    $pau = $oRole->getPau();
    $dmz = $oRole->getDmz();
    $chk_dmz = !empty($dmz) ? 'checked' : '';
    $txt_sfsv = empty($txt_sfsv) ? '' : "($txt_sfsv)";
} else {
    $que_user = 'nuevo';
    $role = '';
    $sf = '';
    $chk_sf = '';
    $sv = '';
    $chk_sv = '';
    $pau = '';
    $dmz = '';
    $chk_dmz = '';
}

$aOpcionesPau = Role::ARRAY_PAU_TXT;
$oDesplPau = new Desplegable();
$oDesplPau->setNombre('pau');
$oDesplPau->setBlanco(TRUE);
$oDesplPau->setOpciones($aOpcionesPau);
$oDesplPau->setOpcion_sel($pau);


$oTabla = '';
if (!empty($Qid_role)) { // si no hay usuario, no puedo poner permisos.
    //grupo
    $oGesGMRol = new menus\GestorGrupMenuRole();
    $cGMR = $oGesGMRol->getGrupMenuRoles(array('id_role' => $Qid_role));

    $i = 0;
    $a_cabeceras = array(array('name' => _("grupo de menus"), 'width' => '350'));
    $a_botones = array(
        array('txt' => _("quitar"), 'click' => "fnjs_del_grupmenu(\"#form_grup_menu\")")
    );
    $a_valores = array();
    foreach ($cGMR as $oGrupMenuRole) {
        $i++;
        $id_item = $oGrupMenuRole->getId_item();
        $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
        $oGrupMenu = new menus\GrupMenu($id_grupmenu);

        $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());

        $a_valores[$i]['sel'] = "$id_item";
        $a_valores[$i][1] = $grup_menu;
    }
    $oTabla = new web\Lista();
    $oTabla->setId_tabla('role_grupmenu');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setBotones($a_botones);
    $oTabla->setDatos($a_valores);
}

$oHash = new web\Hash();
$oHash->setCamposForm('que!role!sf!sv!pau!dmz');
$oHash->setcamposNo('sf!sv!dmz!refresh');
$a_camposHidden = array(
    'id_role' => $Qid_role,
);
$oHash->setArraycamposHidden($a_camposHidden);

$oHash1 = new web\Hash();
$oHash1->setCamposForm('que!sel');
$oHash1->setcamposNo('scroll_id!refresh');
$a_camposHidden = array(
    'id_role' => $Qid_role,
);
$oHash1->setArraycamposHidden($a_camposHidden);


$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oHash1' => $oHash1,
    'Qid_role' => $Qid_role,
    'role' => $role,
    'que_user' => $que_user,
    'txt_sfsv' => $txt_sfsv,
    'permiso' => $permiso,
    'role' => $role,
    'chk_sf' => $chk_sf,
    'chk_sv' => $chk_sv,
    'oDesplPau' => $oDesplPau,
    'chk_dmz' => $chk_dmz,
    'txt_guardar' => $txt_guardar,
    'oTabla' => $oTabla,
    'nuevo' => $Qnuevo,
];

$oView = new core\View('usuarios/controller');
echo $oView->render('role_form.phtml', $a_campos);