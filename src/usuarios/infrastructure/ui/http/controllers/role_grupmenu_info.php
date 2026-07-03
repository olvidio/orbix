<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_role = (int)FilterPostGet::post('id_role');

$error_txt = '';

$RoleRepository = DependencyResolver::get(RoleRepositoryInterface::class);
$oRole = $RoleRepository->findById($Qid_role);
if ($oRole === null) {
    ContestarJson::enviar(_('Rol no encontrado'), 'none');
    return;
}
$role = $oRole->getRoleAsString();
$ambito = ($_SESSION['oConfig'] ?? null) instanceof ConfigSnapshot
    ? $_SESSION['oConfig']->getAmbito()
    : '';

// los que ya tengo:
$GrupMenuRoleRepository = DependencyResolver::get(GrupMenuRoleRepositoryInterface::class);
$cGMR = $GrupMenuRoleRepository->getGrupMenuRoles(array('id_role' => $Qid_role));
$aGrupMenus = [];
foreach ($cGMR as $oGrupMenuRole) {
    $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
    $aGrupMenus[$id_grupmenu] = 'x';
}

$GrupMenuRepository = DependencyResolver::get(GrupMenuRepositoryInterface::class);
$cGM = $GrupMenuRepository->getGrupMenus();
$a_valores = [];
$i = 0;
foreach ($cGM as $oGrupMenu) {
    $i++;
    $id_grupmenu = $oGrupMenu->getId_grupmenu();
    // que no lo tenga
    if (array_key_exists($id_grupmenu, $aGrupMenus)) continue;

    $grup_menu = $oGrupMenu->getGrup_menu($ambito);

    $a_valores[$i]['sel'] = "$Qid_role#$id_grupmenu";
    $a_valores[$i][1] = $grup_menu;
}

$a_cabeceras = array('grupmenu');
$a_botones[] = array('txt' => _("añadir"), 'click' => "fnjs_add_grupmenu(\"#from_grupmenu\")");

$data['a_cabeceras'] = $a_cabeceras;
$data['a_botones'] = $a_botones;
$data['a_valores'] = $a_valores;
$data['role'] = $role;

ContestarJson::enviar($error_txt, $data);

