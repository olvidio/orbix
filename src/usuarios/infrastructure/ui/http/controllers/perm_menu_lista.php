<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\permisos\domain\MenuDlPermissionBits;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_usuario = (string)\src\shared\domain\helpers\FilterPostGet::post('id_usuario');

$PermMenuRepository = DependencyResolver::get(PermMenuRepositoryInterface::class);
$oGrupoGrupoPermMenu = $PermMenuRepository->getPermMenus(array('id_usuario' => $Qid_usuario));

$a_cabeceras = [array('name' => _("oficina o grupo"), 'width' => '350')];
$a_botones = [array('txt' => _("quitar"), 'click' => "fnjs_del_perm_menu(\"#permisos_menu\")")];

$i = 0;
$a_valores = [];
foreach ($oGrupoGrupoPermMenu as $oPermMenu) {
    $i++;

    $id_item = $oPermMenu->getId_item();
    $menu_perm = $oPermMenu->getMenu_perm();

    $a_valores[$i]['sel'] = "$Qid_usuario#$id_item";
    $a_valores[$i][1] = MenuDlPermissionBits::listaTxt((int)$menu_perm);
}

$data = ['a_cabeceras' => $a_cabeceras,
    'a_botones' => $a_botones,
    'a_valores' => $a_valores,
];

$error_txt = '';

ContestarJson::enviar($error_txt, $data);