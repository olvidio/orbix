<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use menus\model\entity\GestorGrupMenu;
use menus\model\entity\GestorGrupMenuRole;
use usuarios\model\entity\GestorRole;
use usuarios\model\entity\Usuario;
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

$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miRole = $oMiUsuario->getId_role();
$miSfsv = ConfigGlobal::mi_sfsv();
// Sólo puede manipular los roles el superadmin (id_role=1).
// y desde el sv
$permiso = 0;
if ($miRole == 1 && ConfigGlobal::mi_sfsv() == 1) {
    $permiso = 1;
}

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

// todos los posibles GrupMenu
$gesGrupMenu = new GestorGrupMenu();
$cGM = $gesGrupMenu->getGrupMenus(array('_ordre' => 'grup_menu'));
$aGrupMenus = array();
foreach ($cGM as $oGrupMenu) {
    $id_grupmenu = $oGrupMenu->getId_grupmenu();
    $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());
    $aGrupMenus[$id_grupmenu] = $grup_menu;
}

$oGesRole = new GestorRole();
$cRoles = $oGesRole->getRoles(['_ordre' => 'role']);

// Para admin, puede modificar los grupmenus que tiene cada rol, pero no 
// crear ni borrar
if ($miRole == 2) {
    $permiso = 2;
}


$a_cabeceras = array('role', 'sf', 'sv', 'pau', 'dmz', 'grup menu');

$a_botones[] = array('txt' => _("modificar"),
    'click' => "fnjs_modificar(\"#seleccionados\")");
if ($permiso > 0) {
    if ($permiso == 1) {
        $a_botones[] = array('txt' => _("borrar"),
            'click' => "fnjs_eliminar()");
    }
} else {
    $a_botones = array();
}

$a_valores = array();
$i = 0;
foreach ($cRoles as $oRole) {
    $id_role = $oRole->getId_role();
    $role = $oRole->getRole();
    $sf = $oRole->getSf();
    $sv = $oRole->getSv();
    $pau = $oRole->getPau();
    $dmz = $oRole->getDmz();

    if (($permiso != 1) && (($miSfsv == 2 && !$sf) || ($miSfsv == 1 && !$sv))) {
        continue;
    }
    $i++;

    $oGesGMRol = new GestorGrupMenuRole();
    $cGMR = $oGesGMRol->getGrupMenuRoles(array('id_role' => $id_role));
    // intentar ordenar por el nombre del grupmenu
    $a_GM = [];
    foreach ($cGMR as $oGrupMenuRole) {
        $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
        $grup_menu = $aGrupMenus[$id_grupmenu];
        $a_GM[$id_grupmenu] = $grup_menu;
    }
    sort($a_GM);
    // pasar a texto:
    $str_GM = '';
    foreach ($a_GM as $grup_menu) {
        $str_GM .= !empty($str_GM) ? ',' : '';
        $str_GM .= $grup_menu;
    }

    $a_valores[$i][1] = $role;
    $a_valores[$i][2] = $sf;
    $a_valores[$i][3] = $sv;
    $a_valores[$i][4] = $pau;
    $a_valores[$i][5] = $dmz;
    $a_valores[$i][6] = $str_GM;
    if ($permiso > 0) {
        $a_valores[$i]['sel'] = "$id_role#";
    }
}
if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new Lista();
$oTabla->setId_tabla('roles_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHash = new Hash();
$oHash->setCamposForm('');
$oHash->setCamposNo('sel!scroll_id!que');


$url_nuevo = Hash::link(ConfigGlobal::getWeb() . '/apps/usuarios/controller/role_form.php?' . http_build_query(array('nuevo' => 1)));

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'permiso' => $permiso,
    'url_nuevo' => $url_nuevo,
];

$oView = new ViewPhtml('usuarios/controller');
$oView->renderizar('role_lista.phtml', $a_campos);