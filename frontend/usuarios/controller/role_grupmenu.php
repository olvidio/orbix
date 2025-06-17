<?php

use src\menus\application\repositories\GrupMenuRepository;
use src\menus\application\repositories\GrupMenuRoleRepository;
use src\shared\ViewSrcPhtml;
use src\usuarios\application\repositories\RoleRepository;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

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

$RoleRepository = new RoleRepository();
$oRole = $RoleRepository->findById($Qid_role);
$role = $oRole->getRoleAsString();

// los que ya tengo:
$GrupMenuRoleRepository = new GrupMenuRoleRepository();
$cGMR = $GrupMenuRoleRepository->getGrupMenuRoles(array('id_role' => $Qid_role));
$aGrupMenus = [];
foreach ($cGMR as $oGrupMenuRole) {
    $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
    $aGrupMenus[$id_grupmenu] = 'x';
}

$GrupMenuRepository = new GrupMenuRepository();
$cGM = $GrupMenuRepository->getGrupMenus();
$a_valores = [];
$i = 0;
foreach ($cGM as $oGrupMenu) {
    $i++;
    $id_grupmenu = $oGrupMenu->getId_grupmenu();
    // que no lo tenga
    if (array_key_exists($id_grupmenu, $aGrupMenus)) continue;

    $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());

    $a_valores[$i]['sel'] = "$Qid_role#$id_grupmenu";
    $a_valores[$i][1] = $grup_menu;
}

$a_cabeceras = array('grupmenu');
$a_botones[] = array('txt' => _("añadir"), 'click' => "fnjs_add_grupmenu(\"#from_grupmenu\")");
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

$oView = new ViewSrcPhtml('frontend\usuarios\controller');
$oView->renderizar('role_grupmenu.phtml', $a_campos);