<?php
use src\shared\infrastructure\DependencyResolver;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\config\ConfigGlobal;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\value_objects\PauType;
use src\shared\web\ContestarJson;

$Qid_role = (int)filter_post('id_role');

$error_txt = '';

$UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
$oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
if ($oMiUsuario === null) {
    ContestarJson::enviar(_('Usuario no encontrado'), 'none');
    return;
}
$miRole = $oMiUsuario->getId_role();
$ambito = ($_SESSION['oConfig'] ?? null) instanceof ConfigSnapshot
    ? $_SESSION['oConfig']->getAmbito()
    : '';
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
    $RoleRepository =  DependencyResolver::get(RoleRepositoryInterface::class);
    $oRole = $RoleRepository->findById($Qid_role);
    if ($oRole === null) {
        ContestarJson::enviar(_('Rol no encontrado'), 'none');
        return;
    }
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
    $pau = $oRole->getPauAsString();
    $dmz = $oRole->isDmz();
    $chk_dmz = !empty($dmz) ? 'checked' : '';
    $txt_sfsv = empty($txt_sfsv) ? '' : "($txt_sfsv)";
    //////////////////// grupmenu de role ////////////////////////////////
    $GrupMenuRoleRepository = DependencyResolver::get(GrupMenuRoleRepositoryInterface::class);
    $cGMR = $GrupMenuRoleRepository->getGrupMenuRoles(array('id_role' => $Qid_role));

    $i = 0;
    $a_cabeceras = [['name' => _("grupo de menus"), 'width' => '350']];
    $a_botones = [
        ['txt' => _("quitar"), 'click' => "fnjs_del_grupmenu(\"#form_grup_menu\")"],
    ];
    $a_valores = [];
    $GrupMenuRepository = DependencyResolver::get(GrupMenuRepositoryInterface::class);
    foreach ($cGMR as $oGrupMenuRole) {
        $i++;
        $id_item = $oGrupMenuRole->getId_item();
        $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
        if ($id_grupmenu === null) {
            continue;
        }
        $oGrupMenu = $GrupMenuRepository->findById($id_grupmenu);
        if ($oGrupMenu === null) {
            continue;
        }

        $grup_menu = $oGrupMenu->getGrup_menu($ambito);

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
$data['aOpcionesPau'] = PauType::getArrayPau();

ContestarJson::enviar($error_txt, $data);

