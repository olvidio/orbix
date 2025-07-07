<?php

use core\ConfigGlobal;
use src\menus\application\repositories\GrupMenuRepository;
use src\menus\application\repositories\GrupMenuRoleRepository;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$Qid_role = (string)filter_input(INPUT_POST, 'id_role');

$error_txt = '';

$UsuarioRepository = new UsuarioRepository();
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
$miRole = $oMiUsuario->getId_role();
// Sólo puede manipular los roles el superadmin (id_role=1).
$permiso = 0;
if ($miRole === 1) {
    $permiso = 1;
}
// Para admin, puede modificar los grupmenus que tiene cada rol, pero no
// crear ni borrar
if ($miRole === 2) {
    $permiso = 2;
}

$txt_sfsv = '';
if (!empty($Qid_role)) {
    $RoleRepository =  new RoleRepository();
    $oRole = $RoleRepository->findById($Qid_role);
    $role = $oRole->getRoleAsString();
    $sf = $oRole->isSf();
    if (!empty($sf)) {
        $chk_sf = 'checked';
        $txt_sfsv = 'sf';
    } else {
        $chk_sf = '';
    }
    $sv = $oRole->isSv();
    if (!empty($sv)) {
        $chk_sv = 'checked';
        $txt_sfsv .= empty($txt_sfsv) ? 'sv' : ',sv';
    } else {
        $chk_sv = '';
    }
    $pau = $oRole->getPau();
    $dmz = $oRole->isDmz();
    $chk_dmz = !empty($dmz) ? 'checked' : '';
    $txt_sfsv = empty($txt_sfsv) ? '' : "($txt_sfsv)";
    //////////////////// grupmenu de role ////////////////////////////////
    $GrupMenuRoleRepository = new GrupMenuRoleRepository();
    $cGMR = $GrupMenuRoleRepository->getGrupMenuRoles(array('id_role' => $Qid_role));

    $i = 0;
    $a_cabeceras = [['name' => _("grupo de menus"), 'width' => '350']];
    $a_botones = [
        ['txt' => _("quitar"), 'click' => "fnjs_del_grupmenu(\"#form_grup_menu\")"],
    ];
    $a_valores = [];
    $GrupMenuRepository = new GrupMenuRepository();
    foreach ($cGMR as $oGrupMenuRole) {
        $i++;
        $id_item = $oGrupMenuRole->getId_item();
        $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
        $oGrupMenu = $GrupMenuRepository->findById($id_grupmenu);

        $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());

        $a_valores[$i]['sel'] = "$id_item";
        $a_valores[$i][1] = $grup_menu;
    }
} else {
    $role = '';
    $sf = '';
    $chk_sf = '';
    $sv = '';
    $chk_sv = '';
    $pau = '';
    $dmz = '';
    $chk_dmz = '';
    $a_cabeceras = [];
    $a_botones = [];
    $a_valores = [];
}

$data['permiso'] = $permiso;
$data['txt_sfsv'] = $txt_sfsv;
$data['role'] = $role;
$data['sf'] = $sf;
$data['chk_sf'] = $chk_sf;
$data['sv'] = $sv;
$data['chk_sv'] = $chk_sv;
$data['pau'] = $pau;
$data['dmz'] = $dmz;
$data['chk_dmz'] = $chk_dmz;
$data['a_cabeceras'] = $a_cabeceras;
$data['a_botones'] = $a_botones;
$data['a_valores'] = $a_valores;

ContestarJson::enviar($error_txt, $data);

